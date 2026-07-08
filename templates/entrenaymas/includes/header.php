<?php
$cant_favoritos = 0;
if (isset($_SESSION["favoritos"])) {
  $arr = explode(",",$_SESSION["favoritos"]);
  $cant_favoritos = sizeof($arr)-1;
}
?>

<header class="psweb-header">
  <div class="container">
    <div class="row align-items-center">
      <?php if ($marca_blanca == "marca") { ?>
        <div class="col-lg-2 col-sm-5 col-9">
          <a href="<?php echo mklink("/") ?>" class="psweb-logo">
            <img src="assets/images/marca.png" alt="<?php echo $empresa->nombre ?>">
          </a>
        </div>
      <?php } else if ($marca_blanca == "tmr-world-shop") { ?>
        <div class="col-lg-2 col-sm-5 col-9">
          <a href="<?php echo mklink("/") ?>" class="psweb-logo">
            <img src="assets/images/tmr-world-shop.png" alt="<?php echo $empresa->nombre ?>">
          </a>
        </div>
      <?php } else { ?>
        <div class="col-lg-3 col-sm-5 col-9">
          <a href="<?php echo mklink("/") ?>" class="psweb-logo">
            <img src="/sistema/<?php echo $empresa->logo_1 ?>" alt="<?php echo $empresa->nombre ?>">
          </a>
        </div>
      <?php } ?>
      <div class="col-lg-9 col-sm-7 col-3">
        <div class="header-right">
          <nav class="psweb-navigation">
            <ul>
              <li><a class="<?php echo (isset($nombre_pagina) && $nombre_pagina == "profesionales")?"active":"" ?>" href="<?php echo mklink("profesionales/") ?>">Profesionales</a></li>
              <li><a class="<?php echo (isset($nombre_pagina) && $nombre_pagina == "productos")?"active":"" ?>" href="<?php echo mklink("productos/") ?>">Caja Regalo</a></li>
              <?php //<li><a href="#0">Planes de Precio</a></li>?>
              <li><a class="<?php echo (isset($nombre_pagina) && $nombre_pagina == "blog")?"active":"" ?>" href="<?php echo mklink("entradas/blog/") ?>">Blog</a></li>
            </ul>
          </nav>
          <div class="header-link">
            <a href="<?php echo mklink("web/registro/") ?>" class="psweb-btn">Regístrate</a>
            <a href="/sistema/" target="_blank" class="psweb-btn psweb-btn-2">Acceder</a>

            <a class="like-icon" href="<?php echo mklink("profesionales/")."?favs=1"; ?>">
              <?php if ($cant_favoritos>0) { ?>
                <i class="fa fa-heart added" aria-hidden="true"></i>
                <span><?php echo $cant_favoritos ?></span>
              <?php } else { ?>
                <i class="fa fa-heart-o" aria-hidden="true"></i>
              <?php } ?>
            </a>

          </div>
          <a href="javascript:void(0);" onclick="$('.psweb-navigation').slideToggle();" class="psweb-toggle"><span class="toggle-separator"></span></a>
        </div>
      </div>
    </div>
  </div>
</header>