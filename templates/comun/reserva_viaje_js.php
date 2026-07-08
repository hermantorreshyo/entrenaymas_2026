<?php $lang = isset($lang) ? $lang : "es"; ?>
<script type="text/javascript">
// En esta variable guardamos los opcionales que tiene el viaje
/*
<?php print_r($viaje) ?>
*/
var opcionales = <?php echo (sizeof($viaje->opcionales)>0) ? json_encode($viaje->opcionales) : "{}"; ?>;

// En esta variable vamos guardando el pedido
var pedido = <?php echo json_encode($pedido) ?>;

// Esta variable indica si todos los pasajeros estan dentro del rango de edad para el viaje
var error_edades = 0;
var mensaje_error = "";

// Cuando se cambia la fecha de reserva, se debe refrescar la pagina
// enviandole por get el nuevo parametro de "fecha"
function modificar_fecha_reserva() {
  var params = <?php echo json_encode($get_params); ?>;
  var fecha = $("#reserva_fecha").val();
  if (isEmpty(fecha)) {
    alert("Por favor seleccione una fecha");
    return;
  }
  params.fecha = moment(fecha,"DD/MM/YYYY").format("YYYY-MM-DD");
  var p = encodeQueryData(params);
  $.cookie('pedido', JSON.stringify(window.pedido));
  location.href = "<?php echo mklink("web/reserva/"); ?>?"+p;
}

// Cuando se modifica la cantidad de pasajeros (tanto mayores como menores)
// Se tienen que crear (o eliminar) nuevos formularios
function modificar_pasajeros() {

  var cantidad_adultos = $("#cantidad_adultos").val();
  var cantidad_menores = $("#cantidad_menores").val();

  var d_adultos = cantidad_adultos - $(".form_adultos").length;
  if (d_adultos > 0) {
    // Debemos crear nuevos formularios de adultos
    var r = crear_form({
      "titulo":"<?php echo ($lang == "es")?"ADULTO":"" ?><?php echo ($lang == "en")?"ADULT":"" ?><?php echo ($lang == "pt")?"ADULTO":"" ?> "+($(".form_adultos").length+1),
      "clase":"form_adultos form_pasajeros",
    });
    $("#pasajeros_adultos").append(r);

  } else if (d_adultos < 0) {
    // Debemos eliminar los ultimos formularios de adultos
    for(var i=0; i<Math.abs(d_adultos);i++) {
      $("#pasajeros_adultos .form_adultos").last().remove();
    }
  }

  var d_menores = cantidad_menores - $(".form_menores").length;
  if (d_menores > 0) {
    // Debemos crear nuevos formularios de menores
    var r = crear_form({
      "titulo":"<?php echo ($lang == "es")?"NI&Ntilde;O":"" ?><?php echo ($lang == "en")?"CHILD":"" ?><?php echo ($lang == "pt")?"MENOR":"" ?> "+($(".form_menores").length+1),
      "clase":"form_menores form_pasajeros",
    });
    $("#pasajeros_menores").append(r);

  } else if (d_menores < 0) {
    // Debemos eliminar los ultimos formularios de menores
    for(var i=0; i<Math.abs(d_menores);i++) {
      $("#pasajeros_menores .form_menores").last().remove();
    }
  }

  $(".fecha_nac").datepicker({
    showOn: "button",
    buttonImage: "images/cal.png",
    buttonImageOnly: true,
    buttonText: "dd/mm/yy",
    dateFormat: "dd/mm/yy",
  });
  $(".fecha_nac").mask("99/99/9999");

  recalcular_totales();
}

