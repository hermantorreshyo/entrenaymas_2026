// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Video = Backbone.Model.extend({
        urlRoot: "videos/",
        defaults: {
            clave: "",
            nombre_es: "",
            nombre_en: "",
            texto_es: "",
            texto_en: "",
            video_es: "",
            video_en: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Videos = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "videos/"
		}
		
	});

})( app.collections, app.models.Video, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.VideoItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#videos_item').html()),
      	events: {
    		"click": "editar",
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
        	location.href="app/#video/"+this.model.id;
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

    app.views.VideosTableView = app.mixins.View.extend({

    	template: _.template($("#videos_panel_template").html()),

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

            this.collection.on('sync', this.addAll, this);
			
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
			var view = new app.views.VideoItem({
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

	views.VideoEditView = app.mixins.View.extend({

		template: _.template($("#videos_edit_panel_template").html()),

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
            this.$("videos_clave").val(this.model.get("clave"));
            return this;
        },

        validar: function() {
            try {
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
                        "clave":$("#videos_clave").val(),
                    },{
                    success: function(model,response) {
                        location.href="app/#videos";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.Video();
            this.render();
        },
		
	});

})(app.views, app.models);