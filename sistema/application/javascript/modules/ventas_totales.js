// -----------
//   MODELO
// -----------

(function ( models ) {

    models.VentaTotalItem = Backbone.Model.extend({
        urlRoot: "ventas/",
        defaults: {
            concepto: "",
            neto: 0,
            iva: 0,
            total: 0,
        }
    });
	    
})( app.models );

// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.VentasTotales = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 9999,
        },        

        paginator_core: {
            url: "ventas/function/totales/",
        },
    });

})( app.collections, app.models.VentaTotalItem, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.VentasTotalesTableView = app.mixins.View.extend({

        template: _.template($("#ventas_totales_template").html()),
            
        myEvents: {
            "change .buscar":"buscar",
            "click .buscar":"buscar",
            "click .exportar":"exportar",
        },
	
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.options = options;
            this.parent = (this.options.parent == undefined) ? false : this.options.parent;
			this.permiso = this.options.permiso;            
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
            }));
            
			// Creamos la lista de paginacion
            /*
			var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
				collection: this.collection
			});
			*/
            
            this.collection.off('sync');
            this.collection.on('sync', this.addAll, this);
            
            // Cargamos el paginador
            //this.$(".pagination_container").html(pagination.el);
            
            if (control.check("vendedores")>0) {
                new app.mixins.Select({
                    modelClass: app.models.Vendedor,
                    url: "vendedores/",
                    render: "#ventas_totales_vendedor",
                    firstOptions: ["<option value='0'>Vendedores</option>"],
                });                
            }
			
            var fecha_desde = new Date();
            var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
            fecha_desde = new Date(y, m, 1);
            fecha_hasta = new Date(y, m+1, 0);	                        
            createdatepicker(this.$("#ventas_totales_desde"),fecha_desde);
            createdatepicker(this.$("#ventas_totales_hasta"),fecha_hasta);
            
            this.buscar();
        },
        
        buscar: function() {
            var self = this;
            var filtros = {};

            if (!isEmpty(this.$("#ventas_listado_buscar").val())) 
                filtros.filter = this.$("#ventas_listado_buscar").val();
                /*
            if (!isEmpty(this.$("#ventas_listado_cliente").val())) 
                filtros.id_cliente = this.$("#ventas_listado_cliente").val();
            if (!isEmpty(this.$("#ventas_listado_vendedor").val())) 
                filtros.id_vendedor = this.$("#ventas_listado_vendedor").val();
            if (!isEmpty(this.$("#ventas_listado_numero").val())) 
                filtros.numero = this.$("#ventas_listado_numero").val();
                */
            var fecha_desde = this.$("#ventas_totales_desde").val();
            if (isEmpty(fecha_desde)) fecha = 0;
            else fecha_desde = fecha_desde.replace(/\//g,"-");
            if (!isEmpty(fecha_desde)) filtros.desde = fecha_desde;
            
            var fecha_hasta = this.$("#ventas_totales_hasta").val();
            if (isEmpty(fecha_hasta)) fecha = 0;
            else fecha_hasta = fecha_hasta.replace(/\//g,"-");
            if (!isEmpty(fecha_hasta)) filtros.hasta = fecha_hasta;
                        
            if (control.check("vendedores")>0) {
                var id_vendedor = 
                filtros.id_vendedor = this.$("#ventas_vendedores").val();
            }
            
            filtros.agrupado_por = this.$("#ventas_totales_agrupado_por").val();
            
            this.collection.server_api = filtros;
            this.collection.pager();            
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
			_.each(self.collection.models,function(m){
				array.push({
					"concepto": m.get("concepto"),
					"neto":Number(m.get("neto")).toFixed(2),
					"iva":Number(m.get("iva")).toFixed(2),
					"total":Number(m.get("total")).toFixed(2),
				});
			});
            this.exportar_excel({
                "filename":"ventas",
                "title":"Totales de Ventas",
                "date":$("#ventas_totales_desde").val()+" - "+$("#ventas_totales_hasta").val(),
                "data":array,
                "header":header,
            });			
        },
        
        addAll : function () {
            this.neto = 0; this.iva = 0; this.total = 0;
            this.$("#ventas_totales_tabla tbody tr").empty();
            this.collection.each(this.addOne);
            this.$("#ventas_totales_total_neto").html(Number(this.neto).toFixed(2));
            this.$("#ventas_totales_total_iva").html(Number(this.iva).toFixed(2));
            this.$("#ventas_totales_total_total").html(Number(this.total).toFixed(2));
        },
        
        addOne : function ( item ) {
            var view = new app.views.VentasTotalesItemResultados({
                model: item,
                parent: this.parent,
            });
            this.neto = this.neto + parseFloat(item.get("neto"));
            this.iva = this.iva + parseFloat(item.get("iva"));
            this.total = this.total + parseFloat(item.get("total"));
            this.$("#ventas_totales_tabla tbody").append(view.render().el);
        },        
	
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.VentasTotalesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#ventas_totales_item_resultados_template").html()),
        tagName: "tr",
        initialize: function(options) {
            var self = this;
            this.options = options;
            this.parent = (this.options.parent != undefined) ? this.options.parent : false;
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
