<?php
header('Content-Type: text/html; charset=UTF-8');
if ( extension_loaded( 'zlib' ) ) { ob_start(); }
ob_start('slib_compress_html');
function lang($languages=array()) {
  $l = (isset($_SESSION["lang"]) ? $_SESSION["lang"] : "es");
  return isset($languages[$l]) ? $languages[$l] : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="resources/images/favicon.ico" type="image/x-icon">
<link rel="icon" href="resources/images/favicon.ico" type="image/x-icon">
<title><?php echo (isset($empresa)) ? (((isset($volver_superadmin) && $volver_superadmin == 1) ? $empresa->id." - " : "").$empresa->nombre) : ""; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<base href="<?php echo URL_BASE ?>/sistema/"/>
<?php if(!empty($css_files)) { ?>
  <?php foreach($css_files as $file) { ?>
    <link rel="stylesheet" href="<?php echo $file ?>"/>
  <?php } ?>
<?php } else { ?>
  <link rel="stylesheet" href="resources/css/min.css"/>
<?php } ?>
<link rel="stylesheet" href="resources/fonts/lato/lato.css" type="text/css" />
<link rel="stylesheet" href="resources/css/font-awesome.min.css" type="text/css" />
<style type="text/css">
.page_after_load { width: 100%; height: calc(100% - 2px); }
</style>  
</head>
<body>
<div id="pageloader" class="white-bg">
  <div class="outter dark-border">
      <div class="mid dark-border"></div>
  </div>
</div>
<script type="text/javascript">
var inicio = '<?php echo $inicio; ?>';
const PERFIL = '<?php echo $perfil; ?>';
const IDIOMA = '<?php echo $idioma; ?>';
const ID_USUARIO = "<?php echo $id_usuario; ?>";
const ID_SUCURSAL = "<?php echo $id_sucursal; ?>";
const ID_VENDEDOR = "<?php echo $id_vendedor; ?>";
const NOMBRE_USUARIO = "<?php echo $nombre_usuario; ?>";
const PATH_USUARIO = "<?php echo $path_usuario; ?>";
const EMAIL_USUARIO = "<?php echo $email; ?>";
const SOLO_USUARIO = "<?php echo $solo_usuario; // Solo ve lo que creo el propio usuario ?>"; 
const CLAVE_ESPECIAL = "<?php echo $clave_especial; // Usado en TOQUE para acceder al dashboard del comercio ?>"; 
const USUARIO_PPAL = "<?php echo $usuario_ppal; // Indica si es el usuario principal de la cuenta ?>"; 
const DESTACADO = "<?php echo $destacado; ?>"; 
const ESTADO = <?php echo $estado; ?>;
const LOCAL = "<?php echo $local; ?>";
const OCULTAR_NOTIFICACIONES = "<?php echo $ocultar_notificaciones; ?>";
const CACHE_ARTICULOS = "<?php echo $cache_articulos; ?>";
const MILLING = <?php echo $milling ?>;
const TOQUE = <?php echo $toque ?>;
const DISTRIBUIDORA = "<?php echo ($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) ? 1:0 ?>";
const MEGASHOP = <?php echo $megashop ?>;
const VOLVER_SUPERADMIN = "<?php echo $volver_superadmin; ?>";
const SINDI_VALOR_CONSULTA = "<?php echo $sindi_valor_consulta ?>";
const USUARIO_HORA_DESDE = "<?php echo $usuario_hora_desde ?>";
const ID_EMPRESA_FACTURACION = "<?php echo $id_empresa_facturacion ?>";
const MENSAJE_CUENTA_NIVEL = "<?php echo $mensaje_cuenta_nivel ?>";
const FORMA_ENVIO = "<?php echo $forma_envio ?>";
const POSICION_PROMEDIO = "<?php echo $posicion_promedio ?>";

var tabla_articulos = <?php echo json_encode($tabla_articulos) ?>;
var tabla_ventas = <?php echo json_encode($tabla_ventas) ?>;
var tabla_compras = <?php echo json_encode($tabla_compras) ?>;

<?php
// Dominio por defecto
if(!empty($empresa->dominios)) {
  $dominio = $empresa->dominios[0]; 
  if (substr($dominio, -1) != "/") $dominio.="/";
} else {
  $dominio = ($empresa->id_web_template != 0 && isset($empresa->dominio_varcreative)) ? $empresa->dominio_varcreative : "";
}
echo "const DOMINIO = '$dominio';";

// Coordenadas por defecto
if (isset($empresa->config["posiciones"]) && !empty($empresa->config["posiciones"])) {
  // Tomamos la primera posicion seteada en la configuracion web
  $pos = explode("/",$empresa->config["posiciones"]);
  $coord = explode(";",$pos[0]);
  if (sizeof($coord)==2) {
    echo "const LATITUD = '".$coord[0]."';";
    echo "const LONGITUD = '".$coord[1]."';";
  } else {
    echo "const LATITUD = '-34.6156625';";
    echo "const LONGITUD = '-58.5033598';";    
  }
} else {
  echo "const LATITUD = '-34.6156625';";
  echo "const LONGITUD = '-58.5033598';";    
}?>

const TIPO_ADJUNTO_ARTICULO = 1;
const TIPO_ADJUNTO_COMPROBANTE = 2;
const TIPO_ADJUNTO_PROPIEDAD = 3;

<?php
// Pasamos todos los atributos del objeto $empresa como constantes de javascript
foreach($empresa as $key => $value) {
  if (!is_array($value)) {
    $value = nl2br($value);
    $value = str_replace("\n","",$value);
    echo "const ".strtoupper($key)." = '$value';\r\n";
  }
  else if ($key == "config") {
    foreach($value as $kkey => $v) {
      $v = nl2br($v);
      $v = str_replace("\n","",$v);
      if (!empty($kkey)) echo "const ".strtoupper($kkey)." = '$v';\r\n";
    }
  }
}

// TODO: Hacer estas constantes dinamicas
if (!isset($empresa->config["facturacion_modificar_iva"])) { ?>
const FACTURACION_MODIFICAR_IVA = ((ID_EMPRESA == 135 || ID_EMPRESA == 574) ? 0 : 1);
<?php } ?>
const API_KEY_GOOGLE_MAPS = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "AIzaSyA6LjgwtjyzmtvXiwaj6dhuPXKTBEuNQNk" : "AIzaSyAXpROdHVy8YYxLeemEyR1hVDCRTL_4UdE";
</script>
<?php
include_once "application/views/templates/blocks/single_upload.php";

// Cargamos los templates de acuerdo al proyecto
if ($empresa->id_proyecto > 0) {
  foreach($permisos as $p) {
    if ($p->id_modulo != 0 && file_exists("application/views/templates/".((!empty($p->dir))?$p->dir."/":"")."$p->nombre.php"))
      include_once "application/views/templates/".((!empty($p->dir))?$p->dir."/":"")."$p->nombre.php";
  }
} else {
  
  // Superadmin o vendedores
  if (file_exists("application/views/templates/bancos.php")) include_once ("application/views/templates/bancos.php");
  if (file_exists("application/views/templates/planes.php")) include_once ("application/views/templates/planes.php");
  if (file_exists("application/views/templates/degenerator.php")) include_once ("application/views/templates/degenerator.php");
  if (file_exists("application/views/templates/proyectos.php")) include_once ("application/views/templates/proyectos.php");
  if (file_exists("application/views/templates/monedas.php")) include_once ("application/views/templates/monedas.php");
  if (file_exists("application/views/templates/inm/tipos_inmueble.php")) include_once ("application/views/templates/inm/tipos_inmueble.php");
  if (file_exists("application/views/templates/inm/tipos_operacion.php")) include_once ("application/views/templates/inm/tipos_operacion.php");
  if (file_exists("application/views/templates/inm/tipos_estado.php")) include_once ("application/views/templates/inm/tipos_estado.php");
  if (file_exists("application/views/templates/clasif/tipos_vehiculos.php")) include_once ("application/views/templates/clasif/tipos_vehiculos.php");
  if (file_exists("application/views/templates/ped/tipos_estado_pedidos.php")) include_once ("application/views/templates/ped/tipos_estado_pedidos.php");
  if (file_exists("application/views/templates/not/publicidades_tipos.php")) include_once ("application/views/templates/not/publicidades_tipos.php");
  if (file_exists("application/views/templates/not/publicidades_categorias.php")) include_once ("application/views/templates/not/publicidades_categorias.php");
  if (file_exists("application/views/templates/versiones_db.php")) include_once ("application/views/templates/versiones_db.php");
  if (file_exists("application/views/templates/web/web_templates.php")) include_once ("application/views/templates/web/web_templates.php");
  if (file_exists("application/views/templates/web/web_textos.php")) include_once ("application/views/templates/web/web_textos.php");
  if (file_exists("application/views/templates/config/videos.php")) include_once ("application/views/templates/config/videos.php");
  if (file_exists("application/views/templates/config/chat_preguntas.php")) include_once ("application/views/templates/config/chat_preguntas.php");
}

// Estos modulos se cargan siempre
if (file_exists("application/views/templates/blocks/image_editor.php")) include_once ("application/views/templates/blocks/image_editor.php");
if (file_exists("application/views/templates/blocks/image_gallery.php")) include_once ("application/views/templates/blocks/image_gallery.php");
if (file_exists("application/views/templates/blocks/image_upload.php")) include_once ("application/views/templates/blocks/image_upload.php");
if (file_exists("application/views/templates/empresas.php")) include_once ("application/views/templates/empresas.php");
if (file_exists("application/views/templates/importacion.php")) include_once ("application/views/templates/importacion.php");
if (file_exists("application/views/templates/monedas.php")) include_once ("application/views/templates/monedas.php");
if (file_exists("application/views/templates/usuarios.php")) include_once ("application/views/templates/usuarios.php");
if (file_exists("application/views/templates/perfiles.php")) include_once ("application/views/templates/perfiles.php");
if (file_exists("application/views/templates/basic_search.php")) include_once ("application/views/templates/basic_search.php");
if (file_exists("application/views/templates/datepicker.php")) include_once ("application/views/templates/datepicker.php");
if (file_exists("application/views/templates/importar.php")) include_once ("application/views/templates/importar.php");
if (file_exists("application/views/templates/inicio.php")) include_once ("application/views/templates/inicio.php");
if (file_exists("application/views/templates/pagination.php")) include_once ("application/views/templates/pagination.php");
if (file_exists("application/views/templates/supervisor.php")) include_once ("application/views/templates/supervisor.php");
if (file_exists("application/views/templates/ayuda.php")) include_once ("application/views/templates/ayuda.php");
if (file_exists("application/views/templates/tipos_comprobante.php")) include_once ("application/views/templates/tipos_comprobante.php");
if (file_exists("application/views/templates/wait.php")) include_once ("application/views/templates/wait.php");
if (file_exists("application/views/templates/bancos.php")) include_once ("application/views/templates/bancos.php");
if (file_exists("application/views/templates/tarjetas.php")) include_once ("application/views/templates/tarjetas.php");
if (file_exists("application/views/templates/localidades.php")) include_once ("application/views/templates/localidades.php");
if (file_exists("application/views/templates/provincias.php")) include_once ("application/views/templates/provincias.php");
if (file_exists("application/views/templates/crm/emails_templates.php")) include_once ("application/views/templates/crm/emails_templates.php");
if (file_exists("application/views/templates/crm/consultas.php")) include_once ("application/views/templates/crm/consultas.php");
if (file_exists("application/views/templates/crm/eventos.php")) include_once ("application/views/templates/crm/eventos.php");
if (file_exists("application/views/templates/crm/tareas.php")) include_once ("application/views/templates/crm/tareas.php");
if (file_exists("application/views/templates/crm/origenes.php")) include_once ("application/views/templates/crm/origenes.php");
if (file_exists("application/views/templates/not/entradas.php")) include_once ("application/views/templates/not/entradas.php");
if (file_exists("application/views/templates/clientes.php")) include_once ("application/views/templates/clientes.php");
if (file_exists("application/views/templates/crm/contactos.php")) include_once ("application/views/templates/crm/contactos.php");
if (file_exists("application/views/templates/web/web_textos.php")) include_once ("application/views/templates/web/web_textos.php");
if (file_exists("application/views/templates/gal/galerias_imagenes.php")) include_once ("application/views/templates/gal/galerias_imagenes.php");
if (file_exists("application/views/templates/gal/galerias_etiquetas.php")) include_once ("application/views/templates/gal/galerias_etiquetas.php");
if (file_exists("application/views/templates/gal/galerias_categorias.php")) include_once ("application/views/templates/gal/galerias_categorias.php");
if (file_exists("application/views/templates/facturacion.php")) include_once ("application/views/templates/facturacion.php");
if (file_exists("application/views/templates/web/web_configuracion.php")) include_once ("application/views/templates/web/web_configuracion.php");
if (file_exists("application/views/templates/recibos_clientes.php")) include_once ("application/views/templates/recibos_clientes.php");
if (file_exists("application/views/templates/cajas.php")) include_once ("application/views/templates/cajas.php");
if (file_exists("application/views/templates/cajas_movimientos.php")) include_once ("application/views/templates/cajas_movimientos.php");
if (file_exists("application/views/templates/gastos.php")) include_once ("application/views/templates/gastos.php");
if (file_exists("application/views/templates/tipos_gastos.php")) include_once ("application/views/templates/tipos_gastos.php");
if (file_exists("application/views/templates/config/mi_cuenta.php")) include_once ("application/views/templates/config/mi_cuenta.php");
if (file_exists("application/views/templates/config/categorias_entrena.php")) include_once ("application/views/templates/config/categorias_entrena.php");
if (file_exists("application/views/templates/select_option.php")) include_once ("application/views/templates/select_option.php");

if ($empresa->id_proyecto == 3) {
  if (file_exists("application/views/templates/inm/dashboard.php")) include_once ("application/views/templates/inm/dashboard.php");
  if (file_exists("application/views/templates/inm/tipos_inmueble.php")) include_once ("application/views/templates/inm/tipos_inmueble.php");
  if (file_exists("application/views/templates/inm/tipos_operacion.php")) include_once ("application/views/templates/inm/tipos_operacion.php");
  if (file_exists("application/views/templates/inm/tipos_estado.php")) include_once ("application/views/templates/inm/tipos_estado.php");
} else if ($empresa->id_proyecto == 5) {
  if (file_exists("application/views/templates/aca/asistencias.php")) include_once ("application/views/templates/aca/asistencias.php");
  if (file_exists("application/views/templates/aca/asistencias_docentes.php")) include_once ("application/views/templates/aca/asistencias_docentes.php");
  if (file_exists("application/views/templates/aca/examenes.php")) include_once ("application/views/templates/aca/examenes.php");
} else if ($empresa->id_proyecto == 11) {
  if (file_exists("application/views/templates/via/dashboard.php")) include_once ("application/views/templates/via/dashboard.php");
} else if ($empresa->id == 856) {
  if (file_exists("application/views/templates/carp/carp_agencias.php")) include_once ("application/views/templates/carp/carp_agencias.php");
  if (file_exists("application/views/templates/carp/carp_propietarios.php")) include_once ("application/views/templates/carp/carp_propietarios.php");
  if (file_exists("application/views/templates/carp/carp_choferes.php")) include_once ("application/views/templates/carp/carp_choferes.php");
}
?>
<!-- Modulo de Permisos -->
<script type="text/javascript">

// Tipos de permisos por modulo
const NO_PERMITIDO = 0;
const PERMISO_LECTURA = 1;
const PERMISO_MODIFICACION = 2;
const PERMISO_CONFIGURACION = 3;

var ControlPermiso = function() {
  <?php 
  // Imprimimos el array de permisos que tenemos guardado en la session de usuario
  echo "var permisos = ".json_encode($permisos).";"; 
  ?>  

  this.get = function(nombre_modulo) {
    for(var i=0;i<permisos.length;i++) {
      var p = permisos[i];
      if (p.nombre == nombre_modulo) return p;
    }
    return {
      "clase":"",
      "title":"",
    };
  };

  // Chequeamos si el estado de los permisos de un determinado modulo
  this.check = function (nombre_modulo) {
    var i = 0;
    // Recorremos los permisos que tiene el usuario
    for(i=0;i<permisos.length;i++) {
      var p = permisos[i];
      // Encontramos el permiso para ese modulo
      if (p.nombre == nombre_modulo) {
        var perm = parseInt(p.permiso);
        switch(perm) {
          case 1: return PERMISO_LECTURA; break;
          case 2: return PERMISO_MODIFICACION; break;
          case 3: return PERMISO_CONFIGURACION; break;
          default: return NO_PERMITIDO; break;
        }
      }
    }
    return NO_PERMITIDO;
  };
}
var control = new ControlPermiso();
</script>

<?php if(!empty($js_files)) {
  $rand = "?p=".$version_js;
  foreach($js_files as $file) {
    echo "<script type='text/javascript' src='".$file.$rand."'></script>\n";
  }  
  foreach($permisos as $p) {
    if ($p->id_modulo != 0 && file_exists("application/views/templates/".((!empty($p->dir))?$p->dir."/":"").$p->nombre.".php"))
      echo "<script type='text/javascript' src='application/javascript/modules/".((!empty($p->dir))?$p->dir."/":"").$p->nombre.".js".$rand."'></script>\n";
  }

  if ($empresa->id == 856) { ?>
    <script type="text/javascript" src="application/javascript/modules/carp/carp_agencias.js"></script>
    <script type="text/javascript" src="application/javascript/modules/carp/carp_propietarios.js"></script>
    <script type="text/javascript" src="application/javascript/modules/carp/carp_choferes.js"></script>
  <?php } ?>
  
<?php } else { ?>
  <script type="text/javascript" src="application/javascript/min_<?php echo $empresa->id_proyecto ?>.js?v=<?php echo $version_js ?>"></script>
<?php } ?>

<?php /* <script type="text/javascript" src="resources/js/common.js"></script> */ ?>
<script type="text/javascript" src="resources/js/libs/ckeditor_4.6/ckeditor.js"></script>

<script type="text/javascript">
var ajax_request = 0;
function waitingMsg() {
  if (ajax_request > 0) $("#waitingMsg").show();
  else $("#waitingMsg").hide();
}
$(document).ready(function(){

  if (MEGASHOP == 1 || ID_EMPRESA == 421) {
    if (typeof alicuotas_iva == "undefined" || alicuotas_iva.length == 0) {
      alert("Error en las alicuotas de IVA. Por favor comuniquese con el soporte.");
    }
  }

  <?php if($empresa->configuracion_menu_iconos == 0) { ?>
    $(".app").addClass("sin-iconos");
  <?php } ?>
  
  // Sacamos el cargados
  $(window).load(function(){
    $("#pageloader").hide();
    $(window).trigger("resize");
  });
  
  $(window).resize(function(){
    var w = $(window).width();
    if (w>=768 && w<1300) {
      $(".app-header-fixed").addClass("app-aside-folded");
    }
    /*
    } else if (location.hash == "#web_configuracion") {
      $(".app-header-fixed").addClass("app-aside-folded");
    } else if (location.hash == "#facturacion" && FACTURACION_TIPO == "pv") {
      $(".app-header-fixed").addClass("app-aside-folded");
    } else {
      $(".app-header-fixed").removeClass("app-aside-folded");
    }*/
  });

    $(".nav li a").click(function(e){
        var li = $(e.currentTarget).parent();
      // Si la barra esta comprimida
      if (!$(".app-aside-fixed").hasClass("app-aside-folded")) {
        if ($(li).find(".nav").length > 0) {
          $(".nav li").removeClass("active");
          $(li).addClass("active");
        }      
      }
    });

  // HANDLERS GLOBALES
  $(document).ajaxSend(function(){
    ajax_request++; waitingMsg();
  });
  $(document).ajaxSuccess(function(){
    if (ajax_request > 0) ajax_request--; waitingMsg();
  });
  $(document).ajaxError(function(event,request,settings,thrownError){
    show(thrownError);
    if (ajax_request > 0) ajax_request--; waitingMsg();
  });
  
  
  // Pymvar o Shopvar, se buscan articulos
  if (ID_PROYECTO == 1 || ID_PROYECTO == 2) {

    // TODO: Hacer configurable por cual campo se desea buscar en la barra
    if (ID_EMPRESA == 228) {
      // BUSCAMOS POR CLIENTES
      var input = $("#buscar_general");
      $(input).customcomplete({
        "url":"/sistema/pres_clientes/function/get_by_descripcion/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "offsetTop":15,
        "closable":false,
        "disableNumber":false,
        "info":"descripcion",
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          location.href="app/#pres_cliente_acciones/"+item.value;
          $("#buscar_general").val(item.label);
        }
      });
    } else {
      // BUSCAMOS POR ARTICULOS
      var input = $("#buscar_general");
      $(input).customcomplete({
        "url":"/sistema/articulos/function/get_by_descripcion/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "closable":false,
        "offsetTop":15,
        "info":"descripcion",
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          location.href="app/#articulo/"+item.value;
          $("#buscar_general").val(item.label);
        }
      });
    }
    
    
  // En Inmovar, se buscan propiedades
  } else if (ID_PROYECTO == 3) {
    
    // AUTOCOMPLETE DE PROPIEDADES
    // -------------------------
    var input = $("#buscar_general");
    var form = new app.views.ClienteEditViewMini({
      "model": new app.models.Cliente(),
      "input": input,
      "tipo_formulario": "contacto",
      "onSave": function(c) {
        console.log(c);
      },
    });      
    $(input).customcomplete({
      "url":"clientes/function/get_by_nombre/",
      "form":form,
      "info":"email",
      "width":"300px",
      "onSelect":function(item){
        var cliente = new app.models.Cliente({"id":item.id});
        cliente.fetch({
          "success":function(){
            location.href="app/#contacto_acciones/"+item.value;
            $("#buscar_general").val(item.label);
          },
        });
      }
    });
    
  // En Newsvar, se buscan noticias
  } else if (ID_PROYECTO == 4) {
    
    // AUTOCOMPLETE DE ENTRADAS
    // -------------------------
    var input = $("#buscar_general");
    $(input).customcomplete({
      "url":"/sistema/entradas/function/ver/",
      "form":null, // No quiero que se creen nuevos productos
      "label":"titulo",
      "info":"subtitulo",
      "closable":false,
      "width":400,
      "offsetTop":15,
      "image_field":"path",
      "image_path":"/sistema",
      "onSelect":function(item){
        location.href="app/#entrada/"+item.value;
        $("#buscar_general").val(item.label);
      }
    });
    
  // En Superadmin, se buscan empresas
  } else if (PERFIL == -1 && ID_USUARIO == 0) {
    var input = $("#buscar_general");
    $(input).customcomplete({
      "url":"/sistema/empresas/function/get_by_descripcion/",
      "form":null, // No quiero que se creen nuevos productos
      "width":400,
      "disableNumber":false,
      "offsetTop":15,
      "onSelect":function(item){
        location.href="app/#empresa/"+item.value;
        $("#buscar_general").val(item.label);
      }
    });
  }
    
});

