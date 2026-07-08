<?php 
include("includes/init.php");
$entrada = $entrada_model->get($id);
$nombre_pagina = "blog";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
</head>
<body>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h1 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo $entrada->categoria ?></h1>
    <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li><?php echo $entrada->categoria ?></li>
    </ul>
  </div>
</section>

<!-- Blog Detail Wrap -->
<section class="blog-detail-wrap">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="blog-details">
          <?php if ($entrada->mostrar_fecha == 1) { ?>
            <div class="blog-date"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $entrada->fecha ?></div>
          <?php } ?>
          <?php if (sizeof($entrada->images)>0) { ?>
            <div class="owl-carousel mb40" data-items="1" data-margin="0" data-loop="true" data-nav="true" data-dots="true" data-autoplay="true">
              <?php foreach($entrada->images as $r) { ?>
                <div class="item">
                  <div class="blog-image"><img src="<?php echo $r->path ?>" alt=""></div>
                </div>
              <?php } ?>
            </div>
          <?php } else if (!empty($entrada->path)) { ?>
            <div class="tac mb40">
              <img class="blog-imagen-ppal" src="<?php echo $entrada->path ?>" class="w100p" alt="<?php echo $entrada->titulo ?>"/>
            </div>
          <?php } ?>
          <div class="blog-inner-detail">
            <div class="blog-title">
              <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Categoría: <?php echo $entrada->categoria ?></h4>
              <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $entrada->titulo ?></h2>
            </div>
            <div class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">
              <?php echo $entrada->texto ?>
            </div>
            <div class="blog-detail-meta wow fadeInUp" data-wow-duration="1s" data-wow-delay="1.1s">
              <div class="row align-items-center">
                <div class="col-sm-6">
                  <a href="javascript:window.history.back()" class="psweb-btn border-btn">Volver</a>
                </div>
                <div class="col-sm-6">
                  <div class="psweb-social rounded">
                    <a href="whatsapp://send?text=<?php echo urlencode(current_url()) ?>" data-toggle="tooltip" data-placement="bottom" title="Compartir en WhatsApp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                    <a href="mailto:?subject=<?php echo html_entity_decode($entrada->titulo,ENT_QUOTES) ?>&body=<?php echo(current_url()) ?>" data-toggle="tooltip" data-placement="bottom" title="Compartir en Envelope"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                    <a href="https://www.facebook.com/sharer.php?u=<?php echo urlencode(current_url()) ?>" onclick="window.open(this.href, 'mywin','left=50,top=50,width=600,height=350,toolbar=0'); return false;" data-toggle="tooltip" data-placement="bottom" title="Compartir en Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="right-sidebar">
          <?php 
          if ($entrada->categorias[0]->id == 1452) {
            $subcategorias = $entrada_model->get_subcategorias(1452);
            if (sizeof($subcategorias)>0) { ?>
              <div class="sidebar-item">
                <h2>Categorías de Blog</h2>
                <ul>
                  <?php foreach($subcategorias as $r) { ?>
                    <li><a href="<?php echo mklink("entradas/blog/$r->link/") ?>"><span><?php echo $r->nombre ?></span></a></li>
                  <?php } ?>
                </ul>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="sidebar-item">
              <h2>Información</h2>
              <ul>
                <li><a href="<?php echo mklink("/") ?>">Inicio</a></li>
                <li><a href="<?php echo mklink("profesionales/") ?>">Profesionales</a></li>
                <?php $ayuda = $entrada_model->get_list(array(
                  "id_categoria"=>1486
                )); 
                foreach($ayuda as $a) { ?>
                  <li><a href="<?php echo mklink($a->link) ?>"><?php echo $a->titulo ?></a></li>
                <?php } ?>
                <li><a href="<?php echo mklink("entradas/blog/") ?>">Blog</a></li>
              </ul>
            </div>            
          <?php } ?>

          <?php $mas_vistos = $entrada_model->mas_leidas(array(
            "from_id_categoria"=>1452,
            "offset"=>3
          ));
          if (sizeof($mas_vistos)>0) { ?>
            <div class="sidebar-item">
              <h4>Los Más Vistos</h4>
              <h2>Blog Informativo</h2>
              <?php foreach($mas_vistos as $r) { ?>
                <div class="post-item">
                  <div class="psweb-image">
                    <a href="<?php echo mklink($r->link) ?>">
                      <img src="<?php echo $r->path ?>" alt="<?php echo $r->titulo ?>">
                    </a>
                  </div>
                  <div class="post-info">
                    <?php if ($r->mostrar_fecha == 1) { ?>
                      <div class="blog-date"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $r->fecha ?></div>
                    <?php } ?>
                    <h5><a href="<?php echo mklink($r->link) ?>"><?php echo $r->titulo ?></a></h5>
                    <a href="<?php echo mklink($r->link) ?>" class="post-link"><span>Leer Más</span></a>
                  </div>
                </div>
              <?php } ?>
              <div class="sidebar-btn">
                <a href="<?php echo mklink("entradas/blog/") ?>" class="psweb-btn psweb-big-btn">Ver Todos Los Post</a>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

</body>
</html>