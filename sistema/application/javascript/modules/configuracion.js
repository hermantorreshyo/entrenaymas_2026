// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Configuracion = Backbone.Model.extend({
        urlRoot: "configuracion/",
        defaults: {
            razon_social: "",
            facturacion_modificar_precio: 0,
            facturacion_modificar_descripcion: 0,
            facturacion_mostrar_fecha: 1,
            facturacion_mostrar_numero: 1,
            facturacion_mostrar_cliente: 1,
            facturacion_modificar_item: 0,
            facturacion_permite_anular_producto: 0,
            facturacion_editar_descuento: 0,
            facturacion_usa_nplu: 0,
            facturacion_identificador_plu: "",
            facturacion_largo_plu: 5,
            facturacion_controlar_caja_abierta: 1,
            facturacion_imp_fiscal: "",
            facturacion_forma_pago: "",
            facturacion_cantidad_items: 0,
        }
    });
	    
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.ConfiguracionEditView = app.mixins.View.extend({

		template: _.template($("#configuracion_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
            
            // Si se cambia el tipo de impresion
            "change input[name=facturacion_tipo_impresion]": function(e) {
                var v = $("input[name=facturacion_tipo_impresion]:checked").val();
                $("#facturacion_imp_fiscal").prop("disabled",(v != "F"));
            },
            
            // Si se cambia que usa o no NPLU
            "change input[name=facturacion_usa_nplu]": function(e) {
                var v = $("input[name=facturacion_usa_nplu]:checked").val();
                $("#configuracion_facturacion_identificador_plu").prop("disabled",(v != 1));
                $("#configuracion_facturacion_largo_plu").prop("disabled",(v != 1));
            },
		},

        initialize: function() {
            _.bindAll(this);
            this.render();
        },

        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
			var self = this;
        	var obj = { id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));
            
            var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
            $(this.el).find("#configuracion_fecha_inicio").datepicker({
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
			$(this.el).find("#configuracion_fecha_inicio").mask("99/99/9999");
            return this;
        },        
		
        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("configuracion_cotizacion_dolar",IS_NUMBER,"Por favor, ingrese un numero.");
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },

        guardar: function() 
        {
            var self = this;
            if (this.validar()) {
                this.model.save({},{
                    success: function(model,response) {
                        show("Los datos han sido guardados correctamente.");
                        location.reload();
                    }
                });
            }
		},		
	});

})(app.views, app.models);