/**
 * Muestra en un panel de dialogo el texto pasado por parametro
 */
function show(info) {
    //if (CONFIGURACION_SONIDO == 1) document.getElementById('audio').play();
    alert(info);
}

var comprobantes = <?php echo json_encode($comprobantes); ?>;
var almacenes = <?php echo json_encode($almacenes); ?>;
var alicuotas_iva = <?php echo json_encode($alicuotas_iva); ?>;
var monedas = <?php echo json_encode($monedas); ?>;
var tipos_inmueble = <?php echo json_encode($tipos_inmueble); ?>;
var tipos_operacion = <?php echo json_encode($tipos_operacion); ?>;
var tipos_estado = <?php echo json_encode($tipos_estado); ?>;
var tipos_estado_pedidos = <?php echo json_encode($tipos_estado_pedidos); ?>;
var tipos_publicidades = <?php echo json_encode($tipos_publicidades); ?>;
var tipos_vehiculo = <?php echo json_encode($tipos_vehiculos); ?>;
var categorias_publicidades = <?php echo json_encode($categorias_publicidades); ?>;
var categorias_noticias = <?php echo json_encode($categorias_noticias); ?>;
var categorias_clasificados = <?php echo json_encode($categorias_clasificados); ?>;
var categorias_viajes = <?php echo json_encode($categorias_viajes); ?>;
var categorias_opcionales = <?php echo json_encode($categorias_opcionales); ?>;
var origenes = <?php echo json_encode($origenes); ?>;
var bancos = <?php echo json_encode($bancos); ?>;
var vendedores = <?php echo json_encode($vendedores); ?>;
var planes = <?php echo json_encode($planes); ?>;
var cuentas_bancarias = <?php echo json_encode($cuentas_bancarias); ?>;
var idiomas = <?php echo json_encode($idiomas); ?>;
var rubros = <?php echo json_encode($rubros); ?>;
var rubros_lpc = <?php echo json_encode($rubros_lpc); ?>;
var tipos_gastos = <?php echo json_encode($tipos_gastos); ?>;
var conceptos_gastos = <?php echo json_encode($conceptos_gastos); ?>;
var conceptos_compras = <?php echo json_encode($conceptos_compras); ?>;
var conceptos_ventas = <?php echo json_encode($conceptos_ventas); ?>;
var salones = <?php echo json_encode($salones); ?>;
var tipos_tarifas = <?php echo json_encode($tipos_tarifas); ?>;
var puntos_venta = <?php echo json_encode($puntos_venta); ?>;
var departamentos_comerciales = <?php echo json_encode($departamentos_comerciales); ?>;
var comisiones = <?php echo json_encode($comisiones); ?>;
var trimestres = <?php echo json_encode($trimestres); ?>;
var hoteles = <?php echo json_encode($hoteles); ?>;
var profesionales = <?php echo json_encode($profesionales); ?>;
var turnos_servicios = <?php echo json_encode($turnos_servicios); ?>;
var modulos = <?php echo json_encode($modulos); ?>;
var emails_templates = <?php echo json_encode($emails_templates); ?>;
var articulos_propiedades = <?php echo json_encode($articulos_propiedades); ?>;
var articulos_etiquetas = <?php echo json_encode($articulos_etiquetas); ?>;
var sectores = <?php echo json_encode($sectores); ?>;
var tipos_mantenimiento = <?php echo json_encode($tipos_mantenimiento); ?>;
var tipos_ordenes_trabajo = <?php echo json_encode($tipos_ordenes_trabajo); ?>;
var tarjetas = <?php echo json_encode($tarjetas); ?>;
var pres_documentaciones = <?php echo json_encode($pres_documentaciones); ?>;
var asuntos = <?php echo json_encode($asuntos); ?>;
var razones_sociales = <?php echo json_encode($razones_sociales); ?>;
var reglas_ofertas = <?php echo json_encode($reglas_ofertas); ?>;
var localidades = <?php echo json_encode($localidades); ?>;
var sucursales_usuario = <?php echo json_encode($sucursales_usuario); ?>;
var cajas = <?php echo json_encode($cajas); ?>;
var paises = <?php echo json_encode($paises); ?>;
var provincias = <?php echo json_encode($provincias); ?>;
var proyectos = <?php echo json_encode($proyectos); ?>;
var toque_categorias = <?php echo json_encode($toque_categorias); ?>;
var consultas_tipos = <?php echo json_encode($consultas_tipos); ?>;
var categorias_entrena = <?php echo json_encode($categorias_entrena); ?>;

