// -----------------------------------------
//   VISTA DE PARAMETROS
// -----------------------------------------
(function ( app ) {

    app.views.RetencionIBParametros = Backbone.View.extend({

	template: _.template($("#retencion_ib_parametros_template").html()),

	events: {
            "click .generar": "generar",
	},

        initialize: function() {
            _.bindAll(this);
            this.render();
            this.add_datepickers(); // Al menos uno para que no haya error
        },
        
        render: function() {
	    var self = this;
            $(this.el).html(this.template());
            return this;
        },
        
        add_datepickers : function() {
            var e = new app.views.DatePicker({
                permitir_borrar: false,
		mostrar_mes: "MES_ANTERIOR",
            });
            $(this.el).find("#retencion_ib_fechas_container").append(e.el);
            
        },
	
        generar: function() {
            var self = this;
            var fecha_desde = $(this.el).find(".fecha_desde").val().replace(/\//g,"-");
            var fecha_hasta = $(this.el).find(".fecha_hasta").val().replace(/\//g,"-");
	    var quincena = ($(this.el).find("input[name=2da_quincena]").is(":checked") ? 2:1);
	    
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
            
            // Lo abrimos en otra pestaña
            var s = "/compras/function/exportar_retencion_ing_brutos";
            s=s+"/"+fecha_desde;
            s=s+"/"+fecha_hasta;
	    s=s+"/"+quincena;
            window.open(s,"_blank");
        },
	
    });

})(app);