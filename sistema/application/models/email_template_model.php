<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Email_Template_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("crm_emails_templates","id","nombre ASC");
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function insert_preset($id_empresa) {

    $template = new stdClass();
    $template->id_empresa = $id_empresa;
    $template->clave = "email-compra-ok";
    $template->nombre = "Muchas gracias por su compra!";
    $template->texto = <<<TEXTO
<div style="background:#f8f8f8">
<table border="0" cellpadding="0" cellspacing="0" style="background:#f8f8f8; font-family:arial,helvetica,sans-serif; font-size:12px; line-height:20px; margin:0 auto; width:600px">
  <tbody>
    <tr>
      <td><a href="{{link_web}}" target="_blank" title="{{empresa}}"><img alt="{{empresa}}" class="CToWUd" src="{{empresa_logo}}" style="border: none; margin-top: 20px; margin-bottom: 20px;" /></a></td>
    </tr>
    <tr>
      <td>
      <div style="background:#fff;padding:16px;border:1px solid #eaeaea">
      <p><span style="font-size:14px">Hola <strong>{{cliente}}!</strong><br />
      <br />
      Muchas gracias por tu compra en <strong>{{empresa}}.</strong><br />
      Podes ver el detalle de tus productos haciendo click en el siguiente link:</span></p>
      <div style="margin:28px 0 16px;text-align:center;font-weight:bold;font-size:14px"><a href="{{link_ver_pedido}}" style="background-color:#80c254;color:#fff;text-decoration:none;padding:8px 22px;border-radius:4px;line-height:50px" target="_blank">Ver Mi Compra</a></div>
      </div>
      </td>
    </tr>
    <tr>
      <td style="text-align:right">&nbsp;</td>
    </tr>
  </tbody>
</table>
</div>
TEXTO;
    $this->db->insert("crm_emails_templates",$template);

    $template = new stdClass();
    $template->id_empresa = $id_empresa;
    $template->clave = "email-compra-ok-admin";
    $template->nombre = "Nueva compra";
    $template->texto = <<<TEXTO
<div style="background:#f8f8f8">
<table border="0" cellpadding="0" cellspacing="0" style="background:#f8f8f8; font-family:arial,helvetica,sans-serif; font-size:12px; line-height:20px; margin:0 auto; width:600px">
  <tbody>
    <tr>
      <td><a href="{{link_web}}" target="_blank" title="{{empresa}}"><img alt="{{empresa}}" class="CToWUd" src="{{empresa_logo}}" style="border: none; margin-top: 20px; margin-bottom: 20px;" /></a></td>
    </tr>
    <tr>
      <td>
      <div style="background:#fff;padding:16px;border:1px solid #eaeaea">
      <p>El cliente {{cliente}} ha hecho una compra. Puede verlo desde el panel de control.</p>
      </div>
      </td>
    </tr>
    <tr>
      <td style="text-align:right">&nbsp;</td>
    </tr>
  </tbody>
</table>
</div>
TEXTO;
    $this->db->insert("crm_emails_templates",$template);

    $template = new stdClass();
    $template->id_empresa = $id_empresa;
    $template->clave = "recuperar-clave";
    $template->nombre = "Recuperar contraseña";
    $template->texto = <<<TEXTO
<div style="background:#f8f8f8">
<table border="0" cellpadding="0" cellspacing="0" style="background:#f8f8f8; font-family:arial,helvetica,sans-serif; font-size:12px; line-height:20px; margin:0 auto; width:600px">
  <tbody>
    <tr>
      <td><a href="{{link_web}}" target="_blank" title="{{empresa}}"><img alt="{{empresa}}" class="CToWUd" src="{{empresa_logo}}" style="border: none; margin-top: 20px; margin-bottom: 20px;" /></a></td>
    </tr>
    <tr>
      <td>
      <div style="background:#fff;padding:16px;border:1px solid #eaeaea">
      <p>Hola {{cliente_nombre}}, tu nueva clave de acceso es: <strong>{{password}}</strong>.</p>
      </div>
      </td>
    </tr>
    <tr>
      <td style="text-align:right">&nbsp;</td>
    </tr>
  </tbody>
</table>
</div>
TEXTO;
    $this->db->insert("crm_emails_templates",$template);

  }

  function get_by_proyecto($clave,$id_proyecto) {
    // En esta funcion, buscamos la pagina web correspondiente al proyecto pasado por parametro
    // y volvemos a llamar a get_by_key
    $id_empresa = 0;
    if ($id_proyecto == 1) {
      // Pymvar
      $id_empresa = 127;
    } else if ($id_proyecto == 2) {
      // Shopvar
      $id_empresa = 119;
    } else if ($id_proyecto == 3) {
      // Inmovar
      $id_empresa = 118;
    } else if ($id_proyecto == 4) {
      // Inforvar
      $id_empresa = 0;
    } else if ($id_proyecto == 5) {
      // Colvar
      $id_empresa = 116;
    } else if ($id_proyecto == 6) {
      // Tripvar
      $id_empresa = 131;
    } else if ($id_proyecto == 7) {
      // Docvar
      $id_empresa = 0;
    } else if ($id_proyecto == 10) {
      // Restovar
      $id_empresa = 0;
    } else if ($id_proyecto == 11) {
      // Viajes
      $id_empresa = 0;
    } else if ($id_proyecto == 14) {
      // Clienapp
      $id_empresa = 290;
    } else if ($id_proyecto == 17) {
      // TurnoClick
      $id_empresa = 1000;
    }
    return $this->get_by_key($clave,$id_empresa);
  }

	function get_by_key($clave,$id_empresa=0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT * FROM crm_emails_templates ";
		$sql.= "WHERE clave = '$clave' AND id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}
	
  function get($id,$id_empresa=0) {
    if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM crm_emails_templates ";
    $sql.= "WHERE id = '$id' AND id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }
}