// Tomamos el nombre del controlador fiscal
var controlador_fiscal = "";
var id_punto_venta_default = 0;
for(var pi=0;pi<puntos_venta.length;pi++) {
  var piv = puntos_venta[pi];
  if (piv.imp_fiscal != "") controlador_fiscal = piv.imp_fiscal;
  if (piv.por_default == 1) id_punto_venta_default = piv.id;
}
<?php
// TODO: Si el sistema esta configurado como PUNTO_VENTA, deberia tirar
// un alerta de que el impresor fiscal no esta configurado
?>


// Usuarios del sistema
var usuarios = new app.collections.Usuarios(<?php echo json_encode($usuarios); ?>);
var usuarios_array = <?php echo json_encode($usuarios); ?>;

// PYMVAR, SHOPVAR O RESTOVAR
if (ID_PROYECTO == 1 || ID_PROYECTO == 2 || ID_PROYECTO == 10) {

  if (app.collections.ArticulosMin != undefined) {
    var articulos = new app.collections.ArticulosMin(<?php echo json_encode($articulos) ?>);
    /*
    var desde = Date.now();
    articulos.fetch({
      "success":function() {
        articulos.pager();
        console.log("Tiempo: "+(Date.now() - desde));
      }
    });
    */
  }  

} else if (ID_PROYECTO == 4) {

  if (app.collections.Publicidades != undefined) {
    var publicidades = new app.collections.Publicidades();
    publicidades.fetch();
  }
  
  if (app.collections.Clasificados != undefined) {
    var clasificados = new app.collections.Clasificados();
    clasificados.fetch();
  }
  
  if (app.collections.ClasificadosAutos != undefined) {
    var autos = new app.collections.ClasificadosAutos();
    autos.fetch();
  }
}

