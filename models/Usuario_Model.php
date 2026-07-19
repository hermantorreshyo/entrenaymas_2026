<?php
class Usuario_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;
  public $sql = "";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : mb_convert_encoding($r, 'UTF-8', 'ISO-8859-1'));
  }

  function get_total_results() {
    return $this->total;
  }

  // Obtenemos el usuario por el link
  function get_by_link($link,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT A.* ";
    $sql.= "FROM com_usuarios A ";
    $sql.= "WHERE A.apellido = '$link' ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) return array();
    $usuario = mysqli_fetch_object($q);
    return $this->get($usuario->id,$config);
  }

  // Obtenemos los datos del entrada
  function get($id,$config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $buscar_horarios = isset($config["buscar_horarios"]) ? $config["buscar_horarios"] : 0;

    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM com_usuarios A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";

    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q) == 0) return array();
    $usuario = mysqli_fetch_object($q);
    $usuario = $this->encoding($usuario);

    $usuario->solo_usuario = 0;
    $usuario->destacado = 0;
    $usuario->path_2 = "";
    $usuario->clave_especial = "";
    $usuario->instagram = "";
    $usuario->facebook = "";
    $usuario->linkedin = "";
    $usuario->custom_1 = "";
    $usuario->custom_2 = "";
    $usuario->custom_3 = "";
    $usuario->custom_4 = "";
    $usuario->custom_5 = "";
    $usuario->custom_6 = "";
    $usuario->custom_7 = "";
    $usuario->custom_8 = "";
    $usuario->custom_9 = "";
    $usuario->custom_10 = "";
    $usuario->id_localidad = 0;
    $usuario->localidad = "";
    $usuario->id_provincia = 0;
    $usuario->provincia = "";
    $usuario->id_pais = 0;
    $usuario->pais = "";
    $usuario->latitud = 0;
    $usuario->longitud = 0;
    $usuario->promedio = 0;
    $sql = "SELECT * FROM com_usuarios_extension WHERE id_empresa = $usuario->id_empresa AND id_usuario = $usuario->id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $rr = mysqli_fetch_object($q);
      $usuario->solo_usuario = $rr->solo_usuario;
      $usuario->destacado = $rr->destacado;
      $usuario->path_2 = ((strpos($rr->path_2,"http")===0)) ? $rr->path_2 : "/sistema/".$rr->path_2;
      $usuario->clave_especial = $rr->clave_especial;
      $usuario->instagram = $rr->instagram;
      $usuario->facebook = $rr->facebook;
      $usuario->linkedin = $rr->linkedin;
      $usuario->custom_1 = $rr->custom_1;
      $usuario->custom_2 = $rr->custom_2;
      $usuario->custom_3 = $rr->custom_3;
      $usuario->custom_4 = $rr->custom_4;
      $usuario->custom_5 = $rr->custom_5;
      $usuario->custom_6 = $rr->custom_6;
      $usuario->custom_7 = $rr->custom_7;
      $usuario->custom_8 = $rr->custom_8;
      $usuario->custom_9 = $rr->custom_9;
      $usuario->custom_10 = $rr->custom_10;
      $usuario->latitud = $rr->latitud;
      $usuario->longitud = $rr->longitud;
      $usuario->id_localidad = $rr->id_localidad;
      $usuario->localidad = $rr->localidad;
      $usuario->id_provincia = $rr->id_provincia;
      $usuario->provincia = $rr->provincia;
      $usuario->id_pais = $rr->id_pais;
      $usuario->pais = $rr->pais;
      $usuario->promedio = $rr->promedio;
    }

    // Arreglamos algunos datos mal escritos de las cuentas
    if ((strpos($usuario->facebook, "http") === FALSE)) $usuario->facebook = "https://www.facebook.com/".$usuario->facebook;
    if ((strpos($usuario->instagram, "http") === FALSE)) $usuario->instagram = "https://www.instagram.com/".str_replace("@", "", $usuario->instagram);


    /*
    $usuario->images = array();
    if ($buscar_imagenes == 1) {
      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.* FROM veh_autos_images AI WHERE AI.id_auto = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
        $usuario->images[] = $r->path;
      }
    }
    */

    if ($buscar_horarios == 1) {
      $usuario->horarios = array();
      $usuario->horarios_entrega = array();
      // Obtenemos los horarios
      $sql = "SELECT AI.* ";
      $sql.= "FROM com_usuarios_horarios AI ";
      $sql.= "WHERE AI.id_usuario = $usuario->id AND AI.id_empresa = $usuario->id_empresa ORDER BY AI.dia ASC";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        if ($h->tipo == 1) $usuario->horarios_entrega[] = $h;
        else $usuario->horarios[] = $h;
      }
    }

    // Link de la imagen
    $usuario->path = (!empty($usuario->path)) ? (((strpos($usuario->path,"http")===0)) ? $usuario->path : "/sistema/".$usuario->path) : "";

    // PSICOWEB
    if ($this->id_empresa == 1245 || $this->id_empresa == 1319) {
      $usuario->obras_sociales = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_obras_sociales A INNER JOIN profesionales_obras_sociales P ON (A.id_empresa = P.id_empresa AND A.id = P.id_obra_social) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->obras_sociales[] = $h;
      }

      $usuario->especialidades = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_especialidades A INNER JOIN profesionales_especialidades P ON (A.id_empresa = P.id_empresa AND A.id = P.id_especialidad) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->especialidades[] = $h;
      }

      $usuario->categorias = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM toque_categorias A INNER JOIN toque_categorias_usuarios P ON (A.id_empresa = P.id_empresa AND A.id = P.id_categoria) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->categorias[] = $h;
      }      

      $usuario->tipos_pacientes = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_tipos_pacientes A INNER JOIN profesionales_tipos_pacientes P ON (A.id_empresa = P.id_empresa AND A.id = P.id_tipo_paciente) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->tipos_pacientes[] = $h;
      }

      $usuario->tipos_atenciones = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_tipos_atenciones A INNER JOIN profesionales_tipos_atenciones P ON (A.id_empresa = P.id_empresa AND A.id = P.id_tipo_atencion) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->tipos_atenciones[] = $h;
      }

      $usuario->tipos_terapias = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_tipos_terapias A INNER JOIN profesionales_tipos_terapias P ON (A.id_empresa = P.id_empresa AND A.id = P.id_tipo_terapia) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->tipos_terapias[] = $h;
      }

      $usuario->titulos = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_titulos A INNER JOIN profesionales_titulos P ON (A.id_empresa = P.id_empresa AND A.id = P.id_titulo) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->titulos[] = $h;
      }

      $usuario->formas_pago = array();
      $sql = "SELECT A.* ";
      $sql.= "FROM med_formas_pago A INNER JOIN profesionales_formas_pago P ON (A.id_empresa = P.id_empresa AND A.id = P.id_forma_pago) ";
      $sql.= "WHERE A.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY A.nombre ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->formas_pago[] = $h;
      }

      $usuario->localidades = array();
      $sql = "SELECT P.* ";
      $sql.= "FROM com_usuarios_localidades P ";
      $sql.= "WHERE P.id_empresa = $this->id_empresa ";
      $sql.= "AND P.id_usuario = $usuario->id ";
      $sql.= "ORDER BY P.localidad ASC ";
      $qq = mysqli_query($this->conx,$sql);
      while(($h = mysqli_fetch_object($qq))!==NULL) { 
        $usuario->localidades[] = $h;
      }
    }

    $usuario->direcciones = array();
    $sql = "SELECT A.* ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "WHERE A.id_empresa = $this->id_empresa ";
    $sql.= "AND A.id_usuario = $usuario->id ";
    $qq = mysqli_query($this->conx,$sql);
    while(($h = mysqli_fetch_object($qq))!==NULL) { 

      // Obtenemos los dias de ese turno
      $sql = "SELECT DISTINCT dia ";
      $sql.= "FROM turnos_servicios_horarios H ";
      $sql.= "WHERE H.id_empresa = $this->id_empresa AND H.id_servicio = $h->id ";
      $qq1 = mysqli_query($this->conx,$sql);
      $h->dias = array();
      while(($rrr = mysqli_fetch_object($qq1))!==NULL) { 
        $h->dias[] = $rrr->dia;
      }

      $h->nombre = ucfirst($h->nombre);
      $h->direccion = ucfirst($h->direccion);

      $usuario->direcciones[] = $h;
    }    

    // Obtenemos las imagenes de ese entrada
    $usuario->images = array();
    $sql = "SELECT AI.* FROM com_usuarios_images AI WHERE AI.id_usuario = $usuario->id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
        $usuario->images[] = $r->path;
      }
    }


    $fecha_desde = date("Y")."-".date("m")."-01 00:00:00";
    $fecha_hasta = date("Y-m-d H:i:s", strtotime($fecha_desde." +1 months"));

    //Verificamos si al usuario le consultaron al menos una vez este mes
    $usuario->tiene_consulta_mensual = 0;
    //Solo verificamos a los gratuitos
    if ($usuario->destacado == 0) {
      $sql = "SELECT CC.* ";
      $sql.= "FROM crm_consultas CC ";
      $sql.= "WHERE CC.id_empresa = '$id_empresa' ";
      $sql.= "AND CC.asunto = 'Contacto' ";
      $sql.= "AND CC.id_origen = '30' ";
      $sql.= "AND CC.id_usuario = '$usuario->id' ";
      $sql.= "AND '$fecha_desde' <= CC.fecha AND CC.fecha < '$fecha_hasta' ";
      $sql.= "ORDER BY CC.fecha ASC ";
      $sql.= "LIMIT 0,1 ";

      $q = mysqli_query($this->conx,$sql);
      if (mysqli_num_rows($q) > 0) $usuario->tiene_consulta_mensual = 1;
    }

    return $usuario;
  }


  function get_list($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $id_perfil = isset($config["id_perfil"]) ? $config["id_perfil"] : 0;
    $usar_get = isset($config["usar_get"]) ? $config["usar_get"] : 0;
    $id_localidad = isset($config["id_localidad"]) ? $config["id_localidad"] : 0;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.id DESC ";
    $obras_sociales = isset($config["obras_sociales"]) ? $config["obras_sociales"] : array();
    $categorias = isset($config["categorias"]) ? $config["categorias"] : array();
    $localidades = isset($config["localidades"]) ? $config["localidades"] : array();
    $tipos_pacientes = isset($config["tipos_pacientes"]) ? $config["tipos_pacientes"] : array();
    $tipos_terapias = isset($config["tipos_terapias"]) ? $config["tipos_terapias"] : array();
    $tipos_atenciones = isset($config["tipos_atenciones"]) ? $config["tipos_atenciones"] : array();
    $formas_pago = isset($config["formas_pago"]) ? $config["formas_pago"] : array();
    $tipos_perfil = isset($config["tipos_perfil"]) ? $config["tipos_perfil"] : array();
    $especialidades = isset($config["especialidades"]) ? $config["especialidades"] : array();
    $titulos = isset($config["titulos"]) ? $config["titulos"] : array();
    $in_ids = isset($config["in_ids"]) ? $config["in_ids"] : array();
    $not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    if ($this->id_empresa == 1319) {
      if (!empty($localidades)) {
        // Si se pasa una localidad, tenemos que seleccionar el presupuesto que el usuario le definio a esa localidad
        $id_loc = $localidades[0];
        $sql.= " (SELECT valor FROM com_usuarios_localidades UL WHERE UL.id_empresa = A.id_empresa AND UL.id_usuario = A.id AND UL.id_localidad = $id_loc LIMIT 0,1) AS valor_localidad, ";
      } else {
        // Si no se pasa una localidad, mostramos el maximo
        $sql.= " (SELECT IF(MAX(valor) IS NULL,0,MAX(valor)) AS valor FROM com_usuarios_localidades UL WHERE UL.id_empresa = A.id_empresa AND UL.id_usuario = A.id) AS valor_localidad, ";
      }
    }
    $sql.= " IF(E.solo_usuario IS NULL,0,E.solo_usuario) AS solo_usuario, ";
    $sql.= " IF(E.destacado IS NULL,0,E.destacado) AS destacado, ";
    $sql.= " IF(E.path_2 IS NULL,'',E.path_2) AS path_2, ";
    $sql.= " IF(E.clave_especial IS NULL,'',E.clave_especial) AS clave_especial, ";
    $sql.= " IF(E.instagram IS NULL,'',E.instagram) AS instagram, ";
    $sql.= " IF(E.facebook IS NULL,'',E.facebook) AS facebook, ";
    $sql.= " IF(E.linkedin IS NULL,'',E.linkedin) AS linkedin, ";
    $sql.= " IF(E.custom_1 IS NULL,'',E.custom_1) AS custom_1, ";
    $sql.= " IF(E.custom_2 IS NULL,'',E.custom_2) AS custom_2, ";
    $sql.= " IF(E.custom_3 IS NULL,'',E.custom_3) AS custom_3, ";
    $sql.= " IF(E.custom_4 IS NULL,'',E.custom_4) AS custom_4, ";
    $sql.= " IF(E.custom_5 IS NULL,'',E.custom_5) AS custom_5, ";
    $sql.= " IF(E.custom_6 IS NULL,'',E.custom_6) AS custom_6, ";
    $sql.= " IF(E.custom_7 IS NULL,'',E.custom_7) AS custom_7, ";
    $sql.= " IF(E.custom_8 IS NULL,'',E.custom_8) AS custom_8, ";
    $sql.= " IF(E.custom_9 IS NULL,'',E.custom_9) AS custom_9, ";
    $sql.= " IF(E.custom_10 IS NULL,'',E.custom_10) AS custom_10, ";
    $sql.= " IF(E.promedio IS NULL,0,E.promedio) AS promedio, ";
    $sql.= " IF(E.id_localidad IS NULL,0,E.id_localidad) AS id_localidad, ";
    $sql.= " IF(E.localidad IS NULL,'',E.localidad) AS localidad, ";
    $sql.= " IF(E.id_provincia IS NULL,0,E.id_provincia) AS id_provincia, ";
    $sql.= " IF(E.provincia IS NULL,'',E.provincia) AS provincia, ";
    $sql.= " IF(E.id_pais IS NULL,0,E.id_pais) AS id_pais, ";
    $sql.= " IF(E.pais IS NULL,'',E.pais) AS pais, ";
    $sql.= " IF(E.latitud IS NULL,0,E.latitud) AS latitud, ";
    $sql.= " IF(E.longitud IS NULL,0,E.longitud) AS longitud ";
    $sql.= "FROM com_usuarios A ";
    $sql.= "LEFT JOIN com_usuarios_extension E ON (A.id_empresa = E.id_empresa AND A.id = E.id_usuario) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $this->id_empresa ";
    $sql.= "AND A.id_perfiles IN (1357, 1358) ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    //if (!empty($id_perfil)) $sql.= "AND A.id_perfiles = $id_perfil ";
    if (!empty($id_localidad)) $sql.= "AND EXISTS (SELECT * FROM turnos_servicios TS WHERE A.id_empresa = TS.id_empresa AND A.id = TS.id_usuario AND TS.id_localidad = $id_localidad ) ";

    if (sizeof($obras_sociales)>0) {
      $os = implode(",", $obras_sociales);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_obras_sociales POS WHERE A.id_empresa = POS.id_empresa AND A.id = POS.id_usuario AND POS.id_obra_social IN ($os) ) ";
    }

    if (sizeof($titulos)>0) {
      $os = implode(",", $titulos);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_titulos TIT WHERE A.id_empresa = TIT.id_empresa AND A.id = TIT.id_usuario AND TIT.id_titulo IN ($os) ) ";
    }

    if (sizeof($tipos_pacientes)>0) {
      $os = implode(",", $tipos_pacientes);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_tipos_pacientes PTP WHERE A.id_empresa = PTP.id_empresa AND A.id = PTP.id_usuario AND PTP.id_tipo_paciente IN ($os) ) ";
    }

    if (sizeof($tipos_terapias)>0) {
      $os = implode(",", $tipos_terapias);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_tipos_terapias PTP WHERE A.id_empresa = PTP.id_empresa AND A.id = PTP.id_usuario AND PTP.id_tipo_terapia IN ($os) ) ";
    }

    if (sizeof($tipos_atenciones)>0) {
      $os = implode(",", $tipos_atenciones);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_tipos_atenciones PTP WHERE A.id_empresa = PTP.id_empresa AND A.id = PTP.id_usuario AND PTP.id_tipo_atencion IN ($os) ) ";
    }

    if (sizeof($formas_pago)>0) {
      $os = implode(",", $formas_pago);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_formas_pago PTP WHERE A.id_empresa = PTP.id_empresa AND A.id = PTP.id_usuario AND PTP.id_forma_pago IN ($os) ) ";
    }

    if (sizeof($especialidades)>0) {
      $os = implode(",", $especialidades);
      $sql.= "AND EXISTS (SELECT 1 FROM profesionales_especialidades PES WHERE A.id_empresa = PES.id_empresa AND A.id = PES.id_usuario AND PES.id_especialidad IN ($os) ) ";
    }

    if (sizeof($categorias)>0) {
      $os = implode(",", $categorias);
      $sql.= "AND EXISTS (SELECT 1 FROM toque_categorias_usuarios CAT WHERE A.id_empresa = CAT.id_empresa AND A.id = CAT.id_usuario AND CAT.id_categoria IN ($os) ) ";
    }

    if (sizeof($tipos_perfil) > 0) {
      if (in_array("2", $tipos_perfil)) {
        //Buscamos todos 
        $sql.= "AND E.destacado IN (0,1) ";
      } else {
        $sql.= "AND E.destacado IN (1) ";
      }
    }

    if (sizeof($localidades)>0) {
      $locs = implode(",", $localidades);
      if ($this->id_empresa == 1319) {
        $sql.= "AND EXISTS (SELECT * FROM com_usuarios_localidades UL WHERE A.id_empresa = UL.id_empresa AND A.id = UL.id_usuario AND UL.id_localidad IN ($locs) ) ";
      } else {
        $sql.= "AND EXISTS (SELECT * FROM turnos_servicios PTS WHERE A.id_empresa = PTS.id_empresa AND A.id = PTS.id_usuario AND PTS.id_localidad IN ($locs) ) ";  
      }
    }

    if (sizeof($in_ids)>0) {
      $s = array();
      foreach($in_ids as $ss) {
        if (!empty($ss)) $s[] = $ss;
      }
      $in_ids = implode(",", $s);
      $sql.= "AND A.id IN ($in_ids) ";
    }

    if (isset($not_ids) && !empty($not_ids)) {
      $not_ids = str_replace("-",",",$not_ids);
      $sql.= "AND A.id NOT IN ($not_ids) ";
    }

    if ($this->id_empresa == 1245) {
      $sql.= "ORDER BY E.destacado DESC, RAND() ASC ";
    } else if ($this->id_empresa == 1319) {
      // Si el saldo es mayor al maximo, es como que vuelve a 0
      // sino, ordenamos por el valor de la localidad descendiente

      if ($order_by == "RAND() ASC") $sql.= "ORDER BY E.destacado DESC, $order_by ";
      else $sql.= "ORDER BY A.puntaje DESC ";
      //$sql.= "ORDER BY IF(E.gasto_real > E.maximo,0,valor_localidad) DESC ";

    } else {
      $sql.= "ORDER BY $order_by ";  
    }
    $sql.= "LIMIT $limit,$offset ";


    $this->sql = $sql;

    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    if ($q === FALSE) return $salida;

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    while(($r=mysqli_fetch_object($q))!==NULL) {
      if ($this->id_empresa == 1319) $valor_localidad = $r->valor_localidad;
      if ($usar_get == 1) {
        $r = $this->get($r->id);
      } else {
        $r = $this->encoding($r);
        $r->path = (!empty($r->path)) ? (((strpos($r->path,"http")===0)) ? $r->path : "/sistema/".$r->path) : "";        
      }
      if ($this->id_empresa == 1319) $r->valor_localidad = $valor_localidad;
      $salida[] = $r;
    }
    return $salida;
  }

  private function encoding($e) {
    $e->nombre = $this->encod($e->nombre);
    $e->cargo = $this->encod($e->cargo);
    return $e;
  }

}
?>