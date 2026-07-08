<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImporter {

  private $log_model;
  private $id_empresa;

  private function log($str) {
    $id_empresa = $this->id_empresa;
    $this->log_model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "file"=>"excel_importer.txt",
      "texto"=>$str,
    ));
  }

  function save_preview($config = array()) {
    
    $id_empresa = $config["id_empresa"];
    $pathfile = $config["pathfile"];
    $tabla = $config["tabla"];
    $db = $config["db"];

    require_once "../vendor/phpoffice/phpspreadsheet/src/Bootstrap.php";
    $spreadsheet = IOFactory::load($pathfile);
    $sheet = $spreadsheet->getActiveSheet();

    $html = "";
    $fila = 0;
    $columnas = 0;
    foreach ($sheet->getRowIterator() as $row) {
      $html.="<tr>";
      $html.="<td>".($fila+1)."</td>";
      $cellIterator = $row->getCellIterator();
      $cellIterator->setIterateOnlyExistingCells(FALSE);
      $nro_columna = 0;
      foreach ($cellIterator as $key => $cell) {
        $cellValue = $cell->getCalculatedValue();
        $html.="<td>";
        $cellValue = str_replace("'", "", $cellValue);
        $cellValue = str_replace("\"", "", $cellValue);
        $html.=$cellValue;
        $html.="</td>";
        $nro_columna++;
      }
      $html.="</tr>";
      $fila++;
      if ($nro_columna > $columnas) $columnas = $nro_columna;
      if ($fila > 25) break;
    }

    // Guardamos el preview
    $sql = "INSERT INTO importacion_configuracion (id_empresa,tabla,preview, columnas, fecha_subido, archivo, estado) VALUES(";
    $sql.= " $id_empresa,'$tabla','$html','".($columnas)."',NOW(), '$pathfile', 0)";
    $db->query($sql);
    $id = $db->insert_id();

    // Si es USHUAIA, tenemos que guardar otra tabla
    if ($id_empresa == 444) {
      $sql = "INSERT INTO importaciones_articulos (id,id_empresa,fecha_alta,estado) VALUES ($id,$id_empresa,NOW(),0)";
      $db->query($sql);
    }

    return $id;
  }

  function process_file($config = array()) {
    
    $id = $config["id"];
    $id_empresa = $config["id_empresa"];
    $this->id_empresa = $id_empresa;
    $fields = $config["fields"];
    $table = $config["table"];
    $db = $config["db"];
    $key_field = isset($config["key_field"]) ? $config["key_field"] : "codigo";
    $ignore_first_line = isset($config["ignore_first_line"]) ? $config["ignore_first_line"] : 0;
    $only_update = isset($config["only_update"]) ? $config["only_update"] : 0;
    $prefijo_codigo = isset($config["prefijo_codigo"]) ? $config["prefijo_codigo"] : "";
    $insert_defaults = isset($config["insert_defaults"]) ? $config["insert_defaults"] : array();
    $update_defaults = isset($config["update_defaults"]) ? $config["update_defaults"] : array();
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $moneda_lista = isset($config["moneda_lista"]) ? $config["moneda_lista"] : '$';
    $cotizacion = isset($config["cotizacion"]) ? $config["cotizacion"] : 1;
    $fecha_stock_default = isset($config["fecha_stock_default"]) ? $config["fecha_stock_default"] : date("Y-m-d");

    $sql = "SELECT * FROM importacion_configuracion WHERE id_empresa = '$id_empresa' AND id = '$id' ";
    $q = $db->query($sql);
    if ($q->num_rows() <= 0) {
      throw new Exception("No hay registros de importacion con ID [$id]", 1);
    }
    $registro = $q->row();
    $pathfile = $registro->archivo;

    require_once "../vendor/phpoffice/phpspreadsheet/src/Bootstrap.php";
    require_once "application/helpers/import_helper.php";
    require_once "application/helpers/file_helper.php";
    require_once "application/helpers/fecha_helper.php";
    require_once "system/core/Model.php";
    require_once "application/models/stock_model.php";
    require_once "application/models/log_model.php";
    require_once "application/models/articulo_model.php";
    $articulo_model = new Articulo_Model();
    $stock_model = new Stock_Model();
    $this->log_model = new Log_Model();

    // Si no se envio sucursal, tenemos que tomar la primera que encontremos
    if (($table == "articulos" || $table == "importaciones_articulos_items") && $id_sucursal == 0) {
      $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ORDER BY orden ASC";
      $q_alm = $db->query($sql);
      if ($q_alm->num_rows()>0) {
        $alm = $q_alm->row();
        $id_sucursal = $alm->id;
      }
    }

    $spreadsheet = IOFactory::load($pathfile);
    $sheet = $spreadsheet->getActiveSheet();

    $cant_inserts = 0;
    $cant_updates = 0;
    $nro_fila = 0;

    // Creamos un array con solo las columnas obligatorias, para corroborar despues si tiene datos
    $columnas_obligatorias = array();
    foreach($fields as $f) {
      if ($f["obligatoria"] == 1) {
        $columnas_obligatorias[] = $f["columna"];
      }
    }

    // Recorremos las filas
    foreach ($sheet->getRowIterator() as $row) {

      $articulos_images = array();

      // Ignoramos la primera fila
      if ($ignore_first_line == 1 && $nro_fila == 0) { $nro_fila++; continue; }

      $nro_columna = 1;
      $cellIterator = $row->getCellIterator();
      $cellIterator->setIterateOnlyExistingCells(FALSE);

      $key_value = false; // Clave por el cual se consulta si ya existe el elemento
      $columns = array(); // Array de columnas
      $values = array();  // Array de valores
      $link_value = "";   // Almacena el link procesado
      $id = 0;            // ID_ARTICULO
      $usa_stock = 0;
      $id_proveedor_registro = 0;
      $id_marca_vehiculo = 0;
      $modelo_vehiculo = "";
      $stock = 0;
      $codigo_barra = "";
      $fecha_stock_col = "";
      $codigo_prov = "";
      $columns_meli = array();
      $values_meli = array();
      $total_columnas_obligatorias = 0; // Contador con la cantidad de columnas obligatorias que tiene el registro

      $recalcular_precios = array();

      // Recorremos las columnas
      foreach ($cellIterator as $key => $cell) {

        $tipo = false;
        $campo = false;

        foreach($fields as $f) {
          // Si el numero de columna tiene que ser procesado
          if ($f["columna"] == $nro_columna) {
            $campo = $f["campo"];
            $tipo = $f["tipo"];
            break;
          }
        }
        // Si no encontramos el campo, continuamos procesando el archivo
        if ($campo === false) { $nro_columna++; continue; }

        // Calculamos el valor de la celda
        $cellValue = $cell->getCalculatedValue();
        $cellValue = str_replace("'", "", $cellValue);
        $cellValue = str_replace("\"", "", $cellValue);

        // Verificamos el tipo de campo
        if ($tipo == "numero" && (!is_numeric($cellValue))) $cellValue = 0;
        else if ($tipo == "texto") $cellValue = trim($cellValue);

        // Este es el campo por el cual vamos a consultar si el elemento existe
        if ($campo == $key_field) {
          $cellValue = $prefijo_codigo.$cellValue;
          $key_value = $cellValue;
        }

        // Campos especiales
        if ($campo == "marca") {
          $campo = "id_marca";
          if (empty($cellValue)) { $nro_columna++; continue; }
          $sql = "SELECT id FROM marcas WHERE nombre = '$cellValue' AND id_empresa = $id_empresa ";
          $this->log($sql);
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            // La marca ya existe, debemos enlazar el ID
            $marca = $q->row();
            $cellValue = $marca->id;
          } else {
            // Creamos la nueva marca
            $link = filename($cellValue,"-",0);
            $sql = "INSERT INTO marcas (id_empresa,nombre,link,activo) VALUES($id_empresa,'$cellValue','$link',1)";
            $this->log($sql);
            $q = $db->query($sql);
            $cellValue = $db->insert_id();
          }
          $columns[] = $campo;
          $values[] = $cellValue;

        } else if ($campo == "rubro") {
          if (empty($cellValue)) { $nro_columna++; continue; }
          $campo = "id_rubro";
          $sql = "SELECT id FROM rubros WHERE nombre = '$cellValue' AND id_empresa = $id_empresa ";
          $this->log($sql);
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            // La marca ya existe, debemos enlazar el ID
            $marca = $q->row();
            $cellValue = $marca->id;
          } else {
            // Creamos la nueva marca
            $link = filename($cellValue,"-",0);
            $sql = "INSERT INTO rubros (id_empresa,nombre,link,activo) VALUES($id_empresa,'$cellValue','$link',1)";
            $this->log($sql);
            $q = $db->query($sql);
            $cellValue = $db->insert_id();
          }
          $columns[] = $campo;
          $values[] = $cellValue;

        } else if ($campo == "subrubro") {

          if (empty($cellValue)) { $nro_columna++; continue; }
          // IMPORTANTE:
          // Tiene que haberse agregado el campo rubro antes
          $encontro = false;
          $i_col = 0;
          foreach($columns as $col) {
            if ($col == "id_rubro") {
              $encontro = true;
              break;
            }
            $i_col++;
          }
          if (!$encontro) { $nro_columna++; continue; }
          $id_rubro = $values[$i_col];
          $id_subrubro = $id_rubro;

          $campo = "id_rubro";
          $sql = "SELECT id FROM rubros WHERE nombre = '$cellValue' AND id_empresa = $id_empresa AND id_padre = $id_rubro ";
          $this->log($sql);
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            // La subrubro ya existe, debemos enlazar el ID
            $subrubro = $q->row();
            $cellValue = $subrubro->id;
          } else {
            // Creamos la nueva subrubro
            $link = filename($cellValue,"-",0);
            $sql = "INSERT INTO rubros (id_empresa,nombre,link,activo,id_padre) VALUES($id_empresa,'$cellValue','$link',1,$id_rubro)";
            $this->log($sql);
            $q = $db->query($sql);
            $cellValue = $db->insert_id();
          }
          $values[$i_col] = $cellValue;

        } else if ($campo == "subsubrubro") {

          if (empty($cellValue)) { $nro_columna++; continue; }
          // IMPORTANTE:
          // Tiene que haberse agregado el campo subrubro antes
          if (!isset($id_subrubro)) { $nro_columna++; continue; }
          $id_subsubrubro = $values[$i_col];

          $campo = "id_rubro";
          $sql = "SELECT id FROM rubros WHERE nombre = '$cellValue' AND id_empresa = $id_empresa AND id_padre = $id_subrubro ";
          $this->log($sql);
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            // La subrubro ya existe, debemos enlazar el ID
            $subsubrubro = $q->row();
            $cellValue = $subsubrubro->id;
          } else {
            // Creamos la nueva subrubro
            $link = filename($cellValue,"-",0);
            $sql = "INSERT INTO rubros (id_empresa,nombre,link,activo,id_padre) VALUES($id_empresa,'$cellValue','$link',1,$id_subrubro)";
            $this->log($sql);
            $q = $db->query($sql);
            $cellValue = $db->insert_id();
          }
          $values[$i_col] = $cellValue;          

        } else if ($campo == "proveedor") {

          $sql = "SELECT id FROM proveedores WHERE (nombre = '$cellValue' OR razon_social = '$cellValue') AND id_empresa = $id_empresa ";
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            $proveedor = $q->row();
            $id_proveedor_registro = $proveedor->id;
          } else {
            $sql = "INSERT INTO proveedores (id_empresa,nombre,razon_social,activo,tipo_proveedor,fecha_alta) VALUES( ";
            $sql.= " $id_empresa,'$cellValue','$cellValue',1,1,NOW())";
            $q = $db->query($sql);
            $id_proveedor_registro = $db->insert_id();
          }

        } else if ($campo == "vendedor") {

          $sql = "SELECT id FROM vendedores WHERE nombre = '$cellValue' AND id_empresa = $id_empresa ";
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            $vendedor = $q->row();
            $id_vendedor_registro = $vendedor->id;
          } else {
            $sql = "INSERT INTO vendedores (id_empresa,nombre) VALUES( ";
            $sql.= " $id_empresa,'$cellValue')";
            $q = $db->query($sql);
            $id_vendedor_registro = $db->insert_id();
          }    
          $columns[] = "id_vendedor";
          $values[] = $id_vendedor_registro;

        } else if ($campo == "marca_vehiculo") {

          $sql = "SELECT id FROM marcas_vehiculos WHERE nombre = '$cellValue' AND id_empresa = $id_empresa ";
          $q = $db->query($sql);
          if ($q->num_rows() > 0) {
            $marca_vehiculo = $q->row();
            $id_marca_vehiculo = $marca_vehiculo->id;
          } else {
            $link_marca_vehiculo = filename($cellValue,"-",0);
            $sql = "INSERT INTO marcas_vehiculos (id_empresa,nombre,activo,link,orden) VALUES( ";
            $sql.= " $id_empresa,'$cellValue',1,'$link_marca_vehiculo',0)";
            $q = $db->query($sql);
            $id_marca_vehiculo = $db->insert_id();
          }

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "modelo_vehiculo") {

          $modelo_vehiculo = $cellValue;

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "porc_iva") {
          $columns[] = "porc_iva";
          $values[] = $cellValue;
          if ($cellValue == 21) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 5;
          } else if ($cellValue == 10.5) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 4;            
          } else if ($cellValue == 0) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 3;
          } else if ($cellValue == 27) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 6;
          } else if ($cellValue == 5) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 8;
          } else if ($cellValue == 2.5) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 9;
          }

        // Si son precios, tenemos que agregarlos a la columna de precios con descuento tambien
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final") {
          $columns[] = "precio_final_dto";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_1",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final_2") {
          $columns[] = "precio_final_dto_2";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_2",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final_3") {
          $columns[] = "precio_final_dto_3";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_3",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final_4") {
          $columns[] = "precio_final_dto_4";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_4",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final_5") {
          $columns[] = "precio_final_dto_5";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_5",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_final_6") {
          $columns[] = "precio_final_dto_6";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_final_dto_nuevo_6",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "costo_final") {
          $columns[] = "costo_final";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"costo_final_nuevo",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "costo_neto_inicial") {
          if ($moneda_lista == "$") {
            $columns[] = "costo_neto_inicial";
            $values[] = $cellValue;            
            $columns[] = "costo_neto_inicial_dolar";
            $values[] = ((float)$cellValue) / $cotizacion;
          } else {
            $columns[] = "costo_neto_inicial_dolar";
            $values[] = $cellValue;
            $columns[] = "costo_neto_inicial";
            $values[] = ((float)$cellValue) * $cotizacion;
          }
          $recalcular_precios[] = array(
            "campo"=>"costo_neto_inicial_nuevo",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "costo_neto") {
          $columns[] = "costo_neto";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"costo_neto_nuevo",
            "valor"=>$cellValue,
          );

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_neto") {
          $columns[] = "precio_neto";
          $values[] = $cellValue;
          $recalcular_precios[] = array(
            "campo"=>"precio_neto_nuevo_1",
            "valor"=>$cellValue,
          );

        // CAMPOS PARA MERCADOLIBRE
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "id_meli") {
          $columns_meli[] = "id_meli";
          $values_meli[] = $cellValue;
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "permalink") {
          $columns_meli[] = "permalink";
          $values_meli[] = $cellValue;
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "activo_meli") {
          $columns_meli[] = "activo_meli";
          $values_meli[] = $cellValue;
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "titulo_meli") {
          $columns_meli[] = "titulo_meli";
          $values_meli[] = $cellValue;
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "texto_meli") {
          $columns_meli[] = "texto_meli";
          $values_meli[] = $cellValue;
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "precio_meli") {
          $columns_meli[] = "precio_meli";
          $values_meli[] = $cellValue;

        // En USHUAIA: Si el nombre viene con el simbolo 1/2, tiene que tomar 10.5 de IVA. Sino siempre 21%
        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "nombre" && $id_empresa == 444) {
          $columns[] = "nombre";
          $values[] = $cellValue;
          if (strpos($cellValue, "½") > 0) {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 4;
            $columns[] = "porc_iva";
            $values[] = 10.5;
            $porc_iva = 10.5;
          } else {
            $columns[] = "id_tipo_alicuota_iva";
            $values[] = 5;
            $columns[] = "porc_iva";
            $values[] = 21;
            $porc_iva = 21;
          }

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "coeficiente" && $id_empresa == 444) {
          $columns[] = "coeficiente";
          $values[] = $cellValue;
          $coeficiente = ((float)$cellValue);

        // Importante:
        // El texto o el nombre se pueden concatenar, entonces buscamos en el array si ya existen y se lo agregamos (obvio es en orden que aparecen las columnas)

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "nombre") {

          // A partir del nombre tomamos el link
          $l = str_replace("/", "-", $cellValue);
          $l = str_replace("\"", "", $cellValue);
          $l = str_replace("'", "", $cellValue);
          $link_value = "producto/".filename($l,"-",0)."-";

          $encontro = false;
          $i_col = 0;
          foreach($columns as $col) {
            if ($col == $campo) {
              $encontro = true;
              break;
            }
            $i_col++;
          }
          if (!$encontro) {
            $columns[] = $campo;
            $values[] = $cellValue;
          } else {
            $values[$i_col] .= " ".$cellValue;
          }

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "texto") {

          // Puede que haya varias columnas que forman la descripcion. Los textos se van concatenando.
          $encontro = false;
          $i_col = 0;
          foreach($columns as $col) {
            if ($col == $campo) {
              $encontro = true;
              break;
            }
            $i_col++;
          }
          if (!$encontro) {
            $columns[] = $campo;
            $values[] = $cellValue;
          } else {
            $values[$i_col] .= " ".$cellValue;
          }

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "stock") {

          $usa_stock = 1;
          $stock = $cellValue;

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "fecha_stock") {

          $fecha_stock_col = $cellValue;
          if (strpos($fecha_stock_col, "/")>0) $fecha_stock_col = fecha_mysql($fecha_stock_col);

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && ($campo == "custom_10" || $campo == "codigo_prov")) {
          $codigo_prov = trim($cellValue);
          $columns[] = $campo;
          $values[] = $cellValue;

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "path") {
          // Si es una imagen y no tiene URL, agregamos el path completo
          if (!empty($cellValue)) {
            $columns[] = $campo;
            $base_path = "uploads/$id_empresa/articulos/";
            if ($id_empresa == 1284) $base_path.= $id_usuario."/";
            $path = ((strpos($cellValue,"http")===0) ? $cellValue : $base_path.filename($cellValue,"-",0));
            // Si no tiene extension, le agregamos .jpg
            if (empty(get_extension($path))) $path = $path.".jpg";
            $values[] = $path;
            $articulos_images[] = $path;
          }

        } else if (($table == "articulos" || $table == "importaciones_articulos_items") && $campo == "codigo_barra") {

          $codigo_barra = $cellValue;          
          $columns[] = "codigo_barra";
          $values[] = $cellValue;

        } else {
          $columns[] = $campo;
          $values[] = $cellValue;
        }

        // Si la columna esta en el array de columnas obligatorias
        if (in_array($nro_columna, $columnas_obligatorias)) {
          $valor_actual = trim($cellValue);
          // Si no es NULL ni blanco, sumamos el contador de columnas obligatorias que pasaron el filtro
          if (!is_null($valor_actual) && $valor_actual != "") {
            $total_columnas_obligatorias++;
          }
        }

        $nro_columna++;
      }

      //file_put_contents("log_importacion.txt", "COLUMNAS\n".print_r($columns,TRUE)."\n", FILE_APPEND);
      //file_put_contents("log_importacion.txt", "VALUES\n".print_r($values,TRUE)."\n", FILE_APPEND);

      if (!empty($id_usuario) && ($table == "articulos" || $table == "importaciones_articulos_items")) {
        $columns[] = "id_usuario";
        $values[] = $id_usuario;
      }


      // Corroboramos que todas las columnas obligatorias tengan datos
      if ($total_columnas_obligatorias != sizeof($columnas_obligatorias)) continue;

      if ($table == "importaciones_articulos_items") {
        //$columns[] = "id_proveedor";
        //$values[] = $id_proveedor;
        $columns[] = "id_importacion";
        $values[] = $registro->id;
        $columns[] = "estado";
        $values[] = 1;
      }

      //file_put_contents("log_importacion.txt", "LLEGO HASTA ACA\n", FILE_APPEND);

      // Si no existe el key_value, pero tenemos el codigo_prov y un id_proveedor
      if ($key_value === false && !empty($codigo_prov) && !empty($id_proveedor)) {
        $codigo_prov = trim($codigo_prov);
        $sql_prov = "SELECT * FROM articulos_proveedores WHERE id_empresa = $id_empresa ";
        $sql_prov.= "AND codigo = '$codigo_prov' AND id_proveedor = $id_proveedor ";
        $q_prov = $db->query($sql_prov);
        if ($q_prov->num_rows() > 0) {
          $r_prov = $q_prov->row();
          $key_value = $r_prov->id_articulo;
          $key_field = "id"; // Obtenemos el ID
        }
      } else if ($key_value === false && !empty($codigo_barra)) {
        // Si no tenemos key_value, pero tenemos cargado el codigo de barra
        $key_value = $codigo_barra;
        $key_field = "codigo_barra";
      }

      if (isset($coeficiente) && isset($porc_iva) && $id_empresa == 444) {
        // Recalculamos el coeficiente
        for($ic = 0;$ic<sizeof($columns);$ic++) {
          $cc = $columns[$ic];
          if ($cc == "coeficiente") {
            $values[$ic] = $coeficiente / $cotizacion / ((100+$porc_iva)/100);
          }
        }
      }

      // Consultamos si el campo existe
      if ($key_value === false || empty($key_value)) continue;
      $sql = "SELECT 1 FROM `$table` WHERE ";
      if ($key_field == "codigo_barra") {
        //$sql_where = "id_empresa = $id_empresa AND $key_field LIKE '%$key_value%' ";  
        $sql_where = "`$table`.`id_empresa` = $id_empresa AND `$table`.`".$key_field."` = '$key_value' ";
      } else if ($id_empresa == 1284) {
        // SI ES ESTEBAN ECHEVERRIA, EL CODIGO PUEDE EXISTIR EN OTRO COMERCIO, POR LO TANTO HAY QUE FILTRARLO ADEMAS POR EL ID_USUARIO
        $sql_where = "`$table`.`id_empresa` = $id_empresa AND `$table`.`".$key_field."` = '$key_value' AND `$table`.`id_usuario` = $id_usuario ";
      } else {
        $sql_where = "`$table`.`id_empresa` = $id_empresa AND `$table`.`".$key_field."` = '$key_value' ";
      }
      // SOLAMENTE CAMBIAMOS SI TIENE LA RELACION CON EL PROVEEDOR
      if ($table == "articulos" && $id_proveedor != 0) $sql_where.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE AP.id_empresa = `$table`.`id_empresa` AND AP.id_articulo = `$table`.`id` AND AP.id_proveedor = $id_proveedor) ";
      else if ($table == "importaciones_articulos_items" && $id_proveedor != 0) $sql_where.= "AND `$table`.`id_proveedor` = $id_proveedor AND `$table`.`id_importacion` = $registro->id ";
      if ($table == "importaciones_articulos_items") $sql_where.= "AND id_importacion = $registro->id ";
      $this->log($sql.$sql_where);
      $q = $db->query($sql.$sql_where);
      if ($q->num_rows() > 0) {

        // Valores por defecto
        if (sizeof($update_defaults)>0) {
          foreach ($update_defaults as $default_key => $default_value) {
            $columns[] = $default_key;
            $values[] = $default_value;
          }
        }

        // Existe, debemos actualizarlo
        $sql = create_update_sql(array(
          "table"=>$table,
          "fields"=>$columns,
          "data"=>$values,
          "where"=>$sql_where
        ));
        $cant_updates++;
        $this->log($sql);
        $db->query($sql);

        $sql = "SELECT id FROM $table WHERE $sql_where ";
        $q = $db->query($sql);
        if ($q->num_rows()>0) {
          $row = $q->row();
          $id = $row->id;
          if (!empty($link_value) && $table == "articulos") {
            $link_value = $link_value.$id."/";
            $sql = "UPDATE $table SET link = '$link_value' WHERE $sql_where ";
            $this->log($sql);
            $db->query($sql);
          }
        }

      } else {

        // Si permitimos la inserccion
        if ($only_update == 0) {

          // Valores por defecto
          if (sizeof($insert_defaults)>0) {
            foreach ($insert_defaults as $default_key => $default_value) {
              $columns[] = $default_key;
              $values[] = $default_value;
            }
          }
          $columns[] = "id_empresa";
          $values[] = $id_empresa;

          // No existe, debemos insertar un nuevo elemento
          $sql = create_insert_sql(array(
            "table"=>$table,
            "fields"=>$columns,
            "data"=>$values,
          ));
          $cant_inserts++;
          $this->log($sql);
          $db->query($sql);
          $id = $db->insert_id();

          if (!empty($link_value) && $table == "articulos") {
            $link_value = $link_value.$id."/";
            $sql = "UPDATE $table SET link = '$link_value' WHERE $sql_where ";
            $this->log($sql);
            $db->query($sql);
          }
        }

      }

      if (($table == "articulos" || $table == "importaciones_articulos_items")) {
        // Recalculamos el tema de precios y costos
        foreach($recalcular_precios as $cambio_precio) {
          $articulo_model->recalcular_precios(array(
            "tabla"=>$table,
            "id_articulo"=>$id,
            "campo"=>$cambio_precio["campo"],
            "valor"=>$cambio_precio["valor"],
            "id_empresa"=>$id_empresa,  
          ));
        }

        // Si pasamos una columna proveedor
        if ($id_proveedor_registro != 0) {
          $sql = "DELETE FROM articulos_proveedores WHERE id_articulo = $id AND id_empresa = $id_empresa AND id_proveedor = $id_proveedor_registro ";
          $db->query($sql);
          $sql = "INSERT INTO articulos_proveedores (id_empresa,id_articulo,codigo,id_proveedor,orden) VALUES ($id_empresa,$id,'$codigo_prov',$id_proveedor_registro,0) ";
          $db->query($sql);
        }

        // Si tenemos que actualizar las marcas y los modelos de vehiculos
        if ($id_marca_vehiculo != 0 && !empty($modelo_vehiculo)) {
          $sql = "DELETE FROM articulos_marcas_vehiculos WHERE id_articulo = $id AND id_empresa = $id_empresa AND id_marca_vehiculo = $id_marca_vehiculo ";
          $db->query($sql);
          $sql = "INSERT INTO articulos_marcas_vehiculos (id_empresa,id_articulo,modelo,id_marca_vehiculo,orden) VALUES ($id_empresa,$id,'$modelo_vehiculo',$id_marca_vehiculo,1) ";
          $db->query($sql);          
        }

        // Si tenemos alguna imagen guardada
        if (sizeof($articulos_images)>0) {
          foreach($articulos_images as $img) {
            $sql = "SELECT * FROM articulos_images WHERE id_empresa = $id_empresa AND id_articulo = $id AND path = '$img' ";
            $q_images = $db->query($sql);
            if ($q_images->num_rows() == 0) {
              $sql = "INSERT INTO articulos_images (id_empresa,id_articulo,path,activo_web,activo_meli,orden) VALUES ($id_empresa,$id,'$img',1,1,0) ";
              $db->query($sql);
            }
          }
        }

      }

      if (($table == "articulos" || $table == "importaciones_articulos_items") && $id_sucursal != 0 && $usa_stock == 1) {

        $fecha_stock = (isset($fecha_stock_col) && strpos($fecha_stock_col, "-")>0) ? $fecha_stock_col : $fecha_stock_default;

        // Agregamos un movimiento al almacen
        $this->log("Ajustar Stock: $key_value $cellValue $id_sucursal");
        $stock_model->ajustar_stock(array(
          "id_articulo"=>$id,
          "cantidad"=>$stock,
          "id_sucursal"=>$id_sucursal,
          "id_empresa"=>$id_empresa,
          "fecha"=>$fecha_stock,
        ));
      }

      // Si tambien tenemos que trabajar sobre la tabla articulos_meli
      if (sizeof($columns_meli)>0 && $id != 0) {

        $columns_meli[] = "id_empresa";
        $values_meli[] = $id_empresa;
        $columns_meli[] = "id_articulo";
        $values_meli[] = $id;

        $sql = "SELECT * FROM articulos_meli WHERE ";
        $sql_where_meli = " id_empresa = $id_empresa AND id_articulo = '$id' ";
        $q = $db->query($sql.$sql_where_meli);
        if ($q->num_rows()>0) {
          // Actualizamos la tabla articulos_meli
          $sql = create_update_sql(array(
            "table"=>"articulos_meli",
            "fields"=>$columns_meli,
            "data"=>$values_meli,
            "where"=>$sql_where_meli
          ));
          $this->log($sql);
          $db->query($sql);
        } else {
          // Insertamos en la tabla articulos_meli
          $sql = create_insert_sql(array(
            "table"=>"articulos_meli",
            "fields"=>$columns_meli,
            "data"=>$values_meli,
          ));
          $this->log($sql);
          $db->query($sql);
        }
      }


      $nro_fila++;
    } // Fin del for de filas

    // Finalizamos el proceso
    $sql = "UPDATE importacion_configuracion SET ";
    $sql.= " fecha_procesado = NOW(), ";
    $sql.= " cant_insertados = $cant_inserts, ";
    $sql.= " cant_modificados = $cant_updates, ";
    $sql.= " estado = 1 ";
    $sql.= "WHERE id = $registro->id AND id_empresa = $id_empresa ";
    $db->query($sql);

    // ===================================================================
    // USHAUIA
    if ($id_empresa == 444) {
      $sql = "UPDATE importaciones_articulos SET ";
      $sql.= " fecha_modif = NOW(), ";
      $sql.= " id_proveedor = $id_proveedor, ";
      $sql.= " id_usuario = $id_usuario, ";
      $sql.= " estado = 0 "; // Este estado indica que todavia el usuario no lo proceso
      $sql.= "WHERE id = $registro->id AND id_empresa = $id_empresa ";
      $db->query($sql);      
    }
    // ===================================================================


    return true;
  }

}
?>