<?php
header('Content-Type: text/html; charset=UTF-8');

function set_language($lang) {
	$disponibles = array("es","en");
  if (in_array($lang, $disponibles)) $_SESSION["language"] = $lang;  
}	

function get_language() {
	$disponibles = array("es","en");
  if (session_status() == PHP_SESSION_NONE) @session_start();
  if (isset($_SESSION["language"])) return $_SESSION["language"]; 
  if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && $_SERVER['HTTP_ACCEPT_LANGUAGE'] != ''){ 
    $idiomas = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    for ($i=0; $i<count($idiomas); $i++){
      if (!isset($_SESSION["language"])){
        foreach($disponibles as $lang) {
          $idioma = substr($idiomas[$i], 0, 2);
          if ($lang == $idioma) {
            set_language($lang);
            return $lang;
          }
        }
      }
    }
  }
  // Si aún no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
  if (!isset($_SESSION["language"])){
    set_language("es");
  }
  return $_SESSION["language"];
}

$lang = get_language();

$ll = array(
	"es"=>array(
		"title"=>"Entrenaymas",
		"subtitle"=>"Ingrese sus datos",
		"email"=>"Email",
		"password"=>"Password",
		"enter_button"=>"Entrar",
		"error_nombre"=>"Por favor ingrese su correo electronico.",
		"error_clave"=>"Por favor ingrese su clave de acceso.",
		"error_login"=>"Nombre de usuario y/o password incorrectos.",
	),
	"en"=>array(
		"title"=>"Control Panel",
		"subtitle"=>"Log In",
		"email"=>"Email Address",
		"password"=>"Password",
		"enter_button"=>"Log In",
		"error_nombre"=>"Please enter your email.",
		"error_clave"=>"Please enter your password.",
		"error_login"=>"Error: The email or password are wrong.",
	),
);
?>
<!DOCTYPE html>
<html>
<head>
<head>
<title><?php echo $ll[$lang]["title"] ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="shortcut icon" href="/sistema/resources/images/favicon.ico" type="image/x-icon">
<link rel="icon" href="/sistema/resources/images/favicon.ico" type="image/x-icon">
<!-- ANDA -->
<!-- <?php print_r($_SERVER); ?>-->
<!-- <?php echo URL_BASE ?>-->
<base href="<?php echo URL_BASE ?>/sistema/"/>
<link rel="stylesheet" type="text/css" href="/sistema/resources/css/common.css"/>
<link rel="stylesheet" href="/sistema/resources/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/animate.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/simple-line-icons.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/font.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/app.css" type="text/css" />
<link rel="stylesheet" href="/sistema/resources/css/loader.css" media="screen"/>

<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script>
<script type="text/javascript" src="/sistema/resources/js/underscore.js"></script>
<script type="text/javascript" src="/sistema/resources/js/backbone.js"></script>
<script type="text/javascript" src="/sistema/resources/js/main.js"></script>
<script type="text/javascript" src="/sistema/resources/js/md5.js"></script>

<script type="text/javascript" src="/sistema/resources/js/libs/bootstrap.min.js"></script>
<script type="text/javascript" src="/sistema/resources/js/common.js"></script>

<script type="text/javascript">
jQuery(document).ready(function($) {
  $("#nombre").focus();
  $(".input").keypress(function(e){
    if (e.keyCode == 13) enviar();
  });
});
</script>
<script type="text/javascript">
function enviar() {
  var nombre = validate_input("nombre",IS_EMPTY,"<?php echo $ll[$lang]["error_nombre"] ?>");
  var password = validate_input("password",IS_EMPTY,"<?php echo $ll[$lang]["error_clave"] ?>");
  password = unescape(password); // Problema con passwords que contenian $
  password = hex_md5(password);
  $.ajax({
    url: '/sistema/login/check',
    type: 'POST',
    dataType: 'json',
    data: {nombre: nombre, 'password': password },
    success: function(data, textStatus, xhr) {
      if (data.error == false) {
        window.location = "/sistema/app/";
      } else {
        if (data.mensaje !== undefined) {
          alert(data.mensaje);
        } else {
          alert("<?php echo $ll[$lang]["error_login"] ?>");
        }
        $("#nombre").focus();                
      }
    },
  });
}
</script>
</head>
<body>
<div class="container w-xxl w-auto-xs">
  <a href class="navbar-brand block m-t">
    <?php echo $ll[$lang]["title"] ?>
  </a>
  <div class="m-b-lg">
    <div class="wrapper text-center">
      <strong><?php echo $ll[$lang]["subtitle"] ?></strong>
    </div>
    <form name="form" onsubmit="return false;" class="form-validation" action="index.php" method="post">
      <?php if (isset($error)) { ?>
        <div class="text-danger wrapper text-center">
          <?php echo $error ?>
        </div>
      <?php } ?>            
      <div class="list-group list-group-sm">
        <div class="list-group-item">
        <input type="text" name="nombre" id="nombre" placeholder="<?php echo $ll[$lang]["email"] ?>" class="form-control no-border" required>
        </div>
        <div class="list-group-item">
         <input type="password" name="password" id="password" placeholder="<?php echo $ll[$lang]["password"] ?>" class="form-control no-border" required>
        </div>
      </div>
      <button type="submit" onclick="enviar()" class="btn btn-lg btn-primary btn-block"><?php echo $ll[$lang]["enter_button"] ?></button>
      <div style="text-align: center; margin-top: 20px">
        <a class="btn btn-default btn-sm btn-block" href="/sistema/login/recuperar/">Recuperar Contrase&ntilde;a</a>
      </div>      
      <?php /*      
      <div class="line line-dashed"></div>
      <div class="text-center">
        <div>&iquest;Todavia no tenes una cuenta?</div>
        <div style="margin-top: 20px">
          <a class="btn btn-info btn-lg btn-block" href="/registro/">Registrate</a>
        </div>
      </div>
      */ ?>
    </form>
  </div>
</div>
</body>
</html> 