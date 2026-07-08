(function ( app ) {

  app.views.EstadisticasArticulosSucursalesView = app.mixins.View.extend({

    template: _.template($("#estadisticas_articulos_sucursales_template").html()),
            
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
      params.desde = self.$("#estadisticas_articulos_sucursales_fecha_desde").val();
      params.hasta = self.$("#estadisticas_articulos_sucursales_fecha_hasta").val();
      params.offset = (!isEmpty(self.$("#estadisticas_articulos_sucursales_cantidad").val())) ? self.$("#estadisticas_articulos_sucursales_cantidad").val() : 50;
      params.id_sucursal_1 = self.$("#estadisticas_articulos_sucursales_1").val();
      params.id_sucursal_2 = self.$("#estadisticas_articulos_sucursales_2").val();
      if (params.id_sucursal_1 == params.id_sucursal_2) {
        alert("Por favor compare dos sucursales distintas.");
        return;
      }
      $.ajax({
        "url":"estadisticas/function/articulos_sucursales/",
        "dataType":"json",
        "timeout":0,
        "data":params,
        "type":"get",
        "success":function(r){
          self.$("#estadisticas_articulos_sucursales_table_1 tbody").empty();
          self.$("#estadisticas_articulos_sucursales_table_2 tbody").empty();

          for(var i=0;i<r.results_1.length;i++) {
            var elem = r.results_1[i];
            var item = new app.views.EstadisticasArticulosSucursalesItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_articulos_sucursales_table_1 tbody").append(item.el);
          }
          for(var i=0;i<r.results_2.length;i++) {
            var elem = r.results_2[i];
            var item = new app.views.EstadisticasArticulosSucursalesItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_articulos_sucursales_table_2 tbody").append(item.el);
          }

        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_articulos_sucursales_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_articulos_sucursales_fecha_hasta"),fecha_hasta);
    },

    exportar: function() {
      var sucursal_1 = self.$("#estadisticas_articulos_sucursales_1 option:selected").text();
      var sucursal_2 = self.$("#estadisticas_articulos_sucursales_2 option:selected").text();
      var desde = self.$("#estadisticas_articulos_sucursales_fecha_desde").val();
      var hasta = self.$("#estadisticas_articulos_sucursales_fecha_hasta").val();
      var array = new Array({
        "articulo_1":sucursal_1,
        "cantidad_1":"",
        "total_1":"",
        "ganancia_1":"",
        "articulo_2":sucursal_2,
        "cantidad_2":"",
        "total_2":"",
        "ganancia_2":"",
      });
      $("#estadisticas_articulos_sucursales_table_1 tbody tr").each(function(i,e){
        array.push({
          "articulo_1":$(e).find("td:eq(0) span").text(),
          "cantidad_1":$(e).find("td:eq(1)").html(),
          "total_1":$(e).find("td:eq(2)").html(),
          "ganancia_1":$(e).find("td:eq(3)").html(),
          "articulo_2":"",
          "cantidad_2":"",
          "total_2":"",
          "ganancia_2":"",
        });
      });
      $("#estadisticas_articulos_sucursales_table_2 tbody tr").each(function(i,e){
        array[i+1].articulo_2 = $(e).find("td:eq(0) span").text();
        array[i+1].cantidad_2 = $(e).find("td:eq(1)").html();
        array[i+1].total_2 = $(e).find("td:eq(2)").html();
        array[i+1].ganancia_2 = $(e).find("td:eq(3)").html();
      });
      var header = new Array("Articulo","Cantidad","Total","Ganancia","Articulo","Cantidad","Total","Ganancia");
      this.exportar_excel({
        "filename":"articulos_sucursales",
        "title":"Ventas por sucursal",
        "data":array,
        "date":"Periodo: "+desde+" a "+hasta,
        "header":header,
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasArticulosSucursalesItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_articulos_sucursales_item_template').html()),
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = { 
        permiso: this.permiso,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );