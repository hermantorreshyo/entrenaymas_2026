<?php
class Reserva_Model {

  private $id_empresa = 0;
  private $conx = null;

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  function get_paypal() {
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_paypal == 0 || empty($medio->paypal_email)) return FALSE;
    return $medio->paypal_email;
  }

  function get_mercadopago($numero_carrito = 0) {

    require_once("mercadopago.php");

    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    $medio = mysqli_fetch_object($q);
    if ($medio->habilitar_mp == 0) return FALSE;

    // La configuracion de las dos cuentas esta separada por ;;;
    // Dependiendo del carrito, tomamos una u otra configuracion
    $clients_id = explode(";;;", $medio->mp_client_id);
    $clients_secret = explode(";;;", $medio->mp_client_secret);

    // Dependiendo de cual carrito estamos haciendo el checkout
    $mp_client_id = trim($clients_id[$numero_carrito]);
    $mp_client_secret = trim($clients_secret[$numero_carrito]);
    if (empty($mp_client_id) || empty($mp_client_secret)) return FALSE; // No fue configurado aun
    return new MP($mp_client_id, $mp_client_secret);
  }

  // Obtenemos la reserva
  function get_reserva($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT H.*, ";
    $sql.= " IF(H.fecha_desde = '0000-00-00','',DATE_FORMAT(H.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(H.fecha_hasta = '0000-00-00','',DATE_FORMAT(H.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM hot_reservas H ";
    $sql.= "WHERE H.id_empresa = $id_empresa ";
    $sql.= "AND H.id = $id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) {
      return FALSE;
    }
    $row = mysqli_fetch_object($q);

    // Obtenemos los datos del cliente
    $cliente = new stdClass();
    $cliente->nombre = "";
    $cliente->email = "";
    $row->cliente = $cliente;
    $sql = "SELECT * FROM clientes WHERE id_empresa = $this->id_empresa AND id = $row->id_cliente ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $cliente = mysqli_fetch_object($q);
      $row->cliente = $cliente;
    }

    return $row;
  }

  // Obtenemos los datos del entrada
  function get($id,$config = array()) {

    // Estos parametros se pueden deshabilitar para ganar velocidad, ya que no tiene sentido a veces cargarlos
    $buscar_imagenes = isset($config["buscar_imagenes"]) ? $config["buscar_imagenes"] : 1;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "A.nombre ASC ";
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 6;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;

    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM hot_tipos_habitaciones A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($not_id > 0) $sql.= "AND A.id != $not_id ";
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";

    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q) == 0) return array();
    $entrada = mysqli_fetch_object($q);
    $entrada = $this->encoding($entrada);

    $entrada->images = array();
    if ($buscar_imagenes == 1) {
      // Obtenemos las imagenes de ese entrada
      $sql = "SELECT AI.* FROM hot_tipos_habitaciones_images AI WHERE AI.id_tipo_habitacion = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
      $q = mysqli_query($this->conx,$sql);
      while(($r=mysqli_fetch_object($q))!==NULL) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
        $entrada->images[] = $r->path;
      }
    }
    // Link de la imagen
    $entrada->path = ((strpos($entrada->path,"http://")===FALSE)) ? "/sistema/".$entrada->path : $entrada->path;
    return $entrada;
  }

  private function encoding($e) {
    $e->texto = mb_convert_encoding($e->texto, 'UTF-8', 'ISO-8859-1');
    $e->plain_text = str_replace("\n", "", strip_tags(html_entity_decode($e->texto,ENT_QUOTES)));
    $e->nombre = mb_convert_encoding($e->nombre, 'UTF-8', 'ISO-8859-1');
    return $e;
  }

	function get_tipos_habitaciones($conf = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    if (session_status() == PHP_SESSION_NONE) {
		  @session_start();
    }
		$desde = isset($conf["desde"]) ? $conf["desde"] : "";
		$hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $moneda = isset($conf["moneda"]) ? $conf["moneda"] : "$"; // La moneda con la que se deben mostrar los precios
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 0;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "";
    $id = isset($conf["id"]) ? $conf["id"] : 0;
		$personas = isset($conf["personas"]) ? $conf["personas"] : 0;
    $buscar_disponibilidad = isset($conf["buscar_disponibilidad"]) ? $conf["buscar_disponibilidad"] : 0;

		$sql = "SELECT SQL_CALC_FOUND_ROWS *, ";
		$language = (isset($_SESSION["language"])) ? $_SESSION["language"] : "";
		if ($language == "en") $sql.= "texto_en AS texto, nombre_en AS nombre, caracteristicas_en AS caracteristicas ";
		else if ($language == "pt") $sql.= "texto_pt AS texto, nombre_pt AS nombre, caracteristicas_pt AS caracteristicas ";
		else $sql.= "texto, nombre, caracteristicas ";
		$sql.= "FROM hot_tipos_habitaciones ";
		$sql.= "WHERE id_empresa = $this->id_empresa ";
    if ($id > 0) $sql.= "AND id = $id ";
		if (!empty($personas)) $sql.= "AND capacidad_maxima >= $personas ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by ";
    if ($offset != 0) $sql.= "LIMIT $limit,$offset ";
		$q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

		$salida = array();
		while(($r=mysqli_fetch_object($q))!==NULL) {

			// Separamos las caracteristicas
			if (!empty($r->caracteristicas)) {
				$caract = explode(";;;", $r->caracteristicas);
				$r->caracteristicas = $caract;
			}

			// Acomodamos la URL de la imagen
			if (!empty($r->path)) {
				$r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
			}

			$r->moneda = trim($r->moneda);
			$r->moneda = strtoupper($r->moneda);
			$cotizacion = 1;

			// Si los precios estan en otra moneda que no sea la que se tiene que mostrar
			if ($r->moneda != $moneda) {
				// TODO: Ver en que moneda por defecto usa la web
        if ($r->moneda == "U\$S") $r->moneda = "U\$D";
				$sql = "SELECT valor FROM cotizaciones WHERE moneda = '$r->moneda' ";
				$q_fact = mysqli_query($this->conx,$sql);
				if (mysqli_num_rows($q_fact)>0) {
					$fact = mysqli_fetch_object($q_fact);
					$cotizacion = $fact->valor;
				}
        $r->moneda = $moneda;
			}
			$r->disponibilidad = -1;
			$r->id_habitacion = 0;
			$r->cantidad_noches = 0;
      $r->precio_sin_descuento = $r->precio;
      $r->precio_por_noche = $r->precio;
      $r->precio_sin_descuento_por_noche = $r->precio_por_noche;

			// DEBEMOS CONTROLAR LA DISPONIBILIDAD O NO PARA LAS FECHAS SELECCIONADAS
			if (!empty($desde) || !empty($hasta)) {

				// La habitacion no es
				$r->disponibilidad = 0;

				// Primero vemos las habitaciones de ESE TIPO que tengan capacidad suficiente
				$sql = "SELECT * FROM hot_habitaciones ";
				$sql.= "WHERE id_empresa = $this->id_empresa ";
				$sql.= "AND id_tipo_habitacion = $r->id ";
				$sql.= "AND capacidad >= $personas ";
				$q_hab = mysqli_query($this->conx,$sql);
				while(($hab=mysqli_fetch_object($q_hab))!==NULL) {

          $r->id_habitacion = $hab->id;

		    	// Si algun dia de ese intervalo no hay disponibilidad
					$sql = "SELECT * ";
					$sql.= "FROM hot_disponibilidad ";
					$sql.= "WHERE disponible < '$personas' ";
					$sql.= "AND id_habitacion = $hab->id ";
					$sql.= "AND '$desde' <= fecha  ";
					$sql.= "AND fecha <= '$hasta' ";
          $sql.= "AND id_empresa = $this->id_empresa ";
					$q_disp = mysqli_query($this->conx,$sql);
					if (mysqli_num_rows($q_disp)<=0) {
						$r->disponibilidad = 1;
						$r->id_habitacion = $hab->id;
						break;
					}
				}


				// Si hay disponibilidad, tenemos que ver el precio de cada noche
				if ($r->disponibilidad == 1) {

					$precio_total = 0;

			    // Recorremos las fechas
			    $d = new DateTime($desde);
			    $h = new DateTime($hasta);
			    $interval = new DateInterval('P1D');
			    $range = new DatePeriod($d,$interval,$h);
					foreach($range as $fecha) {

						$f = $fecha->format("Y-m-d");

						// Primero controlamos si hay un precio especial por esa cantidad de personas
						$sql = "SELECT * FROM hot_precios ";
						$sql.= "WHERE id_empresa = $this->id_empresa ";
						$sql.= "AND fecha_desde <= '$f' ";
						$sql.= "AND '$f' <= fecha_hasta ";
            $sql.= "AND personas = '$personas' ";
						$sql.= "AND id_tipo_habitacion = $r->id ";
						$sql.= "ORDER BY promocion DESC ";
						$sql.= "LIMIT 0,1 ";
						$q_precio = mysqli_query($this->conx,$sql);
						if (mysqli_num_rows($q_precio)>0) {
							$r_precio = mysqli_fetch_object($q_precio);
							$precio_total += $r_precio->precio;
						} else {

              // Si no hay un precio que encaja exacto con la cantidad de personas
              // Buscamos un precio que sea para mas personas de las que necesita
              $sql = "SELECT * FROM hot_precios ";
              $sql.= "WHERE id_empresa = $this->id_empresa ";
              $sql.= "AND fecha_desde <= '$f' ";
              $sql.= "AND '$f' <= fecha_hasta ";
              $sql.= "AND personas > '$personas' ";
              $sql.= "AND id_tipo_habitacion = $r->id ";
              $sql.= "ORDER BY promocion DESC ";
              $sql.= "LIMIT 0,1 ";
              $q_precio = mysqli_query($this->conx,$sql);
              if (mysqli_num_rows($q_precio)>0) {
                $r_precio = mysqli_fetch_object($q_precio);
                $precio_total += $r_precio->precio;
              } else {
                $precio_total += $r->precio;
              }

						}
						
						$r->cantidad_noches++;
					}
          $r->precio_sin_descuento = ($r->cantidad_noches * $r->precio);
					$r->precio = round($precio_total,2);
          $r->precio_por_noche = (($r->cantidad_noches > 0) ? round($r->precio / $r->cantidad_noches,2) : 0);
          $r->precio_sin_descuento_por_noche = (($r->cantidad_noches > 0) ? round($r->precio_sin_descuento / $r->cantidad_noches,2) : 0);
				}

				/*
				// Primero calculamos cuantas habitaciones tenemos de ese tipo
				$sql = "SELECT H.* ";
				$sql.= "FROM hot_habitaciones H ";
				$sql.= "WHERE H.id_empresa = $this->id_empresa ";
				// Tomamos las habitaciones de ese tipo
				$sql.= " AND H.id_tipo_habitacion = $r->id "; 
				// Que no tengan una reserva hecha para esa fecha
				$sql.= " AND NOT EXISTS(SELECT * FROM hot_reservas R WHERE R.id_habitacion = H.id AND '$desde' <= R.fecha_hasta AND R.fecha_desde <= '$hasta') ";
				$q_hab = mysqli_query($this->conx,$sql);
				// Si hay al menos una habitacion, hay disponibilidad
				$r->disponibilidad = (mysqli_num_rows($q_hab)>0)?1:0;
				*/
			}

      $r->precio = $r->precio * $cotizacion;
      $r->precio_sin_descuento = $r->precio_sin_descuento * $cotizacion;
      $r->precio_por_noche = $r->precio_por_noche * $cotizacion;
      $r->precio_sin_descuento_por_noche = $r->precio_sin_descuento_por_noche * $cotizacion;
      $r->descuento = round($r->precio_sin_descuento - $r->precio,2);
      $r->porc_descuento = ($r->precio > 0) ? round(($r->descuento / $r->precio) * 100,2) : 0;

      if ($buscar_disponibilidad == 1 && $r->disponibilidad == 1) $salida[] = $r;
      else if ($buscar_disponibilidad == 0) $salida[] = $r;
		}
		return $salida;
	}

  function get_total_results() {
    return $this->total;
  }

}
?>