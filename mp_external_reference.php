<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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