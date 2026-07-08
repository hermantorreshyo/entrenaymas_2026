<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Curso_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("cursos","id","nombre ASC");
  }

  function get($id,$config = array()) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $sql = "SELECT A.*, ";
    $sql.= " IF(A.fecha = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%m')) AS fecha, ";
    $sql.= " IF(AU.nombre IS NULL,'',AU.nombre) AS autor, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
    $sql.= "FROM cursos A ";
    $sql.= "LEFT JOIN cursos_categorias C ON (A.id_empresa = C.id_empresa AND A.id_categoria = C.id) ";
    $sql.= "LEFT JOIN cursos_autores AU ON (A.id_empresa = AU.id_empresa AND A.id_autor = AU.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id = $id ";
    $q = $this->db->query($sql);
    $curso = $q->row();
    if (empty($curso)) return FALSE;

    // Obtenemos las imagenes de ese entrada
    $sql = "SELECT AI.* FROM cursos_images AI WHERE AI.id_curso = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $curso->images = array();
    foreach($q->result() as $r) {
      $curso->images[] = $r->path;
    }    

    $sql = "SELECT E.nombre ";
    $sql.= " FROM cursos_etiquetas_relacion EE INNER JOIN cursos_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
    $sql.= "WHERE EE.id_curso = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
    $q = $this->db->query($sql);
    $curso->etiquetas = array();
    foreach($q->result() as $r) {
      $curso->etiquetas[] = (html_entity_decode($r->nombre));
    }

    $sql = "SELECT CU.*, C.nombre ";
    $sql.= " FROM cursos_usuarios CU INNER JOIN clientes C ON (CU.id_empresa = C.id_empresa AND CU.id_usuario = C.id) ";
    $sql.= "WHERE CU.id_curso = $id AND CU.id_empresa = $id_empresa ORDER BY C.nombre ASC";
    $q = $this->db->query($sql);
    $curso->usuarios = array();
    foreach($q->result() as $r) {
      $curso->usuarios[] = $r;
    }

    $curso->clases = array();
    $sql = "SELECT * ";
    $sql.= "FROM cursos_clases ";
    $sql.= "WHERE id_curso = $id AND id_empresa = $curso->id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $clase) {

      // Obtenemos las preguntas
      $sql = "SELECT * FROM cursos_clases_preguntas ";
      $sql.= "WHERE id_clase = $clase->id AND id_empresa = $curso->id_empresa ";
      $sql.= "ORDER BY orden ASC ";
      $qq = $this->db->query($sql);
      $clase->preguntas = array();
      foreach($qq->result() as $pregunta) {

        $sql = "SELECT * FROM cursos_clases_respuestas ";
        $sql.= "WHERE id_pregunta = $pregunta->id AND id_empresa = $curso->id_empresa ";
        $sql.= "ORDER BY orden ASC ";
        $qqq = $this->db->query($sql);
        $pregunta->respuestas = array();
        foreach($qqq->result() as $respuesta) {
          $pregunta->respuestas[] = $respuesta;
        }

        $clase->preguntas[] = $pregunta;
      }
      $curso->clases[] = $clase;
    }
    return $curso;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM cursos_clases WHERE id_curso = $id AND id_empresa = $id_empresa ");
    $this->db->query("DELETE FROM cursos_images WHERE id_curso = $id AND id_empresa = $id_empresa  ");
    $this->db->query("DELETE FROM cursos_usuarios WHERE id_curso = $id AND id_empresa = $id_empresa  ");
    $this->db->query("DELETE FROM cursos_etiquetas_relacion WHERE id_curso = $id AND id_empresa = $id_empresa  ");
    parent::delete($id);
  }

  function save($data) {
    $this->load->helper("file_helper");
    $this->load->helper("fecha_helper");
    $clases = $data->clases;
    $etiquetas = $data->etiquetas;
    $images = $data->images;
    $usuarios = $data->usuarios;
    $data->fecha = fecha_mysql($data->fecha);
    $id = parent::save($data);

    $this->load->model("Empresa_Model");
    foreach($clases as $p) {

      if (isset($p->eliminado) && $p->eliminado == 1) {
        
        // ELIMINAMOS LA CLASE
        $this->db->query("DELETE FROM cursos_clases_preguntas WHERE id_clase = $p->id AND id_empresa = $data->id_empresa ");
        $this->db->query("DELETE FROM cursos_clases WHERE id = $p->id AND id_curso = $id AND id_empresa = $data->id_empresa ");

      } else if (isset($p->insertado) && $p->insertado == 1) {

        // INSERTAMOS LA NUEVA CLASE
        $this->db->insert("cursos_clases",array(
          "id_empresa"=>$p->id_empresa,
          "id_curso"=>$id,
          "path_clase"=>$p->path_clase,
          "video"=>$p->video,
          "texto"=>$p->texto,
          "audio"=>$p->audio,
          "custom_1"=>$p->custom_1,
          "custom_2"=>$p->custom_2,
          "custom_3"=>$p->custom_3,
          "custom_4"=>$p->custom_4,
          "custom_5"=>$p->custom_5,
          "custom_6"=>$p->custom_6,
          "custom_7"=>$p->custom_7,
          "custom_8"=>$p->custom_8,
          "custom_9"=>$p->custom_9,
          "custom_10"=>$p->custom_10,
          "nombre"=>$p->nombre,
          "orden"=>$p->orden,
          "respuestas_correctas"=>$p->respuestas_correctas,
        ));
        $i++;
        $p->id = $this->db->insert_id();      

      } else {

        // ACTUALIZAMOS LOS DATOS DE LA CALSE
        $this->db->where("id",$p->id);
        $this->db->where("id_empresa",$p->id_empresa);
        $this->db->update("cursos_clases",array(
          "id_empresa"=>$p->id_empresa,
          "id_curso"=>$id,
          "path_clase"=>$p->path_clase,
          "video"=>$p->video,
          "texto"=>$p->texto,
          "audio"=>$p->audio,
          "custom_1"=>$p->custom_1,
          "custom_2"=>$p->custom_2,
          "custom_3"=>$p->custom_3,
          "custom_4"=>$p->custom_4,
          "custom_5"=>$p->custom_5,
          "custom_6"=>$p->custom_6,
          "custom_7"=>$p->custom_7,
          "custom_8"=>$p->custom_8,
          "custom_9"=>$p->custom_9,
          "custom_10"=>$p->custom_10,
          "nombre"=>$p->nombre,
          "orden"=>$p->orden,
          "respuestas_correctas"=>$p->respuestas_correctas,
        ));
      }

      // Guardamos las preguntas
      $k=0;
      foreach($p->preguntas as $preg) {
        $preg->id_clase = $p->id;
        $preg->id_empresa = $data->id_empresa;
        $preg->orden = $k;
        $this->save_pregunta($preg);
        $k++;
      }      
    }

    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM cursos_etiquetas_relacion WHERE id_curso = $id AND id_empresa = $data->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $data->id_empresa;
      $tag->id_curso = $id;
      $tag->nombre = $e;
      $this->save_tag($tag);
    } 

    $k=0;
    $this->db->query("DELETE FROM cursos_usuarios WHERE id_curso = $id AND id_empresa = $data->id_empresa");
    foreach($usuarios as $im) {
      $this->db->query("INSERT INTO cursos_usuarios (id_empresa,id_curso,id_usuario) VALUES($data->id_empresa,$id,'$im->id_usuario')");
      $k++;
    }    

    $k=0;
    $this->db->query("DELETE FROM cursos_images WHERE id_curso = $id AND id_empresa = $data->id_empresa");
    foreach($images as $im) {
      $this->db->query("INSERT INTO cursos_images (id_empresa,id_curso,path,orden) VALUES($data->id_empresa,$id,'$im',$k)");
      $k++;
    }
    $this->crear_link(array(
      "id"=>$id,
      "id_empresa"=>$data->id_empresa,
      "id_categoria"=>$data->id_categoria,
      "nombre"=>$data->nombre,
      "agregar_id"=>($this->Empresa_Model->es_milling($data->id_empresa) ? 0 : 1),
    ));

    // PARALELAMENTE GUARDAMOS UN ARTICULO CON EL MISMO ID, DE ESTA MANERA UTILIZAMOS EL CARRITO COMO UNA VENTA NORMAL
    $this->load->model("Articulo_Model");
    $this->db->query("DELETE FROM articulos WHERE id_empresa = $data->id_empresa AND id = $id ");
    $articulo = new stdClass();
    $articulo->id = $id;
    $articulo->codigo = $id;
    $articulo->nombre = $data->nombre;
    $articulo->path = $data->path;
    $articulo->id_empresa = $data->id_empresa;
    $articulo->moneda = $data->moneda;
    $articulo->precio_final = $data->precio_final;
    $articulo->porc_bonif = $data->porc_bonif;
    $articulo->precio_final_dto = $data->precio_final_dto;
    $articulo->lista_precios = 2;
    $articulo->fecha_mov = date("Y-m-d");
    $articulo->fecha_ingreso = date("Y-m-d");
    $this->Articulo_Model->insert($articulo);

    return $id;
  }

  function crear_link($config = array()) {

    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $nombre = isset($config["nombre"]) ? $config["nombre"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $agregar_id = isset($config["agregar_id"]) ? $config["agregar_id"] : 1;

    // Tomamos el full link de la categoria
    $this->load->model("Curso_Categoria_Model");
    $s = $this->Curso_Categoria_Model->full_link($id_categoria);
    
    // Si la empresa tiene una configuracion especial "base_curso", la tomamos
    // sino, la base comun de los cursos es cursos/
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $link = ((isset($empresa->config["link_base_curso"])) ? $empresa->config["link_base_curso"] : "cursos")."/";

    // Formamos la url del estilo:
    // cursos/categoria/subcategoria/nombre-del-curso-id/
    $link.= (!empty($s["full_link"])) ? $s["full_link"]."/" : "";
    $link.= filename($nombre,"-",0).(($agregar_id == 1) ? "-".$id : "")."/";
    $this->db->query("UPDATE cursos SET link = '$link' WHERE id = $id AND id_empresa = $id_empresa");
  }

  function save_pregunta($pregunta) {

    // Si se elimino la pregunta, tenemos que borrarla y a sus respuestas
    if (isset($pregunta->eliminado) && $pregunta->eliminado == 1) {
      if (isset($pregunta->id)) {
        $this->db->query("DELETE FROM cursos_clases_respuestas WHERE id_empresa = $pregunta->id_empresa AND id_pregunta = $pregunta->id ");
        $this->db->query("DELETE FROM cursos_clases_preguntas WHERE id_empresa = $pregunta->id_empresa AND id = $pregunta->id ");
      }
      return;
    } else if (isset($pregunta->insertado) && $pregunta->insertado == 1) {
      // Es una pregunta nueva, la insertamos
      $this->db->query("INSERT INTO cursos_clases_preguntas (id_empresa, id_clase, pregunta, orden) VALUES ($pregunta->id_empresa, $pregunta->id_clase, '$pregunta->pregunta', '$pregunta->orden') ");
      $pregunta->id = $this->db->insert_id();
      // Recorremos las respuestas, todas son nuevas
      $k=0;
      foreach($pregunta->respuestas as $respuesta) {
        $sql = "INSERT INTO cursos_clases_respuestas (id_empresa, id_pregunta, orden, respuesta, correcta) VALUES (";
        $sql.= " $pregunta->id_empresa, $pregunta->id, $k, '$respuesta->respuesta', '$respuesta->correcta' )";
        $this->db->query($sql);
        $k++;
      }

    } else {

      // Actualizamos la pregunta
      $sql = "UPDATE cursos_clases_preguntas SET ";
      $sql.= " pregunta = '$pregunta->pregunta', ";
      $sql.= " orden = '$pregunta->orden' ";
      $sql.= "WHERE id_empresa = '$pregunta->id_empresa' ";
      $sql.= "AND id_clase = $pregunta->id_clase ";
      $sql.= "AND id = $pregunta->id ";
      $this->db->query($sql);

      // Puede que las respuestas hayan cambiado, por lo tanto hacemos las operaciones correspondientes
      $k=0;
      foreach($pregunta->respuestas as $respuesta) {

        if ($respuesta->eliminado == 1) {
          $sql = "DELETE FROM cursos_clases_respuestas ";
          $sql.= "WHERE id_empresa = $pregunta->id_empresa ";
          $sql.= "AND id = $respuesta->id ";
        } else if ($respuesta->insertado == 1) {
          $sql = "INSERT INTO cursos_clases_respuestas (id_empresa, id_pregunta, orden, respuesta, correcta) VALUES ($pregunta->id_empresa, $pregunta->id, $k, '$respuesta->respuesta', '$respuesta->correcta') ";
        } else {
          $sql = "UPDATE cursos_clases_respuestas SET ";
          $sql.= " respuesta = '$respuesta->respuesta', ";
          $sql.= " orden = '$k', ";
          $sql.= " correcta = '$respuesta->correcta' ";
          $sql.= "WHERE id_empresa = $pregunta->id_empresa ";
          $sql.= "AND id = $respuesta->id ";
        }
        $this->db->query($sql);
        $k++;
      }
    }
  }

  function save_tag($tag) {
    $this->load->helper("file_helper");
    // Primero controlamos si existe la etiqueta
    $q = $this->db->query("SELECT * FROM cursos_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
    if ($q->num_rows()<=0) {
      // Si no existe, la guardamos
      $link = filename($tag->nombre,"-",0);
      $this->db->query("INSERT INTO cursos_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
      $id_etiqueta = $this->db->insert_id();
    } else {
      $row = $q->row();
      $id_etiqueta = $row->id;
    }
    $this->db->query("INSERT INTO cursos_etiquetas_relacion (id_empresa,id_curso,id_etiqueta) VALUES ($tag->id_empresa,$tag->id_curso,$id_etiqueta) ");
  }  

  function buscar($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $order = isset($conf["order"]) ? $conf["order"] : "ASC";
    if (empty($order)) $order = "ASC";
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "A.nombre";
    if (empty($order_by)) $order_by = "A.nombre";
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(A.fecha = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%m')) AS fecha, ";
    $sql.= " IF(AU.nombre IS NULL,'',AU.nombre) AS autor, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
    $sql.= "FROM cursos A ";
    $sql.= "LEFT JOIN cursos_categorias C ON (A.id_empresa = C.id_empresa AND A.id_categoria = C.id) ";
    $sql.= "LEFT JOIN cursos_autores AU ON (A.id_empresa = AU.id_empresa AND A.id_autor = AU.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );    
  }

}