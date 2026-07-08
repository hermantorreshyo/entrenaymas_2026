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
	
	var email = $("#email").val();
	/*try {
		email = validate_input("email",IS_EMPTY,"Por favor ingrese su email");	
	} catch(e) {
		return false;
	}*/
	if (email == "" || email=="Email" ){
		alert ("Por favor ingrese su email")
		return false;
	}

	$.ajax({
		"url": '/sistema/usuarios/function/recuperar_pass/',
		"type": 'POST',
		"dataType": 'json',
		"data": {
			"email": email,
		},
		success: function(data) {
			alert(data.mensaje);
			location.reload();
		},
	});
	return false;
}
</script>
</head>
<body>
<div class="container w-xxl w-auto-xs">
	<a href class="navbar-brand block m-t">Panel de Control</a>
	<div class="m-b-lg">
		<div class="wrapper text-center">
		  <strong>Ingrese su email para recuperar su contrase&ntilde;a</strong>
		</div>
		<form name="form" onsubmit="return enviar();" class="form-validation">
			<?php if (isset($error)) { ?>
				<div class="text-danger wrapper text-center">
					<?php echo $error ?>
				</div>
			<?php } ?>            
			<div class="list-group list-group-sm">
			  <div class="list-group-item">
					<input type="text" name="email" id="email" placeholder="Email" class="form-control no-border" required>
			  </div>
			</div>
			<button type="submit" class="btn btn-lg btn-primary btn-block">Recuperar</button>
			<div style="text-align: center; margin-top: 20px">
				<a class="btn btn-default btn-sm btn-block" href="login/">Volver al Login</a>
			</div>			
			<div class="line line-dashed"></div>
		</form>
	</div>
</div>
</body>
</html> 
