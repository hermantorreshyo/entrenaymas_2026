<?php 
include("includes/init.php");
extract($entrada_model->get_variables()); 
if ($vc_categoria === FALSE) {
  $vc_categoria = new stdClass();
  $vc_categoria->nombre = "Entradas";
  $vc_categoria->id = 0;
  $vc_categoria->link = "";
}
$nombre_pagina = "blog";
$vc_categoria_titulo = $vc_categoria->nombre;
$vc_categoria_padre_id = $vc_categoria_principal->id;
$vc_pagina = $vc_categoria_principal->link;
if ($vc_total_resultados == 1 && sizeof($vc_listado)>0) {
  $entrada = $vc_listado[0];
  header("Location: ".mklink($entrada->link));
}
$vc_link = mklink($vc_link);
?><!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
</head>
<body>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Blog Informativo</h2>
    <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li><?php echo end($vc_categorias)->nombre ?></li>
    </ul>
  </div>
</section>

<!-- Psweb Blog Wrap -->
<section class="psweb-blog-wrap">
  <div class="container">
    <div class="blog-meta">
      <div class="row align-items-center">
        <div class="col-xl-6">
          <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Últimas Novedades</h2>
          <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s"><?php echo $vc_total_resultados ?> Noticias en el blog</p>
        </div>
        <div class="col-xl-6 textright wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.9s">
          <div class="dropdown checkboxes">
            <div class="select">
              <span><?php echo ($vc_order == 2)?"Alfabetico":"Más Nuevas" ?></span>
            </div>
            <ul class="dropdown-menu checkboxes">
              <li>
                <label>
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="mas_nuevas">
                    <label class="custom-control-label" for="mas_nuevas"><a href="<?php echo current_url(FALSE,TRUE)."?order=0" ?>">Más Nuevas</a></label>
                  </div>
                </label>
              </li>
              <li>
                <label>
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="alfabetico">
                    <label class="custom-control-label" for="alfabetico"><a href="<?php echo current_url(FALSE,TRUE)."?order=2" ?>">Alfabetico</a></label>
                  </div>
                </label>
              </li>
            </ul>
          </div>
          <?php $categorias = $entrada_model->get_subcategorias(1452);
          if (sizeof($categorias)>0) { ?>
            <div class="dropdown checkboxes">
              <div class="select">
                <span>Categoría</span>
              </div>
              <ul class="dropdown-menu checkboxes">
                <?php foreach($categorias as $c) { ?>
                  <li>
                    <label>
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="cat_<?php echo $c->link ?>">
                        <label class="custom-control-label" for="cat_<?php echo $c->link ?>"><a href="<?php echo mklink("entradas/blog/$c->link/") ?>"><?php echo $c->nombre ?></a></label>
                      </div>
                    </label>
                  </li>
                <?php } ?>
              </ul>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="row psweb-blog">
      <?php foreach($vc_listado as $r) { ?>
        <div class="col-lg-4 col-md-6">
          <div class="blog-item">
            <div class="psweb-image">
              <img src="<?php echo $r->path ?>" alt="<?php echo $r->titulo ?>">
              <div class="blog-info-wrap">
                <a href="<?php echo mklink($r->link) ?>" class="zoom-icon"><img src="assets/images/icon16.png" alt="Zoom"></a>
              </div>
            </div>
            <div class="blog-info">
              <h4><a href="<?php echo mklink($r->link) ?>"><?php echo $r->titulo ?></a></h4>
              <ul>
                <li><i class="fa fa-user" aria-hidden="true"></i> <a href="<?php echo mklink("entradas/blog/$r->categoria_link") ?>"><?php echo $r->categoria ?></a></li>
                <li><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo $r->fecha ?></li>
              </ul>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php
    if ($vc_total_paginas > 1) { ?>
      <ul class="pagination">
        <?php if ($vc_page > 0) { ?>
          <li class="page-item"><a class="page-link" href="<?php echo $vc_link.($vc_page-1)."/"; ?>"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
        <?php } ?>
        <?php for($i=0;$i<$vc_total_paginas;$i++) { ?>
          <?php if (abs($vc_page-$i)<3) { ?>
            <li class="page-item <?php echo ($i==$vc_page) ? "active" : ""?>"><a class="page-link" href="<?php echo $vc_link.$i."/"; ?>"><?php echo ($i+1); ?></a></li>
          <?php } ?>
        <?php } ?>
        <?php if ($vc_page < ($vc_total_paginas-1)) { ?>
          <li class="page-item"><a class="page-link" href="<?php echo $vc_link.($vc_page+1)."/"; ?>"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
        <?php } ?>
      </ul>
    <?php } ?>    
    
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<script type="text/javascript">
  var max = Math.max.apply(null, $(".blog-item").map(function() {
    return $(this).height();
  }));
$(".blog-item").height(max);
</script>
</body>
</html>