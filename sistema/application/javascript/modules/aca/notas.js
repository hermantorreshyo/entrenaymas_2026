(function ( models ) {

  models.Examen = Backbone.Model.extend({
    urlRoot: "examenes/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      id_docente: 0,
      id_comision: 0,
      id_materia: 0,
      numerico: 1,
      aprueba_con: 4,
      utilizada_en_promedio: 1,
      cerrada: 0,
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Examenes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "examenes/"
    }
  });

})( app.collections, app.models.Examen, Backbone.Paginator);


(function ( app ) {

  app.views.ExamenItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#examenes_item').html()),
    events: {
      "click .ver": "editar",
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
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#examen/"+this.model.id;
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

(function ( app ) {

  app.views.ExamenesTableView = app.mixins.View.extend({
    template: _.template($("#examenes_panel_template").html()),
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.examenes_filter = (typeof window.examenes_filter != "undefined") ? window.examenes_filter : "";
      window.examenes_page = (typeof window.examenes_page != "undefined") ? window.examenes_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(pagination.el);
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      if (window.examenes_filter != this.$("#examenes_buscar").val().trim()) {
        window.examenes_filter = this.$("#examenes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.examenes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.examenes_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.examenes_page);
    },

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ExamenItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.ExamenEditView = app.mixins.View.extend({

    template: _.template($("#examenes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("examen_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
            location.href="app/#examenes";
          }
        });
      }
    },

  });

})(app.views, app.models);