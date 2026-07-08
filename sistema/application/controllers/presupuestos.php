<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Presupuestos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Presupuesto_Model', 'modelo');
  }

  function pasar_presupuesto_rio() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);
    $id_presupuesto = 634;
    $id_empresa_miguel = 224;
    $id_empresa_mega = 249;
    $id_proveedor_inova = 3132;
    $id_sucursal_rio = 23;
    $this->load->model("Stock_Model");
    $this->load->model("Articulo_Model");

    // Buscamos los articulos de ese presupuesto
    $sql = "SELECT PI.cantidad, A.* FROM presupuestos_items PI INNER JOIN articulos A ON (PI.id_empresa = A.id_empresa AND PI.id_articulo = A.id) ";
    $sql.= "WHERE PI.id_presupuesto = $id_presupuesto AND PI.id_empresa = $id_empresa_miguel ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      $cantidad = $r->cantidad;
      // Primero buscamos si el producto existe en Megashop
      $sql = "SELECT A.* FROM articulos A INNER JOIN articulos_proveedores AP ON (A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo) ";
      $sql.= "WHERE A.id_empresa = $id_empresa_mega AND AP.codigo = '$r->codigo' AND AP.id_proveedor = $id_proveedor_inova ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        // El articulo ya existe
        $articulo = $qq->row();
        $id_articulo_nuevo = $articulo->id;
        echo "($r->codigo) $articulo->nombre YA EXISTE CON EL CODIGO: $articulo->codigo <br/>";
      } else {
        // El articulo no existe, debemos ingresarlo
        echo "($r->codigo) $r->nombre NO EXISTE <br/>";
        unset($r->cantidad);

        $r->codigo = $this->Articulo_Model->next(array(
          "id_empresa"=>$id_empresa_mega,
        ));
        $r->custom_10 = $r->codigo;
        $r->id = 0;
        $r->id_empresa = $id_empresa_mega;
        $id_articulo_nuevo = $this->Articulo_Model->save($r);
        echo "($r->codigo) $r->nombre TIENE NUEVO ID $id_articulo_nuevo<br/>";

        $this->db->insert("articulos_proveedores",array(
          "id_proveedor"=>$id_proveedor_inova,
          "codigo"=>$r->codigo,
          "id_articulo"=>$id_articulo_nuevo,
          "id_empresa"=>$id_empresa_mega,
          "orden"=>0,
        ));

        // Agregamos en las sucursales
        $q_suc = $this->db->query("SELECT * FROM almacenes WHERE id_empresa = $id_empresa_mega");
        foreach($q_suc->result() as $suc) {
          $this->db->insert("articulos_precios_sucursales",array(
            "id_sucursal"=>$suc->id,
            "id_articulo"=>$id_articulo_nuevo,
            "id_empresa"=>$id_empresa_mega,
            "costo_neto"=>$r->costo_neto,
            "costo_final"=>$r->costo_final,
            "precio_neto"=>$r->precio_neto,
            "precio_final"=>$r->precio_final,
            "id_tipo_alicuota_iva"=>$r->id_tipo_alicuota_iva,
            "porc_iva"=>$r->porc_iva,
            "costo_iva"=>$r->costo_iva,
            "porc_ganancia"=>$r->porc_ganancia,
            "ganancia"=>$r->ganancia,
            "porc_bonif"=>$r->porc_bonif,
            "precio_final_dto"=>$r->precio_final_dto,
            "moneda"=>$r->moneda,
            "fecha_mov"=>date("Y-m-d"),
            "last_update"=>time(),
            "costo_neto_inicial"=>(isset($r->costo_neto_inicial) ? $r->costo_neto_inicial : $r->costo_neto),
            "dto_prov"=>(isset($r->dto_prov) ? $r->dto_prov : 0),
            "activo"=>(isset($r->activo) ? $r->activo : 1),
            "porc_ganancia_2"=>(isset($r->porc_ganancia_2) ? $r->porc_ganancia_2 : 0),
            "precio_final_2"=>(isset($r->precio_final_2) ? $r->precio_final_2 : 0),
            "porc_bonif_2"=>(isset($r->porc_bonif_2) ? $r->porc_bonif_2 : 0),
            "precio_final_dto_2"=>(isset($r->precio_final_dto_2) ? $r->precio_final_dto_2 : 0),
            "porc_ganancia_3"=>(isset($r->porc_ganancia_3) ? $r->porc_ganancia_3 : 0),
            "precio_final_3"=>(isset($r->precio_final_3) ? $r->precio_final_3 : 0),
            "porc_bonif_3"=>(isset($r->porc_bonif_3) ? $r->porc_bonif_3 : 0),
            "precio_final_dto_3"=>(isset($r->precio_final_dto_3) ? $r->precio_final_dto_3 : 0),
          ));          
        }
      }

      // Cargamos el stock en RIO GRANDE
      $this->Stock_Model->agregar_stock(array(
        "id_articulo"=>$id_articulo_nuevo,
        "id_empresa"=>$id_empresa_mega,
        "cantidad"=>$cantidad,
        "id_sucursal"=>$id_sucursal_rio,
        "detalle"=>"Importacion Presupuesto Nro 34 Inova",
      ));
    }
    echo "TERMINO";
  }

  function procesar_stock($id,$id_sucursal=0) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = parent::get_empresa();
    $this->load->model("Stock_Model");
    $res = $this->Stock_Model->procesar_presupuesto($id,array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
    ));
    echo json_encode(array(
      "error"=>(($res)?0:1),
    ));
  }

  function imprimir($id_factura) {
    
    $this->load->helper("fecha_helper");
    $factura = $this->modelo->get($id_factura,$id_punto_venta);
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/factura/header",null,true);
    
    $datos = array(
      "presupuesto"=>$factura,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/presupuesto/basico/presupuesto.php",$datos);
  }
  
  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    // TODO: Si elimina un presupuesto que fue procesado al stock, deberiamos volver al stock los items
    $this->db->query("DELETE FROM presupuestos_items WHERE id_presupuesto = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM presupuestos WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array());
  }  
  
  private function remove_attributes($array) {
    
    // Eliminamos los atributos que no se persisten
    unset($array->link);
    unset($array->undefined);
    unset($array->error);
    unset($array->ivas);
    unset($array->mensaje);
    unset($array->items);
    unset($array->tarjetas);
    unset($array->cheques);
    unset($array->cliente);
    unset($array->codigo_cliente);
    unset($array->nombre_cliente);
    unset($array->tipo_comprobante);
    unset($array->letra);
    unset($array->neto);
    unset($array->estado);
    unset($array->gestiona_stock);
    
    // Redondeamos
    $array->total = round($array->total,2);
    $array->subtotal = round($array->subtotal,2);
    $array->porc_descuento = round($array->porc_descuento,2);
    $array->descuento = round($array->descuento,2);
  }
  
  
  function insert() {
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    
    // Tomamos los datos
      $array = $this->parse_put();
      $array->id_empresa = $id_empresa;
      if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
      else $array->fecha = date("Y-m-d");
      $array->hora = date("H:i:s");

      if (isset($array->fecha_hasta)) $array->fecha_hasta = fecha_mysql($array->fecha_hasta);
      else $array->fecha_hasta = date("Y-m-d");        

      $id_usuario = $_SESSION["id"];
      $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;

      // Calculamos el nuevo numero
      $sql = "SELECT MAX(numero) AS numero FROM presupuestos WHERE id_empresa = $id_empresa ";
      $q_pres = $this->db->query($sql);
      $r_pres = $q_pres->row();
      $array->numero = (is_null($r_pres->numero) ? 1 : ($r_pres->numero + 1));

      $items = $array->items;
      $this->remove_attributes($array);
      $id_presupuesto = $this->modelo->insert($array);

      $i=0;
      foreach($items as $l) {
      $this->db->insert("presupuestos_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_presupuesto"=>$id_presupuesto,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total"=>$l->total,
        "bonificacion"=>$l->bonificacion,
        "id_sucursal"=>(isset($l->id_sucursal) ? $l->id_sucursal : 0),
        "codigo"=>(isset($l->codigo) ? $l->codigo : ""),
        "porc_iva"=>(isset($l->porc_iva) ? $l->porc_iva : 0),
        "neto"=>(isset($l->neto) ? $l->neto : 0),
        "orden"=>$i,
      ));
      $i++;
    }

    echo json_encode(array(
      "id"=>$id_presupuesto,
      "error"=>0,
    ));
  }
  
  
  function update($id_presupuesto) {
    
    // Si es 0, entonces lo insertamos
    if ($id_presupuesto == 0) { $this->insert($id_presupuesto); return; }    
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");

    if (isset($array->fecha_hasta)) $array->fecha_hasta = fecha_mysql($array->fecha_hasta);
    else $array->fecha_hasta = date("Y-m-d");        

    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;

    $items = $array->items;
    $this->remove_attributes($array);
    $this->modelo->update($id_presupuesto,$array);

    $i=0;
    $this->db->query("DELETE FROM presupuestos_items WHERE id_presupuesto = $id_presupuesto AND id_empresa = $id_empresa");
    foreach($items as $l) {
      $this->db->insert("presupuestos_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_presupuesto"=>$id_presupuesto,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total"=>$l->total,
        "orden"=>$i,
        "id_sucursal"=>(isset($l->id_sucursal) ? $l->id_sucursal : 0),
        "codigo"=>(isset($l->codigo) ? $l->codigo : ""),
        "porc_iva"=>(isset($l->porc_iva) ? $l->porc_iva : 0),
        "neto"=>(isset($l->neto) ? $l->neto : 0),
      ));
      $i++;
    }
    echo json_encode(array(
      "id"=>$id_presupuesto,
      "error"=>0,
    ));
  }
  
  
  function show_error($mensaje = "Ocurrio un error al guardar el comprobante") {
    echo json_encode(array(
      "error"=>1,
      "mensaje"=>$mensaje,
      "imprimir"=>0,
    ));
    exit();    
  }  
  
  function consulta() {
    
    $id_empresa = parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_cliente = $this->input->get("id_cliente");
    $id_vendedor = $this->input->get("id_vendedor");
    $id_sucursal = $this->input->get("id_sucursal");
    $numero = $this->input->get("numero");
        
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");        
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, ";
    $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= "IF(F.fecha_hasta='0000-00-00','',DATE_FORMAT(F.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
    $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i:%s')) AS hora, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= "IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
    $sql.= "IF(E.nombre IS NULL,'',E.nombre) AS empresa ";
    $sql.= "FROM presupuestos F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN vendedores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
    $sql.= "LEFT JOIN empresas E ON (F.id_empresa = E.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND F.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha <= '$hasta' ";
    if (!empty($id_cliente)) $sql.= "AND F.id_cliente = $id_cliente ";
    if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($numero)) $sql.= "AND F.numero LIKE '%$numero%' ";
    $sql.= "ORDER BY id DESC ";
    if ($limit !== FALSE) $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $lista = $q->result();
            
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $salida = array(
      "total"=> $total->total,
      "results"=>$lista,
    );
    echo json_encode($salida);
  }
  

}