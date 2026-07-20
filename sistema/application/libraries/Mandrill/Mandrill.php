<?php
/**
 * Envio de correos centralizado del proyecto.
 * Usa Google SMTP (Gmail / Workspace) via PHPMailer.
 * Mantiene el nombre de archivo y la funcion mandrill_send() para que
 * los ~40 puntos del proyecto que hacen:
 *   require APPPATH.'libraries/Mandrill/Mandrill.php';
 *   mandrill_send(array(...));
 * sigan funcionando sin cambios. Para cambiar de proveedor en el futuro,
 * solo hay que tocar este archivo.
 */

require_once dirname(__DIR__).'/PHPMailer/class.phpmailer.php';
require_once dirname(__DIR__).'/PHPMailer/class.smtp.php';

function mandrill_send($conf = array()) {

  $subject = isset($conf["subject"]) ? $conf["subject"] : "";
  $body = isset($conf["body"]) ? $conf["body"] : "";
  $body = mb_convert_encoding(str_replace("'", "\"", $body), 'UTF-8', 'ISO-8859-1');

  $from = isset($conf["from"]) ? $conf["from"] : MAIL_FROM_ADDRESS;
  $from_name = isset($conf["from_name"]) ? $conf["from_name"] : "Entrenaymas";

  // Pueden ser un string (separado por comas) o un array
  $to = isset($conf["to"]) ? $conf["to"] : "";
  $cc = isset($conf["cc"]) ? $conf["cc"] : "";
  $bcc = isset($conf["bcc"]) ? $conf["bcc"] : "";

  $to_name = isset($conf["to_name"]) ? $conf["to_name"] : "";
  $cc_name = isset($conf["cc_name"]) ? $conf["cc_name"] : "";
  $bcc_name = isset($conf["bcc_name"]) ? $conf["bcc_name"] : "";
  $reply_to = isset($conf["reply_to"]) ? $conf["reply_to"] : "";
  $reply_to_name = isset($conf["reply_to_name"]) ? $conf["reply_to_name"] : "";

  // Archivos adjuntos
  $attachments = isset($conf["attachments"]) ? $conf["attachments"] : array();

  if (empty($to)) return FALSE;

  $destinatarios_a_lista = function($valor) {
    if (empty($valor)) return array();
    if (is_array($valor)) return $valor;
    return array_map("trim", explode(",", $valor));
  };

  $mail = new PHPMailer(true); // true = lanzar excepciones ante fallos de envio

  try {
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->Host = GOOGLE_SMTP_HOST;
    $mail->Port = GOOGLE_SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Username = GOOGLE_SMTP_USER;
    $mail->Password = GOOGLE_SMTP_PASSWORD;

    $mail->setFrom($from, $from_name);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    foreach ($destinatarios_a_lista($to) as $t) {
      if (!empty($t)) $mail->addAddress($t, $to_name);
    }

    foreach ($destinatarios_a_lista($cc) as $c) {
      if (!empty($c)) $mail->addCC($c, $cc_name);
    }

    foreach ($destinatarios_a_lista($bcc) as $b) {
      if (!empty($b)) $mail->addBCC($b, $bcc_name);
    }

    if (!empty($reply_to)) {
      $mail->addReplyTo($reply_to, $reply_to_name);
    }

    foreach ($attachments as $ad) {
      if (file_exists($ad)) $mail->addAttachment($ad);
    }

    $res = $mail->send();
    file_put_contents(APPPATH."../mail_log.txt", date("Y-m-d H:i:s").": OK - to=".implode(",",$destinatarios_a_lista($to))." subject=\"".$subject."\"\n", FILE_APPEND);
    return $res;

  } catch (Exception $e) {
    file_put_contents(APPPATH."../mail_log.txt", date("Y-m-d H:i:s").": FALLO - to=".implode(",",$destinatarios_a_lista($to))." subject=\"".$subject."\" error=".$mail->ErrorInfo."\n", FILE_APPEND);
    return FALSE;
  }
}
