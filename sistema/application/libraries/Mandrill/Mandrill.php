<?php

require_once 'Mandrill/Templates.php';
require_once 'Mandrill/Exports.php';
require_once 'Mandrill/Users.php';
require_once 'Mandrill/Rejects.php';
require_once 'Mandrill/Inbound.php';
require_once 'Mandrill/Tags.php';
require_once 'Mandrill/Messages.php';
require_once 'Mandrill/Whitelists.php';
require_once 'Mandrill/Ips.php';
require_once 'Mandrill/Internal.php';
require_once 'Mandrill/Subaccounts.php';
require_once 'Mandrill/Urls.php';
require_once 'Mandrill/Webhooks.php';
require_once 'Mandrill/Senders.php';
require_once 'Mandrill/Metadata.php';
require_once 'Mandrill/Exceptions.php';

class Mandrill {
    
    public $apikey;
    public $ch;
    public $root = 'https://mandrillapp.com/api/1.0';
    public $debug = false;

    public static $error_map = array(
        "ValidationError" => "Mandrill_ValidationError",
        "Invalid_Key" => "Mandrill_Invalid_Key",
        "PaymentRequired" => "Mandrill_PaymentRequired",
        "Unknown_Subaccount" => "Mandrill_Unknown_Subaccount",
        "Unknown_Template" => "Mandrill_Unknown_Template",
        "ServiceUnavailable" => "Mandrill_ServiceUnavailable",
        "Unknown_Message" => "Mandrill_Unknown_Message",
        "Invalid_Tag_Name" => "Mandrill_Invalid_Tag_Name",
        "Invalid_Reject" => "Mandrill_Invalid_Reject",
        "Unknown_Sender" => "Mandrill_Unknown_Sender",
        "Unknown_Url" => "Mandrill_Unknown_Url",
        "Unknown_TrackingDomain" => "Mandrill_Unknown_TrackingDomain",
        "Invalid_Template" => "Mandrill_Invalid_Template",
        "Unknown_Webhook" => "Mandrill_Unknown_Webhook",
        "Unknown_InboundDomain" => "Mandrill_Unknown_InboundDomain",
        "Unknown_InboundRoute" => "Mandrill_Unknown_InboundRoute",
        "Unknown_Export" => "Mandrill_Unknown_Export",
        "IP_ProvisionLimit" => "Mandrill_IP_ProvisionLimit",
        "Unknown_Pool" => "Mandrill_Unknown_Pool",
        "NoSendingHistory" => "Mandrill_NoSendingHistory",
        "PoorReputation" => "Mandrill_PoorReputation",
        "Unknown_IP" => "Mandrill_Unknown_IP",
        "Invalid_EmptyDefaultPool" => "Mandrill_Invalid_EmptyDefaultPool",
        "Invalid_DeleteDefaultPool" => "Mandrill_Invalid_DeleteDefaultPool",
        "Invalid_DeleteNonEmptyPool" => "Mandrill_Invalid_DeleteNonEmptyPool",
        "Invalid_CustomDNS" => "Mandrill_Invalid_CustomDNS",
        "Invalid_CustomDNSPending" => "Mandrill_Invalid_CustomDNSPending",
        "Metadata_FieldLimit" => "Mandrill_Metadata_FieldLimit",
        "Unknown_MetadataField" => "Mandrill_Unknown_MetadataField"
    );

    public function __construct($apikey=null) {
        if(!$apikey) $apikey = getenv('MANDRILL_APIKEY');
        if(!$apikey) $apikey = $this->readConfigs();
        if(!$apikey) throw new Mandrill_Error('You must provide a Mandrill API key');
        $this->apikey = $apikey;

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.55');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 600);

        $this->root = rtrim($this->root, '/') . '/';

        $this->templates = new Mandrill_Templates($this);
        $this->exports = new Mandrill_Exports($this);
        $this->users = new Mandrill_Users($this);
        $this->rejects = new Mandrill_Rejects($this);
        $this->inbound = new Mandrill_Inbound($this);
        $this->tags = new Mandrill_Tags($this);
        $this->messages = new Mandrill_Messages($this);
        $this->whitelists = new Mandrill_Whitelists($this);
        $this->ips = new Mandrill_Ips($this);
        $this->internal = new Mandrill_Internal($this);
        $this->subaccounts = new Mandrill_Subaccounts($this);
        $this->urls = new Mandrill_Urls($this);
        $this->webhooks = new Mandrill_Webhooks($this);
        $this->senders = new Mandrill_Senders($this);
        $this->metadata = new Mandrill_Metadata($this);
    }

    public function __destruct() {
        curl_close($this->ch);
    }

