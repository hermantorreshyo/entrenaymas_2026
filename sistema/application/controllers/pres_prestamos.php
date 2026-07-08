<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Prestamos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Prestamo_Model', 'modelo');
  }

  /*
  function ultimo_cliente_cobrado() {
    @session_start();
    $id_cliente = parent::get_post("id_cliente",0);
    $monto = parent::get_post("monto",0);
    if ()
  }
  */

  function facturar_cuota() {
    $id_empresa = parent::get_empresa();
    $id_prestamo = parent::get_post("id_prestamo",0);
    $id_cuota = parent::get_post("id_cuota",0);
    $id_pago = parent::get_post("id_pago",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $id_usuario = $_SESSION["id"];
    $usuario = $_SESSION["nombre"];

    $this->load->model("Pres_Prestamo_Cuota_Model");
    $salida = $this->Pres_Prestamo_Cuota_Model->facturar(array(
      "id_empresa"=>$id_empresa,
      "id_prestamo"=>$id_prestamo,
      "id_cuota"=>$id_cuota,
      "id_pago"=>$id_pago,
      "id_sucursal"=>$id_sucursal,
      "id_usuario"=>$id_usuario,
      "usuario"=>$usuario,
    ));
    echo json_encode($salida);
  }

  function liquidar_cuotas() {
    $id_empresa = parent::get_empresa();
    $id_prestamo = parent::get_post("id_prestamo");
    $saldo = parent::get_post("saldo");
    $id_sucursal = parent::get_post("id_sucursal");
    $ids_cuotas = parent::get_post("cuotas","");
    if (empty($saldo)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El saldo no es valido.",
      ));
      exit();
    }
    if (empty($ids_cuotas)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Por favor seleccione las cuotas que desea saldar.",
      ));
      exit();
    }
    $cuotas = explode("-", $ids_cuotas);

    $prestamo = $this->modelo->get($id_prestamo);
    if ($prestamo === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El prestamo no es valido.",
      ));
      exit();
    }

    // Marcamos como que todas las cuotas seleccionadas estan pagas
    foreach($cuotas as $cuota) {
      $this->db->query("UPDATE pres_prestamos_cuotas SET estado = '1', fecha_pago = NOW(), monto_pagado = saldo, saldo = 0 WHERE id_empresa = $id_empresa AND id_prestamo = $id_prestamo AND id = $cuota ");
    }

    // A la ultima cuota, le ponemos cuanto le pagamos
    //$ultima_cuota = end($cuotas);
    //$this->db->query("UPDATE pres_prestamos_cuotas SET monto_pagado = $saldo WHERE id_empresa = $id_empresa AND id_prestamo = $id_prestamo AND id = $ultima_cuota");

    // Lo sacamos de la caja de la sucursal
    $id_usuario = $_SESSION["id"];
    $this->load->model("Tipo_Gasto_Model");
    $concepto = $this->Tipo_Gasto_Model->get_by_codigo("PAGO");
    if ($concepto !== FALSE) {
      $sql = "INSERT INTO pres_cajas_movimientos (id_empresa,id_concepto,monto,fecha,observaciones,id_prestamo,id_cuota,id_sucursal,id_usuario,tipo) VALUES (";
      $sql.= "$id_empresa,$concepto->id,'$saldo',NOW(),'Liquidacion Prestamo #$prestamo->numero','$id_prestamo',0,'$id_sucursal',$id_usuario,'E') ";
      $this->db->query($sql);          
    }
    echo json_encode(array("error"=>0));
  }


  function listado_reingreso() {
    $this->load->helper("fecha_helper");
    $imprimir = ($this->input->get("imprimir") === FALSE) ? 0 : $this->input->get("imprimir");
    $filter = ($this->input->get("texto") === FALSE) ? "" : urldecode($this->input->get("texto"));
    $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $id_sucursal = parent::get_get("id_sucursal",0);
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 10;
    $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by") : "";
    $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
    $r = $this->modelo->listado_reingreso(array(
      "filter"=>$filter,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "order_by"=>$order_by,
    ));
    if ($imprimir == 0) {
      echo json_encode($r);  
      exit();
      
    } else if ($imprimir == 1) {

      $id_empresa = parent::get_empresa();
      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($id_empresa);
      $header = $this->load->view("reports/prestamo/header",null,true);
      $datos = array(
        "listado"=>$r["results"],
        "empresa"=>$empresa,
        "header"=>$header,
      );
      $this->load->view("reports/prestamo/listado_reingreso.php",$datos);

    }
  }



  function listado_mora() {
    $this->load->helper("fecha_helper");
    $imprimir = ($this->input->get("imprimir") === FALSE) ? 0 : $this->input->get("imprimir");
    $filter = ($this->input->get("texto") === FALSE) ? "" : urldecode($this->input->get("texto"));
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_plan = parent::get_get("id_plan",0);
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 10;
    $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by") : "";
    $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";
    if (empty($order_by)) {
      $order_by = "dias_mora";
      $order = "asc";
    }
    $r = $this->modelo->listado_mora(array(
      "filter"=>$filter,
      "id_sucursal"=>$id_sucursal,
      "id_plan"=>$id_plan,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "order_by"=>$order_by,
    ));
    if ($imprimir == 0) {
      echo json_encode($r);  
      exit();
      
    } else if ($imprimir == 1) {

      $id_empresa = parent::get_empresa();
      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($id_empresa);
      $header = $this->load->view("reports/prestamo/header",null,true);
      $datos = array(
        "listado"=>$r["results"],
        "empresa"=>$empresa,
        "header"=>$header,
      );
      $this->load->view("reports/prestamo/listado_mora.php",$datos);

    }
  }


  function buenos_clientes() {
    $this->load->helper("fecha_helper");
    $imprimir = ($this->input->get("imprimir") === FALSE) ? 0 : $this->input->get("imprimir");
    $filter = ($this->input->get("texto") === FALSE) ? "" : urldecode($this->input->get("texto"));
    $fecha = fecha_mysql(str_replace("-","/",$fecha));
    $id_sucursal = parent::get_get("id_sucursal",0);
    $exportar = parent::get_get("exportar",0);
    $limit = ($this->input->get("limit") !== FALSE) ? $this->input->get("limit") : 0;
    $offset = ($this->input->get("offset") !== FALSE) ? $this->input->get("offset") : 10;
    $order_by = ($this->input->get("order_by") !== FALSE) ? $this->input->get("order_by") : "";
    $order = ($this->input->get("order") !== FALSE) ? $this->input->get("order") : "";

    if ($exportar == 1) {
      $limit = 0; $offset = 99999999;
    }

    $r = $this->modelo->buenos_clientes(array(
      "filter"=>$filter,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "order_by"=>$order_by,
    ));

    if ($exportar == 1) {

      $datos = array();
      foreach($r["results"] as $r) {
        $datos[] = array(
          "nombre"=>$r->nombre." ".$r->apellido,
          "localidad"=>$r->localidad,
          "telefono"=>$r->telefono,
          "dni"=>$r->documento,
        );
      }
      $this->load->library("Excel");
      $this->excel->create(array(
        "filename"=>"buenos_clientes",
        "footer"=>array(),
        "header"=>array("Nombre","Localidad","Telefono","Documento"),
        "data"=>$datos,
        "title"=>"Listado de Buenos Clientes",
      ));              

    } else {
      if ($imprimir == 0) {
        echo json_encode($r);  
        exit();
        
      } else if ($imprimir == 1) {

        $id_empresa = parent::get_empresa();
        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get($id_empresa);
        $header = $this->load->view("reports/prestamo/header",null,true);
        $datos = array(
          "listado"=>$r["results"],
          "empresa"=>$empresa,
          "header"=>$header,
        );
        $this->load->view("reports/prestamo/buenos_clientes.php",$datos);

      }
    }
  }


  function buscar() {
    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $id_cliente = $this->input->get("id_cliente");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_cliente"=>$id_cliente,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }

  function imprimir_prestamo($id,$id_sucursal = 0) {
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $prestamo = $this->modelo->get($id);

    $sucursal = FALSE;
    $this->load->model("Almacen_Model");
    $sucursal = $this->Almacen_Model->get($prestamo->id_sucursal);

    $this->load->model("Pres_Cliente_Model");
    $cliente = $this->Pres_Cliente_Model->get($prestamo->id_cliente);
    $datos = array(
      "sucursal"=>$sucursal,
      "prestamo"=>$prestamo,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
    );
    $this->load->view("reports/prestamo/otorgacion.php",$datos);
  }

  function imprimir_seleccionadas($id,$id_sucursal=0,$cuotas = "") {
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $prestamo = $this->modelo->get($id);

    $sucursal = FALSE;
    if ($id_sucursal != 0) {
      $this->load->model("Almacen_Model");
      $sucursal = $this->Almacen_Model->get($id_sucursal);
    }

    $this->load->model("Pres_Cliente_Model");
    $cliente = $this->Pres_Cliente_Model->get($prestamo->id_cliente);

    $cuotas_array = explode("-", $cuotas);

    $numeros_cuotas = array();
    $total = 0;
    $cancelo = 0;
    foreach($prestamo->cuotas as $cuota) {
      if (in_array($cuota->id, $cuotas_array)) {
        $total += (float)$cuota->monto_pagado + (float)$cuota->interes_pagado;
        $numeros_cuotas[] = $cuota->numero;
        if ($cuota->numero == $prestamo->cantidad_cuotas) {
          // Si estamos pagando la ultima cuota
          $cancelo = 1;
        }
      }
    }

    $datos = array(
      "sucursal"=>$sucursal,
      "prestamo"=>$prestamo,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
      "total"=>$total,
      "cancelo"=>$cancelo,
      "numeros_cuotas"=>$numeros_cuotas,
    );
    $this->load->view("reports/prestamo/pago_liquidacion.php",$datos);
  }  

  function eliminar_prestamo($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM pres_prestamos WHERE id = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM pres_prestamos_cuotas WHERE id_prestamo = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM pres_cajas_movimientos where id_prestamo = $id AND id_empresa = $id_empresa");
    echo json_encode(array("error"=>0));
  }

  function actualizar_prestamos() {
    $sql = "SELECT * FROM pres_prestamos";
    $q = $this->db->query($sql);
    foreach($q->result() as $pres) {
      $capital_cuota = (float)($pres->monto_prestado / $pres->cantidad_cuotas);
      $sql = "UPDATE pres_prestamos_cuotas ";
      $sql.= "SET capital_cuota = $capital_cuota, ";
      $sql.= " interes_cuota = monto - $capital_cuota ";
      $sql.= "WHERE id_empresa = $pres->id_empresa ";
      $sql.= "AND id_prestamo = $pres->id ";
      $this->db->query($sql); 
    }
    echo "TERMINO";
  }

}