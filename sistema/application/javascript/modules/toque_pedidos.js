// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ToquePedido = Backbone.Model.extend({
    urlRoot: "toque/",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha: "",
      hora: "",
      sucursal: "",
      caja: "",
      id_usuario: ID_USUARIO,
      usuario: "",
      id_cliente: 0,
      cliente: "",
      direccion: "",
      email: "",
      telefono: "",
      documento: "",
      id_tipo_estado: 0,
      observaciones: "",
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
      en_comercio: "",
      retirado: "",
      entregado: "",
    }
  });
    
})( app.models );

(function ( models ) {

  models.ToquePedidoItem = Backbone.Model.extend({
    urlRoot: "facturas_items",
    defaults: {
      id_articulo: 0,
      tipo_cantidad: "",
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      id_tipo_estado: 0,
      neto: 0,    // Unitario
      precio: 0,  // Unitario
      nombre: "",
      descripcion: "",
      orden: 0,
      id_rubro: 0,
      iva: 0,
      total_sin_iva: 0, // Totales (unitario * cantidad)
      total_con_iva: 0,
      porc_bonif: 0,
    }
  });

})( app.models );

// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.ToquePedidos = paginator.requestPager.extend({

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

})( app.collections, app.models.ToquePedido, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ToquePedidosTableView = app.mixins.View.extend({

    template: _.template($("#toque_pedidos_resultados_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
      "click .exportar_comercios":"exportar_comercios",
      "click .ordenar_retrasos":"ordenar_retrasos",
      "click .ordenar_por_listo":"ordenar_por_listo",
      "click .editar_repartidor":"editar_repartidor",
      "click .editar_estado":"editar_estado",
      "change #toque_pedidos_listado_buscar":"buscar",
      "change #toque_pedidos_almacenes":"buscar",
      "change #toque_pedidos_forma_pago":"buscar",
      "click .buscar":"buscar",
      "click .sumar_lote":"sumar_lote",
      "click .nuevo":"nuevo",
      "change #toque_pedidos_usuarios":"buscar",
      "change #toque_pedidos_vendedores":"buscar",

      // Para configurar las columnas de la tabla
      "click .configurar_tabla":function() {
        var p = new app.views.ConfiguracionTablaView({
          titulo: "ToquePedidos",
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
        window.toque_pedidos_listado_in_tipos_estados = $(e.currentTarget).data("tipo");
        window.toque_pedidos_listado_con_anulados = $(e.currentTarget).data("anulados");
        $(e.currentTarget).parents(".nav-tabs").find(".active").removeClass("active");
        $(e.currentTarget).parent().addClass("active");
        this.buscar();
      },
      "keydown #toque_pedidos_listado_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#toque_pedidos_tabla tbody tr .radio:first").focus(); }
      },
      "keypress #toque_pedidos_monto":function(e) {
        if (e.which == 13) this.buscar();
      }
    },

    nuevo: function() {
      // Mostramos para tomar el pedido
      var modelo = new app.models.ToquePedido();
      var view = new app.views.ToquePedidoEditView({
        "model":modelo
      });
      crearLightboxHTML({
        "html":view.el,
        "width":600,
        "height":140,
        "escapable":false,
      });
      $("#toque_pedido_cliente").select();
    },   

    editar_repartidor: function() {
      var self = this;
      var checks = this.$("#toque_pedidos_tabla tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var ventas_marcadas = new Array();
      var encontro_pickup = false;
      $(checks).each(function(i,e){

        var numero_envio = $(e).data("numero_envio"); 
        if (numero_envio == "pickup") encontro_pickup = true;

        ventas_marcadas.push({
          "id":$(e).val(),
          "id_punto_venta":$(e).data("id_punto_venta"),
          "efectivo":$(e).data("efectivo"),
        });
      });
      if (encontro_pickup) {
        alert("No se puede asignar un repartidor a un pickup.");
        return;
      }
      var view = new app.views.EditarRepartidorView({
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

    editar_estado: function() {
      var self = this;
      var checks = this.$("#toque_pedidos_tabla tbody .i-checks input[type=checkbox]:checked");
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
      var view = new app.views.EditarEstadoView({
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


    exportar : function() {
      
      var fecha_desde = this.$("#toque_pedidos_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#toque_pedidos_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#toque_pedidos_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#toque_pedidos_hasta").focus();
        return;
      }
      var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
      var url = "/sistema/"+url_base+"/function/exportar_excel/?";
      
      if (this.$("#toque_pedidos_listado_buscar").length > 0 && !isEmpty(this.$("#toque_pedidos_listado_buscar").val()))
        url+="filter="+this.$("#toque_pedidos_listado_buscar").val()+"&";
      
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) url+="desde="+fecha_desde+"&";
      
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) url+="hasta="+fecha_hasta+"&";

      if (PERFIL == 661) url+="id_usuario="+ID_USUARIO+"&";
      url += "con_anulados=3&";
      url += "in_tipos_estados="+window.toque_pedidos_listado_in_tipos_estados+"&";
      url += "id_punto_venta="+this.$("#toque_pedidos_puntos_venta").val();
      
      window.open(url,"_blank")
    },  

    exportar_comercios: function() {
      
      var fecha_desde = this.$("#toque_pedidos_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#toque_pedidos_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#toque_pedidos_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#toque_pedidos_hasta").focus();
        return;
      }
      var url = "/sistema/toque/function/exportar_excel_comercio/?";
      
      if (this.$("#toque_pedidos_listado_buscar").length > 0 && !isEmpty(this.$("#toque_pedidos_listado_buscar").val()))
        url+="filter="+this.$("#toque_pedidos_listado_buscar").val()+"&";
      
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) url+="desde="+fecha_desde+"&";
      
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) url+="hasta="+fecha_hasta+"&";

      if (PERFIL == 661) url+="id_usuario="+ID_USUARIO+"&";
      url += "con_anulados=3&";
      url += "in_tipos_estados="+window.toque_pedidos_listado_in_tipos_estados+"&";
      url += "id_punto_venta="+this.$("#toque_pedidos_puntos_venta").val();
      
      window.open(url,"_blank")
    },        
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.tipos_comprobante = (this.options.tipos_comprobante == undefined) ? "" : this.options.tipos_comprobante;
      this.tipo = (this.options.tipo == undefined) ? "" : this.options.tipo;
      this.fecha = (this.options.fecha == undefined) ? "" : (this.options.fecha).replace(/\//g,"-");
      this.permiso = this.options.permiso;      

      window.toque_pedidos_listado_fecha_desde = (typeof window.toque_pedidos_listado_fecha_desde != "undefined") ? window.toque_pedidos_listado_fecha_desde : this.fecha;
      window.toque_pedidos_listado_fecha_hasta = (typeof window.toque_pedidos_listado_fecha_hasta != "undefined") ? window.toque_pedidos_listado_fecha_hasta : this.fecha;
      window.toque_pedidos_listado_filter = (typeof window.toque_pedidos_listado_filter != "undefined") ? window.toque_pedidos_listado_filter : "";
      window.toque_pedidos_listado_fecha_reparto = (typeof window.toque_pedidos_listado_fecha_reparto != "undefined") ? window.toque_pedidos_listado_fecha_reparto : "";
      window.toque_pedidos_listado_numero_reparto = (typeof window.toque_pedidos_listado_numero_reparto != "undefined") ? window.toque_pedidos_listado_numero_reparto : "";
      window.toque_pedidos_listado_punto_venta = (typeof window.toque_pedidos_listado_punto_venta != "undefined") ? window.toque_pedidos_listado_punto_venta : -1;
      window.toque_pedidos_listado_vendedor = (typeof window.toque_pedidos_listado_vendedor != "undefined") ? window.toque_pedidos_listado_vendedor : 0;
      window.toque_pedidos_listado_sucursal = (typeof window.toque_pedidos_listado_sucursal != "undefined") ? window.toque_pedidos_listado_sucursal : 0;
      window.toque_pedidos_listado_tarjeta = (typeof window.toque_pedidos_listado_tarjeta != "undefined") ? window.toque_pedidos_listado_tarjeta : 0;
      window.toque_pedidos_listado_lote = (typeof window.toque_pedidos_listado_lote != "undefined") ? window.toque_pedidos_listado_lote : "";
      window.toque_pedidos_listado_cupon = (typeof window.toque_pedidos_listado_cupon != "undefined") ? window.toque_pedidos_listado_cupon : "";
      window.toque_pedidos_listado_con_anulados = (typeof window.toque_pedidos_listado_con_anulados != "undefined") ? window.toque_pedidos_listado_con_anulados : 3;
      window.toque_pedidos_listado_monto = (typeof window.toque_pedidos_listado_monto != "undefined") ? window.toque_pedidos_listado_monto : "";
      window.toque_pedidos_listado_custom_10 = (typeof window.toque_pedidos_listado_custom_10 != "undefined") ? window.toque_pedidos_listado_custom_10 : "";
      window.toque_pedidos_listado_monto_tipo = (typeof window.toque_pedidos_listado_monto_tipo != "undefined") ? window.toque_pedidos_listado_monto_tipo : "";
      window.toque_pedidos_listado_tipo_cliente = (typeof window.toque_pedidos_listado_tipo_cliente != "undefined") ? window.toque_pedidos_listado_tipo_cliente : "";
      window.toque_pedidos_listado_forma_pago = (typeof window.toque_pedidos_listado_forma_pago != "undefined") ? window.toque_pedidos_listado_forma_pago : 0;
      window.toque_pedidos_listado_page = (typeof window.toque_pedidos_listado_page != "undefined") ? window.toque_pedidos_listado_page : 1;
      window.toque_pedidos_listado_in_tipos_estados = (typeof window.toque_pedidos_listado_in_tipos_estados != "undefined") ? window.toque_pedidos_listado_in_tipos_estados : "0-1-2-3-4-5-6";
      window.toque_pedidos_listado_order_by = (typeof window.toque_pedidos_listado_order_by != "undefined") ? window.toque_pedidos_listado_order_by : "";      
      window.toque_pedidos_listado_id_usuario = (typeof window.toque_pedidos_listado_id_usuario != "undefined") ? window.toque_pedidos_listado_id_usuario : 0;
      
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
      
      createdatepicker(this.$("#toque_pedidos_desde"),window.toque_pedidos_listado_fecha_desde);
      createdatepicker(this.$("#toque_pedidos_hasta"),window.toque_pedidos_listado_fecha_hasta);
      if (this.$("#toque_pedidos_fecha_reparto").length > 0) createdatepicker(this.$("#toque_pedidos_fecha_reparto"),window.toque_pedidos_listado_fecha_reparto);

      new app.mixins.Select({
        modelClass: app.models.Repartidor,
        url: "repartidores/function/buscar/?activo=-1&limit=0&offset=99999",
        firstOptions: ["<option value='0'>Repartidores</option><option value='-1'>Sin Repartidor Asignado</option>"],
        render: "#toque_pedidos_vendedores",
      });      
      
      this.buscar();

      // Cada 60 segundos volvemos a buscar
      setInterval(function(){
        self.buscar();
      },60000);
    },

    ordenar_retrasos: function() {
      window.toque_pedidos_listado_order_by = "mas_retrasados";
      this.buscar();
    },

    ordenar_por_listo: function() {
      window.toque_pedidos_listado_order_by = "pedidos_listos";
      this.buscar();
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      var filtros = {};

      if (!isEmpty(this.$("#toque_pedidos_listado_cliente").val())) 
        filtros.id_cliente = this.$("#toque_pedidos_listado_cliente").val();
      if (!isEmpty(this.$("#toque_pedidos_listado_numero").val())) 
        filtros.numero = this.$("#toque_pedidos_listado_numero").val();        
      
      if (this.$("#toque_pedidos_custom_10").length > 0) {
        if (window.toque_pedidos_listado_custom_10 != this.$("#toque_pedidos_custom_10").val().trim()) {
          window.toque_pedidos_listado_custom_10 = this.$("#toque_pedidos_custom_10").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_monto").length > 0) {
        if (window.toque_pedidos_listado_monto != this.$("#toque_pedidos_monto").val().trim()) {
          window.toque_pedidos_listado_monto = this.$("#toque_pedidos_monto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_fecha_reparto").length > 0) {
        if (window.toque_pedidos_listado_fecha_reparto != this.$("#toque_pedidos_fecha_reparto").val().trim()) {
          window.toque_pedidos_listado_fecha_reparto = this.$("#toque_pedidos_fecha_reparto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_numero_reparto").length > 0) {
        if (window.toque_pedidos_listado_numero_reparto != this.$("#toque_pedidos_numero_reparto").val().trim()) {
          window.toque_pedidos_listado_numero_reparto = this.$("#toque_pedidos_numero_reparto").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_forma_pago").length > 0) {
        if (window.toque_pedidos_listado_forma_pago != this.$("#toque_pedidos_forma_pago").val().trim()) {
          window.toque_pedidos_listado_forma_pago = this.$("#toque_pedidos_forma_pago").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_monto_tipo").length > 0) {
        if (window.toque_pedidos_listado_monto_tipo != this.$("#toque_pedidos_monto_tipo").val().trim()) {
          window.toque_pedidos_listado_monto_tipo = this.$("#toque_pedidos_monto_tipo").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_tipo_cliente").length > 0) {
        if (window.toque_pedidos_listado_tipo_cliente != this.$("#toque_pedidos_tipo_cliente").val().trim()) {
          window.toque_pedidos_listado_tipo_cliente = this.$("#toque_pedidos_tipo_cliente").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_tarjeta").length > 0) {
        if (window.toque_pedidos_listado_tarjeta != this.$("#toque_pedidos_tarjeta").val()) {
          window.toque_pedidos_listado_tarjeta = this.$("#toque_pedidos_tarjeta").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_lote").length > 0) {
        if (window.toque_pedidos_listado_lote != this.$("#toque_pedidos_lote").val().trim()) {
          window.toque_pedidos_listado_lote = this.$("#toque_pedidos_lote").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_cupon").length > 0) {
        if (window.toque_pedidos_listado_cupon != this.$("#toque_pedidos_cupon").val().trim()) {
          window.toque_pedidos_listado_cupon = this.$("#toque_pedidos_cupon").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_listado_buscar").length > 0 && window.toque_pedidos_listado_filter != this.$("#toque_pedidos_listado_buscar").val().trim()) {
        window.toque_pedidos_listado_filter = this.$("#toque_pedidos_listado_buscar").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#toque_pedidos_desde").length > 0 && window.toque_pedidos_listado_fecha_desde != this.$("#toque_pedidos_desde").val().trim()) {
        window.toque_pedidos_listado_fecha_desde = this.$("#toque_pedidos_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#toque_pedidos_hasta").length > 0 && window.toque_pedidos_listado_fecha_hasta != this.$("#toque_pedidos_hasta").val().trim()) {
        window.toque_pedidos_listado_fecha_hasta = this.$("#toque_pedidos_hasta").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#toque_pedidos_puntos_venta").length > 0) {
        if (window.toque_pedidos_listado_punto_venta != this.$("#toque_pedidos_puntos_venta").val()) {
          window.toque_pedidos_listado_punto_venta = this.$("#toque_pedidos_puntos_venta").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_almacenes").length > 0) {
        if (window.toque_pedidos_listado_sucursal != this.$("#toque_pedidos_almacenes").val()) {
          window.toque_pedidos_listado_sucursal = this.$("#toque_pedidos_almacenes").val();
          cambio_parametros = true;
        }
      }

      if (this.$("#toque_pedidos_vendedores").length > 0) {
        if (window.toque_pedidos_listado_vendedor != this.$("#toque_pedidos_vendedores").val()) {
          window.toque_pedidos_listado_vendedor = this.$("#toque_pedidos_vendedores").val();
          cambio_parametros = true;
        }
      }

      // Filtramos por los tipos de mesas
      var tipos = new Array();
      this.$(".toque_pedidos_tipo_check:checked").each(function(i,e){
        tipos.push($(e).val());
      });
      filtros.tipos = tipos.join("-");      

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.toque_pedidos_listado_page = 1;

      filtros.desde = (isEmpty(window.toque_pedidos_listado_fecha_desde)) ? "" : window.toque_pedidos_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.toque_pedidos_listado_fecha_hasta)) ? "" : window.toque_pedidos_listado_fecha_hasta.replace(/\//g,"-");
      filtros.fecha_reparto = (isEmpty(window.toque_pedidos_listado_fecha_reparto)) ? "" : window.toque_pedidos_listado_fecha_reparto.replace(/\//g,"-");
      filtros.numero_reparto = window.toque_pedidos_listado_numero_reparto;
      filtros.id_vendedor = window.toque_pedidos_listado_vendedor;
      filtros.filter = window.toque_pedidos_listado_filter;
      filtros.id_tarjeta = window.toque_pedidos_listado_tarjeta;
      filtros.lote = window.toque_pedidos_listado_lote;
      filtros.cupon = window.toque_pedidos_listado_cupon;
      filtros.id_punto_venta = window.toque_pedidos_listado_punto_venta;
      filtros.custom_10 = window.toque_pedidos_listado_custom_10;
      filtros.forma_pago = window.toque_pedidos_listado_forma_pago;
      filtros.monto = window.toque_pedidos_listado_monto;
      filtros.monto_tipo = window.toque_pedidos_listado_monto_tipo;
      filtros.tipo_cliente = window.toque_pedidos_listado_tipo_cliente;
      filtros.con_anulados = window.toque_pedidos_listado_con_anulados;
      filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : ((this.$("#toque_pedidos_usuarios").length > 0) ? this.$("#toque_pedidos_usuarios").val() : 0);
      filtros.id_proyecto = ID_PROYECTO;
      filtros.in_tipos_estados = window.toque_pedidos_listado_in_tipos_estados;
      filtros.id_sucursal = (window.toque_pedidos_listado_sucursal != 0) ? window.toque_pedidos_listado_sucursal : ID_SUCURSAL;
      filtros.custom_orden = window.toque_pedidos_listado_order_by;

      // TODO: Hacer esto dinamico si quiere que se totalice aca o no
      this.usa_filtros = (MEGASHOP != 1 && (control.check("toque_pedidos_listado") == 3) && (!isEmpty(filtros.desde) || !isEmpty(filtros.hasta)));
      if (this.usa_filtros) filtros.offset = 99999;

      this.collection.server_api = filtros;
      this.collection.goTo(window.toque_pedidos_listado_page);
    },
    
    addAll : function () {
      var self = this;
      window.toque_pedidos_listado_page = this.pagination.getPage();
      this.$("#toque_pedidos_tabla tbody tr").empty();

      var reproducir_sonido = false;
      var total = 0;
      var cantidad = 0;
      this.collection.each(function(i){
        if (i.get("id_tipo_estado") == 0) reproducir_sonido = true;
        self.addOne(i);
        total += parseFloat(i.get("total"));
        cantidad++;
      });

      if (this.usa_filtros) {
        this.$("#toque_pedidos_resumen_total").html("$ "+Number(total).toFixed(2));
        this.$("#toque_pedidos_resumen_cantidad").html(cantidad);
        this.$(".pagination_container").hide();
        this.$(".resumen").show();
      } else {
        this.$(".resumen").hide();
      }
      
      // Como tenemos un nuevo pedido, hacemos sonar la alarma
      if (reproducir_sonido && ID_EMPRESA == 571 || ID_EMPRESA == 1275) window.playSound();

      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ToquePedidosItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: self,
      });
      this.$("#toque_pedidos_tabla tbody").append(view.render().el);
    },
    
    sumar_lote: function() {
      var checks = this.$("#toque_pedidos_tabla tbody .i-checks input[type=checkbox]:checked");
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
      alert("El total de los comprobantes seleccionados es: $ "+Number(total).toFixed(2));
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ToquePedidosItemResultados = app.mixins.View.extend({
    template: _.template($("#toque_pedidos_item_resultados_template").html()),
    tagName: "tr",
    className: function() {
      return (this.model.get("id_tipo_estado") == 0)?"no_leido":"";
    },
    myEvents: {

      "click .enviar_whatsapp":function(e){
        e.stopPropagation();
        e.preventDefault();
        var mensaje = "";
        var tel = this.model.get("telefono");
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel;
        window.open(link_ws,"_blank");
      },

      "click .pickup_listo":function() {
        var c = prompt("Ingrese el codigo de seguridad: ");
        if (!c) return;
        if (c != this.model.get("link_envio")) {
          alert("El codigo de seguridad es incorrecto.");
          return;
        }
        var self = this;
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "url":url_base+"/function/pickup_listo/",
          "type":"get",
          "data":{
            "id":self.model.id,
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            self.parent.buscar();
          }
        });
      },

      "click .pedido_listo":function() {
        var self = this;
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "url":url_base+"/function/pedido_listo/",
          "type":"get",
          "data":{
            "id":self.model.id,
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            self.parent.buscar();
          }
        });
      },

      "click .aceptar_pedido_deposito":function() {
        var self = this;
        $.ajax({
          "url":"repartidores/function/aceptar_dinero_comercio/",
          "type":"get",
          "data":{
            "id":self.model.id,
            "id_repartidor":self.model.get("id_vendedor"),
            "id_punto_venta":self.model.get("id_punto_venta"),
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            self.parent.buscar();
          }
        });
      },

      "click .rechazar_pedido_deposito":function() {
        var self = this;
        if (!confirm("Realmente desea rechazar el deposito de dinero?")) return;
        $.ajax({
          "url":"repartidores/function/rechazar_dinero_comercio/",
          "type":"get",
          "data":{
            "id":self.model.id,
            "id_repartidor":self.model.get("id_vendedor"),
            "id_punto_venta":self.model.get("id_punto_venta"),
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            self.parent.buscar();
          }
        });
      },
      
      "click .aceptar_pedido_comercio":function() {
        var self = this;
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "url":url_base+"/function/aceptar_pedido_comercio/",
          "type":"get",
          "data":{
            "id":self.model.id,
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            console.log(r);
            //if (r.error == 0) {
              self.parent.buscar();
            //}
          }
        });
      },

      "click .rechazar_pedido_comercio":function() {
        var self = this;
        if (!confirm("Realmente desea rechazar el pedido?")) return;
        var p = new app.views.RechazoPedidoToqueView({
          model: new app.models.AbstractModel({
            "id_pedido":self.model.id,
            "id_empresa":ID_EMPRESA,
          }),
          parent: self,
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });        
      },

      "click .ver_tarjeta":function(e) {
        e.stopPropagation();
        this.ver_tarjeta();
      },
      "click .data":"seleccionar_factura",
      "click .edit":"editar",
      "click .anular":"anular",
      "click .restaurar":"restaurar",
      "click .delete":"borrar",
      "click .editar_observaciones":"editar_observaciones",
    },
    imprimir: function() {
      var self = this;
      workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));
    },
    ver_tarjeta: function() {
      var self = this;
      $.ajax({
        "type":"post",
        "dataType":"json",
        "url":"tarjetas/function/ver_cupon/"+self.model.id+"/"+self.model.get("id_punto_venta"),
        "success":function(r) {
          if (r.length == 0) {
            alert("Ocurrio un error al mostrar el cupon de la tarjeta");
            return;
          }
          var p = new app.views.CuponTarjetaView({
            model: new app.models.AbstractModel({
              "tarjetas":r,
            })
          });
          crearLightboxHTML({
            "html":p.el,
            "width":450,
            "height":140,
          });
        }
      })
    },

    editar_observaciones: function() {
      var self = this;
      var view = new app.views.EditarObservacionesView({
        "model": self.model,
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
        "callback":function() {
          app.views.toque_pedidosTableView.buscar();
        }
      });
    }, 

    seleccionar_factura : function() {
      if (this.options.seleccionar) {
        console.log(this.model);
        window.factura_seleccionada = this.model;
        $('.modal:last').modal('hide');
        //this.parent.importar(this.model);
      }
    },
    editar : function() {
      this.imprimir();
    },
    anular: function() {
      var self = this;
      if (confirmar("Realmente desea anular este comprobante?")) {
        // Se debe ANULAR, NO BORRAR
        $.ajax({
          "url":"facturas/function/anular/"+self.model.id+"/"+self.model.get("id_punto_venta"),
          "dataType":"json",
          "success":function(r){
            app.views.toque_pedidosTableView.buscar();
          }
        });                      
      }
    },
    restaurar: function() {
      var self = this;
      if (confirmar("Realmente desea restaurar este comprobante?")) {
        // Se debe ANULAR, NO BORRAR
        $.ajax({
          "url":"facturas/function/restaurar/"+self.model.id+"/"+self.model.get("id_punto_venta"),
          "dataType":"json",
          "success":function(r){
            app.views.toque_pedidosTableView.buscar();
          }
        });                      
      }
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
              location.reload();
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
              location.reload();
            }
          });

        // Sino, es una FA, FB, NC, ND
        } else {

          $.ajax({
            "url":"facturas/function/borrar_factura/"+self.model.id+"/"+self.model.get("id_punto_venta"),
            "dataType":"json",
            "success":function(r){
              location.reload();
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
      var self = this;
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));

      var id_tipo_estado = this.model.get("id_tipo_estado");

      // custom_2 = Tiempo de retiro por local (estimado) 
      if (!isEmpty(this.model.get("custom_2"))) {
        // Si el estado es aceptado por comercio o repartidor, entonces ponemos el contador
        if ((id_tipo_estado >=1 && id_tipo_estado <= 3)) {
          this.$('.texttiempo').countdown(self.model.get("custom_2"),{elapse: true})
          .on('update.countdown', function(event) {
            var $this = $(this);
            if (event.elapsed) {
              $this.html(event.strftime('-%H:%M:%S'));
            } else {
              $this.html(event.strftime('%H:%M:%S'));
            }
          });
        } else if (id_tipo_estado > 3 && id_tipo_estado <= 6) {
          if (!isEmpty(self.model.get("codigo_postal"))) {
            // Tiempo que marca el negocio como listo
            this.$('.texttiempo').html(diferenciaTiempo(self.model.get("codigo_postal"),self.model.get("custom_2"))+" min.");  
          } else {
            this.$('.texttiempo').html(diferenciaTiempo(self.model.get("retirado"),self.model.get("custom_2"))+" min.");  
          }
        }
      }

      if (id_tipo_estado >= 1 && id_tipo_estado < 6 && this.$(".texttiempo_vencimiento").length > 0) {
        this.$('.texttiempo_vencimiento').countdown(self.model.get("vencimiento"),{elapse: true})
        .on('update.countdown', function(event) {
          var $this = $(this);
          if (event.elapsed) {
            $this.html(event.strftime('-%H:%M:%S'));
          } else {
            $this.html(event.strftime('%H:%M:%S'));
          }
        });
      } else if (id_tipo_estado == 6) {
        this.$('.texttiempo_vencimiento').html(diferenciaTiempo(self.model.get("entregado"),self.model.get("vencimiento"))+" min.");
      }

      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.RechazoPedidoToqueView = app.mixins.View.extend({
    template: _.template($("#rechazo_pedido_toque_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var motivo = self.$("#rechazo_pedido_toque_motivos").val();
        var observaciones = self.$("#rechazo_pedido_toque_observaciones").val();
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "timeout":0,
          "url":url_base+"/function/rechazar_pedido_comercio/",
          "type":"post",
          "data":{
            "motivo":motivo,
            "observaciones":observaciones,
            "id":self.model.get("id_pedido"),
            "id_empresa":ID_EMPRESA,
          },
          "dataType":"json",
          "success":function(r) {
            console.log(r);
            self.parent.parent.buscar();
            $('.modal:last').modal('hide');
          }
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.parent = options.parent;
      $(this.el).html(this.template());
    },
  });
})(app);



(function ( app ) {
  app.views.EditarRepartidorView = app.mixins.View.extend({
    template: _.template($("#editar_repartidor_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_repartidor = self.$("#editar_repartidores").val();
        var repartidor = self.$("#editar_repartidores option:selected").text();
        var saldo = self.$("#editar_repartidores option:selected").data("saldo");
        var limite_efectivo = self.$("#editar_repartidores option:selected").data("limite_efectivo");

        // Primero controlamos que las ventas marcadas no superen el limite del vendedor
        if (limite_efectivo > 0) {
          var total_ventas = 0;
          saldo = parseFloat(saldo);
          if (isNaN(saldo)) saldo = 0;
          for(var i=0;i< self.ventas_marcadas.length; i++) {
            var v = self.ventas_marcadas[i];
            var ef = parseFloat(v.efectivo);
            if (isNaN(ef)) ef = 0;
            total_ventas += ef;
          }
          // Si es un pedido en efectivo
          if (total_ventas > 0) {
            if ((saldo + total_ventas) > limite_efectivo) {
              var mensaje = "El repartidor tiene un limite de efectivo de $ "+Number(limite_efectivo).toFixed(0);
              mensaje += " y un saldo actual de $ "+Number(saldo).toFixed(0);
              mensaje += ". Desea asignarle las ventas seleccionadas igualmente?";
              if (!confirm(mensaje)) return;
            }
          }
        }
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "timeout":0,
          "url":url_base+"/function/editar_repartidor/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ventas":self.ventas_marcadas,
            "id_repartidor":id_repartidor,
            "repartidor":repartidor,
            "id_empresa":ID_EMPRESA,
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
      this.id_repartidor = 0;
      this.ventas_marcadas = options.ventas_marcadas;
      $(this.el).html(this.template());
      this.cargar_vendedores();
    },
    cargar_vendedores: function() {
      new app.mixins.Select({
        modelClass: app.models.Repartidor,
        url: "repartidores/function/buscar/?activo=1&buscar_saldo=1&limit=0&offset=99999",
        fields: ["limite_efectivo","saldo"],
        firstOptions: ["<option value='-1'>Ninguno</option><option value='0'>Recalcular</option>"],
        render: "#editar_repartidores",
        selected: self.id_repartidor,
      });
    }
  });
})(app);


(function ( app ) {
  app.views.EditarObservacionesView = app.mixins.View.extend({
    template: _.template($("#editar_observaciones_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var custom_4 = self.$("#editar_observaciones_comercio").val();
        var observaciones = self.$("#editar_observaciones_repartidor").val();
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "timeout":0,
          "url":url_base+"/function/editar_observaciones/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":self.model.id,
            "id_punto_venta":self.model.get("id_punto_venta"),
            "custom_4":custom_4,
            "observaciones":observaciones,
            "id_empresa":ID_EMPRESA,
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
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);


(function ( app ) {
  app.views.EditarEstadoView = app.mixins.View.extend({
    template: _.template($("#editar_estado_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_estado = self.$("#editar_estados").val();
        var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
        $.ajax({
          "timeout":0,
          "url":url_base+"/function/editar_estado/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ventas":self.ventas_marcadas,
            "id_tipo_estado":id_estado,
            "id_empresa":ID_EMPRESA,
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
      this.id_repartidor = 0;
      this.ventas_marcadas = options.ventas_marcadas;
      $(this.el).html(this.template());
    },
  });
})(app);



(function ( app ) {

  app.views.ToquePedidoEditView = app.mixins.View.extend({

    template: _.template($("#toque_pedido_template").html()),

    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .cerrar_mesa": "cerrar_mesa",
      "click .cerrar_mesa_efectivo": function(){
        var self = this;
        this.total_anterior = parseFloat(this.model.get("total"));
        this.subtotal_anterior = parseFloat(this.model.get("subtotal"));
        this.model.set({
          "total":self.total_anterior,
          "subtotal":self.subtotal_anterior,
        });
        this.cerrar_mesa();
      },
      "click .cerrar_mesa_tarjeta": function() {
        this.cerrar_mesa();
      },

      "change #toque_pedido_paga_con":"calcular_vuelto",
      "click #toque_pedido_demora":"calcular_demora",

      "focusout #toque_pedido_cliente":function(e) {
        // Si el cliente esta vacio, ponemos consumidor final
        if (isEmpty($(e.currentTarget).val())) {
          this.setear_consumidor_final();
        }
      },

      // Buscamos el cliente por codigo
      "click #toque_pedido_buscar_articulo":"ver_buscar_articulo",
      "keypress #toque_pedido_item_articulo": function(e) {
        if (e.which == 13) this.buscar_articulo();
      },
      "keypress #toque_pedido_item_cantidad":function(e) {
        if (e.which == 13) {
          if (this.$("#toque_pedido_item_precio").is(":disabled")) {
            $("#toque_pedido_agregar_item").focus();
          } else {
            $("#toque_pedido_item_precio").select();
          }
        }
      },
      "keypress #toque_pedido_item_precio":function(e) {
        if (e.which == 13) this.agregar_item();
      },
      "click #toque_pedido_agregar_item":function() {
        this.agregar_item();
      },
      "keypress #toque_pedido_item_descripcion":function(e) {
        if (e.which == 13) this.agregar_item();
      },

      "change #toque_pedido_porc_descuento": function() {
        this.calcular_totales();
      },
      "change #toque_pedido_tipo_entrega":function(e) {
        var tipo = $(e.currentTarget).val();
        var titulo = $(e.currentTarget).find("option:selected").text();
        this.model.set({
          "tipo":tipo,
          "titulo":titulo,
        });
        this.render();
        this.render_tabla_items();
      },

      // Mas y menos de Input group
      "click .addon_minus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var min = $(el).attr("min");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (min != undefined && (valor-step < min)) valor = min;
        else valor = valor - step;
        $(el).val(valor);
        $(el).trigger("change");
      },
      "click .addon_plus":function(e) {
        var el = $(e.currentTarget).parents(".input-group").find(".form-control");
        var max = $(el).attr("max");
        var step = (typeof $(el).data("step") != "undefined") ? parseFloat($(el).data("step")) : 1;
        var valor = parseFloat($(el).val());
        if (isNaN(valor)) valor = 0;
        if (max != undefined && (valor+step > max)) valor = max;
        else valor = valor + step;
        $(el).val(valor);
        $(el).trigger("change");
      },

    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.guardando = 0;
      this.items_nuevos = new Array();
      this.items_cocina = new Array();
      
      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ToquePedidoItem,
        });
        this.items = new ItemsCollection();
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);
        
        // Renderizamos y limpiamos
        this.render();
        this.setear_consumidor_final();

        // Estamos editando
      } else {

        this.render();

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.ToquePedidoItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);                
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          p.id_tipo_estado = this.model.get("id_tipo_estado");
          var fi = new app.models.ToquePedidoItem(p);
          this.items.add(fi);
        }
        
        // Buscamos el cliente y lo seteamos
        var id_cliente = self.model.get("id_cliente");
        if (id_cliente == 0) {
          this.setear_consumidor_final();
        } else {
          var cliente = new app.models.Cliente({"id":id_cliente});
          cliente.fetch({
            "success":function() {
              self.seleccionar_cliente(cliente);        
            },
          });
        }
        
        //this.render_tabla_items();
        //$("#tabla_items tbody").empty();
      }
    },

    setear_consumidor_final: function() {
      var cf = new app.models.Cliente({
        "id":0,
        "id_tipo_iva":4,
        "nombre":"",
        "cuit":"",
        "saldo":0,
        "email":"",
        "direccion":"",
        "percibe_ib":0,
        "descuento":0,
        "error":0,
        "id_vendedor":0,
        "lista":0,
        "tipo_pago":"E",
      });            
      this.seleccionar_cliente(cf);
    },

    calcular_demora: function() {
      var self = this;
      var latitud = this.$("#toque_pedido_latitud").val();
      var longitud = this.$("#toque_pedido_longitud").val();
      if (latitud == 0 || longitud == 0) return;
      var id_comercio = (PERFIL == 661) ? ID_USUARIO : this.$("#toque_pedido_comercios").val();
      var its = new Array();
      this.items.each(function(e){
        its.push(e.get("id_articulo"));
      });
      var url_base = (ID_EMPRESA == 571 || ID_EMPRESA == 1275) ? "toque" : "app_pedidos";
      $.ajax({
        "url":"/sistema/"+url_base+"/function/calcular_demora_comercio/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_comercio":id_comercio,
          "latitud_cliente":latitud,
          "longitud_cliente":longitud,
          "items":JSON.stringify(its),
        },
        "success":function(r) {
          if (r.error == 1) alert(r.mensaje);
          else {
            self.$("#toque_pedido_demora").html(Number(r.demora).toFixed(0)+" min.");
            self.$("#toque_pedido_costo_envio").html("$ "+Number(r.valor_envio).toFixed(0));
            self.model.set({
              "costo_envio":r.valor_envio
            });
            self.calcular_totales();
          }
        },
      })
    },

    render: function() {

      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS,"en",{
          "libraries":"places,geometry",
        }).done(self.set_autocomplete);
      } catch(e) {
        setTimeout(function(){
          self.set_autocomplete();
        },1000);
      }      

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#toque_pedido_cliente");
      if (this.model.get("tipo") == "M") {
        var form = new app.views.ClienteEditViewMini({
          "model": new app.models.Cliente(),
          "input": input,
          "onSave": self.seleccionar_cliente,
        });            
      } else {
        var form = null;
      }

      // BUSCAMOS POR NOMBRE
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "hideNoResults":true,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
              self.$("#toque_pedido_item_articulo").focus();
            },
          });
        }
      });                

      // BUSCAMOS POR DNI
      var input_documento = this.$("#toque_pedido_documento");
      $(input_documento).customcomplete({
        "url":"clientes/function/get_by_cuit/",
        "form":null,
        "hideNoResults":true,
        "disableNumber":false,
        "width":"300px",
        "minLength":7,
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
              self.$("#toque_pedido_item_articulo").focus();
            },
          });
        }
      });                

      // BUSCAMOS POR TELEFONO
      var input_telefono = this.$("#toque_pedido_telefono");
      $(input_telefono).customcomplete({
        "url":"clientes/function/get_by_telefono/",
        "form":null,
        "hideNoResults":true,
        "disableNumber":false,
        "width":"300px",
        "minLength":10,
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
              self.$("#toque_pedido_item_articulo").focus();
            },
          });
        }
      });                

      var input = this.$("#toque_pedido_item_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "width":"300px",
        "label":"[nombre] ([codigo])",
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });

      this.calcular_demora();
    },

    set_autocomplete: function() {
      this.autocomplete = new google.maps.places.Autocomplete(this.$('#toque_pedido_direccion')[0], {types: ['geocode']});
      this.autocomplete.setFields(['address_component','formatted_address','geometry']);
      this.autocomplete.setComponentRestrictions({
        'country':"AR",
      });
      this.autocomplete.addListener('place_changed', this.fillInAddress);
    },

    fillInAddress: function() {
      var place = this.autocomplete.getPlace();

      // Analizamos que si o si tiene que tener un componente de numero
      var encontro_numero = false;
      for(var i=0;i< place.address_components.length;i++) {
        var c = place.address_components[i];
        if (typeof c.types != "undefined" && c.types.length > 0) {
          for(var j=0;j< c.types.length;j++) {
            var k = c.types[j];
            if (k == "street_number") {
              encontro_numero = true;
              break;
            }
          }
        }
      }
      if (!encontro_numero) {
        alert("Por favor ingrese la direccion en el formato 'CALLE NUMERO LOCALIDAD' y seleccione de la lista.");
        this.$("#toque_pedido_latitud").val(0);
        this.$("#toque_pedido_longitud").val(0);
        return;
      }
      this.$("#toque_pedido_latitud").val(place.geometry.location.lat());
      this.$("#toque_pedido_longitud").val(place.geometry.location.lng());
      this.calcular_demora();
    },

    seleccionar_cliente: function(r) {
      var self = this;
      console.log(r);
      // Seteamos el cliente
      self.model.set({
        "cliente":r,
        "id_cliente":r.id,
        "nombre":r.get("nombre"),
        "direccion":r.get("direccion"),
        "telefono":r.get("telefono"),
        "email":r.get("email"),
      });
      self.$("#toque_pedido_cliente").val(r.get("nombre"));
      self.$("#toque_pedido_direccion").val(r.get("direccion"));
      self.$("#toque_pedido_telefono").val(r.get("telefono")+""+r.get("celular"));
      self.$("#toque_pedido_documento").val(r.get("cuit"));
      self.$("#toque_pedido_email").val(r.get("email"));
      self.$("#toque_pedido_latitud").val(r.get("latitud"));
      self.$("#toque_pedido_longitud").val(r.get("longitud"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#toque_pedido_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);       

      self.calcular_demora();
    },

    ver_buscar_cliente: function() {
      var self = this;
      var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
        collection: clientes,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarClientes.el);
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":600,
        "height":140,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#toque_pedido_cliente").select();                    
        }
      });
      $(".search_input").select();
    },

    seleccionar_articulo: function(r){
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      this.$("#toque_pedido_item_cantidad").select();
      if (r.get("unidad") == "M") {
        this.$("#toque_pedido_item_cantidad").data("step","0.5");
      } else {
        this.$("#toque_pedido_item_cantidad").data("step","1");
      }

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#toque_pedido_item_articulo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#toque_pedido_item_id_articulo").val(this.item.get("id_articulo"));
      $("#toque_pedido_item_articulo").val(this.item.get("nombre"));
      $("#toque_pedido_item_cantidad").val(this.item.get("cantidad"));
      $("#toque_pedido_item_descripcion").val(this.item.get("descripcion"));
      $("#toque_pedido_item_precio").val(this.item.get("precio"));
      $("#toque_pedido_item_tipo").val(this.item.get("tipo"));
      self.calcular_item();
      this.$("#toque_pedido_item_descripcion").select();            
    },

    duplicar_articulo: function(r) {
      var self = this;
      var id_articulo = r.get("id_articulo");
      // Lo buscamos en el array
      this.articulo = window.articulos.find(function(c){
        return (c.id == id_articulo);
      });
      $("#toque_pedido_item_id_articulo").val(r.get("id_articulo"));
      $("#toque_pedido_item_articulo").val(r.get("nombre"));
      $("#toque_pedido_item_cantidad").val(1);
      $("#toque_pedido_item_descripcion").val(r.get("descripcion"));
      $("#toque_pedido_item_precio").val(r.get("precio"));
      $("#toque_pedido_item_tipo").val(r.get("tipo"));
      $("#toque_pedido_item_no_totalizar_reparto").val(this.articulo.get("no_totalizar_reparto"));
      self.calcular_item();
      self.agregar_item();
      this.articulo = null;
    },

    ver_buscar_articulo: function() {
      var self = this;
      var id_usuario = ((PERFIL == 660 || PERFIL == 862) ? self.$("#toque_pedido_comercios").val() : ID_USUARIO);
      var view = new app.views.BuscarArticulosPorRubroView({
        collection: new app.collections.Articulos(),
        id_usuario: id_usuario,
      });
      window.pedidos = new Array();
      var d = $("<div/>").append(view.el);
      crearLightboxHTML({
        "html":d,
        "width":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? 1400 : 800,
        "height":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? 700 : 500,
        "escapable":(ID_EMPRESA == 171 || ID_EMPRESA == 599) ? false : true,
        "callback":function() {
          if (window.pedidos.length > 0) {
            for(var i=0;i<window.pedidos.length;i++) {
              var pedido = window.pedidos[i];
              if (pedido.cantidad == 0) continue;
              self.articulo = pedido.articulo;
              self.mostrar_articulo();
              self.$("#toque_pedido_item_cantidad").val(pedido.cantidad);
              self.$("#toque_pedido_item_descripcion").val(pedido.descripcion);
              self.calcular_item();
              self.agregar_item();
            }
          }
        }
      });
    },

    buscar_articulo: function() {

      var self = this;
      var codigo = $("#toque_pedido_item_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      
      // Lo buscamos en el array
      var r = window.articulos.find(function(c){
        return (c.get("codigo") == codigo);
      });
      if (typeof r === "undefined") {
        self.articulo = null;
        this.$("#toque_pedido_item_cantidad").select();
      } else {
        this.seleccionar_articulo(r);
      }
    },

    mostrar_articulo : function() {
      this.$("#toque_pedido_item_articulo").val(this.articulo.get("nombre"));
      this.$("#toque_pedido_item_id_articulo").val(this.articulo.id);
      this.$("#toque_pedido_item_descripcion").val(this.articulo.get("descripcion"));
      this.$("#toque_pedido_item_no_totalizar_reparto").val(this.articulo.get("no_totalizar_reparto"));
      // El precio se forma con el adicional
      var precio = parseFloat(this.articulo.get("precio_final_dto"));
      var adicional = parseFloat(this.articulo.get("precio_final_dto_2"));
      this.$("#toque_pedido_item_precio").val(Number(precio+adicional).toFixed(2));
      this.$("#toque_pedido_item_tipo").val(0);
    },

    // Agrega el item a la lista
    agregar_item : function() {

      var self = this;

      var codigo = this.$("#toque_pedido_item_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#toque_pedido_item_articulo").focus();
        return;
      }

      var tipo = this.$("#toque_pedido_item_tipo").val();
      var id_articulo = this.$("#toque_pedido_item_id_articulo").val();
      var cocina = this.$("#toque_pedido_item_no_totalizar_reparto").val();
      var descripcion = this.$("#toque_pedido_item_descripcion").val();
      var id_rubro = (this.articulo != undefined) ? this.articulo.get("id_rubro") : 0;
      var cantidad = this.$("#toque_pedido_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }

      var bonificacion = 0; //this.$("#toque_pedido_item_bonificado").val();
      //var bonificacion = (this.articulo != undefined) ? this.articulo.get("porc_bonif") : 0;
      var precio = parseFloat(this.$("#toque_pedido_item_precio").val());
      var total = precio * ((100-bonificacion)/100) * cantidad;
      
      var values = {
        "id_articulo":id_articulo,
        "no_totalizar_reparto":cocina,
        "precio":precio,
        "nombre":codigo,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "id_rubro":id_rubro,
        "total_con_iva":total,
        "descripcion":descripcion,
        "tipo":tipo,
      };
      
      if (this.item != undefined) {
        // Editamos un ITEM
        this.item.set(values);
      } else {
        // Agregamos un nuevo ITEM
        var item = new app.models.ToquePedidoItem(values);
        item.set({
          "orden":1,
          "tipo_cantidad":moment().format("HHmm"),
          // SON BEBIDAS, VAN A LA BARRA
          "custom_1":((ID_EMPRESA == 162 && item.get("id_rubro")==413)?"1":""),
          // VAN A LA COCINA
          "custom_2":((item.get("no_totalizar_reparto") == 1)?"1":""),
        });
        this.items.add(item);
      }
      
      this.item = undefined;
      this.limpiar_item();
      //this.$("#toque_pedido_item_articulo").select();            

      var wtf    = $('#tabla_items').parent();
      var height = wtf[0].scrollHeight;
      wtf.scrollTop(height);
      this.calcular_demora();
    },

    calcular_item: function() {
      var self = this;
      var cantidad = this.$("#toque_pedido_item_cantidad").val();
      var precio_unit = this.$("#toque_pedido_item_precio").val();
      var bonificado = 0; //this.$("#toque_pedido_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
      this.$("#toque_pedido_item_subtotal").val(subtotal);
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      var self = this;
      var view = new app.views.ToquePedidoItem({
        "model": item,
        "view":this,
        "origen":self.model.get("tipo"),
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },        

    calcular_totales : function() {

      var porc_descuento = 0; var total = 0; var costo_envio = 0;
      var descuento = 0; var subtotal = 0;
      var items = this.model.get("items");
      
      porc_descuento = parseFloat(this.$("#toque_pedido_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      this.items.each(function(item){
        total = total + parseFloat(item.get("total_con_iva")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total_con_iva"));
      });
      
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;

      var costo_envio = this.model.get("costo_envio");
      if (isNaN(costo_envio)) costo_envio = 0;

      this.model.set({
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "subtotal":subtotal,
        "costo_envio":costo_envio,
        "total":total,
      });
      this.$("#toque_pedido_total").html("$ "+Number(total + costo_envio).toFixed(2));
    },

    limpiar_item: function() {
      this.$("#toque_pedido_item_id_articulo").val("0");
      this.$("#toque_pedido_item_cantidad").val("1");
      this.$("#toque_pedido_item_precio").val("0.00");
      this.$("#toque_pedido_item_subtotal").val("");
      this.$("#toque_pedido_item_articulo").val("");
      this.$("#toque_pedido_item_descripcion").val("");
      this.$("#toque_pedido_item_no_totalizar_reparto").val("");
      //this.$("#toque_pedido_item_articulo").focus();
    },

    abrir:function() {
      this.model.set({"estado":"A"});
      this.render();
    },

    validar: function() {
      try {
        var self = this;
        if (this.items.length == 0) {
          alert("Por favor agregue algun producto antes de guardar.");
          return false;
        }

        if (self.$("#toque_pedido_cliente").length>0) {
          var cliente = self.$("#toque_pedido_cliente").val();
          if (isEmpty(cliente)) {
            alert("Por favor ingrese un cliente.");
            this.$("#toque_pedido_cliente").select();
            return false;
          }
        }

        var telefono = this.$("#toque_pedido_telefono").val();
        if (!isTelephone(telefono)) {
          alert("Por favor ingrese un telefono valido sin 0 ni 15.");
          this.$("#toque_pedido_telefono").select();
          return false;
        }

        var documento = this.$("#toque_pedido_documento").val();
        if (isEmpty(documento)) {
          alert("Por favor ingrese un documento.");
          this.$("#toque_pedido_documento").focus();
          return false;
        }

/*
          {lat: -38.926123, lng: -68.012794},
          {lat: -38.925905, lng: -67.982214},
          {lat: -38.928741, lng: -67.982225},
          {lat: -38.928776, lng: -67.972826},
          {lat: -38.931106, lng: -67.972741},
          {lat: -38.931068, lng: -67.966376},
          {lat: -38.948116, lng: -67.966234},
          {lat: -38.946797, lng: -67.972780},
          {lat: -38.959963, lng: -67.976952},
          {lat: -38.942632, lng: -68.011344},
          {lat: -38.936003, lng: -68.011037},
          {lat: -38.935973, lng: -68.013092},
          {lat: -38.926123, lng: -68.012794},*/

        var limites = [
          {lat: -38.916383, lng: -68.017642},
          {lat: -38.916831, lng: -68.000898},
          {lat: -38.921405, lng: -68.000910},
          {lat: -38.921405, lng: -67.999091},
          {lat: -38.925885, lng: -67.999089},
          {lat: -38.925885, lng: -67.982209},
          {lat: -38.928765, lng: -67.982240},
          {lat: -38.928765, lng: -67.955953},
          {lat: -38.950302, lng: -67.955953},
          {lat: -38.946725, lng: -67.972924},
          {lat: -38.959738, lng: -67.977327},
          {lat: -38.942895, lng: -68.010630},
          {lat: -38.935994, lng: -68.010919},
          {lat: -38.935941, lng: -68.012897},
          {lat: -38.925937, lng: -68.013063},
          {lat: -38.916383, lng: -68.017642},
        ];
        var laplatacasco = new google.maps.Polygon({paths: limites});
        var latitud = this.$("#toque_pedido_latitud").val();
        var longitud = this.$("#toque_pedido_longitud").val();
        if (isEmpty(latitud) || isEmpty(longitud))  {
          alert("Por favor seleccione una direccion correcta.");
          return false;
        }
        var pos = new google.maps.LatLng(latitud, longitud);
        if (!google.maps.geometry.poly.containsLocation(pos, laplatacasco)) {
          alert("Toque no llega hasta esa direccion, esperamos poder expandirnos pronto.");
          return false;
        }
        this.model.set({
          "latitud":latitud,
          "longitud":longitud,
        });

        var efectivo = parseFloat(this.$("#toque_pedido_paga_con").val());
        if (isNaN(efectivo) || efectivo <= 0) {
          alert("Por favor ingrese con cuanto paga el cliente.");
          this.$("#toque_pedido_paga_con").focus();
          return false;
        }

        this.calcular_vuelto();
        this.guardar_campos();

        if (this.model.get("vuelto") < 0) {
          alert("Por favor verifique el pago en efectivo.");
          this.$("#toque_pedido_paga_con").select();
          return false;
        }

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    calcular_vuelto: function() {
      var total = this.model.get("total");
      var costo_envio = this.model.get("costo_envio");
      var efectivo = parseFloat(this.$("#toque_pedido_paga_con").val());
      if (isNaN(efectivo)) efectivo = 0;
      if (isNaN(costo_envio)) costo_envio = 0;
      var vuelto = efectivo - total - costo_envio;
      this.model.set({
        "efectivo":efectivo,
        "vuelto":vuelto,
      });
      this.$("#toque_pedido_vuelto").val(Number(vuelto).toFixed(2));
    },

    guardar_campos: function() {
      var self = this;
      var id_usuario = ((PERFIL == 660 || PERFIL == 862) ? self.$("#toque_pedido_comercios").val() : ID_USUARIO);
      var usuario = ((PERFIL == 660 || PERFIL == 862) ? self.$("#toque_pedido_comercios option:selected").text() : NOMBRE_USUARIO);
      this.model.set({
        "items":self.items.toJSON(),
        "id_usuario": id_usuario,
        "usuario": usuario,
        "cliente": ((self.$("#toque_pedido_cliente").length>0) ? self.$("#toque_pedido_cliente").val() : ""),
        "direccion": ((self.$("#toque_pedido_direccion").length>0) ? self.$("#toque_pedido_direccion").val() : ""),
        "email": ((self.$("#toque_pedido_email").length>0) ? self.$("#toque_pedido_email").val() : ""),
        "telefono": ((self.$("#toque_pedido_telefono").length>0) ? self.$("#toque_pedido_telefono").val() : ""),
        "observaciones": ((self.$("#toque_pedido_observaciones").length>0) ? self.$("#toque_pedido_observaciones").val() : ""),
        "documento": ((self.$("#toque_pedido_documento").length>0) ? self.$("#toque_pedido_documento").val() : ""),
        "reference_id":1,
      });
    },

    cerrar_panel: function() {
      console.log("cerrar_panel");
      var self = this;
      this.guardando = 0;
      $(".modal").modal('hide');
    },

    cerrar_mesa:function() {
      var self = this;
      if (this.validar()) {

        if (this.guardando > 0) return;
        this.guardando = 1;

        if (self.model.id == null) {
          self.model.set({id:0});
        }
        this.model.set({
          "id_tipo_estado":0,
        });
        this.model.save({},{
          success: function(model,response) {
            self.guardando = 0;
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              location.reload();
            }
          }
        });
      }
    },

  });

})(app);


(function ( app ) {
  app.views.ToquePedidoItem = app.mixins.View.extend({

    template: _.template($("#toque_pedido_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.duplicar_articulo(this.model);
      },      
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
        return false;
      },
      "click .orden":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var o = $(e.currentTarget).text();
        o = parseInt(o)+1;
        if (o>5) o = 1;
        this.model.set({
          "orden":o,
        });
        this.render();
        return false;
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.id_tipo_estado = this.options.id_tipo_estado;
      this.origen = this.options.origen;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      var obj = { "id_tipo_estado":this.id_tipo_estado, "origen":this.origen };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);




// =================================================================
// CUENTA CORRIENTE DE REPARTIDORES


(function ( models ) {
  models.ToqueBilleteraMovimiento = Backbone.Model.extend({
    urlRoot: "toque_billetera_movimientos/",
    defaults: {
      id_concepto : 0,
      id_usuario: ID_USUARIO,
      id_factura: 0,
      id_punto_venta: 0,
      monto: 0,
      fecha: "",
      concepto: "",
      tipo: 0, // 0 = INGRESO, 1 = EGRESO
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      observaciones: "",
      subtotal: 0, // Atributo calculado para dar el subtotal del saldo
      path: "",
    }
  });
})( app.models );

(function ( views, models ) {

  views.ToqueBilleteraMovimientoView = app.mixins.View.extend({

    template: _.template($("#toque_billetera_movimiento_template").html()),
    
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar": "guardar",
      "click .agregar_concepto":function(e) {
        var self = this;
        var totaliza_en = "G";
        if ($(".concepto_edit_mini").length > 0) return;
        var form = new app.views.TipoGastoMiniEditView({
          "model": new app.models.TipoGasto({
            "totaliza_en":totaliza_en,
          }),
          "callback":function(m){
            var that = self;
            self.model.set({ "id_concepto":m });
            $.ajax({
              "url":"conceptos/function/get_arbol/",
              "type":"get",
              "dataType":"json",
              "success":function(r){
                that.cargar_conceptos(r);
              },
            });
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete concepto_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#concepto_mini_nombre").focus();
      },
    },

    initialize: function() {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.render();
    },

    cargar_conceptos: function(r) {
      var self = this;
      var r = workspace.crear_select(r,"",self.model.get("id_concepto"));
      this.$("#toque_billetera_movimientos_tipo").html(r);
      this.$("#select2-toque_billetera_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#toque_billetera_movimientos_tipo").select2({});
    },

    render: function() {
      var self = this;
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker(this.$("#toque_billetera_movimientos_fecha"),fecha);

      this.$("#select2-toque_billetera_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#toque_billetera_movimientos_tipo").select2({});
      return this;
    },

    // Rellena los campos con el modelo pasado por parametro
    // Luego la vista mostrara los datos para editar o solamente para ver
    ver: function(model) {
      this.model = model;
      this.render();
    },
    
    validar: function() {
      try {
        var self = this;
        if (this.$("#toque_billetera_movimientos_monto").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#toque_billetera_movimientos_monto").focus();
          return false;
        }
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.set({
          "fecha":self.$("#toque_billetera_movimientos_fecha").val(),
          "id_concepto":$(self.el).find("#toque_billetera_movimientos_tipo").val(),
          "monto":$(self.el).find("#toque_billetera_movimientos_monto").val(),
          "path":self.$("#hidden_path").val(),
        });
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            model.set({id:response.id});
            $('.modal:last').modal('hide');
          }
        });
      }
    },

  });

})(app.views, app.models);


(function ( app ) {

  app.views.ToqueBilleteraMovimientosView = app.mixins.View.extend({

    template: _.template($("#toque_billetera_movimientos_panel_template").html()),

    myEvents: {
      "change .check-row2":"sumar",
      "click .nuevo_gasto":"nuevo_gasto",
      "click .nuevo_ingreso":"nuevo_ingreso",
      "change #toque_billetera_movimientos_desde":"render_toque_billetera_movimientos",
      "change #toque_billetera_movimientos_hasta":"render_toque_billetera_movimientos",
      "change #toque_billetera_movimientos_conceptos":"render_toque_billetera_movimientos",
      "click .buscar":"render_toque_billetera_movimientos",
      "click .exportar":"exportar",
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      }
    },

    sumar : function(e) {
      e.stopPropagation();
      e.preventDefault();
      var el = e.currentTarget;
      if (el.type != "checkbox") return;
      if ($(el).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
      var marcado = false;
      var total = 0;
      var j = 0;
      this.$(".check-row2").each(function(i,e){
        if ($(e).is(":checked")) {
          marcado = true;
          total += parseFloat($(e).data("total"));
          j++;
        }
      });
      if (marcado) this.$(".bulk_action").slideDown();
      else this.$(".bulk_action").slideUp();

      this.$("#toque_billetera_movimientos_monto").html("$ "+Number(total).format(2));
      this.$("#toque_billetera_movimientos_cantidad").html(j);
      return false;
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.tipo = (typeof options.tipo == "undefined") ? 0 : options.tipo;
      this.id_cliente = (typeof options.id_cliente == "undefined") ? 0 : options.id_cliente;
      this.titulo = (typeof options.titulo == "undefined") ? "" : options.titulo;
      this.id_concepto = (typeof options.id_concepto == "undefined") ? 0 : options.id_concepto;
      window.toque_billetera_movimientos_desde = (typeof window.toque_billetera_movimientos_desde == "undefined") ? moment().format("DD/MM/YYYY") : window.toque_billetera_movimientos_desde;
      window.toque_billetera_movimientos_hasta = (typeof window.toque_billetera_movimientos_hasta == "undefined") ? moment().format("DD/MM/YYYY") : window.toque_billetera_movimientos_hasta;
      var obj = { 
        permiso: this.permiso,
        tipo: this.tipo,
        id_cliente: this.id_cliente,
        titulo: this.titulo,
        id_concepto: this.id_concepto,
        desde: window.toque_billetera_movimientos_desde,
        hasta: window.toque_billetera_movimientos_hasta,
      };
      $(this.el).html(this.template(obj));

      createdatepicker(this.$("#toque_billetera_movimientos_desde"),window.toque_billetera_movimientos_desde);
      createdatepicker(this.$("#toque_billetera_movimientos_hasta"),window.toque_billetera_movimientos_hasta);

      this.render_toque_billetera_movimientos()
    },

    render_toque_billetera_movimientos: function() {
      var self = this;

      if (this.$("#toque_billetera_movimientos_desde").length > 0 && window.toque_billetera_movimientos_desde != this.$("#toque_billetera_movimientos_desde").val().trim()) {
        window.toque_billetera_movimientos_desde = this.$("#toque_billetera_movimientos_desde").val().trim();
      }

      if (this.$("#toque_billetera_movimientos_hasta").length > 0 && window.toque_billetera_movimientos_hasta != this.$("#toque_billetera_movimientos_hasta").val().trim()) {
        window.toque_billetera_movimientos_hasta = this.$("#toque_billetera_movimientos_hasta").val().trim();
      }      

      $.ajax({
        "url":"toque_billetera_movimientos/function/listado/",
        "data":{
          "id_concepto":self.$("#toque_billetera_movimientos_conceptos").val(),
          "desde":window.toque_billetera_movimientos_desde,
          "hasta":window.toque_billetera_movimientos_hasta,
          "tipo":self.tipo,
          "id_cliente":self.id_cliente,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          var monto = 0;
          var cantidad = 0;
          var saldo_inicial = parseFloat(r.saldo_inicial);
          $(self.el).find("tbody").empty();
          self.$("tbody").append("<tr><td colspan='2' class='ver'></td><td class='ver'><span class='text-info'>Saldo Inicial</span></td><td colspan='2' class='ver'></td><td class='tar ver number'>$ "+Number(saldo_inicial).format(2)+"</td><td></td><tr>");
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            if (o.tipo == 0) saldo_inicial = saldo_inicial + parseFloat(o.monto);
            else saldo_inicial = saldo_inicial - parseFloat(o.monto);                
            o.subtotal = saldo_inicial;
            var item = new app.views.ToqueBilleteraMovimientoItem({
              "model":new app.models.ToqueBilleteraMovimiento(o),
              "tabla":self,
            });
            $(self.el).find("tbody").append(item.el);
            cantidad++;
          }
          window.monto_toque_billetera_movimientos = saldo_inicial - r.saldo_inicial;
        }
      });
    },

    exportar: function() {
      var self = this;
      var titulo = "Exportacion";
      var header = new Array();
      $(".table thead tr th.exportable").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) {
          header.push(t);
        }
      });
      // Acomodamos los datos
      var array = new Array();
      this.$(".table tbody tr").each(function(i,e){
        var obj = {};
        $(e).find("td.exportable").each(function(ii,ee){
          var s = $(ee).text().trim();
          if ($(ee).hasClass("number")) {
            s = s.replace(/\./g,"");
            s = s.replace(/\,/g,".");
            s = s.replace(/\$/g,"");
            s = parseFloat(s.trim());
          }
          obj["s"+ii] = s;
        });
        array.push(obj);
      })
      this.exportar_excel({
        "filename":(isEmpty(titulo) ? "exportacion" : titulo),
        "title":titulo,
        "data":array,
        "header":header,
      });
    },    

    nuevo_gasto: function() {
      var self = this;
      var edicion = new app.views.ToqueBilleteraMovimientoView({
        model: new app.models.ToqueBilleteraMovimiento({
          tipo: 1,
          id_cliente: self.id_cliente,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_toque_billetera_movimientos();
        }
      });
    },

    nuevo_ingreso: function() {
      var self = this;
      var edicion = new app.views.ToqueBilleteraMovimientoView({
        model: new app.models.ToqueBilleteraMovimiento({
          tipo: 0,
          id_cliente: self.id_cliente,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_toque_billetera_movimientos();
        }
      });
    },

  });
})(app);



(function ( app ) {

  app.views.ToqueBilleteraMovimientoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#toque_billetera_movimientos_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
    },
    initialize: function(options) {
      _.bindAll(this);
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.tabla = this.options.tabla;
      this.render();
    },
    render: function() {
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      var self = this;
      var edicion = new app.views.ToqueBilleteraMovimientoView({
        model: self.model
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "callback":function(){
          self.tabla.render_toque_billetera_movimientos();
        },
      });
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy(); // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        this.tabla.render_toque_billetera_movimientos();
      }
    },
  });

})( app );