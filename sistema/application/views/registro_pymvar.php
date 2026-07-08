<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Registro Pymvar.com</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<base href="<?php echo $base_url; ?>"/>
<link rel="stylesheet" type="text/css" href="resources/css/common.css"/>
<link rel="stylesheet" href="resources/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="resources/css/animate.css" type="text/css" />
<link rel="stylesheet" href="resources/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="resources/css/simple-line-icons.css" type="text/css" />
<link rel="stylesheet" href="resources/css/font.css" type="text/css" />
<link rel="stylesheet" href="resources/css/app.css" type="text/css" />
<link rel="stylesheet" href="resources/js/jquery/ui/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="resources/css/loader.css" media="screen"/>
<link rel="stylesheet" href="resources/css/sortable.css"/>
<link rel="stylesheet" href="resources/css/cropper.min.css"/>
<link rel="stylesheet" href="resources/css/cropper.css"/>
<style type="text/css">
@media(min-width:1200px) {
    .container { width: 800px; }    
}
.loader { display: none }
.seccion_vacia .h1 { margin-top: 40px; }
.seccion_vacia .h3 { margin-bottom: 40px; }
#configuracion_color { display: none; }
</style>
</head>
<body>
<div class="container">
    <div class="wrapper-md">
        <div class="seccion_vacia">
          <h1 class="h1">Bienvenido a su cuenta de Pymvar!</h1>
          <h3 class="h3">Complete los siguientes datos para empezar a trabajar</h3>
        </div>    
        <form method="post" action="/sistema/registro/guardar_pymvar/" onsubmit="return guardar()" class="tab-container">
            
            <input type="hidden" name="id_proyecto" value="1"/>
            
            <ul class="nav nav-tabs" role="tablist">
                <li class="active">
                    <a id="tab1_link" href="#tab1" role="tab" data-toggle="tab">Paso 1</a>
                </li>
                <li>
                    <a id="tab2_link" href="#tab2" role="tab" data-toggle="tab">Paso 2</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab1" class="tab-pane active">
                    <h4>Datos de su empresa</h4>
                    <div>
                      <p>Razon Social:</p>
                      <input type="text" placeholder="Nombre de persona fisica o juridica" id="registro_razon_social" name="razon_social" class="form-control" >
                      <p class="m-t">Tipo de Contribuyente:</p>
                      <select id="registro_tipo_iva" name="id_tipo_contribuyente" class="form-control">
                        <?php foreach($tipos_iva as $t) { ?>
                            <?php if($t->id!=4) { ?>
                                <option value="<?php echo $t->id; ?>"><?php echo $t->nombre; ?></option>
                            <?php } ?>
                        <?php } ?>
                      </select>
                      <p class="m-t">CUIT:</p>
                      <input type="text" id="registro_cuit" name="cuit" class="form-control" >
                      <p class="m-t">N&uacute;mero de Punto de Venta electr&oacute;nico (tipo RECE / Web Service):</p>
                      <input type="number" id="registro_punto_venta" name="punto_venta" class="form-control" >                      
                      <div class="line line-dashed b-b line-lg"></div>
                      <div class="m-t m-b text-right">
                        <img class="loader m-r" src="resources/images/ajax-loader.gif"/>
                        <a href="javascript:void(0)" id="registro_siguiente" class="btn btn-success">Siguiente</a>
                      </div>
                    </div>
                </div>
                <div id="tab2" class="tab-pane">
                    <h4>Configura tu dise&ntilde;o</h4>
                    <div>
                        <p>Logo:</p>
                        <div>
                            <img id="preview_path" style="max-width: 60px; max-height: 60px;display:none"/>
                            <input id="hidden_path" type="hidden" name="path"/>
                            <input id="path_data" class="hidden_data" type="hidden"/>
                            <input id="path_src" class="hidden_src" type="hidden"/>
                            <div class="bootstrap-filestyle-container" style="display:inline-block">
                                <input id="path" class="inputFile" type="file" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                <div class="bootstrap-filestyle input-group">
                                    <input type="text" class="form-control" disabled="">
                                    <span class="group-span-filestyle input-group-btn" tabindex="0">
                                        <label for="path" class="btn btn-default ">
                                            <span class="glyphicon glyphicon-folder-open m-r-xs"></span>
                                            Elegir Archivo
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p class="m-t">Nombre de fantasia:</p>
                        <input type="text" name="nombre" placeholder="Nombre que aparecera en el encabezado de los comprobantes" id="registro_nombre" class="form-control">
                        <p class="m-t">Dise&ntilde;o de Comprobantes:</p>
                        <div class="oh">
                            <div class="template_cont col-xs-6 col-md-4">
                                <div>
                                    <img src="/sistema/application/views/reports/factura/modelo1/thumb.png" class="b b-a b-info wrapper-xs bg-white img-responsive">
                                </div>
                                <div class="radio">
                                    <label class="i-checks">
                                      <input type="radio" name="disenio_factura" value="basico" checked="">
                                      <i></i>Clasico
                                    </label>
                                </div>                                
                            </div>
                            <div class="template_cont col-xs-6 col-md-4">
                                <div>
                                    <img src="/sistema/application/views/reports/factura/modelo1/thumb.png" class="b b-a wrapper-xs bg-white img-responsive">
                                </div>
                                <div class="radio">
                                    <label class="i-checks">
                                      <input type="radio" name="disenio_factura" value="modelo1">
                                      <i></i>Moderno
                                    </label>
                                </div>                                
                            </div>                            
                        </div>
                        
                        <div id="configuracion_color">
                            <p class="m-t">Color principal:</p>
                            <select name="disenio_factura_color" id="registro_color" class="form-control">
                                <option value="dark_blue" selected="">Azul Oscuro</option>
                                <option value="blue">Azul</option>
                                <option value="red">Rojo</option>
                                <option value="yellow">Amarillo</option>
                                <option value="pink">Rosa</option>
                                <option value="green">Verde</option>
                            </select>
                        </div>
                        
                        <div class="line line-dashed b-b line-lg"></div>
                        <div class="m-t m-b oh text-right">
                            <img class="loader m-r" src="resources/images/ajax-loader.gif"/>
                            <button class="btn btn-success">LISTO!</button>
                        </div>
                    </div>
                </div>
                
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="resources/js/jquery.js"></script>
<script type="text/javascript" src="resources/js/underscore.js"></script>
<script type="text/javascript" src="resources/js/backbone.js"></script>
<script type="text/javascript" src="resources/js/backbone.paginator.js"></script>
<script type="text/javascript" src="resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="resources/js/main.js"></script>
<script type="text/javascript" src="resources/js/jquery.maskedinput.js"></script>
<script type="text/javascript" src="resources/js/html5-file-upload/js/jquery.filedrop.js"></script>
<script type="text/javascript" src="resources/js/md5.js"></script>
<script type="text/javascript" src="resources/js/libs/bootstrap.min.js"></script>
<script type="text/javascript" src="resources/js/common.js"></script>
<script type="text/javascript" src="resources/js/cropper.min.js"></script>
<script type="text/javascript" src="resources/js/cropper-main.js"></script>
<script type="text/javascript">
var url = "";
function isImageFile(file){
    if (file.type) {
      return /^image\/\w+$/.test(file.type);
    } else {
      return /\.(jpg|jpeg|png|gif)$/.test(file);
    }
}
        
