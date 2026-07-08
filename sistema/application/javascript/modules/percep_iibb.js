// -----------------------------------------
//   VISTA DE PARAMETROS
// -----------------------------------------
(function ( app ) {

    app.views.PercepcionesIIBBParametros = Backbone.View.extend({

	template: _.template($("#percep_iibb_parametros_template").html()),

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
                permitir_borrar: false
            });
            $(this.el).find("#percep_iibb_fechas_container").append(e.el);
        },
		
        generar: function() {
            
            var self = this;
            
            var fecha_desde = $(this.el).find(".fecha_desde").val().replace(/\//g,"-");
            var fecha_hasta = $(this.el).find(".fecha_hasta").val().replace(/\//g,"-");
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
	    
	    var empresa = $(this.el).find("#percep_iibb_empresa").val();
            
            // Lo abrimos en otra pestaña
            var s = "/ventas/function/percepciones_iibb";
            s=s+"/"+fecha_desde;
            s=s+"/"+fecha_hasta;
	    s=s+"/"+empresa;
            window.open(s,"_blank");
        }
	
    });

})(app);