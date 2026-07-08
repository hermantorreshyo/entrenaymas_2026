<?php // EL TOASTER ESTA PARA EL AJAX DEL CARRITO ?>
<script type="text/javascript" src="/sistema/resources/js/jquery/jquery.toaster.js"></script>
<style type="text/css">
.toaster .title { float: none; padding: 0px; margin: 0px; color: inherit; width: auto; border: none; position: relative; font-size: inherit; } 
.toaster .title:after, .toaster .title:before { display: none }
.toaster button { background-color: transparent; color: white; float: right; font-size: 18px; }
</style>
<?php 

// Si estamos usando un template en algun lenguaje en particular
if (isset($default_language)) {
  $checkout_language = $default_language;
} else {
  // Corroboramos el lenguaje
  include_once("models/Language_Model.php");
  $lm = new Language_Model($empresa->id,$conx);
  $checkout_language = $lm->get_language();
}

$ll = array(
  "ver_carrito"=>"Ver Carrito",
  "continuar_comprando"=>"Continuar comprando",
  "producto"=>"Producto",
  "precio"=>"Precio",
  "cantidad"=>"Cantidad",
  "subtotal"=>"Subtotal",
  "titulo_item_agregado"=>"Item a&ntilde;adido al carrito!",
);
if ($checkout_language == "en") {
  $ll["ver_carrito"] = "Go to Cart";
  $ll["continuar_comprando"] = "Continue order";
  $ll["producto"] = "Product";
  $ll["precio"] = "Price";
  $ll["cantidad"] = "Quantity";
  $ll["subtotal"] = "Subtotal";
  $ll["titulo_item_agregado"] = "Item add to cart!";
}

// ================================================
// FUNCIONES PARA EL CALCULO DE ENVIO
// ================================================
?>
<script type="text/javascript">

function cambiar_provincia(id_provincia) {
  var id_localidad_seleccionada = $(".localidad_select").data("selected");
  $.ajax({
    url: '/sistema/localidades/function/get_by_provincia/'+id_provincia+"/",
    dataType: 'json',
    success: function(datos) {
      $(".localidad_select").empty();
      $(".localidad_select").append('<option value="0">Localidad</option>');
      for(var i=0;i<datos.results.length;i++) {
        var o = datos.results[i];
        $(".localidad_select").append('<option '+((o.id == id_localidad_seleccionada)?"selected":"")+' data-latitud="'+o.latitud+'" data-longitud="'+o.longitud+'" value="'+o.id+'">'+o.nombre+'</option>');
      }
    },
  });
}  
<?php if ($carrito->id_provincia != 0 && $carrito->id_localidad != 0) { ?>
  cambiar_provincia(<?php echo $carrito->id_provincia ?>);
<?php } ?>

var id_localidad = 0;
var id_provincia = 0;
var id_sucursal = 0;
var localidad = "";
var sucursal = "";
var distancia = -1;

function consultar_costo_envio_correo_argentino() {

  window.id_provincia = $(".provincia_select").val();
  if (id_provincia == 0) {
    alert("Por favor seleccione una provincia");
    $(".provincia_select").focus();
    return false;
  }
  window.id_localidad = $(".localidad_select").val();
  window.localidad = $(".localidad_select option:selected").text();
  if (window.id_localidad == 0) {
    alert("Por favor seleccione una localidad");
    $(".localidad_select").focus();
    return false;
  }
  $(".ajax-loading").show();
  var tipo_servicio = $("input[name='tipo_servicio_correo_argentino']:checked").val();
  var datos = {
    "numero":<?php echo isset($numero_carrito) ? $numero_carrito : 0 ?>,
    "id_provincia":window.id_provincia,
    "id_localidad":window.id_localidad,
    "peso":window.peso_producto,
    "tipo_servicio":tipo_servicio,
  }
  $.ajax({
    "url":'<?php echo mklink("cart/calculate_shipping_cost_correo_argentino/"); ?>',
    "dataType":"json",
    "type":"post",
    "data":datos,
    "success":function(r) {
      $(".ajax-loading").hide();
      <?php if (isset($consulta_costo_envio)) { ?>
        $('#modal_calcular_costo_envio').modal('hide');
        $(".costo_envio_res").html("$ "+Number(r.costo_envio).toFixed(2));
        $(".costo_envio_calcular").hide();
        $(".costo_envio_resultado").show();
      <?php } else { ?>
        location.reload();
      <?php } ?>
    }
  });
  return false;
}


function consultar_costo_envio_mercadoenvio() {

  window.codigo_postal = $("#costo_envio_codigo_postal").val();
  $(".ajax-loading").show();
  var datos = {
    "numero":<?php echo isset($numero_carrito) ? $numero_carrito : 0 ?>,
    "codigo_postal":window.codigo_postal,
    "peso":window.peso_producto,
    "ancho":window.ancho_producto,
    "alto":window.alto_producto,
    "profundidad":window.profundidad_producto,
    "precio":window.precio_producto,
    "coordinar_envio":((typeof window.coordinar_envio != undefined) ? window.coordinar_envio : 0),
    "redirect":"JSON",
  }
  // Datos opcionales
  if (typeof window.coordinar_envio_producto != "undefined") datos.coordinar_envio = window.coordinar_envio_producto;
  if (typeof ID_EMPRESA != "undefined") datos.id_empresa = ID_EMPRESA;
  $.ajax({
    "url":'<?php echo mklink("cart/calculate_shipping_cost_mercadoenvio/"); ?>',
    "dataType":"json",
    "type":"post",
    "data":datos,
    "success":function(r) {
      $(".ajax-loading").hide();
      <?php if (isset($consulta_costo_envio)) { ?>
        $('#modal_calcular_costo_envio').modal('hide');
        $(".costo_envio_calcular").hide();
        if (r.costo_envio == -1) {
          if ($(".costo_envio_resultado_coordinar").length > 0) {
            $(".costo_envio_resultado_coordinar").show();  
          } else {
            $(".costo_envio_resultado").html("<span>Atencion: El producto supera los limites para su envio.</span>");
            $(".costo_envio_resultado").show();  
          }
        } else if (r.costo_envio == 0) {
          $(".costo_envio_resultado_gratis").show();
        } else {
          $(".costo_envio_res").html("$ "+Number(r.costo_envio).toFixed(2));
          $(".costo_envio_resultado").show();
        }
      <?php } else { ?>
        location.reload();
      <?php } ?>
    }
  });
  return false;
}


function consultar_costo_envio_personalizado(form,callback) {

  if (typeof callback == "undefined") {
    callback = enviar_consulta_costo_envio_personalizado;
  }

  id_provincia = $(form).find(".provincia_select").val();
  if (id_provincia == 0) {
    alert("Por favor seleccione una provincia");
    $(form).find(".provincia_select").focus();
    return false;
  }
  id_localidad = $(form).find(".localidad_select").val();
  localidad = $(form).find(".localidad_select option:selected").text();
  if (id_localidad == 0) {
    alert("Por favor seleccione una localidad");
    $(form).find(".localidad_select").focus();
    return false;
  }
  if ($(form).find(".sucursal_select").length > 0) {
    if ($(form).find(".sucursal_select").is(":radio")) {
      if ($(form).find(".sucursal_select:checked").length == 0) {
        alert("Por favor seleccione una sucursal desde donde se enviara su pedido.");
        return false;
      }
      id_sucursal = $(form).find(".sucursal_select:checked").val();
      sucursal = $(form).find(".sucursal_select:checked").data("nombre");
      var lat_origen = $(form).find(".sucursal_select:checked").data("latitud");
      var lng_origen = $(form).find(".sucursal_select:checked").data("longitud");
    } else {
      id_sucursal = $(form).find(".sucursal_select").val();
      sucursal = $(form).find(".sucursal_select option:selected").text();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal desde donde se enviara su pedido.");
        return false;      
      }
      var lat_origen = $(form).find(".sucursal_select option:selected").data("latitud");
      var lng_origen = $(form).find(".sucursal_select option:selected").data("longitud");
    }
  } else {
    var lat_origen = <?php echo $empresa->latitud ?>;
    var lng_origen = <?php echo $empresa->longitud ?>;
  }

  var lat_destino = $(form).find(".localidad_select option:selected").data("latitud");
  var lng_destino = $(form).find(".localidad_select option:selected").data("longitud");
  var coor_origen = new google.maps.LatLng(lat_origen,lng_origen);
  var coor_destino = new google.maps.LatLng(lat_destino,lng_destino);
  var service = new google.maps.DistanceMatrixService();
  service.getDistanceMatrix({
      origins: [coor_origen],
      destinations: [coor_destino],
      travelMode: google.maps.TravelMode.DRIVING,
  }, callback);
  return false;
}
function enviar_consulta_costo_envio_personalizado(response) {
  if (response.rows.length == 0) return;
  var kms = response.rows[0].elements[0].distance.value;
  kms = kms / 1000;
  window.distancia = kms;
  enviar_ajax_consulta_costo_envio_personalizado()
}

