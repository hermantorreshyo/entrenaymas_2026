<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tareas_Background extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tarea_Background_Model', 'modelo');
  }

  function insert() {}
  function update($id) {}
  function delete($id) {}
  function get() {}

  function ejecutar() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);
    date_default_timezone_set("America/Argentina/Buenos_Aires");

    // A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
    $filename = "sem_tareas_back.txt";
    if (file_exists($filename) === FALSE) file_put_contents($filename, "");
    $file = fopen($filename, "r+");
    // Intenta adquirir un bloqueo exclusivo
    if((flock($file, LOCK_EX | LOCK_NB) === FALSE)) exit();

    $ahora = date("Y-m-d H:i:s");
    $sql = "SELECT * FROM com_tareas_background ";
    $sql.= "WHERE realizada = 0 AND fecha <= '$ahora' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $tarea) {

      // Llamamos a la URL de la tarea programada
      if (!empty($tarea->url)) {
        $ch = curl_init($tarea->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
      }

      // Una vez realizada la marcamos
      $sql = "UPDATE com_tareas_background SET realizada = 1 WHERE id = $tarea->id ";
      $this->db->query($sql);
    }
  }
}