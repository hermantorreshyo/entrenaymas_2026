// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Alumno = Backbone.Model.extend({
    urlRoot: "alumnos/",
    defaults: {
      numero_legajo: "",
      activo: 1,
      id_empresa: ID_EMPRESA,
      id_estado: 0,
      id_tutor: 0,
      nombre: "",
      apellido: "",
      id_cliente: 0,
      cuit: "",
      email: "",
      telefono: "",
      celular: "",
      direccion: "",
      id_localidad: 0,
      localidad: "",
      password: "",
      patologia: "",
      alergia: "",
      medicacion: "",
      obra_social: "",
      numero_obra_social: "",
      fecha_ingreso: "",
      fecha_egreso: "",
      fecha_nac: "",
      path: "",
      inhabilitado_biblioteca: 0,
      sexo: "M",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Alumnos = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
      order_by: 'nombre',
      order: 'asc',
    },        
    paginator_core: {
      url: "alumnos/function/ver"
    }
  });

})( app.collections, app.models.Alumno, Backbone.Paginator);


(function ( app ) {

  app.views.AlumnosTableView = app.mixins.View.extend({

    template: _.template($("#alumnos_panel_template").html()),

    myEvents: {
      "change #alumnos_buscar":"buscar",
      "click #alumnos_buscar_avanzada_btn":"buscar_avanzada",
    },        

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.alumnos_filter = (typeof window.alumnos_filter != "undefined") ? window.alumnos_filter : "";
      window.alumnos_id_comision = (typeof window.alumnos_id_comision != "undefined") ? window.alumnos_id_comision : 0;
      window.alumnos_page = (typeof window.alumnos_page != "undefined") ? window.alumnos_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
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

      if (window.alumnos_id_comision != 0) this.$(".advanced-search-btn").trigger("click");
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      if (window.alumnos_filter != this.$("#alumnos_buscar").val().trim()) {
        window.alumnos_filter = this.$("#alumnos_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#alumnos_buscar_comisiones").val() != null && window.alumnos_id_comision != this.$("#alumnos_buscar_comisiones").val()) {
        window.alumnos_id_comision = this.$("#alumnos_buscar_comisiones").val();
        cambio_parametros = true;
      }
      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.alumnos_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.alumnos_filter),
        "id_comision":window.alumnos_id_comision,
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.alumnos_page);
    },        

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.AlumnoItem({
        model: item,
        collection: this.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

    buscar_avanzada: function() {
      var self = this;
      self.id_comision = self.$("#alumnos_buscar_comisiones").val();
      this.buscar();
    },

    open_advanced_search: function() {
      if (typeof this.advanced_search_opened === "undefined") this.cargar_comisiones();
    },

    cargar_comisiones: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Comision,
        url: "comisiones/?offset=9999",
        render: "#alumnos_buscar_comisiones",
        firstOptions: ["<option value='0'>Comision</option>"],
        selected: window.alumnos_id_comision,
        onComplete:function(c) {
          crear_select2("alumnos_buscar_comisiones");
        }                    
      });
    },       

  });
})(app);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.AlumnoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#alumnos_item').html()),
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
      location.href="app/#alumno_acciones/"+this.model.id;
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

  views.AlumnoEditView = app.mixins.View.extend({

    template: _.template($("#alumnos_edit_panel_template").html()),
    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .nuevo_tutor":function(e){
        var self = this;
        if ($(".tutor_edit_mini").length > 0) return;
        var form = new app.views.TutorEditViewMini({
          "model": new app.models.Tutor(),
          "callback":self.cargar_tutores,
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete tutor_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#tutores_mini_nombre").focus();
      },
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

      var fecha_ingreso = this.model.get("fecha_ingreso");
      createdatepicker($(this.el).find("#alumno_fecha_ingreso"),fecha_ingreso);
      var fecha_egreso = this.model.get("fecha_egreso");
      createdatepicker($(this.el).find("#alumno_fecha_egreso"),fecha_egreso);
      var fecha_nac = this.model.get("fecha_nac");
      createdatepicker($(this.el).find("#alumno_fecha_nac"),fecha_nac);

      this.cargar_tutores();

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Comision,
        url: "comisiones/?offset=9999",
        firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#alumno_comisiones",
        selected: self.model.get("id_comision"),
        onComplete:function(c) {
          $("#alumno_comisiones").removeClass("form-control");
          $("#alumno_comisiones").select2({});
        }                    
      }); 

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#alumno_localidad").autocomplete({
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
        
    cargar_tutores: function(id_tutor) {
      var self = this;
      // Si se manda por parametro un ID, hay que poner ese nuevo en el modelo
      if (id_tutor != undefined) {
          this.model.set({ "id_tutor": id_tutor });
      }
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Tutor,
        url: "tutores/",
        firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#alumno_tutores",
        campoSelect: "nombre,apellido",
        id_field: "id_cliente",
        separador: " ",
        selected: self.model.get("id_tutor"),
        onComplete:function(c) {
          crear_select2("alumno_tutores");
        }                    
      });
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("alumno_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("alumno_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");

        this.model.set({
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
          "id_comision":self.$("#alumno_comisiones").val(),
          "id_tutor":self.$("#alumno_tutores").val(),
        });

        var password_1 = $("#alumno_password").val();
        var password_2 = $("#alumno_password_2").val();

        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#alumno_password_2").focus();
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
            location.href="app/#alumnos";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Alumno();
      this.render();
    },
    
  });

})(app.views, app.models);