// Funcion para crear un formulario especifico de un pasajero
function crear_form(config) {
  var titulo = (typeof config.titulo != "undefined") ? config.titulo : "";
  var clase = (typeof config.clase != "undefined") ? config.clase : "";
  var s = "";
  s+='<div class="'+clase+'">';
  s+='<div class="heading2"><i class="fa fa-check-circle-o" aria-hidden="true"></i> '+titulo+'</div>';
  s+='<div class="forms style2">';
  s+='  <input type="hidden" class="precio"/>';
  s+='  <input type="hidden" class="moneda"/>';
  s+='  <div class="row">';
  s+='    <div class="col-md-6">';
  s+='      <input type="text" class="nombre" placeholder="<?php 
    echo ($lang == "es")?"Nombre completo":"";
    echo ($lang == "en")?"Name":"";
    echo ($lang == "pt")?"Nome":"";
  ?>" />';
  s+='      <i class="fa fa-user" aria-hidden="true"></i>';
  s+='    </div>';
  s+='    <div class="col-md-6">';
  s+='      <input type="text" class="dni" placeholder="<?php
    echo ($lang == "es")?"Pasaporte / DNI":"";
    echo ($lang == "en")?"Passport":"";
    echo ($lang == "pt")?"Passaporte":"";
  ?>" />';
  s+='      <i class="fa fa-address-card" aria-hidden="true"></i>';
  s+='    </div>';
  s+='    <div class="col-md-6">';
  s+='      <label><?php 
    echo ($lang == "es")?"Fecha de nacimiento":"";
    echo ($lang == "en")?"Birthdate":"";
    echo ($lang == "pt")?"Data de nascimento":"";
  ?></label>';
  s+='      <div class="view-inputs">';
  s+='        <input onchange="recalcular_totales()" type="text" placeholder="<?php 
    echo ($lang == "es")?"Fecha de nacimiento":"";
    echo ($lang == "en")?"Birthdate":"";
    echo ($lang == "pt")?"Data de nascimento":"";
  ?>" class="fecha_nac" />';
  s+='      </div>';
  s+='    </div>';
  s+='    <div class="col-md-6">';
  s+='      <select class="nacionalidad">';
  s+='        <option value=""><?php
    echo ($lang == "es")?"Nacionalidad":"";
    echo ($lang == "en")?"Nationality":"";
    echo ($lang == "pt")?"Nacionalidade":"";
  ?></option>';
  <?php $q_nac = mysqli_query($conx,"SELECT * FROM custom_nacionalidades WHERE id_empresa = $empresa->id ORDER BY orden ASC");
  while(($nac=mysqli_fetch_object($q_nac))!==NULL) { ?>
  s+='        <option value="<?php echo utf8_encode($nac->nombre) ?>"><?php echo utf8_encode($nac->nombre) ?></option>';
  <?php } ?>
  s+='      </select>';
  s+='      <i class="fa fa-globe" aria-hidden="true"></i>';
  s+='    </div>';
  s+='  </div>';
  s+='</div>';
  s+='</div>';
  return s;
}

