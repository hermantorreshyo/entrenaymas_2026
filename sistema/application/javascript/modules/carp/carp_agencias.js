(function ( models ) {

  models.CarpAgencia = Backbone.Model.extend({
    urlRoot: "carp_agencias/",
    defaults: {
      password: "",
      id_empresa: ID_EMPRESA,
      id_perfiles: 952,
      nombre: "",
      direccion: "",
      telefono: "",
      email: "",
      activo: 1,
      path: "",
      titulo: "",
      cargo: "",
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.CarpAgencias = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "carp_agencias/"
    }
  });

})( app.collections, app.models.CarpAgencia, Backbone.Paginator);



(function ( app ) {

  app.views.CarpAgenciaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#carp_agencias_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"com_usuarios",
          "url":"carp_agencias/function/change_property/",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
    },
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var self = this;
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));            
      return this;
    },
    editar: function() {
      location.href="app/#carp_agencia/"+this.model.id;
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
    },
  });

})( app );


(function ( app ) {

  app.views.CarpAgenciasTableView = app.mixins.View.extend({

    template: _.template($("#carp_agencias_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #carp_agencias_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = (typeof options.permiso != undefined) ? options.permiso : 0;

      window.carp_agencias_filter = (typeof window.carp_agencias_filter != "undefined") ? window.carp_agencias_filter : "";
      window.carp_agencias_page = (typeof window.carp_agencias_page != "undefined") ? window.carp_agencias_page : 1;

      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {

      var self = this;
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: self.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "edicion":(this.permiso > 3),
      }));
      this.$(".pagination_container").html(this.pagination.el);
      return this;
    },

    buscar: function() {

      var cambio_parametros = false;

      if (window.carp_agencias_filter != this.$("#carp_agencias_buscar").val().trim()) {
        window.carp_agencias_filter = this.$("#carp_agencias_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.carp_agencias_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.carp_agencias_filter),
      }
      this.collection.server_api = datos;
      this.collection.goTo(window.carp_agencias_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CarpAgenciaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.CarpAgenciaEditView = app.mixins.View.extend({

    template: _.template($("#carp_agencias_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1 || this.model.isNew()) edicion = true;
      var obj = { 
        edicion: edicion, 
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
    },

    validar: function() {
      try {
        validate_input("carp_agencias_email",IS_EMAIL,"Por favor, ingrese un email.");

        if (this.model.id == null) {
          validate_input("carp_agencia_password",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
          validate_input("carp_agencia_password_2",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
        }
        var password_1 = $("#carp_agencia_password").val();
        var password_2 = $("#carp_agencia_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#carp_agencia_password_2").focus();
          return false;
        }
        if (!isEmpty(password_1)) {
          password_1 = hex_md5(password_1);
          this.model.set({
            "password":password_1
          });                    
        }          

        this.model.set({
          "path":(this.$("#hidden_path").length > 0) ? $("#hidden_path").val() : "",
        });
        return true;
      } catch(e) {
        return false;
      }
    },
        
    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.set({});
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            location.href = "app/#carp_agencias";
          }
        });
      }
    },
    
  });

})(app.views, app.models);