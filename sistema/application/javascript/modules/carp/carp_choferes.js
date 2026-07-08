(function ( models ) {

  models.CarpChofer = Backbone.Model.extend({
    urlRoot: "carp_choferes/",
    defaults: {
      password: "",
      id_empresa: ID_EMPRESA,
      id_usuario: 0,
      id_propietario: 0,
      propietario: "",
      estado: 0,
      nombre: "",
      dni: "",
      direccion: "",
      agencia: "",
      numero_calle: "",
      ciudad: "",
      apellido: "",
      fecha_alta: "",
      fecha_baja: "",
      telefono: "",
      email: "",
      activo: 1,
      observaciones: "",
      vehiculo: "",
      path: "",
      bolsa_trabajo: 0,
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.CarpChoferes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "carp_choferes/"
    }
    
  });

})( app.collections, app.models.CarpChofer, Backbone.Paginator);



(function ( app ) {

  app.views.CarpChoferItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#carp_choferes_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .enviar_whatsapp":function(){
        var mensaje = ""
        var tel = this.model.get("telefono");
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel+"?text="+encodeURIComponent(mensaje);
        window.open(link_ws,"_blank");
      },
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"com_usuarios",
          "url":"carp_choferes/function/change_property/",
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
      if (PERFIL == 952) {
        // Si es perfil de agencia
        if (this.model.get("id_agencia") != ID_USUARIO) return;
      } else if (PERFIL == 953) {
        // Si es perfil de propietario
        if (this.model.get("id_propietario") != ID_USUARIO) return;
      }
      location.href="app/#carp_chofer/"+this.model.id;
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

  app.views.CarpChoferesTableView = app.mixins.View.extend({

    template: _.template($("#carp_choferes_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "click .cambiar_tab":function(e) {
        window.carp_choferes_estado = $(e.currentTarget).data("tipo");
        $(e.currentTarget).parents(".nav-tabs").find(".active").removeClass("active");
        $(e.currentTarget).parent().addClass("active");
        this.buscar();
      },
      "keypress #carp_choferes_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = (typeof options.permiso != undefined) ? options.permiso : 0;

      window.carp_choferes_filter = (typeof window.carp_choferes_filter != "undefined") ? window.carp_choferes_filter : "";
      window.carp_choferes_estado = (typeof window.carp_choferes_estado != "undefined") ? window.carp_choferes_estado : "";
      window.carp_choferes_id_propietario = (typeof window.carp_choferes_id_propietario != "undefined") ? window.carp_choferes_id_propietario : 0;
      window.carp_choferes_page = (typeof window.carp_choferes_page != "undefined") ? window.carp_choferes_page : 1;

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

      /*
      new app.mixins.Select({
        modelClass: app.models.CarpPropietario,
        url: "carp_propietarios/",
        render: "#carp_choferes_propietarios",
        firstOptions: ["<option value='0'>Propietario</option>"],
      });
      */

      return this;
    },

    buscar: function() {

      var cambio_parametros = false;

      if (window.carp_choferes_filter != this.$("#carp_choferes_buscar").val().trim()) {
        window.carp_choferes_filter = this.$("#carp_choferes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (window.carp_choferes_id_propietario != this.$("#carp_choferes_propietarios").val()) {
        window.carp_choferes_id_propietario = this.$("#carp_choferes_propietarios").val();
        cambio_parametros = true;
      }

      if (cambio_parametros) window.carp_choferes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.carp_choferes_filter),
        "estado":window.carp_choferes_estado,
      }
      if (PERFIL == 952) datos.id_agencia = ID_USUARIO;
      else if (PERFIL == 953) datos.id_propietario = ID_USUARIO;
      this.collection.server_api = datos;
      this.collection.goTo(window.carp_choferes_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CarpChoferItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.CarpChoferEditView = app.mixins.View.extend({

    template: _.template($("#carp_choferes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "change #carp_chofer_dni":"consultar_por_dni",
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

      createdatepicker($(this.el).find("#carp_chofer_fecha_alta"),this.model.get("fecha_alta"));
      createdatepicker($(this.el).find("#carp_chofer_fecha_baja"),this.model.get("fecha_baja"));

      var url = "carp_propietarios/?mostrar_agencias=1";
      if (PERFIL == 953) url+="&id_propietario="+ID_USUARIO;
      new app.mixins.Select({
        modelClass: app.models.CarpPropietario,
        url: url,
        render: "#carp_chofer_propietarios",
        selected: self.model.get("id_propietario"),
        disabled: (!edicion),
        onComplete:function(c) {
          crear_select2("carp_chofer_propietarios");
        }                    
      });      
    },

    consultar_por_dni: function() {
      if (!this.model.isNew()) return; // Si estamos editando, no hace nada
      if (PERFIL != 952 && PERFIL != 953) return; // Solamente en los perfiles de AGENCIA y PROPIETARIO
      var self = this;
      // Si estamos creando un nuevo chofer, primero controlamos por DNI
      var dni = this.$("#carp_chofer_dni").val();
      var id_propietario = this.$("#carp_chofer_propietarios").val();
      if (isEmpty(dni)) return;
      $.ajax({
        "url":"carp_choferes/function/buscar_por_dni/",
        "dataType":"json",
        "data":{
          "dni":dni,
          "id_propietario":id_propietario,
        },
        "type":"post",
        "success":function(r) {
          if (r.error == 0) {
            self.$("#carp_chofer_error").val("0");
            self.$("#carp_chofer_nombre").val(r.nombre);
            self.$("#carp_chofer_apellido").val(r.apellido);
            self.$("#carp_chofer_telefono").val(r.telefono);
            self.$("#carp_chofer_estados").val(r.estado);
            self.$("#carp_chofer_direccion").val(r.direccion);
            self.$("#carp_chofer_numero_calle").val(r.numero_calle);
            self.$("#carp_chofer_ciudad").val(r.ciudad);
            self.$("#carp_chofer_observaciones").val(r.observaciones);
          } else if (r.error == 1) {
            if (!isEmpty(r.mensaje)) {
              self.$("#carp_chofer_error").val("1");
              alert(r.mensaje);
            } 
          }
        }
      })
    },

    validar: function() {
      try {
        validate_input("carp_chofer_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("carp_chofer_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
        validate_input("carp_chofer_dni",IS_EMPTY,"Por favor, ingrese un DNI.");

        var error = this.$("#carp_chofer_error").val();
        if (error == "1") {
          alert("El chofer ya se encuentra cargado en otra agencia. Por favor contacte al administrador.");
          return false;
        }

        this.model.set({
          "path":(this.$("#hidden_path").length > 0) ? $("#hidden_path").val() : "",
          "id_propietario":this.$("#carp_chofer_propietarios").val(),
          "estado":this.$("#carp_chofer_estados").val(),
          "fecha_alta":this.$("#carp_chofer_fecha_alta").val(),
          "fecha_baja":this.$("#carp_chofer_fecha_baja").val(),
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
            location.href = "app/#carp_choferes";
          }
        });
      }
    },
    
  });

})(app.views, app.models);