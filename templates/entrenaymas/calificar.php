<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/init.php"); 
$id = isset($_GET["id"]) ? $_GET["id"] : 0;
$profesional = $usuario_model->get($id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include("includes/head.php"); ?>
</head>
<style>
  @font-face {
    font-family: 'Material Icons';
    font-style: normal;
    font-weight: 400;
    src: local('Material Icons'), local('MaterialIcons-Regular'), url(https://fonts.gstatic.com/s/materialicons/v7/2fcrYFNaTjcS6g4U3t-Y5UEw0lE80llgEseQY3FEmqw.woff2) format('woff2'), url(https://fonts.gstatic.com/s/materialicons/v7/2fcrYFNaTjcS6g4U3t-Y5RV6cRhDpPC5P4GCEJpqGoc.woff) format('woff');
  }
  i {
    cursor :  pointer;
  }
</style>
<body class="wflight">

<?php include("includes/header.php"); ?>

<section class="psweb-search search-style-two search-spacer-three">
  <div class="container">
    <div class="search-wrap">
      <div class="search-form">
        <?php include("includes/searchform.php"); ?>
      </div>
    </div>
  </div>
</section>

<div id="calificar" class="pb40 pt40">
  <div class="sem-container">
    <div class="doctor-style-two doctor-style-three" id="profesional_c">

      <h3 class="bold mb15">Calificación para:</h3>

      <div class="doctor-item">

          <div class="doctor-quote"><i class="fa fa-bookmark" aria-hidden="true"></i></div>

          <div class="psweb-image">
            <?php if (!empty($profesional->path)) { ?>
              <img src="<?php echo $profesional->path ?>" alt="<?php echo ($profesional->nombre);?>">
            <?php } else if (!empty($empresa->no_imagen)) { ?>
              <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($profesional->nombre);?>">
            <?php } else { ?>
              <img src="images/no-imagen.png" alt="<?php echo ($profesional->nombre);?>">
            <?php } ?>
          </div>
            
          <div class="doctor-info">
            <?php if (sizeof($profesional->titulos)>0) { ?>
              <div class="oh">
                <?php foreach($profesional->titulos as $titulo) { ?>
                  <span class="doctor-designation fl mr10"><?php echo $titulo->nombre ?></span>
                <?php } ?>
              </div>
            <?php } ?>
            <h4><?php echo ucwords(mb_strtolower($profesional->nombre)) ?></h4>
            <?php if (!empty($profesional->cargo)) { ?>
              <h5>Número Colegiado: <?php echo $profesional->cargo ?></h5>
            <?php } ?>
            </div>
          </div>
        </div>
      <div class="calificardiv">
        <form class="calificarform" onsubmit="return enviar_calificacion()">
          <input type="hidden" value="<?php echo $id;?>" id="id_usuario">
          <label for="calificar_nombre" class="mt20">Nombre:</label>
          <input class="form-control" type="text" id="calificar_nombre" placeholder="Ingrese su nombre">
          <label for="calificar_comentario" class="mt10">Comentarios:</label>
          <textarea class="form-control" id="calificar_comentario" rows="5" placeholder="Ingrese su comentario"></textarea>
          <div class="rating mt20 mb10 textcenter"></div>
          <input type="submit" class="psweb-btn mt20" value="Calificar" style="margin: 0 auto; display: block;">
        </form>
      </div>
    </div>
  </div>

<?php include("includes/newsletter.php") ?>
<?php include("includes/footer.php") ?>
<script>
  $(document).ready(function(){
    $('.rating').addRating();
    $(".calificarform").addEventListener("submit", function(event){
      event.stopPropagation();
      event.preventDefault();
      enviar_calificacion();
    });
  })

  var flag = 0;
  function enviar_calificacion(){

    var rating = $(".rating .selected").length;
    var id = $("#id_usuario").val();
    var nombre = $('#calificar_nombre').val();
    var comentario = $('#calificar_comentario').val();

    if (isEmpty(nombre)) {
      alert("Por favor ingrese su nombre");
      $("#calificar_nombre").focus();
      return false;              
    }   

    if (isEmpty(comentario)) {
      alert("Por favor ingrese un comentario");
      $("#calificar_comentario").focus();
      return false;              
    }

    if (rating == 0) {
      alert ("Por favor ingrese una calificación");
      return false;
    }

    if (flag == 1) return false;
    flag = 1;    
    $.ajax({
      "url":"/sistema/entrenaymas/function/guardar_calificacion", // Archivo que llama
      "dataType":"json", // Tipo de datos enviados
      "type":"post", // Metodo de envio
      "data":{ // Datos enviados
        "nombre":nombre,
        "comentario": comentario,
        "rating": rating,
        "id": id,
        "id_empresa": ID_EMPRESA,
      },
      "success":function(r){
        flag = 0;
        if (r.error==0){
          alert (r.mensaje);
          location.href = "<?php echo mklink ('/')?>";
        } else {
          alert (r.mensaje_error);
        }
      },
      "error":function() {
        flag = 0;
      }
    })
    return false;
  }
</script>
</body>
</html>