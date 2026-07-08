<?php 
// REEMPLAZAR ESTO POR UNA CONFIGURACION DE LA EMPRESA
$portal_id_perfil_profesional = 1357;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("sistema/application/helpers/fecha_helper.php");

include_once("models/Carrito_Model.php");
$carrito_model = new Carrito_Model($empresa->id,$conx);
$carrito = $carrito_model->get_carrito();

include_once("models/Entrada_Model.php");
$entrada_model = new Entrada_Model($empresa->id,$conx);

include_once("models/Publicidad_Model.php");
$publicidad_model = new Publicidad_Model($empresa->id,$conx);

include_once("models/Web_Model.php");
$web_model = new Web_Model($empresa->id,$conx);

include_once("models/Curso_Model.php");
$curso_model = new Curso_Model($empresa->id,$conx);

include_once("models/Articulo_Model.php");
$articulo_model = new Articulo_Model($empresa->id,$conx);

include_once("models/Pedido_Model.php");
$pedido_model = new Pedido_Model($empresa->id,$conx);

include_once("models/Profesional_Model.php");
$profesional_model = new Profesional_Model($empresa->id,$conx);

include_once("models/Usuario_Model.php");
$usuario_model = new Usuario_Model($empresa->id,$conx);

include_once("models/Cliente_Model.php");
$cliente_model = new Cliente_Model($empresa->id,$conx);

$cliente = FALSE;
if (isset($_COOKIE["id_cliente"])) {
  $id_cliente = intval($_COOKIE["id_cliente"]);
  $cliente = $cliente_model->get($id_cliente);
}

$anunciantes = $web_model->get_anunciantes();

function estaEnFavoritos($id) {
  if (!isset($_SESSION["favoritos"])) return false;
  $favoritos = explode(",",$_SESSION["favoritos"]);
  foreach($favoritos as $f) {
    if ($f == $id) return true;
  }
  return false;
}

function format($n,$consultar = true,$moneda = "€") {
  if ($n==0 && $consultar) return "Consultar";
  $n = number_format($n,2,',','.');
  return $n." ".$moneda;
}

$marca_blanca = "";
if (isset($get_params["canal"]) && ($get_params["canal"] == "marca" || $get_params["canal"] == "tmr-world-shop") ) {
  setcookie("marca",$get_params["canal"],time()+(60*60*24*7),"/");
  $marca_blanca = $get_params["canal"];
}
if (isset($_COOKIE["marca"])) $marca_blanca = $_COOKIE["marca"];

function existe_objetivo($config = array()) {
  global $conx, $empresa;
  $id = isset($config["id"]) ? $config["id"] : 0;
  $sql = "SELECT U.* ";
  $sql.= "FROM seo_urls U ";
  $sql.= "WHERE 1=1 ";
  $sql.= "AND EXISTS (SELECT 1 FROM seo_urls_parametros P WHERE U.id_empresa = P.id_empresa AND P.id_seo = U.id ";
  $sql.= "AND P.valor = '$id' AND P.campo = 'Objetivo') ";
  $q = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q)==0) return FALSE;
  $r = mysqli_fetch_object($q);
  return $r;
}

function existe_categoria($config = array()) {
  global $conx, $empresa;
  $id = isset($config["id"]) ? $config["id"] : 0;
  $sql = "SELECT U.* ";
  $sql.= "FROM seo_urls U ";
  $sql.= "WHERE 1=1 ";
  $sql.= "AND EXISTS (SELECT 1 FROM seo_urls_parametros P WHERE U.id_empresa = P.id_empresa AND P.id_seo = U.id ";
  $sql.= "AND P.valor = '$id' AND P.campo = 'Especialidad') ";
  $q = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q)==0) return FALSE;
  $r = mysqli_fetch_object($q);
  return $r;
}

