<?php
// Archivo de prueba TEMPORAL - borrar del servidor despues de usar.
include("sistema/params.php");

$resultado = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require "sistema/application/libraries/recaptchalib.php";
  $captcha = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : "";
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
<?php if ($resultado !== null) { ?>
  <pre><?php echo htmlspecialchars(print_r($resultado, true)) ?></pre>
  <hr>
<?php } ?>
<form method="post">
  <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY ?>"></div>
  <button type="submit">Probar</button>
</form>
</body>
</html>