function enviar_ajax_consulta_costo_envio_personalizado() {
  $(".ajax-loading").show();
  var datos = {
    "numero":<?php echo isset($numero_carrito) ? $numero_carrito : 0 ?>,
    "distancia":window.distancia,
    "id_provincia":window.id_provincia,
    "id_localidad":window.id_localidad,
    "id_sucursal":window.id_sucursal,
    "sucursal":window.sucursal,
    "localidad":window.localidad,
  }
  if (typeof window.peso_producto != undefined) {
    datos.peso = window.peso_producto;
  }
  $.ajax({
    "url":'<?php echo mklink("cart/calculate_shipping_cost/"); ?>',
    "dataType":"json",
    "type":"post",
    "data":datos,
    "success":function(r) {
      $(".ajax-loading").hide();
      $('#modal_calcular_costo_envio').modal('hide');
      $(".costo_envio_res").html("$ "+Number(r.costo_envio).toFixed(2));
      $(".costo_envio_calcular").hide();
      $(".costo_envio_resultado").show();
    }
  });
}


// FUNCIONES UTILIZADAS EN EL CARRITO, RECARGA LA PAGINA

function calcular_costo_envio_personalizado_<?php echo $carrito->numero ?>(form,callback) {

  if (typeof callback == "undefined") {
    callback = enviar_costo_envio_personalizado_<?php echo $carrito->numero ?>;
  }

  id_provincia = $(form).find(".provincia_select").val();
  if (id_provincia == 0) {
    alert("Por favor seleccione una provincia");
    $(form).find(".provincia_select").focus();
    return false;
  }
  id_localidad = $(form).find(".localidad_select").val();
  localidad = $(form).find(".localidad_select option:selected").text();
  if (id_localidad == 0) {
    alert("Por favor seleccione una localidad");
    $(form).find(".localidad_select").focus();
    return false;
  }
  if ($(form).find(".sucursal_select").length > 0) {
    if ($(form).find(".sucursal_select").is(":radio")) {
      if ($(form).find(".sucursal_select:checked").length == 0) {
        alert("Por favor seleccione una sucursal desde donde se enviara su pedido.");
        return false;
      }
      id_sucursal = $(form).find(".sucursal_select:checked").val();
      sucursal = $(form).find(".sucursal_select:checked").data("nombre");
      var lat_origen = $(form).find(".sucursal_select:checked").data("latitud");
      var lng_origen = $(form).find(".sucursal_select:checked").data("longitud");
    } else {
      id_sucursal = $(form).find(".sucursal_select").val();
      sucursal = $(form).find(".sucursal_select option:selected").text();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal desde donde se enviara su pedido.");
        return false;      
      }
      var lat_origen = $(form).find(".sucursal_select option:selected").data("latitud");
      var lng_origen = $(form).find(".sucursal_select option:selected").data("longitud");
    }
  } else {
    var lat_origen = <?php echo $empresa->latitud ?>;
    var lng_origen = <?php echo $empresa->longitud ?>;
  }

  var lat_destino = $(form).find(".localidad_select option:selected").data("latitud");
  var lng_destino = $(form).find(".localidad_select option:selected").data("longitud");
  var coor_origen = new google.maps.LatLng(lat_origen,lng_origen);
  var coor_destino = new google.maps.LatLng(lat_destino,lng_destino);
  var service = new google.maps.DistanceMatrixService();
  service.getDistanceMatrix({
    origins: [coor_origen],
    destinations: [coor_destino],
    travelMode: google.maps.TravelMode.DRIVING,
  }, callback);
  return false;
}

function enviar_costo_envio_personalizado_<?php echo $carrito->numero ?>(response) {

  if (response.rows.length == 0) return;
  var redirect = CURRENT_URL;
  var kms = response.rows[0].elements[0].distance.value;
  kms = kms / 1000;

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/calculate_shipping_cost/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo isset($numero_carrito) ? $numero_carrito : 0 ?>);
  form.appendChild(input1);

  var input2 = document.createElement('input');
  input2.setAttribute('type', 'hidden');
  input2.setAttribute('name', 'distancia');
  input2.setAttribute('value', kms);
  form.appendChild(input2);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_provincia');
  input3.setAttribute('value', id_provincia);
  form.appendChild(input3);

  var input4 = document.createElement('input');
  input4.setAttribute('type', 'hidden');
  input4.setAttribute('name', 'id_localidad');
  input4.setAttribute('value', id_localidad);
  form.appendChild(input4);

  var input5 = document.createElement('input');
  input5.setAttribute('type', 'hidden');
  input5.setAttribute('name', 'id_sucursal');
  input5.setAttribute('value', id_sucursal);
  form.appendChild(input5);

  var input6 = document.createElement('input');
  input6.setAttribute('type', 'hidden');
  input6.setAttribute('name', 'sucursal');
  input6.setAttribute('value', sucursal);
  form.appendChild(input6);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'localidad');
  input7.setAttribute('value', localidad);
  form.appendChild(input7);

  // SI ESTAMOS CALCULANDO EL COSTO DE ENVIO DE UN UNICO PRODUCTO
  // TODO: No se hace mas asi, sino con AJAX
  <?php /*if (isset($peso_producto)) { ?>
    var input9 = document.createElement('input');
    input9.setAttribute('type', 'hidden');
    input9.setAttribute('name', 'peso');
    input9.setAttribute('value', <?php echo $peso_producto ?>);
    form.appendChild(input9);
  <?php }*/ ?>

  var input8 = document.createElement('input');
  input8.setAttribute('type', 'hidden');
  input8.setAttribute('name', 'redirect');
  input8.setAttribute('value', redirect);
  form.appendChild(input8);
  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
}
</script>


<?php 
// ================================================
// FUNCIONES DEL CARRITO 
// ================================================
?>
<script type="text/javascript">

function vaciar_carrito() {
  $.ajax({
    "url":"<?php echo mklink("cart/empty/") ?>",
    "dataType":"json",
    "success":function(r) {
      if (r.error == 0) location.reload();
    }
  })
}

function eliminar_carrito() {
  $.ajax({
    "url":"<?php echo mklink("cart/delete/") ?>",
    "dataType":"json",
    "success":function(r) {
      if (r.error == 0) {
        if (ID_EMPRESA == 571) location.href = "<?php echo mklink("/") ?>";
        else location.reload();
      }
    }
  })
}

function eliminar_item(e,id) {
  var context = $(e).parents(".producto");
  $(context).find(".prod_"+id+".cantidad").val(0);
  <?php if (isset($add_to_cart)) { ?>
    window.add_to_cart = true;
  <?php } ?>
  modificar_item(id,context);
}

function cambiar_cantidad(e,modificar) {
  modificar = (typeof modificar == "undefined") ? true : false;
  var context = $(e).parents(".producto");
  var id = $(e).data("id");
  var cantidad = $(e).val();
  var precio = $(".prod_"+id+".precio").val();
  var precio_por = Number(cantidad * precio).toFixed(2);
  $(context).find(".prod_"+id+".cantidad").val(cantidad);
  $(context).find(".cantidad").text(cantidad);
  $(context).find(".precio_por").text(precio_por);
  <?php if (isset($add_to_cart)) { ?>
    window.add_to_cart = true;
  <?php } ?>
  if (modificar) {
    modificar_item(id,context);
  } else {
    // Tengo que ver si hay que consultar nuevamente el costo de envio
    <?php if (isset($producto->peso)) { ?>
      if (window.distancia != -1) {
        window.peso_producto = <?php echo $producto->peso ?> * cantidad;
        enviar_ajax_consulta_costo_envio_personalizado();
      }
    <?php } ?>
  }
}


var error_variante = "";

