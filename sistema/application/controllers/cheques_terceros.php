<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cheques_Terceros extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Cheque_Model', 'modelo','id');
    }
    
    
    function get($id) {

        // Obtenemos todos los registros
        if ($id == "index") {
        
            $limit = $this->input->get("limit");
            $offset = $this->input->get("offset");
            $filter = $this->input->get("filter");
            $id_banco = $this->input->get("id_banco");
            $id_cliente = $this->input->get("id_cliente");
            $entregado = $this->input->get("entregado");
			if (empty($entregado)) $entregado = 0;
	    
            $lista = $this->modelo->get_all(array(
				"limit"=>$limit,
				"offset"=>$offset,
				"numero"=>$filter,
				"id_banco"=>$id_banco,
				"tipo"=>'T',
				"entregado"=>$entregado,
				"id_cliente"=>$id_cliente
			));
            if (!$lista) $lista = array();
    
            // Total de lista
            $total = $this->modelo->count_all(array(
				"id_banco"=>$id_banco,
				"tipo"=>'T',
				"numero"=>$filter,
				"entregado"=>$entregado,
				"id_cliente"=>$id_cliente
			));
            
            $suma = $this->modelo->sum_all(array(
				"id_banco"=>$id_banco,
				"tipo"=>'T',
				"numero"=>$filter,
				"entregado"=>$entregado,
				"id_cliente"=>$id_cliente
			));
            
            // Armamos la salida
            $salida = array(
                "_meta"=> array(
                    "suma" => is_null($suma) ? 0 : $suma
                ),
                "total"=> $total,
                "results"=>$lista
            );
            echo json_encode($salida);
        } else {
            // Estamos obteniendo un elemento en particular
            echo json_encode($this->modelo->get($id));
        }
    }
    
    function insert() {
        $this->load->helper("fecha_helper");
		$array = $this->parse_put();
		unset($array->banco);
		unset($array->cliente);
        $array->fecha_emision = fecha_mysql($array->fecha_emision);
        $array->fecha_cobro = fecha_mysql($array->fecha_cobro);
		if (isset($array->fecha_debitado)) $array->fecha_debitado = fecha_mysql($array->fecha_debitado);
        $array->tipo = 'T';
		$insert_id = $this->modelo->save($array);
		$salida = array("id"=>$insert_id);
		echo json_encode($salida);
    }

    function update($id) {
	
		// Si es 0, entonces lo insertamos
		if ($id == 0) { $this->insert($id); return; }
        
        $this->load->helper("fecha_helper");
		$array = $this->parse_put();
		unset($array->banco);
		unset($array->cliente);
        $array->fecha_emision = fecha_mysql($array->fecha_emision);
        $array->fecha_cobro = fecha_mysql($array->fecha_cobro);
		$array->fecha_debitado = fecha_mysql($array->fecha_debitado);
        $array->tipo = 'T';
		$this->modelo->save($array);
	        
        // TODO:
        // Si el cheque fue rechazado, debemos controlar si
        // esta en una orden de pago. Si lo está,
        // se debe generar una nota de débito del proveedor
        // automáticamente, por el monto del cheque rechazado.
        // De esa manera, la cuenta corriente queda consistente
        // Luego se generaría otro pago aparte, no se modifica
        // el pago realizado.
        
		$salida = array("id"=>$id);
		echo json_encode($salida);
    }
    
}