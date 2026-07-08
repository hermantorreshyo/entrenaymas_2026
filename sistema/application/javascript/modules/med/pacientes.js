// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Paciente = Backbone.Model.extend({
    urlRoot: "pacientes/",
    defaults: {
      nombre: "",
      nombre_solo: "",
      apellido: "",
      activo: 1,
      direccion: "",
      email: "",
      id_localidad: 0,
      localidad: "",
      observaciones: "",
      telefono: "",
      celular: "",
      cuit: "",
      fecha_nac: "",
      id_obra_social: 0,
      obra_social: "",
      numero_obra_social: "",
      consultas: [],
      sexo: "M",
      path: "",
      password: "",
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Pacientes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "pacientes/function/ver/"
    },

    paginator_ui: {
      perPage: 10,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.Paciente, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PacienteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#pacientes_item').html()),
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar": "borrar",
      "click .duplicar": "duplicar",
      "keyup .radio":function(e) {
        if (e.which == 13) { this.seleccionar(); }
      },

      "focus .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
      },
      "blur .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
      },
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"med_pacientes",
          "attribute":"activo",
          "value":activo,
          "url":"pacientes/function/change_property/",
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_paciente_seleccionado = this.model.get("codigo");
        window.paciente_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        if (!this.habilitar_seleccion) location.href="app/#paciente_acciones/"+this.model.id;
      }
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso, seleccionar: this.habilitar_seleccion };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
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

  app.views.PacientesTableView = app.mixins.View.extend({

    template: _.template($("#pacientes_panel_template").html()),

    myEvents: {
      "keydown #pacientes_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "keydown #pacientes_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#pacientes_table tbody tr .radio:first").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #pacientes_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.pacientes_filter = (typeof window.pacientes_filter != "undefined") ? window.pacientes_filter : "";
      window.pacientes_page = (typeof window.pacientes_page != "undefined") ? window.pacientes_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);
    },

    buscar: function() {
      var cambio_parametros = false;
      if (window.pacientes_filter != this.$("#pacientes_buscar").val().trim()) {
        window.pacientes_filter = this.$("#pacientes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.pacientes_page = 1;
      var datos = {
        "term":encodeURIComponent(window.pacientes_filter),
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pacientes_page);
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"pacientes",
        "table":"pacientes",
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("pacientes/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"pacientes",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.pacientes_page = this.pagination.getPage();
      this.$("#pacientes_table tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (this.collection.length > 0) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        } else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PacienteItem({
        model: item,
        permiso: this.permiso,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find("tbody").append(view.render().el);
    },

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.PacienteEditView = app.mixins.View.extend({

    template: _.template($("#pacientes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .agregar_obra_social":function(e) {
        var self = this;
        if ($(".obra_social_edit_mini").length > 0) return;
        var form = new app.views.ObraSocialMiniEditView({
          "model": new app.models.ObraSocial(),
          "callback":function(m){
            self.model.set({ "id_obra_social":m });
            self.cargar_obras_sociales();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete obra_social_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#obras_sociales_mini_nombre").focus();
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
      var obj = { "edicion": edicion, "id":this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var fecha_nac = this.model.get("fecha_nac");
      if (isEmpty(fecha_nac)) fecha_nac = new Date();
      createdatepicker($(this.el).find("#pacientes_fecha_nac"),fecha_nac);

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#pacientes_localidad").autocomplete({
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

      var modelo = new app.models.Consulta({
        "id_paciente":self.model.id,
        "fecha":moment().format("DD/MM/YYYY"),
        "hora":moment().format("HH:mm:ss"),
      });
      modelo.on("remove",this.actualizar_consultas,this);
      this.editor = new app.views.CrearConsultaTimeline({
        "model":modelo,
        "view":self,
        "alerta_celular":(isEmpty(self.model.get("celular"))),
        "alerta_email":(isEmpty(self.model.get("email"))),
      });
      this.$("#paciente_crear_consultas").html(this.editor.el);
      this.cargar_obras_sociales();
      this.render_consultas();

      return this;
    },

    actualizar_consultas: function() {
      var self = this;
      $.ajax({
        "url":"pacientes/function/get_consultas/",
        "data":{
          "id_paciente":self.model.id,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r){
          self.model.set({"consultas":r});
          // Limpiamos el editor con una consulta nueva
          var modelo = new app.models.Consulta({
            "id_paciente":self.model.id,
            "fecha":moment().format("DD/MM/YYYY"),
            "hora":moment().format("HH:mm:ss"),
          });
          modelo.on("remove",this.actualizar_consultas,this);
          self.editor.model = modelo;
          self.editor.limpiar();
          self.render_consultas();
        }
      })
    },

    render_consultas: function() {
      var self = this;
      this.$(".streamline").empty();
      var consultas = this.model.get("consultas");
      for(var i=0; i<consultas.length;i++) {
        var c = consultas[i];
        var view = new app.views.ConsultaTimeline({
          "model":new app.models.Consulta(c),
          "editor":self.editor,
        });
        this.$(".streamline").append(view.el);
      }
    },

    cargar_obras_sociales: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.ObraSocial,
        url: "obras_sociales/",
        render: "#paciente_obras_sociales",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_obra_social"),
        onComplete: function() {
          crear_select2("paciente_obras_sociales");
        }
      });
    },

    validar: function() {
      try {
        validate_input("pacientes_nombre_solo",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("pacientes_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");

        var password_1 = $("#pacientes_password").val();
        var password_2 = $("#pacientes_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#pacientes_password_2").focus();
          return false;
        }
        if (!isEmpty(password_1)) {
          password_1 = hex_md5(password_1);
          this.model.set({
            "password":password_1
          });                    
        }
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() 
    {
      var self = this;
      if (this.validar() && this.guardando == 0) {
        this.guardando = 1;
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_obra_social": ((self.$("#paciente_obras_sociales").length > 0) ? self.$("#paciente_obras_sociales").val() : 0),
          "obra_social": ((self.$("#paciente_obras_sociales option:selected").length > 0) ? self.$("#paciente_obras_sociales option:selected").text() : ""),
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              location.href="app/#pacientes";
            }
            self.guardando = 0;
          },
          error: function() {
            show("Ocurrio un error al guardar el paciente.");
            self.guardando = 0;
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Paciente()
      this.render();
    },

  });

})(app.views, app.models);





(function ( views, models ) {

  views.PacienteEditViewMini = app.mixins.View.extend({

    template: _.template($("#pacientes_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .ver_avanzadas":function() {
        $(".ver_avanzadas").parent().hide();
        this.$("#pacientes_edit_mini_avanzadas").slideDown();
      },
      "click .cerrar": "cerrar",
      "keyup #pacientes_mini_nombre":function() {
        // Tenemos enlazada la referencia, por lo que cada vez que escribimos algo, debemos cambiar el input original
        if (this.input != undefined) {
          $(this.input).val($(this.el).find("#pacientes_mini_nombre").val());
        }
      },
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
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.input = this.options.input;
      this.onSave = this.options.onSave;
      this.callback = this.options.callback;
      this.guardando = 0;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        //$(this.el).find("#pacientes_mini_nombre").val($(this.input).val().trim());
      }
      return this;
    },

    focus: function() {
      $(this.el).find("#pacientes_mini_nombre").focus();
    },

    validar: function() {
      try {
        validate_input("pacientes_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar() && this.guardando == 0) {
        this.guardando = 1;
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (typeof self.onSave != "undefined") self.onSave(model);
              if (typeof self.callback != "undefined") self.callback(model.id);
              self.cerrar();
            }
            self.guardando = 0;
          },
          error: function() {
            show("Ocurrio un error al guardar al paciente.");
            self.guardando = 0;
          }
        });
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },    

    limpiar : function() {
      this.model = new app.models.Paciente()
      this.render();
    },        

  });

})(app.views, app.models);