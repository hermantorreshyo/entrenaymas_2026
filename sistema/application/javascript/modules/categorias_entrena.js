(function ( models ) {

  models.CategoriasEntrena = Backbone.Model.extend({
    urlRoot: "categorias_entrena/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.CategoriaEntrena = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "categorias_entrena/"
    },
  });

})( app.collections, app.models.CategoriasEntrena, Backbone.Paginator);


(function ( app ) {

  app.views.CategoriasEntrenaItem = Backbone.View.extend({
    tagName: "tr",
    attributes: function() {
      return {
        id: this.model.id // Es necesario hacer esto para reordenar
      }
    },
    template: _.template($('#categorias_entrena_item').html()),
    events: {
      "click .edit": "editar",
      "click .delete": "borrar",
      "click .notificar": "notificar",
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
      var obj = { 
        permiso: this.permiso,
        seleccion: this.habilitar_seleccion,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#categoria_entrena/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.CategoriasEntrenaTableView = app.mixins.View.extend({

    template: _.template($("#categorias_entrena_panel_template").html()),

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;
      this.habilitar_seleccion = (typeof options.habilitar_seleccion != "undefined") ? options.habilitar_seleccion : false;

      // Creamos la lista de email_templatecion
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
      var obj = { 
        permiso: this.permiso,
        seleccion: this.habilitar_seleccion,
      };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el email_templatedor
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo email_templatemos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.CategoriasEntrenaItem({
        model: item,
        permiso: this.permiso,
        habilitar_seleccion: self.habilitar_seleccion,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CategoriasEntrenaEditView = app.mixins.View.extend({

    template: _.template($("#categorias_entrena_edit_panel_template").html()),

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

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      this.lightbox = (typeof this.options.lightbox != "undefined") ? this.options.lightbox : false;
      var obj = { "edicion": edicion, id:this.model.id, "lightbox":this.lightbox };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      var self = this;
      try {
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
        var nombre = $("#categorias_entrena_nombre").val();
        this.model.save({
            "nombre":nombre,
          },{
          success: function(model,response) {
            if (self.lightbox) location.reload();
            else location.href="app/#categorias_entrena";
          }
        });                 
      }
    },
        
    limpiar : function() {
      this.model = new app.models.CategoriasEntrena();
      this.render();
    },
    
  });

})(app.views, app.models);