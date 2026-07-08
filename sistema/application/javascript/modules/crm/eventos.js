// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Evento = Backbone.Model.extend({
        urlRoot: "eventos",
        defaults: {
            nombre: "",
            id_tipo_evento: 0,
            id_empresa: ID_EMPRESA,
            fecha_inicio: "",
            fecha_fin: "",
            id_usuario: 0,
            id_propiedad: 0,
            id_contacto: 0,
            lugar: "",
            recordar_cantidad: 0,
            recordar_tipo: "M", // D = Dias, H = horas, M = minutos
            observaciones: "",
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Eventos = paginator.clientPager.extend({

        model: model,
        
        paginator_core: {
            url: "eventos/function/ver",
        }
	    
    });

})( app.collections, app.models.Evento, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.EventosTableView = app.mixins.View.extend({

        template: _.template($("#eventos_resultados_template").html()),
            
        myEvents: {
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
			this.permiso = this.options.permiso;
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
            }));
            this.render();
		},
        
        render: function() {
            this.$("#calendar").fullCalendar({
                height: 600,
                lang: "es",
                eventStartEditable: false,
                eventDurationEditable: false,
                /*eventSources : [{
                    url: "get_reservas.php",
                    textColor: 'black'
                }],*/
                eventClick: function(calEvent, jsEvent, view) {
                    /*
                    $.ajax({
                        "url":"get_reserva.php?id="+calEvent.id,
                        "dataType":"json",
                        "success":function(r) {
                            if (r.error == 1) {
                                alert("Error al mostrar la reserva.");
                                return;
                            }
                            $("#reserva_eliminar").show();
                            $("#reserva_id").val(r.id);
                            $("#reserva_nombre").val(r.nombre);
                            $("#reserva_email").val(r.email);
                            $("#reserva_telefono").val(r.telefono);
                            $("#reserva_desde").val(r.desde);
                            $("#reserva_cantidad").val(r.cantidad);
                            $("#reserva_departamento").val(r.id_departamento);
                            $("#reserva_hasta").val(r.hasta);
                            $("#reserva_precio").val(r.precio);
                            $("#reserva_comentario").val(r.comentario);
                            $('#reservaModal').modal({
                                show: true
                            });                    
                        }
                    });
                    */
                },
                dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
                dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
                monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
                monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
            });            
        },
    });

})(app);


// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.EventosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#eventos_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            "click .data":"seleccionar",
            "keyup .radio":function(e) {
                if (e.which == 13) { this.seleccionar(); }
                e.stopPropagation();
            },
            "focus .radio":function(e) {
                $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
				$(e.currentTarget).parents("tr").addClass("fila_roja");
				$(e.currentTarget).prop("checked",true);
                e.stopPropagation();
                e.preventDefault();
                return false;
            },
			"blur .radio":function(e) {
				$(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
				$(".radio").prop("checked",false);
                e.stopPropagation();
                e.preventDefault();
                return false;
			},
            "click .destacado":function(e) {
                e.stopPropagation();
                e.preventDefault();
                var destacado = this.model.get("destacado");
                destacado = (destacado == 1)?0:1;
                this.model.save({
                    "destacado":destacado
                });
                this.render();
                return false;
            },
            "click .eliminar":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                if (confirm("Realmente desea eliminar el elemento?")) {
                    this.model.destroy();	// Eliminamos el modelo
                    $(this.el).remove();	// Lo eliminamos de la vista
                }
                return false;
            },            
        },
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.render();
        },
        render: function() {
        	var obj = { seleccionar: this.habilitar_seleccion };
        	$.extend(obj,this.model.toJSON());
            $(this.el).html(this.template(obj));
            return this;
        },
    });
})(app);



(function ( app ) {

    app.views.EventoView = app.mixins.View.extend({

        template: _.template($("#evento_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
        },
		
        initialize: function(options) {
            var self = this;
            this.options = options;
            _.bindAll(this);
            
        	var edicion = false;
        	if (this.options.permiso > 1) edicion = true;
        	var obj = { "edicion": edicion,"id":this.model.id }
            _.extend(obj,this.model.toJSON());
        	$(this.el).html(this.template(obj));
        },
        
        validar: function() {
            try {
                var self = this;
                validate_input("evento_nombre",IS_EMPTY,"Por favor, ingrese un destinatario del evento.");
                
                var cktext = CKEDITOR.instances['evento_texto'].getData();
                self.model.set({"texto":cktext});
                return true;
            } catch(e) {
                return false;
            }
        },	
	
        guardar:function() {
            if (this.validar()) {
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({},{
                    success: function(model,response) {
                        if (response.error == 1) {
                            show(response.mensaje);
                            return;
                        } else {
                            eventos.add(model);
                            $('.modal:last').modal('hide');
                        }
                    }
                });
            }	    
        },
        
    });
})(app);