function controlar_variantes(id_articulo) {

  var cantidad = $(".prod_"+id_articulo+".cantidad").first().val();

  // Si el producto tiene variantes, debemos controlar que se hayan elegido todas
  if ($("#articulo_propiedades").length>0) {
    var ids_opciones = new Array(0,0,0);
    var encontro = false;
    $("#articulo_propiedades .propiedad_fila").each(function(i,fila){
      if ($(fila).find(".propiedad_opcion.active").length == 0) {
        encontro = $(fila).find(".opcion_titulo").html();
        return; // Salimos de EACH
      } else {
        ids_opciones[$(fila).data("numero")] = $(fila).find(".propiedad_opcion.active").data("id");
      }
    });
    if (encontro != false) {
      window.error_variante = "Por favor seleccione: "+encontro;
      return false;
    }

    // Controlamos si hay stock para la variante seleccionada
    for(var i=0; i<window.variantes.length;i++) {
      var v = window.variantes[i];
      // Buscamos la variante en el array
      if (v.id_opcion_1 == ids_opciones[0] &&
          v.id_opcion_2 == ids_opciones[1] &&
          v.id_opcion_3 == ids_opciones[2]) {

        // No hay stock
        if (v.stock <= 0) {
          window.error_variante = "No hay stock disponible de ese producto.";
          $(".mensaje-sin-stock").html(window.error_variante);
          $(".mensaje-sin-stock").show();
          return false;

        // TODO: La cantidad pedida supera el stock
        } else {

          if (cantidad > v.stock) {
            window.error_variante = "No hay suficiente stock. Stock actual: "+v.stock;
            $(".mensaje-sin-stock").html(window.error_variante);
            $(".mensaje-sin-stock").show();
            return false;
          }
          
          // Esta todo OK
          $(".mensaje-sin-stock").hide();
          $(".prod_"+id_articulo+".descripcion").val(v.nombre);
          $(".prod_"+id_articulo+".id_opcion_1").val(v.id_opcion_1);
          $(".prod_"+id_articulo+".id_opcion_2").val(v.id_opcion_2);
          $(".prod_"+id_articulo+".id_opcion_3").val(v.id_opcion_3);
          $(".prod_"+id_articulo+".id_variante").val(v.id);
          return true;
        }

      }
    }

  } else {
    // El producto no tiene variantes, o NO estamos en el detalle
    return true;
  }
}

// Wrapper para darle la funcionalidad de carrito al item
var add_to_cart = false;
function agregar_carrito(id) {
  window.add_to_cart = true;
  modificar_item(id);
}

// ABM de items en el carrito
function modificar_item(id,context) {

  context = (typeof context == "undefined") ? $("body") : context;

  // Si el producto tiene variantes, debemos controlarlas
  if (!controlar_variantes(id)) {
    alert(window.error_variante);
    return false;
  }

  var numero_carrito = $(context).find(".prod_"+id+".carrito").first().val(); // Identificacion del carrito (algunos productos pueden estar en otro carrito)
  var nombre = $(context).find(".prod_"+id+".nombre").first().val();
  var categoria = $(context).find(".prod_"+id+".categoria").first().val();
  var precio = $(context).find(".prod_"+id+".precio").first().val();
  var peso = $(context).find(".prod_"+id+".peso").first().val();
  var fragil = $(context).find(".prod_"+id+".fragil").first().val();
  var cantidad = $(context).find(".prod_"+id+".cantidad").first().val();
  var descripcion = ($(context).find(".prod_"+id+".descripcion").length > 0) ? $(context).find(".prod_"+id+".descripcion").first().val() : "";
  var ancho = ($(context).find(".prod_"+id+".ancho").length > 0) ? $(context).find(".prod_"+id+".ancho").first().val() : 0;
  var alto = ($(context).find(".prod_"+id+".alto").length > 0) ? $(context).find(".prod_"+id+".alto").first().val() : 0;
  var profundidad = ($(context).find(".prod_"+id+".profundidad").length > 0) ? $(context).find(".prod_"+id+".profundidad").first().val() : 0;
  var imagen = $(context).find(".prod_"+id+".imagen").first().val();
  var porc_iva = ($(context).find(".prod_"+id+".porc_iva").length > 0) ? $(context).find(".prod_"+id+".porc_iva").first().val() : 21;
  var id_opcion_1 = ($(context).find(".prod_"+id+".id_opcion_1").length > 0) ? $(context).find(".prod_"+id+".id_opcion_1").first().val() : 0;
  var id_opcion_2 = ($(context).find(".prod_"+id+".id_opcion_2").length > 0) ? $(context).find(".prod_"+id+".id_opcion_2").first().val() : 0;
  var id_opcion_3 = ($(context).find(".prod_"+id+".id_opcion_3").length > 0) ? $(context).find(".prod_"+id+".id_opcion_3").first().val() : 0;
  var id_variante = ($(context).find(".prod_"+id+".id_variante").length > 0) ? $(context).find(".prod_"+id+".id_variante").first().val() : 0;
  var id_usuario = ($(context).find(".prod_"+id+".id_usuario").length > 0) ? $(context).find(".prod_"+id+".id_usuario").first().val() : 0;
  var envio_gratis = ($(context).find(".prod_"+id+".envio_gratis").length > 0) ? $(context).find(".prod_"+id+".envio_gratis").first().val() : 0;
  var coordinar_envio = ($(context).find(".prod_"+id+".coordinar_envio").length > 0) ? $(context).find(".prod_"+id+".coordinar_envio").first().val() : 0;
  var redirect = (window.add_to_cart) ? CURRENT_URL : "<?php echo (isset($boton_whatsapp) && $boton_whatsapp == 1) ? mklink("carrito/") : mklink("checkout/") ?>";

  // Si estamos eliminando
  if (cantidad == 0) {
    if (!confirm("Realmente desea eliminar el producto de su pedido?")) return;
  }

  if (typeof fbq != "undefined") {
    fbq('track', 'AddToCart');
  }
  if (typeof gtag != "undefined") {
    if (cantidad > 0) {
      gtag('event', 'add_to_cart', {
        "items": [
          {
            "id": id,
            "name": nombre,
            "category": categoria,
            "quantity": cantidad,
            "price": precio
          }
        ]
      });
    }
  }

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/item/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', numero_carrito);
  form.appendChild(input1);

  var input2 = document.createElement('input');
  input2.setAttribute('type', 'hidden');
  input2.setAttribute('name', 'id_articulo');
  input2.setAttribute('value', id);
  form.appendChild(input2);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'cantidad');
  input3.setAttribute('value', cantidad);
  form.appendChild(input3);

  var input4 = document.createElement('input');
  input4.setAttribute('type', 'hidden');
  input4.setAttribute('name', 'nombre');
  input4.setAttribute('value', nombre);
  form.appendChild(input4);

  var input5 = document.createElement('input');
  input5.setAttribute('type', 'hidden');
  input5.setAttribute('name', 'categoria');
  input5.setAttribute('value', categoria);
  form.appendChild(input5);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'precio');
  input7.setAttribute('value', precio);
  form.appendChild(input7);

  var input8 = document.createElement('input');
  input8.setAttribute('type', 'hidden');
  input8.setAttribute('name', 'redirect');
  input8.setAttribute('value', redirect);
  form.appendChild(input8);

  var input9 = document.createElement('input');
  input9.setAttribute('type', 'hidden');
  input9.setAttribute('name', 'peso');
  input9.setAttribute('value', peso);
  form.appendChild(input9);

  // Enlazamos con el ID del usuario
  var input10 = document.createElement('input');
  input10.setAttribute('type', 'hidden');
  input10.setAttribute('name', 'id_usuario');
  input10.setAttribute('value', id_usuario);
  form.appendChild(input10);

  var input11 = document.createElement('input');
  input11.setAttribute('type', 'hidden');
  input11.setAttribute('name', 'porc_iva');
  input11.setAttribute('value', porc_iva);
  form.appendChild(input11);

  <?php if (isset($_SESSION["cliente_descuento"]) && $_SESSION["cliente_descuento"] != 0) { ?>
    var input12 = document.createElement('input');
    input12.setAttribute('type', 'hidden');
    input12.setAttribute('name', 'porc_descuento');
    input12.setAttribute('value', '<?php echo $_SESSION["cliente_descuento"] ?>');
    form.appendChild(input12);
  <?php } ?>

  var input13 = document.createElement('input');
  input13.setAttribute('type', 'hidden');
  input13.setAttribute('name', 'descripcion');
  input13.setAttribute('value', descripcion);
  form.appendChild(input13);

  var input14 = document.createElement('input');
  input14.setAttribute('type', 'hidden');
  input14.setAttribute('name', 'id_opcion_1');
  input14.setAttribute('value', id_opcion_1);
  form.appendChild(input14);

  var input15 = document.createElement('input');
  input15.setAttribute('type', 'hidden');
  input15.setAttribute('name', 'id_opcion_2');
  input15.setAttribute('value', id_opcion_2);
  form.appendChild(input15);

  var input16 = document.createElement('input');
  input16.setAttribute('type', 'hidden');
  input16.setAttribute('name', 'id_opcion_3');
  input16.setAttribute('value', id_opcion_3);
  form.appendChild(input16);

  var input162 = document.createElement('input');
  input162.setAttribute('type', 'hidden');
  input162.setAttribute('name', 'id_variante');
  input162.setAttribute('value', id_variante);
  form.appendChild(input162);

  var input17 = document.createElement('input');
  input17.setAttribute('type', 'hidden');
  input17.setAttribute('name', 'envio_gratis');
  input17.setAttribute('value', envio_gratis);
  form.appendChild(input17);

  var input18 = document.createElement('input');
  input18.setAttribute('type', 'hidden');
  input18.setAttribute('name', 'ancho');
  input18.setAttribute('value', ancho);
  form.appendChild(input18);

  var input19 = document.createElement('input');
  input19.setAttribute('type', 'hidden');
  input19.setAttribute('name', 'alto');
  input19.setAttribute('value', alto);
  form.appendChild(input19);

  var input20 = document.createElement('input');
  input20.setAttribute('type', 'hidden');
  input20.setAttribute('name', 'profundidad');
  input20.setAttribute('value', profundidad);
  form.appendChild(input20);

  var input22 = document.createElement('input');
  input22.setAttribute('type', 'hidden');
  input22.setAttribute('name', 'coordinar_envio');
  input22.setAttribute('value', coordinar_envio);
  form.appendChild(input22);

  if (window.add_to_cart) {
    var input21 = document.createElement('input');
    input21.setAttribute('type', 'hidden');
    input21.setAttribute('name', 'add_params');
    input21.setAttribute('value', 1);
    form.appendChild(input21);    
  }

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
}


