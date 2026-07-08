(function ( models ) {

  models.EstadisticasTarjetas = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      id_punto_venta: -1,
      id_sucursal: 0,
      porcentaje_venta_tarjetas: 0,
      tarjeta_promedio: 0,
      total_sin_interes: 0,
      total_con_interes: 0,
      interes: 0,
      cantidad_operaciones: 0,
      operaciones_por_tarjeta: [],
      listado: [],
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasTarjetasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_tarjetas_template").html()),
            
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
      params.parametro = "T"; //this.$("#estadisticas_tarjetas_parametro").val();
      params.desde = self.$("#estadisticas_tarjetas_fecha_desde").val();
      params.hasta = self.$("#estadisticas_tarjetas_fecha_hasta").val();
      if (self.$("#estadisticas_tarjetas_puntos_venta").length > 0) { 
        params.id_punto_venta = self.$("#estadisticas_tarjetas_puntos_venta").val();
      }
      params.id_sucursal = (self.$("#estadisticas_tarjetas_sucursales").length > 0) ? self.$("#estadisticas_tarjetas_sucursales").val() : ID_SUCURSAL;
      params.id_proyecto = ID_PROYECTO;

      $.ajax({
        "url":"estadisticas/function/tarjetas/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasTarjetas(r);
          self.render();

          // Renderizamos el grafico de tortas
          self.$('#estadisticas_tarjetas_graficos').highcharts({
            chart: {
              plotBackgroundColor: null,
              plotShadow: false
            },
            title: { text: null },
            tooltip: {
              pointFormat: '<b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
              pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: { enabled: false }
              }
            },
            series: [{
              type: 'pie',
              data: r.grafico_tarjetas
            }]
          });

        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_tarjetas_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_tarjetas_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var desde = moment(this.$("#estadisticas_tarjetas_fecha_desde").val(),"DD/MM/YYYY");
      var hasta = moment(this.$("#estadisticas_tarjetas_fecha_hasta").val(),"DD/MM/YYYY");
      if (IDIOMA == "en") {
        this.$(".printer-title").html("Sales");
        this.$(".printer-subtitle").html("Report from: "+desde.format("MM-DD-YYYY")+" to: "+hasta.format("MM-DD-YYYY"));
      } else {
        this.$(".printer-title").html("Estadisticas de tarjetas");
        this.$(".printer-subtitle").html("Reporte desde: "+desde.format("DD/MM/YYYY")+" hasta: "+hasta.format("DD/MM/YYYY"));
      }
      window.print();
    },
        
  });
})(app);