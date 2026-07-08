(function ( models ) {

  models.Conferencista = Backbone.Model.extend({
    urlRoot: "conferencistas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      titulo: "",
      subtitulo: "",
      tematica: "",
      fecha: "",
      lugar: "",
      id_categoria: 0,
      id_cliente: 0,
      id_evento: 0,
      path: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.Conferencistas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "conferencistas/"
    }
  });
})( app.collections, app.models.Conferencista, Backbone.Paginator);


(function ( app ) {
  app.views.ConferencistaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#conferencistas_item').html()),
    events: {
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
      location.href="app/#conferencista/"+this.model.id;
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

  app.views.ConferencistasTableView = app.mixins.View.extend({

   template: _.template($("#conferencistas_panel_template").html()),

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
      var view = new app.views.ConferencistaItem({
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

  views.ConferencistaEditView = app.mixins.View.extend({

    template: _.template($("#conferencistas_edit_panel_template").html()),

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
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var r = workspace.crear_select(categorias_noticias,"",self.model.get("id_categoria"));
      this.$("#conferencista_categorias").html(r);

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker($(this.el).find("#conferencista_fecha"),fecha);

      new app.mixins.Select({
        modelClass: app.models.NotEvento,
        url: "not_eventos/",
        render: "#conferencista_eventos",
        firstOptions: ["<option value='0'>-</option>"],
        campoSelect: "titulo",
        selected: self.model.get("id_evento"),
        onComplete:function(c) {
          crear_select2("conferencista_eventos");
        }                    
      });

      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        render: "#conferencista_clientes",
        name : "id_cliente",
        firstOptions: ["<option value='0'>-</option>"],
        selected: this.model.get("id_cliente"),
      });

      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("conferencistas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "id_cliente":$("#conferencista_clientes").val(),
          "id_evento":$("#conferencista_eventos").val(),
          "id_categoria":$("#conferencista_categorias").val(),
        },{
          success: function(model,response) {
            location.href="app/#conferencistas";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Conferencista();
      this.render();
    },

  });

})(app.views, app.models);