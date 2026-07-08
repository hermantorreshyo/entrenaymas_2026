(function (collections, model, paginator) {

  collections.ToquePedidosRepartidores = paginator.requestPager.extend({

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


(function ( app ) {

  app.views.ToquePedidosRepartidoresTableView = app.mixins.View.extend({

    template: _.template($("#toque_pedidos_repartidores_resultados_template").html()),
      
    myEvents: {
      "change #toque_pedidos_repartidores_quincena":"buscar",
      "click .buscar":"buscar",
    },

    exportar : function() {
      /*
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
      */
    },    
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var filtros = {};

      filtros = {
        "id_vendedor":ID_USUARIO,
        "in_tipos_estados":6,
        "not_in_ids_punto_venta":2444, // Que no tome el punto de venta de DEPOSITO
      };
      var fechas = this.$("#toque_pedidos_repartidores_quincena").val();
      if (fechas == "hoy") {
        // AMBAS FECHAS SON HOY
        filtros.desde = moment().format("DD/MM/YYYY");
        filtros.hasta = moment().format("DD/MM/YYYY");
      } else if (fechas == "ayer") {
        // AMBAS FECHAS SON AYER
        filtros.desde = moment().subtract(1,"days").format("DD/MM/YYYY");
        filtros.hasta = moment().subtract(1,"days").format("DD/MM/YYYY");        
      } else if (fechas == "quincena_actual") {
        if (moment().format("DD") <= 15) {
          // PRIMERO DE MES
          filtros.desde = moment().format("01/MM/YYYY");
        } else {
          // DESDE EL 16
          filtros.desde = moment().format("16/MM/YYYY");
        }
        // PERO LA FECHA HASTA ES SIEMPRE HASTA LA DE HOY
        filtros.hasta = moment().format("DD/MM/YYYY");
      } else if (fechas == "quincena_anterior") {
        if (moment().format("DD") <= 15) {
          // SI ESTAMOS ANTES DEL 15, LA QUINCENA ANTERIOR ES LA SEGUNDA DEL MES PASADO
          filtros.desde = moment().subtract(1,"months").format("16/MM/YYYY");
          filtros.hasta = moment().subtract(1,"months").endOf("month").format("DD/MM/YYYY");
        } else {
          // SI ESTAMOS DESPUES DEL 15, LA QUINCENA ANTERIOR ES LA PRIMERA DE ESTE MES
          filtros.desde = moment().format("01/MM/YYYY");
          filtros.hasta = moment().format("15/MM/YYYY");
        }
      }
      filtros.offset = 999999999;
      this.collection.server_api = filtros;
      this.collection.goTo(1);
    },
    
    addAll : function () {
      var self = this;
      this.$("#toque_pedidos_repartidores_tabla tbody tr").empty();

      var total = 0;
      var cantidad = 0;
      this.collection.each(function(i){
        self.addOne(i);
        total += parseFloat(i.get("costo_envio"));
        cantidad++;
      });
      this.$("#toque_pedidos_repartidores_resumen_total").html("$ "+Number(total * 0.9).format(2));
      this.$("#toque_pedidos_repartidores_resumen_cantidad").html(cantidad);
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ToquePedidosRepartidoresItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: self,
      });
      this.$("#toque_pedidos_repartidores_tabla tbody").append(view.render().el);
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ToquePedidosRepartidoresItemResultados = app.mixins.View.extend({
    template: _.template($("#toque_pedidos_repartidores_item_template").html()),
    tagName: "tr",
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);