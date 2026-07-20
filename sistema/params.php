<?php
// Desde PHP 8.1, mysqli lanza excepciones ante errores de consulta por defecto.
// Todo este codigo asume el comportamiento clasico (devolver FALSE), asi que lo restauramos.
mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() == PHP_SESSION_NONE) {
  @session_start();
}
date_default_timezone_set("America/Argentina/Buenos_Aires");
if (!defined("SERVER_DB")) { DEFINE ("SERVER_DB",(isset($_SERVER["SERVER_DB"]) ? $_SERVER["SERVER_DB"] : "localhost")); }
if (!defined("DATABASE")) { DEFINE ("DATABASE",(isset($_SERVER["DATABASE"]) ? $_SERVER["DATABASE"] : "demo-entrenaymas-mysql")); }
if (!defined("USER_DB")) { DEFINE ("USER_DB",(isset($_SERVER["USER_DB"]) ? $_SERVER["USER_DB"] : "entrenaymas_mysql")); }
if (!defined("PASSWORD_DB")) { DEFINE ("PASSWORD_DB",(isset($_SERVER["PASSWORD_DB"]) ? $_SERVER["PASSWORD_DB"] : "ey+db2026@")); }
if (!defined("FORCE_HTTPS")) { DEFINE ("FORCE_HTTPS",false); }

if (!defined("URL_BASE")) { DEFINE ("URL_BASE",(isset($_SERVER["URL_BASE"]) ? $_SERVER["URL_BASE"] : "https://test.entrenaymas.com")); }
if (!defined("PAYCOMET_TERMINAL")) { DEFINE ("PAYCOMET_TERMINAL",(isset($_SERVER["PAYCOMET_TERMINAL"]) ? $_SERVER["PAYCOMET_TERMINAL"] : "")); }
if (!defined("PAYCOMET_CODE")) { DEFINE ("PAYCOMET_CODE",(isset($_SERVER["PAYCOMET_CODE"]) ? $_SERVER["PAYCOMET_CODE"] : "")); }
if (!defined("PAYCOMET_JET_ID")) { DEFINE ("PAYCOMET_JET_ID",(isset($_SERVER["PAYCOMET_JET_ID"]) ? $_SERVER["PAYCOMET_JET_ID"] : "")); }
if (!defined("PAYCOMET_PASSWORD")) { DEFINE ("PAYCOMET_PASSWORD",(isset($_SERVER["PAYCOMET_PASSWORD"]) ? $_SERVER["PAYCOMET_PASSWORD"] : "")); }

if (!defined("GOOGLE_SMTP_HOST")) { DEFINE ("GOOGLE_SMTP_HOST",(isset($_SERVER["GOOGLE_SMTP_HOST"]) ? $_SERVER["GOOGLE_SMTP_HOST"] : "smtp.gmail.com")); }
if (!defined("GOOGLE_SMTP_PORT")) { DEFINE ("GOOGLE_SMTP_PORT",(isset($_SERVER["GOOGLE_SMTP_PORT"]) ? $_SERVER["GOOGLE_SMTP_PORT"] : "587")); }
if (!defined("GOOGLE_SMTP_USER")) { DEFINE ("GOOGLE_SMTP_USER",(isset($_SERVER["GOOGLE_SMTP_USER"]) ? $_SERVER["GOOGLE_SMTP_USER"] : "tecnologiaentrenaymas@gmail.com")); }
if (!defined("GOOGLE_SMTP_PASSWORD")) { DEFINE ("GOOGLE_SMTP_PASSWORD",(isset($_SERVER["GOOGLE_SMTP_PASSWORD"]) ? $_SERVER["GOOGLE_SMTP_PASSWORD"] : "qxjzfcoktvpyutxn")); }
if (!defined("MAIL_FROM_ADDRESS")) { DEFINE ("MAIL_FROM_ADDRESS",(isset($_SERVER["MAIL_FROM_ADDRESS"]) ? $_SERVER["MAIL_FROM_ADDRESS"] : GOOGLE_SMTP_USER)); }
if (!defined("MAIL_REPLY_TO_ADDRESS")) { DEFINE ("MAIL_REPLY_TO_ADDRESS",(isset($_SERVER["MAIL_REPLY_TO_ADDRESS"]) ? $_SERVER["MAIL_REPLY_TO_ADDRESS"] : GOOGLE_SMTP_USER)); }

if (!defined("RECAPTCHA_SITE_KEY")) { DEFINE ("RECAPTCHA_SITE_KEY",(isset($_SERVER["RECAPTCHA_SITE_KEY"]) ? $_SERVER["RECAPTCHA_SITE_KEY"] : "6LfSqFstAAAAAK3EGtFcaUhUXZTjb0JQehtld2bQ")); }
if (!defined("RECAPTCHA_SECRET_KEY")) { DEFINE ("RECAPTCHA_SECRET_KEY",(isset($_SERVER["RECAPTCHA_SECRET_KEY"]) ? $_SERVER["RECAPTCHA_SECRET_KEY"] : "6LfSqFstAAAAAFNlGjXjGpu1hjOY2kaHVPDcUMSn")); }

if (!function_exists("get_conex")) {
  function get_conex() {
    // Conectamos con la base de datos
    $conx = mysqli_connect(SERVER_DB,USER_DB,PASSWORD_DB,DATABASE);
	//$conx = mysqli_connect("mysql",USER_DB,PASSWORD_DB,DATABASE);
    if ($conx === FALSE) {
      echo "Error al conectar con la base de datos";
      return;
    }
    mysqli_set_charset($conx, "utf8");
    return $conx;
  }
}

if (!function_exists("get_mysqli")) {
  function get_mysqli() {
    // Conectamos con la base de datos
    $mysqli = new mysqli(SERVER_DB,USER_DB,PASSWORD_DB,DATABASE);
    if ($mysqli === FALSE) {
      echo "Error al conectar con la base de datos";
      return;
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
  }
}

if (!function_exists("get_conex_local_data")) {
  function get_conex_local_data() {
    $connection = mysqli_init();
    mysqli_options($connection,MYSQLI_OPT_LOCAL_INFILE,true);
    mysqli_real_connect($connection,SERVER_DB,USER_DB,PASSWORD_DB,DATABASE);
    return $connection;
  }
}

if (!function_exists("current_url")) {
  function current_url($solo_dominio = FALSE, $sin_parametros = FALSE) {
    $pageURL = URL_BASE;
    if (!$solo_dominio) $pageURL.= $_SERVER["REQUEST_URI"];
    if ($sin_parametros && strpos($pageURL, "?")>0) {
      $pageURL = substr($pageURL, 0, strpos($pageURL, "?"));
    }
    return $pageURL;
  }
}

if (!function_exists("send_error")) {
  function send_error($config = array()) {
    $to = (isset($config["to"]) ? $config["to"] : "basile.matias99@gmail.com");
    $subject = (isset($config["subject"]) ? $config["subject"] : "ERROR");
    $message = (isset($config["message"]) ? $config["message"] : "");
    $log_file = (isset($config["log_file"]) ? $config["log_file"] : "");
    $headers = "From:".MAIL_FROM_ADDRESS."\r\n";
    $headers.= "MIME-Version: 1.0\r\n";
    $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    @mail($to,$subject,$message,$headers);
    if (!empty($log_file)) {
      file_put_contents($log_file, date("Y-m-d H:i:s").": ".$subject."\n".$message."\n\n",FILE_APPEND);
    }
  }
}

$conx = get_conex();
?>