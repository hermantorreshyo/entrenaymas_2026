(function (collections, paginator) {

    collections.ArticulosTotales = paginator.requestPager.extend({
        
        paginator_ui: {
            perPage: 999999,
            order_by: 'codigo',
            order: 'asc',
        },
        paginator_core: {
            url: function() {
                var codigo = (isEmpty(this.meta("codigo")) ? 0 : this.meta("codigo"));
                var desde = ((this.meta("desde") == undefined) ? 0 : this.meta("desde"));
                var hasta = ((this.meta("hasta") == undefined) ? 0 : this.meta("hasta"));
                var id_vendedor = ((this.meta("id_vendedor") == undefined) ? 0 : this.meta("id_vendedor"));
                var s = "estadisticas/articulos_totales";
                s=s+"/"+codigo;
                s=s+"/"+desde;
                s=s+"/"+hasta;
                s=s+"/"+id_vendedor;
                return s;
            }
        }
	    
    });

})( app.collections, Backbone.Paginator);



(function ( app ) {

    app.views.ArticulosTotales = app.mixins.View.extend({

        template: _.template($("#articulos_totales_template").html()),
            
        myEvents: {
			"click .buscar":"buscar",
            "click .exportar":"exportar",
            "keypress #articulos_totales_codigo":function(e) {
                if (e.which == 13) {
                    this.buscar_articulo();
                }
            }
        },
        
        buscar_articulo:function() {
            var self = this;
            var id = $("#articulos_totales_codigo").val();
            $.ajax({
                "url":"articulos/"+id,
                "dataType":"json",
                "success":function(r) {
                    if (r.descripcion != undefined) {
                        $("#articulos_totales_descripcion").val(r.descripcion);
                        self.buscar();
                    } else {
                        show("No se encuentra un articulo con el codigo '"+id+"'.");
                        $("#articulos_totales_descripcion").val("");
                    }
                }
            });
        },
        
        initialize: function() {
            var self = this;
            _.bindAll(this);
            $(this.el).html(this.template());
            
            createdatepicker($(this.el).find("#articulos_totales_desde"),new Date());
            createdatepicker($(this.el).find("#articulos_totales_hasta"),new Date());
            
			$(this.el).find("#articulos_totales_codigo").autocomplete({
				"source":function(request,response) {
                    // Si comienza con letras, autocompletamos
                    if (request.term.match(/^[A-Za-z\s]+$/)) {
                        $.ajax({
                            "url":"articulos/function/get_by_descripcion/",
                            "data":{
                                "term":request.term
                            },
                            "dataType":"json",
                            "type":"get",
                            "success":function(res){
                                response(res);
                            }
                        });
                    }
                },
                "select":function() {
                    self.buscar_articulo();
                }
			});               
            
            new app.mixins.Select({
                modelClass: app.models.Vendedor,
                url: "vendedores/",
                render: "#articulos_totales_vendedor",
                firstOptions:["<option value='0'>-</option>"]
            });
            
            this.collection.on('reset', this.addAll, this);
            this.collection.on('add', this.addOne, this);
			
        },
        
        buscar: function() {
            var self = this;
            
            if (ESTADO == 0) {
                if (!confirmar("Esta seguro que desea sacar el reporte?")) return;
            }            
            
            var desde = $("#articulos_totales_desde").val();
            if (isEmpty(desde)) { show("Por favor seleccione una fecha"); return; }
            desde = desde.replace(/\//g,"-");
			
            var hasta = $("#articulos_totales_hasta").val();
            if (isEmpty(hasta)) { show("Por favor seleccione una fecha"); return; }
            hasta = hasta.replace(/\//g,"-");
			
            var codigo = $("#articulos_totales_codigo").val();
			
			var id_vendedor = $("#articulos_totales_vendedor").val();
			            
            this.collection.meta("desde",desde);
            this.collection.meta("hasta",hasta);
            this.collection.meta("codigo",codigo);
            this.collection.meta("id_vendedor",id_vendedor);
            this.collection.pager();
        },
        
        addAll : function () {
            this.total_precio = 0;
            this.total_cantidad = 0;
            this.total_recambio = 0;
            $("#articulos_totales_tabla tbody").empty();
            if (this.collection.length > 0) {
                this.collection.each(this.addOne);
            } else {
                $("#articulos_totales_tabla tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");    
            }
            $("#articulos_totales_total_precio").val(Number(this.total_precio).toFixed(2));
            $("#articulos_totales_total_cantidad").val(Number(this.total_cantidad).toFixed(2));
        },        
        
        addOne : function ( item ) {
            var self = this;
            var total = parseFloat(item.get("total"));
            var cantidad = parseFloat(item.get("cantidad"));
            if (isNaN(total)) total = 0;
            if (isNaN(cantidad)) cantidad = 0;
            this.total_precio += total;
            this.total_cantidad += cantidad;
            var view = new app.views.ArticulosTotalesItemResultados({
                model: item,
                resultados: self
            });
			$(self.el).find("#articulos_totales_tabla tbody").append(view.el);
        },
        
        exportar : function() {
			
            var self = this;
            var header = new Array();
            $("#articulos_totales_tabla thead tr th").each(function(i,e){
                var t = $(e).text();
                if (!isEmpty(t)) header.push(t);
            });
			// Acomodamos los datos
			var array = new Array();
            $("#articulos_totales_tabla tbody tr").each(function(i,e){
                array.push({
                    "codigo":$(e).find("td:eq(0)").html(),
                    "descripcion":$(e).find("td:eq(1)").html(),
                    "cantidad_ventas":$(e).find("td:eq(2)").html(),
                    "cantidad":$(e).find("td:eq(3)").html(),
                    "recambio":$(e).find("td:eq(4)").html(),
                    "porc_recambio":$(e).find("td:eq(5)").html(),
                    "bonificado":$(e).find("td:eq(6)").html(),
                    "total":$(e).find("td:eq(7)").html(),
                });
            });
            this.exportar_excel({
                "filename":"articulos_totales",
                "title":"Totales",
                "date":"Fecha: "+$("#articulos_totales_desde").val()+" - "+$("#articulos_totales_hasta").val(),
                "data":array,
                "header":header,
            });			
        },
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.ArticulosTotalesItemResultados = Backbone.View.extend({
        
        template: _.template($("#articulos_totales_item_resultados_template").html()),
        tagName: "tr",
        events: {
            "click .edit":"editar"
        },
        initialize: function() {
            var self = this;
            _.bindAll(this);
            this.render();
        },
        editar:function() {
            //location.href = "app/#facturacion/"+this.model.id;
        },
        render: function() {
            var obj = this.model.toJSON();
            obj.id = this.model.id;
            $(this.el).html(this.template(obj));
            return this;
        },
    });
})(app);