// Recalcula los totales dependiendo si son mayores o menores
function recalcular_totales() {
  
  pedido.subtotales = {};
  pedido.opcionales = [];
  pedido.total_general = 0;
  error_edades = 0;

  var precios = <?php echo json_encode($viaje->precios) ?>;
  var fecha_reserva = "<?php echo $fecha_reserva ?>";
  $(".form_pasajeros").each(function(i,e){
    var fecha_nacimiento = "";
    try {
      fecha_nacimiento = validate_field($(e).find(".fecha_nac"));
    } catch(e) {
      return false;
    }
    var edad = Math.floor(moment(fecha_reserva).diff(moment(fecha_nacimiento,"DD/MM/YYYY"),'years',true));
    if (edad <= 0) return false;
    var encontro_edad = false;

    // Buscamos la edad dentro del array de precios
    for(var k=0;k<precios.length;k++) {
      var precio = precios[k];
      var edad_desde = parseInt(precio.edad_desde);
      var edad_hasta = parseInt(precio.edad_hasta);
      if (edad_desde <= edad && edad <= edad_hasta && precio.fecha_desde <= fecha_reserva && fecha_reserva <= precio.fecha_hasta) {
        if (typeof pedido.subtotales[precio.id_tipo_tarifa] == "undefined") {
          pedido.subtotales[precio.id_tipo_tarifa] = {
            "cantidad": 0,
            "nombre": precio.nombre,
            "precio": precio.precio,
            "moneda": precio.moneda,
          };
        }
        pedido.subtotales[precio.id_tipo_tarifa].cantidad++;
        $(e).find(".moneda").val(precio.moneda);
        $(e).find(".precio").val(precio.precio);
        encontro_edad = true;
      } else {
        mensaje_error = "La edad permitida para la excursion es entre "+edad_desde+" y "+edad_hasta+" años.";
      }
    }
    // Si no se encontro la edad, hay un error en los rangos
    if (!encontro_edad) error_edades++;
  });

  // Renderizamos los precios del viaje
  $("#precios_viaje").empty();
  console.log(pedido);
  for(var subtotal in pedido.subtotales) {
    var v = pedido.subtotales[subtotal];
    // Ponemos los precios por categoria del viaje
    var s = '';
    s+='<div class="pull-left">';
    s+='<h5>'+v.moneda+' '+Number(v.precio * v.cantidad).toFixed(2)+'</h5>';
    s+='<p>'+v.nombre+'</p>';
    s+='</div>';
    $("#precios_viaje").append(s);
    pedido.total_general += parseFloat(v.precio * v.cantidad);
  }

  // Mostramos los opcionales seleccionados
  $("#opcionales").empty();
  $(".opcionales_checkbox:checked").each(function(i,e){
    var id = $(e).val();
    var opcional = null;
    for(var k=0;k<window.opcionales.length;k++) {
      var o = window.opcionales[k];
      if (o.id == id) { opcional = o; break; }
    }
    if (opcional != null) {

      var subtotales_opcionales = {};
      $(".form_pasajeros").each(function(i,e){

        var fecha_nacimiento = "";
        try {
          fecha_nacimiento = validate_field($(e).find(".fecha_nac"));
        } catch(e) {
          return false;
        }
        var edad = Math.floor(moment(fecha_reserva).diff(moment(fecha_nacimiento,"DD/MM/YYYY"),'years',true));
        if (edad <= 0) return false;

        // Buscamos la edad dentro del array de precios
        for(var jj=0;jj<opcional.precios.length;jj++) {
          var precio = opcional.precios[jj];
          var edad_desde = parseInt(precio.edad_desde);
          var edad_hasta = parseInt(precio.edad_hasta);
          if (edad_desde <= edad && edad <= edad_hasta && precio.fecha_desde <= fecha_reserva && fecha_reserva <= precio.fecha_hasta) {

            if (typeof subtotales_opcionales[precio.id_tipo_tarifa] == "undefined") {
              subtotales_opcionales[precio.id_tipo_tarifa] = {
                "cantidad": 0,
                "id_opcional": opcional.id,
                "nombre": precio.nombre,
                "precio": precio.precio,
                "moneda": precio.moneda,
              };
            }
            subtotales_opcionales[precio.id_tipo_tarifa].cantidad++;
          }
        }
      });

      // Renderizamos los precios del viaje
      var s = '';
      s+='<div class="stotal style2">';
      s+='<h3>'+opcional.nombre+'</h3>';
      for(var subtotal in subtotales_opcionales) {
        var v = subtotales_opcionales[subtotal];
        // Ponemos los precios por categoria del viaje
        s+='<div class="pull-left">';
        s+='<h5>'+v.moneda+' '+Number(v.precio * v.cantidad).toFixed(2)+'</h5>';
        s+='<p>'+v.nombre+'</p>';
        pedido.total_general += parseFloat(v.precio * v.cantidad);
        s+='</div>';
      }
      s+='</div>';
      $("#opcionales").append(s);

      // Si el objeto no es vacio, lo agregamos
      if (!(Object.keys(subtotales_opcionales).length === 0 && subtotales_opcionales.constructor === Object)) { 
        pedido.opcionales.push(subtotales_opcionales);
      }
      
    }
  });

  // Actualizamos el total
  if (pedido.total_general > 0) {
    $("#total_general").html("$ "+Number(pedido.total_general).toFixed(2));
    $(".gtotal").show();
  } else {
    $(".gtotal").hide();
  }
}

function cambiar_metodo_pago() {
  var v = $("input[name=payments]:checked").val();
  $(".finalizar_cont").hide();
  $("#finalizar_"+v).show();
}

