(function ( models ) {

  models.EstadisticasPrestamos = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().startOf("month").format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      id_sucursal: 0,
      total_pagos: 0,
      total_otorgaciones: 0,
      cantidad_pagos: 0,
      cantidad_otorgaciones: 0,
      cancelacion_capital: 0,
      cancelacion_interes: 0,
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasPrestamosView = app.mixins.View.extend({

    template: _.template($("#estadisticas_prestamos_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
      "click .ver_cantidad_otorgaciones":"ver_cantidad_otorgaciones",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.parametro = "T"; //this.$("#estadisticas_prestamos_parametro").val();

      // Llenamos con los IDs de los filtros que corresponden
      /*
      var array = ["rubros","articulos","vendedores","clientes","proveedores"];
      for(var i=0;i<array.length;i++) {
          var o = array[i];
          params[o] = new Array();
          if (this.$("#estadisticas_prestamos_"+o).length == 0) continue;
          this.$("#estadisticas_prestamos_"+o+"_opciones span").each(function(ii,ee){
              params[o].push({
                  "id":$(ee).data("id"),
                  "label":$(ee).data("label"),
              });
          });
      }
      */

      params.desde = self.$("#estadisticas_prestamos_fecha_desde").val();
      params.hasta = self.$("#estadisticas_prestamos_fecha_hasta").val();
      if (self.$("#estadisticas_prestamos_sucursales").length > 0) { 
        params.id_sucursal = self.$("#estadisticas_prestamos_sucursales").val();
      }
      $.ajax({
        "url":"estadisticas/function/prestamos/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasPrestamos(r);
          self.render();

          // Renderizamos el grafico de barras
          $("#estadisticas_prestamos_graficos").empty();
          for(var i=0;i<r.grafico.length;i++) {
            var result = r.grafico[i];
            var grafico = new app.views.EstadisticasPrestamosGraficoView(result);
            $("#estadisticas_prestamos_graficos").html(grafico.el);
          }

          /*
          // Renderizamos el grafico de tortas
          self.$('#dispositivos_bar').highcharts({
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
                ['Efectivo', self.model.get("efectivo")],
                ['Tarjetas', self.model.get("tarjetas")],
                ['Cuenta corriente', self.model.get("cuenta_corriente")],
              ]
            }]
          });
          */

        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_prestamos_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_prestamos_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var pagina = $("#estadisticas_prestamos_container");
      workspace.createPDF([pagina],{
        "titulo":"Estadistica de prestamos",
      });
    },

    ver_cantidad_otorgaciones: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_prestamos_fecha_desde").val();
      params.hasta = self.$("#estadisticas_prestamos_fecha_hasta").val();
      if (self.$("#estadisticas_prestamos_sucursales").length > 0) { 
        params.id_sucursal = self.$("#estadisticas_prestamos_sucursales").val();
      }
      $.ajax({
        "url":"estadisticas/function/otorgaciones/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          var view = new app.views.PrestamosDetalleOtorgacionesView({
            model: new app.models.AbstractModel(r),
          });
          crearLightboxHTML({
            "html":view.el,
            "width":800,
            "height":500,
          });
        },
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasPrestamosGraficoView = app.mixins.View.extend({

    template: _.template($("#estadisticas_prestamos_graficos_template").html()),

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



(function ( app ) {

  app.views.PrestamosDetalleOtorgacionesView = app.mixins.View.extend({

    template: _.template($("#prestamos_detalle_otorgaciones_template").html()),

    myEvents:{
      "click .cerrar":"cerrar",
      "click .ver_link":function(e) {
        var id = $(e.currentTarget).data("id");
        window.open("app/#pres_cliente_acciones/"+id,"_blank");
      }
    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      $(this.el).html(this.template(this.model.toJSON()));
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

  });

})(app);