$(document).ready(function(){
    
    $("#registro_cuit").mask("99-99999999-9");
    
    $("#registro_siguiente").click(function(){
        $("#tab2_link").trigger("click");
    });
    
    $("input[name='disenio_factura']").change(function(e){
        $(".template_cont").find(".b-a").removeClass("b-info");
        $(e.currentTarget).parent().parent().parent().find(".b-a").addClass("b-info");
        
        var v = $("input[name=disenio_factura]:checked").val();
        if (v=="modelo1") {
            $("#configuracion_color").show();    
        } else {
            $("#configuracion_color").hide();    
        }
    });
    
    $(".inputFile").change(function(e){
        
        var self = window;
        $(e.currentTarget).next(".bootstrap-filestyle").find("input[type=text]").val($(e.currentTarget).val());
        
        files = $(e.currentTarget).prop('files');
        if (files.length > 0) {
          file = files[0];
          if (window.isImageFile(file)) {
            if (window.url) {
              URL.revokeObjectURL(window.url); // Revoke the old one
            }
            window.url = URL.createObjectURL(file);
            
            $(e.currentTarget).parent().parent().find(".hidden_src").val(file.name); // Nombre del archivo
            
            var d = "<div class='modal-content'>";
            d+="<div class='modal-header'><h3 class='modal-title'>Editar Imagen</h3></div>";
            d+="<div class='modal-body'>";
            d+="<img src='"+window.url+"'>";
            d+="</div>";
            d+="<div class='modal-footer'>";
            d+="<button class='crop-ok btn btn-success pull-right'>Aceptar</button>";
            d+="</div>";
            d+="</div>";
            var c = $(d);
            c.find("img").cropper({
                aspectRatio: 1,
                strict: true,
                crop: function (data) {
                    var json = [
                          '{"x":' + data.x,
                          '"y":' + data.y,
                          '"height":' + data.height,
                          '"width":' + data.width,
                          '"rotate":' + data.rotate + '}'
                        ].join();
                    $(e.currentTarget).parent().parent().find(".hidden_data").val(json);
                }
            });
            // Aceptar
            c.on("click",".crop-ok",function(){
                $('.modal:last').modal('hide');
                self.subir_foto();
            });
            // Abrimos el lightbox
            crearLightboxHTML({
                "html":c,
                "width":700,
                "height":400,
            });
          }
        }        
    });
    
});

