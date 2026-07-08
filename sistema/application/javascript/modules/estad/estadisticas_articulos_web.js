(function ( app ) {

  app.views.EstadisticasArticulosWebView = app.mixins.View.extend({

    template: _.template($("#estadisticas_articulos_web_template").html()),
            
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
      params.desde = self.$("#estadisticas_articulos_web_fecha_desde").val();
      params.hasta = self.$("#estadisticas_articulos_web_fecha_hasta").val();
      $.ajax({
        "url":"estadisticas_web/function/articulos/",
        "dataType":"json",
        "type":"post",
        "data":params,
        "type":"post",
        "success":function(r){

          var t_visitas = 0;
          var t_ventas = 0;

          // Recorremos los resultados
          self.$("#estadisticas_articulos_web_table tbody").empty();
          for(var i=0;i<r.length;i++) {
            var elem = r[i];
            t_visitas += parseFloat(elem.visitas);
            t_ventas += parseFloat(elem.venta);
            var item = new app.views.EstadisticasArticulosWebItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_articulos_web_table tbody").append(item.el);
          }
          self.$("#estadisticas_articulos_web_visitas").html(Number(t_visitas).toFixed(0));
          self.$("#estadisticas_articulos_web_ventas").html(Number(t_ventas).toFixed(0));
        },
        "error":function(r) {
          alert(r);
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_articulos_web_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_articulos_web_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var pagina = $("#estadisticas_articulos_web_container");
      workspace.createPDF([pagina],{
        "titulo":"Visitas de articulos",
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasArticulosWebItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_articulos_web_item_template').html()),
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