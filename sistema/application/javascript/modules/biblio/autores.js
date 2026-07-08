// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Autor = Backbone.Model.extend({
        urlRoot: "autores/",
        defaults: {
            nombre: "",
            id_empresa: ID_EMPRESA,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Autores = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "autores/"
		}
		
	});

})( app.collections, app.models.Autor, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.AutorItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#autores_item').html()),
      	events: {
    		"click": "editar",
    		"click .ver": "editar",
    		"click .delete": "borrar",
    		"click .duplicar": "duplicar",
            "click .buscar_libros": "buscar_libros",
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
        	location.href="app/#autor/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
            e.stopPropagation();
        },
        buscar_libros: function(e) {
            e.stopPropagation();
            location.href = "app/#libros/autor/"+this.model.id;
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

    app.views.AutoresTableView = app.mixins.View.extend({

    	template: _.template($("#autores_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
            this.options = options;
			this.permiso = this.options.permiso;

			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
				collection: lista
			});

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

			lista.on('add', this.addOne, this);
			lista.on('all', this.addAll, this);
			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			// Cargamos el template
			$(this.el).html(this.template(obj));
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
			// Cargamos el buscador
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo paginamos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.AutorItem({
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

	views.AutorEditView = app.mixins.View.extend({

		template: _.template($("#autores_edit_panel_template").html()),

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

        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));

            return this;
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("autores_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
                    },{
                    success: function(model,response) {
                        location.href="app/#autores";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.Autor()
            this.render();
        },
		
	});

})(app.views, app.models);





(function ( views, models ) {

	views.AutorEditViewMini = app.mixins.View.extend({

		template: _.template($("#autores_edit_mini_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .cerrar": "cerrar",
            "keyup #autores_mini_nombre":function() {
                // Tenemos enlazada la referencia, por lo que cada vez que escribimos algo, debemos cambiar el input original
                if (this.input != undefined) {
                    $(this.input).val($(this.el).find("#autores_mini_nombre").val());
                }
            },
            "keypress .tab":function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $(e.currentTarget).parent().next().find(".tab").focus();
                }
            },
            "keyup .tab":function(e) {
                if (e.which == 27) this.cerrar();
            },
            "keypress .guardar":function(e) {
                if (e.keyCode == 13) this.guardar();
            },
		},
        
        initialize: function(options) {
            this.options = options;
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.input = this.options.input;
            this.onSave = this.options.onSave;
            this.callback = this.options.callback;
            this.render();
        },

        render: function() {
            var self = this;
        	$(this.el).html(this.template(this.model.toJSON()));
            if (this.input != undefined) {
                // Seteamos lo que tiene el input de referencia
                $(this.el).find("#autores_mini_nombre").val($(this.input).val().trim());
            }
            return this;
        },
        
        focus: function() {
            $(this.el).find("#autores_mini_nombre").focus();
        },

        validar: function() {
            try {
                validate_input("autores_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
                        "nombre":$("#autores_mini_nombre").val(),
                    },{
                    success: function(model,response) {
                        if (response.error == 1) {
                            show(response.mensaje);
                        } else {
                            if (typeof self.onSave != "undefined") self.onSave(model);
                            if (typeof self.callback != "undefined") self.callback(model.id);
                            self.cerrar();
                        }
                    }
                });
            }
		},
		
        cerrar: function() {
            $(this.el).parents(".customcomplete").remove();
        },		
        
        limpiar : function() {
            this.model = new app.models.Autor()
            this.render();
        },        
		
	});

})(app.views, app.models);