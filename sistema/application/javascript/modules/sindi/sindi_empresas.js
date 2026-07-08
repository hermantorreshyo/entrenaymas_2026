(function ( models ) {

  models.SindiEmpresa = Backbone.Model.extend({
    urlRoot: "sindi_empresas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      subzona: 0,
      codigo: 0,
      identificador: 0,
      nombre: "",
      domicilio: "",
      email: "",
      cuit: "",
      telefono: "",
      baja: 0,
      titular1: "",
      titular2: "",
      titular3: "",
      id_localidad: 0,
      id_iva: 0,
      id_tipo_sociedad: 0,
      id_estudio_contable: 0,
      fecha_alta: moment().format("YYYY-MM-DD"),
      localidad: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiEmpresas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_empresas/function/buscar/"
    }
  });
})( app.collections, app.models.SindiEmpresa, Backbone.Paginator);


(function ( models ) {
  models.SindiEmpresasAltaBaja = Backbone.Model.extend({
    urlRoot: "sindi_afiliados/function/get_empleados/",
    defaults: {
      id_afiliado: 0,
      id_empresa_transporte: 0,
      id_tipo_afiliado: 0,
      fecha_alta: "",
      fecha_baja: "",
      motivo: "",
      id_empresa: ID_EMPRESA,
    }
  });
})( app.models );

(function ( app ) {
  app.views.SindiEmpresaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_empresas_item').html()),
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
      location.href="app/#sindi_empresa/"+this.model.id;
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

  app.views.SindiEmpresasTableView = app.mixins.View.extend({

   template: _.template($("#sindi_empresas_panel_template").html()),

   myEvents: {
    "click .nuevo":"nuevo",
   },

   initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var self = this;
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

    nuevo: function() {
      var self = this;
      var v = new app.views.SindiEmpresaEditView({
        model: new app.models.SindiEmpresa(),
        permiso: control.check("sindi_empresas"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "escapable":false,
        "callback":function() {
          if (typeof window.id_sindi_empresa == "undefined") return;
          location.href = "app/#sindi_empresa/"+window.id_sindi_empresa;
        }
      });
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.SindiEmpresaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


// =====================================
// VISTA GENERAL DE LA EMPRESA
// =====================================

(function ( views, models ) {

  views.SindiEmpresaDetalleView = app.mixins.View.extend({

    template: _.template($("#sindi_empresa_detalle_template").html()),

    myEvents: {
      "click .editar":"editar",
      "click .alta_empresa": "alta_empresa",
      "click .baja_empresa": "baja_empresa",
      "click .cerrar":"cerrar",
      "click .nuevo_afiliado":function(){
        var self = this;
        var v = new app.views.SindiEmpresaEditView({
          model: new app.models.SindiEmpresa(),
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
      this.tab_activo = (typeof options.tab_activo != "undefined" ? options.tab_activo : "grupo_familiar");
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

      this.cargar_lista_empleados();
      return this;
    },

    cargar_lista_empleados: function() {

    },
    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    alta_empresa: function() {
      var self = this;
      var view = new app.views.SindiEmpresaAltaBajaView({
        model: new app.models.AbstractModel({
          "id_sindi_empresa":self.model.get("id"),
          "motivo":self.model.get("motivo"),
          "tipo":'alta',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    baja_empresa: function() {
      var self = this;
      var view = new app.views.SindiEmpresaAltaBajaView({
        model: new app.models.AbstractModel({
          "id_sindi_empresa":self.model.get("id"),
          "motivo":self.model.get("motivo"),
          "tipo": 'baja',
          "fecha":moment().format("YYYY-MM-DD"),
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":500,
        "height":140,
        "escapable":false,
      });
    },

    editar: function() {
      var self = this;
      var v = new app.views.SindiEmpresaEditView({
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

// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiEmpresaEditView = app.mixins.View.extend({

    template: _.template($("#sindi_empresas_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar":"cerrar",
      "click .nuevo": "limpiar",
      "change .labelcontrol": function() {
        $("#label-codigo").hide();
      },
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.tr_limite = null;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      var self = this;
      if (this.options.permiso > 1) edicion = true;
      var obj = {
        edicion: edicion,
        id:this.model.id,
        permiso:control.check("sindi_empresas")
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiLocalidad, // MODELO
        url: "sindi_localidades/function/buscar/?offset=99999", // URL DEL CONTROL
        firstOptions: ["<option value='0'>Sin Definir</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_empresas_id_localidad", // ID DEL SELECT
        selected: self.model.get("id_localidad"), // VALOR SELECCIONADO
        name: "id_localidad", // NOMBRE DEL CAMPO EN LA TABLA
      });
      new app.mixins.Select({
        modelClass: app.models.SindiEstudioContable, // MODELO
        url: "sindi_estudios_contables/function/buscar/?offset=99999", // URL DEL CONTROL
        firstOptions: ["<option value='0'>Ninguno</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_empresas_id_estudio_contable", // ID DEL SELECT
        selected: self.model.get("id_estudio_contable"), // VALOR SELECCIONADO
        name: "id_estudio_contable", // NOMBRE DEL CAMPO EN LA TABLA
      });
      return this;
    },

    cerrar: function(e) {
      if (confirm('Si continua cerrando los cambios no se guardaran!')) {
        $('.modal:last').modal('hide');
      }
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
      var es_nuevo = false;
      if (this.validar()) {
        if (this.model.id == null) {
          es_nuevo = true;
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            if (es_nuevo) {
              window.id_sindi_empresa = model.id;
              $('.modal:last').modal('hide');
            } else {
              workspace.ver_sindi_empresas();
            }
          },
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiEmpresa()
      this.render();
    },

  });

})(app.views, app.models);

// -------------------------------
// VISTA DE ALTA BAJA EMPRESA
// -------------------------------
(function ( views, models ) {

  views.SindiEmpresaAltaBajaView = app.mixins.View.extend({

    template: _.template($("#sindi_empresas_alta_baja_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar":"cerrar",
    },

    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    guardar: function() {
      var self = this;
      $.ajax({
        "url":"sindi_empresas/function/alta_baja_empresa/",
        "dataType":"json",
        "type":"post",
        "data":{
          "motivo":self.$("#sindi_empresas_alta_baja_motivo").val(),
          "fecha":self.$("#sindi_empresas_alta_baja_fecha").val(),
          "id_sindi_empresa":self.$("#sindi_empresas_alta_baja_id").val(),
          "tipo":self.model.get("tipo"),
        },
        "success":function(response) {
          if (response.error == 1) {
            alert(response.mensaje);
          } else {
            workspace.ver_sindi_empresas();
          }
        }
      });
    },
  });

})(app.views, app.models);