// Activamos las notificaciones
<?php if ($empresa->id_proyecto > 0) { ?>


// Estos modulos utilizan NOTIFICACIONES solamente
/*
if (control.check("cocinas")>0) {

  app.collections.notificaciones = new app.collections.Notificaciones();
  app.collections.notificaciones.on("sync",function(){
    var lista = app.collections.notificaciones;
    if (lista.size()>0) {
      lista.each(function(item){
        $.toaster({
          "message":item.get("texto"),
          "title":"Atenci&oacute;n",
          "settings": {
            'timeout':0, // Que no desaparezcan solas
          }
        });
      })
    }
  },this);

  setInterval(function(){
    app.collections.notificaciones.fetch();
  },<?php echo $tiempo_notificaciones * 1000; ?>);

}
*/

var socket;
$(document).ready(function(){
  //if (OCULTAR_NOTIFICACIONES == 0) init();
  if (ID_PROYECTO == 10) init();
});
function init() {
  var host = "ws://<?php echo str_replace("http://","",current_url(TRUE)); ?>:9000/?id_empresa="+ID_EMPRESA;
  try {
    socket = new WebSocket(host);
    console.log('WebSocket - status '+socket.readyState);
    socket.onopen = function(msg) { 
      console.log("Welcome - status "+this.readyState); 
    };
    socket.onmessage = function(msg) { 
      var data = msg.data;
      // Si enviamos un comando especifico para ejecutar
      if (data.indexOf("COMANDO:") != -1) {
        var comando = data.replace("COMANDO:","");
        eval(comando);
      } else {
        // Es un mensaje comun
        $.toaster({
          "message":data,
          "title":"Atenci&oacute;n",
          "settings": {
            'timeout':0, // Que no desaparezcan solas
          }
        });
      }
    };
    socket.onclose = function(msg) { 
      console.log("Disconnected - status "+this.readyState); 
    };
  }
  catch(ex){ 
    console.log(ex); 
  }
}
function quit(){
  if (socket != null) {
    socket.close();
    socket=null;
  }
}

// PUSH NOTIFICATION
const applicationServerPublicKey = 'BG4hDy_0netdNoxxKir3Z6hGS-5HY5EZgRfXbIpsvfWM78Bc-cZzwyW5UqnNAWnSdF8tcYalaBcHRiYaqByWjnA';
let isSubscribed = false;
let swRegistration = null;

if ( ((MEGASHOP == 1 || ID_EMPRESA == 421) && LOCAL == 0) || ((ID_EMPRESA == 571 || ID_EMPRESA == 1275) && PERFIL == 661) ) {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('sw.js')
    .then(function(swReg) {
      swRegistration = swReg;
      initialiseUI();
    })
    .catch(function(error) {
      console.error('Service Worker Error', error);
    });
  } else {
    console.warn('Push messaging is not supported');
  }
}