function agregar_carrito_ajax(id,callback,context) {

  context = (typeof context == "undefined") ? $("body") : context;

  // Si el producto tiene variantes, debemos controlarlas
  if (!controlar_variantes(id)) {
    alert(window.error_variante);
    return false;
  }

  var numero_carrito = $(context).find(".prod_"+id+".carrito").first().val(); // Identificacion del carrito (algunos productos pueden estar en otro carrito)
  var nombre = $(context).find(".prod_"+id+".nombre").first().val();
  var categoria = $(context).find(".prod_"+id+".categoria").first().val();
  var precio = $(context).find(".prod_"+id+".precio").first().val();
  var peso = $(context).find(".prod_"+id+".peso").first().val();
  var fragil = $(context).find(".prod_"+id+".fragil").first().val();
  var cantidad = $(context).find(".prod_"+id+".cantidad").first().val();
  var descripcion = ($(context).find(".prod_"+id+".descripcion").length > 0) ? $(context).find(".prod_"+id+".descripcion").first().val() : "";
  var ancho = ($(context).find(".prod_"+id+".ancho").length > 0) ? $(context).find(".prod_"+id+".ancho").first().val() : 0;
  var alto = ($(context).find(".prod_"+id+".alto").length > 0) ? $(context).find(".prod_"+id+".alto").first().val() : 0;
  var profundidad = ($(context).find(".prod_"+id+".profundidad").length > 0) ? $(context).find(".prod_"+id+".profundidad").first().val() : 0;
  var imagen = $(context).find(".prod_"+id+".imagen").first().val();
  var porc_iva = ($(context).find(".prod_"+id+".porc_iva").length > 0) ? $(context).find(".prod_"+id+".porc_iva").first().val() : 21;
  var id_opcion_1 = ($(context).find(".prod_"+id+".id_opcion_1").length > 0) ? $(context).find(".prod_"+id+".id_opcion_1").first().val() : 0;
  var id_opcion_2 = ($(context).find(".prod_"+id+".id_opcion_2").length > 0) ? $(context).find(".prod_"+id+".id_opcion_2").first().val() : 0;
  var id_opcion_3 = ($(context).find(".prod_"+id+".id_opcion_3").length > 0) ? $(context).find(".prod_"+id+".id_opcion_3").first().val() : 0;
  var id_variante = ($(context).find(".prod_"+id+".id_variante").length > 0) ? $(context).find(".prod_"+id+".id_variante").first().val() : 0;
  var id_usuario = ($(context).find(".prod_"+id+".id_usuario").length > 0) ? $(context).find(".prod_"+id+".id_usuario").first().val() : 0;
  var envio_gratis = ($(context).find(".prod_"+id+".envio_gratis").length > 0) ? $(context).find(".prod_"+id+".envio_gratis").first().val() : 0;
  var coordinar_envio = ($(context).find(".prod_"+id+".coordinar_envio").length > 0) ? $(context).find(".prod_"+id+".coordinar_envio").first().val() : 0;
  var redirect = (window.add_to_cart) ? CURRENT_URL : "<?php echo mklink("checkout/").((!isset($carrito)) ? "0" : $carrito->numero)."/" ?>";

  // Si estamos eliminando
  if (cantidad == 0 && ID_EMPRESA != 1284) {
    if (!confirm("Realmente desea eliminar el producto de su pedido?")) return;
  }
  var data = {
    "numero":numero_carrito,
    "id_articulo":id,
    "cantidad":cantidad,
    "nombre":nombre,
    "categoria":categoria,
    "precio":precio,
    "redirect":"json", // Esto es muy importante
    "peso":peso,
    "id_usuario":id_usuario,
    "porc_iva":porc_iva,
    <?php if (isset($_SESSION["cliente_descuento"]) && $_SESSION["cliente_descuento"] != 0) { ?>
      'porc_descuento':'<?php echo $_SESSION["cliente_descuento"] ?>',
    <?php } ?>
    "descripcion":descripcion,
    "id_opcion_1":id_opcion_1,
    "id_opcion_2":id_opcion_2,
    "id_opcion_3":id_opcion_3,
    "id_opcion_3":id_opcion_3,
    "id_variante":id_variante,
    "envio_gratis":envio_gratis,
    "ancho":ancho,
    "alto":alto,
    "profundidad":profundidad,
    "coordinar_envio":coordinar_envio,
  };
  enviar_carrito_ajax(data,callback);
}

function enviar_carrito_ajax(data,callback) {
  $.ajax({
    "url":"<?php echo mklink("cart/item/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":data,
    "success":function(r) {

      // En caso de error mostramos el alerta
      if (typeof r.error != "undefined" && r.error == 1) alert(r.mensaje);
      
      // Actualizamos la vista del carrito
      $(".carrito_cantidad").html(r.cantidad);
      $(".carrito_total").html("$ "+Number(r.total).toFixed(2));

      // Mostramos el toaster en caso de que estemos agregando
      if (data.cantidad != 0) $.toaster({ message : data.nombre+" ha sido agregado al carrito.", title: "", priority: "info" });

      // Si tiene definido un callback lo ejecutamos
      if (typeof window[callback] != "undefined") window[callback](r);
    },
  })  
}


function actualizar_carrito(numero_carrito,numero_paso) {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/save/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', numero_carrito);
  form.appendChild(input1);

  if (typeof numero_paso != undefined) {
    var input2 = document.createElement('input');
    input2.setAttribute('type', 'hidden');
    input2.setAttribute('name', 'numero_paso');
    input2.setAttribute('value', numero_paso);
    form.appendChild(input2);
  }

  var input8 = document.createElement('input');
  input8.setAttribute('type', 'hidden');
  input8.setAttribute('name', 'redirect');
  input8.setAttribute('value', CURRENT_URL);
  form.appendChild(input8);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
}
</script>



<?php 
// ================================================
// FUNCIONES DE CHECKOUT V.2
// ================================================
?>
<script type="text/javascript">
// Si intentan modificar el carrito, lo mandamos al principio
function varcreative_ir_modificar_carrito() {
  $.ajax({
    "url":"<?php echo mklink("cart/go_cart/"); ?>",
    "dataType":"json",
    "success":function() {
      location.href = "<?php echo mklink("carrito/") ?>";
    }
  });
}

