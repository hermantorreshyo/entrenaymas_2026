<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Mercadoee extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function categorias() {
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",999999);
    $sql = "SELECT id, nombre, orden, path, path_2 FROM toque_categorias WHERE id_empresa = 1284 ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      $r->path = "https://www.mercadoee.com.ar/sistema/".$r->path;
      $r->path_2 = "https://www.mercadoee.com.ar/sistema/".$r->path_2;
      $salida[] = $r;
    }
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));    
  }

  function comercios() {
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",999999);
    $id_categoria = parent::get_get("id_categoria",0);
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM com_usuarios U ";
    $sql.= "INNER JOIN com_usuarios_extension UE ON (U.id_empresa = UE.id_empresa AND U.id = UE.id_usuario) ";
    $sql.= "WHERE U.id_empresa = 1284 ";
    $sql.= "AND U.id_perfiles = 1400 ";
    if (!empty($id_categoria)) $sql.= "AND EXISTS (SELECT 1 FROM toque_categorias_usuarios CAT WHERE U.id_empresa = CAT.id_empresa AND U.id = CAT.id_usuario AND CAT.id_categoria = $id_categoria ) ";
    $sql.= "ORDER BY U.nombre ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $t = $q_total->row();

    $salida = array();
    foreach($q->result() as $usuario) {

      if ((strpos($usuario->facebook, "http") === FALSE)) $usuario->facebook = "https://www.facebook.com/".$usuario->facebook;
      if ((strpos($usuario->instagram, "http") === FALSE)) $usuario->instagram = "https://www.instagram.com/".str_replace("@", "", $usuario->instagram);

      // Link de la imagen
      $usuario->path = (!empty($usuario->path)) ? (((strpos($usuario->path,"http")===0)) ? $usuario->path : "https://www.mercadoee.com.ar/sistema/".$usuario->path) : "";

      $usuario2 = new stdClass();
      $usuario2->id = $usuario->id;
      $usuario2->nombre = $usuario->nombre;
      $usuario2->direccion = $usuario->direccion;
      $usuario2->telefono = $usuario->telefono;
      $usuario2->celular = $usuario->celular;
      $usuario2->email = $usuario->email;
      $usuario2->path = "https://www.mercadoee.com.ar/sistema/".$usuario->path;
      $usuario2->facebook = $usuario->facebook;
      $usuario2->instagram = $usuario->instagram;
      $usuario2->ciudad = $usuario->custom_1;

      $sql = "SELECT CAT.* FROM toque_categorias CAT ";
      $sql.= "INNER JOIN toque_categorias_usuarios U ON (CAT.id_empresa = U.id_empresa AND CAT.id = U.id_categoria) ";
      $sql.= "WHERE U.id_empresa = 1284 ";
      $sql.= "AND U.id_usuario = $usuario->id ";
      $qq = $this->db->query($sql);
      $usuario2->categorias = array();
      foreach($qq->result() as $cat) {
        $cc = new stdClass();
        $cc->id = $cat->id;
        $cc->nombre = $cat->nombre;
        $usuario2->categorias[] = $cc;
      }

      $salida[] = $usuario2;
    }

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$t->total,
    ));
  }

  function articulos($id_usuario = 0) {
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",999999);
    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= " A.id, A.nombre, A.codigo, A.moneda, A.precio_final AS precio_sin_dto, A.porc_bonif AS descuento, A.precio_final_dto AS precio_final, ";
    $sql.= " A.moneda, A.texto, A.path, A.link, A.fecha_mov AS fecha_modificacion, ";
    $sql.= " IF(R.nombre IS NULL,'',R.nombre) AS rubro, ";
    $sql.= " IF(M.nombre IS NULL,'',M.nombre) AS marca ";
    $sql.= "FROM articulos A ";
    $sql.= " LEFT JOIN rubros R ON (A.id_empresa = R.id_empresa AND A.id_rubro = R.id) ";
    $sql.= " LEFT JOIN marcas M ON (A.id_empresa = M.id_empresa AND A.id_marca = M.id) ";
    $sql.= "WHERE A.id_empresa = 1284 ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    $sql.= "ORDER BY A.nombre ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $t = $q_total->row();
    
    $salida = array();
    foreach($q->result() as $art) {
      $art->path = "https://www.mercadoee.com.ar/sistema/".$art->path;
      $art->link = "https://www.mercadoee.com.ar/".$art->link;
      $salida[] = $art;
    }

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$t->total,
    ));
  }

  function articulo($id = 0) {
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",999999);
    $this->load->model("Articulo_Model");
    $art = $this->Articulo_Model->get($id,1284);
    if (!empty($art)) {
      $salida = new stdClass();
      $salida->nombre = $art->nombre;
      $salida->codigo = $art->codigo;
      $salida->moneda = $art->moneda;
      $salida->precio_sin_dto = $art->precio_final;
      $salida->descuento = $art->porc_bonif;
      $salida->precio_final = $art->precio_final_dto;
      $salida->texto = $art->texto;
      $salida->path = "https://www.mercadoee.com.ar/sistema/".$art->path;
      $salida->link = "https://www.mercadoee.com.ar/".$art->link;
      $salida->fecha_modificacion = $art->fecha_mov;
      $salida->rubro = $art->rubro;
      $salida->marca = $art->marca;
      $salida->variantes = $art->variantes;
      $salida->imagenes = array();
      foreach($art->images as $img) {
        $salida->imagenes[] = "https://www.mercadoee.com.ar/sistema/".$img;
      }      
      echo json_encode(array(
        "error"=>0,
        "detail"=>$salida,
      ));
    } else {
      echo json_encode(array(
        "error"=>1,
      ));
    }
  }
}