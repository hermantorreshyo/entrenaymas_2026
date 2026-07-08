// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Localidad = Backbone.Model.extend({
    urlRoot: "localidades/",
    defaults: {
      nombre: "",
      link: "",
			codigo_postal : "",
      departamento: "",
      provincia: "",
      pais: "",
    }
  });
	  
})( app.models );


(function ( models ) {

  models.Barrio = Backbone.Model.extend({
    urlRoot: "barrios/",
    defaults: {
      nombre: "",
      id_localidad_inmobusqueda: 0,
      id_partido_inmobusqueda: 0,
      id_provincia_inmobusqueda: 0,
    }
  });
    
})( app.models );


(function ( models ) {
  models.ComDepartamento = Backbone.Model.extend({
    urlRoot: "com_departamentos/",
    defaults: {
      nombre: "",
    }
  });
})( app.models );

(function ( models ) {
  models.Provincia = Backbone.Model.extend({
    urlRoot: "provincias/",
    defaults: {
      nombre: "",
    }
  });
})( app.models );

(function ( models ) {
  models.Pais = Backbone.Model.extend({
    urlRoot: "paises/",
    defaults: {
      nombre: "",
    }
  });
})( app.models );


(function (collections, model, paginator) {
  collections.Barrios = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "barrios/"
    }
  });
})( app.collections, app.models.Barrio, Backbone.Paginator);


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Localidades = paginator.requestPager.extend({

	model: model,

	paginator_core: {
	  url: "localidades/"
	}
	  
  });

})( app.collections, app.models.Localidad, Backbone.Paginator);


// ----------------------
//   COLECCION NORMAL
// ----------------------

(function (collections, model) {

  collections.LocalidadesList = Backbone.Collection.extend({
    model: model,
    url: "localidades/",
    initialize: function() {
      this.fetch();
    },
    parse: function(response) {
      return response.results;
    }
  });

})( app.collections, app.models.Localidad);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.LocalidadItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#localidades_item').html()),
    	events: {
	  "click": "editar",
	  "click .ver": "editar",
	  "click .delete": "borrar",
	  "click .duplicar": "duplicar"
  	},
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function()
    {
	  // Creamos un objeto para agregarle las otras propiedades que no son el modelo
	  var obj = {
			permiso: this.permiso,
			id: this.model.id
		};
	  // Extendemos el objeto creado con el modelo de datos
	  $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      location.href="app/#localidad/"+this.model.id;
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
      e.stopPropagation();
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.LocalidadesTableView = Backbone.View.extend({

  	template: _.template($("#localidades_panel_template").html()),

	initialize : function (options) {

	  _.bindAll(this); // Para que this pueda ser utilizado en las funciones

	  var lista = this.collection;

	  // Guardamos las referencias
    this.options = options;
	  this.permiso = this.options.permiso;

	  // Creamos la lista de paginacion
	  var pagination = new app.mixins.PaginationView({
		  collection: lista,
	  });

	  // Creamos el buscador
	  var search = new app.mixins.SearchView({
		  collection: lista
	  });

	  lista.on('add', this.addOne, this);
	  lista.on('pager', this.addAll, this);
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
	},

	addOne : function ( item ) {
	  var view = new app.views.LocalidadItem({
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

  views.LocalidadEditView = app.mixins.View.extend({

    template: _.template($("#localidades_edit_panel_template").html()),
  
    myEvents: {
      "click .guardar": "guardar",
      "click .limpiar": "limpiar",
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id: this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      return this;
    },

    validar: function() {
      try {
        var self = this;
        // Validamos los campos que sean necesarios
        validate_input("localidades_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        
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
        if (this.model.id == null || this.model.id == 0) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            show("Los datos han sido guardados con exito.");
            location.href="app/#localidades";
          }
        });
      }
    },
		
    limpiar : function() {
      this.model = new app.models.Localidad()
      this.render();
    },		
  });

})(app.views, app.models);
