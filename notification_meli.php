<?php
http_response_code(200);

// Obtenemos los datos enviados por MercadoLibre de la entrada standard
$inputJSON = file_get_contents('php://input');

@file_put_contents("ipn/NEW_".microtime(true).".txt", $inputJSON);
echo "OK";
exit();
?>