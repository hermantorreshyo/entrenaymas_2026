// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Tutor = Backbone.Model.extend({
    urlRoot: "tutores/",
    defaults: {
      activo: 1,
      id_empresa: ID_EMPRESA,
      nombre: "",
      apellido: "",
      id_cliente: 0,
      cuit: "",
      email: "",
      telefono: "",
      celular: "",
      telefono_2: "",
      celular_2: "",
      direccion: "",
      id_localidad: 0,
      localidad: "",
      password: "",
      path: "",
      observaciones: "",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Tutores = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
      order_by: 'nombre',
      order: 'asc',
    },        
    paginator_core: {
      url: "tutores/function/ver"
    }
  });

})( app.collections, app.models.Tutor, Backbone.Paginator);


(function ( app ) {

  app.views.TutoresTableView = app.mixins.View.extend({

    template: _.template($("#tutores_panel_template").html()),

    myEvents: {
      "change #tutores_buscar":"buscar",
    },        

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
      this.render();
      this.collection.on('sync', this.addAll, this);
      this.collection.goTo(this.pagina);
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
      var filter = this.$("#tutores_buscar").val();
      self.filter = (typeof filter != "undefined") ? filter.trim() : "";
      this.collection.server_api = {
        "filter":self.filter,
      };
      this.collection.pager();
    },        

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.TutorItem({
        model: item,
        collection: this.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

  });
})(app);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.TutorItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#tutores_item').html()),
    myEvents: {
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
          "table":"clientes",
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
      location.href="app/#tutor_acciones/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.TutorEditView = app.mixins.View.extend({

    template: _.template($("#tutores_edit_panel_template").html()),
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
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#tutor_localidad").autocomplete({
        "minLength":3,
        "source":function(request,response) {
          $.ajax({
            "url":"localidades/function/get_by_nombre/",
            "data":{
              "term":request.term
            },
            "dataType":"json",
            "type":"get",
            "success":function(res){
              response(res);
            }
          });
        },
        "select":function(event,ui){
          self.model.set({
            "id_localidad":ui.item.id,
            "localidad":ui.item.label,
          });
        },
      });
            
      return this;
    },
        
    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("tutor_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("tutor_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
        validate_input("tutor_email",IS_EMAIL,"Por favor, ingrese un email.");

        this.model.set({
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
        });

        var password_1 = $("#tutor_password").val();
        var password_2 = $("#tutor_password_2").val();

        // Si estamos insertando uno nuevo, el password es obligatorio
        if (this.model.id == null && isEmpty(password_1)) {
          show("Por favor ingrese una clave.");
          $("#tutor_password").focus();
          return false;
        }

        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#tutor_password_2").focus();
          return false;
        }
        if (!isEmpty(password_1)) {
          password_1 = hex_md5(password_1);
          this.model.set({
            "password":password_1
          });                    
        }

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
          },{
          success: function(model,response) {
            location.href="app/#tutores";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Tutor();
      this.render();
    },
    
  });

})(app.views, app.models);


(function ( views, models ) {

  views.TutorEditViewMini = app.mixins.View.extend({

    template: _.template($("#tutores_edit_mini_panel_template").html()),
  
    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) $(e.currentTarget).parent().next().find(".tab").focus();
      },
      "keyup .tab":function(e) {
        if (e.which == 27) this.cerrar();
      }
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.input = this.options.input;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#tutores_mini_nombre").val($(this.input).val().trim());
      }
      return this;
    },
    
    focus: function() {
      $(this.el).find("#tutores_mini_nombre").focus();
    },

    validar: function() {
      try {
        validate_input("tutores_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
          "nombre":$("#tutores_mini_nombre").val(),
          "apellido":$("#tutores_mini_apellido").val(),
          "telefono":$("#tutores_mini_telefono").val(),
          "celular":$("#tutores_mini_celular").val(),
          "email":$("#tutores_mini_email").val(),
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              self.cerrar();
              if (self.options.callback != undefined) self.options.callback(self.model.id);
            }
          }
        });
      }
    },
      
    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },
    
    limpiar : function() {
      this.model = new app.models.Tutor();
      this.render();
    },        
  
  });

})(app.views, app.models);