function item($item){
  global $conx, $empresa;
  $r = $item;
  $link = mklink("profesional/".$r->apellido."-".$r->id."/"); ?>
  <div data-id="<?php echo $r->id ?>" class="doctor-item <?php echo ($r->valor_localidad > 0)?"active":"" ?>">
    <?php if ($r->valor_localidad > 0) { ?>
      <div class="doctor-quote"><i class="fa fa-bookmark" aria-hidden="true"></i></div>
    <?php } ?>
    <div class="row align-items-center">
      <div class="col-lg-6">

        <?php if (estaEnFavoritos($r->id)) { ?>
          <a class="like-icon added" data-bookmark-state="" rel="nofollow" href="/sistema/favoritos/eliminar/?id=<?php echo $r->id; ?>">
            <i class="fa fa-heart" aria-hidden="true"></i>
          </a>
        <?php } else { ?>
          <a class="like-icon" rel="nofollow" href="/sistema/favoritos/agregar/?id=<?php echo $r->id; ?>">
            <i class="fa fa-heart-o" aria-hidden="true"></i>
          </a>
        <?php } ?>
        <?php foreach($r->tipos_atenciones as $ta) { ?>
          <?php if ($ta->nombre == "Online") { ?>
            <span class="atencion_en_linea" data-toggle="tooltip" data-placement="bottom" title="Atención en linea">
              <img src="assets/images/atencion.png" alt="atencion" />
            </span>
          <?php } ?>
        <?php } ?>

        <div class="psweb-image">
          <a href="<?php echo $link ?>">
            <?php if (!empty($r->path)) { ?>
              <img src="<?php echo $r->path ?>" alt="<?php echo ($r->nombre);?>">
            <?php } else if (!empty($empresa->no_imagen)) { ?>
              <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($r->nombre);?>">
            <?php } else { ?>
              <img src="images/no-imagen.png" alt="<?php echo ($r->nombre);?>">
            <?php } ?>
          </a>
          <?php if ($r->destacado == 1) { ?>
            <span class="destacado" data-toggle="tooltip" data-placement="bottom" title="Perfil Verificado"><i class="fa fa-check"></i></span>
          <?php } ?>
        </div>
        <div class="doctor-info txtcentertel">
          <?php 
          $prom = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ROUND(AVG(votos_positivos),0) as prom from not_entradas_comentarios WHERE id_entrada = $r->id and id_empresa = $r->id_empresa ";
          $result = mysqli_query($conx, $prom);
          $promedio = mysqli_fetch_object($result);?>
          <div class="rate pb5 ml0">
            <?php for ($i=0; $i < $promedio->prom; $i++) { ?>
              <span class="fa fa-star checked"></span>
            <?php } ?>
            <?php for ($i=$promedio->prom; $i < 5; $i++) { ?>
              <span class="fa fa-star"></span> 
            <?php } ?>
            <?php if ($promedio->cantidad > 0) { ?>
              (De <?php echo $promedio->cantidad ?> comentarios)
            <?php } else { 
              echo "(Sin comentarios)";
            } ?>
          </div>
          <?php if (sizeof($r->titulos)>0) { ?>
            <div class="oh">
              <?php foreach($r->titulos as $titulo) { ?>
                <span class="doctor-designation fl flntel mr10"><?php echo $titulo->nombre ?></span>
              <?php } ?>
            </div>
          <?php } ?>
          <?php if (isset($r->provincia) && !empty($r->provincia)){ ?>
            <p class="descripcion_sobre_mi pb5"><i class="fa fa-paper-plane pr5" aria-hidden="true"></i><?php echo $r->provincia; ?></p>
          <?php } elseif (isset($r->localidad) && !empty($r->localidad)) { ?>
            <p class="descripcion_sobre_mi pb5"><i class="fa fa-paper-plane pr5" aria-hidden="true"></i><?php echo $r->localidad; ?></p>
          <?php } ?>
          <h3 class="profesional-titulo"><a href="<?php echo $link ?>"><?php echo ucwords(mb_strtolower($r->nombre)) ?></a></h3>
          <?php if (!empty($r->sobre_mi)) { ?>
            <p class="descripcion_sobre_mi"><?php echo $r->sobre_mi?></p>
          <?php } else { ?>
            <?php 
            $descripcion = substr($r->custom_3, 0, 150);
            if (strlen($r->custom_3)>150){
              $descripcion.="...";
            }
            ?>
            <p class="descripcion_sobre_mi"><?php echo $descripcion; ?></p>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="doctor-direccion">
          <?php /* if (sizeof($r->direcciones)>0) { ?>
            <div class="doctor-tags">
              <?php $k=0; foreach($r->direcciones as $dir) { ?>
                <a class="<?php echo ($k==0)?"active":"" ?>" data-id="<?php echo $dir->id ?>" id="link_<?php echo $dir->id ?>" onclick="mostrar_direccion(this)" href="javascript:void(0)"><?php echo $dir->nombre ?></a>
              <?php $k++; } ?>
            </div>
            <?php $k=0; foreach($r->direcciones as $dir) { ?>
              <div id="dir_<?php echo $dir->id ?>" class="direccion-wrap <?php echo ($k==0)?"active":"" ?>">
                <div class="psweb-icon"><img src="assets/images/icon32.png" alt="Map"></div>
                <div class="direccion-info">
                  <h4><?php echo $dir->direccion.", ".$dir->localidad ?></h4>
                  <?php if (!empty($dir->costo)) { ?>
                    <span>Desde: <?php echo $dir->costo ?> &euro;</span>
                  <?php } ?>
                </div>
              </div>
            <?php if($k>2) break; 
            $k++; } ?>
          <?php } ?>
          <?php /*
          <a href="#0" class="psweb-btn border-btn"><i class="fa fa-calendar" aria-hidden="true"></i> Solicitar Turno</a>
          */ ?>
          <div class="row">
            <div class="col-md-7 displaynonetel" style="border-right: none;">
              <?php if (sizeof($r->formas_pago)>0){?>
                <p>Objetivos</p>
                <?php $k=0; foreach ($r->formas_pago as $fp) { ?>
                  <?php if ($k==3) { ?>
                    <p class="formas_pago"><i class='fa fa-circle' aria-hidden='true'></i> Otros</p>
                    <?php break; ?>
                  <?php } ?>
                  <p class="formas_pago"><i class='fa fa-circle' aria-hidden='true'></i>
                    <?php 
                    $objetivo = existe_objetivo(array(
                      "id"=>$fp->id,
                    ));
                    if ($objetivo === FALSE) { echo $fp->nombre; } else { ?>
                      <a href="<?php echo mklink($objetivo->url) ?>"><?php echo $fp->nombre ?></a>
                    <?php } ?>
                  </p>
                <?php $k++; } ?>
              <?php }?>
            </div>
            <div class="col-md-5">
              <?php 
              if (sizeof($r->direcciones)>0) {
                $minimo = 99999999999;
                foreach ($r->direcciones as $dir) {
                  if ($dir->costo < $minimo) $minimo = $dir->costo;
                } ?>
                <div class="desde_precio">
                  <span>Desde</span>
                  <p class="tarifas_costo"><?php echo ($minimo == 0) ? "-" : $minimo.' <i class="fa fa-eur" aria-hidden="true"></i>' ?></p>
                  <span>Persona/Sesion</span>
                </div>
              <?php } ?>
              <?php if ($r->sesion_gratis == 1) { ?>
                <div class="desde_precio_footer">
                  <span>1 SESIÓN GRATIS</span>
                </div>
              <?php } ?>
              <div class="txtcentertel">
                <a href="<?php echo $link; ?>" class="desde_precio_contactar">CONTACTAR</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>