<?php
include_once("CotizarEnvio.php");
include_once("Andreani.php");

// Los siguientes datos son de prueba, para la implementación en un entorno productivo deberán reemplazarse por los verdaderos
$request = new CotizarEnvio();
$request->setCodigoDeCliente('CL0003750');
$request->setNumeroDeContrato('400006710');
$request->setCodigoPostal('1014');
$request->setPeso(500);
//$request->setVolumen(100);
//$request->setValorDeclarado(100);

$andreani = new Andreani('STAGING_WS','ANDREANI','prod');
$response = $andreani->call($request);
if($response->isValid()){
    $tarifa = $response->getMessage()->CotizarEnvioResult->Tarifa;
    echo "La cotización funcionó bien y la tarifa es $tarifa";
} else {
    echo "La cotización falló, el mensaje de error es el siguiente";
    var_dump($response->getMessage());
}
?>