// Los parametros de fechas tienen que estar en formato YYYY:MM:DD HH:ii:SS
function diferenciaTiempo(fecha_desde,fecha_hasta) {
  var desde = moment(fecha_desde);
  var hasta = moment(fecha_hasta);
  var d = moment.duration(hasta.diff(desde));
  return Math.ceil(d.asMinutes());
}

String.prototype.replaceAll = function(search, replacement) {
  var target = this;
  return target.replace(new RegExp(search, 'g'), replacement);
};

String.prototype.ucwords = function() {
  str = this.toLowerCase();
  return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
    function(s){
      return s.toUpperCase();
  });
};

function urlB64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function mostrar_fecha(fecha,hora) {
  hora = (hora.length > 0) ? hora.substr(0,5) : "";
  // Si es hoy
  if (fecha == moment().format("DD/MM/YYYY")) {
    if (isEmpty(hora)) return "Hoy";
    else return hora;
  // Si fue ayer
  } else if (fecha == moment().subtract(1,'days').format("DD/MM/YYYY")) {
    return "Ayer"+((!isEmpty(hora))?" a las "+hora:"");
  // Si es mañana
  } else if (fecha == moment().add(1,'days').format("DD/MM/YYYY")) {
    return "Ma&ntilde;ana"+((!isEmpty(hora))?" a las "+hora:"");
  }
  return fecha+" "+hora;
}

function mostrar_fecha_completa(fecha) {
  if (fecha.indexOf(" ")>0) {
    hora = fecha.substr(fecha.indexOf(" ")+1);
    fecha = fecha.substr(0,fecha.indexOf(" "));
    return mostrar_fecha(fecha,hora);
  } else {
    return mostrar_fecha(fecha,""); 
  }
}

if (typeof nl2br == "undefined") {
  function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
  }
}
if (typeof br2nl == "undefined") {
  function br2nl (str) {
    str = (str + '');
    str = str.replaceAll("<br>","\n");
    str = str.replaceAll("<br />","\n");
    str = str.replaceAll("<br/>","\n");
    return str;
  }
}
if (typeof isHtml == "undefined") {
  function isHtml (str) {
    return /<[a-z][\s\S]*>/i.test(str);
  }
}

function crear_select2(id,config) {
  if (typeof config == "undefined") config = {};
  $("#select2-"+id+"-container").parents(".select2-container").remove();
  $("#"+id).select2(config);
}

function htmlentities(str) {
  s = String(str);
  s = s.replace(/&/g, '&amp;');
  s = s.replace(/</g, '&lt;');
  s = s.replace(/>/g, '&gt;');
  s = s.replace(/"/g, '&quot;')
  s = s.replace(/ñ/g, '&ntilde;');
  s = s.replace(/Ñ/g, '&Ntilde;');
  s = s.replace(/á/g, '&aacute;');
  s = s.replace(/é/g, '&eacute;');
  s = s.replace(/í/g, '&iacute;');
  s = s.replace(/ó/g, '&oacute;');
  s = s.replace(/ú/g, '&uacute;');
  s = s.replace(/Á/g, '&Aacute;');
  s = s.replace(/É/g, '&Eacute;');
  s = s.replace(/Í/g, '&Iacute;');
  s = s.replace(/Ó/g, '&Oacute;');
  s = s.replace(/Ú/g, '&Uacute;');
  s = s.replace(/€/g, '&euro;');
  s = s.replace(/\n/g, '<br>'); // Nueva linea
  return s;
}

/**
 * Esta funcion redondea un numero pasado por parametro en 2 decimales
 */
 function redondear(numero)
 {
   var original=parseFloat(numero);
   var result=Math.round(original*100)/100 ;
   return Number(result).toFixed(2);
 }

 function toFloat(n) {
  if (isEmpty(String(n))) return 0;
  var m = parseFloat(n);
  if (isNaN(m)) return 0;
  else return m;
}

function toInteger(n) {
  if (isEmpty(String(n))) return 0;
  var m = parseInt(n);
  if (isNaN(m)) return 0;
  else return m;
}


/**
 * Funcion que evalua si la cadena pasada por parametro es vacia o no
 * @param str String que se desea evaluar
 * @return true en caso de ser vacia, false de otra forma
 */
function isEmpty(str) {
  try {
    if (str == null) return true;
    else {
      str = String(str);
      if (str.replace == undefined) return true;
      return (str.replace(/\s/g,"")=="");
    }
  } catch(e) {
    console.log(e);
    return false;
  }
}


/**
 * Valida una direccion de mail
 * @param mail Direccion que se desea corroborar
 * @return true si es una direccion valida, false en caso contrario
 */
function validateEmail(mail) {
  return (/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,10})$/.test(mail));
}

