<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Abstract_model extends CI_Model {
  
  public $tabla = '';
  public $ident = 'id';
  public $order_by = 'id';
  public $usa_id_empresa = 1;
  private $id_empresa = 0;
  
  function __construct($table = '', $column_id = 'id', $order_by = 'id', $usa_id_empresa = 1) {
    parent::__construct();
    $this->tabla = $table;
    $this->ident = $column_id;
    $this->order_by = $order_by;
    $this->usa_id_empresa = $usa_id_empresa;
  }

  function send_error($mensaje) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 '.$mensaje, true, 500);
    exit();   
  }
  
  function get_empresa() {
    return 1319;
    /*
    // Si no fue seteada a mano
    if (!empty($this->id_empresa)) {
      return $this->id_empresa;
    } else {
      // El valor esta en la session
      if (!isset($_SESSION["id_empresa"])) {
        // TODO: Se vencio la session, tenemos que enviarle el error
        return FALSE;
      } else return $_SESSION["id_empresa"];
    }
    */
  }
  
  function set_empresa($id) {
    $this->id_empresa = $id;
  }
  
  /**
   * Devuelve todos los registros de la tabla
   * @return Lista de registros
   */
  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    
    if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
    else $this->db->order_by($this->order_by);
    if ($this->usa_id_empresa == 1) {
      $id_empresa = $this->get_empresa();
      $this->db->where("id_empresa",$id_empresa);      
    }
    // Si no son NULL y tienen algun valor
    // Nota: No use empty($var) porque si $limit puede ser 0,
    // entonces empty("0") = TRUE y esta mal eso, porque tiene que paginar
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      $query = $this->db->get($this->tabla,$offset,$limit);  
    } else {
      $query = $this->db->get($this->tabla);
    }
    $result = $query->result();
    $this->db->close();
    return $result;
  }
  
  /**
   * Devuelve un registro especificado por $id
   * @param $id Int
   * @return Registro
   */
  function get($id) {
    if ($this->usa_id_empresa == 1) {
      $id_empresa = $this->get_empresa();
      $query = $this->db->get_where($this->tabla,array($this->ident=>$id,"id_empresa"=>$id_empresa));
    } else {
      $query = $this->db->get_where($this->tabla,array($this->ident=>$id));
    }
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }  
    
  
  /**
   * Devuelve la cantidad de registros de la tabla
   * @return Int
   */
  function count_all() {
    if ($this->usa_id_empresa == 1) {
      $id_empresa = $this->get_empresa();
      $this->db->where("id_empresa",$id_empresa);      
    }
    $this->db->from($this->tabla);
    $r = $this->db->count_all_results();
    $this->db->close();
    return $r;
  }
  
  
  /**
   * Actualiza la informacion del registro
   * @param $id ID del registro que se desea actualizar
   * @param $data Array asociativo con la nueva informacion
   * @return Int con la cantidad de registros afectados
   */
  function update($id,$data) {
    
    if ($this->usa_id_empresa == 1) {
      // Controlamos que estemos editando un elemento que nos pertenece como empresa
      if (isset($data->id_empresa)) {
        $q = $this->db->get_where($this->tabla,array("id_empresa"=>$data->id_empresa,$this->ident=>$id));
        if ($q->num_rows()<=0) {
          return 0;
        }
        $this->db->where("id_empresa",$data->id_empresa);
      }
    }
    $data = $this->limpiar_campos($data,$this->tabla);
    $this->db->where($this->ident,$id);
    $this->db->update($this->tabla,$data);
    $aff = $this->db->affected_rows();
    $this->db->close();
    return $aff;
  }
  
  
  /**
   * Inserta el registro
   * @param $data Array asociativo con los datos
   * @return Int que indica el ID del registro insertado o -1 en caso de error
   */
  function insert($data) {
    $data = $this->limpiar_campos($data,$this->tabla);
    $this->db->insert($this->tabla,$data);
    $id = $this->db->insert_id();
    $this->db->close();
    if (!isset($id)) return -1;
    else return $id;
  }


  /**
   * Guarda el elemento, eligiendo el metodo correspondiente
   * Si el ID es 0, lo inserta, sino lo actualiza
   * @param $data Array asociativo con los datos
   * @return 
   */
  function save($data) 
  {
    // Tomamos el atributo identificador
    $id = isset($data->{$this->ident}) ? $data->{$this->ident} : null;
        if (isset($data->undefined)) unset($data->undefined);

    // Si es nulo o cero
    if ( (is_null($id)) || ($id == 0)) {
      // Insertamos los datos, removiendo el id para que no haya problemas
      if (isset($data->{$this->ident})) unset($data->{$this->ident});
      $id = $this->insert($data);
      $this->post_save($id);
    } else {
      // Si tiene algun valor, debemos actualizarlo
      $this->update($id,$data);
      $this->post_save($id);
    }
    return $id;
  }
  
  /**
   * Esta funcion se ejecuta despues de haber guardado (tanto insert como update)
   * Por defecto, no hace nada. La idea es que pueda ser sobreescrita de ser necesario
   */
  function post_save($id) {}

  
  
  /**
   * Elimina un registro especifico
   * @param $id Int que identifica al registro
   * @return Nada
   */
  function delete($id) {
    if ($this->usa_id_empresa == 1) {
      $id_empresa = $this->get_empresa();
      $sql = "DELETE FROM ".$this->tabla." WHERE id_empresa = $id_empresa AND ".$this->ident." = $id ";
    } else {
      $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->ident." = $id ";
    }
    $this->db->query($sql);
  }


  // Remueve todos los campos de un objeto que no sean atributos de una tabla
  public function limpiar_campos($obj = null,$tabla = "") {
    if ($obj === FALSE || empty($obj) || is_null($obj)) return;
    if (empty($tabla)) return;
    $campos = $this->db->list_fields($tabla);
    foreach($obj as $key => $value) {
      if (!in_array($key, $campos)) {
        if (is_object($obj)) unset($obj->{$key});
        else if (is_array($obj)) unset($obj[$key]);
      } else {
        if ($tabla == "articulos" || $tabla == "inm_propiedades" || $tabla == "rubros" || $tabla == "usuarios") {
          // SACAMOS LAS COMILLAS
          if (is_object($obj)) {
            //$obj->{$key} = str_replace("\"", "", $obj->{$key});
            $obj->{$key} = str_replace("'", "", $obj->{$key});
          } else if (is_array($obj)) {
            //$obj[$key] = str_replace("\"", "", $obj[$key]);
            $obj[$key] = str_replace("'", "", $obj[$key]);
          }
        }
      }
    }      
    return $obj;
  }
      
}