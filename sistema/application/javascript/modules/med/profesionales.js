// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Profesional = Backbone.Model.extend({
    urlRoot: "profesionales/",
    defaults: {
      nombre: "",
      apellido: "",
      activo: 1,
      destacado: 0,
      direccion: "",
      email: "",
      id_localidad: 0,
      localidad: "",
      observaciones: "",
      telefono: "",
      celular: "",
      matricula: "",
      dni: "",
      id_especialidad: 0,
      especialidad: "",
      path: "",
      texto: "",
      link: "",
      duracion_turno: 15,
      hora_desde: "",
      hora_hasta: "",

      horario_lunes_1:"",
      horario_lunes_2:"",
      horario_martes_1:"",
      horario_martes_2:"",
      horario_miercoles_1:"",
      horario_miercoles_2:"",
      horario_jueves_1:"",
      horario_jueves_2:"",
      horario_viernes_1:"",
      horario_viernes_2:"",
      horario_sabado_1:"",
      horario_sabado_2:"",            
      horario_domingo_1:"",
      horario_domingo_2:"",

      horario_lunes_3:"",
      horario_lunes_4:"",
      horario_martes_3:"",
      horario_martes_4:"",
      horario_miercoles_3:"",
      horario_miercoles_4:"",
      horario_jueves_3:"",
      horario_jueves_4:"",
      horario_viernes_3:"",
      horario_viernes_4:"",
      horario_sabado_3:"",
      horario_sabado_4:"",            
      horario_domingo_3:"",
      horario_domingo_4:"",   

    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Profesionales = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "profesionales/function/ver/"
    },

    paginator_ui: {
      perPage: 10,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.Profesional, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ProfesionalItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#profesionales_item').html()),
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
          "table":"med_profesionales",
          "attribute":"activo",
          "value":activo,
          "url":"profesionales/function/change_property/",
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"med_profesionales",
          "attribute":"destacado",
          "value":destacado,
          "url":"profesionales/function/change_property/",
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
        window.codigo_profesional_seleccionado = this.model.get("codigo");
        window.profesional_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        if (!this.habilitar_seleccion) location.href="app/#profesional/"+this.model.id;
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

  app.views.ProfesionalesTableView = app.mixins.View.extend({

    template: _.template($("#profesionales_panel_template").html()),

    myEvents: {
      "keydown #profesionales_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "keydown #profesionales_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#profesionales_table tbody tr .radio:first").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #profesionales_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.profesionales_filter = (typeof window.profesionales_filter != "undefined") ? window.profesionales_filter : "";
      window.profesionales_page = (typeof window.profesionales_page != "undefined") ? window.profesionales_page : 1;
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
      if (window.profesionales_filter != this.$("#profesionales_buscar").val().trim()) {
        window.profesionales_filter = this.$("#profesionales_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.profesionales_page = 1;
      var datos = {
        "term":encodeURIComponent(window.profesionales_filter),
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.profesionales_page);
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"profesionales",
        "table":"profesionales",
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("profesionales/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"profesionales",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.profesionales_page = this.pagination.getPage();
      this.$("#profesionales_table tbody").empty();
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
      var view = new app.views.ProfesionalItem({
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

  views.ProfesionalEditView = app.mixins.View.extend({

    template: _.template($("#profesionales_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .agregar_especialidad":function(e) {
        var self = this;
        if ($(".especialidad_edit_mini").length > 0) return;
        var form = new app.views.EspecialidadMiniEditView({
          "model": new app.models.Especialidad(),
          "callback":function(m){
            self.model.set({ "id_especialidad":m });
            self.cargar_especialidades();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete especialidad_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#especialidades_mini_nombre").focus();
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

      $(this.el).find("#profesionales_horario_lunes_1").mask("99:99");
      $(this.el).find("#profesionales_horario_lunes_2").mask("99:99");
      $(this.el).find("#profesionales_horario_martes_1").mask("99:99");
      $(this.el).find("#profesionales_horario_martes_2").mask("99:99");
      $(this.el).find("#profesionales_horario_miercoles_1").mask("99:99");
      $(this.el).find("#profesionales_horario_miercoles_2").mask("99:99");
      $(this.el).find("#profesionales_horario_jueves_1").mask("99:99");
      $(this.el).find("#profesionales_horario_jueves_2").mask("99:99");
      $(this.el).find("#profesionales_horario_viernes_1").mask("99:99");
      $(this.el).find("#profesionales_horario_viernes_2").mask("99:99");
      $(this.el).find("#profesionales_horario_sabado_1").mask("99:99");
      $(this.el).find("#profesionales_horario_sabado_2").mask("99:99");            
      $(this.el).find("#profesionales_horario_domingo_1").mask("99:99");
      $(this.el).find("#profesionales_horario_domingo_2").mask("99:99");

      $(this.el).find("#profesionales_horario_lunes_3").mask("99:99");
      $(this.el).find("#profesionales_horario_lunes_4").mask("99:99");
      $(this.el).find("#profesionales_horario_martes_3").mask("99:99");
      $(this.el).find("#profesionales_horario_martes_4").mask("99:99");
      $(this.el).find("#profesionales_horario_miercoles_3").mask("99:99");
      $(this.el).find("#profesionales_horario_miercoles_4").mask("99:99");
      $(this.el).find("#profesionales_horario_jueves_3").mask("99:99");
      $(this.el).find("#profesionales_horario_jueves_4").mask("99:99");
      $(this.el).find("#profesionales_horario_viernes_3").mask("99:99");
      $(this.el).find("#profesionales_horario_viernes_4").mask("99:99");
      $(this.el).find("#profesionales_horario_sabado_3").mask("99:99");
      $(this.el).find("#profesionales_horario_sabado_4").mask("99:99");            
      $(this.el).find("#profesionales_horario_domingo_3").mask("99:99");
      $(this.el).find("#profesionales_horario_domingo_4").mask("99:99");   

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#profesionales_localidad").autocomplete({
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

      this.cargar_especialidades();
      this.$("#profesional_hora_desde").mask("99:99");
      this.$("#profesional_hora_hasta").mask("99:99");

      return this;
    },

    cargar_especialidades: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Especialidad,
        url: "especialidades/",
        render: "#profesional_especialidades",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_especialidad"),
        onComplete: function() {
          crear_select2("profesional_especialidades");
        }
      });
    },

    validar: function() {
      try {
        validate_input("profesionales_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var password_1 = $("#profesionales_password").val();
        var password_2 = $("#profesionales_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#profesionales_password_2").focus();
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
          "id_especialidad": ((self.$("#profesional_especialidades").length > 0) ? self.$("#profesional_especialidades").val() : 0),
          "especialidad": ((self.$("#profesional_especialidades option:selected").length > 0) ? self.$("#profesional_especialidades option:selected").text() : ""),
          "path":self.$("#hidden_path").val(),
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              location.reload(true);
              //location.href="app/#profesionales";
            }
            self.guardando = 0;
          },
          error: function() {
            show("Ocurrio un error al guardar el profesional.");
            self.guardando = 0;
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Profesional()
      this.render();
    },

  });

})(app.views, app.models);





(function ( views, models ) {

  views.ProfesionalEditViewMini = app.mixins.View.extend({

    template: _.template($("#profesionales_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .ver_avanzadas":function() {
        $(".ver_avanzadas").parent().hide();
        this.$("#profesionales_edit_mini_avanzadas").slideDown();
      },
      "click .cerrar": "cerrar",
      "keyup #profesionales_mini_nombre":function() {
        // Tenemos enlazada la referencia, por lo que cada vez que escribimos algo, debemos cambiar el input original
        if (this.input != undefined) {
          $(this.input).val($(this.el).find("#profesionales_mini_nombre").val());
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
        $(this.el).find("#profesionales_mini_nombre").val($(this.input).val().trim());
      }
      return this;
    },

    focus: function() {
      $(this.el).find("#profesionales_mini_nombre").focus();
    },

    validar: function() {
      try {
        validate_input("profesionales_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
            show("Ocurrio un error al guardar al profesional.");
            self.guardando = 0;
          }
        });
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },    

    limpiar : function() {
      this.model = new app.models.Profesional()
      this.render();
    },        

  });

})(app.views, app.models);





// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.ProfesionalTurnosView = app.mixins.View.extend({

    template: _.template($("#profesional_turnos_panel_template").html()),

    myEvents: {
      "click .pendientes":function() {
        this.estado_turno = 0;
        this.$(".pendientes").removeClass('btn-none').addClass('btn-info');
        this.$(".realizados").removeClass('btn-info').addClass('btn-none');
        this.actualizar_consultas();
      },
      "click .realizados":function() {
        this.estado_turno = 1;
        this.$(".pendientes").removeClass('btn-info').addClass('btn-none');
        this.$(".realizados").removeClass('btn-none').addClass('btn-info');
        this.actualizar_consultas();
      }
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.estado_turno = 0;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template());
      self.consultas = new Array();
      this.actualizar_consultas();
      return this;
    },

    actualizar_consultas: function() {
      var self = this;
      var fecha = moment().format("DD/MM/YYYY");
      $.ajax({
        "url":"profesionales/function/get_consultas/",
        "data":{
          "id_profesional":1, // TODO: Cambiar por el que esta logueado el usuario
          "fecha":fecha,
          "estado_turno":self.estado_turno,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r){
          self.consultas = r;
          self.render_consultas();
        }
      })
    },

    render_consultas: function() {
      var self = this;
      this.$(".streamline").empty();
      for(var i=0; i<self.consultas.length;i++) {
        var c = self.consultas[i];
        var modelo = new app.models.Consulta(c);
        modelo.on("actualizar",self.actualizar_consultas);
        var view = new app.views.ConsultaTimeline({
          "model":modelo,
          "mostrar_paciente":true,
        });
        this.$(".streamline").append(view.el);
      }
    },

  });

})(app.views, app.models);
