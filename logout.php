<?php
//crear y destruir session  
@session_start();  
// vaciarla  
$_SESSION = array();  
// destruirla  
@session_destroy();

// Destruimos todas las cookies
setcookie("id_cliente_1", "", time()-3600);
setcookie("nombre_1", "", time()-3600);

$redirect = isset($_GET["url"]) ? filter_var($_GET["url"],FILTER_SANITIZE_STRING) : "/";

// Finalmente redireccionamos hacia el inicio
header("Location: ".$redirect);
?>