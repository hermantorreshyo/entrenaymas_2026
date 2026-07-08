// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Habitacion = Backbone.Model.extend({
        urlRoot: "habitaciones/",
        defaults: {
            nombre: "",
            tipo: "",
            id_tipo_habitacion: 0,
            activo: 1,
            capacidad: 0,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Habitaciones = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "habitaciones/"
		}
		
	});

})( app.collections, app.models.Habitacion, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.HabitacionItem = app.mixins.View.extend({
        tagName: "tr",
        template: _.template($('#habitaciones_item').html()),
      	myEvents: {
    		"click .ver": "editar",
    		"click .delete": "borrar",
    		"click .duplicar": "duplicar",
            "click .activo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"hot_habitaciones",
                  "url":"habitaciones/function/change_property/",
                  "attribute":"activo",
                  "value":activo,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
                return false;
            },
    	},
        initialize: function(options) {
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.permiso = this.options.permiso;
            _.bindAll(this);
        },
        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var obj = { permiso: this.permiso };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

            $(this.el).html(this.template(obj));
            return this;
        },
        editar: function() {
        	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
        	location.href="app/#habitacion/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
            e.stopPropagation();
        },
        duplicar: function(e) {
        	var clonado = this.model.clone();
        	clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
        	clonado.save({},{
        		success: function(model,response) {
        			model.set({id:response.id});
        		}
        	});
        	this.model.collection.add(clonado);
            e.stopPropagation();
        }
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.HabitacionesTableView = app.mixins.View.extend({

    	template: _.template($("#habitaciones_panel_template").html()),

        initialize : function (options) {
            
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.permiso = this.options.permiso;

            // Filtros de la tipo_habitacion
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            this.render();
            this.collection.on('sync', this.addAll, this);

            this.collection.server_api = {
                "filter":this.filter,
            };            
            this.collection.goTo(this.pagina);
        },

        render: function() {
            // Creamos la lista de paginacion
            var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
                collection: this.collection
            });

            // Creamos el buscador
            var search = new app.mixins.SearchView({
                collection: this.collection
            });
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion,
            }));
            
            // Cargamos el paginador
            this.$(".pagination_container").html(pagination.el);
            this.$(".search_container").html(search.el);            

            return this;
        },
        
        buscar: function() {
            this.filter = this.$("#tipos_habitaciones_buscar").val().trim();
            this.collection.server_api = {
                "filter":this.filter,
            };
            this.collection.pager();            
        },

		addAll : function () {
            $(this.el).find(".tbody").empty();
            // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
            if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
                if (this.collection.length > 0) {
                    this.$(".seccion_vacia").hide();
                    this.$(".seccion_llena").show();
                } else {
                    this.$(".seccion_llena").hide();
                    this.$(".seccion_vacia").show();
                }
            }
            // Renderizamos cada elemento del array
            if (this.collection.length > 0) this.collection.each(this.addOne);            
		},

		addOne : function ( item ) {
			var view = new app.views.HabitacionItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.HabitacionEditView = app.mixins.View.extend({

		template: _.template($("#habitaciones_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
		},

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.options = options;
            this.render();
        },

        render: function() {
            var self = this;
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));

            new app.mixins.Select({
                modelClass: app.models.TipoHabitacion,
                url: "tipos_habitaciones/",
                render: "#habitacion_tipos",
                selected: self.model.get("id_tipo_habitacion"),
                onComplete:function(c) {
                    $("#habitacion_tipos").select2({});
                }
            });

            return this;
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("habitaciones_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({
                        "id_empresa":ID_EMPRESA,
                        "id_tipo_habitacion":$("#habitacion_tipos").val(),
                        "tipo":$("#habitacion_tipos option:selected").text(),
                    },{
                    success: function(model,response) {
                        location.href="app/#habitaciones";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.Habitacion()
            this.render();
        },
		
	});

})(app.views, app.models);