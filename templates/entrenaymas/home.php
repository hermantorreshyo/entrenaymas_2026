<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php"); 
$nombre_pagina = "home"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php"); ?>
<link rel="canonical" href="https://entrenaymas.com/" />
</head>
<body>

<script type='text/javascript'>
  var _pix = document.getElementById('_pix_id_a39f0c7a-3ef4-5d98-9397-e576b32e3c1f');
  if (!_pix) { 
    var protocol = '//';
    var a = Math.random() * 1000000000000000000;
    _pix = document.createElement('iframe');
    _pix.style.display = 'none';
    _pix.setAttribute('src', protocol + 'aax-eu.amazon-adsystem.com/s/iu3?d=generic&ex-fargs=%3Fid%3Da39f0c7a-3ef4-5d98-9397-e576b32e3c1f%26type%3D4%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3DA39F0C7A-3EF4-5D98-9397-E576B32E3C1F' + '&cb=' + a);
    _pix.setAttribute('id','_pix_id_a39f0c7a-3ef4-5d98-9397-e576b32e3c1f');
    document.body.appendChild(_pix);
  }
</script>
<noscript>
  <img height='1' width='1' border='0' alt='' src='https://aax-eu.amazon-adsystem.com/s/iui3?d=forester-did&ex-fargs=%3Fid%3Da39f0c7a-3ef4-5d98-9397-e576b32e3c1f%26type%3D4%26m%3D44551&ex-fch=416613&ex-src=https://entrenaymas.com/&ex-hargs=v%3D1.0%3Bc%3D8269573310802%3Bp%3DA39F0C7A-3EF4-5D98-9397-E576B32E3C1F' />
</noscript>


<?php include("includes/header.php"); ?>

