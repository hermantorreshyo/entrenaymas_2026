(function ( models ) {

  models.EstadisticasCobranzas = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_sucursal: 0,
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().subtract(1,'months').format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      pagos: [],
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasCobranzasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_cobranzas_template").html()),
            
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
      params.parametro = "T";
      params.desde = self.$("#estadisticas_cobranzas_fecha_desde").val(),
      params.hasta = self.$("#estadisticas_cobranzas_fecha_hasta").val(),
      //params.id_sucursal = ((self.$("#estadisticas_cobranzas_sucursales").length > 0) ? self.$("#estadisticas_cobranzas_sucursales").val() : 0),
      params.id_proyecto = ID_PROYECTO;
      $.ajax({
        "url":"estadisticas/function/cobranzas/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasCobranzas(r);
          self.render();
        },
      });
    },

    exportar: function() {
      var array = new Array();
      this.$("#estadisticas_cobranzas_table tbody tr").each(function(i,e){
        array.push({
          "fecha":$(e).find("td:eq(0)").text(),
          "cliente":$(e).find("td:eq(1) a").text(),
          "efectivo":$(e).find("td:eq(2)").text().replace("$ ",""),
          "cheques":$(e).find("td:eq(3)").text().replace("$ ",""),
          "depositos":$(e).find("td:eq(4)").text().replace("$ ",""),
          "tarjetas":$(e).find("td:eq(5)").text().replace("$ ",""),
          "total":$(e).find("td:eq(6)").text().replace("$ ",""),
        });
      });
      var header = new Array("Fecha","Cliente","Efectivo","Cheques","Depositos","Tarjetas","Total");
      this.exportar_excel({
        "filename":"cobranzas",
        "title":"Cobranzas",
        "data":array,
        "header":header,
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var self = this;
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_cobranzas_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_cobranzas_fecha_hasta"),fecha_hasta);
    },
        
  });
})(app);