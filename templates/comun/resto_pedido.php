<?php
include_once("models/Delivery_Model.php");
$delivery_model = new Delivery_Model($conx);
$comercio = $delivery_model->get_empresa($empresa->id);
$id_cliente = 0;
$direcciones = array();

// Si el usuario esta logueado
if (isset($_SESSION["id_cliente"]) && isset($_SESSION["nombre"])) {
  $id_cliente = $_SESSION["id_cliente"];
}
?>
<style type="text/css">
.resto-wrapper{float:left; width:100%;font-size:16px; font-family:'Calibri'; color:#1e1e1e; background:#f4f4f4;padding: 30px 0px}

/*MENU LISTING CSS*/
.resto-wrapper .menu-listing{float:left; width:100%; padding-bottom:5px;}
.resto-wrapper .menu-listing:last-child{padding-bottom:0;}
.resto-wrapper .white-box{float:left; width:100%; box-sizing:border-box; -webkit-box-sizing:border-box; background:#fff; border:1px solid #ddd; padding:15px; margin-bottom:28px; border-radius:5px; -webkit-border-radius:5px;}

/*MENU LIST CSS*/
.resto-wrapper .menu-list{ overflow: hidden; margin-bottom: 15px; padding:10px 20px 12px; background:#f4f4f4; -webkit-transition:all 0.3s ease 0s; -moz-transition:all 0.3s ease 0s; -o-transition:all 0.3s ease 0s; transition:all 0.3s ease 0s; display: table; width: 100%; }
.resto-wrapper .menu-list:hover{background:#fff4de; cursor: pointer;}
.resto-wrapper .menu-list > span { display: table-cell; vertical-align: middle; }
.resto-wrapper .modal-content .menu-list { margin-bottom: 0px; }
.resto-wrapper .modal .menu-list:hover{background:#f4f4f4;}
.resto-wrapper .menu-name{width:100%; font-size:18px; font-family:'Calibri-Bold';}
.resto-wrapper .menu-list p{font-size:14px; line-height:normal;}
.resto-wrapper .menu-list .precio { text-align: right; font-size:24px; width: 100px; padding-right: 5px; }
.resto-wrapper .menu-list .mas{ cursor: pointer; font-size: 22px; display: inline-block; }
.resto-wrapper .menu-list .mas_cont { width: 46px; text-align: center; display: table-cell; vertical-align: middle; }
.resto-wrapper .menu-list:hover .mas{background-position:0 -73px;}
@media(max-width: 792px) {
  .resto-wrapper .menu-list > span:first-child { display: block; width: 100%; }
  .resto-wrapper .menu-list .mas_cont { float: right; width: 50%; text-align: right; }
  .resto-wrapper .menu-list .precio { float: left; width: 50%; text-align: left; }
}

/*LOCATION LIST CSS*/
.resto-wrapper .location-list{ float: left; width: 100%; margin-bottom: 30px; display:block; overflow:hidden; clear:both; border-radius:5px 5px 0 0; -webkit-border-radius:5px 5px 0 0;}
.resto-wrapper .location-list h5{color:#323a45; font-family:'Calibri-Bold';}
.resto-wrapper .location-list .white-box{padding:0; margin:0; overflow:hidden;}
.resto-wrapper .refine-listing{display:block; overflow:hidden; clear:both; padding:20px 20px 15px; border-bottom:1px solid #ddd;}
.resto-wrapper .refine-listing.last{border:none;}
.resto-wrapper .red-counter{float:left; color:#b52020; padding-left:5px;}
.resto-wrapper .location-list .btn{display:block; overflow:hidden; clear:both; padding:10px 10px 12px; margin:0 0 5px;}

/*TOGGLE TITLE CSS*/
.resto-wrapper .location-title{display:block; overflow:hidden; padding:0 0; background:#b52020; border-radius:5px 5px 0 0; -webkit-border-radius:5px 5px 0 0; font-size:21px; text-transform:uppercase; color:#fff;}
.resto-wrapper .location-title a{width:50px; float:left; padding:13px 0 11px; background:#454545; text-align:center; border-right:1px solid #fff; border-bottom:4px solid #5b5b5b;}
.resto-wrapper .location-title a img{float:none;}
.resto-wrapper .location-title span{display:block; overflow:hidden; padding:12px 20px; border-bottom:4px solid #b52020;}
.resto-wrapper .location-title .fa { color: white; }
.resto-wrapper .location-title .trash-button { float: right; }
.resto-wrapper .request-list { padding: 10px; }

/*PLACE ORDFER CSS*/
.resto-wrapper .place-order{float:left; width:100%; padding-top:20px;}
.resto-wrapper .place-order .block{padding-bottom:20px;}
.resto-wrapper .place-order input[type="text"], .resto-wrapper .place-order select {width:100%; box-sizing:border-box; -webkit-box-sizing:border-box; background:#f8f8f8; color:#777; font-size:20px; border:1px solid #e2e2e2;}
.resto-wrapper .place-order input[type="submit"]{float:right; height:51px;}
.resto-wrapper .place-order .jqTransformSelectWrapper{background:#f8f8f8; color:#777; font-size:20px; height:49px; border-radius:5px; -webkit-border-radius:5px;}
.resto-wrapper .place-order .jqTransformSelectWrapper span{line-height:47px; padding-left:17px;}
.resto-wrapper .place-order .jqTransformSelectWrapper ul{top:45px;}

.resto-wrapper .descripcion-hidden { display: none; }
.modal .block { margin-bottom: 15px; }
.modal-content { overflow: hidden; padding: 15px; }
.modal .modal-input { width: 100%; padding: 5px 10px; font-family: Arial; font-size: 16px; border: solid 1px #eee; color: #222; }
</style>
<div class="resto-wrapper">
  <div class="container">
    <div class="row">
      <div class="col-md-9"> 
        <!--WHITE BOX-->
        <div class="white-box"> 
          <div class="restaurant"> 
            <div id="menu"> 
              <!--MENU LISTING-->
              <div class="menu-listing">
                <div class="row">
                  <?php foreach($comercio->productos as $categoria => $prods) { ?>
                    <?php if (!empty($prods)) { ?>
                      <div class="col-xs-12">
                        <h4><?php echo ($categoria); ?></h4>
                      </div>
                      <?php $h=0; foreach($prods as $p) { ?>
                        <div class="col-md-6">
                          <a data-id="<?php echo $p->id ?>" class="modal_pedido menu-list">
                            <span>
                              <span class="menu-name titulo"><?php echo utf8_encode($p->nombre); ?></span><br/>
                              <span class="descripcion"><?php echo (strlen($p->descripcion)>50) ? substr($p->descripcion,0,50)."..." : $p->descripcion ?></span>
                              <span class="descripcion-hidden"><?php echo $p->descripcion ?></span>
                            </span>
                            <span class="precio">
                              $<?php echo $p->precio_final_dto; ?>
                            </span>
                            <span class="mas_cont">
                              <span class="mas"><i class="fa fa-shopping-cart"></i></span>
                            </span>
                          </a>
                        </div>
                      <?php $h++; } ?>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3" > 
        <div id="pedido_contenedor" class="location-list"> 
          <!--WHITE BOX-->
          <div class="white-box">
            <div class="location-title">
              <a href="javascript:void(0);" class="toggle-button" onClick="$('#first').slideToggle();">
                <i class="fa fa-bars"></i>
              </a>
              <span class="pull-left">
                Mi pedido
              </span>
              <a class="trash-button" onclick="eliminar_pedido()" href="javascript:void(0)"><i class="fa fa-trash"></i></a>
            </div>
            <div id="first">
              <div id="pedido_container" class="request"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
  
<div id="modal" class="modal">
  <div class="vertical-alignment-helper">
    <div class="modal-dialog vertical-align-center">  
      <div class='modal-content'>
        <div class="menu-list">
          <div style="overflow: hidden;">
            <div class="menu-name pull-left" id="modal_titulo"></div>
            <a title="Cerrar" style="float: right;" href="javascript:void(0)" onclick="$('.modal-backdrop').trigger('click')">
              <i class="fa fa-times"></i>
            </a>
          </div>
          <input type="hidden" id="modal_id"/>
          <div class="block">
            <div id="modal_descripcion"></div>
          </div>
          <div class="place-order">
            <div class="row">
              <div class="col-xs-6">
                <div class="block">
                  <select class="modal-input" id="modal_cantidad">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                    <option>11</option>
                    <option>12</option>
                    <option>13</option>
                    <option>14</option>
                    <option>15</option>
                    <option>16</option>
                    <option>17</option>
                    <option>18</option>
                    <option>19</option>
                    <option>20</option>
                  </select>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="pull-right"><span style="font-size: 24px" id="modal_precio"></span></div>
              </div>
            </div>
            <div class="block">
              <input id="modal_aclaraciones" class="modal-input" type="text" placeholder="Agregar aclaraciones..." />
            </div>
            <div id="modal_ingredientes"></div>
            <div class="row">
              <div class="col-xs-12">
                <div class="block tar">
                  <input onclick="agregar_pedido()" type="submit" value="agregar al pedido" class="btn btn-danger" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  
<div id="modal_login" class="modal">
  <div class="vertical-alignment-helper">
    <div class="modal-dialog vertical-align-center">  
      <div class='modal-content'>
        <div class="menu-list">
          <div class="menu-name">Ingrese al sistema</div>
          <p>Es necesario entrar al sistema para realizar un pedido.</p>
          <div class="place-order">
            <div class="clear">
            <a href="<?php echo mklink("login/")?>" class="btn btn-danger">Entrar</a>
            <a href="<?php echo mklink("web/registrate/")?>" class="btn btn-danger">Registrarse</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  
<div id="modal_pedido" class="modal">
  <div class="vertical-alignment-helper">
    <div class="modal-dialog vertical-align-center">
      <form onsubmit="return finalizar_pedido()" method="post" class='modal-content'>
        <div style="overflow: hidden;">
          <div class="menu-name pull-left mb15">Finalizar Pedido</div>
          <a title="Cerrar" style="float: right;" href="javascript:void(0)" onclick="$('.modal-backdrop').trigger('click')">
            <i class="fa fa-times"></i>
          </a>
        </div>
        <input type="hidden" name="guardar" value="1"/>
        <div class="block">
          <input id="modal_pedido_nombre" class="modal-input" type="text" value="<?php echo (isset($_COOKIE["nombre"]) ? $_COOKIE["nombre"] : "") ?>" placeholder="Nombre..." />
        </div>
        <div class="block">
          <input id="modal_pedido_telefono" class="modal-input" type="text" value="<?php echo (isset($_COOKIE["telefono"]) ? $_COOKIE["telefono"] : "") ?>" placeholder="Tel&eacute;fono..." />
        </div>
        <div class="block">
          <input id="modal_pedido_email" class="modal-input" type="text" value="<?php echo (isset($_COOKIE["email"]) ? $_COOKIE["email"] : "") ?>" placeholder="Email..." />
        </div>
        <div class="block">
          <input id="modal_pedido_direccion" class="modal-input" type="text" value="<?php echo (isset($_COOKIE["direccion"]) ? $_COOKIE["direccion"] : "") ?>" placeholder="Direcci&oacute;n de entrega..." />
        </div>
        <div class="block">
          <input id="modal_pedido_descripcion" class="modal-input" name="descripcion" type="text" placeholder="Aclaraciones (Ej: porton negro, tocar bocina, etc.)" />
        </div>
        <?php /*
        <div class="block">
          <input id="modal_pedido_abona_con" class="modal-input" name="abona_con" type="text" placeholder="Abona con..." />
        </div>
        */ ?>
        <div class="clear">
          <input type="submit" id="finalizar_pedido_btn" value="finalizar pedido" class="btn btn-danger btn-block"/>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">

// Indica los productos que tienen ingredientes
var productos_ingredientes = new Array();
<?php foreach($comercio->productos as $categoria => $prods) { ?>
  <?php if (!empty($prods)) { ?>
    <?php foreach($prods as $p) { ?>
      <?php foreach($p->ingredientes as $ing) { ?>
        productos_ingredientes.push({
          "id_articulo":<?php echo $ing->id_articulo ?>,
          "nombre":"<?php echo $ing->nombre ?>",
          "valores":"<?php echo $ing->valores ?>",
        });
      <?php } ?>
    <?php } ?>
  <?php } ?>
<?php } ?>

  
<?php if(isset($_SESSION["pedido"]) && !empty($_SESSION["pedido"])) { ?>
var pedido = <?php echo htmlspecialchars_decode($_SESSION["pedido"]); ?>;
<?php } else { ?>
// En esta variable se almacena el pedido
var pedido = nuevo_pedido();
<?php } ?>

const MAP_KEY = "AIzaSyAIhE0gYZFh5YoVMCGVBh2NCbV6x_y3lSU";

$(document).ready(function(){

  if ($(window).width()>792) {
    var maximo = 0;
    $(".menu-list").each(function(i,e){
      if ($(e).height()>maximo) maximo = $(e).height();
    });
    $(".menu-list").height(maximo);
  }

  render_pedido();

  // Cuando se abre el panel de informacion
  $("#mostrar_ubicacion").click(function(){
    setTimeout(function(){
      if (window.map == undefined) window.render_map();
      google.maps.event.trigger(window.map, "resize");
      window.map.setCenter(window.coor);
    },100);    
  });
  /*
  $("#modal_pedido_direcciones").change(function(e){
    mostrar_direccion();
  });
  */
});

/*
function mostrar_direccion() {
var latitud = $("#modal_pedido_direcciones option:selected").data("latitud");
var longitud = $("#modal_pedido_direcciones option:selected").data("longitud");
if (latitud == 0 && longitud == 0) {
  buscar_direccion();
} else {
  window.posicion_modal = new google.maps.LatLng(latitud+0,longitud+0);
  window.render_map_modal();    
}
$("#modal_pedido_indicacion").val($("#modal_pedido_direcciones option:selected").data("indicacion"));
$("#modal_pedido_direccion").val($("#modal_pedido_direcciones option:selected").data("direccion"));
}

var mapa_modal = null;
var posicion_modal = null;
var marcador_modal = null;

// Muestra el mapa dependiendo de la direccion seleccionada
function buscar_direccion() {
var direccion = "<?php //echo $_SESSION["direccion"] ?>";
direccion = direccion+", "+$("#buscar_ciudades option:selected").text();
direccion = direccion+", Argentina";
direccion = encodeURI(direccion);
$.ajax({
  "url":"https://maps.googleapis.com/maps/api/geocode/json?key="+MAP_KEY+"&address="+direccion,
  "dataType":"json",
  "success":function(response) {
  if (response.results.length > 0) {
    var r = response.results[0];
    window.posicion_modal = new google.maps.LatLng(r.geometry.location.lat,r.geometry.location.lng);
    window.render_map_modal();
  }
  }
});
}
*/


function finalizar_pedido() {
  
  var nombre = $("#modal_pedido_nombre").val();
  if (isEmpty(nombre)) {
    alert("Por favor ingrese su nombre.");
    $("#modal_pedido_nombre").focus();
    return false;    
  }

  var telefono = $("#modal_pedido_telefono").val();
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono.");
    $("#modal_pedido_telefono").focus();
    return false;    
  }

  var email = $("#modal_pedido_email").val();
  if (!validateEmail(email)) {
    alert("Por favor ingrese su email.");
    $("#modal_pedido_email").focus();
    return false;    
  }

  // Controlamos que tenga una direccion
  var direccion = $("#modal_pedido_direccion").val();
  if (isEmpty(direccion)) {
    alert("Por favor ingrese su direccion.");
    $("#modal_pedido_direccion").focus();
    return false;    
  }

  $("#finalizar_pedido_btn").attr("disabled","disabled");

  pedido.nombre = nombre;
  pedido.telefono = telefono;
  pedido.email = email;
  pedido.direccion = direccion;
  pedido.id_localidad = 192;
  pedido.localidad = "Chacabuco";
  pedido.id_empresa = <?php echo $comercio->id ?>;
  pedido.descripcion = $("#modal_pedido_descripcion").val();

  // Guardamos el pedido finalizado
  $.ajax({
    "url":"/sistema/pedidos_mesas/function/registrar/",
    "dataType":"json",
    "data":"pedido="+JSON.stringify(pedido),
    "type":"post",
    "success":function(r) {
      if (r.error == 0) {
        $("#finalizar_pedido_btn").removeAttr("disabled");
        alert("Su pedido ha sido enviado. Muchas gracias!");
        location.reload();
      }
    },
    "error":function() {
      $("#finalizar_pedido_btn").removeAttr("disabled");
    }
  })

  return false;
}
  
/*
function render_map_modal() {
  var mapOptions = {
    zoom: 16,
    center: window.posicion_modal
  }
  window.mapa_modal = new google.maps.Map(document.getElementById("modal_pedido_mapa"), mapOptions);
  
  // Place a draggable marker on the map
  window.marcador_modal = new google.maps.Marker({
    position: window.posicion_modal,
    map: window.mapa_modal,
    draggable:true,
    title:"Arrastralo a la direccion correcta"
  });
  }  
*/
  
function render_map() {
  
  var latitud = "<?php echo $comercio->latitud_comercio; ?>";
  var longitud = "<?php echo $comercio->longitud_comercio; ?>";
  if (latitud == 0 && longitud == 0) {
    latitud = -17.8036574; longitud = -63.5674321;
  }
  window.coor = new google.maps.LatLng(latitud,longitud);
  var mapOptions = {
    zoom: 16,
    center: self.coor
  }
  window.map = new google.maps.Map(document.getElementById("map"), mapOptions);
  
  var marker = new google.maps.Marker({
    position: coor,
    map: map,
  });
}
  
   
function nuevo_pedido() {
  return {
    id_empresa: <?php echo $comercio->id; ?>,
    empresa: "<?php echo ($comercio->nombre); ?>",
    link: "<?php echo $comercio->link ?>",
    costo_envio: <?php echo (!empty($comercio->costo_envio)?$comercio->costo_envio:0) ?>,
    pedido_minimo: <?php echo (!empty($comercio->pedido_minimo))?$comercio->pedido_minimo:0 ?>,
    total: 0,
    items: [],
  };  
}
  

$(document).ready(function(){
  
  $(".modal_pedido").click(function(e){
    var id = $(e.currentTarget).data("id");
    var p = $(e.currentTarget);
    var titulo = $(p).find(".titulo").html();
    var descripcion = $(p).find(".descripcion-hidden").html();
    var precio = $(p).find(".precio").html();
    
    // Controlamos si el item ya fue pedido
    var encontro = false;
    /*
    for(var i=0;i<pedido.items.length;i++) {
      var p = pedido.items[i];
      if (p.id == id) {
        $("#modal_aclaraciones").val(p.aclaracion);
        $("#modal_cantidad").val(p.cantidad).trigger("change");
        encontro = true;
      }
    }
    */
    if (!encontro) {
      $("#modal_aclaraciones").val("");
      $("#modal_cantidad").val(1).trigger("change");      
    }

    // Ingredientes del producto
    var s = "";
    $("#modal_ingredientes").empty();
    for(var i=0;i<productos_ingredientes.length;i++) {
      var prod = productos_ingredientes[i];
      if (prod.id_articulo == id) {
        s+='<div class="col-xs-12">';
        s+='<select style="width:100%; margin-bottom: 15px;" class="modal_ingredientes">';
        s+='<option value="">'+prod.nombre+'</option>';
        var valores = prod.valores.split(",");
        for(var j=0;j<valores.length;j++) {
          var valor = valores[j];
          s+='<option>'+valor+'</option>';
        }
        s+='</select>';
        s+='</div>';
      }
    }
    if (!isEmpty(s)) {
      s = "<h4 style='font-size: 18px; font-weight: bold;'>Podes elegir alguna variante</h4><div class='row'>"+s+'</div>';
    }
    $("#modal_ingredientes").html(s);

    $("#modal_id").val(id);
    $("#modal_titulo").html(titulo);
    $("#modal_descripcion").html(descripcion);
    $("#modal_precio").html(precio);
    $("#modal").modal({
      show: true
    });
  });
});
  
function agregar_pedido() {

  var id = $("#modal_id").val();
  var titulo = $("#modal_titulo").html().trim();
  var precio = $("#modal_precio").html().trim();
  precio = precio.replace("$","").trim();
  var descripcion = $("#modal_descripcion").html().trim();
  var cantidad = $("#modal_cantidad").val().trim();
  var aclaracion = $("#modal_aclaraciones").val().trim();

  if ($(".modal_ingredientes").length > 0) {
    $(".modal_ingredientes").each(function(i,e){
      if (!isEmpty($(e).val())) {
        aclaracion += ((!isEmpty(aclaracion))?" - ":"")+$(e).val();
      }
    });
  }
  
  // Controlamos si el item ya fue pedido
  var encontro = false;
  /*
  for(var i=0;i<pedido.items.length;i++) {
    var p = pedido.items[i];
    if (p.id == id) {
      encontro = true;
      p.aclaracion = aclaracion;
      p.cantidad = cantidad;
      p.subtotal = cantidad * precio;
    }
  }
  */
  if (!encontro) {
    pedido.items.push({
      "id":id,
      "titulo":titulo,
      "precio":precio,
      "descripcion":descripcion,
      "cantidad":cantidad,
      "subtotal":cantidad*precio,
      "aclaracion":aclaracion,
    });    
  }
  render_pedido();
  $('#modal').modal('hide');
  //$('#pedido_contenedor').animatescroll();
}
  
function render_pedido() {
  var str = "";
  // El pedido esta vacio
  if (pedido.items.length == 0) {
    str+= '<div class="request-list">Pedi comida online gratis y paga en casa.</div>';
    // Guardamos el pedido en la session
    $.ajax({
      "url":"/sistema/pedidos/function/guardar_session/",
      "dataType":"json",
      "type":"post",
      "data":{
        "pedido":""
      },
    });
  } else {
    // El pedido existe, controlamos si es de esta empresa
    if (pedido.id_empresa == <?php echo $comercio->id ?>) {
      var subtotal = 0;
      for(var i=0;i<pedido.items.length;i++) {
        var p = pedido.items[i];
        str+= '<div class="request-list">';
        str+= '<select onchange="modificar_item('+p.id+',this)">';
        for(j=1;j<=20;j++) {
        str+='<option '+((j==p.cantidad)?"selected":"")+'>'+j+'</option>';
        }
        str+= '<select>';
        str+= '<div class="request-details"><div class="pull-left">'+p.titulo+((!isEmpty(p.aclaracion))?"<br/>"+p.aclaracion:"")+'</div></div>';
        str+= '<div class="request-close"><a onclick="eliminar_item('+p.id+')" href="javascript:void(0)"><i class="fa fa-times"></i></a></div>';
        str+= '<div class="request-price">$'+Number(p.subtotal).toFixed(2)+'</div>';
        str+= '</div>';
        subtotal+=p.subtotal;
      }
      str+= '<div class="total-price">';
      str+= '<dl><dt>Subtotal:</dt><dd>$'+Number(subtotal).toFixed(2)+'</dd></dl>';
      str+= '<dl><dt>Delivery:</dt><dd>$'+Number(pedido.costo_envio).toFixed(2)+'</dd></dl>';
      str+= '<dl><dt><strong>Total:</strong></dt><dd><span>$'+Number(subtotal + pedido.costo_envio).toFixed(2)+'</span></dd></dl>';
      str+= '</div>';
      str+= '<div class="request-button"><a class="btn btn-danger" onclick="aceptar_pedido()" href="javascript:void(0)">finalizar pedido</a></div>';
      str+= '<div class="visible-xs-block request-button pt0"><a class="btn btn-warning" onclick="$(\'#content-tab\').animatescroll();" href="javascript:void(0)">seguir comprando</a></div>';
      pedido.total = subtotal;
    
    } else {
      // El pedido existe, pero no es de esta empresa. Le consultamos al usuario que desea hacer, si ir a la empresa que hizo el pedido o eliminarlo
      str+= "<div class='request-list'>Tenes un pedido pendiente a "+pedido.empresa+". ";
      str+="<a class='rojo' href='"+pedido.link+"'>Ir a "+pedido.empresa+"</a> o <a class='rojo' onclick='eliminar_pedido()' href='javascript:void(0)'>Cancelar el pedido</a></div>";
    }
    
    // Guardamos el pedido en la session
    $.ajax({
      "url":"/sistema/pedidos/function/guardar_session/",
      "dataType":"json",
      "type":"post",
      "data":{
        "pedido":JSON.stringify(pedido)
      },
    });    
  }
  $("#pedido_container").html(str);
}
  
function modificar_item(id,e) {
  var cantidad = $(e).val();
  for(var i=0;i<pedido.items.length;i++) {
    var p = pedido.items[i];
    if (id == p.id) {
    p.cantidad = cantidad;
    p.subtotal = p.cantidad * p.precio;
    }
  }
  render_pedido();
}
  
function eliminar_item(id) {
  // Sacamos el elemento del array
  var items = new Array();
  for(var i=0;i<pedido.items.length;i++) {
    var p = pedido.items[i];
    if (id != p.id) {
    items.push(p);
    }
  }
  pedido.items = items;
  render_pedido();
}
  
function eliminar_pedido() {
  if (confirm("Realmente desea eliminar el pedido?")) {
    pedido = nuevo_pedido();
    render_pedido();
  }
}
  
function aceptar_pedido() {
  
  // Controlamos que el pedido sea mayor al pedido minimo del comercio
  <?php if ($comercio->pedido_minimo > 0) { ?>
    if (pedido.total <= <?php echo $comercio->pedido_minimo?>) {
    alert("El pedido no supera el minimo para este comercio.");
    return;
    }
  <?php } ?>
  $("#modal_pedido").modal({
    show: true
  });
}
</script>