function validateCuit(sCUIT) { 
  sCUIT = String(sCUIT).replace(/\-/g,"");
  var aMult = '5432765432'; 
  var aMult = aMult.split(''); 
  if (sCUIT && sCUIT.length == 11) { 
    aCUIT = sCUIT.split(''); 
    var iResult = 0; 
    for(i = 0; i <= 9; i++) { 
      iResult += aCUIT[i] * aMult[i]; 
    } 
    iResult = (iResult % 11); 
    iResult = 11 - iResult; 
    if (iResult == 11) iResult = 0; 
    if (iResult == 10) iResult = 9; 
    if (iResult == aCUIT[10]) { 
      return true; 
    } 
  }     
  return false; 
}


//================================================================================================

function add_thumbnail_preffix(str)
{
	var posicionPunto = str.lastIndexOf(".");
	return str.substr(0,posicionPunto)+"_t"+str.substr(posicionPunto);
}

function remove_thumbnail_suffix(str)
{
	var posicionPunto = str.lastIndexOf(".");
	return str.substr(0,posicionPunto-2)+str.substr(posicionPunto);	
}


function createdatepicker(e,fecha) {
  var fecha_f = "";

	// Si se envia un Date como fecha
	if (fecha != undefined && fecha instanceof Date) {
		fecha_f = $.datepicker.formatDate("dd/mm/yy",fecha);
	}
	// Si se envia un String como fecha
  else if (fecha != undefined && typeof fecha === "string" && $.trim(fecha) != "" && fecha != "0000-00-00") {
		// Si el formato es YYYY-MM-DD, lo convertimos
    if ( (fecha.indexOf("-")>=0 && fecha.indexOf("-")<3) || (fecha.indexOf("/")>=0 && fecha.indexOf("/")<3)) {
			// Sino lo ponemos igual
			fecha_f = fecha;
		} else {
			// Convertimos la fecha
			fecha_f = moment(fecha).format("DD/MM/YYYY");
		}
	}
  $(e).datepicker({
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
    "defaultDate":fecha_f
  });
  $(e).mask("99/99/9999");
  $(e).val(fecha_f);
}

