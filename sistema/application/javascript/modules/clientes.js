// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Cliente = Backbone.Model.extend({
    urlRoot: "clientes/",
    defaults: {
      nombre: "",
      codigo: "",
      canal: "",
      activo: 1,
      lista: 0,
      id_tipo_documento:99, // Sin identificacion por defecto
      cuit: "",
      direccion: "",
      email: "",
      id_sucursal: ((typeof ID_SUCURSAL != undefined) ? ID_SUCURSAL : 0),
      id_localidad: ((ID_EMPRESA == 70) ? 192 : 0),
      localidad: "",
      id_tipo_iva: 4,
      percibe_ib: 0,
      percepcion_ib: 0,
      descuento: 0,
      id_vendedor: 0,
      id_plan: 0,
      observaciones: "",
      telefono: "",
      fax: "549", // Se usa como codigo de pais
      celular: "",
      forma_pago: ((ID_EMPRESA==46 || ID_EMPRESA == 1354)?"C":"E"),
      contacto_nombre: "",
      contacto_email: "",
      contacto_telefono: "",
      contacto_celular: "",
      saldo_inicial: 0,
      saldo_inicial_2: 0,
      fecha_inicial: "",
      etiquetas: [],
      codigo_postal: "",

      fecha_vencimiento: "",
      no_leido: 0, // Marcador cuando hay una nueva consulta o evento
      custom_1: "",
      custom_2: "",
      custom_3: "", // INMOVAR: Consulta normal = 1 | TOQUE = PISO
      custom_4: "", // INMOVAR: Inquilino = 1 | TOQUE = DEPTO
      custom_5: "", // INMOVAR: Propietario = 1 | TOQUE lo usa como ID_USUARIO
      path: "",

      // DEPRECATED: ESTO QUEDO SIN USO PORQUE UN CLIENTE PUEDE SER UN INQUILINO Y CONSULTA A LA VEZ
      // PARA ESO USAMOS LOS CUSTOM
      // TIPO DE CLIENTE
      // 0 = CLIENTE NORMAL
      // 1 = CONTACTO
      // 2 = INQUILINO
      // 4 = PROPIETARIOS
      tipo: 0, 

      facebook: "",
      twitter: "",
      instagram: "",
      youtube: "",
      linkedin: "",
      latitud: 0,
      longitud: 0,
      zoom: 14,
      path_2: "",
      horario: (ID_EMPRESA == 70) ? "Lunes a Viernes. 8 a 12 hs y 16 a 20 hs.\nSabado: 9 a 12:30 hs." : "",
      nombre_fantasia: "",

      fecha_ult_operacion: "",
      id_origen: 0,
      id_usuario_asignado: 0,
      id_consulta: 0,
      propiedad_nombre: "",
      propiedad_id_tipo_operacion: 0,
      propiedad_tipo_operacion: "",
    }
  });

})( app.models );