// Guardamos los datos de los pasajeros en la session
// y pasamos al formulario de pago
function proceder_pago() {

  var ocurrio_error = false;
  window.pedido.adultos = new Array();
  window.pedido.menores = new Array();

  // Controlamos si hay algun error con la edad
  if (error_edades > 0) {
    alert(window.mensaje_error);
    return false;
  }

  // Validamos si todos los campos estan completos
  $(".form_pasajeros").each(function(i,e){
    var pasajero = {};
    try {
      var nombre = validate_field($(e).find(".nombre"));
      if (nombre != false) pasajero.nombre = nombre;
      var dni = validate_field($(e).find(".dni"));
      if (dni != false) pasajero.dni = dni;
      var fecha_nac = validate_field($(e).find(".fecha_nac"));
      if (fecha_nac != false) pasajero.fecha_nac = fecha_nac;

      var edad = Math.floor(moment().diff(moment(fecha_nac,"DD/MM/YYYY"),'years',true));
      if (edad <= 0) {
        throw "Fecha de nacimiento invalida";
      }

      var nacionalidad = validate_field($(e).find(".nacionalidad"));
      if (nacionalidad != false) pasajero.nacionalidad = nacionalidad;
      var email = validate_field($(e).find(".email"));
      if (email != false) pasajero.email = email;
      var email_2 = validate_field($(e).find(".email_2"));
      if (email_2 != false) pasajero.email_2 = email_2;
      var telefono = validate_field($(e).find(".telefono"));
      if (telefono != false) pasajero.telefono = telefono;
      var celular = validate_field($(e).find(".celular"));
      if (celular != false) pasajero.celular = celular;
      pasajero.precio = $(e).find(".precio").val();
      pasajero.moneda = $(e).find(".moneda").val();

      // Todos los campos estan completos
      if (email != false && email != email_2) {
        $(e).find(".email_2").focus();
        throw "Error: Los emails ingresados no coinciden.";
        return false;
      }

    } catch(error) {
      alert(error);
      ocurrio_error = true;
      return false;
    }

    // Agregamos el pasajero al objeto
    if ($(e).hasClass("form_adultos")) {
      window.pedido.adultos.push(pasajero);
    } else if ($(e).hasClass("form_menores")) {
      window.pedido.menores.push(pasajero);
    }
    
  });
  if (ocurrio_error) return;

  // Controlamos si ingreso un email valido
  var email = $(".email").val();
  if (!validateEmail(email)) {
    alert("Error: Por favor ingrese un email valido.");
    $(".email").focus();
    return;        
  }

  // Si los opcionales no son validos
  if (pedido.opcionales.length != $(".opcionales_checkbox:checked").length) {
    alert("ERROR: Los opcionales no estan bien configurados.");
    return;
  }

  // Si tiene configurado lo de hoteles
  if ($("#pedido_hoteles").length > 0) {
    window.pedido.hotel = $("#pedido_hoteles").val();
    window.pedido.fecha_llegada_hotel = $("#pedido_fecha_llegada_hotel").val();
    window.pedido.hotel_observaciones = $("#pedido_hotel_observaciones").val();
  }
  window.pedido.prestador_servicio = "<?php echo $viaje->custom_8 ?>";
  window.pedido.id_viaje = "<?php echo $id ?>";

  ga('send','event','reserva','datos');

  $.cookie('pedido', JSON.stringify(window.pedido));  
  <?php unset($get_params["step"]); ?>
  location.href = "<?php echo mklink("web/reserva/")."?step=2&".http_build_query($get_params); ?>";
}

// Controlamos que se hayan aceptado los terminos y demas cosas
function validar_pago() {
  if ($("#terminos").length>0 && !($("#terminos").is(":checked"))) {
    alert("Por favor acepte los terminos y condiciones del servicio.");
    return false;
  }
  if (!($("#terms").is(":checked"))) {
    alert("Por favor acepte los terminos y condiciones del servicio.");
    return false;
  }
  return true;
}

function pagar_mercado_pago() {
  if (!validar_pago()) return false;
  ga('send','event','reserva','formapago');
  <?php if (isset($preference)) { ?>
    $MPC.openCheckout ({
      url: "<?php echo $preference["response"]["init_point"]; ?>",
      mode: "modal",
    });
  <?php } ?>
}

function pagar_transferencia_bancaria() {
  if (!validar_pago()) return;
  ga('send','event','reserva','formapago');
  location.href="<?php echo mklink("web/reserva/?id=".$viaje->id."&fecha=".$fecha_reserva."&step=3&result=success&type=transfer"); ?>";
}

function expandir_texto(e) {
  $(e).parents(".texto_cont").find(".texto_breve").hide();
  $(e).parents(".texto_cont").find(".texto_completo").show();
}
function colapsar_texto(e) {
  $(e).parents(".texto_cont").find(".texto_completo").hide();
  $(e).parents(".texto_cont").find(".texto_breve").show();
}

// Helper para validar un campo
function validate_field(field) {
  if ($(field).length > 0) {
    var valor = $(field).val();
    if (isEmpty(valor)) {
      $(field).focus();
      throw new Error("Por favor ingrese un valor");
    } else return valor;
  } else return false;
}

// Helper para crear un string de parametros para GET
function encodeQueryData(data) {
  let ret = [];
  for (let d in data)
    ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
  return ret.join('&');
}
</script>