// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Necrologica = Backbone.Model.extend({
    urlRoot: "necrologicas/",
    defaults: {
      activo: 1,
      nombre: "",
      texto: "",
      edad: "",
      fecha_fallecimiento: "",
      fecha_traslado: "",
      hora_traslado: "",
      casa_duelo: "",
      cementerio: "",
      servicio_velatorio: "",
      id_empresa: ID_EMPRESA,
      participante: "",
      participante_email: "",
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Necrologicas = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "necrologicas/"
		},
		
	});

})( app.collections, app.models.Necrologica, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.NecrologicaItem = app.mixins.View.extend({
    tagName: "tr",
    attributes: function() {
      return {
        id: this.model.id // Es necesario hacer esto para reordenar
      }
    },
    template: _.template($('#necrologicas_item').html()),
  	myEvents: {
      "click .edit": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "url":"necrologicas/function/change_property/",
          "table":"inf_necrologicas",
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
    	location.href="app/#necrologica/"+this.model.id;
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

  app.views.NecrologicasTableView = app.mixins.View.extend({

  	template: _.template($("#necrologicas_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
      this.options = options;
			this.permiso = this.options.permiso;

			// Creamos la lista de necrologicacion
			var pagination = new app.mixins.PaginationView({
				collection: lista
			});

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

      lista.off('sync');
			lista.on('sync', this.addAll, this);
			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			// Cargamos el template
			$(this.el).html(this.template(obj));
			// Cargamos el necrologicador
			$(this.el).find(".pagination_container").html(pagination.el);
			// Cargamos el buscador
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo necrologicamos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.NecrologicaItem({
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

	views.NecrologicaEditView = app.mixins.View.extend({

		template: _.template($("#necrologicas_edit_panel_template").html()),

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
      var edicion = false;
        if (this.options.permiso > 1) edicion = true;
        var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      var fecha_fallecimiento = this.model.get("fecha_fallecimiento");
      if (isEmpty(fecha_fallecimiento)) fecha_fallecimiento = new Date();
      createdatepicker($(this.el).find("#necrologicas_fecha_fallecimiento"),fecha_fallecimiento);

      var fecha_traslado = this.model.get("fecha_traslado");
      if (isEmpty(fecha_traslado)) fecha_traslado = new Date();
      createdatepicker($(this.el).find("#necrologicas_fecha_traslado"),fecha_traslado);
      
      return this;
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("necrologicas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Controlamos que la fecha de traslado sea mayor o igual a la fecha de fallecimiento
        var f_traslado = moment(self.$("#necrologicas_fecha_traslado").val(),"DD/MM/YYYY");
        var f_fallecimiento = moment(self.$("#necrologicas_fecha_fallecimiento").val(),"DD/MM/YYYY");
        if (f_traslado.isBefore(f_fallecimiento)) {
          alert("ERROR: La fecha de traslado no puede ser menor a la fecha de fallecimiento.");
          return false;
        }
        
        this.model.set({
          "fecha_traslado":self.$("#necrologicas_fecha_traslado").val(),
          "fecha_fallecimiento":self.$("#necrologicas_fecha_fallecimiento").val(),
					"cementerio":self.$("#necrologicas_cementerio").val(),
					"servicio_velatorio":self.$("#necrologicas_servicio_velatorio").val(),
        });
        
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
        var cktext = CKEDITOR.instances['necrologicas_texto'].getData();
        this.model.save({
            "texto":cktext,
          },{
          success: function(model,response) {
            location.href="app/#necrologicas";
          }
        });         
      }
    },
    
    limpiar : function() {
      this.model = new app.models.Necrologica()
      this.render();
    },
		
	});

})(app.views, app.models);