(function ( models ) {

  models.Usuario = Backbone.Model.extend({
    urlRoot: "usuarios/",
    defaults: {
      nombre_usuario: "",
      password: "",
      id_perfiles: 0,
      id_empresa: ID_EMPRESA,
      id_sucursal: 0,
      sucursal: "",
      nombre: "",
      apellido: "",
      dni: "",
      fecha_alta: "",
      direccion: "",
      telefono: "",
      celular: "",
      email: "",
      activo: 1,
      admin: 0,
      hora_desde: "",
      hora_hasta: "",
      aparece_web: ((ID_PROYECTO==14)?1:0),
      archivo: "",
      cargo: "",
      titulo: "",
      path: "",
      estado_inicial: ((TOQUE == 1)?1:0),
      ocultar_notificaciones: 0,
      id_vendedor: 0,
      language: "es",
      horarios: [],
      horarios_entrega: [],
      sucursales: [],
      images: [],
      principal: 0,
      sesion_gratis: 0,

      // Estos datos se guardan en otra tabla
      solo_usuario: 0,
      destacado: 0,
      path_2: "",
      clave_especial: "",
      facebook: "",
      instagram: "",
      linkedin: "",
      custom_1: "",
      custom_2: "",
      custom_3: "",
      custom_4: "",
      custom_5: "",
      custom_6: "",
      custom_7: "",
      custom_8: "",
      custom_9: "",
      custom_10: "",
      sobre_mi: "",
      maximo: 0,
      id_localidad: 0,
      id_provincia: 0,
      id_pais: 0,
      localidad: "",
      provincia: "",
      pais: "",
      sobre_mi: "",
      seo_title: "",
      seo_description: "",
      cajas_regalo: [],

      toque_categorias: [],
      localidades: [],

      // PsicoWeb
      obras_sociales: [],
      especialidades: [],
      tipos_pacientes: [],
      tipos_atenciones: [],
      tipos_terapias: [],
      formas_pago: [],
      titulos: [],
      direcciones: [],
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Usuarios = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "usuarios/"
    }
    
  });

})( app.collections, app.models.Usuario, Backbone.Paginator);



