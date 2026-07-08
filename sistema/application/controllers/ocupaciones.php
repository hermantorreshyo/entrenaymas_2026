<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Ocupaciones extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Ocupacion_Model', 'modelo',"fecha ASC",1);
    }

    function ver() {

        $id_empresa = parent::get_empresa();        
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
        $salida = array();

        // Obtenemos las habitaciones
        $this->load->model("Habitacion_Model");
        $habitaciones = $this->Habitacion_Model->get_all();
        
        // Recorremos las fechas
        $d = new DateTime($desde);
        $h = new DateTime($hasta);
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($d,$interval,$h);
        foreach($habitaciones as $hab) {

            $hab->disponibilidad = array();

            foreach($range as $fecha) {

                $disp = $this->modelo->get_disponibilidad(array(
                    "id_habitacion"=>$hab->id,
                    "fecha"=>$fecha->format("Y-m-d"),
                ));
                if ($disp === FALSE) {
                    $disp = new stdClass();
                    // Se encuentra disponible el maximo de la capacidad posible
                    // ya que sino, el campo hubiese sido editado
                    $disp->id = 0;
                    $disp->fecha = $fecha->format("Y-m-d");
                    $disp->disponible = $hab->capacidad;
                }
                $hab->disponibilidad[] = $disp;

            }

            $salida[] = $hab;
        }
        echo json_encode($salida);
    }

}