(function ( app ) {

  app.views.EstadisticasVentasPorProveedorView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_por_proveedor_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_ventas_por_proveedor_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_por_proveedor_fecha_hasta").val();
      params.id_sucursal = (self.$("#estadisticas_ventas_por_proveedor_sucursales").length > 0) ? self.$("#estadisticas_ventas_por_proveedor_sucursales").val() : ID_SUCURSAL;
      params.limit = 0;
      params.offset = 9999999;
      $.ajax({
        "url":"estadisticas/function/ventas_por_proveedor/",
        "dataType":"json",
        "data":params,
        "type":"get",
        "success":function(r){

          // Recorremos los resultados
          self.$("#estadisticas_ventas_por_proveedor_table tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var elem = r.results[i];
            var item = new app.views.EstadisticasVentasPorProveedorItem({
              model: new app.models.AbstractModel(elem),
              total_general: r.meta.total_final,
            });
            self.$("#estadisticas_ventas_por_proveedor_table tbody").append(item.el);
          }
          self.$("#estadisticas_ventas_por_proveedor_cantidad").html(Number(r.meta.cantidad).toFixed(0));
          self.$("#estadisticas_ventas_por_proveedor_costo_final").html("$ "+Number(r.meta.costo_final).toFixed(2));
          self.$("#estadisticas_ventas_por_proveedor_total_final").html("$ "+Number(r.meta.total_final).toFixed(2));
          var ganancia = parseFloat(r.meta.total_final) - parseFloat(r.meta.costo_final);
          self.$("#estadisticas_ventas_por_proveedor_ganancia").html("$ "+Number(ganancia).toFixed(2));
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_proveedor_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_proveedor_fecha_hasta"),fecha_hasta);
    },

    exportar: function() {
      var array = new Array();
      $("#estadisticas_ventas_por_proveedor_table tbody tr").each(function(i,e){
        array.push({
          "codigo":$(e).find("td:eq(0)").html(),
          "nombre":$(e).find("td:eq(1) span").text(),
          "cantidad":$(e).find("td:eq(2)").html(),
          "cmv":$(e).find("td:eq(3)").html(),
          "venta":$(e).find("td:eq(4)").html(),
          "ganancia":$(e).find("td:eq(5)").html(),
          "porcentaje":$(e).find("td:eq(6)").html(),
        });
      });
      var header = new Array("Codigo","Nombre","Cantidad","CMV","Venta","Ganancia","Porcentaje");
      this.exportar_excel({
        "filename":"estadisticas",
        "title":"Estadisticas por proveedor",
        "data":array,
        "header":header,
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasPorProveedorItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_ventas_por_proveedor_item_template').html()),
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      this.total_general = this.options.total_general;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = { 
        permiso: this.permiso,
        total_general: this.total_general,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );