(function ( models ) {

  models.CalmEscena = Backbone.Model.extend({
    urlRoot: "calm_escenas/",
    defaults: {
      nombre: "",
      path: "",
      path_audio: "",
      activo: 1,
      destacado: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.CalmEscenas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "calm_escenas/"
    }
  });
})( app.collections, app.models.CalmEscena, Backbone.Paginator);


(function ( app ) {
  app.views.CalmEscenaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#calm_escenas_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"calm_escenas",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },      
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"calm_escenas",
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
      location.href="app/#calm_escena/"+this.model.id;
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
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.CalmEscenasTableView = app.mixins.View.extend({

   template: _.template($("#calm_escenas_panel_template").html()),

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
      var view = new app.views.CalmEscenaItem({
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

  views.CalmEscenaEditView = app.mixins.View.extend({

    template: _.template($("#calm_escenas_edit_panel_template").html()),

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
      try {
        // Validamos los campos que sean necesarios
        validate_input("calm_escenas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "path":$("#hidden_path").val(),
          "path_audio":$("#hidden_path_audio").val(),
        },{
          success: function(model,response) {
            location.href="app/#calm_escenas";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CalmEscena();
      this.render();
    },

  });

})(app.views, app.models);