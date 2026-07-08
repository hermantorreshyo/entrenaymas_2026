#!/usr/bin/env php
<?php

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "lock.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
if (flock($file, LOCK_EX | LOCK_NB) === FALSE) {  // Intenta adquirir un bloqueo exclusivo
  // Si falla es porque el proceso sigue activo
  exit();
}

require_once('./websockets.php');
require_once('../../../params.php');

class echoServer extends WebSocketServer {
  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.

  private $conx = NULL;
  
  protected function process ($user, $message) {
    
  }

  protected function tick() {
    // Controlamos si hay alguna notificacion nueva
    $sql = "SELECT * FROM com_log WHERE importancia = 'N' AND leida = 0 ";
    if ($this->conx == NULL) $this->conx = get_conex();
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      while(($r=mysqli_fetch_object($q))!==NULL) {

        $enviado = FALSE;
        // Se la enviamos a los usuarios conectados
        foreach($this->users as $user) {
          // SOLO SI CORRESPONDE A ESA EMPRESA
          if ($r->id_empresa == $user->id_empresa) {
            $this->send($user,$r->texto);  
            $enviado = TRUE;
          }
        }

        // Si se envio el mensaje a algun usuario de la empresa
        if ($enviado) {
          // Actualizamos para que no se vuelva a mostrar
          mysqli_query($this->conx,"UPDATE com_log SET leida = 1 WHERE id = $r->id");          
        }
        
      }
    }
  }
  
  protected function connected ($user) {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
    echo "ID_EMPRESA: ".$user->id_empresa;
  }
  
  protected function closed ($user) {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}

$echo = new echoServer("0.0.0.0","9000");

try {
  $echo->run();
}
catch (Exception $e) {
  $echo->stdout($e->getMessage());
}
