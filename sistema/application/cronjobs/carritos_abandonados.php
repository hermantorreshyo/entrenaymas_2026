<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");
include("../libraries/Mandrill/Mandrill.php");
$now = date("Y-m-d H:i:s");

// Controla si un carrito 
// PERIODICIDAD: 1 HORA

// Si hay alguna compra en proceso
// de hace mas de [web_configuracion.tiempo_envio_carrito_abandonado] horas
// que no se le haya enviado previamente el mismo email
// y que la empresa tenga configurado el uso de envio de emails de carritos abandonados

$sql = "SELECT ";
$sql.= " C.id AS id_cliente, C.nombre, C.email, ET.nombre AS asunto, ET.texto, E.id AS id_empresa, F.id_usuario, ";
$sql.= " E.nombre AS empresa, E.email AS empresa_email, E.dominio_ppal, F.id AS id_factura, WC.bcc_email ";
$sql.= "FROM facturas F ";
$sql.= " INNER JOIN empresas E ON (F.id_empresa = E.id) ";
$sql.= " INNER JOIN web_configuracion WC ON (F.id_empresa = WC.id_empresa) ";
$sql.= " INNER JOIN crm_emails_templates ET ON (WC.id_empresa = ET.id_empresa AND WC.id_email_carrito_abandonado = ET.id) ";
$sql.= " INNER JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";

// Compra pendiente
$sql.= "WHERE F.id_tipo_estado = 0 ";
// $sql.= " AND E.activo = 1 ";
// $sql.= " AND E.fecha_suspension > '$now' ";

// Que no se le haya enviado un email anteriormente
$sql.= " AND NOT EXISTS (SELECT * FROM crm_consultas CON WHERE CON.id_empresa = F.id_empresa AND CON.tipo = 1 AND CON.id_contacto = C.id AND CON.id_referencia = F.id) ";

// Que se haya cumplido el tiempo para el envio
$sql.= " AND ABS(TIME_TO_SEC(TIMEDIFF('$now',CONCAT(F.fecha,' ',F.hora))) / 3600) > WC.tiempo_envio_carrito_abandonado ";

// Que exista un email para enviarle
$sql.= " AND WC.id_email_carrito_abandonado != 0 ";

// TODO: TESTING
// $sql.= " AND E.id = 215 ";
$q = mysqli_query($conx,$sql);

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