<section class="psweb-search">
  <div class="container">
    <div class="search-wrap">
      <h1 class="wow fadeInUp hometitle" data-wow-duration="1s" data-wow-delay="0.5s">Busca tu entrenador personal</h1>
      <div class="search-form wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
        <div class="tab-content">
          <div class="tab-pane active" id="Lista">
            <?php 
            $funcion_buscar = "buscar_listado";
            include("includes/searchform.php") ?>
          </div>
        </div>
      </div>

      <?php 
      $q = mysqli_query($conx,"SELECT * FROM toque_categorias WHERE id_empresa = $empresa->id AND path != '' ORDER BY orden DESC LIMIT 0,6");
      if (mysqli_num_rows($q)>0) { ?>
        <div class="search-links">
          <div class="row">
            <?php while (($r=mysqli_fetch_object($q))!==NULL) { ?>
              <div class="search-link-item wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">
                <a href="<?php echo (!empty($r->link) ? $r->link : mklink("profesionales/?cat=$r->id")) ?>" class="search-inner-item">
                  <img src="/sistema/<?php echo $r->path ?>" alt="<?php echo $r->nombre ?>">
                </a>
                <p><?php echo $r->nombre ?></p>
              </div>
            <?php } ?>
            <div class="displaynonetel search-link-item wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">
              <a href="<?php echo mklink("profesionales/") ?>" class="search-inner-item view-more">
                <span>.  .  .</span>
              </a>
              <p>Ver Más</p>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php 
$profesionales = $usuario_model->get_list(array(
  "id_perfil"=>(isset($portal_id_perfil_profesional) ? $portal_id_perfil_profesional : 0),
  "usar_get"=>1,
  "offset"=>6,
  "activo"=>1,
));
if (sizeof($profesionales)>0) { ?>
  <section class="psweb-doctors">
    <?php /*
    <div class="row">
      <div class="col-lg-4">
        <div class="doctors-info-wrap">
          <div class="psweb-icon wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">
            <img src="assets/images/icon10.png" alt="Doctors">
          </div>
          <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
            Encuentra profesionales <br>
            <span>cercanos en <br> tu ciudad</span>
          </h2>
          <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">Contacta de forma rápida y directa. <br> Reserva cita con un toque.</p>
          <div class="psweb-btn-wrap wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
            <a href="<?php echo mklink("profesionales/") ?>" class="psweb-btn">Comenzar</a>
          </div>
        </div>
      </div>
      <div class="col-lg-8">*/ ?>
    <div class="doctors-wrap">
      <div class="owl-carousel" data-nav="true" data-items="6" data-auto-width="true" data-margin="0" data-loop="true" data-dots="false" data-autoplay="true">
        <?php foreach($profesionales as $r) { 
          $link = mklink("profesional/".$r->apellido."-".$r->id."/"); ?>
          <div class="item">
            <div class="doctor-item">
              <?php if ($r->destacado == 1) { ?>
                <div class="doctor-quote"><i class="fa fa-bookmark" aria-hidden="true"></i></div>
              <?php } ?>

              <?php if (estaEnFavoritos($r->id)) { ?>
                <a class="like-icon added" data-bookmark-state="" href="/sistema/favoritos/eliminar/?id=<?php echo $r->id; ?>">
                  <i class="fa fa-heart" aria-hidden="true"></i>
                </a>
              <?php } else { ?>
                <a class="like-icon" href="/sistema/favoritos/agregar/?id=<?php echo $r->id; ?>">
                  <i class="fa fa-heart-o" aria-hidden="true"></i>
                </a>
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
                  <span class="destacado home" data-toggle="tooltip" data-placement="bottom" title="Perfil Verificado"><i class="fa fa-check"></i></span>
                <?php } ?>

              </div>
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
                  (<?php echo $promedio->cantidad ?>)
                <?php } ?>
              </div>              
              <?php if (sizeof($r->titulos)>0) { ?>
                <div class="oh">
                  <?php $k=0; 
                  foreach($r->titulos as $titulo) { ?>
                    <?php if ($k==1) continue; ?>
                    <span class="doctor-designation fl mr10"><?php echo $titulo->nombre ?></span>
                  <?php $k++; } ?>
                </div>
              <?php } ?>
              <span class="profesional-titulo"><?php echo ucwords(mb_strtolower($r->nombre)) ?></span>
              <ul>
                <li><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $r->localidad ?></li>
                <?php if (sizeof($r->direcciones)>0) { 
                  $minimo = 999999;
                  foreach($r->direcciones as $dir) {
                    if ($dir->costo < $minimo) $minimo = $dir->costo;
                  } ?>
                  <li><i class="fa fa-usd" aria-hidden="true"></i> Desde: <span><?php echo ($minimo == 0)?"-":$minimo." &euro;" ?></span></li>
                <?php } ?>
              </ul>
              <div class="psweb-btn-wrap"><a href="<?php echo $link ?>" class="psweb-btn border-btn">Ver Más</a></div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<?php $blog = $entrada_model->get_list(array(
  "from_link_categoria"=>"blog",
  "offset"=>3,
));
if (sizeof($blog)>0) { ?>
  <section class="psweb-blog">
    <div class="container">
      <div class="section-title">
        <?php $t = $web_model->get_text("blog-titulo","Últimas Noticias del Blog"); ?>
        <h3 class="wow fadeInUp editable" data-id="<?php echo $t->id ?>" data-clave="<?php echo $t->clave ?>" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo $t->plain_text ?></h3>
        <?php $t = $web_model->get_text("blog-subtitulo","Novedades Sobre Psicología"); ?>
        <h2 class="wow fadeInUp editable" data-id="<?php echo $t->id ?>" data-clave="<?php echo $t->clave ?>" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $t->plain_text ?></h2>
        <?php $t = $web_model->get_text("blog-parrafo"); ?>
        <p class="wow fadeInUp editable" data-id="<?php echo $t->id ?>" data-clave="<?php echo $t->clave ?>" data-wow-duration="1s" data-wow-delay="0.9s"><?php echo nl2br($t->plain_text) ?></p>
      </div>
      <div class="row">
        <?php foreach($blog as $r) { ?>
          <div class="col-lg-4 col-md-6">
            <div class="blog-item wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
              <div class="psweb-image">
                <img src="<?php echo $r->path ?>" alt="Blog">
                <div class="blog-info-wrap">
                  <a href="<?php echo mklink($r->link) ?>" class="zoom-icon"><img src="assets/images/icon16.png" alt="Zoom"></a>
                </div>
              </div>
              <div class="blog-info">
                <h4><a href="<?php echo mklink($r->link) ?>"><?php echo $r->titulo ?></a></h4>
                <ul>
                  <li><i class="fa fa-user" aria-hidden="true"></i> Categoría: <a href="<?php echo mklink("entradas/$r->categoria_link/") ?>"><?php echo $r->categoria ?></a></li>
                  <li><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo $r->fecha ?></li>
                </ul>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="psweb-btn-wrap wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
        <a href="<?php echo mklink("entradas/blog/") ?>" class="psweb-btn border-btn">Ver Todas</a>
      </div>
    </div>
  </section>
<?php } ?>

<?php 
$slider = $web_model->get_slider(array(
  "clave"=>"slider_1",
));
if (sizeof($slider)>0) { ?>
  <section class="psweb-banner">
    <div class="owl-carousel" data-items="1" data-margin="0" data-loop="true" data-nav="true" data-dots="false" data-autoplay="true">
      <?php foreach($slider as $r) { ?>
        <div class="item">
          <div class="banner-wrap" style="background-image: url('<?php echo $r->path ?>')">
            <div class="psweb-table-wrap">
              <div class="psweb-align-wrap">
                <div class="container">
                  <div class="banner-caption">
                    <?php if (!empty($r->linea_1)) { ?>
                      <h3 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo $r->linea_1 ?></h3>
                    <?php } ?>
                    <?php if (!empty($r->linea_2)) { ?>
                      <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $r->linea_2.(!empty($r->linea_3) ? "<br>$r->linea_3":"") ?></h2>
                    <?php } ?>
                    <?php if (!empty($r->linea_4)) { ?>
                      <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s"><?php echo $r->linea_4.(!empty($r->linea_5) ? "<br>$r->linea_5":"") ?></p>
                    <?php } ?>
                    <?php if (!empty($r->link_1)) { ?>
                      <div class="psweb-btn-wrap wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
                        <a href="<?php echo $r->link_1 ?>" class="psweb-btn"><?php echo $r->texto_link_1 ?></a>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
<?php } ?>

<?php if (sizeof($anunciantes)>0) { ?>
  <section class="psweb-banner mt50 mb50">
    <div class="owl-carousel anunciantes-carousel" data-items="3" data-margin="100" data-loop="true" data-nav="true" data-dots="false" data-autoplay="true">
      <?php foreach($anunciantes as $a) { ?>
        <div class="item">
          <a href="<?php echo $a->link; ?>" target="_blank">
            <img src="<?php echo $a->path ?>">
          </a>
        </div>
      <?php } ?>
    </div>
  </section>
<?php } ?>

<?php include("includes/marcas.php") ?>
<?php include("includes/newsletter.php") ?>
<?php include("includes/footer.php") ?>
<script type="text/javascript">
  var max = Math.max.apply(null, $(".blog-item").map(function() {
    return $(this).height();
  }));
$(".blog-item").height(max);
</script>
<script type="text/javascript">
  $( window ).load(function() {
      var max_entrenadores = Math.max.apply(null, $(".doctor-item").map(function() {
      return $(this).height();
    }));
  $(".doctor-item").height(max_entrenadores);
  });
</script>
</body>
</html>