function varcreative_enviar_registro() {

  var nombre = "";
  var telefono = "";
  var email = $("#registro_email").val().trim();
  var ps = (($("#registro_ps").length > 0) ? $("#registro_ps").val() : "1").trim();
  var cuit = (($("#registro_cuit").length > 0) ? $("#registro_cuit").val() : "").trim();
  var direccion = (($("#registro_direccion").length > 0) ? $("#registro_direccion").val() : "").trim();
  var codigo_postal = (($("#registro_codigo_postal").length > 0) ? $("#registro_codigo_postal").val() : "").trim();

  // Si existe el nombre, controlamos que lo haya cargado
  if ($("#registro_nombre").length > 0) {
    nombre = $("#registro_nombre").val().trim();
    if (isEmpty(nombre)) {
      alert("Por favor ingrese un nombre de contacto");
      $("#registro_nombre").focus();
      return false;
    }
  }

  // Si existe el telefono, controlamos que lo haya cargado
  if ($("#registro_telefono").length > 0) {
    telefono = $("#registro_telefono").val();
    telefono = telefono.trim();
    telefono = telefono.replace(/\D/g,'');
    if (isEmpty(telefono)) {
      alert("Por favor ingrese un telefono de contacto");
      $("#registro_telefono").focus();
      return false;
    }
  }

  if (!validateEmail(email)) {
    alert("Por favor ingrese su email");
    $("#registro_email").prev().addClass("warning");
    $("#registro_email").addClass("warning");
    $("#registro_email").focus();
    return false;
  }

  if ($("#registro_direccion").length > 0) {
    // Si esta habilitada la direccion
    if (isEmpty(direccion)) {
      alert("Por favor ingrese su direccion para facturacion");
      $("#registro_direccion").focus();
      return false;
    }
  }
  if (isEmpty(ps)) {
    alert("Por favor ingrese su clave");
    $("#registro_ps").addClass("warning");
    $("#registro_ps").focus();
    return false;
  }
  ps = hex_md5(ps);

  $("#registro_submit").attr("disabled","disabled");
  $("#registro_submit").val("Enviando datos...");
  $.ajax({
    "url":"/sistema/clientes/function/registrar/",
    "dataType":"json",
    "data": {
      "email":email,
      "ps":ps,
      "nombre":nombre,
      "telefono":telefono,
      "codigo_postal":codigo_postal,
      "cuit":cuit,
      "direccion":direccion,
      "id_empresa":<?php echo $empresa->id ?>,
    },
    "type":"post",
    "success":function(r) {
      if (r.error == 0) {
        // Si nos registramos correctamente, nos logueamos directo
        window.login(email,ps,{
          "cliente":nombre,
          "direccion":direccion,
          "telefono":telefono,
          "codigo_postal":codigo_postal,
          "email":email,
        });
      } else {
        alert(r.mensaje);
        $("#registro_submit").val("Enviar");
        $("#registro_submit").attr("disabled","");
      }
    },
    "error":function() {
      $("#registro_submit").attr("disabled","");
    },
  });
  return false;
}

function varcreative_volver_atras(numero_paso) {
  $.ajax({
    "url":"<?php echo mklink("cart/save/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":{
      "ajax":1,
      "numero_paso":numero_paso,
      "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
    },
    "success":function() {
      location.reload();
    }
  });
}

function varcreative_actualizar(data) {
  data.ajax = 1;
  data.numero = 0;
  $.ajax({
    "url":"<?php echo mklink("cart/save/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":data,
    "success":function() {
      location.reload();
    }
  });
}

function varcreative_cupon_descuento(e) {
  var codigo_cupon = $(e).val();
  varcreative_cupon_descuento_enviar(codigo_cupon);
}

function varcreative_cupon_descuento_click() {
  var codigo_cupon = $("#checkout_cupon_descuento").val();
  varcreative_cupon_descuento_enviar(codigo_cupon);
}

function varcreative_cupon_descuento_enviar(codigo_cupon) {
  $.ajax({
    "url":"<?php echo mklink("cart/set_promo_code/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":{
      "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
      "codigo_cupon":codigo_cupon,
      "valor_envio_toque":((typeof VALOR_ENVIO != "undefined")?VALOR_ENVIO:""),
    },
    "success":function(r) {
      location.reload();
    }
  });  
}

function varcreative_forma_envio(forma_envio) {
  // Si existen diferentes sucursales que podemos elegir
  if (forma_envio == "retiro_sucursal" && $("#varcreative-option-retiro-sucursal-sucursales").length > 0) {
    $(".varcreative-option-retiro-sucursal").addClass("varcreative-option-btn-subpanel");
    $("#varcreative-option-retiro-sucursal-sucursales").slideDown();
  } else {
    $.ajax({
      "url":"<?php echo mklink("cart/set_shipping_method/"); ?>",
      "dataType":"json",
      "type":"post",
      "data":{
        "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
        "forma_envio":forma_envio,
      },
      "success":function() {
        location.reload();
      }
    });
  }
}

function varcreative_forma_envio_siguiente(forma_envio) {
  if ($("input[name='varcreative-sucursal']:checked").length == 0) {
    alert("Por favor seleccione una sucursal");
    return;
  }
  var sucursal = $("input[name='varcreative-sucursal']:checked").val();
  $.ajax({
    "url":"<?php echo mklink("cart/set_shipping_method/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":{
      "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
      "forma_envio":forma_envio,
      "sucursal":sucursal,
    },
    "success":function() {
      location.reload();
    }
  });
}

function varcreative_forma_pago(forma_pago) {
  $.ajax({
    "url":"<?php echo mklink("cart/set_payment/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":{
      "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
      "forma_pago":forma_pago,
    },
    "success":function() {
      location.reload();
    }
  });
}

function varcreative_tipo_servicio_envio(tipo_servicio) {
  $.ajax({
    "url":"<?php echo mklink("cart/set_shipping/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":{
      "numero":"<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>",
      "empresa_envio":"CORREO_ARGENTINO",
      "tipo_servicio":tipo_servicio,
      "ajax":1,
    },
    "success":function() {
      location.reload();
    }
  });  
}

function varcreative_enviar_direccion() {

  var datos = {};

  // Si existe el codigo postal, controlamos que lo haya cargado
  if ($("#registro_codigo_postal").length > 0) {
    datos.codigo_postal = $("#registro_codigo_postal").val();
    if (isEmpty(datos.codigo_postal)) {
      alert("Por favor ingrese su código postal");
      $("#registro_codigo_postal").focus();
      return false;
    }
  }

  // Si existe el domicilio, controlamos que lo haya cargado
  if ($("#registro_direccion").length > 0) {
    datos.direccion = $("#registro_direccion").val();
    if (isEmpty(datos.direccion)) {
      alert("Por favor ingrese su direccion de envio");
      $("#registro_direccion").focus();
      return false;
    }
  }

  if ($("#registro_localidad_texto").length > 0) {
    datos.localidad = $("#registro_localidad_texto").val();
  }

  // Si existe el localidad, controlamos que lo haya cargado
  if ($("#registro_localidad").length > 0) {
    datos.id_provincia = $("#registro_provincia").val();
    datos.id_localidad = $("#registro_localidad").val();
    datos.localidad = $("#registro_localidad option:selected").text();
    if (datos.id_localidad==0) {
      alert("Por favor ingrese su localidad");
      $("#registro_localidad").focus();
      return false;
    }
  }

  if ($("#registro_empresa_envio").length > 0) {
    datos.empresa_envio = $("#registro_empresa_envio").val();
  }
  datos.numero = "<?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>";
  datos.ajax = 1;

  $.ajax({
    "url":"<?php echo mklink("cart/set_shipping/"); ?>",
    "dataType":"json",
    "type":"post",
    "data":datos,
    "success":function(r) {
      location.reload();
    }
  });
  return false;
}
</script>








<?php 
// ================================================
// FUNCIONES DE CHECKOUT
// ================================================
?>
<script type="text/javascript">

function enviar_forma_pago() {
  $("#finalizar_pago_container").find(".finalizar:visible").trigger("click");
}

function finalizar_pago_sucursal() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/complete/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_tipo_estado');
  input3.setAttribute('value', 8); // 8 = PAGO EN SUCURSAL
  form.appendChild(input3);

  var redirect = "<?php echo mklink("compra-ok/")."?payment_type=Sucursal&collection_status=approved&external_reference=".$carrito->id; ?>";
  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;

}

function finalizar_pago_a_convenir() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/complete/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_tipo_estado');
  input3.setAttribute('value', 9); // 9 = PAGO A CONVENIR
  form.appendChild(input3);

  var redirect = "<?php echo mklink("compra-pending/")."?payment_type=Sucursal&collection_status=pending&external_reference=".$carrito->id; ?>";
  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}

function finalizar_pago_contrarrembolso() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/complete/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_tipo_estado');
  input3.setAttribute('value', 10); // 10 = PAGO CONTRARREMBOLSO
  form.appendChild(input3);

  var redirect = "<?php echo mklink("compra-pending/")."?payment_type=Contrarrembolso&collection_status=pending&external_reference=".$carrito->id; ?>";
  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}

function finalizar_pago_transferencia() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/complete/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_tipo_estado');
  input3.setAttribute('value', 3); // 3 = PENDIENTE DE PAGO
  form.appendChild(input3);

  var redirect = "<?php echo mklink("compra-pending/")."?payment_type=transfer&collection_status=pending&external_reference=".$carrito->id; ?>";
  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}

function finalizar_pago_stripe() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/complete/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'id_tipo_estado');
  input3.setAttribute('value', 6); // 6 = COMPRA FINALIZADA
  form.appendChild(input3);

  var redirect = "<?php echo mklink("compra-ok/")."?payment_type=stripe&external_reference=".$carrito->id; ?>";
  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}

