// -----------
//   MODELO
// -----------

(function ( models ) {

    models.GaleriaCategoria = Backbone.Model.extend({
        urlRoot: "galerias_categorias/",
        defaults: {
            nombre: "",
            path: "",
            id_padre: 0,
            activo: 1,
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.GaleriasCategorias = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "galerias_categorias/"
		}
		
	});

})( app.collections, app.models.GaleriaCategoria, Backbone.Paginator);




// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

    app.views.GaleriasCategoriasTreeView = app.mixins.View.extend({

        template: _.template($("#galerias_categorias_tree_panel_template").html()),
        
        myEvents: {
            "click .editar":function(e) {
                var self = this;
                e.preventDefault();
                var id = $(e.currentTarget).parents(".dd-item").data("id");
                var cat = new app.models.GaleriaCategoria({ id: id });
                cat.fetch({
                    "success":function(){
                        self.ver(cat);
                    }
                });
            },
            "click .nuevo":function() {
                var modelo = new app.models.GaleriaCategoria();
                this.ver(modelo);
            },
        },
        
        ver: function(modelo) {
            var categoria = new app.views.GaleriaCategoriaEditView({
                model: modelo,
                permiso: 3,
            });
            var d = $("<div/>").append(categoria.el);
            crearLightboxHTML({
                "html":d,
                "width":860,
                "height":500,
            });
        },
    
        initialize : function () {
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.render();
        },
        
        render : function() {
            
            var self = this;
            $(this.el).html(this.template());
            $.ajax({
                "url":"galerias_categorias/function/get_arbol/",
                "dataType":"json",
                "success":function(r){
                    $("#galerias_categorias_nestable").append(workspace.crear_nestable(r));
                    self.$('.dd').nestable();
                    self.$('.dd').on('change',self.reorder);            
                },
            });
            return this;	    
        },        

        reorder: function() {
            var serialize = this.$('.dd').nestable('serialize');
            $.ajax({
                "url":"galerias_categorias/function/reorder/",
                "type":"post",
                "dataType":"json",
                "data":{
                    "datos":serialize,
                }
            });
        },

    });
})(app);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.GaleriaCategoriaItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#galerias_categorias_item').html()),
      	events: {
    		"click .edit": "editar",
    		"click .ver": "editar",
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
        	location.href="app/#galeria_categoria/"+this.model.id;
        },
        borrar: function() {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
        },
        duplicar: function() {
        	var clonado = this.model.clone();
        	clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
        	clonado.save({},{
        		success: function(model,response) {
        			model.set({id:response.id});
        		}
        	});
        	this.model.collection.add(clonado);
        }
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.GaleriasCategoriasTableView = Backbone.View.extend({

    	template: _.template($("#galerias_categorias_panel_template").html()),

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
			lista.on('reset', this.addAll, this);
			lista.on('all', this.render, this);

			
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
			var view = new app.views.GaleriaCategoriaItem({
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

	views.GaleriaCategoriaEditView = app.mixins.View.extend({

		template: _.template($("#galerias_categorias_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
            "click .eliminar": "eliminar",
		},
        
        eliminar : function() {
            if (!confirmar("Realmente desea eliminar este elemento?")) return;
            var self = this;	    
            var galeria_categoria = new app.models.GaleriaCategoria({
                "id":self.model.id
            });
            galeria_categoria.destroy();
            galeria_categoria.fetch({
                "success":function() {
                    location.reload();
                }
            });
        },        

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            this.options = options;
            _.bindAll(this);
            this.render();
        },

        render: function()
        {
            var self = this;
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));
            
            new app.mixins.Select({
                modelClass: app.models.GaleriaCategoria,
                url: "galerias_categorias/function/get_select/",
                render: "#galerias_categorias_padre",
                firstOptions: ["<option value='0'>Ninguno</option>"],
                name : "id_padre",
                selected: this.model.get("id_padre"),
            });            

            return this;
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("galerias_categorias_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
                self.model.set({
                    "path":self.$("#hidden_path").val(),
                });
                
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
                        "id_padre":$("#galerias_categorias_padre").val(),
                    },{
                    success: function(model,response) {
                        location.reload();
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.GaleriaCategoria()
            this.render();
        },
		
	});

})(app.views, app.models);