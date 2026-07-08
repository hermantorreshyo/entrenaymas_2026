// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ComisionesVendedores = Backbone.Model.extend({
    urlRoot: function() {
      var s = (control.check("viajes")>0) ? "vendedores/function/comisiones_viajes" : "vendedores/function/comisiones";
      s=s+"/"+this.get("fecha_desde");
      s=s+"/"+this.get("fecha_hasta");
      return s;
    },
    defaults: {
      "fecha_desde": 0,
      "fecha_hasta": 0,
      "datos": new Array()
    },
  });
	    
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ComisionesVendedoresResultados = app.mixins.View.extend({

    template: _.template($("#comisiones_vendedores_resultados_template").html()),
        
    myEvents: {
      "click .buscar": "buscar",
      "click .exportar":"exportar",
    },
        
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      // Toma un mes anterior, el mes actual, y el mes siguiente
      var fecha_desde = moment().subtract(1,'month').toDate();
      var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
      fecha_desde = new Date(y, m, 1);
      fecha_hasta = new Date(y, m+1, 0);                      
      createdatepicker($(this.el).find("#comisiones_vendedores_desde"),fecha_desde);
      createdatepicker($(this.el).find("#comisiones_vendedores_hasta"),fecha_hasta);
      this.buscar();
      return this;
    },

    buscar : function() {
      var self = this;
      var fecha_desde = $(this.el).find("#comisiones_vendedores_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#comisiones_vendedores_hasta").val().replace(/\//g,"-");

      if (isEmpty(fecha_desde)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_desde").focus();
        return;                
      }
      if (isEmpty(fecha_hasta)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_hasta").focus();
        return;                
      }
      this.model.set({
        "fecha_desde": fecha_desde,
        "fecha_hasta": fecha_hasta,
      });
      this.model.fetch({
        "success":function(modelo) {
          self.mostrar_resultados(self.model);
        }
      });
    },
        
    mostrar_resultados: function(model) {
      $(this.el).find(".tbody").empty();

      // Primero calculamos los totales
      var total = 0;
      var comision = 0;
      var comprobantes = 0;
      for(i=0;i<model.get("datos").length;i++) {
        var m = model.get("datos")[i];
        total += parseFloat(m.total);
        comision += parseFloat(m.total_comision);
        comprobantes += parseFloat(m.total_facturas);
      }
      
      // Agregamos cada item a la tabla
      for(i=0;i<model.get("datos").length;i++) {
        var m = model.get("datos")[i];
        var Item = Backbone.Model.extend();
        var modelo = new Item();
        modelo.set(m);

        // Calculamos la proporcion de venta de ese vendedor
        var porc_venta = (total>0) ? (m.total / total)*100 : 0;
        modelo.set({"porc_venta":porc_venta});

        var item = new app.views.ComisionesVendedoresItemResultados({
          model: modelo
        });
        $(this.el).find(".tbody").append(item.el);
      }
      this.$("#comisiones_vendedores_total").html("$ "+Number(total).toFixed(2));
      this.$("#comisiones_vendedores_comision").html("$ "+Number(comision).toFixed(2));
      this.$("#comisiones_vendedores_comprobantes").html(Number(comprobantes).toFixed(0));
      var ticket_promedio = (comprobantes>0) ? (total / comprobantes) : 0;
      this.$("#comisiones_vendedores_promedio").html("$ "+Number(ticket_promedio).toFixed(2));
    },
    
    exportar: function() {
      var self = this;
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });
      var fechas = $("#comisiones_vendedores_desde").val()+" - "+$("#comisiones_vendedores_hasta").val();
      this.exportar_excel({
        "filename":"comisiones",
        "title":"Comisiones de Vendedores",
        "date":fechas,
        "data":self.model.get("datos"),
        "header":header,
      });
    },
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ComisionesVendedoresItemResultados = Backbone.View.extend({
    template: _.template($("#comisiones_vendedores_item_resultados_template").html()),
    tagName: "tr",
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);