function do_enviar_correo_argentino() {

  var direccion = $("#registro_direccion").val();
  var id_localidad = $("#registro_localidad").val();
  var localidad = $("#registro_localidad option:selected").text();
  var id_provincia = $("#registro_provincia").val();
  var tipo_servicio = $("input[name='tipo_servicio_correo_argentino']:checked").val();
  var redirect = CURRENT_URL;

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/set_shipping/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo $carrito->numero ?>);
  form.appendChild(input1);

  var input2 = document.createElement('input');
  input2.setAttribute('type', 'hidden');
  input2.setAttribute('name', 'tipo_servicio');
  input2.setAttribute('value', tipo_servicio);
  form.appendChild(input2);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'retirar_envio');
  input3.setAttribute('value', 0);
  form.appendChild(input3);

  var input4 = document.createElement('input');
  input4.setAttribute('type', 'hidden');
  input4.setAttribute('name', 'id_localidad');
  input4.setAttribute('value', id_localidad);
  form.appendChild(input4);

  var input5 = document.createElement('input');
  input5.setAttribute('type', 'hidden');
  input5.setAttribute('name', 'localidad');
  input5.setAttribute('value', localidad);
  form.appendChild(input5);

  var input6 = document.createElement('input');
  input6.setAttribute('type', 'hidden');
  input6.setAttribute('name', 'id_provincia');
  input6.setAttribute('value', id_provincia);
  form.appendChild(input6);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  var input8 = document.createElement('input');
  input8.setAttribute('type', 'hidden');
  input8.setAttribute('name', 'direccion');
  input8.setAttribute('value', direccion);
  form.appendChild(input8);

  if ($("#registro_zona_envio").length > 0) {
    var zona_envio = $("#registro_zona_envio option:selected").text();
    var input20 = document.createElement('input');
    input20.setAttribute('type', 'hidden');
    input20.setAttribute('name', 'zona_envio');
    input20.setAttribute('value', zona_envio);
    form.appendChild(input20);
  }

  $(form).css("display","none");
  document.body.appendChild(form);

  $(form).submit();
  return false;
}

function do_enviar_domicilio(response) {

  if (typeof response.rows == "undefined") return;
  if (response.rows.length == 0) return;
  var redirect = CURRENT_URL;
  var kms = response.rows[0].elements[0].distance.value;
  kms = kms / 1000;

  var direccion = $("#registro_direccion").val();
  var id_localidad = $("#registro_localidad").val();
  var localidad = $("#registro_localidad option:selected").text();
  var id_provincia = $("#registro_provincia").val();
  var id_sucursal = $("#registro_sucursal").val();
  var sucursal = $("#registro_sucursal option:selected").text();

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/set_shipping/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input2 = document.createElement('input');
  input2.setAttribute('type', 'hidden');
  input2.setAttribute('name', 'distancia');
  input2.setAttribute('value', kms);
  form.appendChild(input2);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'retirar_envio');
  input3.setAttribute('value', 0);
  form.appendChild(input3);

  var input4 = document.createElement('input');
  input4.setAttribute('type', 'hidden');
  input4.setAttribute('name', 'id_localidad');
  input4.setAttribute('value', id_localidad);
  form.appendChild(input4);

  var input5 = document.createElement('input');
  input5.setAttribute('type', 'hidden');
  input5.setAttribute('name', 'localidad');
  input5.setAttribute('value', localidad);
  form.appendChild(input5);

  var input6 = document.createElement('input');
  input6.setAttribute('type', 'hidden');
  input6.setAttribute('name', 'id_provincia');
  input6.setAttribute('value', id_provincia);
  form.appendChild(input6);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', redirect);
  form.appendChild(input7);

  var input8 = document.createElement('input');
  input8.setAttribute('type', 'hidden');
  input8.setAttribute('name', 'direccion');
  input8.setAttribute('value', direccion);
  form.appendChild(input8);

  var input9 = document.createElement('input');
  input9.setAttribute('type', 'hidden');
  input9.setAttribute('name', 'id_sucursal');
  input9.setAttribute('value', id_sucursal);
  form.appendChild(input9);

  var input10 = document.createElement('input');
  input10.setAttribute('type', 'hidden');
  input10.setAttribute('name', 'sucursal');
  input10.setAttribute('value', sucursal);
  form.appendChild(input10);

  if ($("#registro_zona_envio").length > 0) {
    var zona_envio = $("#registro_zona_envio option:selected").text();
    var input20 = document.createElement('input');
    input20.setAttribute('type', 'hidden');
    input20.setAttribute('name', 'zona_envio');
    input20.setAttribute('value', zona_envio);
    form.appendChild(input20);
  }

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}


function do_retiro_sucursal() {

  var id_sucursal = $("#retiro_sucursal_sucursales").val();
  var sucursal = $("#retiro_sucursal_sucursales option:selected").text();

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/set_shipping/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'retirar_envio');
  input3.setAttribute('value', 1);
  form.appendChild(input3);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', CURRENT_URL);
  form.appendChild(input7);

  var input9 = document.createElement('input');
  input9.setAttribute('type', 'hidden');
  input9.setAttribute('name', 'id_sucursal');
  input9.setAttribute('value', id_sucursal);
  form.appendChild(input9);

  var input10 = document.createElement('input');
  input10.setAttribute('type', 'hidden');
  input10.setAttribute('name', 'sucursal');
  input10.setAttribute('value', sucursal);
  form.appendChild(input10);

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}


function do_convenir_envio() {

  // Enviamos los datos al controlador
  var form = document.createElement('form');
  form.setAttribute('action', '<?php echo mklink("cart/set_shipping/"); ?>');
  form.setAttribute('method', 'POST');

  var input1 = document.createElement('input');
  input1.setAttribute('type', 'hidden');
  input1.setAttribute('name', 'numero');
  input1.setAttribute('value', <?php echo (!isset($carrito)) ? "0" : $carrito->numero ?>);
  form.appendChild(input1);

  var input3 = document.createElement('input');
  input3.setAttribute('type', 'hidden');
  input3.setAttribute('name', 'retirar_envio');
  input3.setAttribute('value', -1);
  form.appendChild(input3);

  var input4 = document.createElement('input');
  input4.setAttribute('type', 'hidden');
  input4.setAttribute('name', 'numero_paso');
  input4.setAttribute('value', 4);
  form.appendChild(input4);

  var input7 = document.createElement('input');
  input7.setAttribute('type', 'hidden');
  input7.setAttribute('name', 'redirect');
  input7.setAttribute('value', CURRENT_URL);
  form.appendChild(input7);

  if ($("#registro_zona_envio").length > 0) {
    var zona_envio = $("#registro_zona_envio option:selected").text();
    var input20 = document.createElement('input');
    input20.setAttribute('type', 'hidden');
    input20.setAttribute('name', 'zona_envio');
    input20.setAttribute('value', zona_envio);
    form.appendChild(input20);
  }

  $(form).css("display","none");
  document.body.appendChild(form);
  $(form).submit();
  return false;
}

function enviar_login() {
  var self = this;
  var email = validate_input("login_email",IS_EMAIL,"Por favor ingrese un email correcto");
  var ps = validate_input("login_ps",IS_EMPTY,"Por favor ingrese su clave de acceso");
  ps = hex_md5(ps);
  return login(email,ps);
}

function ir_consultar() {
  $("#contacto_nombre").focus();
}

function reset_ps() {
  var email = validate_input("login_email",IS_EMAIL,"Por favor ingrese su email. Le enviaremos una nueva clave a su casilla de correo.");
  $.ajax({
    url: '/sistema/clientes/function/reset_ps/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'id_empresa': ID_EMPRESA,
    },
    success: function(data) {
      alert(data.mensaje);
    },
  });
  return false;
}

function login(email,ps,data) {
  $.ajax({
    url: '/sistema/login/check_cliente/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'ps': ps,
      'id_empresa': ID_EMPRESA,
    },
    success: function(data, textStatus, xhr) {
      if (data.error == false && data.id_empresa == ID_EMPRESA) {
        <?php if (isset($pagina) && $pagina == "login") { ?>
          location.href = "<?php echo mklink("/"); ?>";
        <?php } else { ?>
          // Si se logueo correctamente, vamos al siguiente paso
          data.numero_paso = 1;
          varcreative_actualizar(data);
        <?php } ?>        
      } else {
        if (data.mensaje !== undefined) {
          alert(data.mensaje);
        } else {
          alert("Nombre de usuario y/o ps incorrectos.");
        }
        $("#login_email").focus();                
      }
    },
  });
  return false;
}


function add_placeholder(e) {
  $(e).attr("placeholder",$(e).data("placeholder"));
}
function remove_placeholder(e) {
  $(e).removeAttr("placeholder");
}

</script>

<?php 
// FUNCIONALIDAD DE CLIENAPP
// ============================================

