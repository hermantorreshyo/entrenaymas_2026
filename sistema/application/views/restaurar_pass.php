<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
<head>
<title>Panel de Control</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<base href="<?php echo $base_url; ?>"/>
<link rel="stylesheet" type="text/css" href="resources/css/common.css"/>
<link rel="stylesheet" href="resources/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="resources/css/animate.css" type="text/css" />
<link rel="stylesheet" href="resources/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="resources/css/simple-line-icons.css" type="text/css" />
<link rel="stylesheet" href="resources/css/font.css" type="text/css" />
<link rel="stylesheet" href="resources/css/app.css" type="text/css" />
<link rel="stylesheet" href="resources/css/loader.css" media="screen"/>

<script type="text/javascript" src="resources/js/jquery.js"></script>
<script type="text/javascript" src="resources/js/underscore.js"></script>
<script type="text/javascript" src="resources/js/backbone.js"></script>
<script type="text/javascript" src="resources/js/main.js"></script>
<script type="text/javascript" src="resources/js/md5.js"></script>

<script type="text/javascript" src="resources/js/libs/bootstrap.min.js"></script>
<script type="text/javascript" src="resources/js/common.js"></script>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$("#nombre").focus();
	$(".input").keypress(function(e){
		if (e.keyCode == 13) enviar();
	});
});

function enviar() {
	var id = $("#usuario_id").val();
	var password = validate_input("password",IS_EMPTY,"Por favor ingrese su nueva clave");
	var password2 = validate_input("password2",IS_EMPTY,"Por favor vuelva a ingresar su clave");
	if (password2 != password) {
		alert("Las claves no coinciden. Ingrese nuevamente.");
		$("#password2").focus();
		return false;
	}
	password = hex_md5(password);
	$.ajax({
		url: 'index.php/login/restaurar_pass',
		type: 'POST',
		dataType: 'json',
		data: {"password": password,"id":id },
		success: function(data) {
			alert(data.mensaje);
			window.location = "/sistema/";
		},
	});
}
</script>
</head>
<body>
<div class="container w-xxl w-auto-xs">
	<a href class="navbar-brand block m-t">Panel de Control</a>
	<div class="m-b-lg">
		<div class="wrapper text-center">
		  <strong>Restaurar contrase&ntilde;a</strong>
		</div>
		<form name="form" onsubmit="return false;" class="form-validation" action="index.php" method="post">
			<input type="hidden" id="usuario_id" value="<?php echo $id; ?>"/>
			<?php if (isset($error)) { ?>
				<div class="text-danger wrapper text-center">
					<?php echo $error ?>
				</div>
			<?php } ?>            
			<div class="list-group list-group-sm">
			  <div class="list-group-item">
				 <input type="password" name="password" id="password" placeholder="Password" class="form-control no-border" required>
			  </div>
			  <div class="list-group-item">
				 <input type="password" id="password2" placeholder="Reingresar Password" class="form-control no-border" required>
			  </div>
			</div>
			<button type="submit" onclick="enviar()" class="btn btn-lg btn-primary btn-block">Restaurar</button>
			<div style="text-align: center; margin-top: 20px">
				<a class="btn btn-default btn-sm btn-block" href="login/">Volver al Login</a>
			</div>			
			<div class="line line-dashed"></div>
		</form>
	</div>
</div>
</body>
</html> 
