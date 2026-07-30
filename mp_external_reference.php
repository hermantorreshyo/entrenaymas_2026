<?php
error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

include_once("models/mercadopago.php");
$mp = new MP ("2235221498077246","4N48GNSPtmGdvDzu0f5RAYkkhHuPHTcN");
$payment = $mp->get(
    "/v1/payments/search",
    array(
        "external_reference" => "387546"
    )
);
print_r($payment);
?>