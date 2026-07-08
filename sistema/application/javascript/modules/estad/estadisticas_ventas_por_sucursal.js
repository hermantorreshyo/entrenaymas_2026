(function ( app ) {

  app.views.EstadisticasVentasPorSucursalView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_por_sucursal_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",
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
      params.desde = self.$("#estadisticas_ventas_por_sucursal_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_por_sucursal_fecha_hasta").val();
      $.ajax({
        "url":"estadisticas/function/ventas_por_sucursal/",
        "dataType":"json",
        "timeout":0,
        "data":params,
        "type":"get",
        "success":function(r){

          // Recorremos los resultados
          self.$("#estadisticas_ventas_por_sucursal_table tbody").empty();

          var total = 0;
          var cantidad = 0;
          var costo = 0;
          var ganancia = 0;
          var oferta = 0;
          var descuento = 0;
          for(var i=0;i<r.results.length;i++) {
            var elem = r.results[i];
            total += parseFloat(elem.total);
            cantidad += parseFloat(elem.cantidad);
            costo += parseFloat(elem.costo);
            ganancia += parseFloat(elem.ganancia);
            oferta += parseFloat(elem.oferta);
            descuento += parseFloat(elem.descuento);
            var item = new app.views.EstadisticasVentasPorSucursalItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_ventas_por_sucursal_table tbody").append(item.el);
          }
          self.$("#estadisticas_ventas_por_sucursal_total").html(Number(total).toFixed(2));
          self.$("#estadisticas_ventas_por_sucursal_cantidad").html(Number(cantidad).toFixed(2));
          self.$("#estadisticas_ventas_por_sucursal_costo").html(Number(costo).toFixed(2));
          self.$("#estadisticas_ventas_por_sucursal_ganancia").html(Number(ganancia).toFixed(2));
          self.$("#estadisticas_ventas_por_sucursal_oferta").html(Number(oferta).toFixed(2));
          self.$("#estadisticas_ventas_por_sucursal_descuento").html(Number(descuento).toFixed(2));
          self.$('[data-toggle="tooltip"]').tooltip(); 
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_sucursal_fecha_desde"),fecha_desde);
      var fecha_hasta = new Date();
      createdatepicker($(this.el).find("#estadisticas_ventas_por_sucursal_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {

      var desde = self.$("#estadisticas_ventas_por_sucursal_fecha_desde").val();
      var hasta = self.$("#estadisticas_ventas_por_sucursal_fecha_hasta").val();

      var form = document.createElement("form");
      form.setAttribute("method","post");
      form.setAttribute("target","_blank");
      form.setAttribute("action","exportar/table_to_report/");

      var html = this.$("#estadisticas_ventas_por_sucursal_table").wrap("<p/>").parent().html();
      $(html).find("table").addClass("tabla");

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","tabla");
      hidden.setAttribute("value",html);
      form.appendChild(hidden);

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","titulo");
      hidden.setAttribute("value","VENTAS POR SUCURSAL");
      form.appendChild(hidden);

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","fechas");
      hidden.setAttribute("value",desde+" - "+hasta);
      form.appendChild(hidden);

      $(form).css("display","none");
      document.body.appendChild(form);
      form.submit();
    },

    exportar: function() {
      var desde = self.$("#estadisticas_ventas_por_sucursal_fecha_desde").val();
      var hasta = self.$("#estadisticas_ventas_por_sucursal_fecha_hasta").val();
      var array = new Array();
      $("#estadisticas_ventas_por_sucursal_table tbody tr").each(function(i,e){
        array.push({
          "sucursal":$(e).find("td:eq(0) span").text(),
          "venta":$(e).find("td:eq(1)").html(),
          "variacion":$(e).find("td:eq(2) span").html(),
          "porcentaje":$(e).find("td:eq(3)").html(),
          "cantidad":$(e).find("td:eq(4)").html(),
          "ticket_promedio":$(e).find("td:eq(5)").html(),
          "costo":$(e).find("td:eq(6)").html(),
          "ganancia":$(e).find("td:eq(7)").html(),
          "marcacion":$(e).find("td:eq(8)").html(),
          "oferta":$(e).find("td:eq(8)").html(),
          "descuento":$(e).find("td:eq(10)").html(),
        });
      });
      var header = new Array("Sucursal","Venta","Var.","%","Tickets","Ticket Promedio","CMV","G. Bruta","% Marc.","En Oferta","Bonificacion");
      this.exportar_excel({
        "filename":"ventas_por_sucursal",
        "title":"Ventas por sucursal",
        "data":array,
        "date":desde+" - "+hasta,
        "header":header,
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasPorSucursalItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_ventas_por_sucursal_item_template').html()),
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      //this.total_general = this.options.total_general;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = { 
        permiso: this.permiso,
        //total_general: this.total_general,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );