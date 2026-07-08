<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';
require_once '../models/meli.php';

class Propiedades_Meli extends REST_Controller {

  private $configuracion = null;
  private $meli = null;

  function __construct() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    parent::__construct();
    $this->load->model('Propiedad_Model', 'modelo');
  }

  function get_paquetes_publicacion_usuario() {
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $id_usuario = $this->configuracion->ml_user_id;
    $url = '/users/'.$id_usuario."/classifieds_promotion_packs";
    $response = $this->meli->get($url, $params);
    $salida = array();
    $encontro = false;
    if ($response["httpCode"] == 200) {
      foreach($response["body"] as $paquete) {
        if ($paquete->status == "active") {
          $encontro = true;
          foreach($paquete->listing_details as $tipo) {
            $salida[] = array(
              "id"=>$tipo->listing_type_id,
              "nombre"=>$paquete->description." (Usadas: ".$tipo->used_listings.")",
            );
          }
        }
      }
      if ($encontro) {
        echo json_encode($salida);
        exit();
      }
    }
    echo json_encode(array());
  }

  function pausar() {
    $this->connect();
    $item_id = parent::get_post("id_meli","");
    if (empty($item_id)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "status"=>"paused",
    );
    $response = $this->meli->put('/items/'.$item_id, $body, $params);
    if ($response["httpCode"] == 200) {

      $sql = "UPDATE inm_propiedades_meli SET activo_meli = 0, status = 'paused' WHERE id_meli = '$item_id' ";
      $this->db->query($sql);
      echo json_encode(array(
        "error"=>0,
      ));
      exit();

    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      if (isset($body->cause)) {
        $cause = $body->cause[0];
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>(isset($cause->message) ? $cause->message : $cause),
        ));              
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>print_r($body,TRUE),
        ));                      
      }
      exit();
    }
  }

  function pausar_multiple() {

    $this->connect();
    $ids = parent::get_post("ids","");
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "status"=>"paused",
    );
    $errores = "";
    $ids_array = explode(",",$ids);
    foreach($ids_array as $id) {
      if (empty($id)) continue;
      $art = $this->modelo->get($id);
      if ($art === FALSE) continue;
      if (!isset($art->id_meli) || !isset($art->status)) continue;
      if (empty($art->id_meli)) continue;
      if ($art->status != "active") continue;

      $response = $this->meli->put('/items/'.$art->id_meli, $body, $params);
      if ($response["httpCode"] == 200) {
        $sql = "UPDATE inm_propiedades_meli SET activo_meli = 0, status = 'paused' WHERE id_meli = '$art->id_meli' ";
        $this->db->query($sql);
      } else if ($response["httpCode"] >= 400) {
        $errores.= "No se pudo pausar $art->nombre.\n";
      }
    }
    if (!empty($errores)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$errores,
      ));
    } else {
      echo json_encode(array(
        "error"=>0,
      ));
    }
  }


  // Cambia el estado de PAUSED a ACTIVE nuevamente
  function reactivar() {

    $this->connect();
    $item_id = parent::get_post("id_meli","");
    if (empty($item_id)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "status"=>"active",
    );
    $response = $this->meli->put('/items/'.$item_id, $body, $params);
    if ($response["httpCode"] == 200) {

      $sql = "UPDATE inm_propiedades_meli SET activo_meli = 1, status = 'active' WHERE id_meli = '$item_id' ";
      $this->db->query($sql);
      echo json_encode(array(
        "error"=>0,
      ));
      exit();

    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      if (isset($body->cause)) {
        $cause = $body->cause[0];
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>(isset($cause->message) ? $cause->message : $cause),
        ));              
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>print_r($body,TRUE),
        ));                      
      }
      exit();
    }
  }

  // FINALIZA UNA PUBLICACION
  function finalizar() {

    $this->connect();
    $item_id = parent::get_post("id_meli","");
    if (empty($item_id)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "status"=>"closed",
    );
    $response = $this->meli->put('/items/'.$item_id, $body, $params);
    if ($response["httpCode"] == 200) {

      $sql = "UPDATE inm_propiedades_meli SET status = 'closed' WHERE id_meli = '$item_id' ";
      $this->db->query($sql);
      echo json_encode(array(
        "error"=>0,
      ));
      exit();

    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      if (isset($body->cause)) {
        $cause = $body->cause[0];
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>(isset($cause->message) ? $cause->message : $cause),
        ));              
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>print_r($body,TRUE),
        ));                      
      }
      exit();
    }    
  }

  // Elimina una publicacion. Tiene que estar en estado CLOSED para eliminarla
  function eliminar() {

    $this->connect();
    $item_id = parent::get_post("id_meli","");
    if (empty($item_id)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }

    // Consultamos si el estado es CLOSED antes de eliminar
    $sql = "SELECT * FROM inm_propiedades_meli WHERE id_meli = '$item_id' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: No se encuentra la propiedad con ID: '$item_id'",
      ));
      exit();
    }
    $propiedad = $q->row();
    if ($propiedad->status != "closed") {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: La pubicacion no esta finalizada.",
      ));
      exit();      
    }

    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "deleted"=>"true",
    );
    $response = $this->meli->put('/items/'.$item_id, $body, $params);
    if ($response["httpCode"] == 200) {

      $sql = "DELETE FROM inm_propiedades_meli WHERE id_meli = '$item_id' ";
      $this->db->query($sql);
      echo json_encode(array(
        "error"=>0,
      ));
      exit();

    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      if (isset($body->cause)) {
        $cause = $body->cause[0];
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>(isset($cause->message) ? $cause->message : $cause),
        ));              
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>print_r($body,TRUE),
        ));                      
      }
      exit();
    }    
  }

  function connect() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Web_Configuracion_Model");
    $this->configuracion = $this->Web_Configuracion_Model->get($id_empresa);

    $this->meli = new Meli(ML_APP_ID, ML_APP_SECRET, $this->configuracion->ml_access_token, $this->configuracion->ml_refresh_token);

    // Debemos controlar si el access token sigue siendo valido
    if($this->configuracion->ml_expires_in < time()) {
      try {
        // Refrescamos el access token
        $refresh = $this->meli->refreshAccessToken();
        if (isset($refresh["error"])) {
          parent::send_error($refresh["error"]);
          return;
        }
        $this->configuracion->ml_access_token = $refresh['body']->access_token;
        $this->configuracion->expires_in = time() + $refresh['body']->expires_in;
        $this->configuracion->refresh_token = $refresh['body']->refresh_token;
        $this->guardar_tokens(array(
          "access_token"=>$this->configuracion->ml_access_token,
          "expires_in"=>$this->configuracion->expires_in,
          "refresh_token"=>$this->configuracion->refresh_token,
          "id_empresa"=>$id_empresa,
        ));
      } catch (Exception $e) {
        parent::send_error($e->getMessage());
        return;
      }
    }
  }
  
  function guardar_tokens($array=array()) {
    // Guarda los tokens en la base de datos
    $access_token = $array["access_token"];
    $refresh_token = $array["refresh_token"];
    $expires_in = $array["expires_in"];
    $id_empresa = $array["id_empresa"];
    $sql = "UPDATE web_configuracion SET ";
    $sql.= " ml_access_token = '$access_token', ";
    $sql.= " ml_refresh_token = '$refresh_token', ";
    $sql.= " ml_expires_in = '$expires_in' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $this->db->query($sql);
  }

  function predecir_categoria() {
    $titulo = parent::get_post("titulo","");
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    // Predecimos la categoria en la cual vamos a poner el producto
    $response = $this->meli->get('/sites/MLA/category_predictor/predict?title='.urlencode($titulo), $params);
    if (isset($response["body"])) {
      $body = $response["body"];
      $salida = $this->armar_categorias($body->id);
      echo json_encode($salida);
      return;
    }
  }

  function get_categorias($id_categoria) {
    $this->connect();
    $salida = $this->armar_categorias($id_categoria);
    echo json_encode($salida);
    return;
  }

  // Crea un array con todas las categorias
  function armar_categorias($id_categoria) {

    $params = array('access_token' => $this->configuracion->ml_access_token);
    $salida = array(
      "id"=>"",
      "categorias"=>array(),
    );
    // Ponemos las categorias generales al principio del array
    $response = $this->meli->get('/sites/MLA/categories', $params);
    if (isset($response["body"])) {
      $salida["categorias"][] = array(
        "selected"=>"",
        "children"=>$response["body"],
      );
    }

    $response = $this->meli->get('categories/'.$id_categoria, $params);
    if (isset($response["body"])) {
      $body = $response["body"];
      $salida["id"] = $body->id;

      // Si tiene el path a la raiz
      if (isset($body->path_from_root)) {
        $i = 1;
        foreach($body->path_from_root as $cat) {
          $r = $this->meli->get('categories/'.$cat->id, $params);
          if (isset($r["body"])) {
            $cat_res = $r["body"];
            if (!empty($cat_res->children_categories)) {
              $salida["categorias"][$i-1]["selected"] = $cat->id;
              $salida["categorias"][] = array(
                "children"=>$cat_res->children_categories,
              );
              $i++;
            } else {
              // Si tiene categoria anterior, le ponemos como selected el ID original
              if (isset($salida["categorias"][$i-1])) {
                $salida["categorias"][$i-1]["selected"] = $body->id;
              }
            }
          }
        }
      }
    }
    return $salida;
  }

  // Devuelve las categorias hijas de una categoria especifica
  // Utilizada para ir armando los selects de categorias hijas
  function get_categorias_hijas() {
    $id_categoria = parent::get_post("id_categoria","");
    if (empty($id_categoria)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Por favor ingrese una categoria",
      ));
      return;
    }
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $response = $this->meli->get('categories/'.$id_categoria, $params);
    $salida = array(
      "id"=>$id_categoria,
      "children"=>array(),
    );
    if (isset($response["body"])) {
      $body = $response["body"];
      if (!empty($body->children_categories)) {
        $salida["children"] = $body->children_categories;
      }
    }
    echo json_encode($salida);
  }

  /*
  function get_categorias() {
    $id_categoria = parent::get_post("id_categoria",0);
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    if ($id_categoria === 0) {
      // Obtenemos las categorias padres
      $response = $this->meli->get('/sites/MLA/categories', $params);
    } else {
      // Obtenemos los hijos de una categoria
      $response = $this->meli->get('categories/'.$id_categoria, $params);
    }
    echo json_encode($response);    
  }
  */

  function publicar() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->connect();
    $id_propiedad = parent::get_post("id_propiedad",0);

    $propiedad = $this->modelo->get($id_propiedad);
    $body = $this->preparar_publicar_meli($propiedad);
    if (isset($body["error"])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$body["mensaje"],
      ));
      exit();
    }
    $response = $this->validar_meli($body);    
    if ($response["error"] == 1) {
      echo json_encode($response);
      exit();
    }
    $salida = $this->publicar_meli($body,$propiedad);
    echo json_encode($salida);
  }

  function publicar_multiple() {

    $this->connect();
    $ids = parent::get_post("ids","");
    $categoria_meli = parent::get_post("categoria_meli","");
    $list_type_id = parent::get_post("list_type_id","");
    $images_meli = parent::get_post("images_meli","");

    // Recorremos todos los IDS
    $para_publicar = array();
    $ids_array = explode(",",$ids);
    foreach($ids_array as $id) {
      if (empty($id)) continue;
      $art = $this->modelo->get($id);
      if ($art === FALSE) continue;

      // Solamente publicamos aquellos productos que no estan activos (no existe otra publicacion activa)
      if ($art->status != "active") {

        // Tomamos la configuracion enviada
        $art->categoria_meli = $categoria_meli;
        $art->list_type_id = $list_type_id;

        // Tomamos las nuevas imagenes cargadas
        if (!empty($images_meli)) {

          if (strpos($images_meli, ";;;")) $images_meli_array = explode(";;;",$images_meli);
          else $images_meli_array = array($images_meli);

          $k = 0;
          foreach($images_meli_array as $img_meli) {
            $sql = "INSERT INTO propiedades_images_meli (id_empresa,id_propiedad,path,orden) VALUES (";
            $sql.= " '$art->id_empresa', '$art->id', '$img_meli', '$k' )";
            $this->db->query($sql);
            $k++;
          }
          $art->images_meli = array_merge($art->images_meli,$images_meli_array);
        }

        // La guardamos en la base de datos
        $guardado = $this->modelo->update_meli($art);
        if ($guardado) {
          $body = $this->preparar_publicar_meli($art);
          $para_publicar[] = array(
            "propiedad"=>$art,
            "body"=>$body,
          );          
        }
      }
    }

    // Recorremos el array y controlamos que no haya ningun error
    $hay_error = FALSE;
    $mensaje_error = "";
    foreach($para_publicar as $pp) {
      if (isset($pp["body"]["error"]) && $pp["body"]["error"] == 1) {
        $hay_error = TRUE;
        $mensaje_error = "Error en ".$pp["propiedad"]->nombre.": ".$pp["body"]["mensaje"];
      }
    }
    if ($hay_error) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$mensaje_error,
      ));
      exit();
    } else {
      foreach($para_publicar as $pp) {
        $resp = $this->publicar_meli($pp["body"],$pp["propiedad"]);
      }
      echo json_encode(array(
        "error"=>0,
      ));
      exit();
    }
  }

  // Arma el objeto que se va a enviar a MercadoLibre
  private function preparar_publicar_meli($propiedad) {

    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);

    $this->load->model("Localidad_Model");
    $localidad = $this->Localidad_Model->get($propiedad->id_localidad);

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($propiedad->id_empresa);

    // TODO: Deberiamos poder configurar si los datos de contado de la propiedad salen
    // con los de la empresa, o con los del usuario asignado

    $atributos = array(
      array("id"=>"ROOMS","value_name"=>$propiedad->ambientes),
      array("id"=>"TOTAL_AREA","value_name"=>$propiedad->superficie_total." m²"),
      array("id"=>"COVERED_AREA","value_name"=>$propiedad->superficie_cubierta." m²"),
      array("id"=>"BEDROOMS","value_name"=>$propiedad->dormitorios),
      array("id"=>"FULL_BATHROOMS","value_name"=>$propiedad->banios),
      array("id"=>"PARKING_LOTS","value_name"=>$propiedad->cocheras),
    );

    // Si es un terreno, tenemos que agregar la propiedad LAND_ACCESS
    if ($propiedad->id_tipo_inmueble == 7) {
      $tipo_terreno = "Otro"; // Otro
      if ($propiedad->tipo_calle == 0) {
        return array(
          "error"=>1,
          "mensaje"=>"Error: Ingrese un tipo de acceso al terreno.",
        );
      } else if ($propiedad->tipo_calle == 1) {
        // Asfalto
        $tipo_terreno = "Asfalto";
      } else if ($propiedad->tipo_calle == 2) {
        // Tierra
        $tipo_terreno = "Tierra";        
      } else if ($propiedad->tipo_calle == 3) {
        // Arena
        $tipo_terreno = "Arena";        
      } else if ($propiedad->tipo_calle == 4) {
        // Ripio
        $tipo_terreno = "Ripio";        
      }
      $atributos[] = array("id"=>"LAND_ACCESS","value_name"=>$tipo_terreno);
    }

    // Enviamos la informacion a MercadoLibre
    $body = array(
      "title"=>$propiedad->titulo_meli,
      "seller_custom_field"=>$propiedad->codigo,
      "category_id"=>$propiedad->categoria_meli,
      "price"=>$propiedad->precio_meli,
      "currency_id"=>(($propiedad->moneda == "$") ? "ARS" : "USD"),
      "available_quantity"=>1,
      "buying_mode"=>"classified",
      "listing_type_id"=>$propiedad->list_type_id,
      "condition"=>"not_specified",
      "attributes"=>$atributos,
      "seller_contact"=>array(
        "contact"=>$empresa->nombre,
        "phone"=>$empresa->telefono,
        "email"=>$empresa->email,
      ),
      "location"=>array(
        "city"=>array(
          "id"=>$localidad->mercadolibre_id,
        ),
        "address_line"=>$propiedad->calle." ".$propiedad->altura,
        "latitude"=>$propiedad->latitud,
        "longitude"=>$propiedad->longitud,
      ),
      "description"=> array(
        "plain_text"=>html_entity_decode($propiedad->texto_meli,ENT_QUOTES),
      ),
    );
    if (!empty($localidad->mercadolibre_barrio_id)) {
      $body["location"]["neighborhood"] = array(
        "id"=>$localidad->mercadolibre_barrio_id,
      );
    }

    // Imagenes
    $body["pictures"] = array();
    // Imagen principal
    $body["pictures"][] = array(
      "source"=>"https://www.varcreative.com/sistema/".$propiedad->path,
    );

    foreach($propiedad->images as $img) {
      $body["pictures"][] = array(
        "source"=>"https://www.varcreative.com/sistema/".$img,
      );
    }
    foreach($propiedad->images_meli as $img) {
      $body["pictures"][] = array(
        "source"=>"https://www.varcreative.com/sistema/".$img,
      );
    }
    if (empty($body["pictures"])) {
      return array(
        "error"=>1,
        "mensaje"=>"El producto debe tener al menos una imagen habilitada para MercadoLibre",
      );
    }
    return $body;
  }

  // Validamos la propiedad que estamos por enviar
  private function validar_meli($body) {
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $response = $this->meli->post('/items/validate', $body, $params);
    if ($response["httpCode"] == 204) {
      return array(
        "error"=>0,
      );
    } else if ($response["httpCode"] >= 400) {
      $body_ant = $body;
      $body = $response["body"];
      file_put_contents("compartir_propiedades_error.txt", "\n\n".date("Y-m-d H:i:s")."BODY: \n".print_r($body_ant,TRUE)."\n".print_r($response,TRUE), FILE_APPEND);

      if (isset($body->error)) {
        $mensaje = isset($body->message) ? $body->message : "";
        if (isset($body->cause) && is_array($body->cause)) {
          $cause = $body->cause[0];
          if (is_object($cause)) {
            if ($cause->code == "item.listing_type_id.unavailable") {
              $mensaje = "ERROR: Ha llegado al limite de publicaciones para esa categoria.";
            } else {
              $mensaje = $cause->message;
            }
          } else {
            if ($mensaje == "seller.unable_to_list" && $cause == "has_debt") {
              $mensaje = "El usuario no puede publicar debido a una deuda impaga.";
            } else if ($mensaje == "seller.unable_to_list" && $cause == "address_pending") {
              $mensaje = "Es necesario completar la direccion en el perfil de usuario de MercadoLibre";
            } else if ($mensaje == "seller.unable_to_list" && $cause == "phone_pending") {
              $mensaje = "Es necesario completar el telefono en el perfil de usuario de MercadoLibre";
            } else if ($mensaje == "seller.unable_to_list") {
              $mensaje = "Falta completar algun dato en el perfil de usuario de MercadoLibre para poder publicar.";
            }
          }
        }
        return array(
          "error"=>1,
          "mensaje"=>$mensaje,
        );
      } else {
        return array(
          "error"=>1,
          "mensaje"=>print_r($body,TRUE),
        );                      
      }
    }
  }

  private function publicar_meli($body,$propiedad) {
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);

    // Hay que actualizar
    if (!empty($propiedad->id_meli)) {

      $body2 = array();
      $body2["title"] = $body["title"];
      $body2["seller_contact"]["contact"] = $body["seller_contact"]["contact"];
      $body2["seller_contact"]["phone"] = $body["seller_contact"]["phone"];
      $body2["seller_contact"]["email"] = $body["seller_contact"]["email"];
      $body2["category_id"] = $body["category_id"];
      $body2["price"] = $body["price"];
      $body2["pictures"] = $body["pictures"];
      $response = $this->meli->put("/items/".$propiedad->id_meli, $body2, $params);
      if ($response["httpCode"] == 200) {
        $res = $response["body"];
        return array(
          "error"=>0,
          "link"=>$res->permalink,
        );
      } else if ($response["httpCode"] >= 400){
        // Ocurrio un error, lo enviamos
        $body = $response["body"];
        return array(
          "error"=>1,
          "mensaje"=>$body->message,
        ); 
      }

    // Hay que publicar un nuevo propiedad
    } else {
      $response = $this->meli->post('/items', $body, $params);
      if ($response["httpCode"] == 201) {
        $res = $response["body"];

        // Actualizamos los datos de la propiedad en la base de datos
        $this->db->where("id_propiedad",$propiedad->id);
        $this->db->where("id_empresa",$propiedad->id_empresa);
        $this->db->update("inm_propiedades_meli",array(
          "id_meli"=>$res->id,
          "permalink"=>$res->permalink,
          "activo_meli"=>1,
          "fecha_publicacion"=>date("Y-m-d H:i:s"),
        ));
        return array(
          "error"=>0,
          "link"=>$res->permalink,
        );

      } else if ($response["httpCode"] == 400){

        // Ocurrio un error, lo enviamos
        $body = $response["body"];
        return array(
          "error"=>1,
          "mensaje"=>$body->message,
        );      
      }
    }

  }

}