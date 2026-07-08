<?php
class Whatsapp {

  private $token = "j1rhpdujoblcpvxj";
  private $instance = "https://eu123.chat-api.com/instance133101/";

  function __construct($token = "",$instance = "") {
    if (!empty($token)) $this->token = $token;
    if (!empty($instance)) $this->instance = $instance;
  }

  function send($config = array()) {

    $numbers = isset($config["numbers"]) ? $config["numbers"] : "";
    $body = isset($config["body"]) ? $config["body"] : "";
    if (empty($numbers)) return FALSE;
    if (empty($body)) return FALSE;

    $data = [
      'phone' => $numbers,
      'body' => $body,
    ];
    $json = json_encode($data); // Encode data to JSON
    file_put_contents("/home/ubuntu/data/sistema/log_whatsapp.txt", date("Y-m-d H:i:s").":\n".print_r($data,TRUE)."\n", FILE_APPEND);

    // URL for request POST /message
    $url = $this->instance.'message?token='.$this->token;
    // Make a POST request
    $options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
      ]
    ]);
    // Send a request
    $result = file_get_contents($url, false, $options);  
    return $result;    
  }

}
?>