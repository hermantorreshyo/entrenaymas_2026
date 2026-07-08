// -----------
//   MODELO
// -----------

(function ( models ) {

    models.WebTemplate = Backbone.Model.extend({
        urlRoot: "web_templates/",
        defaults: {
            nombre: "",
            path: "",
            thumbnail: "",
            preview: "",
            config: "",
            publico: 0,
            id_empresa: 0,
            id_proyecto: 0,
            link_demo: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.WebTemplates = paginator.requestPager.extend({

		model: model,

        paginator_ui: {
            perPage: 10,
            order_by: 'nombre',
            order: 'asc',
        },

		paginator_core: {
			url: "web_templates/"
		},
		
	});

})( app.collections, app.models.WebTemplate, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.WebTemplateItem = Backbone.View.extend({
        tagName: "tr",
        attributes: function() {
            return {
                id: this.model.id // Es necesario hacer esto para reordenar
            }
        },
        template: _.template($('#web_templates_item').html()),
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
        	location.href="app/#web_template/"+this.model.id;
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

    app.views.WebTemplatesTableView = app.mixins.View.extend({

    	template: _.template($("#web_templates_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
            this.options = options;
			this.permiso = this.options.permiso;

			// Creamos la lista de web_templatecion
			var pagination = new app.mixins.PaginationView({
				collection: lista
			});

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

            lista.on('sync', this.addAll, this);
			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			// Cargamos el template
			$(this.el).html(this.template(obj));
			// Cargamos el web_templatedor
			$(this.el).find(".pagination_container").html(pagination.el);
			// Cargamos el buscador
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo web_templatemos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.WebTemplateItem({
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

	views.WebTemplateEditView = app.mixins.View.extend({

		template: _.template($("#web_templates_edit_panel_template").html()),

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
            var self = this;
            var edicion = false;
              if (this.options.permiso > 1) edicion = true;
              var obj = { edicion: edicion, id:this.model.id };
            // Extendemos el objeto creado con el modelo de datos
            $.extend(obj,this.model.toJSON());
            $(this.el).html(this.template(obj));
            
            new app.mixins.Select({
                modelClass: app.models.Proyecto,
                url: "proyectos/",
                render: "#web_templates_proyectos",
                selected: self.model.get("id_proyecto"),
            });            
            
            new app.mixins.Select({
                modelClass: app.models.Empresa,
                url: "empresas/",
                render: "#web_templates_empresas",
                firstOptions: ["<option value='0'>-</option>"],
                selected: self.model.get("id_empresa"),
                onComplete:function(c) {
                    $("#web_templates_empresas").select2({});
                }
            });

            return this;
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("web_templates_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                
                this.model.set({
                    "id_empresa":self.$("#web_templates_empresas").val(),
                    "id_proyecto":self.$("#web_templates_proyectos").val(),
                    "thumbnail":self.$("#hidden_thumbnail").val(),
                    "preview":self.$("#hidden_preview").val(),
                });
                
                return true;                
            } catch(e) {
                return false;
            }
        },
        
        guardar: function() {
            var self = this;
            if (this.validar()) {
                this.model.save({
                    },{
                    success: function(model,response) {
                        location.href="app/#web_templates";
                    }
                });                 
            }
		},
        
        limpiar : function() {
            this.model = new app.models.WebTemplate()
            this.render();
        },
		
	});

})(app.views, app.models);