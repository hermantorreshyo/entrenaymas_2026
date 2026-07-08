(function ( models ) {

  models.MantEstadisticas = Backbone.Model.extend({
    urlRoot: function() {
      var fecha_desde = this.get("fecha_desde").replace(/\//g,"-");
      var fecha_hasta = this.get("fecha_hasta").replace(/\//g,"-");
      return "mantenimientos/function/estadisticas/"+fecha_desde+"/"+fecha_hasta+"/";
    },
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      total_sesiones: 0,
      total_usuarios_nuevos: 0,
      total_usuarios_recurrentes: 0,
      usuarios_nuevos: [],
      usuarios_recurrentes: [],
      paginas_vistas: 0,
      porcentaje_rebote: 0,
      ciudades: [],
      paginas_mas_vistas: [],
      fuentes: [],
      desktop: 0,
      mobile: 0,
      tablet: 0,
      error: "",
    },
  });
	  
})( app.models );

(function ( app ) {

  app.views.MantEstadisticasView = app.mixins.View.extend({

    template: _.template($("#mant_estadisticas_template").html()),
      
    myEvents: {
      "click #fecha_hasta_button":function() { this.$("#mant_estadisticas_fecha_hasta").select(); },
      "click #fecha_desde_button":function() { this.$("#mant_estadisticas_fecha_desde").select(); },
      "change #mant_estadisticas_fecha_desde":function(e){
        var self = this;
        this.model.set({
          "fecha_desde":$(e.currentTarget).val()
        });
        this.model.fetch({
          "success":function(){ self.render() }
        });
      },
      "change #mant_estadisticas_fecha_hasta":function(e){
        var self = this;
        this.model.set({
          "fecha_hasta":$(e.currentTarget).val()
        });
        this.model.fetch({
          "success":function(){ self.render() }
        });
      }
    },
    
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      $(this.el).html(this.template(this.model.toJSON()));
      this.model.fetch({
        "success":function(){ self.render() }
      });
    },
    
    render: function() {
      
      $(this.el).html(this.template(this.model.toJSON()));
      
      var self = this;
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#mant_estadisticas_fecha_desde"),fecha_desde);
      
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#mant_estadisticas_fecha_hasta"),fecha_hasta);
      
      var desde_anio = fecha_desde.substr(6);
      var desde_mes = fecha_desde.substr(3,2)-1;
      var desde_dia = fecha_desde.substr(0,2);
      
      // DISPOSITIVOS
      this.$('#dispositivos_bar').highcharts({
        chart: {
          plotBackgroundColor: null,
          plotShadow: false
        },
        title: { text: null },
        tooltip: {
          pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        colors: ['#28b492', '#19a9d5', '#fad733'],
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: { enabled: false }
          }
        },
        series: [{
          type: 'pie',
          data: [
            ['Escritorio', self.model.get("desktop")],
            ['Moviles', self.model.get("mobile")],
            ['Tablets', self.model.get("tablet")],
          ]
        }]
      });
      
      
      // USUARIOS NUEVOS VS USUARIOS RECURRENTES
      /*
      this.$('#visitas_bar').highcharts({
        title: { text: null },
        legend: {
          floating: true,
          align: "right",
          verticalAlign: "top",
          itemStyle: {
            color: "#F0F0F0"
          }
        },
        chart: {
          backgroundColor: "#847abf"
        },
        colors: ['#FFFFFF','#face00'],
        xAxis: {
          type: 'datetime',
          dateTimeLabelFormats: {
            day: '%b %e',
            week: '%b %e'
          },
          labels: {
            style: {
              color: "#F0F0F0"
            }
          },            
        },
        yAxis: {
          allowDecimals: false,
          gridLineColor: '#847abf',
          title: {
            text: null
          },
          labels: {
            style: {
              color: "#F0F0F0"
            }
          },      
          min: 0
        },
        tooltip: {
          dateTimeLabelFormats: {
            day: '%e/%m/%Y',
            week: '%e/%m/%Y',
          }
        },
        plotOptions: {
          series: {
            pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
            pointInterval: 24 * 3600 * 1000,
          }
        },
        series: [{
          name: 'Nuevos',
          data: self.model.get("usuarios_nuevos"),
        },{
          name: 'Recurrentes',
          data: self.model.get("usuarios_recurrentes"),
        }]
      });
      */
      
      // VISION GENERAL
      this.$('#vision_general_bar').highcharts({
        chart: {
          type: 'area',
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
          dateTimeLabelFormats: {
            day: '%b %e',
            week: '%b %e'
          }      
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
          series: {
            pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
            pointInterval: 24 * 3600 * 1000,
          }
        },
        series: [{
          name: 'Preventivo',
          data: self.model.get("sesiones"),
        },{
          name: "Correctivo",
          data: self.model.get("usuarios"),
        }]
      });   
      
    },
    
  });
})(app);