function initialiseUI() {
  // Set the initial subscription value
  swRegistration.pushManager.getSubscription()
  .then(function(subscription) {
    isSubscribed = !(subscription === null);

    if (isSubscribed) {
      console.log('User IS subscribed.');
    } else {
      console.log('User is NOT subscribed.');
      subscribeUser();
    }
  });
}

function subscribeUser() {
  const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
  swRegistration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: applicationServerKey
  })
  .then(function(subscription) {
    console.log('User is subscribed:', subscription);
    updateSubscriptionOnServer(subscription);
    isSubscribed = true;
  })
  .catch(function(err) {
    console.log('Failed to subscribe the user: ', err);
  });
}

function updateSubscriptionOnServer(subscription) {
  var data = "data="+JSON.stringify(subscription)+"&id_empresa="+ID_EMPRESA;
  if (ID_EMPRESA == 571 || ID_EMPRESA == 1275) data += "&id_usuario="+ID_USUARIO;
  $.ajax({
    "url":"notificaciones/function/guardar/",
    "dataType":"json",
    "type":"post",
    "data":data,
    "success":function() {
      console.log("Se guardo la suscripcion");
    },
    "error":function() {
      console.log("Error al guardar la suscripcion");
    },
  });
}

<?php } // Fin Proyecto > 0 ?>

