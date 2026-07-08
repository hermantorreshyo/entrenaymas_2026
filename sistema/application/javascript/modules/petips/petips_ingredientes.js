(function ( models ) {

  models.PetipsIngrediente = Backbone.Model.extend({
    urlRoot: "petips_ingredientes/",
    defaults: {
      nombre: "",
      puntaje: 0,
      id_empresa: ID_EMPRESA,
      es_carne: 0,
      es_calidad: 0,
      normalizado: 0,
      general: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PetipsIngredientes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "petips_ingredientes/"
    }
  });
})( app.collections, app.models.PetipsIngrediente, Backbone.Paginator);


(function ( app ) {
  app.views.PetipsIngredienteItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#petips_ingredientes_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "change .generales": "cambiar_generales",
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
      location.href="app/#petips_ingrediente/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
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
    },
    cambiar_generales: function() {
      var id_normalizado = this.$(".normalizados").val()
      self.model.set({"destacado":f});
      this.change_property({
        "table":"articulos",
        "attribute":"destacado",
        "value":f,
        "id":self.model.id,
        "success":function(){
          self.render();
        }
      });
      return false;        
      
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.PetipsIngredientesTableView = app.mixins.View.extend({

   template: _.template($("#petips_ingredientes_panel_template").html()),

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection,
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
      self.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PetipsIngredienteItem({
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

  views.PetipsIngredienteEditView = app.mixins.View.extend({

    template: _.template($("#petips_ingredientes_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
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
      try {
        // Validamos los campos que sean necesarios

        this.model.set({
          "es_carne":(this.$("#petip_ingrediente_es_carne").is(":checked")?1:0),
          "es_calidad":(this.$("#petip_ingrediente_es_calidad").is(":checked")?1:0),
        });
        
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
            location.href="app/#petips_ingredientes";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.PetipsIngrediente();
      this.render();
    },

  });

})(app.views, app.models);