if (isset($boton_whatsapp) && $boton_whatsapp == 1) { ?>
<script type="text/javascript">

window.id_articulo_agregado = null;
function pedir_whatsapp(id) {
  window.id_articulo_agregado = id;
  agregar_carrito_ajax(id,"abrir_modal_clienapp");
}

// Vamos a consultar el carrito para despues abrir el modal con la info cargada
function abrir_modal_clienapp() {
  $.ajax({
    "url":"/cart/show/",
    "dataType":"json",
    "type":"post",
    "data":{
      "json":"1",
      "id_empresa":ID_EMPRESA,
      "buscar_usuario":1,
    },
    "success":function(r){
      window.render_modal_clienapp(r);
    }
  });
}

window.pedido_clienapp = null;
window.recargar_despues = null;

function render_modal_clienapp(r) {

  window.pedido_clienapp = r;
  $(".clienapp_paso_2").hide();
  $(".clienapp_paso_1").show();
  window.recargar_despues = null;

  $(".clienapp_checkout_titulo").html(r.usuario);
  if (!isEmpty(r.usuario_path)) $("#clienapp_checkout_logo").html("<img src='"+r.usuario_path+"'/>");

  // Precargamos los valores de la cookie asi no los vuelve a escribir
  var cliente = ($.cookie("nombre") != undefined ? $.cookie("nombre") : "");
  $("#clienapp_checkout_nombre").val(cliente);
  var direccion = ($.cookie("direccion") != undefined ? $.cookie("direccion") : "");
  $("#clienapp_checkout_direccion").val(direccion);
  var email = ($.cookie("email") != undefined ? $.cookie("email") : "");
  $("#clienapp_checkout_email").val(email);
  var telefono = ($.cookie("telefono") != undefined ? $.cookie("telefono") : "");
  $("#clienapp_checkout_telefono").val(telefono);

  $("#clienapp_checkout_table tbody").empty();
  for(var i=0; i<r.items.length;i++) {
    var item = r.items[i];
    var s = "<tr>";
    s+="<td class='clienapp_primero'>"+item.nombre+"</td>";
    s+="<td>"+item.cantidad+"</td>";
    s+="<td>$ "+Number(item.precio).toFixed(0)+"</td>";
    s+="<td class='clienapp_ultimo'>$ "+Number(item.total).toFixed(0)+"</td>";
    s+="</tr>";
    $("#clienapp_checkout_table tbody").append(s);
  }
  $("#clienapp_checkout_total").html("$ "+Number(r.total).toFixed(0));
  $("#modal_clienapp").modal('show');
  $('#modal_clienapp').one('hidden.bs.modal', function (e) {
    if (window.recargar_despues != null) {
      if (ID_EMPRESA == 1284) {
        // ESTEBAN ECHEVERRIA QUIERE QUE VUELVA AL INICIO
        location.href = "<?php echo mklink("/") ?>";
      } else {
        location.reload();
      }
    } else if (window.id_articulo_agregado != null) {
      // Si se agrega con cantidad cero, se elimina
      $(".prod_"+window.id_articulo_agregado+".cantidad").val(0);
      agregar_carrito_ajax(window.id_articulo_agregado,"callback_cerrar_clienapp");
    }
  })
}

function callback_cerrar_clienapp(){
  $(".prod_"+window.id_articulo_agregado+".cantidad").val(1);
  window.id_articulo_agregado = null;
}

function do_clienapp_cerrar() {
  $('#modal_clienapp').modal('hide');
}

// CUANDO SE SUBMITEA EL BOTON DE WHATSAPP, LO PRIMERO QUE HACEMOS ES EMPEZAR A REGISTRAR EL USUARIO
function varcreative_registro_clienapp() {
  var nombre = $("#clienapp_checkout_nombre").val();
  nombre = nombre.trim();
  if (isEmpty(nombre)) {
    alert("Por favor ingrese su nombre");
    $("#clienapp_checkout_nombre").focus();
    return false;
  }
  var telefono = $("#clienapp_checkout_telefono").val();
  telefono = telefono.trim();
  telefono = telefono.replace(/\D/g,'');
  if (isEmpty(telefono)) {
    alert("Por favor ingrese su telefono");
    $("#clienapp_checkout_telefono").focus();
    return false;
  }
  var email = $("#clienapp_checkout_email").val();
  if (!validateEmail(email)) {
    alert("Por favor ingrese su email");
    $("#clienapp_checkout_email").focus();
    return false;
  }

  var ps = hex_md5("1");
  clienapp_show_loading();
  $.ajax({
    "url":"/sistema/clientes/function/registrar/",
    "dataType":"json",
    "data": {
      "email":email,
      "ps":ps,
      "nombre":nombre,
      "telefono":telefono,
      "id_empresa":<?php echo $empresa->id ?>,
    },
    "type":"post",
    "success":function(r) {
      if (r.error == 0) {
        // Si nos registramos correctamente, nos logueamos asi quedan las cookies
        varcreative_login_clienapp(email,ps);
      } else {
        alert(r.mensaje);
        clienapp_hide_loading();
      }
    },
    "error":function() {
      clienapp_hide_loading();
    },
  });
  return false;  
}

function clienapp_show_loading() {
  $("#clienapp_submit").attr("disabled","disabled");
  $("#clienapp_loading").show();
}

function clienapp_hide_loading() {
  $("#clienapp_submit").attr("disabled","");
  $("#clienapp_loading").hide();
}

function varcreative_login_clienapp(email,ps) {
  $.ajax({
    url: '/sistema/login/check_cliente/',
    type: 'POST',
    dataType: 'json',
    data: {
      'email': email, 
      'ps': ps,
      'id_empresa': ID_EMPRESA,
    },
    success: function(data, textStatus, xhr) {
      if (data.error == false && data.id_empresa == ID_EMPRESA) {
        varcreative_validar_clienapp(data.id);
      }
    },
    "error":function(){
      clienapp_hide_loading();
    }
  });
  return false;
}

function varcreative_validar_clienapp(id_cliente) {
  var nombre = $("#clienapp_checkout_nombre").val();
  var telefono = $("#clienapp_checkout_telefono").val();
  var email = $("#clienapp_checkout_email").val();  
  var direccion = $("#clienapp_checkout_direccion").val();
  /*
  var forma_envio = $("input[name='clienapp_forma_envio']:checked").val();
  var forma_pago = $("input[name='clienapp_forma_pago']:checked").val();
  */
  var observaciones = $("#clienapp_checkout_observaciones").val();
  var mensaje = "Hola, tenes un nuevo pedido: "+"\n\n";
  mensaje += "Nombre y Apellido: *"+nombre+"*\n\n";
  mensaje += "Telefono: *549"+telefono+"*\n\n";
  mensaje += "Email: *"+email+"*\n\n";
  mensaje += observaciones+"\n\n";
  mensaje += "--------------------\n\n";
  mensaje += "Detalle del pedido: \n\n";
  for(var i=0; i<window.pedido_clienapp.items.length;i++) {
    var item = window.pedido_clienapp.items[i];
    mensaje += "- "+item.cantidad+" x "+item.nombre;
    if (!isEmpty(item.codigo)) mensaje+= " (Cod: "+item.codigo+") ";
    mensaje += " | Precio: $"+Number(item.precio).toFixed(0)+" | Total: $"+Number(item.total).toFixed(0)+"\n\n";
  }
  mensaje += "--------------------\n\n";
  mensaje += "TOTAL: $"+window.pedido_clienapp.total;

  var url = "https://wa.me/"+"549"+window.pedido_clienapp.usuario_celular;
  url+= "?text="+encodeURIComponent(mensaje);  

  // Primero mandamos para finalizar el carrito
  $.ajax({
    "url":"<?php echo mklink("cart/complete_clienapp/"); ?>",
    "type":"post",
    "data":{
      "id_tipo_estado":6,
      "id_cliente":id_cliente,
      "cliente":nombre,
      "email":email,
      "direccion":direccion,
      "telefono":telefono,
      "observaciones":observaciones,
    },
    "dataType":"json",
    "success":function(r){
      // Cuando el carrito fue enviado
      $(".clienapp_paso_1").hide();
      $(".clienapp_paso_2").show();
      $("#clienapp_ver_comprobante").attr("href","<?php echo mklink("sistema/facturas/function/ver_pdf/") ?>"+r.id+"/"+r.id_punto_venta+"/"+ID_EMPRESA+"/");
      window.recargar_despues = 1;
      clienapp_hide_loading();

      // Intentamos abrir otra ventana
      var open = window.open(url,"_blank");
      if (open == null || typeof(open)=='undefined') {
        // Si se bloqueo el popup, se redirecciona
        location.href = url;
      }
    },
    "error":function() {
      clienapp_hide_loading();
    }
  });
  return false;
}
</script>
<link rel="stylesheet" type="text/css" href="/templates/comun/clienapp.css">
<div id="modal_clienapp" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content clienapp_container">  
      <div class="oh tar">
        <img class="cp" style="width: 36px;" src="/templates/comun/img/close.png" alt="Cerrar" onclick="do_clienapp_cerrar()">
      </div>
      <div id="clienapp_checkout_logo"></div>
      <h3 class="clienapp_checkout_titulo" id="clienapp_checkout_titulo"></h3>
      <div class="clienapp_paso_1">
        <h4 class="clienapp_checkout_subtitulo">Complete la siguiente información para enviar su pedido por Whatsapp</h4>
        <div>
          <label class="clienapp_label" for="clienapp_checkout_nombre">Indique su nombre y apellido (*)</label>
          <input type="text" name="clienapp_checkout_nombre" id="clienapp_checkout_nombre" class="clienapp_input" placeholder="Indique su nombre para identificar su pedido">
          <label class="clienapp_label" for="clienapp_checkout_telefono">Indique su número de Whatsapp sin 0 ni 15 (*)</label>
          <input type="text" name="clienapp_checkout_telefono" id="clienapp_checkout_telefono" class="clienapp_input" placeholder="Indique su número para coordinar el pedido">
          <label class="clienapp_label" for="clienapp_checkout_email">Indique su email (*)</label>
          <input type="text" name="clienapp_checkout_email" id="clienapp_checkout_email" class="clienapp_input" placeholder="Indique su email para que le enviemos su recibo">

          <label class="clienapp_label" for="clienapp_checkout_observaciones">Observaciones de su pedido</label>
          <textarea name="clienapp_checkout_observaciones" id="clienapp_checkout_observaciones" class="clienapp_input" placeholder="Escriba algún comentario u observación sobre su pedido"></textarea>

          <?php /*
          <label class="clienapp_label" for="clienapp_checkout_direccion">Indique su direccion</label>
          <input type="text" name="clienapp_checkout_direccion" id="clienapp_checkout_direccion" class="clienapp_input" placeholder="Indique su direccion de entrega">

          <p class="clienapp_label">Como quiere recibir su pedido (*):</p>
          <div class="clienapp_radio">
            <input type="radio" id="clienapp_retiro_comercio" name="clienapp_forma_envio" value="comercio" checked>
            <label for="clienapp_retiro_comercio" class="clienapp_label_radio">RETIRO EN COMERCIO</label><br>
          </div>
          <div class="clienapp_radio">
            <input type="radio" id="clienapp_envio_domicilio" name="clienapp_forma_envio" value="domicilio">
            <label for="clienapp_envio_domicilio" class="clienapp_label_radio">ENVIAR A DOMICILIO</label><br>
          </div>

          <p class="clienapp_label">Como quiere pagar su pedido (*):</p>
          <div class="clienapp_radio">
            <input type="radio" id="clienapp_pago_efectivo" name="clienapp_forma_pago" value="efectivo" checked>
            <label for="clienapp_pago_efectivo" class="clienapp_label_radio">PAGO EN EFECTIVO</label><br>
          </div>
          <div class="clienapp_radio">
            <input type="radio" id="clienapp_pago_tarjeta" name="clienapp_forma_pago" value="tarjeta">
            <label for="clienapp_pago_tarjeta" class="clienapp_label_radio">PAGO CON TARJETA</label><br>
          </div>
          */ ?>

          <p class="clienapp_texto">DETALLE DE SU ORDEN</p>
          <table id="clienapp_checkout_table" class="clienapp_table">
            <thead>
              <tr>
                <th class="clienapp_primero">DESCRIPCIÓN</th>
                <th>CANT.</th>
                <th>PRECIO</th>
                <th class="clienapp_ultimo">TOTAL</th>
              </tr>            
            </thead>
            <tbody></tbody>
          </table>
          <div class="clienapp_center">
            <p class="clienapp_texto">TOTAL DEL PEDIDO</p>
            <p id="clienapp_checkout_total" class="clienapp_precio"></p>
          </div>
          <button onclick="varcreative_registro_clienapp()" id="clienapp_submit" class="clienapp_boton mb20">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> ENVIAR WHATSAPP
          </button>
          <div id="clienapp_loading" style="display: none" class="tac mt10 mb10">
            <img src="/templates/comun/img/ajax-loader.gif" alt="Loading"/>
          </div>
        </div>
        <p class="clienapp_disclaimer">Se requerirá de Whatsapp móvil o web para enviar el mensaje.</p>
        <p class="clienapp_disclaimer">Servicio provisto por <a target="_blank" href="https://www.clienapp.com">Clienapp.com</a></p>
      </div>
      <div class="clienapp_paso_2">
        <div style="max-width: 400px; margin:0 auto">
          <p class="tac">Descargue el siguiente comprobante para presentar en el comercio.</p>
          <div class="mt20 mb20 tac">
            <a href="" target="_blank" id="clienapp_ver_comprobante" class="clienapp_boton mb20">
              VER COMPROBANTE
            </a>
          </div>
          <p class="clienapp_disclaimer">Servicio provisto por <a target="_blank" href="https://www.clienapp.com">Clienapp.com</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php 
// ================================================
// MODAL PARA AGREGAR ITEMS AL CARRITO
// ================================================
?>
<?php 
// Si hemos realizado una operacion con el carrito, nos devuelve un parametro en GET
$ultima_operacion = isset($_GET["o"]) ? filter_var($_GET["o"],FILTER_SANITIZE_STRING) : "";
if (!empty($ultima_operacion)) {

  // Agregamos un item
  if ($ultima_operacion == "add") {
    // Consultamos por el producto agregado
    $id_articulo_agregado = isset($_GET["id"]) ? filter_var($_GET["id"],FILTER_SANITIZE_STRING) : 0;
    $art = $articulo_model->get($id_articulo_agregado);
    if ($art !== FALSE) { 
      $nombre = isset($_GET["name"]) ? urldecode(filter_var($_GET["name"],FILTER_SANITIZE_STRING)) : $art->nombre;
      $categoria = isset($_GET["cat"]) ? urldecode(filter_var($_GET["cat"],FILTER_SANITIZE_STRING)) : $art->rubro;
      $cant = isset($_GET["cant"]) ? urldecode(filter_var($_GET["cant"],FILTER_SANITIZE_STRING)) : 1;
      ?>

<div id="modal_add_cart" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content checkout-page varcreative-checkout">
      <div class="varcreative-panel">
        <div class="varcreative-panel-heading">
          <span class="bold"><?php echo $ll["titulo_item_agregado"] ?></span>         
        </div>
        <div class="varcreative-panel-body">
          <div class="information-blocks">
            <div class="table-responsive">
              <table class="cart-table">
                <tbody>
                    <tr>
                      <th colspan="2" class="column-1 tal"><?php echo $ll["producto"] ?></th>
                      <th><?php echo $ll["precio"] ?></th>
                      <th><?php echo $ll["cantidad"] ?></th>
                      <th><?php echo $ll["subtotal"] ?></th>
                    </tr>
                  <tr>
                    <td>
                      <div class="traditional-cart-entry">
                        <div class="content">
                          <div class="cell-view">
                            <a class="image" href="<?php echo mklink($art->link); ?>">
                              <?php if (!empty($art->path)) { ?>
                                <img src="<?php echo $art->path ?>" alt="<?php echo ($nombre);?>">
                              <?php } else if (!empty($empresa->no_imagen)) { ?>
                                <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($nombre);?>">
                              <?php } else { ?>
                                <img src="images/no-imagen.png" alt="<?php echo ($nombre);?>">
                              <?php } ?>
                            </a>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td style="padding-top: 0px !important">
                      <div class="traditional-cart-entry">
                        <div class="content">
                          <div class="cell-view">
                            <a href="javascript:void(0)" class="tag"><?php echo $categoria ?></a>
                            <a class="traditional-cart-title" href="<?php echo mklink($art->link); ?>"><?php echo $nombre ?></a>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td><div class="subtotal tac">$<?php echo round($art->precio_final_dto,2) ?></div></td>
                    <td><div class="subtotal tac"><?php echo $cant ?></div></td>
                    <td><div class="subtotal tac">$<?php echo round($art->precio_final_dto * $cant,2) ?></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="oh row">
            <div class="col-md-6">
              <a onclick="cerrar_modal()" href="javascript:void(0)" class="btn btn-block bg-sec"><?php echo $ll["continuar_comprando"] ?></a>
            </div>
            <div class="col-md-6">
              <a href="<?php echo mklink("carrito/"); ?>" class="btn btn-block bg-main"><?php echo $ll["ver_carrito"] ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ $("#modal_add_cart").modal('show'); });
function cerrar_modal() { $('#modal_add_cart').modal('hide'); }
</script>
<?php
    }
  }

}
?>