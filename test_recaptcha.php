<?php
// Archivo de prueba TEMPORAL - borrar del servidor despues de usar.
include("sistema/params.php");

$info_servidor = array(
  "fecha_servidor" => date("Y-m-d H:i:s"),
  "php_version" => phpversion(),
  "curl_version" => function_exists("curl_version") ? curl_version()["version"] : "sin curl",
  "ssl_version" => function_exists("curl_version") ? curl_version()["ssl_version"] : "sin curl",
  "http_proxy_env" => getenv("http_proxy") ?: getenv("HTTP_PROXY") ?: "(ninguno)",
  "https_proxy_env" => getenv("https_proxy") ?: getenv("HTTPS_PROXY") ?: "(ninguno)",
  "allow_url_fopen" => ini_get("allow_url_fopen"),
);

$resultado = null;
$raw_vs_post = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $raw_input = file_get_contents("php://input");
  $captcha = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : "";

  // Parseamos nosotros mismos el raw body, sin pasar por $_POST, para comparar
  parse_str($raw_input, $parsed_raw);
  $captcha_desde_raw = isset($parsed_raw["g-recaptcha-response"]) ? $parsed_raw["g-recaptcha-response"] : "";

  $raw_vs_post = array(
    "longitud_raw_input_completo" => strlen($raw_input),
    "longitud_POST_g_recaptcha_response" => strlen($captcha),
    "longitud_parseado_a_mano_desde_raw" => strlen($captcha_desde_raw),
    "coinciden_POST_vs_raw" => ($captcha === $captcha_desde_raw) ? "SI" : "NO - DIFIEREN",
  );

  require "sistema/application/libraries/recaptchalib.php";
  $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET_KEY);
  $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $captcha);
  $resultado = array(
    "longitud_token" => strlen($captcha),
    "success" => $resp->success,
    "errorCodes" => $resp->errorCodes,
  );
}
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<h3>Test aislado de reCAPTCHA</h3>
<pre>INFO SERVIDOR:
<?php echo htmlspecialchars(print_r($info_servidor, true)) ?></pre>
<?php if ($resultado !== null) { ?>
  <pre>RAW vs POST:
<?php echo htmlspecialchars(print_r($raw_vs_post, true)) ?></pre>
  <pre>RESULTADO:
<?php echo htmlspecialchars(print_r($resultado, true)) ?></pre>
  <hr>
<?php } ?>
<form method="post">
  <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY ?>"></div>
  <button type="submit">Probar</button>
</form>
</body>
</html>
