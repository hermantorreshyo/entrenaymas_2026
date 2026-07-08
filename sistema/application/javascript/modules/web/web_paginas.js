// -----------
//   MODELO
// -----------

(function ( models ) {

    models.WebPagina = Backbone.Model.extend({
        urlRoot: "web_paginas/",
        defaults: {
            titulo_es: "",
            texto_es: "",
            breve_es: "",
            path: "",
            path_2: "",
            seo_title: "",
            seo_description: "",
            seo_keywords: "",
            categoria: "",
            id_categoria: 0,
            id_proyecto: ID_PROYECTO,
            activo: 1,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.WebPaginas = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "web_paginas/"
		},
		
	});

})( app.collections, app.models.WebPagina, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.WebPaginaItem = Backbone.View.extend({
        tagName: "tr",
        attributes: function() {
            return {
                id: this.model.id // Es necesario hacer esto para reordenar
            }
        },
        template: _.template($('#web_paginas_item').html()),
      	events: {
    		"click .edit": "editar",
    		"click .delete": "borrar",
    		"click .duplicar": "duplicar"
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
        	location.href="app/#web_pagina/"+this.model.id;
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

    app.views.WebPaginasTableView = app.mixins.View.extend({

    	template: _.template($("#web_paginas_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
            this.options = options;
			this.permiso = this.options.permiso;

			// Creamos la lista de web_paginacion
			var pagination = new app.mixins.PaginationView({
				collection: lista
			});

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

			lista.on('add', this.addOne, this);
			lista.on('reset', this.addAll, this);
			lista.on('all', this.render, this);

			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			// Cargamos el template
			$(this.el).html(this.template(obj));
			// Cargamos el web_paginador
			$(this.el).find(".pagination_container").html(pagination.el);
			// Cargamos el buscador
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo web_paginamos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.WebPaginaItem({
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

	views.WebPaginaEditView = app.mixins.View.extend({

		template: _.template($("#web_paginas_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
		},

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            this.options = options;
            _.bindAll(this);
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
            
            new app.mixins.Select({
                modelClass: app.models.WebCategoria,
                url: "web_categorias/",
                render: "#web_paginas_categorias",
                firstOptions: ["<option value='0'>Ninguna</option>"],
                campoSelect: "nombre_es",
                name : "id_categoria",
                selected: this.model.get("id_categoria"),
            });
            return this;
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("web_paginas_titulo_es",IS_EMPTY,"Por favor, ingrese un titulo_es.");
                
                this.model.set({
                    "id_categoria":$("#web_paginas_categorias").val()
                });
                
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                return true;                
                
            } catch(e) {
                return false;
            }
        },
        
        guardar: function() {
            var self = this;
            if (this.validar()) {
                var cktext = CKEDITOR.instances['web_paginas_texto'].getData();
                this.model.save({
                        "texto_es":cktext,
                        "path":$("#hidden_path").val(),
                        "path_2":$("#hidden_path_2").val(),
                    },{
                    success: function(model,response) {
                        show("Los datos han sido guardados correctamente.");
                        location.href="app/#web_paginas";
                    }
                });                 
            }
		},
        
        limpiar : function() {
            this.model = new app.models.WebPagina()
            this.render();
        },
		
	});

})(app.views, app.models);