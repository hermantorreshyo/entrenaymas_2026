(function ( app ) {

  app.views.EstadisticasVentasPorDepartamentoView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_por_departamento_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",

      "click .sorting":function(e) {
        var asc = $(e.currentTarget).hasClass("sorting_asc");
        var desc = $(e.currentTarget).hasClass("sorting_desc");
        $(".sorting").removeClass("sorting_asc");
        $(".sorting").removeClass("sorting_desc");
        if (asc) $(e.currentTarget).addClass("sorting_desc");
        else if (desc) $(e.currentTarget).addClass("sorting_asc");
        else $(e.currentTarget).addClass("sorting_desc");

        var sort_by = $(e.currentTarget).data("sort-by");
        if (sort_by == undefined) return;
        var sort = (desc)?"desc":"asc";
        window.estadisticas_ventas_por_departamento_order_by = sort_by;
        window.estadisticas_ventas_por_departamento_order = sort;
        this.buscar();
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      if (typeof window.estadisticas_ventas_por_departamento_order == "undefined") window.estadisticas_ventas_por_departamento_order = "cantidad";
      if (typeof window.estadisticas_ventas_por_departamento_order_by == "undefined") window.estadisticas_ventas_por_departamento_order_by = "desc";
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_ventas_por_departamento_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_por_departamento_fecha_hasta").val();
      params.id_sucursal = (self.$("#estadisticas_ventas_por_departamento_sucursales").length > 0) ? self.$("#estadisticas_ventas_por_departamento_sucursales").val() : ID_SUCURSAL;
      params.limit = 0;
      params.offset = 9999999;
      params.order = window.estadisticas_ventas_por_departamento_order;
      params.order_by = window.estadisticas_ventas_por_departamento_order_by;
      console.log(params);
      $.ajax({
        "url":"estadisticas/function/ventas_por_departamento/",
        "dataType":"json",
        "data":params,
        "type":"get",
        "success":function(r){

          // Recorremos los resultados
          self.$("#estadisticas_ventas_por_departamento_table tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var elem = r.results[i];
            var item = new app.views.EstadisticasVentasPorDepartamentoItem({
              model: new app.models.AbstractModel(elem),
              total_general: r.meta.total_final,
            });
            self.$("#estadisticas_ventas_por_departamento_table tbody").append(item.el);
          }
          self.$("#estadisticas_ventas_por_departamento_cantidad").html(Number(r.meta.cantidad).toFixed(0));
          self.$("#estadisticas_ventas_por_departamento_total_final").html("$ "+Number(r.meta.total_final).toFixed(2));
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_departamento_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_departamento_fecha_hasta"),fecha_hasta);
    },

    exportar: function() {
      var array = new Array();
      $("#estadisticas_ventas_por_departamento_table tbody tr").each(function(i,e){
        array.push({
          "codigo":$(e).find("td:eq(0)").html(),
          "nombre":$(e).find("td:eq(1) span").text(),
          "cantidad":$(e).find("td:eq(2)").html(),
          "venta":$(e).find("td:eq(3)").html(),
          "porcentaje":$(e).find("td:eq(4)").html(),
        });
      });
      var header = new Array("Codigo","Nombre","Cantidad","Venta","Porcentaje");
      this.exportar_excel({
        "filename":"estadisticas",
        "title":"Estadisticas por departamento",
        "data":array,
        "header":header,
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasPorDepartamentoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_ventas_por_departamento_item_template').html()),
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