<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Servicios_Envio extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Servicio_Envio_Model', 'modelo',"orden ASC",1);
  }
    
  function calcular_costo_envio() {
        
    $id_empresa = $this->input->post("id_empresa");
    $peso_total = $this->input->post("peso_total");
    $codigo_postal_cliente = $this->input->post("codigo_postal_cliente");
    $lat1 = $this->input->post("latitud_cliente"); // Coordenadas del cliente
    $lon1 = $this->input->post("longitud_cliente");
    $valor = $this->input->post("valor"); // Valor de la mercaderia
    $distancia = 0;
    
    $id_servicio_envio = $this->input->post("id_servicio_envio"); // Servicio elegido por el cliente
    $coef_aforado = 0;
    if ($id_servicio_envio == 0) {
      // Tomamos el servicio por defecto del cliente
      $sql = "SELECT * FROM env_servicios_envio WHERE id_empresa = $id_empresa AND activo = 1 ORDER BY id ASC LIMIT 0,1";
    } else {
      // Tomamos el servicio de envio que establecio el cliente
      $sql = "SELECT * FROM env_servicios_envio WHERE id_empresa = $id_empresa AND id = $id_servicio_envio AND activo = 1";
    }
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $servicio_envio = $q->row();
      $id_servicio_envio = $servicio_envio->id;
      $coef_aforado = $servicio_envio->coef_aforado;
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El servicio seleccionado no existe.",
      ));
      return;
    }
    
    // Calculamos el peso aforado del paquete
    if ($peso_total === FALSE) {
      $peso = $this->input->post("peso");
      $cantidad = $this->input->post("cantidad");
      $ancho = $this->input->post("ancho");
      $alto = $this->input->post("alto");
      $profundidad = $this->input->post("profundidad");
      $peso_aforado = $ancho * $alto * $profundidad * $coef_aforado;
      if ($peso_aforado > $peso) $peso = $peso_aforado; // Si es MAYOR, se toma el PESO AFORADO
      
      $peso = $peso * $cantidad;
      $valor = $valor * $cantidad;      
    } else {
      $peso = $peso_total;
    }
    
    // Si tenemos que utilizar la tabla
    //if ($servicio_envio->metodo == "T") {
      
    
    // Si en cambio tenemos que conectarnos con un webservice
    //} else if ($servicio_envio->metodo == "W") {
      
    if ($servicio_envio->empresa == "Andreani") {
      
      // El codigo postal es obligatorio
      if (empty($codigo_postal_cliente) || is_null($codigo_postal_cliente)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Codigo postal invalido",
        ));
        return;
      }
      
      require APPPATH.'libraries/Andreani/CotizarEnvio.php';
      require APPPATH.'libraries/Andreani/Andreani.php';
      
      // Los siguientes datos son de prueba, para la implementación en un entorno productivo deberán reemplazarse por los verdaderos
      $request = new CotizarEnvio();
      $request->setCodigoDeCliente('CL0010550');
      $request->setNumeroDeContrato('400006813');
      $request->setCodigoPostal($codigo_postal_cliente);
      $request->setPeso($peso);
      //$request->setVolumen(100); // No es necesario informarlo
      $request->setValorDeclarado($valor);
      
      $andreani = new Andreani('ANACLETO_WS','ANDREANI','prod');
      $response = $andreani->call($request);
      if($response->isValid()){
        $costo = $response->getMessage()->CotizarEnvioResult->Tarifa;
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: ".$response->getMessage(),
        ));
        return;          
      }        
      
    } else {
      
      // Calculamos la distancia entre la empresa y el cliente
      $q = $this->db->query("SELECT * FROM web_configuracion WHERE id_empresa = $id_empresa");
      $config = $q->row();
      $this->load->helper("coord_helper");
      $distancia = distance($lat1, $lon1, $config->latitud, $config->longitud);
      
      // Calculamos el costo de envio de acuerdo a la tabla, seguros, etc..
      $this->modelo->set_empresa($id_empresa);
      $costo = $this->modelo->calcular_costo_envio($id_servicio_envio,$peso,$distancia,$valor);              
      
    }
    
  //}
  
    echo json_encode(array(
      "error"=>0,
      "costo"=>$costo,
      "valor"=>$valor,
      "peso"=>$peso,
      "distancia"=>$distancia,
    ));
  }


  function enviar_andreani($id_factura = 0) {

    $id_empresa = parent::get_empresa(); 

    $sql = "SELECT * FROM env_servicios_envio WHERE empresa = 'Andreani' AND activo = 1";
    $q_andreani = $this->db->query($sql);
    if ($q_andreani->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El servicio de Andreani no esta activo o no existe",
      ));
      return;        
    }
    $servicio = $q_andreani->row();

    // Obtenemos los datos del pedido
    $sql_pedido = "SELECT P.*, ";
    $sql_pedido.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql_pedido.= " IF(C.direccion IS NULL,'',C.direccion) AS cliente_direccion, ";
    $sql_pedido.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql_pedido.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql_pedido.= " IF(C.codigo_postal = '',IF(L.codigo_postal IS NULL,'',L.codigo_postal),C.codigo_postal) AS codigo_postal, ";
    $sql_pedido.= " IF(PR.nombre IS NULL,'',PR.nombre) AS provincia ";
    $sql_pedido.= "FROM facturas P ";
    $sql_pedido.= "LEFT JOIN clientes C ON (P.id_cliente = C.id AND P.id_empresa = C.id_empresa) ";
    $sql_pedido.= "LEFT JOIN com_localidades L ON (L.id = C.id_localidad) ";
    $sql_pedido.= "LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql_pedido.= "LEFT JOIN com_provincias PR ON (PR.id = D.id_provincia) ";
    $sql_pedido.= "WHERE P.id = $id_factura ";
    $q = $this->db->query($sql_pedido);
    if ($q->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No existe pedido con ID $id_factura.",
      ));
      return;
    }

    $pedido = $q->row();
    $peso_total = 0;

    // Obtenemos los productos del pedido para sumar su peso
    $sql = "SELECT A.peso, A.ancho, A.alto, A.profundidad, PI.cantidad ";
    $sql.= "FROM facturas_items PI INNER JOIN articulos A ON (PI.id_articulo = A.id AND PI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE PI.id_factura = $id_factura ";
    $q = $this->db->query($sql);
    foreach($q->result() as $item) {
      $peso_total = $peso_total + ($item->cantidad * $item->peso);
    }
    // El peso debe ser expresado en gramos
    $peso_total = $peso_total * 1000;
    $numero_envio = "";
    $link_envio = "";

    require APPPATH.'libraries/Andreani/ConfirmarCompra.php';
    require APPPATH.'libraries/Andreani/ImpresionDeConstancia.php';
    require APPPATH.'libraries/Andreani/Andreani.php';
    
    if ($servicio->test == 0) {
      $usuario = $servicio->prod_usuario;
      $password = $servicio->prod_password;
      $cliente = $servicio->prod_cliente;
      $contrato = $servicio->prod_contrato;
    } else {
      $usuario = $servicio->test_usuario;
      $password = $servicio->test_password;
      $cliente = $servicio->test_cliente;
      $contrato = $servicio->test_contrato;
    }
    
    // Debemos separar la direccion de la altura
    $calle = $pedido->cliente_direccion;
    $altura = "0";
    $pos = strrpos(trim($pedido->cliente_direccion)," ");
    if ($pos>=0) {
      $calle = trim(substr($pedido->cliente_direccion,0,$pos));
      $altura = trim(substr($pedido->cliente_direccion,$pos));
      if (!is_numeric($altura)) {
        $calle = $pedido->cliente_direccion;
        $altura = "0";                          
      }
    }
    
    $request = new ConfirmarCompra();
    $request->setDatosTransaccion($contrato);
    $request->setDatosDestino($pedido->provincia,$pedido->localidad,$pedido->codigo_postal,$calle,$altura);
    // El "0" es porque el numero de documento es OBLIGATORIO y nosotros no lo registramos en la web
    $request->setDatosDestinatario($pedido->cliente,null,"DNI","0");
    $request->setDatosEnvio($peso_total);
    $andreani = new Andreani($usuario,$password,'prod');
    $response = $andreani->call($request);
    if($response->isValid()){
      $numero_envio = $response->getMessage()->ConfirmarCompraResult->NumeroAndreani;

      //if (!empty($numero_envio)) {
        
        $request = new ImpresionDeConstancia();
        //$request->setCodigoDeCliente($cliente);
        //$request->setNumeroDeContrato($contrato);              
        $request->setNumeroDeEnvio($numero_envio);
        $response = $andreani->call($request);
        if($response->isValid()){
          $link_envio = $response->getMessage()->ImprimirConstanciaResult->ResultadoImprimirConstancia->PdfLinkFile;
        } else {
          
          // Me envio un email con el error
          $headers = "From:info@grupoanacleto.com.ar\r\n";
          $headers.= "MIME-Version: 1.0\r\n";
          $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $string = print_r($response,true)."<br/><br/><br/>";
          @mail("basile.matias99@gmail.com","ERROR ANDREANI",$string,$headers);
        }
      //}

      //$link_envio = "https://bpmwmbsrv.andreani.com:41443/ImprimirEtiquetas/".$numero_envio."/63770/0/95226143";
    } else {
      
      // Me envio un email con el error
      $headers = "From:info@grupoanacleto.com.ar\r\n";
      $headers.= "MIME-Version: 1.0\r\n";
      $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      $string = print_r($response,true)."<br/><br/><br/>";
      $string.= "PESO TOTAL: $peso_total <br/><br/>";
      $string.= "SQL PEDIDO: $sql_pedido <br/><br/>";
      @mail("basile.matias99@gmail.com","ERROR ANDREANI",$string,$headers);
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error al enviar el pedido a Andreani.",
      ));
      return;
    }
    // Actualizamos los datos
    $sql = "UPDATE facturas SET ";
    $sql.= "numero_envio = '$numero_envio', ";
    $sql.= "link_envio = '$link_envio' ";
    $sql.= "WHERE id = $pedido->id ";
    $sql.= "AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Pedido generado con exito",
      "link_envio"=>$link_envio,
      "numero_envio"=>$numero_envio,
    ));
    return;
  }

  
  function link_andreani($numero_andreani = "") {

    $id_empresa = parent::get_empresa();
    
    // El numero esta vacio
    if (empty($numero_andreani)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El numero esta vacio",
      ));
      return;        
    }
    
    $sql = "SELECT * FROM env_servicios_envio WHERE empresa = 'Andreani' AND activo = 1 AND id_empresa = $id_empresa";
    $q_andreani = $this->db->query($sql);
    if ($q_andreani->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El servicio de Andreani no esta activo o no existe",
      ));
      return;        
    }
    
    // Obtenemos el pedido que corresponde ese numero
    $sql = "SELECT * FROM facturas WHERE numero_envio = '$numero_andreani' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: El numero no corresponde a ningun pedido",
      ));
      return;        
    }
    $pedido = $q->row();
    
    // El pedido ya tiene un link, enviamos ese
    if (!empty($pedido->link_envio)) {
      echo json_encode(array(
        "error"=>0,
        "link"=>$pedido->link_envio,
      ));
      return;
    }
    
    // Vamos a andreani y lo generamos
    
    $servicio = $q_andreani->row();

    if ($servicio->test == 0) {
      $usuario = $servicio->prod_usuario;
      $password = $servicio->prod_password;
      $cliente = $servicio->prod_cliente;
      $contrato = $servicio->prod_contrato;
    } else {
      $usuario = $servicio->test_usuario;
      $password = $servicio->test_password;
      $cliente = $servicio->test_cliente;
      $contrato = $servicio->test_contrato;
    }
    
    require_once 'application/libraries/Andreani/ImpresionDeConstancia.php';
    require_once 'application/libraries/Andreani/Andreani.php';
    $request = new ImpresionDeConstancia();
    $request->setNumeroDeEnvio($numero_andreani);
    $andreani = new Andreani($usuario,$password,'prod');
    $response = $andreani->call($request);
    if($response->isValid()){
      
      $link_envio = $response->getMessage()->ImprimirConstanciaResult->ResultadoImprimirConstancia->PdfLinkFile;
      
      // Actualizamos el pedido con ese link
      $sql = "UPDATE facturas SET ";
      $sql.= " link_envio = '$link_envio' ";
      $sql.= "WHERE id = $pedido->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
      
      echo json_encode(array(
        "error"=>0,
        "link"=>$link_envio,
      ));
      return;
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: ".$response->getMessage(),
      ));
      return;          
    }  
    
  }
    
    
  function ordenar() {
    $ids = $this->input->post("ids");
    $id_empresa = parent::get_empresa();
    if (!empty($ids)) {
      $ids = json_decode($ids);
      for($i=0;$i<sizeof($ids);$i++) {
        $id = $ids[$i];
        $this->db->query("UPDATE env_servicios_envio SET orden = $i WHERE id = $id AND id_empresa = $id_empresa");
      }
    }
    echo json_encode(array());
  }
    
}