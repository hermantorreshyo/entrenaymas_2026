// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Plan = Backbone.Model.extend({
    urlRoot: "planes/",
    defaults: {
      id_proyecto: 0,
      id_empresa: (ID_PROYECTO != 0) ? ID_EMPRESA : 0,
      nombre: "",
      limite_facturacion:0,
      limite_compras:0,
      limite_articulos:0,
      precio_anual:0,
      observaciones: "",
      forma_generacion: 'F',
      dia_generacion: 1,
      mes_vencimiento: "A",
      boton_pago_mp: "",
      id_articulo: 0,
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Planes = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "planes/"
		}
		
	});

})( app.collections, app.models.Plan, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PlanItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#planes_item').html()),
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
    	var obj = { "permiso": this.permiso, "id":this.model.id };
    	$.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
    	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
    	location.href="app/#plan/"+this.model.id;
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

  app.views.PlanesTableView = Backbone.View.extend({

  	template: _.template($("#planes_panel_template").html()),

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
		},

		addOne : function ( item ) {
			var view = new app.views.PlanItem({
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

	views.PlanEditView = app.mixins.View.extend({

		template: _.template($("#planes_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
      "change #plan_forma_generacion":function() {
        var f = this.$("#plan_forma_generacion").val();
        this.$("#plan_dia_generacion").prop("disabled",(f=="C"));
      },
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
    	$.extend(obj,this.model.toJSON());
    	$(this.el).html(this.template(obj));
      
      if (ID_PROYECTO == 0) {
        new app.mixins.Select({
          modelClass: app.models.Proyecto,
          url: "proyectos/",
          render: "#planes_proyecto",
          name : "id_proyecto",
          selected: this.model.get("id_proyecto"),
        });        
      }
      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("planes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        if (ID_PROYECTO == 0) {
          this.model.set({
            "id_proyecto":$("#planes_proyecto").val(),
          });
        } else {
          this.model.set({
            "dia_generacion":this.$("#plan_dia_generacion").val(),
            "forma_generacion":this.$("#plan_forma_generacion").val(),
            "mes_vencimiento":this.$("#plan_mes_vencimiento").val(),
          })
        }
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
        this.model.save({},{
          success: function(model,response) {
            location.href="app/#planes";
          }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.Plan()
      this.render();
    },
		
	});

})(app.views, app.models);
