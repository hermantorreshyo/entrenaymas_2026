<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Articulo_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("articulos","id");
  }

  // Esta funcion es la encargada de sincronizar los productos entre la cuenta del cliente y la cuenta nuestra
  function actualizar_pedienchacabuco($id_empresa) {
    // Controlamos que este sincronizado con PediEnChacabuco
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);
    if (strpos($empresa->configuraciones_especiales, "sincronizado_pedi_en_chacabuco") !== FALSE) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.varcreative.com/sistema/app_pedidos/function/sincronizar_productos/?id_empresa=".$id_empresa);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      $result = curl_exec($ch);
      curl_close($ch);      
    }
  }

  function duplicar($id) {
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $articulo = $this->get($id);
    if ($articulo === FALSE) {
      return array(
        "error"=>1,
        "mensaje"=>"No se encuentra el articulo con ID: $id",
      );
      return;
    }

    $articulo->id = 0;
    $articulo->codigo = $this->next(); // Ponemos el siguiente codigo
    $articulo->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    
    $variantes = $articulo->variantes;
    $proveedores = $articulo->proveedores;
    $marcas_vehiculos = $articulo->marcas_vehiculos;
    $relacionados = $articulo->relacionados;
    $rubros_relacionados = $articulo->rubros_relacionados; 
    $precios_sucursales = $articulo->precios_sucursales; 
    $images = $articulo->images;
    $images_meli = $articulo->images_meli;
    $ingredientes = $articulo->ingredientes;
    $componentes = isset($articulo->componentes) ? $articulo->componentes : array();
    
    // Acomodamos los datos especificos
    $articulo->fecha_mov = fecha_mysql($articulo->fecha_mov);
    $articulo->fecha_eliminado = fecha_mysql($articulo->fecha_eliminado);
    $articulo->path = str_replace(" ", "", $articulo->path);
    
    $this->remove_properties($articulo);
    $insert_id = $this->insert($articulo);
    
    // Actualizamos el link
    $this->load->model("Empresa_Model");
    $articulo->link = $this->Empresa_Model->get_base_link(array("clave"=>"producto","id_empresa"=>$articulo->id_empresa))."/".filename($articulo->nombre,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE articulos SET link = '$articulo->link' WHERE id = $insert_id");

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $sql = "INSERT INTO articulos_images (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$articulo->id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO articulos_images_meli (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$articulo->id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($relacionados as $p) {
      $this->db->insert("articulos_relacionados",array(
      "id_articulo"=>$insert_id,
      "id_relacion"=>$p->id,
      "id_rubro"=>0,
      "id_empresa"=>$articulo->id_empresa,
      "destacado"=>$p->destacado,
      "orden"=>$i,
      ));
      $i++;
    }
    
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($rubros_relacionados as $p) {
      $this->db->insert("articulos_relacionados",array(
      "id_articulo"=>$insert_id,
      "id_relacion"=>0,
      "id_empresa"=>$articulo->id_empresa,
      "id_rubro"=>$p->id,
      "orden"=>$i,
      ));
      $i++;
    }

    foreach($variantes as $v) {
      $this->db->insert("articulos_variantes",array(
      "id_articulo"=>$insert_id,
      "id_empresa"=>$articulo->id_empresa,
      "id_opcion_1"=>$v->id_opcion_1,
      "id_opcion_2"=>$v->id_opcion_2,
      "id_opcion_3"=>$v->id_opcion_3,
      "nombre"=>strip_tags($v->nombre),
      "path"=>$v->path,
      ));
    }

    // Insertamos los ingredientes
    $i=1;
    foreach($ingredientes as $ingre) {
      $this->db->insert("articulos_ingredientes",array(
        "nombre"=>$ingre->nombre,
        "valores"=>$ingre->valores,
        "adicional"=>$ingre->adicional,
        "activo"=>$ingre->activo,
        "id_articulo"=>$insert_id,
        "id_empresa"=>$articulo->id_empresa,
        "orden"=>$i,
      ));
      $i++;
    }  

    // Insertamos los componentes
    foreach($componentes as $c) {
      $this->db->insert("articulos_componentes",array(
      "id_articulo_componente"=>$c->id_articulo_componente,
      "cantidad"=>$c->cantidad,
      "id_articulo"=>$insert_id,
      "id_empresa"=>$articulo->id_empresa,
      ));
    }
    
    $i=1;
    foreach($proveedores as $prov) {
      $this->db->insert("articulos_proveedores",array(
      "id_proveedor"=>$prov->id,
      "id_articulo"=>$insert_id,
      "id_empresa"=>$prov->id_empresa,
      "orden"=>$i,
      "costo_neto"=>$prov->costo_neto,
      "costo_final"=>$prov->costo_final,
      "precio_neto"=>$prov->precio_neto,
      "precio_final"=>$prov->precio_final,
      ));
      $i++;
    }

    foreach($precios_sucursales as $t) {
      $this->db->insert("articulos_precios_sucursales",array(
      "id_sucursal"=>$t->id_sucursal,
      "id_articulo"=>$insert_id,
      "id_empresa"=>$t->id_empresa,
      "costo_neto"=>$t->costo_neto,
      "costo_final"=>$t->costo_final,
      "precio_neto"=>$t->precio_neto,
      "precio_final"=>$t->precio_final,
      "id_tipo_alicuota_iva"=>$t->id_tipo_alicuota_iva,
      "porc_iva"=>$t->porc_iva,
      "costo_iva"=>$t->costo_iva,
      "porc_ganancia"=>$t->porc_ganancia,
      "ganancia"=>$t->ganancia,
      "porc_bonif"=>$t->porc_bonif,
      "precio_final_dto"=>$t->precio_final_dto,
      "moneda"=>$t->moneda,
      "activo"=>(isset($t->activo) ? $t->activo : 1),
      "fecha_mov"=>$t->fecha_mov,
      "costo_neto_inicial"=>(isset($t->costo_neto_inicial) ? $t->costo_neto_inicial : $t->costo_neto),
      "dto_prov"=>(isset($t->dto_prov) ? $t->dto_prov : 0),
      "porc_ganancia_2"=>(isset($t->porc_ganancia_2) ? $t->porc_ganancia_2 : 0),
      "precio_final_2"=>(isset($t->precio_final_2) ? $t->precio_final_2 : 0),
      "porc_bonif_2"=>(isset($t->porc_bonif_2) ? $t->porc_bonif_2 : 0),
      "precio_final_dto_2"=>(isset($t->precio_final_dto_2) ? $t->precio_final_dto_2 : 0),
      "porc_ganancia_3"=>(isset($t->porc_ganancia_3) ? $t->porc_ganancia_3 : 0),
      "precio_final_3"=>(isset($t->precio_final_3) ? $t->precio_final_3 : 0),
      "porc_bonif_3"=>(isset($t->porc_bonif_3) ? $t->porc_bonif_3 : 0),
      "precio_final_dto_3"=>(isset($t->precio_final_dto_3) ? $t->precio_final_dto_3 : 0),
      ));
    }

    $i=1;
    foreach($marcas_vehiculos as $prov) {
      $this->db->insert("articulos_marcas_vehiculos",array(
      "id_marca_vehiculo"=>$prov->id_marca_vehiculo,
      "id_articulo"=>$insert_id,
      "id_empresa"=>$articulo->id_empresa,
      "modelo"=>$prov->modelo,
      "orden"=>$i,
      ));
      $i++;
    }
    
    return array(
      "id"=>$insert_id
    );
  }

  function remove_properties($array) {
    unset($array->variantes);
    unset($array->images);
    unset($array->images_meli);
    unset($array->proveedores);
    unset($array->marcas_vehiculos);
    unset($array->ingredientes);
    unset($array->marca);
    unset($array->promocion);
    unset($array->promocion_path);
    unset($array->rubro);
    unset($array->subrubro);
    unset($array->etiquetas);
    unset($array->codigo_proveedor);        
    unset($array->relacionados);
    unset($array->rubros_relacionados);
  }   

  // FUNCION UTILIZADA POR EL MEGA PARA CAMBIAR LOS PRECIOS DE LAS SUCURSALES A LA VEZ
  function cambio_precios_sucursales($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $ids_sucursales = isset($config["ids_sucursales"]) ? $config["ids_sucursales"] : array();
    $precio_neto = isset($config["precio_neto"]) ? $config["precio_neto"] : 0;
    $precio_final = isset($config["precio_final"]) ? $config["precio_final"] : 0;
    $costo_neto = isset($config["costo_neto"]) ? $config["costo_neto"] : 0;
    $costo_neto_inicial = isset($config["costo_neto_inicial"]) ? $config["costo_neto_inicial"] : 0;
    $costo_final = isset($config["costo_final"]) ? $config["costo_final"] : 0;
    $dto_prov = isset($config["dto_prov"]) ? $config["dto_prov"] : 0;
    $dto_prov_2 = isset($config["dto_prov_2"]) ? $config["dto_prov_2"] : 0;
    $dto_prov_3 = isset($config["dto_prov_3"]) ? $config["dto_prov_3"] : 0;
    $dto_prov_4 = isset($config["dto_prov_4"]) ? $config["dto_prov_4"] : 0;
    $dto_prov_5 = isset($config["dto_prov_5"]) ? $config["dto_prov_5"] : 0;
    $id_tipo_alicuota_iva = isset($config["id_tipo_alicuota_iva"]) ? $config["id_tipo_alicuota_iva"] : 0;
    $porc_iva = isset($config["porc_iva"]) ? $config["porc_iva"] : 0;
    $porc_ganancia = isset($config["porc_ganancia"]) ? $config["porc_ganancia"] : 0;
    
    
    // Si se cambio el precio, cambiamos la fecha de movimiento
    foreach($ids_sucursales as $id_sucursal) {
      
      // Si hubo un cambio de precio
      if ($this->Articulo_Model->existe_cambio_precio(array(
        "id"=>$id_articulo,
        "precio_final"=>$precio_final,
        "costo_neto"=>$costo_neto,
        "id_sucursal"=>$id_sucursal,
      ))) {

        $fecha_mov = date("Y-m-d");
        $last_update = time();

        $this->load->model("Centro_Costo_Model");
        $this->load->model("Almacen_Model");
        $almacen = $this->Almacen_Model->get($id_sucursal);
        $almacenes = $this->Centro_Costo_Model->get_sucursales($almacen->id_centro_costo);
        $almacenes_array = array();
        foreach ($almacenes as $alm) {
          $almacenes_array[] = $alm->id;
        }
        $almacenes_string = implode(",", $almacenes_array);

        // Actualizamos el precio de todas las sucursales que tengan el mismo centro de costos de esa sucursal
        $sql = "UPDATE articulos_precios_sucursales APC SET ";
        $sql.= " APC.fecha_mov = '$fecha_mov', APC.last_update = '$last_update', ";
        $sql.= " APC.costo_neto_inicial = '$costo_neto_inicial', ";

        // En MEGASHOP, poner los descuentos de los proveedores como string en el custom_1
        if ($id_empresa == 249 || $id_empresa == 868) {
          $dto_array = array();
          if (isset($dto_prov) && $dto_prov > 0) $dto_array[] = $dto_prov."%";
          if (isset($dto_prov_2) && $dto_prov_2 > 0) $dto_array[] = $dto_prov_2."%";
          if (isset($dto_prov_3) && $dto_prov_3 > 0) $dto_array[] = $dto_prov_3."%";
          if (isset($dto_prov_4) && $dto_prov_4 > 0) $dto_array[] = $dto_prov_4."%";
          if (isset($dto_prov_5) && $dto_prov_5 > 0) $dto_array[] = $dto_prov_5."%";
          if (sizeof($dto_array)>0) {
            $dto_string = implode(" + ", $dto_array);
            $sql.=" APC.custom_1 = '$dto_string', "; // Se usa para el globito de los distintos descuentos del producto  
          }
        }

        // Juntamos todos los descuentos
        $dto_prov = (isset($costo_neto_inicial) && $costo_neto_inicial != 0) ? ((1 - ($costo_neto / $costo_neto_inicial)) * 100) : 0;

        if (isset($dto_prov)) $sql.= " APC.dto_prov = '$dto_prov', ";
        $sql.= " APC.id_tipo_alicuota_iva = '$id_tipo_alicuota_iva', ";
        $sql.= " APC.porc_iva = '$porc_iva', ";
        $sql.= " APC.costo_neto = '$costo_neto', ";
        $sql.= " APC.costo_final = '$costo_final', ";
        $sql.= " APC.porc_ganancia = '$porc_ganancia', ";
        $sql.= " APC.precio_neto = '$precio_neto', ";
        $sql.= " APC.precio_final = '$precio_final', ";
        $sql.= " APC.precio_final_dto = $precio_final * ((100-APC.porc_bonif)/100) ";
        $sql.= "WHERE APC.id_empresa = $id_empresa ";
        $sql.= "AND APC.id_articulo = $id_articulo ";
        $sql.= "AND APC.id_sucursal IN ($almacenes_string) ";
        $this->db->query($sql);

        if ($id_sucursal == 7) {
          // Actualizamos el precio del articulo
          $sql = "UPDATE articulos SET ";
          $sql.= " fecha_mov = '$fecha_mov', last_update = '$last_update', ";
          if (isset($costo_neto_inicial)) $sql.= " costo_neto_inicial = '$costo_neto_inicial', ";
          if (isset($dto_prov)) $sql.= " dto_prov = '$dto_prov', ";    
          $sql.= " id_tipo_alicuota_iva = '$id_tipo_alicuota_iva', ";
          $sql.= " porc_iva = '$porc_iva', ";
          $sql.= " costo_neto = '$costo_neto', ";
          $sql.= " costo_final = '$costo_final', ";
          $sql.= " porc_ganancia = '$porc_ganancia', ";
          $sql.= " precio_neto = '$precio_neto', ";
          $sql.= " precio_final = '$precio_final', ";
          $sql.= " precio_final_dto = $precio_final * ((100-porc_bonif)/100) ";
          $sql.= "WHERE id_empresa = $id_empresa ";
          $sql.= "AND id = $id_articulo ";
          $this->db->query($sql);          
        }
      }

    }    
  }

  function recalcular_precios($config = array()) {

    $campo = isset($config["campo"]) ? $config["campo"] : "";
    $valor = isset($config["valor"]) ? $config["valor"] : 0;
    $tabla = isset($config["tabla"]) ? $config["tabla"] : "articulos";
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql_where = " id = $id_articulo AND id_empresa = $id_empresa ";

    $sql = "SELECT * FROM $tabla WHERE ".$sql_where;
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return;
    $r = $q->row();

    if ($campo == "costo_final_nuevo") {
      // Si se cambia el costo final, debemos cambiar el costo neto
      $costo_final = (float) $config["valor"];
      $costo_neto = $costo_final / ((100 + $r->porc_iva) / 100);
      $costo_iva = $costo_final - $costo_neto;
      $sql = "UPDATE $tabla SET ";
      $sql.= " costo_final = '$costo_final', costo_neto = '$costo_neto', costo_iva = '$costo_iva' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_neto_nuevo_1") {
      // Si se cambia el precio neto, debemos cambiar el precio final y el precio final con descuento
      $precio_neto = (float) $config["valor"];
      $precio_final = $precio_neto * ((100 + $r->porc_iva) / 100);
      $precio_final_dto = $precio_final * ((100 - $r->porc_bonif) / 100);
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_neto = '$precio_neto', precio_final = '$precio_final', precio_final_dto = '$precio_final_dto' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);
    
    } else if ($campo == "precio_final_dto_nuevo_1") {
      $precio_final_dto_1 = (float) $config["valor"];
      $precio_final_1 = $precio_final_dto_1 / ((100 + $r->porc_bonif) / 100);
      $precio_neto_1 = $precio_final_1 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_1 = ($r->costo_final > 0) ? ((($precio_final_1 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_1 = $precio_final_1 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto = '$precio_final_dto_1', precio_final = '$precio_final_1', precio_neto = '$precio_neto_1', porc_ganancia = '$porc_ganancia_1', ganancia = '$ganancia_1' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_final_dto_nuevo_2") {
      $precio_final_dto_2 = (float) $config["valor"];
      $precio_final_2 = $precio_final_dto_2 / ((100 + $r->porc_bonif_2) / 100);
      $precio_neto_2 = $precio_final_2 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_2 = ($r->costo_final > 0) ? ((($precio_final_2 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_2 = $precio_final_2 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto_2 = '$precio_final_dto_2', precio_final_2 = '$precio_final_2', precio_neto_2 = '$precio_neto_2', porc_ganancia_2 = '$porc_ganancia_2', ganancia_2 = '$ganancia_2' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_final_dto_nuevo_3") {
      $precio_final_dto_3 = (float) $config["valor"];
      $precio_final_3 = $precio_final_dto_3 / ((100 + $r->porc_bonif_3) / 100);
      $precio_neto_3 = $precio_final_3 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_3 = ($r->costo_final > 0) ? ((($precio_final_3 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_3 = $precio_final_3 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto_3 = '$precio_final_dto_3', precio_final_3 = '$precio_final_3', precio_neto_3 = '$precio_neto_3', porc_ganancia_3 = '$porc_ganancia_3', ganancia_3 = '$ganancia_3' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_final_dto_nuevo_4") {
      $precio_final_dto_4 = (float) $config["valor"];
      $precio_final_4 = $precio_final_dto_4 / ((100 + $r->porc_bonif_4) / 100);
      $precio_neto_4 = $precio_final_4 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_4 = ($r->costo_final > 0) ? ((($precio_final_4 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_4 = $precio_final_4 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto_4 = '$precio_final_dto_4', precio_final_4 = '$precio_final_4', precio_neto_4 = '$precio_neto_4', porc_ganancia_4 = '$porc_ganancia_4', ganancia_4 = '$ganancia_4' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_final_dto_nuevo_5") {
      $precio_final_dto_5 = (float) $config["valor"];
      $precio_final_5 = $precio_final_dto_5 / ((100 + $r->porc_bonif_5) / 100);
      $precio_neto_5 = $precio_final_5 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_5 = ($r->costo_final > 0) ? ((($precio_final_5 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_5 = $precio_final_5 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto_5 = '$precio_final_dto_5', precio_final_5 = '$precio_final_5', precio_neto_5 = '$precio_neto_5', porc_ganancia_5 = '$porc_ganancia_5', ganancia_5 = '$ganancia_5' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    } else if ($campo == "precio_final_dto_nuevo_6") {
      $precio_final_dto_6 = (float) $config["valor"];
      $precio_final_6 = $precio_final_dto_6 / ((100 + $r->porc_bonif_6) / 100);
      $precio_neto_6 = $precio_final_6 / ((100 + $r->porc_iva) / 100);
      $porc_ganancia_6 = ($r->costo_final > 0) ? ((($precio_final_6 - $r->costo_final) / $r->costo_final) * 100) : 0;
      $ganancia_6 = $precio_final_6 - $r->costo_final;
      $sql = "UPDATE $tabla SET ";
      $sql.= " precio_final_dto_6 = '$precio_final_dto_6', precio_final_6 = '$precio_final_6', precio_neto_6 = '$precio_neto_6', porc_ganancia_6 = '$porc_ganancia_6', ganancia_6 = '$ganancia_6' ";
      $sql.= "WHERE ".$sql_where;
      $this->db->query($sql);

    }
  }

  function sincronizar_app($config = array()) {
    
    $id_empresa = $config["id_empresa"];
    $version = isset($config["version"]) ? $config["version"] : 1;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $lista_precios = isset($config["lista_precios"]) ? $config["lista_precios"] : 0;
    if ($version > 1) {
      $this->load->model("Stock_Model");
    }
    $sql = "SELECT A.*, IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.lista_precios > 0 ";
    $q = $this->db->query($sql);
    $salida = "";
    foreach($q->result() as $row) {

      $rr = FALSE;
      if ($id_sucursal != 0) {
        $sql = "SELECT * FROM articulos_precios_sucursales ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_sucursal = $id_sucursal ";
        $sql.= "AND id_articulo = $row->id ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows() > 0) {
          $rr = $qq->row();
          $row->precio_final_dto = $rr->precio_final_dto;
          $row->precio_final_dto_2 = $rr->precio_final_dto_2;
          $row->precio_final_dto_3 = $rr->precio_final_dto_3;
        }
      }

      $row->codigo = str_replace("\"", "", str_replace("'", "", $row->codigo));
      $row->nombre = str_replace("\"", "", str_replace("'", "", $row->nombre));
      $row->marca = str_replace("\"", "", str_replace("'", "", $row->marca));
      if ($version == 2) {
        // Version Android
        $row->stock = $this->Stock_Model->get_stock($row->id,array(
          "id_empresa"=>$id_empresa
        ));
        
        // Si esta desactivado el articulo para esa sucursal
        if ($rr !== FALSE && isset($rr->activo) && $rr->activo == 0) $row->stock = 0;

        $salida.= "INSERT INTO articulos (_id,codigo,descripcion,marca,precio_final,precio_final_2,precio_final_3,stock) VALUES ('$row->id','$row->codigo','$row->nombre','$row->marca','$row->precio_final_dto','$row->precio_final_dto_2','$row->precio_final_dto_3','$row->stock') \n";
      } else if ($version >= 3) {

        // Version NUEVA CORDOVA
        $row->stock = $this->Stock_Model->get_stock($row->id,array(
          "id_empresa"=>$id_empresa
        ));

        // Si esta desactivado el articulo para esa sucursal
        if ($rr !== FALSE && isset($rr->activo) && $rr->activo == 0) $row->stock = 0;

        // Configurar si la lista del vendedor es exclusiva o por defecto
        // TODO: The roxy tiene lista exclusiva
        if ($id_empresa == 853 && $lista_precios != 0) {
          if ($lista_precios == 1) {
            $row->precio_final_dto_2 = 0;
            $row->precio_final_dto_3 = 0;
            $row->precio_final_dto_4 = 0;
            $row->precio_final_dto_5 = 0;
            $row->precio_final_dto_6 = 0;
          } else if ($lista_precios == 2) {
            $row->precio_final_dto = 0;
            $row->precio_final_dto_3 = 0;
            $row->precio_final_dto_4 = 0;
            $row->precio_final_dto_5 = 0;
            $row->precio_final_dto_6 = 0;            
          } else if ($lista_precios == 3) {
            $row->precio_final_dto = 0;
            $row->precio_final_dto_2 = 0;
            $row->precio_final_dto_4 = 0;
            $row->precio_final_dto_5 = 0;
            $row->precio_final_dto_6 = 0;
          }
        }

        // Estructura
        // id,codigo,descripcion,marca,precio_final,precio_final_2,precio_final_3,
        // precio_final_4,precio_final_5,precio_final_6,stock,limite_bonif 
        $sep = ";;;";
        $salida.= "articulos".$sep;
        $salida.= "$row->id".$sep."$row->codigo".$sep."$row->nombre".$sep."$row->marca".$sep."$row->precio_final_dto".$sep."$row->precio_final_dto_2".$sep."$row->precio_final_dto_3".$sep."";
        $salida.= "$row->precio_final_dto_4".$sep."$row->precio_final_dto_5".$sep."$row->precio_final_dto_6".$sep."$row->stock".$sep."0\n";

      } else {
        // Version Android Vieja
        $salida.= "INSERT INTO articulos (_id,codigo,descripcion,marca,precio_final,precio_final_2,precio_final_3) VALUES ('$row->id','$row->codigo','$row->nombre','$row->marca','$row->precio_final_dto','$row->precio_final_dto_2','$row->precio_final_dto_3') \n";
      }
      
    }
    return $salida;
  }

  function set_lista_precios_configuracion($data) {
    // Deshabilitamos los errores de la base de datos de codeigniter para evitar que si no existe la tabla tire un error

    if (!isset($data->lista_1_nombre) || empty($data->lista_1_nombre)) $data->lista_1_nombre = "Lista 1";
    if (!isset($data->lista_2_nombre) || empty($data->lista_2_nombre)) $data->lista_2_nombre = "Lista 2";
    if (!isset($data->lista_3_nombre) || empty($data->lista_3_nombre)) $data->lista_3_nombre = "Lista 3";
    if (!isset($data->lista_4_nombre) || empty($data->lista_4_nombre)) $data->lista_4_nombre = "Lista 4";
    if (!isset($data->lista_5_nombre) || empty($data->lista_5_nombre)) $data->lista_5_nombre = "Lista 5";
    if (!isset($data->lista_6_nombre) || empty($data->lista_6_nombre)) $data->lista_6_nombre = "Lista 6";

    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $sql = "SELECT * FROM lista_precios_configuracion WHERE id_empresa = $data->id_empresa ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q === FALSE) return;
    if ($q->num_rows()>0) {
      $sql = "UPDATE lista_precios_configuracion SET ";
      $sql.= " lista_1_nombre = '$data->lista_1_nombre', ";
      $sql.= " lista_2_nombre = '$data->lista_2_nombre', ";
      $sql.= " lista_3_nombre = '$data->lista_3_nombre', ";
      $sql.= " lista_4_nombre = '$data->lista_4_nombre', ";
      $sql.= " lista_5_nombre = '$data->lista_5_nombre', ";
      $sql.= " lista_6_nombre = '$data->lista_6_nombre' ";
      $sql.= "WHERE id_empresa = $data->id_empresa ";
    } else {
      $sql = "INSERT INTO lista_precios_configuracion (";
      $sql.= " id_empresa, lista_1_nombre, lista_2_nombre, lista_3_nombre, lista_4_nombre, lista_5_nombre, lista_6_nombre ";
      $sql.= ") VALUES (";
      $sql.= " '$data->id_empresa', '$data->lista_1_nombre', '$data->lista_2_nombre', '$data->lista_3_nombre', '$data->lista_4_nombre', '$data->lista_5_nombre', '$data->lista_6_nombre' ";
      $sql.= ")";
    }
    $this->db->query($sql);

  }

  function get_lista_precios_configuracion($conf = array()) {
    // Deshabilitamos los errores de la base de datos de codeigniter para evitar que si no existe la tabla tire un error
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $sql = "SELECT * FROM lista_precios_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $this->db->db_debug = $db_debug;
    if ($q !== FALSE && $q->num_rows()>0) {
      $row = $q->row_array();
      unset($row["id_empresa"]);
      return $row;
    } else {
      // Si no existe, tiramos los nombres de las listas por defecto
      return array(
        "lista_1_nombre"=>"Lista 1",
        "lista_2_nombre"=>"Lista 2",
        "lista_3_nombre"=>"Lista 3",
        "lista_4_nombre"=>"Lista 4",
        "lista_5_nombre"=>"Lista 5",
        "lista_6_nombre"=>"Lista 6",
      );
    }
  }

  function get_images($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS AI.*, A.nombre ";
    $sql.= "FROM articulos_images AI ";
    $sql.= "INNER JOIN articulos A ON (AI.id_articulo = A.id AND AI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE AI.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function get_articulo_meli($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT * FROM articulos_meli ";
    $sql.= "WHERE id_articulo = $id AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $art_meli = $q->row();
      return $art_meli;
    } return FALSE;
  }

  function get_articulo_by_id_meli($id_meli,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT * FROM articulos_meli ";
    $sql.= "WHERE id_meli = '$id_meli' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $art_meli = $q->row();
      return $art_meli;
    } return FALSE;
  }  

  function update_meli($art) {

    // Si no existe esta opcion, solamente salimos
    if (!isset($art->categoria_meli)) return FALSE;
    if (empty($art->categoria_meli)) return FALSE;

    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($art->id_empresa);
    if (!$usa_meli) return FALSE;

    $art_meli = $this->get_articulo_meli($art->id);

    $sql = "SELECT ml_recargo_precio, ml_texto_empresa, ml_lista_base FROM web_configuracion WHERE id_empresa = $art->id_empresa LIMIT 0,1";
    $qw = $this->db->query($sql);
    $web_conf = $qw->row();

    if ($web_conf->ml_lista_base >= 0) {
      // Dependiendo de lo que este configurado
      $precio_final_dto = $art->precio_final_dto;
      if ($web_conf->ml_lista_base == 1) $precio_final_dto = $art->precio_final_dto_2;
      else if ($web_conf->ml_lista_base == 2) $precio_final_dto = $art->precio_final_dto_3;
      else if ($web_conf->ml_lista_base == 3) $precio_final_dto = $art->precio_final_dto_4;
      else if ($web_conf->ml_lista_base == 4) $precio_final_dto = $art->precio_final_dto_5;
      else if ($web_conf->ml_lista_base == 5) $precio_final_dto = $art->precio_final_dto_6;
      $art->precio_meli = round($precio_final_dto * (1 + ($web_conf->ml_recargo_precio / 100)),2);
    }

    if ($art_meli === FALSE) {
      // Debemos insertar el elemento en la otra tabla
      $this->db->insert("articulos_meli",array(
        "id_articulo"=>$art->id,
        "id_empresa"=>$art->id_empresa,
        "activo_meli"=>0,
        "titulo_meli"=>$art->titulo_meli,
        "texto_meli"=>$art->texto_meli,
        "atributos_meli"=>(isset($art->atributos_meli) ? $art->atributos_meli : ""),
        "categoria_meli"=>$art->categoria_meli,
        "precio_meli"=>$art->precio_meli,
        "list_type_id"=>$art->list_type_id,
        "forma_envio_meli"=>$art->forma_envio_meli,
        "forma_pago_meli"=>$art->forma_pago_meli,
        "retiro_sucursal_meli"=>$art->retiro_sucursal_meli,
      ));
    } else {
      // Debemos actualizar los datos de la otra tabla
      $this->db->where("id_articulo",$art->id);
      $this->db->where("id_empresa",$art->id_empresa);
      $this->db->update("articulos_meli",array(
        "id_articulo"=>$art->id,
        "id_empresa"=>$art->id_empresa,
        "activo_meli"=>$art->activo_meli,
        "titulo_meli"=>$art->titulo_meli,
        "texto_meli"=>$art->texto_meli,
        "atributos_meli"=>(isset($art->atributos_meli) ? $art->atributos_meli : ""),
        "categoria_meli"=>$art->categoria_meli,
        "precio_meli"=>$art->precio_meli,
        "list_type_id"=>$art->list_type_id,
        "forma_envio_meli"=>$art->forma_envio_meli,
        "forma_pago_meli"=>$art->forma_pago_meli,
        "retiro_sucursal_meli"=>$art->retiro_sucursal_meli,
      ));
    }
    return TRUE;
  }

  private function check_path($img) {
    if (strpos($img, "http:") !== FALSE || strpos($img, "https:") !== FALSE) return $img;
    else return "https://www.varcreative.com/sistema/".$img;
  }

  function update_publicacion_mercadolibre($id,$conf = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    if (!file_exists("logs/$id_empresa")) mkdir("logs/$id_empresa");
    $log = "logs/$id_empresa/".date("Ymd")."_publicar_meli.txt";
    $body = array();

    // Volvemos a obtener el objeto
    $art_meli = $this->get($id,$id_empresa);
    if ($art_meli === FALSE) return FALSE;
    file_put_contents($log, "\n\n====================================================\n\n", FILE_APPEND);
    file_put_contents($log, date("Y-m-d H:i:s").": UPDATE: \n", FILE_APPEND);
    file_put_contents($log, print_r($art_meli,TRUE)."\n", FILE_APPEND);

    if (isset($art_meli->status) && ($art_meli->status == "active" || $art_meli->status == "paused")) {

      // El articulo esta activo en MercadoLibre
      // entonces lo que tenemos que hacer es sincronizar los datos con la publicacion

      // Obtenemos la configuracion de la empresa
      $this->load->model("Empresa_Model");
      $web_conf = $this->Empresa_Model->get_web_conf($art_meli->id_empresa);
      if ($web_conf === FALSE) return FALSE;
      if (empty($web_conf->ml_access_token) || empty($web_conf->ml_refresh_token)) return FALSE;

      require_once '../models/meli.php';
      $meli = new Meli(ML_APP_ID, ML_APP_SECRET, $web_conf->ml_access_token, $web_conf->ml_refresh_token);
      if($web_conf->ml_expires_in < time()) {
        try {
          // Refrescamos el access token
          $refresh = $meli->refreshAccessToken();
          $web_conf->ml_access_token = $refresh['body']->access_token;
          $web_conf->ml_expires_in = time() + $refresh['body']->expires_in;
          $web_conf->ml_refresh_token = $refresh['body']->refresh_token;
          $this->db->where("id_empresa",$web_conf->id_empresa);
          $this->db->update("web_configuracion",$web_conf);
        } catch (Exception $e) {
          echo $e->getMessage();
          return FALSE;
        }
      }

      $this->load->model("Stock_Model");
      $stock = $this->Stock_Model->get_stock($art_meli->id,array(
        "id_empresa"=>$id_empresa,
      ));

      $params = array('access_token'=>$web_conf->ml_access_token);

      // IMPORTANTE: EL ARTICULO PUEDE TENER VARIANTES EN MERCADOLIBRE
      // Y QUE NO ESTEN PUESTAS EN EL SISTEMA. ENTONCES HACEMOS UN GET
      // ANALIZAMOS SI TIENE VARIANTES, Y ACTUALIZAMOS EL MISMO PRECIO PARA TODAS
      $response = $meli->get("/items/".$art_meli->id_meli, $params);
      //file_put_contents($log, date("Y-m-d H:i:s").": GET: \n".print_r($response,TRUE), FILE_APPEND);
      $art_ml = $response["body"];
      $variantes = array();
      if (isset($art_ml->variations) && sizeof($art_ml->variations)>0) {
        $body = array(
          "variations"=>array()
        );
        foreach($art_ml->variations as $v) {
          $body["variations"][] = array(
            "id"=>$v->id,
            "price"=>$art_meli->precio_meli,
            "available_quantity"=>((int)$stock),
            "picture_ids"=>array(),
          );
        }
      } else {
        $body = array(
          "available_quantity"=>((int)$stock),
          "price"=>$art_meli->precio_meli,
        );        
      }

      // Imagenes
      $imagenes = array();
      $body["pictures"] = array();
      foreach($art_meli->images as $img) {
        $body["pictures"][] = array(
          "source"=>$this->check_path($img),
        );
        $imagenes[] = $this->check_path($img);
      }
      foreach($art_meli->images_meli as $img) {
        $body["pictures"][] = array(
          "source"=>$this->check_path($img),
        );
        $imagenes[] = $this->check_path($img);
      }

      // Obtenemos las imagenes por defecto
      $query = $this->db->query("SELECT * FROM web_configuracion_images_meli WHERE id_empresa = $art_meli->id_empresa ORDER BY orden ASC");
      foreach($query->result() as $image_meli) {
        $body["pictures"][] = array(
          "source"=>$this->check_path($image_meli->path),
        );
        $imagenes[] = $this->check_path($image_meli->path);
      }


      // Armamos los atributos del articulo
      $atributos = $this->preparar_atributos_meli($art_meli);
      if ($atributos !== FALSE) $body["attributes"] = $atributos;

      // Si el producto tiene variantes
      if (sizeof($art_meli->variantes)>0) {
        $variaciones = array();
        foreach($art_meli->variantes as $variacion) {

          // Buscamos el stock de esa variante
          $stk_var = 0;
          foreach($variacion->stock_almacenes as $var) {
            $stk_var += ($var->stock_actual - $var->reservado);
          }
          if (empty($stk_var)) continue;
          $combinacion = array();
          for($i=1;$i<4;$i++) {
            if (empty($variacion->{"id_propiedad_".$i})) continue;
            $combinacion[] = array(
              "id"=>$variacion->{"id_propiedad_".$i},
              "value_id"=>$variacion->{"nombre_opcion_".$i},
            );
          }
          $imagenes2 = $imagenes;
          if (!empty($variacion->path)) {
            $imagenes2[] = $this->check_path($variacion->path);
          }
          $variaciones[] = array(
            "attribute_combinations"=>$combinacion,
            "available_quantity"=>(int)$stk_var,
            "price"=>$art_meli->precio_meli,
            "picture_ids"=>$imagenes2,
          );
        }
        if (!empty($variaciones)) {
          $body["variations"] = $variaciones;
        }
      }      

      file_put_contents($log, date("Y-m-d H:i:s").": REQUEST: \n".print_r($body,TRUE), FILE_APPEND);

      $response = $meli->put("/items/".$art_meli->id_meli, $body, $params);
      file_put_contents($log, date("Y-m-d H:i:s").": RESPONSE: \n".print_r($response,TRUE), FILE_APPEND);
      if ($response["httpCode"] == 200) {

        // Ahora actualizamos la descripcion
        $body = array(
          "plain_text"=>strip_tags($art_meli->texto_meli),
        );        
        $response = $meli->put("/items/".$art_meli->id_meli."/description", $body, $params);
        file_put_contents($log, date("Y-m-d H:i:s").": RESPONSE DESCRIPTION: \n".print_r($response,TRUE), FILE_APPEND);

        return TRUE;

      } else if ($response["httpCode"] == 400) {

        file_put_contents($log, date("Y-m-d H:i:s").": FALLO ACTUALIZACION!! \n", FILE_APPEND);

        // SI FALLO, CORROBORAMOS SI SON LAS IMAGENES DE LAS VARIANTES
        $resubir = FALSE;
        $res = $response["body"];
        if (isset($res->cause) && is_array($res->cause) ) {
          foreach($res->cause as $cause) {
            if ($cause->code == "item.pictures.invalid.missing_ids") {
              foreach($body["variations"] as $v) {
                $v["pictures_ids"] = array();
              }
              $resubir = TRUE;
              break;
            }
          }
        }
        if ($resubir) {
          file_put_contents($log, date("Y-m-d H:i:s").": INTENTAMOS RESUBIR \n", FILE_APPEND);
          file_put_contents($log, date("Y-m-d H:i:s").": RESPONSE: \n".print_r($body,TRUE), FILE_APPEND);
          $response = $meli->put("/items/".$art_meli->id_meli, $body, $params);
          file_put_contents($log, date("Y-m-d H:i:s").": RESPONSE: \n".print_r($response,TRUE), FILE_APPEND);
          if ($response["httpCode"] == 200) return TRUE;
        }

        return FALSE;
      }     

    }

  }

  function preparar_atributos_meli($articulo) {
    if (!isset($articulo->atributos)) return FALSE;
    if (sizeof($articulo->atributos) == 0) return FALSE;
    $atributos = array();
    foreach($articulo->atributos as $atributo) {
      $at = array();
      $at["id"] = $atributo->id_atributo;
      if ($atributo->no_aplica == 1) {
        $at["value_name"] = null;
        $at["value_id"] = "-1";
      } else {
        if (!empty($atributo->value_name)) {
          if (isset($atributo->tipo) && $atributo->tipo == "number") {
            $at["value_name"] = $atributo->value_name + 0;  
          } else {
            $at["value_name"] = $atributo->value_name;
          }
        }
        if (!empty($atributo->value_id)) $at["value_id"] = $atributo->value_id;        
      }
      $atributos[] = $at;
    }
    return $atributos;
  }
  
  
  // Controla si la empresa puede seguir creando articulos o no
  function controlar_plan($id_empresa) {
    
    /*
    // Tomamos el plan de la empresa
    $q = $this->db->query("SELECT P.* FROM empresas E INNER JOIN planes P ON (E.id_plan = P.id) WHERE E.id = $id_empresa");
    if ($q->num_rows()<=0) return FALSE;
    $plan = $q->row();
    
    // Contamos la cantidad de facturas que hizo la empresa en el mes
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM articulos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $total = $q->row();
    
    if ($plan->limite_articulos == 0 || $total->cantidad < $plan->limite_articulos) {
      return TRUE;
    } else {
      return FALSE;
    }
    */
    return TRUE;
    
  }  

  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("nombre",$filter);
    $this->db->or_like("codigo_barra",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function existe_cambio_precio($config = array()) {
    
    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $costo_neto = isset($config["costo_neto"]) ? $config["costo_neto"] : FALSE;

    if ($id_sucursal == 0) {
      $sql = "SELECT * FROM articulos WHERE id = $id AND id_empresa = $id_empresa ";  
    } else {
      $sql = "SELECT * FROM articulos_precios_sucursales WHERE id_articulo = $id AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
    }    
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      if (isset($config["precio_final"])) {
        if ($costo_neto !== FALSE) {
          return (($config["precio_final"] != $row->precio_final) || ($config["costo_neto"] != $row->costo_neto));
        } else {
          return ($config["precio_final"] != $row->precio_final);
        }
      }
      return FALSE;
    }
    return FALSE;
  }

  function limpiar_codigo($codigo) {
    // Acomodamos el codigo si hay algun caracter que no corresponde
    $codigo = trim($codigo);
    $codigo = strip_tags($codigo);
    $codigo = str_replace("/", "-", $codigo);
    $codigo = str_replace("*", "-", $codigo);
    $codigo = str_replace("+", "-", $codigo);
    $codigo = str_replace(".", "-", $codigo);
    $codigo = str_replace(",", "-", $codigo);
    $codigo = str_replace(":", "-", $codigo);
    $codigo = str_replace(";", "-", $codigo);
    $codigo = str_replace("(", "", $codigo);
    $codigo = str_replace(")", "", $codigo);
    $codigo = str_replace("[", "", $codigo);
    $codigo = str_replace("]", "", $codigo);
    $codigo = str_replace("{", "", $codigo);
    $codigo = str_replace("}", "", $codigo);
    $codigo = str_replace("\"", "", $codigo);
    $codigo = str_replace("'", "", $codigo);
    $codigo = str_replace("?", "", $codigo);
    $codigo = str_replace("¿", "", $codigo);
    $codigo = str_replace("!", "", $codigo);
    $codigo = str_replace("¡", "", $codigo);
    $codigo = str_replace("|", "", $codigo);
    $codigo = str_replace("#", "", $codigo);
    $codigo = str_replace("$", "", $codigo);
    $codigo = str_replace("%", "", $codigo);
    $codigo = str_replace("&", "", $codigo);
    $codigo = str_replace("@", "", $codigo);
    $codigo = str_replace("=", "", $codigo);
    $codigo = str_replace("º", "", $codigo);
    $codigo = str_replace(" ", "", $codigo);
    return $codigo;
  }
    
  // Controlamos si existe el codigo
  function existe_codigo($codigo,$id = 0) {
    $id_empresa = parent::get_empresa();
    $codigo = trim($codigo);
    if (empty($codigo)) return FALSE;
    $sql = "SELECT * FROM articulos WHERE codigo = '$codigo' AND id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id != $id ";
    $q = $this->db->query($sql);
    return ($q->num_rows()>0);
  }
  
  // Controlamos si existe el codigo de barra
  function existe_codigo_barra($codigo,$id = 0) {
    if (empty($codigo)) return FALSE;
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa ";
    $sql.= "AND codigo_barra REGEXP '(\#\#\#|^)?(".$codigo.")(\#\#\#|$)' ";
    if ($id != 0) $sql.= "AND id != $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      return array(
        "existe"=>1,
        "articulo"=>$q->row(),
      );
    } else {
      return array(
        "existe"=>0,
      );
    }
  }
  
  /**
   * Obtiene los articulos a partir de diferentes parametros
   */
  function buscar($params = array()) {
    
    // Si MIN=1, buscamos una version minima para cargar en JAVASCRIPT
    $min = isset($params["min"]) ? $params["min"] : 0;

    // ID_SUCURSAL se utiliza junto con MIN=1, para setear los precios en los campos
    // que corresponden de acuerdo a la sucursal que se esta filtrando
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;

    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $codigo_prov = isset($params["codigo_prov"]) ? $params["codigo_prov"] : "";
    $mercadolibre = isset($params["mercadolibre"]) ? $params["mercadolibre"] : "";
    $last_update = isset($params["last_update"]) ? $params["last_update"] : "";
    $id_proveedor = isset($params["id_proveedor"]) ? $params["id_proveedor"] : 0;
    $ids_proveedores = isset($params["ids_proveedores"]) ? $params["ids_proveedores"] : "";
    $not_ids_proveedores = isset($params["not_ids_proveedores"]) ? $params["not_ids_proveedores"] : "";
    $id_marca = isset($params["id_marca"]) ? $params["id_marca"] : 0;
    $id_rubro = isset($params["id_rubro"]) ? $params["id_rubro"] : 0;
    $ids_rubros = isset($params["ids_rubros"]) ? $params["ids_rubros"] : 0;
    $id_departamento = isset($params["id_departamento"]) ? $params["id_departamento"] : 0;
    $stock = isset($params["stock"]) ? $params["stock"] : -1;
    $id_promocion = isset($params["id_promocion"]) ? $params["id_promocion"] : 0;
    $id_usuario = isset($params["id_usuario"]) ? $params["id_usuario"] : 0;
    $id_etiqueta = isset($params["id_etiqueta"]) ? $params["id_etiqueta"] : 0;
    $in_ids = isset($params["in_ids"]) ? $params["in_ids"] : "";
    $fecha = isset($params["fecha"]) ? $params["fecha"] : "";
    $fecha_tipo = isset($params["fecha_tipo"]) ? $params["fecha_tipo"] : "";
    $mostrar = isset($params["mostrar"]) ? $params["mostrar"] : 0;
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $descuento = isset($params["descuento"]) ? $params["descuento"] : -1;
    $offset = isset($params["offset"]) ? $params["offset"] : 0;
    $rubros_excluidos = isset($params["rubros_excluidos"]) ? $params["rubros_excluidos"] : array();
    $negado = isset($params["negado"]) ? $params["negado"] : 0;
    $activo = isset($params["activo"]) ? $params["activo"] : -1;
    $destacado = isset($params["destacado"]) ? $params["destacado"] : -1;
    $imagen = isset($params["imagen"]) ? $params["imagen"] : -1;
    $sacar_plu = isset($params["sacar_plu"]) ? $params["sacar_plu"] : 0;
    $tiene_plu = isset($params["tiene_plu"]) ? $params["tiene_plu"] : 0;
    $order = isset($params["order"]) ? $params["order"] : "";
    $tipo_busqueda = isset($params["tipo_busqueda"]) ? $params["tipo_busqueda"] : 0;
    $buscar_stock = isset($params["buscar_stock"]) ? $params["buscar_stock"] : 0;
    $filtro_stock = isset($params["filtro_stock"]) ? $params["filtro_stock"] : "";
    $custom_5 = isset($params["custom_5"]) ? $params["custom_5"] : "";
    $con_ventas_desde = isset($params["con_ventas_desde"]) ? $params["con_ventas_desde"] : "";
    $id_empresa = parent::get_empresa();

    $this->load->model("Configuracion_Model");
    $configuracion_local = $this->Configuracion_Model->es_local();

    // Buscamos la cantidad de sucursales que tiene configurado
    $sql = "SELECT COUNT(*) AS cantidad FROM almacenes WHERE id_empresa = $id_empresa";
    $qq = $this->db->query($sql);
    $rr = $qq->row();
    $cant_almacenes = $rr->cantidad;
    
    // Con este parametro simplificamos al maximo, ya que solo traemos un array de IDS
    $buscar_solo_id = isset($params["buscar_solo_id"]) ? $params["buscar_solo_id"] : 0;
    if ($buscar_solo_id == 1) $min = 1;    

    if ($buscar_solo_id == 1) { 
      
      $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
      $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND A.id_empresa = R.id_empresa) ";

    } else {

      if ($min == 0) {

        if ($id_sucursal == 0 || $configuracion_local == 1 || $cant_almacenes <= 1) {

          $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
          $sql.= "A.*, ";
          $sql.= "  IF(A.fecha_ingreso='0000-00-00','',DATE_FORMAT(A.fecha_ingreso,'%d/%m/%Y')) AS fecha_ingreso, ";
          $sql.= "  IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
          $sql.= "  IF(R.nombre IS NULL,'-',R.nombre) AS rubro, ";
          $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
          if ($id_proveedor != 0) {
            $sql.= "AP.codigo AS codigo_proveedor, ";
          } else {
            $sql.= "0 AS codigo_proveedor, ";
          }
          $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
          $sql.= "FROM articulos A ";
          $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
          $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND A.id_empresa = R.id_empresa) ";
          $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
          $sql.= "LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa) ";

        } else {

          $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
          $sql.= " A.*, ";
          $sql.= " APS.precio_neto, APS.precio_final, APS.precio_neto AS precio_neto_2, APS.precio_final AS precio_final_2, APS.precio_neto AS precio_neto_3, APS.precio_final AS precio_final_3, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto_2, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto_3, ";
          $sql.= " APS.id_tipo_alicuota_iva, APS.porc_iva, APS.porc_bonif, APS.porc_bonif AS porc_bonif_2, APS.porc_bonif AS porc_bonif_3, A.custom_5, ";
          $sql.= " APS.costo_neto, APS.costo_final, APS.costo_neto_inicial, APS.dto_prov, ";
          $sql.= " APS.porc_ganancia, APS.ganancia, ";
          $sql.= "  IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
          $sql.= "  IF(R.nombre IS NULL,'-',R.nombre) AS rubro, ";
          if ($id_proveedor != 0) {
            $sql.= "AP.codigo AS codigo_proveedor, ";
          } else {
            $sql.= "0 AS codigo_proveedor, ";
          }
          $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
          $sql.= "FROM articulos A ";
          $sql.= "INNER JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND APS.id_sucursal = $id_sucursal) ";
          $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
          $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND A.id_empresa = R.id_empresa) ";
          $sql.= "LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa) ";
        }

      } else if ($min == 1) {

        if ($id_sucursal == 0) {

          // Version minima sin identificar una sucursal especifica
          $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
          $sql.= " A.id, A.nombre, A.tipo, A.precio_neto, A.precio_final, A.precio_neto_2, A.precio_final_2, A.precio_neto_3, A.precio_final_3, ";
          $sql.= " A.precio_final_dto, A.precio_final_dto_2, A.precio_final_dto_3, A.custom_5, ";
          $sql.= " A.precio_neto_4, A.precio_final_4, A.precio_final_dto_4, A.porc_bonif_4, ";
          $sql.= " A.precio_neto_5, A.precio_final_5, A.precio_final_dto_5, A.porc_bonif_5, ";
          $sql.= " A.precio_neto_6, A.precio_final_6, A.precio_final_dto_6, A.porc_bonif_6, ";
          $sql.= " A.id_rubro, A.id_departamento, A.id_marca, A.codigo, A.id_tipo_alicuota_iva, A.porc_iva, A.porc_bonif, A.porc_bonif_2, A.porc_bonif_3, R.nombre AS rubro, ";
          $sql.= " A.percep_viajes, A.unidad, A.costo_neto, A.costo_final, A.no_totalizar_reparto, A.uxb, A.nplu, A.codigo_barra, A.id_usuario ";
          $sql.= "FROM articulos A ";
          $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
          $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND A.id_empresa = R.id_empresa) ";
          $sql.= "LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa) ";

        } else {

          // Version minima para una sucursal especifica
          $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
          $sql.= " A.id, A.nombre, A.tipo, A.id_rubro, A.id_marca, A.codigo, R.nombre AS rubro, A.percep_viajes, A.unidad, A.id_departamento, ";
          $sql.= " A.no_totalizar_reparto, A.uxb, A.nplu, A.codigo_barra, A.id_usuario, ";
          $sql.= " APS.precio_neto, APS.precio_final, APS.precio_neto AS precio_neto_2, APS.precio_final AS precio_final_2, APS.precio_neto AS precio_neto_3, APS.precio_final AS precio_final_3, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto_2, ";
          $sql.= " APS.precio_final_dto AS precio_final_dto_3, ";
          $sql.= " APS.id_tipo_alicuota_iva, APS.porc_iva, APS.porc_bonif, APS.porc_bonif AS porc_bonif_2, APS.porc_bonif AS porc_bonif_3, ";
          $sql.= " APS.porc_ganancia, APS.ganancia, ";
          $sql.= " APS.costo_neto, APS.costo_final, APS.costo_neto_inicial, APS.dto_prov ";
          $sql.= "FROM articulos A ";
          $sql.= "INNER JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND APS.id_sucursal = $id_sucursal) ";
          $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
          $sql.= "LEFT JOIN rubros R ON (A.id_rubro = R.id AND A.id_empresa = R.id_empresa) ";
          $sql.= "LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa) ";
        }

      }
    }

    if ($id_proveedor != 0 || !empty($ids_proveedores) || !empty($not_ids_proveedores)) {
      $sql.= "INNER JOIN articulos_proveedores AP ON (A.id = AP.id_articulo AND A.id_empresa = AP.id_empresa) ";
    }

    $sql.= "WHERE A.id_empresa = $id_empresa ";
    
    // Si estamos filtrando por proveedor
    if ($id_proveedor != 0) {
      if ($negado == 0) $sql.= "AND AP.id_proveedor = $id_proveedor ";
      else $sql.= "AND AP.id_proveedor != $id_proveedor ";
    }

    if (!empty($ids_proveedores)) $sql.= "AND AP.id_proveedor IN ($ids_proveedores) ";
    if (!empty($not_ids_proveedores)) $sql.= "AND AP.id_proveedor NOT IN ($not_ids_proveedores) ";

    $filter = trim($filter);
    if (!empty($filter)) {

      if ($id_empresa == 249 || $id_empresa == 868) {

        // Arreglo para MEGASHOP
        // El sistema anterior cortaba los codigos de barra en 12 digitos
        $sql.= "AND (";
        if (is_numeric($filter)) {
          if (strlen($filter) == 13 && ($id_empresa == 249 || $id_empresa == 868)) {
            $filter_12 = substr($filter, 0, 12);
            $sql.= "(A.codigo_barra LIKE '%$filter%' OR A.codigo_barra LIKE '%$filter_12%') ";
          } else if (strlen($filter)>7) {
            $filter = (double) $filter;
            $sql.= "(A.codigo_barra LIKE '%$filter%') ";
          } else if (strlen($filter) == 7) {
            $filter_6 = substr($filter, 0, 6);
            $filter_0 = "0".$filter;
            $sql.= "(A.codigo_barra = '$filter_0' OR A.codigo = '$filter' OR A.codigo = '$filter_6' OR A.codigo_barra LIKE '%$filter%' OR A.codigo_barra LIKE '$filter_6%') ";
          } else {
            $sql.= "(A.codigo = '$filter') ";
          }
        } else {
          $filter3 = "";
          $filter2 = preg_split('/\s+/', $filter);
          foreach($filter2 as $fil) {
            $filter3 .= "+(*".$fil."*) ";
          }
          $sql.= "( MATCH(A.nombre) AGAINST ('$filter3' IN BOOLEAN MODE)  ) ";
        }
        if ($id_proveedor != 0) {
          $sql.= "OR (AP.codigo LIKE '%$filter%') ";
        }
        $sql.= ") ";

      // TODO: Hacer esto configurable, para poder parametrizar por ejemplo buscar por multiples codigos
      } else if ($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) {
        // VICTOR
        if (strpos($filter,",") !== FALSE) {
          $sql.= "AND A.codigo IN ($filter) ";
        } else if (is_numeric($filter)) {
          $sql.= "AND A.codigo = '$filter' ";
        } else {
          $sql.= "AND (( MATCH(A.nombre) AGAINST ('$filter3' IN BOOLEAN MODE) ) ";
          $sql.= "OR A.nombre LIKE '%$filter%' ) ";
        }
      } else {
        // Si tenemos q seleccionar varios
        if (strpos($filter,",") !== FALSE) {
          $sql.= "AND A.codigo IN ($filter) ";
        } else {
          if ($tipo_busqueda == 0) {
            $sql.= "AND (A.codigo_barra LIKE '%$filter%' ";

            $filter3 = "";
            $filter2 = preg_split('/\s+/', $filter);
            foreach($filter2 as $fil) {
              $filter3 .= "+(*".$fil."*) ";
            }
            $sql.= "OR ( MATCH(A.nombre) AGAINST ('$filter3' IN BOOLEAN MODE) ) ";
            $sql.= "OR A.nombre LIKE '%$filter%' ";

            if ($id_proveedor != 0) {
              $sql.= "OR AP.codigo LIKE '%$filter%' ";
            }
            $sql.= "OR A.codigo LIKE '$filter%') ";
          } elseif ($tipo_busqueda == 1) {
            $filter = (int)$filter;
            $sql.= "AND A.codigo >= $filter ";
          }
        }
      }
    }

    if (!empty($con_ventas_desde)) {
      $sql.= "AND (";
      $sql.= " EXISTS (SELECT 1 FROM facturas_items FI ";
      $sql.= " INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= " WHERE FI.id_empresa = A.id_empresa ";
      $sql.= " AND FI.id_articulo = A.id ";
      if ($id_sucursal != 0) $sql.= " AND F.id_sucursal = $id_sucursal ";
      $sql.= " AND F.fecha >= '$con_ventas_desde' ";
      $sql.= " LIMIT 0,1) ";
      $sql.= ") ";
    }

    if ($filtro_stock != "") {
      $sql.= "AND ( (SELECT IF(SUM(S.stock_actual) IS NULL,0,SUM(S.stock_actual)) AS c FROM stock S ";
      $sql.= "WHERE S.id_empresa = A.id_empresa AND S.id_articulo = A.id ";
      if (!empty($id_sucursal)) $sql.= "AND S.id_sucursal = $id_sucursal ";
      $sql.= ") ";
      if ($filtro_stock == "con_stock") $sql.= " > 0) ";
      else if ($filtro_stock == "sin_stock") $sql.= " <= 0) ";
    }

    if (!empty($codigo_prov)) $sql.= "AND A.custom_10 = '$codigo_prov' ";

    if ($descuento == 0) $sql.= "AND A.porc_bonif = 0 ";
    else if ($descuento == 1) $sql.= "AND A.porc_bonif > 0 ";

    if ($imagen == 0) $sql.= "AND A.path = '' ";
    else if ($imagen == 1) $sql.= "AND A.path != '' ";

    if (!empty($in_ids)) {
      $in_ids = str_replace("-", ",", $in_ids);
      $sql.= "AND A.id IN ($in_ids) ";
    }

    if ($mercadolibre == "P") {
      $sql.= "AND EXISTS (SELECT 1 FROM articulos_meli MELI WHERE A.id = MELI.id_articulo AND A.id_empresa = MELI.id_empresa AND MELI.status = 'paused') ";
    } else if ($mercadolibre == "A") {
      $sql.= "AND EXISTS (SELECT 1 FROM articulos_meli MELI WHERE A.id = MELI.id_articulo AND A.id_empresa = MELI.id_empresa AND MELI.status = 'active') ";
    }

    if ($destacado == 1) $sql.= "AND A.lista_precios >= 3 ";
    else if ($destacado == 0) $sql.= "AND A.lista_precios < 3 ";
    if ($activo == 1) $sql.= "AND A.lista_precios >= 1 ";
    else if ($activo == 0) $sql.= "AND A.lista_precios < 1 ";

    if (!empty($last_update)) $sql.= "AND A.last_update = '$last_update' ";

    if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
    if (!empty($id_marca)) $sql.= "AND M.id = $id_marca ";

    if ($custom_5 == '1') $sql.= "AND A.custom_5 = '1' ";
    else if ($custom_5 == "0") $sql.= "AND A.custom_5 != '1' ";
    else if ($custom_5 == "0_iva") $sql.= "AND A.porc_iva = 0 ";

    $this->load->model("Rubro_Model");

    if ($id_rubro == -1) $sql.= "AND A.id_rubro = 0 ";
    else if (!empty($id_rubro)) {
      $ids_r = $this->Rubro_Model->get_ids_rubros($id_rubro);
      $ids_r = implode(",", $ids_r);
      $sql.= "AND A.id_rubro IN ($ids_r) ";
    }

    // Recorremos todos los rubros incluidos y buscamos cada hijo
    if (!empty($ids_rubros)) {
      $rs = explode(",", $ids_rubros);
      $ids_r = "";
      foreach($rs as $id_rubro) {
        $ids_r.= implode(",", $this->Rubro_Model->get_ids_rubros($id_rubro));
      }
      $sql.= "AND A.id_rubro IN ($ids_r) ";
    }
    
    if (!empty($id_promocion)) $sql.= "AND A.id_promocion = $id_promocion ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    if (!empty($fecha)) {
      $fecha = str_replace("-","/",$fecha);
      if ($fecha_tipo == "mayor") $fecha_tipo = " >= ";
      else if ($fecha_tipo == "menor") $fecha_tipo = " <= ";
      else $fecha_tipo = " = ";
      $sql.= "AND A.fecha_mov $fecha_tipo '$fecha' ";
    }
    if (!empty($rubros_excluidos)) $sql.= "AND A.id_rubro NOT IN (".implode(",",$rubros_excluidos).") ";
    if ($tiene_plu == 1) $sql.= "AND A.nplu != 0 ";

    if (!empty($id_etiqueta)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_etiquetas_relacionadas AER WHERE AER.id_empresa = A.id_empresa AND AER.id_articulo = A.id AND AER.id_etiqueta = $id_etiqueta) ";

    if (empty($order)) $sql.= "ORDER BY A.nombre ASC ";
    else if (strpos($order, "CAST") !== FALSE) $sql.= "ORDER BY $order ";
    else $sql.= "ORDER BY A.$order ";
    
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    //echo $sql; exit();
    $sql_main = $sql;
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    
    // Recorremos el resultado
    if ($min == 0) {
      $resultado = array();
      foreach($q->result() as $r) {

        // Si esta compartido en MercadoLibre
        $r->activo_meli = 0;
        $r->categoria_meli = "";
        $r->permalink = "";
        $r->status = "";
        $r->id_meli = "";
        $qqq = $this->db->query("SELECT * FROM articulos_meli WHERE id_empresa = $id_empresa AND id_articulo = $r->id ");
        if ($qqq->num_rows()>0) {
          $rrr = $qqq->row();
          $r->id_meli = $rrr->id_meli;
          $r->activo_meli = $rrr->activo_meli;
          $r->categoria_meli = $rrr->categoria_meli;
          $r->permalink = $rrr->permalink;
          $r->status = $rrr->status;
        }

        // Obtenemos los codigos de proveedores
        $sql = "SELECT AP.id_proveedor, AP.codigo, P.nombre FROM articulos_proveedores AP ";
        $sql.= "INNER JOIN proveedores P ON (AP.id_proveedor = P.id AND AP.id_empresa = P.id_empresa) ";
        $sql.= "WHERE AP.id_articulo = $r->id AND AP.id_empresa = $r->id_empresa ";
        $sql.= "ORDER BY AP.orden ASC ";
        $qqq = $this->db->query($sql);
        $r->proveedores = array();
        foreach($qqq->result() as $rrr) {
          $obj = new stdClass();
          $obj->id_proveedor = $rrr->id_proveedor;
          $obj->nombre = $rrr->nombre;
          $obj->codigo = $rrr->codigo;
          $r->proveedores[] = $obj;
        }

        $r->stock_almacenes = array();
        if ($buscar_stock == 1) {
          // Obtenemos el stock de cada almacen de ese articulo
          $q_almacenes = $this->db->query("SELECT * FROM almacenes WHERE id_empresa = $id_empresa ORDER BY nombre ASC");
          foreach($q_almacenes->result() as $almacen) {
            $sql = "SELECT ";
            $sql.= " IF (S.stock_actual IS NULL,0,S.stock_actual) AS stock_actual, ";
            $sql.= " IF (S.reservado IS NULL,0,S.reservado) AS reservado, ";
            $sql.= " IF (S.stock_minimo IS NULL,0,S.stock_minimo) AS stock_minimo ";
            $sql.= "FROM stock S ";
            $sql.= "WHERE S.id_empresa = $id_empresa ";
            $sql.= "AND S.id_articulo = $r->id ";
            $sql.= "AND S.id_sucursal = $almacen->id ";
            $sql.= "LIMIT 0,1";
            $qqq = $this->db->query($sql);
            $obj = new stdClass();
            $obj->nombre = $almacen->nombre;
            $obj->id_sucursal = $almacen->id;
            if ($qqq->num_rows() > 0) {
              $rrr = $qqq->row();
              $obj->stock_actual = $rrr->stock_actual;
              $obj->stock_minimo = $rrr->stock_minimo;
              $obj->reservado = $rrr->reservado;
            } else {
              $obj->stock_actual = 0;
              $obj->stock_minimo = 0;
              $obj->reservado = 0;
            }
            $r->stock_almacenes[] = $obj;
          }
        }

        $r->variantes = $this->get_variantes($r->id,array(
          "id_empresa"=>$id_empresa,
        ));

        // Si tiene etiquetas relacionadas
        $sql = "SELECT E.nombre ";
        $sql.= "FROM articulos_etiquetas_relacionadas ER INNER JOIN articulos_etiquetas E ON (ER.id_etiqueta = E.id AND ER.id_empresa = E.id_empresa) ";
        $sql.= "WHERE ER.id_articulo = $r->id AND ER.id_empresa = $id_empresa ";
        $sql.= "ORDER BY ER.orden ASC";
        $qq = $this->db->query($sql);
        $r->etiquetas = array();
        if ($qq->num_rows()>0) {
          foreach($qq->result() as $etiqueta) $r->etiquetas[] = $etiqueta->nombre;
        }
        $resultado[] = $r;
      }      
    } else if ($min == 1) {

      $resultado = array();
      foreach($q->result() as $r) {

        if ($buscar_solo_id) {
          $resultado[] = $r;
          continue;
        }

        // A cada producto, explotamos el codigo de barra 
        $r->codigos = array();
        $codigos = explode("###", $r->codigo_barra);
        if (sizeof($codigos)>0) {
          foreach($codigos as $cb) {
            $cb = trim($cb);
            if (!empty($cb)) $r->codigos[] = $cb;
          }
        }

        // Si esta compartido en MercadoLibre
        $r->activo_meli = -1; // -1 indica que nunca fue subido
        $r->categoria_meli = "";
        $r->permalink = "";
        $r->status = "";
        $r->id_meli = "";
        $qqq = $this->db->query("SELECT * FROM articulos_meli WHERE id_empresa = $id_empresa AND id_articulo = $r->id ");
        if ($qqq->num_rows()>0) {
          $rrr = $qqq->row();
          $r->id_meli = $rrr->id_meli;
          $r->activo_meli = $rrr->activo_meli;
          $r->categoria_meli = $rrr->categoria_meli;
          $r->permalink = $rrr->permalink;
          $r->status = $rrr->status;
        }

        // Obtenemos los ingredientes de ese producto
        $sql = "SELECT E.nombre, E.valores, E.adicional, E.activo ";
        $sql.= "FROM articulos_ingredientes E ";
        $sql.= "WHERE E.id_articulo = $r->id AND E.id_empresa = $id_empresa ";
        $sql.= "ORDER BY E.orden ASC ";
        $qq = $this->db->query($sql);
        $r->ingredientes = array();
        foreach($qq->result() as $rr) {
          $r->ingredientes[] = $rr;
        } 

        $resultado[] = $r;
      }      
    }
    return array(
      "results"=>$resultado,
      "total"=>$total->total,
      "sql"=>$sql_main,
      "filter"=>$filter,
    );
  }
  
  function count_actives() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM articulos WHERE id_empresa = $id_empresa AND eliminado = 0 ");
    $r = $q->row();
    return $r->cantidad;
  }
  
  function next($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $q = $this->db->query("SELECT IF(MAX(CAST(codigo AS UNSIGNED)) IS NULL,0,MAX(CAST(codigo AS UNSIGNED))) AS codigo FROM articulos WHERE id_empresa = $id_empresa");
    $r = $q->row();
    return ((int)$r->codigo + 1);
  }
  
  function save_tag($tag) {
    $this->load->helper("file_helper");
    // Primero controlamos si existe la etiqueta
    $q = $this->db->query("SELECT * FROM articulos_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
    if ($q->num_rows()<=0) {
      // Si no existe, la guardamos
      $link = filename($tag->nombre,"-",0);
      $this->db->query("INSERT INTO articulos_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
      $id_etiqueta = $this->db->insert_id();
    } else {
      $row = $q->row();
      $id_etiqueta = $row->id;
    }
    $q = $this->db->query("SELECT * FROM articulos_etiquetas_relacionadas WHERE id_empresa = $tag->id_empresa AND id_articulo = $tag->id_articulo AND id_etiqueta = $id_etiqueta");
    if ($q->num_rows()==0) {
      $this->db->query("INSERT INTO articulos_etiquetas_relacionadas (id_empresa,id_articulo,id_etiqueta) VALUES ($tag->id_empresa,$tag->id_articulo,$id_etiqueta) ");  
    }
  }

  function save_variante($v) {
    if ($v->id_propiedad_1 != 0) {
      $sql = "SELECT * FROM articulos_propiedades_opciones ";
      $sql.= "WHERE id_empresa = $v->id_empresa ";
      $sql.= "AND id_propiedad = $v->id_propiedad_1 ";
      $sql.= "AND nombre = '$v->nombre_opcion_1' ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        // La opcion existe, tomamos el ID
        $opcion = $q->row();
        $v->id_opcion_1 = $opcion->id;
      } else {
        // La opcion no existe, la creamos
        $sql = "INSERT INTO articulos_propiedades_opciones (";
        $sql.= "id_empresa,id_propiedad,nombre,etiqueta) VALUES(";
        $sql.= "'$v->id_empresa','$v->id_propiedad_1','$v->nombre_opcion_1','$v->etiqueta_opcion_1')";
        $this->db->query($sql);
        $v->id_opcion_1 = $this->db->insert_id();
      }
    } else { 
      $v->id_opcion_1 = 0;
    }
    if ($v->id_propiedad_2 != 0) {
      $sql = "SELECT * FROM articulos_propiedades_opciones ";
      $sql.= "WHERE id_empresa = $v->id_empresa ";
      $sql.= "AND id_propiedad = $v->id_propiedad_2 ";
      $sql.= "AND nombre = '$v->nombre_opcion_2' ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        // La opcion existe, tomamos el ID
        $opcion = $q->row();
        $v->id_opcion_2 = $opcion->id;
      } else {
        // La opcion no existe, la creamos
        $sql = "INSERT INTO articulos_propiedades_opciones (";
        $sql.= "id_empresa,id_propiedad,nombre,etiqueta) VALUES(";
        $sql.= "'$v->id_empresa','$v->id_propiedad_2','$v->nombre_opcion_2','$v->etiqueta_opcion_2')";
        $this->db->query($sql);
        $v->id_opcion_2 = $this->db->insert_id();
      }
    } else {
      $v->id_opcion_2 = 0;
    }
    if ($v->id_propiedad_3 != 0) {
      $sql = "SELECT * FROM articulos_propiedades_opciones ";
      $sql.= "WHERE id_empresa = $v->id_empresa ";
      $sql.= "AND id_propiedad = $v->id_propiedad_3 ";
      $sql.= "AND nombre = '$v->nombre_opcion_3' ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        // La opcion existe, tomamos el ID
        $opcion = $q->row();
        $v->id_opcion_3 = $opcion->id;
      } else {
        // La opcion no existe, la creamos
        $sql = "INSERT INTO articulos_propiedades_opciones (";
        $sql.= "id_empresa,id_propiedad,nombre,etiqueta) VALUES(";
        $sql.= "'$v->id_empresa','$v->id_propiedad_3','$v->nombre_opcion_3','$v->etiqueta_opcion_3')";
        $this->db->query($sql);
        $v->id_opcion_3 = $this->db->insert_id();
      }
    } else {
      $v->id_opcion_3 = 0;
    }

    $sql = "SELECT * FROM articulos_variantes ";
    $sql.= "WHERE id_empresa = $v->id_empresa ";
    $sql.= "AND id_articulo = $v->id_articulo ";
    $sql.= "AND id_opcion_1 = $v->id_opcion_1 ";
    $sql.= "AND id_opcion_2 = $v->id_opcion_2 ";
    $sql.= "AND id_opcion_3 = $v->id_opcion_3 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $sql = "UPDATE articulos_variantes ";
      $sql.= "SET path = '$v->path', nombre = '$v->nombre', eliminado = 0 ";
      $sql.= "WHERE id_empresa = $v->id_empresa ";
      $sql.= "AND id_articulo = $v->id_articulo ";
      $sql.= "AND id_opcion_1 = $v->id_opcion_1 ";
      $sql.= "AND id_opcion_2 = $v->id_opcion_2 ";
      $sql.= "AND id_opcion_3 = $v->id_opcion_3 ";
      $this->db->query($sql);
    } else {
      $this->db->insert("articulos_variantes",array(
        "id_articulo"=>$v->id_articulo,
        "id_empresa"=>$v->id_empresa,
        "id_opcion_1"=>$v->id_opcion_1,
        "id_opcion_2"=>$v->id_opcion_2,
        "id_opcion_3"=>$v->id_opcion_3,
        "eliminado"=>0,
        "nombre"=>$v->nombre,
        "path"=>$v->path,
      ));      
    }
  }

  function get_by_sucursal($id,$id_empresa,$id_sucursal) {
    $articulo = $this->get($id,$id_empresa);
    if ($articulo === FALSE) return FALSE;
    foreach($articulo->precios_sucursales as $suc) {
      if ($suc->id_sucursal == $id_sucursal) {
        $articulo->fecha_mov = $suc->fecha_mov;
        $articulo->id_tipo_alicuota_iva = $suc->id_tipo_alicuota_iva;
        $articulo->porc_iva = $suc->porc_iva;
        $articulo->costo_iva = $suc->costo_iva;
        $articulo->costo_neto = $suc->costo_neto;
        $articulo->costo_final = $suc->costo_final;
        $articulo->porc_ganancia = $suc->porc_ganancia;
        $articulo->ganancia = $suc->ganancia;
        $articulo->precio_neto = $suc->precio_neto;
        $articulo->precio_final = $suc->precio_final;
        $articulo->porc_bonif = $suc->porc_bonif;
        $articulo->precio_final_dto = $suc->precio_final_dto;
        $articulo->costo_neto_inicial = $suc->costo_neto_inicial;
        $articulo->dto_prov = $suc->dto_prov;
      }
    }
    return $articulo;
  }

  // Version minificada de get
  function get_by_id($id,$conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $articulo = $q->row();
    return $articulo;
  }
  
  function get_variantes($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    // Consultamos todos los almacenes para obtener el stock
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $almacenes = $q->result();

    $sql = "SELECT DISTINCT AV.*, ";
    $sql.= " IF(APO_1.nombre IS NULL,'',APO_1.nombre) AS nombre_opcion_1, ";
    $sql.= " IF(APO_2.nombre IS NULL,'',APO_2.nombre) AS nombre_opcion_2, ";
    $sql.= " IF(APO_3.nombre IS NULL,'',APO_3.nombre) AS nombre_opcion_3, ";
    $sql.= " IF(APO_1.etiqueta IS NULL,'',APO_1.etiqueta) AS etiqueta_opcion_1, ";
    $sql.= " IF(APO_2.etiqueta IS NULL,'',APO_2.etiqueta) AS etiqueta_opcion_2, ";
    $sql.= " IF(APO_3.etiqueta IS NULL,'',APO_3.etiqueta) AS etiqueta_opcion_3, ";
    $sql.= " IF(AP_1.nombre IS NULL,'',AP_1.nombre) AS nombre_propiedad_1, ";
    $sql.= " IF(AP_2.nombre IS NULL,'',AP_2.nombre) AS nombre_propiedad_2, ";
    $sql.= " IF(AP_3.nombre IS NULL,'',AP_3.nombre) AS nombre_propiedad_3, ";
    $sql.= " IF(AP_1.id IS NULL,0,AP_1.id) AS id_propiedad_1, ";
    $sql.= " IF(AP_2.id IS NULL,0,AP_2.id) AS id_propiedad_2, ";
    $sql.= " IF(AP_3.id IS NULL,0,AP_3.id) AS id_propiedad_3 ";
    $sql.= "FROM articulos_variantes AV ";
    $sql.= " LEFT JOIN articulos_propiedades_opciones APO_1 ON (AV.id_opcion_1 = APO_1.id AND AV.id_empresa = APO_1.id_empresa) ";
    $sql.= " LEFT JOIN articulos_propiedades AP_1 ON (APO_1.id_propiedad = AP_1.id) ";
    $sql.= " LEFT JOIN articulos_propiedades_opciones APO_2 ON (AV.id_opcion_2 = APO_2.id AND AV.id_empresa = APO_2.id_empresa) ";
    $sql.= " LEFT JOIN articulos_propiedades AP_2 ON (APO_2.id_propiedad = AP_2.id) ";
    $sql.= " LEFT JOIN articulos_propiedades_opciones APO_3 ON (AV.id_opcion_3 = APO_3.id AND AV.id_empresa = APO_3.id_empresa) ";
    $sql.= " LEFT JOIN articulos_propiedades AP_3 ON (APO_3.id_propiedad = AP_3.id) ";
    $sql.= "WHERE AV.id_articulo = $id ";
    $sql.= "AND AV.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AV.id ASC ";
    $q = $this->db->query($sql);
    $variantes = array();
    foreach($q->result() as $r) {

      $r->stock_almacenes = array();
      foreach($almacenes as $alm) {
        // Ademas consultamos el stock de la variante
        $sql = "SELECT * FROM stock_variantes ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_variante = $r->id ";
        $sql.= "AND id_articulo = $r->id_articulo ";
        $sql.= "AND id_sucursal = $alm->id ";
        $qq = $this->db->query($sql);
        $sv = new stdClass();
        if ($qq->num_rows() == 0) {
          $sv->stock_actual = 0;
          $sv->reservado = 0;
        } else {
          $rr = $qq->row();
          $sv->stock_actual = $rr->stock_actual;
          $sv->reservado = $rr->reservado;
        }
        $sv->id_sucursal = $alm->id;
        $sv->sucursal = $alm->nombre;
        $r->stock_almacenes[] = $sv;
      }

      // Finalmente agregamos el objeto al array
      $variantes[] = $r;
    }
    return $variantes;
  }
  
  function get($id,$id_empresa = 0) {
    $id_empresa = ($id_empresa != 0) ? $id_empresa : parent::get_empresa();
    // Obtenemos los datos del articulo
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
    $sql.= "IF(A.fecha_eliminado='0000-00-00','',DATE_FORMAT(A.fecha_eliminado,'%d/%m/%Y')) AS fecha_eliminado, ";
    $sql.= "IF(PRO.nombre IS NULL,'',PRO.nombre) AS promocion, ";
    $sql.= "IF(PRO.path IS NULL,'',PRO.path) AS promocion_path, ";
    $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "LEFT JOIN promociones PRO ON (A.id_promocion = PRO.id AND A.id_empresa = PRO.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $articulo = $q->row();

    // Obtenemos los clientes de ese articulo
    $sql = "SELECT AP.*, P.nombre FROM articulos_clientes AP ";
    $sql.= "INNER JOIN clientes P ON (AP.id_cliente = P.id AND AP.id_empresa = P.id_empresa) ";
    $sql.= "WHERE AP.id_articulo = $id AND AP.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $articulo->clientes = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id_cliente = $r->id_cliente;
      $obj->nombre = $r->nombre;
      $obj->codigo = $r->codigo;
      $articulo->clientes[] = $obj;
    }

    // Obtenemos los proveedores de ese articulo
    $sql = "SELECT AP.*, P.nombre FROM articulos_proveedores AP ";
    $sql.= "INNER JOIN proveedores P ON (AP.id_proveedor = P.id AND AP.id_empresa = P.id_empresa) ";
    $sql.= "WHERE AP.id_articulo = $id AND AP.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AP.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->proveedores = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id_proveedor = $r->id_proveedor;
      $obj->nombre = $r->nombre;
      $obj->codigo = $r->codigo;
      $articulo->proveedores[] = $obj;
    }
    
    $sql = "SELECT AP.*, P.nombre FROM articulos_marcas_vehiculos AP ";
    $sql.= "INNER JOIN marcas_vehiculos P ON (AP.id_marca_vehiculo = P.id AND AP.id_empresa = P.id_empresa) ";
    $sql.= "WHERE AP.id_articulo = $id AND AP.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AP.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->marcas_vehiculos = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id_marca_vehiculo = $r->id_marca_vehiculo;
      $obj->nombre = $r->nombre;
      $obj->modelo = $r->modelo;
      $articulo->marcas_vehiculos[] = $obj;
    }

    // Obtenemos los articulos relacionados con ese producto
    $sql = "SELECT A.id, A.nombre, A.path, AR.destacado ";
    $sql.= "FROM articulos A INNER JOIN articulos_relacionados AR ON (A.id = AR.id_relacion AND A.id_empresa = AR.id_empresa) ";
    $sql.= "WHERE AR.id_articulo = $id AND AR.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $obj->path = $r->path;
      $obj->destacado = $r->destacado;
      $articulo->relacionados[] = $obj;
    }
    
    // Obtenemos las categorias relacionados con ese producto
    $sql = "SELECT R.id, R.nombre ";
    $sql.= "FROM rubros R INNER JOIN articulos_relacionados AR ON (R.id = AR.id_rubro AND R.id_empresa = AR.id_empresa) ";
    $sql.= "WHERE AR.id_articulo = $id AND AR.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->rubros_relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $articulo->rubros_relacionados[] = $obj;
    }
    
    // Obtenemos las etiquetas de ese producto
    $sql = "SELECT E.nombre ";
    $sql.= "FROM articulos_etiquetas E INNER JOIN articulos_etiquetas_relacionadas ER ON (E.id = ER.id_etiqueta AND E.id_empresa = ER.id_empresa) ";
    $sql.= "WHERE ER.id_articulo = $id AND E.id_empresa = $id_empresa ";
    $sql.= "ORDER BY ER.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->etiquetas = array();
    foreach($q->result() as $r) {
      $articulo->etiquetas[] = $r->nombre;
    }  

    // Obtenemos los ingredientes de ese producto
    $sql = "SELECT E.nombre, E.valores, E.adicional, E.activo ";
    $sql.= "FROM articulos_ingredientes E ";
    $sql.= "WHERE E.id_articulo = $id AND E.id_empresa = $id_empresa ";
    $sql.= "ORDER BY E.orden ASC ";
    $q = $this->db->query($sql);
    $articulo->ingredientes = array();
    foreach($q->result() as $r) {
      $articulo->ingredientes[] = $r;
    } 

    // Obtenemos los componentes de ese producto
    $sql = "SELECT A.nombre, A.path, E.* ";
    $sql.= "FROM articulos_componentes E INNER JOIN articulos A ON (A.id_empresa = E.id_empresa AND A.id = E.id_articulo_componente) ";
    $sql.= "WHERE E.id_articulo = $id AND E.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $articulo->componentes = array();
    foreach($q->result() as $r) {
      $articulo->componentes[] = $r;
    } 

    // Obtenemos los atributos especificos
    $sql = "SELECT * ";
    $sql.= "FROM articulos_atributos E ";
    $sql.= "WHERE E.id_articulo = $id AND E.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $articulo->atributos = array();
    foreach($q->result() as $r) {
      $articulo->atributos[] = $r;
    } 

    // Obtenemos el stock de cada almacen de ese articulo
    $articulo->stock_almacenes = array();
    $q_almacenes = $this->db->query("SELECT * FROM almacenes WHERE id_empresa = $id_empresa ");
    foreach($q_almacenes->result() as $almacen) {
      $sql = "SELECT ";
      $sql.= " IF (S.stock_actual IS NULL,0,S.stock_actual) AS stock_actual, ";
      $sql.= " IF (S.reservado IS NULL,0,S.reservado) AS reservado, ";
      $sql.= " IF (S.stock_minimo IS NULL,0,S.stock_minimo) AS stock_minimo ";
      $sql.= "FROM stock S ";
      $sql.= "WHERE S.id_empresa = $id_empresa ";
      $sql.= "AND S.id_articulo = $articulo->id ";
      $sql.= "AND S.id_sucursal = $almacen->id ";
      $sql.= "LIMIT 0,1";
      $qqq = $this->db->query($sql);
      $obj = new stdClass();
      $obj->nombre = $almacen->nombre;
      $obj->id_sucursal = $almacen->id;
      if ($qqq->num_rows() > 0) {
        $rrr = $qqq->row();
        $obj->stock_actual = $rrr->stock_actual;
        $obj->stock_minimo = $rrr->stock_minimo;
        $obj->reservado = $rrr->reservado;
      } else {
        $obj->stock_actual = 0;
        $obj->stock_minimo = 0;
        $obj->reservado = 0;
      }
      $articulo->stock_almacenes[] = $obj;
    }

    // Obtenemos las variantes del articulo
    $articulo->variantes = $this->get_variantes($id,array(
      "id_empresa"=>$id_empresa
    ));
    
    // Obtenemos las imagenes de ese articulo
    $sql = "SELECT AI.* FROM articulos_images AI ";
    $sql.= "WHERE AI.id_articulo = $id AND AI.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $articulo->images = array();
    foreach($q->result() as $r) {
      $articulo->images[] = $r->path;
    }

    // Obtenemos las imagenes de ese articulo
    $sql = "SELECT AI.* FROM articulos_images_meli AI ";
    $sql.= "WHERE AI.id_articulo = $id AND AI.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $articulo->images_meli = array();
    foreach($q->result() as $r) {
      $articulo->images_meli[] = $r->path;
    }

    $articulo->profesionales = array();
    $sql_2 = "SELECT AP.*, U.nombre, C.nombre as nombre_categoria from articulos_profesionales AP INNER JOIN com_usuarios U ON (U.id=AP.id_usuario) INNER JOIN categorias_entrena C ON (C.id = AP.id_categoria) WHERE AP.id_articulo='$id'";
    $query = $this->db->query($sql_2);
    foreach ($query->result() as $fila) {
      $articulo->profesionales[]=$fila;
    }
    // Obtenemos los precios de las sucursales
    $sql = "SELECT AI.* FROM articulos_precios_sucursales AI ";
    $sql.= "WHERE AI.id_articulo = $id AND AI.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $articulo->precios_sucursales = array();
    foreach($q->result() as $r) {
      $articulo->precios_sucursales[] = $r;
    }

    // Buscamos si el articulo esta compartido en MERCADOLIBRE
    $articulo_meli = $this->get_articulo_meli($id,array(
      "id_empresa"=>$id_empresa,
    ));

    $sql = "SELECT ml_recargo_precio, ml_texto_empresa, ml_lista_base FROM web_configuracion WHERE id_empresa = $id_empresa LIMIT 0,1";
    $qw = $this->db->query($sql);
    $web_conf = $qw->row();

    if ($articulo_meli !== FALSE) {
      $articulo->id_meli = $articulo_meli->id_meli;
      $articulo->permalink = $articulo_meli->permalink;
      $articulo->fecha_publicacion = $articulo_meli->fecha_publicacion;
      $articulo->titulo_meli = $articulo_meli->titulo_meli;
      $articulo->atributos_meli = $articulo_meli->atributos_meli;
      
      //$articulo->texto_meli = $articulo_meli->texto_meli;
      $articulo->texto_meli = strip_tags($articulo->texto)."\n\n".$web_conf->ml_texto_empresa;

      $articulo->precio_meli = $articulo_meli->precio_meli;
      $articulo->categoria_meli = $articulo_meli->categoria_meli;
      $articulo->activo_meli = $articulo_meli->activo_meli;
      $articulo->list_type_id = $articulo_meli->list_type_id;
      $articulo->forma_envio_meli = $articulo_meli->forma_envio_meli;
      $articulo->forma_pago_meli = $articulo_meli->forma_pago_meli;
      $articulo->retiro_sucursal_meli = $articulo_meli->retiro_sucursal_meli;
      $articulo->status = $articulo_meli->status;
    } else {

      $articulo->id_meli = "";
      $articulo->permalink = "";
      $articulo->fecha_publicacion = "";
      $articulo->atributos_meli = "";
      $articulo->titulo_meli = ($id_empresa == 342) ? $articulo->custom_1 : $articulo->nombre;
      $articulo->texto_meli = strip_tags($articulo->texto)."\n\n".$web_conf->ml_texto_empresa;

      // Dependiendo de lo que este configurado
      $precio_final_dto = $articulo->precio_final_dto;
      if ($web_conf->ml_lista_base == 1) $precio_final_dto = $articulo->precio_final_dto_2;
      else if ($web_conf->ml_lista_base == 2) $precio_final_dto = $articulo->precio_final_dto_3;
      else if ($web_conf->ml_lista_base == 3) $precio_final_dto = $articulo->precio_final_dto_4;
      else if ($web_conf->ml_lista_base == 4) $precio_final_dto = $articulo->precio_final_dto_5;
      else if ($web_conf->ml_lista_base == 5) $precio_final_dto = $articulo->precio_final_dto_6;

      $articulo->precio_meli = $precio_final_dto * (1 + ($web_conf->ml_recargo_precio / 100));
      $articulo->categoria_meli = "";
      $articulo->activo_meli = -1; // Todavia no fue compartido
      $articulo->list_type_id = 0;
      $articulo->forma_envio_meli = "";
      $articulo->forma_pago_meli = "";
      $articulo->status = "";
      $articulo->retiro_sucursal_meli = 0;
    }

    return $articulo;
  }

  function get_by_codigo_barra($codigo,$conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    // Obtenemos los datos del articulo
    $codigo = (int)$codigo;
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
    $sql.= "IF(A.fecha_eliminado='0000-00-00','',DATE_FORMAT(A.fecha_eliminado,'%d/%m/%Y')) AS fecha_eliminado, ";
    $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "WHERE A.codigo_barra LIKE '%$codigo%' AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $articulo = $q->row();      
    } else {
      $articulo = FALSE;
    }
    return $articulo;
  }

  function get_by_codigo($codigo,$conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    // Obtenemos los datos del articulo
    $codigo = (int)$codigo;
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
    $sql.= "IF(A.fecha_eliminado='0000-00-00','',DATE_FORMAT(A.fecha_eliminado,'%d/%m/%Y')) AS fecha_eliminado, ";
    $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "WHERE A.codigo = $codigo AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $articulo = $q->row();      
    } else {
      $articulo = FALSE;
    }
    return $articulo;
  }

  // La diferencia con get_by_codigo es que el codigo es String
  function get_by_codigo_string($codigo,$conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
    $sql.= "IF(A.fecha_eliminado='0000-00-00','',DATE_FORMAT(A.fecha_eliminado,'%d/%m/%Y')) AS fecha_eliminado, ";
    $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "WHERE A.codigo = '$codigo' AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $articulo = $q->row();      
    } else {
      $articulo = FALSE;
    }
    return $articulo;
  }

  function get_nombre($id,$conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    // Obtenemos los datos del articulo
    $sql = "SELECT A.*, ";
    $sql.= "IF(A.fecha_mov='0000-00-00','',DATE_FORMAT(A.fecha_mov,'%d/%m/%Y')) AS fecha_mov, ";
    $sql.= "IF(A.fecha_eliminado='0000-00-00','',DATE_FORMAT(A.fecha_eliminado,'%d/%m/%Y')) AS fecha_eliminado, ";
    $sql.= "IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= "LEFT JOIN marcas M ON (A.id_marca = M.id AND A.id_empresa = M.id_empresa) ";
    $sql.= "WHERE A.id = $id AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $articulo = $q->row();      
    } else {
      $articulo = FALSE;
    }
    return $articulo;
  }
  
  function get_primer_proveedor($id_articulo) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el primer proveedor de ese articulo
    $sql = "SELECT AP.*, P.nombre FROM articulos_proveedores AP ";
    $sql.= "INNER JOIN proveedores P ON (AP.id_proveedor = P.id AND AP.id_empresa = P.id_empresa) ";
    $sql.= "WHERE AP.id_articulo = $id_articulo ";
    $sql.= "AND P.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AP.orden ASC ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $proveedor = $q->row();
    } else {
      $proveedor = FALSE;
    }
    return $proveedor;
  }
  
  // PRODUCTOS CON MAS GANANCIA
  function mayor_ganancia($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $not_in_articulos = isset($conf["not_in_articulos"]) ? $conf["not_in_articulos"] : "";
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $in_sucursales = isset($conf["in_sucursales"]) ? $conf["in_sucursales"] : "";
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT A.codigo, A.nombre, ";
    $sql.= " ROUND(SUM(FI.cantidad),0) AS cantidad,  ";
    $sql.= " ROUND(SUM(FI.total_con_iva),0) AS venta,  ";
    $sql.= " ROUND(SUM(FI.costo_final),0) AS costo,  ";
    $sql.= " ROUND(SUM(FI.total_con_iva) - SUM(FI.costo_final),2) AS diferencia ";
    $sql.= "FROM facturas_items FI ";
    $sql.= " INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
    $sql.= " INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.anulado = 0 ";
    $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql.= "AND F.id_punto_venta != 0 ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
    if (!empty($desde)) $sql.= "AND F.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha <= '$hasta' ";
    if (!empty($not_in_articulos)) $sql.= "AND FI.id_articulo NOT IN ($not_in_articulos) ";
    $sql.= "AND F.id_tipo_estado != 7 ";
    $sql.= "GROUP BY FI.id_articulo ";
    $sql.= "ORDER BY diferencia DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  // PRODUCTOS MAS VENDIDOS
  function mas_vendidos($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id_proyecto = isset($conf["id_proyecto"]) ? $conf["id_proyecto"] : 0;
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $in_sucursales = isset($conf["in_sucursales"]) ? $conf["in_sucursales"] : "";
    $not_in_articulos = isset($conf["not_in_articulos"]) ? $conf["not_in_articulos"] : "";
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT A.codigo, A.nombre, ";
    $sql.= " ROUND(SUM(FI.cantidad),0) AS cantidad,  ";
    $sql.= " ROUND(SUM(FI.total_con_iva),0) AS venta,  ";
    $sql.= " ROUND(SUM(FI.costo_final),0) AS costo,  ";
    $sql.= " ROUND(SUM(FI.total_con_iva) - SUM(FI.costo_final),2) AS diferencia ";
    $sql.= "FROM facturas_items FI ";
    $sql.= " INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
    $sql.= " INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.anulado = 0 ";
    $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    if ($id_proyecto == 1) $sql.= "AND F.id_punto_venta != 0 ";
    else $sql.= "AND F.id_tipo_estado = 6 ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
    if (!empty($desde)) $sql.= "AND F.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha <= '$hasta' ";
    if (!empty($not_in_articulos)) $sql.= "AND FI.id_articulo NOT IN ($not_in_articulos) ";
    $sql.= "AND F.id_tipo_estado != 7 ";
    $sql.= "GROUP BY FI.id_articulo ";
    $sql.= "ORDER BY SUM(cantidad) DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    return $q->result();
  }
  
  /**
   * Guarda la relacion entre articulos_proveedores
   * Controla si existe primero, y luego inserta o actualiza
   */
  function articulos_proveedores($id_proveedor,$id_articulo,$codigo = 0) {
    
    $id_empresa = parent::get_empresa();
    $articulo = $this->get($id_articulo);
    
    // Consultamos si ya existe la relacion
    $sql = "SELECT * FROM articulos_proveedores AP ";
    $sql.= "WHERE AP.id_proveedor = $id_proveedor ";
    $sql.= "AND AP.id_articulo = $articulo->id ";
    $sql.= "AND AP.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) {
      // Debemos agregar la relacion
      $this->db->insert("articulos_proveedores",array(
        "id_proveedor"=>$id_proveedor,
        "id_articulo"=>$articulo->id,
        "id_empresa"=>$id_empresa,
        "codigo"=>$codigo,
        "orden"=>0,
        "costo_neto"=>$articulo->costo_neto,
        "costo_final"=>$articulo->costo_final,
        "precio_neto"=>$articulo->precio_neto,
        "precio_final"=>$articulo->precio_final,
      ));
    }
  }
  
  
  function delete($id) {
    // Controlamos que se este borrando un articulo que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM articulos WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {

      // TODO: En caso de que el producto este compartido con MercadoLibre, debemos poner
      // la publicacion status = closed
      $this->db->query("DELETE FROM articulos_precios_sucursales WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_meli WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_variantes WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_ingredientes WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_marcas_vehiculos WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_proveedores WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_relacionados WHERE id_articulo = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM articulos_images WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM articulos_images_meli WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM articulos_etiquetas_relacionadas WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM stock_variantes WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM stock WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM stock_movimientos WHERE id_articulo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM articulos WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

  function save($data) {
    $profesionales = $data->profesionales;
    $id = parent::save($data);
    $id_articulo = ($data->id != 0) ? $data->id : $id;
    $sql = "DELETE FROM articulos_profesionales where id_articulo = $id_articulo ";
    $this->db->query($sql);
    foreach ($profesionales as $u) {
      $sql_clientes = "INSERT INTO articulos_profesionales (id_usuario, id_articulo, id_categoria, id_empresa) 
      VALUES ('$u->id_usuario', '$id_articulo', $u->id_categoria, 1319)";
      $this->db->query($sql_clientes);
    }

    return $id;
  } 

}