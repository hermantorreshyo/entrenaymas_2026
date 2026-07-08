(function ( app ) {

  app.views.EstadisticasVentasPorDiaView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_por_dia_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_ventas_por_dia_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_por_dia_fecha_hasta").val();
      params.id_sucursal = (self.$("#estadisticas_ventas_por_dia_sucursales").length > 0) ? self.$("#estadisticas_ventas_por_dia_sucursales").val() : ID_SUCURSAL;
      params.id_proyecto = ID_PROYECTO;
      $.ajax({
        "url":"estadisticas/function/ventas_por_dia/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){

          var t_cantidad = 0;
          var t_efectivo = 0;
          var t_tarjetas = 0;
          var t_intereses = 0;
          var t_total = 0;
          var t_costo = 0;
          var t_ganancia = 0;

          // Recorremos los resultados
          self.$("#estadisticas_ventas_por_dia_table tbody").empty();
          for(var i=0;i<r.length;i++) {
            var elem = r[i];

            t_cantidad += parseFloat(elem.cantidad);
            t_efectivo += parseFloat(elem.efectivo);
            t_tarjetas += parseFloat(elem.tarjetas);
            t_intereses += parseFloat(elem.intereses);
            t_total += parseFloat(elem.total);
            t_costo += parseFloat(elem.costo);
            t_ganancia += parseFloat(elem.total - elem.costo);

            var item = new app.views.EstadisticasVentasPorDiaItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_ventas_por_dia_table tbody").append(item.el);
          }

          // Totalizamos
          self.$("#estadisticas_ventas_por_dia_tickets").html(Number(t_cantidad).toFixed(0));
          self.$("#estadisticas_ventas_por_dia_efectivo").html("$ "+Number(t_efectivo).toFixed(2));
          self.$("#estadisticas_ventas_por_dia_tarjetas").html("$ "+Number(t_tarjetas).toFixed(2));
          self.$("#estadisticas_ventas_por_dia_intereses").html("$ "+Number(t_intereses).toFixed(2));
          self.$("#estadisticas_ventas_por_dia_venta").html("$ "+Number(t_total).toFixed(2));
          self.$("#estadisticas_ventas_por_dia_cmv").html("$ "+Number(t_costo).toFixed(2));
          self.$("#estadisticas_ventas_por_dia_ganancia").html("$ "+Number(t_ganancia).toFixed(2));
          var t_margen = (t_costo > 0) ? ( ((t_total-t_costo)/t_costo) * 100) : 0;
          self.$("#estadisticas_ventas_por_dia_margen").html(Number(t_margen).toFixed(2)+" %");
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_dia_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_dia_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var pagina = $("#estadisticas_ventas_por_dia_container");
      workspace.createPDF([pagina],{
        "titulo":"Estadistica de ventas",
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasPorDiaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_ventas_por_dia_item_template').html()),
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );