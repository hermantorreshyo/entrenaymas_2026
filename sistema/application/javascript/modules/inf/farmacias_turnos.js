// -----------
//   MODELO
// -----------

(function ( models ) {

    models.FarmaciaTurno = Backbone.Model.extend({
        urlRoot: "farmacias_turnos",
        defaults: {
            farmacia: "",
            id_farmacia: 0,
            id_empresa: ID_EMPRESA,
            fecha: "",
        },
    });
	    
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.FarmaciasTurnosTableView = app.mixins.View.extend({

        template: _.template($("#farmacias_turnos_template").html()),
            
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
            
            setTimeout(function(){
                self.render();
            },200);
        },
        
        render: function() {
            this.$("#calendar").fullCalendar({
                height: 600,
                lang: "es",
                eventStartEditable: false,
                eventDurationEditable: false,
                eventSources : "farmacias_turnos/function/guardia/",
                dayClick: function(day, jsEvent, view) {
                    var modelo = new app.models.FarmaciaTurno({
                        fecha: day.format("DD/MM/YYYY")
                    });
                    app.views.turno = new app.views.FarmaciaTurnoView({
                        "model":modelo
                    });
                    crearLightboxHTML({
                        "html":app.views.turno.el,
                        "width":450,
                        "height":140,
                    });
                },
                eventClick: function(calEvent, jsEvent, view) {
                    // Creamos un modelo con esos datos
                    var modelo = new app.models.FarmaciaTurno({
                        id: calEvent.id,
                        fecha: calEvent.start.format("DD/MM/YYYY"),
                        id_empresa: calEvent.id_empresa,
                        id_farmacia: calEvent.id_farmacia,
                    });
                    app.views.turno = new app.views.FarmaciaTurnoView({
                        "model":modelo
                    });
                    crearLightboxHTML({
                        "html":app.views.turno.el,
                        "width":450,
                        "height":140,
                    });
                },
                dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
                dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
                monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
                monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
            });
            this.$("#calendar").fullCalendar('render');
        },
        
    });

})(app);


(function ( app ) {

    app.views.FarmaciaTurnoView = app.mixins.View.extend({

        template: _.template($("#farmacia_turno_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .eliminar": "eliminar",
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
            
            new app.mixins.Select({
                modelClass: app.models.Farmacia,
                url: "farmacias/",
                render: "#farmacia_turno_farmacias",
                name : "id_farmacia",
                selected: this.model.get("id_farmacia"),
            });
            
            var fecha = this.model.get("fecha")
            $(this.el).find("#farmacia_turno_fecha").datepicker({
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
            this.$("#farmacia_turno_fecha").val(fecha);
            this.$("#farmacia_turno_fecha").mask("99/99/9999");
        },
        
        validar: function() {
            try {
                var self = this;
                var id_farmacia = this.$("#farmacia_turno_farmacias").val();
                this.model.set({
                    "id_farmacia":id_farmacia,
                });
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
                            $('.modal:last').modal('hide');
                            $('#calendar').fullCalendar('refetchEvents');
                        }
                    }
                });
            }	    
        },
        
        eliminar: function() {
            if (!confirm("Realmente desea eliminar este turno?")) return;
            $.ajax({
                "url":"farmacias_turnos/"+this.model.id,
                "type":"delete",
                "success":function() {
                    $('.modal:last').modal('hide');
                    $('#calendar').fullCalendar('refetchEvents');
                }
            })
        }
        
    });
})(app);
