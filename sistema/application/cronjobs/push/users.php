<?php

class WebSocketUser {

  public $socket;
  public $id;
  public $id_empresa;
  public $headers = array();
  public $handshake = false;

  public $handlingPartialPacket = false;
  public $partialBuffer = "";

  public $sendingContinuous = false;
  public $partialMessage = "";
  
  public $hasSentClose = false;

  function __construct($id, $socket, $id_empresa = 0) {
    $this->id = $id;
    $this->socket = $socket;
    $this->id_empresa = $id_empresa;
  }
}