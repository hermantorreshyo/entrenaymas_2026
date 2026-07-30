<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

  function __construct() {
    parent::__construct();
  }
  
  function index() {

    ini_set('memory_limit', '-1');
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen el HTML del panel
    @session_start();
    if (!isset($_SESSION["perfil"])) redirect("/");
    $this->load->helper('url');

    $this->load->model('Permiso_Model');
    $this->load->model('Empresa_Model');
    $this->load->model('Proyecto_Model');
    $this->load->model('Usuario_Model');
    $this->load->helper("fecha_helper");
    
    $comprobantes = array();
    $almacenes = array();
    $puntos_venta = array();
    $tipos_estado = array();
    $tipos_tarifas = array();
    $tipos_estado_pedidos = array();
    $tipos_inmueble = array();
    $tipos_operacion = array();
    $tipos_publicidades = array();
    $tipos_vehiculos = array();
    $categorias_publicidades = array();
    $categorias_noticias = array();
    $categorias_viajes = array();
    $categorias_opcionales = array();
    $categorias_clasificados = array();
    $origenes = array();
    $bancos = array();
    $articulos = array();
    $cuentas_bancarias = array();
    $articulos_propiedades = array();
    $vendedores = array();
    $planes = array();
    $salones = array();
    $templates = array();
    $proyectos = array();
    $usuarios = array();
    $rubros = array();
    $tarjetas = array();
    $razones_sociales = array();
    $videos = array();
    $hoteles = array();
    $trimestres = array();
    $comisiones = array();
    $sectores = array();
    $tipos_mantenimiento = array();
    $tipos_ordenes_trabajo = array();
    $pres_documentaciones = array();
    $profesionales = array();
    $turnos_servicios = array();
    $asuntos = array();
    $tipos_gastos = array();
    $reglas_ofertas = array();
    $emails_templates = array();
    $conceptos_gastos = array();
    $conceptos_compras = array();
    $conceptos_ventas = array();
    $localidades = array();
    $departamentos_comerciales = array();
    $sindi_valor_consulta = 0;
    $sucursales_usuario = array();
    $articulos_etiquetas = array();
    $toque_categorias = array();
    $cajas = array();
    $paises = array();
    $provincias = array();
    $rubros_lpc = array();
    $forma_envio = "";
    
    $tabla_articulos = array();
    $tabla_ventas = array();
    $tabla_compras = array();
    $otras_empresas = array();
    $consultas_tipos = array();
    $categorias_entrena = array();

    $q = $this->db->query("SELECT * FROM com_idiomas ORDER BY id ASC");
    $idiomas = $q->result();
    $mensaje_cuenta = "";
    $cache_articulos = 0;
    $destacado = 0;

    $usuario = new stdClass();
    $usuario->id = 0;
    $usuario->path = "";
    $usuario->apellido = "";
    $usuario->id_sucursal = 0;
    $usuario->language = "es";
    $usuario->hora_desde = "00:00:00";
    $usuario->solo_usuario = 0;
    $usuario->horarios = array();
    $mensaje_cuenta_nivel = 0;
    $clave_especial = "";
    
    // Debemos cargar el usuario con todo su perfil
    $perfil = $_SESSION["perfil"];
    // Si el perfil es -1, es porque es SUPERADMIN
    
    if ($perfil == -1) {
      $inicio = "inicio_pymvar";
      $empresa = new stdClass();
      $empresa->id = 0;
      $empresa->id_empresa = 0;
      $empresa->id_proyecto = 0;
      $empresa->proyecto = "VarCreative";
      $empresa->nombre = (isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "Varcreative");
      $empresa->configuracion_menu_iconos = 1;
      $empresa->configuracion_menu = 1;
      $empresa->configuracion_sonido = 0;
      $empresa->config = array();
      $empresa->id_web_template = 0;
      $empresa->servidor_local = "";
      $empresa->administrar_pagos = 0;

      // Si tenemos DEBUG = 1, entonces tomamos los archivos directamente
      // Sino, usamos su version comprimida y compilada
      $q = $this->db->query("SELECT * FROM com_configuracion WHERE id = 1");
      $configuracion = $q->row();
      $js_files = array();
      $css_files = array();
      if ($configuracion->debug == 1) {
        $css_files = $this->css_files();
        $js_files = $this->js_files($empresa->id_proyecto);
      }
      
      $proyectos = $this->Proyecto_Model->activos();

      $usuarios = $this->Usuario_Model->buscar(array(
        "admin"=>1,
        "offset"=>9999,
      ));

      $this->load->model('Modulo_Model');
      $modulos = $this->Modulo_Model->get_all();

      $perfil_row = new stdClass();
      $perfil_row->solo_usuario = 0;
      $perfil_row->principal = 1;
      $usuario->ocultar_notificaciones = 1;
      $posicion_promedio = 0;

      $q = $this->db->query("SELECT * FROM planes ORDER BY nombre ASC");
      $planes = $q->result();
      
    } else {
      // Obtenemos la pantalla que se debe mostrar al inicio
      $inicio = "";
      $modulos = array();
      
      // Obtenemos la empresa que se esta viendo
      $id_empresa = $_SESSION["id_empresa"];
      $empresa = $this->Empresa_Model->get($id_empresa);
      $empresa->id_empresa = $empresa->id;
      $_SESSION["id_empresa"] = $empresa->id;
      $_SESSION["cuit"] = str_replace("-","",$empresa->cuit);

      // Si tiene como vendedor a la constructora
      $es_constructora = false;
      if (isset($empresa->vendedores)) {
        foreach($empresa->vendedores as $emp_vend) {
          if ($emp_vend->id_usuario == 1676) {
            $es_constructora = true;
            break;
          }
        }
      }
      if ($es_constructora) {
        $sql = "SELECT U.*, U.id AS id_usuario, IF(E.razon_social != U.nombre,CONCAT(E.razon_social,': ',U.nombre),E.razon_social) AS razon_social ";
        $sql.= "FROM empresas E INNER JOIN empresas_vendedores VEND ON (E.id = VEND.id_empresa) ";
        $sql.= "INNER JOIN com_usuarios U ON (E.id = U.id_empresa) ";
        $sql.= "WHERE VEND.id_usuario = 1676 ";
        $q_cons = $this->db->query($sql);
        $otras_empresas = $q_cons->result();
      }

      // Controlamos el estado de la cuenta
      $fecha_vencimiento = new DateTime($empresa->fecha_suspension);
      $fecha_vencimiento->modify("-5 days");
      if ($empresa->administrar_pagos == 1 && $empresa->fecha_suspension < date("Y-m-d")) {
        $mensaje_cuenta = '<div>Su cuenta ha sido suspendida. Por favor regularice su situaci&oacute;n para seguir utilizando el servicio: </div><div><a href="app/#mi_cuenta" class="btn btn-lg btn-danger m-l">Pagar</a></div>';
        $mensaje_cuenta_nivel = 2;
      } else if ($empresa->administrar_pagos == 1 &&  $fecha_vencimiento->format("Y-m-d") < date("Y-m-d")) {
        $mensaje_cuenta = 'Su cuenta se encuentra vencida. Por favor regularice su situaci&oacute;n: <a href="app/#mi_cuenta" class="btn btn-danger m-l">Pagar</a>';
        $mensaje_cuenta_nivel = 1;
      }

      // Si tenemos DEBUG = 1, entonces tomamos los archivos directamente
      // Sino, usamos su version comprimida y compilada
      $q = $this->db->query("SELECT * FROM com_configuracion WHERE id = 1");
      $configuracion = $q->row();
      $js_files = array();
      $css_files = array();
      if ($configuracion->debug == 1) {
        $css_files = $this->css_files();
        $js_files = $this->js_files($empresa->id_proyecto);
      }

      if (!empty($_SESSION["id"])) {
        $id_usuario = $_SESSION["id"];
        $usuario = $this->Usuario_Model->get($id_usuario);
        $sucursales_usuario = $usuario->sucursales;
        $clave_especial = $usuario->clave_especial;
        $destacado = $usuario->destacado;
        $posicion_promedio = $usuario->posicion_promedio;
        $_SESSION["perfil"] = $usuario->id_perfiles; // Actualizamos por si cambio el perfil por ej cuando paga
      }


      // Esto se cachea para ahorrar consultas ajax:
      // -------------------------------------------
      
      if (file_exists("application/models/categoria_entrada_model.php")) {
        $this->load->Model("Categoria_Entrada_Model");
        $categorias_noticias = $this->Categoria_Entrada_Model->get_arbol();
      }

      if (file_exists("application/models/categoria_viaje_model.php")) {
        $this->load->Model("Categoria_Viaje_Model");
        $categorias_viajes = $this->Categoria_Viaje_Model->get_arbol(); 
      }

      if (file_exists("application/models/categoria_opcional_model.php")) {
        $this->load->Model("Categoria_Opcional_Model");
        $categorias_opcionales = $this->Categoria_Opcional_Model->get_arbol();
      }

      if (file_exists("application/models/rubro_model.php")) {
        $this->load->Model("Rubro_Model");
        $r_config = ($empresa->id == 571 && $perfil == 661) ? array("id_usuario"=>$usuario->id) : array();
        $rubros = $this->Rubro_Model->get_arbol(0,"",$r_config);
      }

      if ($empresa->config["tipo_empresa"] == 4) {
        $this->load->Model("Rubro_Lpc_Model");
        $rubros_lpc = $this->Rubro_Lpc_Model->get_arbol(0,"");
        $empresa->configuracion_autogenerar_codigos = 1;
      }

      if (file_exists("application/models/tipo_gasto_model.php")) {
        $this->load->Model("Tipo_Gasto_Model");
        $tipos_gastos = $this->Tipo_Gasto_Model->get_arbol();
      }
      if (file_exists("application/models/concepto_model.php")) {
        $this->load->Model("Concepto_Model");
        $conceptos_gastos = $this->Concepto_Model->get_arbol(0,array("totaliza_en"=>"G"));
        $conceptos_compras = $this->Concepto_Model->get_arbol(0,array("totaliza_en"=>"C"));
        $conceptos_ventas = $this->Concepto_Model->get_arbol(0,array("totaliza_en"=>"V"));
      }

      // Primero consultamos si no existen algunos registros especificos de la empresa      
      $q = $this->db->query("SELECT * FROM ped_tipos_estado WHERE id_empresa = $id_empresa ORDER BY orden ASC");
      if ($q->num_rows() == 0) {
        // Sino, tomamos los valores por defecto (id_empresa = 0)
        $q = $this->db->query("SELECT * FROM ped_tipos_estado WHERE id_empresa = 0 ORDER BY orden ASC");
      }
      $tipos_estado_pedidos = $q->result();
      
      $q = $this->db->query("SELECT * FROM web_templates WHERE id_proyecto = $empresa->id_proyecto AND publico = 1 ORDER BY id DESC");
      $templates = $q->result();      

      $q = $this->db->query("SELECT * FROM tarjetas WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $tarjetas = $q->result();     

      // ARGENCASH
      if ($empresa->id == 228) {
        $q = $this->db->query("SELECT * FROM pres_documentacion WHERE id_empresa = $empresa->id");
        $pres_documentaciones = $q->result();
      }

      /*
      $q = $this->db->query("SELECT ET.* FROM crm_emails_templates ET INNER JOIN empresas E ON (ET.id_empresa = E.id) WHERE ET.id_empresa = $id_empresa AND E.id_proyecto != 9 AND E.id NOT IN (936) ");
      foreach($q->result() as $temp) {
        // Si el nombre de la empresa o el logo todavia no fueron seteados en el template
        if (strpos($temp->texto, "{{empresa}}")>0 || strpos($temp->texto, "{{empresa_logo}}")>0) {
          // Armamos el link del logo
          if (sizeof($empresa->dominios)>0 && isset($empresa->config["logo_1"]) && !empty($empresa->config["logo_1"])) {
            $dominio = $empresa->dominios[0];
            $empresa_logo = $dominio."/sistema/".$empresa->config["logo_1"];
            $empresa_logo = "http://".str_replace("//", "/", $empresa_logo);
            $temp->texto = str_replace("{{empresa_logo}}", $empresa_logo, $temp->texto);
          }
          $temp->texto = str_replace("{{empresa}}", $empresa->nombre, $temp->texto);
          // Actualizamos el template para que al momento de editarlo se vea bien
          $this->db->where("id",$temp->id);
          $this->db->where("id_empresa",$empresa->id);
          $this->db->update("crm_emails_templates",$temp);
        }
        $emails_templates[] = $temp;
      }
      */
      
      $q = $this->db->query("SELECT * FROM tipos_comprobante ORDER BY id ASC");
      $comprobantes = $q->result();        

      $q = $this->db->query("SELECT * FROM razones_sociales WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $razones_sociales = $q->result();        

      $q = $this->db->query("SELECT * FROM departamentos_comerciales WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $departamentos_comerciales = $q->result();        

      $q = $this->db->query("SELECT * FROM categorias_entrena WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $categorias_entrena = $q->result();   
      
      $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal FROM puntos_venta PV LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
      $sql.= "WHERE PV.id_empresa = $empresa->id AND PV.activo = 1 ";
      $sql.= ($this->db->field_exists("numero_fiscal","puntos_venta") && ($id_empresa == 249 || $id_empresa == 868)) ? "ORDER BY ALM.nombre ASC, PV.numero_fiscal ASC" : "ORDER BY PV.numero ASC";
      $q = $this->db->query($sql);
      $puntos_venta = $q->result();
      if ($id_empresa == 249 || $id_empresa == 868) {
        foreach($puntos_venta as $pv) {
          $ll = explode("-", $pv->sucursal);
          $pv->sucursal = trim(end($ll));
        }
      }
      
      $q = $this->db->query("SELECT * FROM bancos ORDER BY nombre ASC");
      $bancos = $q->result();
      
      $q = $this->db->query("SELECT * FROM cuentas_bancarias WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $cuentas_bancarias = $q->result();
      
      $q = $this->db->query("SELECT * FROM vendedores WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $vendedores = $q->result();        

      $q = $this->db->query("SELECT * FROM planes WHERE id_proyecto = $empresa->id_proyecto ORDER BY nombre ASC");
      $planes = $q->result();

      $q = $this->db->query("SELECT * FROM hot_hoteles WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $hoteles = $q->result();
        
      $q = $this->db->query("SELECT * FROM inm_tipos_inmueble ORDER BY orden ASC");
      $tipos_inmueble = $q->result();

      // Primero consultamos si no existen algunos registros especificos de la empresa      
      $q = $this->db->query("SELECT * FROM inm_tipos_operacion WHERE id_empresa = $id_empresa ORDER BY orden ASC");
      if ($q->num_rows() == 0) {
        // Sino, tomamos los valores por defecto (id_empresa = 0)
        $q = $this->db->query("SELECT * FROM inm_tipos_operacion WHERE id_empresa = 0 ORDER BY orden ASC");
      }
      $tipos_operacion = $q->result();

      $q = $this->db->query("SELECT * FROM crm_asuntos WHERE id_empresa = 0 OR id_empresa = $id_empresa ORDER BY orden ASC");
      $asuntos = $q->result();
      
      $q = $this->db->query("SELECT * FROM inm_tipos_estado ORDER BY orden ASC");
      $tipos_estado = $q->result();      

      $q = $this->db->query("SELECT * FROM cajas WHERE id_empresa = $id_empresa ORDER BY nombre ASC");
      $cajas = $q->result();
        
      $q = $this->db->query("SELECT * FROM not_publicidades_tipos ORDER BY orden ASC");
      $tipos_publicidades = $q->result();      
      
      $q = $this->db->query("SELECT * FROM not_publicidades_categorias WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $categorias_publicidades = $q->result();
      
      if (file_exists("application/models/clasificado_categoria_model.php")) {
        $this->load->Model("Clasificado_Categoria_Model");
        $categorias_clasificados = $this->Clasificado_Categoria_Model->get_arbol();
      }

      if ($empresa->id == 571) {
        $q = $this->db->query("SELECT * FROM toque_categorias WHERE id_padre = 0 ORDER BY id ASC");
        foreach($q->result() as $r_toque_cat) {
          $q_toque_children = $this->db->query("SELECT * FROM toque_categorias WHERE id_padre = $r_toque_cat->id ORDER BY id ASC");
          $r_toque_cat->children = $q_toque_children->result();
          $toque_categorias[] = $r_toque_cat;
        }
      }

      $q = $this->db->query("SELECT * FROM veh_tipos ORDER BY orden ASC");
      $tipos_vehiculos = $q->result();

      $q = $this->db->query("SELECT * FROM via_tipos_tarifas WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $tipos_tarifas = $q->result();
      
      $q = $this->db->query("SELECT * FROM crm_origenes ORDER BY orden ASC");
      $origenes = $q->result();      
      
      $q = $this->db->query("SELECT * FROM almacenes WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $almacenes = $q->result();

      $q = $this->db->query("SELECT * FROM articulos_etiquetas WHERE id_empresa = $empresa->id ORDER BY nombre ASC");
      $articulos_etiquetas = $q->result();

      // Tablas dinamicas
      $this->load->model("Configuracion_Model");
      $tabla_articulos = $this->Configuracion_Model->get_tabla_articulos(array("id_empresa"=>$empresa->id));
      $tabla_ventas = $this->Configuracion_Model->get_tabla_ventas(array("id_empresa"=>$empresa->id));
      $tabla_compras = $this->Configuracion_Model->get_tabla_compras(array("id_empresa"=>$empresa->id));
      
      // Di Piero Lobos, tiene que ponerse el PV 3 por defecto
      if ($id_empresa == 229 && $usuario->id == 1389) {
        foreach($puntos_venta as $pv) {
          $pv->por_default = (($pv->id == 1647) ? 1 : 0);
        }
      }

      $usuarios = $this->Usuario_Model->buscar(array(
        "offset"=>999999,
      ));

      // PYMVAR, SHOPVAR o RESTOVAR
      if ($empresa->id_proyecto == 1 || $empresa->id_proyecto == 2 || $empresa->id_proyecto == 10 || $empresa->id_proyecto == 19 || $empresa->id_proyecto == 4 || $empresa->id_proyecto == 7) {

        if (file_exists("application/models/articulo_model.php")) {
          $this->load->model("Articulo_Model");
          if ((isset($empresa->config["facturacion_usa_cache_articulos"]) && $empresa->config["facturacion_usa_cache_articulos"] == 1)) {
            // Si hay mas de 20.000 articulos, que no se puedan cachear ya que seria muy pesado
            $total_articulos = $this->Articulo_Model->count_actives();
            if ($total_articulos > 200000) {
              $articulos = array();
            } else {
              $articulos = $this->Articulo_Model->buscar(array(
                "min"=>1,
                "activo"=>1,
                "id_sucursal"=>((isset($usuario->id_sucursal) && $configuracion->local == 0) ? $usuario->id_sucursal : 0),
              ))["results"];          
              $cache_articulos = 1;
            }
          }
          // Configuracion de las listas de precios
          $listas_precios = $this->Articulo_Model->get_lista_precios_configuracion(array(
            "id_empresa"=>$empresa->id
          ));
          // A la configuracion de las listas de precios lo concatenamos al objeto empresa, para que luego
          // en la vista se conviertan en constantes de JS
          $empresa = (object) array_merge((array) $empresa, $listas_precios);
        }
      }

      // RESTOVAR
      $q = $this->db->query("SELECT * FROM res_salones WHERE id_empresa = $empresa->id ORDER BY id ASC");
      $salones = $q->result();

      // DOCVAR
      if ($empresa->id_proyecto == 7) {
        $q = $this->db->query("SELECT * FROM med_profesionales WHERE id_empresa = $empresa->id ORDER BY apellido ASC, nombre ASC");
        $profesionales = $q->result();
      }

      // Si es SINDICATO
      if ($empresa->id_proyecto == 16) {
        $sql = "SELECT * FROM sindi_configuracion WHERE id_empresa = $empresa->id ";
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $r_sindi_conf = $q->row();
          $sindi_valor_consulta = $r_sindi_conf->valor_consulta;
        }

        // Corremos la limpieza de limites
        $this->load->model("Sindi_Afiliado_Model");
        $this->Sindi_Afiliado_Model->limpiar_limites();
      }
      
      if (file_exists("application/models/turno_servicio_model.php")) {
        $this->load->model("Turno_Servicio_Model");
        $turnos_servicios = $this->Turno_Servicio_Model->buscar(array(
          "offset"=>9999,
        ))["results"];
      }

      // COLVAR
      if ($empresa->id_proyecto == 5) {
        
        
        if (file_exists("application/models/comision_model.php")) {
          $this->load->model("Comision_Model");
          $comisiones = $this->Comision_Model->buscar();
        }

        if (file_exists("application/models/trimestre_model.php")) {
          $this->load->model("Trimestre_Model");
          $trimestres = $this->Trimestre_Model->buscar();
        }
      }

      // SHOPVAR
      if ($empresa->id_proyecto == 2) {
        if (file_exists("application/models/articulo_propiedad_model.php")) {
          $this->load->model("Articulo_Propiedad_Model");
          $articulos_propiedades = $this->Articulo_Propiedad_Model->get_all();
        }

        // Pasamos los carritos abandonados
        $this->load->model("Factura_Model");
        $this->Factura_Model->pasar_a_abandonados();
      }

      // MANTENIMIENTO
      if ($empresa->id_proyecto == 13) {
        if (file_exists("application/models/sector_model.php")) {
          $this->load->model("Sector_Model");
          $sectores = $this->Sector_Model->get_all();        
        }
        if (file_exists("application/models/tipo_mantenimiento_model.php")) {
          $this->load->model("Tipo_Mantenimiento_Model");
          $tipos_mantenimiento = $this->Tipo_Mantenimiento_Model->get_all();
        }
        if (file_exists("application/models/tipo_orden_trabajo_model.php")) {
          $this->load->model("Tipo_Orden_Trabajo_Model");
          $tipos_ordenes_trabajo = $this->Tipo_Orden_Trabajo_Model->get_all();        
        }
      }

      if ($empresa->id_proyecto == 3 || $empresa->id == 821) {

        $this->load->model("Pais_Model");
        $paises = $this->Pais_Model->get_select();

        $this->load->model("Provincia_Model");
        $provincias = $this->Provincia_Model->get_all(0,99999);

        $this->load->model('Localidad_Model');
        $localidades = $this->Localidad_Model->utilizadas(array(
          "id_empresa"=>$empresa->id,
          "id_proyecto"=>$empresa->id_proyecto,
        ));
      }

      // Perfil de usuario
      if (file_exists("application/models/perfil_model.php")) {
        $this->load->model("Perfil_Model");
        $perfil_row = $this->Perfil_Model->get($perfil);
      }

      // OFERTAS
      $this->load->model("Regla_Oferta_Model");
      $reglas_ofertas = $this->Regla_Oferta_Model->buscar(array(
        "fecha"=>date("Y-m-d"),
        "offset"=>99999,
        "id_empresa"=>$empresa->id,
        "id_sucursal"=>((isset($usuario->id_sucursal)) ? $usuario->id_sucursal : 0),
      ))["results"];

      if ($this->db->table_exists('crm_consultas_tipos')) {
        $sql = "SELECT * FROM crm_consultas_tipos WHERE id_empresa = $empresa->id ORDER BY orden ASC ";
        $q_consultas = $this->db->query($sql);
        $consultas_tipos = $q_consultas->result();
      }

      // Metodo de envio
      $sql = "SELECT * FROM env_configuracion WHERE id_empresa = $empresa->id";
      $q_envio = $this->db->query($sql);
      if ($q_envio->num_rows() > 0) {
        $r_envio = $q_envio->row();
        $forma_envio = $r_envio->forma_envio;
      }

    }
    
    // Si la empresa tiene alicuotas de IVA especificas
    $q = $this->db->query("SELECT * FROM tipos_alicuotas_iva WHERE id_empresa = $empresa->id ORDER BY orden ASC");
    if ($q->num_rows() > 0) {
      $alicuotas_iva = $q->result();  
    } else {
      $q = $this->db->query("SELECT * FROM tipos_alicuotas_iva WHERE id_empresa = 0 ORDER BY orden ASC");
      $alicuotas_iva = $q->result();      
    }
    
    $q = $this->db->query("SELECT * FROM com_monedas ORDER BY id ASC");
    $monedas = $q->result();
    
    $q = $this->db->query("SELECT * FROM com_videos");
    foreach($q->result_array() as $v) {
      $videos[$v["clave"]] = $v;
    }

    // INMOVAR Y SHOPVAR SIEMPRE EN ESTADO 1
    if ($empresa->id_proyecto == 3 || $empresa->id_proyecto == 2) {
      $_SESSION["estado"] = 1;
      //$js_files = array(); // DEBUG = 0
      //$css_files = array();
    }

    // TODO: Hack temporario para VICTOR
    if ($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) {
      $empresa->config["facturacion_saltar_precio"] = 1;
    }
    // TODO: Temporario para MEGA
    if (!isset($empresa->config["facturacion_ocultar_buscador"]) && ($empresa->id == 249 || $empresa->id == 868) && isset($usuario->id_sucursal) && $usuario->id_sucursal != 56) {
      $empresa->config["facturacion_ocultar_buscador"] = 1;
    } else {
      $empresa->config["facturacion_ocultar_buscador"] = 0;
    }

    // ID_EMPRESA que realiza la factura
    // Si existe el parametro dinamico id_empresa_facturacion, lo tomamos
    if (isset($empresa->config["id_empresa_facturacion"])) {
      $id_empresa_facturacion = $empresa->config["id_empresa_facturacion"];
      unset($empresa->config["id_empresa_facturacion"]);
    } else {
      $id_empresa_facturacion = 936;
    }

    $es_milling = $this->Empresa_Model->es_milling($empresa->id);
    $es_toque = $this->Empresa_Model->es_toque($empresa->id);

    // Este array tiene las variables que son utilizadas por la vista
    $data = array(
      "base_url"=>URL_BASE."/sistema/",
      "db"=>$this->db,

      // Datos del usuario
      "id_usuario" => $_SESSION["id"],
      "idioma" => (empty($usuario->language) ? "es" : $usuario->language),
      "path_usuario" => $usuario->path,
      "id_sucursal" => (isset($usuario->id_sucursal)) ? $usuario->id_sucursal : 0,
      "id_vendedor" => (isset($usuario->id_vendedor)) ? $usuario->id_vendedor : 0,
      "usuario_hora_desde" => (isset($usuario->hora_desde)) ? $usuario->hora_desde : "00:00:00",
      "horarios"=>$usuario->horarios,
      "perfil" => $perfil,
      "solo_usuario"=> max($perfil_row->solo_usuario, $usuario->solo_usuario), // De los dos posibles valores tomamos el mayor
      "usuario_ppal"=> $perfil_row->principal,
      "link_web"=>"profesional/".$usuario->apellido."-".$usuario->id,
      "ocultar_notificaciones"=> $usuario->ocultar_notificaciones,
      "mensaje_cuenta"=>$mensaje_cuenta,
      "mensaje_cuenta_nivel"=>$mensaje_cuenta_nivel,
      "sucursales_usuario"=>$sucursales_usuario,
      "id_empresa_facturacion"=>$id_empresa_facturacion,
      "clave_especial"=>$clave_especial,
      "destacado"=>$destacado,

      // Datos de permisos
      "permisos" => $this->Permiso_Model->get_permisos(array(
        "id_perfil"=>$perfil,
        "id_proyecto"=>$empresa->id_proyecto,
        "lang"=>$usuario->language,
      )),
      "modulos"=>$modulos,
      "nombre_usuario" => $_SESSION["nombre_usuario"],
      "email" => $_SESSION["email"],
      "empresa" => $empresa,
      "tiempo_notificaciones"=>$configuracion->tiempo_notificaciones,
      "version_js"=>((isset($configuracion->version_js) && !empty($configuracion->version_js) && $configuracion->debug == 1) ? $configuracion->version_js : 0),
      "local"=>$configuracion->local,
      "inicio" => $inicio,
      "js_files" => $js_files,  // Si se tiene que usar la version cacheada, se envia []
      "css_files" => $css_files,
      "estado" => ($_SESSION["estado"] == 1 ? 1 : 0),
      "volver_superadmin" => ((isset($_SESSION["volver_superadmin"]) && $_SESSION["volver_superadmin"] == 1) ? 1 : 0),
      "proyectos" => $proyectos,
      "forma_envio" => $forma_envio,

      "articulos" => $articulos,
      "cache_articulos" => $cache_articulos,

      // Este array tiene info de login de otras empresas relacionadas con el usuario
      "otras_empresas"=>$otras_empresas,

      // Arrays utilizados para cachear y ahorrar AJAX requests
      "departamentos_comerciales" => $departamentos_comerciales,
      "categorias_entrena" => $categorias_entrena,
      "consultas_tipos" => $consultas_tipos,
      "paises" => $paises,
      "provincias" => $provincias,
      "usuarios" => $usuarios,
      "idiomas" => $idiomas,
      "tarjetas" => $tarjetas,
      "templates" => $templates,
      "comprobantes" => $comprobantes,
      "almacenes" => $almacenes,
      "puntos_venta" => $puntos_venta,
      "origenes" => $origenes,
      "monedas" => $monedas,
      "salones" => $salones,
      "bancos" => $bancos,
      "vendedores" => $vendedores,
      "planes" => $planes,
      "cuentas_bancarias" => $cuentas_bancarias,
      "alicuotas_iva" => $alicuotas_iva,
      "videos" => $videos,
      "trimestres" => $trimestres,
      "comisiones" => $comisiones,
      "hoteles" => $hoteles,
      "profesionales" => $profesionales,
      "turnos_servicios" => $turnos_servicios,
      "articulos_propiedades" => $articulos_propiedades,
      "articulos_etiquetas" => $articulos_etiquetas,
      "reglas_ofertas" => $reglas_ofertas,
      "localidades" => $localidades,
      "cajas" => $cajas,
      "toque_categorias" => $toque_categorias,
      
      // Tablas dinamicas
      "tabla_articulos" => $tabla_articulos,
      "tabla_ventas" => $tabla_ventas,
      "tabla_compras" => $tabla_compras,

      "tipos_tarifas" => $tipos_tarifas,
      "tipos_estado" => $tipos_estado,
      "tipos_estado_pedidos" => $tipos_estado_pedidos,
      "tipos_inmueble" => $tipos_inmueble,
      "tipos_operacion" => $tipos_operacion,
      "tipos_publicidades" => $tipos_publicidades,
      "tipos_vehiculos" => $tipos_vehiculos,
      "asuntos" => $asuntos,

      "sectores" => $sectores,
      "tipos_mantenimiento"=>$tipos_mantenimiento,
      "tipos_ordenes_trabajo"=>$tipos_ordenes_trabajo,
      "pres_documentaciones"=>$pres_documentaciones,
      
      "razones_sociales" => $razones_sociales,
      "rubros" => $rubros,
      "rubros_lpc" => $rubros_lpc,
      "tipos_gastos" => $tipos_gastos,
      "conceptos_gastos" => $conceptos_gastos,
      "conceptos_compras" => $conceptos_compras,
      "conceptos_ventas" => $conceptos_ventas,
      "categorias_publicidades" => $categorias_publicidades,
      "categorias_noticias" => $categorias_noticias,
      "categorias_viajes" => $categorias_viajes,
      "categorias_opcionales" => $categorias_opcionales,
      "categorias_clasificados" => $categorias_clasificados,
      "emails_templates" => $emails_templates,

      "milling"=>$es_milling,
      "toque"=>$es_toque,
      "megashop"=>(($empresa->id == 249 || $empresa->id == 868) ? 1 : 0),
      "sindi_valor_consulta"=>$sindi_valor_consulta,
      "posicion_promedio"=>$posicion_promedio,
    );
    
    $this->load->view('application',$data);
  }

  function sincronizar() {
    header('Access-Control-Allow-Origin: *');
    set_time_limit(0);
    ob_flush();
    $version = ($this->input->post("version") !== FALSE) ? $this->input->post("version") : "1";
    $id_empresa = ($this->input->post("id_empresa") !== FALSE) ? $this->input->post("id_empresa") : -1; // Con esto indicamos que tenemos que buscar la empresa
    $dispositivo_string = $this->input->post("dispositivo");
    $id_vendedor = $this->input->post("id_vendedor");
    $id_sucursal = 0;
    $lista_precios = 0;

    if (!empty($dispositivo_string) && $dispositivo_string != "0") {
      $this->load->model("Dispositivo_Model");
      $dispositivo = $this->Dispositivo_Model->get_by_dispositivo($dispositivo_string);
      if ($dispositivo === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Error: Dispositivo no encontrado.",
        ));
        return;
      }
      $id_empresa = $dispositivo->id_empresa;
    } elseif (!empty($id_vendedor)) {
      $id_vendedor = (int) $id_vendedor;
      $this->load->model("Vendedor_Model");  
      $vendedor = $this->Vendedor_Model->get($id_vendedor,array(
        "id_empresa"=>$id_empresa,
      ));
      file_put_contents("log_sincronizar_app.txt", date("Y-m-d H:i:s")." Vendedor: $vendedor->nombre ID_EMPRESA: $id_empresa \n", FILE_APPEND);
      if ($vendedor === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Error: Vendedor no encontrador",
        ));
        return;        
      }
      $id_empresa = $vendedor->id_empresa;
      $lista_precios = $vendedor->lista_defecto;
      $id_sucursal = $vendedor->id_sucursal; // Ponemos que tome la sucursal del vendedor
    } else {
      file_put_contents("log_sincronizar_app.txt", "ENTRO NINGUNO DE LOS DOS \n", FILE_APPEND);
    }
    $this->load->model("Articulo_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Empresa_Model");
    $salida = "";
    $salida.= $this->Articulo_Model->sincronizar_app(array(
      "id_empresa"=>$id_empresa,
      "version"=>$version,
      "id_sucursal"=>$id_sucursal,
      "lista_precios"=>$lista_precios,
    ));

    // Si los vendedores de la empresa comparten clientes
    // TODO: Hacer esto dinamico despues
    $comparte_clientes = (($id_empresa == 972) ? 0 : 1);
    $conf_clientes = array(
      "id_empresa"=>$id_empresa,
      "version"=>$version,
    );
    if ($comparte_clientes == 0) {
      $conf_clientes["id_vendedor"] = $id_vendedor;
    }
    $salida.= $this->Cliente_Model->sincronizar_app($conf_clientes);

    // OPCIONES ESPECIFICAS DESDE LA VERSION 3
    if ($version >= 3) {

      // Vendedores empresas
      $sep = ";;;";

      // SI ES DON YEYO
      $ids_don_yeyo = $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo());
      if (in_array($id_empresa, $ids_don_yeyo)) {
        // Recorremos los IDS
        foreach($ids_don_yeyo as $id_don_yeyo) {
          if ($id_don_yeyo == 980) continue;
          $emp_don_yeyo = $this->Empresa_Model->get_min($id_don_yeyo);
          // Y formamos la salida
          $salida.= "empresas".$sep.$id_don_yeyo.$sep.$emp_don_yeyo->nombre."\n";
        }
      } else {
        $this->load->model("Empresa_Model");
        $empresa = $this->Empresa_Model->get_min($id_empresa);
        $salida.= "empresas".$sep.$empresa->id.$sep.$empresa->nombre."\n";
      }

      // Mandamos todas las facturas que se marcaron como enviadas por el REPARTIDOR
      // para que en el listado de pedidos del vendedor aparezca un doble CHECK
      $desde = new DateTime("-1 month");
      $desde_f = $desde->format("Y-m-d");
      $sql = "SELECT * FROM facturas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND fecha >= '$desde_f' ";  // Tomamos un mes para atras, para no enviar todas juntas
      $sql.= "AND coordinar_envio >= 1 ";  // Solamente las marcadas que se entregaron
      $sql.= "AND id_vendedor = $id_vendedor "; // Y solamente por ese vendedor
      $q = $this->db->query($sql);
      foreach($q->result() as $r) {
        if ($version <= 4) {
          $salida.= "facturas".$sep.$r->numero_referencia.$sep.$r->id_vendedor.$sep.$r->id_empresa."\n";
        } else if ($version >= 5) {
          $r->observaciones = str_replace("\n", "", $r->observaciones);
          $salida.= "facturas".$sep.$r->coordinar_envio.$sep.$r->observaciones.$sep.$r->numero_referencia.$sep.$r->id_vendedor.$sep.$r->id_empresa."\n";
        }
      }

      if ($version >= 4) {
        $sql = "SELECT * FROM lista_precios_configuracion WHERE id_empresa = $id_empresa ";
        $q = $this->db->query($sql);
        if ($q->num_rows() > 0) {
          $c = $q->row();
          $lista_1 = $c->lista_1_nombre;
          $lista_2 = $c->lista_2_nombre;
          $lista_3 = $c->lista_3_nombre;
          $lista_4 = $c->lista_4_nombre;
          $lista_5 = $c->lista_5_nombre;
          $lista_6 = $c->lista_6_nombre;
        } else {
          $lista_1 = "Lista 1";
          $lista_2 = "Lista 2";
          $lista_3 = "Lista 3";
          $lista_4 = "Lista 4";
          $lista_5 = "Lista 5";
          $lista_6 = "Lista 6";
        }
        // Tabla de configuracion
        $salida.= "configuracion".$sep."1".$sep.$vendedor->limite_descuento.$sep.$lista_1.$sep.$lista_2.$sep.$lista_3.$sep.$lista_4.$sep.$lista_5.$sep.$lista_6."\n";
      }

      // Tabla de descuentos de articulos
      if ($version >= 8) {
        if ($id_empresa == 972) {
          // Los productos de BASILE tienen todos un descuento por monto fijo a partir de determinadas cantidades
          $sql = "SELECT * FROM articulos WHERE id_empresa = $id_empresa AND lista_precios > 0 ";
          $q = $this->db->query($sql);
          $ii = 0;
          foreach($q->result() as $row) {
            $salida.= "articulos_descuentos".$sep.$ii.$sep.$row->id.$sep."100".$sep."300".$sep."0".$sep."10"."\n";
            $ii++;
            $salida.= "articulos_descuentos".$sep.$ii.$sep.$row->id.$sep."301".$sep."99999".$sep."0".$sep."15"."\n";
            $ii++;
          }
        }
      }
    }

    file_put_contents("app_sincronizar.txt", $salida);

    echo $salida;
    flush();
  }
    
  function check_supervisor() {
    $codigo = trim($this->input->post("codigo"));
    $id_empresa = trim($this->input->post("id_empresa"));
    $sql = "SELECT * FROM empresas WHERE id = $id_empresa AND supervisor = '$codigo'";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      echo json_encode(array("error"=>0));
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Codigo incorrecto."
      ));
    }        
  }
  
  function get_info_dashboard() {
    
    $this->load->model("Factura_Model");
    $this->load->model("Cliente_Model");
    $this->load->model("Articulo_Model");
    $this->load->model("Log_Model");
    $this->load->helper("fecha_helper");
    $id_empresa = $_SESSION["id_empresa"];
    $estado = ($_SESSION["estado"] == 1 ? 1 : 0);
    $desde = $this->input->post("desde");
    $hasta = $this->input->post("hasta");
    if ($desde === FALSE) $desde = date("d-m-Y");
    if ($hasta === FALSE) $hasta = date("d-m-Y");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);
    
    $datos = array();
    $datos["facturacion"] = $this->Factura_Model->get_between_days($desde,$hasta,$estado);
    
    $datos["cantidad_articulos"] = $this->Articulo_Model->count_actives();
    $datos["cantidad_clientes"] = $this->Cliente_Model->count_actives();
    
    $datos["ultimos_comprobantes"] = $this->Factura_Model->get_all(0,5);
    
    $datos["actividades"] = $this->Log_Model->get_recent_activity(10);
    
    echo json_encode($datos);
  }

  function get_info_classvar_dashboard($id_empresa) {
    
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();

    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];

    // Ultimos comentarios
    $this->load->model("Comentario_Model");
    $comentarios = $this->Comentario_Model->buscar(array(
      "offset"=>3
    ));
    $datos["comentarios"] = $comentarios["results"];

    // Total de consultas
    $datos["total_consultas"] = $this->Consulta_Model->count_all();

    // Total de entradas
    $this->load->model("Entrada_Model");
    $datos["total_entradas"] = $this->Entrada_Model->count_all();    

    // CANTIDAD DE VISITAS
    $datos["total_sesiones"] = 0;
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (!empty($conf->view_id)) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos["view_id"] = $conf->view_id;
      $datos = $this->calcular_visitas($datos);
    }

    // Total de visitas
    $datos["total_visitas"] = 0;
    
    echo json_encode($datos);
  }  
  
  function get_info_inforvar_dashboard($id_empresa) {
    
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();

    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];

    // Ultimos comentarios
    $this->load->model("Comentario_Model");
    $comentarios = $this->Comentario_Model->buscar(array(
      "offset"=>3
    ));
    $datos["comentarios"] = $comentarios["results"];

    // Total de consultas
    $datos["total_consultas"] = $this->Consulta_Model->count_all();

    // Total de entradas
    $this->load->model("Entrada_Model");
    $datos["total_entradas"] = $this->Entrada_Model->count_all();    

    // CANTIDAD DE VISITAS
    $datos["total_sesiones"] = 0;
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (!empty($conf->view_id)) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos["view_id"] = $conf->view_id;
      $datos = $this->calcular_visitas($datos);
    }

    // Total de visitas
    $datos["total_visitas"] = 0;
    
    echo json_encode($datos);
  }
  
  function get_info_docvar_dashboard($id_empresa) {
    
    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];
    
    echo json_encode($datos);
  }
  
  function get_info_colvar_dashboard($id_empresa) {
    
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();

    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];

    // CANTIDAD DE VISITAS
    $datos["total_sesiones"] = 0;
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (!empty($conf->view_id)) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos["view_id"] = $conf->view_id;
      $datos = $this->calcular_visitas($datos);
    }
    
    echo json_encode($datos);
  }  
  

  function get_info_clienapp_dashboard($id_empresa) {
    
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();

    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];

    // CANTIDAD DE VISITAS
    $datos["total_sesiones"] = 0;
    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (!empty($conf->view_id)) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos["view_id"] = $conf->view_id;
      $datos = $this->calcular_visitas($datos);
    }
    
    echo json_encode($datos);
  }  

  
  function get_info_shopvar_dashboard($id_empresa,$id_usuario=0) {
    @session_start();
    $this->load->helper("fecha_helper");
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();   

    $usuarios_registrados = ($this->input->post("usuarios_registrados") !== FALSE) ? $this->input->post("usuarios_registrados") : 0; 
    
    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa, view_id ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();

    $sql = "SELECT COUNT(*) AS total ";
    $sql.= "FROM articulos WHERE id_empresa = $id_empresa AND lista_precios > 0 ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $datos["total_articulos"] = (is_null($r->total) ? 0 : $r->total);
    
    // Ultimos pedidos
    $this->load->model("Pedido_Model");
    $datos["pedidos"] = $this->Pedido_Model->get_all(array(
      "offset"=>6,
      "in_ids_estados"=>"1-2-3-4-5-6-8-9-10", // Todos menos en proceso o cancelado
      "id_usuario"=>$id_usuario,
    ));
    foreach($datos["pedidos"] as $r) {
      $r->stamp = fecha_mysql($r->fecha)." ".$r->hora;
    }
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>(($usuarios_registrados == 1)?"0":"20").",18,32",
      "tipo"=>0,
      "offset"=>6,
      "id_usuario"=>$id_usuario,
    ));
    $datos["consultas"] = $consultas["results"];
    foreach($datos["consultas"] as $r) {
      $r->stamp = fecha_mysql($r->fecha)." ".$r->hora.":00";
    }
    $datos["total_consultas"] = $this->Consulta_Model->total_consultas();

    // Forma de pago
    $sql = "SELECT * FROM medios_pago_configuracion WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      $datos["configurar_metodo_pago"] = (empty($r->mp_client_id) ? 0 : 1);
    } else {
      $datos["configurar_metodo_pago"] = 0;
    }

    // Metodo de envio
    $sql = "SELECT * FROM env_configuracion WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      $datos["configurar_forma_envio"] = (empty($r->forma_envio) ? 0 : 1);
    } else {
      $datos["configurar_forma_envio"] = 0;
    }

    // Sumamos los pedidos finalizados
    /*
    $sql = "SELECT IF(SUM(total) IS NULL,0,SUM(total)) AS total, ";
    $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_tipo_estado IN (6,5,4,8,9,10) ";
    if (!empty($id_usuario)) $sql.= "AND id_usuario = $id_usuario ";
    $q = $this->db->query($sql);
    $row = $q->row();
    $datos["total_ventas"] = $row->total;
    $datos["cantidad_ventas"] = $row->cantidad;

    /*
    $datos["total_sesiones"] = 0;
    if (!empty($datos["view_id"])) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos = $this->calcular_visitas($datos);
    }
    */

    // Si tiene el sistema de turnos
    $turnos = ($this->input->post("turnos") !== FALSE) ? $this->input->post("turnos") : 0;
    if ($turnos == 1) {
      // Contamos la cantidad de turnos
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad_turnos ";
      $sql.= "FROM turnos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      if ($id_usuario != 0) $sql.= "AND id_usuario = $id_usuario ";
      $q = $this->db->query($sql);
      $t = $q->row();
      $datos["cantidad_turnos"] = $t->cantidad_turnos;

      // Contamos la cantidad de clientes distintos
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad_clientes ";
      $sql.= "FROM clientes ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND tipo = 1 AND activo = 1 ";
      $q = $this->db->query($sql);
      $t = $q->row();
      $datos["cantidad_clientes"] = $t->cantidad_clientes;
    }

    echo json_encode($datos);
  }
  


  function get_info_tripvar_dashboard($id_empresa) {

    @session_start();
    $this->load->helper("fecha_helper");
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();   

    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa, view_id ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    // Ultimos pedidos
    $this->load->model("Reserva_Model");
    $s = $this->Reserva_Model->buscar(array(
      "offset"=>6,
    ));
    $datos["pedidos"] = $s["results"];
    foreach($datos["pedidos"] as $r) {
      $r->stamp = fecha_mysql($r->fecha_reserva);
    }
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "tipo"=>0,
      "offset"=>6,
    ));
    $datos["consultas"] = $consultas["results"];
    foreach($datos["consultas"] as $r) {
      $r->stamp = fecha_mysql($r->fecha)." ".$r->hora.":00";
    }

    // Sumamos los pedidos finalizados
    $sql = "SELECT IF(SUM(precio) IS NULL,0,SUM(precio)) AS precio, ";
    $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM hot_reservas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row = $q->row();
    $datos["total_ventas"] = $row->precio;
    $datos["cantidad_ventas"] = $row->cantidad;

    $datos["total_sesiones"] = 0;
    if (!empty($datos["view_id"])) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos = $this->calcular_visitas($datos);
    }

    echo json_encode($datos);
  }

  
  function get_info_inmovar_dashboard($id_empresa) {

    $desde = new DateTime("-1 month");
    $hasta = new DateTime();
    $datos = array();
    
    $this->load->model("Propiedad_Model");
    $datos["total_propiedades"] = $this->Propiedad_Model->count_all();
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];
    $datos["total_consultas"] = $this->Consulta_Model->count_all();

    echo json_encode($datos);
  }

  function calcular_visitas($datos = array()) {
    try {
      $fecha_desde = $datos["desde"];
      $fecha_hasta = $datos["hasta"];
      $view_id = $datos["view_id"];
    
      set_include_path(get_include_path().PATH_SEPARATOR.APPPATH.'libraries/Google/');
      require APPPATH.'libraries/Google/Client.php';
      require APPPATH.'libraries/Google/Service/Analytics.php';
      
      $client_id = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q.apps.googleusercontent.com'; //Client ID
      $service_account_name = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q@developer.gserviceaccount.com'; //Email Address 
      $key_file_location = APPPATH.'libraries/Google/key.p12';
      
      $client = new Google_Client();
      $client->setApplicationName("Client_Library_Examples");
      $service = new Google_Service_Analytics($client);
      
      if (isset($_SESSION['service_token'])) {
        $client->setAccessToken($_SESSION['service_token']);
      }
      $key = file_get_contents($key_file_location);
      $cred = new Google_Auth_AssertionCredentials(
        $service_account_name,
        array('https://www.googleapis.com/auth/analytics.readonly'),
        $key
      );
      $client->setAssertionCredentials($cred);
      if($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($cred);
      }
      $_SESSION['service_token'] = $client->getAccessToken();
      
      $view_id = "ga:".$view_id;
      
      // SESIONES
      $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:sessions');
      if (count($results->getRows()) > 0) {
        $rows = $results->getRows();
        $datos["total_sesiones"] = $rows[0][0];
      }
      
    } catch(Exception $e) {
      $datos["exception"] = $e->getMessage();
    }  
    return $datos;
  }


  function get_info_viajes_dashboard($id_empresa) {

    @session_start();
    $desde = new DateTime("-1 month");
    $hasta = new DateTime();
    
    // Obtenemos de la configuracion de la web los parametros que muestran u ocultan los cartelitos de informacion
    $sql = "SELECT sin_pagos, sin_envios, configurar_disenio, subir_elemento, datos_empresa, view_id ";
    $sql.= "FROM web_configuracion WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $datos = $q->row_array();
    
    $this->load->model("Viaje_Model");
    $this->load->helper("fecha_helper");
    $datos["total_viajes"] = $this->Viaje_Model->count_all();

    // Ultimos pedidos
    $this->load->model("Reserva_Asiento_Model");
    $datos["reservas"] = $this->Reserva_Asiento_Model->get_all(array(
      "offset"=>6,
      "in_ids_estados"=>"0-1-2-3-4-5-6-8-9-10", // Todos menos en proceso o cancelado
    ));
    foreach($datos["reservas"] as $r) {
      $r->stamp = fecha_mysql($r->fecha_reserva);
    }
    
    // Ultimas consultas
    $this->load->model("Consulta_Model");
    $consultas = $this->Consulta_Model->buscar(array(
      "not_ids_origen"=>"20,18,32,13",
      "tipo"=>0,
      "offset"=>3
    ));
    $datos["consultas"] = $consultas["results"];
    $datos["total_consultas"] = $this->Consulta_Model->count_all();

    // CANTIDAD DE VISITAS
    $datos["total_sesiones"] = 0;
    if (!empty($datos["view_id"])) {
      $datos["desde"] = $desde->format("Y-m-d");
      $datos["hasta"] = $hasta->format("Y-m-d");
      $datos = $this->calcular_visitas($datos);
    }
    
    echo json_encode($datos);
  }
  
  /**
   * GENERA TODOS LOS ARCHIVOS NECESARIOS, PARA TODOS LOS PROYECTOS
   */
  function compress() {
    
    set_time_limit(0);
    
    // PARA EL ADMINISTRADOR O VENDEDORES
    $this->compress_js(0);
    
    // PARA CADA UNO DE LOS PROYECTOS
    $this->load->model("Proyecto_Model");
    $proyectos = $this->Proyecto_Model->get_all();
    foreach($proyectos as $p) {
      echo $p->id."<br/>";
      $this->compress_js($p->id);
    }
    
    // Actualizamos la version del JS
    $this->db->query("UPDATE com_configuracion SET version_js = version_js + 1 WHERE id = 1");
    
    // CSS
    $this->compress_css();
    
    echo "TERMINO";
  }
  
  
  /**
   * Comprime todos los archivos JS en un unico archivo para mejorar la performance
   */
  function compress_js($id_proyecto = 0) {
    
    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen el HTML del panel

    $path =  APPPATH.'libraries';
    require_once $path . '/minify/src/Minify.php';
    require_once $path . '/minify/src/JS.php';
    $minifier = new JS();
    $array = $this->js_files($id_proyecto);
    foreach($array as $a) {
      $minifier->add($a);
    }
    file_put_contents("application/javascript/min_$id_proyecto.js",$minifier->minify().";");
    
    if ($id_proyecto > 0) {
      
      // Por algun motivo, con Minify no puedo agregar los otros modulos
      // Entonces lo que hago es simplemente eliminar los comentarios y espacios en blanco
      // y lo agrego al final del archivo
      
      $salida = "";
      $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
      
      // Seleccionamos los modulos de los proyectos
      $this->load->model("Proyecto_Model");
      $modulos = $this->Proyecto_Model->get_modulos($id_proyecto);
      foreach($modulos as $r) {
        $minifier = new JS();
        // Excluir los siguientes
        if ($r->nombre == "mis_datos") continue;
        
        $path = "application/javascript/modules/";
        if (!empty($r->dir)) $path.= "$r->dir/";
        $path.= $r->nombre.".js";
        if (!file_exists($path)) continue;
        if (in_array($path,$array)) continue; // Ya fue incluido
        
        $archivo = file_get_contents($path);
        $archivo = preg_replace($pattern, '', $archivo);
        $minifier->add($archivo);
        $salida.= $minifier->minify().";";
      }
      
      file_put_contents("application/javascript/min_$id_proyecto.js",$salida,FILE_APPEND);
    }
  }
  
  private function js_files($id_proyecto = 0) {
    
    // Librerias comunes a todos los proyectos
    $array = array(    
      //"resources/js/jquery.min.js",
      "resources/js/jquery-2.2.4.min.js",
      "resources/js/application.js", // TODO: Reemplazar esto
      "resources/js/jquery/ui/jquery-ui.min.js",
      "resources/js/common.js",
      "resources/js/backbone.paginator.js",
      "resources/js/jquery.dynatree.min.js",
      "resources/js/jquery.simplemodal.js",
      "resources/js/jquery-fieldselection.js",
      "resources/js/jquery.maskedinput.js",
      "resources/js/html5-file-upload/js/jquery.filedrop.js",
      "resources/js/jquery.scrollTo-1.4.3.1-min.js",
      "resources/js/jquery.ajaxq-0.0.1.js",
      "resources/js/libs/bootstrap.min.js",
      "resources/js/libs/screenfull.min.js",
      "resources/js/jquery/jquery.tablednd.0.6.min.js",
      "resources/js/jquery/highcharts.js",
      "resources/js/jquery/chosen/chosen.jquery.min.js",
      "resources/js/jquery/touchspin/jquery.bootstrap-touchspin.min.js",
      "resources/js/jquery/select2/select2.full.min.js",
      "resources/js/jquery/select2/i18n/es.js",
      "resources/js/app/map/load-google-maps.js",
      "resources/js/moment.min.js",
      "resources/js/libs/fancytree/jquery.fancytree.min.js",
      "resources/js/cropper.min.js",
      "resources/js/cropper-main.js",
      "resources/js/jspdf.min.js",
      "resources/js/html2canvas.min.js",
      "resources/js/jquery-ui-timepicker-addon.js",
      "resources/js/jquery.countTo.js",
      "resources/js/libs/colorpicker/js/bootstrap-colorpicker.min.js",
      "resources/js/libs/camanjs/caman.full.min.js",
      "resources/js/jquery/nestable/jquery.nestable.js",
      "resources/js/fullcalendar.min.js",
      "resources/js/jquery/jquery-ui-multiselect/src/jquery.multiselect.min.js",
      "resources/js/jquery/jquery-ui-multiselect/src/jquery.multiselect.filter.min.js",
      "resources/js/jquery/jquery-ui-multiselect/i18n/jquery.multiselect.es.js",
      "resources/js/jquery/jquery-ui-multiselect/i18n/jquery.multiselect.filter.es.js",
      "resources/js/scheduler.min.js",
      "resources/js/fullcalendar-locale/es.js",
      "resources/js/jquery.cookie.js",
      "resources/js/jquery/jquery.toaster.js",
      "resources/js/jquery/contextmenu/jquery.contextMenu.min.js",

      "resources/js/jquery/upload/js/jquery.iframe-transport.js",
      "resources/js/jquery/upload/js/jquery.fileupload.js",
      "resources/js/owl.carousel.min.js",
      "resources/js/jquery.flexslider.js",
      
      // PUNTO DE ENTRADA A LA APLICACION
      "application/javascript/main.js",
    );
    
    // Cargamos todos los MIXINS (estan en todos los proyectos)
    foreach (glob("application/javascript/mixins/*.js") as $filename){
      $array[] = $filename;
    }
    
    // Estos modulos estan en TODOS los proyectos
    $array[] = 'application/javascript/modules/inicio.js';
    $array[] = 'application/javascript/modules/image_editor.js';
    $array[] = 'application/javascript/modules/image_gallery.js';
    $array[] = 'application/javascript/modules/image_upload.js';
    $array[] = 'application/javascript/modules/empresas.js';
    $array[] = 'application/javascript/modules/importacion.js';
    $array[] = 'application/javascript/modules/notificaciones.js';
    $array[] = 'application/javascript/modules/monedas.js';
    $array[] = 'application/javascript/modules/provincias.js';
    $array[] = 'application/javascript/modules/localidades.js';
    $array[] = 'application/javascript/modules/tarjetas.js';
    $array[] = 'application/javascript/modules/crm/emails_templates.js';
    $array[] = 'application/javascript/modules/crm/consultas.js';
    $array[] = 'application/javascript/modules/crm/eventos.js';
    $array[] = 'application/javascript/modules/crm/tareas.js';
    $array[] = 'application/javascript/modules/crm/origenes.js';
    $array[] = 'application/javascript/modules/not/entradas.js';
    $array[] = 'application/javascript/modules/web/web_textos.js';
    $array[] = 'application/javascript/modules/gal/galerias_imagenes.js';
    $array[] = 'application/javascript/modules/gal/galerias_etiquetas.js';
    $array[] = 'application/javascript/modules/gal/galerias_categorias.js';
    $array[] = 'application/javascript/modules/clientes.js';
    $array[] = 'application/javascript/modules/crm/contactos.js';
    $array[] = 'application/javascript/modules/usuarios.js';
    $array[] = 'application/javascript/modules/perfiles.js';
    $array[] = 'application/javascript/modules/facturacion.js';
    $array[] = 'application/javascript/modules/recibos_clientes.js';
    $array[] = 'application/javascript/modules/web/web_configuracion.js';
    $array[] = 'application/javascript/modules/cajas.js';
    $array[] = 'application/javascript/modules/cajas_movimientos.js';
    $array[] = 'application/javascript/modules/gastos.js';
    $array[] = 'application/javascript/modules/tipos_gastos.js';
    $array[] = 'application/javascript/modules/config/mi_cuenta.js';
    $array[] = 'application/javascript/modules/categorias_entrena.js';
    
    // Modulos especificos por cada proyecto
    
    if ($id_proyecto == 1) {
    
    } else if ($id_proyecto == 2) {
      
    } else if ($id_proyecto == 3) {
      
      $array[] = 'application/javascript/modules/inm/propiedades.js';
      $array[] = 'application/javascript/modules/inm/dashboard.js';
      $array[] = 'application/javascript/modules/inm/tipos_operacion.js';
      $array[] = 'application/javascript/modules/inm/tipos_inmueble.js';
      $array[] = 'application/javascript/modules/inm/tipos_estado.js';

    } else if ($id_proyecto == 5) {
      
      $array[] = 'application/javascript/modules/aca/asistencias.js';
      $array[] = 'application/javascript/modules/aca/asistencias_docentes.js';
      $array[] = 'application/javascript/modules/aca/examenes.js';

    } else if ($id_proyecto == 10) {

      $array[] = "resources/js/jquery/jquery.countdown.min.js";

    } else if ($id_proyecto == 11) {
      
      $array[] = 'application/javascript/modules/via/dashboard.js';
      
    } else if ($id_proyecto == 0) {
      
      $array[] = 'application/javascript/modules/bancos.js';
      $array[] = 'application/javascript/modules/monedas.js';
      $array[] = 'application/javascript/modules/planes.js';
      $array[] = 'application/javascript/modules/proyectos.js';
      $array[] = 'application/javascript/modules/degenerator.js';
      $array[] = 'application/javascript/modules/inm/tipos_operacion.js';
      $array[] = 'application/javascript/modules/inm/tipos_inmueble.js';
      $array[] = 'application/javascript/modules/inm/tipos_estado.js';
      $array[] = 'application/javascript/modules/ped/tipos_estado_pedidos.js';
      $array[] = 'application/javascript/modules/not/publicidades_tipos.js';
      $array[] = 'application/javascript/modules/versiones_db.js';
      $array[] = 'application/javascript/modules/web/web_templates.js';
      $array[] = 'application/javascript/modules/web/web_textos.js';
      $array[] = 'application/javascript/modules/clasif/tipos_vehiculos.js';
      $array[] = 'application/javascript/modules/config/videos.js';
      
    }    
    
    return $array;
  }
  

  // Obtiene los ultimos IDS de las tablas
  // Sirve para actualizar una caja restaurada y que despues cuando se suban las ventas
  // no se generen conflictos con los IDS
  function get_max_ids() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta JSON

    $id_empresa = $this->input->get("id_empresa");
    $punto_venta = $this->input->get("punto_venta");

    $sql = "SELECT * FROM puntos_venta WHERE id_empresa = $id_empresa AND numero = $punto_venta ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el punto de venta."
      ));
      exit();
    }
    $pv = $q->row();
    $id_punto_venta = $pv->id;

    $salida = "";
    $tablas = array("facturas", "facturas_items", "caja_diaria", "cupones_tarjetas");
    foreach($tablas as $t) {
      $sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id FROM $t WHERE id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $proximo = $r->id + 1;
      $s = "ALTER TABLE $t AUTO_INCREMENT = $proximo; ";
      $salida.= $s;
    }
    echo $salida;
    /*
    echo json_encode(array(
      "error"=>0,
      "datos"=>$salida,
    ));
    */
  }
  
  /**
   * COMPRIME TODOS LOS ARCHIVOS CSS EN UN UNICO ARCHIVO "resources/css/min.css"
   */
  function compress_css() {
    
    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen el HTML del panel

    $path =  APPPATH.'libraries';
    require_once $path . '/minify/src/Minify.php';
    require_once $path . '/minify/src/CSS.php';
    $minifier = new CSS();
    $array = $this->css_files();
    foreach($array as $a) {
      $minifier->add($a);
    }
    file_put_contents("resources/css/min.css",$minifier->minify());
    echo "TERMINO";      
  }
  
  /**
   * ARRAY CON LOS ARCHIVOS CSS QUE SE DEBEN INCLUIR EN EL PROYECTO
   */
  private function css_files() {
    $array = array(    
      "resources/css/common.css",
      "resources/css/bootstrap.css",
      "resources/css/animate.css",
      "resources/css/simple-line-icons.css",
      //"resources/css/font.css",
      "resources/fonts/lato/lato.css",
      "resources/css/app.css?v=11",
      "resources/js/jquery/ui/jquery-ui.min.css",
      "resources/css/tablednd.css",
      "resources/css/footable/footable.core.css",
      "resources/css/loader.css",
      "resources/js/jquery/chosen/chosen.css",
      "resources/js/jquery/select2/select2.css",
      "resources/js/libs/fancytree/skin-win7/ui.fancytree.min.css",
      "resources/js/jquery/touchspin/jquery.bootstrap-touchspin.css",
      "resources/css/sortable.css",
      "resources/css/cropper.min.css",
      "resources/css/cropper.css",
      "resources/css/jquery-ui-timepicker-addon.css",
      "resources/js/libs/colorpicker/css/bootstrap-colorpicker.min.css",
      "resources/js/jquery/nestable/nestable.css",
      //"resources/js/jquery/fullcalendar/fullcalendar.css",
      "resources/css/fullcalendar.min.css",
      "resources/css/scheduler.min.css",
      "resources/js/jquery/jquery-ui-multiselect/jquery.multiselect.css",
      "resources/js/jquery/jquery-ui-multiselect/jquery.multiselect.filter.css",
      "resources/js/jquery/upload/css/jquery.fileupload.css",
      "resources/js/jquery/contextmenu/jquery.contextMenu.min.css",
      "resources/css/owl.carousel.min.css",
      "resources/css/flexslider.css",
    );
    return $array;
  }

  // Esta funcion es llamada periodicamente para mantener viva la session
  function refresh_session() {
    @session_start();
    echo json_encode(array("error"=>0));
  }
  

  function degenerator() {

    $singular = $this->input->post("singular");
    $plural = $this->input->post("plural");
    $carpeta = $this->input->post("carpeta");
    $base = "application/views/degenerator/";
    $tagfontawesome = $this->input->post("tagfontawesome");
    $tagprincipal =  $this->input->post("tagprincipal");
    $tagnombre =  $this->input->post("tagnombre");
    
    //Control de tags
    $tagfontawesome = (empty($tagfontawesome)?"fa-tags":$tagfontawesome);
    $tagprincipal = (empty($tagprincipal)?"":$tagprincipal." /");
   
    //Deconstruccion y Reconstruccion de los strings
    $singularminusculasyguion = str_replace(" ","_",$singular);
    $pluralminusculasyguion = str_replace(" ","_",$plural); 
    $singularcamelcaseyguion = str_replace(" ", "_", ucwords($singular)); 
    $pluralcamelcaseyguion = str_replace(" ", "_", ucwords($plural));  
    $singularminusculas = str_replace(" ", "",$singular); 
    $pluralminusculas = str_replace(" ", "",$plural);
    $singularcamelcase = str_replace(" ", "", ucwords($singular)); 
    $pluralcamelcase = str_replace(" ", "", ucwords($plural)); 

    $nombre_archivo1 = $singularminusculasyguion."_model.php"; 
    $nombre_archivo2 = $pluralminusculasyguion.".php"; 
    $nombre_archivo3 = $pluralminusculasyguion.".js"; 
    $nombre_archivo4 = $pluralminusculasyguion.".php"; 

    if (file_exists("application/models/".$nombre_archivo1) 
      || file_exists("application/controllers/".$nombre_archivo2)
      || file_exists("application/javascript/modules/".((empty($carpeta))?"":$carpeta."/").$nombre_archivo3)
      || file_exists("application/views/templates/".((empty($carpeta))?"":$carpeta."/").$nombre_archivo4)
    ) {
      echo json_encode(array(
        "salida"=>"ERROR: el archivo ya existe en el sistema.",
      ));
      exit();
    }

    //Archivo Modelo PHP
    $archivo1 = file_get_contents($base."model.php");
    $archivo1 = str_replace("singularcamelcaseyguion", $singularcamelcaseyguion, $archivo1);
    $archivo1 = str_replace("pluralminusculasyguion", $pluralminusculasyguion, $archivo1);
    file_put_contents("application/models/".$nombre_archivo1, $archivo1);

    //Archivo Controller PHP
    $archivo2 = file_get_contents($base."controller.php");
    $archivo2 = str_replace("pluralcamelcaseyguion", $pluralcamelcaseyguion, $archivo2);
    $archivo2 = str_replace("singularcamelcaseyguion", $singularcamelcaseyguion, $archivo2);
    file_put_contents("application/controllers/".$nombre_archivo2, $archivo2);

    //Archivo Modulo JS
    $archivo3 = file_get_contents($base."modulos.js");
    $archivo3 = str_replace("singularminusculasyguion", $singularminusculasyguion, $archivo3);
    $archivo3 = str_replace("pluralminusculasyguion", $pluralminusculasyguion, $archivo3);
    $archivo3 = str_replace("singularcamelcase", $singularcamelcase, $archivo3);
    $archivo3 = str_replace("pluralcamelcase", $pluralcamelcase, $archivo3);
    file_put_contents("application/javascript/modules/".((empty($carpeta))?"":$carpeta."/").$nombre_archivo3, $archivo3);

    //Archivo Template PHP
    $archivo4 = file_get_contents($base."template.php");
    $archivo4 = str_replace("singularminusculasyguion", $singularminusculasyguion, $archivo4);
    $archivo4 = str_replace("pluralminusculasyguion", $pluralminusculasyguion, $archivo4);
    $archivo4 = str_replace("TAGFONTAWESOME", $tagfontawesome, $archivo4);
    $archivo4 = str_replace("TAGPRINCIPAL", $tagprincipal, $archivo4);
    $archivo4 = str_replace("TAGNOMBRE", $tagnombre, $archivo4);
    file_put_contents("application/views/templates/".((empty($carpeta))?"":$carpeta."/").$nombre_archivo4, $archivo4);

    //Para agregar al Main.js
    $main_definicion = file_get_contents($base."base.txt");
    $main_definicion = str_replace("1", '      "'.$pluralminusculasyguion.'": "ver_'.$pluralminusculasyguion.'",', $main_definicion);
    $main_definicion = str_replace("2", '      "'.$singularminusculasyguion.'": "ver_'.$singularminusculasyguion.'",', $main_definicion);
    $main_definicion = str_replace("3", '      "'.$singularminusculasyguion.'/:id": "ver_'.$singularminusculasyguion.'",', $main_definicion);
    
    $main_implementacion = file_get_contents($base."main.txt");
    $main_implementacion = str_replace("singularminusculasyguion", $singularminusculasyguion, $main_implementacion);
    $main_implementacion = str_replace("pluralminusculasyguion", $pluralminusculasyguion, $main_implementacion);
    $main_implementacion = str_replace("singularcamelcase", $singularcamelcase, $main_implementacion);
    $main_implementacion = str_replace("pluralcamelcase", $pluralcamelcase, $main_implementacion);

    $main = file_get_contents("application/javascript/main.js");
    $main = str_replace("// NEXT_DEFINICION", $main_definicion, $main);
    $main = str_replace("// NEXT_IMPLEMENTACION", $main_implementacion, $main);
    file_put_contents("application/javascript/main.js", $main);

    echo json_encode(array(
      "salida"=>"OK",
    ));
  }

}