function subir_foto() {
    var path = $("#path").val();
    if (!isEmpty(path)) {
        $(".loader").show();
        var formData = new FormData();
        formData.append("filename",$("#path_src").val()); // Nombre del archivo
        formData.append("data",$("#path_data").val()); // Datos
        formData.append("registro","1"); // Indica que no debe tomar el id_empresa porque todavia no fue creada
        formData.append("path",$("#path")[0].files[0]);
        $.ajax({
            url: '/sistema/empresas/function/save_image/',
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(r){
                if (!isEmpty(r.path)) {
                    $("#hidden_path").val(r.path);
                    $("#preview_path").attr("src",r.path);
                    $("#preview_path").show();
                    $("#preview_path").nextAll("i").show();
                    $("#preview_path").nextAll(".bootstrap-filestyle-container").hide();
                }
                $(".loader").hide();
            }
        });
    }       
}

function guardar() {
    
    if ($(".loader:last").is(":visible")) {
        alert("Por favor espere hasta que se termine de subir la imagen.");
        return false;
    }
    
    var razon_social = $("#registro_razon_social").val();
    if (isEmpty(razon_social)) {
        $("#tab1_link").trigger("click");
        alert("Ingrese la razon social de su empresa");
        $("#registro_razon_social").select();
        return false;
    }
    
    var cuit = $("#registro_cuit").val();
    if (isEmpty(cuit)) {
        $("#tab1_link").trigger("click");
        alert("Ingrese el CUIT de su empresa");
        $("#registro_cuit").select();
        return false;
    }
    
    var punto_venta = $("#registro_punto_venta").val();
    if (isEmpty(punto_venta)) {
        $("#tab1_link").trigger("click");
        alert("Ingrese el numero de punto de venta electronico (tipo RECE/Web Service) configurado en AFIP. Por ej: 2.");
        $("#registro_punto_venta").select();
        return false;
    }
    
    var nombre = $("#registro_nombre").val();
    if (isEmpty(nombre)) {
        $("#tab2_link").trigger("click");
        alert("Ingrese el nombre de su empresa");
        $("#registro_nombre").select();
        return false;
    }
    
    return true;
}
</script>
 
</body>
</html>