    public function call($url, $params) {
        $params['key'] = $this->apikey;
        $params = json_encode($params);
        $ch = $this->ch;

        curl_setopt($ch, CURLOPT_URL, $this->root . $url . '.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);

        $start = microtime(true);
        $this->log('Call to ' . $this->root . $url . '.json: ' . $params);
        if($this->debug) {
            $curl_buffer = fopen('php://memory', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        }

        $response_body = curl_exec($ch);
        $info = curl_getinfo($ch);
        $time = microtime(true) - $start;
        if($this->debug) {
            rewind($curl_buffer);
            $this->log(stream_get_contents($curl_buffer));
            fclose($curl_buffer);
        }
        $this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        $this->log('Got response: ' . $response_body);

        if(curl_error($ch)) {
            throw new Mandrill_HttpError("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);
        if($result === null) throw new Mandrill_Error('We were unable to decode the JSON response from the Mandrill API: ' . $response_body);
        
        if(floor($info['http_code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function readConfigs() {
        $paths = array('~/.mandrill.key', '/etc/mandrill.key');
        foreach($paths as $path) {
            if(file_exists($path)) {
                $apikey = trim(file_get_contents($path));
                if($apikey) return $apikey;
            }
        }
        return false;
    }

    public function castError($result) {
        if($result['status'] !== 'error' || !$result['name']) throw new Mandrill_Error('We received an unexpected error: ' . json_encode($result));

        $class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Mandrill_Error';
        return new $class($result['message'], $result['code']);
    }

    public function log($msg) {
        if($this->debug) error_log($msg);
    }
}



function mandrill_send($conf = array()) {

  $subject = isset($conf["subject"]) ? $conf["subject"] : "";
  $body = isset($conf["body"]) ? $conf["body"] : "";
  $body = utf8_encode(str_replace("'", "\"", $body));

  $important = isset($conf["important"]) ? $conf["important"] : false;
  $from = isset($conf["from"]) ? $conf["from"] : "no-reply@varcreative.com";
  $from_name = isset($conf["from_name"]) ? $conf["from_name"] : "Varcreative";

  // Pueden ser un string o un array
  $to = isset($conf["to"]) ? $conf["to"] : "";
  $cc = isset($conf["cc"]) ? $conf["cc"] : "";
  $bcc = isset($conf["bcc"]) ? $conf["bcc"] : "";

  $to_name = isset($conf["to_name"]) ? $conf["to_name"] : "";
  $cc_name = isset($conf["cc_name"]) ? $conf["cc_name"] : "";
  $bcc_name = isset($conf["bcc_name"]) ? $conf["bc_name"] : "";
  $reply_to = isset($conf["reply_to"]) ? $conf["reply_to"] : "";
  $reply_to_name = isset($conf["reply_to_name"]) ? $conf["reply_to_name"] : "";

  // Archivos adjuntos
  $attachments = isset($conf["attachments"]) ? $conf["attachments"] : array();

  // Informacion que se utiliza para identificar al email
  $metadata = isset($conf["metadata"]) ? $conf["metadata"] : array();

  if (empty($to)) return FALSE;
  $emails = array();

  if (is_string($to) && !empty($to)) {

    if (strpos($to, ",") !== FALSE) {
      $ems = explode(",", $to);
      foreach($ems as $em) {
        $em = trim($em);
        $a = array(
          "email"=>$em,
          "type"=>"to",
        );
        if (!empty($em)) {
          $emails[] = $a;
        }
      }
    } else {
      $a = array(
        "email"=>$to,
        "type"=>"to",
      );
      if (!empty($to_name)) $a["name"] = $to_name;
      $emails[] = $a;
    }
    
  } else if (is_array($to)) {
    foreach($to as $t) {
      if (!empty($t)) {
        $emails[] = array("email"=>$t);
      }
    }
  }

  // Con copia a
  if (is_string($cc) && !empty($cc)) {
    $a = array(
      "email"=>$cc,
      "type"=>"cc",
    );
    if (!empty($cc_name)) $a["name"] = $cc_name;
    $emails[] = $a;
  } else if (is_array($cc)) {
    foreach($cc as $t) {
      if (!empty($t)) {
        $emails[] = array("email"=>$t,"type"=>"cc");
      }
    }
  }

  // Con copia oculta
  if (is_string($bcc) && !empty($bcc)) {

    if (strpos($bcc, ",") !== FALSE) {
      $ems = explode(",", $bcc);
      foreach($ems as $em) {
        $em = trim($em);
        if (!empty($em)) {
          $a = array(
            "email"=>$em,
            "type"=>"bcc",
          );
          $emails[] = $a;
        }
      }
    } else {
      $a = array(
        "email"=>$bcc,
        "type"=>"bcc",
      );      
      if (!empty($bcc_name)) $a["name"] = $bcc_name;
      $emails[] = $a;
    }
  } else if (is_array($bcc)) {
    foreach($bcc as $t) {
      if (!empty($t)) {
        $emails[] = array("email"=>$t,"type"=>"bcc");
      }
    }
  }

  // Si tiene adjuntos
  $adjuntos = array();
  if (is_array($attachments) && sizeof($attachments)>0) {
    foreach($attachments as $ad) {
      if (file_exists($ad)) {
        $file_mime = mime_content_type($ad);
        $file_content = base64_encode(file_get_contents($ad));
        $file_name = basename($ad);
        $adjuntos[] = array(
          "type"=>$file_mime,
          "name"=>$file_name,
          "content"=>$file_content,
        );
      }
    }
  }

  try {
    $mandrill = new Mandrill('md-nDzIPS-pFBwKKlbRPMI6qw'); // API KEY
    $message = array(
      'html' => $body,
      'subject' => $subject,
      'from_email' => $from,
      'from_name' => $from_name,
      'to' => $emails,
      'important' => $important,
      'track_opens' => true,
      'track_clicks' => true,
      'auto_text' => null,
      'auto_html' => null,
      'inline_css' => null,
      'url_strip_qs' => null,
      'preserve_recipients' => false,
      'view_content_link' => null,
      'tracking_domain' => null,
      'signing_domain' => null,
      'return_path_domain' => null,
      'merge' => true,
      'merge_language' => 'mailchimp',
    );
    if (!empty($reply_to)) {
      $message['headers'] = array('Reply-To'=>$reply_to);
    }
    if (sizeof($adjuntos)>0) {
      $message['attachments'] = $adjuntos;
    }
    if (sizeof($metadata)>0) {
      $message['metadata'] = $metadata;
    }
    $async = true;
    $ip_pool = 'Main Pool';

    $result = $mandrill->messages->send($message, $async, $ip_pool);
    return TRUE;
  } catch(Exception $e) {
    file_put_contents("mandrill.txt", print_r($e,TRUE), FILE_APPEND);
    return FALSE;
  }
}