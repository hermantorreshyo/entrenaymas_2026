<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// La tarea programada esta en el servidor de varcreative cada 1 minuto

// El applet de IFTTT esta registrado con la cuenta:
// no-reply@varcreative.com
// Pass: qu4r2200
// Pero el email que esta configurado para detectar el trigger de IFTTT es: videos.quepensaschacabuco@gmail.com

// Tambien hay un facebook que se llama "Quepensas Varcreative"
// mismo email y contraseña, con permisos de administrador
// que esta configurado para escribir con IFTTT

set_time_limit(0);
$id_empresa = 70;
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'videos.quepensaschacabuco@gmail.com';
$password = 'quepefortin2510';
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
$emails = imap_search($inbox,'UNSEEN');
if($emails) {        
  include("../../params.php");
  include("../libraries/PHPMailer/class.phpmailer.php");
  include("../libraries/PHPMailer/class.smtp.php");
  include("../helpers/file_helper.php");
  rsort($emails);        
  foreach($emails as $email_number) {                
    $overview = imap_fetch_overview($inbox,$email_number,0);
    $message = imap_fetchbody($inbox,$email_number,2);
    // Extraemos el ID del video
    $cadena = "youtube.com/watch?v=3D";
    $pos = strpos($message, $cadena) + strlen($cadena);
    $cadena2 = "&feature=3D";
    $pos2 = strpos($message, $cadena2);
    $id = substr($message, $pos, $pos2 - $pos);

    $titulo = "Transmision en vivo";
    $id_categoria = 71;
    $video = '<iframe width="100%" height="500" src="https://www.youtube.com/embed/'.$id.'?autoplay=1&mute=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
    $path = "https://img.youtube.com/vi/$id/0.jpg";

    $sql = "INSERT INTO not_entradas (id_empresa,titulo,fecha,id_categoria,activo,destacado,video,path) VALUES($id_empresa,'$titulo',NOW(),$id_categoria,1,1,'$video','$path') ";
    mysqli_query($conx,$sql);
    $id_entrada = mysqli_insert_id($conx);
    $link = "entrada/".filename($titulo,"-",0)."-$id_entrada/";
    $sql = "UPDATE not_entradas SET link = '$link' WHERE id = $id_entrada AND id_empresa = $id_empresa ";
    mysqli_query($conx,$sql);

    $mail = new PHPMailer(); 
    $mail->IsSMTP(); // send via SMTP
    $mail->SMTPAuth = true; // turn on SMTP authentication
    $mail->Host = "ssl://smtp.gmail.com";
    $mail->Port = 465;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->From = $username;
    $mail->AddAddress("trigger@applet.ifttt.com");
    $mail->IsHTML(true); // send as HTML
    $mail->Subject = "https://www.quepensaschacabuco.com/web/vivo/?id=$id";
    $mail->Body = $titulo;
    if(!$mail->Send()) {
      echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
      echo "Message has been sent";
    }

    break; // Solamente procesamos el ultimo email que llego
  }
}
imap_close($inbox);
?>