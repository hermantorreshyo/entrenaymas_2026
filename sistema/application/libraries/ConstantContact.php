<?php
  class ConstantContact {

    private $con;
    private $id_empresa = 256;

    function __construct($config = array()){
      $this->id_empresa = 256;
      $servidor = "localhost"; 
      $usuario = "root"; 
      $password = "qu4r2200"; 
      $base = "servidor"; 
      $this->con = mysqli_connect($servidor, $usuario, $password, $base);
    }

    public function enviar_contacto($array=array()) {

      $objeto = new stdClass();
      $objeto->email_address = $array["email"];
      $objeto->list_memberships = $array["listas"];
      if (isset($array["nombre"])) $objeto->first_name = $array["nombre"];
      if (isset($array["apellido"])) $objeto->last_name = $array["apellido"];
      if (isset($array["telefono"])) $objeto->phone_number = $array["telefono"];
      if (isset($array["compania"])) $objeto->company_name = $array["compania"];
      if (isset($array["titulo"])) $objeto->job_titñe = $array["titulo"];
      $fields = json_encode($objeto);

      /*
      $fields = '{
        "email_address": "jdodge@example.com",
        "first_name": "Nico",
        "last_name": "Mati",
        "job_title": "Musician",
        "company_name": "Acme Corp.",
        "phone_number": "(555) 555-1212",
        "list_memberships": [
          "a3bfd2a6-3183-11ea-8915-d4ae5275dbea"
        ],
        "anniversary": "11-15-2006",
        "birthday_month": 11,
        "birthday_day": 24,
      }';
      */

      //conseguir tokens base de datos
      //{"access_token":"JE2jbo9XRgYEnKs6OySYyy4OFRF5","refresh_token":"iQxJkphs8R31oFHtxw6PUlvwtH4LXGLjXGI7XTzXVK","token_type":"Bearer"}
      $sql="SELECT ml_access_token, ml_refresh_token FROM web_configuracion WHERE id_empresa = $this->id_empresa LIMIT 0,1";
      $getTokens=mysqli_query($this->con, $sql);
      while ($fila=mysqli_fetch_array($getTokens)){
        $token=$fila["ml_access_token"];
        $refreshToken=$fila["ml_refresh_token"];
      }
      //headers
      $headers[] = "Authorization: Bearer $token";
      $headers[] = 'Content-Type: application/json';
      $headers[] = 'accept: applicaton/json';
      //logica
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, 'https://api.cc.email/v3/contacts/sign_up_form');
      curl_setopt($ch,CURLOPT_POST, 1);
      curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
      $result = curl_exec($ch);
      $resultado=json_decode($result);
      //si el token no funciona
      if (@$resultado->error_key=="unauthorized" or @$resultado->error_key=="token.invalid"){
        $resultado2=$this->refresh_token($refreshToken);
        $resultado2=json_decode($resultado2);
        $token=$resultado2->access_token;
        $refreshToken=$resultado2->refresh_token;
        $sql="UPDATE web_configuracion SET ml_access_token = '$token', ml_refresh_token = '$refreshToken' WHERE id_empresa = '$this->id_empresa' ";
        mysqli_query($this->con, $sql);
        $this->ejecuciondos($token, $fields);

      }
    }
    private function refresh_Token($refreshToken) { //refresca el token si el anterior no funciona
      $ch = curl_init();
      $base = 'https://idfed.constantcontact.com/as/token.oauth2';
      $url = $base . '?refresh_token=' . $refreshToken . '&grant_type=refresh_token';
      curl_setopt($ch, CURLOPT_URL, $url);
      $auth = "97a68f08-d460-4067-8181-538350002ad2" . ':' . "YVh3AX6eKfXVAMh77p2C4g";
      $credentials = base64_encode($auth);
      $authorization = 'Authorization: Basic ' . $credentials;
      curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }
    private function ejecuciondos($token, $fields){ //vuelve a ejecutar la primera funcion
      //diferente headers, mismo fields
      $headers[] = "Authorization: Bearer $token";
      $headers[] = 'Content-Type: application/json';
      $headers[] = 'accept: applicaton/json';

      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, 'https://api.cc.email/v3/contacts/sign_up_form');
      curl_setopt($ch,CURLOPT_POST, 1);
      curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
      $result = curl_exec($ch);
    }
  }
?>
