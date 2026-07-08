(function ( models ) {

  models.EstadisticasWeb = Backbone.Model.extend({
    urlRoot: function() {
      var fecha_desde = this.get("fecha_desde").replace(/\//g,"-");
      var fecha_hasta = this.get("fecha_hasta").replace(/\//g,"-");
      return "estadisticas/function/web/"+fecha_desde+"/"+fecha_hasta+"/";
    },
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      total_sesiones: 0,
      total_usuarios: 0,
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

  app.views.EstadisticasWebView = app.mixins.View.extend({

    template: _.template($("#estadisticas_web_template").html()),
      
    myEvents: {
      "click #fecha_hasta_button":function() { this.$("#estadisticas_web_fecha_hasta").select(); },
      "click #fecha_desde_button":function() { this.$("#estadisticas_web_fecha_desde").select(); },
      "click .imprimir":function() {
        var desde = moment(this.$("#estadisticas_web_fecha_desde").val(),"DD/MM/YYYY");
        var hasta = moment(this.$("#estadisticas_web_fecha_hasta").val(),"DD/MM/YYYY");
        this.$(".printer-title").html("Web");
        if (IDIOMA == "en") {
          this.$(".printer-subtitle").html("Report from: "+desde.format("MM-DD-YYYY")+" to: "+hasta.format("MM-DD-YYYY"));
        } else {
          this.$(".printer-subtitle").html("Reporte desde: "+desde.format("DD/MM/YYYY")+" hasta: "+hasta.format("DD/MM/YYYY"));
        }
        window.print();
      },
      "change #estadisticas_web_fecha_desde":function(e){
        var self = this;
        this.model.set({
          "fecha_desde":$(e.currentTarget).val()
        });
        this.model.fetch({
          "success":function(){ self.render() }
        });
      },
      "change #estadisticas_web_fecha_hasta":function(e){
        var self = this;
        this.model.set({
          "fecha_hasta":$(e.currentTarget).val()
        });
        this.model.fetch({
          "success":function(){ self.render() }
        });
      },
      "click .ver_detalle":function(e) {
        var self = this;
        var id = $(e.currentTarget).data("id");
        var nombre = $(e.currentTarget).text();
        this.$(".div_subcategorias .panel-heading").html(nombre);
        $.ajax({
          "url":"estadisticas_web/function/paginas_por_categoria/",
          "type":"get",
          "data":{
            "id_categoria":id,
            "desde":self.$("#estadisticas_web_fecha_desde").val().replace(/\//g,"-"),
            "hasta":self.$("#estadisticas_web_fecha_hasta").val().replace(/\//g,"-"),
          },
          "dataType":"json",
          "success":function(r){
            self.render_grafico_subcategorias(r);
          }
        })
      },
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
      createdatepicker($(this.el).find("#estadisticas_web_fecha_desde"),fecha_desde);
      
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_web_fecha_hasta"),fecha_hasta);
      
      var desde_anio = fecha_desde.substr(6);
      var desde_mes = fecha_desde.substr(3,2)-1;
      var desde_dia = fecha_desde.substr(0,2);

      $('[data-toggle="tooltip"]').tooltip(); 

      if (ID_EMPRESA == 256) {
        $.ajax({
          "url":"estadisticas_web/function/categorias_paginas/",
          "type":"get",
          "data":{
            "desde":self.$("#estadisticas_web_fecha_desde").val().replace(/\//g,"-"),
            "hasta":self.$("#estadisticas_web_fecha_hasta").val().replace(/\//g,"-"),
          },
          "dataType":"json",
          "success":function(r){
            self.render_grafico_categorias(r);
          }
        });
      }
      
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
          name: ((IDIOMA == "en")?"Visits":'Visitas'),
          data: self.model.get("sesiones"),
        },{
          name: ((IDIOMA == "en")?"Unique Users":"Usuarios \u00DAnicos"),
          data: self.model.get("usuarios"),
        }]
      });   
      
    },

    sortByKeyDesc: function (array, key) {
      return array.sort(function (a, b) {
          var x = a[key]; var y = b[key];
          return ((x > y) ? -1 : ((x < y) ? 1 : 0));
      });
    },

    render_grafico_categorias: function(r) {

      console.log(r);
      console.log(r.length);

      r = this.sortByKeyDesc(r,"porcentaje");

      var datos = new Array();
      for (var i=0; i<r.length;i++) {
        var obj = r[i];
        var a = new Array();
        a.push(obj.nombre);
        a.push(obj.visitas);
        datos.push(a);

        var tr = "<tr>";
        tr+="<td><a href='javascript:void(0)' class='ver_detalle' data-id='"+obj.id+"'>"+obj.nombre+"</a></td>";
        tr+="<td>"+obj.visitas+"</td>";
        //tr+="<td>"+obj.cantidad_entradas+"</td>";
        tr+="<td>"+Number(obj.porcentaje).toFixed(2)+"%</td>";
        tr+="</tr>";
        this.$(".tabla_categorias tbody").append(tr);
      }
      console.log(datos);

      this.$('#torta_categorias').highcharts({
        chart: {
          plotBackgroundColor: null,
          plotShadow: false
        },
        title: { text: null },
        tooltip: {
          pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        //colors: ['#28b492', '#19a9d5', '#fad733'],
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: { enabled: false }
          }
        },
        series: [{
          type: 'pie',
          data: datos
        }]
      });

    },


    render_grafico_subcategorias: function(r) {

      console.log(r);
      r = this.sortByKeyDesc(r,"porcentaje");

      var datos = new Array();
      this.$(".tabla_subcategorias tbody").empty();
      for (var i=0; i<r.length;i++) {
        var obj = r[i];
        var a = new Array();
        var visitas = parseInt(obj.visitas);
        a.push(obj.nombre);
        a.push(visitas);
        datos.push(a);

        var tr = "<tr>";
        tr+="<td><a href='javascript:void(0)' class='ver_detalle' data-id='"+obj.id+"'>"+obj.nombre+"</a></td>";
        tr+="<td>"+obj.visitas+"</td>";
        //tr+="<td>"+obj.cantidad_entradas+"</td>";
        tr+="<td>"+Number(obj.porcentaje).toFixed(2)+"%</td>";
        tr+="</tr>";
        this.$(".tabla_subcategorias tbody").append(tr);
      }
      console.log(datos);

      this.$('#torta_subcategorias').highcharts({
        chart: {
          plotBackgroundColor: null,
          plotShadow: false
        },
        title: { text: null },
        tooltip: {
          pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        //colors: ['#28b492', '#19a9d5', '#fad733'],
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: { enabled: false }
          }
        },
        series: [{
          type: 'pie',
          data: datos
        }]
      });

      this.$(".div_subcategorias").show();

    },

    
  });
})(app);
