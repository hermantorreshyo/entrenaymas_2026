// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Almacen = Backbone.Model.extend({
    urlRoot: "almacenes/",
    defaults: {
      nombre: "",
      direccion: "",
      puntos_venta: [],
      id_centro_costo: 0,
      id_razon_social: 0,
      orden: 0,
      para_retiro: 0,
      centro_costo: "",
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Almacenes = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "almacenes/"
		}
		
	});

})( app.collections, app.models.Almacen, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.AlmacenItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#almacenes_item').html()),
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
    	location.href="app/#almacen/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("ATENCION: Si elimina el almacen se eliminara todo el stock asociado al mismo. Realmente desea hacerlo?")) {
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

  app.views.AlmacenesTableView = app.mixins.View.extend({

  	template: _.template($("#almacenes_panel_template").html()),

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
			var view = new app.views.AlmacenItem({
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

	views.AlmacenEditView = app.mixins.View.extend({

		template: _.template($("#almacenes_edit_panel_template").html()),

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

      var puntos_venta = new Array();
      _.each(this.model.get("puntos_venta"),function(elem){
        puntos_venta.push(elem.id);
      });
      
      if (control.check("puntos_venta")>0) {
        new app.mixins.Select({
          modelClass: app.models.PuntoVenta,
          url: "puntos_venta/",
          render: "#almacen_puntos_venta",
          selected: puntos_venta,
          campoSelect: "numero",
          multiple: true,
          onComplete:function(c) {
            $("#almacen_puntos_venta").chosen({
              "placeholder_text_multiple":"Seleccione",
            });
          }
        });
      }

      if (control.check("razones_sociales")>0) { 
        new app.mixins.Select({
          modelClass: app.models.RazonSocial,
          url: "razones_sociales/",
          render: "#almacen_razones_sociales",
          firstOptions: ["<option value='0'>Seleccione</option>"],
          selected: self.model.get("id_razon_social"),
        });
      }      

      new app.mixins.Select({
        modelClass: app.models.CentroCosto,
        url: "centros_costos/",
        render: "#almacen_centros_costos",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        selected: self.model.get("id_centro_costo"),
      });

      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("almacenes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        if (control.check("puntos_venta")>0) {
          var puntos_venta = $("#almacen_puntos_venta").val();
          if (puntos_venta == null) puntos_venta = new Array();
          this.model.set({"puntos_venta":puntos_venta});
        }

        if (control.check("razones_sociales")>0) { 
          this.model.set({
            "id_razon_social":self.$("#almacen_razones_sociales").val(),
          });
        }

        if (this.$("#almacen_centros_costos").length > 0) { 
          this.model.set({
            "id_centro_costo":self.$("#almacen_centros_costos").val(),
          });
        }

        if (this.$("#almacen_para_retiro").length > 0) {
          this.model.set({
            "para_retiro":(self.$("#almacen_para_retiro").is(":checked")?1:0)
          });
        }

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
            location.reload();
          }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.Almacen()
      this.render();
    },
		
	});

})(app.views, app.models);






(function ( models ) {

  models.CentroCosto = Backbone.Model.extend({
    urlRoot: "centros_costos/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA
    }
  });
    
})( app.models );


(function (collections, model, paginator) {

  collections.CentrosCostos = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "centros_costos/"
    }
    
  });

})( app.collections, app.models.CentroCosto, Backbone.Paginator);


(function ( app ) {

  app.views.CentroCostoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#centros_costos_item').html()),
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
      location.href="app/#centro_costo/"+this.model.id;
    },
    borrar: function(e) {
      this.model.destroy(); // Eliminamos el modelo
      $(this.el).remove();  // Lo eliminamos de la vista
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

  app.views.CentrosCostosTableView = app.mixins.View.extend({

    template: _.template($("#centros_costos_panel_template").html()),

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
      var view = new app.views.CentroCostoItem({
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

  views.CentroCostoEditView = app.mixins.View.extend({

    template: _.template($("#centros_costos_edit_panel_template").html()),

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
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      var self = this;
      try {
        validate_input("centros_costos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
            location.reload();
          }
        });
      }
    },
    
    limpiar : function() {
      this.model = new app.models.CentroCosto();
      this.render();
    },
    
  });

})(app.views, app.models);