function createmonthpicker(e,fecha) {
  $(e).datepicker({
    "dateFormat": "MM yy",
    "changeMonth": true,
    "changeYear": true,
    "showButtonPanel": true,
    "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
    "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
    "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
    "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
    "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
    onClose: function(dateText, inst) {
      /*
      function isDonePressed(){
        return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
      }
      if (isDonePressed()){
        */
        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
        $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');
        $('.date-picker').focusout()//Added to remove focus from datepicker input box on selecting date
      //}
    },
    beforeShow : function(input, inst) {
      inst.dpDiv.addClass('month_year_datepicker')
      if ((datestr = $(this).val()).length > 0) {
        year = datestr.substring(datestr.length-4, datestr.length);
        month = datestr.substring(0, 2);
        $(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
        $(this).datepicker('setDate', new Date(year, month-1, 1));
        $(".ui-datepicker-calendar").hide();
      }
    }
  });
}


function createtimepicker(e,fecha) {
  var fecha_f = "";
	// Si se envia un String como fecha
  if (fecha != undefined && typeof fecha === "string" && $.trim(fecha) != "" && fecha != "0000-00-00") {
		// Si el formato es YYYY-MM-DD, lo convertimos
    if (fecha.indexOf("-")>0) {
            // Convertimos la fecha a formato castellano
            fecha_f = moment(fecha).format("DD/MM/YYYY HH:mm");            
          } else {
            fecha_f = fecha;
          }
        }
        $(e).datetimepicker({
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
          "defaultDate":fecha_f,
          "timeOnlyTitle": 'Tiempo',
          "timeText": 'Tiempo',
          "hourText": 'Hora',
          "minuteText": 'Minutos',
          "secondText": 'Segundos',
          "currentText": 'Ahora',
          "closeText": 'OK',
        });
        if (fecha != undefined && fecha instanceof Date) $(e).datetimepicker('setDate',fecha);
        else {
          $(e).mask("99/99/9999 99:99");
          $(e).val(fecha_f);        
        }
      }



//================================================================================================

// Crea un lightbox pasandole el HTML
function crearLightboxHTML(config) {

  var html = (typeof config.html != "undefined") ? config.html : "";
  var width = (typeof config.width != "undefined") ? config.width : 0;
  var height = (typeof config.height != "undefined") ? config.height : 0;
  var escapable = (typeof config.escapable != "undefined") ? config.escapable : true;

  var cant = $(".modal").length;
  var zindex = 2060 + (cant * 10);
  
  var d = $("<div/>",{
    "class":"modal",
    "role":"dialog",
    "style":"z-index: "+zindex,
    //"tabindex":"-1"
  });
  $(d).append("<div class='modal-dialog'><div class='modal-content'></div></div>");
  $(d).find(".modal-content").append(html);
  
  // Si podemos elegir el ancho
  if ($(document).outerWidth() > width) {
    $(d).find(".modal-dialog").width(width+"px");
  } else {
    $(d).find(".modal-dialog").css("width","inherit");
  }

  // Configuracion del modal
  var c = {
    "show": true,
    "keyboard": escapable,    
  };
  if (escapable == false) c.backdrop = 'static';
  $(d).modal(c);
  $(".modal-backdrop:last").css("z-index",zindex-5);
  $(d).on('hidden.bs.modal',function(){
    // Para que se pueda seguir scrolleando si se abren lightbox sucesivos
    if($('.modal').hasClass('in')) {
      $('body').addClass('modal-open');
    }
    // Si tiene un callback
    if (config.callback != undefined) config.callback();
    $(this).remove();
  });
}

// ================================================================================================

// Muestra el loading de la pagina
function show_loading() {

}

// Oculta el loading de la pagina
function hide_loading() {

}

// Esta funcion es un ARREGLO para no tener que modificar todos los paths de las imagenes
// Si comienza con http, devuelve el path igual, sino le agrega /sistema/+path
function show_path(path) {
  if (path.indexOf("http://")>=0 || path.indexOf("https://")>=0) {
    return path;
  } else {
    return "/sistema/"+path;
  }
}

// Funcion que imita a file_helper.php/filename
function filename(str,replace_char){
  if (typeof replace_char == "undefined") replace_char = "-";
  return removeDiacritics(str).toLowerCase().replace(/ /g,replace_char);
}

function removeDiacritics(str) {

  var defaultDiacriticsRemovalMap = [
  {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
  {'base':'AA','letters':/[\uA732]/g},
  {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
  {'base':'AO','letters':/[\uA734]/g},
  {'base':'AU','letters':/[\uA736]/g},
  {'base':'','letters':/[\u0028\u0029\u002B\u002A\u002C\u0021\u0022\u0023\u005B\u005C\u005D\u005E\u0060\u0040\u003A\u003B\u003D\u003E\u003F\u00BF\u007B\u007C\u007D\u007E\u0025\u0026\u0027]/g},
  {'base':'AV','letters':/[\uA738\uA73A]/g},
  {'base':'AY','letters':/[\uA73C]/g},
  {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
  {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
  {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
  {'base':'DZ','letters':/[\u01F1\u01C4]/g},
  {'base':'Dz','letters':/[\u01F2\u01C5]/g},
  {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
  {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
  {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
  {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
  {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
  {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
  {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
  {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
  {'base':'LJ','letters':/[\u01C7]/g},
  {'base':'Lj','letters':/[\u01C8]/g},
  {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
  {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
  {'base':'NJ','letters':/[\u01CA]/g},
  {'base':'Nj','letters':/[\u01CB]/g},
  {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
  {'base':'OI','letters':/[\u01A2]/g},
  {'base':'OO','letters':/[\uA74E]/g},
  {'base':'OU','letters':/[\u0222]/g},
  {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
  {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
  {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
  {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
  {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
  {'base':'TZ','letters':/[\uA728]/g},
  {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
  {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
  {'base':'VY','letters':/[\uA760]/g},
  {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
  {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
  {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
  {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
  {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
  {'base':'aa','letters':/[\uA733]/g},
  {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
  {'base':'ao','letters':/[\uA735]/g},
  {'base':'au','letters':/[\uA737]/g},
  {'base':'av','letters':/[\uA739\uA73B]/g},
  {'base':'ay','letters':/[\uA73D]/g},
  {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
  {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
  {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
  {'base':'dz','letters':/[\u01F3\u01C6]/g},
  {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
  {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
  {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
  {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
  {'base':'hv','letters':/[\u0195]/g},
  {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
  {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
  {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
  {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
  {'base':'lj','letters':/[\u01C9]/g},
  {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
  {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
  {'base':'nj','letters':/[\u01CC]/g},
  {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
  {'base':'oi','letters':/[\u01A3]/g},
  {'base':'ou','letters':/[\u0223]/g},
  {'base':'oo','letters':/[\uA74F]/g},
  {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
  {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
  {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
  {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
  {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
  {'base':'tz','letters':/[\uA729]/g},
  {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
  {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
  {'base':'vy','letters':/[\uA761]/g},
  {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
  {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
  {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
  {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
  ];

  for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
    str = str.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
  }
  return str;
}

function zeroFill(number,width) {
  width -= number.toString().length;
  if ( width > 0 ) {
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
    return number + ""; // always return a string
  }

// Este metodo es usado en la facturacion, para separar los campos por caracteres no numericos
// @param solo_numero: indica si se procesa un numero o acepta caracteres de letras
function getField(string,pos,solo_numero) {
  solo_numero = (typeof solo_numero == "undefined")?true:false;
  var s = "";
  for(var i=pos;i<string.length;i++) {
    var c = string.charAt(i);
    if (solo_numero) {
      if (isNaN(parseInt(c)) && (c!=".") && (c!="-")) break;
      else s=s+c;
    } else {
      s=s+c;            
    }
  }
  if (solo_numero) {
    try {
      var f = parseFloat(s);
      if (isNaN(f)) return 0;
      else return f;
    } catch(e) {
      return 0;
    }        
  } else {
    return s;
  }
}

// El numero viene como string en formato español: 1.000.000,56
// y hay q devolver un float bien
function decimal(f) {
    f = f.replace(",","-"); // 1.000.000-56
    f = f.replace(/\./g,"");
    f = f.replace(/\-/g,".");
    return parseFloat(f);
  }

  function str2date(s) {
    var d = new Date(s);
    var dia = lpad(String(d.getDate()),2,"0");
    var mes = lpad(String(d.getMonth()+1),2,"0");
    var anio = d.getFullYear();
    var hora = lpad(String(d.getHours()),2,"0");
    var minutos = lpad(String(d.getMinutes()),2,"0");
    return dia+"/"+mes+"/"+anio+" "+hora+":"+minutos;
  }

  function date2str(d) {
    var dia = lpad(String(d.getDate()),2,"0");
    var mes = lpad(String(d.getMonth()+1),2,"0");
    var anio = d.getFullYear();
    var hora = lpad(String(d.getHours()),2,"0");
    var minutos = lpad(String(d.getMinutes()),2,"0");
    return dia+"/"+mes+"/"+anio+" "+hora+":"+minutos;
  }

  function lpad(ContentToSize,PadLength,PadChar){
    var PaddedString=ContentToSize.toString();
    for(var i=ContentToSize.length+1;i<=PadLength;i++) {
      PaddedString=PadChar+PaddedString;
    }
    return PaddedString;
  }

  function getWeekNumber(d) {
    // Copy date so don't modify original
    d = new Date(d);
    d.setHours(0,0,0);
    // Set to nearest Thursday: current date + 4 - current day number
    // Make Sunday's day number 7
    d.setDate(d.getDate() + 4 - (d.getDay()||7));
    // Get first day of year
    var yearStart = new Date(d.getFullYear(),0,1);
    // Calculate full weeks to nearest Thursday
    var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
    // Return array of year and week number
    return weekNo;
  }

Number.prototype.format = function(redondeo){
  redondeo = (typeof redondeo == "undefined") ? 2 : redondeo;
  var negativo = "";
  var number = new String(this.toFixed(redondeo));
  // Si es negativo
  if (number.indexOf("-") != -1) {
    var negativo = "-";
    number = number.substring(1);
  }
  // Si tiene punto decimal
  var pos_punto = number.indexOf(".");
  if (pos_punto != -1) {

    // Parte decimal
    var parte_decimal = number.substring(pos_punto+1);
    var decimal = parseFloat("0."+parte_decimal);
    parte_decimal = decimal.toFixed(redondeo).substring(redondeo);
    
    // Parte entera
    number = number.substring(0,pos_punto);
    
  } else {
    var parte_decimal = "00";
  }
  var result = '';
  while( number.length > 3 ) {
    result = '.' + number.substr(number.length - 3) + result;
    number = number.substring(0, number.length - 3);
  }
  result = negativo + number + result + ((redondeo > 0) ? ("," + parte_decimal) : "");
  return result;
};

function confirmar(info) {
  var audio = document.getElementById('audio');
  if (audio != null) audio.play();
  return confirm(info);
}


/**********************************************************************************
 *
 *  FUNCIONES DE VALIDACION DE DATOS
 *
 *********************************************************************************/

// Constantes utilizadas en el parametro "type" de validate_input
var IS_EMPTY = 1;
var IS_EMAIL = 2;
var IS_INTEGER = 3;
var IS_DECIMAL = 4;
var NOT_EMPTY_INTEGER = 5;
var NOT_EMPTY_DECIMAL = 6;
var NOT_EMPTY_INTEGER_OR_DECIMAL = 7;
var IS_NUMBER = 8;

/**
 * Esta funcion encapsula la validacion de los campos
 * @param id String que contiene el ID del input que se desea validar
 * @param const Constante que indica que tipo de validacion se quiere hacer
 * @param info String que contiene la informacion a mostrar en el error
 */
function validate_input(id,type,info) {

  var valor = $("#"+id).val();
  if (type==IS_EMPTY) {
  // CONTROLAMOS QUE NO SEA VACIO
  if (isEmpty(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == IS_EMAIL) {
  // CONTROLAMOS QUE SEA UN EMAIL
  if (!validateEmail(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == IS_INTEGER) {
  // CONTROLAMOS QUE SEA UN ENTERO
  if (!isInteger(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);        
  } else if (type == IS_DECIMAL) {
  // CONTROLAMOS QUE SEA UN DECIMAL
  if (!isDecimal(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == NOT_EMPTY_INTEGER) {
  // CONTROLAMOS QUE NO SEA VACIO Y SEA ENTERO
  if (isEmpty(valor) || !isInteger(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == NOT_EMPTY_DECIMAL) {
  // CONTROLAMOS QUE NO SEA VACIO Y SEA DECIMAL
  if (isEmpty(valor) || !isDecimal(valor)) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == NOT_EMPTY_INTEGER_OR_DECIMAL) {
  // CONTROLAMOS QUE NO SEA VACIO Y SEA ENTERO O DECIMAL
  if (isEmpty(valor) || !(isInteger(valor) || isDecimal(valor))) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  } else if (type == IS_NUMBER) {
  // CONTROLAMOS QUE SEA ENTERO O DECIMAL
  if (!(isInteger(valor) || isDecimal(valor))) {
    alert(info);
    $("#"+id).focus();
    $("#"+id).addClass("error");
    $("#"+id).parent().find(".icono_requerido").addClass("error");
    throw new Error(id);
  }
  return escape(valor);
  }
  throw new Error(id);
  }

function isDecimal(s)  {
 if (isEmpty(s)) return true;
 var reg = new RegExp("^[-]?[0-9]+[\.|,]?[0-9]+$");
 return reg.test(s);
}

function isTelephone(s) {
  s = s.trim();
  if (isEmpty(s)) return false;
  s = s.replace(/\D/g,'');
  // El largo del telefono tiene que ser de 10 caracteres
  if (s.length != 10) return false;
  return true;
}

function isInteger(s) {
 if (isEmpty(s)) return true;
 var reg = new RegExp("^[-]?[0-9]+$");
 return reg.test(s);
}

/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.2 Copyright (C) Paul Johnston 1999 - 2009
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */
 var hexcase=0;function hex_md5(a){return rstr2hex(rstr_md5(str2rstr_utf8(a)))}function hex_hmac_md5(a,b){return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a),str2rstr_utf8(b)))}function md5_vm_test(){return hex_md5("abc").toLowerCase()=="900150983cd24fb0d6963f7d28e17f72"}function rstr_md5(a){return binl2rstr(binl_md5(rstr2binl(a),a.length*8))}function rstr_hmac_md5(c,f){var e=rstr2binl(c);if(e.length>16){e=binl_md5(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binl_md5(a.concat(rstr2binl(f)),512+f.length*8);return binl2rstr(binl_md5(d.concat(g),512+128))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binl(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(c%32)}return a}function binl2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(c%32))&255)}return a}function binl_md5(p,k){p[k>>5]|=128<<((k)%32);p[(((k+64)>>>9)<<4)+14]=k;var o=1732584193;var n=-271733879;var m=-1732584194;var l=271733878;for(var g=0;g<p.length;g+=16){var j=o;var h=n;var f=m;var e=l;o=md5_ff(o,n,m,l,p[g+0],7,-680876936);l=md5_ff(l,o,n,m,p[g+1],12,-389564586);m=md5_ff(m,l,o,n,p[g+2],17,606105819);n=md5_ff(n,m,l,o,p[g+3],22,-1044525330);o=md5_ff(o,n,m,l,p[g+4],7,-176418897);l=md5_ff(l,o,n,m,p[g+5],12,1200080426);m=md5_ff(m,l,o,n,p[g+6],17,-1473231341);n=md5_ff(n,m,l,o,p[g+7],22,-45705983);o=md5_ff(o,n,m,l,p[g+8],7,1770035416);l=md5_ff(l,o,n,m,p[g+9],12,-1958414417);m=md5_ff(m,l,o,n,p[g+10],17,-42063);n=md5_ff(n,m,l,o,p[g+11],22,-1990404162);o=md5_ff(o,n,m,l,p[g+12],7,1804603682);l=md5_ff(l,o,n,m,p[g+13],12,-40341101);m=md5_ff(m,l,o,n,p[g+14],17,-1502002290);n=md5_ff(n,m,l,o,p[g+15],22,1236535329);o=md5_gg(o,n,m,l,p[g+1],5,-165796510);l=md5_gg(l,o,n,m,p[g+6],9,-1069501632);m=md5_gg(m,l,o,n,p[g+11],14,643717713);n=md5_gg(n,m,l,o,p[g+0],20,-373897302);o=md5_gg(o,n,m,l,p[g+5],5,-701558691);l=md5_gg(l,o,n,m,p[g+10],9,38016083);m=md5_gg(m,l,o,n,p[g+15],14,-660478335);n=md5_gg(n,m,l,o,p[g+4],20,-405537848);o=md5_gg(o,n,m,l,p[g+9],5,568446438);l=md5_gg(l,o,n,m,p[g+14],9,-1019803690);m=md5_gg(m,l,o,n,p[g+3],14,-187363961);n=md5_gg(n,m,l,o,p[g+8],20,1163531501);o=md5_gg(o,n,m,l,p[g+13],5,-1444681467);l=md5_gg(l,o,n,m,p[g+2],9,-51403784);m=md5_gg(m,l,o,n,p[g+7],14,1735328473);n=md5_gg(n,m,l,o,p[g+12],20,-1926607734);o=md5_hh(o,n,m,l,p[g+5],4,-378558);l=md5_hh(l,o,n,m,p[g+8],11,-2022574463);m=md5_hh(m,l,o,n,p[g+11],16,1839030562);n=md5_hh(n,m,l,o,p[g+14],23,-35309556);o=md5_hh(o,n,m,l,p[g+1],4,-1530992060);l=md5_hh(l,o,n,m,p[g+4],11,1272893353);m=md5_hh(m,l,o,n,p[g+7],16,-155497632);n=md5_hh(n,m,l,o,p[g+10],23,-1094730640);o=md5_hh(o,n,m,l,p[g+13],4,681279174);l=md5_hh(l,o,n,m,p[g+0],11,-358537222);m=md5_hh(m,l,o,n,p[g+3],16,-722521979);n=md5_hh(n,m,l,o,p[g+6],23,76029189);o=md5_hh(o,n,m,l,p[g+9],4,-640364487);l=md5_hh(l,o,n,m,p[g+12],11,-421815835);m=md5_hh(m,l,o,n,p[g+15],16,530742520);n=md5_hh(n,m,l,o,p[g+2],23,-995338651);o=md5_ii(o,n,m,l,p[g+0],6,-198630844);l=md5_ii(l,o,n,m,p[g+7],10,1126891415);m=md5_ii(m,l,o,n,p[g+14],15,-1416354905);n=md5_ii(n,m,l,o,p[g+5],21,-57434055);o=md5_ii(o,n,m,l,p[g+12],6,1700485571);l=md5_ii(l,o,n,m,p[g+3],10,-1894986606);m=md5_ii(m,l,o,n,p[g+10],15,-1051523);n=md5_ii(n,m,l,o,p[g+1],21,-2054922799);o=md5_ii(o,n,m,l,p[g+8],6,1873313359);l=md5_ii(l,o,n,m,p[g+15],10,-30611744);m=md5_ii(m,l,o,n,p[g+6],15,-1560198380);n=md5_ii(n,m,l,o,p[g+13],21,1309151649);o=md5_ii(o,n,m,l,p[g+4],6,-145523070);l=md5_ii(l,o,n,m,p[g+11],10,-1120210379);m=md5_ii(m,l,o,n,p[g+2],15,718787259);n=md5_ii(n,m,l,o,p[g+9],21,-343485551);o=safe_add(o,j);n=safe_add(n,h);m=safe_add(m,f);l=safe_add(l,e)}return Array(o,n,m,l)}function md5_cmn(h,e,d,c,g,f){return safe_add(bit_rol(safe_add(safe_add(e,h),safe_add(c,f)),g),d)}function md5_ff(g,f,k,j,e,i,h){return md5_cmn((f&k)|((~f)&j),g,f,e,i,h)}function md5_gg(g,f,k,j,e,i,h){return md5_cmn((f&j)|(k&(~j)),g,f,e,i,h)}function md5_hh(g,f,k,j,e,i,h){return md5_cmn(f^k^j,g,f,e,i,h)}function md5_ii(g,f,k,j,e,i,h){return md5_cmn(k^(f|(~j)),g,f,e,i,h)}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};