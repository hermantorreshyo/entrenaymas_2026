// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Venta = Backbone.Model.extend({
    urlRoot: "facturas/function/consulta/",
    defaults: {
      fecha: "",
      hora: "",
      sucursal: "",
      caja: "",
      usuario: "",
      cliente: "",
      neto: 0,
      iva: 0,
      total: 0,
      efectivo: 0,
      vuelto: 0,
      tarjeta: 0,
      cta_cte: 0,
      cheque: 0,
      pendiente: 0,
      visto:0,
      telefono: "",
      tipo_impresion: "",
    }
  });
    
})( app.models );

// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Ventas = paginator.requestPager.extend({

    model: model,

    modelId: function (attrs) {
      return attrs.id + "-" + attrs.punto_venta;
    },
    
    paginator_ui: {
      perPage: 30,
    },    

    paginator_core: {
      url: "facturas/function/consulta/",
    },
  });

})( app.collections, app.models.Venta, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.VentasTableView = app.mixins.View.extend({

    template: _.template($("#ventas_resultados_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
      "click .exportar_yeyo":"exportar_yeyo",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar",
      "click .importar_caja":"importar_caja",
      "change #ventas_listado_buscar":"buscar",
      "change #ventas_almacenes":"buscar",
      "change #ventas_forma_pago":"buscar",
      "click .buscar":"buscar",
      "click .enviar":"enviar",
      "click .imprimir_lote":"imprimir_lote",
      "click .imprimir_agrupado":"imprimir_agrupado",
      "click .calcular_iva_lote":"calcular_iva_lote",
      "click .sumar_lote":"sumar_lote",
      "click .ventas_tipo_comprobante_check":"buscar",
      "click .iva_ventas":"iva_ventas",
      "click .percep_ganancias":"percep_ganancias",
      "click .percep_iibb":"percep_iibb",
      "click .editar_vendedor":"editar_vendedor",
      "click .editar_reparto":"editar_reparto",
      "click .imprimir_caja_tato":"imprimir_caja_tato",
      // Para configurar las columnas de la tabla
      "click .configurar_tabla":function() {
        var p = new app.views.ConfiguracionTablaView({
          titulo: "Ventas",
          tabla: window.tabla_ventas,
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });
      },
      "click .cambiar_tab":function(e) {
        window.ventas_listado_in_tipos_estados = $(e.currentTarget).data("tipo");
        this.change_cambiar_tab();
        this.buscar();
      },
      "click .cambiar_tab_origen":function(e) {
        window.ventas_listado_id_origen = $(e.currentTarget).data("origen");
        $(e.currentTarget).parents(".nav-tabs").find(".active").removeClass("active");
        $(e.currentTarget).parent().addClass("active");
        if (window.ventas_listado_id_origen == "1") {
          this.$("#filtro_web").show();
        } else {
          this.$("#filtro_web").hide();
          window.ventas_listado_in_tipos_estados = "";
          this.change_cambiar_tab();
        }
        this.buscar();
      },
      "keydown #ventas_listado_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#ventas_tabla tbody tr .radio:first").focus(); }
      },
      "keypress #ventas_monto":function(e) {
        if (e.which == 13) this.buscar();
      }
    },

    // Se encarga de cambiar el tab de filtros web
    change_cambiar_tab: function() {
      var c = window.ventas_listado_in_tipos_estados;
      this.$("#filtro_web").find(".active").removeClass("active");
      if (c == "") this.$("#cambiar_tab_todos").addClass("active");
      else if (c == "4-5-6-8-9-10") this.$("#cambiar_tab_finalizadas").addClass("active");
      else if (c == "0-1-2-3") this.$("#cambiar_tab_en_proceso").addClass("active");
      else if (c == "7") this.$("#cambiar_tab_abandonados").addClass("active");
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.tipos_comprobante = (this.options.tipos_comprobante == undefined) ? "" : this.options.tipos_comprobante;
      this.tipo = (this.options.tipo == undefined) ? "" : this.options.tipo;
      this.fecha = (this.options.fecha == undefined) ? "" : (this.options.fecha).replace(/\//g,"-");
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;      

      window.ventas_listado_fecha_desde = (typeof window.ventas_listado_fecha_desde != "undefined") ? window.ventas_listado_fecha_desde : this.fecha;
      window.ventas_listado_fecha_hasta = (typeof window.ventas_listado_fecha_hasta != "undefined") ? window.ventas_listado_fecha_hasta : this.fecha;
      window.ventas_listado_hora_desde = (typeof window.ventas_listado_hora_desde != "undefined") ? window.ventas_listado_hora_desde : "";
      window.ventas_listado_hora_hasta = (typeof window.ventas_listado_hora_hasta != "undefined") ? window.ventas_listado_hora_hasta : "";
      window.ventas_listado_filter = (typeof window.ventas_listado_filter != "undefined") ? window.ventas_listado_filter : "";
      window.ventas_listado_fecha_reparto = (typeof window.ventas_listado_fecha_reparto != "undefined") ? window.ventas_listado_fecha_reparto : "";
      window.ventas_listado_numero_reparto = (typeof window.ventas_listado_numero_reparto != "undefined") ? window.ventas_listado_numero_reparto : "";
      window.ventas_listado_punto_venta = (typeof window.ventas_listado_punto_venta != "undefined") ? window.ventas_listado_punto_venta : -1;
      window.ventas_listado_vendedor = (typeof window.ventas_listado_vendedor != "undefined") ? window.ventas_listado_vendedor : 0;
      window.ventas_listado_concepto = (typeof window.ventas_listado_concepto != "undefined") ? window.ventas_listado_concepto : 0;
      window.ventas_listado_sucursal = (typeof window.ventas_listado_sucursal != "undefined") ? window.ventas_listado_sucursal : 0;
      window.ventas_listado_tarjeta = (typeof window.ventas_listado_tarjeta != "undefined") ? window.ventas_listado_tarjeta : 0;
      window.ventas_listado_lote = (typeof window.ventas_listado_lote != "undefined") ? window.ventas_listado_lote : "";
      window.ventas_listado_cupon = (typeof window.ventas_listado_cupon != "undefined") ? window.ventas_listado_cupon : "";
      window.ventas_listado_con_anulados = (typeof window.ventas_listado_con_anulados != "undefined") ? window.ventas_listado_con_anulados : 3;
      window.ventas_listado_monto = (typeof window.ventas_listado_monto != "undefined") ? window.ventas_listado_monto : "";
      window.ventas_listado_codigo_articulo = (typeof window.ventas_listado_codigo_articulo != "undefined") ? window.ventas_listado_codigo_articulo : "";
      window.ventas_listado_monto_tipo = (typeof window.ventas_listado_monto_tipo != "undefined") ? window.ventas_listado_monto_tipo : "";
      window.ventas_listado_tipo_cliente = (typeof window.ventas_listado_tipo_cliente != "undefined") ? window.ventas_listado_tipo_cliente : "";
      window.ventas_listado_forma_pago = (typeof window.ventas_listado_forma_pago != "undefined") ? window.ventas_listado_forma_pago : 0;
      window.ventas_listado_pago = (typeof window.ventas_listado_pago != "undefined") ? window.ventas_listado_pago : -1;
      window.ventas_listado_page = (typeof window.ventas_listado_page != "undefined") ? window.ventas_listado_page : 1;
      window.ventas_listado_tipo_estado = (typeof window.ventas_listado_tipo_estado != "undefined") ? window.ventas_listado_tipo_estado : -1;
      window.ventas_listado_id_origen = (typeof window.ventas_listado_id_origen != "undefined") ? window.ventas_listado_id_origen : -1;
      window.ventas_listado_in_tipos_estados = (typeof window.ventas_listado_in_tipos_estados != "undefined") ? window.ventas_listado_in_tipos_estados : "";
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
        "tipos_comprobante":this.tipos_comprobante,
        "tipo":this.tipo,
      }));
      
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
        
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      
      // Cargamos el paginador
      this.$(".pagination_container").html(this.pagination.el);
      
      createdatepicker(this.$("#ventas_desde"),window.ventas_listado_fecha_desde);
      createdatepicker(this.$("#ventas_hasta"),window.ventas_listado_fecha_hasta);
      if (this.$("#ventas_fecha_reparto").length > 0) createdatepicker(this.$("#ventas_fecha_reparto"),window.ventas_listado_fecha_reparto);
      this.$("#ventas_hora_desde").mask("99:99");
      this.$("#ventas_hora_hasta").mask("99:99");
      
      this.buscar();
    },
    
    exportar_csv: function(obj) {
      var desde = $("#ventas_desde").val();
      desde = desde.replace(/\//g,"-");
      var hasta = $("#ventas_hasta").val();
      hasta = hasta.replace(/\//g,"-");
      window.open("facturas/function/exportar_csv/"+desde+"/"+hasta,"_blank");
    },
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"facturas"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },

    importar_caja: function() {
      var view = new app.views.Importar({
        "table":"ventas",
        "url":"ventas/function/importar_caja/",
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
      });
    },

    editar_vendedor: function() {
      var self = this;
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var ventas_marcadas = new Array();
      $(checks).each(function(i,e){
        ventas_marcadas.push({
          "id":$(e).val(),
          "id_punto_venta":$(e).data("id_punto_venta"),
        });
      });
      var view = new app.views.EditarVendedorView({
        "model": new app.models.AbstractModel(),
        "ventas_marcadas":ventas_marcadas,
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
        "callback":function() {
          self.buscar();
        }
      });
    },

    imprimir_caja_tato: function() {
      var param = {};
      param.desde = window.ventas_listado_fecha_desde;
      param.hasta = window.ventas_listado_fecha_hasta;
      param.tipo_cliente = window.ventas_listado_tipo_cliente;
      $.ajax({
        "url":"facturas/function/imprimir_reporte_epson/",
        "dataType":"json",
        "data":param,
        "type":"post",
      });      
    },

    editar_reparto: function() {
      var self = this;
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var ventas_marcadas = new Array();
      $(checks).each(function(i,e){
        ventas_marcadas.push({
          "id":$(e).val(),
          "id_punto_venta":$(e).data("id_punto_venta"),
        });
      });
      var view = new app.views.EditarFechaRepartoView({
        "model": new app.models.AbstractModel(),
        "ventas_marcadas":ventas_marcadas,
      });
      crearLightboxHTML({
        "html":view.el,
        "width":280,
        "height":140,
        "callback":function() {
          self.buscar();
        }
      });
    },


    iva_ventas: function() {
      var p = new app.views.IvaVentasView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":550,
        "height":140,
      });
    },

    percep_ganancias: function() {
      var p = new app.views.PercepcionGananciasView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":450,
        "height":140,
      });
    },

    percep_iibb: function() {
      var p = new app.views.PercepcionIIBBView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":450,
        "height":140,
      });
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      var filtros = {};

      if (!isEmpty(this.$("#ventas_listado_cliente").val())) 
        filtros.id_cliente = this.$("#ventas_listado_cliente").val();
      if (!isEmpty(this.$("#ventas_listado_numero").val())) 
        filtros.numero = this.$("#ventas_listado_numero").val();        
      
      if (this.$("#ventas_con_anulados").length > 0) {
        if (window.ventas_listado_con_anulados != this.$("#ventas_con_anulados").val()) {
          window.ventas_listado_con_anulados = this.$("#ventas_con_anulados").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_codigo_articulo").length > 0) {
        if (window.ventas_listado_codigo_articulo != this.$("#ventas_codigo_articulo").val().trim()) {
          window.ventas_listado_codigo_articulo = this.$("#ventas_codigo_articulo").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_monto").length > 0) {
        if (window.ventas_listado_monto != this.$("#ventas_monto").val().trim()) {
          window.ventas_listado_monto = this.$("#ventas_monto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_fecha_reparto").length > 0) {
        if (window.ventas_listado_fecha_reparto != this.$("#ventas_fecha_reparto").val().trim()) {
          window.ventas_listado_fecha_reparto = this.$("#ventas_fecha_reparto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_numero_reparto").length > 0) {
        if (window.ventas_listado_numero_reparto != this.$("#ventas_numero_reparto").val().trim()) {
          window.ventas_listado_numero_reparto = this.$("#ventas_numero_reparto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_tipo_estado").length > 0) {
        if (window.ventas_listado_tipo_estado != this.$("#ventas_tipo_estado").val().trim()) {
          window.ventas_listado_tipo_estado = this.$("#ventas_tipo_estado").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_forma_pago").length > 0) {
        if (window.ventas_listado_forma_pago != this.$("#ventas_forma_pago").val().trim()) {
          window.ventas_listado_forma_pago = this.$("#ventas_forma_pago").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_monto_tipo").length > 0) {
        if (window.ventas_listado_monto_tipo != this.$("#ventas_monto_tipo").val().trim()) {
          window.ventas_listado_monto_tipo = this.$("#ventas_monto_tipo").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_hora_desde").length > 0) {
        if (window.ventas_listado_hora_desde != this.$("#ventas_hora_desde").val().trim()) {
          window.ventas_listado_hora_desde = this.$("#ventas_hora_desde").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#ventas_hora_hasta").length > 0) {
        if (window.ventas_listado_hora_hasta != this.$("#ventas_hora_hasta").val().trim()) {
          window.ventas_listado_hora_hasta = this.$("#ventas_hora_hasta").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_tipo_cliente").length > 0) {
        if (window.ventas_listado_tipo_cliente != this.$("#ventas_tipo_cliente").val().trim()) {
          window.ventas_listado_tipo_cliente = this.$("#ventas_tipo_cliente").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_tarjeta").length > 0) {
        if (window.ventas_listado_tarjeta != this.$("#ventas_tarjeta").val()) {
          window.ventas_listado_tarjeta = this.$("#ventas_tarjeta").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_pago").length > 0) {
        if (window.ventas_listado_pago != this.$("#ventas_pago").val()) {
          window.ventas_listado_pago = this.$("#ventas_pago").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_lote").length > 0) {
        if (window.ventas_listado_lote != this.$("#ventas_lote").val().trim()) {
          window.ventas_listado_lote = this.$("#ventas_lote").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_cupon").length > 0) {
        if (window.ventas_listado_cupon != this.$("#ventas_cupon").val().trim()) {
          window.ventas_listado_cupon = this.$("#ventas_cupon").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_listado_buscar").length > 0 && window.ventas_listado_filter != this.$("#ventas_listado_buscar").val().trim()) {
        window.ventas_listado_filter = this.$("#ventas_listado_buscar").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#ventas_desde").length > 0 && window.ventas_listado_fecha_desde != this.$("#ventas_desde").val().trim()) {
        window.ventas_listado_fecha_desde = this.$("#ventas_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#ventas_hasta").length > 0 && window.ventas_listado_fecha_hasta != this.$("#ventas_hasta").val().trim()) {
        window.ventas_listado_fecha_hasta = this.$("#ventas_hasta").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#ventas_puntos_venta").length > 0) {
        if (window.ventas_listado_punto_venta != this.$("#ventas_puntos_venta").val()) {
          window.ventas_listado_punto_venta = this.$("#ventas_puntos_venta").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_puntos_venta").length > 0) {
        if (window.ventas_listado_punto_venta != this.$("#ventas_puntos_venta").val()) {
          window.ventas_listado_punto_venta = this.$("#ventas_puntos_venta").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_almacenes").length > 0) {
        if (window.ventas_listado_sucursal != this.$("#ventas_almacenes").val()) {
          window.ventas_listado_sucursal = this.$("#ventas_almacenes").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_vendedores").length > 0) {
        if (window.ventas_listado_vendedor != this.$("#ventas_vendedores").val()) {
          window.ventas_listado_vendedor = this.$("#ventas_vendedores").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#ventas_conceptos").length > 0) {
        if (window.ventas_listado_concepto != this.$("#ventas_conceptos").val()) {
          window.ventas_listado_concepto = this.$("#ventas_conceptos").val();
          cambio_parametros = true;
        }
      }

      // Filtramos por los tipos de comprobantes
      var tipos_comprobante = new Array();
      if (this.$(".ventas_tipo_comprobante_check").length > 0) {
        var alguno_no_seleccionado = false;
        this.$(".ventas_tipo_comprobante_check").each(function(i,e){
          if ($(e).is(":checked")) {
            tipos_comprobante.push($(e).val());
          } else {
            alguno_no_seleccionado = true;
          }
        });
        if (alguno_no_seleccionado) filtros.tc = tipos_comprobante.join("-");
      } else {
        // No hay filtro en la vista, pasamos los que tenemos por parametro
        filtros.tc = this.tipos_comprobante;
      }

      // Filtramos por los tipos de mesas
      var tipos = new Array();
      this.$(".ventas_tipo_check:checked").each(function(i,e){
        tipos.push($(e).val());
      });
      filtros.tipos = tipos.join("-");      

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.ventas_listado_page = 1;

      filtros.desde = (isEmpty(window.ventas_listado_fecha_desde)) ? "" : window.ventas_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.ventas_listado_fecha_hasta)) ? "" : window.ventas_listado_fecha_hasta.replace(/\//g,"-");
      filtros.fecha_reparto = (isEmpty(window.ventas_listado_fecha_reparto)) ? "" : window.ventas_listado_fecha_reparto.replace(/\//g,"-");
      filtros.hora_desde = window.ventas_listado_hora_desde;
      filtros.hora_hasta = window.ventas_listado_hora_hasta;
      filtros.numero_reparto = window.ventas_listado_numero_reparto;
      filtros.id_vendedor = window.ventas_listado_vendedor;
      filtros.id_concepto = window.ventas_listado_concepto;
      filtros.filter = window.ventas_listado_filter;
      filtros.id_tarjeta = window.ventas_listado_tarjeta;
      filtros.lote = window.ventas_listado_lote;
      filtros.cupon = window.ventas_listado_cupon;
      filtros.id_punto_venta = window.ventas_listado_punto_venta;
      filtros.codigo_articulo = window.ventas_listado_codigo_articulo;
      filtros.forma_pago = window.ventas_listado_forma_pago;
      filtros.pago = window.ventas_listado_pago;
      filtros.tipo_estado = window.ventas_listado_tipo_estado;
      filtros.monto = window.ventas_listado_monto;
      filtros.monto_tipo = window.ventas_listado_monto_tipo;
      filtros.tipo_cliente = window.ventas_listado_tipo_cliente;
      filtros.con_anulados = window.ventas_listado_con_anulados;
      filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : ((this.$("#ventas_usuarios").length > 0) ? this.$("#ventas_usuarios").val() : 0);
      filtros.id_proyecto = ID_PROYECTO;
      filtros.in_tipos_estados = window.ventas_listado_in_tipos_estados;
      filtros.id_origen = window.ventas_listado_id_origen;
      filtros.id_sucursal = (window.ventas_listado_sucursal != 0) ? window.ventas_listado_sucursal : ID_SUCURSAL;

      // TODO: Hacer esto dinamico si quiere que se totalice aca o no
      this.usa_filtros = (MEGASHOP != 1 && (control.check("ventas_listado") == 3) && (!isEmpty(filtros.desde) || !isEmpty(filtros.hasta)));
      filtros.incluir_suma = ((this.usa_filtros)?1:0);
      //if (this.usa_filtros) filtros.offset = 99999;

      this.collection.server_api = filtros;
      this.collection.goTo(window.ventas_listado_page);
    },
    
    exportar : function() {
      
      var fecha_desde = this.$("#ventas_desde").val();
      var fecha_hasta = this.$("#ventas_hasta").val();

      var fecha_reparto = "";
      var numero_reparto = 0;
      if (this.$("#ventas_fecha_reparto").length > 0) {
        fecha_reparto = this.$("#ventas_fecha_reparto").val();  
        numero_reparto = this.$("#ventas_numero_reparto").val();
      }
      if (isEmpty(fecha_reparto)) {
        if (isEmpty(fecha_hasta)) {
          alert("Por favor seleccione una fecha");
          this.$("#ventas_hasta").focus();
          return;
        }
        if (isEmpty(fecha_desde)) {
          alert("Por favor seleccione una fecha");
          this.$("#ventas_desde").focus();
          return;
        }
      }
      
      var url = "/sistema/facturas/function/exportar_excel/?";
      
      if (this.$("#ventas_listado_buscar").length > 0 && !isEmpty(this.$("#ventas_listado_buscar").val()))
        url+="filter="+this.$("#ventas_listado_buscar").val()+"&";
      
      if (this.$("#ventas_listado_cliente").length > 0 && !isEmpty(this.$("#ventas_listado_cliente").val()))
        url+="id_cliente="+this.$("#ventas_listado_cliente").val()+"&";
      
      if (this.$("#ventas_vendedores").length > 0 && !isEmpty(this.$("#ventas_vendedores").val()))
        url+="id_vendedor="+this.$("#ventas_vendedores").val()+"&";
      
      if (this.$("#ventas_listado_numero").length > 0 && !isEmpty(this.$("#ventas_listado_numero").val()))
        url+="numero="+this.$("#ventas_listado_numero").val()+"&";
      
      if (this.$("#ventas_tipo_cliente").length > 0 && !isEmpty(this.$("#ventas_tipo_cliente").val()))
        url+="tipo_cliente="+this.$("#ventas_tipo_cliente").val()+"&";
      
      if (this.$("#ventas_puntos_venta").length > 0 && this.$("#ventas_puntos_venta").val() != -1)
        url+="id_punto_venta="+this.$("#ventas_puntos_venta").val()+"&";

      if (this.$("#ventas_almacenes").length > 0 && this.$("#ventas_almacenes").val() != -1)
        url+="id_sucursal="+this.$("#ventas_almacenes").val()+"&";
        
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) url+="desde="+fecha_desde+"&";
      
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) url+="hasta="+fecha_hasta+"&";

      if (!isEmpty(fecha_reparto)) {
        fecha_reparto = fecha_reparto.replace(/\//g,"-");
        url+="fecha_reparto="+fecha_reparto+"&";
        url+="numero_reparto="+numero_reparto+"&";
      }
      
      // Filtramos por los tipos de comprobantes
      var tipos_comprobante = new Array();
      if (this.$(".ventas_tipo_comprobante_check").length > 0) {
        var alguno_no_seleccionado = false;
        this.$(".ventas_tipo_comprobante_check").each(function(i,e){
          if ($(e).is(":checked")) {
            tipos_comprobante.push($(e).val());
          } else {
            alguno_no_seleccionado = true;
          }
        });
        if (alguno_no_seleccionado) url+="tc="+tipos_comprobante.join("-")+"&";
      } else {
        // No hay filtro en la vista, pasamos los que tenemos por parametro
        url+="tc="+this.tipos_comprobante+"&";
      }

      url+="estado="+ESTADO+"&";

      if (DISTRIBUIDORA == 1) url+="distribuidora="+DISTRIBUIDORA+"&";

      window.open(url,"_blank")
    },

    exportar_yeyo : function() {
      
      var fecha_desde = this.$("#ventas_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#ventas_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#ventas_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#ventas_hasta").focus();
        return;
      }
      
      var url = "/sistema/distrivar/function/exportar_excel/?";
      
      if (this.$("#ventas_listado_buscar").length > 0 && !isEmpty(this.$("#ventas_listado_buscar").val()))
        url+="filter="+this.$("#ventas_listado_buscar").val()+"&";
      
      if (this.$("#ventas_listado_cliente").length > 0 && !isEmpty(this.$("#ventas_listado_cliente").val()))
        url+="id_cliente="+this.$("#ventas_listado_cliente").val()+"&";
      
      if (this.$("#ventas_vendedores").length > 0 && !isEmpty(this.$("#ventas_vendedores").val()))
        url+="id_vendedor="+this.$("#ventas_vendedores").val()+"&";
      
      if (this.$("#ventas_listado_numero").length > 0 && !isEmpty(this.$("#ventas_listado_numero").val()))
        url+="numero="+this.$("#ventas_listado_numero").val()+"&";
      
      if (this.$("#ventas_tipo_cliente").length > 0 && !isEmpty(this.$("#ventas_tipo_cliente").val()))
        url+="tipo_cliente="+this.$("#ventas_tipo_cliente").val()+"&";
      
      if (this.$("#ventas_puntos_venta").length > 0 && this.$("#ventas_puntos_venta").val() != -1)
        url+="id_punto_venta="+this.$("#ventas_puntos_venta").val()+"&";

      if (this.$("#ventas_almacenes").length > 0 && this.$("#ventas_almacenes").val() != -1)
        url+="id_sucursal="+this.$("#ventas_almacenes").val()+"&";
        
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) url+="desde="+fecha_desde+"&";
      
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) url+="hasta="+fecha_hasta+"&";
      
      // Filtramos por los tipos de comprobantes
      var tipos_comprobante = new Array();
      this.$(".ventas_tipo_comprobante_check:checked").each(function(i,e){
        tipos_comprobante.push($(e).val());
      });
      url+="tc="+tipos_comprobante.join("-")+"&";

      window.open(url,"_blank")
    },    
    
    addAll : function () {
      var self = this;
      window.ventas_listado_page = this.pagination.getPage();
      this.$("#ventas_tabla tbody tr").empty();

      this.collection.each(function(i){
        self.addOne(i);
      });

      if (this.usa_filtros) {
        this.$("#ventas_resumen_total").html(Number(this.collection.meta("suma")).format(2)+" €");
        this.$("#ventas_resumen_cantidad").html(this.collection.meta("cantidad"));
        this.$(".resumen").show();
      } else {
        this.$(".resumen").hide();
      }
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var view = new app.views.VentasItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#ventas_tabla tbody").append(view.render().el);
    },
    
    enviar: function() {
      var checks = this.$("#ventas_tabla .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var comprobante = $(e).parents("tr").find(".comprobante").html();
        links_adjuntos.push({
          tipo: TIPO_ADJUNTO_COMPROBANTE,
          id_objeto: id,
          nombre: comprobante,
        });
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Factura Electronica",
      });
      workspace.nuevo_email(email);
    },

    imprimir_lote: function() {
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      if (ID_EMPRESA == 133) {
        var ids = new Array();
        $(checks).each(function(i,e){
          ids.push($(e).val());
        });
        var ids_s = ids.join("-");
        workspace.imprimir_remito(ids_s);
      } else {
        var facturas = new Array();
        $(checks).each(function(i,e){
          facturas.push({
            "id":$(e).val(),
            "id_punto_venta":$(e).data("id_punto_venta"),
          });
        });
        var url = "facturas/function/imprimir_lote/?facturas="+JSON.stringify(facturas);
        workspace.imprimir_reporte(url);        
      }
    },

    imprimir_agrupado: function() {
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var facturas = new Array();
      $(checks).each(function(i,e){
        facturas.push({
          "id":$(e).val(),
          "id_punto_venta":$(e).data("id_punto_venta"),
        });
      });
      var url = "facturas/function/imprimir_agrupado/?facturas="+JSON.stringify(facturas);
      workspace.imprimir_reporte(url);
    },    
  
    calcular_iva_lote: function() {
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var ids = new Array();
      $(checks).each(function(i,e){
        ids.push($(e).val()+"."+$(e).data("id_punto_venta"));
      });
      var ids_s = ids.join("-");

      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='facturas/function/mostrar_iva/"+ids_s+"'></iframe>";
      crearLightboxHTML({
        "html":iframe,
        "width":500,
        "height":200,
      });
    },

    sumar_lote: function() {
      var checks = this.$("#ventas_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var total = 0;
      $(checks).each(function(i,e){
        var t = parseFloat($(e).data("total"));
        if (isNaN(t)) t = 0;
        total += t;
      });
      alert("El total de los comprobantes seleccionados es: "+Number(total).toFixed(2)+" €");
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.VentasItemResultados = app.mixins.View.extend({
    template: _.template($("#ventas_item_resultados_template").html()),
    tagName: "tr",
    className: function() {
      return (this.model.get("nueva") == 1)?"no_leido":"";
    },
    myEvents: {
      "click .enviar_whatsapp": "enviar_whatsapp",
      "click .cambiar_metodo_pago":"ver_tarjeta",
      "click .ver_tarjeta":function(e) {
        e.stopPropagation();
        this.ver_tarjeta();
      },
      "click .convertir_factura":function(e) {
        e.stopPropagation();
        var self = this;
        this.convertir_factura(0); // Indica que no cambia la fecha
      },
      "click .modificar_codigo":function(e){
        var self = this;
        var v = new app.views.ModificarCodigoView({
          model: self.model,
        });
        // Abrimos el lightbox de pagos
        crearLightboxHTML({
          "html":v.el,
          "width":500,
          "height":500,
        });
      },
      "click .editar_pago":function(e){
        e.stopPropagation();
        e.preventDefault();
        var self = this;
        var id_cliente = this.model.get("id_cliente");
        if (this.model.get("pagada") == 1) return;
        var comprobantes = new Array();
        var negativo = self.model.get("negativo");
        comprobantes.push({
          "debe": (negativo == 0) ? self.model.get("total") : 0,
          "haber": (negativo == 1) ? self.model.get("total") : 0,
          "total_pagado": self.model.get("pago"),
          "id": self.model.id,
          "negativo": negativo,
          "fecha": self.model.get("fecha"),
          "numero": self.model.get("numero"),
          "tipo_comprobante": self.model.get("tipo_comprobante"),
        });
        var reciboCliente = new app.models.ReciboCliente({
          "cotizacion_dolar":COTIZACION_DOLAR,
          "id_empresa":ID_EMPRESA,
          "id_sucursal":ID_SUCURSAL,
          "id_cliente":id_cliente,
          "id_usuario":ID_USUARIO,
          "cheques": [],
          "depositos": [],
          "tarjetas": [],
          "movimientos_efectivo":[],
          "comprobantes": comprobantes,
        });
        var v = new app.views.ReciboClientes({
          model: reciboCliente
        });
        // Abrimos el lightbox de pagos
        crearLightboxHTML({
          "html":v.el,
          "width":900,
          "height":500,
          "escapable":false,
          "callback":function(){
            app.views.ventasTableView.buscar();
          }
        });
      },

      "click .enviar_email_descuento":"enviar_email_descuento",
      "click .data":"seleccionar_factura",
      "click .edit":"editar",
      "click .anular":"anular",
      "click .delete":"borrar",
      "click .imprimir":function() {
        var self = this;
        if (ID_EMPRESA == 574 || ID_EMPRESA == 1326) {
          var that = self;
          $.ajax({
            "url":"facturas/function/imprimir_epson/"+self.model.id+"/"+self.model.get("id_punto_venta"),
            "dataType":"json",
          });

        } else if (ID_PROYECTO == 10) {
          var modelo = new app.models.Factura({
          "id": self.model.id,
          });
          modelo.fetch({
          "success":function() {
            var data = "pedido="+JSON.stringify(modelo.toJSON());
            workspace.imprimir_comanda(data);
          }
          });
        } else {
          if (ID_PROYECTO == 2) {
            workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));  
          } else {
            if (FACTURACION_TIPO == "pv" && ID_EMPRESA != 1021) {
              workspace.imprimir_remito(this.model.id,this.model.get("id_punto_venta"));
            } else {
              workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"),"",function(){
                if (ID_EMPRESA == 229 || ID_EMPRESA == 230 || ID_EMPRESA == 1355) {
                  app.views.ventasTableView.buscar();
                }
              });  
            }
          }
          //if (this.model.get("id_tipo_comprobante") == 999) {
          //workspace.imprimir_remito(this.model.id,this.model.get("id_punto_venta"));
          //} else {
          //workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));
          //}          
        }
      },
      "click .imprimir_remito":function() {
        workspace.imprimir_remito(this.model.id,this.model.get("id_punto_venta"));
      },
      "click .imprimir_factura":function() {
        workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));
      },
      "click .imprimir_plano":function() {
        workspace.imprimir_reporte("facturas/function/imprimir_plano/"+this.model.id,this.model.get("id_punto_venta"));
      },

      "click .editar_custom_6":function(e) {
        var self = this;
        var valor = $(e.currentTarget).data("valor");
        var id = self.model.id;
        var id_punto_venta = self.model.get("id_punto_venta");
        $.ajax({
          "url":"facturas/function/change_property/",
          "type":"post",
          "data":{
            "id":id,
            "id_punto_venta":id_punto_venta,
            "attribute":"custom_6",
            "value":valor,
          },
          "dataType":"json",
          "success":function(r) {
            app.views.ventasTableView.buscar();
          }
        });
      },

      "click .editar_fecha":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var cont = $(e.currentTarget).find(".inline-text-cont");
        var text = $(e.currentTarget).find(".inline-text");
        var fecha_anterior = parseFloat($(text).val());
        $(cont).hide();
        $(text).one("focusout",function(){
          var fecha = $(text).val();
          var a = moment(fecha,"DD/MM/YYYY",true);
          if (!a.isValid()) {
            alert("Por favor ingrese una fecha correcta");
            $(text).empty();
            $(text).hide();
            $(cont).show();
            return;
          }
          if (fecha_anterior == fecha) {
            $(text).hide();
            $(cont).show();
            return;            
          }
          $.ajax({
            "url":"facturas/function/editar_fecha/",
            "dataType":"json",
            "type":"post",
            "data":{
              "id":self.model.id,
              "id_punto_venta":self.model.get("id_punto_venta"),
              "id_empresa":ID_EMPRESA,
              "fecha":fecha
            },
            "success":function(r){
              app.views.ventasTableView.buscar();
            },
            "error":function(){
              $(cont).show();
              $(text).hide();              
            }
          });
        });
        $(text).show();
        $(text).select();        
      },

      "click .editar_tipo_pago":function(e) {
        var self = this;
        var valor = $(e.currentTarget).data("valor");
        var id = self.model.id;
        var id_punto_venta = self.model.get("id_punto_venta");
        $.ajax({
          "url":"facturas/function/change_property/",
          "type":"post",
          "data":{
            "id":id,
            "id_punto_venta":id_punto_venta,
            "attribute":"tipo_pago",
            "value":valor,
          },
          "dataType":"json",
          "success":function(r) {
            app.views.ventasTableView.buscar();
          }
        });
      },

      "click .verificar_comprobante":function() {
        var self = this;
        $.ajax({
          "url":"facturas/function/verificar/"+self.model.id,
          "dataType":"json",
          "success":function(r) {
          if (r.error == 0) location.reload();
          alert(r.mensaje);
          }
        });
      },
      "keydown .radio":function(e) {
        if (e.which == 13) { this.seleccionar_factura(); }
      },
    },
    ver_tarjeta: function() {
      var self = this;
      $.ajax({
        "type":"post",
        "dataType":"json",
        "url":"tarjetas/function/ver_cupon/"+self.model.id+"/"+self.model.get("id_punto_venta"),
        "success":function(r) {
          var p = new app.views.CuponTarjetaView({
            model: new app.models.AbstractModel({
              "tarjetas":r,
              "id":self.model.id,
              "id_punto_venta":self.model.get("id_punto_venta"),
              "id_caja_diaria":self.model.get("id_caja_diaria"),
              "fecha":self.model.get("fecha"),
              "efectivo":self.model.get("efectivo"),
              "vuelto":self.model.get("vuelto"),
              // TODO: Hacer esto dinamico
              "modificar_forma_pago":((ID_EMPRESA == 1021 || VOLVER_SUPERADMIN == 1)?1:0),
            })
          });
          crearLightboxHTML({
            "html":p.el,
            "width":450,
            "height":140,
            "callback":function(){
              app.views.ventasTableView.buscar();
            },
          });
        }
      })
    },
    enviar_whatsapp: function() {
      var tel = this.model.get("telefono");
      tel = tel.replace(/[^\d.-]/g, '');
      tel = tel.replace(/\-/g, "");
      var link_ws = "https://wa.me/"+tel;
      window.open(link_ws,"_blank");
    },
    seleccionar_factura : function() {
      if (this.options.seleccionar) {
        console.log(this.model);
        window.factura_seleccionada = this.model;
        $('.modal:last').modal('hide');
        //this.parent.importar(this.model);
      }
    },

    convertir_factura: function(cambiar_fecha) {
      var self = this;      
      $.ajax({
        "url":"facturas/function/convertir_factura/",
        "type":"post",
        "data":{
          "id_punto_venta":self.model.get("id_punto_venta"),
          "id_factura":self.model.id,
          "cambiar_fecha":cambiar_fecha,
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) {
            self.$(".comprobante").text(r.comprobante);
            self.$(".numero").text(r.numero);
            self.$(".convertir_factura").hide();
            self.$(".btn-group").removeClass("open");
          } else {
            // TODO: En caso de que el error sea por la fecha del comprobante,
            // deberia llamar al mismo metodo pero con parametro 1
            if (r.mensaje == "El numero o fecha del comprobante no se corresponde con el proximo a autorizar. Consultar metodo FECompUltimoAutorizado.") {
              if (!confirm("La fecha de comprobante no se corresponde con la proxima para autorizar. Desea cambiar la fecha del comprobante?")) return;
              self.convertir_factura(1);
            } else {
              alert(r.mensaje);  
            }
          }
        }
      });
    },

    editar : function() {
      if (!this.options.seleccionar && control.check("ventas_listado") == 3) {
        if ( (MEGASHOP == 1 || ID_EMPRESA == 421) && control.check("facturacion")>1) {
          location.href="app/#comprobante/"+this.model.id+"/"+this.model.get("id_punto_venta");
        } else if (MEGASHOP != 1 && ID_EMPRESA != 421) {
          location.href="app/#comprobante/"+this.model.id+"/"+this.model.get("id_punto_venta");
        }
        /*else {
          if (this.model.get("id_tipo_comprobante") == 999) {
          location.href="app/#remitos/"+this.model.id;  
          }
        }*/
      }
    },
    anular: function() {
      var self = this;
      if (confirmar("Realmente desea anular este comprobante?")) {
        // Se debe ANULAR, NO BORRAR
        $.ajax({
          "url":"facturas/function/anular/"+self.model.id+"/"+self.model.get("id_punto_venta"),
          "dataType":"json",
          "success":function(r){
            app.views.ventasTableView.buscar();
          }
        });                      
      }
    },
    enviar_email_descuento: function() {
      var self = this;
      var email_template = false;
      for(var i=0;i< window.emails_templates.length;i++) {
        var o = window.emails_templates[i];
        if (o.clave == "compra-descuento") {
          // Clonamos el objeto porque si lo asignabamos directamente, a la segunda vez que mandabamos el descuento
          // no se cambiaba el nombre ya que el placeholder habia sido reemplazado la primera vez
          email_template = Object.assign({}, o);
          break;
        }
      }
      if (email_template == false) {
        alert("No esta configurada la plantilla de email para enviar el descuento.");
        return;
      }

      var porc_descuento = prompt("Ingrese el descuento que desea enviarle por email ")
      porc_descuento = parseFloat(porc_descuento);
      if (isNaN(porc_descuento)) {
        alert("Por favor ingrese un numero");
        return;
      }

      $.ajax({
        "url":"ventas/function/enviar_email_descuento/",
        "dataType":"json",
        "type":"post",
        "data": {
          "id_cliente":self.model.get("id_cliente"),
          "id_factura":self.model.id,
          "id_punto_venta":self.model.get("id_punto_venta"),
          "porc_descuento":porc_descuento,
        },
        "success":function(r) {

          // Reemplazamos los valores del template
          email_template.texto = email_template.texto.replaceAll("{{porc_descuento}}",porc_descuento);
          email_template.texto = email_template.texto.replaceAll("{{cliente}}",r.nombre);
          email_template.texto = email_template.texto.replaceAll("{{link_web}}","http://"+DOMINIO);

          var email = new app.models.Consulta({
            "email":r.email,
            "asunto":email_template.nombre,
            "id_usuario":ID_USUARIO,
            "id_contacto":r.id_cliente,
            "texto":email_template.texto,
            "tipo":1, // Estamos enviando la consulta
          });
          workspace.nuevo_email(email);          
        }
      })
    },
    borrar : function() {
      if (confirmar("Realmente desea eliminar este comprobante?")) {
      var self = this;

        // Si es un pago
        if (this.model.get("tipo") == "P") {
          var url = "recibos/function/borrar_recibo/"+this.model.id;
          $.ajax({
            "url":url,
            "dataType": "json",
            "success": function() {
              show("El comprobante ha sido eliminado exitosamente.");
              app.views.ventasTableView.buscar();
            },
            "error" : function() {
              show("Error al eliminar el comprobante.");
            }
          });
          
        // Si es un REMITO
        } else if (this.model.get("estado") == 1) {
          // Se elimina directamente
          $.ajax({
            "url":"facturas/function/borrar_factura/"+self.model.id+"/"+self.model.get("id_punto_venta"),
            "dataType":"json",
            "success":function(r){
              app.views.ventasTableView.buscar();
            }
          });

        // Sino, es una FA, FB, NC, ND
        } else {

          $.ajax({
            "url":"facturas/function/borrar_factura/"+self.model.id+"/"+self.model.get("id_punto_venta"),
            "dataType":"json",
            "success":function(r){
              app.views.ventasTableView.buscar();
            }
          });
          
        }
      }
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);



(function ( app ) {

  app.views.PercepcionGananciasView = app.mixins.View.extend({

    template: _.template($("#percep_ganancias_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
      var desde = moment().startOf("month").format("DD/MM/YYYY");
      var hasta = moment().endOf("month").format("DD/MM/YYYY");
      createdatepicker(this.$("#percep_ganancias_desde"),desde);
      createdatepicker(this.$("#percep_ganancias_hasta"),hasta);
    },
    
    exportar : function() {
      var fecha_desde = this.$("#percep_ganancias_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#percep_ganancias_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#percep_ganancias_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#percep_ganancias_hasta").focus();
        return;
      }
      fecha_desde = fecha_desde.replace(/\//g,"-");
      fecha_hasta = fecha_hasta.replace(/\//g,"-");
      var url = "/sistema/ventas/function/exportar_percepcion_ganancias/"+fecha_desde+"/"+fecha_hasta+"/";
      window.open(url,"_blank");
    },
  });

})(app);


(function( app ) {

  app.views.PercepcionIIBBView = app.mixins.View.extend({

    template: _.template($("#percep_iibb_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
      var desde = moment().startOf("month").format("DD/MM/YYYY");
      var hasta = moment().endOf("month").format("DD/MM/YYYY");
      createdatepicker(this.$("#percep_iibb_desde"),desde);
      createdatepicker(this.$("#percep_iibb_hasta"),hasta);
    },
    
    exportar : function() {
      var fecha_desde = this.$("#percep_iibb_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#percep_iibb_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#percep_iibb_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#percep_iibb_hasta").focus();
        return;
      }
      fecha_desde = fecha_desde.replace(/\//g,"-");
      fecha_hasta = fecha_hasta.replace(/\//g,"-");

      var descarga = this.$("#percep_iibb_descarga").val();

      var url = "/sistema/ventas/function/percepciones_iibb/"+fecha_desde+"/"+fecha_hasta+"/"+descarga+"/";
      window.open(url,"_blank");
    },
  });

})(app);


(function ( views, models ) {

  views.CuponTarjetaView = app.mixins.View.extend({

  template: _.template($("#cupon_tarjeta_panel_template").html()),
    myEvents:{
      "click .mover_tarjeta1_efectivo":function(){
        // Sumamos el importe de la tarjeta al efectivo
        var efectivo = parseFloat(this.$("#cupon_tarjeta_efectivo").val());
        var importe = parseFloat(this.$("#tarjeta1_importe").val());
        if (isNaN(importe)) importe = 0;
        efectivo += importe;
        this.$("#cupon_tarjeta_efectivo").val(efectivo);

        // Limpiamos los campos
        this.$("#tarjeta1_tarjeta").val(0);
        this.$("#tarjeta1_cuotas").val(0);
        this.$("#tarjeta1_cupon").val(0);
        this.$("#tarjeta1_lote").val(0);
        this.$("#tarjeta1_importe").val(0);
        this.$("#tarjeta1_interes").val(0);
        this.$("#tarjeta1_total").val(0);

        // Mostramos el tab de efectivo
        this.$("#ver_efectivo").trigger("click");
      },
      "click .mover_tarjeta2_efectivo":function(){
        // Sumamos el importe de la tarjeta al efectivo
        var efectivo = parseFloat(this.$("#cupon_tarjeta_efectivo").val());
        var importe = parseFloat(this.$("#tarjeta2_importe").val());
        if (isNaN(importe)) importe = 0;
        efectivo += importe;
        this.$("#cupon_tarjeta_efectivo").val(efectivo);

        // Limpiamos los campos
        this.$("#tarjeta2_tarjeta").val(0);
        this.$("#tarjeta2_cuotas").val(0);
        this.$("#tarjeta2_cupon").val(0);
        this.$("#tarjeta2_lote").val(0);
        this.$("#tarjeta2_importe").val(0);
        this.$("#tarjeta2_interes").val(0);
        this.$("#tarjeta2_total").val(0);

        // Mostramos el tab de efectivo
        this.$("#ver_efectivo").trigger("click");
      },      
      "click .guardar":function(){
        var self = this;

        // Controlamos que este bien cargado
        var efectivo = parseFloat(this.$("#cupon_tarjeta_efectivo").val());
        if (isNaN(efectivo)) {
          this.$("#ver_efectivo").trigger("click");
          this.$("#cupon_tarjeta_efectivo").select();
          alert("Por favor ingrese un valor correcto.");
          return;
        }

        var vuelto = parseFloat(this.$("#cupor_tarjeta_vuelto").val());
        if (isNaN(vuelto)) {
          this.$("#ver_efectivo").trigger("click");
          this.$("#cupor_tarjeta_vuelto").select();
          alert("Por favor ingrese un valor correcto.");
          return;
        }

        var obj = {
          "id":self.model.id,
          "id_caja_diaria":self.model.get("id_caja_diaria"),
          "fecha":self.model.get("fecha"),
          "id_punto_venta":self.model.get("id_punto_venta"),
          "efectivo":self.$("#cupon_tarjeta_efectivo").val(),
          "vuelto":self.$("#cupor_tarjeta_vuelto").val(),
          "tarjetas":[],
        };
        if (this.$("#tarjeta1").length > 0) {
          obj.tarjetas.push({
            "id":self.$("#tarjeta1_id").val(),
            "id_factura":self.model.id,
            "id_punto_venta":self.model.get("id_punto_venta"),
            "id_tarjeta":self.$("#tarjeta1_tarjeta").val(),
            "cuotas":self.$("#tarjeta1_cuotas").val(),
            "cupon":self.$("#tarjeta1_cupon").val(),
            "lote":self.$("#tarjeta1_lote").val(),
            "importe":self.$("#tarjeta1_importe").val(),
            "interes":self.$("#tarjeta1_interes").val(),
            "total":self.$("#tarjeta1_total").val(),            
          });
        }
        if (this.$("#tarjeta2").length > 0) {
          obj.tarjetas.push({
            "id":self.$("#tarjeta1_id").val(),
            "id_factura":self.model.id,
            "id_punto_venta":self.model.get("id_punto_venta"),
            "id_tarjeta":self.$("#tarjeta2_tarjeta").val(),
            "cuotas":self.$("#tarjeta2_cuotas").val(),
            "cupon":self.$("#tarjeta2_cupon").val(),
            "lote":self.$("#tarjeta2_lote").val(),
            "importe":self.$("#tarjeta2_importe").val(),
            "interes":self.$("#tarjeta2_interes").val(),
            "total":self.$("#tarjeta2_total").val(),            
          });
        }
        $.ajax({
          "timeout":0,
          "url":"tarjetas/function/editar_forma_pago/",
          "dataType":"json",
          "type":"post",
          "data":{
            "datos":JSON.stringify(obj),
          },
          "success":function(r) {
            if (r.error == 1) alert(r.mensaje);
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al editar las ventas.");
            $('.modal:last').modal('hide');
          },
        });
      },
      "click #ver_efectivo":function() {
        this.$("#ver_tarjeta_1").removeClass("active");
        this.$("#tarjeta1").removeClass("active");
        this.$("#ver_tarjeta_2").removeClass("active");
        this.$("#tarjeta2").removeClass("active");
        this.$("#ver_efectivo").addClass("active");
        this.$("#efectivo").addClass("active");
      },
      "click #ver_tarjeta_1":function(){
        this.$("#ver_tarjeta_1").removeClass("active");
        this.$("#tarjeta1").removeClass("active");
        this.$("#ver_efectivo").removeClass("active");
        this.$("#efectivo").removeClass("active");
        this.$("#ver_tarjeta_2").addClass("active");
        this.$("#tarjeta2").addClass("active");
      },
      "click #ver_tarjeta_2":function(){
        this.$("#ver_tarjeta_2").removeClass("active");
        this.$("#tarjeta2").removeClass("active");
        this.$("#ver_efectivo").removeClass("active");
        this.$("#efectivo").removeClass("active");
        this.$("#ver_tarjeta_1").addClass("active");
        this.$("#tarjeta1").addClass("active");
      },
    },
    initialize: function(options) {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
  
})(app.views, app.models);



(function ( app ) {
  app.views.EditarVendedorView = app.mixins.View.extend({
    template: _.template($("#editar_vendedor_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_vendedor = self.$("#editar_vendedor_vendedores").val();
        if (id_vendedor == 0) {
          alert("Por favor seleccione un vendedor.");
          self.$("#editar_vendedor_vendedores").focus();
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"ventas/function/editar_vendedor/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ventas":self.ventas_marcadas,
            "id_vendedor":id_vendedor,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al editar las ventas.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.id_vendedor = 0;
      this.ventas_marcadas = options.ventas_marcadas;
      console.log(this.ventas_marcadas);
      $(this.el).html(this.template());
      this.cargar_vendedores();
    },
    cargar_vendedores: function() {
      new app.mixins.Select({
        modelClass: app.models.Vendedor,
        url: "vendedores/",
        render: "#editar_vendedor_vendedores",
        name : "id_vendedor",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.id_vendedor,
      });
    }
  });
})(app);



(function ( app ) {
  app.views.EditarFechaRepartoView = app.mixins.View.extend({
    template: _.template($("#editar_fecha_reparto_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var fecha_reparto = self.$("#editar_fecha_reparto_fecha_reparto").val();
        if (isEmpty(fecha_reparto)) {
          alert("Por favor seleccione una fecha.");
          self.$("#editar_fecha_reparto_fecha_reparto").focus();
          return;
        }
        var numero_reparto = self.$("#editar_fecha_reparto_numero_reparto").val();
        if (isEmpty(numero_reparto)) {
          alert("Por favor ingrese un numero de reparto.");
          self.$("#editar_fecha_reparto_numero_reparto").focus();
          return;
        }
        fecha_reparto = fecha_reparto.replace(/\//g,"-");
        $.ajax({
          "timeout":0,
          "url":"ventas/function/editar_fecha_reparto/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ventas":self.ventas_marcadas,
            "fecha_reparto":fecha_reparto,
            "reparto":numero_reparto,
          },
          "success":function() {
            $('.modal:last').modal('hide');
          },
          "error":function() {
            alert("Ocurrio un error al editar las ventas.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.id_vendedor = 0;
      this.ventas_marcadas = options.ventas_marcadas;
      $(this.el).html(this.template());

      createdatepicker(this.$("#editar_fecha_reparto_fecha_reparto"),new Date());

      this.$("#editar_fecha_reparto_numero_reparto").TouchSpin({
        verticalbuttons: true,
        min: 0,
      });
    },
  });
})(app);


(function ( app ) {

  app.views.IvaVentasView = app.mixins.View.extend({

    template: _.template($("#iva_ventas_template").html()),
      
    myEvents: {
      "click .imprimir":"imprimir",
      "click .citi":"citi",
      "click .citi_comprobantes":"citi_comprobantes",
      "click .citi_alicuotas":"citi_alicuotas",
      "click .iva_excel":"iva_excel",
      "click #iva_ventas_por_concepto": "ventas_por_concepto",
      "click #ventas_por_concepto": "imprimir_ventas_por_concepto",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
      var desde = moment().subtract(1,"month").startOf("month").format("DD/MM/YYYY");
      var hasta = moment().subtract(1,"month").endOf("month").format("DD/MM/YYYY");
      createdatepicker(this.$("#iva_ventas_fecha_desde"),desde);
      createdatepicker(this.$("#iva_ventas_fecha_hasta"),hasta);
    },
    imprimir : function() {
      var fecha_desde = this.$("#iva_ventas_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#iva_ventas_fecha_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#iva_ventas_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#iva_ventas_fecha_hasta").focus();
        return;
      }
      fecha_desde = fecha_desde.replace(/\//g,"-");
      fecha_hasta = fecha_hasta.replace(/\//g,"-");
      var desde = $(this.el).find("#iva_ventas_desde").val();
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      workspace.imprimir_reporte("iva/function/ventas/"+fecha_desde+"/"+fecha_hasta+"/"+desde+"/"+id_razon_social+"/");
    },
    citi: function() {
      var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/cbte/"+id_razon_social+"/","_blank");
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/alicuotas/"+id_razon_social+"/","_blank");
    },   
    citi_comprobantes:function() {
      var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/cbte/"+id_razon_social+"/","_blank");
    },    
    citi_alicuotas:function() {
      var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      window.open("ventas/function/regimen_informacion/"+fecha_desde+"/"+fecha_hasta+"/alicuotas/"+id_razon_social+"/","_blank");
    },    
    iva_excel: function() {
      var fecha_desde = this.$("#iva_ventas_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#iva_ventas_fecha_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#iva_ventas_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#iva_ventas_fecha_hasta").focus();
        return;
      }
      fecha_desde = fecha_desde.replace(/\//g,"-");
      fecha_hasta = fecha_hasta.replace(/\//g,"-");
      var desde = $(this.el).find("#iva_ventas_desde").val();
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      window.open("iva/function/ventas/"+fecha_desde+"/"+fecha_hasta+"/"+desde+"/"+id_razon_social+"/?excel=1","_blank");
    },        
    ventas_por_concepto: function() {
      var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      workspace.imprimir_reporte("ventas/function/por_concepto/?desde="+fecha_desde+"&hasta="+fecha_hasta+"/");
    },
    imprimir_ventas_por_concepto: function() {
      var fecha_desde = $(this.el).find("#iva_ventas_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#iva_ventas_fecha_hasta").val().replace(/\//g,"-");
      var id_razon_social = ((this.$("#iva_ventas_razones_sociales").length > 0) ? this.$("#iva_ventas_razones_sociales").val() : 0);
      workspace.imprimir_reporte("ventas/function/imprimir_resumen_arbol_ventas/?id_razon_social="+id_razon_social+"&desde="+fecha_desde+"&hasta="+fecha_hasta+"/");      
    },
  });

})(app);


(function ( app ) {
  app.views.ModificarCodigoView = app.mixins.View.extend({
    template: _.template($("#modificar_codigo_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var codigo_activacion = self.$("#modificar_codigo_activacion").val();
        if (codigo_activacion == "") {
          alert ("Por favor inserte un codigo");
          return;
        }
        $.ajax({
          "timeout":0,
          "url":"ventas/function/modificar_codigo_activacion/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id_factura":self.model.id,
            "codigo":codigo_activacion,
          },
          "success":function() {
            $('.modal:last').modal('hide');
            location.reload();
          },
          "error":function() {
            alert("Ocurrio un error al editar el codigo.");
            $('.modal:last').modal('hide');
          },
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.id_vendedor = 0;
      $(this.el).html(this.template());
    },
  });
})(app);