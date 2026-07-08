(function ( models ) {

  models.EstadisticasPublicidades = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      total: 0,
      promedio_por_dia: 0,
    },
  });
	    
})( app.models );


(function ( app ) {

  app.views.EstadisticasPublicidadesView = app.mixins.View.extend({

    template: _.template($("#estadisticas_publicidades_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
      "change #estadisticas_publicidades_campanias":function(){
        var id_campania = $("#estadisticas_publicidades_campanias").val();
        if ($("#estadisticas_publicidades_piezas").data('select2')) {
          $("#estadisticas_publicidades_piezas").select2('destroy');
        }
        new app.mixins.Select({
          modelClass: app.models.Pieza,
          url: "campanias/function/ver_piezas/?id_campania="+id_campania,
          firstOptions: ["<option value='0'>All</option>"],
          multiple: true,
          render: "#estadisticas_publicidades_piezas",
          onComplete:function(c) {
            crear_select2("estadisticas_publicidades_piezas",{
              "placeholder":"Filtrar por pieza",
            });
          }
        });
      }
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.grafico = options.grafico;
      this.render();
      //this.buscar();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.parametro = "T"; //this.$("#estadisticas_publicidades_parametro").val();
      params.desde = self.$("#estadisticas_publicidades_fecha_desde").val();
      params.hasta = self.$("#estadisticas_publicidades_fecha_hasta").val();
      if (this.$("#estadisticas_publicidades_campanias").length > 0) {
        params.id_campania = self.$("#estadisticas_publicidades_campanias").val();
        if (params.id_campania == 0) {
          alert("Please select a company");
          return;
        }
      }
      if (this.$("#estadisticas_publicidades_clientes").length > 0) {
        params.id_cliente = self.$("#estadisticas_publicidades_clientes").val();
        if (params.id_cliente == 0) {
          alert("Please select a company");
          return;
        }
      }
      var piezas = self.$("#estadisticas_publicidades_piezas").val();
      if (piezas != null && piezas.length > 0) params.ids_piezas = piezas.join(",");
      $.ajax({
        "url":"estadisticas_web/function/publicidades/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          var view = new app.views.EstadisticasPublicidadesGraficoView({
            model: new app.models.EstadisticasPublicidades(r),
          })
          self.$("#estadisticas_publicidades_resultado").html(view.el);
        },
      });

      // En MILLING traemos ademas otros datos
      if (MILLING == 1) {
        var id_cliente = this.$("#estadisticas_publicidades_campanias option:selected").data("id_cliente");
        console.log(id_cliente);
      }
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_publicidades_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_publicidades_fecha_hasta"),fecha_hasta);

      // Creamos el select
      if (this.$("#estadisticas_publicidades_campanias").length > 0) {
        new app.mixins.Select({
          modelClass: app.models.Campania,
          url: "campanias/",
          fields: ["id_cliente"],
          firstOptions: ["<option value='0'>Select a company</option>"],
          render: "#estadisticas_publicidades_campanias",
          onComplete:function(c) {
            crear_select2("estadisticas_publicidades_campanias",{
              "placeholder":"Filtrar por campaña",
            });
          }                    
        });
      }

      if (this.$("#estadisticas_publicidades_clientes").length > 0) {
        new app.mixins.Select({
          modelClass: app.models.Cliente,
          url: "clientes/?tipo=0",
          render: "#estadisticas_publicidades_clientes",
          firstOptions: ["<option value='0'>Customer</option>"],
        });
      }

    },

    imprimir: function() {
      var desde = moment(this.$("#estadisticas_publicidades_fecha_desde").val(),"DD/MM/YYYY");
      var hasta = moment(this.$("#estadisticas_publicidades_fecha_hasta").val(),"DD/MM/YYYY");
      var empresa = this.$("#estadisticas_publicidades_clientes option:selected").text();
      if (IDIOMA == "en") {
        this.$(".printer-title").html("Advertising");
        this.$(".printer-subtitle").html("Report of <b>"+empresa+"</b> from: "+desde.format("MM-DD-YYYY")+" to: "+hasta.format("MM-DD-YYYY"));
      } else {
        this.$(".printer-title").html("Publicidades");
        this.$(".printer-subtitle").html("Reporte de <b>"+empresa+"</b> desde: "+desde.format("DD/MM/YYYY")+" hasta: "+hasta.format("DD/MM/YYYY"));
      }
      window.print();
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasPublicidadesGraficoView = app.mixins.View.extend({

    template: _.template($("#estadisticas_publicidades_graficos_template").html()),

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      $(this.el).html(this.template(self.model.toJSON()));
      //self.render_grafico();
    },

    render_grafico: function() {
      var grafico = this.model.get("grafico")[0];
      console.log(grafico);
      var desde_anio = grafico.desde.substr(6);
      var desde_mes = grafico.desde.substr(3,2)-1;
      var desde_dia = grafico.desde.substr(0,2);

      if (grafico.intervalo == "W") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointInterval: 24 * 3600 * 1000 * 7,
        };
      } else if (grafico.intervalo == "D") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointInterval: 24 * 3600 * 1000,
        };
      } else if (grafico.intervalo == "M") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointIntervalUnit: 'month',
        };
      }
      
      // VISION GENERAL
      this.$('.grafico').highcharts({
        chart: {
          type: 'column',
          zoomType: 'x',
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
        series: grafico.series
      });
      setInterval(function() {
        $(window).resize();
      }, 1);
    },

  });

})(app);