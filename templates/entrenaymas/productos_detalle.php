<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("includes/init.php");

$producto = $articulo_model->get($id,array(
  "buscar_relacionados"=>1,
  "buscar_variantes"=>1,
  "consultar_stock"=>1,
));
if ($producto === FALSE) {
  header("Location: 404.php");
  exit();
}

// Tomamos los datos de SEO
$seo_title = (!empty($producto->seo_title)) ? $producto->seo_title : $empresa->seo_title;
$seo_description = (!empty($producto->seo_description)) ? $producto->seo_description : $empresa->seo_description;
$seo_keywords = (!empty($producto->seo_keywords)) ? $producto->seo_keywords : $empresa->seo_keywords;

$imagen_ppal = (!empty($producto->path)) ? $producto->path : (!empty($empresa->no_imagen) ? "/sistema/".$empresa->no_imagen : "images/no-imagen.png");
if (sizeof($producto->images)==0) {
  $image = new stdClass();
  $image->path = $imagen_ppal;
  $producto->images[] = $image;
}

// Cantidad por default
$cantidad_producto = 1;

$seo_title = $empresa->seo_title.((!empty($producto->nombre)) ? " | ".$producto->nombre : "");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php") ?>
<meta property="og:url" content="<?php echo current_url(); ?>" />
<meta property="og:type" content="website" />
<meta property="og:description" content="<?php echo str_replace("\n","",strip_tags($producto->texto)) ?>" />
<meta property="og:title" content="<?php echo $producto->nombre; ?>" />
<meta property="og:image" content="<?php echo current_url(TRUE).$imagen_ppal; ?>"/>

<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/css/paquete.css">
<style type="text/css">
.propiedad_opcion.active { text-decoration: underline !important; color: var(--c1) !important; }
</style>
</head>
<body>

<?php include("includes/header.php") ?>

<!-- Psweb Page Title -->
<section class="psweb-page-title">
  <div class="container">
    <h2 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">Caja Regalo</h2>
    <!-- <ul class="breadcrumb wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
      <li><a href="<?php echo mklink("/") ?>">Home</a></li>
      <li><i class="fs14 ml10 mr10 fa fa-chevron-right"></i></li>
      <li>Paquetes</li>
    </ul> -->
  </div>
</section>

