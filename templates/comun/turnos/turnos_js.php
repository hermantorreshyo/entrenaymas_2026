<script type="text/javascript" src="/sistema/resources/js/jquery/ui/jquery-ui.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

  $("#turno_fecha").datepicker({
    "dateFormat":"dd/mm/yy",
    "currentText":"Hoy",
    "buttonImage": "/resources/images/datepicker.png",
    "buttonImageOnly": true,
    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
    "nextText":"Proximo",
    "prevText":"Anterior",
    "minDate":new Date(),
    "beforeShowDay":function(date) {
      var dia = date.getDay(); // Domingo es 0
      var dias = $("#turno_servicio option:selected").data("dias");
      if (isEmpty(dias)) return [true];
      dias = dias.split("-");
      salida = false;
      for(var i=0;i<dias.length;i++) {
        var d = dias[i];
        if (dia == d) {
          salida = true;
          break;
        }
      }
      return [salida];
    }
  });
  $("#turno_fecha").change(buscar_horarios);
});

function cambiar_calendario() {
  $("#turno_fecha").val("");
  $("#turno_fecha").datepicker("refresh");
  buscar_horarios();
}

function buscar_horarios() {
  var fecha = $("#turno_fecha").val();
  var id_servicio = $("#turno_servicio").val();
  if (isEmpty(fecha)) return;
  if (id_servicio == 0) return;
  $.ajax({
    "url":"/sistema/turnos/function/disponibles/",
    "dataType":"json",
    "type":"post",
    "data":{
      "id_empresa":ID_EMPRESA,
      "id_servicio":id_servicio,
      "fecha":fecha,
    },
    "success":function(r) {
      if (typeof r.error != "undefined") {
        alert(r.error);
      } else {
        $("#turno_horario").empty();
        $("#turno_horario").append('<option value="0">Elija Horario</option>');
        for(var i=0; i<r.disponibles.length; i++) {
          var d = r.disponibles[i];
          $("#turno_horario").append('<option>'+d.hora+'</option>');
        }
      }
    }
  });
}
function enviar_turno() {
  var nombre = $("#turno_nombre").val();
  var telefono = $("#turno_telefono").val();
  var email = $("#turno_email").val();
  var id_servicio = $("#turno_servicio").val();
  var servicio = $("#turno_servicio option:selected").text();
  var fecha = $("#turno_fecha").val();
  var horario = $("#turno_horario").val();
  
  if (isEmpty(nombre) || nombre == "Nombre") {
    alert("Por favor ingrese un nombre");
    $("#turno_nombre").focus();
    return false;          
  }
  if (isEmpty(telefono)) {
    alert("Por favor ingrese un telefono");
    $("#turno_telefono").focus();
    return false;          
  }
 
  if (!validateEmail(email)) {
    alert("Por favor ingrese un email valido");
    $("#turno_email").focus();
    return false;          
  }
  if (id_servicio == 0) {
    alert("Por favor seleccione un servicio");
    $("#turno_servicio").focus();
    return false;              
  }    

  if (isEmpty(fecha)) {
    alert("Por favor ingrese una fecha");
    $("#turno_fecha").focus();
    return false;          
  }
  if (horario == 0) {
    alert("Por favor seleccione un horario");
    $("#turno_servicio").focus();
    return false;              
  }    
  
  $("#turno_submit").val("ENVIANDO...");
  $("#turno_submit").attr('disabled', 'disabled');
  var datos = {
    "nombre":nombre,
    "telefono":telefono,
    "id_servicio":id_servicio,
    "fecha":fecha,
    "hora":horario,
    "email":email,
    "asunto":servicio,
    "id_empresa":ID_EMPRESA,
    "para":"<?php echo $empresa->email ?>",
    "bcc":"<?php echo $empresa->bcc_email ?>",
  }
  $.ajax({
    "url":"/sistema/turnos/function/enviar/",
    "type":"post",
    "dataType":"json",
    "data":datos,
    "success":function(r){
      if (r.error == 0) {
        alert("El turno se ha realizado correctamente. Hemos enviado un email con el comprobante. Muchas gracias!");
        location.reload();
      } else {
        alert(r.mensaje);
        $("#turno_submit").val("ENVIAR");
        $("#turno_submit").removeAttr('disabled');
      }
    }
  });
  return false;
}
</script>
