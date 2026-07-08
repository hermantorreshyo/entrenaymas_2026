// -----------
//   MODELO
// -----------

(function ( models ) {

    models.VentasDiarias = Backbone.Model.extend({
        urlRoot: function() {
            var s = "ventas/function/totales";
            s=s+"/"+this.get("tipo_venta");
            s=s+"/"+this.get("fecha_desde");
            s=s+"/"+this.get("fecha_hasta");
            s=s+"/-1";//+this.get("id_rubro");
            s=s+"/-1";//+this.get("id_subrubro");
            s=s+"/0";//+this.get("id_proveedor");
            s=s+"/0";//+this.get("codigo");
            return s;
        },
        defaults: {
            "tipo_venta" : 0,
            "fecha_desde" : '',
            "fecha_hasta" : '',
            "id_rubro": -1,
            "id_proveedor": 0,
            "codigo": 0,
            "datos": new Array()
        },
    });
	    
})( app.models );





// -----------------------------------------
//   VISTA DE PARAMETROS DE VENTAS DIARIAS
// -----------------------------------------
(function ( app ) {

    app.views.VentasDiariasParametros = Backbone.View.extend({

        template: _.template($("#ventas_diarias_parametros_template").html()),
    
        events: {
            "click .generar": "onclick_buscar",
            "click .limpiar": "limpiar",
            "change #ventas_diarias_proveedores": function(e){
                var id_proveedor = $(e.currentTarget).val();
                $(this.el).find("#ventas_diarias_codigo_proveedor").val(id_proveedor);
            },
            // Buscamos el proveedor por codigo
            "keypress #ventas_diarias_codigo_proveedor": function(e) {
                if (e.keyCode == 13) {
                    this.buscar_proveedor();
                }
            },
            "focusout #ventas_diarias_codigo_proveedor": function(e) {
                this.buscar_proveedor();
            },	    
            "keypress #ventas_diarias_codigo": function(e) {
                if (e.keyCode == 13) {
                    this.onclick_buscar();
                }
            },
        },
        
        buscar_proveedor : function() {
            var id_proveedor = $(this.el).find("#ventas_diarias_codigo_proveedor").val();
            $(this.el).find("#ventas_diarias_proveedores").val(id_proveedor).change();
        },    

        initialize: function(options) {
            _.bindAll(this);
            this.render();
            this.options = options;
            this.resultados = this.options.resultados;
            this.bind("seleccionar",this.seleccionar,this);
            
            // Parametros de busqueda
            this.fecha_desde = "";
            this.fecha_hasta = "";
            this.rubro = -1;
            this.tipo_venta = 0;
            this.codigo = 0;
        },
        
        render: function() {
            
            var self = this;
            $(this.el).html(this.template());
	    
            new app.mixins.Select({
                modelClass: app.models.Proveedor,
                url: "proveedores/",
                renderIn: "#ventas_diarias_proveedores_select_container",
                firstOptions: ["<option value='0'>Proveedor</option>"],
                selected: 0,
                length: 23,
            });	    
                
            var rubroSelect = new app.mixins.Select({
                modelClass: app.models.Rubro,
                url: "rubros/",
                renderIn: "#ventas_diarias_rubros_select_container",
                name : "id_rubro",
                firstOptions: ["<option value='-1'>Todos</option>"],
                selected: -1,
                success: function() {
                    self.model.set({"id_rubro" : $(self.el).find("#ventas_diarias_rubros").val() });
                }
            });  
            
            return this;
        },
        	
        limpiar : function() {
            $(this.el).find("#ventas_diarias_rubros").val("-1");
        },
        
        onclick_buscar : function() {
            var self = this;
            this.fecha_desde = $(this.el).find("#ventas_diarias_desde").val().replace(/\//g,"-");
            this.fecha_hasta = $(this.el).find("#ventas_diarias_hasta").val().replace(/\//g,"-");
            if (isEmpty(this.fecha_desde)) {
                show("Por favor seleccione una fecha");
                $(this.el).find(".fecha_desde").focus();
                return;                
            }
            if (isEmpty(this.fecha_hasta)) {
                show("Por favor seleccione una fecha");
                $(this.el).find(".fecha_hasta").focus();
                return;                
            }
            this.codigo = $(this.el).find("#ventas_diarias_codigo").val().replace(/\,/g,"-");
            this.id_proveedor = $(this.el).find("#ventas_diarias_proveedores").val();	    
            this.rubro = ($(this.el).find("#ventas_diarias_rubros").val());
            this.tipo_venta = ($(this.el).find("#ventas_diarias_tipo_venta").val());
            this.buscar();
        },
	
        buscar : function() {
            var self = this;
            $("#ventas_diarias_loading").show();
            this.model.set({
                "tipo_venta": self.tipo_venta,
                "fecha_desde": self.fecha_desde,
                "fecha_hasta": self.fecha_hasta,
                "id_rubro": self.rubro,
                "id_proveedor": self.id_proveedor,
                "codigo": self.codigo,
            });
            this.model.fetch({
                "success":function(modelo) {
                    self.resultados.trigger("actualizar",self.model);
                    $(self.el).find(".exportar").show();
                }
            });
        },
            
        seleccionar : function(tipo,id) {
            
            // Se busca por rubro
            if (tipo == 0) {
                $(this.el).find("#ventas_diarias_rubros").val(id);
                var rubro = $(this.el).find("#ventas_diarias_rubros option:selected").text();
                this.onclick_buscar();
            }
        },
        
    });

})(app);





// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.VentasDiariasResultados = Backbone.View.extend({

        template: _.template($("#ventas_diarias_resultados_template").html()),

        initialize: function() {
            var self = this;
            _.bindAll(this);
            this.render();
            this.bind("actualizar",this.mostrar_resultados);
        },
	
        events: {
            "click .exportar":"exportar",
            "click .generar_pedido":"generar_pedido",
            "click .imprimir":"imprimir",	    
        },
        
        exportar : function() {
            var form = document.createElement("form");
            form.setAttribute("method","post");
            form.setAttribute("target","_blank");
            form.setAttribute("action","exportar/excel/ventas/");
            var hidden = document.createElement("input");
            hidden.setAttribute("name","e");
            hidden.setAttribute("value",$(this.el).find("#ventas_diarias_tabla").parent().html());
            form.appendChild(hidden);
            $(form).css("display","none");
            document.body.appendChild(form);
            form.submit();
        },

        imprimir: function() {
            var self = this;
            var id_proveedor = $("#ventas_diarias_proveedores").val();
            if (id_proveedor == 0) {
                show("Por favor seleccione un proveedor.");
                $("#ventas_diarias_codigo_proveedor").focus();
                return;
            }
            
            var fecha_desde = $("#ventas_diarias_desde").val();
            if (isEmpty(fecha_desde)) fecha = 0;
            else fecha_desde = fecha_desde.replace(/\//g,"-");
            
            var fecha_hasta = $("#ventas_diarias_hasta").val();
            if (isEmpty(fecha_hasta)) fecha = 0;
            else fecha_hasta = fecha_hasta.replace(/\//g,"-");
            
            window.open("ventas/function/imprimir_semana/"+fecha_desde+"/"+fecha_hasta+"/"+id_proveedor);
        },	
	
        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
	
        generar_pedido: function() {
            var self = this;
            var id_proveedor = $("#ventas_diarias_proveedores").val();
            if (id_proveedor == 0) {
                show("Por favor seleccione un proveedor.");
                $("#ventas_diarias_codigo_proveedor").focus();
                return;
            }
            
            var fecha_desde = $("#ventas_diarias_desde").val();
            if (isEmpty(fecha_desde)) fecha = 0;
            else fecha_desde = fecha_desde.replace(/\//g,"-");
            
            var fecha_hasta = $("#ventas_diarias_hasta").val();
            if (isEmpty(fecha_hasta)) fecha = 0;
            else fecha_hasta = fecha_hasta.replace(/\//g,"-");
            
            $.ajax({
                "url":"ventas/function/generar_pedido/"+fecha_desde+"/"+fecha_hasta+"/"+id_proveedor,
                "dataType":"json",
                "success":function() {
                    show("El pedido se ha generado correctamente.");
                }
            });
        },
	
        mostrar_resultados: function(model) {
            
            // Limpiamos la tabla
            $(this.el).find(".tbody").empty();
                
            var cantidad = 0;
            var total = 0;
            var neto = 0;
            
            var length = model.get("results").length;
            if (length == 0) {
                $(this.el).find(".tbody").append("<tr><td colspan='10' style='width: 724px'>No se encontraron resultados.</td></tr>");
            } else {
                
                // Recorremos los resultados
                for(i=0;i<length;i++) {
                    
                    var m = model.get("results")[i];
                    total = total + parseFloat(m.total);
                    neto = neto + parseFloat(m.neto);
                    cantidad = cantidad + parseFloat(m.cantidad);

                    // Creamos una fila nueva
                    var Item = Backbone.Model.extend({
                        defaults: {
                            "id": m.id,
                            "cantidad": m.cantidad,
                            "uxb": m.uxb,
                            "total": m.total,
                            "neto": m.neto,
                            "descripcion": m.descripcion,
                        }
                    });
                    var item = new app.views.VentasDiariasItemResultados({
                        model: new Item()
                    });
                    // La agregamos a la tabla
                    $(this.el).find(".tbody").append(item.el);
                }
            }
            // Mostramos los totales
            $(this.el).find("#total").html(Number(total).format());
            $(this.el).find("#neto").html(Number(neto).format());
            $(this.el).find("#cantidad").html(Number(cantidad).format());
            $("#ventas_diarias_loading").hide();
        }
        
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.VentasDiariasItemResultados = Backbone.View.extend({

        template: _.template($("#ventas_diarias_item_resultados_template").html()),
        
        tagName: "tr",
		
        events: {
            "click td":"seleccionar"
        },
        
        initialize: function() {
            var self = this;
            _.bindAll(this);
            this.render();
        },
		
        seleccionar : function() {
            // El tipo depende de lo que se esta buscando
            var tipo = this.model.get("tipo");
            var id;
            if (tipo == 2) return;
            else if (tipo == 0) id = this.model.get("id_rubro");
            app.views.ventas_diariasParametros.trigger("seleccionar",tipo,id);
        },

        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
    });
})(app);
