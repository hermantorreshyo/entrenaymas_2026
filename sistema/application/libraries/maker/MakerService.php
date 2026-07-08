<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Clase utilizada para conectar con Maker Servicios
// http://www.makerservicios.com.ar:60005/MakerServicios.asmx?WSDL
class MakerService {
  
  public function __construct() {
  }
  
  public function get_soap() {
    return new SoapClient("MakerServicios.xml",array(
      'soap_version'=>SOAP_1_2,
    ));
  }

  public function hello() {
    $client = $this->get_soap();
    $h = $client('HelloWorld');
    print_r($h);
  }
  
}
?>