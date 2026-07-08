(function ( models ) {

  models.Caja = Backbone.Model.extend({
    urlRoot: "cajas/",
    defaults: {
      nombre: "",
      activo: 1,
      saldo: 0,
      id_sucursal: 0,
      sucursal: "",
      id_empresa: ID_EMPRESA,
      tipo: 0, // 0 = Efectivo, 1 = Cuenta Bancaria
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.Cajas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cajas/"
    }
  });
})( app.collections, app.models.Caja, Backbone.Paginator);


(function ( app ) {
  app.views.CajaItem = app.mixins.View.extend({
    className: "col-md-4 col-sm-6 col-xs-12",
    template: _.template($('#cajas_item').html()),
    events: {
      "click .editar": "editar",
      "click .delete": "borrar",
      "click .ocultar": "ocultar",
      "click .ver_movimientos": "ver_movimientos",
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
      location.href="app/#caja/"+this.model.id;
    },
    ver_movimientos: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#ver_cajas_movimientos/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    ocultar: function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      var activo = this.model.get("activo");
      activo = (activo == 1)?0:1;
      self.model.set({"activo":activo});
      this.change_property({
        "table":"cajas",
        "attribute":"activo",
        "value":activo,
        "id":self.model.id,
        "success":function(){
          if (window.location.hash == "#cajas") location.reload();
          else location.href = "app/#cajas";
        }
      });
      return false;      
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.CajasTableView = app.mixins.View.extend({

    template: _.template($("#cajas_panel_template").html()),

    myEvents: {
      "click .cambiar_tab":function(e) {
        var tipo = $(e.currentTarget).data("tipo");
        this.$(".cambiar_tab").parent().removeClass("active");
        $(e.currentTarget).parent().addClass("active");
        window.cajas_tipo = tipo;
        this.buscar();
      },
      "click .transferencia":function() {
        var self = this;
        var edicion = new app.views.CajaTransferenciaView({
          model: new app.models.AbstractModel(),
        });
        crearLightboxHTML({
          "html":edicion.el,
          "width":500,
          "height":500,
          "escapable":false,
          "callback":function(){
            self.buscar();
          }
        });
      },
    },

    initialize : function (options) {
      _.bindAll(this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.collection.on('sync', this.addAll, this);
      window.cajas_ver_todas = (typeof window.cajas_ver_todas != "undefined") ? window.cajas_ver_todas : -1;
      window.cajas_tipo = (typeof window.cajas_tipo != "undefined") ? window.cajas_tipo : -1;
      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      this.buscar();
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      var filtros = {};
      filtros.tipo = window.cajas_tipo;
      filtros.offset = 99999;
      if (ID_SUCURSAL != 0) filtros.id_sucursal = ID_SUCURSAL;
      filtros.activo = window.cajas_ver_todas;
      this.collection.server_api = filtros;
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find(".listado").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      if (ID_EMPRESA == 249 && PERFIL == 1457 && item.id == 881) return;
      var view = new app.views.CajaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find(".listado").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CajaEditView = app.mixins.View.extend({

    template: _.template($("#cajas_edit_panel_template").html()),

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
        validate_input("cajas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "tipo":self.$("#cajas_tipo").val(),
        },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Caja();
      this.render();
    },

  });

})(app.views, app.models);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CajaMiniEditView = app.mixins.View.extend({

    template: _.template($("#cajas_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          $(e.currentTarget).parent().next().find(".tab").focus();
        }
      },
      "keyup .tab":function(e) {
        if (e.which == 27) this.cerrar();
      },
      "keypress .guardar":function(e) {
        if (e.keyCode == 13) this.guardar();
      },
    },

    initialize: function(options) {
      this.options = options;
      this.input = this.options.input;
      this.onSave = this.options.onSave;
      this.callback = this.options.callback;

      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var obj = { id:this.model.id };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#cajas_mini_nombre").val($(this.input).val().trim());
      }

      return this;
    },

    focus: function() {
      $(this.el).find("#cajas_mini_nombre").focus();
    },

    validar: function() {
      var self = this;
      try {
        validate_input("cajas_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "nombre":$("#cajas_mini_nombre").val(),
          "activo":1,
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (typeof self.onSave != "undefined") self.onSave(model);
              if (typeof self.callback != "undefined") self.callback(model.id);
              self.cerrar();
            }
          }
        });
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },

  });

})(app.views, app.models);