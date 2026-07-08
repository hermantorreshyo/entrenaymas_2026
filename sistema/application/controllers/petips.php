<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips extends REST_Controller {

  function calcular_ranking() {

    $this->load->model("Petips_Producto_Model");
    $sql = "SELECT * FROM petips_productos ";
    $q = $this->db->query($sql);
    foreach($q->result() as $p) {

      $perfil_nutricional = 0;
      $suma_iconos = 0;
      $suma_ingredientes = 0;
      $suma_claims = 0;
      $tiene_ingredientes_calidad = FALSE;
      $tiene_carne = FALSE;

      $r = $this->Petips_Producto_Model->get($p->id);
      echo "<b>ANALIZANDO $r->nombre </b><br/>";

      $perfil_nutricional += ((float)$r->proteina * 2);
      $perfil_nutricional += ((float)$r->grasa * 2);
      $perfil_nutricional += (float)$r->fibra;
      $perfil_nutricional += ((float)$r->cenizas * -1);
      $perfil_nutricional += (float)$r->carbohidratos;
      echo "PERFIL NUTRICIONAL $perfil_nutricional <br/>";

      foreach($r->ingredientes as $ing) {
        $suma_ingredientes += (float) $ing->puntaje;
        if ($ing->es_calidad == 1) $tiene_ingredientes_calidad = TRUE;
        if ($ing->es_carne == 1) $tiene_carne = TRUE;
      }
      echo "SUMA INGREDIENTES $suma_ingredientes <br/>";

      foreach($r->claims as $ing) {
        $suma_claims += (float) $ing->puntaje;
      }
      echo "SUMA CLAIMS $suma_claims <br/>";

      if ($r->nutricionalmente_completo == 1) $suma_iconos += 5;
      if (($r->proteina + $r->grasa) > 38) $suma_iconos += 5;
      if ($tiene_ingredientes_calidad) $suma_iconos += 5;
      if ($tiene_carne) $suma_iconos += 5;
      if ($r->es_hipoalergenico == 1) $suma_iconos += 10;
      if ($r->es_natural == 1) $suma_iconos += 10;
      echo "SUMA ICONOS $suma_iconos <br/>";

      echo "OPINIONES CLIENTES $r->opiniones_clientes <br/>";

      echo "OPINIONES CLIENTES $r->reputacion_mercado <br/>";

      // NORMALIZAMOS
      $perfil_nutricional = $perfil_nutricional * 0.3;
      $suma_ingredientes = $suma_ingredientes * 0.3;
      $suma_claims = $suma_claims * 0.2;
      $opiniones_clientes = $opiniones_clientes * 0.075;
      $reputacion_mercado = $reputacion_mercado * 0.075;
      $suma_iconos = $suma_iconos * 0.05;

      $total = $perfil_nutricional + $suma_ingredientes + $suma_claims + $opiniones_clientes + $reputacion_mercado + $suma_iconos;
      echo "TOTAL NORMALIZADO: $total <br/>";
    }

  }
	
}