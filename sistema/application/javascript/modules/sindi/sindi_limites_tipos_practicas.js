(function ( models ) {

  models.SindiLimiteTipoPractica = Backbone.Model.extend({
    urlRoot: "sindi_limites_tipos_practicas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      meses: 0,
      cantidad: 0,
      id_tipo_practica: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiLimitesTiposPracticas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_limites_tipos_practicas/function/buscar"
    }
  });
})( app.collections, app.models.SindiLimiteTipoPractica, Backbone.Paginator);


(function ( app ) {
  app.views.SindiLimiteTipoPracticaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_limites_tipos_practicas_item').html()),
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
      var self = this;
      var sindi_practica = new app.models.SindiLimiteTipoPractica({ "id": self.model.id });
      sindi_practica.fetch({
        "success":function() {
          var that = self
          var vista = new app.views.SindiLimiteTipoPracticaEditView({
            model: sindi_practica,
            permiso: control.check("sindi_limites_tipos_practicas")
          });
          crearLightboxHTML({
            "html":vista.el,
            "width":900,
            "height":140,
            "escapable":false,
          });
        }
      });
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

  app.views.SindiLimitesTiposPracticasTableView = app.mixins.View.extend({

    template: _.template($("#sindi_limites_tipos_practicas_panel_template").html()),

    myEvents: {
      "click .nuevo":"nuevo",
    },

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiLimiteTipoPracticaEditView({
        model: new app.models.SindiLimiteTipoPractica(),
        permiso: control.check("sindi_limites_tipos_practicas"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":700,
        "height":140,
        "escapable":false,
        "callback":function() {
          self.collection.pager();
        }
      });
    },

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
      var view = new app.views.SindiLimiteTipoPracticaItem({
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

  views.SindiLimiteTipoPracticaEditView = app.mixins.View.extend({

    template: _.template($("#sindi_limites_tipos_practicas_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .nuevo": "limpiar",
      "click .cerrar": function() {$('.modal:last').modal('hide');},
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

      new app.mixins.Select({
        modelClass: app.models.SindiTipoPractica,
        url: "sindi_tipos_practicas/",
        render: "#sindi_limite_tipos_practicas",
        selected: self.model.get("id_tipo_practica"),
      });

      return this;
    },

    validar: function() {
      try {
        this.model.set({
          "id_tipo_practica":self.$("#sindi_limite_tipos_practicas").val(),
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
            $('.modal:last').modal('hide');
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiLimiteTipoPractica();
      this.render();
    },

  });

})(app.views, app.models);