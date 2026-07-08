// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Tripulante = Backbone.Model.extend({
    urlRoot: "tripulantes/",
    defaults: {
      nombre: "",
      dni: "",
      nacionalidad: "",
      telefono: "",
      activo: 1,
      email: "",
      password: "",
      sueldo_base: 0,
    }
  });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Tripulantes = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "tripulantes/"
		}
		
	});

})( app.collections, app.models.Tripulante, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.TripulanteItem = app.mixins.View.extend({
        tagName: "tr",
        template: _.template($('#tripulantes_item').html()),
      	myEvents: {
    		  "click .ver": "editar",
    		  "click .delete": "borrar",
        
            "click .activo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"via_tripulantes",
                  "url":"tripulantes/function/change_property/",
                  "attribute":"activo",
                  "value":activo,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
                return false;
            },

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
        	location.href="app/#tripulante/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
            e.stopPropagation();
        },
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.TripulantesTableView = app.mixins.View.extend({

    	template: _.template($("#tripulantes_panel_template").html()),

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
			var view = new app.views.TripulanteItem({
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

	views.TripulanteEditView = app.mixins.View.extend({

		template: _.template($("#tripulantes_edit_panel_template").html()),

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

    render: function() {
    	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
    	var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
    	// Extendemos el objeto creado con el modelo de datos
    	$.extend(obj,this.model.toJSON());
    	$(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("tripulantes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        if (this.$("#tripulantes_password").length > 0) {
          if (this.model.id == null) {
            validate_input("tripulantes_password",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
            validate_input("tripulantes_password_2",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
          }
          var password_1 = this.$("#tripulantes_password").val();
          console.log(password_1);
          var password_2 = this.$("#tripulantes_password_2").val();
          console.log(password_2);
          if (password_1 != password_2) {
            show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
            this.$("#tripulantes_password_2").focus();
            return false;
          }
          if (!isEmpty(password_1)) {
            password_1 = hex_md5(password_1);
            this.model.set({
              "password":password_1
            });                    
          }          
        }

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
                location.href="app/#tripulantes";
            }
        });
      }
		},
		
    limpiar : function() {
      this.model = new app.models.Tripulante()
      this.render();
    },
	
  });

})(app.views, app.models);