// -----------
//   MODELO
// -----------

(function ( models ) {

  models.WebTexto = Backbone.Model.extend({
    urlRoot: "web_textos/",
    defaults: {
      titulo: "",
      texto: "",
      texto_en: "",
      texto_pt: "",
      clave: "",
      link: "",
      id_empresa: 0,
      id_web_template: 0,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.WebTextos = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "web_textos/"
		},
		
	});

})( app.collections, app.models.WebTexto, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.WebTextoItem = Backbone.View.extend({
    tagName: "tr",
    attributes: function() {
      return { 
        id: this.model.id // Es necesario hacer esto para reordenar
      }
    },
    template: _.template($('#web_textos_item').html()),
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
    render: function() {
    	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
    	var obj = { permiso: this.permiso };
    	// Extendemos el objeto creado con el modelo de datos
    	$.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
    	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
    	location.href="app/#web_texto/"+this.model.id;
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

  app.views.WebTextosTableView = app.mixins.View.extend({

    template: _.template($("#web_textos_panel_template").html()),

    initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

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
			$(this.el).find(".pagination_container").html(pagination.el);
			$(this.el).find(".search_container").html(search.el);

			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.WebTextoItem({
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

	views.WebTextoEditView = app.mixins.View.extend({

		template: _.template($("#web_textos_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
      "click #link_tab2":function() {
        if (typeof CKEDITOR.instances["web_textos_texto_en"] == "undefined") { 
          workspace.crear_editor('web_textos_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_tab3":function() {
        if (typeof CKEDITOR.instances["web_textos_texto_pt"] == "undefined") {
          workspace.crear_editor('web_textos_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      this.options = options;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      this.lightbox = (typeof this.options.lightbox != "undefined") ? this.options.lightbox : false;
      this.es_imagen = (typeof this.options.es_imagen != "undefined") ? this.options.es_imagen : false;
      this.width = (typeof this.options.width != "undefined") ? this.options.width : 256;
      this.height = (typeof this.options.height != "undefined") ? this.options.height : 256;
      this.quality = (typeof this.options.quality != "undefined") ? this.options.quality : 0.9;
      var obj = { 
        "edicion": edicion, 
        "id": this.model.id, 
        "lightbox": this.lightbox,
        "es_imagen": this.es_imagen,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      new app.mixins.Select({
        modelClass: app.models.WebTemplate,
        url: "web_templates/",
        render: "#web_textos_templates",
        selected: self.model.get("id_web_template"),
      });

      if (this.es_imagen) {
        this.$("#texto_width").val(this.width);
        this.$("#texto_height").val(this.height);
        this.$("#texto_quality").val(this.quality);
      }
      
      return this;
    },

    validar: function() {
      var self = this;
      try {
          // Validamos los campos que sean necesarios
          if (!this.lightbox) {
            validate_input("web_textos_titulo",IS_EMPTY,"Por favor, ingrese un titulo.");
            var id_web_template = $("#web_textos_templates").val();
            this.model.set({
              "id_web_template":id_web_template,
            });
          }
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
          if (!this.es_imagen) {
            if (typeof CKEDITOR.instances['web_textos_texto'] != "undefined") {
              var texto = CKEDITOR.instances['web_textos_texto'].getData();    
              this.model.set({
                "texto":texto,
              });
            }
            if (typeof CKEDITOR.instances['web_textos_texto_en'] != "undefined") {
              var texto_en = CKEDITOR.instances['web_textos_texto_en'].getData();    
              this.model.set({
                "texto_en":texto_en,
              });
            }
            if (typeof CKEDITOR.instances['web_textos_texto_pt'] != "undefined") {
              var texto_pt = CKEDITOR.instances['web_textos_texto_pt'].getData();    
              this.model.set({
                "texto_pt":texto_pt,
              });
            }
          } else {
            this.model.set({"texto": "/sistema/"+self.$("#hidden_texto").val()});
          }
          this.model.save({},{
            success: function(model,response) {
              if (self.lightbox) location.reload();
              else location.href="app/#web_textos";
            }
          });                 
        }
      },

      limpiar : function() {
        this.model = new app.models.WebTexto()
        this.render();
      },

    });

})(app.views, app.models);