(function ( models ) {

  models.EstadisticasPagos = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_sucursal: 0,
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      total_pagos: 0,
      cantidad_operaciones: 0,
      cheques_emitidos: 0,
      total_efectivo: 0,
      ordenes_pago: [],
      comprobantes_efectivo: [],
      cheques_terceros: 0,
      cheques_por_cobrar: [],
      transferencias: 0,
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasPagosView = app.mixins.View.extend({

    template: _.template($("#estadisticas_pagos_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .ver_cheque":function(e){
        var self = this;
        var id = $(e.currentTarget).data("id");
        var cheque = new app.models.Cheque({
          "id":id
        });
        cheque.fetch({
          "success":function(){
            var view = new app.views.ChequeEditView({
              model: cheque,
            });
            crearLightboxHTML({
              "html":view.el,
              "width":450,
              "height":300,
            });
          }
        })
      },
      "click .exportar_pagos":function() {
        var self = this;
        var header = new Array("Proveedor","Sucursal","Fecha","Efectivo","Cheque","Total");
        var array = new Array();
        $(".estadisticas_pagos_table tbody tr").each(function(i,e){
          array.push({
            "proveedor":$(e).find("td:eq(0)").find("a").text(),
            "sucursal":$(e).find("td:eq(1)").html(),
            "fecha":$(e).find("td:eq(2)").html(),
            "efectivo":$(e).find("td:eq(3)").html(),
            "cheques":$(e).find("td:eq(4)").html(),
            "total":$(e).find("td:eq(5)").html(),
          });
        });
        this.exportar_excel({
          "filename":"ordenes_pago",
          "title":"Ordenes de Pago",
          "date":"Fecha: "+$("#estadisticas_pagos_fecha_desde").val()+" - "+$("#estadisticas_pagos_fecha_hasta").val(),
          "data":array,
          "header":header,
        }); 
      },
      "click .exportar_cheques":function() {
        var self = this;
        var header = new Array("Proveedor","Sucursal","Banco","Numero","Fecha Cobro","Monto");
        var array = new Array();
        $(".estadisticas_pagos_cheques_por_cobrar tbody tr").each(function(i,e){
          array.push({
            "proveedor":$(e).find("td:eq(0)").find("span").text(),
            "sucursal":$(e).find("td:eq(1)").html(),
            "banco":$(e).find("td:eq(2)").html(),
            "numero":$(e).find("td:eq(3)").html(),
            "fecha_cobro":$(e).find("td:eq(4)").html(),
            "monto":$(e).find("td:eq(5)").html(),
          });
        });
        this.exportar_excel({
          "filename":"cheques",
          "title":"Cheques por cancelar",
          "date":"Fecha: "+$("#estadisticas_pagos_fecha_desde").val()+" - "+$("#estadisticas_pagos_fecha_hasta").val(),
          "data":array,
          "header":header,
        }); 
      },
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
      params.parametro = "T";
      params.desde = self.$("#estadisticas_pagos_fecha_desde").val(),
      params.hasta = self.$("#estadisticas_pagos_fecha_hasta").val(),
      params.id_sucursal = ((self.$("#estadisticas_pagos_sucursales").length > 0) ? self.$("#estadisticas_pagos_sucursales").val() : 0),
      params.id_proyecto = ID_PROYECTO;
      $.ajax({
        "url":"estadisticas/function/pagos/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasPagos(r);
          self.render();

          // Renderizamos el grafico de barras
          /*
          $("#estadisticas_pagos_graficos").empty();
          for(var i=0;i<r.grafico.length;i++) {
            var result = r.grafico[i];
            var grafico = new app.views.EstadisticasPagosGraficoView(result);
            $("#estadisticas_pagos_graficos").html(grafico.el);
          }
          */

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
            colors: ['#28b492', '#19a9d5', '#fad733', '#e06559'],
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
                ['Efectivo', self.model.get("total_efectivo")],
                ['Cheques propios', self.model.get("cheques_emitidos")],
                ['Cheques de terceros', self.model.get("cheques_terceros")],
                ['Depositos/Transf.', self.model.get("transferencias")],
              ]
            }]
          });

        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var self = this;
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_pagos_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_pagos_fecha_hasta"),fecha_hasta);
    },
        
  });
})(app);