// -----------
//   MODELO
// -----------

(function ( models ) {

    models.RssSource = Backbone.Model.extend({
        urlRoot: "rss_sources/",
        defaults: {
            nombre: "",
            url: "",
            activo: 1,
            noticias_cantidad: 0,
            tiempo: 30,
            noticias_activo: 0,
            noticias_destacado: 0,
            noticias_etiquetas: "",
            noticias_id_categoria: 0,
            noticias_incluir_contenido: 0,
            noticias_path_contenido: "",
            reemplazos: "",
            fuente: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.RssSources = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "rss_sources/"
		}
		
	});

})( app.collections, app.models.RssSource, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.RssSourceItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#rss_sources_item').html()),
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
        	location.href="app/#rss_source/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar la etiqueta?")) {
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

    app.views.RssSourcesTableView = app.mixins.View.extend({

    	template: _.template($("#rss_sources_panel_template").html()),

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
			var view = new app.views.RssSourceItem({
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

	views.RssSourceEditView = app.mixins.View.extend({

		template: _.template($("#rss_sources_edit_panel_template").html()),

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
            var self = this;
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

        	$(this.el).html(this.template(obj));
            
            /*
            if (self.model.get("noticias_etiquetas").length>0) {
                $(this.el).find("#rss_sources_noticias_etiquetas").val(self.model.get("noticias_etiquetas"));
            }            
            // Cargamos las etiquetas con AJAX
            $.ajax({
                "url":"entradas_etiquetas/",
                "dataType":"json",
                "success":function(r) {
                    var entradas_etiquetas = new Array();
                    for(var i=0;i<r.results.length;i++) {
                        var a = r.results[i];
                        entradas_etiquetas.push(a.nombre);
                    }
                    $(self.el).find("#rss_sources_noticias_etiquetas").select2({
                        tags: entradas_etiquetas,
                    });
                }
            });
            */
            
            var r = "<option value='0'>-</option>"+workspace.crear_select(categorias_noticias,"",self.model.get("noticias_id_categoria"));
            this.$("#rss_sources_categorias").html(r);

            return this;
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("rss_sources_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                
                // Las etiquetas se tratan como array porque son entidades separadas
                //var etiquetas = this.$("#rss_sources_noticias_etiquetas").select2("val");
                this.model.set({
                    //"noticias_etiquetas":etiquetas.join(";;;"),
                    "noticias_id_categoria":self.$("#rss_sources_categorias").val(),
                });
                
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },
        

        guardar: function() {
            var self = this;
            if (this.validar()) {
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({
                        "id_empresa":ID_EMPRESA,
                    },{
                    success: function(model,response) {
                        location.href="app/#rss_sources";
                    }
                });
            }
		},
		
        limpiar : function() {
            this.model = new app.models.RssSource();
            this.render();
        },
		
	});

})(app.views, app.models);