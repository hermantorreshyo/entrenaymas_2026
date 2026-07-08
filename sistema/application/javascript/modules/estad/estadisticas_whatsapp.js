(function ( models ) {

  models.EstadisticasWhatsapp = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      total_clicks: 0,
      consultas_fuera_linea: 0,
      promedio_por_dia: 0,
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasWhatsappView = app.mixins.View.extend({

    template: _.template($("#estadisticas_whatsapp_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      window.estadisticas_whatsapp_id_usuario = (typeof window.estadisticas_whatsapp_id_usuario != "undefined") ? window.estadisticas_whatsapp_id_usuario : 0;
      this.render();
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.parametro = "T"; //this.$("#estadisticas_whatsapp_parametro").val();
      params.desde = self.$("#estadisticas_whatsapp_fecha_desde").val();
      params.hasta = self.$("#estadisticas_whatsapp_fecha_hasta").val();
      window.estadisticas_whatsapp_id_usuario = self.$("#estadisticas_whatsapp_usuarios").val();
      params.id_usuario = window.estadisticas_whatsapp_id_usuario;
      $.ajax({
        "url":"estadisticas/function/whatsapp/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasWhatsapp(r);
          self.render();

          // Renderizamos el grafico de barras
          $("#estadisticas_whatsapp_graficos").empty();
          for(var i=0;i<r.grafico.length;i++) {
            var result = r.grafico[i];
            var grafico = new app.views.EstadisticasWhatsappGraficoView(result);
            $("#estadisticas_whatsapp_graficos").html(grafico.el);
          }
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_whatsapp_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_whatsapp_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var desde = moment(this.$("#estadisticas_whatsapp_fecha_desde").val(),"DD/MM/YYYY");
      var hasta = moment(this.$("#estadisticas_whatsapp_fecha_hasta").val(),"DD/MM/YYYY");
      this.$(".printer-title").html("Whatsapp");
      if (IDIOMA == "en") {
        this.$(".printer-subtitle").html("Report from: "+desde.format("MM-DD-YYYY")+" to: "+hasta.format("MM-DD-YYYY"));
      } else {
        this.$(".printer-subtitle").html("Reporte desde: "+desde.format("DD/MM/YYYY")+" hasta: "+hasta.format("DD/MM/YYYY"));
      }
      window.print();
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasWhatsappGraficoView = app.mixins.View.extend({

      template: _.template($("#estadisticas_whatsapp_graficos_template").html()),

      myEvents:{
      },

      initialize: function(options) {
          _.bindAll(this);
          var self = this;
          this.options = options;
          $(this.el).html(this.template(this.options));

          var desde_anio = this.options.desde.substr(6);
          var desde_mes = this.options.desde.substr(3,2)-1;
          var desde_dia = this.options.desde.substr(0,2);

          if (this.options.intervalo == "W") {
              var plotOptionsSeries = {
                  pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
                  pointInterval: 24 * 3600 * 1000 * 7,
              };
          } else if (this.options.intervalo == "D") {
              var plotOptionsSeries = {
                  pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
                  pointInterval: 24 * 3600 * 1000,
              };
          } else if (this.options.intervalo == "M") {
              var plotOptionsSeries = {
                  pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
                  pointIntervalUnit: 'month',
              };
          }
          
          // VISION GENERAL
          this.$('.grafico').highcharts({
              chart: {
                  type: 'column',
                  zoomType: 'x'
              },
              title: { text: null },
              legend: {
                  floating: true,
                  align: "right",
                  verticalAlign: "top",
              },
              colors: ['#28b492','#19a9d5'],
              xAxis: {
                  type: 'datetime',
                  /*dateTimeLabelFormats: {
                      day: '%b %e',
                      week: '%b %e'
                  } */           
              },
              yAxis: {
                  allowDecimals: false,
                  gridLineColor: '#f9f9f9',
                  title: {
                      text: null
                  }
              },
              tooltip: {
                  dateTimeLabelFormats: {
                      day: '%e/%m/%Y',
                      week: '%e/%m/%Y',
                  }
              },
              plotOptions: {
                  area: {
                      marker: {
                          enabled: false,
                          symbol: 'circle',
                          radius: 2,
                          states: {
                              hover: { enabled: true }
                          }
                      }
                  },
                  series: plotOptionsSeries
              },
              series: self.options.series
          });   
      },

  });

})(app);