(function ( models ) {

  models.CarpPropietario = Backbone.Model.extend({
    urlRoot: "carp_propietarios/",
    defaults: {
      password: "",
      id_empresa: ID_EMPRESA,
      id_perfiles: 953,
      nombre: "",
      apellido: "",
      documento: "",
      direccion: "",
      numero_calle: "",
      ciudad: "",
      numero_interno: "",
      telefono: "",
      email: "",
      activo: 1,
      path: "",
      titulo: "",
      agencia: "",
      id_agencia: 0,
      observaciones: "",
      vehiculo: "",
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.CarpPropietarios = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "carp_propietarios/"
    }
  });

})( app.collections, app.models.CarpPropietario, Backbone.Paginator);



(function ( app ) {

  app.views.CarpPropietarioItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#carp_propietarios_item').html()),
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
          "url":"carp_propietarios/function/change_property/",
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
      location.href="app/#carp_propietario/"+this.model.id;
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

  app.views.CarpPropietariosTableView = app.mixins.View.extend({

    template: _.template($("#carp_propietarios_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #carp_propietarios_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = (typeof options.permiso != undefined) ? options.permiso : 0;

      window.carp_propietarios_filter = (typeof window.carp_propietarios_filter != "undefined") ? window.carp_propietarios_filter : "";
      window.carp_propietarios_page = (typeof window.carp_propietarios_page != "undefined") ? window.carp_propietarios_page : 1;

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

      if (window.carp_propietarios_filter != this.$("#carp_propietarios_buscar").val().trim()) {
        window.carp_propietarios_filter = this.$("#carp_propietarios_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.carp_propietarios_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.carp_propietarios_filter),
      }
      if (PERFIL == 952) datos.id_agencia = ID_USUARIO;
      this.collection.server_api = datos;
      this.collection.goTo(window.carp_propietarios_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CarpPropietarioItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.CarpPropietarioEditView = app.mixins.View.extend({

    template: _.template($("#carp_propietarios_edit_panel_template").html()),

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
        permiso: self.options.permiso,
        edicion: edicion, 
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var url = "carp_agencias/";
      if (PERFIL == 952) url+="?id_agencia="+ID_USUARIO;
      new app.mixins.Select({
        modelClass: app.models.CarpAgencia,
        url: url,
        render: "#carp_propietario_agencias",
        disabled: (!edicion || self.options.permiso <= 2),
        selected: self.model.get("id_agencia"),
        onComplete:function(c) {
          crear_select2("carp_propietario_agencias");
        }                    
      });      
    },

    validar: function() {
      try {
        validate_input("carp_propietario_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("carp_propietario_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
        validate_input("carp_propietario_documento",IS_EMPTY,"Por favor, ingrese un DNI.");
        validate_input("carp_propietarios_email",IS_EMAIL,"Por favor, ingrese un email.");

        var documento = this.$("#carp_propietario_documento").val();
        if (!isInteger(documento)) {
          alert("Ingrese el documento solo con numeros.");
          this.$("#carp_propietario_documento").select();
          return false;
        }

        var telefono = this.$("#carp_propietario_telefono").val();
        if (!isTelephone(telefono)) {
          alert("Ingrese el telefono solo con numeros, sin 0 ni 15.");
          this.$("#carp_propietario_telefono").select();
          return false;
        }

        if (this.model.id == null) {
          validate_input("carp_propietario_password",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
          validate_input("carp_propietario_password_2",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
        }
        var password_1 = $("#carp_propietario_password").val();
        var password_2 = $("#carp_propietario_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#carp_propietario_password_2").focus();
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
          "id_agencia":this.$("#carp_propietario_agencias").val(),
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
            location.href = "app/#carp_propietarios";
          }
        });
      }
    },
    
  });

})(app.views, app.models);