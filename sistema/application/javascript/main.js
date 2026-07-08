var timer;
var paginas = new Array(); // En este array se van guardanlo los canvas a medidas que se van renderizando

(function(){

  window.app = {};
  app.collections = {};
  app.models = {};
  app.views = {};
  app.mixins = {};
  app.modules = {}; // Referencia a todos los modulos del sistema

  $(document).ready(function() {

    var Workspace = Backbone.Router.extend({
      
    // Define todas las rutas de la aplicacion
    routes: {
      "": "ver_index",
      "inicio": "ver_index",

      "ver_proyecto/:id":function(id) {
        this.ver_empresas(id);
      },
      "nuevo_proyecto/:id":function(id) {
        this.nueva_empresa(id);
      },
      
      "iva_ventas":"ver_iva_ventas",
      "iva_compras":"ver_iva_compras",
      "degenerator":"ver_degenerator",
      
      "configuracion": "ver_configuracion",
      "configuracion_facturacion":"ver_configuracion_facturacion",
      "editor_web":"ver_editor_web",
      "web_editor":"ver_web_editor",
      
      "articulos": "ver_articulos",
      "articulo": "ver_articulo",
      "articulo/:id": "ver_articulo",

      "importaciones_articulos": "ver_importaciones_articulos",
      "importacion_articulo/:id": "ver_importacion_articulo",

      "importacion/:tabla/:id": "ver_importacion",

      "ambientaciones": "ver_ambientaciones",
      "ambientacion": "ver_ambientacion",
      "ambientacion/:id": "ver_ambientacion",
      
      "consultas": "ver_consultas",
      "consultas/:id_origen": "ver_consultas",
      "consulta/:id": "ver_consulta",

      "webinars": "ver_webinars",
      "webinar": "ver_webinar",
      "webinar/:id": "ver_webinar",
      
      "entradas": "ver_entradas",
      "entradas/:id_categoria": "ver_entradas",
      "entrada": "ver_entrada",
      "entrada/:id": "ver_entrada",
      "nueva_entrada/:id_categoria": "ver_nueva_entrada",
      "entradas_papelera": "ver_entradas_papelera",

      "clasificados": "ver_clasificados",
      "clasificado": "ver_clasificado",
      "clasificado/:id": "ver_clasificado",

      "dispositivos": "ver_dispositivos",
      "dispositivo": "ver_dispositivo",
      "dispositivo/:id": "ver_dispositivo",

      "seo_urls": "ver_seo_urls",
      "seo_url": "ver_seo_url",
      "seo_url/:id": "ver_seo_url",

      "sectores": "ver_sectores",
      "sector": "ver_sector",
      "sector/:id": "ver_sector",

      "tipos_mantenimiento": "ver_tipos_mantenimiento",
      "tipo_mantenimiento": "ver_tipo_mantenimiento",
      "tipo_mantenimiento/:id": "ver_tipo_mantenimiento",

      "tipos_ordenes_trabajo": "ver_tipos_ordenes_trabajo",
      "tipo_orden_trabajo": "ver_tipo_orden_trabajo",
      "tipo_orden_trabajo/:id": "ver_tipo_orden_trabajo",

      "tipos_tareas": "ver_tipos_tareas",
      "tipo_tarea": "ver_tipo_tarea",
      "tipo_tarea/:id": "ver_tipo_tarea",

      "eventos": "ver_eventos",

      "alertas": "ver_alertas",
      
      "precios": "ver_precios",
      "mi_cuenta": "ver_mi_cuenta",

      "estadisticas_web": "ver_estadisticas_web",
      "estadisticas_consultas": "ver_estadisticas_consultas",
      "estadisticas_whatsapp": "ver_estadisticas_whatsapp",
      "estadisticas_ventas": "ver_estadisticas_ventas",
      "estadisticas_tarjetas": "ver_estadisticas_tarjetas",
      "estadisticas_articulos_web": "ver_estadisticas_articulos_web",
      "estadisticas_ventas_por_dia": "ver_estadisticas_ventas_por_dia",
      "estadisticas_sucursales": "ver_estadisticas_sucursales",
      "estadisticas_ventas_por_proveedor": "ver_estadisticas_ventas_por_proveedor",
      "estadisticas_compras_ventas": "ver_estadisticas_compras_ventas",
      "estadisticas_compras_ventas_por_articulos": "ver_estadisticas_compras_ventas_por_articulos",
      "estadisticas_ventas_por_departamento": "ver_estadisticas_ventas_por_departamento",
      "estadisticas_ventas_por_sucursal": "ver_estadisticas_ventas_por_sucursal",
      "estadisticas_compras": "ver_estadisticas_compras",
      "estadisticas_gastos": "ver_estadisticas_gastos",
      "estadisticas_pagos": "ver_estadisticas_pagos",
      "estadisticas_cobranzas": "ver_estadisticas_cobranzas",
      "estadisticas_resumen": "ver_estadisticas_resumen",
      "estadisticas_prestamos": "ver_estadisticas_prestamos",
      "estadisticas_prestamos_activos": "ver_estadisticas_prestamos_activos",
      "estadisticas_prestamos_tareas": "ver_estadisticas_prestamos_tareas",
      "estadisticas_publicidades": "ver_estadisticas_publicidades",
      "estadisticas_articulos_sucursales": "ver_estadisticas_articulos_sucursales",
      "mant_estadisticas": "ver_mant_estadisticas",

      "maquinas": "ver_maquinas",
      "maquina": "ver_maquina",
      "maquina/:id": "ver_maquina",

      "ver_consulta_precio":function(){
        var self = this;
        var view = new app.views.ArticulosMostrarPrecioView({
          collection: ((CACHE_ARTICULOS == 1) ? window.articulos : new app.collections.Articulos()),
          habilitar_seleccion: true,
        });   
        var d = $("<div/>").append(view.el);
        crearLightboxHTML({
          "html":d,
          "width":500,
          "height":500,
        });
        setTimeout(function(){
          $("#articulos_mostrar_precio_buscar").focus();
        },500);
      },
      
      "propiedades": "ver_propiedades",
      "propiedad": "ver_propiedad",
      "propiedad/:id": "ver_propiedad",
      "propiedades_similares/:id":"ver_propiedades",
      "propiedades_red/:id_empresa": function(id_empresa){
        window.propiedades_buscar_red = 1;
        window.propiedades_buscar_red_empresa = id_empresa;
        this.ver_propiedades();
      },

      "alquileres": "ver_alquileres",
      "alquiler": "ver_alquiler",
      "alquiler/:id": "ver_alquiler",      
      "recibos_alquileres": function() { this.ver_recibos_alquileres(0); },
      "recibos_alquileres/:estado": "ver_recibos_alquileres",
      
      "clasificados_propiedades": "ver_clasificados_propiedades",
      "clasificado_propiedad": "ver_clasificado_propiedad",
      "clasificado_propiedad/:id": "ver_clasificado_propiedad",

      "clasificados_autos": "ver_clasificados_autos",
      "clasificado_auto": "ver_clasificado_auto",
      "clasificado_auto/:id": "ver_clasificado_auto",

      "tdf_sorteos": "ver_tdf_sorteos",
      "tdf_sorteo": "ver_tdf_sorteo",
      "tdf_sorteo/:id": "ver_tdf_sorteo",
      
      "clasificados_objetos": "ver_clasificados_objetos",
      "clasificado_objeto": "ver_clasificado_objeto",
      "clasificado_objeto/:id": "ver_clasificado_objeto",

      "proveedores": "ver_proveedores",
      "proveedor": "ver_proveedor",
      "proveedor/:id": "ver_proveedor",

      "bobinas": "ver_bobinas",
      "bobina": "ver_bobina",
      "bobina/:id": "ver_bobina",
      "cargar_bobinas": "ver_cargar_bobinas",

      "tipos_bobinas": "ver_tipos_bobinas",
      "tipo_bobina": "ver_tipo_bobina",
      "tipo_bobina/:id": "ver_tipo_bobina",
      
      "clientes": "ver_clientes",
      "cliente": "ver_cliente",
      "cliente/:id": "ver_cliente",
      "contacto_acciones/:id": "ver_contacto_acciones",
      "cliente_acciones/:id": "ver_cliente_acciones",
      "alumno_acciones/:id": "ver_alumno_acciones",
      "docente_acciones/:id": "ver_docente_acciones",
      "tutor_acciones/:id": "ver_tutor_acciones",
      "paciente_acciones/:id": "ver_paciente_acciones",

      "pres_clientes": "ver_pres_clientes",
      "pres_clientes/:id_plan": "ver_pres_clientes",
      "pres_cliente": "ver_pres_cliente",
      "pres_cliente/:id": "ver_pres_cliente",
      "pres_cliente_acciones/:id": "ver_pres_cliente_acciones",
      "pres_cliente_acciones/:id/:tab": "ver_pres_cliente_acciones",

      "pres_garantes": "ver_pres_garantes",
      "pres_garante": "ver_pres_garante",
      "pres_garante/:id": "ver_pres_garante",

      "pres_documentaciones": "ver_pres_documentaciones",
      "pres_documentacion": "ver_pres_documentacion",
      "pres_documentacion/:id": "ver_pres_documentacion",

      "pres_planes_credito": "ver_pres_planes_credito",
      "pres_plan_credito": "ver_pres_plan_credito",
      "pres_plan_credito/:id": "ver_pres_plan_credito",
      "pres_listado_mora": "ver_pres_listado_mora",
      "pres_listado_reingreso": "ver_pres_listado_reingreso",
      "pres_buenos_clientes": "ver_pres_buenos_clientes",

      "razones_sociales": "ver_razones_sociales",
      "razon_social": "ver_razon_social",
      "razon_social/:id": "ver_razon_social",

      "profesionales": "ver_profesionales",
      "profesional": "ver_profesional",
      "profesional/:id": "ver_profesional",

      "turnos_servicios": "ver_turnos_servicios",
      "turno_servicio": "ver_turno_servicio",
      "turno_servicio/:id": "ver_turno_servicio",

      "mantenimientos": "ver_mantenimientos",

      "pacientes": "ver_pacientes",
      "paciente": "ver_paciente",
      "paciente/:id": "ver_paciente",

      "afiliados": "ver_afiliados",
      "afiliado": "ver_afiliado",
      "afiliado/:id": "ver_afiliado",
      
      "puntos_venta": "ver_puntos_venta",
      "punto_venta": "ver_punto_venta",
      "punto_venta/:id": "ver_punto_venta",

      "cajas_actualizadas": "ver_cajas_actualizadas",
      "cajas": "ver_cajas",
      "cajas/:todas": "ver_cajas",
      "caja": "ver_caja",
      "caja/:id": "ver_caja",

      "cipal_invitaciones": "ver_cipal_invitaciones",
      
      "marcas": "ver_marcas",
      "marca": "ver_marca",
      "marca/:id": "ver_marca",

      "toque_categorias": "ver_toque_categorias",
      "toque_categoria": "ver_toque_categoria",
      "toque_categoria/:id": "ver_toque_categoria",

      "milling_halloffames": "ver_milling_halloffames",
      "milling_halloffame": "ver_milling_halloffame",
      "milling_halloffame/:id": "ver_milling_halloffame",

      "cursos_categorias": "ver_cursos_categorias",
      "curso_categoria": "ver_curso_categoria",
      "curso_categoria/:id": "ver_curso_categoria",

      "cursos_evaluaciones":"ver_cursos_evaluaciones",
      "cursos": "ver_cursos",
      "curso": "ver_curso",
      "curso/:id": "ver_curso",

      "calm_escenas": "ver_calm_escenas",
      "calm_escena": "ver_calm_escena",
      "calm_escena/:id": "ver_calm_escena",

      "calm_categorias": "ver_calm_categorias",
      "calm_categoria": "ver_calm_categoria",
      "calm_categoria/:id": "ver_calm_categoria",

      "calm_cursos": "ver_calm_cursos",
      "calm_curso": "ver_calm_curso",
      "calm_curso/:id": "ver_calm_curso",

      "calm_clientes": "ver_calm_clientes",
      "calm_cliente": "ver_calm_cliente",
      "calm_cliente/:id": "ver_calm_cliente",

      "env_zonas": "ver_env_zonas",
      "env_zona": "ver_env_zona",
      "env_zona/:id": "ver_env_zona",

      "reglas_ofertas": "ver_reglas_ofertas",
      "regla_oferta": "ver_regla_oferta",
      "regla_oferta/:id": "ver_regla_oferta",
      "regla_oferta_2": "ver_regla_oferta_2",
      "regla_oferta_2/:id": "ver_regla_oferta_2",

      "asuntos": "ver_asuntos",
      "asunto": "ver_asunto",
      "asunto/:id": "ver_asunto",

      "organizadores_eventos": "ver_organizadores_eventos",
      "organizador_evento": "ver_organizador_evento",
      "organizador_evento/:id": "ver_organizador_evento",

      "conferencistas": "ver_conferencistas",
      "conferencista": "ver_conferencista",
      "conferencista/:id": "ver_conferencista",

      "not_eventos": "ver_not_eventos",
      "not_evento": "ver_not_evento",
      "not_evento/:id": "ver_not_evento",

      "fot_trabajos": "ver_fot_trabajos",
      "fot_trabajo": "ver_fot_trabajo",
      "fot_trabajo/:id": "ver_fot_trabajo",

      "departamentos_comerciales": "ver_departamentos_comerciales",
      "departamento_comercial": "ver_departamento_comercial",
      "departamento_comercial/:id": "ver_departamento_comercial",

      "tipos_alicuotas_iva": "ver_tipos_alicuotas_iva",
      "tipo_alicuota_iva": "ver_tipo_alicuota_iva",
      "tipo_alicuota_iva/:id": "ver_tipo_alicuota_iva",

      "empresas_tercerizadas": "ver_empresas_tercerizadas",
      "empresa_tercerizada": "ver_empresa_tercerizada",
      "empresa_tercerizada/:id": "ver_empresa_tercerizada",

      "especialidades": "ver_especialidades",
      "especialidad": "ver_especialidad",
      "especialidad/:id": "ver_especialidad",

      "obras_sociales": "ver_obras_sociales",
      "obra_social": "ver_obra_social",
      "obra_social/:id": "ver_obra_social",

      "tipos_pacientes": "ver_tipos_pacientes",
      "tipo_paciente": "ver_tipo_paciente",
      "tipo_paciente/:id": "ver_tipo_paciente",

      "tipos_terapias": "ver_tipos_terapias",
      "tipo_terapia": "ver_tipo_terapia",
      "tipo_terapia/:id": "ver_tipo_terapia",

      "tipos_atenciones": "ver_tipos_atenciones",
      "tipo_atencion": "ver_tipo_atencion",
      "tipo_atencion/:id": "ver_tipo_atencion",

      "titulos": "ver_titulos",
      "titulo": "ver_titulo",
      "titulo/:id": "ver_titulo",

      "formas_pago": "ver_formas_pago",
      "forma_pago": "ver_forma_pago",
      "forma_pago/:id": "ver_forma_pago",

      "marcas_vehiculos": "ver_marcas_vehiculos",
      "marca_vehiculo": "ver_marca_vehiculo",
      "marca_vehiculo/:id": "ver_marca_vehiculo",

      "nacionalidades": "ver_nacionalidades",
      "nacionalidad": "ver_nacionalidad",
      "nacionalidad/:id": "ver_nacionalidad",

      "tripulantes": "ver_tripulantes",
      "tripulante": "ver_tripulante",
      "tripulante/:id": "ver_tripulante",

      "videos": "ver_videos",
      "video": "ver_video",
      "video/:id": "ver_video",

      "not_videos": "ver_not_videos",
      "not_video": "ver_not_video",
      "not_video/:id": "ver_not_video",

      "chat_preguntas": "ver_chat_preguntas",
      "chat_pregunta": "ver_chat_pregunta",
      "chat_pregunta/:id": "ver_chat_pregunta",

      "zetas": "ver_zetas",
      "zeta": "ver_zeta",
      "zeta/:id": "ver_zeta",

      "mesas": "ver_mesas",

      "promociones": "ver_promociones",
      "promocion": "ver_promocion",
      "promocion/:id": "ver_promocion",

      "sucursales": "ver_sucursales",
      "sucursal": "ver_sucursal",
      "sucursal/:id": "ver_sucursal",

      "habitaciones": "ver_habitaciones",
      "habitacion": "ver_habitacion",
      "habitacion/:id": "ver_habitacion",

      "tipos_habitaciones": "ver_tipos_habitaciones",
      "tipo_habitacion": "ver_tipo_habitacion",
      "tipo_habitacion/:id": "ver_tipo_habitacion",
      
      "cuentas_bancarias": "ver_cuentas_bancarias",
      "cuenta_bancaria": "ver_cuenta_bancaria",
      "cuenta_bancaria/:id": "ver_cuenta_bancaria",        
      
      "almacenes": "ver_almacenes",
      "almacen": "ver_almacen",
      "almacen/:id": "ver_almacen",        

      "centros_costos": "ver_centros_costos",
      "centro_costo": "ver_centro_costo",
      "centro_costo/:id": "ver_centro_costo",        
      
      "articulos_etiquetas": "ver_articulos_etiquetas",
      "articulo_etiqueta": "ver_articulo_etiqueta",
      "articulo_etiqueta/:id": "ver_articulo_etiqueta",

      "entradas_etiquetas": "ver_entradas_etiquetas",
      "entrada_etiqueta": "ver_entrada_etiqueta",
      "entrada_etiqueta/:id": "ver_entrada_etiqueta",
      
      "rss_sources": "ver_rss_sources",
      "rss_source": "ver_rss_source",
      "rss_source/:id": "ver_rss_source",

      "servicios_envio": "ver_servicios_envio",
      "servicio_envio": "ver_servicio_envio",
      "servicio_envio/:id": "ver_servicio_envio",
      
      "costos_envio": "ver_costos_envio",
      
      "propietarios": "ver_propietarios",
      "propietario": "ver_propietario",
      "propietario/:id": "ver_propietario",
      
      "alumnos": "ver_alumnos",
      "alumno": "ver_alumno",
      "alumno/:id": "ver_alumno",
      "alumnos/comision/:id_comision": "ver_alumnos_por_comision",
      
      "docentes": "ver_docentes",
      "docentes/departamento/:id_departamento": "ver_docentes_por_departamento",
      "docente": "ver_docente",
      "docente/:id": "ver_docente",

      "asistencias": "ver_asistencias",
      "asistencias/:id": "ver_asistencias",
      "asistencia": "ver_asistencia",
      "asistencia/:id": "ver_asistencia",

      "asistencias_docentes": "ver_asistencias_docentes",
      "asistencia_docente": "ver_asistencia_docente",

      "reporte_asistencias": "ver_reporte_asistencias",
      "reporte_asistencias/:id": "ver_reporte_asistencias",

      "examenes": "ver_examenes",
      "examenes/:id": "ver_examenes",
      "examen": "ver_examen",
      "examen/:id": "ver_examen",
      
      "departamentos": "ver_departamentos",
      "departamento": "ver_departamento",
      "departamento/:id": "ver_departamento",

      "calificaciones": "ver_calificaciones",
      "calificacion": "ver_calificacion",
      "calificacion/:id": "ver_calificacion",

      "trimestres": "ver_trimestres",
      "trimestre": "ver_trimestre",
      "trimestre/:id": "ver_trimestre",

      "ingresos_proveedores": "ver_ingresos_proveedores",
      "ingreso_proveedor": "ver_ingreso_proveedor",
      "ingreso_proveedor/:id": "ver_ingreso_proveedor",
      "nuevo_ingreso_proveedor/:id": "ver_nuevo_ingreso_proveedor",

      "reposicion_asistida": "ver_reposicion_asistida",
      "reposicion_asistida/:id_proveedor/:id_sucursal": "ver_detalle_reposicion_asistida",

      "reparaciones": "ver_reparaciones",

      "transferencias_stock": "ver_transferencias_stock",
      "transferencia_stock": "ver_transferencia_stock",
      "transferencia_stock/:id": "ver_transferencia_stock",
      "nuevo_transferencia_stock/:id": "ver_nuevo_transferencia_stock",

      "comisiones": "ver_comisiones",
      "comision": "ver_comision",
      "comision/:id": "ver_comision",

      "recorridos_clientes": "ver_recorridos_clientes",
      "recorrido_cliente": "ver_recorrido_cliente",
      "recorrido_cliente/:id": "ver_recorrido_cliente",

      "comision_calendario/:id": "ver_comision_calendario",
      "via_comisiones_vendedores": "ver_via_comisiones_vendedores",
      "liquidacion_choferes": "ver_liquidacion_choferes",

      "carreras": "ver_carreras",
      "carrera": "ver_carrera",
      "carrera/:id": "ver_carrera",

      "tutores": "ver_tutores",
      "tutor": "ver_tutor",
      "tutor/:id": "ver_tutor",
      
      "contactos_web": "ver_contactos_web",
      "contactos": "ver_contactos",
      "contacto/:id": "ver_contacto",
      
      "bancos": "ver_bancos",
      "banco": "ver_banco",
      "banco/:id": "ver_banco",
      
      "versiones_db": "ver_versiones_db",
      "version_db": "ver_version_db",
      "version_db/:id": "ver_version_db",
      
      "tipos_estado_pedidos": "ver_tipos_estado_pedidos",
      "tipo_estado_pedido": "ver_tipo_estado_pedido",
      "tipo_estado_pedido/:id": "ver_tipo_estado_pedido",
      
      "landing_pages": "ver_landing_pages",
      "landing_pages_impresiones": "ver_landing_pages_impresiones",
      "landing_page": "ver_landing_page",
      "landing_page/:id": "ver_landing_page",

      "peliculas": "ver_peliculas",
      "pelicula": "ver_pelicula",
      "pelicula/:id": "ver_pelicula",
      
      "publicidades": "ver_publicidades",
      "publicidades_impresiones": "ver_publicidades_impresiones",
      "publicidad": "ver_publicidad",
      "publicidad/:id": "ver_publicidad",
      
      "campanias": "ver_campanias",
      "campania": "ver_campania",
      "campania/:id": "ver_campania",
      
      "encuestas": "ver_encuestas",
      "encuesta": "ver_encuesta",
      "encuesta/:id": "ver_encuesta",
      
      "sorteos": "ver_sorteos",
      "sorteo": "ver_sorteo",
      "sorteo/:id": "ver_sorteo",
      
      "comentarios": "ver_comentarios",
      "comentario": "ver_comentario",
      "comentario/:id": "ver_comentario",

      "publicidades_tipos": "ver_publicidades_tipos",
      "publicidad_tipo": "ver_publicidad_tipo",
      "publicidad_tipo/:id": "ver_publicidad_tipo",
      
      "publicidades_categorias": "ver_publicidades_categorias",
      "publicidad_categoria": "ver_publicidad_categoria",
      "publicidad_categoria/:id": "ver_publicidad_categoria",        
      
      "tipos_estado": "ver_tipos_estado",
      "tipo_estado": "ver_tipo_estado",
      "tipo_estado/:id": "ver_tipo_estado",

      "estado_reservas": "ver_estado_reservas",
      "estado_reserva": "ver_estado_reserva",
      "estado_reserva/:id": "ver_estado_reserva",
      
      "origenes": "ver_origenes",
      "origen": "ver_origen",
      "origen/:id": "ver_origen",
      
      "tipos_operacion": "ver_tipos_operacion",
      "tipo_operacion": "ver_tipo_operacion",
      "tipo_operacion/:id": "ver_tipo_operacion",
      
      "tipos_inmueble": "ver_tipos_inmueble",
      "tipo_inmueble": "ver_tipo_inmueble",
      "tipo_inmueble/:id": "ver_tipo_inmueble",
      
      "tipos_vehiculos": "ver_tipos_vehiculos",
      "tipo_vehiculo": "ver_tipo_vehiculo",
      "tipo_vehiculo/:id": "ver_tipo_vehiculo",

      "vehiculos": "ver_vehiculos",
      "vehiculo": "ver_vehiculo",
      "vehiculo/:id": "ver_vehiculo",

      "pedidos_mesas": "ver_pedidos_mesas",

      "viajes": "ver_viajes",
      "viaje": "ver_viaje",
      "viaje/:id": "ver_viaje",
      "viajes_asientos/:id": "ver_asientos",

      "opcionales": "ver_opcionales",
      "opcional": "ver_opcional",
      "opcional/:id": "ver_opcional",

      "tarjetas": "ver_tarjetas",
      "tarjeta": "ver_tarjeta",
      "tarjeta/:id": "ver_tarjeta",

      "tipos_tarifas": "ver_tipos_tarifas",
      "tipo_tarifa": "ver_tipo_tarifa",
      "tipo_tarifa/:id": "ver_tipo_tarifa",

      "hoteles": "ver_hoteles",
      "hotel": "ver_hotel",
      "hotel/:id": "ver_hotel",

      "administradores": "ver_administradores",
      "administrador": "ver_administrador",
      "administrador/:id": "ver_administrador",        
      
      "monedas": "ver_monedas",
      "moneda": "ver_moneda",
      "moneda/:id": "ver_moneda",
      
      "planes": "ver_planes",
      "plan": "ver_plan",
      "plan/:id": "ver_plan",

      "empresas_gestion_pagos": "ver_empresas_gestion_pagos",
      "gestion_pagos": "ver_gestion_pagos",
      
      "proyectos": "ver_proyectos",
      "proyecto": "ver_proyecto",
      "proyecto/:id": "ver_proyecto",                
      
      "provincias": "ver_provincias",
      "provincia": "ver_provincia",
      "provincia/:id": "ver_provincia",
      
      "rubros": "ver_rubros",
      "rubro": "ver_rubro",
      "rubro/:id": "ver_rubro",

      "consultas_tipos": "ver_consultas_tipos",
      "consulta_tipo": "ver_consulta_tipo",
      "consulta_tipo/:id": "ver_consulta_tipo",
      
      "categorias_entradas": "ver_categorias_entradas",
      "categoria_entrada": "ver_categoria_entrada",
      "categoria_entrada/:id": "ver_categoria_entrada",

      "categorias_viajes": "ver_categorias_viajes",
      "categoria_viaje": "ver_categoria_viaje",
      "categoria_viaje/:id": "ver_categoria_viaje",

      "categorias_opcionales": "ver_categorias_opcionales",
      "categoria_opcional": "ver_categoria_opcional",
      "categoria_opcional/:id": "ver_categoria_opcional",
      
      "clasificados_categorias": "ver_clasificados_categorias",
      "clasificado_categoria": "ver_clasificado_categoria",
      "clasificado_categoria/:id": "ver_clasificado_categoria",

      "seguimiento_vendedores": "ver_seguimiento_vendedores",
      "seguimiento_vendedores/:id": "ver_seguimiento_vendedores",
      
      "vendedores": "ver_vendedores",
      "vendedor": "ver_vendedor",
      "vendedor/:id": "ver_vendedor",                
      
      "empresas": "ver_empresas",
      "empresa": "ver_empresa",
      "empresa/:id": "ver_empresa",
      
      "mis_datos": "ver_mis_datos",
      "configuracion_emails": "ver_configuracion_emails",
      "mant_configuracion": "ver_mant_configuracion",

      "categorias_entrena": "ver_categorias_entrena",
      "categoria_entrena": "ver_categoria_entrena",
      "categoria_entrena/:id": "ver_categoria_entrena",
      
      "localidades": "ver_localidades",
      "localidad": "ver_localidad",
      "localidad/:id": "ver_localidad",

      // Es una copia de usuarios, solamente se usa para cambiar el menu en TOQUE
      "comercios": "ver_usuarios",
      "comercio": "ver_usuario",
      "comercio/:id": "ver_usuario",

      "profes": "ver_profes",
      "profe": "ver_profe",
      "profe/:id": "ver_profe",

      "toque_dashboard":"ver_toque_dashboard",
      "toque_dashboard_comercios":"ver_toque_dashboard_comercios",

      "carp_agencias": "ver_carp_agencias",
      "carp_agencia": "ver_carp_agencia",
      "carp_agencia/:id": "ver_carp_agencia",      

      "carp_choferes": "ver_carp_choferes",
      "carp_chofer": "ver_carp_chofer",
      "carp_chofer/:id": "ver_carp_chofer",      

      "carp_postulantes": "ver_carp_postulantes",
      "carp_postulante": "ver_carp_postulante",
      "carp_postulante/:id": "ver_carp_postulante",      

      "carp_propietarios": "ver_carp_propietarios",
      "carp_propietario": "ver_carp_propietario",
      "carp_propietario/:id": "ver_carp_propietario",      
      
      "usuarios": "ver_usuarios",
      "usuario": "ver_usuario",
      "usuario/:id": "ver_usuario",
      "mi_usuario": "ver_mi_usuario",
      
      "perfiles": "ver_perfiles",
      "perfil": "ver_perfil",
      "perfil/:id": "ver_perfil",        
      
      "tipos_gastos": "ver_tipos_gastos",
      "tipo_gasto": "ver_tipo_gasto",
      "tipo_gasto/:id": "ver_tipo_gasto",
      
      "cheques": "ver_cheques",
      "cheque": "ver_cheque",
      "cheque/:id": "ver_cheque",

      "tareas": "ver_tareas",

      "descuentos": "ver_descuentos",

      "stock": "ver_stock",
      "stock_por_sucursal": "ver_stock_por_sucursal",
      "stock_valoracion": "ver_stock_valoracion",
      "facturacion": "ver_facturacion",
      
      // Casos especiales de facturacion
      "facturacion_574": "ver_facturacion_574",

      "facturacion/:id": "ver_facturacion",
      "comprobante/:id/:id_punto_venta": "ver_comprobante",
      "ventas_diarias": "ver_ventas_diarias",
      "remitos": "ver_remito",
      "remitos/:id": "ver_remito",
      
      "deliveries": "ver_deliveries",
      "mostradores": "ver_mostradores",
      "cocinas": "ver_cocinas",
      "barras": "ver_barras",

      "ventas_listado": "ver_ventas",
      "ventas_listado/:id_punto_venta": "ver_ventas",
      "toque_pedidos": "ver_toque_pedidos",
      "toque_pedidos_repartidores": "ver_toque_pedidos_repartidores",
      "ventas_totales": "ver_ventas_totales",
      "compras_listado": "ver_compras_listado",
      "compras_listado/:mes/:anio/:id_concepto": "ver_compras_listado",
      "ordenes_pago": "ver_ordenes_pago",
      "recibos_clientes": "ver_recibos_clientes",
      "repartos": "ver_repartos",
      "compras_resumen": "ver_compras_resumen",

      "conceptos": "ver_conceptos",
      "conceptos/:totaliza_en/": "ver_conceptos",
      "ver_cajas_movimientos/:id_caja": "ver_cajas_movimientos",
      "gastos": "ver_gastos",
      "otros_ingresos": "ver_otros_ingresos",
      
      "pedidos": "ver_pedidos",
      "pedido": "ver_pedido",
      "pedido/:id": "ver_pedido",

      "pedidos_proveedores": "ver_pedidos_proveedores",
      "pedido_proveedor": "ver_pedido_proveedor",
      "pedido_proveedor/:id": "ver_pedido_proveedor",
      
      "presupuestos": "ver_presupuestos",
      "presupuesto": "ver_presupuesto",
      "presupuesto/:id": "ver_presupuesto",

      "roturas_mercaderias": "ver_roturas_mercaderias",
      "rotura_mercaderia": "ver_rotura_mercaderia",
      "rotura_mercaderia/:id": "ver_rotura_mercaderia",
      
      "procesar_pagos": "ver_procesar_pagos",
      "procesar_pagos_campanias": "ver_procesar_pagos_campanias",
      "procesar_pedidos": "ver_procesar_pedidos",
      "resumen_caja": "ver_resumen_caja",
      "cajas_diarias": "ver_cajas_diarias",
      "caja_diaria": "ver_caja_diaria",
      "caja_diaria/:id/:id_punto_venta/": "ver_caja_diaria",
      "actualizacion_precios": "ver_actualizacion_precios",
      "megashop_cambios_precios": "ver_megashop_cambios_precios",

      "pres_caja_diaria": "ver_pres_caja_diaria",
      
      "comparacion_ventas": "ver_comparacion_ventas",
      "articulos_ventas": "ver_articulos_ventas",
      "articulos_comparacion": "ver_articulos_comparacion",
      "articulos_totales": "ver_articulos_totales",
      
      "cuentas_corrientes_clientes": "ver_cuentas_corrientes_clientes",
      "cuentas_corrientes_proveedores": "ver_cuentas_corrientes_proveedores",
      
      "cuentas_corrientes_clientes/:id": "ver_cuentas_corrientes_clientes",
      "cuentas_corrientes_proveedores/:id": "ver_cuentas_corrientes_proveedores",
      
      "comisiones_vendedores": "ver_comisiones_vendedores",
      
      "listado_saldos_clientes": "ver_listado_saldos_clientes",
      "listado_saldos_proveedores": "ver_listado_saldos_proveedores",
      "deuda_proveedores": "ver_deuda_proveedores",
      "deuda_sucursales": "ver_deuda_sucursales",
      "deuda_totales": "ver_deuda_totales",
      
      "compras": "ver_compras",
      "compras/:id": "ver_compra",
      
      "web_sliders": "ver_web_sliders",
      "web_slider": "ver_web_slider",
      "web_slider/:id": "ver_web_slider",
      
      "web_users": "ver_web_users",
      "web_user": "ver_web_user",
      "web_user/:id": "ver_web_user",

      "web_banners": "ver_web_banners",
      "web_banner": "ver_web_banner",
      "web_banner/:id": "ver_web_banner",
      
      "web_categorias": "ver_web_categorias",
      "web_categoria": "ver_web_categoria",
      "web_categoria/:id": "ver_web_categoria",
      
      "web_paginas": "ver_web_paginas",
      "web_pagina": "ver_web_pagina",
      "web_pagina/:id": "ver_web_pagina",
      
      "web_textos": "ver_web_textos",
      "web_texto": "ver_web_texto",
      "web_texto/:id": "ver_web_texto",
      
      "emails_templates": "ver_emails_templates",
      "email_template": "ver_email_template",
      "email_template/:id": "ver_email_template",
      
      "web_templates": "ver_web_templates",
      "web_template": "ver_web_template",
      "web_template/:id": "ver_web_template",
      
      "farmacias": "ver_farmacias",
      "farmacia": "ver_farmacia",
      "farmacia/:id": "ver_farmacia",
      "farmacias_turnos": "ver_farmacias_turnos",

      "turnos_medicos": "ver_turnos_medicos",
      "profesional_turnos": "ver_profesional_turnos",

      "turnos": "ver_turnos",
      "turnos_calendario": "ver_turnos_calendario",

      "propiedades_reservas_listado": "ver_propiedades_reservas_listado",
      "propiedades_reservas": "ver_propiedades_reservas",

      "reservas_listado": "ver_reservas_listado",
      "reservas": "ver_reservas",
      "reservas_viajes": "ver_reservas_viajes",
      "ocupaciones": "ver_ocupaciones",
      
      "necrologicas": "ver_necrologicas",
      "necrologica": "ver_necrologica",
      "necrologica/:id": "ver_necrologica",
      
      "web_configuracion": "ver_web_configuracion",
      "web_estructura": "ver_web_estructura",
      "web_seo": "ver_web_seo",
      "chat_configuracion": "ver_chat_configuracion",
      "web_elegir_template": "ver_web_elegir_template",
      "medios_pago_configuracion": "ver_medios_pago_configuracion",
      "formas_envio_configuracion": "ver_formas_envio_configuracion",
      "permisos_red": "ver_permisos_red",
      
      "libros": "ver_libros",
      "libros/etiqueta/:id_etiqueta": "ver_libros_por_etiqueta",
      "libros/autor/:id_autor": "ver_libros_por_autor",
      "libro": "ver_libro",
      "libro/:id": "ver_libro",

      "sitemaps": "ver_sitemaps",
      "sitemap": "ver_sitemap",
      "sitemap/:id": "ver_sitemap",

      "galerias_imagenes": "ver_galerias_imagenes",
      "galerias_imagenes/etiqueta/:id_etiqueta": "ver_galerias_imagenes_por_etiqueta",
      "galeria_imagen": "ver_galeria_imagen",
      "galeria_imagen/:id": "ver_galeria_imagen",

      "galerias_etiquetas": "ver_galerias_etiquetas",
      "galeria_etiqueta": "ver_galeria_etiqueta",
      "galeria_etiqueta/:id": "ver_galeria_etiqueta",

      "galerias_categorias": "ver_galerias_categorias",
      "galeria_categoria": "ver_galeria_categoria",
      "galeria_categoria/:id": "ver_galeria_categoria",      
      
      "libros_etiquetas": "ver_libros_etiquetas",
      "libro_etiqueta": "ver_libro_etiqueta",
      "libro_etiqueta/:id": "ver_libro_etiqueta",
      
      "autores": "ver_autores",
      "autor": "ver_autor",
      "autor/:id": "ver_autor",

      "campanias_envios": "ver_campanias_envios",
      "campania_envio": "ver_campania_envio",
      "campania_envio/:id": "ver_campania_envio",
      
      "libros_prestamos": "ver_libros_prestamos",

      "sindi_empresas": "ver_sindi_empresas",
      "sindi_empresa": "ver_sindi_empresa",
      "sindi_empresa/:id": "ver_sindi_empresa",

      "sindi_estudios_contables": "ver_sindi_estudios_contables",
      "sindi_estudio_contable": "ver_sindi_estudio_contable",
      "sindi_estudio_contable/:id": "ver_sindi_estudio_contable",

      "sindi_nomencladores": "ver_sindi_nomencladores",

      "sindi_tipos_afiliados": "ver_sindi_tipos_afiliados",
      "sindi_tipo_afiliado": "ver_sindi_tipo_afiliado",
      "sindi_tipo_afiliado/:id": "ver_sindi_tipo_afiliado",

      "sindi_tipos_bonos": "ver_sindi_tipos_bonos",
      "sindi_tipo_bono": "ver_sindi_tipo_bono",
      "sindi_tipo_bono/:id": "ver_sindi_tipo_bono",

      "sindi_tipos_documentaciones": "ver_sindi_tipos_documentaciones",
      "sindi_tipo_documentacion": "ver_sindi_tipo_documentacion",
      "sindi_tipo_documentacion/:id": "ver_sindi_tipo_documentacion",

      "sindi_tipos_practicas": "ver_sindi_tipos_practicas",

      "sindi_tipos_reintegros": "ver_sindi_tipos_reintegros",
      "sindi_tipo_reintegro": "ver_sindi_tipo_reintegro",
      "sindi_tipo_reintegro/:id": "ver_sindi_tipo_reintegro",

      "sindi_afiliados": "ver_sindi_afiliados",
      "sindi_afiliado": "ver_sindi_afiliado",
      "sindi_afiliado/:id": "ver_sindi_afiliado",

      "sindi_localidades": "ver_sindi_localidades",
      "sindi_localidad": "ver_sindi_localidad",
      "sindi_localidad/:id": "ver_sindi_localidad",

      "cursos_autores": "ver_cursos_autores",
      "curso_autor": "ver_curso_autor",
      "curso_autor/:id": "ver_curso_autor",

      "not_editores": "ver_not_editores",
      "not_editor": "ver_not_editor",
      "not_editor/:id": "ver_not_editor",

      "sindi_condiciones_especiales": "ver_sindi_condiciones_especiales",
      "sindi_condicion_especial": "ver_sindi_condicion_especial",
      "sindi_condicion_especial/:id": "ver_sindi_condicion_especial",

      "sindi_limites_afiliados": "ver_sindi_limites_afiliados",
      "sindi_limite_afiliado": "ver_sindi_limite_afiliado",
      "sindi_limite_afiliado/:id": "ver_sindi_limite_afiliado",

      "sindi_limites_condiciones_especiales": "ver_sindi_limites_condiciones_especiales",
      "sindi_limite_condicion_especial": "ver_sindi_limite_condicion_especial",
      "sindi_limite_condicion_especial/:id": "ver_sindi_limite_condicion_especial",

      "sindi_limites_tipos_practicas": "ver_sindi_limites_tipos_practicas",
      "sindi_limite_tipo_practica": "ver_sindi_limite_tipo_practica",
      "sindi_limite_tipo_practica/:id": "ver_sindi_limite_tipo_practica",

      "sindi_bonos": "ver_sindi_bonos",
      "sindi_limites": "ver_sindi_limites",

      "repartidores": "ver_repartidores",
      "repartidor": "ver_repartidor",
      "repartidor/:id": "ver_repartidor",
      "cuenta_repartidor/:id": "ver_cuenta_repartidor",
      "cuenta_cliente/:id": "ver_cuenta_cliente",

      "gustos_helados": "ver_gustos_helados",
      "gusto_helado": "ver_gusto_helado",
      "gusto_helado/:id": "ver_gusto_helado",

      "cupones_descuentos": "ver_cupones_descuentos",
      "cupon_descuento": "ver_cupon_descuento",
      "cupon_descuento/:id": "ver_cupon_descuento",

      "cupones": "ver_cupones",
      "cupon": "ver_cupon",
      "cupon/:id": "ver_cupon",


      "andromeda_prescriptores": "ver_andromeda_prescriptores",
      "andromeda_prescriptor": "ver_andromeda_prescriptor",
      "andromeda_prescriptor/:id": "ver_andromeda_prescriptor",

      "petips_claims": "ver_petips_claims",
      "petips_claim": "ver_petips_claim",
      "petips_claim/:id": "ver_petips_claim",

      "petips_animales": "ver_petips_animales",
      "petips_animal": "ver_petips_animal",
      "petips_animal/:id": "ver_petips_animal",

      "petips_edades": "ver_petips_edades",
      "petips_edad": "ver_petips_edad",
      "petips_edad/:id": "ver_petips_edad",

      "petips_especialidades": "ver_petips_especialidades",
      "petips_especialidad": "ver_petips_especialidad",
      "petips_especialidad/:id": "ver_petips_especialidad",

      "petips_fabricantes": "ver_petips_fabricantes",
      "petips_fabricante": "ver_petips_fabricante",
      "petips_fabricante/:id": "ver_petips_fabricante",

      "petips_ingredientes": "ver_petips_ingredientes",
      "petips_ingrediente": "ver_petips_ingrediente",
      "petips_ingrediente/:id": "ver_petips_ingrediente",

      "petips_marcas": "ver_petips_marcas",
      "petips_marca": "ver_petips_marca",
      "petips_marca/:id": "ver_petips_marca",

      "petips_razas": "ver_petips_razas",
      "petips_raza": "ver_petips_raza",
      "petips_raza/:id": "ver_petips_raza",

      "petips_segmentos": "ver_petips_segmentos",
      "petips_segmento": "ver_petips_segmento",
      "petips_segmento/:id": "ver_petips_segmento",

      "petips_tamanios_animales": "ver_petips_tamanios_animales",
      "petips_tamanio_animal": "ver_petips_tamanio_animal",
      "petips_tamanio_animal/:id": "ver_petips_tamanio_animal",

      "petips_tipos_alimentos": "ver_petips_tipos_alimentos",
      "petips_tipo_alimento": "ver_petips_tipo_alimento",
      "petips_tipo_alimento/:id": "ver_petips_tipo_alimento",

      "petips_productos": "ver_petips_productos",
      "petips_producto": "ver_petips_producto",
      "petips_producto/:id": "ver_petips_producto",

      "clientes_log": "ver_clientes_log",

      "facturas_paycomet": "ver_facturas_paycomet",
      "mi_plan": "ver_mi_plan",
      "muchas_gracias": "ver_muchas_gracias",
      "pago_rechazado": "ver_pago_rechazado",

      "anunciantes": "ver_anunciantes",
      "anunciante": "ver_anunciante",
      "anunciante/:id": "ver_anunciante",
      


// NEXT_DEFINICION
    },

    // Antes de cambiar a la siguiente pagina del ROUTER
    before: function () {
      $(".customcomplete.closable").remove(); // Cerramos si hay un customcomplete abierto
    },

    get_domain: function() {
      return window.location.protocol+"//"+window.location.hostname;
    },

    mostrar: function(params) {

      // Valores por defecto de los parametros
      params.left || ( params.left = "" );
      params.right || ( params.right = "" );
      params.bottom || ( params.bottom = "" );
      params.top || ( params.top = "" );
      params.left_width || ( params.left_width = "50%" );
      params.right_width || ( params.right_width = "50%" );
      params.bottom_width || ( params.bottom_width = "100%" );
      params.top_width || ( params.top_width = "100%" );
      params.top_height || ( params.top_height = "" );
      params.full || ( params.full = 0 );
      if (typeof params.mostrar_mensaje_full == "undefined") params.mostrar_mensaje_full = MENSAJE_CUENTA_NIVEL;
      
      $("#main_container").empty();
      $("#second_container").empty();
      //$("#bottom_container").empty();
      $("#top_container").empty();
      
      $("#main_container").html(params.left);
      $("#second_container").html(params.right);
      //$("#bottom_container").html(params.bottom);
      $("#top_container").html(params.top);
      
      if (params.left === "") $("#main_container").hide();
      else $("#main_container").show();

      if (params.right === "") $("#second_container").hide();
      else $("#second_container").show();

      if (PERFIL == -1 || PERFIL == 1355) $("#bottom_container").hide();

      if (params.top === "") $("#top_container").hide();
      else $("#top_container").show();                
      
      $("#main_container").css("width",params.left_width);
      $("#second_container").css("width",params.right_width);
      //$("#bottom_container").css("width",params.bottom_width);
      $("#top_container").css("width",params.top_width);

      // Si tenemos que tomar el 100% del alto
      if (params.full == 1) {
        $(".app-content").addClass("app-content-full-height");
        $(".app-header-fixed").addClass("app-aside-folded");
        $("#top_container").addClass("full-height");
      } else {
        if (ID_EMPRESA != 444) {
          $(".app-content").removeClass("app-content-full-height");
          $(".app-header-fixed").removeClass("app-aside-folded");
          $("#top_container").removeClass("full-height");
        }
      }

      // Si tenemos que mostrar el mensaje full
      if (params.mostrar_mensaje_full == 2) {
        $(".full-message").show();
      } else {
        $(".full-message").hide();
      }

    },
    
    ver_compras: function() {
      if (control.check("compras_listado") > 0) {
        app.views.cargarCompra = new app.views.CargarCompras({
          model: new app.models.Compra({
            "netos": [],
          })
        });
        this.mostrar({
          "top" : app.views.cargarCompra.el,
        });
        $("#cargar_compras_codigo_proveedor").focus();
      }
    },
    
    ver_compra: function(id) {
      if (control.check("compras_listado") > 0) {
        var self = this;
        var model = new app.models.Compra({
          "id": id
        });
        model.fetch({
          "success":function(modelo) {
            
            // Si el comprobante es una Nota de Credito,
            // en la base de datos los montos estan negativos,
            // pero en la vista se deben poner positivos
            if (modelo.get("id_tipo_comprobante") == 3) {
              modelo.set({
                "total_general":Math.abs(modelo.get("total_general")),
                "total_neto":Math.abs(modelo.get("total_neto")),
                "perc_ing_brutos":Math.abs(modelo.get("perc_ing_brutos")),
                "perc_iva":Math.abs(modelo.get("perc_iva")),
                "impuesto_interno":Math.abs(modelo.get("impuesto_interno")),
                "exento":Math.abs(modelo.get("exento")),
                "total_iva":Math.abs(modelo.get("total_iva")),
                "subtotal":Math.abs(modelo.get("subtotal")),
                "total_regimenes_especiales":Math.abs(modelo.get("total_regimenes_especiales")),
              });
              
              for (var i = 0; i < modelo.get("netos").length; i++) {
                var neto = modelo.get("netos")[i];
                neto.neto = Math.abs(neto.neto);
                neto.iva = Math.abs(neto.iva);
                neto.neto_dto = Math.abs(neto.neto_dto);
                neto.porc_dto = Math.abs(neto.porc_dto);
                neto.porc_iva = Math.abs(neto.porc_iva);
              }
            }
            
            app.views.cargarCompra = new app.views.CargarCompras({
              "model": modelo
            });
            self.mostrar({
              "top" : app.views.cargarCompra.el,
            });
          }
        });
      }
    },              
    
    ver_index: function() {
      if (ID_PROYECTO == 3) this.ver_index_inmovar();
      else if (ID_PROYECTO == 1 || ID_PROYECTO == 2) this.ver_index_shopvar();
      else if (ID_PROYECTO == 4) this.ver_index_inforvar();
      else if (ID_PROYECTO == 5) this.ver_index_colvar();
      else if (ID_PROYECTO == 6) this.ver_index_tripvar();
      else if (ID_PROYECTO == 7) this.ver_index_docvar();
      else if (ID_PROYECTO == 11) this.ver_index_viajes();
      else if (ID_PROYECTO == 14) this.ver_index_clienapp();
      else if (ID_PROYECTO == 19) this.ver_index_classvar();
    },
    
    ver_index_pymvar: function() {
      this.mostrar({
        "top" : "",
      });
    },

    ver_toque_dashboard: function() {
      if (!(ID_EMPRESA == 571 || ID_EMPRESA == 1275)) return;
      var inicio = new app.views.ToqueDashboard({
        model: new app.models.AbstractModel(),
      });
      this.mostrar({
        "top" : inicio.el,
      });
    },

    ver_toque_dashboard_comercios: function() {
      if (!(ID_EMPRESA == 571 || ID_EMPRESA == 1275)) return;
      var s = prompt("Para entrar a esta seccion debe ingresar la clave de administracion:");
      if (!s) return;
      if (s != CLAVE_ESPECIAL) {
        alert("La clave es incorrecta."); return;
      }
      var inicio = new app.views.ToqueDashboardComercio({
        model: new app.models.AbstractModel(),
      });
      this.mostrar({
        "top" : inicio.el,
      });
    },
    
    ver_index_inmovar: function() {
      var self = this;
      $.ajax({
        "url":"/sistema/app/get_info_inmovar_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.DashboardPropiedades({
            model: m
          });

          // Recorremos las consultas
          _.each(m.get("consultas"),function(p){
            var item = new app.models.AbstractModel(p);
            var view = new app.views.ConsultasDashboard({
              model: item,
            });
            $(inicio.el).find("#dashboard_propiedades_consultas").append(view.render().el);
          });
          
          self.mostrar({
            "top" : inicio.el,
          });             
        }
      });        
    },

    ver_index_viajes: function() {
      var self = this;
      if (ID_EMPRESA == 501) return;
      $.ajax({
        "url":"/sistema/app/get_info_viajes_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var porc_total = 3;
          var porc = 0;
          if (res.configurar_disenio != 0 ) porc++;
          if (res.subir_elemento != 0 ) porc++;
          if (res.datos_empresa != 0) porc++;
          res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.DashboardViajes({
            model: m
          });
          
          // Agrupamos ambos arrays y los ordenamos
          var salida = new Array();
          _.each(m.get("reservas"),function(p){
            p.tipo = "reserva";
            p.stamp = moment(p.fecha_realizacion,"DD/MM/YY HH:mm:ss").format("YYYY-MM-DD HH:mm");
            salida.push(p);
          });
          _.each(m.get("consultas"),function(p){
            p.tipo = "consulta";
            p.stamp = moment(p.fecha+" "+p.hora,"DD/MM/YY HH:mm").format("YYYY-MM-DD HH:mm");
            salida.push(p);
          });
          salida.sort(function(a,b){
            return ((a.stamp < b.stamp) ? 1 : ((a.stamp > b.stamp) ? -1 : 0));
          });
          // Solamente tomamos los 6 primeros
          var i = 0;
          _.each(salida,function(p){
            if (i < 6) {
              if (p.tipo == "reserva") {
                var item = new app.models.AbstractModel(p);
                var view = new app.views.ReservaAsientoDashboard({
                  model: item,
                });
                $(inicio.el).find("#dashboard_viajes_consultas").append(view.render().el);                
              } else if (p.tipo == "consulta") {                
                var item = new app.models.AbstractModel(p);
                var view = new app.views.ConsultasDashboard({
                  model: item,
                });
                $(inicio.el).find("#dashboard_viajes_consultas").append(view.render().el);
              }                
            }
            i++;
          });
          
          self.mostrar({
            "top" : inicio.el,
          });             
        }
      });        
    },
    
    ver_index_shopvar: function() {
      if (control.check("inicio")>0) {
        var self = this;
        var id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : 0;
        var data = {
          "turnos":(control.check("turnos")>0)?1:0,
          // Vultrack tiene usuarios registrados y hay que notificarlos en el DASHBOARD
          "usuarios_registrados":(ID_EMPRESA == 186)?1:0, 
        };
        $.ajax({
          "url":"/sistema/app/get_info_shopvar_dashboard/"+ID_EMPRESA+"/"+id_usuario+"/",
          "dataType":"json",
          "data":data,
          "type":"post",
          "success":function(res) {
            
            _.extend(res,data);
            var m = new Backbone.Model(res);
            var inicio = new app.views.InicioShopvar({
              model: m
            });
          
            // Agrupamos ambos arrays y los ordenamos
            var salida = new Array();
            _.each(m.get("pedidos"),function(p){
              p.tipo = "pedido";
              salida.push(p);
            });
            _.each(m.get("consultas"),function(p){
              p.tipo = "consulta";
              salida.push(p);
            });
            salida.sort(function(a,b){
              return ((a.stamp < b.stamp) ? 1 : ((a.stamp > b.stamp) ? -1 : 0));
            });
            // Solamente tomamos los 6 primeros
            var i = 0;
            _.each(salida,function(p){
              if (i < 6) {
                if (p.tipo == "pedido") {
                  var item = new app.models.AbstractModel(p);
                  var view = new app.views.PedidoDashboard({
                    model: item,
                  });
                  $(inicio.el).find("#dashboard_shopvar_consultas").append(view.render().el);                
                } else if (p.tipo == "consulta") {                
                  var item = new app.models.AbstractModel(p);
                  var view = new app.views.ConsultasDashboard({
                    model: item,
                  });
                  $(inicio.el).find("#dashboard_shopvar_consultas").append(view.render().el);
                }                
              }
              i++;
            });
            
            self.mostrar({
              "top" : inicio.el,
            });             
          }
        });        
      }
    },
    
    ver_index_classvar: function() {
      if (control.check("inicio")>0) {
        var self = this;
        $.ajax({
          "url":"/sistema/app/get_info_classvar_dashboard/"+ID_EMPRESA+"/",
          "dataType":"json",
          "type":"post",
          "success":function(res) {
            
            var porc_total = 3;
            var porc = 0;
            if (res.configurar_disenio != 0 ) porc++;
            if (res.subir_elemento != 0 ) porc++;
            if (res.sin_envios != 0 ) porc++;
            res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
            
            var m = new Backbone.Model(res);
            var inicio = new app.views.InicioNewsvar({
              model: m
            });
            
            // Recorremos las consultas
            _.each(m.get("consultas"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ConsultasDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_classvar_consultas").append(view.render().el);
            });            

            // Recorremos los comentarios
            _.each(m.get("comentarios"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ComentarioDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_classvar_consultas").append(view.render().el);
            });            
            
            self.mostrar({
              "top" : inicio.el,
            });             
          }
        });
      }
    },

    ver_index_inforvar: function() {
      if (control.check("inicio")>0) {
        var self = this;
        $.ajax({
          "url":"/sistema/app/get_info_inforvar_dashboard/"+ID_EMPRESA+"/",
          "dataType":"json",
          "type":"post",
          "success":function(res) {
            
            var porc_total = 3;
            var porc = 0;
            if (res.configurar_disenio != 0 ) porc++;
            if (res.subir_elemento != 0 ) porc++;
            if (res.sin_envios != 0 ) porc++;
            res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
            
            var m = new Backbone.Model(res);
            var inicio = new app.views.InicioNewsvar({
              model: m
            });
            
            // Recorremos las consultas
            _.each(m.get("consultas"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ConsultasDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_inforvar_consultas").append(view.render().el);
            });            

            // Recorremos los comentarios
            _.each(m.get("comentarios"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ComentarioDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_inforvar_consultas").append(view.render().el);
            });            
            
            self.mostrar({
              "top" : inicio.el,
            });             
          }
        });
      }
    },
    
    ver_index_colvar: function() {
      var self = this;
      $.ajax({
        "url":"/sistema/app/get_info_colvar_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var porc_total = 2;
          var porc = 0;
          if (res.configurar_disenio != 0 ) porc++;
          if (res.subir_elemento != 0 ) porc++;
          res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.InicioColvar({
            model: m
          });
          
            // Recorremos las consultas
            _.each(m.get("consultas"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ConsultasDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_colvar_consultas").append(view.render().el);
            });
            
            self.mostrar({
              "top" : inicio.el,
            });             
          }
        });        
    },

    ver_index_clienapp: function() {
      var self = this;
      $.ajax({
        "url":"/sistema/app/get_info_clienapp_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var porc_total = 2;
          var porc = 0;
          if (res.configurar_disenio != 0 ) porc++;
          if (res.subir_elemento != 0 ) porc++;
          res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.InicioClienApp({
            model: m
          });
          
          // Recorremos las consultas
          _.each(m.get("consultas"),function(p){
            var item = new app.models.AbstractModel(p);
            var view = new app.views.ConsultasDashboard({
              model: item,
            });
            $(inicio.el).find("#dashboard_clienapp_consultas").append(view.render().el);
          });
          
          self.mostrar({
            "top" : inicio.el,
          });             
        }
      });        
    },
    
    ver_index_tripvar: function() {
      var self = this;
      $.ajax({
        "url":"/sistema/app/get_info_tripvar_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var porc_total = 2;
          var porc = 0;
          if (res.configurar_disenio != 0 ) porc++;
          if (res.subir_elemento != 0 ) porc++;
          res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.InicioTripvar({
            model: m
          });

          // Recorremos las consultas
          _.each(m.get("pedidos"),function(p){
            var item = new app.models.AbstractModel({
              "id_tipo_estado":p.id_estado,
              "cliente":p.cliente.nombre,
              "cliente_email":p.cliente.email,
              "cliente_telefono":p.cliente.telefono,
              "fecha":p.fecha_reserva,
              "hora":p.hora_reserva,
              "total":p.precio,
              "codigo_autorizacion":"",
            });
            var view = new app.views.PedidoDashboard({
              model: item,
            });
            $(inicio.el).find("#dashboard_tripvar_consultas").append(view.render().el);                
          }); 

          
          // Recorremos las consultas
          _.each(m.get("consultas"),function(p){
            var item = new app.models.AbstractModel(p);
            var view = new app.views.ConsultasDashboard({
              model: item,
            });
            $(inicio.el).find("#dashboard_tripvar_consultas").append(view.render().el);
          });
          
          self.mostrar({
            "top" : inicio.el,
          });             
        }
      });                   
    },
    
    ver_index_docvar: function() {
      var self = this;
      $.ajax({
        "url":"/sistema/app/get_info_docvar_dashboard/"+ID_EMPRESA+"/",
        "dataType":"json",
        "type":"post",
        "success":function(res) {
          
          var porc_total = 3;
          var porc = 0;
          if (res.configurar_disenio != 0 ) porc++;
          if (res.subir_elemento != 0 ) porc++;
          if (res.sin_envios != 0 ) porc++;
          res.porcentaje = Number((porc / porc_total) * 100).toFixed(0);
          
          var m = new Backbone.Model(res);
          var inicio = new app.views.InicioDocvar({
            model: m
          });
          
            // Recorremos las consultas
            _.each(m.get("consultas"),function(p){
              var item = new app.models.AbstractModel(p);
              var view = new app.views.ConsultasDashboard({
                model: item,
              });
              $(inicio.el).find("#dashboard_docvar_consultas").append(view.render().el);
            });            
            
            self.mostrar({
              "top" : inicio.el,
            });             
          }
        });        
    },      
    
    ver_precios: function() {
      var precio = new app.views.Precios({
        model: new Backbone.Model()
      });
      this.mostrar({
        "top" : precio.el,
      });                    
    },

    ver_mi_cuenta: function() {
      var self = this;
      $.ajax({
        "url":"empresas/function/get_datos_cuenta/",
        "type":"post",
        "data":{
          "id_usuario":ID_USUARIO,
        },
        "dataType":"json",
        "success":function(res) {
          var modelo = new Backbone.Model(res);
          var view = new app.views.MiCuenta({
            model: modelo
          });
          self.mostrar({
            "top": view.el,
            "mostrar_mensaje_full":0, // Con esto deshabilitamos siempre el mensaje full por si quiere pagar
          });                    
        }
      });
    },
    
    ver_estadisticas_web: function() {
      var self = this;
      var m = new app.models.EstadisticasWeb();
      var inicio = new app.views.EstadisticasWebView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_consultas: function() {
      var self = this;
      var inicio = new app.views.EstadisticasConsultasView({
        model: new app.models.AbstractModel({
          "desde":moment().startOf('month').format("DD/MM/YYYY"),
          "hasta":moment().format("DD/MM/YYYY"),
        })
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_mant_estadisticas: function() {
      var self = this;
      var m = new app.models.MantEstadisticas();
      var inicio = new app.views.MantEstadisticasView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },
    
    ver_estadisticas_ventas: function() {
      var self = this;
      var m = new app.models.EstadisticasVentas();
      var inicio = new app.views.EstadisticasVentasView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_tarjetas: function() {
      var self = this;
      var m = new app.models.EstadisticasTarjetas();
      var inicio = new app.views.EstadisticasTarjetasView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
        "top_height": "100%",
        "full": 1,
      });
    },    

    ver_estadisticas_whatsapp: function() {
      var self = this;
      var m = new app.models.EstadisticasWhatsapp();
      var inicio = new app.views.EstadisticasWhatsappView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_articulos_web: function() {
      var self = this;
      var inicio = new app.views.EstadisticasArticulosWebView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_ventas_por_dia: function() {
      var self = this;
      var inicio = new app.views.EstadisticasVentasPorDiaView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_sucursales: function() {
      var self = this;
      var inicio = new app.views.EstadisticasSucursalesView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
        "top_height": "100%",
        "full": 1,        
      });
    },

    ver_estadisticas_articulos_sucursales: function() {
      var self = this;
      var inicio = new app.views.EstadisticasArticulosSucursalesView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_ventas_por_proveedor: function() {
      var self = this;
      var inicio = new app.views.EstadisticasVentasPorProveedorView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_compras_ventas: function() {
      var self = this;
      var inicio = new app.views.EstadisticasComprasVentasView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_compras_ventas_por_articulos: function() {
      var self = this;
      var inicio = new app.views.EstadisticasComprasVentasPorArticulosView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_ventas_por_departamento: function() {
      var self = this;
      var inicio = new app.views.EstadisticasVentasPorDepartamentoView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_ventas_por_sucursal: function() {
      var self = this;
      var inicio = new app.views.EstadisticasVentasPorSucursalView({
        model: new app.models.AbstractModel(),
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_publicidades: function() {
      var self = this;
      var m = new app.models.EstadisticasPublicidades();
      var view = new app.views.EstadisticasPublicidadesView({
        model: m
      });
      self.mostrar({
        "top":view.el,
      });
    },

    ver_estadisticas_prestamos: function() {
      var self = this;
      var m = new app.models.EstadisticasPrestamos();
      var inicio = new app.views.EstadisticasPrestamosView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_pagos: function() {
      var self = this;
      var m = new app.models.EstadisticasPagos();
      var inicio = new app.views.EstadisticasPagosView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_cobranzas: function() {
      var self = this;
      var m = new app.models.EstadisticasCobranzas();
      var inicio = new app.views.EstadisticasCobranzasView({
        model: m
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_compras: function() {
      var self = this;
      var inicio = new app.views.EstadisticasComprasView({
        model: new app.models.AbstractModel(),
        tipo_proveedor: "C",
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    ver_estadisticas_resumen: function() {
      var self = this;
      var inicio = new app.views.EstadisticasResumenView({
        model: new app.models.AbstractModel()
      });
      self.mostrar({
        "top" : inicio.el,
      });
    },

    
    ver_ventas_diarias: function() {
      var permiso = control.check("ventas_diarias");
      if (permiso > 0) {
        
        var modelo = new app.models.VentasDiarias();
        
        app.views.ventas_diariasResultados = new app.views.VentasDiariasResultados({
          permiso: permiso,
          model: modelo
        });
        
        app.views.ventas_diariasParametros = new app.views.VentasDiariasParametros({
          permiso: permiso,
          model: modelo,
          resultados: app.views.ventas_diariasResultados
        });
        
        this.mostrar({
          "top" : app.views.ventas_diariasParametros.el,
          "bottom" : app.views.ventas_diariasResultados.el,
        });
      }
    },

    ver_deliveries: function() {
      var permiso = control.check("deliveries");
      if (permiso > 0) {
        app.views.deliveriesTableView = new app.views.DeliveriesTableView({
          collection: new app.collections.Ventas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" :app.views.deliveriesTableView.el,
        });
      }
    },

    ver_mostradores: function() {
      var permiso = control.check("mostradores");
      if (permiso > 0) {
        app.views.mostradoresTableView = new app.views.MostradoresTableView({
          collection: new app.collections.Ventas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" :app.views.mostradoresTableView.el,
        });
      }
    },

    ver_barras: function() {
      var permiso = control.check("barras");
      if (permiso > 0) {
        app.views.barrasTableView = new app.views.BarrasTableView({
          collection: new app.collections.Ventas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" :app.views.barrasTableView.el,
        });
      }
    },

    ver_cocinas: function() {
      var permiso = control.check("cocinas");
      if (permiso > 0) {
        app.collections.cocinas = new app.collections.Cocinas();
        app.views.cocinaView = new app.views.CocinasView({
          collection: app.collections.cocinas,
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cocinaView.el,
        });
      }
    },

    ver_ventas: function(id_punto_venta) {
      var permiso = control.check("ventas_listado");
      if (permiso > 0) {

        if (id_punto_venta != undefined) window.ventas_listado_punto_venta = id_punto_venta;

        // TODO: Hacer esto dinamico despues
        // gastrober quiere que el listado de ventas siempre venta filtrado por fecha de hoy por defecto
        if (ID_EMPRESA == 342 || ID_EMPRESA == 1282) {
          window.ventas_listado_fecha_desde = moment().toDate();
          window.ventas_listado_fecha_hasta = moment().toDate();
        }

        if (ID_EMPRESA == 1319 && PERFIL == 1355) window.ventas_listado_id_origen = "1";

        // MEGASHOP PONEMOS LOS ULTIMOS DIAS NADA MAS, PARA QUE NO CARGUE TANTO
        if (MEGASHOP == 1 || ID_EMPRESA == 356) {
          if (typeof window.ventas_listado_fecha_desde == "undefined") window.ventas_listado_fecha_desde = moment().subtract(1,"days").toDate();
          if (typeof window.ventas_listado_fecha_hasta == "undefined") window.ventas_listado_fecha_hasta = moment().toDate();
        } else if (ID_EMPRESA != 1284) {
          if (typeof window.ventas_listado_fecha_desde == "undefined") window.ventas_listado_fecha_desde = moment().subtract(1,"month").toDate();
          if (typeof window.ventas_listado_fecha_hasta == "undefined") window.ventas_listado_fecha_hasta = moment().toDate();
        }

        // DI PIERO LOBOS
        if (ID_EMPRESA == 229 && ID_USUARIO == 1389) {
          window.ventas_listado_vendedor = 112;
        }

        if (ID_EMPRESA == 1284) {
          window.ventas_listado_in_tipos_estados = "4-5-6";
        }

        app.views.ventasTableView = new app.views.VentasTableView({
          collection: new app.collections.Ventas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.ventasTableView.el,
        });
      }
    },

    ver_toque_pedidos: function() {
      var permiso = control.check("toque_pedidos");
      if (permiso > 0) {
        window.toque_pedidos_listado_fecha_desde = moment().subtract(1,"days").format("DD/MM/YYYY");
        window.toque_pedidos_listado_fecha_hasta = moment().format("DD/MM/YYYY");
        app.views.toque_pedidosTableView = new app.views.ToquePedidosTableView({
          collection: new app.collections.ToquePedidos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.toque_pedidosTableView.el,
          "top_height": "100%",
          "full": 1,          
        });
      }
    },

    ver_toque_pedidos_repartidores: function() {
      var permiso = control.check("toque_pedidos_repartidores");
      if (permiso > 0) {
        var view = new app.views.ToquePedidosRepartidoresTableView({
          collection: new app.collections.ToquePedidosRepartidores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
          "top_height": "100%",
          "full": 1,          
        });
      }
    },

    ver_ventas_totales: function() {
      var permiso = control.check("ventas_totales");
      if (permiso > 0) {
        app.views.ventasTotalesTableView = new app.views.VentasTotalesTableView({
          collection: new app.collections.VentasTotales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.ventasTotalesTableView.el,
        });
      }
    },      
    
    ver_compras_listado: function(mes,anio,id_concepto) {
      
      var desde = ""; var hasta = "";
      mes = (mes == undefined)?"":mes;
      anio = (anio == undefined)?"":anio;
      if (mes.indexOf("-")>0) {
        // No estamos mandamos el mes, sino la fecha desde
        desde = mes;
        mes = "";
      }
      if (anio.indexOf("-")>0) {
        hasta = anio;
        anio = "";
      }
      id_concepto = (id_concepto == undefined)?0:id_concepto;

      var permiso = control.check("compras_listado");
      if (permiso > 0) {
        var compras = new app.collections.Compras();
        var view = new app.views.ComprasListadoView({
          permiso: permiso,
          collection: compras,
          mes: mes,
          anio: anio,
          desde: desde,
          hasta: hasta,
          id_concepto: id_concepto,
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_ordenes_pago: function() {      
      var permiso = control.check("cuentas_corrientes_proveedores");
      if (permiso > 0) {
        var view = new app.views.OrdenesPagoListadoView({
          permiso: permiso,
          collection: new app.collections.OrdenesPago(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_recibos_clientes: function() {      
      var permiso = control.check("cuentas_corrientes_clientes");
      if (permiso > 0) {
        var view = new app.views.RecibosClientesListadoView({
          permiso: permiso,
          collection: new app.collections.RecibosClientes(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_compras_resumen: function() {
      var permiso = control.check("compras_resumen");
      if (permiso > 0) {
        app.views.compras_resumenResultados = new app.views.ComprasResumenResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : app.views.compras_resumenResultados.el,
        });
      }
    },             

    ver_estadisticas_gastos: function() {
      var permiso = control.check("estadisticas_gastos");
      if (permiso > 0) {
        var view = new app.views.EstadisticasGastosResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_repartos: function() {
      var permiso = control.check("repartos");
      if (permiso > 0) {
        app.views.repartosResultados = new app.views.RepartosResultados({
          permiso: permiso,
        });
        this.mostrar({
          "top" : app.views.repartosResultados.el,
        });
      }
    },
    
    ver_categorias_entrena: function() {
      var permiso = control.check("categorias_entrena");
      if (permiso > 0) {
        view = new app.views.CategoriasEntrenaTableView({
          collection: new app.collections.CategoriaEntrena(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_categoria_entrena: function(id) {
      var self = this;
      var permiso = control.check("categorias_entrena");
      if (permiso > 0) {
        if (id == undefined) {
          view = new app.views.CategoriasEntrenaEditView({
            model: new app.models.CategoriasEntrena(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var opciones_devoluciones = new app.models.CategoriasEntrena({ "id": id });
          opciones_devoluciones.fetch({
            "success":function() {
              view = new app.views.CategoriasEntrenaEditView({
                model: opciones_devoluciones,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
        
      }
    },

    ver_procesar_pagos: function() {
      var permiso = control.check("procesar_pagos");
      if (permiso > 0) {
        app.views.procesarPagosResultados = new app.views.ProcesarPagosResultados({
          permiso: permiso,
          model: new app.models.CajaReparto(),
        });
        this.mostrar({
          "top" : app.views.procesarPagosResultados.el,
        });
      }
    },             
    
    ver_procesar_pagos_campanias: function() {
      var permiso = control.check("procesar_pagos_campanias");
      if (permiso > 0) {
        var view = new app.views.ProcesarPagosCampaniasResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },     

    ver_seguimiento_vendedores: function(id_vendedor) {
      id_vendedor = (typeof id_vendedor != "undefined" ? id_vendedor : 0);
      var permiso = control.check("seguimiento_vendedores");
      if (permiso > 0) {
        var view = new app.views.VendedoresSeguimientoView({
          permiso: permiso,
          model: new app.models.AbstractModel({
            "id_vendedor":id_vendedor,
          }),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },             

    ver_via_comisiones_vendedores: function() {
      var permiso = control.check("via_comisiones_vendedores");
      if (permiso > 0) {
        var view = new app.views.ViaComisionesVendedoresResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },             

    ver_liquidacion_choferes: function() {
      var permiso = control.check("liquidacion_choferes");
      if (permiso > 0) {
        var view = new app.views.LiquidacionChoferesResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },             

    ver_procesar_pedidos: function() {
      var permiso = control.check("procesar_pedidos");
      if (permiso > 0) {
        var view = new app.views.ProcesarPedidosResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },             
    
    ver_actualizacion_precios : function() {
      var permiso = control.check("actualizacion_precios");
      if (permiso > 0) {
        app.views.actualizacionPreciosResultados = new app.views.ActualizacionPreciosResultados({
          collection: new app.collections.Articulos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.actualizacionPreciosResultados.el,
        });
      }
    },

    ver_megashop_cambios_precios : function() {
      var permiso = control.check("megashop_cambios_precios");
      if (permiso > 0) {
        var view = new app.views.MegashopCambiosPreciosView({
          model: new app.models.AbstractModel(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_resumen_caja: function() 
    {
      var permiso = control.check("resumen_caja");
      if (permiso > 0) {
        var modelo = new app.models.ResumenCaja();
        app.views.resumenCajaResultados = new app.views.ResumenCajaResultados({
          permiso: permiso,
          model: modelo
        });
        app.views.resumenCajaParametros = new app.views.ResumenCajaParametros({
          permiso: permiso,
          model: modelo,
          resultados: app.views.resumenCajaResultados
        });
        this.mostrar({
          "top" : app.views.resumenCajaParametros.el,
          "bottom" : app.views.resumenCajaResultados.el,
        });
      }
    },
    
    ver_cajas_diarias: function() {
      var permiso = control.check("cajas_diarias");
      if (permiso > 0) {
        var view = new app.views.CajasDiariasTableView({
          collection: new app.collections.CajasDiarias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_pres_caja_diaria: function() {
      var permiso = control.check("pres_caja_diaria");
      if (permiso > 0) {
        var view = new app.views.PresCajasDiariasTableView({
          collection: new app.collections.PresCajasDiarias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
          "full":1,
        });
      }
    },

    ver_caja_diaria: function(id,id_punto_venta) {
      var self = this;
      var permiso = Math.max(control.check("caja_diaria"),control.check("cajas_diarias"));
      if (permiso > 0) {

        if (id == undefined) {
          var caja_diaria = new app.models.CajaDiaria();
          app.views.cajaDiaria = new app.views.CajaDiaria({
            model: caja_diaria,
            permiso: permiso,
          });
          this.mostrar({
            "top" : app.views.cajaDiaria.el,
          });
        } else {
          $.ajax({
            "url":"caja_diaria/function/get_by_pv/"+id+"/"+id_punto_venta,
            "dataType":"json",
            "success":function(m) {
              var caja_diaria = new app.models.CajaDiaria(m);
              var edit = new app.views.CajaDiaria({
                model: caja_diaria,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          })
        }

      }
    },
    
    ver_comparacion_ventas: function() {
      var permiso = control.check("comparacion_ventas");
      if (permiso > 0) {
        app.collections.comparacionVentas = new app.collections.ComparacionVentas();
        app.views.comparacionVentas = new app.views.ComparacionVentas({
          permiso: permiso,
          collection: app.collections.comparacionVentas
        });
        this.mostrar({
          "top" : app.views.comparacionVentas.el,
        });
      }
    },
    
    ver_articulos_ventas: function() {
      var permiso = control.check("articulos_ventas");
      if (permiso > 0) {
        app.collections.articulosVentas = new app.collections.ArticulosVentas();
        app.views.articulosVentas = new app.views.ArticulosVentas({
          permiso: permiso,
          collection: app.collections.articulosVentas
        });
        this.mostrar({
          "top" : app.views.articulosVentas.el,
          "top_height": "100%",
          "full": 1,
        });
      }
    },

    ver_articulos_comparacion: function() {
      var permiso = control.check("articulos_comparacion");
      if (permiso > 0) {
        app.collections.articulosComparacion = new app.collections.ArticulosComparacion();
        app.views.articulosComparacion = new app.views.ArticulosComparacion({
          permiso: permiso,
          collection: app.collections.articulosComparacion
        });
        this.mostrar({
          "top" : app.views.articulosComparacion.el,
          "top_height": "100%",
          "full": 1,
        });
      }
    },
    
    ver_articulos_totales: function() {
      var permiso = control.check("articulos_totales");
      if (permiso > 0) {
        app.collections.articulosTotales = new app.collections.ArticulosTotales();
        app.views.articulosTotales = new app.views.ArticulosTotales({
          permiso: permiso,
          collection: app.collections.articulosTotales
        });
        this.mostrar({
          "top" : app.views.articulosTotales.el,
        });
      }
    },             
    
    ver_descuentos : function() {
      var permiso = control.check("descuentos");
      if (permiso > 0) {
        var descuentos = new app.collections.Descuentos();
        var view = new app.views.DescuentosResultados({
          permiso: permiso,
          collection: descuentos
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_stock : function() {
      var permiso = control.check("stock");
      if (permiso > 0) {
        var stocks = new app.collections.Stocks();
        app.views.stocksResultados = new app.views.StocksResultados({
          permiso: permiso,
          collection: stocks
        });
        this.mostrar({
          "top" : app.views.stocksResultados.el,
        });
      }
    },
    
    ver_clientes: function() {
      var permiso = control.check("clientes");
      if (permiso > 0) {
        window.clientes_tipo = 0;
        var view = new app.views.ClientesTableView({
          collection: new app.collections.Clientes(),
          vista_contactos: false,
          modulo: control.get("clientes"),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_cliente: function(id) {
      var self = this;
      var permiso_contactos = control.check("contactos");
      var permiso_clientes = control.check("clientes");
      var permiso_consultas = control.check("consultas");
      var vista_contactos = (permiso_contactos > 0 && permiso_clientes > 0) ? false : true;
      if (!vista_contactos) window.clientes_tipo = 0;
      var permiso = Math.max(permiso_contactos,permiso_clientes,permiso_consultas);
      if (permiso > 0) {
        if (id == undefined) {
          app.views.clienteEditView = new app.views.ClienteEditView({
            model: new app.models.Cliente(),
            permiso: permiso,
            modulo: ((control.check("clientes")>0) ? control.get("clientes") : control.get("contactos")),
            vista_contactos: vista_contactos,
          });
          this.mostrar({
            "top" : app.views.clienteEditView.el,
          });
          if (MILLING == 1 || ID_EMPRESA == 70) workspace.crear_editor('cliente_observaciones',{"toolbar":"Basic"});
        } else {
          var cliente = new app.models.Cliente({"id":id});
          cliente.fetch({
            "success":function() {
              var edit = new app.views.ClienteEditView({
                model: cliente,
                modulo: ((control.check("clientes")>0) ? control.get("clientes") : control.get("contactos")),
                permiso: permiso,
                vista_contactos: vista_contactos,
              });
              self.mostrar({
                "top" : edit.el,
              });
              if (MILLING == 1 || ID_EMPRESA == 70) workspace.crear_editor('cliente_observaciones',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },

    // TODO: Hacer mejor la parte de contactos y clientes
    ver_contacto: function(id) {
      var self = this;
      var vista_contactos = true;
      var permiso = control.check("contactos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.clienteEditView = new app.views.ClienteEditView({
            model: new app.models.Cliente(),
            permiso: permiso,
            modulo: ((control.check("clientes")>0) ? control.get("clientes") : control.get("contactos")),
            vista_contactos: vista_contactos,
          });
          this.mostrar({
            "top" : app.views.clienteEditView.el,
          });
          if (MILLING == 1) workspace.crear_editor('cliente_observaciones',{"toolbar":"Basic"});
        } else {
          var cliente = new app.models.Cliente({"id":id});
          cliente.fetch({
            "success":function() {
              var edit = new app.views.ClienteEditView({
                model: cliente,
                modulo: ((control.check("clientes")>0) ? control.get("clientes") : control.get("contactos")),
                permiso: permiso,
                vista_contactos: vista_contactos,
              });
              self.mostrar({
                "top" : edit.el,
              });
              if (MILLING == 1) workspace.crear_editor('cliente_observaciones',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },

    ver_pres_garantes: function() {
      var permiso = control.check("pres_garantes");
      if (permiso > 0) {
        window.pres_clientes_garante = 1; // Filtramos solo los garantes
        var view = new app.views.PresClientesTableView({
          collection: new app.collections.PresClientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_pres_garante: function(id) {
      var self = this;
      var permiso = control.check("pres_garantes");
      if (permiso > 0) {
        window.pres_clientes_garante = 1;
        if (id == undefined) {
          var view = new app.views.PresClienteEditView({
            model: new app.models.PresCliente(),
            permiso: permiso,
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var garante = new app.models.PresCliente({
            "id":id,
            "garante":1,
          });
          garante.fetch({
            "success":function() {
              var edit = new app.views.PresClienteEditView({
                model: garante,
                permiso: permiso,
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_pres_clientes: function(id_plan) {
      if (typeof id_plan != "undefined" && id_plan != null) {
        console.log(id_plan);
        window.pres_clientes_estado = 1;
        window.pres_clientes_id_plan = id_plan;
      }
      var permiso = control.check("pres_clientes");
      if (PERFIL == 1181) permiso = 3; // Perfil de usuario morosos
      if (permiso > 0) {
        window.pres_clientes_garante = 0;
        var view = new app.views.PresClientesTableView({
          collection: new app.collections.PresClientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_pres_cliente: function(id) {
      var self = this;
      var permiso = control.check("pres_clientes");
      if (PERFIL == 1181) permiso = 3; // Perfil de usuario morosos
      if (permiso > 0) {
        window.pres_clientes_garante = 0;
        if (id == undefined) {
          var view = new app.views.PresClienteEditView({
            model: new app.models.PresCliente(),
            permiso: permiso,
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var cliente = new app.models.PresCliente({"id":id});
          cliente.fetch({
            "success":function() {
              var edit = new app.views.PresClienteEditView({
                model: cliente,
                permiso: permiso,
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_pres_cliente_acciones: function(id,tab) {
      var self = this;
      if (typeof tab == undefined || tab == null) tab = "prestamos";
      console.log(tab);
      var permiso = control.check("pres_clientes");
      if (PERFIL == 1181) permiso = 3; // Perfil de usuario morosos
      if (permiso > 0) {
        window.pres_clientes_garante = 0;
        var cliente = new app.models.PresCliente({"id":id});
        cliente.fetch({
          "success":function() {
            var edit = new app.views.PresClienteTimelineView({
              "model": cliente,
              "permiso": permiso,
              "tab_activo": tab
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    },

    ver_contacto_acciones: function(id) {
      var self = this;
      var permiso = control.check("contactos");
      if (control.check("consultas")>0) {
        var permiso = control.check("consultas");
      }
      if (permiso > 0) {
        var contacto = new app.models.Contacto({"id":id});
        contacto.fetch({
          "success":function() {
            var edit = new app.views.ContactoFichaView({
              model: contacto,
              permiso: permiso,
              contacto_tab_principal: "seguimiento", // Abrimos en la parte de seguimiento
            });
            self.mostrar({
              "top" : edit.el,
              "top_height": "100%",
              "full": 1,
            });
          }
        });
      }
    },

    ver_cliente_acciones: function(id) {
      var self = this;
      var permiso = Math.max(control.check("clientes"),control.check("contactos"),control.check("consultas"));
      if (permiso > 0) {
        var modulo = control.get("contactos");
        var cliente = new app.models.Cliente({"id":id});

        $.ajax({
          "url": "clientes/function/check_blur",
          "type": "post",
          "dataType": "json",
          "data": {
            "perfil": PERFIL,
            "id_empresa": ID_EMPRESA,
            "id_cliente": id,
            "id_usuario": ID_USUARIO,
          },
          "success": function(r) {
            cliente.fetch({
              "success":function() {
                var edit = new app.views.ClienteTimelineView({
                  model: cliente,
                  titulo_modulo: modulo.title,
                  clase_modulo: modulo.clase,
                  tipo_cliente: "cliente",
                  permiso: permiso,
                  blur: r.blur,
                });
                self.mostrar({
                  "top" : edit.el,
                });
              }
            });
          },
        });

      }
    },

    ver_paciente_acciones: function(id) {
      var self = this;
      var permiso = control.check("pacientes");
      if (permiso > 0) {
        var modulo = control.get("pacientes");
        var paciente = new app.models.Cliente({"id":id});
        paciente.fetch({
          "success":function() {
            var edit = new app.views.ClienteTimelineView({
              model: paciente,
              titulo_modulo: modulo.title,
              clase_modulo: modulo.clase,
              tipo_cliente: "paciente",
              permiso: permiso
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    },

    ver_alumno_acciones: function(id) {
      var self = this;
      var permiso = control.check("alumnos");
      if (permiso > 0) {
        var modulo = control.get("alumnos");
        var alumno = new app.models.Cliente({"id":id});
        alumno.fetch({
          "success":function() {
            var edit = new app.views.ClienteTimelineView({
              model: alumno,
              titulo_modulo: modulo.title,
              clase_modulo: modulo.clase,
              tipo_cliente: "alumno",
              permiso: permiso
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    },

    ver_docente_acciones: function(id) {
      var self = this;
      var permiso = control.check("docentes");
      if (permiso > 0) {
        var modulo = control.get("docentes");
        var docente = new app.models.Cliente({"id":id});
        docente.fetch({
          "success":function() {
            var edit = new app.views.ClienteTimelineView({
              model: docente,
              titulo_modulo: modulo.title,
              clase_modulo: modulo.clase,
              tipo_cliente: "docente",
              permiso: permiso
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    },

    ver_tutor_acciones: function(id) {
      var self = this;
      var permiso = control.check("tutores");
      if (permiso > 0) {
        var modulo = control.get("tutores");
        var tutor = new app.models.Cliente({"id":id});
        tutor.fetch({
          "success":function() {
            var edit = new app.views.ClienteTimelineView({
              model: tutor,
              titulo_modulo: modulo.title,
              clase_modulo: modulo.clase,
              tipo_cliente: "tutor",
              permiso: permiso
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    },

    ver_turnos_servicios: function() {
      var permiso = control.check("turnos_servicios");
      if (permiso > 0) {
        var view = new app.views.TurnosServiciosTableView({
          collection: new app.collections.TurnosServicios(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_turno_servicio: function(id) {
      var self = this;
      var permiso = control.check("turnos_servicios");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.turno_servicioEditView = new app.views.TurnoServicioEditView({
            model: new app.models.TurnoServicio(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.turno_servicioEditView.el,
          });
        } else {
          var turno_servicio = new app.models.TurnoServicio({"id":id});
          turno_servicio.fetch({
            "success":function() {
              var edit = new app.views.TurnoServicioEditView({
                model: turno_servicio,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_profesionales: function() {
      var permiso = control.check("profesionales");
      if (permiso > 0) {
        var view = new app.views.ProfesionalesTableView({
          collection: new app.collections.Profesionales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_profesional: function(id) {
      var self = this;
      var permiso = control.check("profesionales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.profesionalEditView = new app.views.ProfesionalEditView({
            model: new app.models.Profesional(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.profesionalEditView.el,
          });
        } else {
          var profesional = new app.models.Profesional({"id":id});
          profesional.fetch({
            "success":function() {
              var edit = new app.views.ProfesionalEditView({
                model: profesional,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_pacientes: function() {
      var permiso = control.check("pacientes");
      if (permiso > 0) {
        var view = new app.views.PacientesTableView({
          collection: new app.collections.Pacientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_paciente: function(id) {
      var self = this;
      var permiso = control.check("pacientes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.pacienteEditView = new app.views.PacienteEditView({
            model: new app.models.Paciente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.pacienteEditView.el,
          });
        } else {
          var paciente = new app.models.Paciente({"id":id});
          paciente.fetch({
            "success":function() {
              var edit = new app.views.PacienteEditView({
                model: paciente,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_afiliados: function() {
      var permiso = control.check("afiliados");
      if (permiso > 0) {
        if (permiso < 3) {
          location.href="app/#afiliado";
          return;
        }
        var view = new app.views.AfiliadosTableView({
          collection: new app.collections.Afiliados(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_afiliado: function(id) {
      var self = this;
      var permiso = control.check("afiliados");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.afiliadoEditView = new app.views.AfiliadoEditView({
            model: new app.models.Afiliado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.afiliadoEditView.el,
          });
        } else {
          var afiliado = new app.models.Afiliado({"id":id});
          afiliado.fetch({
            "success":function() {
              var edit = new app.views.AfiliadoEditView({
                model: afiliado,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    ver_tareas: function() {
        var permiso = control.check("tareas");
        if (permiso > 0) {
              if (ID_EMPRESA == 228) { 
              app.views.tareasListadoView = new app.views.TareasListadoView({
                collection: new app.collections.Tareas(),
                permiso: permiso
              });    
              this.mostrar({
                "top" : app.views.tareasListadoView.el,
              });
              } else {
              app.views.tareasTableView = new app.views.TareasTableView({
                collection: new app.collections.Tareas(),
                permiso: permiso
              });    
              this.mostrar({
                "top" : app.views.tareasTableView.el,
              });
          }
        }
    },

    ver_cheques: function() {
      var permiso = control.check("cheques");
      if (permiso > 0) {
        app.views.chequesTableView = new app.views.ChequesTableView({
          collection: new app.collections.Cheques(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.chequesTableView.el,
          "full": 1,
        });
      }
    },
    ver_cheque: function(id) {
      var self = this;
      var permiso = control.check("cheques");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.chequeEditView = new app.views.ChequeEditView({
            model: new app.models.Cheque(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.chequeEditView.el,
          });
        } else {
          var cheque = new app.models.Cheque({ "id": id });
          cheque.fetch({
            "success":function() {
              app.views.chequeEditView = new app.views.ChequeEditView({
                model: cheque,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.chequeEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_cargar_bobinas: function() {
      var permiso = control.check("bobinas");
      if (permiso > 0) {
        var view = new app.views.CargarBobinasView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_bobinas: function() {
      var permiso = control.check("bobinas");
      if (permiso > 0) {
        app.views.bobinasTableView = new app.views.BobinasTableView({
          collection: new app.collections.Bobinas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.bobinasTableView.el,
        });
      }
    },
    ver_bobina: function(id) {
      var self = this;
      var permiso = control.check("bobinas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.bobinaEditView = new app.views.BobinaEditView({
            model: new app.models.Bobina(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.bobinaEditView.el,
          });
        } else {
          var bobina = new app.models.Bobina({ "id": id });
          bobina.fetch({
            "success":function() {
              app.views.bobinaEditView = new app.views.BobinaEditView({
                model: bobina,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.bobinaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_bobinas: function() {
      var permiso = control.check("tipos_bobinas");
      if (permiso > 0) {
        app.views.tipos_bobinasTableView = new app.views.TiposBobinasTableView({
          collection: new app.collections.TiposBobinas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_bobinasTableView.el,
        });
      }
    },
    ver_tipo_bobina: function(id) {
      var self = this;
      var permiso = control.check("tipos_bobinas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_bobinaEditView = new app.views.TipoBobinaEditView({
            model: new app.models.TipoBobina(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_bobinaEditView.el,
          });
        } else {
          var tipo_bobina = new app.models.TipoBobina({ "id": id });
          tipo_bobina.fetch({
            "success":function() {
              app.views.tipo_bobinaEditView = new app.views.TipoBobinaEditView({
                model: tipo_bobina,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_bobinaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_proveedores: function() {
      var permiso = control.check("proveedores");
      if (permiso > 0) {
        app.views.proveedoresTableView = new app.views.ProveedoresTableView({
          collection: new app.collections.Proveedores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.proveedoresTableView.el,
        });
      }
    },
    ver_proveedor: function(id) {
      var self = this;
      var permiso = control.check("proveedores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.proveedorEditView = new app.views.ProveedorEditView({
            model: new app.models.Proveedor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.proveedorEditView.el,
          });
        } else {
          var proveedor = new app.models.Proveedor({ "id": id });
          proveedor.fetch({
            "success":function() {
              app.views.proveedorEditView = new app.views.ProveedorEditView({
                model: proveedor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.proveedorEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_vendedores: function() {
      var permiso = control.check("vendedores");
      if (permiso > 0) {
        app.views.vendedoresTableView = new app.views.VendedoresTableView({
          collection: new app.collections.Vendedores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.vendedoresTableView.el,
        });
      }
    },
    ver_vendedor: function(id) {
      var self = this;
      var permiso = control.check("vendedores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.vendedorEditView = new app.views.VendedorEditView({
            model: new app.models.Vendedor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.vendedorEditView.el,
          });
        } else {
          var vendedor = new app.models.Vendedor({ "id": id });
          vendedor.fetch({
            "success":function() {
              app.views.vendedorEditView = new app.views.VendedorEditView({
                model: vendedor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.vendedorEditView.el,
              });
            }
          });
        }
      }                
    },            

    ver_mesas: function() {
      var permiso = control.check("mesas");
      if (permiso > 0) {
        var view = new app.views.SalonesView({
          permiso: permiso,
          edicion: true,
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_organizadores_eventos: function() {
      var permiso = control.check("organizadores_eventos");
      if (permiso > 0) {
        app.views.organizadores_eventosTableView = new app.views.OrganizadoresEventosTableView({
          collection: new app.collections.OrganizadoresEventos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.organizadores_eventosTableView.el,
        });
      }
    },
    ver_organizador_evento: function(id) {
      var self = this;
      var permiso = control.check("organizadores_eventos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.organizador_eventoEditView = new app.views.OrganizadorEventoEditView({
            model: new app.models.OrganizadorEvento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.organizador_eventoEditView.el,
          });
        } else {
          var organizador_evento = new app.models.OrganizadorEvento({ "id": id });
          organizador_evento.fetch({
            "success":function() {
              app.views.organizador_eventoEditView = new app.views.OrganizadorEventoEditView({
                model: organizador_evento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.organizador_eventoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_conferencistas: function() {
      var permiso = control.check("conferencistas");
      if (permiso > 0) {
        app.views.conferencistasTableView = new app.views.ConferencistasTableView({
          collection: new app.collections.Conferencistas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.conferencistasTableView.el,
        });
      }
    },
    ver_conferencista: function(id) {
      var self = this;
      var permiso = control.check("conferencistas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.conferencistaEditView = new app.views.ConferencistaEditView({
            model: new app.models.Conferencista(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.conferencistaEditView.el,
          });
        } else {
          var conferencista = new app.models.Conferencista({ "id": id });
          conferencista.fetch({
            "success":function() {
              app.views.conferencistaEditView = new app.views.ConferencistaEditView({
                model: conferencista,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.conferencistaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_not_eventos: function() {
      var permiso = control.check("not_eventos");
      if (permiso > 0) {
        app.views.not_eventosTableView = new app.views.NotEventosTableView({
          collection: new app.collections.NotEventos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.not_eventosTableView.el,
        });
      }
    },
    ver_not_evento: function(id) {
      var self = this;
      var permiso = control.check("not_eventos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.not_eventoEditView = new app.views.NotEventoEditView({
            model: new app.models.NotEvento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.not_eventoEditView.el,
          });
          workspace.crear_editor('not_evento_texto',{"toolbar":"Basic"});
          // Eliminamos los editores para volverlos a crear
          var en = CKEDITOR.instances["not_evento_texto_en"];
          if (en) CKEDITOR.remove(en);
          var pt = CKEDITOR.instances["not_evento_texto_pt"];
          if (pt) CKEDITOR.remove(pt);

        } else {
          var not_evento = new app.models.NotEvento({ "id": id });
          not_evento.fetch({
            "success":function() {
              app.views.not_eventoEditView = new app.views.NotEventoEditView({
                model: not_evento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.not_eventoEditView.el,
              });
              workspace.crear_editor('not_evento_texto',{"toolbar":"Basic"});
              // Eliminamos los editores para volverlos a crear
              var en = CKEDITOR.instances["not_evento_texto_en"];
              if (en) CKEDITOR.remove(en);
              var pt = CKEDITOR.instances["not_evento_texto_pt"];
              if (pt) CKEDITOR.remove(pt);

            }
          });
        }
      }                
    },


    ver_fot_trabajos: function() {
      var permiso = control.check("fot_trabajos");
      if (permiso > 0) {
        app.views.fot_trabajosTableView = new app.views.FotTrabajosTableView({
          collection: new app.collections.FotTrabajos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.fot_trabajosTableView.el,
        });
      }
    },
    ver_fot_trabajo: function(id) {
      var self = this;
      var permiso = control.check("fot_trabajos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.fot_trabajoEditView = new app.views.FotTrabajoEditView({
            model: new app.models.FotTrabajo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.fot_trabajoEditView.el,
          });
          workspace.crear_editor('fot_trabajo_texto',{"toolbar":"Basic"});
          // Eliminamos los editores para volverlos a crear
          var en = CKEDITOR.instances["fot_trabajo_texto_en"];
          if (en) CKEDITOR.remove(en);
          var pt = CKEDITOR.instances["fot_trabajo_texto_pt"];
          if (pt) CKEDITOR.remove(pt);

        } else {
          var fot_trabajo = new app.models.FotTrabajo({ "id": id });
          fot_trabajo.fetch({
            "success":function() {
              app.views.fot_trabajoEditView = new app.views.FotTrabajoEditView({
                model: fot_trabajo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.fot_trabajoEditView.el,
              });
              workspace.crear_editor('fot_trabajo_texto',{"toolbar":"Basic"});
              // Eliminamos los editores para volverlos a crear
              var en = CKEDITOR.instances["fot_trabajo_texto_en"];
              if (en) CKEDITOR.remove(en);
              var pt = CKEDITOR.instances["fot_trabajo_texto_pt"];
              if (pt) CKEDITOR.remove(pt);

            }
          });
        }
      }                
    },

    ver_cajas_actualizadas: function() {
      var permiso = control.check("cajas_actualizadas");
      if (permiso > 0) {
        app.views.cajas_actualizadasTableView = new app.views.CajasActualizadasTableView({
          collection: new app.collections.CajasActualizadas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cajas_actualizadasTableView.el,
        });
      }
    },

    ver_cajas: function(todas) {
      var permiso = control.check("cajas");
      if (permiso > 0) {
        var cajas = new app.collections.Cajas();
        if (todas == undefined) window.cajas_ver_todas = 1;
        else window.cajas_ver_todas = -1;
        console.log(window.cajas_ver_todas);
        if (ID_USUARIO == 1299) window.cajas_tipo = 0;
        app.views.cajasTableView = new app.views.CajasTableView({
          collection: cajas,
          permiso: permiso,
        });    
        this.mostrar({
          "top" : app.views.cajasTableView.el,
        });
      }
    },
    ver_caja: function(id) {
      var self = this;
      var permiso = control.check("cajas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.cajaEditView = new app.views.CajaEditView({
            model: new app.models.Caja(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.cajaEditView.el,
          });
        } else {
          var caja = new app.models.Caja({ "id": id });
          caja.fetch({
            "success":function() {
              app.views.cajaEditView = new app.views.CajaEditView({
                model: caja,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.cajaEditView.el,
              });
            }
          });
        }
      }                
    },


    ver_reglas_ofertas: function() {
      var permiso = control.check("reglas_ofertas");
      if (permiso > 0) {
        app.views.reglas_ofertasTableView = new app.views.ReglasOfertasTableView({
          collection: new app.collections.ReglasOfertas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.reglas_ofertasTableView.el,
        });
      }
    },
    ver_regla_oferta: function(id) {
      var self = this;
      var permiso = control.check("reglas_ofertas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.regla_ofertaEditView = new app.views.ReglaOfertaEditView({
            model: new app.models.ReglaOferta({
              articulos:[],
              sucursales:[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.regla_ofertaEditView.el,
          });
        } else {
          var regla_oferta = new app.models.ReglaOferta({ "id": id });
          regla_oferta.fetch({
            "success":function() {
              app.views.regla_ofertaEditView = new app.views.ReglaOfertaEditView({
                model: regla_oferta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.regla_ofertaEditView.el,
              });
            }
          });
        }
      }                
    },
    ver_regla_oferta_2: function(id) {
      var self = this;
      var permiso = control.check("reglas_ofertas");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.ReglaOfertaSinArticulosEditView({
            model: new app.models.ReglaOferta({
              articulos:[],
              sucursales:[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var regla_oferta = new app.models.ReglaOferta({ "id": id });
          regla_oferta.fetch({
            "success":function() {
              var view = new app.views.ReglaOfertaSinArticulosEditView({
                model: regla_oferta,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    },    

    ver_cipal_invitaciones: function() {
      var permiso = control.check("cipal_invitaciones");
      if (permiso > 0) {
        app.views.cipal_invitacionesTableView = new app.views.CipalInvitacionesTableView({
          collection: new app.collections.CipalInvitaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cipal_invitacionesTableView.el,
        });
      }
    },
    
    ver_marcas: function() {
      var permiso = control.check("marcas");
      if (permiso > 0) {
        app.views.marcasTableView = new app.views.MarcasTableView({
          collection: new app.collections.Marcas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.marcasTableView.el,
        });
      }
    },
    ver_marca: function(id) {
      var self = this;
      var permiso = control.check("marcas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.marcaEditView = new app.views.MarcaEditView({
            model: new app.models.Marca(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.marcaEditView.el,
          });
        } else {
          var marca = new app.models.Marca({ "id": id });
          marca.fetch({
            "success":function() {
              app.views.marcaEditView = new app.views.MarcaEditView({
                model: marca,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.marcaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_toque_categorias: function() {
      var permiso = control.check("toque_categorias");
      if (permiso > 0) {
        app.views.toque_categoriasTableView = new app.views.ToqueCategoriasTableView({
          collection: new app.collections.ToqueCategorias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.toque_categoriasTableView.el,
        });
      }
    },
    ver_toque_categoria: function(id) {
      var self = this;
      var permiso = control.check("toque_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.toque_categoriaEditView = new app.views.ToqueCategoriaEditView({
            model: new app.models.ToqueCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.toque_categoriaEditView.el,
          });
        } else {
          var toque_categoria = new app.models.ToqueCategoria({ "id": id });
          toque_categoria.fetch({
            "success":function() {
              app.views.toque_categoriaEditView = new app.views.ToqueCategoriaEditView({
                model: toque_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.toque_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_milling_halloffames: function() {
      var permiso = control.check("milling_halloffames");
      if (permiso > 0) {
        app.views.milling_halloffamesTableView = new app.views.MillingHallOfFamesTableView({
          collection: new app.collections.MillingHallOfFames(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.milling_halloffamesTableView.el,
        });
      }
    },
    ver_milling_halloffame: function(id) {
      var self = this;
      var permiso = control.check("milling_halloffames");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.milling_halloffameEditView = new app.views.MillingHallOfFameEditView({
            model: new app.models.MillingHallOfFame(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.milling_halloffameEditView.el,
          });
          if ($("#milling_halloffames_texto").length > 0) workspace.crear_editor('milling_halloffames_texto',{
            "toolbar":"Basic"
          });
          if ($("#milling_halloffames_comite").length > 0) workspace.crear_editor('milling_halloffames_comite',{
            "toolbar":"Basic"
          });
        } else {
          var milling_halloffame = new app.models.MillingHallOfFame({ "id": id });
          milling_halloffame.fetch({
            "success":function() {
              app.views.milling_halloffameEditView = new app.views.MillingHallOfFameEditView({
                model: milling_halloffame,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.milling_halloffameEditView.el,
              });
              if ($("#milling_halloffames_texto").length > 0) workspace.crear_editor('milling_halloffames_texto',{
                "toolbar":"Basic"
              });
              if ($("#milling_halloffames_comite").length > 0) workspace.crear_editor('milling_halloffames_comite',{
                "toolbar":"Basic"
              });
            }
          });
        }
      }                
    },

    ver_calm_escenas: function() {
      var permiso = control.check("calm_escenas");
      if (permiso > 0) {
        app.views.calm_escenasTableView = new app.views.CalmEscenasTableView({
          collection: new app.collections.CalmEscenas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.calm_escenasTableView.el,
        });
      }
    },
    ver_calm_escena: function(id) {
      var self = this;
      var permiso = control.check("calm_escenas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.calm_escenaEditView = new app.views.CalmEscenaEditView({
            model: new app.models.CalmEscena(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.calm_escenaEditView.el,
          });
        } else {
          var calm_escena = new app.models.CalmEscena({ "id": id });
          calm_escena.fetch({
            "success":function() {
              app.views.calm_escenaEditView = new app.views.CalmEscenaEditView({
                model: calm_escena,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.calm_escenaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_calm_categorias: function() {
      var permiso = control.check("calm_categorias");
      if (permiso > 0) {
        app.views.calm_categoriasTableView = new app.views.CalmCategoriasTableView({
          collection: new app.collections.CalmCategorias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.calm_categoriasTableView.el,
        });
      }
    },
    ver_calm_categoria: function(id) {
      var self = this;
      var permiso = control.check("calm_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.calm_categoriaEditView = new app.views.CalmCategoriaEditView({
            model: new app.models.CalmCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.calm_categoriaEditView.el,
          });
        } else {
          var calm_categoria = new app.models.CalmCategoria({ "id": id });
          calm_categoria.fetch({
            "success":function() {
              app.views.calm_categoriaEditView = new app.views.CalmCategoriaEditView({
                model: calm_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.calm_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_calm_cursos: function() {
      var permiso = control.check("calm_cursos");
      if (permiso > 0) {
        app.views.calm_cursosTableView = new app.views.CalmCursosTableView({
          collection: new app.collections.CalmCursos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.calm_cursosTableView.el,
        });
      }
    },
    ver_calm_curso: function(id) {
      var self = this;
      var permiso = control.check("calm_cursos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.calm_cursoEditView = new app.views.CalmCursoEditView({
            model: new app.models.CalmCurso(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.calm_cursoEditView.el,
          });
          workspace.crear_editor('calm_curso_texto',{"toolbar":"Basic"});
        } else {
          var calm_curso = new app.models.CalmCurso({ "id": id });
          calm_curso.fetch({
            "success":function() {
              app.views.calm_cursoEditView = new app.views.CalmCursoEditView({
                model: calm_curso,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.calm_cursoEditView.el,
              });
              workspace.crear_editor('calm_curso_texto',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },  

    ver_calm_clientes: function() {
      var permiso = control.check("calm_clientes");
      if (permiso > 0) {
        app.views.calm_clientesTableView = new app.views.CalmClientesTableView({
          collection: new app.collections.CalmClientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.calm_clientesTableView.el,
        });
      }
    },
    ver_calm_cliente: function(id) {
      var self = this;
      var permiso = control.check("calm_clientes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.calm_clienteEditView = new app.views.CalmClienteEditView({
            model: new app.models.CalmCliente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.calm_clienteEditView.el,
          });
          workspace.crear_editor('calm_cliente_texto',{"toolbar":"Basic"});
        } else {
          var calm_cliente = new app.models.CalmCliente({ "id": id });
          calm_cliente.fetch({
            "success":function() {
              app.views.calm_clienteEditView = new app.views.CalmClienteEditView({
                model: calm_cliente,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.calm_clienteEditView.el,
              });
              workspace.crear_editor('calm_curso_texto',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },


    ver_cursos_categorias: function() {
      var permiso = control.check("cursos");
      if (permiso > 0) {
        app.views.cursos_categoriasTableView = new app.views.CursosCategoriasTableView({
          collection: new app.collections.CursosCategorias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cursos_categoriasTableView.el,
        });
      }
    },
    ver_curso_categoria: function(id) {
      var self = this;
      var permiso = control.check("cursos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.curso_categoriaEditView = new app.views.CursoCategoriaEditView({
            model: new app.models.CursoCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.curso_categoriaEditView.el,
          });
        } else {
          var curso_categoria = new app.models.CursoCategoria({ "id": id });
          curso_categoria.fetch({
            "success":function() {
              app.views.curso_categoriaEditView = new app.views.CursoCategoriaEditView({
                model: curso_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.curso_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_cursos: function() {
      var permiso = control.check("cursos");
      if (permiso > 0) {
        app.views.cursosTableView = new app.views.CursosTableView({
          collection: new app.collections.Cursos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cursosTableView.el,
        });
      }
    },
    ver_curso: function(id) {
      var self = this;
      var permiso = control.check("cursos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.cursoEditView = new app.views.CursoEditView({
            model: new app.models.Curso(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.cursoEditView.el,
          });
          workspace.crear_editor('curso_texto',{"toolbar":"Basic"});
        } else {
          var curso = new app.models.Curso({ "id": id });
          curso.fetch({
            "success":function() {
              app.views.cursoEditView = new app.views.CursoEditView({
                model: curso,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.cursoEditView.el,
              });
              workspace.crear_editor('curso_texto',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },  


    ver_env_zonas: function() {
      var permiso = control.check("env_zonas");
      if (permiso > 0) {
        app.views.env_zonasTableView = new app.views.EnvZonasTableView({
          collection: new app.collections.EnvZonas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.env_zonasTableView.el,
        });
      }
    },
    ver_env_zona: function(id) {
      var self = this;
      var permiso = control.check("env_zonas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.env_zonaEditView = new app.views.EnvZonaEditView({
            model: new app.models.EnvZona(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.env_zonaEditView.el,
          });
        } else {
          var env_zona = new app.models.EnvZona({ "id": id });
          env_zona.fetch({
            "success":function() {
              app.views.env_zonaEditView = new app.views.EnvZonaEditView({
                model: env_zona,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.env_zonaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_asuntos: function() {
      app.views.asuntosTableView = new app.views.AsuntosTableView({
        collection: new app.collections.Asuntos(),
      });    
      this.mostrar({
        "top" : app.views.asuntosTableView.el,
      });
    },
    ver_asunto: function(id) {
      var self = this;
      if (id == undefined) {
        app.views.asuntoEditView = new app.views.AsuntoEditView({
          model: new app.models.Asunto(),
        });
        this.mostrar({
          "top" : app.views.asuntoEditView.el,
        });
      } else {
        var asunto = new app.models.Asunto({ "id": id });
        asunto.fetch({
          "success":function() {
            app.views.asuntoEditView = new app.views.AsuntoEditView({
              model: asunto,
            });
            self.mostrar({
              "top" : app.views.asuntoEditView.el,
            });
          }
        });
      }
    },

    ver_departamentos_comerciales: function() {
      var permiso = control.check("departamentos_comerciales");
      if (permiso > 0) {
        app.views.departamentos_comercialesTableView = new app.views.DepartamentosComercialesTableView({
          collection: new app.collections.DepartamentosComerciales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.departamentos_comercialesTableView.el,
        });
      }
    },
    ver_departamento_comercial: function(id) {
      var self = this;
      var permiso = control.check("departamentos_comerciales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.departamento_comercialEditView = new app.views.DepartamentoComercialEditView({
            model: new app.models.DepartamentoComercial(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.departamento_comercialEditView.el,
          });
        } else {
          var departamento_comercial = new app.models.DepartamentoComercial({ "id": id });
          departamento_comercial.fetch({
            "success":function() {
              app.views.departamento_comercialEditView = new app.views.DepartamentoComercialEditView({
                model: departamento_comercial,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.departamento_comercialEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_tipos_alicuotas_iva: function() {
      var permiso = control.check("tipos_alicuotas_iva");
      if (permiso > 0) {
        app.views.tipos_alicuotas_ivaTableView = new app.views.TiposAlicuotasIvaTableView({
          collection: new app.collections.TiposAlicuotasIva(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_alicuotas_ivaTableView.el,
        });
      }
    },
    ver_tipo_alicuota_iva: function(id) {
      var self = this;
      var permiso = control.check("tipos_alicuotas_iva");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_alicuota_ivaEditView = new app.views.TipoAlicuotaIvaEditView({
            model: new app.models.TipoAlicuotaIva(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_alicuota_ivaEditView.el,
          });
        } else {
          var tipo_alicuota_iva = new app.models.TipoAlicuotaIva({ "id": id });
          tipo_alicuota_iva.fetch({
            "success":function() {
              app.views.tipo_alicuota_ivaEditView = new app.views.TipoAlicuotaIvaEditView({
                model: tipo_alicuota_iva,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_alicuota_ivaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_pres_documentaciones: function() {
      var permiso = control.check("pres_documentaciones");
      if (permiso > 0) {
        app.views.pres_documentacionesTableView = new app.views.PresDocumentacionesTableView({
          collection: new app.collections.PresDocumentaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pres_documentacionesTableView.el,
        });
      }
    },
    ver_pres_documentacion: function(id) {
      var self = this;
      var permiso = control.check("pres_documentaciones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.pres_documentacionEditView = new app.views.PresDocumentacionEditView({
            model: new app.models.PresDocumentacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.pres_documentacionEditView.el,
          });
        } else {
          var pres_documentacion = new app.models.PresDocumentacion({ "id": id });
          pres_documentacion.fetch({
            "success":function() {
              app.views.pres_documentacionEditView = new app.views.PresDocumentacionEditView({
                model: pres_documentacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.pres_documentacionEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_pres_listado_reingreso: function() {
      var permiso = control.check("pres_listado_reingreso");
      if (permiso > 0) {
        var view = new app.views.PresListadoReingresoTableView({
          collection: new app.collections.PresListadoReingreso(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_pres_listado_mora: function() {
      var permiso = control.check("pres_listado_mora");
      if (permiso > 0) {
        app.views.pres_listado_moraTableView = new app.views.PresListadoMoraTableView({
          collection: new app.collections.PresListadoMora(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pres_listado_moraTableView.el,
        });
      }
    },
    ver_estadisticas_prestamos_activos: function() {
      var permiso = control.check("estadisticas_prestamos_activos");
      if (permiso > 0) {
        app.views.estadisticas_prestamos_activosTableView = new app.views.EstadisticasPrestamosActivosTableView({
          collection: new app.collections.EstadisticasPrestamosActivos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.estadisticas_prestamos_activosTableView.el,
        });
      }
    },
    ver_estadisticas_prestamos_tareas: function() {
      var permiso = control.check("estadisticas_prestamos_tareas");
      if (permiso > 0) {
        var v = new app.views.EstadisticasPrestamosTareasResultados({
          model: new app.models.EstadisticasPrestamosTareas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : v.el,
        });
      }
    },
    ver_pres_buenos_clientes: function() {
      var permiso = control.check("pres_buenos_clientes");
      if (permiso > 0) {
        app.views.pres_buenos_clientesTableView = new app.views.PresBuenosClientesTableView({
          collection: new app.collections.PresBuenosClientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pres_buenos_clientesTableView.el,
        });
      }
    },
    ver_pres_planes_credito: function() {
      var permiso = control.check("pres_planes_credito");
      if (permiso > 0) {
        app.views.pres_planes_creditoTableView = new app.views.PresPlanesCreditoTableView({
          collection: new app.collections.PresPlanesCredito(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pres_planes_creditoTableView.el,
        });
      }
    },
    ver_pres_plan_credito: function(id) {
      var self = this;
      var permiso = control.check("pres_planes_credito");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.pres_plan_creditoEditView = new app.views.PresPlanCreditoEditView({
            model: new app.models.PresPlanCredito(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.pres_plan_creditoEditView.el,
          });
        } else {
          var pres_plan_credito = new app.models.PresPlanCredito({ "id": id });
          pres_plan_credito.fetch({
            "success":function() {
              app.views.pres_plan_creditoEditView = new app.views.PresPlanCreditoEditView({
                model: pres_plan_credito,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.pres_plan_creditoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_razones_sociales: function() {
      var permiso = control.check("razones_sociales");
      if (permiso > 0) {
        app.views.razones_socialesTableView = new app.views.RazonesSocialesTableView({
          collection: new app.collections.RazonesSociales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.razones_socialesTableView.el,
        });
      }
    },
    ver_razon_social: function(id) {
      var self = this;
      var permiso = control.check("razones_sociales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.razon_socialEditView = new app.views.RazonSocialEditView({
            model: new app.models.RazonSocial(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.razon_socialEditView.el,
          });
        } else {
          var razon_social = new app.models.RazonSocial({ "id": id });
          razon_social.fetch({
            "success":function() {
              app.views.razon_socialEditView = new app.views.RazonSocialEditView({
                model: razon_social,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.razon_socialEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_especialidades: function() {
      var permiso = control.check("especialidades");
      if (permiso > 0) {
        app.views.especialidadesTableView = new app.views.EspecialidadesTableView({
          collection: new app.collections.Especialidades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.especialidadesTableView.el,
        });
      }
    },
    ver_especialidad: function(id) {
      var self = this;
      var permiso = control.check("especialidades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.especialidadEditView = new app.views.EspecialidadEditView({
            model: new app.models.Especialidad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.especialidadEditView.el,
          });
        } else {
          var especialidad = new app.models.Especialidad({ "id": id });
          especialidad.fetch({
            "success":function() {
              app.views.especialidadEditView = new app.views.EspecialidadEditView({
                model: especialidad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.especialidadEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_obras_sociales: function() {
      var permiso = control.check("obras_sociales");
      if (permiso > 0) {
        app.views.obras_socialesTableView = new app.views.ObrasSocialesTableView({
          collection: new app.collections.ObrasSociales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.obras_socialesTableView.el,
        });
      }
    },
    ver_obra_social: function(id) {
      var self = this;
      var permiso = control.check("obras_sociales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.obra_socialEditView = new app.views.ObraSocialEditView({
            model: new app.models.ObraSocial(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.obra_socialEditView.el,
          });
        } else {
          var obra_social = new app.models.ObraSocial({ "id": id });
          obra_social.fetch({
            "success":function() {
              app.views.obra_socialEditView = new app.views.ObraSocialEditView({
                model: obra_social,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.obra_socialEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_pacientes: function() {
      var permiso = control.check("tipos_pacientes");
      if (permiso > 0) {
        app.views.tipos_pacientesTableView = new app.views.TiposPacientesTableView({
          collection: new app.collections.TiposPacientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_pacientesTableView.el,
        });
      }
    },
    ver_tipo_paciente: function(id) {
      var self = this;
      var permiso = control.check("tipos_pacientes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_pacienteEditView = new app.views.TipoPacienteEditView({
            model: new app.models.TipoPaciente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_pacienteEditView.el,
          });
        } else {
          var tipo_paciente = new app.models.TipoPaciente({ "id": id });
          tipo_paciente.fetch({
            "success":function() {
              app.views.tipo_pacienteEditView = new app.views.TipoPacienteEditView({
                model: tipo_paciente,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_pacienteEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_terapias: function() {
      var permiso = control.check("tipos_terapias");
      if (permiso > 0) {
        app.views.tipos_terapiasTableView = new app.views.TiposTerapiasTableView({
          collection: new app.collections.TiposTerapias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_terapiasTableView.el,
        });
      }
    },
    ver_tipo_terapia: function(id) {
      var self = this;
      var permiso = control.check("tipos_terapias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_terapiaEditView = new app.views.TipoTerapiaEditView({
            model: new app.models.TipoTerapia(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_terapiaEditView.el,
          });
        } else {
          var tipo_terapia = new app.models.TipoTerapia({ "id": id });
          tipo_terapia.fetch({
            "success":function() {
              app.views.tipo_terapiaEditView = new app.views.TipoTerapiaEditView({
                model: tipo_terapia,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_terapiaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_atenciones: function() {
      var permiso = control.check("tipos_atenciones");
      if (permiso > 0) {
        app.views.tipos_atencionesTableView = new app.views.TiposAtencionesTableView({
          collection: new app.collections.TiposAtenciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_atencionesTableView.el,
        });
      }
    },
    ver_tipo_atencion: function(id) {
      var self = this;
      var permiso = control.check("tipos_atenciones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_atencionEditView = new app.views.TipoAtencionEditView({
            model: new app.models.TipoAtencion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_atencionEditView.el,
          });
        } else {
          var tipo_atencion = new app.models.TipoAtencion({ "id": id });
          tipo_atencion.fetch({
            "success":function() {
              app.views.tipo_atencionEditView = new app.views.TipoAtencionEditView({
                model: tipo_atencion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_atencionEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_titulos: function() {
      var permiso = control.check("titulos");
      if (permiso > 0) {
        app.views.titulosTableView = new app.views.TitulosTableView({
          collection: new app.collections.Titulos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.titulosTableView.el,
        });
      }
    },
    ver_titulo: function(id) {
      var self = this;
      var permiso = control.check("titulos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tituloEditView = new app.views.TituloEditView({
            model: new app.models.Titulo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tituloEditView.el,
          });
        } else {
          var titulo = new app.models.Titulo({ "id": id });
          titulo.fetch({
            "success":function() {
              app.views.tituloEditView = new app.views.TituloEditView({
                model: titulo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tituloEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_formas_pago: function() {
      var permiso = control.check("formas_pago");
      if (permiso > 0) {
        app.views.formas_pagoTableView = new app.views.FormasPagoTableView({
          collection: new app.collections.FormasPago(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.formas_pagoTableView.el,
        });
      }
    },
    ver_forma_pago: function(id) {
      var self = this;
      var permiso = control.check("formas_pago");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.forma_pagoEditView = new app.views.FormaPagoEditView({
            model: new app.models.FormaPago(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.forma_pagoEditView.el,
          });
        } else {
          var forma_pago = new app.models.FormaPago({ "id": id });
          forma_pago.fetch({
            "success":function() {
              app.views.forma_pagoEditView = new app.views.FormaPagoEditView({
                model: forma_pago,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.forma_pagoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_marcas_vehiculos: function() {
      var permiso = control.check("marcas_vehiculos");
      if (permiso > 0) {
        app.views.marcas_vehiculosTableView = new app.views.MarcasVehiculosTableView({
          collection: new app.collections.MarcasVehiculos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.marcas_vehiculosTableView.el,
        });
      }
    },
    ver_marca_vehiculo: function(id) {
      var self = this;
      var permiso = control.check("marcas_vehiculos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.marca_vehiculoEditView = new app.views.MarcaVehiculoEditView({
            model: new app.models.MarcaVehiculo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.marca_vehiculoEditView.el,
          });
        } else {
          var marca_vehiculo = new app.models.MarcaVehiculo({ "id": id });
          marca_vehiculo.fetch({
            "success":function() {
              app.views.marca_vehiculoEditView = new app.views.MarcaVehiculoEditView({
                model: marca_vehiculo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.marca_vehiculoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_nacionalidades: function() {
      var permiso = control.check("nacionalidades");
      if (permiso > 0) {
        app.views.nacionalidadesTableView = new app.views.NacionalidadesTableView({
          collection: new app.collections.Nacionalidades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.nacionalidadesTableView.el,
        });
      }
    },
    ver_nacionalidad: function(id) {
      var self = this;
      var permiso = control.check("nacionalidades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.nacionalidadEditView = new app.views.NacionalidadEditView({
            model: new app.models.Nacionalidad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.nacionalidadEditView.el,
          });
        } else {
          var nacionalidad = new app.models.Nacionalidad({ "id": id });
          nacionalidad.fetch({
            "success":function() {
              app.views.nacionalidadEditView = new app.views.NacionalidadEditView({
                model: nacionalidad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.nacionalidadEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tripulantes: function() {
      var permiso = control.check("tripulantes");
      if (permiso > 0) {
        app.views.tripulantesTableView = new app.views.TripulantesTableView({
          collection: new app.collections.Tripulantes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tripulantesTableView.el,
        });
      }
    },
    ver_tripulante: function(id) {
      var self = this;
      var permiso = control.check("tripulantes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tripulanteEditView = new app.views.TripulanteEditView({
            model: new app.models.Tripulante(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tripulanteEditView.el,
          });
        } else {
          var tripulante = new app.models.Tripulante({ "id": id });
          tripulante.fetch({
            "success":function() {
              app.views.tripulanteEditView = new app.views.TripulanteEditView({
                model: tripulante,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tripulanteEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_videos: function() {
      var permiso = control.check("videos");
      if (permiso > 0) {
        app.views.videosTableView = new app.views.VideosTableView({
          collection: new app.collections.Videos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.videosTableView.el,
        });
      }
    },
    ver_video: function(id) {
      var self = this;
      var permiso = control.check("videos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.videoEditView = new app.views.VideoEditView({
            model: new app.models.Video(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.videoEditView.el,
          });
        } else {
          var video = new app.models.Video({ "id": id });
          video.fetch({
            "success":function() {
              app.views.videoEditView = new app.views.VideoEditView({
                model: video,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.videoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_chat_preguntas: function() {
      var permiso = control.check("chat_preguntas");
      if (permiso > 0) {
        app.views.chat_preguntasTableView = new app.views.ChatPreguntasTableView({
          collection: new app.collections.ChatPreguntas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.chat_preguntasTableView.el,
        });
      }
    },
    ver_chat_pregunta: function(id) {
      var self = this;
      var permiso = control.check("chat_preguntas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.chat_preguntaEditView = new app.views.ChatPreguntaEditView({
            model: new app.models.ChatPregunta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.chat_preguntaEditView.el,
          });
        } else {
          var chat_pregunta = new app.models.ChatPregunta({ "id": id });
          chat_pregunta.fetch({
            "success":function() {
              app.views.chat_preguntaEditView = new app.views.ChatPreguntaEditView({
                model: chat_pregunta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.chat_preguntaEditView.el,
              });
            }
          });
        }
      }                
    },
    ver_zetas: function() {
      var permiso = control.check("zetas");
      if (permiso > 0) {
        app.views.zetasTableView = new app.views.ZetasTableView({
          collection: new app.collections.Zetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.zetasTableView.el,
        });
      }
    },
    ver_zeta: function(id) {
      var self = this;
      var permiso = control.check("zetas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.zetaEditView = new app.views.ZetaEditView({
            model: new app.models.Zeta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.zetaEditView.el,
          });
        } else {
          var zeta = new app.models.Zeta({ "id": id });
          zeta.fetch({
            "success":function() {
              app.views.zetaEditView = new app.views.ZetaEditView({
                model: zeta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.zetaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_promociones: function() {
      var permiso = control.check("promociones");
      if (permiso > 0) {
        app.views.promocionesTableView = new app.views.PromocionesTableView({
          collection: new app.collections.Promociones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.promocionesTableView.el,
        });
      }
    },
    ver_promocion: function(id) {
      var self = this;
      var permiso = control.check("promociones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.promocionEditView = new app.views.PromocionEditView({
            model: new app.models.Promocion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.promocionEditView.el,
          });
        } else {
          var promocion = new app.models.Promocion({ "id": id });
          promocion.fetch({
            "success":function() {
              app.views.promocionEditView = new app.views.PromocionEditView({
                model: promocion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.promocionEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_sucursales: function() {
      var permiso = control.check("sucursales");
      if (permiso > 0) {
        app.views.sucursalesTableView = new app.views.SucursalesTableView({
          collection: new app.collections.Sucursales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sucursalesTableView.el,
        });
      }
    },
    ver_sucursal: function(id) {
      var self = this;
      var permiso = control.check("sucursales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sucursalEditView = new app.views.SucursalEditView({
            model: new app.models.Sucursal(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sucursalEditView.el,
          });
        } else {
          var sucursal = new app.models.Sucursal({ "id": id });
          sucursal.fetch({
            "success":function() {
              app.views.sucursalEditView = new app.views.SucursalEditView({
                model: sucursal,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sucursalEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_habitaciones: function() {
      var permiso = control.check("habitaciones");
      if (permiso > 0) {
        var view = new app.views.HabitacionesTableView({
          collection: new app.collections.Habitaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_habitacion: function(id) {
      var self = this;
      var permiso = control.check("habitaciones");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.HabitacionEditView({
            model: new app.models.Habitacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var habitacion = new app.models.Habitacion({ "id": id });
          habitacion.fetch({
            "success":function() {
              var view = new app.views.HabitacionEditView({
                model: habitacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_habitaciones: function() {
      var permiso = control.check("tipos_habitaciones");
      if (permiso > 0) {
        var view = new app.views.TiposHabitacionesTableView({
          collection: new app.collections.TiposHabitaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_tipo_habitacion: function(id) {
      var self = this;
      var permiso = control.check("tipos_habitaciones");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.TipoHabitacionEditView({
            model: new app.models.TipoHabitacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
          workspace.crear_editor('tipo_habitacion_texto',{
            "toolbar":"Basic"
          });
        } else {
          var tipo_habitacion = new app.models.TipoHabitacion({ "id": id });
          tipo_habitacion.fetch({
            "success":function() {
              var view = new app.views.TipoHabitacionEditView({
                model: tipo_habitacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
              workspace.crear_editor('tipo_habitacion_texto',{
                "toolbar":"Basic"
              });
            }
          });
        }
      }                
    },
    
    ver_cuentas_bancarias: function() {
      var permiso = control.check("cuentas_bancarias");
      if (permiso > 0) {
        app.views.cuentas_bancariasTableView = new app.views.CuentasBancariasTableView({
          collection: new app.collections.CuentasBancarias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cuentas_bancariasTableView.el,
        });
      }
    },
    ver_cuenta_bancaria: function(id) {
      var self = this;
      var permiso = control.check("cuentas_bancarias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.cuenta_bancariaEditView = new app.views.CuentaBancariaEditView({
            model: new app.models.CuentaBancaria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.cuenta_bancariaEditView.el,
          });
        } else {
          var cuenta_bancaria = new app.models.CuentaBancaria({ "id": id });
          cuenta_bancaria.fetch({
            "success":function() {
              app.views.cuenta_bancariaEditView = new app.views.CuentaBancariaEditView({
                model: cuenta_bancaria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.cuenta_bancariaEditView.el,
              });
            }
          });
        }
      }                
    },      

    ver_centros_costos: function() {
      var permiso = control.check("almacenes");
      if (permiso > 0) {
        var v = new app.views.CentrosCostosTableView({
          collection: new app.collections.CentrosCostos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : v.el,
        });
      }
    },
    ver_centro_costo: function(id) {
      var self = this;
      var permiso = control.check("almacenes");
      if (permiso > 0) {
        if (id == undefined) {
          var v = new app.views.CentroCostoEditView({
            model: new app.models.CentroCosto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : v.el,
          });
        } else {
          var centro_costo = new app.models.CentroCosto({ "id": id });
          centro_costo.fetch({
            "success":function() {
              var v = new app.views.CentroCostoEditView({
                model: centro_costo,
                permiso: permiso
              });
              self.mostrar({
                "top" : v.el,
              });
            }
          });
        }
      }                
    },
    
    ver_almacenes: function() {
      var permiso = control.check("almacenes");
      if (permiso > 0) {
        app.views.almacenesTableView = new app.views.AlmacenesTableView({
          collection: new app.collections.Almacenes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.almacenesTableView.el,
        });
      }
    },
    ver_almacen: function(id) {
      var self = this;
      var permiso = control.check("almacenes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.almacenEditView = new app.views.AlmacenEditView({
            model: new app.models.Almacen(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.almacenEditView.el,
          });
        } else {
          var almacen = new app.models.Almacen({ "id": id });
          almacen.fetch({
            "success":function() {
              app.views.almacenEditView = new app.views.AlmacenEditView({
                model: almacen,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.almacenEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_articulos_etiquetas: function() {
      var permiso = control.check("articulos");
      if (permiso > 0) {
        app.views.articulos_etiquetasTableView = new app.views.ArticulosEtiquetasTableView({
          collection: new app.collections.ArticulosEtiquetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.articulos_etiquetasTableView.el,
        });
      }
    },
    ver_articulo_etiqueta: function(id) {
      var self = this;
      var permiso = control.check("articulos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.articulo_etiquetaEditView = new app.views.ArticuloEtiquetaEditView({
            model: new app.models.ArticuloEtiqueta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.articulo_etiquetaEditView.el,
          });
        } else {
          var articulo_etiqueta = new app.models.ArticuloEtiqueta({ "id": id });
          articulo_etiqueta.fetch({
            "success":function() {
              app.views.articulo_etiquetaEditView = new app.views.ArticuloEtiquetaEditView({
                model: articulo_etiqueta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.articulo_etiquetaEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_entradas_etiquetas: function() {
      var permiso = control.check("entradas_etiquetas");
      if (permiso > 0) {
        app.views.entradas_etiquetasTableView = new app.views.EntradasEtiquetasTableView({
          collection: new app.collections.EntradasEtiquetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.entradas_etiquetasTableView.el,
        });
      }
    },
    ver_entrada_etiqueta: function(id) {
      var self = this;
      var permiso = control.check("entradas_etiquetas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.entrada_etiquetaEditView = new app.views.EntradaEtiquetaEditView({
            model: new app.models.EntradaEtiqueta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.entrada_etiquetaEditView.el,
          });
        } else {
          var entrada_etiqueta = new app.models.EntradaEtiqueta({ "id": id });
          entrada_etiqueta.fetch({
            "success":function() {
              app.views.entrada_etiquetaEditView = new app.views.EntradaEtiquetaEditView({
                model: entrada_etiqueta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.entrada_etiquetaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_cursos_evaluaciones: function() {
      var self = this;
      var permiso = control.check("cursos_evaluaciones");
      if (permiso > 0) {
        var v = new app.views.CursosEvaluacionesView({
            collection: new app.collections.CursosEvaluaciones(),
            permiso: permiso
        });    
        this.mostrar({
          "top" : v.el,
        });
      }
    },
    
    ver_libros_prestamos: function(id_libro,id_alumno) {
      var permiso = control.check("libros_prestamos");
      id_libro = (id_libro || 0);
      id_alumno = (id_alumno || 0);
      if (permiso > 0) {
        app.views.librosPrestamosTableView = new app.views.LibrosPrestamosTableView({
          collection: new app.collections.LibrosPrestamos(),
          permiso: permiso,
          id_libro: id_libro,
          id_alumno: id_alumno,
        });    
        this.mostrar({
          "top" : app.views.librosPrestamosTableView.el,
        });
      }
    },
    ver_libros_por_autor: function(id_autor) {
      this.ver_libros(id_autor,0);
    },
    ver_libros_por_etiqueta: function(id_etiqueta) {
      this.ver_libros(0,id_etiqueta);  
    },
    ver_libros: function(id_autor,id_etiqueta) {
      var permiso = control.check("libros");
      id_autor = (id_autor || 0);
      id_etiqueta = (id_etiqueta || 0);
      if (permiso > 0) {
        app.views.librosTableView = new app.views.LibrosTableView({
          collection: new app.collections.Libros(),
          permiso: permiso,
          id_autor: id_autor,
          id_etiqueta: id_etiqueta,
        });    
        this.mostrar({
          "top" : app.views.librosTableView.el,
        });
      }
    },
    ver_libro: function(id) {
      var self = this;
      var permiso = control.check("libros");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.libroEditView = new app.views.LibroEditView({
            model: new app.models.Libro(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.libroEditView.el,
          });
          workspace.crear_editor('libro_sinopsis');
        } else {
          var libro = new app.models.Libro({ "id": id });
          libro.fetch({
            "success":function() {
              app.views.libroEditView = new app.views.LibroEditView({
                model: libro,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.libroEditView.el,
              });
              workspace.crear_editor('libro_sinopsis');
            }
          });
        }
      }                
    },
    
    ver_libros_etiquetas: function() {
      var permiso = control.check("libros_etiquetas");
      if (permiso > 0) {
        app.views.libros_etiquetasTableView = new app.views.LibrosEtiquetasTableView({
          collection: new app.collections.LibrosEtiquetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.libros_etiquetasTableView.el,
        });
      }
    },
    ver_libro_etiqueta: function(id) {
      var self = this;
      var permiso = control.check("libros_etiquetas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.libro_etiquetaEditView = new app.views.LibroEtiquetaEditView({
            model: new app.models.LibroEtiqueta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.libro_etiquetaEditView.el,
          });
        } else {
          var libro_etiqueta = new app.models.LibroEtiqueta({ "id": id });
          libro_etiqueta.fetch({
            "success":function() {
              app.views.libro_etiquetaEditView = new app.views.LibroEtiquetaEditView({
                model: libro_etiqueta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.libro_etiquetaEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_autores: function() {
      var permiso = control.check("autores");
      if (permiso > 0) {
        app.views.autoresTableView = new app.views.AutoresTableView({
          collection: new app.collections.Autores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.autoresTableView.el,
        });
      }
    },
    ver_autor: function(id) {
      var self = this;
      var permiso = control.check("autores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.autorEditView = new app.views.AutorEditView({
            model: new app.models.Autor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.autorEditView.el,
          });
        } else {
          var autor = new app.models.Autor({ "id": id });
          autor.fetch({
            "success":function() {
              app.views.autorEditView = new app.views.AutorEditView({
                model: autor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.autorEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_campanias: function() {
      var permiso = control.check("campanias");
      if (permiso > 0) {
        app.views.campaniasTableView = new app.views.CampaniasTableView({
          collection: new app.collections.Campanias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.campaniasTableView.el,
          "top_height": ((ID_EMPRESA == 70)?"100%":""),
          "full": ((ID_EMPRESA == 70)?1:0),
        });
      }
    },
    ver_campania: function(id) {
      var self = this;
      var permiso = control.check("campanias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.campaniaEditView = new app.views.CampaniaEditView({
            model: new app.models.Campania(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.campaniaEditView.el,
          });
        } else {
          var campania = new app.models.Campania({ "id": id });
          campania.fetch({
            "success":function() {
              app.views.campaniaEditView = new app.views.CampaniaEditView({
                model: campania,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.campaniaEditView.el,
              });
            }
          });
        }
      }                
    },
    

    ver_rss_sources: function() {
      var permiso = control.check("rss_sources");
      if (permiso > 0) {
        app.views.rss_sourcesTableView = new app.views.RssSourcesTableView({
          collection: new app.collections.RssSources(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.rss_sourcesTableView.el,
        });
      }
    },
    ver_rss_source: function(id) {
      var self = this;
      var permiso = control.check("rss_sources");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.rss_sourceEditView = new app.views.RssSourceEditView({
            model: new app.models.RssSource(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.rss_sourceEditView.el,
          });
        } else {
          var rss_source = new app.models.RssSource({ "id": id });
          rss_source.fetch({
            "success":function() {
              app.views.rss_sourceEditView = new app.views.RssSourceEditView({
                model: rss_source,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.rss_sourceEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_galerias_categorias: function() {
      var permiso = control.check("galerias_categorias");
      if (permiso > 0) {
        var view = new app.views.GaleriasCategoriasTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_galeria_categoria: function(id) {
      var self = this;
      var permiso = control.check("galerias_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.GaleriaCategoriaEditView({
            model: new app.models.GaleriaCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var categoria = new app.models.GaleriaCategoria({ "id": id });
          categoria.fetch({
            "success":function() {
              var view = new app.views.GaleriaCategoriaEditView({
                model: categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }
    },

    ver_galerias_imagenes_por_etiqueta: function(id_etiqueta) {
      this.ver_galerias_imagenes(id_etiqueta);  
    },
    ver_galerias_imagenes: function(id_etiqueta) {
      var permiso = control.check("galerias_imagenes");
      id_etiqueta = (id_etiqueta || 0);
      if (permiso > 0) {
        app.views.galerias_imagenesTableView = new app.views.GaleriasImagenesTableView({
          collection: new app.collections.GaleriasImagenes(),
          permiso: permiso,
          id_etiqueta: id_etiqueta,
        });    
        this.mostrar({
          "top" : app.views.galerias_imagenesTableView.el,
        });
      }
    },
    ver_galeria_imagen: function(id) {
      var self = this;
      var permiso = control.check("galerias_imagenes");
      if (permiso > 0) {
        if (id == undefined) {
          var edit = new app.views.GaleriaImagenEditView({
            model: new app.models.GaleriaImagen(),
            permiso: permiso
          });
          this.mostrar({
            "top" : edit.el,
          });
        } else {
          var imagen = new app.models.GaleriaImagen({ "id": id });
          imagen.fetch({
            "success":function() {
              var edit = new app.views.GaleriaImagenEditView({
                model: imagen,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },
    
    ver_galerias_etiquetas: function() {
      var permiso = control.check("galerias_etiquetas");
      if (permiso > 0) {
        var galerias_etiquetasTableView = new app.views.GaleriasEtiquetasTableView({
          collection: new app.collections.GaleriasEtiquetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : galerias_etiquetasTableView.el,
        });
      }
    },
    ver_galeria_etiqueta: function(id) {
      var self = this;
      var permiso = control.check("galerias_etiquetas");
      if (permiso > 0) {
        if (id == undefined) {
          var galeria_imagenEditView = new app.views.GaleriaEtiquetaEditView({
            model: new app.models.GaleriaEtiqueta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : galeria_imagenEditView.el,
          });
        } else {
          var galeria_imagen = new app.models.GaleriaEtiqueta({ "id": id });
          galeria_imagen.fetch({
            "success":function() {
              var galeria_imagenEditView = new app.views.GaleriaEtiquetaEditView({
                model: galeria_imagen,
                permiso: permiso
              });
              self.mostrar({
                "top" : galeria_imagenEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_propietarios: function() {
      var permiso = control.check("contactos");
      if (permiso > 0) {
        app.views.propietariosTableView = new app.views.PropietariosTableView({
          collection: new app.collections.Propietarios(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.propietariosTableView.el,
        });
      }
    },
    ver_propietario: function(id) {
      var self = this;
      var permiso = control.check("contactos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.propietarioEditView = new app.views.PropietarioEditView({
            model: new app.models.Propietario(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.propietarioEditView.el,
          });
        } else {
          var propietario = new app.models.Propietario({ "id": id });
          propietario.fetch({
            "success":function() {
              app.views.propietarioEditView = new app.views.PropietarioEditView({
                model: propietario,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.propietarioEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_publicidades_categorias: function() {
      var permiso = control.check("publicidades_categorias");
      if (permiso > 0) {
        app.views.publicidades_categoriasTableView = new app.views.PublicidadesCategoriasTableView({
          collection: new app.collections.PublicidadesCategorias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.publicidades_categoriasTableView.el,
        });
      }
    },
    ver_publicidad_categoria: function(id) {
      var self = this;
      var permiso = control.check("publicidades_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.publicidad_categoriaEditView = new app.views.PublicidadCategoriaEditView({
            model: new app.models.PublicidadCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.publicidad_categoriaEditView.el,
          });
        } else {
          var publicidad_categoria = new app.models.PublicidadCategoria({ "id": id });
          publicidad_categoria.fetch({
            "success":function() {
              app.views.publicidad_categoriaEditView = new app.views.PublicidadCategoriaEditView({
                model: publicidad_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.publicidad_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_contactos_web: function() {
      var permiso = control.check("contactos_web");
      if (permiso > 0) {
        var view = new app.views.ClientesTableView({
          collection: new app.collections.Clientes(),
          vista_contactos: true,
          modulo: control.get("contactos_web"),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_contactos: function() {
      if (MILLING == 1) {
        window.clientes_tipo = 3;
      } else {
        window.clientes_tipo = -1; // Ver todos
      }
      var permiso = control.check("contactos");
      if (permiso > 0) {


        // PARA OBLIGAR A QUE VAYA A LA PARTE NUEVA EN WHATSAPP
        if (ID_PROYECTO == 14) {
          var view = new app.views.ConsultasTableView({
            collection: new app.collections.Clientes(),
            vista_contactos: true,
            modulo: control.get("consultas"),
            permiso: permiso
          });    
          this.mostrar({
            "top" : view.el,
            "full": 1,
          });
          return;            
        }

            /*
        var view = new app.views.ConsultasTableView({
          collection: new app.collections.Clientes(),
          vista_contactos: true,
          modulo: control.get("contactos"),
        });    
        this.mostrar({
          "top" : view.el,
          "full": 1,
        });*/
        var view = new app.views.ClientesTableView({
          collection: new app.collections.Clientes(),
          vista_contactos: true,
          modulo: control.get("contactos"),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_puntos_venta: function() {
      var permiso = control.check("puntos_venta");
      if (permiso > 0) {
        app.views.puntos_ventaTableView = new app.views.PuntosVentaTableView({
          collection: new app.collections.PuntosVenta(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.puntos_ventaTableView.el,
        });
      }
    },
    ver_punto_venta: function(id) {
      var self = this;
      var permiso = control.check("puntos_venta");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.punto_ventaEditView = new app.views.PuntoVentaEditView({
            model: new app.models.PuntoVenta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.punto_ventaEditView.el,
          });
        } else {
          var punto_venta = new app.models.PuntoVenta({ "id": id });
          punto_venta.fetch({
            "success":function() {
              app.views.punto_ventaEditView = new app.views.PuntoVentaEditView({
                model: punto_venta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.punto_ventaEditView.el,
              });
            }
          });
        }
      }                
    },            
    
    
    ver_bancos: function() {
      var permiso = control.check("bancos");
      if (permiso > 0) {
        window.bancos = new app.collections.Bancos();
        app.views.bancosTableView = new app.views.BancosTableView({
          collection: window.bancos,
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.bancosTableView.el,
        });
      }
    },
    ver_banco: function(id) {
      var self = this;
      var permiso = control.check("bancos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.bancoEditView = new app.views.BancoEditView({
            model: new app.models.Banco(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.bancoEditView.el,
          });
        } else {
          var banco = new app.models.Banco({ "id": id });
          banco.fetch({
            "success":function() {
              app.views.bancoEditView = new app.views.BancoEditView({
                model: banco,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.bancoEditView.el,
              });
            }
          });
        }
      }                
    },
    

    ver_versiones_db: function() {
      var permiso = control.check("versiones_db");
      if (permiso > 0) {
        window.versiones_db = new app.collections.VersionesDb();
        app.views.versiones_dbTableView = new app.views.VersionesDbTableView({
          collection: window.versiones_db,
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.versiones_dbTableView.el,
        });
      }
    },
    ver_version_db: function(id) {
      var self = this;
      var permiso = control.check("versiones_db");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.version_dbEditView = new app.views.VersionDbEditView({
            model: new app.models.VersionDb(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.version_dbEditView.el,
          });
        } else {
          var version_db = new app.models.VersionDb({ "id": id });
          version_db.fetch({
            "success":function() {
              app.views.version_dbEditView = new app.views.VersionDbEditView({
                model: version_db,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.version_dbEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_tipos_estado: function() {
      var permiso = control.check("tipos_estado");
      if (permiso > 0) {
        app.views.tipos_estadoTableView = new app.views.TiposEstadoTableView({
          collection: new app.collections.TiposEstado(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_estadoTableView.el,
        });
      }
    },
    ver_tipo_estado: function(id) {
      var self = this;
      var permiso = control.check("tipos_estado");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_estadoEditView = new app.views.TipoEstadoEditView({
            model: new app.models.TipoEstado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_estadoEditView.el,
          });
        } else {
          var tipo_estado = new app.models.TipoEstado({ "id": id });
          tipo_estado.fetch({
            "success":function() {
              app.views.tipo_estadoEditView = new app.views.TipoEstadoEditView({
                model: tipo_estado,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_estadoEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_estado_reservas: function() {
      var permiso = control.check("estado_reservas");
      if (permiso > 0) {
        var view = new app.views.EstadosReservasTableView({
          collection: new app.collections.EstadosReservas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_estado_reserva: function(id) {
      var self = this;
      var permiso = control.check("estado_reservas");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.EstadoReservaEditView({
            model: new app.models.EstadoReserva(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var tipo_estado = new app.models.EstadoReserva({ "id": id });
          tipo_estado.fetch({
            "success":function() {
              var view = new app.views.EstadoReservaEditView({
                model: tipo_estado,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    },
    
    ver_origenes: function() {
      var permiso = control.check("origenes");
      if (permiso > 0) {
        app.views.origenesTableView = new app.views.OrigenesTableView({
          collection: new app.collections.Origenes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.origenesTableView.el,
        });
      }
    },
    ver_origen: function(id) {
      var self = this;
      var permiso = control.check("origenes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.origenEditView = new app.views.OrigenEditView({
            model: new app.models.Origen(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.origenEditView.el,
          });
        } else {
          var origen = new app.models.Origen({ "id": id });
          origen.fetch({
            "success":function() {
              app.views.origenEditView = new app.views.OrigenEditView({
                model: origen,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.origenEditView.el,
              });
            }
          });
        }
      }                
    },
    
    
    
    ver_publicidades_tipos: function() {
      var permiso = control.check("publicidades_tipos");
      if (permiso > 0) {
        app.views.publicidades_tiposTableView = new app.views.PublicidadesTiposTableView({
          collection: new app.collections.PublicidadesTipos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.publicidades_tiposTableView.el,
        });
      }
    },
    ver_publicidad_tipo: function(id) {
      var self = this;
      var permiso = control.check("publicidades_tipos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.publicidad_tipoEditView = new app.views.PublicidadTipoEditView({
            model: new app.models.PublicidadTipo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.publicidad_tipoEditView.el,
          });
        } else {
          var publicidad_tipo = new app.models.PublicidadTipo({ "id": id });
          publicidad_tipo.fetch({
            "success":function() {
              app.views.publicidad_tipoEditView = new app.views.PublicidadTipoEditView({
                model: publicidad_tipo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.publicidad_tipoEditView.el,
              });
            }
          });
        }
      }                
    },
    

    
    ver_campanias_envios: function() {
      var permiso = control.check("campanias_envios");
      if (permiso > 0) {
        app.views.campanias_enviosTableView = new app.views.CampaniasEnviosTableView({
          collection: new app.collections.CampaniasEnvios(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.campanias_enviosTableView.el,
        });
      }
    },
    ver_campania_envio: function(id) {
      var self = this;
      var permiso = control.check("campanias_envios");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.campania_envioEditView = new app.views.CampaniaEnvioEditView({
            model: new app.models.CampaniaEnvio(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.campania_envioEditView.el,
          });
          self.crear_editor("campanias_envios_texto");
        } else {
          var campania_envio = new app.models.CampaniaEnvio({ "id": id });
          campania_envio.fetch({
            "success":function() {
              app.views.campania_envioEditView = new app.views.CampaniaEnvioEditView({
                model: campania_envio,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.campania_envioEditView.el,
              });
              self.crear_editor("campanias_envios_texto");
            }
          });
        }
      }                
    },
    

    ver_tipos_estado_pedidos: function() {
      var permiso = control.check("tipos_estado_pedidos");
      if (permiso > 0) {
        app.views.tipos_estado_pedidosTableView = new app.views.TiposEstadoPedidosTableView({
          collection: new app.collections.TiposEstadoPedidos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_estado_pedidosTableView.el,
        });
      }
    },
    ver_tipo_estado_pedido: function(id) {
      var self = this;
      var permiso = control.check("tipos_estado_pedidos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_estado_pedidoEditView = new app.views.TipoEstadoPedidoEditView({
            model: new app.models.TipoEstadoPedido(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_estado_pedidoEditView.el,
          });
        } else {
          var tipo_estado_pedido = new app.models.TipoEstadoPedido({ "id": id });
          tipo_estado_pedido.fetch({
            "success":function() {
              app.views.tipo_estado_pedidoEditView = new app.views.TipoEstadoPedidoEditView({
                model: tipo_estado_pedido,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_estado_pedidoEditView.el,
              });
            }
          });
        }
      }                
    },
    
    
    ver_tipos_operacion: function() {
      var permiso = control.check("tipos_operacion");
      if (permiso > 0) {
        app.views.tipos_operacionTableView = new app.views.TiposOperacionTableView({
          collection: new app.collections.TiposOperacion(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_operacionTableView.el,
        });
      }
    },
    ver_tipo_operacion: function(id) {
      var self = this;
      var permiso = control.check("tipos_operacion");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_operacionEditView = new app.views.TipoOperacionEditView({
            model: new app.models.TipoOperacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_operacionEditView.el,
          });
        } else {
          var tipo_operacion = new app.models.TipoOperacion({ "id": id });
          tipo_operacion.fetch({
            "success":function() {
              app.views.tipo_operacionEditView = new app.views.TipoOperacionEditView({
                model: tipo_operacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_operacionEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_tipos_inmueble: function() {
      var permiso = control.check("tipos_inmueble");
      if (permiso > 0) {
        app.views.tipos_inmuebleTableView = new app.views.TiposInmuebleTableView({
          collection: new app.collections.TiposInmueble(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_inmuebleTableView.el,
        });
      }
    },
    ver_tipo_inmueble: function(id) {
      var self = this;
      var permiso = control.check("tipos_inmueble");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_inmuebleEditView = new app.views.TipoInmuebleEditView({
            model: new app.models.TipoInmueble(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_inmuebleEditView.el,
          });
        } else {
          var tipo_inmueble = new app.models.TipoInmueble({ "id": id });
          tipo_inmueble.fetch({
            "success":function() {
              app.views.tipo_inmuebleEditView = new app.views.TipoInmuebleEditView({
                model: tipo_inmueble,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_inmuebleEditView.el,
              });
            }
          });
        }
      }                
    },      
    
    ver_tipos_vehiculos: function() {
      var permiso = control.check("tipos_vehiculos");
      if (permiso > 0) {
        app.views.tipos_vehiculosTableView = new app.views.TiposVehiculosTableView({
          collection: new app.collections.TiposVehiculos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_vehiculosTableView.el,
        });
      }
    },
    ver_tipo_vehiculo: function(id) {
      var self = this;
      var permiso = control.check("tipos_vehiculos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipo_vehiculoEditView = new app.views.TipoVehiculoEditView({
            model: new app.models.TipoVehiculo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipo_vehiculoEditView.el,
          });
        } else {
          var tipo_vehiculo = new app.models.TipoVehiculo({ "id": id });
          tipo_vehiculo.fetch({
            "success":function() {
              app.views.tipo_vehiculoEditView = new app.views.TipoVehiculoEditView({
                model: tipo_vehiculo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_vehiculoEditView.el,
              });
            }
          });
        }
      }                
    },      

    ver_vehiculos: function() {
      var permiso = control.check("vehiculos");
      if (permiso > 0) {
        app.views.vehiculosTableView = new app.views.VehiculosTableView({
          collection: new app.collections.Vehiculos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.vehiculosTableView.el,
        });
      }
    },
    ver_vehiculo: function(id) {
      var self = this;
      var permiso = control.check("vehiculos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.vehiculoEditView = new app.views.VehiculoEditView({
            model: new app.models.Vehiculo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.vehiculoEditView.el,
          });
        } else {
          var vehiculo = new app.models.Vehiculo({ "id": id });
          vehiculo.fetch({
            "success":function() {
              app.views.vehiculoEditView = new app.views.VehiculoEditView({
                model: vehiculo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.vehiculoEditView.el,
              });
            }
          });
        }
      }                
    },      


    ver_pedidos_mesas: function() {
      var permiso = control.check("pedidos_mesas");
      if (permiso > 0) {
        var view = new app.views.SalonesView({
          permiso: permiso,
          edicion: false,
        });    
        this.mostrar({
          "top" : view.el,
          "top_height": "100%",
          "full": 1,
        });
      }
    },

    ver_tarjetas: function() {
      var permiso = control.check("tarjetas");
      if (permiso > 0) {
        app.views.tarjetasTableView = new app.views.TarjetasTableView({
          collection: new app.collections.Tarjetas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tarjetasTableView.el,
        });
      }
    },
    ver_tarjeta: function(id) {
      var self = this;
      var permiso = control.check("tarjetas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tarjetaEditView = new app.views.TarjetaEditView({
            model: new app.models.Tarjeta(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tarjetaEditView.el,
          });
        } else {
          var tarjeta = new app.models.Tarjeta({ "id": id });
          tarjeta.fetch({
            "success":function() {
              app.views.tarjetaEditView = new app.views.TarjetaEditView({
                model: tarjeta,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tarjetaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_opcionales: function() {
      var permiso = control.check("opcionales");
      if (permiso > 0) {
        app.views.opcionalesTableView = new app.views.OpcionalesTableView({
          collection: new app.collections.Opcionales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.opcionalesTableView.el,
        });
      }
    },
    ver_opcional: function(id) {
      var self = this;
      var permiso = control.check("opcionales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.opcionalEditView = new app.views.OpcionalEditView({
            model: new app.models.Opcional(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.opcionalEditView.el,
          });
          workspace.crear_editor('opcional_texto',{"toolbar":"Basic"});
          // Eliminamos los editores para volverlos a crear
          var en = CKEDITOR.instances["opcional_texto_en"];
          if (en) CKEDITOR.remove(en);
          var pt = CKEDITOR.instances["opcional_texto_pt"];
          if (pt) CKEDITOR.remove(pt);
        } else {
          var opcional = new app.models.Opcional({ "id": id });
          opcional.fetch({
            "success":function() {
              app.views.opcionalEditView = new app.views.OpcionalEditView({
                model: opcional,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.opcionalEditView.el,
              });
              workspace.crear_editor('opcional_texto',{"toolbar":"Basic"});
              // Eliminamos los editores para volverlos a crear
              var en = CKEDITOR.instances["opcional_texto_en"];
              if (en) CKEDITOR.remove(en);
              var pt = CKEDITOR.instances["opcional_texto_pt"];
              if (pt) CKEDITOR.remove(pt);
            }
          });
        }
      }                
    },

    ver_viajes: function() {
      var permiso = control.check("viajes");
      if (permiso > 0) {
        app.views.viajesTableView = new app.views.ViajesTableView({
          collection: new app.collections.Viajes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.viajesTableView.el,
        });
      }
    },
    ver_viaje: function(id) {
      var self = this;
      var permiso = control.check("viajes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.viajeEditView = new app.views.ViajeEditView({
            model: new app.models.Viaje({
              "images":[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.viajeEditView.el,
          });
          // Si tiene habilitado el modulo web
          if (control.check("web_configuracion")>0) {
            workspace.crear_editor('viaje_texto',{"toolbar":"Basic"});

            // Eliminamos los editores para volverlos a crear
            var en = CKEDITOR.instances["viaje_texto_en"];
            if (en) CKEDITOR.remove(en);
            var pt = CKEDITOR.instances["viaje_texto_pt"];
            if (pt) CKEDITOR.remove(pt);
          }
        } else {
          var viaje = new app.models.Viaje({ "id": id });
          viaje.fetch({
            "success":function() {
              app.views.viajeEditView = new app.views.ViajeEditView({
                model: viaje,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.viajeEditView.el,
              });
              // Si tiene habilitado el modulo web
              if (control.check("web_configuracion")>0) {
                workspace.crear_editor('viaje_texto',{"toolbar":"Basic"});
                // Eliminamos los editores para volverlos a crear
                var en = CKEDITOR.instances["viaje_texto_en"];
                if (en) CKEDITOR.remove(en);
                var pt = CKEDITOR.instances["viaje_texto_pt"];
                if (pt) CKEDITOR.remove(pt);
              }
            }
          });
        }
      }                
    },      

    ver_asientos: function(id) {
      var self = this;
      var permiso = control.check("viajes");
      if (permiso > 0) {
        var viaje = new app.models.Viaje({ "id": id });
        viaje.fetch({
          "success":function() {

            // Guardamos la referencia para usarla en ReservaAsientos
            window.viaje = viaje;
            
            app.views.viajeAsientoEditView = new app.views.ViajeAsientosView({
              model: viaje,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.viajeAsientoEditView.el,
            });
          }
        });
      }                
    },      
    
        ver_tipos_tarifas: function() {
          var permiso = control.check("tipos_tarifas");
          if (permiso > 0) {
            app.views.tipos_tarifasTableView = new app.views.TiposTarifasTableView({
              collection: new app.collections.TiposTarifas(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.tipos_tarifasTableView.el,
            });
          }
        },
        ver_tipo_tarifa: function(id) {
          var self = this;
          var permiso = control.check("tipos_tarifas");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.tipo_tarifaEditView = new app.views.TipoTarifaEditView({
                model: new app.models.TipoTarifa(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.tipo_tarifaEditView.el,
              });
            } else {
              var tipo_tarifa = new app.models.TipoTarifa({ "id": id });
              tipo_tarifa.fetch({
                "success":function() {
                  app.views.tipo_tarifaEditView = new app.views.TipoTarifaEditView({
                    model: tipo_tarifa,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.tipo_tarifaEditView.el,
                  });
                }
              });
            }
          }                
        },      

        ver_hoteles: function() {
          var permiso = control.check("hoteles");
          if (permiso > 0) {
            app.views.hotelesTableView = new app.views.HotelesTableView({
              collection: new app.collections.Hoteles(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.hotelesTableView.el,
            });
          }
        },
        ver_hotel: function(id) {
          var self = this;
          var permiso = control.check("hoteles");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.hotelEditView = new app.views.HotelEditView({
                model: new app.models.Hotel(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.hotelEditView.el,
              });
            } else {
              var hotel = new app.models.Hotel({ "id": id });
              hotel.fetch({
                "success":function() {
                  app.views.hotelEditView = new app.views.HotelEditView({
                    model: hotel,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.hotelEditView.el,
                  });
                }
              });
            }
          }                
        },      

        ver_monedas: function() {
          var permiso = control.check("monedas");
          if (permiso > 0) {
            app.views.monedasTableView = new app.views.MonedasTableView({
              collection: new app.collections.Monedas(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.monedasTableView.el,
            });
          }
        },
        ver_moneda: function(id) {
          var self = this;
          var permiso = control.check("monedas");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.monedaEditView = new app.views.MonedaEditView({
                model: new app.models.Moneda(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.monedaEditView.el,
              });
            } else {
              var moneda = new app.models.Moneda({ "id": id });
              moneda.fetch({
                "success":function() {
                  app.views.monedaEditView = new app.views.MonedaEditView({
                    model: moneda,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.monedaEditView.el,
                  });
                }
              });
            }
          }                
        },
        
        
        ver_planes: function() {
          var permiso = control.check("planes");
          if (permiso > 0) {
            app.views.planesTableView = new app.views.PlanesTableView({
              collection: new app.collections.Planes(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.planesTableView.el,
            });
          }
        },
        ver_plan: function(id) {
          var self = this;
          var permiso = control.check("planes");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.planEditView = new app.views.PlanEditView({
                model: new app.models.Plan(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.planEditView.el,
              });
            } else {
              var plan = new app.models.Plan({ "id": id });
              plan.fetch({
                "success":function() {
                  app.views.planEditView = new app.views.PlanEditView({
                    model: plan,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.planEditView.el,
                  });
                }
              });
            }
          }                
        },

        ver_gestion_pagos: function() {
          var self = this;
          var permiso = control.check("gestion_pagos");
          if (permiso > 0) {            
            var view = new app.views.GestionPagosView({
              model: new app.models.AbstractModel()
            });    
            this.mostrar({
              "top" : view.el,
            });
          }
        },
        
        ver_empresas_gestion_pagos: function() {
          if (PERFIL != -1) return;
          var view = new app.views.EmpresasGestionPagosView({
            model: new app.models.AbstractModel()
          });    
          this.mostrar({
            "top" : view.el,
          });
        },
        
        ver_proyectos: function() {
          var permiso = control.check("proyectos");
          if (permiso > 0) {
            app.views.proyectosTableView = new app.views.ProyectosTableView({
              collection: new app.collections.Proyectos(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.proyectosTableView.el,
            });
          }
        },
        ver_proyecto: function(id) {
          var self = this;
          var permiso = control.check("proyectos");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.proyectoEditView = new app.views.ProyectoEditView({
                model: new app.models.Proyecto(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.proyectoEditView.el,
              });
            } else {
              var proyecto = new app.models.Proyecto({ "id": id });
              proyecto.fetch({
                "success":function() {
                  app.views.proyectoEditView = new app.views.ProyectoEditView({
                    model: proyecto,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.proyectoEditView.el,
                  });
                }
              });
            }
          }                
        },             
        
        
        
        ver_provincias: function() {
          var permiso = control.check("provincias");
          if (permiso > 0) {
            app.views.provinciasTableView = new app.views.ProvinciasTableView({
              collection: new app.collections.Provincias(),
              permiso: permiso
            });    
            this.mostrar({
              "top" : app.views.provinciasTableView.el,
            });
          }
        },
        ver_provincia: function(id) {
          var self = this;
          var permiso = control.check("provincias");
          if (permiso > 0) {
            if (id == undefined) {
              app.views.provinciaEditView = new app.views.ProvinciaEditView({
                model: new app.models.Provincia(),
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.provinciaEditView.el,
              });
            } else {
              var provincia = new app.models.Provincia({ "id": id });
              provincia.fetch({
                "success":function() {
                  app.views.provinciaEditView = new app.views.ProvinciaEditView({
                    model: provincia,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.provinciaEditView.el,
                  });
                }
              });
            }
          }                
        },

        ver_degenerator: function() {
          if (PERFIL != -1) return;
          var degenerator = new app.views.Degenerator({
            "model":new app.models.AbstractModel(),
          });
          this.mostrar({
            "top":degenerator.el
          });
        },
        
        ver_empresas: function(id_proyecto) {
          var permiso = (PERFIL == -1)?3:0;
          if (permiso > 0) {
            if (id_proyecto == undefined) id_proyecto = 0;
            var empresas = new app.collections.Empresas();
            app.views.empresasTableView = new app.views.EmpresasTableView({
              collection: empresas,
              permiso: permiso,
              id_proyecto: id_proyecto,
            });    
            this.mostrar({
              "top" : app.views.empresasTableView.el,
            });
          }
        },
        ver_empresa: function(id) {
          var self = this;
          var permiso = (PERFIL == -1)?3:0;
          if (permiso > 0) {
            if (id == undefined) {
              var empresa = new app.models.Empresa({
                "asignado_a":ID_USUARIO,
              });
              app.views.empresaEditView = new app.views.EmpresaEditView({
                model: empresa,
                permiso: permiso
              });
              this.mostrar({
                "top" : app.views.empresaEditView.el,
              });
            } else {
              var empresa = new app.models.Empresa({ "id": id });
              empresa.fetch({
                "success":function() {
                  app.views.empresaEditView = new app.views.EmpresaEditView({
                    model: empresa,
                    permiso: permiso
                  });
                  self.mostrar({
                    "top" : app.views.empresaEditView.el,
                  });
                }
              });
            }
          }                
        },
        
        ver_editor_web: function() {
          var self = this;
          var permiso = (PERFIL == -1)?3:0;
          if (permiso > 0) {
            var empresa = new app.models.Empresa({ "id": ID_EMPRESA });
            empresa.fetch({
              "success":function() {
                app.views.editorWebView = new app.views.EditorWebView({
                  model: empresa,
                  permiso: permiso
                });
                self.mostrar({
                  "top" : app.views.editorWebView.el,
                });
              }
            });
          }                
        },
        
        
        nueva_empresa: function(id_proyecto) {
          var self = this;
          var permiso = (PERFIL == -1)?3:0;
          if (permiso > 0) {
            var empresa = new app.models.Empresa({
              "asignado_a":ID_USUARIO,
              "id_proyecto":id_proyecto,
            });
            app.views.empresaEditView = new app.views.EmpresaEditView({
              model: empresa,
              permiso: permiso
            });
            this.mostrar({
              "top" : app.views.empresaEditView.el,
            });
          }                
        },
        
        
        
    ver_mis_datos: function(id) {
      var self = this;
      if (ID_PROYECTO == 10 && PERFIL != -1) {
        // Mi empresa en RESTOVAR es distinta
        var empresa = new app.models.EmpresaRestovar({ "id": ID });
        empresa.fetch({
          "success":function() {
            var view = new app.views.EmpresaRestovarEditView({
              model: empresa,
              permiso: 0
            });
            self.mostrar({
              "top" : view.el,
            });
            workspace.crear_editor('empresas_detalle_texto_comercio',{"toolbar":"Basic"});
          }
        });
      } else if (ID_PROYECTO == 5 && PERFIL != -1) {
        // Mi empresa en COLVAR es distinta
        var empresa = new app.models.EmpresaColvar({ "id": ID });
        empresa.fetch({
          "success":function() {
            var view = new app.views.EmpresaColvarEditView({
              model: empresa,
              permiso: 0
            });
            self.mostrar({
              "top" : view.el,
            });
          }
        });
      } else if (ID_EMPRESA == 856) {
        // Agencias de Remises

        // Perfil AGENCIA
        if (PERFIL == 952) {
          var carp_agencia = new app.models.CarpAgencia({"id":ID_USUARIO});
          carp_agencia.fetch({
            "success":function() {
              var view = new app.views.CarpAgenciaEditView({
                model: carp_agencia,
                permiso: 2
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });

        // Perfil PROPIETARIOS
        } else if (PERFIL == 953) {
          var carp_propietario = new app.models.CarpPropietario({"id":ID_USUARIO});
          carp_propietario.fetch({
            "success":function() {
              var view = new app.views.CarpPropietarioEditView({
                model: carp_propietario,
                permiso: 2
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });

        }

      } else {                
        var empresa = new app.models.Empresa({ "id": ID });
        empresa.fetch({
          "success":function() {
            app.views.empresaEditView = new app.views.EmpresaEditView({
              model: empresa,
              permiso: 0
            });
            self.mostrar({
              "top" : app.views.empresaEditView.el,
            });
          }
        });
      }
    },

    ver_mant_configuracion: function(id) {
      if (control.check("mant_configuracion") > 0) {
        var self = this;
        var conf = new app.models.MantConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function(model) {
            var view = new app.views.MantConfiguracionEditView({
              model: model,
              id_modulo: "mant_configuracion"
            });
            self.mostrar({
              "top" : view.el,
            });
          }
        });
      }
    },

    ver_configuracion_emails: function(id) {
      if (control.check("configuracion_emails") > 0) {
        var self = this;
        var conf = new app.models.WebConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function(model) {
            var view = new app.views.WebConfiguracionEmailEditView({
              model: model,
              id_modulo: "configuracion_emails"
            });
            self.mostrar({
              "top" : view.el,
            });
          }
        });
      }
    },
    
    ver_medios_pago_configuracion: function(id) {
      var self = this;
      var medios_pago_configuracion = new app.models.MedioPagoConfiguracion({ "id_empresa": ID });
      medios_pago_configuracion.fetch({
        "success":function() {
          app.views.medios_pago_configuracionEditView = new app.views.MedioPagoConfiguracionEditView({
            model: medios_pago_configuracion,
            permiso: 0
          });
          self.mostrar({
            "top" : app.views.medios_pago_configuracionEditView.el,
          });
        }
      });
    },
    
    ver_formas_envio_configuracion: function(id) {
      var self = this;
      var formas_envio_configuracion = new app.models.FormaEnvioConfiguracion({ "id_empresa": ID });
      formas_envio_configuracion.fetch({
        "success":function() {
          app.views.formas_envio_configuracionEditView = new app.views.FormaEnvioConfiguracionEditView({
            model: formas_envio_configuracion,
            permiso: 0
          });
          self.mostrar({
            "top" : app.views.formas_envio_configuracionEditView.el,
          });
        }
      });
    },
    
    ver_localidades: function() {
      var permiso = control.check("localidades");
      if (permiso > 0) {
        app.views.localidadesTableView = new app.views.LocalidadesTableView({
          collection: new app.collections.Localidades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.localidadesTableView.el,
        });
      }
    },
    ver_localidad: function(id) {
      var self = this;
      var permiso = control.check("localidades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.localidadEditView = new app.views.LocalidadEditView({
            model: new app.models.Localidad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.localidadEditView.el,
          });
        } else {
          var localidad = new app.models.Localidad({ "id": id });
          localidad.fetch({
            "success":function() {
              app.views.localidadEditView = new app.views.LocalidadEditView({
                model: localidad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.localidadEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_eventos: function() {
      var permiso = control.check("eventos");
      if (permiso > 0) {
        app.views.eventosTableView = new app.views.EventosTableView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.eventosTableView.el,
        });
      }
    },

    ver_alertas: function() {
      var permiso = control.check("alertas");
      if (permiso > 0) {
        var alertas = new app.views.AlertasMapaView({
          permiso: permiso,
        });    
        this.mostrar({
          "left" : alertas.el,
          "left_width":"100%"
        });
      }
    },      
    
    ver_consultas: function(id_origen) {
      id_origen = (id_origen || 0);
      var permiso = control.check("consultas");
      if (permiso > 0) {
        var view = new app.views.ConsultasTableView({
          collection: new app.collections.Clientes(),
          vista_contactos: true,
          modulo: control.get("consultas"),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
          "full": 1,
        });
      }
    },
    ver_consulta: function(id) {
      var self = this;
      var permiso = control.check("consultas");
      if (permiso > 0) {
        var consulta = new app.models.Consulta({ "id": id });
        consulta.fetch({
          "success":function() {
            app.views.consultaDetalleView = new app.views.ConsultaDetalleView({
              model: consulta,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.consultaDetalleView.el,
            });
          }
        });
      }                
    },             
    
    ver_ambientaciones: function() {
      var permiso = control.check("ambientaciones");
      if (permiso > 0) {
        app.views.ambientacionesTableView = new app.views.AmbientacionesTableView({
          collection: new app.collections.Ambientaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.ambientacionesTableView.el,
        });
      }
    },
    ver_ambientacion: function(id) {
      var self = this;
      var permiso = control.check("ambientaciones");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.AmbientacionEditView({
            model: new app.models.Ambientacion({
              "images":[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
          if ($("#ambientacion_texto").length > 0) workspace.crear_editor('ambientacion_texto',{
            "toolbar":"Basic"
          });
        } else {
          var ambientacion = new app.models.Ambientacion({
            "id":id,
            "images":[],
          });
          ambientacion.fetch({
            "success":function() {
              var view = new app.views.AmbientacionEditView({
                model: ambientacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
              if ($("#ambientacion_texto").length > 0) workspace.crear_editor('ambientacion_texto',{
                "toolbar":"Basic"
              });
            }
          });
        }
      }                
    },   

    ver_importacion: function(tabla, id) {
      // Tenemos que tener permiso para poder editar la informacion
      var permiso_tabla = tabla;
      if (tabla == "clientes" && ID_PROYECTO == 3) permiso_tabla = "contactos";
      var permiso = control.check(permiso_tabla);
      if (permiso > 1) {
        var self = this;
        $.ajax({
          "url":"importar/function/get/",
          "dataType":"json",
          "data":{
            "tabla":tabla,
            "id":id
          },
          "success":function(r) {
            if (r.error == 1) return;
            if (ID_EMPRESA == 444) r.tabla = "importaciones_articulos_items";
            var view = new app.views.ImportacionView({
              model: new app.models.AbstractModel(r),
            });
            self.mostrar({
              "top": view.el,
            });
          }
        });
      }
    },

    ver_stock_por_sucursal: function() {
      var permiso = control.check("stock_por_sucursal");
      if (permiso > 0) {
        app.views.stock_por_sucursalTableView = new app.views.StockPorSucursalTableView({
          collection: new app.collections.Articulos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.stock_por_sucursalTableView.el,
        });
      }
    },
    
    ver_articulos: function() {
      var permiso = control.check("articulos");
      if (permiso > 0) {
        app.views.articulosTableView = new app.views.ArticulosTableView({
          collection: new app.collections.Articulos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.articulosTableView.el,
        });
      }
    },
    ver_articulo: function(id) {
      var self = this;
      var permiso = control.check("articulos");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.ArticuloEditView({
            model: new app.models.Articulo({
              "images":[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
          if ($("#articulo_texto").length > 0) workspace.crear_editor('articulo_texto',{
            "toolbar":"Basic"
          });
          if ($("#articulo_breve").length > 0) workspace.crear_editor('articulo_breve',{
            "toolbar":"Basic"
          });
        } else {
          var articulo = new app.models.Articulo({
            "id":id,
            "images":[],
          });
          articulo.fetch({
            "success":function() {
              var view = new app.views.ArticuloEditView({
                model: articulo,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
              if ($("#articulo_texto").length > 0) workspace.crear_editor('articulo_texto',{
                "toolbar":"Basic"
              });
              if ($("#articulo_breve").length > 0) workspace.crear_editor('articulo_breve',{
                "toolbar":"Basic"
              });
            }
          });
        }
      }                
    },    


    ver_importaciones_articulos: function() {
      var permiso = control.check("importaciones_articulos");
      if (permiso > 0) {
        app.views.importaciones_articulosTableView = new app.views.ImportacionesArticulosTableView({
          collection: new app.collections.ImportacionesArticulos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.importaciones_articulosTableView.el,
        });
      }
    },
    ver_importacion_articulo: function(id) {
      var self = this;
      var permiso = control.check("importaciones_articulos");
      if (permiso > 0) {
        var self = this;
        $.ajax({
          "url":"importaciones_articulos/function/ver/"+id,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 1) return;
            var view = new app.views.ImportacionArticuloEditView({
              model: new app.models.AbstractModel(r),
            });
            self.mostrar({
              "top": view.el,
            });
          }
        });
      }
    },  

    ver_not_videos: function() {
      var permiso = control.check("not_videos");
      if (permiso > 0) {
        app.views.not_videosTableView = new app.views.NotVideosTableView({
          collection: new app.collections.NotVideos(),
          permiso: permiso,
        });    
        this.mostrar({
          "top" : app.views.not_videosTableView.el,
        });
      }
    },
    ver_not_video: function(id) {
      var self = this;
      var permiso = control.check("not_videos");
      if (permiso > 0) {
        if (id == undefined) {
          var not_videoEditView = new app.views.NotVideoEditView({
            model: new app.models.NotVideo({
              "images":[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : not_videoEditView.el,
          });
          workspace.crear_editor('not_video_texto',{"toolbar":"Basic"});
          // Eliminamos los editores para volverlos a crear
          var en = CKEDITOR.instances["not_video_texto_en"];
          if (en) CKEDITOR.remove(en);
          var pt = CKEDITOR.instances["not_video_texto_pt"];
          if (pt) CKEDITOR.remove(pt);

        } else {
          var not_video = new app.models.NotVideo({ 
            "id": id,
            "images":[],
          });
          not_video.fetch({
            "success":function() {
              var not_videoEditView = new app.views.NotVideoEditView({
                model: not_video,
                permiso: permiso
              });
              self.mostrar({
                "top" : not_videoEditView.el,
              });
              workspace.crear_editor('not_video_texto',{"toolbar":"Basic"});

              // Eliminamos los editores para volverlos a crear
              var en = CKEDITOR.instances["not_video_texto_en"];
              if (en) CKEDITOR.remove(en);
              var pt = CKEDITOR.instances["not_video_texto_pt"];
              if (pt) CKEDITOR.remove(pt);
            }
          });
        }
      }                
    },

    ver_webinars: function() {
      var permiso = control.check("webinars");
      if (permiso > 0) {
        app.views.webinarsTableView = new app.views.WebinarsTableView({
          collection: new app.collections.Webinars(),
          permiso: permiso,
        });    
        this.mostrar({
          "top" : app.views.webinarsTableView.el,
        });
      }
    },
    ver_webinar: function(id) {
      var self = this;
      var permiso = control.check("webinars");
      if (permiso > 0) {
        if (id == undefined) {
          var webinarEditView = new app.views.WebinarEditView({
            model: new app.models.Webinar(),
            permiso: permiso
          });
          this.mostrar({
            "top" : webinarEditView.el,
          });
          workspace.crear_editor('webinars_texto',{"toolbar":"Basic"});
        } else {
          var webinar = new app.models.Webinar({ 
            "id": id,
          });
          webinar.fetch({
            "success":function() {
              var webinarEditView = new app.views.WebinarEditView({
                model: webinar,
                permiso: permiso
              });
              self.mostrar({
                "top" : webinarEditView.el,
              });
              workspace.crear_editor('webinars_texto',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },

    ver_entradas: function(id_categoria) {
      var permiso = control.check("entradas");
      window.entradas_id_categoria = (id_categoria || 0);
      if (permiso > 0) {
        app.views.entradasTableView = new app.views.EntradasTableView({
          collection: new app.collections.Entradas(),
          permiso: permiso,
        });    
        this.mostrar({
          "top" : app.views.entradasTableView.el,
        });
      }
    },
    ver_entrada: function(id) {
      var self = this;
      var permiso = control.check("entradas");
      if (permiso > 0) {
        if (id == undefined) {
          var entradaEditView = new app.views.EntradaEditView({
            model: new app.models.Entrada({
              "images":[],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : entradaEditView.el,
          });
          workspace.crear_editor('entrada_texto',{"toolbar":"Basic"});
          // Eliminamos los editores para volverlos a crear
          var en = CKEDITOR.instances["entrada_texto_en"];
          if (en) CKEDITOR.remove(en);
          var pt = CKEDITOR.instances["entrada_texto_pt"];
          if (pt) CKEDITOR.remove(pt);

          if (ID_EMPRESA == 285) {
            en = CKEDITOR.instances["entrada_custom_8"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_9"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_10"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_11"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_12"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_13"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_14"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_16"]; if (en) CKEDITOR.remove(en);
          } else if (ID_EMPRESA == 606) {
            // COLEGIO DE ODONTOLOGOS
            en = CKEDITOR.instances["entrada_custom_8"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_9"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_10"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_11"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_12"]; if (en) CKEDITOR.remove(en);
            en = CKEDITOR.instances["entrada_custom_13"]; if (en) CKEDITOR.remove(en);
          }

        } else {
          var entrada = new app.models.Entrada({ 
            "id": id,
            "images":[],
          });
          entrada.fetch({
            "success":function() {
              var entradaEditView = new app.views.EntradaEditView({
                model: entrada,
                permiso: permiso
              });
              self.mostrar({
                "top" : entradaEditView.el,
              });
              workspace.crear_editor('entrada_texto',{"toolbar":"Basic"});

              // Eliminamos los editores para volverlos a crear
              var en = CKEDITOR.instances["entrada_texto_en"];
              if (en) CKEDITOR.remove(en);
              var pt = CKEDITOR.instances["entrada_texto_pt"];
              if (pt) CKEDITOR.remove(pt);

              if (ID_EMPRESA == 285) {
                en = CKEDITOR.instances["entrada_custom_8"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_9"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_10"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_11"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_12"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_13"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_14"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_16"]; if (en) CKEDITOR.remove(en);
              } else if (ID_EMPRESA == 606) {
                en = CKEDITOR.instances["entrada_custom_8"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_9"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_10"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_11"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_12"]; if (en) CKEDITOR.remove(en);
                en = CKEDITOR.instances["entrada_custom_13"]; if (en) CKEDITOR.remove(en);                  
              }
              
            }
          });
        }
      }                
    },
    ver_nueva_entrada: function(id_categoria) {
      var self = this;
      var permiso = control.check("entradas");
      if (permiso > 0) {
        var entradaEditView = new app.views.EntradaEditView({
          model: new app.models.Entrada({
            "id_categoria":id_categoria,
            "images":[],
          }),
          permiso: permiso
        });
        this.mostrar({
          "top" : entradaEditView.el,
        });
        workspace.crear_editor('entrada_texto');
      }                
    },
  
    ver_entradas_papelera: function(id_categoria) {
      var permiso = control.check("entradas_papelera");
      id_categoria = (id_categoria || 0);
      if (permiso > 0) {
        var view = new app.views.PapeleraReciclajeEntradasView({
          collection: new app.collections.Entradas(),
          permiso: permiso,
          id_categoria: id_categoria,
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },


  ver_clasificados: function() {
    var permiso = control.check("clasificados");
    if (permiso > 0) {
      app.views.clasificadosTableView = new app.views.ClasificadosTableView({
        collection: clasificados,
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.clasificadosTableView.el,
      });
    }
  },
  ver_clasificado: function(id) {
    var self = this;
    var permiso = control.check("clasificados");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.clasificadoEditView = new app.views.ClasificadoEditView({
          model: new app.models.Clasificado(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.clasificadoEditView.el,
        });
        workspace.crear_editor('clasificado_texto');
        workspace.crear_editor('clasificado_texto_privado');
      } else {
        var clasificado = clasificados.get(id);
        if (typeof clasificado == "undefined") clasificado = new app.models.Clasificado({ "id": id });
        clasificado.fetch({
          "success":function() {
            app.views.clasificadoEditView = new app.views.ClasificadoEditView({
              model: clasificado,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.clasificadoEditView.el,
            });
            workspace.crear_editor('clasificado_texto');
            workspace.crear_editor('clasificado_texto_privado');
          }
        });
      }
    }                
  },
  
  
  ver_publicidades: function() {
    var permiso = control.check("publicidades");
    if (permiso > 0) {
      app.views.publicidadesTableView = new app.views.PublicidadesTableView({
        collection: new app.collections.Publicidades(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.publicidadesTableView.el,
      });
    }
  },
  ver_publicidad: function(id) {
    var self = this;
    var permiso = control.check("publicidades");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.publicidadEditView = new app.views.PublicidadEditView({
          model: new app.models.Publicidad(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.publicidadEditView.el,
        });            
      } else {
        var publicidad = publicidades.get(id);
        publicidad.fetch({
          "success":function() {
            app.views.publicidadEditView = new app.views.PublicidadEditView({
              model: publicidad,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.publicidadEditView.el,
            });
          }
        });
      }
    }                
  },
  
  ver_encuestas: function() {
    var permiso = control.check("encuestas");
    if (permiso > 0) {
      app.views.encuestasTableView = new app.views.EncuestasTableView({
        collection: new app.collections.Encuestas(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.encuestasTableView.el,
      });
    }
  },
  ver_encuesta: function(id) {
    var self = this;
    var permiso = control.check("encuestas");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.encuestaEditView = new app.views.EncuestaEditView({
          model: new app.models.Encuesta(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.encuestaEditView.el,
        });            
      } else {
        var encuesta = new app.models.Encuesta({ "id": id });
        encuesta.fetch({
          "success":function() {
            app.views.encuestaEditView = new app.views.EncuestaEditView({
              model: encuesta,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.encuestaEditView.el,
            });
          }
        });
      }
    }                
  },
  
  ver_sorteos: function() {
    var permiso = control.check("sorteos");
    if (permiso > 0) {
      app.views.sorteosTableView = new app.views.SorteosTableView({
        collection: new app.collections.Sorteos(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.sorteosTableView.el,
      });
    }
  },
  ver_sorteo: function(id) {
    var self = this;
    var permiso = control.check("sorteos");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.sorteoEditView = new app.views.SorteoEditView({
          model: new app.models.Sorteo(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.sorteoEditView.el,
        });            
      } else {
        var sorteo = new app.models.Sorteo({ "id": id });
        sorteo.fetch({
          "success":function() {
            app.views.sorteoEditView = new app.views.SorteoEditView({
              model: sorteo,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.sorteoEditView.el,
            });
          }
        });
      }
    }                
  },
  
  
  
  ver_comentarios: function() {
    var permiso = control.check("comentarios");
    if (permiso > 0) {
      app.views.comentariosTableView = new app.views.ComentariosTableView({
        collection: new app.collections.Comentarios(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.comentariosTableView.el,
      });
    }
  },
  ver_comentario: function(id) {
    var self = this;
    var permiso = control.check("comentarios");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.comentarioEditView = new app.views.ComentarioEditView({
          model: new app.models.Comentario(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.comentarioEditView.el,
        });            
      } else {
        var comentario = new app.models.Comentario({ "id": id });
        comentario.fetch({
          "success":function() {
            app.views.comentarioEditView = new app.views.ComentarioEditView({
              model: comentario,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.comentarioEditView.el,
            });
          }
        });
      }
    }                
  },
  
  
  ver_publicidades_impresiones: function() {
    var permiso = control.check("publicidades");
    if (permiso > 0) {
      app.views.publicidades_impresionesTableView = new app.views.PublicidadesImpresionesView({
        collection: new app.collections.PublicidadesImpresiones(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.publicidades_impresionesTableView.el,
      });
    }
  },
  
  
  ver_peliculas: function() {
    var permiso = control.check("peliculas");
    if (permiso > 0) {
      app.views.peliculasTableView = new app.views.PeliculasTableView({
        collection: new app.collections.Peliculas(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.peliculasTableView.el,
      });
    }
  },
  ver_pelicula: function(id) {
    var self = this;
    var permiso = control.check("peliculas");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.peliculaEditView = new app.views.PeliculaEditView({
          model: new app.models.Pelicula(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.peliculaEditView.el,
        });
        workspace.crear_editor("pelicula_texto");
      } else {
        var pelicula = new app.models.Pelicula({ "id": id });
        pelicula.fetch({
          "success":function() {
            app.views.peliculaEditView = new app.views.PeliculaEditView({
              model: pelicula,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.peliculaEditView.el,
            });
            workspace.crear_editor("pelicula_texto");
          }
        });
      }
    }                
  },
  
  
  
  ver_landing_pages: function() {
    var permiso = control.check("landing_pages");
    if (permiso > 0) {
      app.views.landing_pagesTableView = new app.views.LandingPagesTableView({
        collection: new app.collections.LandingPages(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.landing_pagesTableView.el,
      });
    }
  },
  ver_landing_page: function(id) {
    var self = this;
    var permiso = control.check("landing_pages");
    if (permiso > 0) {
      if (id == undefined) {
        app.views.landing_pageEditView = new app.views.LandingPageEditView({
          model: new app.models.LandingPage(),
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.landing_pageEditView.el,
        });
        workspace.crear_editor("landing_page_texto");
      } else {
        var landing_page = new app.models.LandingPage({ "id": id });
        landing_page.fetch({
          "success":function() {
            app.views.landing_pageEditView = new app.views.LandingPageEditView({
              model: landing_page,
              permiso: permiso
            });
            self.mostrar({
              "top" : app.views.landing_pageEditView.el,
            });
            workspace.crear_editor("landing_page_texto");
          }
        });
      }
    }                
  },
  ver_landing_pages_impresiones: function() {
    var permiso = control.check("landing_pages");
    if (permiso > 0) {
      app.views.publicidades_impresionesTableView = new app.views.PublicidadesImpresionesView({
        collection: new app.collections.PublicidadesImpresiones(),
        permiso: permiso
      });    
      this.mostrar({
        "top" : app.views.publicidades_impresionesTableView.el,
      });
    }
  },    

  ver_maquinas: function(conf) {
    var self = this;
    var permiso = control.check("maquinas");
    if (permiso > 0) {
      var obj = {
        "collection": new app.collections.Maquinas(),
        "permiso": permiso,                                        
      };
      _.extend(obj,conf); // Extendemos el objeto recibido por la configuracion
      app.views.maquinasTableView = new app.views.MaquinasTableView(obj);
      this.mostrar({
        "top" : app.views.maquinasTableView.el,
      });
    }
  },
  ver_maquina: function(id) {
    var self = this;
    var permiso = control.check("maquinas");
    if (permiso > 0) {
      if (id == undefined) {
        var maquinaEditView = new app.views.MaquinaEditView({
          model: new app.models.Maquina({
            images: [],
            planos: [],
          }),
          permiso: permiso
        });
        this.mostrar({
          "top" : maquinaEditView.el,
        });
      } else {
        var maquina = new app.models.Maquina({ "id": id });
        maquina.fetch({
          "success":function() {
            var maquinaEditView = new app.views.MaquinaEditView({
              model: maquina,
              permiso: permiso
            });
            self.mostrar({
              "top" : maquinaEditView.el,
            });
          }
        });
      }
    }                
  },   
  
  
  
  ver_propiedades: function(conf) {
    var self = this;
    var permiso = control.check("propiedades");
    if (permiso > 0) {
      var obj = {
        "collection": new app.collections.Propiedades(),
        "permiso": permiso,                                        
      };
      _.extend(obj,conf); // Extendemos el objeto recibido por la configuracion
      app.views.propiedadesTableView = new app.views.PropiedadesTableView(obj);
      this.mostrar({
        "top" : app.views.propiedadesTableView.el,
      });
    }
  },
    ver_propiedad: function(id) {
      var self = this;
      var permiso = control.check("propiedades");
      if (permiso > 0) {
        if (id == undefined) {
          var propiedadEditView = new app.views.PropiedadEditView({
            model: new app.models.Propiedad({
              images: [],
              planos: [],
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : propiedadEditView.el,
          });
          workspace.crear_editor('propiedad_texto');
          
        } else {
          var propiedad = new app.models.Propiedad({ "id": id });
          propiedad.fetch({
            "success":function() {
              var propiedadEditView = new app.views.PropiedadEditView({
                model: propiedad,
                permiso: permiso
              });
              self.mostrar({
                "top" : propiedadEditView.el,
              });
              workspace.crear_editor('propiedad_texto');
            }
          });
        }
      }                
    },             

    ver_recibos_alquileres: function(estado) {
      var estado = ((typeof estado != "undefined") ? estado : 0);
      var permiso = control.check("alquileres");
      if (permiso > 0) {
        app.views.recibos_alquileresTableView = new app.views.RecibosAlquileresTableView({
          collection: new app.collections.RecibosAlquileres(),
          permiso: permiso,
          estado: estado,
        });    
        this.mostrar({
          "top" : app.views.recibos_alquileresTableView.el,
        });
      }
    },
    ver_alquileres: function() {
      var permiso = control.check("alquileres");
      if (permiso > 0) {
        app.views.alquileresTableView = new app.views.AlquileresTableView({
          collection: new app.collections.Alquileres(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.alquileresTableView.el,
        });
      }
    },
    ver_alquiler: function(id) {
      var self = this;
      var permiso = control.check("alquileres");
      if (permiso > 0) {
        if (id == undefined) {
          var alquilerEditView = new app.views.AlquilerEditView({
            model: new app.models.Alquiler(),
            permiso: permiso
          });
          this.mostrar({
            "top" : alquilerEditView.el,
          });
        } else {
          var alquiler = new app.models.Alquiler({ "id": id });
          alquiler.fetch({
            "success":function() {
              var alquilerEditView = new app.views.AlquilerEditView({
                model: alquiler,
                permiso: permiso
              });
              self.mostrar({
                "top" : alquilerEditView.el,
              });
            }
          });
        }
      }                
    },             

    
    ver_clasificados_propiedades: function() {
      var permiso = control.check("clasificados_propiedades");
      if (permiso > 0) {
        app.views.clasificados_propiedadesTableView = new app.views.ClasificadosPropiedadesTableView({
          collection: new app.collections.Propiedades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.clasificados_propiedadesTableView.el,
        });
      }
    },
    ver_clasificado_propiedad: function(id) {
      var self = this;
      var permiso = control.check("clasificados_propiedades");
      if (permiso > 0) {
        if (id == undefined) {
          var clasificado_propiedadEditView = new app.views.ClasificadoPropiedadEditView({
            model: new app.models.Propiedad({
              latitud: LATITUD,
              longitud: LONGITUD,
              id_localidad: ID_LOCALIDAD,
            }),
            permiso: permiso
          });
          this.mostrar({
            "top" : clasificado_propiedadEditView.el,
          });
          
        } else {
          var clasificado_propiedad = new app.models.Propiedad({ "id": id });
          clasificado_propiedad.fetch({
            "success":function() {
              var clasificado_propiedadEditView = new app.views.ClasificadoPropiedadEditView({
                model: clasificado_propiedad,
                permiso: permiso
              });
              self.mostrar({
                "top" : clasificado_propiedadEditView.el,
              });
            }
          });
        }
      }                
    },        

    ver_dispositivos: function() {
      var permiso = control.check("dispositivos");
      if (permiso > 0) {
        app.views.dispositivosTableView = new app.views.DispositivosTableView({
          collection: new app.collections.Dispositivos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.dispositivosTableView.el,
        });
      }
    },
    ver_dispositivo: function(id) {
      var self = this;
      var permiso = control.check("dispositivos");
      if (permiso > 0) {
        if (id == undefined) {
          var dispositivoEditView = new app.views.DispositivoEditView({
            model: new app.models.Dispositivo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : dispositivoEditView.el,
          });
          
        } else {
          var dispositivo = new app.models.Dispositivo({ "id": id });
          dispositivo.fetch({
            "success":function() {
              app.views.dispositivoEditView = new app.views.DispositivoEditView({
                model: dispositivo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.dispositivoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_seo_urls: function() {
      var permiso = control.check("seo_urls");
      if (permiso > 0) {
        app.views.seo_urlsTableView = new app.views.SeoUrlsTableView({
          collection: new app.collections.SeoUrls(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.seo_urlsTableView.el,
        });
      }
    },
    ver_seo_url: function(id) {
      var self = this;
      var permiso = control.check("seo_urls");
      if (permiso > 0) {
        if (id == undefined) {
          var seo_urlEditView = new app.views.SeoUrlEditView({
            model: new app.models.SeoUrl(),
            permiso: permiso
          });
          this.mostrar({
            "top" : seo_urlEditView.el,
          });
          workspace.crear_editor('seo_url_texto',{"toolbar":"Basic"});
        } else {
          var seo_url = new app.models.SeoUrl({ "id": id });
          seo_url.fetch({
            "success":function() {
              app.views.seo_urlEditView = new app.views.SeoUrlEditView({
                model: seo_url,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.seo_urlEditView.el,
              });
              workspace.crear_editor('seo_url_texto',{"toolbar":"Basic"});
            }
          });
        }
      }                
    },

    ver_sectores: function() {
      var permiso = control.check("sectores");
      if (permiso > 0) {
        app.views.sectoresTableView = new app.views.SectoresTableView({
          collection: new app.collections.Sectores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sectoresTableView.el,
        });
      }
    },
    ver_sector: function(id) {
      var self = this;
      var permiso = control.check("sectores");
      if (permiso > 0) {
        if (id == undefined) {
          var sectorEditView = new app.views.SectorEditView({
            model: new app.models.Sector(),
            permiso: permiso
          });
          this.mostrar({
            "top" : sectorEditView.el,
          });
        } else {
          var sector = new app.models.Sector({ "id": id });
          sector.fetch({
            "success":function() {
              app.views.sectorEditView = new app.views.SectorEditView({
                model: sector,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sectorEditView.el,
              });
            }
          });
        }
      }                
    },


    ver_tipos_mantenimiento: function() {
      var permiso = control.check("tipos_mantenimiento");
      if (permiso > 0) {
        app.views.tipos_mantenimientoTableView = new app.views.TiposMantenimientoTableView({
          collection: new app.collections.TiposMantenimiento(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_mantenimientoTableView.el,
        });
      }
    },
    ver_tipo_mantenimiento: function(id) {
      var self = this;
      var permiso = control.check("tipos_mantenimiento");
      if (permiso > 0) {
        if (id == undefined) {
          var tipo_mantenimientoEditView = new app.views.TipoMantenimientoEditView({
            model: new app.models.TipoMantenimiento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : tipo_mantenimientoEditView.el,
          });
        } else {
          var tipo_mantenimiento = new app.models.TipoMantenimiento({ "id": id });
          tipo_mantenimiento.fetch({
            "success":function() {
              app.views.tipo_mantenimientoEditView = new app.views.TipoMantenimientoEditView({
                model: tipo_mantenimiento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_mantenimientoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_empresas_tercerizadas: function() {
      var permiso = control.check("empresas_tercerizadas");
      if (permiso > 0) {
        app.views.empresas_tercerizadasTableView = new app.views.EmpresasTercerizadasTableView({
          collection: new app.collections.EmpresasTercerizadas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.empresas_tercerizadasTableView.el,
        });
      }
    },
    ver_empresa_tercerizada: function(id) {
      var self = this;
      var permiso = control.check("empresas_tercerizadas");
      if (permiso > 0) {
        if (id == undefined) {
          var empresa_tercerizadaEditView = new app.views.EmpresaTercerizadaEditView({
            model: new app.models.EmpresaTercerizada(),
            permiso: permiso
          });
          this.mostrar({
            "top" : empresa_tercerizadaEditView.el,
          });
        } else {
          var empresa_tercerizada = new app.models.EmpresaTercerizada({ "id": id });
          empresa_tercerizada.fetch({
            "success":function() {
              app.views.empresa_tercerizadaEditView = new app.views.EmpresaTercerizadaEditView({
                model: empresa_tercerizada,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.empresa_tercerizadaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_ordenes_trabajo: function() {
      var permiso = control.check("tipos_ordenes_trabajo");
      if (permiso > 0) {
        app.views.tipos_ordenes_trabajoTableView = new app.views.TiposOrdenesTrabajoTableView({
          collection: new app.collections.TiposOrdenesTrabajo(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_ordenes_trabajoTableView.el,
        });
      }
    },
    ver_tipo_orden_trabajo: function(id) {
      var self = this;
      var permiso = control.check("tipos_ordenes_trabajo");
      if (permiso > 0) {
        if (id == undefined) {
          var tipo_orden_trabajoEditView = new app.views.TipoOrdenTrabajoEditView({
            model: new app.models.TipoOrdenTrabajo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : tipo_orden_trabajoEditView.el,
          });
        } else {
          var tipo_orden_trabajo = new app.models.TipoOrdenTrabajo({ "id": id });
          tipo_orden_trabajo.fetch({
            "success":function() {
              app.views.tipo_orden_trabajoEditView = new app.views.TipoOrdenTrabajoEditView({
                model: tipo_orden_trabajo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_orden_trabajoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tipos_tareas: function() {
      var permiso = control.check("tipos_tareas");
      if (permiso > 0) {
        app.views.tipos_tareasTableView = new app.views.TiposTareasTableView({
          collection: new app.collections.TiposTareas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tipos_tareasTableView.el,
        });
      }
    },
    ver_tipo_tarea: function(id) {
      var self = this;
      var permiso = control.check("tipos_tareas");
      if (permiso > 0) {
        if (id == undefined) {
          var tipo_tareaEditView = new app.views.TipoTareaEditView({
            model: new app.models.TipoTarea(),
            permiso: permiso
          });
          this.mostrar({
            "top" : tipo_tareaEditView.el,
          });
        } else {
          var tipo_tarea = new app.models.TipoTarea({ "id": id });
          tipo_tarea.fetch({
            "success":function() {
              app.views.tipo_tareaEditView = new app.views.TipoTareaEditView({
                model: tipo_tarea,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipo_tareaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_tdf_sorteos: function() {
      var permiso = control.check("tdf_sorteos");
      if (permiso > 0) {
        app.views.tdf_sorteosTableView = new app.views.TdfSorteosTableView({
          collection: new app.collections.TdfSorteos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tdf_sorteosTableView.el,
        });
      }
    },
    ver_tdf_sorteo: function(id) {
      var self = this;
      var permiso = control.check("tdf_sorteos");
      if (permiso > 0) {
        if (id == undefined) {
          var tdf_sorteoEditView = new app.views.TdfSorteoEditView({
            model: new app.models.TdfSorteo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : tdf_sorteoEditView.el,
          });
          workspace.crear_editor('tdf_sorteo_texto');
          
        } else {
          var tdf_sorteo = new app.models.TdfSorteo({ "id": id });
          tdf_sorteo.fetch({
            "success":function() {
              app.views.tdf_sorteoEditView = new app.views.TdfSorteoEditView({
                model: tdf_sorteo,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tdf_sorteoEditView.el,
              });
              workspace.crear_editor('tdf_sorteo_texto');
            }
          });
        }
      }                
    },  

    
    ver_clasificados_autos: function() {
      var permiso = control.check("clasificados_autos");
      if (permiso > 0) {
        app.views.clasificados_autosTableView = new app.views.ClasificadosAutosTableView({
          collection: new app.collections.ClasificadosAutos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.clasificados_autosTableView.el,
        });
      }
    },
    ver_clasificado_auto: function(id) {
      var self = this;
      var permiso = control.check("clasificados_autos");
      if (permiso > 0) {
        if (id == undefined) {
          var clasificado_autoEditView = new app.views.ClasificadoAutoEditView({
            model: new app.models.ClasificadoAuto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : clasificado_autoEditView.el,
          });
          workspace.crear_editor('clasificado_auto_texto');
          
        } else {
          var clasificado_auto = new app.models.ClasificadoAuto({ "id": id });
          clasificado_auto.fetch({
            "success":function() {
              app.views.clasificado_autoEditView = new app.views.ClasificadoAutoEditView({
                model: clasificado_auto,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.clasificado_autoEditView.el,
              });
              workspace.crear_editor('clasificado_auto_texto');
            }
          });
        }
      }                
    },             
    
    ver_clasificados_objetos: function() {
      var permiso = control.check("clasificados_objetos");
      if (permiso > 0) {
        app.views.clasificados_objetosTableView = new app.views.ClasificadosObjetosTableView({
          collection: new app.collections.ClasificadosObjetos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.clasificados_objetosTableView.el,
        });
      }
    },
    ver_clasificado_objeto: function(id) {
      var self = this;
      var permiso = control.check("clasificados_objetos");
      if (permiso > 0) {
        if (id == undefined) {
          var clasificado_objetoEditView = new app.views.ClasificadoObjetoEditView({
            model: new app.models.ClasificadoObjeto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : clasificado_objetoEditView.el,
          });
          
        } else {
          var clasificado_objeto = new app.models.ClasificadoObjeto({ "id":id });
          clasificado_objeto.fetch({
            "success":function() {
              var clasificado_objetoEditView = new app.views.ClasificadoObjetoEditView({
                model: clasificado_objeto,
                permiso: permiso
              });
              self.mostrar({
                "top" : clasificado_objetoEditView.el,
              });
            }
          });
        }
      }                
    },   

    ver_conceptos: function(totaliza_en) {
      totaliza_en = (typeof totaliza_en == "undefined") ? "G" : totaliza_en;
      var permiso = control.check("conceptos");
      if (permiso > 0) {
        var view = new app.views.ConceptosTreeView({
          "permiso": permiso,
          "totaliza_en": totaliza_en,
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    }, 

    ver_cajas_movimientos: function(id_caja) {
      var permiso = control.check("cajas");
      if (permiso > 0) {
        var view = new app.views.ListadoCajasMovimientosView({
          ver_saldos: 1,
          id_caja: id_caja,
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_gastos: function() {
      var permiso = control.check("gastos");
      if (permiso > 0) {
        var view = new app.views.ListadoCajasMovimientosView({
          tipo: 1,
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_otros_ingresos: function() {
      var permiso = control.check("otros_ingresos");
      if (permiso > 0) {
        var view = new app.views.ListadoCajasMovimientosView({
          tipo: 0,
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },  
    
    ver_tipos_gastos: function() {
      var permiso = control.check("tipos_gastos");
      if (permiso > 0) {
        app.views.gastosTreeView = new app.views.GastosTreeView({
          permiso: permiso
        });
        this.mostrar({
          "top" : app.views.gastosTreeView.el,
        });
      }
    },
    ver_tipo_gasto: function(id) {
      var self = this;
      var permiso = control.check("tipos_gastos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tipoGastoEditView = new app.views.TipoGastoEditView({
            model: new app.models.TipoGasto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tipoGastoEditView.el,
          });
        } else {
          var tipo_gasto = new app.models.TipoGasto({ "id": id });
          tipo_gasto.fetch({
            "success":function() {
              app.views.tipoGastoEditView = new app.views.TipoGastoEditView({
                model: tipo_gasto,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tipoGastoEditView.el,
              });
            }
          });
        }
      }                
    },             
    
    ver_consultas_tipos: function() {
      var permiso = control.check("consultas");
      if (permiso > 0) {
        var v = new app.views.ConsultasTiposTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : v.el,
        });
      }
    },
    ver_consulta_tipo: function(id) {
      var self = this;
      var permiso = control.check("consultas");
      if (permiso > 0) {
        if (id == undefined) {
          var v = new app.views.ConsultaTipoEditView({
            model: new app.models.ConsultaTipo(),
            permiso: permiso
          });
          this.mostrar({
            "top" : v.el,
          });
        } else {
          var consultaTipo = new app.models.ConsultaTipo({ "id": id });
          consultaTipo.fetch({
            "success":function() {
              var v = new app.views.ConsultaTipoEditView({
                model: consultaTipo,
                permiso: permiso
              });
              self.mostrar({
                "top" : v.el,
              });
            }
          });
        }
      }                
    },
    
    ver_rubros: function() {
      var permiso = control.check("rubros");
      if (permiso > 0) {
        app.views.rubrosTreeView = new app.views.RubrosTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.rubrosTreeView.el,
        });
      }
    },
    ver_rubro: function(id) {
      var self = this;
      var permiso = control.check("rubros");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.rubroEditView = new app.views.RubroEditView({
            model: new app.models.Rubro(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.rubroEditView.el,
          });
        } else {
          var rubro = new app.models.Rubro({ "id": id });
          rubro.fetch({
            "success":function() {
              app.views.rubroEditView = new app.views.RubroEditView({
                model: rubro,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.rubroEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_categorias_entradas: function() {
      var permiso = control.check("categorias_entradas");
      if (permiso > 0) {
        app.views.categorias_entradasTreeView = new app.views.CategoriasEntradasTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.categorias_entradasTreeView.el,
        });
      }
    },
    ver_categoria_entrada: function(id) {
      var self = this;
      var permiso = control.check("categorias_entradas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.categoria_entradaEditView = new app.views.CategoriaEntradaEditView({
            model: new app.models.CategoriaEntrada(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.categoria_entradaEditView.el,
          });
        } else {
          var categoria_entrada = new app.models.CategoriaEntrada({ "id": id });
          categoria_entrada.fetch({
            "success":function() {
              app.views.categoria_entradaEditView = new app.views.CategoriaEntradaEditView({
                model: categoria_entrada,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.categoria_entradaEditView.el,
              });
            }
          });
        }
      }                
    },
    

    ver_categorias_viajes: function() {
      var permiso = control.check("categorias_viajes");
      if (permiso > 0) {
        app.views.categorias_viajesTreeView = new app.views.CategoriasViajesTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.categorias_viajesTreeView.el,
        });
      }
    },
    ver_categoria_viaje: function(id) {
      var self = this;
      var permiso = control.check("categorias_viajes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.categoria_viajeEditView = new app.views.CategoriaViajeEditView({
            model: new app.models.CategoriaViaje(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.categoria_viajeEditView.el,
          });
        } else {
          var categoria_viaje = new app.models.CategoriaViaje({ "id": id });
          categoria_viaje.fetch({
            "success":function() {
              app.views.categoria_viajeEditView = new app.views.CategoriaViajeEditView({
                model: categoria_viaje,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.categoria_viajeEditView.el,
              });
            }
          });
        }
      }                
    },


    ver_categorias_opcionales: function() {
      var permiso = control.check("categorias_opcionales");
      if (permiso > 0) {
        app.views.categorias_opcionalesTreeView = new app.views.CategoriasOpcionalesTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.categorias_opcionalesTreeView.el,
        });
      }
    },
    ver_categoria_opcional: function(id) {
      var self = this;
      var permiso = control.check("categorias_opcionales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.categoria_opcionalEditView = new app.views.CategoriaViajeEditView({
            model: new app.models.CategoriaOpcional(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.categoria_opcionalEditView.el,
          });
        } else {
          var categoria_opcional = new app.models.CategoriaOpcional({ "id": id });
          categoria_opcional.fetch({
            "success":function() {
              app.views.categoria_opcionalEditView = new app.views.CategoriaOpcionalEditView({
                model: categoria_opcional,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.categoria_opcionalEditView.el,
              });
            }
          });
        }
      }                
    },
    
    
    ver_clasificados_categorias: function() {
      var permiso = control.check("clasificados_categorias");
      if (permiso > 0) {
        app.views.clasificados_categoriasTreeView = new app.views.ClasificadosCategoriasTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.clasificados_categoriasTreeView.el,
        });
      }
    },
    ver_clasificado_categoria: function(id) {
      var self = this;
      var permiso = control.check("clasificados_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.clasificado_categoriaEditView = new app.views.ClasificadoCategoriaEditView({
            model: new app.models.ClasificadoCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.clasificado_categoriaEditView.el,
          });
        } else {
          var clasificado_categoria = new app.models.ClasificadoCategoria({ "id": id });
          clasificado_categoria.fetch({
            "success":function() {
              app.views.clasificado_categoriaEditView = new app.views.ClasificadoCategoriaEditView({
                model: clasificado_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.clasificado_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_administradores: function() {
      if (PERFIL == -1) {
        app.views.usuariosTableView = new app.views.UsuariosTableView({
          collection: window.usuarios,
          admin: 1,
          permiso: 3,
        });    
        this.mostrar({
          "top" : app.views.usuariosTableView.el,
        });
      }
    },      
    ver_administrador: function(id) {
      if (PERFIL == -1) {
        var self = this;
        if (id == undefined) {
          app.views.usuarioEditView = new app.views.UsuarioEditView({
            model: new app.models.Usuario({admin: 1}),
            permiso: 3
          });
          this.mostrar({
            "top" : app.views.usuarioEditView.el,
          });
        } else {
          var usuario = new app.models.Usuario({ "id": id });
          usuario.fetch({
            "success":function() {
              app.views.usuarioEditView = new app.views.UsuarioEditView({
                model: usuario,
                permiso: 3
              });
              self.mostrar({
                "top" : app.views.usuarioEditView.el,
              });
            }
          });
        }
      }
    },  

    ver_carp_agencias: function() {
      var permiso = control.check("carp_agencias");
      if (permiso > 0) {
        var view = new app.views.CarpAgenciasTableView({
          collection: new app.collections.CarpAgencias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_carp_agencia: function(id) {
      var self = this;
      var permiso = control.check("carp_agencias");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.CarpAgenciaEditView({
            model: new app.models.CarpAgencia(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var carp_agencia = new app.models.CarpAgencia({"id":id});
          carp_agencia.fetch({
            "success":function() {
              var view = new app.views.CarpAgenciaEditView({
                model: carp_agencia,
                permiso: permiso
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });
        }
      }                
    },  
    
    ver_carp_choferes: function() {
      var permiso = control.check("carp_choferes");
      if (permiso > 0) {
        var view = new app.views.CarpChoferesTableView({
          collection: new app.collections.CarpChoferes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_carp_chofer: function(id) {
      var self = this;
      var permiso = control.check("carp_choferes");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.CarpChoferEditView({
            model: new app.models.CarpChofer(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var carp_chofer = new app.models.CarpChofer({"id":id});
          carp_chofer.fetch({
            "success":function() {
              var view = new app.views.CarpChoferEditView({
                model: carp_chofer,
                permiso: permiso
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });
        }
      }                
    }, 

    ver_carp_postulantes: function() {
      var permiso = control.check("carp_postulantes");
      if (permiso > 0) {
        var view = new app.views.CarpPostulantesTableView({
          collection: new app.collections.CarpPostulantes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_carp_postulante: function(id) {
      var self = this;
      var permiso = control.check("carp_postulantes");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.CarpPostulanteEditView({
            model: new app.models.CarpPostulante(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var carp_postulante = new app.models.CarpPostulante({"id":id});
          carp_postulante.fetch({
            "success":function() {
              var view = new app.views.CarpPostulanteEditView({
                model: carp_postulante,
                permiso: permiso
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });
        }
      }                
    },     

    ver_carp_propietarios: function() {
      var permiso = control.check("carp_propietarios");
      if (permiso > 0) {
        var view = new app.views.CarpPropietariosTableView({
          collection: new app.collections.CarpPropietarios(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_carp_propietario: function(id) {
      var self = this;
      var permiso = control.check("carp_propietarios");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.CarpPropietarioEditView({
            model: new app.models.CarpPropietario(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var carp_propietario = new app.models.CarpPropietario({"id":id});
          carp_propietario.fetch({
            "success":function() {
              var view = new app.views.CarpPropietarioEditView({
                model: carp_propietario,
                permiso: permiso
              });
              self.mostrar({
                "top": view.el,
              });
            }
          });
        }
      }                
    }, 

    ver_profes: function() {
      var permiso = control.check("profes");
      if (permiso > 0) {
        app.views.usuariosTableView = new app.views.UsuariosTableView({
          collection: usuarios,
          permiso: permiso,
          link_nuevo: "profe",
        });    
        this.mostrar({
          "top" : app.views.usuariosTableView.el,
        });
      }
    },
    ver_profe: function(id) {
      var self = this;
      var permiso = control.check("profes");
      if (permiso > 0) {
        if (id == undefined) {
          var view = new app.views.UsuarioEditView({
            id_perfil_default: 1357, // TODO: Hacer dinamico esto
            model: new app.models.Usuario(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var usuario = usuarios.get(id);
          usuario.fetch({
            "success":function() {
              var view = new app.views.UsuarioEditView({
                model: usuario,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    }, 

    ver_usuarios: function() {
      var permiso = control.check("usuarios");
      if (permiso > 0) {
        app.views.usuariosTableView = new app.views.UsuariosTableView({
          collection: usuarios,
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.usuariosTableView.el,
        });
      }
    },
    ver_usuario: function(id) {
      var self = this;
      var permiso = control.check("usuarios");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.usuarioEditView = new app.views.UsuarioEditView({
            model: new app.models.Usuario(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.usuarioEditView.el,
          });
        } else {
          var usuario = usuarios.get(id);
          usuario.fetch({
            "success":function() {
              app.views.usuarioEditView = new app.views.UsuarioEditView({
                model: usuario,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.usuarioEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_mi_usuario: function(id) {
      var self = this;
      var usuario = usuarios.get(ID_USUARIO);
      usuario.fetch({
        "success":function() {
          var view = new app.views.UsuarioEditView({
            model: usuario,
            permiso: 2,
          });
          self.mostrar({
            "top" : view.el,
          });
        }
      });
    },
    
    ver_stock_valoracion : function() {
      var permiso = control.check("stock_valoracion");
      if (permiso > 0) {
        var modelo = new app.models.ValoracionStock();
        var view = new app.views.ValoracionStockResultados({
          permiso: permiso,
          model: modelo
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_web_sliders: function() {
      var permiso = control.check("web_sliders");
      if (permiso > 0) {
        app.views.web_sliderTableView = new app.views.Web_SliderTableView({
          collection: new app.collections.Web_Slider(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_sliderTableView.el,
        });
      }
    },
    ver_web_slider: function(id) {
      var self = this;
      var permiso = control.check("web_sliders");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_slideEditView = new app.views.Web_SlideEditView({
            model: new app.models.Web_Slide(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_slideEditView.el,
          });
        } else {
          var web_slide = new app.models.Web_Slide({ "id": id });
          web_slide.fetch({
            "success":function() {
              app.views.web_slideEditView = new app.views.Web_SlideEditView({
                model: web_slide,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_slideEditView.el,
              });
            }
          });
        }
        
      }                
    },      
    
    ver_web_users: function() {
      var permiso = control.check("web_users");
      if (permiso > 0) {
        app.views.web_usersTableView = new app.views.Web_UsersTableView({
          collection: new app.collections.Web_Users(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_usersTableView.el,
        });
      }
    },
    ver_web_user: function(id) {
      var self = this;
      var permiso = control.check("web_users");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_userEditView = new app.views.Web_UserEditView({
            model: new app.models.Web_User(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_userEditView.el,
          });
        } else {
          var web_user = new app.models.Web_User({ "id": id });
          web_user.fetch({
            "success":function() {
              app.views.web_userEditView = new app.views.Web_UserEditView({
                model: web_user,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_userEditView.el,
              });
            }
          });
        }
        
      }                
    },      

    ver_web_banners: function() {
      var permiso = control.check("web_banners");
      if (permiso > 0) {
        app.views.web_bannersTableView = new app.views.WebBannersTableView({
          collection: new app.collections.WebBanners(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_bannersTableView.el,
        });
      }
    },
    ver_web_banner: function(id) {
      var self = this;
      var permiso = control.check("web_banners");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_bannerEditView = new app.views.WebBannerEditView({
            model: new app.models.WebBanner(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_bannerEditView.el,
          });
        } else {
          var web_banner = new app.models.WebBanner({ "id": id });
          web_banner.fetch({
            "success":function() {
              app.views.web_bannerEditView = new app.views.WebBannerEditView({
                model: web_banner,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_bannerEditView.el,
              });
            }
          });
        }
        
      }                
    },      

    ver_configuracion_facturacion: function() {
      if (control.check("configuracion_facturacion") > 0) {
        var self = this;
        var configuracion = new app.models.ConfiguracionFacturacion({ "id": ID_EMPRESA });
        configuracion.fetch({
          "success":function() {
            app.views.configuracionFacturacionView = new app.views.ConfiguracionFacturacionView({
              model: configuracion,
            });
            self.mostrar({
              "top" : app.views.configuracionFacturacionView.el,
            });
          },
        });
      }
    },
    
    ver_web_configuracion: function() {
      if (control.check("web_configuracion") > 0) {
        var self = this;
        var conf = new app.models.WebConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function(model) {
            var view = new app.views.WebConfiguracionEditView({
              model: model,
              id_modulo: "web_configuracion"
            });
            self.mostrar({
              "top" : view.el,
              "top_height": "100%",
              "full": 1,
            });
          }
        });
      }
    },

    ver_web_estructura: function() {
      if (control.check("web_estructura") > 0) {
        var self = this;
        var conf = new app.models.WebConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function(model) {
            var view = new app.views.WebEstructuraEditView({
              model: model,
              id_modulo: "web_estructura"
            });
            self.mostrar({
              "top" : view.el,
              "top_height": "100%",
              "full": 1,
            });
          }
        });
      }
    },

    ver_permisos_red: function() {
      if (control.check("permisos_red")<=0) return;
      var self = this;
      $.ajax({
        "url":"permisos_red/function/get_by_empresa/",
        "dataType":"json",
        "success":function(r) {
          var edit = new app.views.PermisosRedView({
            model: new app.models.AbstractModel(r),
          });
          self.mostrar({
            "top": edit.el,
          });
        }
      });
    }, 

    ver_web_seo: function() {
      if (control.check("web_seo")>0) {
        var self = this;
        var conf = new app.models.WebConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function() {
            var edit = new app.views.WebSeoEditView({
              model: conf,
              id_modulo: "web_configuracion"
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    }, 

    ver_chat_configuracion: function() {
      if (control.check("chat_configuracion") > 0) {
        var self = this;
        var conf = new app.models.ChatConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function() {
            var edit = new app.views.ChatConfiguracionEditView({
              model: conf,
              id_modulo: "chat_configuracion"
            });
            self.mostrar({
              "top" : edit.el,
            });
          }
        });
      }
    }, 
    
    ver_web_elegir_template: function() {
      if (control.check("web_elegir_template") > 0) {
        var self = this;
        $.ajax({
          "url":"web_templates/function/lista/",
          "data":{
            "id_proyecto":ID_PROYECTO,
            "id_empresa":ID_EMPRESA,
          },
          "type":"post",
          "dataType":"json",
          "success":function(r) {
            var modelo = new app.models.WebElegirTemplate();
            modelo.set(r);
            app.views.webElegirTemplateView = new app.views.WebElegirTemplateView({
              model: modelo,
              id_modulo: "web_elegir_template"
            });
            self.mostrar({
              "top" : app.views.webElegirTemplateView.el,
            });
          },
        });
      }
    },
    
    
    ver_web_categorias: function() {
      var permiso = control.check("web_categorias");
      if (permiso > 0) {
        app.views.web_categoriasTreeView = new app.views.WebCategoriasTreeView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_categoriasTreeView.el,
        });
      }
    },
    ver_web_categoria: function(id) {
      var self = this;
      var permiso = control.check("web_categorias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_categoriaEditView = new app.views.WebCategoriaEditView({
            model: new app.models.WebCategoria(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_categoriaEditView.el,
          });
        } else {
          var web_categoria = new app.models.WebCategoria({ "id": id });
          web_categoria.fetch({
            "success":function() {
              app.views.web_categoriaEditView = new app.views.WebCategoriaEditView({
                model: web_categoria,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_categoriaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_comprobante: function(id,id_punto_venta) {
      var perm = (ID_PROYECTO == 2) ? 3 : control.check("facturacion");
      if ( (MEGASHOP == 1 || ID_EMPRESA == 421) && perm <= 0) return;
      var self = this;
      $.ajax({
        "url":"facturas/function/ver_comprobante/"+id+"/"+id_punto_venta+"/",
        "dataType":"json",
        "success":function(r){
          var modelo = new app.models.Factura(r);
          var view = null;
          if (control.check("remitos")>0 && r.id_tipo_comprobante == 999) {
            view = new app.views.RemitoEditView({
              model: modelo,
              permiso: perm,
            });
          } else {
            view = new app.views.FacturaEditView({
              model: modelo,
              permiso: perm,
              id_modulo: "facturas"
            });            
          }
          self.mostrar({
            "top" : view.el,
          });
        },
      });
    },

    ver_facturacion_574: function() {
      window.factura_nueva = new app.models.Factura({
        "items":[],
        "tarjetas":[],
        "cheques":[],
        "cotizacion_dolar":COTIZACION_DOLAR,
        "numero_paso":1, // Indica que es un pedido
      });
      this.ver_facturacion();
    },
    
    ver_facturacion: function(id) {
      if (ID_PROYECTO == 2 || control.check("facturacion") > 0 || control.check("ventas_listado") > 2) {
        
        var self = this;
        var perm = control.check("facturas");

          // Estamos viendo una factura
          if (id != undefined) {
            var modelo = new app.models.Factura({
              "id": id,
            });
            modelo.fetch({
              "success":function() {
                app.views.facturaEditView = new app.views.FacturaEditView({
                  model: modelo,
                  permiso: perm,
                  id_modulo: "facturas"
                });
                var conf = {
                  "top" : app.views.facturaEditView.el,
                };
                self.mostrar(conf);
              }
            })
            
          // Facturacion nueva
        } else {

          var factura = new app.models.Factura({
            "items":[],
            "tarjetas":[],
            "cheques":[],
            "cotizacion_dolar":(typeof COTIZACION_DOLAR != "undefined" ? COTIZACION_DOLAR : 0),
          });
          if (typeof window.factura_nueva != "undefined") {
            factura = window.factura_nueva;
            delete window.factura_nueva;
          }
          
          if (LOCAL == 1 && typeof FACTURACION_CONTROLAR_CAJA_ABIERTA != "undefined" && FACTURACION_CONTROLAR_CAJA_ABIERTA == 1) {
            
            // Lo primero que hacemos es controlar si la caja esta abierta
            $.ajax({
              "url":"caja/function/esta_abierta/",
              "dataType":"json",
              "success":function(r){
                if (r.abierta == 1) {
                  app.views.facturaEditView = new app.views.FacturaEditView({
                    model: factura,
                    permiso: perm,
                    id_modulo: "facturas"
                  });
                  self.mostrar({
                    "top" : app.views.facturaEditView.el,
                    "full": (FACTURACION_TIPO == "pv")?1:0,
                  });
                  $("#facturacion_codigo_articulo").select();
                } else {
                  show(r.mensaje);
                  location.hash = "caja_diaria";
                }
              }
            });                            
            
          } else {
            app.views.facturaEditView = new app.views.FacturaEditView({
              model: factura,
              permiso: perm,
              id_modulo: "facturas"
            });
            var conf = {
              "top" : app.views.facturaEditView.el,
              "full": (FACTURACION_TIPO == "pv")?1:0,
            };
            self.mostrar(conf);
            if (FACTURACION_TIPO != "pv") {
              $("#facturacion_codigo_cliente").select();  
            } else {
              $("#facturacion_codigo_articulo").select();
            }
            
          }
        }
        
      }
    },
          
    ver_remito: function(id) {
      if (control.check("remitos") > 0) {
        var self = this;
        var perm = control.check("remitos");
        // Estamos viendo una factura
        if (id != undefined) {
          var modelo = new app.models.Remito({
            "id": id,
          });
          modelo.fetch({
            "success":function() {
              app.views.remitoEditView = new app.views.RemitoEditView({
                model: modelo,
                permiso: perm,
                id_modulo: "remitos"
              });
              self.mostrar({
                "top" : app.views.remitoEditView.el,
              });                        
              
            }
          })
          
        } else {
          app.views.remitoEditView = new app.views.RemitoEditView({
            model: new app.models.Remito({
              "items":[],
              "tarjetas":[],
              "cheques":[],
              "cotizacion_dolar":(typeof COTIZACION_DOLAR != "undefined")?COTIZACION_DOLAR:0,
            }),
            permiso: perm,
            id_modulo: "remitos"
          });
          self.mostrar({
            "top" : app.views.remitoEditView.el,
          });
          $("#remito_codigo_cliente").select();
        }
        
      }
    },
      
    ver_pedidos: function() {
      var permiso = control.check("pedidos");
      if (permiso > 0) {
        app.views.pedidosTableView = new app.views.PedidosTableView({
          collection: new app.collections.Pedidos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pedidosTableView.el,
        });
      }
    },            
      
    ver_pedido: function(id) {
      var self = this;
      var permiso = control.check("pedidos");
      if (permiso > 0) {
        if (id != undefined) {
          var modelo = new app.models.Pedido({
            "id": id,
          });
          modelo.fetch({
            "success":function() {
              app.views.pedidoEditView = new app.views.PedidoEditView({
                model: modelo,
                permiso: permiso,
              });
              self.mostrar({
                "top" : app.views.pedidoEditView.el,
              });                        
            }
          })
        } else {
          app.views.pedidoEditView = new app.views.PedidoEditView({
            model: new app.models.Pedido({
              "items":[],
            }),
            permiso: permiso,
          });
          self.mostrar({
            "top" : app.views.pedidoEditView.el,
          });
          $("#pedidos_codigo_cliente").select();
        }
      }
    },

    ver_pedidos_proveedores: function() {
      var permiso = control.check("pedidos_proveedores");
      if (permiso > 0) {
        app.views.pedidos_proveedoresTableView = new app.views.PedidosProveedoresTableView({
          collection: new app.collections.PedidosProveedores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.pedidos_proveedoresTableView.el,
        });
      }
    },            
    
    ver_pedido_proveedor: function(id) {
      var self = this;
      var permiso = control.check("pedidos_proveedores");
      if (permiso > 0) {
        if (id != undefined) {
          var modelo = new app.models.PedidoProveedor({
            "id": id,
          });
          modelo.fetch({
            "success":function() {
              app.views.pedido_proveedorEditView = new app.views.PedidoProveedorEditView({
                model: modelo,
                permiso: permiso,
              });
              self.mostrar({
                "top" : app.views.pedido_proveedorEditView.el,
              });                        
            }
          })
        } else {
          app.views.pedido_proveedorEditView = new app.views.PedidoProveedorEditView({
            model: new app.models.PedidoProveedor({
              "items":[],
            }),
            permiso: permiso,
          });
          self.mostrar({
            "top" : app.views.pedido_proveedorEditView.el,
          });
          $("#pedidos_proveedores_codigo_proveedor").select();
        }
      }
    },

    ver_presupuestos: function() {
      var permiso = control.check("presupuestos");
      if (permiso > 0) {
        app.views.presupuestosTableView = new app.views.PresupuestosTableView({
          collection: new app.collections.Presupuestos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.presupuestosTableView.el,
        });
      }
    },            
    
    ver_presupuesto: function(id) {
      var self = this;
      var permiso = control.check("presupuestos");
      if (permiso > 0) {
        if (id != undefined) {
          var modelo = new app.models.Presupuesto({
            "id": id,
          });
          modelo.fetch({
            "success":function() {
              app.views.presupuestoEditView = new app.views.PresupuestoEditView({
                model: modelo,
                permiso: permiso,
              });
              self.mostrar({
                "top" : app.views.presupuestoEditView.el,
              });                        
            }
          })
        } else {
          app.views.presupuestoEditView = new app.views.PresupuestoEditView({
            model: new app.models.Presupuesto({
              "items":[],
            }),
            permiso: permiso,
          });
          self.mostrar({
            "top" : app.views.presupuestoEditView.el,
          });
          $("#presupuestos_codigo_cliente").select();
        }
      }
    },

    ver_roturas_mercaderias: function() {
      var permiso = control.check("roturas_mercaderias");
      if (permiso > 0) {
        app.views.roturas_mercaderiasTableView = new app.views.RoturasMercaderiasTableView({
          collection: new app.collections.RoturasMercaderias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.roturas_mercaderiasTableView.el,
        });
      }
    },            
    
    ver_rotura_mercaderia: function(id) {
      var self = this;
      var permiso = control.check("roturas_mercaderias");
      if (permiso > 0) {
        if (id != undefined) {
          var modelo = new app.models.RoturaMercaderia({
            "id": id,
          });
          modelo.fetch({
            "success":function() {
              app.views.rotura_mercaderiaEditView = new app.views.RoturaMercaderiaEditView({
                model: modelo,
                permiso: permiso,
              });
              self.mostrar({
                "top" : app.views.rotura_mercaderiaEditView.el,
              });                        
            }
          })
        } else {
          app.views.rotura_mercaderiaEditView = new app.views.RoturaMercaderiaEditView({
            model: new app.models.RoturaMercaderia({
              "items":[],
            }),
            permiso: permiso,
          });
          self.mostrar({
            "top" : app.views.rotura_mercaderiaEditView.el,
          });
        }
      }
    },
    
    ver_configuracion: function() {
      if (control.check("configuracion") > 0) {
        var self = this;
        var conf = new app.models.Configuracion({
          "id":1
        });
        conf.fetch({
          "success":function() {
            app.views.configuracionEditView = new app.views.ConfiguracionEditView({
              model: conf,
              id_modulo: "configuracion"
            });
            self.mostrar({
              "top" : app.views.configuracionEditView.el,
            });
          }
        });
      }
    },
    
    ver_web_editor: function() {
      if (control.check("web_editor") > 0) {
        var self = this;
        var conf = new app.models.WebConfiguracion({
          "id":ID_EMPRESA
        });
        conf.fetch({
          "success":function() {
            app.views.webEditorView = new app.views.WebEditorView({
              model: conf,
            });
            self.mostrar({
              "top" : app.views.webEditorView.el,
            });
          }
        });
      }
    },      
    
    ver_perfiles: function() {
      var permiso = control.check("perfiles");
      if (permiso > 0) {
        app.views.perfilesTableView = new app.views.PerfilesTableView({
          collection: new app.collections.Perfiles(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.perfilesTableView.el,
        });
      }
    },
    ver_perfil: function(id) {
      var self = this;
      var permiso = control.check("perfiles");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.perfilEditView = new app.views.PerfilEditView({
            model: new app.models.Perfil(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.perfilEditView.el,
          });
        } else {
          var perfil = new app.models.Perfil({ "id": id });
          perfil.fetch({
            "success":function() {
              app.views.perfilEditView = new app.views.PerfilEditView({
                model: perfil,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.perfilEditView.el,
              });
            }
          });
        }
      }                
    },       
    
    ver_cuentas_corrientes_clientes : function(id) {
      var permiso = control.check("cuentas_corrientes_clientes");
      if (permiso > 0) {
        var modelo = new app.models.CuentasCorrientesClientes();
        if (typeof id !== "undefined" && id != null) modelo.set({ "id_cliente":id });
        app.views.cuentas_corrientes_clientesResultados = new app.views.CuentasCorrientesClientesResultados({
          permiso: permiso,
          model: modelo,
        });
        this.mostrar({
          "top" : app.views.cuentas_corrientes_clientesResultados.el,
        });
        $("#cuentas_corrientes_clientes_codigo").select();
      }
    },

    ver_cuentas_corrientes_proveedores : function(id) {
      var permiso = control.check("cuentas_corrientes_proveedores");
      if (permiso > 0) {
        var modelo = new app.models.CuentasCorrientesProveedores();
        if (typeof id !== "undefined" && id != null) modelo.set({ "id_proveedor":id });
        app.views.cuentas_corrientes_proveedoresResultados = new app.views.CuentasCorrientesProveedoresResultados({
          permiso: permiso,
          model: modelo
        });
        this.mostrar({
          "top" : app.views.cuentas_corrientes_proveedoresResultados.el,
        });
        $("#cuentas_corrientes_codigo_proveedor").select();
      }
    },
    
    ver_listado_saldos_clientes : function() {
      var permiso = control.check("listado_saldos_clientes");
      if (permiso > 0) {
        var collection = new app.collections.ListadoSaldosClientes();
        app.views.listado_saldos_clientesResultados = new app.views.ListadoSaldosClientesResultados({
          permiso: permiso,
          collection: collection
        });
        this.mostrar({
          "top" : app.views.listado_saldos_clientesResultados.el,
        });
      }
    },
    
    ver_listado_saldos_proveedores : function() {
      var permiso = control.check("listado_saldos_proveedores");
      if (permiso > 0) {
        var collection = new app.collections.ListadoSaldosProveedores();
        app.views.listado_saldos_proveedoresResultados = new app.views.ListadoSaldosProveedoresResultados({
          permiso: permiso,
          collection: collection
        });
        this.mostrar({
          "top" : app.views.listado_saldos_proveedoresResultados.el,
        });
      }
    },     

    ver_deuda_proveedores : function() {
      var permiso = control.check("deuda_proveedores");
      if (permiso > 0) {
        var collection = new app.collections.DeudaProveedores();
        app.views.deuda_proveedoresResultados = new app.views.DeudaProveedoresResultados({
          permiso: permiso,
          collection: collection
        });
        this.mostrar({
          "top" : app.views.deuda_proveedoresResultados.el,
        });
      }
    }, 

    ver_deuda_sucursales : function() {
      var permiso = control.check("deuda_sucursales");
      if (permiso > 0) {
        var collection = new app.collections.DeudaSucursales();
        app.views.deuda_sucursalesResultados = new app.views.DeudaSucursalesResultados({
          permiso: permiso,
          collection: collection
        });
        this.mostrar({
          "top" : app.views.deuda_sucursalesResultados.el,
        });
      }
    }, 

    ver_deuda_totales : function() {
      var permiso = control.check("deuda_totales");
      if (permiso > 0) {
        var view = new app.views.DeudaTotalesResultados({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    }, 
    
    ver_comisiones_vendedores : function() {
      var permiso = control.check("comisiones_vendedores");
      if (permiso > 0) {
        var modelo = new app.models.ComisionesVendedores();
        app.views.comisiones_vendedoresResultados = new app.views.ComisionesVendedoresResultados({
          permiso: permiso,
          model: modelo
        });
        this.mostrar({
          "top" : app.views.comisiones_vendedoresResultados.el,
        });
      }
    },
    
    ver_iva_ventas: function() 
    {
      var permiso = control.check("iva_ventas");
      if (permiso > 0) {
        app.views.iva_ventasParametros = new app.views.IvaVentasParametros({
          permiso: permiso,
        });
        this.mostrar({
          "top" : app.views.iva_ventasParametros.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },
    
    ver_iva_compras: function() 
    {
      var permiso = control.check("iva_compras");
      if (permiso > 0) {
        app.views.iva_comprasParametros = new app.views.IvaComprasParametros({
          permiso: permiso,
        });
        this.mostrar({
          "top" : app.views.iva_comprasParametros.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },    
    
    ver_percepcion_iibb : function() {
      var permiso = control.check("percepcion_iibb");
      if (permiso > 0) {
        app.views.percepcionesIIBBParametros = new app.views.PercepcionesIIBBParametros();
        this.mostrar({
          "top" : app.views.percepcionesIIBBParametros.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },
    
    ver_retencion_ib : function() {
      var permiso = control.check("retencion_ib");
      if (permiso > 0) {
        app.views.retencionIBParametros = new app.views.RetencionIBParametros();
        this.mostrar({
          "top" : app.views.retencionIBParametros.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },
    
    ver_retencion_ganancias : function() {
      var permiso = control.check("retencion_ganancias");
      if (permiso > 0) {
        app.views.retencionGananciasParametros = new app.views.RetencionGananciasParametros();
        this.mostrar({
          "top" : app.views.retencionGananciasParametros.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },
    
    ver_actualizar_padron : function() {
      var permiso = control.check("actualizar_padron");
      if (permiso > 0) {
        app.views.actualizarPadron = new app.views.ActualizarPadron();
        this.mostrar({
          "top" : app.views.actualizarPadron.el,
          "left_width" : 0,
          "right_width" : 0,            
        });
      }
    },
    
    ver_servicios_envio: function() {
      var permiso = control.check("servicios_envio");
      if (permiso > 0) {
        app.views.servicios_envioTableView = new app.views.ServiciosEnvioTableView({
          collection: new app.collections.ServiciosEnvio(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.servicios_envioTableView.el,
        });
      }
    },
    ver_servicio_envio: function(id) {
      var self = this;
      var permiso = control.check("servicios_envio");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.servicio_envioEditView = new app.views.ServicioEnvioEditView({
            model: new app.models.ServicioEnvio(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.servicio_envioEditView.el,
          });
        } else {
          var servicio_envio = new app.models.ServicioEnvio({ "id": id });
          servicio_envio.fetch({
            "success":function() {
              app.views.servicio_envioEditView = new app.views.ServicioEnvioEditView({
                model: servicio_envio,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.servicio_envioEditView.el,
              });
            }
          });
        }
      }                
    },      
    
    ver_web_paginas: function() {
      var permiso = control.check("web_paginas");
      if (permiso > 0) {
        app.views.web_paginasTableView = new app.views.WebPaginasTableView({
          collection: new app.collections.WebPaginas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_paginasTableView.el,
        });
      }
    },
    ver_web_pagina: function(id) {
      var self = this;
      var permiso = control.check("web_paginas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_paginaEditView = new app.views.WebPaginaEditView({
            model: new app.models.WebPagina(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_paginaEditView.el,
          });
          workspace.crear_editor('web_paginas_texto');
        } else {
          var web_pagina = new app.models.WebPagina({ "id": id });
          web_pagina.fetch({
            "success":function() {
              app.views.web_paginaEditView = new app.views.WebPaginaEditView({
                model: web_pagina,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_paginaEditView.el,
              });
              workspace.crear_editor('web_paginas_texto');
            }
          });
        }
        
      }                
    },
    
    
    ver_web_textos: function() {
      var permiso = control.check("web_textos");
      if (permiso > 0) {
        app.views.web_textosTableView = new app.views.WebTextosTableView({
          collection: new app.collections.WebTextos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_textosTableView.el,
        });
      }
    },
    ver_web_texto: function(id) {
      var self = this;
      var permiso = control.check("web_textos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_textoEditView = new app.views.WebTextoEditView({
            model: new app.models.WebTexto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_textoEditView.el,
          });
          workspace.crear_editor('web_textos_texto');
        } else {
          var web_texto = new app.models.WebTexto({ "id": id });
          web_texto.fetch({
            "success":function() {
              app.views.web_textoEditView = new app.views.WebTextoEditView({
                model: web_texto,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_textoEditView.el,
              });
              workspace.crear_editor('web_textos_texto');
            }
          });
        }
        
      }                
    },
    
    ver_emails_templates: function() {
      var permiso = control.check("emails_templates");
      if (permiso > 0) {
        app.views.emails_templatesTableView = new app.views.EmailsTemplatesTableView({
          collection: new app.collections.EmailsTemplates(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.emails_templatesTableView.el,
        });
      }
    },
    ver_email_template: function(id) {
      var self = this;
      var permiso = control.check("emails_templates");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.email_templateEditView = new app.views.EmailTemplateEditView({
            model: new app.models.EmailTemplate(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.email_templateEditView.el,
          });
          self.crear_editor("emails_templates_texto");
        } else {
          var email_template = new app.models.EmailTemplate({ "id": id });
          email_template.fetch({
            "success":function() {
              app.views.email_templateEditView = new app.views.EmailTemplateEditView({
                model: email_template,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.email_templateEditView.el,
              });
              self.crear_editor("emails_templates_texto");
            }
          });
        }
        
      }                
    },
    
    
    ver_web_templates: function() {
      var permiso = control.check("web_templates");
      if (permiso > 0) {
        app.views.web_templatesTableView = new app.views.WebTemplatesTableView({
          collection: new app.collections.WebTemplates(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.web_templatesTableView.el,
        });
      }
    },
    ver_web_template: function(id) {
      var self = this;
      var permiso = control.check("web_templates");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.web_templateEditView = new app.views.WebTemplateEditView({
            model: new app.models.WebTemplate(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.web_templateEditView.el,
          });
        } else {
          var web_template = new app.models.WebTemplate({ "id": id });
          web_template.fetch({
            "success":function() {
              app.views.web_templateEditView = new app.views.WebTemplateEditView({
                model: web_template,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.web_templateEditView.el,
              });
            }
          });
        }
        
      }                
    },

    ver_farmacias: function() {
      var permiso = control.check("farmacias");
      if (permiso > 0) {
        app.views.farmaciasTableView = new app.views.FarmaciasTableView({
          collection: new app.collections.Farmacias(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.farmaciasTableView.el,
        });
      }
    },
    ver_farmacia: function(id) {
      var self = this;
      var permiso = control.check("farmacias");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.farmaciaEditView = new app.views.FarmaciaEditView({
            model: new app.models.Farmacia(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.farmaciaEditView.el,
          });
        } else {
          var farmacia = new app.models.Farmacia({ "id": id });
          farmacia.fetch({
            "success":function() {
              app.views.farmaciaEditView = new app.views.FarmaciaEditView({
                model: farmacia,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.farmaciaEditView.el,
              });
            }
          });
        }
        
      }                
    },
    
    ver_farmacias_turnos: function() {
      var permiso = control.check("farmacias_turnos");
      if (permiso > 0) {
        app.views.farmacias_turnosTableView = new app.views.FarmaciasTurnosTableView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.farmacias_turnosTableView.el,
        });
      }
    },

    ver_turnos_medicos: function() {
      var permiso = control.check("turnos_medicos");
      if (permiso > 0) {
        app.views.turnos_medicosView = new app.views.TurnosMedicosView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.turnos_medicosView.el,
        });
      }
    },

    ver_turnos: function() {
      var permiso = control.check("turnos");
      if (permiso > 0) {
        app.views.turnosTableView = new app.views.TurnosTableView({
          collection: new app.collections.Turnos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.turnosTableView.el,
        });
      }
    },
    ver_turnos_calendario: function() {
      var permiso = control.check("turnos");
      if (permiso > 0) {
        app.views.turnosView = new app.views.TurnosView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.turnosView.el,
        });
      }
    },

    ver_mantenimientos: function() {
      var permiso = control.check("mantenimientos");
      if (permiso > 0) {
        app.views.mantenimientosView = new app.views.MantenimientosView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.mantenimientosView.el,
        });
      }
    },

    ver_profesional_turnos: function() {
      var permiso = control.check("profesional_turnos");
      if (permiso > 0) {
        var view = new app.views.ProfesionalTurnosView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_propiedades_reservas: function() {
      var permiso = control.check("propiedades_reservas");
      if (permiso > 0) {
        var view = new app.views.PropiedadesReservasTableView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
          "top_height": "100%",
          "full": 1,          
        });
      }
    },

    ver_propiedades_reservas_listado: function() {
      var permiso = control.check("propiedades_reservas");
      if (permiso > 0) {
        var view = new app.views.PropiedadesReservasListadoView({
          collection: new app.collections.PropiedadesReservas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
          "top_height": "100%",
          "full": 1,
        });
      }
    },

    ver_reservas: function() {
      var permiso = control.check("reservas");
      if (permiso > 0) {
        var view = new app.views.ReservasTableView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_reservas_listado: function() {
      var permiso = control.check("reservas");
      if (permiso > 0) {
        var view = new app.views.ReservasListadoView({
          collection: new app.collections.Reservas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_reservas_viajes: function() {
      var permiso = control.check("reservas_viajes");
      if (permiso > 0) {
        var view = new app.views.ReservasViajesTableView({
          collection: new app.collections.ReservasViajes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_ocupaciones: function() {
      var permiso = control.check("ocupaciones");
      if (permiso > 0) {
        var view = new app.views.OcupacionesTableView({
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    
    ver_necrologicas: function() {
      var permiso = control.check("necrologicas");
      if (permiso > 0) {
        app.views.necrologicasTableView = new app.views.NecrologicasTableView({
          collection: new app.collections.Necrologicas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.necrologicasTableView.el,
        });
      }
    },
    ver_necrologica: function(id) {
      var self = this;
      var permiso = control.check("necrologicas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.necrologicaEditView = new app.views.NecrologicaEditView({
            model: new app.models.Necrologica(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.necrologicaEditView.el,
          });
          workspace.crear_editor('necrologicas_texto');
        } else {
          var necrologica = new app.models.Necrologica({ "id": id });
          necrologica.fetch({
            "success":function() {
              app.views.necrologicaEditView = new app.views.NecrologicaEditView({
                model: necrologica,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.necrologicaEditView.el,
              });
              workspace.crear_editor('necrologicas_texto');
            }
          });
        }
      }
    },
    
    ver_tutores: function() {
      var permiso = control.check("tutores");
      if (permiso > 0) {
        app.views.tutoresTableView = new app.views.TutoresTableView({
          collection: new app.collections.Tutores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.tutoresTableView.el,
        });
      }
    },
    ver_tutor: function(id) {
      var self = this;
      var permiso = control.check("tutores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.tutorEditView = new app.views.TutorEditView({
            model: new app.models.Tutor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.tutorEditView.el,
          });
        } else {
          var tutor = new app.models.Tutor({ "id": id });
          tutor.fetch({
            "success":function() {
              app.views.tutorEditView = new app.views.TutorEditView({
                model: tutor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.tutorEditView.el,
              });
            }
          });
        }
      }                
    },
    
    ver_alumnos_por_comision:function(id_comision) {
      this.ver_alumnos(id_comision);
    },
    ver_alumnos: function(id_comision) {
      var permiso = control.check("alumnos");
      id_comision = (id_comision || 0);
      if (permiso > 0) {
        app.views.alumnosTableView = new app.views.AlumnosTableView({
          collection: new app.collections.Alumnos(),
          id_comision: id_comision,
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.alumnosTableView.el,
        });
      }
    },
    ver_alumno: function(id) {
      var self = this;
      var permiso = control.check("alumnos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.alumnoEditView = new app.views.AlumnoEditView({
            model: new app.models.Alumno(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.alumnoEditView.el,
          });
        } else {
          var alumno = new app.models.Alumno({ "id": id });
          alumno.fetch({
            "success":function() {
              app.views.alumnoEditView = new app.views.AlumnoEditView({
                model: alumno,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.alumnoEditView.el,
              });
            }
          });
        }
      }                
    },

    cargar_comision: function(id_comision,success) {
      window.comision = new app.models.Comision({ "id": id_comision });
      window.comision.fetch({
        "success":success
      });
    },

    ver_examenes: function(id_comision) {
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        var self = this;
        this.cargar_comision(id_comision,function(){
          app.views.examenesTableView = new app.views.ReporteExamenesTableView({
            permiso: permiso
          });
          self.mostrar({
            "top" : app.views.examenesTableView.el,
          });
        });
      }
    },

    ver_examen: function(id) {
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        var self = this;
        if (id == undefined) {
          if (typeof window.comision === "undefined") {
            alert("Por favor seleccione una comision."); return;
          }
          if (typeof window.id_materia === "undefined") {
            alert("Por favor seleccione una materia."); return;
          }
          $.ajax({
            "url":"examenes/function/nuevo/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id_comision":window.window.comision.id,
              "id_materia":window.id_materia,              
            },
            "success":function(r) {
              var view = new app.views.ExamenView({
                model: new app.models.Examen(r),
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          })
        } else {
          var examen = new app.models.Examen({ "id": id });
          examen.fetch({
            "success":function() {
              var view = new app.views.ExamenView({
                model: examen,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    },

    ver_comision_calendario: function(id) {
      var self = this;
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        this.cargar_comision(id,function(){
          app.views.comisionEditView = new app.views.ComisionCalendarioView({
            permiso: permiso
          });
          self.mostrar({
            "top" : app.views.comisionEditView.el,
          });
        });
      }
    }, 

    ver_asistencias: function(id_comision) {
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        var self = this;
        this.cargar_comision(id_comision,function(){
          app.views.asistenciasTableView = new app.views.ReporteAsistenciasTableView({
            permiso: permiso
          });
          self.mostrar({
            "top" : app.views.asistenciasTableView.el,
          });
        });
      }
    },
    ver_asistencia: function(id_comision) {
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        var self = this;
        this.cargar_comision(id_comision,function(){
          app.views.asistenciasTableView = new app.views.AsistenciasTableView({
            permiso: permiso
          });
          self.mostrar({
            "top" : app.views.asistenciasTableView.el,
          });
        });
      }
    },

    ver_asistencias_docentes: function() {
      var permiso = control.check("docentes");
      if (permiso > 0) {
        var self = this;
        app.views.asistencias_docentesTableView = new app.views.ReporteAsistenciasDocentesTableView({
          permiso: permiso
        });
        self.mostrar({
          "top" : app.views.asistencias_docentesTableView.el,
        });
      }
    },
    ver_asistencia_docente: function() {
      var permiso = control.check("docentes");
      if (permiso > 0) {
        var self = this;
        app.views.asistencias_docentesTableView = new app.views.AsistenciasDocentesTableView({
          permiso: permiso
        });
        self.mostrar({
          "top" : app.views.asistencias_docentesTableView.el,
        });
      }
    },


    ver_docentes_por_departamento:function(id_departamento) {
      this.ver_docentes(id_departamento);
    },
    ver_docentes: function(id_departamento) {
      id_departamento = (id_departamento || 0);
      var permiso = control.check("docentes");
      if (permiso > 0) {
        app.views.docentesTableView = new app.views.DocentesTableView({
          id_departamento: id_departamento,
          collection: new app.collections.Docentes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.docentesTableView.el,
        });
      }
    },
    ver_docente: function(id) {
      var self = this;
      var permiso = control.check("docentes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.docenteEditView = new app.views.DocenteEditView({
            model: new app.models.Docente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.docenteEditView.el,
          });
        } else {
          var docente = new app.models.Docente({ "id": id });
          docente.fetch({
            "success":function() {
              app.views.docenteEditView = new app.views.DocenteEditView({
                model: docente,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.docenteEditView.el,
              });
            }
          });
        }
      }                
    },

    
    ver_departamentos: function() {
      var permiso = control.check("departamentos");
      if (permiso > 0) {
        app.views.departamentosTableView = new app.views.DepartamentosTableView({
          collection: new app.collections.Departamentos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.departamentosTableView.el,
        });
      }
    },
    ver_departamento: function(id) {
      var self = this;
      var permiso = control.check("departamentos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.departamentoEditView = new app.views.DepartamentoEditView({
            model: new app.models.Departamento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.departamentoEditView.el,
          });
        } else {
          var departamento = new app.models.Departamento({ "id": id });
          departamento.fetch({
            "success":function() {
              app.views.departamentoEditView = new app.views.DepartamentoEditView({
                model: departamento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.departamentoEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_reposicion_asistida: function() {
      var permiso = control.check("reposicion_asistida");
      if (permiso > 0) {
        var view = new app.views.ReposicionAsistidaTableView({
          collection: new app.collections.ReposicionAsistida(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_detalle_reposicion_asistida: function(id_proveedor,id_sucursal) {
      var self = this;
      var permiso = control.check("reposicion_asistida");
      if (permiso > 0) {
          var view = new app.views.ReposicionAsistidaEditView({
            model: new app.models.ReposicionAsistida(),
            permiso: permiso,
            id_proveedor: id_proveedor,
            id_sucursal: id_sucursal,
          });
          this.mostrar({
            "top" : view.el,
          });
      }                
    },


    ver_ingresos_proveedores: function() {
      var permiso = control.check("ingresos_proveedores");
      if (permiso > 0) {
        app.views.ingresos_proveedoresTableView = new app.views.IngresosProveedoresTableView({
          collection: new app.collections.IngresosProveedores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.ingresos_proveedoresTableView.el,
        });
      }
    },
    ver_ingreso_proveedor: function(id) {
      var self = this;
      var permiso = control.check("ingresos_proveedores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.ingreso_proveedorEditView = new app.views.IngresoProveedorEditView({
            model: new app.models.IngresoProveedor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.ingreso_proveedorEditView.el,
          });
        } else {
          var ingreso_proveedor = new app.models.IngresoProveedor({ "id": id });
          ingreso_proveedor.fetch({
            "success":function() {
              app.views.ingreso_proveedorEditView = new app.views.IngresoProveedorEditView({
                model: ingreso_proveedor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.ingreso_proveedorEditView.el,
              });
            }
          });
        }
      }                
    },
    ver_nuevo_ingreso_proveedor: function(id) {
      var self = this;
      if (id == undefined) return;
      var permiso = control.check("ingresos_proveedores");
      if (permiso > 0) {
        var view = new app.views.IngresoProveedorEditView({
          model: new app.models.IngresoProveedor({
            "id_proveedor":id
          }),
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }                
    },

    ver_reparaciones: function() {
      var permiso = control.check("reparaciones");
      if (permiso > 0) {
        app.views.reparacionesTableView = new app.views.ReparacionesTableView({
          collection: new app.collections.Reparaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.reparacionesTableView.el,
        });
      }
    },

    ver_transferencias_stock: function() {
      var permiso = control.check("transferencias_stock");
      if (permiso > 0) {
        app.views.transferencia_stockTableView = new app.views.TransferenciasStockTableView({
          collection: new app.collections.TransferenciasStock(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.transferencia_stockTableView.el,
        });
      }
    },
    ver_transferencia_stock: function(id) {
      var self = this;
      var permiso = control.check("transferencias_stock");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.transferencia_stockEditView = new app.views.TransferenciaStockEditView({
            model: new app.models.TransferenciaStock(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.transferencia_stockEditView.el,
          });
        } else {
          var transferencia_stock = new app.models.TransferenciaStock({ "id": id });
          transferencia_stock.fetch({
            "success":function() {
              app.views.transferencia_stockEditView = new app.views.TransferenciaStockEditView({
                model: transferencia_stock,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.transferencia_stockEditView.el,
              });
            }
          });
        }
      }                
    },
    ver_nuevo_transferencia_stock: function(id) {
      var self = this;
      if (id == undefined) return;
      var permiso = control.check("transferencias_stock");
      if (permiso > 0) {
        var view = new app.views.TransferenciaStockEditView({
          model: new app.models.TransferenciaStock({
            "id_proveedor":id
          }),
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }                
    },


    ver_calificaciones: function() {
      var permiso = control.check("calificaciones");
      if (permiso > 0) {
        app.views.calificacionesTableView = new app.views.CalificacionesTableView({
          collection: new app.collections.Calificaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.calificacionesTableView.el,
        });
      }
    },
    ver_calificacion: function(id) {
      var self = this;
      var permiso = control.check("calificaciones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.calificacionEditView = new app.views.CalificacionEditView({
            model: new app.models.Calificacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.calificacionEditView.el,
          });
        } else {
          var calificacion = new app.models.Calificacion({ "id": id });
          calificacion.fetch({
            "success":function() {
              app.views.calificacionEditView = new app.views.CalificacionEditView({
                model: calificacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.calificacionEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_trimestres: function() {
      var permiso = control.check("trimestres");
      if (permiso > 0) {
        app.views.trimestresTableView = new app.views.TrimestresTableView({
          collection: new app.collections.Trimestres(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.trimestresTableView.el,
        });
      }
    },
    ver_trimestre: function(id) {
      var self = this;
      var permiso = control.check("trimestres");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.trimestreEditView = new app.views.TrimestreEditView({
            model: new app.models.Trimestre(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.trimestreEditView.el,
          });
        } else {
          var trimestre = new app.models.Trimestre({ "id": id });
          trimestre.fetch({
            "success":function() {
              app.views.trimestreEditView = new app.views.TrimestreEditView({
                model: trimestre,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.trimestreEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_recorridos_clientes: function() {
      var permiso = control.check("recorridos_clientes");
      if (permiso > 0) {
        app.views.recorridos_clientesTableView = new app.views.RecorridosClientesTableView({
          collection: new app.collections.RecorridosClientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.recorridos_clientesTableView.el,
        });
      }
    },
    ver_recorrido_cliente: function(id) {
      var self = this;
      var permiso = control.check("recorridos_clientes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.recorrido_clienteEditView = new app.views.RecorridoClienteEditView({
            model: new app.models.RecorridoCliente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.recorrido_clienteEditView.el,
          });
        } else {
          var recorrido_cliente = new app.models.RecorridoCliente({ "id": id });
          recorrido_cliente.fetch({
            "success":function() {
              app.views.recorrido_clienteEditView = new app.views.RecorridoClienteEditView({
                model: recorrido_cliente,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.recorrido_clienteEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_comisiones: function() {
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        app.views.comisionesTableView = new app.views.ComisionesTableView({
          collection: new app.collections.Comisiones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.comisionesTableView.el,
        });
      }
    },
    ver_comision: function(id) {
      var self = this;
      var permiso = control.check("comisiones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.comisionEditView = new app.views.ComisionEditView({
            model: new app.models.Comision(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.comisionEditView.el,
          });
        } else {
          var comision = new app.models.Comision({ "id": id });
          comision.fetch({
            "success":function() {
              app.views.comisionEditView = new app.views.ComisionEditView({
                model: comision,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.comisionEditView.el,
              });
            }
          });
        }
      }                
    },       

    ver_sitemaps: function() {
      var permiso = control.check("sitemaps");
      if (permiso > 0) {
        var tabla = new app.views.SitemapsTableView({
          collection: new app.collections.Sitemaps(),
          permiso: permiso,
        });    
        this.mostrar({
          "top" : tabla.el,
        });
      }
    },
    ver_sitemap: function(id) {
      var self = this;
      var permiso = control.check("sitemaps");
      if (permiso > 0) {
        if (id == undefined) {
          var edit = new app.views.SitemapEditView({
            model: new app.models.Sitemap(),
            permiso: permiso
          });
          this.mostrar({
            "top" : edit.el,
          });
        } else {
          var sitemap = new app.models.Sitemap({ "id": id });
          sitemap.fetch({
            "success":function() {
              var edit = new app.views.SitemapEditView({
                model: sitemap,
                permiso: permiso
              });
              self.mostrar({
                "top" : edit.el,
              });
            }
          });
        }
      }                
    },

    
    ver_carreras: function() {
      var permiso = control.check("carreras");
      if (permiso > 0) {
        app.views.carrerasTableView = new app.views.CarrerasTableView({
          collection: new app.collections.Carreras(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.carrerasTableView.el,
        });
      }
    },
    ver_carrera: function(id) {
      var self = this;
      var permiso = control.check("carreras");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.carreraEditView = new app.views.CarreraEditView({
            model: new app.models.Carrera(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.carreraEditView.el,
          });
        } else {
          var carrera = new app.models.Carrera({ "id": id });
          carrera.fetch({
            "success":function() {
              app.views.carreraEditView = new app.views.CarreraEditView({
                model: carrera,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.carreraEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_empresas: function() {
      var permiso = control.check("sindi_empresas");
      if (permiso > 0) {
        app.views.sindi_empresasTableView = new app.views.SindiEmpresasTableView({
          collection: new app.collections.SindiEmpresas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_empresasTableView.el,
        });
      }
    },
    ver_sindi_empresa: function(id) {
      var self = this;
      var permiso = control.check("sindi_empresas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_empresaEditView = new app.views.SindiEmpresaEditView({
            model: new app.models.SindiEmpresa(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_empresaEditView.el,
          });
        } else {
          var sindi_empresa = new app.models.SindiEmpresa({ "id": id });
          sindi_empresa.fetch({
            "success":function() {
              app.views.sindi_empresaEditView = new app.views.SindiEmpresaDetalleView({
                model: sindi_empresa,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_empresaEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_estudios_contables: function() {
      var permiso = control.check("sindi_estudios_contables");
      if (permiso > 0) {
        app.views.sindi_estudios_contablesTableView = new app.views.SindiEstudiosContablesTableView({
          collection: new app.collections.SindiEstudiosContables(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_estudios_contablesTableView.el,
        });
      }
    },
    ver_sindi_estudio_contable: function(id) {
      var self = this;
      var permiso = control.check("sindi_estudios_contables");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_estudio_contableEditView = new app.views.SindiEstudioContableEditView({
            model: new app.models.SindiEstudioContable(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_estudio_contableEditView.el,
          });
        } else {
          var sindi_estudio_contable = new app.models.SindiEstudioContable({ "id": id });
          sindi_estudio_contable.fetch({
            "success":function() {
              app.views.sindi_estudio_contableEditView = new app.views.SindiEstudioContableDetalleView({
                model: sindi_estudio_contable,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_estudio_contableEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_nomencladores: function() {
      var permiso = control.check("sindi_nomencladores");
      if (permiso > 0) {
        var view = new app.views.SindiPracticasView({
          model: new app.models.AbstractModel(),
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_sindi_tipos_afiliados: function() {
      var permiso = control.check("sindi_tipos_afiliados");
      if (permiso > 0) {
        app.views.sindi_tipos_afiliadosTableView = new app.views.SindiTiposAfiliadosTableView({
          collection: new app.collections.SindiTiposAfiliados(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_tipos_afiliadosTableView.el,
        });
      }
    },
    ver_sindi_tipo_afiliado: function(id) {
      var self = this;
      var permiso = control.check("sindi_tipos_afiliados");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_tipo_afiliadoEditView = new app.views.SindiTipoAfiliadoEditView({
            model: new app.models.SindiTipoAfiliado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_tipo_afiliadoEditView.el,
          });
        } else {
          var sindi_tipo_afiliado = new app.models.SindiTipoAfiliado({ "id": id });
          sindi_tipo_afiliado.fetch({
            "success":function() {
              app.views.sindi_tipo_afiliadoEditView = new app.views.SindiTipoAfiliadoEditView({
                model: sindi_tipo_afiliado,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_tipo_afiliadoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_tipos_bonos: function() {
      var permiso = control.check("sindi_tipos_bonos");
      if (permiso > 0) {
        app.views.sindi_tipos_bonosTableView = new app.views.SindiTiposBonosTableView({
          collection: new app.collections.SindiTiposBonos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_tipos_bonosTableView.el,
        });
      }
    },
    ver_sindi_tipo_bono: function(id) {
      var self = this;
      var permiso = control.check("sindi_tipos_bonos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_tipo_bonoEditView = new app.views.SindiTipoBonoEditView({
            model: new app.models.SindiTipoBono(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_tipo_bonoEditView.el,
          });
        } else {
          var sindi_tipo_bono = new app.models.SindiTipoBono({ "id": id });
          sindi_tipo_bono.fetch({
            "success":function() {
              app.views.sindi_tipo_bonoEditView = new app.views.SindiTipoBonoEditView({
                model: sindi_tipo_bono,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_tipo_bonoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_tipos_documentaciones: function() {
      var permiso = control.check("sindi_tipos_documentaciones");
      if (permiso > 0) {
        app.views.sindi_tipos_documentacionesTableView = new app.views.SindiTiposDocumentacionesTableView({
          collection: new app.collections.SindiTiposDocumentaciones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_tipos_documentacionesTableView.el,
        });
      }
    },
    ver_sindi_tipo_documentacion: function(id) {
      var self = this;
      var permiso = control.check("sindi_tipos_documentaciones");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_tipo_documentacionEditView = new app.views.SindiTipoDocumentacionEditView({
            model: new app.models.SindiTipoDocumentacion(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_tipo_documentacionEditView.el,
          });
        } else {
          var sindi_tipo_documentacion = new app.models.SindiTipoDocumentacion({ "id": id });
          sindi_tipo_documentacion.fetch({
            "success":function() {
              app.views.sindi_tipo_documentacionEditView = new app.views.SindiTipoDocumentacionEditView({
                model: sindi_tipo_documentacion,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_tipo_documentacionEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_tipos_practicas: function() {
      var permiso = control.check("sindi_tipos_practicas");
      if (permiso > 0) {
        app.views.sindi_tipos_practicasTableView = new app.views.SindiTiposPracticasTableView({
          collection: new app.collections.SindiTiposPracticas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_tipos_practicasTableView.el,
        });
      }
    },

    ver_sindi_tipos_reintegros: function() {
      var permiso = control.check("sindi_tipos_reintegros");
      if (permiso > 0) {
        app.views.sindi_tipos_reintegrosTableView = new app.views.SindiTiposReintegrosTableView({
          collection: new app.collections.SindiTiposReintegros(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_tipos_reintegrosTableView.el,
        });
      }
    },
    ver_sindi_tipo_reintegro: function(id) {
      var self = this;
      var permiso = control.check("sindi_tipos_reintegros");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_tipo_reintegroEditView = new app.views.SindiTipoReintegroEditView({
            model: new app.models.SindiTipoReintegro(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_tipo_reintegroEditView.el,
          });
        } else {
          var sindi_tipo_reintegro = new app.models.SindiTipoReintegro({ "id": id });
          sindi_tipo_reintegro.fetch({
            "success":function() {
              app.views.sindi_tipo_reintegroEditView = new app.views.SindiTipoReintegroEditView({
                model: sindi_tipo_reintegro,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_tipo_reintegroEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_afiliados: function() {
      var permiso = control.check("sindi_afiliados");
      if (permiso > 0) {
        app.views.sindi_afiliadosTableView = new app.views.SindiAfiliadosTableView({
          collection: new app.collections.SindiAfiliados(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_afiliadosTableView.el,
        });
      }
    },
    ver_sindi_afiliado: function(id) {
      var self = this;
      var permiso = control.check("sindi_afiliados");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_afiliadoEditView = new app.views.SindiAfiliadoEditView({
            model: new app.models.SindiAfiliado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_afiliadoEditView.el,
          });
        } else {
          var sindi_afiliado = new app.models.SindiAfiliado({ "id": id });
          sindi_afiliado.fetch({
            "success":function() {
              var view = new app.views.SindiAfiliadoDetalleView({
                model: sindi_afiliado,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_localidades: function() {
      var permiso = control.check("sindi_localidades");
      if (permiso > 0) {
        app.views.sindi_localidadesTableView = new app.views.SindiLocalidadesTableView({
          collection: new app.collections.SindiLocalidades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_localidadesTableView.el,
        });
      }
    },
    ver_sindi_localidad: function(id) {
      var self = this;
      var permiso = control.check("sindi_localidades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_localidadEditView = new app.views.SindiLocalidadEditView({
            model: new app.models.SindiLocalidad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_localidadEditView.el,
          });
        } else {
          var sindi_localidad = new app.models.SindiLocalidad({ "id": id });
          sindi_localidad.fetch({
            "success":function() {
              app.views.sindi_localidadEditView = new app.views.SindiLocalidadEditView({
                model: sindi_localidad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_localidadEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_cursos_autores: function() {
      var permiso = control.check("cursos");
      if (permiso > 0) {
        app.views.cursos_autoresTableView = new app.views.CursosAutoresTableView({
          collection: new app.collections.CursosAutores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cursos_autoresTableView.el,
        });
      }
    },
    ver_curso_autor: function(id) {
      var self = this;
      var permiso = control.check("cursos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.curso_autorEditView = new app.views.CursoAutorEditView({
            model: new app.models.CursoAutor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.curso_autorEditView.el,
          });
        } else {
          var curso_autor = new app.models.CursoAutor({ "id": id });
          curso_autor.fetch({
            "success":function() {
              app.views.curso_autorEditView = new app.views.CursoAutorEditView({
                model: curso_autor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.curso_autorEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_not_editores: function() {
      var permiso = control.check("not_editores");
      if (permiso > 0) {
        app.views.not_editoresTableView = new app.views.NotEditoresTableView({
          collection: new app.collections.NotEditores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.not_editoresTableView.el,
        });
      }
    },
    ver_not_editor: function(id) {
      var self = this;
      var permiso = control.check("not_editores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.not_editorEditView = new app.views.NotEditorEditView({
            model: new app.models.NotEditor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.not_editorEditView.el,
          });
        } else {
          var not_editor = new app.models.NotEditor({ "id": id });
          not_editor.fetch({
            "success":function() {
              app.views.not_editorEditView = new app.views.NotEditorEditView({
                model: not_editor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.not_editorEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_condiciones_especiales: function() {
      var permiso = control.check("sindi_condiciones_especiales");
      if (permiso > 0) {
        app.views.sindi_condiciones_especialesTableView = new app.views.SindiCondicionesEspecialesTableView({
          collection: new app.collections.SindiCondicionesEspeciales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_condiciones_especialesTableView.el,
        });
      }
    },
    ver_sindi_condicion_especial: function(id) {
      var self = this;
      var permiso = control.check("sindi_condiciones_especiales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_condicion_especialEditView = new app.views.SindiCondicionEspecialEditView({
            model: new app.models.SindiCondicionEspecial(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_condicion_especialEditView.el,
          });
        } else {
          var sindi_condicion_especial = new app.models.SindiCondicionEspecial({ "id": id });
          sindi_condicion_especial.fetch({
            "success":function() {
              app.views.sindi_condicion_especialEditView = new app.views.SindiCondicionEspecialEditView({
                model: sindi_condicion_especial,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_condicion_especialEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_limites_afiliados: function() {
      var permiso = control.check("sindi_limites_afiliados");
      if (permiso > 0) {
        app.views.sindi_limites_afiliadosTableView = new app.views.SindiLimitesAfiliadosTableView({
          collection: new app.collections.SindiLimitesAfiliados(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_limites_afiliadosTableView.el,
        });
      }
    },
    ver_sindi_limite_afiliado: function(id) {
      var self = this;
      var permiso = control.check("sindi_limites_afiliados");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_limite_afiliadoEditView = new app.views.SindiLimiteAfiliadoEditView({
            model: new app.models.SindiLimiteAfiliado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_limite_afiliadoEditView.el,
          });
        } else {
          var sindi_limite_afiliado = new app.models.SindiLimiteAfiliado({ "id": id });
          sindi_limite_afiliado.fetch({
            "success":function() {
              app.views.sindi_limite_afiliadoEditView = new app.views.SindiLimiteAfiliadoEditView({
                model: sindi_limite_afiliado,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_limite_afiliadoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_limites_condiciones_especiales: function() {
      var permiso = control.check("sindi_limites_condiciones_especiales");
      if (permiso > 0) {
        app.views.sindi_limites_condiciones_especialesTableView = new app.views.SindiLimitesCondicionesEspecialesTableView({
          collection: new app.collections.SindiLimitesCondicionesEspeciales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_limites_condiciones_especialesTableView.el,
        });
      }
    },
    ver_sindi_limite_condicion_especial: function(id) {
      var self = this;
      var permiso = control.check("sindi_limites_condiciones_especiales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_limite_condicion_especialEditView = new app.views.SindiLimiteCondicionEspecialEditView({
            model: new app.models.SindiLimiteCondicionEspecial(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_limite_condicion_especialEditView.el,
          });
        } else {
          var sindi_limite_condicion_especial = new app.models.SindiLimiteCondicionEspecial({ "id": id });
          sindi_limite_condicion_especial.fetch({
            "success":function() {
              app.views.sindi_limite_condicion_especialEditView = new app.views.SindiLimiteCondicionEspecialEditView({
                model: sindi_limite_condicion_especial,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_limite_condicion_especialEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_limites_tipos_practicas: function() {
      var permiso = control.check("sindi_limites_tipos_practicas");
      if (permiso > 0) {
        app.views.sindi_limites_tipos_practicasTableView = new app.views.SindiLimitesTiposPracticasTableView({
          collection: new app.collections.SindiLimitesTiposPracticas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_limites_tipos_practicasTableView.el,
        });
      }
    },
    ver_sindi_limite_tipo_practica: function(id) {
      var self = this;
      var permiso = control.check("sindi_limites_tipos_practicas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_limite_tipo_practicaEditView = new app.views.SindiLimiteTipoPracticaEditView({
            model: new app.models.SindiLimiteTipoPractica(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_limite_tipo_practicaEditView.el,
          });
        } else {
          var sindi_limite_tipo_practica = new app.models.SindiLimiteTipoPractica({ "id": id });
          sindi_limite_tipo_practica.fetch({
            "success":function() {
              app.views.sindi_limite_tipo_practicaEditView = new app.views.SindiLimiteTipoPracticaEditView({
                model: sindi_limite_tipo_practica,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_limite_tipo_practicaEditView.el,
              });
            }
          });
        }
      }                
    },

    ver_sindi_bonos: function() {
      var permiso = control.check("sindi_bonos");
      if (permiso > 0) {
        app.views.sindi_bonosView = new app.views.SindiBonosView({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });    
        this.mostrar({
          "top" : app.views.sindi_bonosView.el,
        });
      }
    },

    ver_sindi_limites: function() {
      var permiso = control.check("sindi_limites");
      if (permiso > 0) {
        app.views.sindi_limitesView = new app.views.SindiLimitesView({
          permiso: permiso,
          model: new app.models.AbstractModel(),
        });    
        this.mostrar({
          "top" : app.views.sindi_limitesView.el,
        });
      }
    },  

    ver_sindi_consultas: function() {
      var permiso = control.check("sindi_consultas");
      if (permiso > 0) {
        app.views.sindi_consultasTableView = new app.views.SindiConsultasTableView({
          collection: new app.collections.SindiConsultas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_consultasTableView.el,
        });
      }
    },

    ver_sindi_practicas: function() {
      var permiso = control.check("sindi_practicas");
      if (permiso > 0) {
        app.views.sindi_practicasTableView = new app.views.Sindi_practicasTableView({
          collection: new app.collections.Sindi_practicas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_practicasTableView.el,
        });
      }
    },
    ver_sindi_practica: function(id) {
      var self = this;
      var permiso = control.check("sindi_practicas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_practicaEditView = new app.views.Sindi_practicaEditView({
            model: new app.models.Sindi_practica(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_practicaEditView.el,
          });
        } else {
          var sindi_practica = new app.models.Sindi_practica({ "id": id });
          sindi_practica.fetch({
            "success":function() {
              app.views.sindi_practicaEditView = new app.views.Sindi_practicaEditView({
                model: sindi_practica,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_practicaEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_reintegros: function() {
      var permiso = control.check("sindi_reintegros");
      if (permiso > 0) {
        app.views.sindi_reintegrosTableView = new app.views.Sindi_reintegrosTableView({
          collection: new app.collections.Sindi_reintegros(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_reintegrosTableView.el,
        });
      }
    },
    ver_sindi_reintegro: function(id) {
      var self = this;
      var permiso = control.check("sindi_reintegros");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_reintegroEditView = new app.views.Sindi_reintegroEditView({
            model: new app.models.Sindi_reintegro(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_reintegroEditView.el,
          });
        } else {
          var sindi_reintegro = new app.models.Sindi_reintegro({ "id": id });
          sindi_reintegro.fetch({
            "success":function() {
              app.views.sindi_reintegroEditView = new app.views.Sindi_reintegroEditView({
                model: sindi_reintegro,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_reintegroEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_sindi_recetarios: function() {
      var permiso = control.check("sindi_recetarios");
      if (permiso > 0) {
        app.views.sindi_recetariosTableView = new app.views.Sindi_recetariosTableView({
          collection: new app.collections.Sindi_recetarios(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.sindi_recetariosTableView.el,
        });
      }
    },
    ver_sindi_recetario: function(id) {
      var self = this;
      var permiso = control.check("sindi_recetarios");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.sindi_recetarioEditView = new app.views.Sindi_recetarioEditView({
            model: new app.models.Sindi_recetario(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.sindi_recetarioEditView.el,
          });
        } else {
          var sindi_recetario = new app.models.Sindi_recetario({ "id": id });
          sindi_recetario.fetch({
            "success":function() {
              app.views.sindi_recetarioEditView = new app.views.Sindi_recetarioEditView({
                model: sindi_recetario,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.sindi_recetarioEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_repartidores: function() {
      var permiso = control.check("repartidores");
      if (permiso > 0) {
        app.views.repartidoresTableView = new app.views.RepartidoresTableView({
          collection: new app.collections.Repartidores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.repartidoresTableView.el,
        });
      }
    },
    ver_repartidor: function(id) {
      var self = this;
      var permiso = control.check("repartidores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.repartidorEditView = new app.views.RepartidorEditView({
            model: new app.models.Repartidor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.repartidorEditView.el,
          });
        } else {
          var repartidor = new app.models.Repartidor({ "id": id });
          repartidor.fetch({
            "success":function() {
              app.views.repartidorEditView = new app.views.RepartidorEditView({
                model: repartidor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.repartidorEditView.el,
              });
            }
          });
        }
      }                
    },  
    ver_cuenta_repartidor: function(id) {
      var self = this;
      var permiso = control.check("repartidores");
      if (permiso > 0) {
        var view = new app.views.RepartidoresCajasMovimientosView({
          ver_saldos: 1,
          id_repartidor: id,
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_cuenta_cliente: function(id) {
      var self = this;
      var permiso = control.check("clientes");
      // Que solo los administradores puedan verlo
      if (permiso > 0 && PERFIL == 660) {
        var cliente = new app.models.Cliente({"id":id});
        cliente.fetch({
          "success":function() {
            var view = new app.views.ToqueBilleteraMovimientosView({
              ver_saldos: 1,
              id_cliente: id,
              titulo: cliente.get("nombre"),
              permiso: permiso
            });
            self.mostrar({
              "top" : view.el,
            });
          },
        });
      }
    },

    ver_gustos_helados: function() {
      var permiso = control.check("gustos_helados");
      if (permiso > 0) {
        app.views.gustos_heladosTableView = new app.views.GustosHeladosTableView({
          collection: new app.collections.GustosHelados(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.gustos_heladosTableView.el,
        });
      }
    },
    ver_gusto_helado: function(id) {
      var self = this;
      var permiso = control.check("gustos_helados");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.gusto_heladoEditView = new app.views.GustoHeladoEditView({
            model: new app.models.GustoHelado(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.gusto_heladoEditView.el,
          });
        } else {
          var gusto_helado = new app.models.GustoHelado({ "id": id });
          gusto_helado.fetch({
            "success":function() {
              app.views.gusto_heladoEditView = new app.views.GustoHeladoEditView({
                model: gusto_helado,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.gusto_heladoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_cupones_descuentos: function() {
      var permiso = control.check("cupones_descuentos");
      if (permiso > 0) {
        app.views.cupones_descuentosTableView = new app.views.CuponesDescuentosTableView({
          collection: new app.collections.CuponesDescuentos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.cupones_descuentosTableView.el,
        });
      }
    },
    ver_cupon_descuento: function(id) {
      var self = this;
      var permiso = control.check("cupones_descuentos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.cupon_descuentoEditView = new app.views.CuponDescuentoEditView({
            model: new app.models.CuponDescuento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.cupon_descuentoEditView.el,
          });
        } else {
          var cupon_descuento = new app.models.CuponDescuento({ "id": id });
          cupon_descuento.fetch({
            "success":function() {
              app.views.cupon_descuentoEditView = new app.views.CuponDescuentoEditView({
                model: cupon_descuento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.cupon_descuentoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_cupones: function() {
      var permiso = control.check("cupones");
      if (permiso > 0) {
        view = new app.views.CuponesTableView({
          collection: new app.collections.Cupones(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },
    ver_cupon: function(id) {
      var self = this;
      var permiso = control.check("cupones");
      if (permiso > 0) {
        if (id == undefined) {
          view = new app.views.CuponesEditView({
            model: new app.models.Cupones(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var cupon = new app.models.Cupones({ "id": id });
          cupon.fetch({
            "success":function() {
              view = new app.views.CuponesEditView({
                model: cupon,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    }, 


    ver_andromeda_prescriptores: function() {
      var permiso = control.check("andromeda_prescriptores");
      if (permiso > 0) {
        app.views.andromeda_prescriptoresTableView = new app.views.AndromedaPrescriptoresTableView({
          collection: new app.collections.AndromedaPrescriptores(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.andromeda_prescriptoresTableView.el,
        });
      }
    },
    ver_andromeda_prescriptor: function(id) {
      var self = this;
      var permiso = control.check("andromeda_prescriptores");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.andromeda_prescriptorEditView = new app.views.AndromedaPrescriptorEditView({
            model: new app.models.AndromedaPrescriptor(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.andromeda_prescriptorEditView.el,
          });
        } else {
          var andromeda_prescriptor = new app.models.AndromedaPrescriptor({ "id": id });
          andromeda_prescriptor.fetch({
            "success":function() {
              app.views.andromeda_prescriptorEditView = new app.views.AndromedaPrescriptorEditView({
                model: andromeda_prescriptor,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.andromeda_prescriptorEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_claims: function() {
      var permiso = control.check("petips_claims");
      if (permiso > 0) {
        app.views.petips_claimsTableView = new app.views.PetipsClaimsTableView({
          collection: new app.collections.PetipsClaims(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_claimsTableView.el,
        });
      }
    },
    ver_petips_claim: function(id) {
      var self = this;
      var permiso = control.check("petips_claims");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_claimEditView = new app.views.PetipsClaimEditView({
            model: new app.models.PetipsClaim(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_claimEditView.el,
          });
        } else {
          var petips_claim = new app.models.PetipsClaim({ "id": id });
          petips_claim.fetch({
            "success":function() {
              app.views.petips_claimEditView = new app.views.PetipsClaimEditView({
                model: petips_claim,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_claimEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_animales: function() {
      var permiso = control.check("petips_animales");
      if (permiso > 0) {
        app.views.petips_animalesTableView = new app.views.PetipsAnimalesTableView({
          collection: new app.collections.PetipsAnimales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_animalesTableView.el,
        });
      }
    },
    ver_petips_animal: function(id) {
      var self = this;
      var permiso = control.check("petips_animales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_animalEditView = new app.views.PetipsAnimalEditView({
            model: new app.models.PetipsAnimal(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_animalEditView.el,
          });
        } else {
          var petips_animal = new app.models.PetipsAnimal({ "id": id });
          petips_animal.fetch({
            "success":function() {
              app.views.petips_animalEditView = new app.views.PetipsAnimalEditView({
                model: petips_animal,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_animalEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_edades: function() {
      var permiso = control.check("petips_edades");
      if (permiso > 0) {
        app.views.petips_edadesTableView = new app.views.PetipsEdadesTableView({
          collection: new app.collections.PetipsEdades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_edadesTableView.el,
        });
      }
    },
    ver_petips_edad: function(id) {
      var self = this;
      var permiso = control.check("petips_edades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_edadEditView = new app.views.PetipsEdadEditView({
            model: new app.models.PetipsEdad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_edadEditView.el,
          });
        } else {
          var petips_edad = new app.models.PetipsEdad({ "id": id });
          petips_edad.fetch({
            "success":function() {
              app.views.petips_edadEditView = new app.views.PetipsEdadEditView({
                model: petips_edad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_edadEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_especialidades: function() {
      var permiso = control.check("petips_especialidades");
      if (permiso > 0) {
        app.views.petips_especialidadesTableView = new app.views.PetipsEspecialidadesTableView({
          collection: new app.collections.PetipsEspecialidades(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_especialidadesTableView.el,
        });
      }
    },
    ver_petips_especialidad: function(id) {
      var self = this;
      var permiso = control.check("petips_especialidades");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_especialidadEditView = new app.views.PetipsEspecialidadEditView({
            model: new app.models.PetipsEspecialidad(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_especialidadEditView.el,
          });
        } else {
          var petips_especialidad = new app.models.PetipsEspecialidad({ "id": id });
          petips_especialidad.fetch({
            "success":function() {
              app.views.petips_especialidadEditView = new app.views.PetipsEspecialidadEditView({
                model: petips_especialidad,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_especialidadEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_fabricantes: function() {
      var permiso = control.check("petips_fabricantes");
      if (permiso > 0) {
        app.views.petips_fabricantesTableView = new app.views.PetipsFabricantesTableView({
          collection: new app.collections.PetipsFabricantes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_fabricantesTableView.el,
        });
      }
    },
    ver_petips_fabricante: function(id) {
      var self = this;
      var permiso = control.check("petips_fabricantes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_fabricanteEditView = new app.views.PetipsFabricanteEditView({
            model: new app.models.PetipsFabricante(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_fabricanteEditView.el,
          });
        } else {
          var petips_fabricante = new app.models.PetipsFabricante({ "id": id });
          petips_fabricante.fetch({
            "success":function() {
              app.views.petips_fabricanteEditView = new app.views.PetipsFabricanteEditView({
                model: petips_fabricante,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_fabricanteEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_ingredientes: function() {
      var permiso = control.check("petips_ingredientes");
      if (permiso > 0) {
        app.views.petips_ingredientesTableView = new app.views.PetipsIngredientesTableView({
          collection: new app.collections.PetipsIngredientes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_ingredientesTableView.el,
        });
      }
    },
    ver_petips_ingrediente: function(id) {
      var self = this;
      var permiso = control.check("petips_ingredientes");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_ingredienteEditView = new app.views.PetipsIngredienteEditView({
            model: new app.models.PetipsIngrediente(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_ingredienteEditView.el,
          });
        } else {
          var petips_ingrediente = new app.models.PetipsIngrediente({ "id": id });
          petips_ingrediente.fetch({
            "success":function() {
              app.views.petips_ingredienteEditView = new app.views.PetipsIngredienteEditView({
                model: petips_ingrediente,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_ingredienteEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_marcas: function() {
      var permiso = control.check("petips_marcas");
      if (permiso > 0) {
        app.views.petips_marcasTableView = new app.views.PetipsMarcasTableView({
          collection: new app.collections.PetipsMarcas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_marcasTableView.el,
        });
      }
    },
    ver_petips_marca: function(id) {
      var self = this;
      var permiso = control.check("petips_marcas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_marcaEditView = new app.views.PetipsMarcaEditView({
            model: new app.models.PetipsMarca(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_marcaEditView.el,
          });
        } else {
          var petips_marca = new app.models.PetipsMarca({ "id": id });
          petips_marca.fetch({
            "success":function() {
              app.views.petips_marcaEditView = new app.views.PetipsMarcaEditView({
                model: petips_marca,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_marcaEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_razas: function() {
      var permiso = control.check("petips_razas");
      if (permiso > 0) {
        app.views.petips_razasTableView = new app.views.PetipsRazasTableView({
          collection: new app.collections.PetipsRazas(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_razasTableView.el,
        });
      }
    },
    ver_petips_raza: function(id) {
      var self = this;
      var permiso = control.check("petips_razas");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_razaEditView = new app.views.PetipsRazaEditView({
            model: new app.models.PetipsRaza(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_razaEditView.el,
          });
        } else {
          var petips_raza = new app.models.PetipsRaza({ "id": id });
          petips_raza.fetch({
            "success":function() {
              app.views.petips_razaEditView = new app.views.PetipsRazaEditView({
                model: petips_raza,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_razaEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_segmentos: function() {
      var permiso = control.check("petips_segmentos");
      if (permiso > 0) {
        app.views.petips_segmentosTableView = new app.views.PetipsSegmentosTableView({
          collection: new app.collections.PetipsSegmentos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_segmentosTableView.el,
        });
      }
    },
    ver_petips_segmento: function(id) {
      var self = this;
      var permiso = control.check("petips_segmentos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_segmentoEditView = new app.views.PetipsSegmentoEditView({
            model: new app.models.PetipsSegmento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_segmentoEditView.el,
          });
        } else {
          var petips_segmento = new app.models.PetipsSegmento({ "id": id });
          petips_segmento.fetch({
            "success":function() {
              app.views.petips_segmentoEditView = new app.views.PetipsSegmentoEditView({
                model: petips_segmento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_segmentoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_tamanios_animales: function() {
      var permiso = control.check("petips_tamanios_animales");
      if (permiso > 0) {
        app.views.petips_tamanios_animalesTableView = new app.views.PetipsTamaniosAnimalesTableView({
          collection: new app.collections.PetipsTamaniosAnimales(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_tamanios_animalesTableView.el,
        });
      }
    },
    ver_petips_tamanio_animal: function(id) {
      var self = this;
      var permiso = control.check("petips_tamanios_animales");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_tamanio_animalEditView = new app.views.PetipsTamanioAnimalEditView({
            model: new app.models.PetipsTamanioAnimal(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_tamanio_animalEditView.el,
          });
        } else {
          var petips_tamanio_animal = new app.models.PetipsTamanioAnimal({ "id": id });
          petips_tamanio_animal.fetch({
            "success":function() {
              app.views.petips_tamanio_animalEditView = new app.views.PetipsTamanioAnimalEditView({
                model: petips_tamanio_animal,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_tamanio_animalEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_tipos_alimentos: function() {
      var permiso = control.check("petips_tipos_alimentos");
      if (permiso > 0) {
        app.views.petips_tipos_alimentosTableView = new app.views.PetipsTiposAlimentosTableView({
          collection: new app.collections.PetipsTiposAlimentos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_tipos_alimentosTableView.el,
        });
      }
    },
    ver_petips_tipo_alimento: function(id) {
      var self = this;
      var permiso = control.check("petips_tipos_alimentos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_tipo_alimentoEditView = new app.views.PetipsTipoAlimentoEditView({
            model: new app.models.PetipsTipoAlimento(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_tipo_alimentoEditView.el,
          });
        } else {
          var petips_tipo_alimento = new app.models.PetipsTipoAlimento({ "id": id });
          petips_tipo_alimento.fetch({
            "success":function() {
              app.views.petips_tipo_alimentoEditView = new app.views.PetipsTipoAlimentoEditView({
                model: petips_tipo_alimento,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_tipo_alimentoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_petips_productos: function() {
      var permiso = control.check("petips_productos");
      if (permiso > 0) {
        app.views.petips_productosTableView = new app.views.PetipsProductosTableView({
          collection: new app.collections.PetipsProductos(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.petips_productosTableView.el,
        });
      }
    },
    ver_petips_producto: function(id) {
      var self = this;
      var permiso = control.check("petips_productos");
      if (permiso > 0) {
        if (id == undefined) {
          app.views.petips_productoEditView = new app.views.PetipsProductoEditView({
            model: new app.models.PetipsProducto(),
            permiso: permiso
          });
          this.mostrar({
            "top" : app.views.petips_productoEditView.el,
          });
        } else {
          var petips_producto = new app.models.PetipsProducto({ "id": id });
          petips_producto.fetch({
            "success":function() {
              app.views.petips_productoEditView = new app.views.PetipsProductoEditView({
                model: petips_producto,
                permiso: permiso
              });
              self.mostrar({
                "top" : app.views.petips_productoEditView.el,
              });
            }
          });
        }
      }                
    },  

    ver_clientes_log: function() {
      var permiso = control.check("clientes_log");
      if (permiso > 0) {
        app.views.clientes_logTableView = new app.views.ClientesLogTableView({
          collection: new app.collections.ClientesLog(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : app.views.clientes_logTableView.el,
        });
      }
    },

    ver_facturas_paycomet: function() {
      var permiso = control.check("facturas_paycomet");
      if (permiso > 0) {
        var view = new app.views.FacturasPaycometTableView({
          collection: new app.collections.FacturasPaycomet(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_mi_plan: function() {
      var self = this;
      var permiso = control.check("mi_plan");
      if (permiso > 0) {
        $.ajax({
          "url": "planes/function/get_info_planes",
          "type": "post",
          "dataType": "json",
          "data": {
            "id_empresa": ID_EMPRESA,
          },success:function(r) {
            var view = new app.views.MiPlanView({
              model: new app.models.AbstractModel({
                "planes": r.planes,
              }),
              permiso: permiso
            });
            self.mostrar({
              "top" : view.el,
            });
          },
        });
      }
    },

    ver_muchas_gracias: function() {
      var self = this;
      var permiso = control.check("mi_plan");
      if (permiso > 0) {
        view = new app.views.MuchasGraciasView({
          model: new app.models.AbstractModel(),
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_pago_rechazado: function() {
      var self = this;
      var permiso = control.check("mi_plan");
      if (permiso > 0) {
        view = new app.views.PagoRechazadoView({
          model: new app.models.AbstractModel(),
          permiso: permiso
        });
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_anunciantes: function() {
      var permiso = control.check("anunciantes");
      if (permiso > 0) {
        view = new app.views.AnunciantesTableView({
          collection: new app.collections.Anunciantes(),
          permiso: permiso
        });    
        this.mostrar({
          "top" : view.el,
        });
      }
    },

    ver_anunciante: function(id) {
      var self = this;
      var permiso = control.check("anunciantes");
      if (permiso > 0) {
        if (id == undefined) {
          view = new app.views.AnunciantesEditView({
            model: new app.models.Anunciantes(),
            permiso: permiso
          });
          this.mostrar({
            "top" : view.el,
          });
        } else {
          var anunciante = new app.models.Anunciantes({ "id": id });
          anunciante.fetch({
            "success":function() {
              view = new app.views.AnunciantesEditView({
                model: anunciante,
                permiso: permiso
              });
              self.mostrar({
                "top" : view.el,
              });
            }
          });
        }
      }                
    }, 


// NEXT_IMPLEMENTACION

















    nuevo_email: function(consulta) {

      // Si no se manda un modelo
      if (consulta == undefined) {
        consulta = new app.models.Consulta({
          "tipo":1, // 0=Recibido, 1=Enviado
        });
      }

      // Si no tiene seteado adjuntos por defecto
      if (typeof consulta.get("links_adjuntos") == "undefined") {
        consulta.set({"links_adjuntos":[]});
      }

      // Indica que estamos mandando un email con el usuario que estamos logueados
      consulta.set({
        "id_origen":5,
        "id_usuario":ID_USUARIO,
        "fecha":moment().format("DD/MM/YYYY"),
        "hora":moment().format("HH:mm:ss"),
      });

      var emailView = new app.views.EmailView({
        model: consulta
      });
      var d = $("<div/>").append(emailView.el);
      crearLightboxHTML({
        "html":d,
        "width":800,
        "height":400,
        "escapable":false,
      });
      workspace.crear_editor('email_texto',{"toolbar":"Basic"});
    },
    
    nueva_consulta: function(consulta) {
      if (consulta == undefined) {
        consulta = new app.models.Consulta();
      }
      var consultaEditView = new app.views.ConsultaEditView({
        model: consulta
      });
      var d = $("<div/>").append(consultaEditView.el);
      crearLightboxHTML({
        "html":d,
        "width":600,
        "height":500,
      });
    },

    // Abre el cuadro de dialogo para enviar la factura por email
    enviar_factura: function(id,id_punto_venta) {
      var factura = new app.models.Factura({
        "id": id,
        "id_punto_venta": id_punto_venta,
        "id_empresa": ID_EMPRESA,
      });
      factura.fetch({
        "success":function() {
          var url = "https://www.varcreative.com/sistema/facturas/function/ver_pdf/"+id+"/"+id_punto_venta+"/"+ID_EMPRESA+"?v=1";
          var a = "<a style='font-size:15px;text-decoration:none;background-color:#4dbecf;color:white;font-weight:bold;padding:10px 25px;border-radius:5px;display:inline-block' href='"+url+"'>Ver comprobante</a>";
          var consulta = new app.models.Consulta({
            "tipo":1, // 0=Recibido, 1=Enviado
            "id_contacto":factura.get("id_cliente"),
            "email":factura.get("cliente").email,
            "id_usuario":ID_USUARIO,
            "links_adjuntos":[{
              "tipo":-1,
              "nombre":factura.get("comprobante"),
              "id_objeto":a,
            }]
          });
          workspace.nuevo_email(consulta);
        }
      });
    },
    
    imprimir_factura: function(id,id_punto_venta,tipo_impresion,callback) {
      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='facturas/function/ver_pdf/"+id+"/"+id_punto_venta+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      iframe+='<button onclick="workspace.enviar_factura('+id+','+id_punto_venta+')" class="btn btn-info btn-addon m-r">';
      iframe+='<i class="fa fa-send"></i><span>Enviar</span>';
      iframe+='</button>';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":920,
        "height":600,
        "callback":callback
      });
    },
    
    imprimir_remito: function(id,id_punto_venta,callback) {
      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='remitos/function/ver_pdf/"+id+"/"+id_punto_venta+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      iframe+='<button class="btn btn-info btn-addon m-r">';
      iframe+='<i class="fa fa-send"></i><span>Enviar</span>';
      iframe+='</button>';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":920,
        "height":600,
        "callback":callback
      });
    },
    
    imprimir_pedido: function(id,callback) {
      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='pedidos/function/ver_pdf/"+id+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      iframe+='<button class="btn btn-info btn-addon m-r">';
      iframe+='<i class="fa fa-send"></i><span>Enviar</span>';
      iframe+='</button>';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":920,
        "height":600,
        "callback":callback
      });
    },      
    
    cerrar_impresion: function() {
      $('.modal:last').modal('hide');        
    },
    
    
    imprimir_reporte: function(url,callback) {
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='"+url+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":920,
        "height":600,
        "callback":callback
      });
    },

    actualizar_articulos: function() {
      workspace.esperar("Actualizando articulos...");
      var id_punto_venta = 0;
      for(var i=0; i< puntos_venta.length; i++) {
        var pv = puntos_venta[i];
        if (pv.por_default == 1) {
          id_punto_venta = pv.id;
          break;
        }
      }
      $.ajax({
        "timeout":0,
        "url":"articulos/function/get_data_from_server/",
        "data":{
          "id_sucursal":ID_SUCURSAL,
          "id_usuario":ID_USUARIO,
          "id_empresa":ID_EMPRESA,
          "id_punto_venta":id_punto_venta,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          alert(r.mensaje);
          if (r.error==0) location.reload();
          $(".modal:last").trigger('click');
        },
        "error":function(r) {
          $(".modal:last").trigger('click');
        }
      })
    },

    // Esta funcion actualiza todo lo que el PUNTO DE VENTA necesita 
    // Articulos, clientes, tarjetas, tarjetas_intereses, etc.
    actualizar_informacion: function(completa) {
      completa = ((typeof completa == "undefined") ? 0 : 1);
      workspace.esperar("Actualizando...");
      var id_punto_venta = 0;
      for(var i=0; i< puntos_venta.length; i++) {
        var pv = puntos_venta[i];
        if (pv.por_default == 1) {
          id_punto_venta = pv.id;
          break;
        }
      }
      $.ajax({
        "timeout":0,
        "url":"uploader/function/get_data_from_server/",
        "data":{
          "id_sucursal":ID_SUCURSAL,
          "id_usuario":ID_USUARIO,
          "id_empresa":ID_EMPRESA,
          "id_punto_venta":id_punto_venta,
          "completa":completa,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          alert(r.mensaje);
          if (r.error==0) location.reload();
          $(".modal:last").trigger('click');
        },
        "error":function(r) {
          $(".modal:last").trigger('click');
        }
      });
    },

    // Actualiza el propio sistema, utilizando GIT
    actualizar_sistema: function() {
      workspace.esperar("Actualizando...");
      $.ajax({
        "timeout":0,
        "url":"uploader/function/start_upgrade/",
        "dataType":"json",
        "success":function(r) {
          alert(r.mensaje);
          if (r.error==0) location.reload();
          $(".modal:last").trigger('click');
        },
        "error":function(r) {
          $(".modal:last").trigger('click');
        }
      });
    },

    actualizar_articulos_clientes_don_yeyo: function() {
      this.actualizar_articulos_clientes(953);
    },

    actualizar_articulos_clientes: function(id_empresa) {
      var url = "articulos/function/calcular_promedio_por_cliente/";
      if (typeof id_empresa != undefined) url += "?id_empresa="+id_empresa;
      workspace.esperar("Actualizando...");
      $.ajax({
        "timeout":0,
        "url":url,
        "dataType":"json",
        "success":function(r) {
          alert(r.mensaje);
          if (r.error==0) location.reload();
          $(".modal:last").trigger('click');
        },
        "error":function(r) {
          $(".modal:last").trigger('click');
        }
      });
    },
    
    // Abre un lightbox con un mensaje de espera
    esperar: function(mensaje) {
      var a = new app.mixins.Wait({
        model: new app.models.AbstractModel(),
        mensaje: mensaje,
      });
      crearLightboxHTML({
        "html":a.el,
        "width":400,
        "height":200,
      });
    },

    cambiar_usuario: function() {
      var email = $("#app_otras_empresas").val();
      var pass = prompt("Ingrese la clave: ");
      if (!pass) return;
      pass = hex_md5(pass);
      $.ajax({
        "url":"login/check/",
        "type":"post",
        "data":{
          "nombre":email,
          "password":pass,
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == false) window.location.reload();
          else alert(r.mensaje);
        }
      });  
    },

    // CAMBIA LA SUCURSAL QUE ESTA VIENDO
    cambiar_sucursal: function() {
      $.ajax({
        "url":"usuarios/function/cambiar_sucursal/",
        "type":"post",
        "data":{
          "id_sucursal":$("#app_sucursales").val(),
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == false) window.location.reload();
        }
      });  
    },
    
    // CAMBIA EL ESTADO DEL SISTEMA
    cambiar_estado: function() {
      if (ESTADO == 0) {
        window.acepto_supervisor = 0;
        var abstractModel = Backbone.Model.extend();
        var sup = new app.views.CodigoSupervisorView({
          model: new abstractModel()
        });
        crearLightboxHTML({
          "html":sup.el,
          "width":400,
          "height":200,
          "callback":function(){
            if (window.acepto_supervisor == 1) {
              $.ajax({
                "url":"login/estado/",
                "dataType":"json",
                "success":function(r) {
                  if (r.error == false) window.location.reload();
                }
              });  
            }
          }
        });
        $("#codigo_supervisor_texto").focus();
      } else {
        $.ajax({
          "url":"login/estado/",
          "dataType":"json",
          "success":function(r) {
            if (r.error == false) window.location.reload();
          }
        });  
      }
    },
    
    crear_editor:function(nombre,config) {
      // Esto se hace para que no tire error de que el CKEditor ya fue creado
      if (typeof config === "undefined") config = {};
      CKEDITOR.dtd.$removeEmpty['span'] = false;
      if (MILLING == 1 || IDIOMA == "en") {
        config.defaultLanguage = 'en';
        config.language = 'en-EN';
        config.wsc_lang = 'en_EN';
        config.scayt_sLang = 'en_EN';
      } else {
        config.defaultLanguage = 'es';
        config.language = 'es-ES';
        config.wsc_lang = 'es_ES';
        config.scayt_sLang = 'es_ES';
      }
      config.filebrowserBrowseUrl = '/sistema/uploads/'+ID_EMPRESA+'/editor/index.php';
      config.disableNativeSpellChecker = false;
      config.allowedContent = true;
      config.extraPlugins = 'image2,youtube,codemirror,widget,lineutils,colordialog,fontawesome,confighelper,scayt,iframe,font,pastefromword';
      config.uploadUrl = '/sistema/uploads/'+ID_EMPRESA+'/editor/connectors/php/filemanager.php';
      config.contentsCss = ['/sistema/resources/css/font-awesome.min.css','/sistema/resources/js/libs/ckeditor_4.6/contents.css'];
      if (config.toolbar == "Basic") {
        config.toolbar = [
          { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
          { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'FontSize', 'PasteFromWord' ] },
          { name: 'links', items: [ 'Link', 'Unlink','-','Undo', 'Redo' ] },
          { name: 'color', items: [ 'TextColor','BGColor' ] },
          { name: 'source', items: [ 'Print','-','Maximize', 'ShowBlocks','Source','Scayt'] },
        ];
      } else {
        config.toolbar = [
          { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
          { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'FontSize', 'PasteFromWord' ] },
          { name: 'links', items: [ 'Templates','-', 'Link', 'Unlink','-','Undo', 'Redo' ] },
          { name: 'insert', items: [ 'Image', 'Youtube', 'Iframe', 'Table', 'HorizontalRule' ] },
          { name: 'color', items: [ 'TextColor','BGColor', 'FontAwesome' ] },
          { name: 'source', items: [ 'Print','-','Maximize', 'ShowBlocks','Source','Scayt'] },
        ];
      }
      config.forcePasteAsPlainText = true;
      config.scayt_autoStartup = true;
      config.scayt_autoStartup = true;
      myinstance = CKEDITOR.instances[nombre];
      if (myinstance) CKEDITOR.remove(myinstance);
      CKEDITOR.replace(nombre,config);
    },

    open_video: function(e) {
      var iframe = $(e).data("iframe");
      var m = '';
      m+='<div class="panel panel-iframe panel-default">';
      m+='<div class="panel-body">';
      m+=iframe;
      m+='</div>';
      m+='</div>';
      m+='</div>';
      crearLightboxHTML({
        "html":m,
        "width":594,
        "height":315,
      });
    },
    
    crear_nestable: function(array, config) {

      if (typeof config === "undefined") config = {};
      config.seleccionar = (config.seleccionar || false);
      if (typeof config.ordenable === "undefined") config = {};

      if (typeof array === "undefined") return "";
      if (array.length == 0) return "";
      var r = '<ol class="dd-list">';
      for(var i=0;i<array.length;i++) {
        var o = array[i];
        r+='<li class="dd-item dd3-item" data-id="'+o.id+'">';
        if (!config.seleccionar) r+='<div class="dd-handle dd3-handle">Drag</div>';
        r+='<div class="dd3-content">';
        if (!config.seleccionar) {
          r+= '<label class="i-checks m-b-none m-r-xs">';
          r+= '<input class="esc check-row" value="'+o.id+'" type="checkbox"><i></i>';
          r+= '</label>';
          r+= '<a href="javascript:void" class="editar cp text-info">'+(typeof o.title != "undefined" ? o.title : (typeof o.nombre != "undefined" ? o.nombre : "") )+'</a>';
        } else {
          r+=o.title;
        } 
        r+='</div>';       
          if (!config.seleccionar) {
          r+=workspace.crear_nestable(o.children);
        }
        r+='</li>';
      }
      r+='</ol>';
      return r;
    },

      imprimir_comanda: function(id,titulo,usuario) {
        $.ajax({
          "url":"pedidos_mesas/function/imprimir_comanda/"+id,
          "dataType":"json",
          "success":function(r) {
           r.titulo = titulo;
           r.usuario = usuario;
           var data = "pedido="+JSON.stringify(r);
           $.ajax({
            "url":SERVIDOR_LOCAL+"/imprimir_comanda.php",
            "data":data,
            "dataType":"json",
            "type":"post",
          });
         }
       })
      },
      
    // Crea el arbol de <select> a partir de una estructura de Array con children
    crear_select:function(array,ident,selected,condition) {
      var r = "";
      if (!$.isArray(array) || array.length <= 0) return r;
      for(var i=0;i<array.length;i++) {
        var o = array[i];

        var ingresar = true;
        if (typeof condition == "function") {
          ingresar = condition(o);
        }
        if (ingresar) {
          // En data-ids ponemos todos los hijos de la categoria padre
          var ids = workspace.get_sub_ids(o.children);
          var ids_s = (ids.length > 0) ? o.id+"-"+ids.join("-") : o.id;

          r+= "<option data-ids='"+ids_s+"' value='"+o.id+"' "+((selected == o.id)?"selected":"");
          if (typeof o.totaliza_en != "undefined") r+=" data-totaliza_en='"+o.totaliza_en+"' ";
          r+=">";
          r+= ident + o.title;
          r+="</option>";
          r+= workspace.crear_select(o.children,ident+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",selected,condition);          
        }
      }
      return r;
    },
    get_sub_ids:function(array) {
      // Esta funcion es utilizada para obtener todos los IDS de las categorias hijos
      var ids = new Array();
      for(var i=0;i<array.length;i++) {
        var o = array[i];
        ids.push(o.id);
        if (o.children.length > 0) ids = ids.concat(workspace.get_sub_ids(o.children));
      }
      return ids;
    },

    // Aplana una estructura con children
    flatten: function(array) {
      var res = new Array();
      if (!$.isArray(array) || array.length <= 0) return null;
      for(var i=0;i<array.length;i++) {
        var o = array[i];
        res.push(o);
        var r = this.flatten(o.children);
        if (r !== null) res = res.concat(r);
      }
      return res;
    },


    mostrar_notificaciones() {
      app.collections.notificaciones = new app.collections.Notificaciones();
      app.collections.notificaciones.fetch({
        "success":function(r){
          if (r.models.length>0) {
            $("#notification_news").html(r.models.length);
            _.each(r.models,function(item){
              var el = new app.views.NotificacionItem({
                "model":item,
              });
              $("#notification_panel .list-group").append(el.el);
            });
          } else {
            var el = "<div class='media list-group-item'><div class='media-body block m-b-none'>No hay notificaciones nuevas.</div></div>";
            $("#notification_panel .list-group").append(el);
          }
        },
      });
    },

    limpiar_notificaciones: function() {
      $.ajax({
        "url":"notificaciones/function/limpiar/"+ID_EMPRESA+"/0/"+ID_SUCURSAL,
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) workspace.mostrar_notificaciones();
        }
      });
    },

    setear_demora: function(e) {
      var demora = $(e).val()
      $.ajax({
        "url":"usuarios/function/change_property/",
        "type":"post",
        "data":{
          "table":"com_usuarios",
          "id_empresa":ID_EMPRESA,
          "id":ID_USUARIO,
          "attribute":"hora_desde",
          "value":demora,
        },
        "dataType":"json",
        "success":function(r) {
          //location.reload();
        }
      });
    },

    // Funciones para crear un PDF de la misma pantalla
    // array: Objetos seleccionados por jQuery Ej: $(".pagina"), que corresponden a cada pagina a imprimir
    createPDF:function(array,config){
      if (typeof config == "undefined") config = {};
      for(var i=0;i<array.length;i++) {
        this.getCanvas(i,array[i],config);
      }
    },
    checkRender:function(config) {
      var titulo = (typeof config.titulo != "undefined") ? config.titulo : "Reporte";
      var format = (typeof config.format != "undefined") ? config.format : "a4";
      var orientation = (typeof config.orientation != "undefined") ? config.orientation : "landscape";
      // Ordenamos el array
      window.paginas.sort(window.compare);
      var doc = new jsPDF({
        unit:'px', 
        orientation: orientation,
        format: format,
      });     
      var width = doc.internal.pageSize.width * 0.95;
      var height = doc.internal.pageSize.height * 0.95;
      for(var i=0;i<window.paginas.length;i++) {
        var pagina = window.paginas[i];
        var img = pagina.canvas.toDataURL("image/png", 1.0);
        var width = (pagina.canvas.width * height) / pagina.canvas.height;
        //var height = (pagina.canvas.height * 0.95 * width / pagina.canvas.width); // Ajustamos el alto
        doc.addImage(img, 'PNG', 20, 20, width, height);
        if (i != window.paginas.length-1) doc.addPage();
      }
      doc.save(titulo+'.pdf');
      window.paginas = new Array();
    },
    getCanvas: function(page,elem,config){
      var self = this;
      html2canvas($(elem)[0],{
        async:false,
        imageTimeout:0,
        removeContainer:true,
      }).then(function(canvas){
        // Agregamos el canvas en el array
        window.paginas.push({
          "numero": page,
          "canvas": canvas
        });
        self.checkRender(config);
      }); 
    },

    abrir_cajon: function() {
      if (ID_EMPRESA != 224 && MEGASHOP != 1 && ID_EMPRESA != 421 && ID_EMPRESA != 356) {
        $.ajaxq("cola_items",{
          "url":"impresor_fiscal/abrir_cajon/",
          "dataType":"json",
        });
      }
    },

    abrir_calculadora_prestamos: function(id_cliente) {
      console.log("Calculadora prestamos");
      window.calculadora_prestamos = new app.views.CalculadoraPrestamos({
        model: new app.models.AbstractModel({
          "id_cliente":id_cliente
        }),
      });
      console.log(window.calculadora_prestamos);
      $("body").append(window.calculadora_prestamos.el);
    },

    cerrar_calculadora_prestamos: function() {
      window.calculadora_prestamos.remove();
      delete window.calculadora_prestamos;
    },

    toggle_menu: function() {
      $('.app-aside').toggleClass('off-screen');
    },

    asignar_color: function(i) {
      if (i == 0) return "#14d0ad";
      else if (i == 1) return "#28bfd2";
      else if (i == 2) return "#e7ad63";
      else if (i == 3) return "#7798cd";
      else if (i == 4) return "#ea6c5e";
      else if (i == 5) return "#7266ba";
      else if (i == 6) return "#ff8137";
      else if (i == 7) return "#ea5ed9";
      else return "#000";
    },

  });

  window.workspace = new Workspace();

    // Por ahora solo ARGENCASH tiene notificaciones
    if (ID_EMPRESA == 228) {
      window.workspace.mostrar_notificaciones();
    }

    // Cuando cambia de pagina
    window.workspace.on("route", function(route, params) {

      // Si hay algun EventSource abierto, lo cerramos
      if (typeof window.source !== "undefined") {
        window.source.close();
      }

      $('.app-aside').removeClass('off-screen');

      window.ajax_request = 0;
      
      // Simulamos el resize del window por si en la pagina que estamos entrando tiene que tener la barra cerrada
      $(window).trigger("resize");
      
      // Simulamos un click en el header para que se cierren los autocompletes si quedaron abiertos
      $(".navbar-header").trigger("click");

      if (typeof window.timer !== "undefined" && route != "ver_cocinas") {
        window.clearInterval(window.timer);
      }

    });
    
    Backbone.history.start();
    
    if (inicio != "") {
      location.hash = inicio;
    }
    
    $("#fullscreen").click(function(e){
      if (screenfull.enabled) {
        if (screenfull.isFullscreen) {
          $(e.currentTarget).find(".fa").removeClass("fa-compress");
          $(e.currentTarget).find(".fa").addClass("fa-expand");
          screenfull.exit();
        }
        else {
          $(e.currentTarget).find(".fa").removeClass("fa-expand");
          $(e.currentTarget).find(".fa").addClass("fa-compress");
          screenfull.request();
        }
      }
    });
    
    $(".navbar-btn").click(function(e){
      $(".app").toggleClass("app-aside-folded");
    });


    // Esta funcion se llama cada cierto tiempo, con el unico objetivo
    // de mantener viva la session
    window.setInterval(function() {
      $.ajax({
        cache: false,
        type: "GET",
        url: "/sistema/app/refresh_session/",
        success: function(data) {}
      });
    },600000); // Cada 10 minutos        
    
  });

  // IMPORTANTE: LAS CONSULTAS EN AJAX NO SE PUEDEN CACHEAR
  $.ajaxSetup({ cache: false, timeout: 0 });
  
  window.onbeforeunload = function() {
    if (location.hash == "#facturacion") {
      return "Esta seguro que desea salir?";
    }
  }


  window.compare = function(a,b){
    if (a.numero < b.numero) return -1;
    else if (a.numero > b.numero) return 1;
    else return 0;
  }

  /*
  $(window).on('hashchange', function(e){

    if (e.originalEvent.oldURL.indexOf("#facturacion")>0) {
      if (!confirm("Realmente desea salir de la facturacion?")) {
        location.href.hash
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    }
  });
  */

})();