(function ( models ) {
  models.SindiLimiteAfiliado = Backbone.Model.extend({
    urlRoot: "sindi_limites_afiliados/",
    defaults: {
      id_empresa: ID_EMPRESA,
      meses: 0,
      cantidad: 0,
      id_afiliado: 0,
      id_tipo_practica: 0,
      motivo: "",
      tipo: 0,
      consulta: 0,
      recetarios: 0
    }
  });
})( app.models );

(function (collections, model, paginator) {
  collections.SindiLimitesAfiliados = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_limites_afiliados/function/buscar"
    }
  });
})( app.collections, app.models.SindiLimiteAfiliado, Backbone.Paginator);

(function ( app ) {
  app.views.SindiLimiteAfiliadoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_limites_afiliados_item').html()),
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
      console.log(self.model);
      var modelo = new app.models.SindiAfiliado({ 'id':self.model.get("id_afiliado") });
      modelo.fetch({
        "success":function() {
          var v = new app.views.SindiAfiliadoEditView({
            model: modelo,
            permiso: control.check("sindi_afiliados"),
            tab_activo: "limites",
            subtab_activo: ((typeof self.options.subtab_activo != "undefined") ? self.options.subtab_activo : ""),
            view: self,
          });
          crearLightboxHTML({
            "html":v.el,
            "width":900,
            "height":140,
            "escapable":false,
            "callback":function() {
             // location.reload();
            }
          });
        },
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

  app.views.SindiLimitesAfiliadosTableView = app.mixins.View.extend({

   template: _.template($("#sindi_limites_afiliados_panel_template").html()),

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
      var obj = { 
        permiso: this.permiso,
        tipo: ((typeof this.options.tipo != "undefined") ? this.options.tipo : 5),
      };

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
      var self = this;
      var view = new app.views.SindiLimiteAfiliadoItem({
        model: item,
        permiso: this.permiso,
        subtab_activo: ((typeof self.options.subtab_activo != "undefined") ? self.options.subtab_activo : ""),
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiLimiteAfiliadoEditView = app.mixins.View.extend({

    template: _.template($("#sindi_limites_afiliados_edit_panel_template").html()),

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
            //location.href="app/#sindi_limites_afiliados";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiLimiteAfiliado();
      this.render();
    },

  });

})(app.views, app.models);