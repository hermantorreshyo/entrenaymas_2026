// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ResumenCaja = Backbone.Model.extend({
        urlRoot: function() {
            var s = "caja/function/resumen";
            s=s+"/"+this.get("fecha_desde");
            s=s+"/"+this.get("fecha_hasta");
            return s;
        },
        defaults: {
            "fecha_desde" : '',
            "fecha_hasta" : '',
            "datos": new Array()
        },
    });
	    
})( app.models );



(function ( app ) {

    app.views.ResumenCajaParametros = Backbone.View.extend({

        template: _.template($("#resumen_caja_parametros_template").html()),
    
        events: {
            "click .generar": "onclick_buscar",
            "click .limpiar": "limpiar",
        },
        
        initialize: function(options) {
            _.bindAll(this);
            this.render();
            this.add_datepickers(); // Al menos uno para que no haya error
            this.options = options;
            this.resultados = this.options.resultados;
            
            // Parametros de busqueda
            this.fecha_desde = "";
            this.fecha_hasta = "";
        },
        
        render: function() {
            var self = this;
            $(this.el).html(this.template());            
            return this;
        },
        
        add_datepickers : function() {
            var e = new app.views.DatePicker({
                permitir_borrar: false,
                mostrar_mes: "ACTUAL",
            });
            $(this.el).find("#resumen_caja_fechas_container").append(e.el);
        },
	
        limpiar : function() {
        },
        
        onclick_buscar : function() {
            var self = this;
            this.fecha_desde = $(this.el).find(".fecha_desde").val().replace(/\//g,"-");
            this.fecha_hasta = $(this.el).find(".fecha_hasta").val().replace(/\//g,"-");
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
            this.buscar();
        },
	
        buscar : function() {
            var self = this;
            $("#resumen_caja_loading").show();
            this.model.set({
                "fecha_desde": self.fecha_desde,
                "fecha_hasta": self.fecha_hasta,
            });
            this.model.fetch({
                "success":function(modelo) {
                    self.resultados.trigger("actualizar",self.model);
                }
            });
        },
            
    });

})(app);





// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ResumenCajaResultados = Backbone.View.extend({

        template: _.template($("#resumen_caja_resultados_template").html()),

        initialize: function() {
            var self = this;
            _.bindAll(this);
            this.render();
            this.bind("actualizar",this.mostrar_resultados);
        },
	
        events: {
            // Mostramos los cupones
            "click #resumen_caja_resultados_tarjetas_boton": function() {
                var self = this;
                var fecha_desde = $(".fecha_desde").first().val();
                if (isEmpty(fecha_desde)) return;
                else fecha_desde = fecha_desde.replace(/\//g,"-");
                var fecha_hasta = $(".fecha_hasta").first().val();
                if (isEmpty(fecha_hasta)) return;
                else fecha_hasta = fecha_hasta.replace(/\//g,"-");
                var cupones = new app.views.CuponesAgrupadosTableView({
                    "fecha_desde":fecha_desde,
                    "fecha_hasta":fecha_hasta,
                });
                crearLightboxHTML({
                    "html":cupones.el,
                    "width":400,
                    "height":500,
                });
            },
            
            // Mostramos los cheques
            "click #resumen_caja_resultados_cheques_boton": function() {
                var self = this;
                var fecha_desde = $(".fecha_desde").first().val();
                if (isEmpty(fecha_desde)) return;
                else fecha_desde = fecha_desde.replace(/\//g,"-");
                var fecha_hasta = $(".fecha_hasta").first().val();
                if (isEmpty(fecha_hasta)) return;
                else fecha_hasta = fecha_hasta.replace(/\//g,"-");
                var cheques = new app.views.ChequesTableView({
                    "fecha_desde":fecha_desde,
                    "fecha_hasta":fecha_hasta,
                });
                crearLightboxHTML({
                    "html":cheques.el,
                    "width":600,
                    "height":500,
                });
            },
            
            // Mostramos los cupones
            "click #resumen_caja_resultados_gastos_boton": function() {
                var self = this;
                var fecha_desde = $(".fecha_desde").first().val();
                if (isEmpty(fecha_desde)) return;
                else fecha_desde = fecha_desde.replace(/\//g,"-");
                var fecha_hasta = $(".fecha_hasta").first().val();
                if (isEmpty(fecha_hasta)) return;
                else fecha_hasta = fecha_hasta.replace(/\//g,"-");
                var gastos = new app.views.GastosAgrupadosTableView({
                    "fecha_desde":fecha_desde,
                    "fecha_hasta":fecha_hasta,
                });
                crearLightboxHTML({
                    "html":gastos.el,
                    "width":400,
                    "height":500,
                });
            },            
        },
	
        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
	
        mostrar_resultados: function(model) {
            
            var efectivo = parseFloat(model.get("efectivo"));
            var cta_cte = parseFloat(model.get("cta_cte"));
			var pagos = parseFloat(model.get("pagos"));
            var tarjeta = parseFloat(model.get("tarjeta"));
            var cheque = parseFloat(model.get("cheque"));
            var gastos = parseFloat(model.get("gastos"));
            if (isNaN(efectivo)) efectivo = 0;
            if (isNaN(cta_cte)) cta_cte = 0;
            if (isNaN(tarjeta)) tarjeta = 0;
            if (isNaN(cheque)) cheque = 0;
            if (isNaN(gastos)) gastos = 0;
			if (isNaN(pagos)) pagos = 0;
            
            $("#resumen_caja_resultados_efectivo").val(Number(efectivo).toFixed(2));
            $("#resumen_caja_resultados_tarjetas").val(Number(tarjeta).toFixed(2));
            $("#resumen_caja_resultados_cheques").val(Number(cheque).toFixed(2));
            $("#resumen_caja_resultados_gastos").val(Number(gastos).toFixed(2));
			$("#resumen_caja_resultados_pagos").val(Number(pagos).toFixed(2));
            
            var total = efectivo + pagos + tarjeta + cheque - gastos;
            $("#resumen_caja_resultados_total").val(Number(total).toFixed(2));
            
            $("#resumen_caja_resultados_cta_cte").val(Number(cta_cte - pagos).toFixed(2));
            $("#resumen_caja_resultados_general").val(Number(total + cta_cte - pagos).toFixed(2));
            
            $("#resumen_caja_loading").hide();
        }
        
    });

})(app);
