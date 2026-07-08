// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Reparto = Backbone.Model.extend({
    urlRoot: "repartos/",
    defaults: {
      id_articulo: 0,
      descripcion: "",
    }
  });
	  
})( app.models );


(function ( app ) {

  app.views.RepartosResultados = app.mixins.View.extend({

    template: _.template($("#repartos_resultados_template").html()),
      
    events: {
      "click .buscar_por_articulos":"buscar_por_articulos",
      "click .buscar_por_facturas":"buscar_por_facturas",
      "click .exportar":"exportar",
      "click .exportar_por_facturas":"exportar_por_facturas",
      "click .imprimir":"imprimir",
      "click .imprimir_facturas": "imprimir_facturas",
    },
	
    initialize: function() {
      var self = this;
      _.bindAll(this);
      
      $(this.el).html(this.template());
      
      $(this.el).find("#repartos_numero").TouchSpin({
        verticalbuttons: true,
        min: 0,
      });
      $(this.el).find("#repartos_por_factura_numero").TouchSpin({
        verticalbuttons: true,
        min: 0,
      });
      
      var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
      $(this.el).find("#repartos_fecha").datepicker({
        "dateFormat":"dd/mm/yy",
        "currentText":"Hoy",
        "buttonImage": "resources/images/datepicker.png",
        "buttonImageOnly": true,
        "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
        "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
        "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
        "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
        "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "nextText":"Proximo",
        "prevText":"Anterior",
        "defaultDate":fecha
      });
			$(this.el).find("#repartos_fecha").mask("99/99/9999");
      
      var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
      $(this.el).find("#repartos_por_factura_fecha").datepicker({
        "dateFormat":"dd/mm/yy",
        "currentText":"Hoy",
        "buttonImage": "resources/images/datepicker.png",
        "buttonImageOnly": true,
        "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
        "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
        "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
        "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
        "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "nextText":"Proximo",
        "prevText":"Anterior",
        "defaultDate":fecha
      });
			$(this.el).find("#repartos_por_factura_fecha").mask("99/99/9999");        
    },
    
    imprimir: function() {
      var fecha = $("#repartos_fecha").val();
      if (isEmpty(fecha)) return;
      else fecha = fecha.replace(/\//g,"-");
      var numero = $("#repartos_numero").val();
      if (isEmpty(numero)) return;
      workspace.imprimir_reporte("repartos/function/imprimir/"+fecha+"/"+numero);
    },

    imprimir_facturas: function() {
      var fecha = $("#repartos_fecha").val();
      if (isEmpty(fecha)) return;
      else fecha = fecha.replace(/\//g,"-");
      var numero = $("#repartos_numero").val();
      if (isEmpty(numero)) return;
      workspace.imprimir_reporte("repartos/function/imprimir_facturas/"+fecha+"/"+numero);
    },
	
    exportar : function() {
      var fecha = $("#repartos_fecha").val();
      if (isEmpty(fecha)) return;
      else fecha = fecha.replace(/\//g,"-");
      var numero = $("#repartos_numero").val();
      if (isEmpty(numero)) return;
      window.open("repartos/function/exportar/"+fecha+"/"+numero,"_blank");
    },
    
    exportar_por_facturas:function() {
      var self = this;
      var header = new Array();
      $("#repartos_por_factura_tabla thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });
			// Acomodamos los datos
			var array = new Array();
      $("#repartos_por_factura_tabla tbody tr").each(function(i,e){
				array.push({
          "numero": $(e).find("td:eq(0)").text(),
					"fecha": $(e).find("td:eq(1)").text(),
					"comprobante": $(e).find("td:eq(2)").text(),
					"cliente": $(e).find("td:eq(3) span").text(),
					"vendedor": $(e).find("td:eq(4)").text(),
					"total": $(e).find("td:eq(5)").text(),
          "saldo": $(e).find("td:eq(6)").text(),
				});        
      });
      this.exportar_excel({
        "filename":"repartos",
        "title":"Repartos",
        "date":$("#repartos_por_factura_numero").val()+"-"+$("#repartos_por_factura_fecha").val(),
        "data":array,
        "header":header,
      });
    },
    
    buscar_por_articulos:function() {
      var self = this;
      var fecha = $(this.el).find("#repartos_fecha").val();
      var numero = $("#repartos_numero").val();
      $.ajax({
        "url":"repartos/function/consulta/",
        "data":{
          "fecha":fecha,
          "numero":numero,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r){
          self.total_facturado = 0;
          self.total_bonificacion = 0;
          self.total_devolucion = 0;
          $(self.el).find("#repartos_tabla .tbody").empty();
          if (r.results.length == 0) {
            $(self.el).find("#repartos_tabla .tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");
          } else {
            _.each(r.results,self.addOne)
          }          
          $("#reparto_total_facturado").html(Number(self.total_facturado).toFixed(2));
          $("#reparto_total_bonificacion").html(Number(self.total_bonificacion).toFixed(2));
          $("#reparto_total_devolucion").html(Number(self.total_devolucion).toFixed(2));
          $("#reparto_total").html(Number(self.total_facturado + self.total_bonificacion + self.total_devolucion).toFixed(2));
          var porc_recambio = Number( self.total_devolucion / (self.total_facturado + self.total_devolucion + self.total_bonificacion) * 100 ).toFixed(5);
          if (isNaN(porc_recambio)) porc_recambio = "0.00000";
          $("#repartos_porc_recambio").val(porc_recambio);          
        }
      });
    },
    
    buscar_por_facturas: function() {
      var self = this;
      this.contador = 1;
      var fecha = $(this.el).find("#repartos_por_factura_fecha").val();
      fecha = fecha.replace(/\//g,"-");
      var numero = $("#repartos_por_factura_numero").val();
      $.ajax({
        "url":"facturas/function/consulta/",
        "data": {
          "fecha_reparto":fecha,
          "numero_reparto":numero,
          "incluir_saldo":1,
          "offset":99999,
        },
        "type":"get",
        "dataType":"json",
        "success":function(r){
          self.total_por_factura = 0;
          self.neto_por_factura = 0;
          self.iva_por_factura = 0;          
          $(self.el).find("#repartos_por_factura_tabla .tbody").empty();
          if (r.results.length == 0) {
            $(self.el).find("#repartos_por_factura_tabla .tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");
          } else {
            _.each(r.results,self.addItem)
          }
          $("#repartos_cant_clientes").val(r.meta.cantidad_clientes);
          $("#repartos_cant_facturas").val(r.meta.cantidad_facturas);
          
          $("#reparto_por_factura_neto").html(Number(self.neto_por_factura).toFixed(2));
          $("#reparto_por_factura_iva").html(Number(self.iva_por_factura).toFixed(2));
          $("#reparto_por_factura_total").html(Number(self.total_por_factura).toFixed(2));
        }
      });      
    },
    
    addOne : function ( i ) {
      var item = new app.models.Reparto(i);
      var view = new app.views.RepartosItemResultados({
        model: item,
      });
      this.total_facturado += parseFloat(item.get("facturado"));
      this.total_bonificacion += parseFloat(item.get("bonificacion"));
      this.total_devolucion += (parseFloat(item.get("devolucion")));
      $(this.el).find("#repartos_tabla .tbody").append(view.render().el);
    },
    
    addItem: function(i) {
      var self = this;
      var item = new app.models.Reparto(i);
      item.set({"contador":self.contador});
      var view = new app.views.RepartosPorFacturaItem({
        model: item,
      });
      this.total_por_factura += ((item.get("negativo") == 1) ? -1 : 1) * parseFloat(item.get("total"));
      this.iva_por_factura += ((item.get("negativo") == 1) ? -1 : 1) * parseFloat(item.get("iva"));
      this.neto_por_factura += ((item.get("negativo") == 1) ? -1 : 1) * (parseFloat(item.get("neto")));      
      $(this.el).find("#repartos_por_factura_tabla .tbody").append(view.render().el);
      this.contador++;
    },
	
  });

})(app);



(function ( app ) {
  app.views.RepartosItemResultados = Backbone.View.extend({
    
    template: _.template($("#repartos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click .edit":"editar",
      "click .delete":"borrar",
      "click .print":"imprimir",
      "click .checkbox":"seleccionar",
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
      $(this.el).addClass("seleccionado");
      } else {
      $(this.el).removeClass("seleccionado");
      }
    },
    editar : function() {
      var self = this;
      var aplicacion = new app.models.Aplicacion({
        "id":self.model.id
      });
      aplicacion.fetch({
        "success":function() {
          app.views.aplicacionEditView = new app.views.AplicacionEditView({
            "model": aplicacion,
            "edicion": false
          });
          crearLightboxHTML({
            "html":app.views.aplicacionEditView.el,
            "width":600,
            "height":500,
          });
        }
      });
    },
    borrar : function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();	// Eliminamos el modelo
        $(this.el).remove();	// Lo eliminamos de la vista
      }
    },
    imprimir: function() {
      window.open("repartos/function/imprimir/"+this.model.id,"_blank");
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);


(function ( app ) {
  app.views.RepartosPorFacturaItem = Backbone.View.extend({
    
    template: _.template($("#repartos_por_factura_item_template").html()),
    tagName: "tr",
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