(function ( app ) {

  app.views.UsuarioItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#usuarios_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"usuarios/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              //window.location.href = "app/#articulo/"+r.id;
              location.reload();
            },
          });
        }
        return false;
      },
      "click .login":"login",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) {
          $.ajax({
            "url":"usuarios/function/activar_usuario/",
            "dataType":"json",
            "data":{
              "id":self.model.id,
              "activo":activo,
            },
            "type":"post",
            "success":function(){
              self.render();
            }
          })
        } else {
          this.change_property({
            "table":"com_usuarios",
            "url":"usuarios/function/change_property/",
            "attribute":"activo",
            "value":activo,
            "id":self.model.id,
            "success":function(){
              self.render();
            }
          });          
        }
        return false;
      },
      "click .reparto_propio":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var aparece_web = this.model.get("aparece_web");
        aparece_web = (aparece_web == 1)?0:1;
        self.model.set({"aparece_web":aparece_web});
        this.change_property({
          "table":"com_usuarios",
          "url":"usuarios/function/change_property/",
          "attribute":"aparece_web",
          "value":aparece_web,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .sumar_tiempo_servicio":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var id_referencia = this.model.get("id_referencia");
        id_referencia = (id_referencia == 1)?0:1;
        self.model.set({"id_referencia":id_referencia});
        this.change_property({
          "table":"com_usuarios",
          "url":"usuarios/function/change_property/",
          "attribute":"id_referencia",
          "value":id_referencia,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      }, 
      "click .pickup":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var ocultar_notificaciones = this.model.get("ocultar_notificaciones");
        ocultar_notificaciones = (ocultar_notificaciones == 1)?0:1;
        self.model.set({"ocultar_notificaciones":ocultar_notificaciones});
        this.change_property({
          "table":"com_usuarios",
          "url":"usuarios/function/change_property/",
          "attribute":"ocultar_notificaciones",
          "value":ocultar_notificaciones,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      }, 
      "click .habilitar_deposito":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var custom_1 = this.model.get("custom_1");
        custom_1 = (custom_1 == 1)?0:1;
        self.model.set({"custom_1":custom_1});
        this.change_property({
          "table":"com_usuarios_extension",
          "url":"usuarios/function/change_property/",
          "attribute":"custom_1",
          "id_field":"id_usuario",
          "value":custom_1,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      }, 

      "click .habilitar_entrega_programada":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var custom_2 = this.model.get("custom_2");
        custom_2 = (custom_2 == 1)?0:1;
        self.model.set({"custom_2":custom_2});
        this.change_property({
          "table":"com_usuarios_extension",
          "url":"usuarios/function/change_property/",
          "attribute":"custom_2",
          "id_field":"id_usuario",
          "value":custom_2,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      }, 

      // PARA ESTEBAN ECHEVERRIA
      "click .coordinar_envio":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var custom_3 = this.model.get("custom_3");
        custom_3 = (custom_3 == 1)?0:1;
        self.model.set({"custom_3":custom_3});
        this.change_property({
          "table":"com_usuarios_extension",
          "url":"usuarios/function/change_property/",
          "attribute":"custom_3",
          "id_field":"id_usuario",
          "value":custom_3,
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
          "table":"com_usuarios_extension",
          "url":"usuarios/function/change_destacado/",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },

      "click .no_calcular_puntaje":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var no_calcular_puntaje = this.model.get("no_calcular_puntaje");
        no_calcular_puntaje = (no_calcular_puntaje == 1)?0:1;
        self.model.set({"no_calcular_puntaje":no_calcular_puntaje});
        this.change_property({
          "table":"com_usuarios",
          "url":"usuarios/function/change_property/",
          "attribute":"no_calcular_puntaje",
          "value":no_calcular_puntaje,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },

      "change .puntaje":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var puntaje = $(e.currentTarget).val();
        self.model.set({"puntaje":puntaje});
        this.change_property({
          "table":"com_usuarios",
          "url":"usuarios/function/change_property/",
          "attribute":"puntaje",
          "value":puntaje,
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
      $('[data-toggle="tooltip"]').tooltip();
      return this;
    },
    editar: function() {
      var admin = (this.model.get("admin") == 0) ? "usuario":"administrador";
      location.href="app/#"+admin+"/"+this.model.id;
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
    },
    login: function(e) {
      var self = this;
      e.stopPropagation();
      $.ajax({
        "url":"login/cambiar_usuario/"+ID_EMPRESA+"/"+self.model.id,
        "dataType":"json",
        "success":function(r){
          if (r.error == 1) {
            alert(r.mensaje)
          } else {
            location.reload();
          }
        }
      });
    },
  });

})( app );


(function ( app ) {

  app.views.UsuariosTableView = app.mixins.View.extend({

    template: _.template($("#usuarios_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #usuarios_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = (typeof options.permiso != undefined) ? options.permiso : 0;
      this.link_nuevo = (typeof options.link_nuevo != undefined && !isEmpty(options.link_nuevo)) ? options.link_nuevo : "usuario";
      this.admin = (typeof options.admin != undefined) ? options.admin : 0;

      window.usuarios_filter = (typeof window.usuarios_filter != "undefined") ? window.usuarios_filter : "";
      window.usuarios_id_sucursal = (typeof window.usuarios_id_sucursal != "undefined") ? window.usuarios_id_sucursal : 0;
      window.usuarios_id_perfil = (typeof window.usuarios_id_perfil != "undefined") ? window.usuarios_id_perfil : 0;
      window.usuarios_page = (typeof window.usuarios_page != "undefined") ? window.usuarios_page : 1;

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
        "link_nuevo":this.link_nuevo,
        "permiso":this.permiso,
        "edicion":(this.permiso > 3),
        "admin":this.admin,
      }));

      this.$(".pagination_container").html(this.pagination.el);

      new app.mixins.Select({
        modelClass: app.models.Perfil,
        url: "perfiles/",
        render: "#usuarios_perfiles",
        firstOptions: ["<option value='0'>Perfil</option>"],
      });

      return this;
    },

    buscar: function() {

      var cambio_parametros = false;

      if (window.usuarios_filter != this.$("#usuarios_buscar").val().trim()) {
        window.usuarios_filter = this.$("#usuarios_buscar").val().trim();
        cambio_parametros = true;
      }
      if (window.usuarios_id_sucursal != this.$("#usuarios_sucursales").val()) {
        window.usuarios_id_sucursal = this.$("#usuarios_sucursales").val();
        cambio_parametros = true;
      }
      if (window.usuarios_id_perfil != this.$("#usuarios_perfiles").val()) {
        window.usuarios_id_perfil = this.$("#usuarios_perfiles").val();
        cambio_parametros = true;
      }

      if (cambio_parametros) window.usuarios_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.usuarios_filter),
        "id_sucursal":window.usuarios_id_sucursal,
        "id_perfil":(TOQUE == 1) ? 661 : window.usuarios_id_perfil,
        "admin":this.admin,
      }
      this.collection.server_api = datos;
      this.collection.goTo(window.usuarios_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.UsuarioItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.UsuarioEditView = app.mixins.View.extend({

    template: _.template($("#usuarios_edit_panel_template").html()),

    myEvents: {

      // Utilizados en Entrena y Mas
      "change #usuario_provincias":"cargar_localidades",
      "click #usuario_localidad_agregar":"agregar_localidad",
      "click .editar_localidad":"editar_localidad",
      "click .eliminar_localidad":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #usuario_localidad_valor":function(e) {
        if (e.which == 13) this.agregar_localidad();
      },
      "click .copiar":function(e){
        var aux = document.createElement("input");
        aux.setAttribute("value", "https://entrenaymas.com/web/calificar/?id="+this.model.id);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
      },
      "click .guardar": "guardar",
      "click #horario_agregar":"agregar_horario",
      "click .editar_horario":"editar_horario",
      "click .eliminar_horario":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #usuario_horario_hasta":function(e) {
        if (e.which == 13) this.agregar_horario();
      },

      "click #horario_entrega_agregar":"agregar_horario_entrega",
      "click .editar_horario_entrega":"editar_horario_entrega",
      "click .eliminar_horario_entrega":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #usuario_horario_entrega_hasta":function(e) {
        if (e.which == 13) this.agregar_horario_entrega();
      },

      "change #usuario_localidades":function() {
        if (ID_EMPRESA == 1319) {
          var self = this;
          $("#usuario_localidades_puja").val("");
          var id_localidad = this.$("#usuario_localidades").val();
          $.ajax({
            "url":"entrenaymas/function/ver_puja_ciudad/",
            "dataType":"json",
            "type":"get",
            "data":{
              "id_usuario":(self.model.isNew() ? 0 : self.model.id),
              "id_localidad":id_localidad,
            },
            "success":function(r) {
              $("#usuario_localidades_puja").val(r.maximo);
            },
          });
        }
      },

      "change #usuario_eym_provincias":"cargar_localidades_eym",

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "usuarios/function/upload_images/",
          "view": self,
        });
      },
      "click .nueva_direccion":function(){
        var self = this;
        var v = new app.views.TurnoServicioEditView({
          model: new app.models.TurnoServicio({
            "nuevo":1,
            "id_usuario":self.model.id,
          }),
          collection: self.direcciones,
          lightbox: true,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "callback":function() {
            console.log(self.direcciones);
          }
        });
      },      
    },

    cargar_localidades_eym: function() {
      if (this.$("#usuario_eym_provincias").length > 0) {
        var id_departamento = this.$("#usuario_eym_provincias").val();
        var id_localidad = this.model.get("id_localidad");
        new app.mixins.Select({
          modelClass: app.models.Localidad,
          url: "/sistema/localidades/function/get_by_departamento/"+id_departamento,
          render: "#usuario_eym_localidades",
          selected: id_localidad,
          onComplete:function(c) {
            crear_select2("usuario_eym_localidades");
          }
        });
      }
    },    

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.id_perfil_default = (typeof options != undefined) ? options.id_perfil_default : PERFIL;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        edicion: edicion, 
        id:this.model.id,
        cambiar_password: (ID_USUARIO == this.model.id),
      };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      $(this.el).find("#usuario_horario_desde").mask("99:99");
      $(this.el).find("#usuario_horario_hasta").mask("99:99");

      $(this.el).find("#usuario_horario_entrega_desde").mask("99:99");
      $(this.el).find("#usuario_horario_entrega_hasta").mask("99:99");

      $(this.el).find("#usuarios_sucursales").select2({
        tags: true,
      });

      this.stopListening();
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      $(this.el).find("#images_tabla").sortable();

      if (TOQUE == 1 || ID_EMPRESA == 1245 || ID_EMPRESA == 1319 || ID_EMPRESA == 1284) {
        var toque_categorias = new Array();
        _.each(this.model.get("toque_categorias"),function(elem){
          toque_categorias.push(elem.id);
        });
        
        new app.mixins.Select({
          modelClass: app.models.ToqueCategoria,
          url: "toque/function/get_categorias/?id_empresa="+ID_EMPRESA,
          render: "#usuario_toque_categorias",
          selected: toque_categorias,
          multiple: true,
          onComplete:function(c) {
            $("#usuario_toque_categorias").chosen({
              "placeholder_text_multiple":"Seleccione",
            });
          }
        });
      }

      // PSICOWEB
      if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) {

        if (this.$("#usuario_obras_sociales").length > 0) {
          var obras_sociales = new Array();
          _.each(this.model.get("obras_sociales"),function(elem){
            obras_sociales.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.ObraSocial,
            url: "obras_sociales/",
            render: "#usuario_obras_sociales",
            selected: obras_sociales,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_obras_sociales").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_especialidades").length > 0) {
          var especialidades = new Array();
          _.each(this.model.get("especialidades"),function(elem){
            especialidades.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.Especialidad,
            url: "especialidades/",
            render: "#usuario_especialidades",
            selected: especialidades,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_especialidades").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_tipos_pacientes").length > 0) {
          var tipos_pacientes = new Array();
          _.each(this.model.get("tipos_pacientes"),function(elem){
            tipos_pacientes.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.TipoPaciente,
            url: "tipos_pacientes/",
            render: "#usuario_tipos_pacientes",
            selected: tipos_pacientes,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_tipos_pacientes").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_tipos_atenciones").length > 0) {
          var tipos_atenciones = new Array();
          _.each(this.model.get("tipos_atenciones"),function(elem){
            tipos_atenciones.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.TipoAtencion,
            url: "tipos_atenciones/",
            render: "#usuario_tipos_atenciones",
            selected: tipos_atenciones,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_tipos_atenciones").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_tipos_terapias").length > 0) {
          var tipos_terapias = new Array();
          _.each(this.model.get("tipos_terapias"),function(elem){
            tipos_terapias.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.TipoTerapia,
            url: "tipos_terapias/",
            render: "#usuario_tipos_terapias",
            selected: tipos_terapias,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_tipos_terapias").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_titulos").length > 0) {
          var titulos = new Array();
          _.each(this.model.get("titulos"),function(elem){
            titulos.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.Titulo,
            url: "titulos/",
            render: "#usuario_titulos",
            selected: titulos,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_titulos").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        if (this.$("#usuario_formas_pago").length > 0) {
          var formas_pago = new Array();
          _.each(this.model.get("formas_pago"),function(elem){
            formas_pago.push(elem.id);
          });
          new app.mixins.Select({
            modelClass: app.models.FormaPago,
            url: "formas_pago/",
            render: "#usuario_formas_pago",
            selected: formas_pago,
            multiple: true,
            onComplete:function(c) {
              $("#usuario_formas_pago").chosen({
                "placeholder_text_multiple":"Seleccione",
              });
            }
          });
        }

        self.direcciones = new app.collections.TurnosServicios();
        var dep = this.model.get("direcciones");
        for(var i=0;i<dep.length;i++) {
          var dd = dep[i];
          var ddo = new app.models.TurnoServicio(dd);
          self.direcciones.add(ddo);
        }
        this.direccionesTable = new app.views.TurnosServiciosTableView({
          id_usuario: ((self.model.id == undefined) ? -1 : self.model.id),
          collection: self.direcciones,
          lightbox: true,
        });
        this.$("#usuario_direcciones").html(this.direccionesTable.el);


      }
        
      if (edicion) {
        new app.mixins.Select({
          modelClass: app.models.Perfil,
          url: "perfiles/",
          render: "#usuarios_perfiles",
          name : "id_perfiles",
          firstOptions: ["<option value='0'>-</option>"],
          selected : self.model.get("id_perfiles"),
        });
      }

      // Si esta habilitado el modulo y existe el componente
      if (control.check("vendedores")>0 && this.$("#usuario_vendedores").length > 0) {
        new app.mixins.Select({
          modelClass: app.models.Vendedor,
          url: "vendedores/",
          render: "#usuario_vendedores",
          name : "id_vendedor",
          firstOptions: ["<option value='0'>-</option>"],
          selected: self.model.get("id_vendedor"),
        });
      }

      if (ID_EMPRESA == 1319) {
        this.localidad = null;
        this.cargar_localidades_eym();
        this.cargar_localidades();
      }
      $('[data-toggle="tooltip"]').tooltip();

    },


    // ============================
    // UTILIZADOS EN ENTRENA Y MAS

    cargar_localidades: function() {
      if (ID_EMPRESA != 1319) return;
      var self = this;
      var id_departamento = this.$("#usuario_provincias").val();
      var id_localidad = ((this.localidad != null) ? $(this.localidad).data("id") : 0);
      new app.mixins.Select({
        modelClass: app.models.Localidad,
        url: "/sistema/localidades/function/get_by_departamento/"+id_departamento,
        render: "#usuario_localidades",
        selected: id_localidad,
        onComplete:function(c) {
          crear_select2("usuario_localidades");
          self.$("usuario_localidades").trigger("change");
        }
      });      
    },

    editar_localidad: function(e) {
      this.localidad = $(e.currentTarget).parents("tr");
      $("#usuario_provincias").val($(this.localidad).data("id_provincia"));
      $("#usuario_localidad_valor").val($(this.localidad).data("valor"));
      this.cargar_localidades();
    },

    agregar_localidad: function() {
        
      var id_localidad = this.$("#usuario_localidades").val();
      if (id_localidad == 0) {
        alert("Por favor seleccione un localidad");
        this.$("#usuario_localidades").focus();
        return;
      }
      var localidad = this.$("#usuario_localidades option:selected").text();
      var id_provincia = this.$("#usuario_provincias").val();
      var provincia = this.$("#usuario_provincias option:selected").text();
      var valor = 0;
      /*
      var valor = this.$("#usuario_localidad_valor").val();
      if (isEmpty(valor)) {
        alert("Por favor ingrese un valor.");
        this.$("#usuario_localidad_valor").focus();
        return;
      }
      valor = parseFloat(valor);
      if (isNaN(valor)) {
        alert("Por favor ingrese un valor correcto.");
        this.$("#usuario_localidad_valor").focus();
        return;
      }*/

      // Prevenimos que ingresen dos veces lo mismo
      if (this.localidad == null && this.$("#usuario_localidades_tabla tbody tr[data-id='"+id_localidad+"']").length > 0) return;

      var tr = "<tr data-valor='"+valor+"' data-id_provincia='"+id_provincia+"' data-id='"+id_localidad+"'>";
      tr+="<td>"+provincia+"</td>";
      tr+="<td>"+localidad+"</td>";
      tr+="<td class='dn'>"+valor+"</td>";
      tr+="<td><button class='btn btn-white editar_localidad'><i class='fa fa-pencil'></i></button></td>";
      tr+="<td><button class='btn btn-white eliminar_localidad'><i class='fa fa-trash'></i></button></td>";
      tr+="</tr>";

      if (this.localidad == null) {
        $("#usuario_localidades_tabla tbody").append(tr);
      } else {
        $(this.localidad).replaceWith(tr);
        this.localidad = null;
      }
      $("#usuario_localidad_valor").val("");
      $("#usuario_localidades").focus();
    },

    // ============================


    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length == 0) {
        this.$("#images_container").removeClass('tiene');
      } else {
        this.$("#images_container").addClass('tiene');
        for(var i=0;i<images.length;i++) {
          var path = images[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
        }
      }
    }, 

    agregar_horario: function() {
      // Controlamos los valores
      var desde = $("#usuario_horario_desde").val();
      if (isEmpty(desde)) {
        alert("Por favor ingrese una fecha");
        $("#usuario_horario_desde").focus();
        return;
      }
      var hasta = $("#usuario_horario_hasta").val();
      if (isEmpty(hasta)) {
        alert("Por favor ingrese una fecha");
        $("#usuario_horario_hasta").focus();
        return;
      }
      var dia = $("#usuario_horario_dia").val();
      var nombre_dia = $("#usuario_horario_dia option:selected").text();
      var tr = "<tr>";
      tr+="<td class='dn dia'>"+dia+"</td>";
      tr+="<td class='editar_horario'><span class='text-info'>"+nombre_dia+"</td>";
      tr+="<td class='desde editar_horario'>"+desde+"</td>";
      tr+="<td class='hasta editar_horario'>"+hasta+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_horario'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_horario == null) {
        $("#usuario_horarios_tabla tbody").append(tr);
      } else {
        $(this.item_horario).replaceWith(tr);
        this.item_horario = null;
      }
      $("#usuario_horario_desde").val("");
      $("#usuario_horario_hasta").val("");
      $("#usuario_horario_dia").focus();
    },
    
    editar_horario: function(e) {
      this.item_horario = $(e.currentTarget).parents("tr");
      $("#usuario_horario_dia").val($(this.item_horario).find(".dia").text());
      $("#usuario_horario_desde").val($(this.item_horario).find(".desde").text());
      $("#usuario_horario_hasta").val($(this.item_horario).find(".hasta").text());
    },

    agregar_horario_entrega: function() {
      // Controlamos los valores
      var desde = $("#usuario_horario_entrega_desde").val();
      if (isEmpty(desde)) {
        alert("Por favor ingrese una fecha");
        $("#usuario_horario_entrega_desde").focus();
        return;
      }
      var hasta = $("#usuario_horario_entrega_hasta").val();
      if (isEmpty(hasta)) {
        alert("Por favor ingrese una fecha");
        $("#usuario_horario_entrega_hasta").focus();
        return;
      }
      var dia = $("#usuario_horario_entrega_dia").val();
      var nombre_dia = $("#usuario_horario_entrega_dia option:selected").text();
      var tr = "<tr>";
      tr+="<td class='dn dia'>"+dia+"</td>";
      tr+="<td class='editar_horario_entrega'><span class='text-info'>"+nombre_dia+"</td>";
      tr+="<td class='desde editar_horario_entrega'>"+desde+"</td>";
      tr+="<td class='hasta editar_horario_entrega'>"+hasta+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_horario_entrega'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_horario_entrega == null) {
        $("#usuario_horario_entregas_tabla tbody").append(tr);
      } else {
        $(this.item_horario_entrega).replaceWith(tr);
        this.item_horario_entrega = null;
      }
      $("#usuario_horario_entrega_desde").val("");
      $("#usuario_horario_entrega_hasta").val("");
      $("#usuario_horario_entrega_dia").focus();
    },
    
    editar_horario_entrega: function(e) {
      this.item_horario_entrega = $(e.currentTarget).parents("tr");
      $("#usuario_horario_entrega_dia").val($(this.item_horario_entrega).find(".dia").text());
      $("#usuario_horario_entrega_desde").val($(this.item_horario_entrega).find(".desde").text());
      $("#usuario_horario_entrega_hasta").val($(this.item_horario_entrega).find(".hasta").text());
    },

    validar: function() {
      var self = this;
      try {
        validate_input("usuarios_email",IS_EMAIL,"Por favor, ingrese un email.");

        if (this.$("#usuario_horarios_tabla").length > 0) {
          var k = 0;
          var horarios = new Array();
          $("#usuario_horarios_tabla tbody tr").each(function(i,e){
            horarios.push({
              "dia": $(e).find(".dia").text(),
              "desde": $(e).find(".desde").text(),
              "hasta": $(e).find(".hasta").text(),
            });
            k++;
          });
          this.model.set({"horarios":horarios});
        }

        if (this.$("#usuario_horario_entregas_tabla").length > 0) {
          var k = 0;
          var horarios_entrega = new Array();
          $("#usuario_horario_entregas_tabla tbody tr").each(function(i,e){
            horarios_entrega.push({
              "dia": $(e).find(".dia").text(),
              "desde": $(e).find(".desde").text(),
              "hasta": $(e).find(".hasta").text(),
            });
            k++;
          });
          this.model.set({"horarios_entrega":horarios_entrega});
        }

        // Listado de Imagenes
        if (this.$("#images_tabla").length > 0) {
          var images = new Array();
          $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
            images.push($(e).text());
          });
          self.model.set({"images":images});
        }

        if (this.$("#usuario_toque_categorias").length > 0) {
          var toque_categorias = $("#usuario_toque_categorias").val();
          if (toque_categorias == null) toque_categorias = new Array();
          this.model.set({"toque_categorias":toque_categorias});
        }

        if (ID_EMPRESA == 1245 || ID_EMPRESA == 1319) {

          if (this.$("#usuario_obras_sociales").length > 0) {
            var obras_sociales = $("#usuario_obras_sociales").val();
            if (obras_sociales == null) obras_sociales = new Array();
            this.model.set({"obras_sociales":obras_sociales});
          } else {
            this.model.set({"obras_sociales":[]});
          }

          if (this.$("#usuario_especialidades").length > 0) {
            var especialidades = $("#usuario_especialidades").val();
            if (especialidades == null) especialidades = new Array();
            this.model.set({"especialidades":especialidades});
          } else {
            this.model.set({"especialidades":[]});
          }

          if (this.$("#usuario_tipos_pacientes").length > 0) {
            var tipos_pacientes = $("#usuario_tipos_pacientes").val();
            if (tipos_pacientes == null) tipos_pacientes = new Array();
            this.model.set({"tipos_pacientes":tipos_pacientes});
          } else {
            this.model.set({"tipos_pacientes":[]});
          }

          if (this.$("#usuario_tipos_atenciones").length > 0) {
            var tipos_atenciones = $("#usuario_tipos_atenciones").val();
            if (tipos_atenciones == null) tipos_atenciones = new Array();
            this.model.set({"tipos_atenciones":tipos_atenciones});
          } else {
            this.model.set({"tipos_atenciones":[]});
          }

          if (this.$("#usuario_tipos_terapias").length > 0) {
            var tipos_terapias = $("#usuario_tipos_terapias").val();
            if (tipos_terapias == null) tipos_terapias = new Array();
            this.model.set({"tipos_terapias":tipos_terapias});
          } else {
            this.model.set({"tipos_terapias":[]});
          }

          if (this.$("#usuario_titulos").length > 0) {
            var titulos = $("#usuario_titulos").val();
            if (titulos == null) titulos = new Array();
            this.model.set({"titulos":titulos});
          } else {
            this.model.set({"titulos":[]});
          }

          if (this.$("#usuario_formas_pago").length > 0) {
            var formas_pago = $("#usuario_formas_pago").val();
            if (formas_pago == null) formas_pago = new Array();
            this.model.set({"formas_pago":formas_pago});
          } else {
            this.model.set({"formas_pago":[]});
          }

          // Listado de direcciones
          var direcciones = new Array();
          self.direcciones.each(function(dpto){
            direcciones.push(dpto.toJSON());
          });
          self.model.set({"direcciones":direcciones});          
        }

        if (this.$("#usuarios_password").length > 0) {
          if (this.model.id == null) {
            validate_input("usuarios_password",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
            validate_input("usuarios_password_2",IS_EMPTY,"Por favor, ingrese una clave para el usuario.");
          }
          var password_1 = $("#usuarios_password").val();
          var password_2 = $("#usuarios_password_2").val();
          if (password_1 != password_2) {
            show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
            $("#usuarios_password_2").focus();
            return false;
          }
          if (!isEmpty(password_1)) {
            password_1 = hex_md5(password_1);
            this.model.set({
              "password":password_1
            });                    
          }          
        }

        if (this.$("#usuarios_sucursales").length > 0) {
          var c = $("#usuarios_sucursales").select2("val");
          if (c != null && c.length > 0) {
            var id_sucursal = c[0];
            this.model.set({
              "id_sucursal":id_sucursal,
              "sucursales":c,
            });
          } else {
            this.model.set({
              "id_sucursal":0,
              "sucursales":[],
            });            
          }
        }

        if (this.$("#usuario_vendedores").length > 0) {
          this.model.set({
            "id_vendedor":this.$("#usuario_vendedores").val(),
          });
        }

        this.model.set({
          "archivo":(this.$("#hidden_archivo").length > 0) ? $("#hidden_archivo").val() : "",
          "path":(this.$("#hidden_path").length > 0) ? $("#hidden_path").val() : "",
        });

        this.model.set({
          "id_perfiles": self.$("#profesional_id_perfiles").val(),
        });

        if (this.$("#hidden_path_2").length > 0) {
          this.model.set({
            "path_2":(this.$("#hidden_path_2").length > 0) ? $("#hidden_path_2").val() : "",
          });
        }

        if (this.$("#usuario_localidades_tabla").length > 0) {
          var valor_maximo = 0;
          var localidades = new Array();
          this.$("#usuario_localidades_tabla tbody tr").each(function(i,e){
            var valor = $(e).find("td:eq(2)").text();
            valor = parseFloat(valor);
            if (valor > valor_maximo) valor_maximo = valor;
            localidades.push({
              "id_localidad":$(e).data("id"),
              "id_provincia":$(e).data("id_provincia"),
              "provincia":$(e).find("td:eq(0)").text(),
              "localidad":$(e).find("td:eq(1)").text(),
              "valor":valor,
            });
          });
          var maximo = parseFloat(self.$("#usuario_maximo").val());
          if (isNaN(maximo)) {
            alert("El valor de tope maximo es incorrecto.");
            self.$("#usuario_maximo").focus();
            return false;
          }
          if (valor_maximo > maximo) {
            alert("El precio por contacto supera al tope maximo. Por favor revise los valores.");
            return false;
          }          
          this.model.set({
            "localidades":localidades,
          })
        }

        // ENTRENA Y MAS
        if (this.$("#usuario_eym_localidades").length > 0) {
          this.model.set({
            "id_localidad":self.$("#usuario_eym_localidades").val(),
            "localidad":self.$("#usuario_eym_localidades option:selected").text(),
            "id_provincia":self.$("#usuario_eym_provincias").val(),
            "provincia":self.$("#usuario_eym_provincias option:selected").text(),
          });
        }

        this.model.set({
          "sesion_gratis":(self.$("#usuario_sesion_gratis").is(":checked") ? 1 : 0),
        });

        if (this.model.get("id_perfiles") == 0) {
          alert("Por favor seleccione un perfil de usuario.");
          return false;
        }

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },
        
    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.set({
          "language": ((self.$("#usuario_language").length > 0) ? self.$("#usuario_language").val() : 0),
        });
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              alert(response.mensaje);
            } else {
              location.reload();
            }
          },
        });
      }
    },
    
  });

})(app.views, app.models);







(function ( models ) {

  models.ToqueCategoria = Backbone.Model.extend({
    urlRoot: "toque_categorias/",
    defaults: {
      nombre: "",
      path: "",
      path_2: "",
      link: "",
      orden: 0,
      subtitulo: "",
    }
  });

})( app.models );