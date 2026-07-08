<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Articulos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Articulo_Model', 'modelo');
  }

  function actualizar_images() {
    $id_empresa = 1296;
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $id = $r->id;
      $sql = "SELECT * FROM articulos_images WHERE id_empresa = $id_empresa AND id_articulo = $id ORDER BY orden ASC ";
      $q_images = $this->db->query($sql);
      if ($q_images->num_rows() > 0) {
        $img = $q_images->row();
        $sql = "UPDATE articulos SET path = '$img->path' WHERE id = $id AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      }
    }
    echo "TERMINO";
  }

  // Esta funcion se ejecuta en un cronjob, sirve para volver a habilitar todas las variantes desactivadas de toque
  function habilitar_variantes() {
    $sql = "UPDATE articulos_ingredientes SET activo = 1 WHERE id_empresa = 571";
    $this->db->query($sql);
  }

  function reemplazar_articulos() {
    $id_empresa = 249;
    $sql = "SELECT * FROM articulos_2 WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    foreach($q->result() as $art) {
      $sql = "UPDATE articulos SET costo_final = $art->costo_final, ";
      $sql.= " costo_neto = $art->costo_neto, ";
      $sql.= " precio_final = $art->precio_final, ";
      $sql.= " precio_final_dto = $art->precio_final_dto, ";
      $sql.= " precio_final_2 = $art->precio_final_2, ";
      $sql.= " precio_final_dto_2 = $art->precio_final_dto_2, ";
      $sql.= " precio_final_3 = $art->precio_final_3, ";
      $sql.= " precio_final_dto_3 = $art->precio_final_dto_3, ";
      $sql.= " porc_ganancia = $art->porc_ganancia, ";
      $sql.= " porc_ganancia_2 = $art->porc_ganancia_2, ";
      $sql.= " porc_ganancia_3 = $art->porc_ganancia_3 ";
      $sql.= "WHERE id_empresa = $id_empresa AND id = $art->id ";
      $this->db->query($sql);

      $sql = "SELECT * FROM articulos_precios_sucursales_2 ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_articulo = $art->id ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $rr) {
        $sql = "UPDATE articulos_precios_sucursales SET costo_final = $rr->costo_final, ";
        $sql.= " costo_neto = $rr->costo_neto, ";
        $sql.= " precio_final = $rr->precio_final, ";
        $sql.= " precio_final_dto = $rr->precio_final_dto, ";
        $sql.= " precio_final_2 = $rr->precio_final_2, ";
        $sql.= " precio_final_dto_2 = $rr->precio_final_dto_2, ";
        $sql.= " precio_final_3 = $rr->precio_final_3, ";
        $sql.= " precio_final_dto_3 = $rr->precio_final_dto_3, ";
        $sql.= " porc_ganancia = $rr->porc_ganancia, ";
        $sql.= " porc_ganancia_2 = $rr->porc_ganancia_2, ";
        $sql.= " porc_ganancia_3 = $rr->porc_ganancia_3 ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_articulo = $art->id AND id_sucursal = $rr->id_sucursal ";
        $this->db->query($sql);
      }
    }
    echo "TERMINO";
  }  

  // Esto se ejecuta en un cron de varcreative porque en la PC de javier no tomaba el https
  function bajar_precios_maximos() {
    $s = file_get_contents("https://preciosmaximos.argentina.gob.ar/api/products?pag=1&Provincia=Buenos%20Aires&regs=10000000");
    file_put_contents("/home/ubuntu/data/products.json", $s);
  }

  function comparar_precios_maximos() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $excel = parent::get_get("excel",1);
    $id_empresa = parent::get_empresa();
    // Lo traemos de varcreative porque no tomaba el https
    $result = file_get_contents("http://www.varcreative.com/products.json");
    $r = json_decode($result,true);
    $articulos = array();
    for($i=0;$i<sizeof($r["result"]);$i++) {
      $p = $r["result"][$i];
      $codigo_barra = str_replace("-", "", $p["id_producto"]);
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "WHERE A.codigo_barra = '$codigo_barra' AND A.id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $articulo = $q->row();      
        $articulo->precio_final_dto = (float)$articulo->precio_final_dto;
        $articulo->precio_maximo = (float)$p["Precio sugerido"];
        $obj = new stdClass();
        $obj->codigo = $articulo->codigo;
        $obj->codigo_barra = $articulo->codigo_barra;
        $obj->nombre = $articulo->nombre;
        $obj->precio_final_dto = $articulo->precio_final_dto;
        $obj->precio_maximo = $articulo->precio_maximo;
        $obj->diferencia = $articulo->precio_final_dto - $articulo->precio_maximo;
        $articulos[] = $obj;
      }
    }
    if ($excel == 1) {
      $this->load->library("Excel");
      $this->excel->create(array(
        "date"=>"",
        "filename"=>"precios_maximos",
        "footer"=>array(),
        "header"=>array("Codigo","Barra","Nombre","Precio","Maximo","Diferencia"),
        "data"=>$articulos,
        "title"=>"Listado de Precios Maximos",
      ));
    } else {
      $this->load->view("reports/precios_maximos",array(
        "resultados"=>$articulos,
      ));
    }
  }
  
  function aumentar_por_rubro() {
    $f = file_get_contents("rubros.csv");
    $hoy = date("Y-m-d");
    $lineas = explode("\n", $f);
    foreach ($lineas as $linea) {
      $campos = explode(';', $linea);
      $id_rubro = $campos[0];
      if (empty($id_rubro)) continue;
      $porc = $campos[2];
      $porc = (100+$porc)/100;
      $sql = "UPDATE articulos SET precio_neto = precio_neto * $porc, precio_final = precio_final * $porc, precio_final_dto = precio_final_dto * $porc, fecha_mov = '$hoy' ";
      $sql.= "WHERE id_empresa = 574 and id_rubro = $id_rubro";
      echo $sql.";<br/>";
    }
  }

  function restablecer_codigos() {    
    $id_empresa = 574;
    $sql = "SELECT * FROM tato ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "UPDATE articulos SET codigo = '$r->codigo', codigo_barra = '$r->codigo_barra' WHERE id_empresa = $id_empresa AND id = $r->id ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  function crear_barcode() {
    $id_empresa = 620;
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $this->load->helper("ean_helper");
    foreach($q->result() as $r) {
      $codigo = str_pad($r->codigo, 12, "0", STR_PAD_LEFT);
      $codigo_barra = $codigo.ean13_checksum($codigo);
      $sql = "UPDATE articulos SET codigo_barra = '$codigo_barra' WHERE id_empresa = $r->id_empresa AND id = $r->id ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  function calcular_promedio_por_cliente() {
    $id_empresa = parent::get_get("id_empresa",parent::get_empresa());
    $cant_semanas = parent::get_get("cant_semanas",8);
    if ($cant_semanas <= 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"La cantidad de semanas no puede ser 0.",
      ));
      exit();
    }

    $fecha_desde = date('Y-m-d', strtotime("-".$cant_semanas." week"));

    // Recorremos los clientes
    $sql = "SELECT id FROM clientes WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $cliente) {

      // Recorremos los articulos
      $sql = "SELECT id FROM articulos WHERE id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $articulo) {

        // Calculamos cuantos articulos le vendieron a ese cliente en ese periodo de tiempo
        $sql = "SELECT ";
        $sql.= " SUM(IF(FI.tipo_cantidad = '' || FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'C',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cambio, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS bonificado, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS devolucion ";
        $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (F.id = FI.id_factura AND F.id_punto_venta = FI.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
        $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
        $sql.= "WHERE F.id_cliente = $cliente->id ";
        $sql.= "AND F.id_empresa = $id_empresa ";
        $sql.= "AND F.fecha >= '$fecha_desde' ";
        $sql.= "AND FI.id_articulo = $articulo->id ";
        $qqq = $this->db->query($sql);
        $rr = $qqq->row();
        $rr->cantidad = is_null($rr->cantidad) ? 0 : $rr->cantidad;
        $rr->bonificado = is_null($rr->bonificado) ? 0 : $rr->bonificado;
        $rr->devolucion = is_null($rr->devolucion) ? 0 : $rr->devolucion;
        $rr->cambio = is_null($rr->cambio) ? 0 : $rr->cambio;

        $promedio = ($rr->cantidad + $rr->bonificado - $rr->devolucion - $rr->cambio) / $cant_semanas;

        // Buscamos la ultima venta
        $fecha_ultima_venta = '0000-00-00';
        $ultima_venta = 0;
        $sql = "SELECT F.fecha, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'C',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cambio, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS bonificado, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS devolucion ";
        $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (F.id = FI.id_factura AND F.id_punto_venta = FI.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
        $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
        $sql.= "WHERE F.id_cliente = $cliente->id ";
        $sql.= "AND F.id_empresa = $id_empresa ";
        $sql.= "AND F.fecha >= '$fecha_desde' ";
        $sql.= "AND FI.id_articulo = $articulo->id ";
        $sql.= "GROUP BY F.fecha ";
        $sql.= "ORDER BY F.fecha DESC ";
        $qqq = $this->db->query($sql);
        if ($qqq->num_rows() > 0) {
          $rrr = $qqq->row();
          $fecha_ultima_venta = $rrr->fecha;
          $ultima_venta = ($rrr->cantidad + $rrr->bonificado - $rrr->devolucion - $rrr->cambio);
        }

        // Buscamos el registro en la tabla
        $sql = "SELECT * FROM articulos_clientes WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $articulo->id AND id_cliente = $cliente->id ";
        $qqq = $this->db->query($sql);
        if ($qqq->num_rows() > 0) {
          // Actualizamos el registro
          $sql = "UPDATE articulos_clientes ";
          $sql.= "SET promedio_venta = $promedio, ";
          $sql.= " fecha_ultima_venta = '$fecha_ultima_venta', ultima_venta = '$ultima_venta' ";
          $sql.= "WHERE id_empresa = $id_empresa ";
          $sql.= "AND id_cliente = $cliente->id ";
          $sql.= "AND id_articulo = $articulo->id ";
        } else {
          // Lo insertamos
          $sql = "INSERT INTO articulos_clientes (id_empresa,id_articulo,id_cliente,promedio_venta,fecha_ultima_venta,ultima_venta) VALUES (";
          $sql.= "'$id_empresa','$articulo->id','$cliente->id','$promedio','$fecha_ultima_venta','$ultima_venta')";
        }
        $this->db->query($sql);
      }
    }
    
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Los promedios de venta por cliente se han actualizado correctamente.",
    ));
  }

  function comparar_articulos() {
    $id_empresa = 574;
    $sql = "SELECT id,precio_final_dto,nombre FROM articulos WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    foreach($q->result() as $art) {
      $sql = "SELECT * FROM articulos_3 WHERE id_empresa = $id_empresa AND id = $art->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $art2 = $qq->row();
        if ($art2->precio_final_dto > $art->precio_final_dto) {
          echo $art->codigo." $art->nombre VIEJO: ".$art2->precio_final_dto." NUEVO: ".$art->precio_final_dto."<br/>";
          $sql = "UPDATE articulos SET ";
          $sql.= "costo_neto = $art2->costo_neto, ";
          $sql.= "costo_final = $art2->costo_final, ";
          $sql.= "precio_neto = $art2->precio_neto, ";
          $sql.= "precio_final = $art2->precio_final, ";
          $sql.= "porc_ganancia = $art2->porc_ganancia, ";
          $sql.= "ganancia = $art2->ganancia, ";
          $sql.= "precio_final_dto = $art2->precio_final_dto ";
          $sql.= "WHERE id_empresa = $id_empresa AND id = $art2->id ";
          $this->db->query($sql);
        }
      }
    }
    echo "TERMINO";
  }

  function copiar_precios_sucursales() {
    $id_empresa = 249;
    $id_sucursal = 21;
    $id_sucursal_destino = 1037;
    $i = 0;
    $sql = "SELECT * FROM articulos_precios_sucursales WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      // Controlamos si existe primero
      $sql = "SELECT * FROM articulos_precios_sucursales WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal_destino AND id_articulo = $row->id_articulo ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() == 0) {
        $row->id_sucursal = $id_sucursal_destino;
        $this->db->insert("articulos_precios_sucursales",$row);
        $i++;        
      }
    }
    echo "TERMINO $i";
  }

  function cargar_precios_sucursales() {
    $id_empresa = 445;

    $almacenes = array();
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $almacenes[] = $r;
    }

    $i = 0;
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    // RECORREMOS LOS ARTICULOS
    foreach($q->result() as $row) {

      // RECORREMOS LAS SUCURSALES
      foreach($almacenes as $sucursal) {
        // Controlamos si existe primero
        $sql = "SELECT * FROM articulos_precios_sucursales WHERE id_empresa = $id_empresa AND id_articulo = $row->id AND id_sucursal = $sucursal->id ";
        $qq = $this->db->query($sql);

        $precio = new stdClass();
        $precio->id_sucursal = $sucursal->id;
        $precio->id_articulo = $row->id;
        $precio->id_empresa = $id_empresa;
        $precio->fecha_mov = date("Y-m-d");
        $precio->id_tipo_alicuota_iva = $row->id_tipo_alicuota_iva;
        $precio->moneda = $row->moneda;
        $precio->porc_iva = $row->porc_iva;
        $precio->costo_iva = $row->costo_iva;
        $precio->costo_neto = $row->costo_neto;
        $precio->costo_final = $row->costo_final;
        $precio->porc_ganancia = $row->porc_ganancia;
        $precio->ganancia = $row->ganancia;
        $precio->precio_neto = $row->precio_neto;
        $precio->precio_final = $row->precio_final;
        $precio->porc_bonif = $row->porc_bonif;
        $precio->precio_final_dto = $row->precio_final_dto;
        $precio->last_update = $row->last_update;
        $precio->costo_neto_inicial = $row->costo_neto_inicial;
        $precio->dto_prov = $row->dto_prov;
        $precio->custom_1 = $row->custom_1;
        $precio->activo = 1;
        $precio->porc_ganancia_2 = $row->porc_ganancia_2;
        $precio->precio_final_2 = $row->precio_final_2;
        $precio->porc_bonif_2 = $row->porc_bonif_2;
        $precio->precio_final_dto_2 = $row->precio_final_dto_2;
        $precio->porc_ganancia_3 = $row->porc_ganancia_3;
        $precio->precio_final_3 = $row->precio_final_3;
        $precio->porc_bonif_3 = $row->porc_bonif_3;
        $precio->precio_final_dto_3 = $row->precio_final_dto_3;
        if ($qq->num_rows() > 0) {
          $this->db->where("id_articulo",$row->id);
          $this->db->where("id_sucursal",$sucursal->id);
          $this->db->where("id_empresa",$id_empresa);
          $this->db->update("articulos_precios_sucursales",$precio);
        } else {
          $this->db->insert("articulos_precios_sucursales",$precio);
        }
      }
    }
    echo "TERMINO";
  }

  function get_images() {
    $id_empresa = parent::get_empresa();
    $filter = ($this->input->get("filter") !== FALSE) ? $this->input->get("filter") : "";
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 10;
    $salida = $this->modelo->get_images(array(
      "id_empresa"=>$id_empresa,
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
    ));
    echo json_encode($salida);
  }

  function notificar() {
    $id_empresa = parent::get_empresa();
    $mensaje = "Hay nuevas modificaciones de precios.";
    $url = "https://www.varcreative.com/sistema/application/cronjobs/push_notification.php?id_empresa=$id_empresa&texto=".urlencode($mensaje);
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_exec($c);
    echo json_encode(array("error"=>0));
  }


  function eliminar_por_lote() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids");
    if (!is_array($ids) || sizeof($ids) == 0) {
      echo json_encode(array("error"=>1));
      exit();
    }
    foreach($ids as $id) {
      $this->modelo->delete($id);
    }    
    echo json_encode(array("error"=>0));
  }

  function cambiar_imagenes_por_lote() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids");
    if (!is_array($ids) || sizeof($ids) == 0) {
      echo json_encode(array("error"=>1));
      exit();
    }
    $images = parent::get_post("images");
    if (!is_array($images) || sizeof($images) == 0) {
      echo json_encode(array("error"=>1));
      exit();
    }
    foreach($ids as $id) {
      $this->db->query("DELETE FROM articulos_images WHERE id_empresa = $id_empresa AND id_articulo = $id ");
      $path = $images[0];
      $this->db->query("UPDATE articulos SET path = '$path' WHERE id_empresa = $id_empresa AND id = $id ");
      $i=0;
      foreach($images as $image) {
        $this->db->query("INSERT INTO articulos_images (id_empresa,id_articulo,path,orden) VALUES ($id_empresa,$id,'$image',$i)");
        $i++;
      }
    }
    echo json_encode(array("error"=>0));
  }

  
  function test() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $actualizados = 0;
    $id_empresa = 868;
    $sql = "SELECT * FROM articulos A WHERE A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      // Obtenemos el proveedor si lo tiene
      $proveedor = "";
      $id_proveedor = "";
      $sql = "SELECT P.nombre, AP.id_proveedor FROM articulos_proveedores AP INNER JOIN proveedores P ON (AP.id_empresa = P.id_empresa AND AP.id_proveedor = P.id) ";
      $sql.= "WHERE AP.id_empresa = $id_empresa ";
      $sql.= "AND AP.id_articulo = $row->id ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $prov = $qq->row();
        $proveedor = $prov->nombre;
        $id_proveedor = $prov->id_proveedor;
      }
      if (!empty($proveedor)) {
        $proveedor = str_replace("'", "", $proveedor);
        $proveedor = str_replace("\"", "", $proveedor);
        $sql = "UPDATE articulos SET custom_6 = '$proveedor', custom_7 = '$id_proveedor' ";
        $sql.= "WHERE id = $row->id AND id_empresa = $row->id_empresa ";
        $this->db->query($sql);
        $actualizados++;
      }
    }
    echo "ACTUALIZADOS: $actualizados";
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"producto_galeria_image_width",
      "clave_height"=>"producto_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/articulos/",
    ));
  }

  function acomodar_link() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM articulos WHERE id_empresa = 1041");
    foreach($q->result() as $row) {
      $row->nombre = trim($row->nombre);
      $row->nombre = str_replace("/", "-", $row->nombre);
      $link = "producto/".filename($row->nombre,"-",0)."-$row->id/";
      $this->db->query("UPDATE articulos SET link = '$link' WHERE id = $row->id AND id_empresa = $row->id_empresa ");
    }
    echo "TERMINO";
  }

  function arreglar_links() {
    
    $sql = "SELECT * FROM articulos_images ";
    $q = $this->db->query($sql);
    foreach($q->result() as $image) {
      $image->path = str_replace(" ", "", $image->path);
      $sql = "UPDATE articulos_images SET ";
      $sql.= " path = '$image->path' ";
      $sql.= "WHERE id = $image->id AND id_empresa = $image->id_empresa ";
      $this->db->query($sql);
    }

    $sql = "SELECT * FROM articulos WHERE path != '' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $image) {
      $image->path = str_replace(" ", "", $image->path);
      $sql = "UPDATE articulos SET ";
      $sql.= " path = '$image->path' ";
      $sql.= "WHERE id = $image->id AND id_empresa = $image->id_empresa ";
      $this->db->query($sql);
    }

    echo "TERMINO 5";
  }

  // Esta funcion es ejecutada llamando al metodo EXPORT() del server
  function get_data_from_server() {

    set_time_limit(0);
    $id_empresa = $this->get_empresa();
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);

    // La configuracion del sistema debe ser LOCAL
    $q = $this->db->query("SELECT * FROM com_configuracion WHERE id = 1");
    $configuracion = $q->row();
    if ($configuracion->local == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El sistema no esta configurado para conectarse al servidor (LOCAL=1).",
      ));
      return;            
    }

    $url_server = (empty($configuracion->url_server)) ? "www.varcreative.com" : $configuracion->url_server;

    // Debe haber conexion con el servidor
    $this->load->helper("connection_helper");
    if (!is_connected($url_server)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No hay conexion con el servidor.",
      ));
      return;
    }

    $url_server = ($url_server == "www.varcreative.com") ? "https://".$url_server : "http".$url_server;
    $current_timestamp = time();

    // Obtenemos la ultima configuracion
    $q = $this->db->query("SELECT articulos_last_update FROM fact_configuracion WHERE id_empresa = $id_empresa");
    $conf = $q->row();

    // Guardamos la version en la que se encuentra el punto de venta
    $head = FALSE;
    if (($id_empresa == 249 || $id_empresa == 868) && $id_sucursal != 0) {
      $this->load->helper("git_helper");
      $head = get_current_git_commit();
    }
    $head = ($head === FALSE) ? "Desconocido" : $head;

    // Obtenemos las sentencias SQL
    $url = "$url_server/sistema/articulos/function/export/$id_empresa/$id_sucursal/$conf->articulos_last_update/$id_punto_venta/$head/$configuracion->version_db/";
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $html = curl_exec($c);

    $salida = gzinflate($html); // Descomprimimos

    // Si no obtuvimos ningun dato
    if ($salida == "0") {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No hay datos nuevos para importar."
      ));
      return;
    }

    // Si el string no comienza con CERO
    $comienzo = substr($salida, 0, 2);
    if ($comienzo != "id") {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Las sentencias son incorrectas."
      ));
      return;
    }

    $this->load->helper("import_helper");
    $array = explode("|||", $salida);
    $campos = array();
    $i=0;
    // Ejecutamos una a una cada sentencia
    foreach($array as $sentence) {
      if (empty($sentence)) continue;
      if ($i==0) {
        // La primer linea contiene todos los campos
        $campos = explode(";;;", $sentence);
        $i++;
        continue;
      }
      
      $datos = explode(";;;", $sentence);
      $sql = "SELECT * FROM articulos WHERE ";
      $sql_where = "id = ".$datos[0]." AND id_empresa = $id_empresa ";
      $sql = $sql.$sql_where;
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $sql = create_update_sql(array(
          "fields"=>$campos,
          "table"=>"articulos",
          "data"=>$datos,
          "where"=>$sql_where,
        ));        
      } else {
        $sql = create_insert_sql(array(
          "fields"=>$campos,
          "table"=>"articulos",
          "data"=>$datos,
        ));        
      }
      $this->db->query($sql);
      $i++;
    }

    // Actualizamos el ultimo timestamp
    $this->db->query("UPDATE fact_configuracion SET articulos_last_update = '$current_timestamp' WHERE id_empresa = $id_empresa ");

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Los articulos han sido actualizados correctamente.",
    ));

  }

  // Manda todos INSERT para ser agregados al punto de venta
  function export($id_empresa = 0, $id_sucursal = 0, $last_update = 0, $id_punto_venta = 0, $head = "", $version_db = "") {

    set_time_limit(0);
    ini_set('memory_limit', '8G');
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    
    // TODO: Por ahora solo MEGA tiene precios sucursales distintos
    if ($id_empresa != 249 && $id_empresa != 868 && $id_empresa != 224) $id_sucursal = 0;

    if ($id_punto_venta != 0) {
      $sql = "INSERT INTO actualizaciones_cajas (id_empresa,id_punto_venta,fecha,version_git,version_db) VALUES (";
      $sql.= " '$id_empresa','$id_punto_venta',NOW(),'$head','$version_db' ";
      $sql.= ")";
      $this->db->query($sql);
    }
    if ($id_sucursal != 0) {
      $sql = "SELECT A.*, ";
      $sql.= " APS.precio_final, APS.precio_final AS precio_final_2, APS.precio_final AS precio_final_3, ";
      $sql.= " IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final) AS precio_final_dto, ";
      $sql.= " IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final) AS precio_final_dto_2, ";
      $sql.= " IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final) AS precio_final_dto_3, ";
      if ($id_sucursal == 23 && $id_empresa == 249) {
        // RIO GRANDE NO TIENE IVA, TODOS SON EXENTOS
        $sql.= " APS.costo_final AS costo_neto, APS.costo_final AS costo_neto_inicial, APS.precio_final AS precio_neto, APS.precio_final AS precio_neto_2, APS.precio_final AS precio_neto_3, ";
        $sql.= " '20' AS id_tipo_alicuota_iva, 0 AS porc_iva, ";
      } else {
        $sql.= " APS.costo_neto, APS.costo_neto_inicial, APS.precio_neto, APS.precio_neto AS precio_neto_2, APS.precio_neto AS precio_neto_3, ";
        $sql.= " APS.id_tipo_alicuota_iva, APS.porc_iva, ";
      }
      $sql.= " IF(ADS.precio_final IS NULL,'','1') AS custom_2, ";
      $sql.= " APS.costo_final, APS.dto_prov, APS.activo AS lista_precios, ";
      $sql.= " IF(APS.precio_final > 0,(100 - (((IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final)) * 100) / APS.precio_final)),0) AS porc_bonif, ";
      $sql.= " IF(APS.precio_final > 0,(100 - (((IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final)) * 100) / APS.precio_final)),0) AS porc_bonif_2, ";
      $sql.= " IF(APS.precio_final > 0,(100 - (((IF(ADS.precio_final IS NULL,APS.precio_final_dto,ADS.precio_final)) * 100) / APS.precio_final)),0) AS porc_bonif_3 ";
      $sql.= "FROM articulos A ";
      $sql.= "INNER JOIN articulos_precios_sucursales APS ON (A.id = APS.id_articulo AND A.id_empresa = APS.id_empresa AND APS.id_sucursal = $id_sucursal) ";
      $sql.= "LEFT JOIN articulos_descuentos_sucursales ADS ON (A.id = ADS.id_articulo AND A.id_empresa = ADS.id_empresa AND ADS.id_sucursal = $id_sucursal AND ADS.desde <= NOW() AND NOW() <= ADS.hasta ) ";
    } else {
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
    }
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_variantes($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos_variantes A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($id_sucursal != 0) $sql.= "AND A.id_sucursal = $id_sucursal ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_propiedades($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos_propiedades A ";
    $sql.= "WHERE A.id_empresa = $id_empresa OR A.id_empresa = 0 ";
    //if ($id_sucursal != 0) $sql.= "AND A.id_sucursal = $id_sucursal ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_propiedades_opciones($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos_propiedades_opciones A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($id_sucursal != 0) $sql.= "AND A.id_sucursal = $id_sucursal ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_atributos($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos_atributos A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($id_sucursal != 0) $sql.= "AND A.id_sucursal = $id_sucursal ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/articulos/";
    // En Esteban Echeverria, cargar las imagenes dentro de las carpetas segun el ID_USUARIO
    if ($id_empresa == 1284) {
      @session_start();
      $id_usuario = $_SESSION["id"];
      $dir = $dir.$id_usuario."/";
      if (!file_exists($dir)) @mkdir($dir);
    }    
    $filename = parent::get_post("file");
    echo parent::save_image($dir,$filename);
  } 

  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
        ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/articulos/";
    // En Esteban Echeverria, cargar las imagenes dentro de las carpetas segun el ID_USUARIO
    if ($id_empresa == 1284) {
      @session_start();
      $id_usuario = $_SESSION["id"];
      $path = $path.$id_usuario."/";
      if (!file_exists($path)) @mkdir($path);
    }
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path.$filename);
    echo json_encode(array(
      "path"=>$path.$filename,
      "error"=>0,
    ));
  }

  function next() {
    $codigo = $this->modelo->next();
    echo json_encode(array(
      "codigo"=>$codigo
    ));
  }

  function duplicar($id) {
    $salida = $this->modelo->duplicar($id);
    echo json_encode($salida);
  }

  function cambiar_rubro() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $articulos = parent::get_post("articulos",array());
    $id_rubro = parent::get_post("id_rubro",0);
    if (empty($id_rubro) || empty($articulos)) {
      echo json_encode(array("error"=>0));
      exit();
    }
    foreach($articulos as $art) {
      $sql = "UPDATE articulos SET id_rubro = $id_rubro ";
      $sql.= "WHERE id = '$art' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  }   

  function cambiar_etiqueta() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $articulos = parent::get_post("articulos",array());
    $etiquetas = parent::get_post("etiquetas",array());
    $this->load->model("Articulo_Model");
    foreach($articulos as $art) {
      foreach($etiquetas as $t) {
        $tag = new stdClass();
        $tag->nombre = $t;
        $tag->id_empresa = $id_empresa;
        $tag->id_articulo = $art;
        $this->Articulo_Model->save_tag($tag);
      }
    }
    echo json_encode(array("error"=>0));
  }   

  function cambiar_marca() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $articulos = parent::get_post("articulos",array());
    $id_marca = parent::get_post("id_marca",0);
    foreach($articulos as $art) {
      $sql = "UPDATE articulos SET id_marca = '".$id_marca."' ";
      $sql.= "WHERE id = '$art' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  }

  function cambiar_moneda() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $articulos = parent::get_post("articulos",array());
    $moneda = parent::get_post("moneda",0);
    if (empty($moneda) || empty($articulos)) {
      echo json_encode(array("error"=>0));
      exit();
    }
    foreach($articulos as $art) {
      $sql = "UPDATE articulos SET moneda = '".$moneda."' ";
      $sql.= "WHERE id = '$art' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  } 

  function cambiar_promocion() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $articulos = parent::get_post("articulos",array());
    $descuento = parent::get_post("descuento",0);
    $id_promocion = parent::get_post("id_promocion",0);
    if (empty($articulos)) {
      echo json_encode(array("error"=>0));
      exit();
    }
    $hoy = date("Y-m-d");
    $last_update = time();
    foreach($articulos as $art) {
      $sql = "UPDATE articulos SET ";
      $sql.= " id_promocion = '$id_promocion', ";
      $sql.= " porc_bonif = '$descuento', ";
      $sql.= " precio_final_dto = precio_final * ((100-$descuento)/100), ";
      $sql.= " fecha_mov = '$hoy', last_update = '$last_update' ";
      $sql.= "WHERE id = '$art' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);
    }
    echo json_encode(array("error"=>0));
  } 

  function actualizar() {

    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $filter = parent::get_get("texto","");
    $id_marca = parent::get_get("id_marca",0);
    $id_sucursal = parent::get_get("id_sucursal",-1); // 0 = Todas
    $id_proveedor = parent::get_get("id_proveedor",0);
    $ids_proveedores = str_replace("-", ",", parent::get_get("ids_proveedores",""));
    $not_ids_proveedores = str_replace("-", ",", parent::get_get("not_ids_proveedores",""));
    $in_rubros = str_replace("-", ",", parent::get_get("ids_rubros",""));
    $id_rubro = parent::get_get("id_rubro",0);
    $id_departamento = parent::get_get("id_departamento",0);
    $base = parent::get_get("base","");
    $id_rubro = parent::get_get("id_rubro",0);
    $campo = parent::get_get("campo","");
    $tipo = parent::get_get("tipo","");
    $redondeo = parent::get_get("redondeo",0);
    $monto = parent::get_get("monto",0);
    $fecha = parent::get_get("fecha","");
    $fecha_tipo = parent::get_get("fecha_tipo","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $this->load->model("Rubro_Model");
    $ids_rubros = "";
    if ($id_rubro != 0) $ids_rubros = implode(",",$this->Rubro_Model->get_ids_rubros($id_rubro));
    if (!empty($in_rubros)) {
      $in_rubros = explode(",", $in_rubros);
      $in_r = "";
      foreach($in_rubros as $id_r) {
        $in_r = implode(",",$this->Rubro_Model->get_ids_rubros($id_r));
      }
      $ids_rubros = (!empty($ids_rubros) ? $ids_rubros."," : "").$in_r;
    }

    $fecha_modif = date("Y-m-d");
    $last_update = time();

    // Estamos editando el descuento
    if ($campo == "D" || $campo == "D2" || $campo == "D3" || $campo == "D4") {

      // Se actualiza el PORCENTAJE DE DESCUENTO
      $sql = "UPDATE articulos A SET ";
      if ($campo == "D") {
        if ($redondeo > 0) $sql.= " A.precio_final_dto = ROUND((A.precio_final * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " A.precio_final_dto = (A.precio_final * ((100-$monto)/100)), ";          
        $sql.= " A.porc_bonif = '$monto', ";
      } else if ($campo == "D2") {
        if ($redondeo > 0) $sql.= " A.precio_final_dto_2 = ROUND((A.precio_final_2 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " A.precio_final_dto_2 = (A.precio_final_2 * ((100-$monto)/100)), ";          
        $sql.= " A.porc_bonif_2 = '$monto', ";
      } else if ($campo == "D3") {
        if ($redondeo > 0) $sql.= " A.precio_final_dto_3 = ROUND((A.precio_final_3 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " A.precio_final_dto_3 = (A.precio_final_3 * ((100-$monto)/100)), ";          
        $sql.= " A.porc_bonif_3 = '$monto', ";
      } else if ($campo == "D4") {
        if ($redondeo > 0) $sql.= " A.precio_final_dto_4 = ROUND((A.precio_final_4 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " A.precio_final_dto_4 = (A.precio_final_4 * ((100-$monto)/100)), ";          
        $sql.= " A.porc_bonif_4 = '$monto', ";
      }        
      $sql.= " A.fecha_mov = '$fecha_modif', ";
      $sql.= " A.last_update = '$last_update' ";
      $sql.= "WHERE A.id_empresa = $id_empresa ";
      if (!empty($id_proveedor)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor = $id_proveedor) ";
      if (!empty($ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor IN ($ids_proveedores)) ";
      if (!empty($not_ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor NOT IN ($not_ids_proveedores)) ";
      if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
      if (!empty($id_rubro) || !empty($ids_rubros)) $sql.= "AND A.id_rubro IN ($ids_rubros) ";
      if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
      if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
      if (!empty($fecha)) {
        $fecha = str_replace("-","/",$fecha);
        if ($fecha_tipo == "mayor") $fecha_tipo = " >= ";
        else if ($fecha_tipo == "menor") $fecha_tipo = " <= ";
        else $fecha_tipo = " = ";
        $sql.= "AND A.fecha_mov $fecha_tipo '$fecha' ";
      }
      $this->db->query($sql);

      // Actualizamos los precios_sucursales
      $sql = "UPDATE articulos_precios_sucursales APS ";
      $sql.= " INNER JOIN articulos A ON (APS.id_empresa = A.id_empresa AND APS.id_articulo = A.id) ";
      $sql.= " INNER JOIN articulos_proveedores AP ON (APS.id_empresa = AP.id_empresa AND APS.id_articulo = AP.id_articulo) ";
      $sql.= "SET ";
      if ($campo == "D") {
        if ($redondeo > 0) $sql.= " APS.precio_final_dto = ROUND((APS.precio_final * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " APS.precio_final_dto = (APS.precio_final * ((100-$monto)/100)), ";
        $sql.= " APS.porc_bonif = '$monto', ";          
      } else if ($campo == "D2") {
        if ($redondeo > 0) $sql.= " APS.precio_final_dto_2 = ROUND((APS.precio_final_2 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " APS.precio_final_dto_2 = (APS.precio_final_2 * ((100-$monto)/100)), ";
        $sql.= " APS.porc_bonif_2 = '$monto', ";
      } else if ($campo == "D3") {
        if ($redondeo > 0) $sql.= " APS.precio_final_dto_3 = ROUND((APS.precio_final_3 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " APS.precio_final_dto_3 = (APS.precio_final_3 * ((100-$monto)/100)), ";
        $sql.= " APS.porc_bonif_3 = '$monto', ";
      /*} else if ($campo == "D4") {
        if ($redondeo > 0) $sql.= " APS.precio_final_dto_4 = ROUND((APS.precio_final_4 * ((100-$monto)/100))*$redondeo,0)/$redondeo, ";
        else $sql.= " APS.precio_final_dto_4 = (APS.precio_final_4 * ((100-$monto)/100)), ";
        $sql.= " APS.porc_bonif_4 = '$monto', ";
      */
      }
      $sql.= " APS.last_update = '$last_update' ";
      $sql.= "WHERE APS.id_empresa = $id_empresa ";
      if (!empty($id_proveedor)) $sql.= "AND AP.id_proveedor = $id_proveedor ";
      if (!empty($ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor IN ($ids_proveedores)) ";
      if (!empty($not_ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor NOT IN ($not_ids_proveedores)) ";
      if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
      if (!empty($id_rubro) || !empty($ids_rubros)) $sql.= "AND A.id_rubro IN ($ids_rubros) ";
      if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
      if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
      if (!empty($fecha)) {
        $fecha = str_replace("-","/",$fecha);
        if ($fecha_tipo == "mayor") $fecha_tipo = " >= ";
        else if ($fecha_tipo == "menor") $fecha_tipo = " <= ";
        else $fecha_tipo = " = ";
        $sql.= "AND A.fecha_mov $fecha_tipo '$fecha' ";
      }
      $this->db->query($sql);


    // Estamos editando el descuento
    /*
    } else if ($campo == "M1" || $campo == "M2" || $campo == "M3" || $campo == "M4") {

      // Se actualiza el PORCENTAJE DE GANANCIA
      // Lo que se hace es se suma el valor al actual
      $sql = "UPDATE articulos A SET ";
      if ($campo == "M1") {
        $sql.= " A.porc_ganancia = A.porc_ganancia + '$monto', ";
        if ($redondeo > 0) $sql.= " A.precio_final = ROUND( (A.costo_final * ((100+A.porc_ganancia)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final = A.costo_final * ((100+A.porc_ganancia)/100), ";
        $sql.= " A.precio_neto = A.costo_neto * ((100+A.porc_ganancia)/100), ";
        $sql.= " A.ganancia = A.costo_final * (A.porc_ganancia/100), ";
        if ($redondeo > 0) $sql.= " A.precio_final_dto = ROUND( (A.precio_final * ((100-A.porc_bonif)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_dto = A.precio_final * ((100-A.porc_bonif)/100), ";
      } else if ($campo == "M2") {
        $sql.= " A.porc_ganancia_2 = A.porc_ganancia_2 + '$monto', ";
        if ($redondeo > 0) $sql.= " A.precio_final_2 = ROUND( (A.costo_final * ((100+A.porc_ganancia_2)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_2 = A.costo_final * ((100+A.porc_ganancia_2)/100), ";
        $sql.= " A.precio_neto_2 = A.costo_neto * ((100+A.porc_ganancia_2)/100), ";
        $sql.= " A.ganancia_2 = A.costo_final * (A.porc_ganancia_2/100), ";
        if ($redondeo > 0) $sql.= " A.precio_final_dto_2 = ROUND( (A.precio_final_2 * ((100-A.porc_bonif_2)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_dto_2 = A.precio_final_2 * ((100-A.porc_bonif_2)/100), ";
      } else if ($campo == "M3") {
        $sql.= " A.porc_ganancia_3 = A.porc_ganancia_3 + '$monto', ";
        if ($redondeo > 0) $sql.= " A.precio_final_3 = ROUND( (A.costo_final * ((100+A.porc_ganancia_3)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_3 = A.costo_final * ((100+A.porc_ganancia_3)/100), ";
        $sql.= " A.precio_neto_3 = A.costo_neto * ((100+A.porc_ganancia_3)/100), ";
        $sql.= " A.ganancia_3 = A.costo_final * (A.porc_ganancia_3/100), ";
        if ($redondeo > 0) $sql.= " A.precio_final_dto_3 = ROUND( (A.precio_final_3 * ((100-A.porc_bonif_3)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_dto_3 = A.precio_final_3 * ((100-A.porc_bonif_3)/100), ";
      } else if ($campo == "M4") {
        $sql.= " A.porc_ganancia_4 = A.porc_ganancia_4 + '$monto', ";
        if ($redondeo > 0) $sql.= " A.precio_final_4 = ROUND( (A.costo_final * ((100+A.porc_ganancia_4)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_4 = A.costo_final * ((100+A.porc_ganancia_4)/100), ";
        $sql.= " A.precio_neto_4 = A.costo_neto * ((100+A.porc_ganancia_4)/100), ";
        $sql.= " A.ganancia_4 = A.costo_final * (A.porc_ganancia_4/100), ";
        if ($redondeo > 0) $sql.= " A.precio_final_dto_4 = ROUND( (A.precio_final_4 * ((100-A.porc_bonif_4)/100)),0 )/$redondeo, ";
        else $sql.= " A.precio_final_dto_4 = A.precio_final_4 * ((100-A.porc_bonif_4)/100), ";
      }        
      $sql.= " A.fecha_mov = '$fecha_modif', ";
      $sql.= " A.last_update = '$last_update' ";
      $sql.= "WHERE A.id_empresa = $id_empresa ";
      if (!empty($id_proveedor)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor = $id_proveedor) ";
      if (!empty($ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor IN ($ids_proveedores)) ";
      if (!empty($not_ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor NOT IN ($not_ids_proveedores)) ";
      if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
      if (!empty($id_rubro) || !empty($ids_rubros)) $sql.= "AND A.id_rubro IN ($ids_rubros) ";
      if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
      if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
      if (!empty($fecha)) {
        $fecha = str_replace("-","/",$fecha);
        if ($fecha_tipo == "mayor") $fecha_tipo = " >= ";
        else if ($fecha_tipo == "menor") $fecha_tipo = " <= ";
        else $fecha_tipo = " = ";
        $sql.= "AND A.fecha_mov $fecha_tipo '$fecha' ";
      }
      echo $sql; exit();
      $this->db->query($sql);

      // Actualizamos los precios_sucursales
      $sql = "UPDATE articulos_precios_sucursales APS ";
      $sql.= " INNER JOIN articulos A ON (APS.id_empresa = A.id_empresa AND APS.id_articulo = A.id) ";
      $sql.= " INNER JOIN articulos_proveedores AP ON (APS.id_empresa = AP.id_empresa AND APS.id_articulo = AP.id_articulo) ";
      $sql.= "SET ";
      if ($campo == "M1") {
        $sql.= " APS.porc_ganancia = APS.porc_ganancia + '$monto', ";
        if ($redondeo > 0) $sql.= " APS.precio_final = ROUND( (APS.costo_final * ((100+APS.porc_ganancia)/100)),0 )/$redondeo, ";
        else $sql.= " APS.precio_final = APS.costo_final * ((100+APS.porc_ganancia)/100), ";
        $sql.= " APS.precio_neto = APS.costo_neto * ((100+APS.porc_ganancia)/100), ";
        $sql.= " APS.ganancia = APS.costo_final * (APS.porc_ganancia/100), ";
        if ($redondeo > 0) $sql.= " APS.precio_final_dto = ROUND( (APS.precio_final * ((100-APS.porc_bonif)/100)),0 )/$redondeo, ";
        else $sql.= " APS.precio_final_dto = APS.precio_final * ((100-APS.porc_bonif)/100), ";
      } else if ($campo == "M2") {
      } else if ($campo == "M3") {
      } else if ($campo == "M4") {
      }
      $sql.= " APS.last_update = '$last_update' ";
      $sql.= "WHERE APS.id_empresa = $id_empresa ";
      if (!empty($id_proveedor)) $sql.= "AND AP.id_proveedor = $id_proveedor ";
      if (!empty($ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor IN ($ids_proveedores)) ";
      if (!empty($not_ids_proveedores)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor NOT IN ($not_ids_proveedores)) ";
      if (!empty($id_marca)) $sql.= "AND A.id_marca = $id_marca ";
      if (!empty($id_rubro) || !empty($ids_rubros)) $sql.= "AND A.id_rubro IN ($ids_rubros) ";
      if (!empty($id_departamento)) $sql.= "AND A.id_departamento = $id_departamento ";
      if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
      if (!empty($fecha)) {
        $fecha = str_replace("-","/",$fecha);
        if ($fecha_tipo == "mayor") $fecha_tipo = " >= ";
        else if ($fecha_tipo == "menor") $fecha_tipo = " <= ";
        else $fecha_tipo = " = ";
        $sql.= "AND A.fecha_mov $fecha_tipo '$fecha' ";
      }
      $this->db->query($sql);      
      */

    } else {

      // IMPORTANTE: FORMULA PARA REDONDEAR DE A 5 PESOS
      // (CEIL((63/100)*20)/20)*100

      $r = $this->modelo->buscar(array(
        "buscar_solo_id"=>1,
        "filter"=>$filter,
        "id_marca"=>$id_marca,
        "id_proveedor"=>$id_proveedor,
        "id_departamento"=>$id_departamento,
        "id_rubro"=>$id_rubro,
        "ids_rubros"=>$ids_rubros,
        "ids_proveedores"=>$ids_proveedores,
        "not_ids_proveedores"=>$not_ids_proveedores,
        "offset"=>0,
        "fecha"=>$fecha,
        "fecha_tipo"=>$fecha_tipo,
      ));

      foreach($r["results"] as $row) {

        if ($campo == "C") {

          // Se actualiza el costo
          $costo = (float)($row->costo_neto);
          if ($tipo == "P") {
            $costo = (float)($costo + ($costo * $monto / 100));
          } else if ($tipo == "F") {
            $costo = (float)($costo + $monto);
          } else if ($tipo == "I") {
            $costo = (float)($monto);
          }
          $porc_iva = (float)($row->porc_iva);
          $costo_iva = (float)($costo * ($porc_iva / 100));
          $costo_final = (float)($costo) + (float)($costo_iva);

          $porc_ganancia = (float)($row->porc_ganancia);
          $ganancia = (float)($costo_final * ($porc_ganancia / 100));
          $precio_neto = (float)($costo)  * (1+($porc_ganancia / 100));
          $precio_final = (float)($costo_final) * (1+($porc_ganancia / 100));
          if ($redondeo > 0) $precio_final = round($precio_final * $redondeo,0) / $redondeo;

          $porc_ganancia_2 = (float)($row->porc_ganancia_2);
          $ganancia_2 = (float)($costo_final * ($porc_ganancia_2 / 100));
          $precio_neto_2 = (float)($costo)  * (1+($porc_ganancia_2 / 100));
          $precio_final_2 = (float)($costo_final)  * (1+($porc_ganancia_2 / 100));
          if ($redondeo > 0) $precio_final_2 = round($precio_final_2 * $redondeo,0) / $redondeo;

          $porc_ganancia_3 = (float)($row->porc_ganancia_3);
          $ganancia_3 = (float)($costo_final * ($porc_ganancia_3 / 100));
          $precio_neto_3 = (float)($costo)  * (1+($porc_ganancia_3 / 100));
          $precio_final_3 = (float)($costo_final)  * (1+($porc_ganancia_3 / 100));
          if ($redondeo > 0) $precio_final_3 = round($precio_final_3 * $redondeo,0) / $redondeo;

          $porc_ganancia_4 = (float)($row->porc_ganancia_4);
          $ganancia_4 = (float)($costo_final * ($porc_ganancia_4 / 100));
          $precio_neto_4 = (float)($costo)  * (1+($porc_ganancia_4 / 100));
          $precio_final_4 = (float)($costo_final)  * (1+($porc_ganancia_4 / 100));
          if ($redondeo > 0) $precio_final_4 = round($precio_final_4 * $redondeo,0) / $redondeo;

          $precio_final_dto = (float)($precio_final) * (1-($row->porc_bonif / 100));
          $precio_final_dto_2 = (float)($precio_final_2) * (1-($row->porc_bonif_2 / 100));
          $precio_final_dto_3 = (float)($precio_final_3) * (1-($row->porc_bonif_3 / 100));
          $precio_final_dto_4 = (float)($precio_final_4) * (1-($row->porc_bonif_4 / 100));
          if ($redondeo > 0) $precio_final_dto = round($precio_final_dto * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_2 = round($precio_final_dto_2 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_3 = round($precio_final_dto_3 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_4 = round($precio_final_dto_4 * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " costo_neto = '$costo', ";
          $sql.= " costo_iva = '$costo_iva', ";
          $sql.= " costo_final = '$costo_final', ";
          $sql.= " porc_ganancia = '$porc_ganancia', ";
          $sql.= " ganancia = '$ganancia', ";
          $sql.= " precio_neto = '$precio_neto', ";
          $sql.= " precio_final = '$precio_final', ";
          $sql.= " porc_ganancia_2 = '$porc_ganancia_2', ";
          $sql.= " ganancia_2 = '$ganancia_2', ";
          $sql.= " precio_neto_2 = '$precio_neto_2', ";
          $sql.= " precio_final_2 = '$precio_final_2', ";
          $sql.= " porc_ganancia_3 = '$porc_ganancia_3', ";
          $sql.= " ganancia_3 = '$ganancia_3', ";
          $sql.= " precio_neto_3 = '$precio_neto_3', ";
          $sql.= " precio_final_3 = '$precio_final_3', ";
          $sql.= " porc_ganancia_4 = '$porc_ganancia_4', ";
          $sql.= " ganancia_4 = '$ganancia_4', ";
          $sql.= " precio_neto_4 = '$precio_neto_4', ";
          $sql.= " precio_final_4 = '$precio_final_4', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto = '$precio_final_dto', ";
          $sql.= " precio_final_dto_2 = '$precio_final_dto_2', ";
          $sql.= " precio_final_dto_3 = '$precio_final_dto_3', ";
          $sql.= " precio_final_dto_4 = '$precio_final_dto_4' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

          if ($id_sucursal >= 0) {
            $sql = "UPDATE articulos_precios_sucursales SET ";
            if ($tipo == "P") {
              $sql.= " costo_neto_inicial = (costo_neto_inicial * ((100+$monto) / 100)), ";
            } else if ($tipo == "F") {
              $sql.= " costo_neto_inicial = costo_neto_inicial + $monto, ";
            } else if ($tipo == "I") {
              $sql.= " costo_neto_inicial = $monto, ";
            }
            $sql.= " costo_neto = (costo_neto_inicial * (100-dto_prov) / 100), ";
            $sql.= " costo_final = (costo_neto * ((100+porc_iva) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final = ROUND( costo_final * ((100+porc_ganancia) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final = (costo_final * ((100+porc_ganancia) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_2 = ROUND( costo_final * ((100+porc_ganancia_2) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_2 = (costo_final * ((100+porc_ganancia_2) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_3 = ROUND( costo_final * ((100+porc_ganancia_3) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_3 = (costo_final * ((100+porc_ganancia_3) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_dto = ROUND( precio_final * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto = (precio_final * (100-porc_bonif) / 100), ";
            if ($redondeo > 0) $sql.= " precio_final_dto_2 = ROUND( precio_final_2 * ((100-porc_bonif_2) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_2 = (precio_final_2 * (100-porc_bonif_2) / 100), ";
            if ($redondeo > 0) $sql.= " precio_final_dto_3 = ROUND( precio_final_3 * ((100-porc_bonif_3) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_3 = (precio_final_3 * (100-porc_bonif_3) / 100), ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update' ";
            $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
            if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          }

        } else if ($campo == "CI") {

          // Se actualiza el costo
          $costo = (float)($row->costo_neto_inicial);
          if ($tipo == "P") {
            $costo = (float)($costo + ($costo * $monto / 100));
          } else if ($tipo == "F") {
            $costo = (float)($costo + $monto);
          } else if ($tipo == "I") {
            $costo = (float)($monto);
          }
          $costo_neto_inicial = $costo;
          $row->dto_prov = (float)$row->dto_prov;
          $costo = (float)($costo_neto_inicial * ((100-$row->dto_prov) / 100));

          $porc_iva = (float)($row->porc_iva);
          $costo_iva = (float)($costo * ($porc_iva / 100));
          $costo_final = (float)($costo) + (float)($costo_iva);

          $porc_ganancia = (float)($row->porc_ganancia);
          $ganancia = (float)($costo_final * ($porc_ganancia / 100));
          $precio_neto = (float)($costo)  * (1+($porc_ganancia / 100));
          $precio_final = (float)($costo_final) * (1+($porc_ganancia / 100));
          if ($redondeo > 0) $precio_final = round($precio_final * $redondeo,0) / $redondeo;

          $porc_ganancia_2 = (float)($row->porc_ganancia_2);
          $ganancia_2 = (float)($costo_final * ($porc_ganancia_2 / 100));
          $precio_neto_2 = (float)($costo)  * (1+($porc_ganancia_2 / 100));
          $precio_final_2 = (float)($costo_final)  * (1+($porc_ganancia_2 / 100));
          if ($redondeo > 0) $precio_final_2 = round($precio_final_2 * $redondeo,0) / $redondeo;

          $porc_ganancia_3 = (float)($row->porc_ganancia_3);
          $ganancia_3 = (float)($costo_final * ($porc_ganancia_3 / 100));
          $precio_neto_3 = (float)($costo)  * (1+($porc_ganancia_3 / 100));
          $precio_final_3 = (float)($costo_final)  * (1+($porc_ganancia_3 / 100));
          if ($redondeo > 0) $precio_final_3 = round($precio_final_3 * $redondeo,0) / $redondeo;

          $porc_ganancia_4 = (float)($row->porc_ganancia_4);
          $ganancia_4 = (float)($costo_final * ($porc_ganancia_4 / 100));
          $precio_neto_4 = (float)($costo)  * (1+($porc_ganancia_4 / 100));
          $precio_final_4 = (float)($costo_final)  * (1+($porc_ganancia_4 / 100));
          if ($redondeo > 0) $precio_final_4 = round($precio_final_4 * $redondeo,0) / $redondeo;

          $precio_final_dto = (float)($precio_final) * (1-($row->porc_bonif / 100));
          $precio_final_dto_2 = (float)($precio_final_2) * (1-($row->porc_bonif_2 / 100));
          $precio_final_dto_3 = (float)($precio_final_3) * (1-($row->porc_bonif_3 / 100));
          $precio_final_dto_4 = (float)($precio_final_4) * (1-($row->porc_bonif_4 / 100));
          if ($redondeo > 0) $precio_final_dto = round($precio_final_dto * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_2 = round($precio_final_dto_2 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_3 = round($precio_final_dto_3 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_4 = round($precio_final_dto_4 * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " costo_neto_inicial = '$costo_neto_inicial', ";
          $sql.= " costo_neto = '$costo', ";
          $sql.= " costo_iva = '$costo_iva', ";
          $sql.= " costo_final = '$costo_final', ";
          $sql.= " porc_ganancia = '$porc_ganancia', ";
          $sql.= " ganancia = '$ganancia', ";
          $sql.= " precio_neto = '$precio_neto', ";
          $sql.= " precio_final = '$precio_final', ";
          $sql.= " porc_ganancia_2 = '$porc_ganancia_2', ";
          $sql.= " ganancia_2 = '$ganancia_2', ";
          $sql.= " precio_neto_2 = '$precio_neto_2', ";
          $sql.= " precio_final_2 = '$precio_final_2', ";
          $sql.= " porc_ganancia_3 = '$porc_ganancia_3', ";
          $sql.= " ganancia_3 = '$ganancia_3', ";
          $sql.= " precio_neto_3 = '$precio_neto_3', ";
          $sql.= " precio_final_3 = '$precio_final_3', ";
          $sql.= " porc_ganancia_4 = '$porc_ganancia_4', ";
          $sql.= " ganancia_4 = '$ganancia_4', ";
          $sql.= " precio_neto_4 = '$precio_neto_4', ";
          $sql.= " precio_final_4 = '$precio_final_4', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto = '$precio_final_dto', ";
          $sql.= " precio_final_dto_2 = '$precio_final_dto_2', ";
          $sql.= " precio_final_dto_3 = '$precio_final_dto_3', ";
          $sql.= " precio_final_dto_4 = '$precio_final_dto_4' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

          if ($id_sucursal >= 0) {
            $sql = "UPDATE articulos_precios_sucursales SET ";
            if ($tipo == "P") {
              $sql.= " costo_neto_inicial = (costo_neto_inicial * ((100+$monto) / 100)), ";
            } else if ($tipo == "F") {
              $sql.= " costo_neto_inicial = costo_neto_inicial + $monto, ";
            } else if ($tipo == "I") {
              $sql.= " costo_neto_inicial = $monto, ";
            }
            $sql.= " costo_neto = (costo_neto_inicial * (100-dto_prov) / 100), ";
            $sql.= " costo_final = (costo_neto * ((100+porc_iva) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final = ROUND( costo_final * ((100+porc_ganancia) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final = (costo_final * ((100+porc_ganancia) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_2 = ROUND( costo_final * ((100+porc_ganancia_2) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_2 = (costo_final * ((100+porc_ganancia_2) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_3 = ROUND( costo_final * ((100+porc_ganancia_3) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_3 = (costo_final * ((100+porc_ganancia_3) / 100)), ";
            if ($redondeo > 0) $sql.= " precio_final_dto = ROUND( precio_final * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto = (precio_final * (100-porc_bonif) / 100), ";
            if ($redondeo > 0) $sql.= " precio_final_dto_2 = ROUND( precio_final_2 * ((100-porc_bonif_2) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_2 = (precio_final_2 * (100-porc_bonif_2) / 100), ";
            if ($redondeo > 0) $sql.= " precio_final_dto_3 = ROUND( precio_final_3 * ((100-porc_bonif_3) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_3 = (precio_final_3 * (100-porc_bonif_3) / 100), ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update' ";
            $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
            if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          }

        } else if ($campo == "DP") {
          // Se actualiza el descuento del proveedor

          // TODO: Por ahora el descuento del proveedor solo se puede reemplazar
          if ($tipo == "I") {

            $costo_neto_inicial = (float)($row->costo_neto_inicial);
            $dto_prov = (float) $monto;
            $costo = $costo_neto_inicial * (100-$dto_prov) / 100;

            $porc_iva = (float)($row->porc_iva);
            $costo_iva = (float)($costo * ($porc_iva / 100));
            $costo_final = (float)($costo) + (float)($costo_iva);

            $porc_ganancia = (float)($row->porc_ganancia);
            $ganancia = (float)($costo_final * ($porc_ganancia / 100));
            $precio_neto = (float)($costo)  * (1+($porc_ganancia / 100));
            $precio_final = (float)($costo_final) * (1+($porc_ganancia / 100));
            if ($redondeo > 0) $precio_final = round($precio_final * $redondeo,0) / $redondeo;

            $porc_ganancia_2 = (float)($row->porc_ganancia_2);
            $ganancia_2 = (float)($costo_final * ($porc_ganancia_2 / 100));
            $precio_neto_2 = (float)($costo)  * (1+($porc_ganancia_2 / 100));
            $precio_final_2 = (float)($costo_final)  * (1+($porc_ganancia_2 / 100));
            if ($redondeo > 0) $precio_final_2 = round($precio_final_2 * $redondeo,0) / $redondeo;

            $porc_ganancia_3 = (float)($row->porc_ganancia_3);
            $ganancia_3 = (float)($costo_final * ($porc_ganancia_3 / 100));
            $precio_neto_3 = (float)($costo)  * (1+($porc_ganancia_3 / 100));
            $precio_final_3 = (float)($costo_final)  * (1+($porc_ganancia_3 / 100));
            if ($redondeo > 0) $precio_final_3 = round($precio_final_3 * $redondeo,0) / $redondeo;

            $porc_ganancia_4 = (float)($row->porc_ganancia_4);
            $ganancia_4 = (float)($costo_final * ($porc_ganancia_4 / 100));
            $precio_neto_4 = (float)($costo)  * (1+($porc_ganancia_4 / 100));
            $precio_final_4 = (float)($costo_final)  * (1+($porc_ganancia_4 / 100));
            if ($redondeo > 0) $precio_final_4 = round($precio_final_4 * $redondeo,0) / $redondeo;

            $precio_final_dto = (float)($precio_final) * (1-($row->porc_bonif / 100));
            $precio_final_dto_2 = (float)($precio_final_2) * (1-($row->porc_bonif_2 / 100));
            $precio_final_dto_3 = (float)($precio_final_3) * (1-($row->porc_bonif_3 / 100));
            $precio_final_dto_4 = (float)($precio_final_4) * (1-($row->porc_bonif_4 / 100));
            if ($redondeo > 0) $precio_final_dto = round($precio_final_dto * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_2 = round($precio_final_dto_2 * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_3 = round($precio_final_dto_3 * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_4 = round($precio_final_dto_4 * $redondeo,0) / $redondeo;

            $sql = "UPDATE articulos SET ";
            $sql.= " costo_neto_inicial = '$costo_neto_inicial', ";
            $sql.= " dto_prov = '$dto_prov', ";
            $sql.= " costo_neto = '$costo', ";
            $sql.= " costo_iva = '$costo_iva', ";
            $sql.= " costo_final = '$costo_final', ";
            $sql.= " porc_ganancia = '$porc_ganancia', ";
            $sql.= " ganancia = '$ganancia', ";
            $sql.= " precio_neto = '$precio_neto', ";
            $sql.= " precio_final = '$precio_final', ";
            $sql.= " porc_ganancia_2 = '$porc_ganancia_2', ";
            $sql.= " ganancia_2 = '$ganancia_2', ";
            $sql.= " precio_neto_2 = '$precio_neto_2', ";
            $sql.= " precio_final_2 = '$precio_final_2', ";
            $sql.= " porc_ganancia_3 = '$porc_ganancia_3', ";
            $sql.= " ganancia_3 = '$ganancia_3', ";
            $sql.= " precio_neto_3 = '$precio_neto_3', ";
            $sql.= " precio_final_3 = '$precio_final_3', ";
            $sql.= " porc_ganancia_4 = '$porc_ganancia_4', ";
            $sql.= " ganancia_4 = '$ganancia_4', ";
            $sql.= " precio_neto_4 = '$precio_neto_4', ";
            $sql.= " precio_final_4 = '$precio_final_4', ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update', ";
            $sql.= " precio_final_dto = '$precio_final_dto', ";
            $sql.= " precio_final_dto_2 = '$precio_final_dto_2', ";
            $sql.= " precio_final_dto_3 = '$precio_final_dto_3', ";
            $sql.= " precio_final_dto_4 = '$precio_final_dto_4' ";
            $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
            $this->db->query($sql);
          }


        } else if ($campo == "M1") {

          // TODO: Por ahora el descuento del proveedor solo se puede reemplazar
          if ($tipo == "I") {

            // Se actualiza Porcentaje Marcacion 1
            $porc_ganancia = $monto;
            $porc_iva = (float)($row->porc_iva);
            $costo_neto = (float)($row->costo_neto);
            $costo_final = (float)($row->costo_final);

            $ganancia = (float)($costo_final * ($porc_ganancia / 100));
            $precio_neto = (float)($costo_neto)  * (1+($porc_ganancia / 100));
            $precio_final = (float)($costo_final)  * (1+($porc_ganancia / 100));
            $precio_final_dto = (float)($precio_final) * (1-($row->porc_bonif / 100));

            if ($redondeo > 0) $precio_final = round($precio_final * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto = round($precio_final_dto * $redondeo,0) / $redondeo;

            $sql = "UPDATE articulos SET ";
            $sql.= " id_empresa = '$id_empresa', ";
            $sql.= " porc_ganancia = '$porc_ganancia', ";
            $sql.= " ganancia = '$ganancia', ";
            $sql.= " precio_neto = '$precio_neto', ";
            $sql.= " precio_final = '$precio_final', ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update', ";
            $sql.= " precio_final_dto = '$precio_final_dto' ";
            $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
            $this->db->query($sql);

            if ($id_sucursal >= 0) {
              $sql = "UPDATE articulos_precios_sucursales SET ";
              if ($redondeo > 0) $sql.= " precio_final = ROUND(costo_final * ((100+$porc_ganancia) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final = costo_final * ((100+$porc_ganancia) / 100), ";
              if ($redondeo > 0) $sql.= " precio_neto = ROUND(costo_neto * ((100+$porc_ganancia) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_neto = costo_neto * ((100+$porc_ganancia) / 100), ";
              if ($redondeo > 0) $sql.= " precio_final_dto = ROUND( precio_final * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
              else $sql.= " precio_final_dto = (precio_final * (100-porc_bonif) / 100), ";
              $sql.= " porc_ganancia = $porc_ganancia, ";
              $sql.= " fecha_mov = '$fecha_modif', ";
              $sql.= " last_update = '$last_update' ";
              $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
              if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
              $this->db->query($sql);
            }
          }

        } else if ($campo == "M2") {

          // TODO: Por ahora el descuento del proveedor solo se puede reemplazar
          if ($tipo == "I") {

            // Se actualiza Porcentaje Marcacion 1
            $porc_ganancia_2 = $monto;
            $porc_iva = (float)($row->porc_iva);
            $costo_neto = (float)($row->costo_neto);
            $costo_final = (float)($row->costo_final);

            $ganancia_2 = (float)($costo_final * ($porc_ganancia_2 / 100));
            $precio_neto_2 = (float)($costo_neto)  * (1+($porc_ganancia_2 / 100));
            $precio_final_2 = (float)($costo_final)  * (1+($porc_ganancia_2 / 100));
            $precio_final_dto_2 = (float)($precio_final_2) * (1-($row->porc_bonif / 100));

            if ($redondeo > 0) $precio_final_2 = round($precio_final_2 * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_2 = round($precio_final_dto_2 * $redondeo,0) / $redondeo;

            $sql = "UPDATE articulos SET ";
            $sql.= " id_empresa = '$id_empresa', ";
            $sql.= " porc_ganancia_2 = '$porc_ganancia_2', ";
            $sql.= " ganancia_2 = '$ganancia_2', ";
            $sql.= " precio_neto_2 = '$precio_neto_2', ";
            $sql.= " precio_final_2 = '$precio_final_2', ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update', ";
            $sql.= " precio_final_dto_2 = '$precio_final_dto_2' ";
            $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
            $this->db->query($sql);

            if ($id_sucursal >= 0) {
              $sql = "UPDATE articulos_precios_sucursales SET ";
              if ($redondeo > 0) $sql.= " precio_final_2 = ROUND(costo_final * ((100+$porc_ganancia_2) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_2 = costo_final * ((100+$porc_ganancia_2) / 100), ";
              if ($redondeo > 0) $sql.= " precio_neto_2 = ROUND(costo_neto * ((100+$porc_ganancia_2) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_neto_2 = costo_neto * ((100+$porc_ganancia_2) / 100), ";
              if ($redondeo > 0) $sql.= " precio_final_dto_2 = ROUND( precio_final_2 * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
              else $sql.= " precio_final_dto_2 = (precio_final_2 * (100-porc_bonif) / 100), ";
              $sql.= " porc_ganancia_2 = $porc_ganancia_2, ";
              $sql.= " fecha_mov = '$fecha_modif', ";
              $sql.= " last_update = '$last_update' ";
              $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
              if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
              $this->db->query($sql);
            }
          }

        } else if ($campo == "M3") {

          // TODO: Por ahora el descuento del proveedor solo se puede reemplazar
          if ($tipo == "I") {

            // Se actualiza Porcentaje Marcacion 1
            $porc_ganancia_3 = $monto;
            $porc_iva = (float)($row->porc_iva);
            $costo_neto = (float)($row->costo_neto);
            $costo_final = (float)($row->costo_final);

            $ganancia_3 = (float)($costo_final * ($porc_ganancia_3 / 100));
            $precio_neto_3 = (float)($costo_neto)  * (1+($porc_ganancia_3 / 100));
            $precio_final_3 = (float)($costo_final)  * (1+($porc_ganancia_3 / 100));
            $precio_final_dto_3 = (float)($precio_final_3) * (1-($row->porc_bonif / 100));

            if ($redondeo > 0) $precio_final_3 = round($precio_final_3 * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_3 = round($precio_final_dto_3 * $redondeo,0) / $redondeo;

            $sql = "UPDATE articulos SET ";
            $sql.= " id_empresa = '$id_empresa', ";
            $sql.= " porc_ganancia_3 = '$porc_ganancia_3', ";
            $sql.= " ganancia_3 = '$ganancia_3', ";
            $sql.= " precio_neto_3 = '$precio_neto_3', ";
            $sql.= " precio_final_3 = '$precio_final_3', ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update', ";
            $sql.= " precio_final_dto_3 = '$precio_final_dto_3' ";
            $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
            $this->db->query($sql);

            if ($id_sucursal >= 0) {
              $sql = "UPDATE articulos_precios_sucursales SET ";
              if ($redondeo > 0) $sql.= " precio_final_3 = ROUND(costo_final * ((100+$porc_ganancia_3) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_3 = costo_final * ((100+$porc_ganancia_3) / 100), ";
              if ($redondeo > 0) $sql.= " precio_neto_3 = ROUND(costo_neto * ((100+$porc_ganancia_3) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_neto_3 = costo_neto * ((100+$porc_ganancia_3) / 100), ";
              if ($redondeo > 0) $sql.= " precio_final_dto_3 = ROUND( precio_final_3 * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
              else $sql.= " precio_final_dto_3 = (precio_final_3 * (100-porc_bonif) / 100), ";
              $sql.= " porc_ganancia_3 = $porc_ganancia_3, ";
              $sql.= " fecha_mov = '$fecha_modif', ";
              $sql.= " last_update = '$last_update' ";
              $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
              if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
              $this->db->query($sql);
            }
          }

        } else if ($campo == "M4") {

          // TODO: Por ahora el descuento del proveedor solo se puede reemplazar
          if ($tipo == "I") {

            // Se actualiza Porcentaje Marcacion 1
            $porc_ganancia_4 = $monto;
            $porc_iva = (float)($row->porc_iva);
            $costo_neto = (float)($row->costo_neto);
            $costo_final = (float)($row->costo_final);

            $ganancia_4 = (float)($costo_final * ($porc_ganancia_4 / 100));
            $precio_neto_4 = (float)($costo_neto)  * (1+($porc_ganancia_4 / 100));
            $precio_final_4 = (float)($costo_final)  * (1+($porc_ganancia_4 / 100));
            $precio_final_dto_4 = (float)($precio_final_4) * (1-($row->porc_bonif / 100));

            if ($redondeo > 0) $precio_final_4 = round($precio_final_4 * $redondeo,0) / $redondeo;
            if ($redondeo > 0) $precio_final_dto_4 = round($precio_final_dto_4 * $redondeo,0) / $redondeo;

            $sql = "UPDATE articulos SET ";
            $sql.= " id_empresa = '$id_empresa', ";
            $sql.= " porc_ganancia_4 = '$porc_ganancia_4', ";
            $sql.= " ganancia_4 = '$ganancia_4', ";
            $sql.= " precio_neto_4 = '$precio_neto_4', ";
            $sql.= " precio_final_4 = '$precio_final_4', ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update', ";
            $sql.= " precio_final_dto_4 = '$precio_final_dto_4' ";
            $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
            $this->db->query($sql);

            /*
            if ($id_sucursal > 0) {
              $sql = "UPDATE articulos_precios_sucursales SET ";
              if ($redondeo > 0) $sql.= " precio_final_4 = ROUND(costo_final * ((100+$porc_ganancia_4) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_4 = costo_final * ((100+$porc_ganancia_4) / 100), ";
              if ($redondeo > 0) $sql.= " precio_neto_4 = ROUND(costo_neto * ((100+$porc_ganancia_4) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_neto_4 = costo_neto * ((100+$porc_ganancia_4) / 100), ";
              if ($redondeo > 0) $sql.= " precio_final_dto_4 = ROUND( precio_final_4 * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
              else $sql.= " precio_final_dto_4 = (precio_final_4 * (100-porc_bonif) / 100), ";
              $sql.= " porc_ganancia_4 = $porc_ganancia_4, ";
              $sql.= " fecha_mov = '$fecha_modif', ";
              $sql.= " last_update = '$last_update' ";
              $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
              $sql.= "AND id_sucursal = $id_sucursal ";
              $this->db->query($sql);
            }
            */
          }

        } else if ($campo == "P") {

          // Se actualiza LISTA 1
          if ($base == "P2") $precio_final = (float)($row->precio_final_2);
          else if ($base == "P3") $precio_final = (float)($row->precio_final_3);
          else if ($base == "P4") $precio_final = (float)($row->precio_final_4);
          else $precio_final = (float)($row->precio_final);

          if ($tipo == "P") {
            $precio_final = (float)($precio_final + ($precio_final * $monto / 100));
          } else if ($tipo == "F") {
            $precio_final = (float)($precio_final + $monto);
          } else if ($tipo == "I") {
            $precio_final = (float)($monto);
          }
          $porc_iva = (float)($row->porc_iva);
          $costo_neto = (float)($row->costo_neto);
          $costo_final = (float)($row->costo_final);
          if ($costo_final != 0) $porc_ganancia = (float)( (($precio_final / $costo_final) - 1) * 100);
          else $porc_ganancia = 0;
          $ganancia = (float)($costo_final * ($porc_ganancia / 100));
          $precio_neto = (float)($costo_neto)  * (1+($porc_ganancia / 100));
          $precio_final_dto = (float)($precio_final) * (1-($row->porc_bonif / 100));

          if ($redondeo > 0) $precio_final = round($precio_final * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto = round($precio_final_dto * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " id_empresa = '$id_empresa', ";
          $sql.= " porc_ganancia = '$porc_ganancia', ";
          $sql.= " ganancia = '$ganancia', ";
          $sql.= " precio_neto = '$precio_neto', ";
          $sql.= " precio_final = '$precio_final', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto = '$precio_final_dto' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

          if ($id_sucursal >= 0) {
            $sql = "UPDATE articulos_precios_sucursales SET ";
            if ($tipo == "P") {
              if ($redondeo > 0) $sql.= " precio_final = ROUND(precio_final * ((100+$monto) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final = precio_final * ((100+$monto) / 100), ";
            } else if ($tipo == "F") {
              if ($redondeo > 0) $sql.= " precio_final = ROUND((precio_final + $monto) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final = precio_final + $monto, ";
            } else if ($tipo == "I") {
              $sql.= " precio_final = $monto, ";
            }
            if ($redondeo > 0) $sql.= " precio_final_dto = ROUND( precio_final * ((100-porc_bonif) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto = (precio_final * (100-porc_bonif) / 100), ";
            $sql.= " porc_ganancia = IF(costo_final > 0,((precio_final - costo_final) / costo_final) * 100,0), ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update' ";
            $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
            if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          }

        } else if ($campo == "P2") {

          // Se actualiza LISTA 2
          if ($base == "P") $precio_final_2 = (float)($row->precio_final);
          else if ($base == "P3") $precio_final_2 = (float)($row->precio_final_3);
          else if ($base == "P4") $precio_final_2 = (float)($row->precio_final_4);
          else $precio_final_2 = (float)($row->precio_final_2);

          if ($tipo == "P") {
            $precio_final_2 = (float)($precio_final_2 + ($precio_final_2 * $monto / 100));
          } else if ($tipo == "F") {
            $precio_final_2 = (float)($precio_final_2 + $monto);
          } else if ($tipo == "I") {
            $precio_final_2 = (float)($monto);
          }
          $porc_iva = (float)($row->porc_iva);
          $costo_neto = (float)($row->costo_neto);
          $costo_final = (float)($row->costo_final);
          if ($costo_final != 0) $porc_ganancia_2 = (float)( (($precio_final_2 / $costo_final) - 1) * 100);
          else $porc_ganancia_2 = 0;
          $ganancia_2 = (float)($costo_final * ($porc_ganancia_2 / 100));
          $precio_neto_2 = (float)($costo_neto)  * (1+($porc_ganancia_2 / 100));
          $precio_final_dto_2 = (float)($precio_final_2) * (1-($row->porc_bonif_2 / 100));

          if ($redondeo > 0) $precio_final_2 = round($precio_final_2 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_2 = round($precio_final_dto_2 * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " id_empresa = '$id_empresa', ";
          $sql.= " porc_ganancia_2 = '$porc_ganancia_2', ";
          $sql.= " ganancia_2 = '$ganancia_2', ";
          $sql.= " precio_neto_2 = '$precio_neto_2', ";
          $sql.= " precio_final_2 = '$precio_final_2', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto_2 = '$precio_final_dto_2' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

          if ($id_sucursal >= 0) {
            $sql = "UPDATE articulos_precios_sucursales SET ";
            if ($tipo == "P") {
              if ($redondeo > 0) $sql.= " precio_final_2 = ROUND(precio_final_2 * ((100+$monto) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_2 = precio_final_2 * ((100+$monto) / 100), ";
            } else if ($tipo == "F") {
              if ($redondeo > 0) $sql.= " precio_final_2 = ROUND((precio_final_2 + $monto) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_2 = precio_final_2 + $monto, ";
            } else if ($tipo == "I") {
              $sql.= " precio_final_2 = $monto, ";
            }
            if ($redondeo > 0) $sql.= " precio_final_dto_2 = ROUND( precio_final_2 * ((100-porc_bonif_2) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_2 = (precio_final_2 * (100-porc_bonif_2) / 100), ";
            $sql.= " porc_ganancia_2 = IF(costo_final > 0,((precio_final_2 - costo_final) / costo_final) * 100,0), ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update' ";
            $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
            if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          }

        } else if ($campo == "P3") {

          // Se actualiza LISTA 3
          if ($base == "P") $precio_final_3 = (float)($row->precio_final);
          else if ($base == "P2") $precio_final_3 = (float)($row->precio_final_2);
          else if ($base == "P4") $precio_final_3 = (float)($row->precio_final_4);
          else $precio_final_3 = (float)($row->precio_final_3);

          if ($tipo == "P") {
            $precio_final_3 = (float)($precio_final_3 + ($precio_final_3 * $monto / 100));
          } else if ($tipo == "F") {
            $precio_final_3 = (float)($precio_final_3 + $monto);
          } else if ($tipo == "I") {
            $precio_final_3 = (float)($monto);
          }
          $porc_iva = (float)($row->porc_iva);
          $costo_neto = (float)($row->costo_neto);
          $costo_final = (float)($row->costo_final);
          if ($costo_final != 0) $porc_ganancia_3 = (float)( (($precio_final_3 / $costo_final) - 1) * 100);
          else $porc_ganancia_3 = 0;
          $ganancia_3 = (float)($costo_final * ($porc_ganancia_3 / 100));
          $precio_neto_3 = (float)($costo_neto)  * (1+($porc_ganancia_3 / 100));
          $precio_final_dto_3 = (float)($precio_final_3) * (1-($row->porc_bonif_3 / 100));

          if ($redondeo > 0) $precio_final_3 = round($precio_final_3 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_3 = round($precio_final_dto_3 * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " id_empresa = '$id_empresa', ";
          $sql.= " porc_ganancia_3 = '$porc_ganancia_3', ";
          $sql.= " ganancia_3 = '$ganancia_3', ";
          $sql.= " precio_neto_3 = '$precio_neto_3', ";
          $sql.= " precio_final_3 = '$precio_final_3', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto_3 = '$precio_final_dto_3' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

          if ($id_sucursal >= 0) {
            $sql = "UPDATE articulos_precios_sucursales SET ";
            if ($tipo == "P") {
              if ($redondeo > 0) $sql.= " precio_final_3 = ROUND(precio_final_3 * ((100+$monto) / 100) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_3 = precio_final_3 * ((100+$monto) / 100), ";
            } else if ($tipo == "F") {
              if ($redondeo > 0) $sql.= " precio_final_3 = ROUND((precio_final_3 + $monto) * $redondeo,0) / $redondeo, ";
              else $sql.= " precio_final_3 = precio_final_3 + $monto, ";
            } else if ($tipo == "I") {
              $sql.= " precio_final_3 = $monto, ";
            }
            if ($redondeo > 0) $sql.= " precio_final_dto_3 = ROUND( precio_final_3 * ((100-porc_bonif_3) / 100) * $redondeo, 0) / $redondeo, ";
            else $sql.= " precio_final_dto_3 = (precio_final_3 * (100-porc_bonif_3) / 100), ";
            $sql.= " porc_ganancia_3 = IF(costo_final > 0,((precio_final_3 - costo_final) / costo_final) * 100,0), ";
            $sql.= " fecha_mov = '$fecha_modif', ";
            $sql.= " last_update = '$last_update' ";
            $sql.= "WHERE id_articulo = $row->id AND id_empresa = $id_empresa ";
            if ($id_sucursal > 0) $sql.= "AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          }

        } else if ($campo == "P4") {

          // Se actualiza LISTA 4
          if ($base == "P") $precio_final_4 = (float)($row->precio_final);
          else if ($base == "P2") $precio_final_4 = (float)($row->precio_final_2);
          else if ($base == "P3") $precio_final_4 = (float)($row->precio_final_3);
          else $precio_final_4 = (float)($row->precio_final_4);

          if ($tipo == "P") {
            $precio_final_4 = (float)($precio_final_4 + ($precio_final_4 * $monto / 100));
          } else if ($tipo == "F") {
            $precio_final_4 = (float)($precio_final_4 + $monto);
          } else if ($tipo == "I") {
            $precio_final_4 = (float)($monto);
          }
          $porc_iva = (float)($row->porc_iva);
          $costo_neto = (float)($row->costo_neto);
          $costo_final = (float)($row->costo_final);
          if ($costo_final != 0) $porc_ganancia_4 = (float)( (($precio_final_4 / $costo_final) - 1) * 100);
          else $porc_ganancia_4 = 0;
          $ganancia_4 = (float)($costo_final * ($porc_ganancia_4 / 100));
          $precio_neto_4 = (float)($costo_neto)  * (1+($porc_ganancia_4 / 100));
          $precio_final_dto_4 = (float)($precio_final_4) * (1-($row->porc_bonif_4 / 100));

          if ($redondeo > 0) $precio_final_4 = round($precio_final_4 * $redondeo,0) / $redondeo;
          if ($redondeo > 0) $precio_final_dto_4 = round($precio_final_dto_4 * $redondeo,0) / $redondeo;

          $sql = "UPDATE articulos SET ";
          $sql.= " id_empresa = '$id_empresa', ";
          $sql.= " porc_ganancia_4 = '$porc_ganancia_4', ";
          $sql.= " ganancia_4 = '$ganancia_4', ";
          $sql.= " precio_neto_4 = '$precio_neto_4', ";
          $sql.= " precio_final_4 = '$precio_final_4', ";
          $sql.= " fecha_mov = '$fecha_modif', ";
          $sql.= " last_update = '$last_update', ";
          $sql.= " precio_final_dto_4 = '$precio_final_dto_4' ";
          $sql.= "WHERE id = $row->id AND id_empresa = $id_empresa ";
          $this->db->query($sql);

        }
      }
    }

    $error = 0;

    // Si tenemos articulos compartidos en MERCADOLIBRE
    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($id_empresa);
    if ($usa_meli) {
      $resultado = $this->actualizar_precios_meli($last_update);
      if (!$resultado) $error = 1;
    }

    echo json_encode(array(
      "error"=>$error,
    ));
  }

  function sincronizacion_completa_meli() {
    set_time_limit(0);
    $salida = $this->actualizar_precios_meli();
    echo json_encode(array(
      "error"=>($salida)?0:1,
    ));
  }

  function actualizar_precios_meli($last_update = "") {
    try {
      set_time_limit(0);
      $id_empresa = parent::get_empresa();
      $articulos = $this->modelo->buscar(array(
        "min"=>1,
        "last_update"=>$last_update,
        "offset"=>9999999,
      ));
      foreach($articulos["results"] as $array) {
        $art = $this->modelo->get($array->id,$id_empresa);
        if ($art->status == "active" || $art->status == "paused") {
          $this->modelo->update_meli($art);
          $this->modelo->update_publicacion_mercadolibre($art->id,array(
            "id_empresa"=>$id_empresa,
          ));
          usleep(200000);
        }
      }
      return TRUE;
    } catch(Exception $e) {
      file_put_contents("log_actualizar_precios_meli.txt", print_r($e,TRUE), FILE_APPEND);
      return FALSE;
    }
  }

  function update($id) {

    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();
    
    // Ponemos excepciones para el control de session
    if (isset($array->id_empresa) && $array->id_empresa == 263) {
      $id_empresa = 263;
    } else {
      $id_empresa = parent::get_empresa();  
    }
    $array->id_empresa = $id_empresa;
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_mov = fecha_mysql($array->fecha_mov);
    if (!empty($array->fecha_eliminado)) $array->fecha_eliminado = fecha_mysql($array->fecha_eliminado);
    if ($array->eliminado == 0) $array->fecha_eliminado = "";
    $array->fecha_ingreso = fecha_mysql($array->fecha_ingreso);
    
    // Eliminamos todo lo que no se persiste
    $clientes = isset($array->clientes) ? $array->clientes : array();
    $images = $array->images;
    $images_meli = $array->images_meli;
    $etiquetas = $array->etiquetas;
    $ingredientes = $array->ingredientes;
    $proveedores = $array->proveedores;
    $marcas_vehiculos = $array->marcas_vehiculos;
    $productos_relacionados = $array->relacionados;
    $rubros_relacionados = $array->rubros_relacionados;
    $precios_sucursales = $array->precios_sucursales;
    $atributos = (isset($array->atributos)) ? $array->atributos : array();
    $componentes = (isset($array->componentes)) ? $array->componentes : array();
    $variantes = $array->variantes;

    $id_meli = isset($array->id_meli) ? $array->id_meli : "";
    $permalink = isset($array->permalink) ? $array->permalink : "";
    $fecha_publicacion = isset($array->fecha_publicacion) ? $array->fecha_publicacion : "";
    $activo_meli = isset($array->activo_meli) ? $array->activo_meli : 0;
    $titulo_meli = isset($array->titulo_meli) ? $array->titulo_meli : "";
    $texto_meli = isset($array->texto_meli) ? $array->texto_meli : "";
    $atributos_meli = isset($array->atributos_meli) ? $array->atributos_meli : "";
    $categoria_meli = isset($array->categoria_meli) ? $array->categoria_meli : "";
    $precio_meli = isset($array->precio_meli) ? $array->precio_meli : 0;
    $list_type_id = isset($array->list_type_id) ? $array->list_type_id : "";
    $forma_envio_meli = isset($array->forma_envio_meli) ? $array->forma_envio_meli : "";
    $forma_pago_meli = isset($array->forma_pago_meli) ? $array->forma_pago_meli : "";
    $retiro_sucursal_meli = isset($array->retiro_sucursal_meli) ? $array->retiro_sucursal_meli : 0;
    $status = isset($array->status) ? $array->status : "";
    $this->modelo->remove_properties($array);
    
    // Si se cambio el precio, cambiamos la fecha de movimiento
    if ($this->modelo->existe_cambio_precio(array(
      "id"=>$id,
      "id_empresa"=>$array->id_empresa,
      "precio_final"=>$array->precio_final,
    ))) {
      $array->fecha_mov = date("Y-m-d");  
      $array->last_update = time();
    }
    
    $array->codigo_barra = trim($array->codigo_barra);
    $array->codigo = $this->modelo->limpiar_codigo($array->codigo);
    $array->codigo = trim($array->codigo);

    if ($this->modelo->existe_codigo($array->codigo,$id) && $array->id_empresa != 1284) {
      $salida = array(
        "error"=>1,
        "mensaje"=>"El codigo '$array->codigo' ya existe."
        );
      echo json_encode($salida);
      return;
    }

    $codigos_barra = explode("###", $array->codigo_barra);
    foreach($codigos_barra as $codigo_barra) {
      if (strlen($codigo_barra)<8) continue;
      $res = $this->modelo->existe_codigo_barra($codigo_barra,$id);
      if ($res["existe"] == 1) {
        $art_conflicto = $res["articulo"];
        $salida = array(
          "error"=>1,
          "mensaje"=>"El codigo de barra '$codigo_barra' ya existe en el articulo: '$art_conflicto->nombre' (Cod: $art_conflicto->codigo).",
        );
        echo json_encode($salida);
        exit();
      }        
    }
    
    // Actualizamos el link
    $this->load->model("Empresa_Model");
    $array->link = $this->Empresa_Model->get_base_link(array("clave"=>"producto","id_empresa"=>$array->id_empresa))."/".filename($array->nombre,"-",0)."-".$id."/";

    // Guardamos la primera imagen como path
    if (sizeof($images)>0) {
      $ppal = $images[0];
      $array->path = $ppal;
    }
    $array->path = str_replace(" ", "", $array->path);

    // Actualizamos los datos del articulo
    $this->modelo->save($array);

    if (sizeof($clientes)>0) {
      $this->db->query("DELETE FROM articulos_clientes WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
      foreach($clientes as $c) {
        $this->db->query("INSERT INTO articulos_clientes (id_empresa,id_articulo,id_cliente) VALUES ('$array->id_empresa','$id','$c->id_cliente')");
      }
    }
    
    // Eliminamos toda la relacion entre articulos y proveedores
    $this->db->query("DELETE FROM articulos_proveedores WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    // Volvemos a guardar la relacion entre articulos y proveedores actualizada
    $i=1;
    $custom_6 = "";  // Guardamos el nombre del proveedor
    $custom_10 = ""; // Guardamos el codigo del proveedor
    foreach($proveedores as $prov) {
      if ($i == 1) {
        $custom_10 = $prov->codigo;
        $custom_6 = (isset($prov->nombre)) ? $prov->nombre : "";
      }
      $this->db->insert("articulos_proveedores",array(
        "id_proveedor"=>$prov->id_proveedor,
        "codigo"=>$prov->codigo,
        "id_articulo"=>$id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    }
    if (!empty($custom_10) || !empty($custom_6)) {
      $this->db->query("UPDATE articulos SET custom_10 = '$custom_10', custom_6 = '$custom_6' WHERE id = $id AND id_empresa = $array->id_empresa ");
    }

    $this->db->query("DELETE FROM articulos_marcas_vehiculos WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    $i=1;
    foreach($marcas_vehiculos as $prov) {
      $this->db->insert("articulos_marcas_vehiculos",array(
        "id_marca_vehiculo"=>$prov->id_marca_vehiculo,
        "modelo"=>$prov->modelo,
        "id_articulo"=>$id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    } 

    $k=0;
    $this->db->query("DELETE FROM articulos_precios_sucursales WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    foreach($precios_sucursales as $t) {
      $this->db->insert("articulos_precios_sucursales",array(
        "id_sucursal"=>$t->id_sucursal,
        "id_articulo"=>$id,
        "id_empresa"=>$array->id_empresa,
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
        "fecha_mov"=>$t->fecha_mov,
        "costo_neto_inicial"=>(isset($t->costo_neto_inicial) ? $t->costo_neto_inicial : $t->costo_neto),
        "dto_prov"=>(isset($t->dto_prov) ? $t->dto_prov : 0),
        "activo"=>(isset($t->activo) ? $t->activo : 1),
        "porc_ganancia_2"=>(isset($t->porc_ganancia_2) ? $t->porc_ganancia_2 : 0),
        "precio_final_2"=>(isset($t->precio_final_2) ? $t->precio_final_2 : 0),
        "porc_bonif_2"=>(isset($t->porc_bonif_2) ? $t->porc_bonif_2 : 0),
        "precio_final_dto_2"=>(isset($t->precio_final_dto_2) ? $t->precio_final_dto_2 : 0),
        "porc_ganancia_3"=>(isset($t->porc_ganancia_3) ? $t->porc_ganancia_3 : 0),
        "precio_final_3"=>(isset($t->precio_final_3) ? $t->precio_final_3 : 0),
        "porc_bonif_3"=>(isset($t->porc_bonif_3) ? $t->porc_bonif_3 : 0),
        "precio_final_dto_3"=>(isset($t->precio_final_dto_3) ? $t->precio_final_dto_3 : 0),
      ));

      if ($k==0) {
        $this->db->where("id",$id);
        $this->db->where("id_empresa",$id_empresa);
        $this->db->update("articulos",array(
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
          "fecha_mov"=>$t->fecha_mov,
        ));
      }

      $k++;
    }

    // Eliminamos las relaciones entre articulos
    $this->db->query("DELETE FROM articulos_relacionados WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($productos_relacionados as $p) {
      $this->db->insert("articulos_relacionados",array(
        "id_articulo"=>$id,
        "id_relacion"=>$p->id,
        "id_rubro"=>0,
        "destacado"=>$p->destacado,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    }
    
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($rubros_relacionados as $p) {
      $this->db->insert("articulos_relacionados",array(
        "id_articulo"=>$id,
        "id_relacion"=>0,
        "id_rubro"=>$p->id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    }
    
    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM articulos_etiquetas_relacionadas WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_articulo = $id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }   

    // Insertamos los ingredientes
    $i=1;
    $this->db->query("DELETE FROM articulos_ingredientes WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    foreach($ingredientes as $ingre) {
      $this->db->insert("articulos_ingredientes",array(
        "nombre"=>$ingre->nombre,
        "valores"=>$ingre->valores,
        "adicional"=>$ingre->adicional,
        "activo"=>$ingre->activo,
        "id_articulo"=>$id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
      ));
      $i++;
    }  

    // Insertamos los componentes
    $this->db->query("DELETE FROM articulos_componentes WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    foreach($componentes as $c) {
      $this->db->insert("articulos_componentes",array(
        "cantidad"=>$c->cantidad,
        "id_articulo_componente"=>$c->id_articulo_componente,
        "id_articulo"=>$id,
        "id_empresa"=>$array->id_empresa,
      ));
    }  

    // Primero marcamos como que todos las variantes van a ser eliminadas
    $this->db->query("UPDATE articulos_variantes SET eliminado = 1 WHERE id_articulo = $id AND id_empresa = $array->id_empresa");
    foreach($variantes as $v) {
      $v->id_articulo = $id;
      $v->nombre = strip_tags($v->nombre);
      $v->id_empresa = $array->id_empresa;
      $v->eliminado = 0;
      $this->modelo->save_variante($v);
    }
    // Eliminamos el stock de las variantes que ya no estan
    $this->db->query("DELETE SV FROM stock_variantes SV LEFT JOIN articulos_variantes AV ON (SV.id_variante = AV.id AND SV.id_articulo = AV.id_articulo AND SV.id_empresa = AV.id_empresa) WHERE AV.id IS NULL AND SV.id_empresa = $array->id_empresa AND SV.id_articulo = $id");
    // Eliminamos las variantes que ya no estan
    $this->db->query("DELETE FROM articulos_variantes WHERE id_articulo = $id AND id_empresa = $array->id_empresa AND eliminado = 1 ");
    // TODO: Tendriamos que recalcular el stock general
    
    // Guardamos las imagenes
    $this->db->query("DELETE FROM articulos_images WHERE id_articulo = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $sql = "INSERT INTO articulos_images (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }

    $this->db->query("DELETE FROM articulos_images_meli WHERE id_articulo = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO articulos_images_meli (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }    


    // Actualizamos los atributos especificos
    $this->db->query("DELETE FROM articulos_atributos WHERE id_articulo = $id AND id_empresa = $id_empresa");
    foreach($atributos as $p) {
      $this->db->insert("articulos_atributos",array(
        "id_articulo"=>$id,
        "id_empresa"=>$id_empresa,
        "id_atributo"=>$p->id_atributo,
        "no_aplica"=>(isset($p->no_aplica) ? $p->no_aplica : 0),
        "tipo"=>(isset($p->tipo) ? $p->tipo : ""),
        "value_name"=>(isset($p->value_name) ? $p->value_name : ""),
        "value_id"=>(isset($p->value_id) ? $p->value_id : ""),
      ));
    }


    // CONTROLAMOS SI ESTA PUBLICADO EN MERCADOLIBRE
    $array->categoria_meli = $categoria_meli;
    $array->id_meli = $id_meli;
    $array->permalink = $permalink;
    $array->fecha_publicacion = $fecha_publicacion;
    $array->activo_meli = $activo_meli;
    $array->titulo_meli = $titulo_meli;
    $array->texto_meli = $texto_meli;
    $array->atributos_meli = $atributos_meli;
    $array->precio_meli = $precio_meli;
    $array->list_type_id = $list_type_id;
    $array->forma_envio_meli = $forma_envio_meli;
    $array->forma_pago_meli = $forma_pago_meli;
    $array->retiro_sucursal_meli = $retiro_sucursal_meli;
    $array->status = $status;
    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($id_empresa);
    if ($usa_meli) {
      $publicado = $this->modelo->update_meli($array);
      if ($publicado) $this->modelo->update_publicacion_mercadolibre($id);
    }

    // Llamamos para actualizar en caso de corresponder
    $this->modelo->actualizar_pedienchacabuco($id_empresa);    
    
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }


  function cambiar_fecha($fecha_anterior="",$fecha_posterior="") {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $fecha_anterior = fecha_mysql($fecha_anterior);
    $fecha_posterior = fecha_mysql($fecha_posterior);
    $sql = "UPDATE articulos SET ";
    $sql.= "fecha_mov = '$fecha_posterior' ";
    $sql.= "WHERE fecha_mov = '$fecha_anterior' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    $num = $this->db->affected_rows();
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Actualizacion realizada. Nro de Articulos modificados: $num"
      ));
  }


  function insert() {

    $this->load->helper("file_helper");
    $array = $this->parse_put();

    file_put_contents("log_articulo.txt", print_r($array,TRUE), FILE_APPEND);
    
    // Ponemos excepciones para el control de session
    if (isset($array->id_empresa) && $array->id_empresa == 263) {
      $id_empresa = 263;
    } else {
      $id_empresa = parent::get_empresa();  
    }
    $array->id_empresa = $id_empresa;
    
    // Controlamos el tipo de plan, para saber si el usuario no esta pasado
    if ($this->modelo->controlar_plan($id_empresa) === FALSE) {
      parent::send_error("Ha alcanzado el limite de articulos para su plan");
    }        
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_mov = date("Y-m-d");
    $array->last_update = time();
    if ($id_empresa == 263) {
      // Utilizamos la fecha de ingreso como fecha de vencimiento
      $this->load->model("Web_Configuracion_Model");
      $web_conf = $this->Web_Configuracion_Model->get($id_empresa);
      $cant_dias = (empty($web_conf->texto_quienes_somos)) ? 30 : ((int)$web_conf->texto_quienes_somos);
      $array->fecha_ingreso = date("Y-m-d",strtotime("+".$cant_dias." days"));
    } else {
      $array->fecha_ingreso = $array->fecha_mov;  
    }
    if (!empty($array->fecha_eliminado)) $array->fecha_eliminado = fecha_mysql($array->fecha_eliminado);
    if ($array->eliminado == 0) $array->fecha_eliminado = "";
    
    // Eliminamos todo lo que no se persiste
    $clientes = isset($array->clientes) ? $array->clientes : array();
    $images = $array->images;
    $images_meli = $array->images_meli;
    $variantes = $array->variantes;
    $ingredientes = $array->ingredientes;
    $etiquetas = $array->etiquetas;
    $proveedores = $array->proveedores;
    $marcas_vehiculos = $array->marcas_vehiculos;
    $productos_relacionados = $array->relacionados;
    $rubros_relacionados = $array->rubros_relacionados;
    $precios_sucursales = $array->precios_sucursales;
    $atributos = (isset($array->atributos)) ? $array->atributos : array();
    $componentes = (isset($array->componentes)) ? $array->componentes : array();
    $array->codigo_barra = trim($array->codigo_barra);
    $array->codigo = $this->modelo->limpiar_codigo($array->codigo);
    $array->codigo = trim($array->codigo);
    $this->modelo->remove_properties($array);

    if ($this->modelo->existe_codigo($array->codigo)) {
      $salida = array(
        "error"=>1,
        "mensaje"=>"El codigo '$array->codigo' ya existe."
        );
      echo json_encode($salida);
      return;
    }
    
    $codigos_barra = explode("###", $array->codigo_barra);
    foreach($codigos_barra as $codigo_barra) {
      if (strlen($codigo_barra)<8) continue;
      $res = $this->modelo->existe_codigo_barra($codigo_barra);
      if ($res["existe"] == 1) {
        $art_conflicto = $res["articulo"];
        $salida = array(
          "error"=>1,
          "mensaje"=>"El codigo de barra '$codigo_barra' ya existe en el articulo: '$art_conflicto->nombre' (Cod: $art_conflicto->codigo).",
        );
        echo json_encode($salida);
        exit();
      }        
    }

    // Guardamos la primera imagen como path
    if (sizeof($images)>0) {
      $ppal = $images[0];
      $array->path = $ppal;
      $array->path = str_replace(" ", "", $array->path);
    }
    
    // Insertamos el articulo
    $insert_id = $this->modelo->save($array);
    $id_primer_proveedor = 0;
    
    // Actualizamos el link
    $this->load->model("Empresa_Model");
    $array->link = $this->Empresa_Model->get_base_link(array("clave"=>"producto","id_empresa"=>$array->id_empresa))."/".filename($array->nombre,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE articulos SET link = '$array->link' WHERE id = $insert_id");

    if (sizeof($clientes)>0) {
      $this->db->query("DELETE FROM articulos_clientes WHERE id_articulo = $insert_id AND id_empresa = $array->id_empresa");
      foreach($clientes as $c) {
        $this->db->query("INSERT INTO articulos_clientes (id_empresa,id_articulo,id_cliente) VALUES ('$array->id_empresa','$insert_id','$c->id_cliente')");
      }
    }

    // Insertamos la relacion entre articulos y proveedores
    $custom_10 = "";
    $i=1;
    foreach($proveedores as $prov) {
      if ($i==1) {
        $id_primer_proveedor = $prov->id_proveedor;
        $custom_10 = $prov->codigo;
      }
      $this->db->insert("articulos_proveedores",array(
        "id_proveedor"=>$prov->id_proveedor,
        "codigo"=>$prov->codigo,
        "id_articulo"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    }
    if (!empty($custom_10)) {
      $this->db->query("UPDATE articulos SET custom_10 = '$custom_10' WHERE id = $insert_id AND id_empresa = $array->id_empresa ");
    }

    $k=0;
    foreach($precios_sucursales as $t) {
      $this->db->insert("articulos_precios_sucursales",array(
        "id_sucursal"=>$t->id_sucursal,
        "id_articulo"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
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
        "fecha_mov"=>date("Y-m-d"),
        "last_update"=>time(),
        "costo_neto_inicial"=>(isset($t->costo_neto_inicial) ? $t->costo_neto_inicial : $t->costo_neto),
        "dto_prov"=>(isset($t->dto_prov) ? $t->dto_prov : 0),
        "activo"=>(isset($t->activo) ? $t->activo : 1),
        "porc_ganancia_2"=>(isset($t->porc_ganancia_2) ? $t->porc_ganancia_2 : 0),
        "precio_final_2"=>(isset($t->precio_final_2) ? $t->precio_final_2 : 0),
        "porc_bonif_2"=>(isset($t->porc_bonif_2) ? $t->porc_bonif_2 : 0),
        "precio_final_dto_2"=>(isset($t->precio_final_dto_2) ? $t->precio_final_dto_2 : 0),
        "porc_ganancia_3"=>(isset($t->porc_ganancia_3) ? $t->porc_ganancia_3 : 0),
        "precio_final_3"=>(isset($t->precio_final_3) ? $t->precio_final_3 : 0),
        "porc_bonif_3"=>(isset($t->porc_bonif_3) ? $t->porc_bonif_3 : 0),
        "precio_final_dto_3"=>(isset($t->precio_final_dto_3) ? $t->precio_final_dto_3 : 0),
      ));
      if ($k==0) {
        $this->db->where("id",$insert_id);
        $this->db->where("id_empresa",$array->id_empresa);
        $this->db->update("articulos",array(
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
          "fecha_mov"=>$t->fecha_mov,
        ));
      }
      $k++;
    }  

    $i=1;
    foreach($marcas_vehiculos as $prov) {
      $this->db->insert("articulos_marcas_vehiculos",array(
        "id_marca_vehiculo"=>$prov->id_marca_vehiculo,
        "modelo"=>$prov->modelo,
        "id_articulo"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
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
        "id_empresa"=>$array->id_empresa,
        "orden"=>$i,
        ));
      $i++;
    }  

    // Insertamos los componentes
    foreach($componentes as $c) {
      $this->db->insert("articulos_componentes",array(
        "id_articulo"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "id_articulo_componente"=>$c->id_articulo_componente,
        "cantidad"=>$c->cantidad,
      ));
    }  
    
    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM articulos_etiquetas_relacionadas WHERE id_articulo = $insert_id AND id_empresa = $array->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_articulo = $insert_id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }

    // Guardamos las variantes de un producto
    $this->db->query("DELETE FROM articulos_variantes WHERE id_articulo = $insert_id AND id_empresa = $array->id_empresa");
    foreach($variantes as $v) {
      $v->id_articulo = $insert_id;
      $v->nombre = strip_tags($v->nombre);
      $v->id_empresa = $array->id_empresa;
      $this->modelo->save_variante($v);
    }
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($productos_relacionados as $p) {
      $this->db->insert("articulos_relacionados",array(
        "id_articulo"=>$insert_id,
        "id_relacion"=>$p->id,
        "id_rubro"=>0,
        "id_empresa"=>$array->id_empresa,
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
        "id_empresa"=>$array->id_empresa,
        "id_rubro"=>$p->id,
        "orden"=>$i,
        ));
      $i++;
    }
    
    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $sql = "INSERT INTO articulos_images (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }
    $k=0;
    foreach($images_meli as $im) {
      $sql = "INSERT INTO articulos_images_meli (id_empresa,id_articulo,path,orden";
      $sql.= ") VALUES( ";
      $sql.= "$id_empresa,$insert_id,'$im',$k)";
      $this->db->query($sql);
      $k++;
    }


    // Actualizamos los atributos especificos
    foreach($atributos as $p) {
      $this->db->insert("articulos_atributos",array(
        "id_articulo"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "id_atributo"=>$p->id_atributo,
        "no_aplica"=>(isset($p->no_aplica) ? $p->no_aplica : 0),
        "tipo"=>(isset($p->tipo) ? $p->tipo : ""),
        "value_name"=>(isset($p->value_name) ? $p->value_name : ""),
        "value_id"=>(isset($p->value_id) ? $p->value_id : ""),
      ));
    }


    // Agregamos al stock
    if ($array->usa_stock == 1) {
      $this->load->model("Stock_Model");
      $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa";
      $q_sec = $this->db->query($sql);
      foreach($q_sec->result() as $sucursal) {
        if ($array->stock > 0) {
          $this->Stock_Model->agregar($insert_id,$array->stock,$sucursal->id,"","",$id_primer_proveedor);
        } else {
          $this->Stock_Model->ajustar($insert_id,0,$sucursal->id,"",$id_primer_proveedor);
        }
      }
    }
    
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
      );
    echo json_encode($salida);        
  }

  function acomodar_proveedores() {
    set_time_limit(0);
    $id_empresa = 249;
    $sql = "select distinct id_articulo from facturas_items where id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT id_proveedor FROM articulos_proveedores ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      $sql.= "LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $sql = "UPDATE facturas_items SET id_proveedor = $rr->id_proveedor ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $this->db->query($sql);
      }
    }
    echo "TERMINO";
  }


  function ajuste_masivo_canasta_basica() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids","");
    $estado = parent::get_post("estado",1);
    $ids = str_replace("-", ",", $ids);
    $fecha = date("Y-m-d");
    $sql = "UPDATE articulos SET custom_5 = '$estado', fecha_mov = '$fecha' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id IN ($ids) ";
    $this->db->query($sql);

    // Llamamos para actualizar en caso de corresponder
    $this->modelo->actualizar_pedienchacabuco($id_empresa);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  function editar_masivo_proveedor() {
    $id_empresa = parent::get_empresa();
    $id_proveedor = parent::get_post("id_proveedor",0);
    $ids = parent::get_post("articulos",array());
    foreach($ids as $id) {
      $sql = "SELECT 1 FROM articulos_proveedores ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_proveedor = $id_proveedor ";
      $sql.= "AND id_articulo = $id ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) {
        // Agregamos el proveedor
        $sql = "INSERT INTO articulos_proveedores (id_empresa,id_proveedor,id_articulo) VALUES (";
        $sql.= "$id_empresa, $id_proveedor, $id)";
        $this->db->query($sql);
      }
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  // Funcion utilizada en los puntos de venta para que funcione mas rapido
  function get_by_codigo_pv($codigo = '') {
    $codigo = (!is_numeric($codigo)) ? urldecode($codigo) : $codigo;
    $id_empresa = parent::get_empresa();
    $lista_precios = parent::get_post("lista_precios",0);

    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.lista_precios >= 1 ";
    if ($id_empresa == 249 || $id_empresa == 868) {
      // Arreglo para MEGASHOP
      // El sistema anterior cortaba los codigos de barra en 12 digitos
      if (strlen($codigo) == 13) {
        $codigo_12 = substr($codigo, 0, 12);
        $sql.= "AND (A.codigo_barra LIKE '%$codigo%' OR A.codigo_barra LIKE '%$codigo_12%') ";
      } else if (strlen($codigo)>7) {
        $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
      } else if (strlen($codigo) == 7) {
        $codigo_6 = substr($codigo, 0, 6);
        $codigo_0 = "0".$codigo;
        $sql.= "AND (A.codigo_barra = '$codigo_0' OR A.codigo = '$codigo' OR A.codigo = '$codigo_6' OR A.codigo_barra LIKE '%$codigo%' OR A.codigo_barra LIKE '$codigo_6%') ";
      } else {
        $sql.= "AND A.codigo = '$codigo' ";
      }
    } else if ($id_empresa == 465) {
      if (is_numeric($codigo) && strlen($codigo) >= 7) {
        $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
      } else {
        $sql.= "AND A.codigo = '$codigo' "; 
      }
    } else {
      if (is_numeric($codigo) && strlen($codigo) >= 8) {
        $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
      } else {
        $sql.= "AND A.codigo = '$codigo' "; 
      }
    }
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $articulo = $this->modelo->get_by_id($row->id);
      echo json_encode(array(
        "error"=>0,
        "articulo"=>$articulo,
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un articulo con el codigo: '$codigo'",
        "sql"=>$sql,
      ));
    }
  }


  function get_by_codigo($codigo='') {

    $codigo = (!is_numeric($codigo)) ? urldecode($codigo) : $codigo;
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_post("id_sucursal",0);
    $lista_precios = parent::get_post("lista_precios",0);
    $consultar_stock = parent::get_post("consultar_stock",0);

    // Primero buscamos si existe con 13 digitos
    $articulo = null;
    if (($id_empresa == 249 || $id_empresa == 868) && strlen($codigo) == 13) {
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "WHERE A.lista_precios >= 1 ";
      $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $row = $q->row();
        $articulo = $this->modelo->get($row->id);        
      }
    }

    // Si no existe con 13, probamos con 12
    if ($articulo == null) {
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "WHERE A.lista_precios >= 1 ";
      if ($id_empresa == 249 || $id_empresa == 868) {
        // Arreglo para MEGASHOP
        // El sistema anterior cortaba los codigos de barra en 12 digitos
        if (strlen($codigo) == 13) {
          $codigo_12 = substr($codigo, 0, 12);
          $sql.= "AND (A.codigo_barra LIKE '%$codigo%' OR A.codigo_barra LIKE '%$codigo_12%') ";
        } else if (strlen($codigo)>7) {
          $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
        } else if (strlen($codigo) == 7) {
          $codigo_6 = substr($codigo, 0, 6);
          $codigo_0 = "0".$codigo;
          $sql.= "AND (A.codigo_barra = '$codigo_0' OR A.codigo = '$codigo' OR A.codigo = '$codigo_6' OR A.codigo_barra LIKE '%$codigo%' OR A.codigo_barra LIKE '$codigo_6%') ";
        } else {
          $sql.= "AND A.codigo = '$codigo' ";
        }
      } else if ($id_empresa == 465) {
        if (is_numeric($codigo) && strlen($codigo) >= 7) {
          $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
        } else {
          $sql.= "AND A.codigo = '$codigo' "; 
        }      
      } else {
        if ($id_empresa == 435) {
          $sql.= "AND A.codigo = '$codigo' "; 
        } else {
          if (is_numeric($codigo) && strlen($codigo) >= 9) {
            $sql.= "AND A.codigo_barra LIKE '%$codigo%' ";
          } else {
            $sql.= "AND A.codigo = '$codigo' "; 
          }
        }
      }
      $sql.= "AND id_empresa = $id_empresa ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $row = $q->row();
        $articulo = $this->modelo->get($row->id);
      }
    }

    if ($articulo == null) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un articulo con el codigo: '$codigo'",
        "sql"=>$sql,
      ));
      exit();
    }

    // Ponemos los precios de acuerdo a la sucursal que va
    if ($id_sucursal != 0) {
      foreach($articulo->precios_sucursales as $precio_sucursal) {
        if ($precio_sucursal->id_sucursal == $id_sucursal) {
          // Reemplazamos los valores por los que corresponden a esa sucursal
          $articulo->costo_neto = $precio_sucursal->costo_neto;
          $articulo->costo_neto_inicial = $precio_sucursal->costo_neto_inicial;
          $articulo->dto_prov = $precio_sucursal->dto_prov;
          $articulo->id_tipo_alicuota_iva = $precio_sucursal->id_tipo_alicuota_iva;
          $articulo->costo_final = $precio_sucursal->costo_final;
          $articulo->porc_ganancia = $precio_sucursal->porc_ganancia;
          $articulo->precio_final = $precio_sucursal->precio_final;
          $articulo->porc_bonif = $precio_sucursal->porc_bonif;
          $articulo->precio_final_dto = $precio_sucursal->precio_final_dto;
          $articulo->ganancia = $precio_sucursal->ganancia;
          $articulo->precio_neto = $precio_sucursal->precio_neto;
          $articulo->precio_final_dto = $precio_sucursal->precio_final_dto;
          $articulo->porc_iva = $precio_sucursal->porc_iva;
          $articulo->costo_iva = $precio_sucursal->costo_iva;
          $articulo->lista_precios = $precio_sucursal->activo;
          $articulo->porc_ganancia_2 = $precio_sucursal->porc_ganancia_2;
          $articulo->precio_final_2 = $precio_sucursal->precio_final_2;
          $articulo->porc_bonif_2 = $precio_sucursal->porc_bonif_2;
          $articulo->precio_final_dto_2 = $precio_sucursal->precio_final_dto_2;
          $articulo->porc_ganancia_3 = $precio_sucursal->porc_ganancia_3;
          $articulo->precio_final_3 = $precio_sucursal->precio_final_3;
          $articulo->porc_bonif_3 = $precio_sucursal->porc_bonif_3;
          $articulo->precio_final_dto_3 = $precio_sucursal->precio_final_dto_3;
          $articulo->moneda = $precio_sucursal->moneda;
        }
      }
    }

    // Si enviamos el parametro para consultar el stock
    $articulo->stock = 0;
    if ($consultar_stock == 1) {
      $this->load->model("Stock_Model");
      $articulo->stock = $this->Stock_Model->get_stock($articulo->id,array(
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
      ));
    }

    // Consultamos si hay alguna oferta activa para ese articulo
    if ($id_sucursal != 0) {
      $ahora = date("Y-m-d H:i:s");
      $sql = "SELECT * FROM articulos_descuentos_sucursales ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "AND id_articulo = $articulo->id ";
      $sql.= "AND desde <= '$ahora' ";
      $sql.= "AND '$ahora' <= hasta ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $r = $q->row();
        $articulo->porc_bonif = $r->porc_bonif;
        $articulo->precio_final_dto = $r->precio_final;
      }
    }

    echo json_encode(array(
      "error"=>0,
      "articulo"=>$articulo,
    ));
  }

  function get_by_nplu($nplu='') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.lista_precios >= 1 ";
    $sql.= "AND (A.nplu = '$nplu') ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      echo json_encode(array(
        "error"=>0,
        "articulo"=>$row,
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un articulo con el PLU: '$nplu'"
      ));
    }
  }    


  function get_by_codigo_proveedor() {

    $id_empresa = parent::get_empresa();
    $codigo = parent::get_post("codigo");
    $id_proveedor = parent::get_post("id_proveedor");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $sql = "SELECT A.id ";
    $sql.= "FROM articulos_proveedores AP ";
    $sql.= "INNER JOIN articulos A ON (AP.id_articulo = A.id AND AP.id_empresa = AP.id_empresa) ";
    $sql.= "WHERE AP.id_empresa = $id_empresa AND AP.codigo = '$codigo' AND AP.id_proveedor = '$id_proveedor' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      $row = $q->row();
      $articulo = $this->modelo->get($row->id);

      // Ponemos los precios de acuerdo a la sucursal que va
      if ($id_sucursal != 0) {
        foreach($articulo->precios_sucursales as $precio_sucursal) {
          if ($precio_sucursal->id_sucursal == $id_sucursal) {
            // Reemplazamos los valores por los que corresponden a esa sucursal
            $articulo->costo_neto = $precio_sucursal->costo_neto;
            $articulo->costo_neto_inicial = $precio_sucursal->costo_neto_inicial;
            $articulo->dto_prov = $precio_sucursal->dto_prov;
            $articulo->id_tipo_alicuota_iva = $precio_sucursal->id_tipo_alicuota_iva;
            $articulo->costo_final = $precio_sucursal->costo_final;
            $articulo->porc_ganancia = $precio_sucursal->porc_ganancia;
            $articulo->precio_final = $precio_sucursal->precio_final;
            $articulo->porc_bonif = $precio_sucursal->porc_bonif;
            $articulo->precio_final_dto = $precio_sucursal->precio_final_dto;
            $articulo->ganancia = $precio_sucursal->ganancia;
            $articulo->precio_neto = $precio_sucursal->precio_neto;
            $articulo->precio_final_dto = $precio_sucursal->precio_final_dto;
            $articulo->porc_iva = $precio_sucursal->porc_iva;
            $articulo->costo_iva = $precio_sucursal->costo_iva;
            $articulo->lista_precios = $precio_sucursal->activo;
            $articulo->porc_ganancia_2 = $precio_sucursal->porc_ganancia_2;
            $articulo->precio_final_2 = $precio_sucursal->precio_final_2;
            $articulo->porc_bonif_2 = $precio_sucursal->porc_bonif_2;
            $articulo->precio_final_dto_2 = $precio_sucursal->precio_final_dto_2;
            $articulo->porc_ganancia_3 = $precio_sucursal->porc_ganancia_3;
            $articulo->precio_final_3 = $precio_sucursal->precio_final_3;
            $articulo->porc_bonif_3 = $precio_sucursal->porc_bonif_3;
            $articulo->precio_final_dto_3 = $precio_sucursal->precio_final_dto_3;
            $articulo->moneda = $precio_sucursal->moneda;
          }
        }
      }
      echo json_encode(array(
        "error"=>0,
        "articulo"=>$articulo,
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un articulo con el codigo: '$codigo' para ese proveedor"
      ));
    }
  }    

  function get_web_autocomplete() {
    $id_empresa = $this->input->get("id_empresa");
    $descripcion = $this->input->get("term");

    $resultado = array();
    $sql = "SELECT * FROM articulos_etiquetas A ";
    $sql.= "WHERE (A.nombre LIKE '%".$this->db->escape_like_str($descripcion)."%' ESCAPE '!') ";
    $sql.= "AND A.id_empresa = '".$this->db->escape_str($id_empresa)."' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $rr->link = $r->nombre;
      $rr->tag = 1;
      $resultado[] = $rr;
    }

    $sql = "SELECT A.*, MATCH(A.nombre) AGAINST ('".$this->db->escape_str($descripcion)."') AS relevance ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.lista_precios >= 1 ";
    $sql.= "AND (A.codigo = '".$this->db->escape_str($descripcion)."' ";
    $sql.= "OR MATCH(A.nombre) AGAINST ('".$this->db->escape_str($descripcion)."') ";
    $sql.= "OR A.nombre LIKE '%".$this->db->escape_like_str($descripcion)."%' ESCAPE '!' ) ";
    $sql.= "AND A.id_empresa = '".$this->db->escape_str($id_empresa)."' ";
    $sql.= "ORDER BY relevance DESC ";
    $sql.= "LIMIT 0,30 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $rr->link = $r->link;
      $rr->tag = 0;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }


  function get_by_descripcion() {
    $id_empresa = parent::get_empresa();
    $descripcion = $this->input->get("term");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $ver_codigo = parent::get_get("ver_codigo",0);

    $sql = "SELECT A.* ";
    $sql.= "FROM articulos A ";
    $sql.= "WHERE A.lista_precios >= 1 ";
    $sql.= "AND A.nombre LIKE '%$descripcion%' ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {

      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->id_real = $r->id;
      $rr->value = $r->id;
      $rr->label = $r->nombre;
      if ($ver_codigo == 1) $rr->label.= " - COD: ".$r->codigo;
      $rr->path = $r->path;
      $rr->codigo = $r->codigo;

      if ($id_sucursal != 0) {
        $articulo = $this->modelo->get($r->id);
        foreach($articulo->precios_sucursales as $precio_sucursal) {
          if ($precio_sucursal->id_sucursal == $id_sucursal) {
            // Reemplazamos los valores por los que corresponden a esa sucursal
            $rr->costo_neto = $precio_sucursal->costo_neto;
            $rr->costo_neto_inicial = $precio_sucursal->costo_neto_inicial;
            $rr->dto_prov = $precio_sucursal->dto_prov;
            $rr->id_tipo_alicuota_iva = $precio_sucursal->id_tipo_alicuota_iva;
            $rr->costo_final = $precio_sucursal->costo_final;
            $rr->porc_ganancia = $precio_sucursal->porc_ganancia;
            $rr->precio_final = $precio_sucursal->precio_final;
            $rr->porc_bonif = $precio_sucursal->porc_bonif;
            $rr->precio_final_dto = $precio_sucursal->precio_final_dto;
            $rr->ganancia = $precio_sucursal->ganancia;
            $rr->precio_neto = $precio_sucursal->precio_neto;
            $rr->precio_final_dto = $precio_sucursal->precio_final_dto;
            $rr->porc_iva = $precio_sucursal->porc_iva;
            $rr->costo_iva = $precio_sucursal->costo_iva;
            $rr->lista_precios = $precio_sucursal->activo;
            $rr->porc_ganancia_2 = $precio_sucursal->porc_ganancia_2;
            $rr->precio_final_2 = $precio_sucursal->precio_final_2;
            $rr->porc_bonif_2 = $precio_sucursal->porc_bonif_2;
            $rr->precio_final_dto_2 = $precio_sucursal->precio_final_dto_2;
            $rr->porc_ganancia_3 = $precio_sucursal->porc_ganancia_3;
            $rr->precio_final_3 = $precio_sucursal->precio_final_3;
            $rr->porc_bonif_3 = $precio_sucursal->porc_bonif_3;
            $rr->precio_final_dto_3 = $precio_sucursal->precio_final_dto_3;
            $rr->moneda = $precio_sucursal->moneda;
          }
        }
      }
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }    


  /**
   *  Obtenemos los datos de un articulo en particular
   */
  function get($id,$id_empresa = 0) {
    $id_empresa = ($id_empresa != 0) ? $id_empresa : parent::get_empresa();
      // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT A.* ";
      $sql.= "FROM articulos A ";
      $sql.= "WHERE A.lista_precios >= 1 AND id_empresa = $id_empresa ";
      $sql.= "ORDER BY A.nombre ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $articulo = $this->modelo->get($id,$id_empresa);
      echo json_encode($articulo);
    }
  }


  /**
   *  Muestra todos los articulos filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver($min=0) {

    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $id_proveedor = $this->input->get("id_proveedor");
    $ids_rubros = str_replace("-", ",", parent::get_get("ids_rubros",""));
    $ids_proveedores = str_replace("-", ",", parent::get_get("ids_proveedores",""));
    $not_ids_proveedores = str_replace("-", ",", parent::get_get("not_ids_proveedores",""));
    $id_marca = $this->input->get("id_marca");
    $id_rubro = $this->input->get("id_rubro");
    $id_etiqueta = $this->input->get("id_etiqueta");
    $id_promocion = $this->input->get("id_promocion");
    $fecha = parent::get_get("fecha","");
    $fecha_tipo = parent::get_get("fecha_tipo","");
    $mostrar = $this->input->get("mostrar");
    $id_usuario = ($this->input->get("id_usuario") === FALSE) ? 0 : $this->input->get("id_usuario");
    $id_departamento = ($this->input->get("id_departamento") === FALSE) ? 0 : $this->input->get("id_departamento");
    $negado = $this->input->get("negado");
    $tipo_busqueda = $this->input->get("tipo_busqueda");         
    $id_sucursal = parent::get_get("id_sucursal",0);
    $custom_5 = parent::get_get("custom_5","");
    $descuento = parent::get_get("descuento",-1);
    $activo = parent::get_get("activo",-1);
    $imagen = parent::get_get("imagen",-1);
    $destacado = parent::get_get("destacado",-1);
    $buscar_stock = parent::get_get("buscar_stock",0);
    $filtro_stock = parent::get_get("filtro_stock","");
    $codigo_prov = parent::get_get("codigo_prov","");
    $mercadolibre = parent::get_get("mercadolibre","");

    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    if ($order_by == "proveedor") $order_by = "custom_6";
    $order = $this->input->get("order");

    $id_empresa = parent::get_empresa();
    if ($id_empresa == 571 && $order_by == "custom_1") $order = "CAST(custom_1 AS SIGNED) $order ";
    else if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";

    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);

    $r = $this->modelo->buscar(array(
      "min"=>$min, // Parametro para obtener una version minima
      "filter"=>$filter,
      "codigo_prov"=>$codigo_prov,
      "mercadolibre"=>$mercadolibre,
      "filtro_stock"=>$filtro_stock,
      "activo"=>$activo,
      "imagen"=>$imagen,
      "destacado"=>$destacado,
      "buscar_stock"=>$buscar_stock,
      "id_proveedor"=>$id_proveedor,
      "id_promocion"=>$id_promocion,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_etiqueta"=>$id_etiqueta,
      "id_departamento"=>$id_departamento,
      "id_usuario"=>$id_usuario,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "descuento"=>$descuento,
      "negado"=>$negado,
      "tipo_busqueda"=>$tipo_busqueda,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "custom_5"=>$custom_5,
      "ids_proveedores"=>$ids_proveedores,
      "not_ids_proveedores"=>$not_ids_proveedores,
      "ids_rubros"=>$ids_rubros,
    ));
    echo json_encode($r);
  }

  function exportar() {

    $id_empresa = parent::get_empresa();
    $filter = urldecode(parent::get_get("texto",""));
    $id_proveedor = parent::get_get("id_proveedor",0);
    $id_departamento = parent::get_get("id_departamento",0);
    $id_marca = parent::get_get("id_marca",0);
    $id_rubro = parent::get_get("id_rubro",0);
    $activo = parent::get_get("activo",-1);
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Primero buscamos
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "fecha"=>$fecha,
      "activo"=>$activo,
      "fecha_tipo"=>$fecha_tipo,
      "id_departamento"=>$id_departamento,
      "order"=>$order,
      "buscar_stock"=>(($empresa->id_proyecto != 1) ? 1 : 0),
    ));
    $datos = array();

    // SI ES PYMVAR
    if ($empresa->id_proyecto == 1) {

      $header = array("Codigo","EAN","Cod. Prov.","Descripcion","Costo Neto");
      foreach($r["results"] as $ar) {

        // Explotamos el codigo de barra
        $codigos_barras = explode("###", $ar->codigo_barra);

        for($j=0;$j<sizeof($codigos_barras);$j++) {
          $cod = $codigos_barras[$j];
          $codigo_proveedor = "";
          if (isset($ar->proveedores[$j])) {
            $cp = $ar->proveedores[$j];
            $codigo_proveedor = $cp->codigo;
          }
          if ($j==0) {
            $datos[] = array(
              "codigo"=>$ar->codigo,
              "codigo_barra"=>$cod,
              "codigo_proveedor"=>$codigo_proveedor,
              "nombre"=>$ar->nombre,
              "costo_neto"=>$ar->costo_neto,
              "precio_final"=>$ar->precio_final_dto,
              "precio_final_2"=>$ar->precio_final_dto_2,
              "precio_final_3"=>$ar->precio_final_dto_3,
              "precio_final_4"=>$ar->precio_final_dto_4,
              "precio_final_5"=>$ar->precio_final_dto_5,
              "precio_final_6"=>$ar->precio_final_dto_6,
            );
          } else {
            $datos[] = array(
              "codigo"=>"",
              "codigo_barra"=>$cod,
              "codigo_proveedor"=>$codigo_proveedor,
              "nombre"=>"",
              "costo_neto"=>"",
              "precio_final"=>"",
              "precio_final_2"=>"",
              "precio_final_3"=>"",
              "precio_final_4"=>"",
              "precio_final_5"=>"",
              "precio_final_6"=>"",
            );          
          }
        }
      }

    } else {
    // SI ES ALGUNO DE LOS OTROS PROYECTOS
      $header = array("Codigo","Nombre","Stock","Costo");
      foreach($r["results"] as $ar) {
        $stock = 0;
        foreach($ar->stock_almacenes as $sa) {
          $stock += $sa->stock_actual;
        }
        $datos[] = array(
          "codigo"=>$ar->codigo,
          "nombre"=>$ar->nombre,
          "stock"=>$stock,
          "costo_final"=>$ar->costo_final,
          "precio_final"=>$ar->precio_final_dto,
          "precio_final_2"=>$ar->precio_final_dto_2,
          "precio_final_3"=>$ar->precio_final_dto_3,
          "precio_final_4"=>$ar->precio_final_dto_4,
          "precio_final_5"=>$ar->precio_final_dto_5,
          "precio_final_6"=>$ar->precio_final_dto_6,
        );        
      }
    }

    // Obtenemos el nombre de la lista de precios
    $sql = "SELECT * FROM lista_precios_configuracion WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $rr = $q->row();
      $header[] = (!empty($rr->lista_1_nombre) ? $rr->lista_1_nombre : "Lista 1");
      $header[] = (!empty($rr->lista_2_nombre) ? $rr->lista_2_nombre : "Lista 2");
      $header[] = (!empty($rr->lista_3_nombre) ? $rr->lista_3_nombre : "Lista 3");
      $header[] = (!empty($rr->lista_4_nombre) ? $rr->lista_4_nombre : "Lista 4");
      $header[] = (!empty($rr->lista_5_nombre) ? $rr->lista_5_nombre : "Lista 5");
      $header[] = (!empty($rr->lista_6_nombre) ? $rr->lista_6_nombre : "Lista 6");
    } else {
      $header[] = "Lista 1";
      $header[] = "Lista 2";
      $header[] = "Lista 3";
      $header[] = "Lista 4";
      $header[] = "Lista 5";
      $header[] = "Lista 6";
    }

    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>"",
      "filename"=>"articulos",
      "footer"=>array(),
      "header"=>$header,
      "data"=>$datos,
      "title"=>"",
    ));        
  }

  function exportar_csv() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $this->load->dbutil();
    $this->load->helper('download');
    $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa AND lista_precios > 0";
    $query = $this->db->query($sql);
    $salida = $this->dbutil->csv_from_result($query, ";", "\r\n");
    force_download('articulos.csv', $salida);
  }

  function importar_fotos_zip() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = parent::get_empresa();
    $this->load->helper("file_helper");
    $this->load->helper("imagen_helper");
    $base = "/home/ubuntu/data/sistema/uploads/$id_empresa/articulos/";

    // En Esteban Echeverria, cargar las imagenes dentro de las carpetas segun el ID_USUARIO
    if ($id_empresa == 1284) {
      @session_start();
      $id_usuario = $_SESSION["id"];
      $base = $base.$id_usuario."/";
      if (!file_exists($base)) @mkdir($base);
    }

    $file = $base.filename($_FILES["file"]["name"],"-",0);
    $r = move_uploaded_file($_FILES["file"]["tmp_name"],$file);
    if ($r === FALSE) {
      // Deberiamos agregar algun parametro de error
      header("Location: /sistema/app/#articulos");
    }
    $archivos = array();
    $zip = new ZipArchive;
    $res = $zip->open($file);
    if ($res === TRUE) {
      // Recorremos los archivos que componen el ZIP, para poder luego renombrarlos
      for( $i = 0; $i < $zip->numFiles; $i++ ){ 
        $stat = $zip->statIndex($i);
        $archivos[] = $stat['name'];
      }      
      // Extraemos
      $zip->extractTo($base);
      $zip->close();
      // Renombramos los archivos
      foreach($archivos as $a) {
        $new = filename($a,"-",0);
        rename($base.$a, $base.$new);
        resize(array(
          "dir"=>$base,
          "filename"=>$new,
          "max_width"=>800,
          "max_height"=>800,
        ));
      }
    }
    @unlink($file);
    header("Location: /sistema/app/#articulos");
  }

  function importar_excel() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = parent::get_empresa();
    $tabla = "articulos";
    try {
      $id = parent::start_import_excel(array(
        "tabla"=>$tabla
      ));
    } catch(Exception $e) {
      header("Location: /sistema/app/#$tabla");
    }
    header("Location: /sistema/app/#importacion/$tabla/$id");  
  }

  function importar() {
    $tabla = "articulos";
    parent::import($tabla,1);
    header("Location: /sistema/app/#$tabla");
  }

  function importar_csv() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $file = "../importar/articulos.csv";
    if (!file_exists($file)) {
      echo "No se encuentra el archivo importar/articulos.csv";
      exit();
    }
    $id_empresa = parent::get_empresa();
    $tabla = "articulos";
    $this->load->helper("import_helper");
    $db = $this->db;
    importar_tabla_csv(array(
      "tabla"=>"articulos",
      "borrar_tabla"=>1,
      "id_empresa"=>$id_empresa,
      "archivo"=>$file,
      "db"=>$db,
    ));
    echo "TERMINO IMPORTACION. PUEDE CERRAR ESTA PESTAÑA Y RECARGAR EL SISTEMA.";
  }

  function imprimir_precios() {
    $id_empresa = parent::get_empresa();
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $stock = parent::get_post("stock",-1);
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $id_sucursal = parent::get_post("id_sucursal");
    $mostrar = parent::get_post("mostrar");
    $negado = parent::get_post("negado");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "id_proveedor"=>$id_proveedor,
      "id_sucursal"=>$id_sucursal,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "custom_5"=>$custom_5,
      "mostrar"=>$mostrar,
      "negado"=>$negado,
      "in_ids"=>$in_ids,
    ));
    $r["id_empresa"] = $id_empresa;
    $this->load->view("reports/cartelitos",$r);
  }


  function imprimir_precios_grandes() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $id_marca = parent::get_post("id_marca");
    $id_sucursal = parent::get_post("id_sucursal");
    $id_rubro = parent::get_post("id_rubro");
    $stock = parent::get_post("stock",-1);
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "id_proveedor"=>$id_proveedor,
      "id_sucursal"=>$id_sucursal,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "custom_5"=>$custom_5,
      "mostrar"=>$mostrar,
      "in_ids"=>$in_ids,
    ));
    $this->load->view("reports/cartelitos_grandes",$r);
  }


  function imprimir_precios_medianos() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $stock = parent::get_post("stock",-1);
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "custom_5"=>$custom_5,
      "mostrar"=>$mostrar,
      "in_ids"=>$in_ids,
    ));
    $this->load->view("reports/cartelitos_medianos",$r);
  }    

  function imprimir_precios_medianos_sin_oferta() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $stock = parent::get_post("stock",-1);
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "custom_5"=>$custom_5,
      "in_ids"=>$in_ids,
    ));
    $this->load->view("reports/cartelitos_medianos_sin_oferta",$r);
  } 

  function imprimir_ofertas() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $stock = parent::get_post("stock",-1);
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "custom_5"=>$custom_5,
      "in_ids"=>$in_ids,
    ));
    $this->load->view("reports/ofertas",$r);
  }  

  function imprimir_por_proveedor() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_get("id_sucursal",0);
    $con_ventas_desde = parent::get_get("con_ventas_desde","");
    $this->load->helper("fecha_helper");
    if (!empty($con_ventas_desde)) $con_ventas_desde = fecha_mysql($con_ventas_desde);

    $sql = "SELECT A.id, A.codigo, A.nombre, A.codigo_barra, APS.precio_final, APS.precio_final_dto ";
    $sql.= "FROM articulos_precios_sucursales APS INNER JOIN articulos A ON (APS.id_empresa = A.id_empresa AND APS.id_articulo = A.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND APS.id_sucursal = $id_sucursal ";
    if (!empty($con_ventas_desde)) $sql.= "AND EXISTS (SELECT 1 FROM facturas_items FI INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) WHERE APS.id_articulo = FI.id_articulo AND FI.anulado = 0 AND F.anulada = 0 AND F.fecha >= '$con_ventas_desde') ";
    $q = $this->db->query($sql);
    $set = array();
    foreach($q->result() as $r) {
      $proveedor = $this->modelo->get_primer_proveedor($r->id);
      if ($proveedor === FALSE) continue;
      if (!isset($set[$proveedor->nombre])) {
        $set[$proveedor->nombre] = array();
      }

      $r->codigo_proveedor = "";
      $sql = "SELECT AP.codigo FROM articulos_proveedores AP WHERE id_empresa = $id_empresa ";
      $sql.= "AND AP.id_proveedor = $proveedor->id AND AP.id_articulo = $r->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $r->codigo_proveedor = $rr->codigo;
      }

      $set[$proveedor->nombre][0][] = $r;
    }
    ksort($set);

    $this->load->view("reports/articulos",array(
      "resultados"=>$set,
      "ver_listas"=>"1",
    ));    
  }  


  function imprimir() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $stock = parent::get_post("stock",-1);
    $filtro_stock = parent::get_post("filtro_stock","");
    $activo = parent::get_post("activo",-1);
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $ver_listas = parent::get_post("ver_listas","1-1-1-1-1-1");

    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_proveedor"=>$id_proveedor,
      "stock"=>$stock,
      "filtro_stock"=>$filtro_stock,
      "activo"=>$activo,
      "id_sucursal"=>$id_sucursal,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "custom_5"=>$custom_5,
      "mostrar"=>$mostrar,
      "in_ids"=>$in_ids,
    ));
    
    // Agrupamos los items de acuerdo al rubro
    $set = array();
    foreach($r["results"] as $item) {
      if (!isset($set[$item->rubro])) {
        $set[$item->rubro] = array();
      }
      if (isset($item->subrubro)) {
        $set[$item->rubro][$item->subrubro][] = $item;  
        ksort($set[$item->rubro]);
      } else {
        if (empty($item->rubro)) $item->rubro = array();
        $set[$item->rubro][0][] = $item;
      }
    }
    ksort($set);

    $this->load->view("reports/articulos",array(
      "resultados"=>$set,
      "ver_listas"=>$ver_listas,
    ));
  }

  function imprimir_costos() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $stock = parent::get_post("stock",-1);
    $activo = parent::get_post("activo",-1);
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "activo"=>$activo,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "custom_5"=>$custom_5,
      "in_ids"=>$in_ids,
    ));
    $this->load->view("reports/costos",$r);
  }    

  function imprimir_stock() {
    $filter = urldecode(parent::get_post("texto"));
    $stock = parent::get_post("stock",-1);
    $activo = parent::get_post("activo",-1);
    $id_proveedor = parent::get_post("id_proveedor");
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $custom_5 = parent::get_post("custom_5","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "activo"=>$activo,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "custom_5"=>$custom_5,
      "in_ids"=>$in_ids,
    ));

    // Tomamos los datos del proveedor
    if ($id_proveedor != 0) {
      $this->load->model("Proveedor_Model");
      $proveedor = $this->Proveedor_Model->get($id_proveedor);
    } else {
      $proveedor = null;
    }

    // Agrupamos los items de acuerdo al rubro
    $set = array();
    foreach($r["results"] as $item) {
      if (!isset($set[$item->rubro])) {
        $set[$item->rubro] = array();
      }
      if (isset($item->subrubro)) {
        $set[$item->rubro][$item->subrubro][] = $item;  
        ksort($set[$item->rubro]);
      } else {
        if (empty($item->rubro)) $item->rubro = array();
        $set[$item->rubro][0][] = $item;
      }
    }
    ksort($set);
    $this->load->view("reports/listado_stock",array(
      "resultados"=>$set,
      "proveedor"=>$proveedor,
    ));
  }

  function imprimir_plu() {
    $filter = urldecode(parent::get_post("texto"));
    $id_proveedor = parent::get_post("id_proveedor");
    $stock = parent::get_post("stock",-1);
    $activo = parent::get_post("activo",-1);
    $id_marca = parent::get_post("id_marca");
    $id_rubro = parent::get_post("id_rubro");
    $id_sucursal = parent::get_post("id_sucursal");
    $fecha = parent::get_post("fecha");
    $fecha_tipo = parent::get_post("fecha_tipo","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $mostrar = parent::get_post("mostrar");
    $in_ids = parent::get_post("in_ids");
    $negado = parent::get_post("negado");
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "stock"=>$stock,
      "activo"=>$activo,
      "id_proveedor"=>$id_proveedor,
      "id_marca"=>$id_marca,
      "id_rubro"=>$id_rubro,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$fecha,
      "fecha_tipo"=>$fecha_tipo,
      "mostrar"=>$mostrar,
      "in_ids"=>$in_ids,
      "negado"=>$negado,
      "tiene_plu"=>1,
    ));
    
    // Agrupamos los items de acuerdo al rubro
    $set = array();
    foreach($r["results"] as $item) {
      if (!isset($set[$item->rubro])) {
        $set[$item->rubro] = array();
      }
      if (isset($item->subrubro)) {
        $set[$item->rubro][$item->subrubro][] = $item;  
        ksort($set[$item->rubro]);
      } else {
        if (empty($item->rubro)) $item->rubro = array();
        $set[$item->rubro][0][] = $item;
      }
    }
    ksort($set);
    $this->load->view("reports/plus",array(
      "resultados"=>$set
    ));
  }

  function importar_megacompras() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);
    $id_empresa = 1277;
    $file = "/home/ubuntu/data/sistema/uploads/1277/Ecommerce.csv";
    //Codigo Interno;Nombre del producto;Categoria;Subcategoria;Descripcion;Codigo Sucursal;Marca;Precio;Stock;Fotos
    $f = fopen($file,"r+");
    $i = 0;
    while(($linea = fgets($f))!==FALSE) {
      // La primera linea no la tomamos
      if ($i==0) { $i++; continue; }
      $linea = trim($linea);
      $campos = explode(";", $linea);
      $codigo = intval($campos[0]);
      $nombre = $campos[1];
      $nombre = str_replace("'", "", $nombre);
      $nombre = str_replace('"', "", $nombre);
      $categoria = $campos[2];
      $categoria = str_replace("'", "", $categoria);
      $categoria = str_replace('"', "", $categoria);
      $subcategoria = $campos[3];
      $subcategoria = str_replace("'", "", $subcategoria);
      $subcategoria = str_replace('"', "", $subcategoria);
      $descripcion = $campos[4];
      $descripcion = str_replace("'", "", $descripcion);
      $descripcion = str_replace('"', "", $descripcion);
      $id_sucursal = intval($campos[5]);
      $marca = $campos[6];
      $marca = str_replace("'", "", $marca);
      $marca = str_replace('"', "", $marca);
      $precio = floatval($campos[7]);
      $stock = floatval($campos[8]);

      // Guardamos las categorias
      $id_rubro = 0;
      $this->load->model("Rubro_Model");
      if (!empty($categoria)) {
        $id_rubro = $this->Rubro_Model->create(array(
          "id_empresa"=>$id_empresa,
          "nombre"=>$categoria,
          "id_padre"=>0,
        ));
        if (!empty($subcategoria)) {
          $id_rubro = $this->Rubro_Model->create(array(
            "id_empresa"=>$id_empresa,
            "nombre"=>$subcategoria,
            "id_padre"=>$id_rubro,
          ));          
        }
      }

      // Guardamos las marcas
      $id_marca = 0;
      $this->load->model("Marca_Model");
      if (!empty($marca)) {
        $id_marca = $this->Marca_Model->create(array(
          "id_empresa"=>$id_empresa,
          "nombre"=>$marca,
        ));
      }

      // Creamos el link
      $this->load->model("Empresa_Model");
      $link = $this->Empresa_Model->get_base_link(array("clave"=>"producto","id_empresa"=>$id_empresa))."/".filename($nombre,"-",0)."-".$codigo."/";

      // Guardamos los datos del articulo
      $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa AND id = '$codigo' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {

        // El articulo ya existe, lo modificamos
        $sql = "UPDATE articulos SET ";
        //$sql.= " nombre = '$nombre', ";
        $sql.= " link = '$link', ";
        $sql.= " fecha_mov = NOW(), ";
        //$sql.= " lista_precios = 2, "; // Para que no se reemplacen los destacados
        $sql.= " texto = '$descripcion', ";
        $sql.= " id_rubro = '$id_rubro', ";
        $sql.= " id_marca = '$id_marca' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = '$codigo' ";
        $this->db->query($sql);

      } else {
        // Cargamos el articulo
        $sql = "INSERT INTO articulos (";
        $sql.= " id, codigo, nombre, tipo, id_empresa, texto, id_rubro, id_marca, fecha_ingreso, moneda, id_tipo_alicuota_iva, porc_iva, seo_title, fecha_mov, lista_precios, link ";
        $sql.= ") VALUES (";
        $sql.= " '$codigo', '$codigo', '$nombre', 0, '$id_empresa', '$descripcion', $id_rubro, $id_marca, NOW(), 1, 5, 21, '$nombre', NOW(), 2, '$link' ";
        $sql.= ")";
        $this->db->query($sql);
      }

      // Guardamos los datos del stock
      $sql = "SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_articulo = $codigo AND id_sucursal = $id_sucursal ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $this->db->query("UPDATE stock SET stock_actual = $stock WHERE id_empresa = $id_empresa AND id_articulo = $codigo AND id_sucursal = $id_sucursal ");
      } else {
        $this->db->query("INSERT INTO stock (id_articulo,id_sucursal,stock_actual,id_empresa) VALUES ($codigo,$id_sucursal,$stock,$id_empresa)");
      }

      // Guardamos el precio de esa sucursal
      $sql = "SELECT * FROM articulos_precios_sucursales WHERE id_empresa = $id_empresa AND id_articulo = $codigo AND id_sucursal = $id_sucursal ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $this->db->query("UPDATE articulos_precios_sucursales SET precio_final_dto = $precio, precio_final = $precio, fecha_mov = NOW() WHERE id_empresa = $id_empresa AND id_articulo = $codigo AND id_sucursal = $id_sucursal ");
      } else {
        $this->db->query("INSERT INTO articulos_precios_sucursales (id_articulo,id_sucursal,id_empresa,fecha_mov,id_tipo_alicuota_iva,porc_iva,precio_final,precio_final_dto,activo) VALUES ($codigo,$id_sucursal,$id_empresa,NOW(),5,21,$precio,$precio,1)");
      }
      $i++;
    } 
    fclose($f);
    echo "TERMINO $i";
  }


  function importar_vulca($id_empresa,$es_web = 1) {

    $lineas = array();
    if ($es_web == 1) {
      $file = "uploads/".$_FILES["file"]["name"];
      if (move_uploaded_file($_FILES["file"]["tmp_name"],$file)) {
        $f = fopen($file,"r+");
        while(($linea = fgets($f))!==FALSE) $lineas[] = trim($linea);
        fclose($f);
      } else {
        echo "No se encuentra el archivo $file"; exit();
      }
    } else {
      // Si no es web, tenemos que tomar el archivo subido por FTP
      $file = "/var/www/vulca2/PRODUTECA.TXT";
      if (!file_exists($file)) {
        echo "El archivo $file no existe."; exit();
      }
      $f = fopen($file,"r+");
      while(($linea = fgets($f))!==FALSE) $lineas[] = trim($linea);
      fclose($f);
    }

    // Buscamos las promociones activas
    $sql = "SELECT * FROM promociones WHERE id_empresa = $id_empresa AND activo = 1 ";
    $q_promo = $this->db->query($sql);
    $promos = array();
    foreach($q_promo->result() as $promo) {
      $promos[] = $promo->id;
    }

    // Desactivamos todos los articulos, para que los que no estan en el archivo no aparezcan en la web
    $sql = "UPDATE articulos SET lista_precios = 0 WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_rubro IN (233,235,236,237,238) "; // Solo en VULCATIRES
    $this->db->query($sql);

    $i = 0;
    foreach($lineas as $linea) {
      $campos = explode(";", $linea);
      $codigo = $campos[0];
      if ($codigo == "0091010233") continue;
      $stock = $campos[3];
      $precio = (float)$campos[4];

      $sql = "SELECT A.*, IF(M.descuento IS NULL,0,M.descuento) AS marca_descuento ";
      $sql.= "FROM articulos A LEFT JOIN marcas M ON (A.id_empresa = M.id_empresa AND A.id_marca = M.id) ";
      $sql.= "WHERE A.id_empresa = $id_empresa AND A.codigo = '$codigo' ";
      $q_art = $this->db->query($sql);
      if ($q_art->num_rows()>0) {
        $art = $q_art->row();
        $precio_dto = $precio;

        // Al precio debemos aplicarle el descuento de la marca
        $precio_dto = round($precio * ((100 - $art->marca_descuento) / 100),2);

        /*
        if ($art->id_marca == 54) {
          // GOODYEAR
          $precio_dto = round($precio * 0.75,2); 
        } else if ($art->id_marca == 55) {
          // TOYOTIRES
          $precio_dto = round($precio * 0.85,2); // Al precio debemos aplicarle un descuento
        }
        */
        $precio = round($precio,2);
        $porc_bonif = round((($precio - $precio_dto) / $precio) * 100,0);

        // Actualizamos los precios SI NO ESTAN EN UNA PROMOCION ACTIVA
        if (!in_array($art->id_promocion, $promos)) {
          $sql = "UPDATE articulos SET ";
          $sql.= "lista_precios = 2, ";
          $sql.= "precio_final = '$precio', ";
          $sql.= "porc_bonif = '$porc_bonif', ";
          $sql.= "precio_final_dto = '$precio_dto' ";
          $sql.= "WHERE id_empresa = $id_empresa AND id = '$art->id' AND codigo = '$codigo' ";
        } else {

          // Buscamos la promocion
          $sql = "SELECT * FROM promociones WHERE id_empresa = $id_empresa AND id = $art->id_promocion ";
          $q_promo = $this->db->query($sql);
          $p = $q_promo->row();
          // Calculamos el descuento en base a lo que ya tiene
          $porc_bonif = round($art->porc_bonif,2);
          $precio_dto = round($precio * ((100 - $porc_bonif) / 100),2);

          $sql = "UPDATE articulos SET ";
          $sql.= "lista_precios = 2, ";
          $sql.= "precio_final = '$precio', ";
          $sql.= "precio_final_dto = '$precio_dto' ";
          $sql.= "WHERE id_empresa = $id_empresa AND id = '$art->id' AND codigo = '$codigo' ";
        }
        $this->db->query($sql);

        $sucursales = array(78,491,492);
        foreach($sucursales as $id_sucursal) {
          $sql = "SELECT * FROM stock WHERE id_articulo = $art->id AND id_sucursal = $id_sucursal AND id_empresa = $id_empresa ";
          $q_stock = $this->db->query($sql);
          if ($q_stock->num_rows() > 0) {
            $sql = "UPDATE stock SET stock_actual = $stock ";
            $sql.= "WHERE id_empresa = $id_empresa ";
            $sql.= "AND id_articulo = $art->id AND id_sucursal = $id_sucursal ";
            $this->db->query($sql);
          } else {
            $sql = "INSERT INTO stock (id_articulo,id_sucursal,stock_actual,id_empresa) VALUES (";
            $sql.= "$art->id, $id_sucursal, $stock, $id_empresa )";
            $this->db->query($sql);
          }
          $fecha = date("Y-m-d");
          $sql = "INSERT INTO stock_movimientos (id_articulo,id_sucursal,saldo,cantidad,id_empresa,fecha,movimiento) VALUES (";
          $sql.= "$art->id, $id_sucursal, $stock, $stock, $id_empresa,'$fecha','M' )";
          $this->db->query($sql);
        }

        $i++;
      }
    }
    //if ($es_web) header("Location: /sistema/app/#articulos");
    //else 
    echo "Cantidad de productos actualizados: $i";
  }


  function importar_vultrack($id_empresa = 186) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $this->load->helper("file_helper");
    $file = "/var/www/vulca2/PRODUTECA_VULTRACK.TXT";
    if (!file_exists($file)) {
      echo "El archivo $file no existe."; exit();
    }
    $f = fopen($file,"r+");
    $lineas = array();
    $i=0;
    while(($linea = fgets($f))!==FALSE) {
      if ($i==0) { $i++; continue; }
      $lineas[] = trim($linea);
      $i++;
    }
    fclose($f);

    $cant_insertados = 0;
    $cant_actualizados = 0;
    $id_sucursal = 88;

    foreach($lineas as $linea) {
      $campos = explode(";", $linea);
      $codigo = $campos[0];
      $rubro = $campos[1];
      $nombre = $campos[2];
      $detalle = $campos[3];
      $proveedor = $campos[4];
      $precio = $campos[5];
      $stock = $campos[6];
      $web_subtitulo = $campos[7]; // custom_1
      $web_ceo = $campos[8];
      $web_caracteristicas = $campos[9];
      $web_descripcion = $campos[10];
      $ml_descripcion = $campos[11];
      $peso = $campos[12];
      $alto = $campos[13];
      $largo = $campos[14];
      $ancho = $campos[15];
      $aforo = $campos[16];

      // Consultamos si el rubro ya existe
      $rubro2 = strtoupper($rubro);
      $sql = "SELECT * FROM rubros WHERE UPPER(nombre) = '$rubro2' AND id_empresa = $id_empresa";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        // Obtenemos el ID del rubro
        $row = $q->row();
        $id_rubro = $row->id;

      } else {
        // Debemos insertar el nuevo rubro
        $sql = "SELECT IF(MAX(orden) IS NULL,0,MAX(orden)) AS maximo FROM rubros WHERE id_empresa = $id_empresa";
        $q_max = $this->db->query($sql);
        $maximo = $q_max->row();
        $orden = $maximo->maximo + 1;
        $link = filename($rubro,"-",0);
        $sql = "INSERT INTO rubros (id_empresa, nombre, id_padre, link, orden, activo) VALUES (";
        $sql.= "$id_empresa, '$rubro', 0, '$link', $orden, 1)";
        $this->db->query($sql);
        $id_rubro = $this->db->insert_id();
      }

      // Consultamos si existe el codigo
      $id_articulo = 0;
      $sql = "SELECT * FROM articulos WHERE codigo = '$codigo' AND id_empresa = $id_empresa LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $art = $q->row();
        $id_articulo = $art->id;
        // Debemos actualizarlo
        $sql = "UPDATE articulos SET ";
        $sql.= " fecha_mov = NOW(), ";
        $sql.= " nombre = '$nombre', custom_1 = '$web_subtitulo', ";
        $sql.= " id_rubro = '$id_rubro', ";
        $sql.= " precio_final = '$precio', precio_final_dto = '$precio', ";
        $sql.= " texto = '$web_descripcion', ";
        $sql.= " stock = '$stock', ";
        $sql.= " ancho = '$ancho', ";
        $sql.= " alto = '$alto', ";
        $sql.= " seo_title = '$web_ceo', ";
        $sql.= " profundidad = '$largo', ";
        $sql.= " peso = '$peso' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = '$art->id' ";
        $this->db->query($sql);
        $cant_actualizados++;
      } else {
        // Debemos insertar el nuevo producto
        $sql = "INSERT INTO articulos (";
        $sql.= " codigo, nombre, id_empresa, id_rubro, fecha_ingreso, fecha_mov, id_tipo_alicuota_iva, moneda, ";
        $sql.= " lista_precios, usa_stock, stock, unidad, ancho, alto, profundidad, peso, ";
        $sql.= " precio_final, precio_final_dto, custom_1, texto, seo_title ";
        $sql.= ") VALUES (";
        $sql.= " '$codigo', '$nombre', '$id_empresa', '$id_rubro', NOW(), NOW(), 5, '$', ";
        $sql.= " 2, 1, '$stock', 'U', '$ancho', '$alto', '$largo', '$peso', ";
        $sql.= " '$precio', '$precio', '$web_subtitulo', '$web_descripcion', '$web_ceo' ";
        $sql.= ")";
        $this->db->query($sql);
        $id_articulo = $this->db->insert_id();
        // Actualizamos el link
        $link = "producto/".filename($nombre,"-",0)."-".$id_articulo."/";
        $this->db->query("UPDATE articulos SET link = '$link' WHERE id = $id_articulo AND id_empresa = $id_empresa");
        $cant_insertados++;
      }

      $sql = "SELECT * FROM articulos_meli WHERE id_articulo = '$id_articulo' AND id_empresa = $id_empresa LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $sql = "UPDATE articulos_meli SET ";
        $sql.= " titulo_meli = '$nombre', ";
        $sql.= " texto_meli = '$ml_descripcion', ";
        $sql.= " precio_meli = '$precio' ";
        $sql.= "WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      } else {
        $sql = "INSERT INTO articulos_meli (";
        $sql.= " id_articulo, id_empresa, activo_meli, precio_meli, titulo_meli, texto_meli ";
        $sql.= ") VALUES (";
        $sql.= " $id_articulo, $id_empresa, -1, '$precio', '$nombre', '$ml_descripcion' ";
        $sql.= ")";
        $this->db->query($sql);
      }

      // Actualizamos el stock
      $sql = "SELECT * FROM stock WHERE id_articulo = $id_articulo AND id_sucursal = $id_sucursal AND id_empresa = $id_empresa ";
      $q_stock = $this->db->query($sql);
      if ($q_stock->num_rows() > 0) {
        $sql = "UPDATE stock SET stock_actual = '$stock' ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $id_articulo AND id_sucursal = $id_sucursal ";
        $this->db->query($sql);
      } else {
        $sql = "INSERT INTO stock (id_articulo,id_sucursal,stock_actual,id_empresa) VALUES (";
        $sql.= "$id_articulo, $id_sucursal, '$stock', $id_empresa )";
        $this->db->query($sql);
      }
      $fecha = date("Y-m-d");
      $sql = "INSERT INTO stock_movimientos (id_articulo,id_sucursal,saldo,cantidad,id_empresa,fecha,movimiento) VALUES (";
      $sql.= "$id_articulo, $id_sucursal, '$stock', '$stock', $id_empresa,'$fecha','M' )";
      $this->db->query($sql);

    }

    echo "INSERTADOS: $cant_insertados - ACTUALIZADOS: $cant_actualizados";
  }


  function importar_center($id_empresa,$es_web = 1) {

    $id_categoria_padre_center = 234;
    $this->load->helper("file_helper");
    $lineas = array();
    $marcas_vehiculos = array();
    $categorias = array();
    if ($es_web == 1) {
      $file = "uploads/".$_FILES["file"]["name"];
      if (move_uploaded_file($_FILES["file"]["tmp_name"],$file)) {
        $f = fopen($file,"r+");
        while(($linea = fgets($f))!==FALSE) {
          $linea = trim($linea);
          $obj = new stdClass();
          $campos = explode(";", $linea);
          $obj->codigo = $campos[0];
          $obj->nombre = $campos[1];
          $obj->nombre_meli = $campos[2];
          $obj->categoria = $campos[3];
          $obj->marca = trim(str_replace($campos[3], "", $campos[4]));
          $obj->nombre = str_replace($obj->categoria, "", $obj->nombre);
          $obj->nombre = str_replace($obj->marca, "", $obj->nombre);
          $obj->apto_web = $campos[5];
          $obj->apto_meli = $campos[6];
          $obj->apto_oferta_destacada = $campos[7];
          $obj->kgs = $campos[8];
          $obj->alto = $campos[9];
          $obj->largo = $campos[10];
          $obj->ancho = $campos[11];
          $obj->stock = $campos[12];
          $obj->precio_meli = (float)$campos[13];
          $obj->precio_web = (float)$campos[14];
          // Del campo 15 para adelante, son todas marcas de vehiculos y modelos, separados por "-"
          $obj->vehiculos = array();
          if (sizeof($campos)>15) {
            for($i=15;$i<sizeof($campos);$i++) {
              $v = explode("|",$campos[$i]);
              if (sizeof($v) == 2) {
                $mv = new stdClass();
                $mv->id = 0;
                $mv->nombre = strtoupper($v[0]);
                $marcas_vehiculos[] = $mv;

                $mv1 = new stdClass();
                $mv1->id = 0;
                $mv1->nombre = strtoupper($v[0]);
                $mv1->modelo = $v[1];
                $obj->vehiculos[] = $mv1;
              }
            }
          }
          
          $lineas[] = $obj;

          // Lo agregamos al array de categorias
          $encontro = FALSE;
          foreach($categorias as $cat) {
            if ($cat->nombre == $obj->categoria) {
              $encontro = TRUE;
              break;
            }
          }
          if (!$encontro) {
            $cat = new stdClass();
            $cat->nombre = $obj->categoria;
            $categorias[] = $cat;            
          }
        }
        fclose($f);
      } else {
        echo "No se encuentra el archivo $file"; exit();
      }
    }

    foreach($categorias as $cat) {
      $nombre = strtoupper($cat->nombre);
      $sql = "SELECT * FROM rubros WHERE UPPER(nombre) = '$nombre' AND id_empresa = $id_empresa AND id_padre = $id_categoria_padre_center";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $row = $q->row();
        $cat->id = $row->id;
      } else {
        // Debemos insertar el nuevo rubro

        $sql = "SELECT IF(MAX(orden) IS NULL,0,MAX(orden)) AS maximo FROM rubros WHERE id_empresa = $id_empresa AND id_padre = $id_categoria_padre_center";
        $q_max = $this->db->query($sql);
        $maximo = $q_max->row();
        $orden = $maximo->maximo + 1;

        $link = filename($cat->nombre,"-",0);
        $sql = "INSERT INTO rubros (id_empresa, nombre, id_padre, link, orden, activo) VALUES (";
        $sql.= "$id_empresa, '$cat->nombre', $id_categoria_padre_center, '$link', $orden, 1)";
        $this->db->query($sql);
        $cat->id = $this->db->insert_id();
      }      
    }

    foreach($marcas_vehiculos as $marca_vehiculo) {
      $id_marca_vehiculo = 0;
      $nombre = $marca_vehiculo->nombre;
      $sql = "SELECT * FROM marcas_vehiculos WHERE UPPER(nombre) = '$nombre' AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $row = $q->row();
        $marca_vehiculo->id = $row->id;
      } else {
        // Debemos insertar la nueva marca
        $link = filename($nombre,"-",0);
        $sql = "INSERT INTO marcas_vehiculos (id_empresa, nombre, link, activo) VALUES (";
        $sql.= "$id_empresa, '$nombre', '$link', 1)";
        $this->db->query($sql);
        $marca_vehiculo->id = $this->db->insert_id();
      }
    }

    $i = 0;
    foreach($lineas as $r) {

      // Buscamos la categoria a la que pertenece
      $r->id_rubro = 0;
      foreach($categorias as $cat) {
        if ($cat->nombre == $r->categoria) {
          $r->id_rubro = $cat->id;
          break;
        }
      }

      $string_vehiculos = "";
      foreach($r->vehiculos as $veh) {
        foreach($marcas_vehiculos as $mv) {
          if ($veh->nombre == $mv->nombre) {
            $veh->id = $mv->id; break;
          }
        }
        $string_vehiculos.= $veh->nombre." ".$veh->modelo." ";
      }

      $sql = "SELECT * FROM marcas WHERE nombre = '$r->marca' AND id_empresa = $id_empresa LIMIT 0,1";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $marca = $q->row();
        $id_marca = $marca->id;
      } else {
        $id_marca = 0;
      }

      $id_articulo = 0;
      $sql = "SELECT * FROM articulos WHERE codigo = '$r->codigo' AND id_empresa = $id_empresa LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $art = $q->row();
        $id_articulo = $art->id;
        // Debemos actualizarlo
        $sql = "UPDATE articulos SET ";
        $sql.= " fecha_mov = NOW(), ";
        $sql.= " nombre = '$r->categoria', custom_1 = '$r->nombre', ";
        $sql.= " id_rubro = '$r->id_rubro', id_marca = '$id_marca', ";
        $sql.= " precio_final = '$r->precio_web', precio_final_dto = '$r->precio_web', ";
        $sql.= " stock = '$r->stock', ";
        $sql.= " ancho = '$r->ancho', ";
        $sql.= " alto = '$r->alto', ";
        $sql.= " custom_8 = '$string_vehiculos', ";
        $sql.= " profundidad = '$r->largo', ";
        $sql.= " peso = '$r->kgs' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = '$art->id' ";
        $this->db->query($sql);
      } else {
        // Debemos insertar el nuevo producto
        $sql = "INSERT INTO articulos (";
        $sql.= " codigo, nombre, id_empresa, id_rubro, fecha_ingreso, fecha_mov, id_tipo_alicuota_iva, moneda, ";
        $sql.= " lista_precios, usa_stock, stock, unidad, ancho, alto, profundidad, peso, ";
        $sql.= " precio_final, precio_final_dto, custom_8, custom_1, id_marca ";
        $sql.= ") VALUES (";
        $sql.= " '$r->codigo', '$r->categoria', '$id_empresa', '$r->id_rubro', NOW(), NOW(), 5, '$', ";
        $sql.= " 2, 1, '$r->stock', 'U', '$r->ancho', '$r->alto', '$r->largo', '$r->kgs', ";
        $sql.= " '$r->precio_web', '$r->precio_web', '$string_vehiculos', '$r->nombre', '$id_marca' ";
        $sql.= ")";
        $this->db->query($sql);
        $id_articulo = $this->db->insert_id();
        // Actualizamos el link
        $link = "producto/".filename($r->nombre,"-",0)."-".$id_articulo."/";
        $this->db->query("UPDATE articulos SET link = '$link' WHERE id = $id_articulo AND id_empresa = $id_empresa");
      }

      //echo $r->codigo."<br/>";
      //print_r($r->vehiculos); echo "<br/><br/>";

      $sql = "SELECT * FROM articulos_meli WHERE id_articulo = '$id_articulo' AND id_empresa = $id_empresa LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $sql = "UPDATE articulos_meli SET ";
        $sql.= " titulo_meli = '$r->nombre_meli', ";
        $sql.= " precio_meli = '$r->precio_meli' ";
        $sql.= "WHERE id_articulo = $id_articulo AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      } else {
        $sql = "INSERT INTO articulos_meli (";
        $sql.= " id_articulo, id_empresa, activo_meli, precio_meli, titulo_meli ";
        $sql.= ") VALUES (";
        $sql.= " $id_articulo, $id_empresa, -1, '$r->precio_meli', '$r->nombre_meli' ";
        $sql.= ")";
        $this->db->query($sql);
      }

      $this->db->query("DELETE FROM articulos_marcas_vehiculos WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo ");
      foreach($r->vehiculos as $v) {
        $sql = "INSERT INTO articulos_marcas_vehiculos (";
        $sql.= " id_empresa,id_marca_vehiculo,id_articulo,modelo,orden";
        $sql.= ") VALUES(";
        $sql.= " $id_empresa,'$v->id',$id_articulo,'$v->modelo',1";
        $sql.= ")";
        $this->db->query($sql);
      }
    }
    if ($es_web) header("Location: /sistema/app/#articulos");
    else echo "Cantidad de productos actualizados: $i";
  }



  /*
  PARA CONFIGURAR EL SATO
  Download Router: https://chrome.google.com/webstore/detail/downloads-router-with-ove/gdhonnfhmbfjipjbjineecnknbpjeemh
  - Abrir consola y crear link simbolico: mklink /D "C:\Users\MyName\Downloads\C" "C:\"
  - Crear regla Filename -> folder mapping. Habilitar override
  */
  function imprimir_sato() {
    $id_empresa = parent::get_empresa();
    $this->load->helper("ean_helper");
    $items = parent::get_post("items");
    $id_sucursal = (parent::get_post("id_sucursal") !== FALSE) ? parent::get_post("id_sucursal") : 0;
    if ($items === FALSE) {
      echo "No se enviaron datos."; exit();
    }
    $items = json_decode($items);

    $enter = ($id_sucursal == 56 || $id_sucursal == 223 || $id_sucursal == 224) ? "\r\n" : "\n";
    $limite_nombre = ($id_sucursal == 56) ? 20 : 30;

    $salida = ($id_sucursal == 17 || $id_sucursal == 16) ? "" : "EAN,DESCRIPCION,PRECIO".$enter;
    foreach($items as $item) {

      if ($id_sucursal == 0) {
        $articulo = $this->modelo->get($item->id_articulo);  
      } else {
        $articulo = $this->modelo->get_by_sucursal($item->id_articulo,$id_empresa,$id_sucursal);
      }
      $lineas = ceil($item->cantidad / 3);
      for($i=0;$i<$lineas;$i++) {
        $articulo->nombre = preg_replace('/[[:^print:]]/', '?', $articulo->nombre);
        $codigo = str_pad($articulo->codigo, 12, "0", STR_PAD_LEFT);
        $codigo = $codigo.ean13_checksum($codigo);
        $salida.= '"'.$codigo.'",';
        $salida.= '"'.str_pad(substr($articulo->nombre, 0, $limite_nombre), $limite_nombre, " ", STR_PAD_RIGHT).'",';
        $salida.= '"$'.str_pad(substr($articulo->precio_final_dto, 0, 9), 9, " ", STR_PAD_LEFT).'"';
        if ($id_sucursal != 56) $salida.= ',"'.$articulo->fecha_mov.'","                    "';
        $salida.= $enter;        
      }
    }
    header("Content-disposition: attachment; filename=etiquetr.txt");
    header("Content-type: application/octet-stream");
    echo $salida;
  }


  function imprimir_sato_directo() {
    $id_empresa = parent::get_empresa();
    $this->load->helper("ean_helper");
    $items = parent::get_post("items");
    $id_sucursal = (parent::get_post("id_sucursal") !== FALSE) ? parent::get_post("id_sucursal") : 0;
    if ($items === FALSE) {
      echo "No se enviaron datos."; exit();
    }
    $items = json_decode($items);

    $enter = ($id_sucursal == 56 || $id_sucursal == 223 || $id_sucursal == 224) ? "\r\n" : "\n";
    $limite_nombre = ($id_sucursal == 56) ? 20 : 30;

    $salida = array();
    foreach($items as $item) {

      if ($id_sucursal == 0) {
        $articulo = $this->modelo->get($item->id_articulo);  
      } else {
        $articulo = $this->modelo->get_by_sucursal($item->id_articulo,$id_empresa,$id_sucursal);
      }
      if (!empty($articulo->codigo_barra)) {
        $codigo_barra = explode("###",$articulo->codigo_barra);
        $codigo = $codigo_barra[0];
      } else {
        $codigo = $articulo->codigo;
      }
      if (strlen($codigo) < 13) {
        $codigo = str_pad($codigo, 12, "0", STR_PAD_LEFT);
        $codigo = $codigo.ean13_checksum($codigo);
      }
      $articulo->codigo = $codigo;
      $articulo->nombre = str_pad(substr($articulo->nombre, 0, $limite_nombre), $limite_nombre, " ", STR_PAD_RIGHT);
      for($i=0;$i<$item->cantidad;$i++) {
        $salida[] = $articulo;
      }
    }
    $this->load->view("reports/etiquetas_sato",array(
      "articulos"=>$salida,
    ));
  }

  function set_lista_precios_configuracion() {
    $row = new stdClass();
    $row->id_empresa = parent::get_empresa();
    $row->lista_1_nombre = parent::get_post("lista_1_nombre");
    $row->lista_2_nombre = parent::get_post("lista_2_nombre");
    $row->lista_3_nombre = parent::get_post("lista_3_nombre");
    $row->lista_4_nombre = parent::get_post("lista_4_nombre");
    $row->lista_5_nombre = parent::get_post("lista_5_nombre");
    $row->lista_6_nombre = parent::get_post("lista_6_nombre");
    $this->modelo->set_lista_precios_configuracion($row);
    echo json_encode(array(
      "error"=>0,
    ));
  }

}