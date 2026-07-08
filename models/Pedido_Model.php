<?php
class Pedido_Model {

  private $id_empresa = 0;
  private $conx = null;

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }

  function get($id_pedido,$config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    
    // Obtenemos los datos del pedido
    $sql_pedido = "SELECT P.*, ";
    $sql_pedido.= " IF(P.fecha = '0000-00-00','',DATE_FORMAT(P.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql_pedido.= " IF(P.vencimiento = '0000-00-00 00:00:00','',DATE_FORMAT(P.vencimiento,'%d/%m/%Y')) AS vencimiento, ";
    $sql_pedido.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql_pedido.= " IF(C.direccion IS NULL,'',C.direccion) AS cliente_direccion, ";
    $sql_pedido.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql_pedido.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql_pedido.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql_pedido.= " IF(U.direccion IS NULL,'',U.direccion) AS usuario_direccion, ";
    $sql_pedido.= " IF(L.codigo_postal IS NULL,'',L.codigo_postal) AS codigo_postal, ";
    $sql_pedido.= " IF(PR.nombre IS NULL,'',PR.nombre) AS provincia ";
    $sql_pedido.= "FROM facturas P ";
    $sql_pedido.= "LEFT JOIN clientes C ON (P.id_cliente = C.id AND P.id_empresa = C.id_empresa) ";
    $sql_pedido.= "LEFT JOIN com_usuarios U ON (P.id_usuario = U.id AND P.id_empresa = U.id_empresa) ";
    $sql_pedido.= "LEFT JOIN com_localidades L ON (L.id = C.id_localidad) ";
    $sql_pedido.= "LEFT JOIN com_departamentos D ON (L.id_departamento = D.id) ";
    $sql_pedido.= "LEFT JOIN com_provincias PR ON (PR.id = D.id_provincia) ";
    $sql_pedido.= "WHERE P.id = $id_pedido ";
    $sql_pedido.= "AND P.id_empresa = $id_empresa ";
    if (!empty($id_punto_venta)) $sql_pedido.= "AND P.id_punto_venta = $id_punto_venta ";
    
    $q = mysqli_query($this->conx,$sql_pedido);
    if ($q === FALSE || mysqli_num_rows($q) == 0) {
      // No se encontro el pedido con ese ID
      // Me envio un email con toda la traza de MercadoPago
      $headers = "From:info@grupoanacleto.com.ar\r\n";
      $headers.= "MIME-Version: 1.0\r\n";
      $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      $body = "EMPRESA: $id_empresa <br/>";
      $body.= "<br/>$sql_pedido<br/><br/>";
      $body.= $sql_pedido;
      @mail("basile.matias99@gmail","ERROR: PedidoModel.get",$body,$headers);
      return FALSE;
    }

    $factura = mysqli_fetch_object($q);

    $factura->items = array();
    $sql = "SELECT * FROM facturas_items ";
    $sql.= "WHERE id_factura = $factura->id ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $factura->id_punto_venta ";
    $sql.= "ORDER BY orden ASC";
    $q_items = mysqli_query($this->conx,$sql);
    while(($item=mysqli_fetch_object($q_items))!==NULL) {
      $item->nombre = $this->encod($item->nombre);
      $factura->items[] = $item;
    }
    return $factura;
  }

}
?>