(function ( models ) {
  models.ClienteEtiqueta = Backbone.Model.extend({
    urlRoot: "clientes_etiquetas/",
    defaults: {
      nombre: "",
    }
  });    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Clientes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "clientes/"
    },

    paginator_ui: {
      perPage: 20,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.Cliente, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ClienteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#clientes_item').html()),
    myEvents: {
      "click .data":"seleccionar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .enviar_whatsapp":function(){
        var mensaje = "Hola "+this.model.get("nombre")+"\n";
        var tel = this.model.get("fax")+""+this.model.get("telefono");
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel+"?text="+encodeURIComponent(mensaje);
        window.open(link_ws,"_blank");
      },      

      "click .canasta_basica":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var custom_5 = this.model.get("custom_5");
        custom_5 = (custom_5 == 1)?0:1;
        self.model.set({"custom_5":custom_5});
        $.ajax({
          "timeout":0,
          "url":"clientes/function/ajuste_masivo_canasta_basica/",
          "dataType":"json",
          "type":"post",
          "data":{
            "ids":self.model.id,
            "estado":custom_5,
          },
          "success":function() {
            self.render();
          },
        });        
        return false;
      },      

      "click .activar_laboral_gym":function(e) {
        var self = this;
        var activo = $(e.currentTarget).data("activo");
        $.ajax({
          "url":"laboral_gym/function/notificar_activo/",
          "data":{
            "id_cliente":self.model.id,
            "activo":activo,
          },
          "type":"post",
          "dataType":"json",
          "success":function() {
            location.reload();
          }
        })
      },

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
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_cliente_seleccionado = this.model.get("codigo");
        window.cliente_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        if (this.vista_contactos) {
          if (ID_PROYECTO == 3) {
            location.href="app/#contacto_acciones/"+this.model.id;    
          } else {
            location.href="app/#cliente_acciones/"+this.model.id;    
          }
        } else {
          location.href="app/#cliente/"+this.model.id;  
        }
        /*
        if (ID_EMPRESA == 70 || ID_PROYECTO == 1 && ((typeof FACTURACION_USA_CREDITOS_PERSONALES == "undefined") || FACTURACION_USA_CREDITOS_PERSONALES == 0)) {
          location.href="app/#cliente/"+this.model.id;  
        } else if (MILLING == 1) {
          if (this.vista_contactos) {
            location.href="app/#cliente_acciones/"+this.model.id;  
          } else {
            location.href="app/#cliente/"+this.model.id;  
          }
        } else {
          location.href="app/#cliente_acciones/"+this.model.id;
        }
        */
      }
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.modulo = this.options.modulo;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.vista_contactos = (this.options.vista_contactos == undefined || this.options.vista_contactos == false) ? false : true;
      this.parent = (this.options.parent == undefined) ? this.options.parent : null;
      _.bindAll(this);
    },
    render: function() {
      var self = this;
      var obj = this.model.toJSON();
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      obj.vista_contactos = this.vista_contactos;
      obj.modulo = this.modulo;
      $(this.el).html(this.template(obj));

      if (obj.no_leido == 1) $(this.el).addClass("no_leido");

      // Si tiene calificacion
      if (obj.vista_contactos == 1 && (this.$(".calification-container").length>0)) {
        var calif = new app.views.RatingView({
          stars: 5,
          value: self.model.get("zoom"),
          selected: function(value) {
            self.model.set({
              "zoom":value
            });
            self.change_property({
              "table":"clientes",
              "attribute":"zoom",
              "value":value,
              "id":self.model.id,
              "success":function(){
                self.render();
              }
            });
          },
        });
        this.$(".calification-container").append(calif.el);
      }
      $('[data-toggle="tooltip"]').tooltip();

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

  app.views.ClientesTableView = app.mixins.View.extend({

    template: _.template($("#clientes_panel_template").html()),

    myEvents: {
      "click .nuevo_propietario":function() {
        location.href = "app/#propietario";
      },
      "click .nuevo_cliente": "nuevo_cliente",
      "click .nuevo_inquilino": "nuevo_inquilino",      
      "keydown #clientes_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "keydown #clientes_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#clientes_table tbody tr .radio:first").focus(); }
      },
      "keypress #clientes_codigo_propiedad":function(e) {
        if (e.which == 13) { this.buscar(); }
      },

      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #clientes_buscar":"buscar",
      "click .buscar":"buscar",
      "click .cambiar_tab":function(e) {
        var self = this;
        var tipo = $(e.currentTarget).data("tipo");
        this.cambio_parametros = true;

        // En caso de INMOVAR
        if (ID_PROYECTO == 3) {
          window.clientes_custom_3 = "";
          window.clientes_custom_4 = "";
          window.clientes_custom_5 = "";
          if (typeof $(e.currentTarget).data("custom_3") != "undefined") window.clientes_custom_3 = $(e.currentTarget).data("custom_3");
          if (typeof $(e.currentTarget).data("custom_4") != "undefined") window.clientes_custom_4 = $(e.currentTarget).data("custom_4");
          if (typeof $(e.currentTarget).data("custom_5") != "undefined") window.clientes_custom_5 = $(e.currentTarget).data("custom_5");

          // Dependiendo del custom_5 mostramos u ocultamos el boton de nuevo propietario
          if (window.clientes_custom_5 == 1) {
            self.$(".nuevo_cliente").hide();
            self.$(".nuevo_inquilino").hide();
            self.$(".nuevo_propietario").show();
          } else if (window.clientes_custom_4 == 1) {
            // Nuevo inquilino
            self.$(".nuevo_cliente").hide();
            self.$(".nuevo_propietario").hide();
            self.$(".nuevo_inquilino").show();
          } else {
            // Nueva consulta
            self.$(".nuevo_propietario").hide();
            self.$(".nuevo_inquilino").hide();
            self.$(".nuevo_cliente").show();
          }
        }

        window.clientes_tipo = tipo;
        this.buscar();
      },
      // METODOS ESPECIALES
      "click .importar_cuentas":"importar_cuentas",
      "click .importar_mora":"importar_mora",
      "click .modificar_cuentas":"modificar_cuentas",
    },

    nuevo_cliente: function() {
      if (this.vista_contactos) {
        var self = this;
        if (ID_PROYECTO == 3) {
          var view = new app.views.ContactoEditView({
            model: new app.models.Cliente({
              "tipo":2,
              "custom_3":1,
            }),
            view: self,
          });            
        } else {
          var view = new app.views.ConsultaEditView({
            model: new app.models.Consulta(),
            view: self,
          });
        }
        crearLightboxHTML({
          "html":view.el,
          "width":550,
          "height":140,
          "escapable":false,
        });
      } else {
        window.location = "app/#cliente";  
      }
    },

    nuevo_inquilino: function() {
      if (this.vista_contactos) {
        var self = this;
        if (ID_PROYECTO == 3) {
          var view = new app.views.ContactoEditView({
            model: new app.models.Cliente({
              "tipo":2,
              "custom_4":1,
            }),
            view: self,
          });            
        } else {
          var view = new app.views.ConsultaEditView({
            model: new app.models.Consulta(),
            view: self,
          });
        }
        crearLightboxHTML({
          "html":view.el,
          "width":550,
          "height":140,
          "escapable":false,
        });
      } else {
        window.location = "app/#cliente";  
      }
    },    

    initialize : function (options) {
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.vista_contactos = (this.options.vista_contactos == undefined || this.options.vista_contactos == false) ? false : true;
      this.permiso = this.options.permiso;
      this.modulo = this.options.modulo;
      window.clientes_custom_3 = (typeof window.clientes_custom_3 != "undefined") ? window.clientes_custom_3 : "";
      window.clientes_custom_4 = (typeof window.clientes_custom_4 != "undefined") ? window.clientes_custom_4 : "";
      window.clientes_custom_5 = (typeof window.clientes_custom_5 != "undefined") ? window.clientes_custom_5 : "";
      window.clientes_filter = (typeof window.clientes_filter != "undefined") ? window.clientes_filter : "";
      window.clientes_codigo_propiedad = (typeof window.clientes_codigo_propiedad != "undefined") ? window.clientes_codigo_propiedad : "";
      window.clientes_page = (typeof window.clientes_page != "undefined") ? window.clientes_page : 1;
      window.clientes_tipo = (typeof window.clientes_tipo != "undefined") ? window.clientes_tipo : -1;
      window.clientes_id_vendedor = (typeof window.clientes_id_vendedor != "undefined") ? window.clientes_id_vendedor : 0;
      window.clientes_id_etiqueta = (typeof window.clientes_id_etiqueta != "undefined") ? window.clientes_id_etiqueta : 0;
      if (ID_VENDEDOR != 0 && ID_EMPRESA != 287) window.clientes_id_vendedor = ID_VENDEDOR;

      // Si es una INMOBILIARIA, por defecto mostramos los CONTACTOS primero
      if (ID_PROYECTO == 3 && window.clientes_custom_3 == "" && window.clientes_custom_4 == "" && window.clientes_custom_5 == "") window.clientes_custom_3 = "1";

      this.cambio_parametros = false;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    buscar: function() {
      
      if (window.clientes_filter != this.$("#clientes_buscar").val().trim()) {
        window.clientes_filter = this.$("#clientes_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (this.$("#clientes_codigo_propiedad").length > 0) {
        if (window.clientes_codigo_propiedad != this.$("#clientes_codigo_propiedad").val().trim()) {
          window.clientes_codigo_propiedad = this.$("#clientes_codigo_propiedad").val().trim();
          this.cambio_parametros = true;
        }
      }
      if (this.$("#clientes_vendedores").length > 0) {
        if (window.clientes_id_vendedor != this.$("#clientes_vendedores").val()) {
          window.clientes_id_vendedor = this.$("#clientes_vendedores").val();
          this.cambio_parametros = true;
        }
      }
      if (this.$("#clientes_etiquetas").length > 0) {
        if (window.clientes_id_etiqueta != this.$("#clientes_etiquetas").val()) {
          window.clientes_id_etiqueta = this.$("#clientes_etiquetas").val();
          this.cambio_parametros = true;
        }
      }
      if (this.cambio_parametros) {
        window.clientes_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "term":encodeURIComponent(window.clientes_filter),
        "codigo_propiedad":encodeURIComponent(window.clientes_codigo_propiedad),
        "id_vendedor":window.clientes_id_vendedor,
        "id_etiqueta":window.clientes_id_etiqueta,
        "tipo":window.clientes_tipo,
        "custom_3":window.clientes_custom_3,
        "custom_4":window.clientes_custom_4,
        "custom_5":window.clientes_custom_5,
        "id_proyecto":ID_PROYECTO,
      };

      if (typeof SOLO_USUARIO != "undefined" && SOLO_USUARIO == 1 && ID_EMPRESA != 224) datos.id_usuario = ID_USUARIO;

      // Si es TOQUE y PERFIL Comercio
      if (ID_EMPRESA == 571 && PERFIL == 661) {
        datos.custom_5 = ID_USUARIO;
      }

      // Si estamos viendo los contactos, el orden por defecto es por fecha_ult_operacion
      if (this.vista_contactos) {
        if (ID_EMPRESA == 220) {
          this.collection.paginator_ui.order_by = "C.fecha_vencimiento";
        } else {
          this.collection.paginator_ui.order_by = "C.fecha_ult_operacion";
        }
        this.collection.paginator_ui.order = "desc";
        datos.buscar_respuesta = 1;
      }

      this.collection.server_api = datos;
      this.collection.goTo(window.clientes_page);
    },

    render: function() {
      var self = this;
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection,
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
        "vista_contactos":this.vista_contactos,
        "modulo":this.modulo,
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.ClienteEtiqueta,
        url: "clientes_etiquetas/",
        firstOptions: ["<option value='0'>Etiqueta</option>"],
        render: "#clientes_etiquetas",
        onComplete:function(c) {
          crear_select2("clientes_etiquetas");
        }                    
      });

    },

    exportar: function(obj) {
      var url = this.collection.url+"function/exportar/?order="+encodeURI("C.fecha_ult_operacion DESC");
      url+="&tipo="+window.clientes_tipo;
      url+="&custom_3="+window.clientes_custom_3;
      url+="&custom_4="+window.clientes_custom_4;
      url+="&custom_5="+window.clientes_custom_5;
      if (!isEmpty(this.$("#clientes_buscar").val())) url+="&filter="+encodeURI(this.$("#clientes_buscar").val());
      if (this.$("#clientes_codigo_propiedad").length > 0) url+="&codigo_propiedad="+encodeURI(this.$("#clientes_codigo_propiedad").val());
      if (ID_PROYECTO == 3) url+="&id_proyecto="+ID_PROYECTO;
      if (PERFIL == 1357) url+="&id_profesional="+ID_USUARIO;
      window.open(url,"_blank");
    },

    importar: function() {
      app.views.importar = new app.views.Importar({
        "url":"clientes/function/importar_excel/",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },        

    exportar_csv: function(obj) {
      window.open("clientes/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"clientes",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.clientes_page = this.pagination.getPage();
      this.$("#clientes_table tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      //if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        //if (this.collection.length > 0 || window.clientes_tipo != -1 || ID_PROYECTO == 3) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        /*} else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }*/
      //}
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ClienteItem({
        model: item,
        permiso: this.permiso,
        habilitar_seleccion: this.habilitar_seleccion, 
        vista_contactos: this.vista_contactos, 
        modulo: this.modulo,
        parent: this,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

    // Metodos especiales de ARECOCARD
    importar_cuentas : function() {
      app.views.importar = new app.views.Importar({
        "table":"clientes",
        "url":"clientes/function/importar_arecocard/"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },
    importar_mora: function() {
      app.views.importar = new app.views.Importar({
        "table":"clientes",
        "url":"clientes/function/importar_mora_arecocard/",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },
    modificar_cuentas : function() {
      app.views.importar = new app.views.Importar({
        "table":"clientes",
        "url":"clientes/function/modificar_archivo_cuentas/",
        "titulo":"Modificar archivo de cuentas",
        "texto":"Seleccione el archivo de cuentas",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.ClienteEditView = app.mixins.View.extend({

    template: _.template($("#clientes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": function() {
        this.guardar_directo = 1;
        this.guardar();
      },
      "click .nuevo": "limpiar",
      "change #clientes_tipo_documento":"enmascarar",
      "click #expand_principal":"expand_principal",
      "click #cargar_mapa":"get_coords_by_address",
      "click #expand_mapa":"expand_mapa",
      "click .add_marker": function() {
        this.add_marker(LATITUD,LONGITUD);
      },
      "click .generar_qr":function() {
        this.guardar_directo = 0;
        this.guardar();
      },
      "click .generar_short_link":function() {
        this.guardar_directo = 2;
        this.guardar();
      },
    },

    enmascarar: function() {
      var t = this.$("#clientes_tipo_documento").val();
      if (t == 80 || t == 86) { // CUIT / CUIL
        this.$("#clientes_cuit").mask("99-99999999-9");    
      } else {
        this.$("#clientes_cuit").unmask();
      }            
    },

    initialize: function(options) {
      this.options = options;
      this.vista_contactos = (this.options.vista_contactos == undefined || this.options.vista_contactos == false) ? false : true;
      this.modulo = this.options.modulo;
      this.model.bind("destroy",this.render,this);
      this.guardando = 0;
      this.expand_principal_key = 0;
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
        "id":self.model.id,
        "vista_contactos":self.vista_contactos,
        "modulo":self.modulo,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // AUTOCOMPLETE DE LOCALIDADES
      if (ID_EMPRESA != 1129) {
        $(this.el).find("#clientes_localidad").autocomplete({
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
      }

      if (this.$("#clientes_fecha_inicial").length > 0) {
        var fecha_inicial = this.model.get("fecha_inicial");
        if (isEmpty(fecha_inicial)) fecha_inicial = new Date();
        createdatepicker($(this.el).find("#clientes_fecha_inicial"),fecha_inicial);        
      }

      if (this.$("#clientes_fecha_ult_operacion").length > 0) {
        var fecha_ult_operacion = this.model.get("fecha_ult_operacion");
        createdatepicker($(this.el).find("#clientes_fecha_ult_operacion"),fecha_ult_operacion);
      }

      if (this.$("#clientes_fecha_vencimiento").length > 0) {
        var fecha_vencimiento = this.model.get("fecha_vencimiento");
        createtimepicker($(this.el).find("#clientes_fecha_vencimiento"),fecha_vencimiento);
      }        
            
      if (CONFIGURACION_AUTOGENERAR_CODIGOS == 1) {
        // Estamos creando un cliente nuevo
        if (this.model.id == undefined) {
          $.ajax({
            "url":"clientes/function/next/",
            "dataType":"json",
            "success":function(r) {
              $(self.el).find("#clientes_codigo").val(r.codigo);
            }
          });
        }                
      }

      this.enmascarar();

      if (ID_EMPRESA == 70) {
        this.expand_mapa_key = 0;
        this.cargar_etiquetas();
      }
      return this;
    },

    get_coords_by_address: function() {
      var self = this;
      if (self.map == undefined) return;
      var calle = $("#clientes_direccion").val();
      var localidad = $("#clientes_localidad").val();
      var pais = "Argentina";
      if (isEmpty(calle)) {
        alert("Por favor ingrese una calle");
        $("#clientes_direccion").focus();
        return;
      }
      if (isEmpty(localidad)) {
        alert("Por favor ingrese una localidad");
        $("#clientes_localidad").focus();
        return;
      }

      localidad = localidad.replace("(Bs. As.)",", Buenos Aires");
      localidad = localidad.replace("(CABA)",", Ciudad Autonoma de Buenos Aires");
      localidad = localidad.replace("(Cat.)",", Catamarca");
      localidad = localidad.replace("(Chaco)",", Chaco");
      localidad = localidad.replace("(Chu.)",", Chubut");
      localidad = localidad.replace("(Cba.)",", Cordoba");
      localidad = localidad.replace("(Corr.)",", Corrientes");
      localidad = localidad.replace("(E. Rios)",", Entre Rios");
      localidad = localidad.replace("(For.)",", Formosa");
      localidad = localidad.replace("(Jujuy)",", Jujuy");
      localidad = localidad.replace("(La Pampa)",", La Pampa");
      localidad = localidad.replace("(La Rioja)",", La Rioja");
      localidad = localidad.replace("(Mend.)",", Mendoza");
      localidad = localidad.replace("(Mis.)",", Misiones");
      localidad = localidad.replace("(Neuq.)",", Neuquen");
      localidad = localidad.replace("(Rio N.)",", Rio Negro");
      localidad = localidad.replace("(Salta)",", Salta");
      localidad = localidad.replace("(S. Juan)",", San Juan");
      localidad = localidad.replace("(S. Luis)",", San Luis");
      localidad = localidad.replace("(S. Cruz)",", Santa Cruz");
      localidad = localidad.replace("(Sta. Fe)",", Santa Fe");
      localidad = localidad.replace("(Sgo. Est.)",", Santiago del Estero");
      localidad = localidad.replace("(T. Fgo.)",", Tierra del Fuego");
      localidad = localidad.replace("(Tucuman)",", Tucuman");

      var address = calle + ", " + localidad + ((!isEmpty(pais)) ? ", "+pais : "");
      self.geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          var location = results[0].geometry.location;
          var latitud = location.lat();
          var longitud = location.lng();
          self.add_marker(latitud,longitud);
          self.map.setCenter(location);
          self.model.set({
            "latitud":latitud,
            "longitud":longitud,
          });
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    },

    render_map: function() {
      var self = this;
      var latitud = self.model.get("latitud");
      var longitud = self.model.get("longitud");
      var zoom = parseInt(self.model.get("zoom"));
      if (latitud == 0 && longitud == 0) {
        latitud = LATITUD;
        longitud = LONGITUD;
        zoom: 12;
      }
      if (zoom == 0) zoom = 12;
      
      self.coor = new google.maps.LatLng(latitud,longitud);
      var mapOptions = {
        zoom: zoom,
        center: self.coor
      }
      self.map = new google.maps.Map(self.$("#mapa")[0], mapOptions);
      self.geocoder = new google.maps.Geocoder();
      // Si tiene seteado coordenadas, ponemos el marcador
      if (self.model.get("latitud") != 0 && self.model.get("longitud") != 0) {
        self.add_marker(self.model.get("latitud"),self.model.get("longitud"));
      }
    },   

    add_marker: function(latitud,longitud) {
      var self = this;
      var coord = new google.maps.LatLng(latitud,longitud);    
      this.marker = new google.maps.Marker({
        position: coord,
        map: self.map,
        draggable:true,
        title:"Arrastralo a la direccion correcta"
      });
      google.maps.event.addListener(this.marker, "dblclick", function (e) { 
        if (confirm("Realmente desea eliminar el marcador?")) {
          self.marker.setMap(null);
        }
      });
    },

    expand_mapa: function() {

      var self = this;
      if (this.expand_mapa_key == 1) return;
      this.expand_mapa_key = 1;

      try {
        loadGoogleMaps('3',API_KEY_GOOGLE_MAPS).done(self.render_map);
      } catch(e) {
        console.log(e);
      }
      
      setTimeout(function(){
        if (self.map == undefined) self.render_map();
        google.maps.event.trigger(self.map, "resize");
        self.map.setCenter(self.coor);
      },1000);
    },

    validar: function() {
      var self = this;
      try {
        validate_input("clientes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Tenemos que validar el CUIT
        if (this.$("#clientes_tipo_documento").length > 0) {
          if (this.$("#clientes_tipo_documento").val() == 80) {
            var cuit = this.$("#clientes_cuit").val();
            if (!validateCuit(cuit)) {
              show("ERROR: El CUIT no es valido.");
              this.$("#clientes_cuit").focus();
              return false;
            }
          }
        }

        if (ID_EMPRESA == 70) {
          // Coordenadas
          if (self.marker != undefined) {
            var pos = self.marker.getPosition();
            var zoom = self.map.getZoom();
            this.model.set({
              "latitud":(isNaN(pos.lat())) ? 0 : pos.lat(),
              "longitud":(isNaN(pos.lng())) ? 0 : pos.lng(),
              "zoom":zoom,
            });
          }
          this.model.set({
            "custom_3":$("#cliente_custom_3").val(),
          });
        }

        // Si los custom llegan a ser fileuploaders, hay que setearlos en el modelo
        for(var i=1;i<=5;i++) {
          if ((self.$("#hidden_custom_"+i).length > 0)) {
            var cus = $(self.el).find("#hidden_custom_"+i).val();
            var key = "custom_"+i;
            var obj = {};
            obj[key] = cus;
            this.model.set(obj);
          }          
        }

        // Las etiquetas se tratan como array porque son entidades separadas
        if (this.expand_principal_key == 1) {
          if (self.$("#cliente_etiquetas").length > 0) {
            var c = self.$("#cliente_etiquetas").select2("val");
            this.model.set({ "etiquetas":((c==null)?[]:c) });
          }
        }
        
        if (this.$("#clientes_password").length > 0) {
          var password_1 = $("#clientes_password").val();
          var password_2 = $("#clientes_password_2").val();
          if (password_1 != password_2) {
            show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
            $("#clientes_password_2").focus();
            return false;
          }
          if (!isEmpty(password_1)) {
            password_1 = hex_md5(password_1);
            this.model.set({
              "password":password_1
            });                    
          }          
        }
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },


    // Cuando expandimos por primera vez el panel principal
    expand_principal: function() {
      var self = this;
      if (this.expand_principal_key == 1) return;
      this.expand_principal_key = 1;
      this.cargar_etiquetas();
    },

    cargar_etiquetas: function() {
      var self = this;
      if (self.$("#cliente_etiquetas").length > 0) { 
        self.$("#cliente_etiquetas").select2({
          tags: true,
          minimumInputLength: 3,
          ajax: {
            url: "clientes_etiquetas/function/get_by_nombre/",
            dataType: 'json',
            delay: 1000,
            data: function (params) {
              return {
                term: params.term,
                page: params.page
              };
            },
            processResults: function (data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              params.page = params.page || 1;
              return {
                results: data,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
        });
        this.select2_sortable(self.$("#cliente_etiquetas"));
      }
    },

    select2_sortable: function($select2){
      var ul = $select2.next('.select2-container').first('ul.select2-selection__rendered');
      ul.sortable({
        placeholder : 'ui-state-highlight',
        forcePlaceholderSize: true,
        items       : 'li:not(.select2-search__field)',
        tolerance   : 'pointer',
        stop: function() {
          $($(ul).find('.select2-selection__choice').get().reverse()).each(function() {
            var id = $(this).data('data').id;
            var option = $select2.find('option[value="' + id + '"]')[0];
            $select2.prepend(option);
          });
        }
      });
    },

    guardar: function() {
      var self = this;
      if (this.validar() && this.guardando == 0) {
        this.guardando = 1;
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.unset("provincia");
        this.model.unset("tipo_iva");
        
        // Las etiquetas se tratan como array porque son entidades separadas
        if (self.$("#cliente_etiquetas").length > 0) { 
          try {
            /*
            var etiquetas = self.$("#cliente_etiquetas").select2("val");
            if (etiquetas == null) etiquetas = new Array();
            */

            var etiquetas = new Array();
            self.$(".select2-selection--multiple .select2-selection__choice").each(function(i,e){
              etiquetas.push($(e).attr("title"));
            });
            this.model.set({
              "etiquetas":etiquetas,
            });

          } catch(e) {
            console.log(e);
          }
        }

        if (ID_EMPRESA == 571) {
          this.model.set({
            "custom_5":ID_USUARIO,
          });
        }

        // Tomamos el custom_2, la primera etiqueta
        if (ID_EMPRESA == 70) {
          var etiquetas = this.model.get("etiquetas");
          if (etiquetas.length > 0) {
            var et = etiquetas[0];
            this.model.set({
              "custom_2":et,
            });
          }
        }
        if (MILLING == 1 || ID_EMPRESA == 70) {
          self.model.set({
            "observaciones":CKEDITOR.instances['cliente_observaciones'].getData(),
          });
        }
        if (MILLING == 1) {
          self.model.set({
            "codigo_postal":self.$("#clientes_codigo_postal").val(),
          })
        }

        var fax = ((MILLING == 0) ? ((self.$("#cliente_codigos_paises").length > 0) ? (self.$("#cliente_codigos_paises").val()) : 549) : self.$("#clientes_fax").val());
        this.model.save({
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
          "path_2": ((self.$("#hidden_path_2").length > 0) ? self.$("#hidden_path_2").val() : ""),
          "codigo":((self.$("#clientes_codigo").length > 0) ? self.$("#clientes_codigo").val() : ""),
          "lista": ((MILLING == 1) ? ((self.$("#cliente_lista").is(":checked"))?1:0) : ((self.$("#clientes_lista").length > 0) ? self.$("#clientes_lista").val() : 0)),
          "fecha_inicial": ((self.$("#clientes_fecha_inicial").length > 0) ? self.$("#clientes_fecha_inicial").val() : ""),
          "forma_pago": ((self.$("#clientes_forma_pago").length > 0) ? self.$("#clientes_forma_pago").val() : "E"),
          "id_tipo_iva": ((self.$("#clientes_tipo_iva").length > 0) ? self.$("#clientes_tipo_iva").val() : 0),
          "id_tipo_documento": ((self.$("#clientes_tipo_documento").length > 0) ? self.$("#clientes_tipo_documento").val() : 80),
          "id_vendedor":(self.$("#clientes_vendedores").length > 0) ? (self.$("#clientes_vendedores").val()) : 0,
          "id_plan":(self.$("#clientes_planes").length > 0) ? (self.$("#clientes_planes").val()) : 0,
          "fax":fax,
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (self.guardar_directo == 1) window.history.back();
              else if (self.guardar_directo == 0 && MILLING) window.open("clientes/function/generar_qr/"+self.model.id,"_blank");
              else if (self.guardar_directo == 2 && MILLING) {
                var that = self;
                $.ajax({
                  "url":"clientes/function/generar_qr_link/"+that.model.id,
                  "dataType":"json",
                  "success":function(r) {
                    alert(r.link);
                  }
                });
              }
            }
            self.guardando = 0;
          },
          error: function() {
            show("Ocurrio un error al guardar el cliente.");
            self.guardando = 0;
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Cliente()
      this.render();
    },

  });

})(app.views, app.models);



(function ( views, models ) {

  views.ClienteEditViewMini = app.mixins.View.extend({

    template: _.template($("#clientes_edit_mini_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .ver_avanzadas":function() {
        $(".ver_avanzadas").parent().hide();
        this.$("#clientes_edit_mini_avanzadas").slideDown();
      },
      "click .cerrar": "cerrar",
      "keyup #clientes_mini_nombre":function() {
        // Tenemos enlazada la referencia, por lo que cada vez que escribimos algo, debemos cambiar el input original
        if (this.input != undefined) {
          $(this.input).val($(this.el).find("#clientes_mini_nombre").val());
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
      "change #clientes_mini_tipo_documento":"enmascarar",
    },

    enmascarar: function() {
      var t = this.$("#clientes_mini_tipo_documento").val();
      if (t == 80 || t == 86) { // CUIT / CUIL
        this.$("#clientes_mini_cuit").mask("99-99999999-9");    
      } else {
        this.$("#clientes_mini_cuit").unmask();
      }            
    },        

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.input = this.options.input;
      this.onSave = this.options.onSave;
      this.callback = this.options.callback;
      this.tipo_formulario = (typeof options.tipo_formulario != "undefined") ? options.tipo_formulario : "";
      this.guardando = 0;
      this.render();
    },

    render: function() {
      var self = this;
      var obj = this.model.toJSON();
      obj.tipo_formulario = this.tipo_formulario;
      $(this.el).html(this.template(obj));
      if (this.input != undefined) {
        // Seteamos lo que tiene el input de referencia
        $(this.el).find("#clientes_mini_nombre").val($(this.input).val().trim());
      }
      this.enmascarar();
      return this;
    },

    focus: function() {
      $(this.el).find("#clientes_mini_nombre").focus();
    },

    validar: function() {
      try {
        validate_input("clientes_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        //validate_input("clientes_mini_email",IS_EMAIL,"Por favor, ingrese un email.");
        //validate_input("clientes_mini_telefono",IS_EMPTY,"Por favor, ingrese un telefono.");
        //validate_input("clientes_mini_direccion",IS_EMPTY,"Por favor, ingrese una direccion.");
        //validate_input("clientes_mini_cuit",IS_EMPTY,"Por favor, ingrese el CUIT del cliente.");

        // Tenemos que validar el CUIT
        if (this.$("#clientes_mini_tipo_documento").length > 0) {
          if (this.$("#clientes_mini_tipo_documento").val() == 80) {
            var cuit = this.$("#clientes_mini_cuit").val();
            if (!validateCuit(cuit)) {
              show("ERROR: El CUIT no es valido.");
              this.$("#clientes_mini_cuit").focus();
              return false;
            }
          }
        }

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
        this.model.unset("provincia");
        this.model.unset("tipo_iva");
        this.model.save({
          "nombre":$("#clientes_mini_nombre").val(),
          "email":$("#clientes_mini_email").val(),
          "telefono":$("#clientes_mini_telefono").val(),
          "fax":"549",
          "cuit":$("#clientes_mini_cuit").val(),
          "id_tipo_documento":$("#clientes_mini_tipo_documento").val(),
          "id_tipo_iva":$("#clientes_mini_tipo_iva").val(),
          "direccion":$("#clientes_mini_direccion").val(),
          "observaciones":((self.$("#clientes_mini_observaciones").length > 0) ? self.$("#clientes_mini_observaciones").val() : ""),
          "localidad":"",
        },{
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
            show("Ocurrio un error al guardar al cliente.");
            self.guardando = 0;
          }
        });
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },    

    limpiar : function() {
      this.model = new app.models.Cliente()
      this.render();
    },        

  });

})(app.views, app.models);




(function ( views, models ) {

  views.ClienteTimelineView = app.mixins.View.extend({

    template: _.template($("#clientes_timeline_panel_template").html()),

    myEvents: {
      "click .mostrar_consultas": function() {
        var self = this;
        if (confirm("¿Estas seguro que desea mostrar la consulta?")) {        
          $.ajax({
            "url": "/sistema/consultas/function/mostrar_siempre",
            "type": "post",
            "dataType": "json",
            "data": {
              "id_cliente": self.model.id,
              "id_empresa": ID_EMPRESA,
            }, success: function(r) {
              if (r.error == 0) {
                location.reload();
              }
            },
          });
        }
      },
      "click .guardar": "guardar",
      "click .expand-link":function(){}, // Se sobreescribe para no ejecutarla dos veces
      "click .editar_tipo":function(e) {
        var tipo = $(e.currentTarget).data("tipo");
        var self = this;
        self.model.set({"tipo":tipo});
        this.change_property({
          "table":"clientes",
          "attribute":"tipo",
          "value":tipo,
          "id":self.model.id,
          "success":function() {
            self.render();
          }
        });
      }
    },

    guardar_nota: function(value) {
      var self = this;
      self.model.set({"observaciones":value});
      this.change_property({
        "table":"clientes",
        "attribute":"observaciones",
        "value":value,
        "id":self.model.id,
      });      
    },

    marcar_leido:function() {
      var self = this;
      self.model.set({"no_leido":0});
      this.change_property({
        "table":"clientes",
        "attribute":"no_leido",
        "value":0,
        "id":self.model.id,
      });
      return false;
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.titulo_modulo = (typeof options.titulo_modulo != "undefined" ? options.titulo_modulo : "");
      this.clase_modulo = (typeof options.clase_modulo != "undefined" ? options.clase_modulo : "");
      this.tipo_cliente = (typeof options.tipo_cliente != "undefined" ? options.tipo_cliente : "CLIENTE");
      this.blur = (typeof options.blur != "undefined" ? options.blur : 0);
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
        "titulo_modulo":this.titulo_modulo,
        "clase_modulo":this.clase_modulo,
        "tipo_cliente":this.tipo_cliente,
        "blur":this.blur,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // Cuando entramos a la consulta, marcamos al cliente como leido
      this.marcar_leido();

      if (MILLING == 0) {
        var modelo = new app.models.Consulta({
          "id_contacto":self.model.id,
          "fecha":moment().format("DD/MM/YYYY"),
          "hora":moment().format("HH:mm:ss"),
        });
        modelo.on("remove",this.actualizar_consultas,this);
        this.editor = new app.views.CrearConsultaTimeline({
          "model":modelo,
          "view":self,
          "mostrar_tarea": (control.check("tareas")>0),
          "alerta_celular":(isEmpty(self.model.get("telefono"))),
          "alerta_email":(isEmpty(self.model.get("email"))),
          "nota": self.model.get("observaciones"),
          "blur": self.blur,
        });
        this.$("#cliente_crear_consultas").html(this.editor.el);        
      }
      this.render_consultas();

      var calif = new app.views.RatingView({
        stars: 5,
        value: self.model.get("zoom"),
        selected: function(value) {
          self.model.set({
            "zoom":value
          });
          self.change_property({
            "table":"clientes",
            "attribute":"zoom",
            "value":value,
            "id":self.model.id,
            "success":function(){
              self.render();
            }
          });
        },
      });
      this.$(".calification-container").append(calif.el);

      return this;
    },

    actualizar_consultas: function() {
      var self = this;
      $.ajax({
        "url":"clientes/function/get_consultas/",
        "data":{
          "id_cliente":self.model.id,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r){
          self.model.set({"consultas":r});
          // Limpiamos el editor con una consulta nueva
          var modelo = new app.models.Consulta({
            "id_contacto":self.model.id,
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
          "blur": self.blur,
        });
        this.$(".streamline").append(view.el);
      }
    },

    validar: function() {
      try {
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
        this.model.unset("provincia");
        this.model.unset("tipo_iva");

        this.model.save({
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (ID_PROYECTO == 3) {
                location.href="app/#contactos";
              } else {
                location.href="app/#clientes";  
              }
            }
            self.guardando = 0;
          },
          error: function() {
            show("Ocurrio un error al guardar el cliente.");
            self.guardando = 0;
          }
        });
      }
    },

  });

})(app.views, app.models);