$(document).ready(function(){
  $(".nav-bar-menu, .navbar-brand").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if ($(window).width() > 768) {
      if ($(".app").hasClass("app-aside-folded")) {
        $(".app").removeClass("app-aside-folded");
      } else {
        $(".app").addClass("app-aside-folded");
      }
    }
    return false;
  });
})

window.onload = function () {  
  document.onkeydown = function (e) {  
    return (e.which || e.keyCode) != 116;  
  };  
}  
</script>
<div class="page_after_load">
    
  <div class="app app-header-fixed <?php echo (isset($empresa->config["tipo_empresa"]) && $empresa->config["tipo_empresa"] == 4) ? 'laplataconstruye' : '' ?> <?php echo ($empresa->id == 444)?"app-aside-folded":"" ?> <?php echo($empresa->configuracion_menu == 1)?"app-aside-fixed":"app-aside-dock" ?>">
    
    <!-- HEADER -->
    <div class="app-header navbar">
      
      <div class="navbar-header bg-black">
        <a href="app/#inicio" class="hidden-xs navbar-brand text-lt">
          <?php if (isset($empresa->config["tipo_empresa"]) && $empresa->config["tipo_empresa"] == 4) { ?>
            <img src="/sistema/resources/images/lpc_logo.png"/>
          <?php } else { ?>
            <img src="/sistema/resources/images/varcreative.jpg"/>
          <?php } ?>
        </a>
        <a href="javascript:void(0)" onclick="workspace.toggle_menu()" class="visible-xs-block navbar-brand text-lt">
          <i style="color: white; padding: 15px;" class="fa fa-bars"></i>
        </a>
      </div>
    
      <div class="collapse navbar-collapse box-shadow bg-white-only">
        
        <div class="nav navbar-nav m-l-sm m-t-sm" style="<?php echo ($empresa->id == 1319)?"display:none":""; ?>">
          <i id="lupa" class="fa fa-search"></i>
          <?php 
          $placeholder = "Buscar...";
          if ($empresa->id == 228) $placeholder = "Buscar cliente...";
          else if ($empresa->id_proyecto == 3) $placeholder = lang(array("es"=>"Buscar o crear contacto...","en"=>"Search or create new contact..."));
          else $placeholder = lang(array("es"=>"Buscar por nombre o código","en"=>"Search"))."...";  ?>
          <input id="buscar_general" type="text" placeholder="<?php echo $placeholder ?>"/>
        </div>

        <ul class="nav navbar-nav navbar-right">     

          <?php if (isset($volver_superadmin) && $volver_superadmin == 1) { ?>
            <li class="dropdown">
              <a href="javascript:void(0)" onclick="volver_superadmin()"><i class="fa fa-user"></i></a>
            </li>
            <script type="text/javascript">
            function volver_superadmin() {
              $.ajax({
              "url":"login/cambiar_empresa/",
              "dataType":"json",
              "success":function(r) {
                 if (r.error == false) window.location = "app/";
              }
              });
            }
            </script>
          <?php } ?>

          <?php 
          // Si es toque, y es el perfil de comercios
          if (($empresa->id == 571 || $empresa->id == 1234 || $empresa->id == 1275) && $perfil == 661) { ?>

            <?php 
            $abierto = FALSE;
            if (isset($horarios)) {
              // Estamos viendo el usuario actual
              foreach($horarios as $h) {
                if ($h->dia == date("N") && $h->desde <= date("H:i:s") && date("H:i:s") <= $h->hasta) {
                  $abierto = TRUE;
                  break;
                }
              }
            } ?>

            <li class="dropdown">
              <a href="app/#mi_usuario" class="btn btn-<?php echo ($abierto)?"success":"danger" ?>"><?php echo ($abierto)?"Abierto":"Fuera de Horario" ?></a>
            </li>            

            <li class="dropdown">
              <select onchange="workspace.setear_demora(this)" class="form-control no-model w200 mt7">
                <option <?php echo ($usuario_hora_desde == "00:00:00")?"selected":"" ?> value="00:00:00">Sin demora</option>
                <option <?php echo ($usuario_hora_desde == "00:15:00")?"selected":"" ?> value="00:15:00">15 min demora</option>
                <option <?php echo ($usuario_hora_desde == "00:30:00")?"selected":"" ?> value="00:30:00">30 min demora</option>
                <option <?php echo ($usuario_hora_desde == "00:45:00")?"selected":"" ?> value="00:45:00">45 min demora</option>
                <option <?php echo ($usuario_hora_desde == "01:00:00")?"selected":"" ?> value="01:00:00">1 h demora</option>
                <option <?php echo ($usuario_hora_desde == "01:30:00")?"selected":"" ?> value="01:30:00">1:30 h demora</option>
                <option <?php echo ($usuario_hora_desde == "02:00:00")?"selected":"" ?> value="02:00:00">2 hs demora</option>
              </select>
            </li>
          <?php } ?>

          <?php // Si es CONSTRUCTORA 
          if (isset($otras_empresas) && !empty($otras_empresas)) { ?>
            <li class="dropdown">
              <select id="app_otras_empresas" onchange="workspace.cambiar_usuario()" class="form-control no-model w200 mt7">
                <?php foreach($otras_empresas as $otra) { ?>
                  <option <?php echo ($otra->id_usuario == $id_usuario && $otra->id_empresa == $empresa->id)?"selected":"" ?> data-id="<?php echo $otra->id_usuario ?>" value="<?php echo $otra->email ?>"><?php 
                    echo $otra->razon_social;
                  ?></option>
                <?php } ?>
              </select>
            </li>
          <?php } ?>


          <?php if (sizeof($sucursales_usuario)>1) { ?>
            <li class="dropdown">
              <select id="app_sucursales" onchange="workspace.cambiar_sucursal()" class="form-control no-model w200 mt7">
                <?php foreach($sucursales_usuario as $suc_us) { ?>
                  <option <?php echo ($suc_us->id_sucursal == $id_sucursal)?"selected":"" ?> value="<?php echo $suc_us->id_sucursal ?>"><?php 
                    $nombre_sucursal = $suc_us->nombre;
                    echo substr($nombre_sucursal, strpos($nombre_sucursal,"-")+1);
                  ?></option>
                <?php } ?>
              </select>
            </li>
          <?php } ?>
          
          <li class="dropdown">
            <a href class="dropdown-toggle clear" style="border-left:solid 1px #d9d9d9;margin-left: 15px;" data-toggle="dropdown">
              <span class="fs14">
                <?php echo lang(array("es"=>"Mi cuenta","en"=>"My account")); ?>
              </span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInRight w menu-perfil">
              <?php
              if (!empty($dominio)) { ?>
                <li>
                  <?php $web = ($perfil == 1357) ? "https://www.entrenaymas.com/".$link_web : "https://www.entrenaymas.com" ?>
                  <a href="<?php echo $web ?>" target="_blank">
                    <?php echo lang(array("es"=>"Ver mi web","en"=>"View Website")); ?>
                  </a>
                </li>
              <?php } ?>
              <li class="divider"></li>
              <li>
                <a href="login/logout/">
                  <?php echo lang(array("es"=>"Salir","en"=>"Exit")); ?>
                </a>
              </li>
            </ul>
          </li>
        </ul>
    
      </div>
    </div>
    <!-- FIN HEADER -->
        
    <div class="app-aside hidden-xs bg-black">
      
      <div class="aside-wrap">
        <div class="navi-wrap">
        
        <div class="nav-bar-menu">
          <?php if (isset($empresa->config["tipo_empresa"]) && $empresa->config["tipo_empresa"] == 4) { ?>
            <img src="/sistema/resources/images/lpc_logo.png"/>
          <?php } else { ?>
            <img src="/sistema/resources/images/varcreative.jpg"/>
          <?php } ?>
        </div>
           
        <div class="clearfix text-center" id="aside-user">
          <div class="dropdown wrapper">
            <a href="app/#inicio">
              <span class="thumb-lg w-auto-folded avatar m-t-sm">
              <?php if (!empty($path_usuario)) { ?>
                <img src="/sistema/<?php echo $path_usuario; ?>" alt="..."/>
              <?php } else if (!empty($empresa->path)) { ?>
                <img src="/sistema/<?php echo $empresa->path; ?>" class="img-full" alt="..."/>
              <?php } else { ?>
                <img src="resources/images/a0.jpg" class="img-full" alt="..."/>
              <?php } ?>
              </span>
            </a>
            <a href class="dropdown-toggle hidden-folded" data-toggle="dropdown">
              <span class="clear">
              <span class="block m-t-sm">
                <div>
                  <strong class="nombre_usuario"><?php echo (isset($nombre_usuario)) ? $nombre_usuario : "Usuario" ?></strong>
                </div>
                <?php if ($empresa->nombre != $nombre_usuario) { ?>
                  <div class="nombre_empresa text-info"><?php echo $empresa->nombre; ?></div>
                <?php } ?>
              </span>
              </span>
            </a>
          </div>
        </div>
      
        <nav class="navi">
          <ul class="nav">
            <?php
            function get_children($lista,$nivel,$params) {
              global $id_usuario;
              $emp = $params["empresa"];
              $categorias_noticias = $params["categorias_noticias"];
              foreach($lista as $t) {
                if ($t->permiso > 0) {                  
                  
                  // CASOS ESPECIALES:
                  
                  // Cuando haya categorias_entradas con FIJO = 1, directamente reemplazar
                  // el menu de noticias por el listado de estas categorias fijas
                  
                  if ($t->id == 600) { // Menu de noticias
                    
                    // Controlamos si hay alguna categoria fija
                    $hay_fijas = 0;
                    foreach($categorias_noticias as $c) {
                      if ($c->fija == 1) { $hay_fijas = 1; break; }
                    }
                    
                    if ($hay_fijas == 1) {
                      
                      $anteriores = array();
                      $posteriores = array();
                      
                      if (sizeof($t->children)>0) {
                        // Si tienen orden negativo, va antes de las categorias fijas
                        foreach($t->children as $child) {
                          if ($child->orden < 0) $anteriores[] = $child;
                          else $posteriores[] = $child;
                        }
                      }
                      
                      echo "<li>";
                      echo " <a href='javascript:void(0)'>";
                      echo '  <i class="fa fa-file-text-o"></i>';
                      echo '  <span class="pull-right text-muted">';
                      echo '   <i class="fa fa-fw fa-angle-right text"></i>';
                      echo '   <i class="fa fa-fw fa-angle-down text-active"></i>';
                      echo '  </span>';                    
                      echo "  <span class='bold'>".(($emp->id_proyecto == 4) ? "Noticias" : $t->title)."</span>";
                      echo "</a>";
                      echo "<ul class='nav nav-sub'>";
                      get_children($anteriores,$nivel+1,$params);
                      foreach($categorias_noticias as $c) {
                        echo "<li>";
                        echo " <a href='app/#entradas/$c->id'>";
                        echo "  <span>".$c->title."</span>";
                        echo " </a>";
                        echo "</li>";
                      }
                      get_children($posteriores,$nivel+1,$params);
                      echo "</ul>";
                      echo "</li>";
                      continue;                      
                    }
                  }                  
                  
                  // =======================================================
                  
                }
              }
            } // Fin get_children
            
            // Si es un vendedor
            if ($perfil == -1) {

              // Listamos todos los proyectos
              foreach($proyectos as $proyecto) {
                if ($proyecto->id == 0) continue;
                echo "<li><a href='app/#ver_proyecto/".$proyecto->id."'>";
                echo "<i class='icon fa fa-file-text'></i>";
                echo "<span class='font-bold'>".$proyecto->nombre."</span></a></li>";  
              }
              if ($id_usuario == 0 || $id_usuario == 1675) {
                echo "<li><a href='app/#administradores'><i class='icon glyphicon glyphicon-user'></i><span class='font-bold'>Administradores</span></a></li>";
                echo "<li><a>";
                echo '<i class="fa fa-cog"></i>';
                echo '<span class="pull-right text-muted">';
                echo '<i class="fa fa-fw fa-angle-right text"></i>';
                echo '<i class="fa fa-fw fa-angle-down text-active"></i>';
                echo '</span>';
                echo "<span class='font-bold'>Configuraci&oacute;n</span>";
                echo "</a>";
                echo "<ul class='nav nav-sub'>";
                foreach($permisos as $t) {
                  echo "<li>";
                  echo "<a href='app/#$t->nombre'>";
                  echo html_entity_decode($t->title,ENT_QUOTES);
                  echo "</a>";
                  echo "</li>";
                }
                echo "</ul>";
                echo "</li>";
              }

            } else {
              // Cargamos el MENU

              $i = 0;
              $orden_1_ant = -1;
              $abrio_menu = 0;
              for($i=0;$i<sizeof($permisos);$i++) {
                
                $t = $permisos[$i];
                $proximo = (($i+1)<sizeof($permisos)) ? $permisos[$i+1] : null;

                if ($t->permiso == 0) continue;
                if (isset($t->visible) && $t->visible == 0) continue;
                if ($t->id_modulo == 0 && $proximo == null) continue;
                if ($t->id_modulo == 0 && $proximo != null && $proximo->orden_1 != $orden_1_ant) continue;

                if ($perfil == 1338 && $empresa->id == 249 && $t->nombre == "articulos") continue;

                $href = ($t->id_modulo != 0) ? "href='app/#$t->nombre'" : "";
                
                $class = "";
                if ($t->orden_1 != $orden_1_ant) {
                  $orden_1_ant = $t->orden_1;

                  if ($abrio_menu == 1) {
                    echo "</ul>";
                    echo "</li>";
                  }

                  echo "<li class='".(($t->nombre=="inicio")?"active":"")."'>";
                  echo "<a $href class='$class'>";
                  echo '<i class="'.$t->clase.'"></i>';
                  if ($proximo != null && $proximo->orden_1 == $t->orden_1 && $proximo->visible == 1) {
                    echo '<span class="pull-right text-muted">';
                    echo '<i class="fa fa-fw fa-angle-right text"></i>';
                    echo '</span>';                      
                  }

                  echo "<span class='font-bold'>";
                  echo html_entity_decode($t->title,ENT_QUOTES);
                  echo "</span>";
                  echo "</a>";
                  echo "<ul class='nav nav-sub'>";
                  echo "<li class='nav-sub-title'><a href='app/#$t->nombre'>".html_entity_decode($t->title,ENT_QUOTES)."</a></li>";

                  $abrio_menu = 1;

                } else {

                  // Casos especiales
                  if ($t->id == 26 && $empresa->id == 133) {
                    // CARBONI TIENE LOS PUNTOS DE VENTA EN EL MENU SEPARADOS
                    foreach($puntos_venta as $pv) {
                      echo '<li><a href="app/#ventas_listado/'.$pv->id.'">'.$pv->nombre.'</a></li>';
                    }

                  } else if ($t->tiene_pantalla == 1) {
                    echo "<li><a $href>";
                    echo html_entity_decode($t->title,ENT_QUOTES);
                    echo "</a></li>";
                  }

                  if ($t->id == 604) { // Despues de NUEVA ENTRADA
                    // Controlamos si hay alguna categoria fija
                    foreach($categorias_noticias as $c) {
                      if ($c->fija == 1) {
                        echo "<li><a href='app/#entradas/$c->id'>".html_entity_decode($c->title,ENT_QUOTES)."</a></li>";
                      }
                    }
                  }

                }

              }
              if ($abrio_menu == 1) {
                echo "</ul>";
                echo "</li>";
              }
            }
            ?>
          </ul>
        </nav>
        </div>
      </div>
        </div>
    <!-- FIN MENU -->
        
      <div class="app-content">
        <div id="top_container"></div>
        <div id="main_container"></div>
        <div id="second_container"></div>
        <div id="bottom_container">¿Necesitas ayuda? Envíanos un email a <span>info@entrenaymas.com</span> o comunicate al <span>(+34) 641522483</span></div>
      </div>
    
    <div id="waitingMsg">Por favor espere...</div>
        
    </div>

  <?php if (!empty($mensaje_cuenta) && $mensaje_cuenta_nivel == 1) { ?>
    <div class="bottom-message">
      <div class="message">
        <?php echo $mensaje_cuenta ?>
      </div>
    </div>
  <?php } else if (!empty($mensaje_cuenta) && $mensaje_cuenta_nivel == 2) { ?>
    <div class="full-message">
      <div class="full-message-text">
        <span><?php echo $mensaje_cuenta ?></span>
      </div>
    </div>
  <?php } ?>
</div>

<?php include("/var/www/templates/entrenaymas/includes/paycomet_iframe.php") ?>

<?php 
// En TOQUE reproducimos un audio
if ($empresa->id == 571 || $empresa->id == 1275) { ?>
<audio id="audio" style="display:none" src="resources/sounds/toque.mp3" preload="auto"></audio>
<script type="text/javascript">
function playSound() {
  var audio = document.getElementById("audio");
  audio.play();
}
</script>
<?php } ?>

<!--
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){
z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?3Dgf3IsFpW47unDh7TD6dIAAzGIqFeAs';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
-->
</body>
</html>
<?php
ob_end_flush();
function slib_compress_html( $buffer ) {
  $replace = array(
    "#<!--.*?-->#s" => "",      // strip comments
    "#>\s+<#"       => ">\n<",  // strip excess whitespace
    "#\n\s+<#"      => "\n<"    // strip excess whitespace
  );
  $search = array_keys( $replace );
  $html = preg_replace( $search, $replace, $buffer );
  return trim( $html );
}
?>