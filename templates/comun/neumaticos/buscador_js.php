<script type="text/javascript">
function actualizar_select(e) {

  var id = $(e).attr("id");
  var marca = $("#buscador_marca").val();
  var modelo = $("#buscador_modelo").val();
  var anio = $("#buscador_anio").val();
  var version = $("#buscador_version").val();

  if (id == "buscador_marca") {

    if (marca == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_marca").focus();
      return;
    }

    $.ajax({
      "url":"/sistema/vehiculos/function/get_by_field/",
      "dataType":"json",
      "type":"post",
      "data":{
        "id_empresa":"<?php echo (isset($id_empresa_referencia) ? $id_empresa_referencia : 120) ?>",
        "campos":"DISTINCT modelo ",
        "marca":marca,
        "order_by":"modelo",
      },
      "success":function(r) {
        $("#buscador_modelo").empty();
        $("#buscador_modelo").append("<option value='0'>Modelo</option>");
        for(var i=0; i<r.length;i++) {
          var o = r[i];
          $("#buscador_modelo").append("<option>"+o.modelo+"</option>");
        }
      }
    });

  } else if (id == "buscador_modelo") {

    if (marca == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_marca").focus();
      return;
    }
    if (modelo == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_modelo").focus();
      return;
    }

    $.ajax({
      "url":"/sistema/vehiculos/function/get_by_field/",
      "dataType":"json",
      "type":"post",
      "data":{
        "id_empresa":"<?php echo (isset($id_empresa_referencia) ? $id_empresa_referencia : 120) ?>",
        "campos":"DISTINCT anio ",
        "marca":marca,
        "modelo":modelo,
        "order_by":"anio",
      },
      "success":function(r) {
        $("#buscador_anio").empty();
        $("#buscador_anio").append("<option value='0'>A&ntilde;o</option>");
        for(var i=0; i<r.length;i++) {
          var o = r[i];
          $("#buscador_anio").append("<option>"+o.anio+"</option>");
        }
      }
    });

  } else if (id == "buscador_anio") {

    if (marca == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_marca").focus();
      return;
    }
    if (modelo == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_modelo").focus();
      return;
    }
    if (anio == 0) {
      alert("Por favor seleccione una opcion");
      $("#buscador_anio").focus();
      return;
    }

    $.ajax({
      "url":"/sistema/vehiculos/function/get_by_field/",
      "dataType":"json",
      "type":"post",
      "data":{
        "id_empresa":"<?php echo (isset($id_empresa_referencia) ? $id_empresa_referencia : 120) ?>",
        "campos":"version, rodado, ancho, perfil ",
        "marca":marca,
        "modelo":modelo,
        "anio":anio,
        "order_by":"version",
      },
      "success":function(r) {
        $("#buscador_version").empty();
        $("#buscador_version").append("<option value='0'>Versi&oacute;n</option>");
        for(var i=0; i<r.length;i++) {
          var o = r[i];
          var opt = "<option ";
          opt+="data-rodado='"+o.rodado+"' ";
          opt+="data-perfil='"+o.perfil+"' ";
          opt+="data-ancho='"+o.ancho+"' ";
          opt+=">"+o.version+"</option>";
          $("#buscador_version").append(opt);
        }
      }
    });

  }
}

function buscar_por_marca() {
  var marca = $("#buscador_marca").val();
  if (marca == 0) {
    alert("Por favor seleccione una opcion");
    $("#buscador_marca").focus();
    return false;
  }
  var modelo = $("#buscador_marca").val();
  if (modelo == 0) {
    alert("Por favor seleccione una opcion");
    $("#buscador_modelo").focus();
    return false;
  }
  var anio = $("#buscador_marca").val();
  if (anio == 0) {
    alert("Por favor seleccione una opcion");
    $("#buscador_anio").focus();
    return false;
  }
  var version = $("#buscador_version").val();
  if (version == 0) {
    alert("Por favor seleccione una opcion");
    $("#buscador_version").focus();
    return false;
  }

  var ancho = $("#buscador_version option:selected").data("ancho");
  var perfil = $("#buscador_version option:selected").data("perfil");
  var rodado = $("#buscador_version option:selected").data("rodado");
  $("#buscar_por_marca_ancho").val(ancho);
  $("#buscar_por_marca_perfil").val(perfil);
  $("#buscar_por_marca_rodado").val(rodado);
  return true;
}
</script>