<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Emails extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Email_Model', 'modelo');
    }

  // PASA DE LOS EMAILS AL SISTEMA DE CONSULTAS NUEVO
  function pasar_a_consultas() {
    $id_empresa = 42;
    $sql = "SELECT * FROM crm_emails WHERE id_empresa = $id_empresa AND id_consulta != 0 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "INSERT INTO crm_consultas (";
      $sql.= " id_contacto, id_empresa, fecha, asunto, texto, id_origen, id_usuario, tipo, id_email_respuesta ";
      $sql.= ") VALUES (";
      $sql.= " $r->id_contacto, $r->id_empresa, '$r->fecha', 'Respuesta', '$r->texto', 5, '$r->id_usuario', 1, '$r->id_consulta' ";
      $sql.= ")";
      $this->db->query($sql);
    }
    echo "TERMINO";
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
        $path = "uploads/$id_empresa/$filename";
        @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
        echo json_encode(array(
            "path"=>$path,
            "error"=>0,
        ));
    }		
    
	function insert() {
		
		$array = $this->parse_put();
		if (isset($array->id_empresa)) $id_empresa = $array->id_empresa;
		else $id_empresa = parent::get_empresa();

		$this->load->model("Empresa_Model");
		$empresa = $this->Empresa_Model->get($id_empresa);
        
        $adjuntos = $array->adjuntos;
        $array->fecha = date("Y-m-d H:i:s");
        unset($array->adjuntos);

        // Archivo adjunto de VERDAD (NO LINK)
        $archivo = $array->archivo;
		
        // Guardamos el email
        $id_email = $this->modelo->save($array);
        

		// Adjuntamos los archivos que son de VERDAD
		include "resources/php/phpmailer/class.phpmailer.php";
		$email = new PHPMailer();
		$email->From = $array->email_from;
		$email->FromName = $empresa->nombre;
		if (!empty($array->email_cc)) $email->AddCC($array->email_cc);
		if (!empty($array->email_bcc)) $email->AddCC($array->email_bcc);
		$email->Subject = $array->asunto;
		$email->IsHTML(true);
		$email->Body = $texto;
		$email->AddAddress($array->email_to);

		if (!empty($archivo)) {
			$filename = end(explode("/", $archivo));
			$email->AddAttachment($archivo, $filename);
		}
		$resultado = $email->Send();

		$mensaje = "";
		if ($resultado === FALSE) {
			$mensaje = "Hubo un error al enviar el email.";
		} else {
			// Marcamos como enviados si corresponde
			foreach($adjuntos as $l) {
				// SI ES UN COMPROBANTE
				if ($l->tipo == 2) {
					$q = $this->db->query("UPDATE facturas SET enviada = 1 WHERE id = $l->id_objeto");
				}
			}
		}

		// Si el email fue una respuesta a una consulta
		if (!empty($array->id_consulta)) {
			$this->db->query("UPDATE crm_consultas SET id_email_respuesta = $id_email WHERE id = $array->id_consulta AND id_empresa = $id_empresa");
		}

		echo json_encode(array(
			"id"=>$id_email,
			"error"=>(($resultado === TRUE)?0:1),
			"mensaje"=>$mensaje,
		));
	}

}