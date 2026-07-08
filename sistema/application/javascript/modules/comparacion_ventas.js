(function (collections, paginator) {

    collections.ComparacionVentas = paginator.requestPager.extend({
        
        paginator_ui: {
            perPage: 999999,
            order_by: 'descripcion',
            order: 'asc',
        },
        paginator_core: {
            url: function() {
                var periodo1_desde = ((this.meta("periodo1_desde") == undefined) ? 0 : this.meta("periodo1_desde"));
                var periodo1_hasta = ((this.meta("periodo1_hasta") == undefined) ? 0 : this.meta("periodo1_hasta"));
                var periodo2_desde = ((this.meta("periodo2_desde") == undefined) ? 0 : this.meta("periodo2_desde"));
                var periodo2_hasta = ((this.meta("periodo2_hasta") == undefined) ? 0 : this.meta("periodo2_hasta"));
                var comparacion = ((this.meta("comparacion") == undefined) ? 0 : this.meta("comparacion"));
                var id_vendedor = ((this.meta("id_vendedor") == undefined) ? 0 : this.meta("id_vendedor"));
                var s = "estadisticas/comparacion";
                s=s+"/"+periodo1_desde;
                s=s+"/"+periodo1_hasta;
                s=s+"/"+periodo2_desde;
                s=s+"/"+periodo2_hasta;
                s=s+"/"+comparacion;
                s=s+"/"+id_vendedor;
                return s;
            }
        }
	    
    });

})( app.collections, Backbone.Paginator);


(function ( app ) {

    app.views.ComparacionVentas = app.mixins.View.extend({

        template: _.template($("#comparacion_ventas_template").html()),
            
        myEvents: {
			"click .buscar":"buscar",
            "click .exportar":"exportar",
        },
                
        initialize: function() {
            var self = this;
            _.bindAll(this);
            $(this.el).html(this.template());
            
            createdatepicker($(this.el).find("#comparacion_ventas_desde_1"),new Date());
            createdatepicker($(this.el).find("#comparacion_ventas_desde_2"),new Date());
            createdatepicker($(this.el).find("#comparacion_ventas_hasta_1"),new Date());
            createdatepicker($(this.el).find("#comparacion_ventas_hasta_2"),new Date());
            
            new app.mixins.Select({
                modelClass: app.models.Vendedor,
                url: "vendedores/",
                render: "#comparacion_ventas_vendedor",
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

            var periodo1_desde = $("#comparacion_ventas_desde_1").val();
            if (isEmpty(periodo1_desde)) { show("Por favor seleccione una fecha"); return; }
            periodo1_desde = periodo1_desde.replace(/\//g,"-");
			
            var periodo1_hasta = $("#comparacion_ventas_hasta_1").val();
            if (isEmpty(periodo1_hasta)) { show("Por favor seleccione una fecha"); return; }
            periodo1_hasta = periodo1_hasta.replace(/\//g,"-");
			
            var periodo2_desde = $("#comparacion_ventas_desde_2").val();
            if (isEmpty(periodo2_desde)) { show("Por favor seleccione una fecha"); return; }
            periodo2_desde = periodo2_desde.replace(/\//g,"-");
			
            var periodo2_hasta = $("#comparacion_ventas_hasta_2").val();
            if (isEmpty(periodo2_hasta)) { show("Por favor seleccione una fecha"); return; }
            periodo2_hasta = periodo2_hasta.replace(/\//g,"-");			
			
            var comparacion = $("#comparacion_ventas_parametro").val();
			
			var id_vendedor = $("#comparacion_ventas_vendedor").val();
            
            this.collection.meta("periodo1_desde",periodo1_desde);
            this.collection.meta("periodo1_hasta",periodo1_hasta);
            this.collection.meta("periodo2_desde",periodo2_desde);
            this.collection.meta("periodo2_hasta",periodo2_hasta);
            this.collection.meta("comparacion",comparacion);
            this.collection.meta("id_vendedor",id_vendedor);
            this.collection.pager();
        },
        
        addAll : function () {
            this.total_ventas_1 = 0;
            this.total_ventas_2 = 0;
            this.total_cantidad_1 = 0;
            this.total_cantidad_2 = 0;
            $("#comparacion_ventas_tabla tbody").empty();
            if (this.collection.length > 0) {
                this.collection.each(this.addOne);
            } else {
                $(self.el).find("#comparacion_ventas_tabla tbody").append("<tr><td colspan='30'>No se encontraron resultados.</td></tr>");	
            }
            $("#total_ventas_1").html(Number(this.total_ventas_1).toFixed(2));
            $("#total_ventas_2").html(Number(this.total_ventas_2).toFixed(2));
            $("#total_cantidad_1").html(Number(this.total_cantidad_1).toFixed(2));
            $("#total_cantidad_2").html(Number(this.total_cantidad_2).toFixed(2));
        },           
        
        addOne : function ( item ) {
            var self = this;
            var total_1 = parseFloat(item.get("total_1"));
            var total_2 = parseFloat(item.get("total_2"));
            var cantidad_1 = parseFloat(item.get("cantidad_1"));
            var cantidad_2 = parseFloat(item.get("cantidad_2"));
            if (isNaN(total_1)) total_1 = 0;
            if (isNaN(total_2)) total_2 = 0;
            if (isNaN(cantidad_1)) cantidad_1 = 0;
            if (isNaN(cantidad_2)) cantidad_2 = 0;
            this.total_ventas_1 += total_1;
            this.total_ventas_2 += total_2;
            this.total_cantidad_1 += cantidad_1;
            this.total_cantidad_2 += cantidad_2;
            var view = new app.views.ComparacionVentasItemResultados({
                model: item,
                resultados: self
            });
			$(self.el).find("#comparacion_ventas_tabla tbody").append(view.el);
        },
        
        exportar : function() {
			
            var self = this;
            var header = new Array();
            $("#comparacion_ventas_tabla thead tr th").each(function(i,e){
                var t = $(e).text();
                if (!isEmpty(t)) header.push(t);
            });
			// Acomodamos los datos
			var array = new Array();
            $("#comparacion_ventas_tabla tbody tr").each(function(i,e){
                array.push({
                    "articulo":$(e).find("td:eq(0)").html(),
                    "venta_1":$(e).find("td:eq(1)").html(),
                    "cantidad_1":$(e).find("td:eq(2)").html(),
                    "venta_2":$(e).find("td:eq(3)").html(),
                    "cantidad_2":$(e).find("td:eq(4)").html(),
                    "variacion":$(e).find("td:eq(5)").html(),
                });
            });
            var footer = new Array(
                "",
                $("#total_ventas_1").html(),
                $("#total_cantidad_1").html(),
                $("#total_ventas_2").html(),
                $("#total_cantidad_2").html(),
                ""
            );
            
            this.exportar_excel({
                "filename":"comparacion_ventas",
                "title":"Comparacion de Ventas",
                "date":"Periodo 1: "+$("#comparacion_ventas_desde_1").val()+" - "+$("#comparacion_ventas_hasta_1").val()+" Periodo 2: "+$("#comparacion_ventas_desde_2").val()+" - "+$("#comparacion_ventas_hasta_2").val(),
                "data":array,
                "header":header,
                "footer":footer,
            });			
        },
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.ComparacionVentasItemResultados = Backbone.View.extend({
        
        template: _.template($("#comparacion_ventas_item_resultados_template").html()),
        tagName: "tr",
        events: {
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
