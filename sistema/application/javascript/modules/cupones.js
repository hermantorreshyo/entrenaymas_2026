(function ( models ) {

  models.Cupones = Backbone.Model.extend({
    urlRoot: "cupones/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
      fecha_desde: "",
      fecha_hasta: "",
      fecha_creado: "",
      maximo_utilizable: 0,
      cantidad_utilizada: 0,
      descuento: "",
      codigo: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.Cupones = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cupones/"
    }
  });
})( app.collections, app.models.Cupones, Backbone.Paginator);


(function ( app ) {
  app.views.CuponesItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cupones_item').html()),
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
      location.href="app/#cupon/"+this.model.id;
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

  app.views.CuponesTableView = app.mixins.View.extend({

   template: _.template($("#cupones_panel_template").html()),

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
      var view = new app.views.CuponesItem({
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

  views.CuponesEditView = app.mixins.View.extend({

    template: _.template($("#cupones_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
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

      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#cupones_fecha_desde"),fecha_desde);

      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#cupones_fecha_hasta"),fecha_hasta);

      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios

        var nombre = $("#cupones_nombre").val();
        var codigo = $("#cupones_codigo").val();
        var descuento = $("#cupones_descuento").val();
        var fecha_desde = $("#cupones_fecha_desde").val();
        var fecha_hasta = $("#cupones_fecha_hasta").val();

        if (nombre == "") {
          alert ("Por favor ingrese un nombre");
          return false;
        }

        if (codigo == "") {
          alert ("Por favor ingrese un codigo");
          return false;
        }

        if (descuento == 0) {
          alert ("Por favor ingrese un descuento");
          return false;
        }

        if (fecha_desde == "") {
          alert ("Por favor ingrese una fecha desde");
          return false;
        }

        if (fecha_hasta == "") {
          alert ("Por favor ingrese una fecha hasta");
          return false;
        }

 
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({
            id:0,
            fecha_creado: moment().format("YYYY-MM-DD HH:mm:ss"),
          });
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            location.href="app/#cupones";
          }
        });
      }
    },

  });

})(app.views, app.models);