while(($row = mysqli_fetch_object($q))!==NULL) {

  // Controlamos que tenga email cargado
  if (empty($row->email)) continue;

  // Controlamos que el email no sea vacio
  if (empty($row->texto)) continue;

  // Seleccionamos los items
  $sql = "SELECT * FROM facturas_items FI ";
  $sql.= "INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
  $sql.= "WHERE FI.id_empresa = $row->id_empresa ";
  $sql.= "AND FI.id_factura = $row->id_factura ";
  $q_items = mysqli_query($conx,$sql);
  $row->items = array();
  while(($item = mysqli_fetch_object($q_items))!==NULL) {
    $row->items[] = $item;
  }

	// Tomamos el texto
	$texto = $row->texto;
  $texto = str_replace("{{nombre}}",$row->nombre,$texto);
  $texto = str_replace("{{cliente}}",$row->nombre,$texto);

$item_tmp = <<<TMP
<table border="0" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-spacing:0;font-family:Lato,Arial,sans-serif;padding-bottom:50px;width:100%">
  <tbody><tr style="font-family:Lato,Arial,sans-serif">
    <td style="font-family:Lato,Arial,sans-serif;font-size:0;padding:0;padding-bottom:0;text-align:center">
      <div style="display:inline-block;font-family:Lato,Arial,sans-serif;max-width:300px;vertical-align:top;width:100%">
        <table style="border-spacing:0;font-family:Lato,Arial,sans-serif;font-size:14px;text-align:left;width:100%">
          <tbody><tr style="font-family:Lato,Arial,sans-serif">
            <td style="font-family:Lato,Arial,sans-serif;padding:0 20px 20px 20px">
              <a target="_blank" href="{{link_carrito}}">
                <table style="border-spacing:0;display:block;font-family:Lato,Arial,sans-serif;margin:0 auto;padding:0;width:100%">
                  <tbody style="display:block;font-family:Lato,Arial,sans-serif;margin:0 auto;padding:0;width:100%">
                    <tr style="background:#ffffff;display:block;font-family:Lato,Arial,sans-serif;margin:0 auto;padding:0;text-align:center;vertical-align:middle">
                      <td style="display:block;font-family:Lato,Arial,sans-serif;padding:0;text-align:center;vertical-align:middle">
                        <img height="275" src="{{item_path}}" alt="{{item_nombre}}" style="border:0;font-family:Lato,Arial,sans-serif;height:auto;text-align:center;vertical-align:middle;width:100%">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </a>
            </td>
          </tr>
        </tbody></table>
      </div>
      
      <div style="display:inline-block;font-family:Lato,Arial,sans-serif;max-width:300px;vertical-align:top;width:100%">

        <table style="border-spacing:0;font-family:Lato,Arial,sans-serif;font-size:14px;text-align:left;width:100%">

          <tbody><tr style="font-family:Lato,Arial,sans-serif">
            <td style="font-family:Lato,Arial,sans-serif;padding:0 20px">
              <div style="display:inline-block;font-family:Lato,Arial,sans-serif;text-align:left;vertical-align:top;width:100%">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-spacing:0;font-family:Lato,Arial,sans-serif">

                  <tbody><tr style="font-family:Lato,Arial,sans-serif">
                    <td style="font-family:Lato,Arial,sans-serif;padding:0">

                      <table style="border-spacing:0;font-family:Lato,Arial,sans-serif;word-break:normal;word-wrap:normal">
                        <tbody>
                          <tr style="font-family:Lato,Arial,sans-serif;word-break:normal;word-wrap:normal">
                            <td style="font-family:Lato,Arial,sans-serif;padding:0;word-break:normal;word-wrap:normal">
                              <h2 style="color:#333333;font-family:Lato,Arial,sans-serif;font-size:24px;font-weight:normal;line-height:20px;margin:0;padding:20px 0 0 0;text-align:left;text-indent:0;word-break:normal;word-wrap:normal">
                                <a href="{{link_carrito}}" style="color:#999999;font-family:Lato,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.4;margin-bottom:5px;margin-top:0;padding:0;text-align:left;text-decoration:none;text-indent:0;word-break:normal;word-wrap:normal" target="_blank">
                                  {{item_nombre}}
                                </a>
                              </h2>
                            </td>
                          </tr>
                          <tr style="font-family:Lato,Arial,sans-serif;word-break:normal;word-wrap:normal">
                            <td style="font-family:Lato,Arial,sans-serif;padding:0;word-break:normal;word-wrap:normal">
                              <div style="display:block;font-family:Lato,Arial,sans-serif;text-align:left;word-break:normal;word-wrap:normal">
                                <strong style="margin:30px 0px 20px 0px;color:#333333;display:block;font-family:Lato,Arial,sans-serif;font-size:31px;font-weight:400;text-align:left;word-break:normal;word-wrap:normal">
                                {{item_precio}}
                                </strong>
                              </div>
                            </td>
                          </tr>
                          <tr style="font-family:Lato,Arial,sans-serif;word-break:normal;word-wrap:normal">
                            <td style="font-family:Lato,Arial,sans-serif;padding:0;padding-top:20px;word-break:normal;word-wrap:normal">
                              <a href="{{link_carrito}}" target="_blank" style="text-decoration:none">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;font-family:Lato,Arial,sans-serif;height:48px;max-height:48px;width:auto;word-break:normal;word-wrap:normal">
                                  <tbody><tr style="font-family:Lato,Arial,sans-serif;word-break:normal;word-wrap:normal">
                                    <td align="center" style="background-color:#ee8534;border:0;border-radius:4px;color:#ffffff;font-family:Lato,Arial,sans-serif;font-size:18px;font-weight:300;height:48px;margin:5px 0;max-width:280px;outline:0;padding:0 30px;text-align:center;text-decoration:none;width:100%;word-break:normal;word-wrap:normal">
                                      Ver detalle
                                    </td>
                                  </tr>
                                </tbody></table>
                              </a>
                            </td>
                          </tr>
                      </tbody></table>
                    </td>
                  </tr>
                </tbody></table>
              </div>
            </td>
          </tr>
        </tbody></table>
      </div>
      
    </td>
  </tr>
</tbody></table>
TMP;

  $link_carrito = "http://www.grupoanacleto.com.ar/sistema/consultas/function/aviso_carrito_abandonado/$row->id_empresa/$row->id_cliente/";
  $items_template = "";
  foreach($row->items as $item) {
    $item_tpl = $item_tmp;
    $item_tpl = str_replace("{{link_carrito}}",$link_carrito,$item_tpl);
    $item_tpl = str_replace("{{item_nombre}}",$item->nombre,$item_tpl);
    $item_tpl = str_replace("{{item_precio}}","$ ".$item->precio_final_dto,$item_tpl);
    $item_tpl = str_replace("{{item_path}}","http://www.grupoanacleto.com.ar/sistema/".$item->path,$item_tpl);
    $items_template.= $item_tpl;
  }
  $texto = str_replace("{{items}}",$items_template,$texto);
  $texto = str_replace("'", "\"", $texto);

  $bcc = array();
  $bcc[] = "basile.matias99@gmail.com";
  $row->bcc_email = trim($row->bcc_email);
  if (!empty($row->bcc_email)) {
    $bcc_2 = explode(",", $row->bcc_email);  
    $bcc = array_merge($bcc,$bcc_2);
  }

  // Mandamos el email
  mandrill_send(array(
    "to"=>$row->email,
    "to_name"=>$row->nombre,
    "from_name"=>$row->empresa,
    "reply_to"=>$row->empresa_email,
    "subject"=>$row->asunto,
    "bcc"=>$bcc,
    "body"=>$texto,
  ));

  // ENVIO AUTOMATICO DE CARRITO ABANDONADO
  $id_origen = 21;

  // Creamos la consulta
  $sql = "INSERT INTO crm_consultas (id_contacto,id_empresa,fecha,asunto,texto,id_origen,id_usuario,tipo,id_referencia) VALUES(";
  $sql.= "'$row->id_cliente','$row->id_empresa','$now','$row->asunto','$texto','$id_origen','$row->id_usuario','1','$row->id_factura') ";
  mysqli_query($conx,$sql);
}
?>