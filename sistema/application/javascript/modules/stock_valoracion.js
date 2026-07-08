// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ValoracionStock = Backbone.Model.extend({
        urlRoot: function() {
            var s = "stock/function/valoracion";
            s=s+"/"+this.get("fecha");
            s=s+"/"+this.get("id_sucursal");
            return s;
        },
        defaults: {
            "fecha" : '',
            "id_sucursal": 0,
            "datos": new Array()
        },
    });
	    
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ValoracionStockResultados = app.mixins.View.extend({

		template: _.template($("#valoracion_stock_resultados_template").html()),
			
		myEvents: {
			"click .buscar": "buscar",
			"click .exportar":"exportar"
		},

	    buscar : function() {
			var self = this;
			var fecha = $(this.el).find("#valoracion_stock_fecha").val().replace(/\//g,"-");
      var id_sucursal = this.$("#valoracion_stock_sucursales").val();
			
			if (isEmpty(fecha)) {
			    show("Por favor seleccione una fecha");
			    $(this.el).find("#valoracion_stock_fecha").focus();
			    return;                
			}
			this.model.set({
			    "fecha": fecha,
          "id_sucursal": id_sucursal,
			});
			$(self.el).find(".generar").text("Por favor espere...");
			this.model.fetch({
			    "success":function(modelo) {
			    	self.mostrar_resultados(modelo);
			    }
			});
	    },
		
		exportar : function() {
            var self = this;
            var header = new Array();
            $(".table thead tr th").each(function(i,e){
                var t = $(e).text();
                if (!isEmpty(t)) header.push(t);
            });
			// Acomodamos los datos
			var array = new Array();
			_.each(self.model.get("datos"),function(m){
				array.push({
					"fecha": m.fecha,
					"codigo": m.codigo,
					"descripcion": m.descripcion,
					"cantidad": m.cantidad,
					"costo_neto": m.costo_neto,
					"costo_final": m.costo_final,
					"precio": m.precio,
				});
			});
            this.exportar_excel({
                "filename":"valoracion_stock",
                "title":"Valoracion de Stock",
                "date":$("#valoracion_stock_fechas_container .desde").val(),
                "data":array,
                "header":header,
            });
		},

        initialize: function() {
            var self = this;
            _.bindAll(this);
            this.render();
            this.bind("actualizar",this.mostrar_resultados);
        },

        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            createdatepicker(this.$("#valoracion_stock_fecha"),moment().format("DD/MM/YYYY"));
            return this;
        },

        mostrar_resultados: function(model) {
	    
            // Limpiamos la tabla
            $(this.el).find(".tbody").empty();
			var costo_neto_total = 0;
			var costo_final_total = 0;
			var precio_total = 0;
                        
            var length = model.get("datos").length;
            if (length == 0) {
                $(this.el).find(".tbody").append("<tr><td colspan='10' style='width: 724px'>No se encontraron resultados.</td></tr>");
            } else {
						
				// Recorremos los resultados
				for(i=0;i<length;i++) {
				    var m = model.get("datos")[i];
				    
				    costo_neto_total = parseFloat(costo_neto_total) + parseFloat(m.costo_neto);
				    costo_final_total = parseFloat(costo_final_total) + parseFloat(m.costo_final);
				    precio_total = parseFloat(precio_total) + parseFloat(m.precio);
				    
				    // Creamos una fila nueva
				    var Item = Backbone.Model.extend({
					defaults: {
					    "id":m.id,
					    "codigo": m.codigo,
					    "nombre": m.nombre,
					    "fecha": m.fecha,
					    "cantidad": m.cantidad,
					    "uxb": m.uxb,
					    "costo_neto": m.costo_neto,
					    "costo_final": m.costo_final,
					    "precio": m.precio,
					}
				    });
				    var modelo = new Item();
				    var item = new app.views.ValoracionStockItemResultados({
					model: modelo
				    });
				    // La agregamos a la tabla
				    $(this.el).find(".tbody").append(item.el);
				}
		    }
		    $(this.el).find("#costo_neto_total").html("$ "+Number(costo_neto_total).toFixed(2));
		    $(this.el).find("#costo_final_total").html("$ "+Number(costo_final_total).toFixed(2));
		    $(this.el).find("#precio_total").html("$ "+Number(precio_total).toFixed(2));
        },
	
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ValoracionStockItemResultados = Backbone.View.extend({

	template: _.template($("#valoracion_stock_item_resultados_template").html()),
        
        tagName: "tr",
		
	events: {
	},
	
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