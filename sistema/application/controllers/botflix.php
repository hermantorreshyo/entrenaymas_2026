<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Botflix extends REST_Controller {

  private $access_token = "";
  private $profile = null;

  function webhook() {

    // Proceso de validacion de la URL
    $hub_mode = parent::get_get("hub_mode","");
    if ($hub_mode == "subscribe") {
      $hub_challenge = parent::get_get("hub_challenge","");
      $hub_verify_token = parent::get_get("hub_verify_token","");
      if ($hub_mode == 'subscribe' && $hub_verify_token == "EAAHCmHAUZAmYBAE602ZBPeua58T1ZAm71JTMs3do3ZCfvcZCLmIgI2gHFCabx0fZC7xEV8NgkZBgm9Y2GPmyjNS5GJEOJA6WORW1gldL4klyCjj5EJH5XWZCvpEYl7SZCXpxi9Nafnjna7bjwBePUDFLNwJWp8ypN7GQwF2hedZBCBQneZAbEykZCy1Q") {
        // Responds with the challenge token from the request
        http_response_code(200);
        echo $hub_challenge;
      } else {
        // Responds with '403 Forbidden' if verify tokens do not match
        http_response_code(403);
      }
      exit();
    }

    // ============================================

    // Proceso de recepcion de mensajes
    $request = json_decode(file_get_contents('php://input'));

    if ($request->object == "page") {
      foreach($request->entry as $entry) {
        $webhook_event = $entry->messaging[0];
        $sender_psid = $webhook_event->sender->id;
        $recipient_psid = $webhook_event->recipient->id;
        file_put_contents("botflix.txt", "SENDER ID: [$sender_psid]\n", FILE_APPEND);

        // Obtenemos el access token
        $this->getAccessToken($recipient_psid);
        file_put_contents("botflix.txt", "ACCESS TOKEN: [".$this->access_token."]\n", FILE_APPEND);

        // Obtenemos quien nos esta hablando
        $this->getProfile($sender_psid);
        file_put_contents("botflix.txt", print_r($this->profile, TRUE)."\n", FILE_APPEND);

        // Dependiendo del estado del cliente, tenemos que hacer una accion u otra

        if (isset($webhook_event->message)) {
          $this->handleMessage($sender_psid, $recipient_psid, $webhook_event->message);
        } else if (isset($webhook_event->postback)) {
          $this->handlePostback($sender_psid, $recipient_psid, $webhook_event->postback);
        }

      }
    }

    http_response_code(200);
    echo "EVENT_RECEIVED";
  }

  private function getAccessToken($recipient_psid) {
    $sql = "SELECT fb_page_access_token FROM web_configuracion WHERE fb_page_id = '".$recipient_psid."' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      $this->access_token = $r->fb_page_access_token;
    } else {
      $this->access_token = "";
    }
  }

  private function getProfile($sender_psid) {
    $url = "https://graph.facebook.com/".$sender_psid."?fields=first_name,last_name,profile_pic&access_token=".$this->access_token;
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $json = curl_exec($c);
    $json = json_decode($json);
    $this->profile = $json;
    curl_close($c);

    // Consultamos a la base de datos si ya existe ese cliente

  }

  // Handles messages events
  private function handleMessage($sender_psid, $recipient_psid, $received_message) {
    file_put_contents("botflix.txt", "HANDLE MESSAGE\n", FILE_APPEND);
    // Check if the message contains text
    if (isset($received_message->text)) {   
      // Create the payload for a basic text message
      $response = array(
        "attachment" => array(
          "type" => "template",
          "payload" => array(
            "template_type" => "generic",
            "elements" => array(
              array(
                "title" => "Turnos disponibles",
                "buttons" => array(
                  array(
                    "type" => "postback",
                    "title" => "Lunes 9/9",
                    "payload" => "lunes",
                  ),
                  array(
                    "type" => "postback",
                    "title" => "Martes 10/9",
                    "payload" => "martes",
                  ),
                ),
              ),
            ),
          ),
        ),
      );
    }  
    // Sends the response message
    $this->callSendAPI($sender_psid, $recipient_psid, $response);
  } 

  // Handles messaging_postbacks events
  private function handlePostback($sender_psid, $recipient_psid, $received_postback) {
    file_put_contents("botflix.txt", "HANDLE POSTBACK\n", FILE_APPEND);
    $response = array(
      "text" => "Tu turno ha sido aceptado! Muchas gracias!",
    );
    $this->callSendAPI($sender_psid, $recipient_psid, $response);
    // Set the response based on the postback payload
    /*
    if (payload === 'yes') {
      response = { "text": "Thanks!" }
    } else if (payload === 'no') {
      response = { "text": "Oops, try sending another image." }
    }
    // Send the message to acknowledge the postback
    callSendAPI(sender_psid, recipient_psid, response);
    */
  }

  // Sends response messages via the Send API
  private function callSendAPI($sender_psid, $recipient_psid, $response) {
    // Construct the message body
    $response_body = array(
      "recipient" => array(
        "id" => $sender_psid
      ),
      "message" => $response
    );
    $headers = array(
      'Content-Type: application/json'
    );
    file_put_contents("botflix.txt", "RESPONSE:".print_r($response_body,TRUE)." \n", FILE_APPEND);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, "https://graph.facebook.com/v2.6/me/messages?access_token=".$this->access_token);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($response_body));
    $result = curl_exec($ch);
    curl_close($ch);
  }

}