(function ( models ) {

  models.SindiEstudioContable = Backbone.Model.extend({
    urlRoot: "sindi_estudios_contables/",
    defaults: {
      id_empresa: ID_EMPRESA,
      codigo: 0,
      nombre: "",
      domicilio: "",
      id_localidad: 0,
      cuit: "",
      telefono: "",
      email: "",
      localidad: ""
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiEstudiosContables = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_estudios_contables/function/buscar/"
    }
  });
})( app.collections, app.models.SindiEstudioContable, Backbone.Paginator);


(function ( app ) {
  app.views.SindiEstudioContableItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_estudios_contables_item').html()),
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
      location.href="app/#sindi_estudio_contable/"+this.model.id;
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

  app.views.SindiEstudiosContablesTableView = app.mixins.View.extend({

   template: _.template($("#sindi_estudios_contables_panel_template").html()),

   myEvents: {
    "click .nuevo":"nuevo",
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
      var view = new app.views.SindiEstudioContableItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiEstudioContableEditView({
        model: new app.models.SindiEstudioContable(),
        permiso: control.check("sindi_estudios_contables"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":700,
        "height":140,
        "escapable":false,
        "callback":function() {
          if (typeof window.id_sindi_estudio_contable == "undefined") return;
          location.href = "app/#sindi_empresa/"+window.id_sindi_estudio_contable;
        }        
      });
    },

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiEstudioContableEditView = app.mixins.View.extend({

    template: _.template($("#sindi_estudios_contables_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",      
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
      var self = this;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiLocalidad, // MODELO
        url: "sindi_localidades/function/buscar/", // URL DEL CONTROL
        firstOptions: ["<option value='0'>Sin Definir</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_estudios_contables_id_localidad", // ID DEL SELECT
        selected: self.model.get("id_localidad"), // VALOR SELECCIONADO
        name: "id_localidad", // NOMBRE DEL CAMPO EN LA TABLA
      });
      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },    

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        // No hay ningun error

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
              location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiEstudioContable()
      this.render();
    },

  });

})(app.views, app.models);


// =====================================
// VISTA GENERAL DE LA EMPRESA
// =====================================

(function ( views, models ) {

  views.SindiEstudioContableDetalleView = app.mixins.View.extend({

    template: _.template($("#sindi_estudio_contable_detalle_template").html()),

    myEvents: {
      "click .editar":"editar",
      "click .alta_empresa": "alta_empresa",
      "click .baja_empresa": "baja_empresa",
      "click .cerrar":"cerrar",
      "click #historial_link":"cargar_historial",
      "click .nuevo_afiliado":function(){
        var self = this;
        var v = new app.views.SindiEstudioContableEditView({
          model: new app.models.SindiEstudioContable(),
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":900,
          "height":140,
          "escapable":false,
        });
      },
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.guardando = 0;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = {
        "edicion": edicion,
        "id":this.model.id,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    editar: function() {
      var self = this;
      var v = new app.views.SindiEstudioContableEditView({
        model: self.model,
        permiso: control.check("sindi_empresas"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "escapable":false,
      });
    },

  });

})(app.views, app.models);