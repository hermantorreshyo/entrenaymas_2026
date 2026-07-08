(function ( models ) {

  models.Anunciantes = Backbone.Model.extend({
    urlRoot: "anunciantes/",
    defaults: {
      texto: "",
      link: "",
      path: "",
      nombre: "",
      id_empresa: ID_EMPRESA,
      mostrar: "todos",
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Anunciantes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "anunciantes/"
    },
  });

})( app.collections, app.models.Anunciantes, Backbone.Paginator);


(function ( app ) {

  app.views.AnunciantesItem = app.mixins.View.extend({
    tagName: "tr",
    attributes: function() {
      return {
        id: this.model.id // Es necesario hacer esto para reordenar
      }
    },
    template: _.template($('#anunciantes_item').html()),
    events: {
      "click .edit": "editar",
      "click .delete": "borrar",
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
      location.href="app/#anunciante/"+this.model.id;
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

  app.views.AnunciantesTableView = app.mixins.View.extend({

    template: _.template($("#anunciantes_panel_template").html()),

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
      var view = new app.views.AnunciantesItem({
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

  views.AnunciantesEditView = app.mixins.View.extend({

    template: _.template($("#anunciantes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
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


        var nombre = this.$("#anunciantes_nombre").val();
        var link = this.$("#anunciantes_link").val();
        var texto = this.$("#anunciantes_texto").val();
        var path = this.$("#hidden_path").val();

        if (nombre == "") {
          alert ("Por favor ingrese un nombre");
          return false;
        }

        if (link == "") {
          alert ("Por favor ingrese el link de redirección");
          return false;
        }

        if (texto == "") {
          alert ("Por favor ingrese el texto");
          return false;
        }

        if (path == "") {
          alert ("Por favor ingrese una imagen");
          return false;
        }

        this.model.set({
          "nombre": nombre,
          "link": link,
          "texto": texto,
          "path": path,
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
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              alert(response.mensaje);
            } else {
              location.href="app/#anunciantes";
              location.reload();
            }
          },
        });
      }
    },
    
  });

})(app.views, app.models);