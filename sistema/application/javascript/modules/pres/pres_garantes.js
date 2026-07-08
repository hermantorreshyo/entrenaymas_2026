// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PresGarante = Backbone.Model.extend({
    urlRoot: "pres_garantes/",
    defaults: {
      nombre: "",
      apellido: "",
      sexo: "M",
      activo: 1,
      id_tipo_documento:96,
      cuit: "",
      documento: "",
      direccion: "",
      email: "",
      id_localidad: 0,
      localidad: "",
      id_tipo_iva: 4,
      percibe_ib: 0,
      percepcion_ib: 0,
      descuento: 0,
      observaciones: "",
      telefono: "",
      telefono_2: "",
      telefono_3: "",
      telefono_4: "",
      telefono_5: "",
      telefono_6: "",
      telefono_obs: "",
      telefono_2_obs: "",
      telefono_3_obs: "",
      telefono_4_obs: "",
      telefono_5_obs: "",
      telefono_6_obs: "",
      saldo_inicial: 0,
      saldo_inicial_2: 0,
      fecha_inicial: "",
      codigo_postal: "",
      path: "",
      fecha_nac: "",
      fecha_ult_operacion: "",
      consultas: [],
      prestamos: [],
      estados_laborales: [],
      documentaciones: [],
      banco: "",
      cbu: "",
      tarjeta: "",
      emisor_tarjeta: "",
      nota: "",
      id_sucursal: ID_SUCURSAL,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.PresGarantes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "pres_garantes/"
    },

    paginator_ui: {
      perPage: 20,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.PresGarante, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PresGaranteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#pres_garantes_item').html()),
    myEvents: {
      "click .data":"seleccionar",
      "click .delete": "borrar",
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
          "table":"pres_garantes",
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
        window.cliente_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        if (!this.habilitar_seleccion) {
          location.href="app/#pres_cliente_acciones/"+this.model.id;  
        }
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
      var obj = this.model.toJSON();
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      $(this.el).html(this.template(obj));

      if (obj.no_leido == 1) $(this.el).addClass("no_leido");

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

  app.views.PresGarantesTableView = app.mixins.View.extend({

    template: _.template($("#pres_garantes_panel_template").html()),

    myEvents: {
      "keydown #pres_garantes_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "keydown #pres_garantes_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#pres_garantes_table tbody tr .radio:first").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #pres_garantes_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.pres_garantes_filter = (typeof window.pres_garantes_filter != "undefined") ? window.pres_garantes_filter : "";
      window.pres_garantes_page = (typeof window.pres_garantes_page != "undefined") ? window.pres_garantes_page : 1;
      this.cambio_parametros = false;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    buscar: function() {
      
      if (window.pres_garantes_filter != this.$("#pres_garantes_buscar").val().trim()) {
        window.pres_garantes_filter = this.$("#pres_garantes_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.pres_garantes_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "term":encodeURIComponent(window.pres_garantes_filter),
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pres_garantes_page);
    },

    render: function() {
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection,
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"pres_garantes",
        "table":"pres_garantes",
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("pres_garantes/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"pres_garantes",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.pres_garantes_page = this.pagination.getPage();
      this.$("#pres_garantes_table tbody").empty();
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
      $('[data-toggle="tooltip"]').tooltip();
    },

    addOne : function ( item ) {
      var view = new app.views.PresGaranteItem({
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

  views.PresGaranteEditView = app.mixins.View.extend({

    template: _.template($("#pres_garantes_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo_estado_laboral":function(){
        var self = this;
        var v = new app.views.EstadoLaboralEditView({
          model: new app.models.EstadoLaboral(),
          collection: self.estados_laborales,
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "escapable":false,
        });
      },
      "click .nueva_documentacion":function(){
        var self = this;
        var v = new app.views.DocumentacionEditView({
          model: new app.models.Documentacion(),
          collection: self.documentaciones,
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "escapable":false,
        });
      },
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.guardando = 0;

      // Ponemos el analisis del cliente como observaciones
      if (typeof window.analisis_cliente != "undefined" && this.model.id == undefined) {
        this.model.set({"observaciones":window.analisis_cliente});
      }
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
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#pres_garantes_localidad").autocomplete({
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
          self.$("#pres_garantes_codigo_postal").val(ui.item.codigo_postal);
          self.model.set({
            "codigo_postal":ui.item.codigo_postal,
            "id_localidad":ui.item.id,
            "localidad":ui.item.label,
          });
        },
      }); 

      var fecha_nac = this.model.get("fecha_nac");
      if (isEmpty(fecha_nac)) fecha_nac = new Date();
      createdatepicker($(this.el).find("#pres_garantes_fecha_nac"),fecha_nac);

      this.$("#pres_garantes_documento").mask("99999999");
      this.$("#pres_garantes_cuit").mask("99-99999999-9");

      self.estados_laborales = new app.collections.EstadosLaborales();
      var dep = this.model.get("estados_laborales");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.EstadoLaboral(dd);
        self.estados_laborales.add(ddo);
      }
      this.estados_laboralesTable = new app.views.EstadosLaboralesTableView({
        collection: self.estados_laborales,
        view: self,
      });
      this.$("#pres_cliente_estados_laborales").html(this.estados_laboralesTable.el);



      self.documentaciones = new app.collections.Documentaciones();
      var dep = this.model.get("documentaciones");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.Documentacion(dd);
        self.documentaciones.add(ddo);
      }
      this.documentacionesTable = new app.views.DocumentacionesTableView({
        collection: self.documentaciones,
        view: self,
      });
      this.$("#pres_cliente_documentaciones").html(this.documentacionesTable.el);

      setTimeout(function(){
        $('[data-toggle="tooltip"]').tooltip();
      },1000);

      return this;
    },

    validar: function() {
      try {
        var self = this;
        validate_input("pres_garantes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Tenemos que validar el CUIT
        if (this.$("#pres_garantes_tipo_documento").val() == 80) {
          var cuit = this.$("#pres_garantes_cuit").val();
          if (!validateCuit(cuit)) {
            show("ERROR: El CUIT no es valido.");
            this.$("#pres_garantes_cuit").focus();
            return false;
          }
        }

        var estados_laborales = new Array();
        self.estados_laborales.each(function(t){
          estados_laborales.push(t.attributes);
        });
        this.model.set({
          "estados_laborales":estados_laborales,
        });

        var obligatorias = new Array();
        for(var i=0;i<pres_documentaciones.length;i++) {
          var d = pres_documentaciones[i];
          if (d.obligatoria == 1) {
            obligatorias.push({
              "nombre":d.nombre,
              "id":d.id
            });
          }
        }

        var documentaciones = new Array();
        self.documentaciones.each(function(t){
          /*
          obligatorias = _.reject(obligatorias,function(dd){
            return (dd.id == t.get("id_documentacion"));
          });
          */
          documentaciones.push(t.attributes);
        });
        this.model.set({
          "documentaciones":documentaciones,
        });

        // Aun faltan guardar documentacion que es obligatoria
        if (obligatorias.length > 0) {
          var o = obligatorias[0];
          alert("Falta documentacion obligatoria: "+o.nombre);
          return false;
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

        var password_1 = $("#pres_garantes_password").val();
        var password_2 = $("#pres_garantes_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#pres_garantes_password_2").focus();
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
        console.log(e);
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
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
          "fecha_inicial": ((self.$("#pres_garantes_fecha_inicial").length > 0) ? self.$("#pres_garantes_fecha_inicial").val() : ""),
          "fecha_nac": ((self.$("#pres_garantes_fecha_nac").length > 0) ? self.$("#pres_garantes_fecha_nac").val() : ""),
          "sexo": ((self.$("#pres_garantes_sexo").length > 0) ? self.$("#pres_garantes_sexo").val() : "M"),
          "id_tipo_documento": ((self.$("#pres_garantes_tipo_documento").length > 0) ? self.$("#pres_garantes_tipo_documento").val() : 80),
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              location.href="app/#pres_garantes";
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
      this.model = new app.models.PresGarante();
      this.render();
    },

  });

})(app.views, app.models);