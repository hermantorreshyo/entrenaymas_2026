<?php
file_put_contents("log_webhook_mandrill.txt", print_r($_POST,true), FILE_APPEND);
if (!isset($_POST["mandrill_events"])) exit();

include_once("../../params.php");
$events = json_decode($_POST["mandrill_events"]);
foreach($events as $e) {
	if ($e->event == "open") {
		$msg = $e->msg;
		file_put_contents("log_webhook_mandrill.txt", "EMAIL: $msg->email\n", FILE_APPEND);
		if (isset($msg->metadata)) {
			$metadata = $msg->metadata;
			file_put_contents("log_webhook_mandrill.txt", "METADATA: ".print_r($metadata,TRUE)."\n", FILE_APPEND);
			if (isset($metadata["id_contacto"]) && isset($metadata["id_consulta"]) && isset($metadata["id_empresa"])) {
				$id_contacto = $metadata["id_contacto"];
				$id_consulta = $metadata["id_consulta"];
				$id_empresa = $metadata["id_empresa"];
				$sql = "UPDATE crm_consultas SET fecha_visto = NOW() ";
				$sql.= "WHERE id_empresa = $id_empresa ";
				$sql.= "AND id = $id_consulta ";
				$sql.= "AND id_contacto = $id_contacto ";
				mysqli_query($conx,$sql);
				file_put_contents("log_webhook_mandrill.txt", "$sql\n", FILE_APPEND);
			}
		} else {
			file_put_contents("log_webhook_mandrill.txt", "NO EXISTE METADATA\n", FILE_APPEND);
		}
	}
}
?>