<!-- Blog Detail Wrap -->
<section class="blog-detail-wrap">
  <div class="container">
    <div class="tamanio producto detail">

      <?php // Datos utilizados por el carrito ?>
      <input type="hidden" class="prod id" value="<?php echo $producto->id ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> carrito" value="0" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> cantidad" value="1" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> porc_iva" value="<?php echo $producto->porc_iva ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> peso" value="<?php echo $producto->peso ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> ancho" value="<?php echo $producto->ancho ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> alto" value="<?php echo $producto->alto ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> profundidad" value="<?php echo $producto->profundidad ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> envio_gratis" value="<?php echo $producto->no_totalizar_reparto ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> precio" value="<?php echo ($producto->precio_final_dto)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> nombre" value="<?php echo $producto->nombre ?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> categoria" value="<?php echo ($producto->rubro)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> imagen" value="<?php echo ($producto->path)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> fragil" value="<?php echo ($producto->fragil)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> stock" value="<?php echo ($producto->stock)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> coordinar_envio" value="<?php echo ($producto->coordinar_envio)?>" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> descripcion" value="" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_1" value="0" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_2" value="0" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_3" value="0" />
      <input type="hidden" class="prod_<?php echo $producto->id ?> id_variante" value="0" />      

      <div class="detalle">
        <div class="row">
          <div class="col-sm-6">
            <div class="caru">
              <div id="carouselExampleControls" class="carousel slide " >
                <div class="">
                  <div class="carousel-inner">
                    <?php $i=0; foreach($producto->images as $img) { ?>
                      <div class="carousel-item <?php echo ($i==0)?"active":"" ?>">
                        <img src="<?php echo $img->path ?>" class="d-block w-100" alt="...">
                      </div>
                      <?php $i++;
                    } ?>
                  </div>
                  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>
                <ol class="carousel-indicators">
                  <?php $i=0; foreach($producto->images as $img) { ?>
                    <li data-target="#carouselExampleCaptions" data-slide-to="<?php echo $i ?>" class="<?php echo ($i==0)?"active":"" ?>"></li>
                  <?php } ?>
                </ol>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class=" texto">
              <h3><?php echo $producto->nombre ?></h3>
              <h4><?php echo $producto->precio_final ?> €</h4>
              <div class="listas">  
                <h6 class="negrita">ELIGE ENTRE:</h6>
                <?php echo $producto->texto ?>
              </div>
            </div>
            <div class="posi">
              <div class="formato-container">
                <h6 class="color-h6">Producto disponible en formato:</h6>

                <?php 
                function ordenar_opciones($a,$b) {
                  return strcmp($a["nombre"], $b["nombre"]);
                } 
                // PROPIEDADES
                if (sizeof($producto->propiedades)>0) { ?>
                  <div id="articulo_propiedades" class="oh mt20">
                    <?php $j=0; foreach($producto->propiedades as $id_propiedad => $prop) { ?>
                      <div data-numero="<?php echo $j ?>" class="propiedad_fila row">
                        <h4 style="display: none" class="opcion_titulo"><?php 
                          $nombre_propiedad = $prop["nombre"];
                          if ($nombre_propiedad == "Indumentaria") $nombre_propiedad = "Talle";
                          else if ($nombre_propiedad == "Calzado") $nombre_propiedad = "Tama&ntilde;o";
                          echo $nombre_propiedad;
                        ?></h4>
                        <?php 
                        usort($prop["opciones"],"ordenar_opciones");
                        foreach($prop["opciones"] as $opcion) { 

                          // Si hay una unica propiedad, podemos marcar que no tienen stock
                          $sin_stock = "";
                          if (sizeof($producto->propiedades) == 1) {
                            $stock_variante = -1;
                            foreach($producto->variantes as $variante) {
                              if (($variante->id_propiedad_1 == $id_propiedad || $variante->id_propiedad_2 == $id_propiedad || $variante->id_propiedad_3 == $id_propiedad)
                                && ($variante->id_opcion_1 == $opcion["id"] || $variante->id_opcion_2 == $opcion["id"] || $variante->id_opcion_3 == $opcion["id"])) {
                                $stock_variante = $variante->stock;
                                break;
                              }
                            }
                            $sin_stock = ($stock_variante == 0) ? "sin_stock" : "";
                          } ?>
                          <div class="col col-sm-6 img">
                            <div class="dt">
                              <div class="dtc vam">
                                <img style="height: 80px; object-fit: contain;" class="img-detalle" src="<?php echo $opcion["path"] ?>">
                              </div>
                              <div class="dtc vam">
                                <h6 class="produc">
                                  <a data-id_propiedad="<?php echo $id_propiedad ?>" data-id="<?php echo $opcion["id"] ?>" href="javascript:void(0)" class="propiedad_opcion <?php echo $sin_stock ?> <?php echo (sizeof($prop["opciones"]) == 1) ? "active":"" ?>">
                                    <?php echo $opcion["etiqueta"] ?>
                                  </a>                                      
                                </h6>
                              </div>
                            </div>
                          </div>

                        <?php } ?>
                      </div>
                    <?php $j++; } ?>
                  </div>
                <?php } ?>

              </div>  
              <div class="botones">
                <div class="row">
                  <div class="col-sm-6 comprar">
                    <a class="btn" onclick="modificar_item(<?php echo $producto->id ?>)" href="javascript:void(0)" data-id="<?php echo $producto->id ?>">Comprar Ahora</a>
                  </div>
                  <a href="<?php echo mklink('web/activacion/')?>">
                    <div class="row">
                      <div class="col-sm-12 p">
                        <button href="<?php echo mklink('web/activacion/')?>" class="cuadrado">
                          <h6>¿Ya tienes este producto?</h6>
                          <span>¡Actívalo haciendo click aquí!</span>
                        </button>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>   
        </div>
      </div>
    </div>
    
  </div>
</section>

<?php include("includes/newsletter.php") ?>

<?php include("includes/footer.php") ?>

<script type="text/javascript">
var variantes = new Array();
<?php foreach($producto->variantes as $v) { ?>
  variantes.push(<?php echo json_encode($v)?>);
<?php } ?>

$(document).ready(function(){
  $(".propiedad_opcion").click(function(e) {
    if ($(e.currentTarget).hasClass("sin_stock")) return;
    var id_articulo = $(e.currentTarget).data("id_articulo");
    var id_propiedad = $(e.currentTarget).data("id_propiedad");
    $(".propiedad_opcion[data-id_propiedad="+id_propiedad+"]").removeClass("active");
    $(e.currentTarget).addClass("active");
    // Movemos la imagen a la que corresponde
    if ($("li[data-id_propiedad_1="+id_propiedad+"]").length > 0) 
      $("li[data-id_propiedad_1="+id_propiedad+"]:first").trigger("click");
    else if ($("li[data-id_propiedad_2="+id_propiedad+"]").length > 0) 
      $("li[data-id_propiedad_2="+id_propiedad+"]:first").trigger("click");
    else if ($("li[data-id_propiedad_3="+id_propiedad+"]").length > 0) 
      $("li[data-id_propiedad_3="+id_propiedad+"]:first").trigger("click");

    // Controlamos la variante seleccionada
    controlar_variantes(id_articulo);
  });
})
</script>

</body>
</html>