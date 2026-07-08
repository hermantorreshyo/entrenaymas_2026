(function ( app ) {

    app.views.DatePicker = Backbone.View.extend({

	template: _.template($("#datepicker_template").html()),
        
        attributes: {
            "class": "fecha_container"
        },
        
        events: {
            "click .borrar": "eliminar_fila"
        },

        initialize: function(options) {
            _.bindAll(this);
            this.options = options;
            this.mostrar_hasta = (this.options.mostrar_hasta != undefined) ? this.options.mostrar_hasta : true;
            this.permitir_borrar = (this.options.permitir_borrar != undefined) ? this.options.permitir_borrar : true;
            this.texto_desde = (this.options.texto_desde != undefined) ? this.options.texto_desde : "Desde: ";
            this.texto_hasta = (this.options.texto_hasta != undefined) ? this.options.texto_hasta : "Hasta: ";
            this.mostrar_mes = (this.options.mostrar_mes != undefined) ? this.options.mostrar_mes : "";
            this.vertical = (this.options.vertical != undefined) ? this.options.vertical : false;
            this.render();
        },
        
        eliminar_fila : function() {
            $(this.el).remove();
        },

        render: function() {
	    var self = this;
            $(this.el).html(this.template({
                mostrar_hasta: self.mostrar_hasta,
                permitir_borrar : self.permitir_borrar,
                texto_desde: self.texto_desde,
                texto_hasta: self.texto_hasta,
		vertical: self.vertical
            }));
	    	    
	    var fecha_desde = new Date();
	    var fecha_hasta = new Date();
	    
	    // Si debemos mostrar desde la fecha actual
	    // al mismo dia pero del mes anterior
	    if (self.mostrar_mes == "ANTERIOR") {
		fecha_desde.setDate(fecha_desde.getDate()-30);
	    }
	    else if (self.mostrar_mes == "SEMANA_ANTERIOR") {
		var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth(), d = fecha_desde.getDate();
		fecha_desde = new Date(y, m, d -fecha_desde.getDay()+1 -7);
		fecha_hasta = new Date(y, m, d -fecha_hasta.getDay()+1);
	    }
	    else if (self.mostrar_mes == "ACTUAL") {
		var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
		fecha_desde = new Date(y, m, 1);
		fecha_hasta = new Date(y, m + 1, 0);						
	    }
	    else if (self.mostrar_mes == "MES_ANTERIOR") {
		var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
		fecha_desde = new Date(y, m-1, 1);
		fecha_hasta = new Date(y, m, 0);						
	    }
	    else if (self.mostrar_mes == "TRES_MESES_ACTUAL") {
		// Toma un mes anterior, el mes actual, y el mes siguiente
		var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
		fecha_desde = new Date(y, m - 1, 1);
		fecha_hasta = new Date(y, m + 2, 0);						
	    }
	    
	    var fecha_desde_f = $.datepicker.formatDate("dd/mm/yy",fecha_desde);
            $(this.el).find(".fecha_desde").datepicker({
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
                "defaultDate":fecha_desde_f
            });

            var fecha_hasta_f = $.datepicker.formatDate("dd/mm/yy",fecha_hasta);
	    $(this.el).find(".fecha_hasta").datepicker({
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
                "defaultDate":fecha_hasta_f
            });
	    
	    if (self.mostrar_mes != "NINGUNO") {
		$(this.el).find(".fecha_desde").val(fecha_desde_f);
		$(this.el).find(".fecha_hasta").val(fecha_hasta_f);
	    }

            return this;
        }        
    });

})(app);
