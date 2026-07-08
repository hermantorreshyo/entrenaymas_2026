// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ChatPregunta = Backbone.Model.extend({
    urlRoot: "chat_preguntas/",
    defaults: {
      tipo: "questions",
      orden: 0,
      pregunta: "",
      pregunta_en: "",
      success_text: "",
      success_text_en: "",
      fail_text: "",
      fail_text_en: "",
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.ChatPreguntas = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "chat_preguntas/"
		}
		
	});

})( app.collections, app.models.ChatPregunta, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ChatPreguntaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#chat_preguntas_item').html()),
    events: {
  		"click .ver": "editar",
  		"click .delete": "borrar",
  		"click .duplicar": "duplicar"
  	},
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      _.bindAll(this);
    },
    render: function() {
    	var obj = { id: this.model.id };
    	$.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
    	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
    	location.href="app/#chat_pregunta/"+this.model.id;
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

  app.views.ChatPreguntasTableView = app.mixins.View.extend({

  	template: _.template($("#chat_preguntas_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
      this.options = options;

			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
				collection: lista
			});

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

      this.collection.on('sync', this.addAll, this);
			
			var obj = { };
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
			var view = new app.views.ChatPreguntaItem({
				model: item,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.ChatPreguntaEditView = app.mixins.View.extend({

		template: _.template($("#chat_preguntas_edit_panel_template").html()),

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
      var obj = { id:this.model.id };
    	$.extend(obj,this.model.toJSON());
    	$(this.el).html(this.template(obj));
      this.$("chat_preguntas_clave").val(this.model.get("clave"));
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
            "clave":$("#chat_preguntas_clave").val(),
          },{
          success: function(model,response) {
            location.href="app/#chat_preguntas";
          }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.ChatPregunta();
      this.render();
    },
		
	});

})(app.views, app.models);