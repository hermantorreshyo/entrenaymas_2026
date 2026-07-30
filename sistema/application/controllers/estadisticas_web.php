<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Estadisticas_Web extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function get_ga_service() {
    set_include_path(get_include_path().PATH_SEPARATOR.APPPATH.'libraries/Google/');
    require APPPATH.'libraries/Google/Client.php';
    require APPPATH.'libraries/Google/Service/Analytics.php';

    $id_empresa = parent::get_empresa();
    if ($id_empresa == 730 || $id_empresa == 1234 || $id_empresa == 1229) {
      $service_account_name = 'analytics-2@varcreative-1470261596740.iam.gserviceaccount.com';
      $key_file_location = APPPATH.'libraries/Google/key2.p12';
    } else {
      $client_id = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q.apps.googleusercontent.com'; //Client ID  
      $service_account_name = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q@developer.gserviceaccount.com'; //Email Address 
      $key_file_location = APPPATH.'libraries/Google/key.p12';
    }
    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $service = new Google_Service_Analytics($client);
    
    if (isset($_SESSION['service_token'])) {
      $client->setAccessToken($_SESSION['service_token']);
    }
    $key = file_get_contents($key_file_location);
    $cred = new Google_Auth_AssertionCredentials(
      $service_account_name,
      array('https://www.googleapis.com/auth/analytics.readonly'),
      $key
    );
    $client->setAssertionCredentials($cred);
    if($client->getAuth()->isAccessTokenExpired()) {
      $client->getAuth()->refreshTokenWithAssertion($cred);
    }
    $_SESSION['service_token'] = $client->getAccessToken();
    return $service;
  }


  function categorias_paginas() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($conf->view_id)) {
      parent::send_error("ERROR: No esta configurada correctamente la vista de Analytics.");
      exit();
    }
    $view_id = "ga:".$conf->view_id;

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(parent::get_get("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_get("hasta",date("d/m/Y")));
    $salida = array();

    $this->load->model("Entrada_Model");
    $this->load->model("Categoria_Entrada_Model");
    $total = 0;

    try {
      
      $service = $this->get_ga_service();

      // PAGINAS MAS VISTAS
      $results = $service->data_ga->get($view_id,$desde,$hasta,'ga:pageviews',array(
        "sort" => "-ga:pageviews",
        "dimensions" => "ga:pagePath"
      ));
      if (count($results->getRows()) > 0) {
        foreach ($results->getRows() as $r) {
          $url = $r[0];
          $url2 = $url;

          /*
          echo "<tr>";
          echo "<td>".$r[0]."</td>";
          echo "<td>".$r[1]."</td>";
          echo "</tr>";
          continue;
          */

          // Si comienza con el string
          $query = "/entrada/";
          if (substr($url, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url);
            $id = substr($url, strrpos($url, "-")+1);
            $id = str_replace("/", "", $id);

            if (strpos($id, "?")>0) {
              $id = substr($id, 0, strpos($id,"?"));
            }
            $obj = new stdClass();
            $obj->id = $id;
            $obj->url = $url2;
            $obj->visitas = $r[1];

            // Consultamos por el articulo
            $entrada = $this->Entrada_Model->get($id,array(
              "id_empresa"=>$id_empresa,
              "min"=>1,
            ));
            if (empty($entrada)) continue;

            $id_root = $this->Categoria_Entrada_Model->get_id_root($entrada->id_categoria,array(
              "id_empresa"=>$id_empresa
            ));
            $categoria = $this->Categoria_Entrada_Model->get($id_root);

            $total = $total + $obj->visitas;

            if (!isset($salida[$id_root])) {
              $salida[$id_root] = array(
                "id"=>$categoria->id,
                "nombre"=>$categoria->nombre,
                "visitas"=>$obj->visitas,
                "cantidad_entradas"=>1,
              );
            } else {
              $salida[$id_root]["visitas"] = $salida[$id_root]["visitas"] + $obj->visitas;
              $salida[$id_root]["cantidad_entradas"] = $salida[$id_root]["cantidad_entradas"] + 1;
            }
            
          }

          // Si comienza con el string
          $query = "/entradas/";
          if (substr($url, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url);
            $category = substr($url, 0, strpos($url, "/"));
            $categoria = $this->Categoria_Entrada_Model->get_by_link($category,array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria)) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

          // Si comienza con el string /video
          $query = "/web/video";
          if (substr($url, 0, strlen($query)) === $query) {
            $categoria = $this->Categoria_Entrada_Model->get_by_link("mag-tv",array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria)) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

          // Si comienza con el string
          $query = "/web/event";
          if (substr($url, 0, strlen($query)) === $query) {
            $categoria = $this->Categoria_Entrada_Model->get_by_link("events",array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria)) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

          // Si comienza con el string
          if ($url == "/") {
            $categoria = $this->Categoria_Entrada_Model->get_by_link("home",array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria)) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

          // Si comienza con el string
          $query = "/web/compan";
          if (substr($url, 0, strlen($query)) === $query) {
            $categoria = $this->Categoria_Entrada_Model->get_by_link("companies",array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria)) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

        }
      }

      // Calculamos los %
      $salida2 = array();
      foreach($salida as $key => $value) {
        $salida[$key]["porcentaje"] = ($value["visitas"] / $total * 100);
        $value["porcentaje"] = $salida[$key]["porcentaje"];
        $salida2[] = $value;
      }
      echo json_encode($salida2);
    } catch(Exception $e) {
      parent::send_error($e->getMessage());
    }
  }


  function paginas_por_categoria() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($conf->view_id)) {
      parent::send_error("ERROR: No esta configurada correctamente la vista de Analytics.");
      exit();
    }
    $view_id = "ga:".$conf->view_id;

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(parent::get_get("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_get("hasta",date("d/m/Y")));
    $id_categoria = parent::get_get("id_categoria");
    $salida = array();

    $this->load->model("Entrada_Model");
    $this->load->model("Categoria_Entrada_Model");
    $total = 0;

    try {
      
      $service = $this->get_ga_service();

      // PAGINAS MAS VISTAS
      $results = $service->data_ga->get($view_id,$desde,$hasta,'ga:pageviews',array(
        "sort" => "-ga:pageviews",
        "dimensions" => "ga:pagePath"
      ));
      if (count($results->getRows()) > 0) {
        foreach ($results->getRows() as $r) {
          $url = $r[0];
          $url2 = $url;

          // Si comienza con el string
          $query = "/entrada/";
          if (substr($url, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url);
            $id = substr($url, strrpos($url, "-")+1);
            $id = str_replace("/", "", $id);

            if (strpos($id, "?")>0) {
              $id = substr($id, 0, strpos($id,"?"));
            }
            $obj = new stdClass();
            $obj->id = $id;
            $obj->url = $url2;
            $obj->visitas = $r[1];

            // Consultamos por el articulo
            $entrada = $this->Entrada_Model->get($id,array(
              "id_empresa"=>$id_empresa,
              "min"=>1,
            ));
            if (empty($entrada)) continue;

            $id_root = $this->Categoria_Entrada_Model->get_id_root($entrada->id_categoria,array(
              "id_empresa"=>$id_empresa
            ));
            $categoria = $this->Categoria_Entrada_Model->get($id_root);

            if ($categoria->id == $id_categoria) {
              $total = $total + $obj->visitas;

              if (!isset($salida[$entrada->id_categoria])) {
                $salida[$entrada->id_categoria] = array(
                  "id"=>$entrada->id_categoria,
                  "nombre"=>$entrada->categoria,
                  "visitas"=>$obj->visitas,
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$entrada->id_categoria]["visitas"] = $salida[$entrada->id_categoria]["visitas"] + $obj->visitas;
                $salida[$entrada->id_categoria]["cantidad_entradas"] = $salida[$entrada->id_categoria]["cantidad_entradas"] + 1;
              }              
            }
            
          }

          // Si comienza con el string
          $query = "/entradas/";
          if (substr($url, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url);
            $category = substr($url, 0, strpos($url, "/"));
            $categoria = $this->Categoria_Entrada_Model->get_by_link($category,array(
              "id_empresa"=>$id_empresa
            ));
            if (!empty($categoria) && $categoria->id == $id_categoria) {

              $total = $total + $r[1];

              if (!isset($salida[$categoria->id])) {
                $salida[$categoria->id] = array(
                  "id"=>$categoria->id,
                  "nombre"=>$categoria->nombre,
                  "visitas"=>$r[1],
                  "cantidad_entradas"=>1,
                );
              } else {
                $salida[$categoria->id]["visitas"] = $salida[$categoria->id]["visitas"] + $r[1];
                $salida[$categoria->id]["cantidad_entradas"] = $salida[$categoria->id]["cantidad_entradas"] + 1;
              }
            }
          }

        }
      }

      // Calculamos los %
      $salida2 = array();
      foreach($salida as $key => $value) {
        $salida[$key]["porcentaje"] = ($value["visitas"] / $total * 100);
        $value["porcentaje"] = $salida[$key]["porcentaje"];
        $salida2[] = $value;
      }
      echo json_encode($salida2);
    } catch(Exception $e) {
      parent::send_error($e->getMessage());
    }
  }



  function articulos() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($conf->view_id)) {
      parent::send_error("ERROR: No esta configurada correctamente la vista de Analytics.");
      exit();
    }
    $view_id = "ga:".$conf->view_id;

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(parent::get_post("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_post("hasta",date("d/m/Y")));
    $salida = array();

    $this->load->model("Venta_Model");
    $this->load->model("Articulo_Model");
    $this->load->model("Consulta_Model");

    try {
      
      $service = $this->get_ga_service();

      // PAGINAS MAS VISTAS
      $results = $service->data_ga->get($view_id,$desde,$hasta,'ga:pageviews',array(
        "sort" => "-ga:pageviews",
        "dimensions" => "ga:pagePath"
      ));
      if (count($results->getRows()) > 0) {
        foreach ($results->getRows() as $r) {
          $url = $r[0];
          // Si comienza con el string
          $query = "/producto/";
          if (substr($url, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url);
            $id = substr($url, strrpos($url, "-")+1);
            $id = str_replace("/", "", $id);

            if (strpos($id, "?")>0) {
              $id = substr($id, 0, strpos($id,"?"));
            }
            if (!is_numeric($id)) continue;

            $obj = new stdClass();
            $obj->id = $id;
            $obj->visitas = $r[1];

            // Consultamos por el articulo
            $art = $this->Articulo_Model->get_nombre($id,array(
              "id_empresa"=>$id_empresa
            ));
            if ($art === FALSE) {
              // No se encuentra el articulo
              continue;
            }
            $obj->nombre = $art->nombre;
            $obj->path = $art->path;
            $obj->path = (!empty($obj->path)) ? (((strpos($obj->path,"http://")===FALSE)) ? "/sistema/".$obj->path : $obj->path) : "";

            $ventas = $this->Venta_Model->articulos(array(
              "id_empresa"=>$id_empresa,
              "id_articulo"=>$art->id,
              "desde"=>$desde,
              "hasta"=>$hasta,
              "anulada"=>-1,
              "in_tipos_estados"=>"4,5,6,8,9,10",
            ));
            if (sizeof($ventas["results"])>0) {
              $venta = $ventas["results"][0];
              $obj->venta = $venta->cantidad;
            } else {
              $obj->venta = 0;
            }

            $obj->consultas = $this->Consulta_Model->contar(array(
              "id_referencia"=>$id,
              "id_empresa"=>$id_empresa,
              "tipo"=>0,
              "desde"=>$desde,
              "hasta"=>$hasta,
            ));

            $salida[] = $obj;
          }
        }
      }
      echo json_encode($salida);
    } catch(Exception $e) {
      parent::send_error($e->getMessage());
    }      
  }


  function publicidades() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);
    if (empty($conf->view_id)) {
      parent::send_error("ERROR: No esta configurada correctamente la vista de Analytics.");
      exit();
    }
    $view_id = "ga:".$conf->view_id;

    if (isset($conf->estadisticas_factor)) {
      $factor = ($conf->estadisticas_factor + 100) / 100;
    } else $factor = 1;

    $this->load->helper("fecha_helper");
    $id_cliente = parent::get_post("id_cliente");
    $desde = fecha_mysql(parent::get_post("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_post("hasta",date("d/m/Y")));
    
    $entradas = array();
    $videos = array();
    $total_paginas = 0;
    $total_videos = 0;
    $total_clicks = 0;
    $total_qr = 0;

    $this->load->model("Entrada_Model");
    $this->load->model("Not_Video_Model");
    try {
      
      $service = $this->get_ga_service();

      // PAGINAS MAS VISTAS
      $results = $service->data_ga->get($view_id,$desde,$hasta,'ga:pageviews',array(
        "sort" => "-ga:pageviews",
        "dimensions" => "ga:pagePath"
      ));
      if (count($results->getRows()) > 0) {
        foreach ($results->getRows() as $r) {
          $url_base = $r[0];
          // Si comienza con el string
          $query = "/entrada/";
          if (substr($url_base, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url_base);
            $id = substr($url, strrpos($url, "-")+1);
            $id = str_replace("/", "", $id);
            $obj = new stdClass();
            $obj->id = $id;
            $obj->visitas = round((int)$r[1] * $factor,0);
            $obj->link = $url_base;

            // Consultamos por la entrada
            $ent = $this->Entrada_Model->get($id,array(
              "id_empresa"=>$id_empresa,
              "id_cliente"=>$id_cliente,
              "min"=>1,
            ));
            if (empty($ent)) {
              // No se encuentra el articulo
              continue;
            }
            $obj->nombre = $ent->titulo;
            $entradas[] = $obj;
            $total_paginas += $obj->visitas;
          }

          // Si comienza con /web/video/
          $query = "/web/video/";
          if (substr($url_base, 0, strlen($query)) === $query) {
            $url = str_replace($query."?id=", "", $url_base);
            if (strpos($url, "&")>0) $url = substr($url, 0, strpos($url,"&")-1);
            if (is_numeric($url)) {
              $id = (int)$url;
              // Consultamos por el video
              $ent = $this->Not_Video_Model->get($id,array(
                "id_empresa"=>$id_empresa,
                "id_cliente"=>$id_cliente,
              ));
              if (empty($ent)) {
                // No se encuentra el articulo
                continue;
              }
              $obj = new stdClass();
              $obj->id = $id;
              $obj->visitas = round((int)$r[1] * $factor,0);
              $obj->nombre = $ent->titulo;
              $obj->link = $url_base;
              $total_videos += $obj->visitas;

              // Controlamos si no fue ingresado antes
              $encontro = false;
              foreach($videos as $v) {
                if ($v->id == $obj->id) {
                  $v->visitas = $v->visitas + $obj->visitas;
                  $encontro = true;
                  break;
                }
              }
              if (!$encontro) $videos[] = $obj;
            }
          }

          // Los videos tambien se pueden ver desde la pagina de empresas, y tienen el parametro id_video
          $query = "/web/company/";
          if (substr($url_base, 0, strlen($query)) === $query) {
            $url = str_replace($query, "", $url_base);
            parse_str($url,$pars);
            if (isset($pars["id_video"])) {
              $id = $pars["id_video"];
              // Consultamos por el video
              $ent = $this->Not_Video_Model->get($id,array(
                "id_empresa"=>$id_empresa,
                "id_cliente"=>$id_cliente,
              ));
              if (empty($ent)) {
                // No se encuentra el articulo
                continue;
              }
              $obj = new stdClass();
              $obj->id = $id;
              $obj->visitas = round((int)$r[1] * $factor,0);
              $obj->nombre = $ent->titulo;
              $obj->link = $url_base;
              $total_videos += $obj->visitas;

              // Controlamos si no fue ingresado antes
              $encontro = false;
              foreach($videos as $v) {
                if ($v->id == $obj->id) {
                  $v->visitas = $v->visitas + $obj->visitas;
                  $encontro = true;
                  break;
                }
              }
              if (!$encontro) $videos[] = $obj;
            }
          }

        }
      }

      // PUBLICIDADES
      $piezas = array();
      $total_publicidades = 0;
      $results = $service->data_ga->get($view_id,$desde,$hasta,'ga:totalEvents',array(
        "sort" => "-ga:totalEvents",
        "dimensions" => "ga:eventAction,ga:eventCategory"
      ));
      if (count($results->getRows()) > 0) {
        foreach ($results->getRows() as $r) {
          $id_pieza = $r[0];
          $categoria = $r[1]; // La categoria es Publicidad o Click

          if (is_numeric($id_pieza)) {
            $sql = "SELECT PP.* FROM pub_piezas PP INNER JOIN pub_campanias PC ON (PP.id_empresa = PC.id_empresa AND PP.id_campania = PC.id) ";
            $sql.= "WHERE PP.id = $id_pieza AND PP.id_empresa = $id_empresa ";
            $sql.= "AND PC.id_cliente = $id_cliente ";
            $q = $this->db->query($sql);
            if ($q->num_rows()>0) { 
              $rr = $q->row();
              $obj = new stdClass();
              $obj->id = $rr->id;
              $obj->nombre = $rr->nombre;
              $obj->visitas = ($categoria == "Publicidad") ? round((int)$r[2] * $factor,0) : 0;
              $obj->clicks = ($categoria == "Click") ? round((int)$r[2] * $factor,0) : 0;
              $obj->link = $rr->link;

              // Buscamos si ya fue ingresado al array de salida
              $encontro = false;
              foreach($piezas as $v) {
                if ($v->id == $obj->id) {
                  $v->visitas = $v->visitas + $obj->visitas;
                  $v->clicks = $v->clicks + $obj->clicks;
                  $encontro = true;
                  break;
                }
              }
              if (!$encontro) $piezas[] = $obj;
              $total_publicidades += $obj->visitas;
              $total_clicks += $obj->clicks;
            }
          }

        }
      }

      // TOTAL CODIGOS QR
      $sql = "SELECT COUNT(*) AS visitas, link AS nombre, link ";
      $sql.= "FROM qr_click_links ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_cliente = $id_cliente ";
      $sql.= "GROUP BY link ";
      $q = $this->db->query($sql);
      $qr_links = array();
      foreach($q->result() as $row) {
        $row->visitas = round((int)$row->visitas * $factor,0);
        $qr_links[] = $row;
        $total_qr += $row->visitas;
      }

      echo json_encode(array(
        "paginas"=>$entradas,
        "videos"=>$videos,
        "piezas"=>$piezas,
        "qr_links"=>$qr_links,
        "total_clicks"=>$total_clicks,
        "total_publicidades"=>$total_publicidades,
        "total_paginas"=>$total_paginas,
        "total_videos"=>$total_videos,
        "total_qr"=>$total_qr,
        "promedio_por_dia"=>0,
        "promedio_clicks_por_dia"=>0,
      ));
    } catch(Exception $e) {
      parent::send_error($e->getMessage());
    }      
  }

}