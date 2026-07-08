<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Poloniex extends REST_Controller {

  function get_bitcoin() {
    $url = "https://poloniex.com/public?command=returnTicker";
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $html = curl_exec($c);
    $resultado = json_decode($html);
    echo $resultado->USDT_EOSBEAR->last;    
  }
}