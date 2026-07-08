<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';
require_once '../models/meli.php';

class Articulos_Meli extends REST_Controller {

  private $configuracion = null;
  private $meli = null;

  function __construct() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    parent::__construct();
    $this->load->model('Articulo_Model', 'modelo');
  }

  function publicar_md() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id_empresa = 699;
    $errores = array();
    $ids = array(10283707,10283704,10283705,10289898,10282833,10296349,10283824,10280961,10280960,10283810,10252700,10240290,10239776,10239965,10267775,10283839,10283794,10283821,10240040,10283715,10284474,10239963,10252701,10264294,10240247,10280816,10280797,10280795,10280792,10280789,10280778,10280777,10280774,10280773,10280772,10280771,10280769,10280770,10280768,10280767,10280756,10272180,10272181,10272179,10271258,10271262,10272170,10272178,10271260,10271259,10268882,10268880,10268878,10268813,10268823,10268812,10268811,10268809,10268805,10268807,10268804,10268715,10268678,10269370,10272168,10266871,10267496,10267507,10267510,10267511,10267772,10267774,10267773,10268199,10265568,10265502,10265499,10264917,10255313,10255295,10267508,10255300,10240041,10240053,10240060,10254701,10254700,10254699,10254511,10280198,10252699,10252684,10252680,10252681,10252679,10252677,10252676,10252675,10252669,10252558,10252289,10252080,10251686,10251619,10251618,10251617,10251615,10251612,10251611,10251610,10251609,10251607,10251608,10251606,10251605,10251603,10251602,10251600,10251601,10251596,10251594,10251595,10251593,10251592,10251589,10251588,10251587,10251585,10251584,10251578,10251577,10251575,10251562,10251561,10251560,10251557,10251556,10251554,10251552,10251549,10251550,10251544,10251542,10251541,10251540,10251539,10251537,10251535,10251533,10251532,10251529,10250807,10250803,10250798,10250797,10250782,10250771,10250685,10272184,10249994,10249988,10250016,10249933,10249930,10249739,10247567,10247565,10247566,10247561,10247560,10247546,10247547,10247540,10247538,10247536,10247524,10247522,10247521,10247518,10247515,10249146,10247510,10247497,10247495,10247494,10247493,10247486,10247485,10247484,10247469,10247468,10247464,10247462,10247461,10247460,10247459,10247454,10247436,10247433,10247434,10247432,10247431,10247430,10247429,10247428,10247427,10247426,10247425,10247424,10247423,10247419,10247418,10247417,10247416,10247414,10247411,10247408,10247407,10247405,10247404,10247403,10247402,10247388,10247382,10247371,10247368,10247367,10247365,10247364,10247363,10247359,10239876,10239875,10239867,10239865,10239864,10239862,10239857,10239858,10239856,10239855,10239854,10239852,10239851,10239846,10239843,10239844,10239842,10239841,10239839,10239837,10239835,10239834,10239833,10239832,10239831,10239829,10239828,10239827,10239823,10239818,10239814,10239812,10239813,10239811,10239809,10239810,10239808,10239807,10239801,10239800,10239798,10239797,10239796,10280237,10280235,10280232,10280231,10280216,10280210,10280196,10280194,10280197,10280190,10280191,10280192,10239792,10239793,10239790,10239788,10239787,10239786,10239784,10239783,10239782,10239781,10239779,10239778,10239777,10239774,10239771,10239770,10239768,10239767,10239766,10239765,10239763,10239759,10239755,10239754,10239753,10239752,10239751,10239750,10239749,10239745,10239744,10239743,10239742,10239741,10239740,10239739,10239735,10239736,10239737,10239738,10240431,10240429,10240428,10240427,10240426,10240418,10240416,10240415,10240414,10240413,10240412,10240411,10240410,10240409,10240407,10240406,10240405,10240404,10240403,10240402,10240401,10240398,10240397,10240396,10240395,10240390,10240391,10240389,10240387,10240386,10240383,10240378,10240376,10240374,10240373,10240372,10240363,10240358,10240356,10240355,10240354,10240353,10240352,10240351,10240348,10240342,10240335,10240333,10240332,10240331,10240330,10240329,10240327,10240325,10240321,10240318,10240313,10240311,10240312,10240306,10240304,10240282,10240281,10240279,10240278,10240277,10240271,10240272,10240270,10240269,10240261,10240260,10240258,10240249,10240248,10240246,10240245,10240238,10240235,10240234,10240221,10240219,10240218,10240217,10240215,10240214,10240213,10240212,10240210,10240209,10240208,10240205,10240204,10240201,10240193,10283721,10240169,10240166,10240161,10240168,10240151,10240150,10240149,10240239,10240276,10240314,10240320,10240136,10240134,10240133,10240132,10240130,10240128,10240127,10240106,10240105,10240103,10240267,10240076,10240074,10240073,10240071,10240067,10240020,10240012,10240010,10240008,10240007,10240000,10239994,10239993,10239992,10240216,10240011,10240027,10239979,10239980,10239978,10239977,10239976,10239972,10239970,10239969,10239966,10239975,10239973,10239960,10239959,10239958,10240075,10239955,10239954,10239953,10239946,10239936,10239937,10239938,10239941,10239935,10240186,10287268,10288209,10239919,10239907,10240512,10240480,10240479,10240478,10240477,10240476,10240472,10240606,10240604,10302754,10283688,10283691,10283685,10283812,10283736,10283819,10283722,10282856,10282850,10282829,10240555,10240554,10240552,10246679,10246673,10246672,10246671,10246670,10246669,10246666,10246665,10246664,10246661,10246654,10240319,10249578,10280811,10289900,10287270,10283818,10239964,10280781,10239929,10239933,10251581,10281800,10246231,10283713,10283719,10251604,10250796,10246685,10246686,10268934,10239930,10239920,10240441,10240461,10240462,10240463,10240469,10239877,10239878,10239883,10239886,10239888,10239897,10239898,10239899,10239900,10239901,10239903,10239905,10240483,10240484,10240485,10240488,10240489,10240490,10240495,10240496,10240497,10240507,10240558,10240559,10240562,10240565,10240566,10240571,10240572,10240574,10240577,10240587,10240588,10240592,10240594,10240595,10240596,10240597,10240602,10246680,10246694,10246703,10246714,10279780,10247491,10254509,10254599,10240529,10240530,10240532,10240533,10240536,10240537,10240541,10240542,10240543,10240544,10240548,10240550,10246611,10246617,10246623,10249966,10268708,10268710,10268712,10268713,10245314,10281198,10281207,10281208,10281210,10281212,10281213,10281215,10281216,10281814,10281850,10282825,10281870,10281872,10283948,10283952,10284473,10284482,10284489,10284492,10284498,10284527,10284534,10330924,10294850,10295899,10296756,10296991,10296999,10297000,10297009,10330317,10299196,10299197,10299198,10299203,10299110,10330913,10302764,10302814,10302816,10302819,10303078,10303255,10303257,10303263,10303294,10303295,10303296,10303297,10303388,10303411,10303657,10303661,10303662,10303769,10303770,10305176,10305269,10306848,10306851,10306854,10306934,10306937,10306939,10306942,10306944,10306949,10306954,10306960,10306962,10306967,10306970,10306995,10307003,10307024,10307028,10307029,10307030,10307033,10307046,10307049,10307053,10307054,10309751,10330688,10328638,10328888,10330261,10330262,10330315,10330316,10333763);
    foreach($ids as $id_articulo) {
      $articulo = $this->modelo->get($id_articulo);
      //if ($articulo->status == "active") continue;
      echo $articulo->id.": ".$articulo->status."<br/>";
      $this->modelo->update_meli($articulo);

      //$articulo->id_meli = ""; // Para volver a publicar
      $body = $this->preparar_publicar_meli($articulo);
      if (isset($body["error"])) {
        $errores[] = $body["mensaje"];
        continue;
      }
      $salida = $this->publicar_meli($body,$articulo);
      if (isset($salida["link"])) {
        echo $salida["link"]."<br/>";

        // Actualizamos
        $sql = "UPDATE articulos_meli SET status = 'active' WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo ";
        $this->db->query($sql);

      } else {
        print_r($salida);
        //exit();
      }
    }
    print_r($errores);
  }

  function log($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $texto = isset($config["texto"]) ? $config["texto"] : "";
    $this->load->model("Log_Model");
    $log = date("Ymd")."_publicar_meli.txt";
    $this->Log_Model->imprimir(array(
      "file"=>$log,
      "texto"=>$texto,
      "id_empresa"=>$id_empresa,
    ));
  }

  function pasar_atributos() {
    $id_empresa = 614;
    $sql = "SELECT DISTINCT id_articulo FROM articulos_atributos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $sql = "SELECT * FROM articulos_atributos WHERE id_empresa = $id_empresa AND id_articulo = $row->id_articulo AND no_aplica = 0 ";
      $qq = $this->db->query($sql);
      $json = array();
      // [{"id":"BRAND","value_id":"75718","value_name":"Bridgestone"},{"id":"MODEL","value_name":"Turanza ER 300"}]
      foreach($qq->result() as $r) {
        $obj = new stdClass();
        $obj->id = $r->id_atributo;
        $obj->value_name = $r->value_name;
        if (!empty($r->value_id)) {
          $obj->value_id = $r->value_id;
        }
        $json[] = $obj;
      }
      // Actualizamos la base
      $j = json_encode($json);
      $sql = "UPDATE articulos SET seo_description = '$j' WHERE id = $row->id_articulo AND id_empresa = $id_empresa ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }


  // Obtiene los datos de una publicacion en particular de MercadoLibre
  private function get_publicacion($config = array()) {
    $publicacion = FALSE;
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_meli = (isset($config["id_meli"])) ? $config["id_meli"] : "";
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $response = $this->meli->get('/items/'.$id_meli, $params);
    if ($response["httpCode"] == 200 && isset($response["body"])) {
      return $response["body"];
    }
  }

  // Obtiene la descripcion de una publicacion en particular
  private function get_descripcion($config = array()) {
    $descripcion = FALSE;
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_meli = (isset($config["id_meli"])) ? $config["id_meli"] : "";
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $response = $this->meli->get('/items/'.$id_meli."/description", $params);
    if ($response["httpCode"] == 200 && isset($response["body"])) {
      $body = $response["body"];
      if (isset($body->plain_text)) {
        return $body->plain_text;
      }
    }
    return $descripcion;
  }

  // Importa una publicacion de MercadoLibre a nuestra base
  private function importar_publicacion($config = array()) {

    set_time_limit(0);

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $publicacion = (isset($config["publicacion"])) ? $config["publicacion"] : FALSE;
    if ($publicacion === FALSE) return;

    $this->load->model("Stock_Model");
    $this->load->model("Articulo_Model");
    $this->load->helper("file_helper");
    file_put_contents("articulos_meli_importacion.txt", print_r($publicacion,TRUE), FILE_APPEND);

    $sql = "SELECT ml_recargo_precio, ml_texto_empresa, ml_lista_base FROM web_configuracion WHERE id_empresa = $id_empresa LIMIT 0,1";
    $qw = $this->db->query($sql);
    $web_conf = $qw->row();

    // Obtenemos el articulo de la tabla articulos_meli
    $articulo_meli = $this->modelo->get_articulo_by_id_meli($publicacion->id,array(
      "id_empresa"=>$id_empresa
    ));
    if ($articulo_meli === FALSE) {
      // No existe tenemos que insertarlo
      $articulo = array(
        "lista_precios"=>2, // Que automaticamente tambien se publique en la web
        "nombre"=>(isset($publicacion->title) ? $publicacion->title : ""),
        "texto"=>(isset($publicacion->plain_text) ? nl2br($publicacion->plain_text) : ""),
        "id_empresa"=>$id_empresa,
        "path"=>(isset($publicacion->pictures) && !empty($publicacion->pictures) ? $publicacion->pictures[0]->secure_url : ""),
        "fecha_mov"=>date("Y-m-d"),
        "fecha_ingreso"=>date("Y-m-d"),
        "id_tipo_alicuota_iva"=>5,
        "moneda"=>"$",
        "porc_iva"=>21,
        "usa_stock"=>1,
      );
      // Dependiendo de lo que este configurado
      if (isset($publicacion->price)) {
        if ($web_conf->ml_lista_base == 0) {
          $articulo["precio_final_dto"] = $publicacion->price;
          $articulo["precio_final"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 1) {
          $articulo["precio_final_dto_2"] = $publicacion->price;
          $articulo["precio_final_2"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 2) {
          $articulo["precio_final_dto_3"] = $publicacion->price;
          $articulo["precio_final_3"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 3) {
          $articulo["precio_final_dto_4"] = $publicacion->price;
          $articulo["precio_final_4"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 4) {
          $articulo["precio_final_dto_5"] = $publicacion->price;
          $articulo["precio_final_5"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 5) {
          $articulo["precio_final_dto_6"] = $publicacion->price;
          $articulo["precio_final_6"] = $publicacion->price;
        }
      }

      // Guardamos en la tabla articulos
      $this->db->insert("articulos",$articulo);
      $id_articulo = $this->db->insert_id();

      // Actualizamos el link
      $articulo["nombre"] = trim($articulo["nombre"]);
      $articulo["nombre"] = str_replace("/", "-", $articulo["nombre"]);
      $link = "producto/".filename($articulo["nombre"],"-",0)."-".$id_articulo."/";
      $this->db->query("UPDATE articulos SET link = '$link' WHERE id = $id_articulo AND id_empresa = $id_empresa ");

      // Guardamos en la tabla articulos_meli
      $publicacion->formas_pago = "";
      if (isset($publicacion->non_mercado_pago_payment_methods) && is_array($publicacion->non_mercado_pago_payment_methods) && sizeof($publicacion->non_mercado_pago_payment_methods)>0) {
        $publicacion->formas_pago = array();
        foreach($publicacion->non_mercado_pago_payment_methods as $mp) {
          $publicacion->formas_pago[] = $mp->id;
        }
        $publicacion->formas_pago = implode(",", $publicacion->formas_pago);
      }
      
      $this->db->insert("articulos_meli",array(
        "id_articulo"=>$id_articulo,
        "id_empresa"=>$id_empresa,
        "id_meli"=>$publicacion->id,
        "activo_meli"=>1,
        "permalink"=>(isset($publicacion->permalink) ? $publicacion->permalink : ""),
        "fecha_publicacion"=>date("Y-m-d H:i:s"),
        "titulo_meli"=>(isset($publicacion->title) ? $publicacion->title : ""),
        "texto_meli"=>(isset($publicacion->plain_text) ? $publicacion->plain_text : ""),
        "atributos_meli"=>(isset($publicacion->attributes) ? json_encode($publicacion->attributes) : ""),
        "categoria_meli"=>(isset($publicacion->category_id) ? $publicacion->category_id : ""),
        "precio_meli"=>(isset($publicacion->price) ? $publicacion->price : 0),
        "list_type_id"=>(isset($publicacion->listing_type_id) ? $publicacion->listing_type_id : ""),
        "forma_envio_meli"=>(isset($publicacion->shipping->mode) ? $publicacion->shipping->mode : ""),
        "status"=>(isset($publicacion->status) ? $publicacion->status : ""),
        "forma_pago_meli"=>(isset($publicacion->formas_pago) ? $publicacion->formas_pago : ""),
        "retiro_sucursal_meli"=>(isset($publicacion->shipping->local_pick_up) ? $publicacion->shipping->local_pick_up : 0),
      ));

      // Guardamos las imagenes
      if (isset($publicacion->pictures)) {
        $i=0;
        foreach($publicacion->pictures as $pic) {
          $imgf = str_replace("-O.jpg", "-F.jpg", $pic->secure_url);
          $this->db->insert("articulos_images",array(
            "id_articulo"=>$id_articulo,
            "id_empresa"=>$id_empresa,
            "path"=>$imgf,
            "picture_id"=>$pic->id,
            "orden"=>$i,
            "activo_web"=>1,
            "activo_meli"=>1,
          ));
          $i++;
        }
      }

      // Guardamos el stock     
      $sql = "SELECT PV.* ";
      $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
      $sql.= "WHERE PV.id_empresa = $id_empresa ";
      $sql.= "LIMIT 0,1 ";
      $q_sec = $this->db->query($sql);
      foreach($q_sec->result() as $punto_venta) {
        $this->Stock_Model->ajustar($id_articulo,$publicacion->available_quantity,$punto_venta->id_sucursal);
      }

      // Importamos las variantes
      $this->importar_variantes(array(
        "publicacion"=>$publicacion,
        "id_articulo"=>$id_articulo,
      ));


    } else {

      // YA EXISTE EL ARTICULO, ACTUALIZAMOS NUESTROS DATOS

      // Actualizamos el stock que dice MercadoLibre
      $sql = "SELECT PV.* ";
      $sql.= "FROM puntos_venta PV INNER JOIN web_configuracion CONF ON (PV.id = CONF.id_punto_venta AND PV.id_empresa = CONF.id_empresa) ";
      $sql.= "WHERE PV.id_empresa = $id_empresa ";
      $sql.= "LIMIT 0,1 ";
      $q_sec = $this->db->query($sql);
      foreach($q_sec->result() as $punto_venta) {
        $this->Stock_Model->ajustar($articulo_meli->id_articulo,$publicacion->available_quantity,$punto_venta->id_sucursal);
      }

      // Dependiendo de lo que este configurado
      $articulo = array(
        "id_empresa"=>$id_empresa,
        "fecha_mov"=>date("Y-m-d"),
      );
      if (isset($publicacion->price)) {
        if ($web_conf->ml_lista_base == 0) {
          $articulo["precio_final_dto"] = $publicacion->price;
          $articulo["precio_final"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 1) {
          $articulo["precio_final_dto_2"] = $publicacion->price;
          $articulo["precio_final_2"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 2) {
          $articulo["precio_final_dto_3"] = $publicacion->price;
          $articulo["precio_final_3"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 3) {
          $articulo["precio_final_dto_4"] = $publicacion->price;
          $articulo["precio_final_4"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 4) {
          $articulo["precio_final_dto_5"] = $publicacion->price;
          $articulo["precio_final_5"] = $publicacion->price;
        } else if ($web_conf->ml_lista_base == 5) {
          $articulo["precio_final_dto_6"] = $publicacion->price;
          $articulo["precio_final_6"] = $publicacion->price;
        }
      }   
      if (isset($publicacion->title)) $articulo["nombre"] = $publicacion->title;
      if ($id_empresa != 1041) {
        if (isset($publicacion->plain_text)) $articulo["texto"] = nl2br($publicacion->plain_text);
      }
      if (isset($publicacion->attributes)) $articulo["atributos_meli"] = json_encode($publicacion->attributes);
      if (isset($publicacion->pictures) && !empty($publicacion->pictures)) $articulo["path"] = $publicacion->pictures[0]->secure_url;
      $this->Articulo_Model->update($articulo_meli->id_articulo,$articulo);

      // Actualizamos las imagenes
      if (isset($publicacion->pictures)) {
        $sql = "DELETE FROM articulos_images WHERE id_articulo = $articulo_meli->id_articulo AND id_empresa = $id_empresa AND activo_meli = 1 ";
        $this->db->query($sql);
        $i=0;
        foreach($publicacion->pictures as $pic) {
          $imgf = str_replace("-O.jpg", "-F.jpg", $pic->secure_url);
          $this->db->insert("articulos_images",array(
            "id_articulo"=>$articulo_meli->id_articulo,
            "id_empresa"=>$id_empresa,
            "path"=>$imgf,
            "picture_id"=>$pic->id,
            "orden"=>$i,
            "activo_web"=>1,
            "activo_meli"=>1,
          ));
          $i++;
        }
      }

      // Importamos las variantes
      $this->importar_variantes(array(
        "publicacion"=>$publicacion,
        "id_articulo"=>$articulo_meli->id_articulo,
        "id_sucursal"=>$punto_venta->id_sucursal,
      ));
    }
  }

  private function importar_variantes($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $publicacion = (isset($config["publicacion"])) ? $config["publicacion"] : FALSE;
    $id_articulo = (isset($config["id_articulo"])) ? $config["id_articulo"] : 0;
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;
    if ($publicacion === FALSE || $id_articulo == 0) return;
    if (!isset($publicacion->variations)) return;

    $this->load->helper("file_helper");

    // Borramos las variantes
    $sql = "DELETE FROM articulos_variantes WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo ";
    $this->db->query($sql);
    // Volvemos a actualizar el stock
    if (!empty($id_sucursal)) $sql = "DELETE FROM stock_variantes WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo AND id_sucursal = $id_sucursal ";

    // Recorremos las variantes
    foreach($publicacion->variations as $variacion) {

      // Recorremos la combinacion de articulos
      $vars = array();
      $nombre_variacion = "";
      $i = 0;
      foreach($variacion->attribute_combinations as $atributo) {

        // Buscamos la propiedad
        $sql = "SELECT * FROM articulos_propiedades ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND LOWER(nombre) = '".strtolower($atributo->name)."' ";
        $q = $this->db->query($sql);
        if ($q->num_rows() > 0) {
          $r = $q->row();
          $id_propiedad = $r->id;
        } else {
          $sql = "INSERT INTO articulos_propiedades (id_empresa,nombre) VALUES ($id_empresa,'$atributo->name') ";
          $this->db->query($sql);
          $id_propiedad = $this->db->insert_id();
        }

        // Buscamos la opcion
        $sql = "SELECT * FROM articulos_propiedades_opciones ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_propiedad = $id_propiedad ";
        $sql.= "AND LOWER(etiqueta) = '".strtolower($atributo->value_name)."' ";
        $q = $this->db->query($sql);
        $nombre = filename($atributo->value_name,'-',0);
        if ($q->num_rows() > 0) {
          $r = $q->row();
          $id_opcion = $r->id;
        } else {
          $sql = "INSERT INTO articulos_propiedades_opciones (id_empresa, id_propiedad, nombre, etiqueta) VALUES (";
          $sql.= " $id_empresa, $id_propiedad, '$nombre', '$atributo->value_name') ";
          $this->db->query($sql);
          $id_opcion = $this->db->insert_id();
        }

        $vars[] = array("id_propiedad"=>$id_propiedad,"id_opcion"=>$id_opcion,"nombre"=>$atributo->value_name);
        $nombre_variacion = $atributo->value_name." / ";
        $i++;
      }

      // Insertamos la variante
      $sql = "INSERT INTO articulos_variantes (id_empresa, id_articulo, id_opcion_1, id_opcion_2, id_opcion_3, nombre, path, stock) VALUES (";
      $sql.= " $id_empresa, $id_articulo, ";
      $sql.= (isset($vars[0]) ? $vars[0]["id_opcion"] : 0).", ";
      $sql.= (isset($vars[1]) ? $vars[1]["id_opcion"] : 0).", ";
      $sql.= (isset($vars[2]) ? $vars[2]["id_opcion"] : 0).", ";
      $sql.= " '$nombre','', '$variacion->available_quantity') ";
      $this->db->query($sql);
      $id_variante = $this->db->insert_id();

      // Modificamos el stock de la variante
      if (!empty($id_sucursal)) {
        $sql = "INSERT INTO stock_variantes (id_empresa, id_articulo, id_variante, id_sucursal, stock_actual, reservado) VALUES (";
        $sql.= " '$id_empresa', '$id_articulo', '$id_variante', '$id_sucursal', '$variacion->available_quantity', 0)";
        $this->db->query($sql);
      }
    }
  }

  private function obtener_publicaciones_meli($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $limit = (isset($config["limit"])) ? $config["limit"] : 100;
    $offset = (isset($config["offset"])) ? $config["offset"] : 0;
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token, "limit" => $limit, "offset" => $offset);
    $response = $this->meli->get('/users/'.$this->configuracion->ml_user_id.'/items/search', $params);
    if ($response["httpCode"] == 200 && isset($response["body"])) {
      $body = $response["body"];
      if (isset($body->results) && sizeof($body->results)>0) {
        // Recorremos los resultados
        foreach($body->results as $res) {
          // Obtenemos la publicacion en particular
          $publicacion = $this->get_publicacion(array(
            "id_meli"=>$res,
            "id_empresa"=>$id_empresa,
          ));
          // Obtenemos la descripcion de la publicacion
          $publicacion->plain_text = $this->get_descripcion(array(
            "id_meli"=>$res,
            "id_empresa"=>$id_empresa,
          ));    
          // La importamos al sistema
          $this->importar_publicacion(array(
            "publicacion"=>$publicacion,
            "id_empresa"=>$id_empresa,
          ));
        }
        return $body;
      }
      return FALSE;
    } else {
      return FALSE;
    }
  }

  // Devuelve el listado de todas las publicaciones que tiene el usuario en MercadoLibre
  // y las guardamos en nuestro sistema para que despues queden sincronizadas
  function obtener_publicaciones() {
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = parent::get_empresa();
    $limit = 100;
    $total = 99999;
    $offset = 0;
    while($offset < $total) {
      $s = $this->obtener_publicaciones_meli(array(
        "id_empresa"=>$id_empresa,
        "offset"=>$offset,
        "limit"=>$limit,
      ));
      if (isset($s->paging) && isset($s->paging->offset) && isset($s->paging->total)) {
        $offset = $s->paging->offset + $limit;
        $total = $s->paging->total;
      } else {
        echo json_encode(array("error"=>1));
        exit();
      }
    }
    echo json_encode(array("error"=>0));
  }

  // Obtenemos todos los productos publicados en mercadolibre
  function listar() {
    $id_empresa = parent::get_empresa();
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $ids = array();
    $sql = "SELECT MELI.*, A.codigo, A.precio_final_dto_4 FROM articulos_meli MELI ";
    $sql.= "INNER JOIN articulos A ON (MELI.id_articulo = A.id AND MELI.id_empresa = A.id_empresa) ";
    $sql.= "WHERE MELI.id_empresa = $id_empresa";
    
    $s = "CODIGO;ID_MELI;LISTA 3;LISTA MELI;PRECIO MERCADO LIBRE\n";
    file_put_contents("gastrober.csv", $s);
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $response = $this->meli->get('/items/'.$row->id_meli, $params);
      if ($response["httpCode"] != 200) continue;
      $body = $response["body"];
      $s = $row->codigo.";".$row->id_meli.";".$row->precio_final_dto_4.";".$row->precio_meli.";".$body->price."\n";
      file_put_contents("gastrober.csv", $s, FILE_APPEND);
    }
    echo "TERMINO";
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

      $sql = "UPDATE articulos_meli SET activo_meli = 0, status = 'paused' WHERE id_meli = '$item_id' ";
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
        $sql = "UPDATE articulos_meli SET activo_meli = 0, status = 'paused' WHERE id_meli = '$art->id_meli' ";
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


  function reactivar_multiple() {

    $this->connect();
    $ids = parent::get_post("ids","");
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $body = array(
      "status"=>"active",
    );
    $errores = "";
    $ids_array = explode(",",$ids);
    foreach($ids_array as $id) {
      if (empty($id)) continue;
      $art = $this->modelo->get($id);
      if ($art === FALSE) continue;
      if (!isset($art->id_meli) || !isset($art->status)) continue;
      if (empty($art->id_meli)) continue;
      if ($art->status != "paused") continue;

      $response = $this->meli->put('/items/'.$art->id_meli, $body, $params);
      if ($response["httpCode"] == 200) {
        $sql = "UPDATE articulos_meli SET activo_meli = 1, status = 'active' WHERE id_meli = '$art->id_meli' ";
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
    $id_meli = parent::get_post("id_meli","");
    $id_articulo = parent::get_post("id_articulo",0);
    if (empty($id_meli)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }
    $this->log(array("texto"=>"REACTIVAR PRODUCTO $id_articulo [$id_meli]"));
    $params = array('access_token' => $this->configuracion->ml_access_token);

    // Consultamos el estado actual en MercadoLibre
    $publicacion = $this->get_publicacion(array(
      "id_meli"=>$id_meli
    ));
    $this->log(array("texto"=>"Obteniendo publicacion de $id_articulo [$id_meli]"));
    $this->log(array("texto"=>print_r($publicacion,TRUE)));
    if (!isset($publicacion->status)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se pudo obtener la publicacion en MercadoLibre.",
      ));
      exit();
    }

    // Si la publicacion estaba cerrada en ML, la republicamos
    if ($publicacion->status == "closed") {
      $salida = $this->do_republicar(array(
        "id_meli"=>$id_meli,
        "id_articulo"=>$id_articulo,
      ));

    // Si la publicacion estaba pausada en ML, la reactivamos
    } else if ($publicacion->status == "paused") {
      $salida = $this->do_reactivar(array(
        "id_meli"=>$id_meli,
        "id_articulo"=>$id_articulo,
      ));

    } else {

      // En cualquier otro caso mostramos ERROR
      $salida = array(
        "error"=>1,
        "mensaje"=>"Hubo un error al reactivar la publicacion en MercadoLibre",
      );
    }
    
    echo json_encode($salida);
  }

  function do_reactivar($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $id_meli = isset($config["id_meli"]) ? $config["id_meli"] : "";

    $body = array(
      "status"=>"active",
    );
    $response = $this->meli->put('/items/'.$id_meli, $body, $params);
    if ($response["httpCode"] == 200) {

      $sql = "UPDATE articulos_meli SET activo_meli = 1, status = 'active' WHERE id_meli = '$id_meli' ";
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

      $sql = "UPDATE articulos_meli SET status = 'closed' WHERE id_meli = '$item_id' ";
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
    $sql = "SELECT * FROM articulos_meli WHERE id_meli = '$item_id' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: No se encuentra el articulo con ID: '$item_id'",
      ));
      exit();
    }
    $articulo = $q->row();
    if ($articulo->status != "closed") {
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

      $sql = "DELETE FROM articulos_meli WHERE id_meli = '$item_id' ";
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

  function do_republicar($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_articulo = isset($config["id_articulo"]) ? $config["id_articulo"] : 0;
    $id_meli = isset($config["id_meli"]) ? $config["id_meli"] : "";
    
    $articulo = $this->modelo->get($id_articulo,$id_empresa);
    $this->modelo->update_meli($articulo);
    $body = $this->preparar_publicar_meli($articulo);
    if (isset($body["error"])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>$body["mensaje"],
      ));
      exit();
    }

    $params = array('access_token' => $this->configuracion->ml_access_token);
    if (isset($body["variations"])) {
      $body2 = array(
        "listing_type_id"=>$body["listing_type_id"],
        "variations"=>$body["variations"],
      );
    } else {
      $body2 = array(
        "price"=>$body["price"],
        "quantity"=>$body["available_quantity"],
        "listing_type_id"=>$body["listing_type_id"],
      );      
    }
    $this->log(array("texto"=>"Republicar $id_articulo"));
    $this->log(array("texto"=>print_r($body2,TRUE)));
    $response = $this->meli->post('/items/'.$id_meli.'/relist', $body2, $params);
    if ($response["httpCode"] == 201) {

      $body = $response["body"];
      $sql = "UPDATE articulos_meli SET activo_meli = 1, status = 'active', ";
      $sql.= " permalink = '".$body["permalink"]."', ";
      $sql.= " fecha_publicacion = '".date("Y-m-d H:i:s")."' ";
      $sql.= "WHERE id_meli = '$id_meli' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);
      return array(
        "error"=>0,
      );

    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      $this->log(array("texto"=>"ERROR AL REPUBLICAR"));
      $this->log(array("texto"=>print_r($body,TRUE)));
      if (isset($body->message)) {
        return array(
          "error"=>1,
          "mensaje"=>$body->message,
        );
      } else {
        return array(
          "error"=>1,
          "mensaje"=>"Error al republicar el articulo",
        );
      }
    }
  }


  function republicar() {

    $this->connect();
    $id_articulo = parent::get_post("id_articulo",0);
    $id_meli = parent::get_post("id_meli","");
    if (empty($id_meli)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: falta id_meli",
      ));
      exit();
    }
    // Consultamos si el estado es CLOSED antes de eliminar
    $sql = "SELECT * FROM articulos_meli WHERE id_meli = '$id_meli' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: No se encuentra el articulo con ID: '$id_meli'",
      ));
      exit();
    }
    $articulo = $q->row();
    if ($articulo->status != "closed") {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: La pubicacion no esta finalizada.",
      ));
      exit();      
    }

    $id_empresa = parent::get_empresa();
    $salida = $this->do_republicar(array(
      "id_empresa"=>$id_empresa,
      "id_meli"=>$id_meli,
      "id_articulo"=>$id_articulo,
    ));
    echo json_encode($salida);
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

  // Utilizado en PASO 3 para llenar la ficha tecnica
  function controlar_atributos($id_articulo,$id_categoria) {
    $articulo = $this->modelo->get($id_articulo);
    if (empty($articulo)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el articulo con ID $id_articulo",
      ));
      exit();
    }
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $url = '/categories/'.$id_categoria."/attributes";
    $response = $this->meli->get($url, $params);
    if (isset($response["body"])) {
      $error = 0;
      $body = $response["body"];
      $salida = array();
      foreach($body as $atributo) {
        if (isset($atributo->tags) && isset($atributo->tags->required) && $atributo->tags->required == 1) {
          // Si tiene un atributo obligatorio, tenemos que controlar que haya una variante de ese mismo atributo
          $encontro = false;
          foreach($articulo->variantes as $variante) {
            if ($variante->id_propiedad_1 == $atributo->id || $variante->id_propiedad_2 == $atributo->id || $variante->id_propiedad_3 == $atributo->id) {
              $encontro = true;
              break 2;
            }
          }
        }
        $salida[] = $atributo;
      }
      if (sizeof($salida) > 0) {
        // Mandamos todo el body a la vista para mostrar la ficha a completar
        echo json_encode(array(
          "error"=>0,
          "mensaje"=>"Es necesario completar atributos",
          "atributos"=>$salida,
        ));
      } else {
        echo json_encode(array("error"=>0,"atributos"=>$salida,"mensaje"=>""));
      }
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: No se encuentra la categoria $id_categoria",
      ));
    }
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
      if (isset($body->status) && $body->status == 404) {
        // La categoria no existe, devolvemos el array principal
        return $salida;
      }

      $salida["id"] = isset($body->id) ? $body->id : "";

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
    $id_articulo = parent::get_post("id_articulo",0);

    $articulo = $this->modelo->get($id_articulo);
    $body = $this->preparar_publicar_meli($articulo);
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
    $salida = $this->publicar_meli($body,$articulo);
    echo json_encode($salida);
  }

  function publicar_multiple() {

    $this->connect();
    $ids = parent::get_post("ids","");
    $categoria_meli = parent::get_post("categoria_meli","");
    $list_type_id = parent::get_post("list_type_id","");
    $forma_envio_meli = parent::get_post("forma_envio_meli","");
    $forma_pago_meli = parent::get_post("forma_pago_meli","");
    $retiro_sucursal_meli = parent::get_post("retiro_sucursal_meli","");
    $images_meli = parent::get_post("images_meli","");

    // Recorremos todos los IDS
    $para_publicar = array();
    $ids_array = explode(",",$ids);
    foreach($ids_array as $id) {
      if (empty($id)) continue;
      $art = $this->modelo->get($id);
      if ($art === FALSE) continue;

      // Solamente publicamos aquellos productos que no estan activos (no existe otra publicacion activa)
      if ($art->status == "active") {

        // Tomamos la configuracion enviada
        $art->categoria_meli = $categoria_meli;
        $art->list_type_id = $list_type_id;
        $art->forma_envio_meli = $forma_envio_meli;
        $art->forma_pago_meli = $forma_pago_meli;
        $art->retiro_sucursal_meli = $retiro_sucursal_meli;

        // Tomamos las nuevas imagenes cargadas
        if (!empty($images_meli)) {

          if (strpos($images_meli, ";;;")) $images_meli_array = explode(";;;",$images_meli);
          else $images_meli_array = array($images_meli);

          $k = 0;
          foreach($images_meli_array as $img_meli) {
            $sql = "INSERT INTO articulos_images_meli (id_empresa,id_articulo,path,orden) VALUES (";
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
            "articulo"=>$art,
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
        $mensaje_error = "Error en ".$pp["articulo"]->nombre.": ".$pp["body"]["mensaje"];
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
        $resp = $this->publicar_meli($pp["body"],$pp["articulo"]);
      }
      echo json_encode(array(
        "error"=>0,
      ));
      exit();
    }
  }

  // Arma el objeto que se va a enviar a MercadoLibre
  private function preparar_publicar_meli($articulo) {

    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);

    // Variable para llevar el stock total del producto
    // En caso de tener variantes, es la suma del stock de todas las variantes
    $stock = 0;
    $this->load->model("Stock_Model");
    $this->load->model("Articulo_Model");
    $this->load->model("Punto_Venta_Model");
    $pv = $this->Punto_Venta_Model->get_punto_venta_web(array(
      "id_empresa"=>$articulo->id_empresa
    ));
    if ($pv === FALSE) {
      return array(
        "error"=>1,
        "mensaje"=>"ERROR: No esta configurado el Punto de Venta asociado a la web.",
      );
    }
    
    if (sizeof($articulo->variantes)>0) {
      $stock_variantes = $this->Stock_Model->get_variantes(array(
        "id_articulo"=>$articulo->id,
        "id_empresa"=>$articulo->id_empresa,
        "id_sucursal"=>$pv->id_sucursal
      ));
      foreach($stock_variantes as $var) {
        $stock += ($var->stock - $var->reservado);
      }
    } else {
      $stock = $this->Stock_Model->get_stock($articulo->id,array(
        "id_empresa"=>$articulo->id_empresa,
      ));      
    }
    if ($stock == 0) {
      return array(
        "error"=>1,
        "mensaje"=>"ERROR: El articulo '".$articulo->nombre."' (Cod: ".$articulo->codigo.") no tiene stock.",
      );
    }

    // Enviamos la informacion a MercadoLibre
    $body = array(
      "title"=>$articulo->titulo_meli,
      "category_id"=>$articulo->categoria_meli,
      "price"=>$articulo->precio_meli,
      "currency_id"=>"ARS",
      "available_quantity"=>$stock,
      "buying_mode"=>"buy_it_now",
      "listing_type_id"=>$articulo->list_type_id,
      "condition"=>"new",
      "shipping"=>array(
        "mode"=> $articulo->forma_envio_meli,
        "local_pick_up"=> (($articulo->retiro_sucursal_meli == 1) ? true : false),
      ),
      //"domain_id"=>"MLA-FANS",
      "description"=> array(
        "plain_text"=>strip_tags(html_entity_decode($articulo->texto_meli,ENT_QUOTES)),
      ),
    );

    // Si tenemos atributos cargados
    if (!empty($articulo->atributos_meli)) {
      $body["attributes"] = json_decode($articulo->atributos_meli);
    }

    // Para publicar productos mayores a X monto, hay que poner obligatorio free shipping
    if ($articulo->precio_meli > 2500) {
      $body["shipping"]["tags"] = array("mandatory_free_shipping");
    }

    // Imagenes
    $imagenes = array();
    $body["pictures"] = array();
    foreach($articulo->images as $img) {
      $body["pictures"][] = array(
        "source"=>"https://www.varcreative.com/sistema/".$img,
      );
      $imagenes[] = "https://www.varcreative.com/sistema/".$img;
    }
    foreach($articulo->images_meli as $img) {
      $body["pictures"][] = array(
        "source"=>"https://www.varcreative.com/sistema/".$img,
      );
      $imagenes[] = "https://www.varcreative.com/sistema/".$img;
    }

    // Obtenemos las imagenes por defecto
    $query = $this->db->query("SELECT * FROM web_configuracion_images_meli WHERE id_empresa = $articulo->id_empresa ORDER BY orden ASC");
    foreach($query->result() as $image_meli) {
      $body["pictures"][] = array(
        "source"=>"https://www.varcreative.com/sistema/".$image_meli->path,
      );
      $imagenes[] = "https://www.varcreative.com/sistema/".$image_meli->path;      
    }

    if (empty($body["pictures"])) {
      return array(
        "error"=>1,
        "mensaje"=>"El producto debe tener al menos una imagen habilitada para MercadoLibre",
      );
    }

    // Formas de pago
    $formas_pago = explode(",",$articulo->forma_pago_meli);
    if (sizeof($formas_pago)>0) {
      $body["non_mercado_pago_payment_methods"] = array();
      foreach($formas_pago as $fp) {
        $body["non_mercado_pago_payment_methods"][] = array(
          "id"=>$fp
        );
      }
    }

    // Esta forma quedo deprecada y se reemplazo por el paso 3 de completar la ficha tecnica
    // Armamos los atributos del articulo
    //$atributos = $this->modelo->preparar_atributos_meli($articulo);
    //if ($atributos !== FALSE) $body["attributes"] = $atributos;

    // Si el producto tiene variantes
    if (sizeof($articulo->variantes)>0) {
      $variaciones = array();
      foreach($articulo->variantes as $variacion) {

        // Buscamos el stock de esa variante
        $stk_var = 0;
        foreach($stock_variantes as $var) {
          if ($variacion->id == $var->id) $stk_var = $var->stock - $var->reservado;
        }
        if (empty($stk_var)) continue;
        $combinacion = array();
        for($i=1;$i<4;$i++) {
          if (empty($variacion->{"id_propiedad_".$i})) continue;
          $combinacion[] = array(
            "id"=>$variacion->{"id_propiedad_".$i},
            "value_id"=>$variacion->{"nombre_opcion_".$i},
          );
        }
        $imagenes2 = $imagenes;
        if (!empty($variacion->path)) {
          $imagenes2[] = "https://www.varcreative.com/sistema/".$variacion->path;
        }
        $variaciones[] = array(
          "attribute_combinations"=>$combinacion,
          "available_quantity"=>(int)$stk_var,
          "price"=>$articulo->precio_meli,
          "picture_ids"=>$imagenes2,
        );
      }
      if (!empty($variaciones)) {
        $body["variations"] = $variaciones;
      }
    }

    return $body;
  }

  // Validamos el articulo que estamos por enviar
  private function validar_meli($body) {
    $this->connect();
    file_put_contents("compartir_articulos_error.txt", print_r($body,TRUE), FILE_APPEND);
    $params = array('access_token' => $this->configuracion->ml_access_token);
    $response = $this->meli->post('/items/validate', $body, $params);
    if ($response["httpCode"] == 204) {
      return array(
        "error"=>0,
      );
    } else if ($response["httpCode"] >= 400) {
      $body = $response["body"];
      file_put_contents("compartir_articulos_error.txt", print_r($body,TRUE), FILE_APPEND);
      if (isset($body->error)) {
        $mensaje = isset($body->message) ? $body->message : "";
        if (isset($body->cause) && is_array($body->cause) && sizeof($body->cause)>0) {
          foreach($body->cause as $cause) {
            if ($cause->type == "error") {
              if (is_object($cause) && isset($cause->message)) {
                $mensaje = $cause->message;
              } else if ($mensaje == "seller.unable_to_list" && $cause == "has_debt") {
                $mensaje = "El usuario no puede publicar debido a una deuda impaga.";
              }              
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

  private function publicar_meli($body,$articulo) {
    $this->connect();
    $params = array('access_token' => $this->configuracion->ml_access_token);

    // Hay que actualizar
    if (!empty($articulo->id_meli)) {
      $body2 = array();
      $body2["title"] = $body["title"];
      $body2["category_id"] = $body["category_id"];
      $body2["currency_id"] = $body["currency_id"];
      $body2["price"] = $body["price"];
      $body2["available_quantity"] = $body["available_quantity"];
      $body2["pictures"] = $body["pictures"];
      if (isset($body["attributes"])) $body2["attributes"] = $body["attributes"];
      file_put_contents("compartir_articulos_error.txt", print_r($body2,TRUE), FILE_APPEND);
      $response = $this->meli->put("/items/".$articulo->id_meli, $body2, $params);
      if ($response["httpCode"] == 200) {
        $res = $response["body"];
        return array(
          "error"=>0,
          "link"=>$res->permalink,
        );
      } else if ($response["httpCode"] >= 400){
        file_put_contents("compartir_articulos_error.txt", print_r($response,TRUE), FILE_APPEND);
        // Ocurrio un error, lo enviamos
        $body = $response["body"];
        return array(
          "error"=>1,
          "mensaje"=>$body->message,
        ); 
      }

    // Hay que publicar un nuevo articulo
    } else {
      $response = $this->meli->post('/items', $body, $params);
      if ($response["httpCode"] == 201) {
        $res = $response["body"];

        // Actualizamos los datos del articulo en la base de datos
        $this->db->where("id_articulo",$articulo->id);
        $this->db->where("id_empresa",$articulo->id_empresa);
        $this->db->update("articulos_meli",array(
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