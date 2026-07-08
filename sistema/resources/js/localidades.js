/**
 * Funcion encargada de cargar con AJAX el combo de provincias pasado por parametro
 */
function cargar_provincia(paisCmb,id) {
    show_loading();
    $("#"+id).empty();
    var datos = "id_pais="+$(paisCmb).val();
    $.ajax({
        url: "control/provincias/get.php",
        type: "post",
        dataType: "json",
        data: datos,
        success: function(resp) {
            if (resp.error == false) {
                $("#"+id).append("<option value='0'></option>");
                for(i=0;i<resp.data.length;i++) {
                    var p = resp.data[i];
                    $("#"+id).append("<option value='"+p.id_provincias+"'>"+p.nombre_provincia+"</option>");
                }
            }
            hide_loading();
        }
    });
}


/**
 * Funcion encargada de cargar con AJAX el combo de departamentos pasado por parametro
 */
function cargar_departamento(pciaCmb,id) {
    show_loading();
    $("#"+id).empty();
    var datos = "id_provincia="+$(pciaCmb).val();
    $.ajax({
        url: "control/departamentos/get.php",
        type: "post",
        dataType: "json",
        data: datos,
        success: function(resp) {
            if (resp.error == false) {
                $("#"+id).append("<option value='0'></option>");
                for(i=0;i<resp.data.length;i++) {
                    var p = resp.data[i];
                    $("#"+id).append("<option value='"+p.id_departamentos+"'>"+p.nombre_departamento+"</option>");
                }
            }
            hide_loading();
        }
    });
}


/**
 * Funcion encargada de cargar con AJAX el combo de localidades pasado por parametro
 */
function cargar_localidad(dptoCmb,id) {
    show_loading();
    $("#"+id).empty();
    var datos = "id_departamento="+$(dptoCmb).val();
    $.ajax({
        url: "control/localidades/get.php",
        type: "post",
        dataType: "json",
        data: datos,
        success: function(resp) {
            if (resp.error == false) {
                $("#"+id).append("<option value='0'></option>");
                for(i=0;i<resp.data.length;i++) {
                    var p = resp.data[i];
                    $("#"+id).append("<option value='"+p.id_localidades+"'>"+p.nombre_localidad+"</option>");
                }
            }
            hide_loading();
        }
    });
}
