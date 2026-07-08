<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Repartidor_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("repartidores","id","nombre ASC");
  }

  // Devuelve la cantidad de repartidores activos
  function cantidad_activos($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM repartidores F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.activo = 1 ";
    $q_repartidores = $this->db->query($sql);
    $r_repartidores = $q_repartidores->row();
    return $r_repartidores->cantidad;  
  }

  // Indica si un repartidor esta libre o no
  function esta_libre($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $id_repartidor = (isset($config["id_repartidor"])) ? $config["id_repartidor"] : 0;
    $hoy = date("Y-m-d");
    $sql = "SELECT 1 FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_vendedor = $id_repartidor ";
    $sql.= "AND F.id_tipo_estado >=1 AND F.id_tipo_estado < 6 AND F.fecha = '$hoy' ";
    $q = $this->db->query($sql);
    return ($q->num_rows() == 0);
  }

  // Devuelve la cantidad de repartidores libres (activos y sin pedidos)
  function cantidad_libres($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->id_empresa;
    $efectivo = (isset($config["efectivo"])) ? $config["efectivo"] : 0;
    $id_pedido = (isset($config["id_pedido"])) ? $config["id_pedido"] : 0; // Para el log
    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $hoy = date("Y-m-d");
    $sql = "SELECT SQL_CALC_FOUND_ROWS R.* ";
    $sql.= "FROM repartidores R ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    $sql.= "AND R.token != '' "; // Tiene que tener token activo dentro de la plataforma
    $sql.= "AND R.activo = 1 "; // Y que este activo
    $sql.= "AND NOT EXISTS (SELECT 1 FROM facturas F WHERE F.id_empresa = R.id_empresa AND F.id_vendedor = R.id AND F.id_tipo_estado >=1 AND F.id_tipo_estado < 6 AND F.fecha = '$hoy') ";
    $sql.= "ORDER BY R.fecha_ultima_entrega ASC ";
    $q_repartidores = $this->db->query($sql);
    $cantidad = 0;
    $salida = array();
    foreach($q_repartidores->result() as $repartidor) {
      // Si se mando un parametro de efectivo, calculamos el saldo del repartidor y controlamos si se llega al limite
      if ($efectivo > 0 && $repartidor->limite_efectivo > 0) {
        $saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
          "id_empresa"=>$id_empresa,
          "id_repartidor"=>$repartidor->id,
        ));
        if ( ($saldo + $efectivo) > $repartidor->limite_efectivo ) {
          // TODO: MANDAR UNA NOTIFICACION PARA AVISARLE QUE DEPOSITE
          $linea = "ATENCION: $repartidor->nombre tiene $ $saldo de efectivo. El pedido de $ $efectivo supera el limite de $ $repartidor->limite_efectivo.";
          file_put_contents("logs/$id_empresa/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);
          continue;
        }
      }
      $cantidad++;
      $salida[] = $repartidor;
    }
    return array(
      "cantidad"=>$cantidad,
      "results"=>$salida,
    );
  }

  function save($data) {
    $id = parent::save($data);
    $this->load->model("Empresa_Model");
    if ($this->Empresa_Model->es_toque($data->id_empresa)) {
      $usuario = new stdClass();
      $usuario->id = $id; // TODO: Esto podria generar conflicto de ID mas adelante
      $usuario->nombre = $data->nombre;
      $usuario->email = $data->email;
      $usuario->telefono = $data->telefono;
      $usuario->password = md5($data->password);
      $usuario->id_perfiles = 1393;
      $usuario->activo = $data->activo;
      $usuario->id_empresa = $data->id_empresa;
      $usuario->path = $data->path;
      $usuario->estado_inicial = 1;
      $sql = "SELECT * FROM com_usuarios WHERE id_empresa = $data->id_empresa AND id = $id";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0)  {
        $this->db->insert("com_usuarios",$usuario);
      } else {
        $sql = "UPDATE com_usuarios SET ";
        $sql.= " nombre = '$data->nombre', ";
        $sql.= " email = '$data->email', ";
        $sql.= " telefono = '$data->telefono', ";
        $sql.= " password = '".md5($data->password)."', ";
        $sql.= " id_perfiles = 1393, ";
        $sql.= " activo = '$data->activo', ";
        $sql.= " id_empresa = '$data->id_empresa', ";
        $sql.= " path = '$data->path', ";
        $sql.= " estado_inicial = 1 ";
        $sql.= "WHERE id_empresa = $data->id_empresa AND id = $id ";
        $this->db->query($sql);
      }
    }
    return $id;
  }

  function comparar_tiempos($a,$b) {
    if ($a->libre > $b->libre) return 1;
    else if ($a->libre < $b->libre) return -1;
    else {
      if ($a->tiempo_en_camino > $b->tiempo_en_camino) return 1;
      else if ($a->tiempo_en_camino < $b->tiempo_en_camino) return -1;
      else return 0;
    }
  }

  function asignar_pedido($config = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = 571;
    $id_pedido_solicitado = isset($config["id_pedido_solicitado"]) ? $config["id_pedido_solicitado"] : 0;
    $pedido_solicitado = FALSE;
    $test = isset($config["test"]) ? $config["test"] : 1;
    include_once("/home/ubuntu/data/sistema/application/helpers/coord_helper.php");

    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $this->load->model("Web_Configuracion_Model");
    $empresa = $this->Web_Configuracion_Model->get($id_empresa);
    $metros_por_minuto_default = (isset($empresa->texto_staff) && !empty($empresa->texto_staff) && is_numeric($empresa->texto_staff)) ? ((float)$empresa->texto_staff) : 333;

    if ($id_pedido_solicitado != 0) {
      $sql = "SELECT F.id, F.numero, F.id_punto_venta, U.titulo, C.latitud, C.longitud ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN com_usuarios U ON (U.id_empresa = F.id_empresa AND U.id = F.id_usuario) ";
      $sql.= "INNER JOIN clientes C ON (C.id_empresa = F.id_empresa AND C.id = F.id_cliente) ";
      $sql.= "WHERE F.id_empresa = $id_empresa ";
      $sql.= "AND F.id_tipo_estado = 1 "; // ACEPTADO POR EL COMERCIO
      //$sql.= "AND F.codigo_postal != '' "; // Que el pedido ESTE LISTO
      $sql.= "AND F.anulada = 0 "; // No fue anulada
      $sql.= "AND F.custom_2 != '' ";
      $sql.= "AND F.id_punto_venta != 2444 ";
      $sql.= "AND F.id_vendedor = 0 "; // No tiene que tener algo asignado ya
      $sql.= "AND F.numero_envio != 'pickup' "; // Que no sea pickup
      $sql.= "AND F.id = $id_pedido_solicitado ";
      $q_pedidos = $this->db->query($sql);
      if ($q_pedidos->num_rows() == 0) return FALSE;
      $pedido_solicitado = $q_pedidos->row();
    }    

    // Tomamos los repartidores que terminaron o van a terminar
    $sql = "SELECT R.* ";
    $sql.= "FROM repartidores R ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    $sql.= "AND R.token != '' "; // Tiene que tener token activo dentro de la plataforma
    $sql.= "AND R.activo = 1 "; // Y que este activo
    $sql.= "AND NOT EXISTS (SELECT 1 FROM facturas F WHERE F.id_empresa = R.id_empresa AND F.id_vendedor = R.id AND F.id_tipo_estado >=1 AND F.id_tipo_estado < 4) ";
    $q_repartidor = $this->db->query($sql);
    $repartidores = array();
    foreach($q_repartidor->result() as $repartidor) {
      // Tomamos solamente aquellos que estan por terminar o terminaron
      // Para eso tenemos que consultar el estado del ultimo pedido que sea F o L

      // Obtenemos las posiciones finales de donde va a estar el repartidor (la latitud y longitud del cliente)
      $sql = "SELECT C.latitud, C.longitud, RP.estado, U.titulo ";
      $sql.= "FROM repartidores_pedidos RP ";
      $sql.= "INNER JOIN facturas F ON (RP.id_factura = F.id AND RP.id_punto_venta = F.id_punto_venta AND RP.id_empresa = F.id_empresa) ";
      $sql.= "INNER JOIN clientes C ON (C.id_empresa = F.id_empresa AND C.id = F.id_cliente) ";
      $sql.= "INNER JOIN com_usuarios U ON (U.id_empresa = F.id_empresa AND U.id = F.id_usuario) ";
      $sql.= "WHERE RP.id_empresa = $id_empresa ";
      $sql.= "AND RP.id_repartidor = $repartidor->id ";
      $sql.= "ORDER BY RP.fecha DESC ";
      $sql.= "LIMIT 0,1 ";
      $q_repartidor_2 = $this->db->query($sql);
      if ($q_repartidor_2->num_rows() > 0) {
        $rr = $q_repartidor_2->row();
        $o = new stdClass();
        $o->tiempo_en_camino = 0;

        if ($test == 1) {
          echo $repartidor->nombre." (".$rr->estado.") ".$repartidor->id."<br/>";
        }

        // Estado de los pedidos:
        // F = Finalizado (lo ultimo que hizo es finalizar, ya esta libre)
        // C = Calculado (deprecado)
        // L = En camino al cliente
        // R = Rechazado (lo ultimo que hizo es rechazar, pero por ahora lo seguimos mandando porque esta activo)

        // Si el estado del ultimo pedido no es Finalizado (o sea que ya esta libre) o En Camino, lo ignoramos porque estan ocupados
        if ($rr->estado != "L" && $rr->estado != "F" && $rr->estado != "C" && $rr->estado != "R") continue;

        // Si tenemos que controlar el efectivo que tiene el repartidor
        // Primero obtenemos el saldo
        if ($pedido_solicitado !== FALSE) {
          $efectivo = ($pedido_solicitado->efectivo - $pedido_solicitado->vuelto);
          if ($efectivo > 0 && $repartidor->limite_efectivo > 0) {
            $saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
              "id_empresa"=>$id_empresa,
              "id_repartidor"=>$repartidor->id,
            ));
            if ( ($saldo + $efectivo) > $repartidor->limite_efectivo ) {
              // TODO: MANDAR UNA NOTIFICACION PARA AVISARLE QUE DEPOSITE
              $linea = "$repartidor->nombre tiene $ $saldo de efectivo. El pedido de $ $efectivo supera el limite de $ $repartidor->limite_efectivo.";
              if ($test == 1) {
                echo $linea;
              }
              //file_put_contents("logs/571/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);
              continue;
            }
          }
        }

        // Es en CAMINO
        if ($rr->estado == "L" || $rr->estado == "C") {
          // Tomamos la posicion del cliente
          $o->latitud = $rr->latitud;
          $o->longitud = $rr->longitud;          

          // Calculamos el tiempo que tarda el comercio al cliente que esta yendo en camino
          $coor_comercio = explode(";", $rr->titulo);
          $latitud_comercio = (float)$coor_comercio[0];
          $longitud_comercio = (float)$coor_comercio[1];
          $distancia = distance($latitud_comercio,$longitud_comercio,$rr->latitud,$rr->longitud) * 1000; // Esta en KM, pasarlo a Mtrs
          $metros_por_minuto = ($repartidor->metros_por_minuto != 0) ? $repartidor->metros_por_minuto : $metros_por_minuto_default;
          $o->tiempo_en_camino = round($distancia / $metros_por_minuto,0);
          $o->libre = ($rr->estado == "L") ? 0 : 1;

        // FINALIZADO
        } else if ($rr->estado == "F" || $rr->estado == "R") {
          $o->libre = 1;
          // Tomamos la posicion actual del repartidor
          $sql = "SELECT * FROM repartidores_posiciones WHERE id_empresa = $id_empresa AND id_repartidor = $repartidor->id ORDER BY tiempo DESC LIMIT 0,1 ";
          $q_posicion = $this->db->query($sql);
          if ($q_posicion->num_rows() > 0) {
            $posicion = $q_posicion->row();
            $o->latitud = $posicion->latitud;
            $o->longitud = $posicion->longitud;
          } else {
            // Sino tomamos al menos la posicion del cliente
            $o->latitud = $rr->latitud;
            $o->longitud = $rr->longitud;
          }
        }
        $o->estado = $rr->estado;
        $o->id = $repartidor->id;
        $o->nombre = $repartidor->nombre;
        $o->metros_por_minuto = $repartidor->metros_por_minuto;
        $repartidores[] = $o;
      }
    }

    // Ordenamos el array por el tiempo en camino
    usort($repartidores, array($this, "comparar_tiempos"));

    // Tomamos 3 repartidores
    if (sizeof($repartidores)>3) $repartidores = array_slice($repartidores, 0, 3);

    $pedidos = array();
    $matriz_tiempos = array();

    $offset = 3;
    $comparacion_pedidos = array();
    if ($id_pedido_solicitado != 0) {
      // Si estamos consultando la funcion por un pedido en particular, lo agregamos primero a la comparacion de pedidos
      $offset = 2;
      $comparacion_pedidos[] = $pedido_solicitado;
    }

    // Tomamos los proximos 2 pedidos que van a estar listos
    $sql = "SELECT F.id, F.numero, F.id_punto_venta, U.titulo, C.latitud, C.longitud ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN com_usuarios U ON (U.id_empresa = F.id_empresa AND U.id = F.id_usuario) ";
    $sql.= "INNER JOIN clientes C ON (C.id_empresa = F.id_empresa AND C.id = F.id_cliente) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_tipo_estado = 1 "; // ACEPTADO POR EL COMERCIO
    //$sql.= "AND F.codigo_postal != '' "; // Que el pedido ESTE LISTO
    $sql.= "AND F.anulada = 0 "; // No fue anulada
    $sql.= "AND F.custom_2 != '' ";
    $sql.= "AND F.id_punto_venta != 2444 ";
    $sql.= "AND F.id_vendedor = 0 "; // No tiene que tener algo asignado ya
    $sql.= "AND F.numero_envio != 'pickup' "; // Que no sea pickup
    $sql.= "AND F.id != $id_pedido_solicitado ";
    $sql.= "ORDER BY (IF(F.codigo_postal != '',0,10000) + TIMEDIFF(F.custom_2,NOW())) ASC ";
    $sql.= "LIMIT 0,$offset ";
    $q_pedidos = $this->db->query($sql);
    if ($q_pedidos->num_rows() == 0) return FALSE;
    foreach($q_pedidos->result() as $pedido) $comparacion_pedidos[] = $pedido;

    $i=0;
    foreach($comparacion_pedidos as $pedido) {

      $coor_comercio = explode(";", $pedido->titulo);
      $pedido->latitud_comercio = (float)$coor_comercio[0];
      $pedido->longitud_comercio = (float)$coor_comercio[1];
      $pedido->repartidores = array();

      $pedidos[] = $pedido;

      // Recorremos los repartidores y simulamos cuanto tardaria cada uno en ir a buscar ese pedido
      $j=0;
      foreach($repartidores as $repartidor) {

        $metros_por_minuto = ($repartidor->metros_por_minuto != 0) ? $repartidor->metros_por_minuto : $metros_por_minuto_default;

        // Distancia y tiempo para ir hasta el comercio
        $distancia_comercio = distance($pedido->latitud_comercio,$pedido->longitud_comercio,$repartidor->latitud,$repartidor->longitud) * 1000; // Esta en KM, pasarlo a Mtrs
        $tiempo_recorrido_comercio = round($distancia_comercio / $metros_por_minuto,0);

        // Distancia y tiempo para ir hasta el cliente
        $distancia_cliente = distance($pedido->latitud_comercio,$pedido->longitud_comercio,$pedido->latitud,$pedido->longitud) * 1000; // Esta en KM, pasarlo a Mtrs
        $tiempo_recorrido_cliente = round($distancia_cliente / $metros_por_minuto,0);

        // El tiempo total del pedido es la suma de los dos tiempos, mas el tiempo en camino
        $tiempo_recorrido = $tiempo_recorrido_comercio + $tiempo_recorrido_cliente + $repartidor->tiempo_en_camino;

        $obj = new stdClass();
        $obj->tiempo = $tiempo_recorrido;
        $obj->id_pedido = $pedido->id;
        $obj->id_repartidor = $repartidor->id;
        $obj->repartidor = $repartidor->nombre;
        $obj->pedido = $pedido->numero;
        $matriz_tiempos[$i][$j] = $obj;
        $j++;
      }
      $i++;
    }

    // Mostramos la matriz
    if ($test == 1) {
      echo "<table border=1>";
      echo "<tr><td></td>";
      foreach ($pedidos as $pedido) {
        echo "<td>$pedido->numero<br/>$pedido->id</td>";
      }
      echo "</tr>";
      $j=0;
      foreach($repartidores as $repartidor) {
        echo "<tr>";
        echo "<td>$repartidor->nombre</td>";
        $i=0;
        foreach($pedidos as $pedido) {
          echo "<td>";
          
          $url = "https://maps.googleapis.com/maps/api/staticmap?key=AIzaSyAXpROdHVy8YYxLeemEyR1hVDCRTL_4UdE&zoom=16&size=800x600&center=".$repartidor->latitud.",".$repartidor->longitud;
          $url.= "&markers=".$repartidor->latitud.",".$repartidor->longitud;
          echo "<a target='_blank' href='$url'>Repartidor $repartidor->nombre </a><br/>";

          $url = "https://maps.googleapis.com/maps/api/staticmap?key=AIzaSyAXpROdHVy8YYxLeemEyR1hVDCRTL_4UdE&zoom=16&size=800x600&center=".$pedido->latitud_comercio.",".$pedido->longitud_comercio;
          $url.= "&markers=".$pedido->latitud_comercio.",".$pedido->longitud_comercio;
          echo "<a target='_blank' href='$url'>Posicion Comercio</a><br/>";

          $url = "https://maps.googleapis.com/maps/api/staticmap?key=AIzaSyAXpROdHVy8YYxLeemEyR1hVDCRTL_4UdE&zoom=16&size=800x600&center=".$pedido->latitud.",".$pedido->longitud;
          $url.= "&markers=".$pedido->latitud.",".$pedido->longitud;       
          echo "<a target='_blank' href='$url'>Posicion Cliente</a><br/>";

          $url = "https://www.google.com/maps/dir/?api=1&origin=".$repartidor->latitud.",".$repartidor->longitud;
          $url.= "&destination=".$pedido->latitud.",".$pedido->longitud;
          $url.= "&waypoints=".$pedido->latitud_comercio.",".$pedido->longitud_comercio;
          $t = $matriz_tiempos[$i][$j];
          echo "<a target='_blank' href='$url'>".$t->tiempo."</a>";
          echo "</td>";
          $i++;
        }
        echo "</tr>";
        $j++;
      }
      echo "</table>";
    }

    $opciones = array();

    // Dependiendo de como es la matriz
    if (sizeof($repartidores) == 3) {

      if (sizeof($pedidos) == 3) {
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[0][1];
        $c = $matriz_tiempos[0][2];
        $d = $matriz_tiempos[1][0];
        $e = $matriz_tiempos[1][1];
        $f = $matriz_tiempos[1][2];
        $g = $matriz_tiempos[2][0];
        $h = $matriz_tiempos[2][1];
        $i = $matriz_tiempos[2][2];
        
        // A E I
        $opcion1 = $a->tiempo + $e->tiempo + $i->tiempo;
        if ($test == 1) echo "Opcion 1: [$a->repartidor - $a->pedido ($a->tiempo)] [$e->repartidor - $e->pedido ($e->tiempo)] [$i->repartidor - $i->pedido ($i->tiempo)] => Tiempo: $opcion1 <br/>";
        $opciones[] = array($a,$e,$i);
        
        // A H F
        $opcion2 = $a->tiempo + $h->tiempo + $f->tiempo;
        if ($test == 1) echo "Opcion 2: [$a->repartidor - $a->pedido ($a->tiempo)] [$h->repartidor - $h->pedido ($h->tiempo)] [$f->repartidor - $f->pedido ($f->tiempo)] => Tiempo: $opcion2 <br/>";
        $opciones[] = array($a,$h,$f);
        
        // D B I
        $opcion3 = $d->tiempo + $b->tiempo + $i->tiempo;
        if ($test == 1) echo "Opcion 3: [$d->repartidor - $d->pedido ($d->tiempo)] [$b->repartidor - $b->pedido ($b->tiempo)] [$i->repartidor - $i->pedido ($i->tiempo)] => Tiempo: $opcion3 <br/>";
        $opciones[] = array($d,$b,$i);
        
        // D H C
        $opcion4 = $d->tiempo + $h->tiempo + $c->tiempo;
        if ($test == 1) echo "Opcion 4: [$d->repartidor - $d->pedido ($d->tiempo)] [$h->repartidor - $h->pedido ($h->tiempo)] [$c->repartidor - $c->pedido ($c->tiempo)] => Tiempo: $opcion4 <br/>";
        $opciones[] = array($d,$h,$c);
        
        // G B F
        $opcion5 = $g->tiempo + $b->tiempo + $f->tiempo;
        if ($test == 1) echo "Opcion 5: [$g->repartidor - $g->pedido ($g->tiempo)] [$b->repartidor - $b->pedido ($b->tiempo)] [$f->repartidor - $f->pedido ($f->tiempo)] => Tiempo: $opcion5 <br/>";
        $opciones[] = array($g,$b,$f);
        
        // G E C
        $opcion6 = $g->tiempo + $e->tiempo + $c->tiempo;
        if ($test == 1) echo "Opcion 6: [$g->repartidor - $g->pedido ($g->tiempo)] [$e->repartidor - $e->pedido ($e->tiempo)] [$c->repartidor - $c->pedido ($c->tiempo)] => Tiempo: $opcion6 <br/>";
        $opciones[] = array($g,$e,$c);

      } else if (sizeof($pedidos) == 2) {

        // Tengo 2 pedidos y 3 repartidores
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[1][0];
        $c = $matriz_tiempos[0][1];
        $d = $matriz_tiempos[1][1];
        $e = $matriz_tiempos[0][2];
        $f = $matriz_tiempos[1][2];
        $opcion1 = $a->tiempo + $d->tiempo;
        $opciones[] = array($a,$d);
        $opcion2 = $a->tiempo + $f->tiempo;
        $opciones[] = array($a,$f);
        $opcion3 = $b->tiempo + $c->tiempo;
        $opciones[] = array($b,$c);
        $opcion4 = $b->tiempo + $e->tiempo;
        $opciones[] = array($b,$e);

      } else if (sizeof($pedidos) == 1) {
        $a = $matriz_tiempos[0][0];
        $opcion1 = $a->tiempo;
        $opciones[] = array($a);
        $b = $matriz_tiempos[0][1];
        $opcion2 = $b->tiempo;
        $opciones[] = array($b);
        $c = $matriz_tiempos[0][2];
        $opcion3 = $c->tiempo;
        $opciones[] = array($c);
      }

    } else if (sizeof($repartidores) == 2) {

      if (sizeof($pedidos) == 3) {
        // Tengo 3 pedidos y dos repartidores
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[1][0];
        $c = $matriz_tiempos[2][0];
        $d = $matriz_tiempos[0][1];
        $e = $matriz_tiempos[1][1];
        $f = $matriz_tiempos[2][1];
        $opcion1 = $a->tiempo + $e->tiempo;
        $opciones[] = array($a,$c);
        $opcion2 = $a->tiempo + $f->tiempo;
        $opciones[] = array($a,$f);
        $opcion3 = $b->tiempo + $d->tiempo;
        $opciones[] = array($b,$d);
        $opcion4 = $b->tiempo + $f->tiempo;
        $opciones[] = array($b,$f);
        $opcion5 = $c->tiempo + $d->tiempo;
        $opciones[] = array($c,$d);
        $opcion6 = $c->tiempo + $e->tiempo; 
        $opciones[] = array($c,$e);

      } else if (sizeof($pedidos) == 2) {
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[0][1];
        $c = $matriz_tiempos[1][0];
        $d = $matriz_tiempos[1][1];
        $opcion1 = $a->tiempo + $d->tiempo;
        $opciones[] = array($a,$d);
        $opcion2 = $b->tiempo + $c->tiempo;
        $opciones[] = array($b,$c);

      } else if (sizeof($pedidos) == 1) {

        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[1][0];
        $opcion1 = $a->tiempo;
        $opciones[] = array($a);
        $opcion2 = $b->tiempo;
        $opciones[] = array($b);
      }

    } else if (sizeof($repartidores) == 1) {

      if (sizeof($pedidos) == 3) {
        // Tengo 3 pedidos y 1 repartidor
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[1][0];
        $c = $matriz_tiempos[2][0];
        $opcion1 = $a->tiempo;
        $opciones[] = array($a);
        $opcion2 = $b->tiempo;
        $opciones[] = array($b);
        $opcion3 = $c->tiempo;
        $opciones[] = array($c);

      } else if (sizeof($pedidos) == 2) {
        $a = $matriz_tiempos[0][0];
        $b = $matriz_tiempos[1][0];
        $opcion1 = $a->tiempo;
        $opciones[] = array($a);
        $opcion2 = $b->tiempo;
        $opciones[] = array($b);

      } else if (sizeof($pedidos) == 1) {

        $a = $matriz_tiempos[0][0];
        $opcion1 = $a->tiempo;
        $opciones[] = array($a);
      }

    } 

    // Buscamos el minimo
    $tiempo_minimo = $opcion1;
    $opcion_minima = 1;
    if (isset($opcion2) && $opcion2 < $tiempo_minimo) { $tiempo_minimo = $opcion2; $opcion_minima = 2; }
    if (isset($opcion3) && $opcion3 < $tiempo_minimo) { $tiempo_minimo = $opcion3; $opcion_minima = 3; }
    if (isset($opcion4) && $opcion4 < $tiempo_minimo) { $tiempo_minimo = $opcion4; $opcion_minima = 4; }
    if (isset($opcion5) && $opcion5 < $tiempo_minimo) { $tiempo_minimo = $opcion5; $opcion_minima = 5; }
    if (isset($opcion6) && $opcion6 < $tiempo_minimo) { $tiempo_minimo = $opcion6; $opcion_minima = 6; }

    if ($test == 1) echo "MEJOR OPCION $opcion_minima. TIEMPO MINIMO: $tiempo_minimo <br/>";
    
    // Elegimos el repartidor
    $id_repartidor_elegido = 0;
    if (isset($opciones[$opcion_minima-1])) {
      foreach($opciones[$opcion_minima-1] as $o) {
        if ($o->id_pedido == $id_pedido_solicitado) {
          $id_repartidor_elegido = $o->id_repartidor;
          if ($test == 1) {
            echo "EL REPARTIDOR ASIGNADO ES : $o->repartidor <br/>";
          }
        }
      }
    }
    return $id_repartidor_elegido;
  }

  // DADO UN PEDIDO, BUSCA UN REPARTIDOR
  function buscar_repartidor($config = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_pedido = isset($config["id_pedido"]) ? $config["id_pedido"] : 0;
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sin_token = isset($config["sin_token"]) ? $config["sin_token"] : 0;

    // El parametro efectivo sirve para marcar el control si el repartidor llego al limite del efectivo
    $efectivo = isset($config["efectivo"]) ? $config["efectivo"] : 0;

    $this->load->model("Repartidor_Caja_Movimiento_Model");
    $this->load->model("Toque_Model");

    // Tenemos que seleccionar un repartidor en particular (por eso se le pasa el ID)
    if (!empty($id_repartidor)) {

      $sql = "SELECT R.* ";
      $sql.= "FROM repartidores R ";
      $sql.= "WHERE R.id_empresa = $id_empresa ";
      if ($sin_token == 0) $sql.= "AND R.token != '' "; // Tiene que tener token activo dentro de la plataforma
      $sql.= "AND R.activo = 1 "; // Y que este activo
      $sql.= "AND R.id = $id_repartidor ";
      $q = $this->db->query($sql);
      $repartidor = $q->row();

      // Si tenemos que controlar el efectivo que tiene el repartidor
      // Primero obtenemos el saldo
      if ($efectivo > 0 && $repartidor->limite_efectivo > 0) {
        $saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
          "id_empresa"=>$id_empresa,
          "id_repartidor"=>$repartidor->id,
        ));
        if ( ($saldo + $efectivo) > $repartidor->limite_efectivo ) {
          $linea = "$repartidor->nombre tiene $ $saldo de efectivo. El pedido de $ $efectivo supera el limite de $ $repartidor->limite_efectivo.";
          file_put_contents("logs/$id_empresa/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);
          return FALSE;
        }
      }

      return $repartidor;

    } else {

      // TENEMOS QUE BUSCAR UN REPARTIDOR
      // Dependiendo si es una asignacion automatica (si es manual no hace nada)
      $sql = "SELECT mostrar_numeros_direccion_listado FROM web_configuracion WHERE id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $empresa = $q->row();
      if ($empresa->mostrar_numeros_direccion_listado == 1) {
        // Si es una ASIGNACION AUTOMATICA
        $repartidores = $this->cantidad_libres(array(
          "id_empresa"=>$id_empresa,
          "efectivo"=>$efectivo, // Con esto controla el efectivo
          "id_pedido"=>$id_pedido,
        ));
        $repartidores_libres = $repartidores["cantidad"];
        $linea = "CANTIDAD DE REPARTIDORES LIBRES: $repartidores_libres";
        file_put_contents("logs/$id_empresa/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);

        $pedidos_no_asignados = $this->Toque_Model->cantidad_pedidos_no_asignados(array(
          "id_empresa"=>$id_empresa,
        ));
        $linea = "CANTIDAD DE PEDIDOS EN PROCESO: $pedidos_no_asignados";
        file_put_contents("logs/$id_empresa/".$id_pedido.".txt", date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);

        // SI HAY MAS REPARTIDORES QUE PEDIDOS, DIRECTAMENTE TOMA UNO Y LO ASIGNA
        if ($repartidores_libres > 0 && $repartidores_libres >= $pedidos_no_asignados) {
          // Tomamos el primero
          return $repartidores["results"][0];
        }
      }
    }
    // En cualquier otro caso (por ej: asignacion manual, no devuelve nada
    return FALSE;
  }

  // Ingresa un registro en la tabla de eventos
  function registrar_evento($config = array()) {
    $estado = isset($config["estado"]) ? $config["estado"] : '';
    $id_pedido = isset($config["id_pedido"]) ? $config["id_pedido"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $now = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $sql = "INSERT INTO repartidores_pedidos (id_empresa, id_repartidor, id_factura, id_punto_venta, estado, fecha) VALUES (";
    $sql.= "'$id_empresa', '$id_repartidor', '$id_pedido', '$id_punto_venta', '$estado', '$now' )";
    $this->db->query($sql);
  }

  function get($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $query = $this->db->get_where($this->tabla,array($this->ident=>$id,"id_empresa"=>$id_empresa));
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }

  function buscar($config = array()){
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $buscar_saldo = isset($config["buscar_saldo"]) ? $config["buscar_saldo"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 99999;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM repartidores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if ($activo != -1) $sql.= "AND activo = $activo ";
    if (!empty($id_usuario)) $sql.= "AND id = $id_usuario ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $resultado = $q->result();    
    $this->load->model("Repartidor_Caja_Movimiento_Model");
    foreach($resultado as $r) {
      $r->saldo = 0;
      $r->efectivo = 0; // Es igual a saldo
      if ($buscar_saldo == 1) {
        $r->saldo = $this->Repartidor_Caja_Movimiento_Model->calcular_saldo(array(
          "id_empresa"=>$id_empresa,
          "id_repartidor"=>$r->id,
        ));
        $r->efectivo = $r->saldo;
      }
    }
    return array(
      "results"=>$resultado,
      "total"=>$total->total,
    );    
  }

  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("nombre",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }    
}