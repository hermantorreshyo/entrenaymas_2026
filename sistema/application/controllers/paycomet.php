<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("/var/www/sistema/application/libraries/Paycomet_Bankstore.php");
require APPPATH.'libraries/REST_Controller.php';

class Paycomet extends REST_Controller {
    
  function __construct() {
    parent::__construct();
    $this->load->model("Paycomet_Model", "modelo");
  }

  function crear_suscripcion_registro_iframe() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Europe/Madrid");

    @session_start();
    $fail_url = URL_BASE."/web/error/";
    $ok_url = URL_BASE."/web/muchas_gracias/";

    $this->load->model("Log_Model");

    // Parametros enviados por POST
    $periodicity = parent::get_post("periodicity",30);
    $user_id = parent::get_post("user_id",0);

    try {

      if ($periodicity == 30) {
        // Es Mensual
        $amount = 490;
      } else {
        // Es Anual
        $amount = 4900;
      }

      $bonificacion = 0;
      // Si el registro se realiza entre el 1 y el 10, se le cobra la totalidad de la cuota
      $startDate = date("Ymd");
      $endDate = date("Ym"."01",strtotime("+3 year"));
      $dia = date("d");

      // Si el registro se realiza entre el 11 y el 20, se le cobra este mes 50% y la cuota completa a partir del mes que viene
      if ($dia >= 11 && $dia <= 20 && $periodicity == 30) {
        $bonificacion = 245;
      }

      // Si el registro se realiza entre el 21 y el 31, se le bonifica la cuota de este mes y empieza el mes que viene
      if ($dia >= 21 && $periodicity == 30) {
        $bonificacion = 490;
      }

      // Si tenemos que aplicar una bonificacion por este mes
      if ($bonificacion > 0) {
        $sql = "UPDATE com_usuarios SET bonificacion = $bonificacion ";
        $sql.= "WHERE id = '$user_id' ";
        $this->db->query($sql);
      }      

      $order = "SUSCRIPCION-".$user_id."-".$startDate."-".date("H:m");

      $r = $this->modelo->createSubscriptionIframe(array(
        "id_usuario" => $user_id,
        "order" => $order,
        "startDate" => $startDate,
        "endDate" => $endDate,
        "amount" => $amount,
        "periodicity" => $periodicity,
        "urlOk" => $ok_url,
        "urlKo" => $fail_url,
      ));

      echo json_encode(array(
        "error"=>0,
        "order"=>$order,
        "redirect"=>$r->URL_REDIRECT,
      ));

    } catch(Exception $e) {

      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto" => $e->getMessage()."\n",
      ));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$e->getMessage(),
      ));
    }
  }  

  /*

  USABA EL METODO JET-ID PERO LO TERMINE DEPRECANDO PORQUE NO PERMITIA PAGO SEGURO

  function crear_suscripcion_registro() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Europe/Madrid");

    @session_start();
    $fail_url = URL_BASE."/web/error/";
    $ok_url = URL_BASE."/web/muchas_gracias/";

    $this->load->model("Log_Model");
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file" => Paycomet_Model::LOG_FILE,
      "texto" => print_r($_POST,true)."\n\n",
    ));

    // Parametros enviados por POST
    $periodicity = parent::get_post("periodicity",30);
    $user_id = parent::get_post("user_id",0);
    $token = parent::get_post("paytpvToken","");

    // Controlamos que todos los parametros sean los correctos
    if (empty($token)) {
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto"=>"TOKEN VACIO\n",
      ));
      header("Location: $fail_url");
    }
    if (strlen($token) != 64) {
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto"=>"LONGITUD DE TOKEN INVALIDA\n",
      ));
      header("Location: $fail_url");      
    }
    if (empty($user_id)) {
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto"=>"USER_ID INVALIDO\n",
      ));
      header("Location: $fail_url");      
    }

    try {

      if ($periodicity == 30) {
        // Es Mensual
        $amount = 490;
      } else {
        // Es Anual
        $amount = 4900;
      }

      $bonificacion = 0;
      // Si el registro se realiza entre el 1 y el 10, se le cobra la totalidad de la cuota
      $startDate = date("Ymd");
      $endDate = date("Ym"."01",strtotime("+3 year"));
      $dia = date("d");

      // Si el registro se realiza entre el 11 y el 20, se le cobra este mes 50% y la cuota completa a partir del mes que viene
      if ($dia >= 11 && $dia <= 20 && $periodicity == 30) {
        $bonificacion = 245;
      }

      // Si el registro se realiza entre el 21 y el 31, se le bonifica la cuota de este mes y empieza el mes que viene
      if ($dia >= 21 && $periodicity == 30) {
        $bonificacion = 490;
      }

      // Si tenemos que aplicar una bonificacion por este mes
      if ($bonificacion > 0) {
        $sql = "UPDATE com_usuarios SET bonificacion = $bonificacion ";
        $sql.= "WHERE id = '$user_id' ";
        $this->db->query($sql);
      }      

      $order = "SUSCRIPCION-".$user_id."-".$startDate."-".date("H:m");

      // Creamos el token de la tarjeta del usuario
      $result = $this->modelo->addUser(array(
        "user_id" => $user_id,
        "token" => $token,
      ));
      if (!$result) {
        $this->Log_Model->imprimir(array(
          "id_empresa" => Paycomet_Model::ID_EMPRESA,
          "file" => Paycomet_Model::LOG_FILE,
          "texto"=>"ERROR EN ADDUSER",
        ));
        header("Location: $fail_url");
      }
      if (isset($result->RESULT) && $result->RESULT == "KO") {
        $this->Log_Model->imprimir(array(
          "id_empresa" => Paycomet_Model::ID_EMPRESA,
          "file" => Paycomet_Model::LOG_FILE,
          "texto"=>"ERROR EN ADDUSER: ".$result->DS_ERROR_ID,
        ));
        header("Location: $fail_url");        
      }

      $result = $this->modelo->createSubscription(array(
        "order" => $order,
        "user_id" => $user_id,
        "token" => $token,
        "startDate" => $startDate,
        "endDate" => $endDate,
        "amount" => $amount,
        "periodicity" => $periodicity,
      ));
      if (!$result) {
        $this->Log_Model->imprimir(array(
          "id_empresa" => Paycomet_Model::ID_EMPRESA,
          "file" => Paycomet_Model::LOG_FILE,
          "texto"=>"ERROR EN CREATE SUBSCRIPTION",
        ));
        header("Location: $fail_url");
      }

      if (isset($result->RESULT) && $result->RESULT == "OK") {
        // Si la subscripcion es correcta, tenemos que activar el usuario
        $sql = "UPDATE com_usuarios SET activo = 1, id_perfiles = 1358 ";
        $sql.= "WHERE id = '$user_id' ";
        $this->db->query($sql);        
        $_SESSION["perfil"] = 1358;
      }

      header("Location: $ok_url");

    } catch(Exception $e) {

      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto" => $e->getMessage()."\n",
      ));
      header("Location: $fail_url");
    }
  }
  */
  
  // Esta funcion es la IPN cuando se realiza un cobro o suscripcion
  function subscription_ok() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->load->model("Log_Model");
    $this->Log_Model->imprimir(array(
      "id_empresa" => Paycomet_Model::ID_EMPRESA,
      "file"=>"ipn.txt",
      "texto"=>print_r($_POST,true)."\n\n",
    ));

    $order = parent::get_post("Order", "");
    $user_id = parent::get_post("IdUser", 0);
    $amount = parent::get_post("Amount", 0);
    $auth_code = parent::get_post("AuthCode", 0);
    $transactionType = parent::get_post("TransactionType", 1);

    $es_suscripcion = (strpos($order, "SUSCRIPCION") !== false);
    $es_verificacion = (strpos($order, "VERIFICACION") !== false);

    $usuario = $this->modelo->getUser(array(
      "paycomet_user_id" => $user_id
    ));
    if ($usuario === false) {
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file"=>"ipn.txt",
        "texto"=>"ERROR: No existe el usuario $user_id, buscamos ahora por ORDER",
      ));

      // Si no existe el usuario, nos fijamos si es una suscripcion que la ORDER ya tiene el ID
      if ($es_suscripcion || $es_verificacion) {
        $partes = explode("-", $order);
        if (sizeof($partes)>1) {
          $usuario = $this->modelo->getUser(array(
            "user_id" => $partes[1]
          ));

          if ($usuario != false) {
            // Tenemos que actualizar el IdUser y TokenUser porque es la primera vez
            $paycomet_id_user = parent::get_post("IdUser", 0);
            $paycomet_user_token = parent::get_post("TokenUser", "");
            $sql = "UPDATE com_usuarios SET ";
            if ($es_suscripcion) $sql.= "activo = 1, id_perfiles = 1358, ";
            $sql.= " paycomet_user = '$paycomet_id_user', paycomet_token = '$paycomet_user_token' ";
            $sql.= "WHERE id = '".$partes[1]."' ";
            $this->db->query($sql);            

          } else {
            return false;
          }

        } else {
          // Sino, no hacemos nada
          return false;          
        }
      } else {
        // Sino, no hacemos nada
        return false;
      }
    }

    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    $id_factura = 0;
    if ($es_suscripcion) {
      // Si es un pago de suscripcion
      $sql = "SELECT FP.* ";
      $sql.= "FROM facturas_paycomet FP ";
      $sql.= "WHERE FP.orden = '$order' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        // Actualizamos el estado a pagado
        $sql = "UPDATE facturas_paycomet ";
        $sql.= "SET estado = 1 ";
        $sql.= "WHERE orden = '$order' ";
        $this->db->query($sql);        
      } else {
        // Insertamos la nueva factura de suscripcion
        $this->load->model("Facturas_Paycomet_Model");
        $factura = new stdClass();
        $factura->id = 0;
        $factura->id_empresa = Paycomet_Model::ID_EMPRESA;
        $factura->id_usuario = $usuario->id;
        $factura->monto = $amount;
        $factura->fecha = date("Y-m-d H:i:s");
        $factura->estado = 1;
        $factura->auth_code = $auth_code;
        if ($amount > 1000) {
          $factura->tipo = "Subscripcion";
          $factura->periodicidad = 365;
        } else {
          $factura->tipo = "Subscripcion";
          $factura->periodicidad = 30;
        }
        $factura->orden = $order;
        $id_factura = $this->Facturas_Paycomet_Model->save($factura);          
      }

      // Actualizamos el usuario para que este activo
      $sql = "UPDATE com_usuarios SET activo = 1 ";
      $sql.= "WHERE id = '$usuario->id' ";
      $this->db->query($sql);

      // El campo bonificacion se usa una sola vez
      // Si aplica segun el registro del usuario se setea el monto que tenemos que aplicar el refund
      // Se hace en este paso porque el Order es necesario y cambia del devuelto por Paycomet
      if ($usuario->bonificacion > 0) {

        $this->Log_Model->imprimir(array(
          "id_empresa" => Paycomet_Model::ID_EMPRESA,
          "file"=>"ipn.txt",
          "texto"=>"$user_id aplica para bonificacion $usuario->bonificacion | REFERENCE: $order | AUTH CODE: $auth_code",
        ));

        $response_refund = $this->modelo->createRefund(array(
          "id_usuario" => $usuario->id,
          "amount" => $usuario->bonificacion,
          "reference" => $order,
          "auth_code" => $auth_code,
        ));
        if ($response_refund === false) {
          $this->Log_Model->imprimir(array(
            "id_empresa" => Paycomet_Model::ID_EMPRESA,
            "file"=>"ipn.txt",
            "texto"=>"RESPONSE REFUND ES FALSO",
          ));
        } else {
          $this->Log_Model->imprimir(array(
            "id_empresa" => Paycomet_Model::ID_EMPRESA,
            "file"=>"ipn.txt",
            "texto"=>print_r($response_refund,TRUE),
          ));          
        }

        if (isset($response_refund->RESULT) && $response_refund->RESULT == "OK") {
          // Lo blanqueamos para que no se aplique de nuevo
          $sql = "UPDATE com_usuarios SET bonificacion = 0 ";
          $sql.= "WHERE id = '$usuario->id' ";
          $this->db->query($sql);

          // Y agregamos la linea en la factura con el descuento correspondiente
          if (!empty($id_factura)) {
            $sql = "UPDATE facturas_paycomet SET monto = monto - $usuario->bonificacion, bonificacion = $usuario->bonificacion WHERE id = $id_factura";
            $this->db->query($sql);
          }
        }

      } // Fin de la bonificacion

      // El usuario ademas puede usar un cupon de descuento
      if (!empty($usuario->cupon)) {
        // Buscamos si existe un cupon de descuento con ese codigo
        $this->load->model("Cupones_Model");
        $porc_dto = $this->Cupones_Model->verificar_cupon($usuario->cupon,array(
          "id_empresa"=>$usuario->id_empresa,
          "use_if_valid"=>1, // Ya lo marca como que se uso
        ));
        if ($porc_dto > 0 && $usuario->cupon_aplicado == 0) {

          // Siempre tomamos como base el mensual, nunca el anual
          $amount = 490;
          // Al monto le descontamos la bonificacion
          $amount = $amount - $usuario->bonificacion;
          if ($amount > 0) {
           
            // Aplicamos una devolucion con el % del cupon
            $descuento = $amount * ($porc_dto / 100);

            $this->Log_Model->imprimir(array(
              "id_empresa" => Paycomet_Model::ID_EMPRESA,
              "file"=>"ipn.txt",
              "texto"=>"$user_id aplica el cupon de descuento [$usuario->cupon] por $porc_dto %. TOTAL A DESCONTAR: $descuento",
            ));

            $response_refund = $this->modelo->createRefund(array(
              "id_usuario" => $usuario->id,
              "amount" => $descuento,
              "reference" => $order,
              "auth_code" => $auth_code,
            ));
            if ($response_refund === false) {
              $this->Log_Model->imprimir(array(
                "id_empresa" => Paycomet_Model::ID_EMPRESA,
                "file"=>"ipn.txt",
                "texto"=>"RESPONSE REFUND CUPON ES FALSO",
              ));
            } else {
              $this->Log_Model->imprimir(array(
                "id_empresa" => Paycomet_Model::ID_EMPRESA,
                "file"=>"ipn.txt",
                "texto"=>print_r($response_refund,TRUE),
              ));          
            }

            if (isset($response_refund->RESULT) && $response_refund->RESULT == "OK") {
              // Lo blanqueamos para que no se aplique de nuevo
              $sql = "UPDATE com_usuarios SET cupon_aplicado = 1 ";
              $sql.= "WHERE id = '$usuario->id' ";
              $this->db->query($sql);

              // Y agregamos la linea en la factura con el descuento correspondiente
              if (!empty($id_factura)) {
                $sql = "SELECT * FROM cupones WHERE codigo = '$usuario->cupon' ";
                $q = $this->db->query($sql);
                $cupon_dto = $q->row();
                $cupon_descuento_descripcion = "Cupon de Descuento: ".$cupon_dto->codigo." ($cupon_dto->descuento %)";
                $sql = "UPDATE facturas_paycomet SET monto = monto - $descuento, cupon_descuento = $descuento, cupon_descuento_descripcion = '$cupon_descuento_descripcion' WHERE id = $id_factura";
                $this->db->query($sql);
              }
            }
          }

        }
      }

      // Enviamos un email al administrador
      $texto = $usuario->nombre." ha realizado un pago de la suscripcion del perfil Premium.";
      mandrill_send(array(
        "to"=>"info@entrenaymas.com",
        "subject"=>"Suscripcion Perfil Premium",
        "body"=>$texto,
        "bcc"=>$bcc_array,
      ));
    
    // ES UNA VERIFICACION DE PERFIL
    } else if ($es_verificacion) {

      // Lo verificamos
      $sql = "UPDATE com_usuarios_extension SET destacado = 1 WHERE id_usuario = $usuario->id ";
      $this->db->query($sql);

      // Insertamos la nueva factura
      $this->load->model("Facturas_Paycomet_Model");
      $factura = new stdClass();
      $factura->id = 0;
      $factura->id_empresa = Paycomet_Model::ID_EMPRESA;
      $factura->id_usuario = $usuario->id;
      $factura->monto = "390";
      $factura->fecha = date("Y-m-d H:i:s");
      $factura->estado = 1;
      $factura->tipo = "Verificacion de Perfil";
      $factura->orden = $order;    
      $this->Facturas_Paycomet_Model->save($factura);          

      // Enviamos un email al administrador
      $bcc_array = array("basile.matias99@gmail.com");
      require APPPATH.'libraries/Mandrill/Mandrill.php';        
      $texto = $usuario->nombre." ha realizado un pago para la verificacion de su perfil.";
      mandrill_send(array(
        "to"=>"info@entrenaymas.com",
        "subject"=>"Verificacion de Perfil",
        "body"=>$texto,
        "bcc"=>$bcc_array,
      )); 

    }
    echo "END";
  }


  function verificar_perfil_iframe() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Europe/Madrid");

    $fail_url = URL_BASE."/sistema/app/#pago_rechazado";
    $ok_url = URL_BASE."/sistema/app/#muchas_gracias";

    $this->load->model("Log_Model");

    // Parametros enviados por POST
    $user_id = parent::get_post("user_id",0);
    if (empty($user_id)) {
      $mensaje = "USER_ID INVALIDO";
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto"=>$mensaje."\n",
      ));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$mensaje,
      ));
      return;
    }

    try {

      // Un unico pago de 3.90
      $amount = 390;

      $order = "VERIFICACION-".$user_id."-".date("Y/m/d h:I:s");
      $r = $this->modelo->createPaymentIframe(array(
        "order" => $order,
        "id_usuario" => $user_id,
        "amount" => $amount,
        "urlOk" => $ok_url,
        "urlKo" => $fail_url,
      ));      

      echo json_encode(array(
        "error"=>0,
        "order"=>$order,
        "redirect"=>$r->URL_REDIRECT,
      ));

    } catch(Exception $e) {
      $mensaje = $e->getMessage();
      $this->Log_Model->imprimir(array(
        "id_empresa" => Paycomet_Model::ID_EMPRESA,
        "file" => Paycomet_Model::LOG_FILE,
        "texto" => $mensaje."\n",
      ));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$mensaje,
      ));
      return;
    }
  }



  function endSubscription() {
    $id_usuario = parent::get_post("id_usuario", 0);
    $usuario = $this->modelo->getUser(array(
      "user_id" => $id_usuario
    ));

    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';        
    $texto = $usuario->nombre." ha solicitado la baja de su suscripcion premium.";
    mandrill_send(array(
      "to"=>"info@entrenaymas.com",
      "subject"=>"Baja de suscripcion",
      "body"=>$texto,
      "bcc"=>$bcc_array,
    )); 

    $this->load->model("Usuario_Model");
    $new_user = new stdClass();
    $new_user->id = $id_usuario;
    $new_user->id_perfiles = 1357;
    $new_user->id_user_paycomet = "";
    $new_user->token_paycomet = "";
    $new_user->verificado = 0;
    $this->Usuario_Model->save($new_user);

    /*
    $response = $this->modelo->endSubscription(array(
      "id_usuario"=>$id_usuario,
      "id_empresa"=>Paycomet_Model::ID_EMPRESA,
    ));

    //if ($response == "ok") {
      $this->load->model("Usuario_Model");
      $user = $this->Usuario_Model->get($id_usuario,array(
        "id_empresa"=>Paycomet_Model::ID_EMPRESA
      ));

      $new_user = new stdClass();
      $new_user->id = $id_usuario;
      $new_user->id_perfiles = 1357;
      $new_user->id_user_paycomet = "";
      $new_user->token_paycomet = "";
      $new_user->verificado = 0;

      $this->Usuario_Model->save($new_user);

      echo json_encode(array(
        "error"=>0,
        "mensaje"=>"Su subscripcion ha finalizada correctamente",
      ));
      exit();
    //}

    echo json_encode(array(
      "error"=>1,
      "mensaje"=>"Hubo un error al finalizar su subscripcion",
    ));
    */
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Ha solicitado la baja de su suscripcion correctamente.",
    ));
  }

}