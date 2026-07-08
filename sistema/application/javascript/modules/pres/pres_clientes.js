// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PresCliente = Backbone.Model.extend({
    urlRoot: "pres_clientes/",
    defaults: {
      nombre: "",
      apellido: "",
      nombre_completo: "",
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
      garante: 0,
      id_garante_de: 0,
      estudio: 0,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.PresClientes = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "pres_clientes/"
    },

    paginator_ui: {
      perPage: 20,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.PresCliente, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PresClienteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#pres_clientes_item').html()),
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
          "table":"pres_clientes",
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
          window.open("app/#pres_cliente_acciones/"+this.model.id,"_blank");
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

  app.views.PresClientesTableView = app.mixins.View.extend({

    template: _.template($("#pres_clientes_panel_template").html()),

    myEvents: {
      "keydown #pres_clientes_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "keydown #pres_clientes_buscar":function(e) {
        // Flechita de abajo en el campo de busqueda
        if (e.which == 40) { e.preventDefault(); $("#pres_clientes_table tbody tr .radio:first").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #pres_clientes_buscar":"buscar",
      "change #pres_clientes_filtro_especial":"buscar",
      "click .buscar":"buscar",
      "change #pres_clientes_filtro_fecha_vencimiento":"buscar",
      "change #pres_clientes_planes":function() {
        window.pres_clientes_id_plan = this.$("#pres_clientes_planes").val();
        this.buscar();
      },
      "change #pres_clientes_estado":"buscar",
      "change #pres_clientes_sucursales":"buscar",

      "click .simular":"simular",

      "keypress #pres_clientes_numero_prestamo":function(e) {
        if (e.which == 13) this.buscar();
      },

      "click .nuevo_cliente":"nuevo_cliente",
    },

    initialize : function (options) {
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.pres_clientes_garante = (typeof window.pres_clientes_garante != "undefined") ? window.pres_clientes_garante : -1;
      window.pres_clientes_filter = (typeof window.pres_clientes_filter != "undefined") ? window.pres_clientes_filter : "";
      window.pres_clientes_numero_prestamo = (typeof window.pres_clientes_numero_prestamo != "undefined") ? window.pres_clientes_numero_prestamo : "";
      window.pres_clientes_page = (typeof window.pres_clientes_page != "undefined") ? window.pres_clientes_page : 1;
      window.pres_clientes_id_sucursal = (typeof window.pres_clientes_id_sucursal != "undefined") ? window.pres_clientes_id_sucursal : ID_SUCURSAL;
      window.pres_clientes_filtro_especial = (typeof window.pres_clientes_filtro_especial != "undefined") ? window.pres_clientes_filtro_especial : 0;
      window.pres_clientes_id_plan = (typeof window.pres_clientes_id_plan != "undefined") ? window.pres_clientes_id_plan : 0;
      window.pres_clientes_estado = (typeof window.pres_clientes_estado != "undefined") ? window.pres_clientes_estado : 0;
      window.pres_clientes_filtro_fecha_vencimiento = (typeof window.pres_clientes_filtro_fecha_vencimiento != "undefined") ? window.pres_clientes_filtro_fecha_vencimiento : "";
      this.cambio_parametros = false;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    buscar: function() {
      
      if (window.pres_clientes_filter != this.$("#pres_clientes_buscar").val().trim()) {
        window.pres_clientes_filter = this.$("#pres_clientes_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (window.pres_clientes_numero_prestamo != this.$("#pres_clientes_numero_prestamo").val().trim()) {
        window.pres_clientes_numero_prestamo = this.$("#pres_clientes_numero_prestamo").val().trim();
        this.cambio_parametros = true;
      }
      if (window.pres_clientes_id_sucursal != this.$("#pres_clientes_sucursales").val()) {
        window.pres_clientes_id_sucursal = this.$("#pres_clientes_sucursales").val();
        this.cambio_parametros = true;
      }
      if (window.pres_clientes_filtro_especial != this.$("#pres_clientes_filtro_especial").val()) {
        window.pres_clientes_filtro_especial = this.$("#pres_clientes_filtro_especial").val();
        this.cambio_parametros = true;
      }
      if (window.pres_clientes_estado != this.$("#pres_clientes_estado").val()) {
        window.pres_clientes_estado = this.$("#pres_clientes_estado").val();
        this.cambio_parametros = true;
      }
      if (window.pres_clientes_filtro_fecha_vencimiento != this.$("#pres_clientes_filtro_fecha_vencimiento").val()) {
        window.pres_clientes_filtro_fecha_vencimiento = this.$("#pres_clientes_filtro_fecha_vencimiento").val();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.pres_clientes_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "term":encodeURIComponent(window.pres_clientes_filter),
        "garante":window.pres_clientes_garante,
        "id_sucursal":window.pres_clientes_id_sucursal,
        "filtro_especial":window.pres_clientes_filtro_especial,
        "id_plan":window.pres_clientes_id_plan,
        "estado":window.pres_clientes_estado,
        "numero_prestamo":window.pres_clientes_numero_prestamo,
        "fecha_vencimiento":window.pres_clientes_filtro_fecha_vencimiento.replace(/\//g,"-"),
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pres_clientes_page);
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

      createdatepicker(this.$("#pres_clientes_filtro_fecha_vencimiento"),window.pres_clientes_filtro_fecha_vencimiento);

      new app.mixins.Select({
        modelClass: app.models.PresPlanCredito,
        url: "pres_planes_credito/",
        render: "#pres_clientes_planes",
        firstOptions: ["<option value='0'>Plan</option>"],
        selected: window.pres_clientes_id_plan,
      });
    },

    nuevo_cliente: function() {
      if (window.pres_clientes_garante == 0) {
        window.analisis_cliente = "";
        var view = new app.views.AnalisisPresClienteView({
          model: new app.models.AbstractModel()
        });
        var d = $("<div/>").append(view.el);
        crearLightboxHTML({
          "html":d,
          "width":600,
          "height":500,
          "escapable":false,
          "callback":function(){
            if (window.analisis_cliente != "") {
              location.href="app/#pres_cliente";
            }
          }
        });
      } else {
        location.href="app/#pres_garante";
      }
    },

    simular: function() {
      var view = new app.views.PrestamoSimuladorView({
        model: new app.models.Prestamo()
      });
      var d = $("<div/>").append(view.el);
      crearLightboxHTML({
        "html":d,
        "width":600,
        "height":500,
        "escapable":false,
      });
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"pres_clientes",
        "table":"pres_clientes",
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("pres_clientes/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"pres_clientes",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.pres_clientes_page = this.pagination.getPage();
      this.$("#pres_clientes_table tbody").empty();
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
      var view = new app.views.PresClienteItem({
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

  views.PresClienteEditView = app.mixins.View.extend({

    template: _.template($("#pres_clientes_edit_panel_template").html()),

    myEvents: {
      "change #pres_clientes_documento":function(){
        console.log("ANDA");
        if (this.model.isNew()) {
          var documento = this.$("#pres_clientes_documento").val();
          $.ajax({
            "url":"pres_clientes/function/existe_documento/",
            "dataType":"json",
            "type":"post",
            "data":{
              "documento":documento,
            },
            "success":function(r){
              if (r.existe == 1) alert(r.mensaje);
            }
          });
        }
      },
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
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
      $(this.el).find("#pres_clientes_localidad").autocomplete({
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
          self.$("#pres_clientes_codigo_postal").val(ui.item.codigo_postal);
          self.model.set({
            "codigo_postal":ui.item.codigo_postal,
            "id_localidad":ui.item.id,
            "localidad":ui.item.label,
          });
        },
      }); 

      var fecha_nac = this.model.get("fecha_nac");
      if (isEmpty(fecha_nac)) fecha_nac = new Date();
      createdatepicker($(this.el).find("#pres_clientes_fecha_nac"),fecha_nac);

      this.$("#pres_clientes_documento").mask("99999999");
      this.$("#pres_clientes_cuit").mask("99-99999999-9");

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
        validate_input("pres_clientes_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Tenemos que validar el CUIT
        if (this.$("#pres_clientes_tipo_documento").val() == 80) {
          var cuit = this.$("#pres_clientes_cuit").val();
          if (!validateCuit(cuit)) {
            show("ERROR: El CUIT no es valido.");
            this.$("#pres_clientes_cuit").focus();
            return false;
          }
        }

        this.model.set({
          "id_sucursal":self.$("#pres_cliente_sucursales").val()
        });

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
          obligatorias = _.reject(obligatorias,function(dd){
            return (dd.id == t.get("id_documentacion"));
          });
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

        var password_1 = $("#pres_clientes_password").val();
        var password_2 = $("#pres_clientes_password_2").val();
        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#pres_clientes_password_2").focus();
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
          "fecha_inicial": ((self.$("#pres_clientes_fecha_inicial").length > 0) ? self.$("#pres_clientes_fecha_inicial").val() : ""),
          "fecha_nac": ((self.$("#pres_clientes_fecha_nac").length > 0) ? self.$("#pres_clientes_fecha_nac").val() : ""),
          "sexo": ((self.$("#pres_clientes_sexo").length > 0) ? self.$("#pres_clientes_sexo").val() : "M"),
          "id_tipo_documento": ((self.$("#pres_clientes_tipo_documento").length > 0) ? self.$("#pres_clientes_tipo_documento").val() : 80),
          "garante": ((typeof window.pres_clientes_garante != "undefined") ? window.pres_clientes_garante : 0),
        },{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              if (window.pres_clientes_garante == 1) {
                location.href="app/#pres_garantes";
              } else {
                location.href="app/#pres_clientes";
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
      this.model = new app.models.PresCliente();
      this.render();
    },

  });

})(app.views, app.models);



(function ( views, models ) {

  views.AnalisisPresClienteView = app.mixins.View.extend({
    template: _.template($("#pres_clientes_analisis_view").html()),
    myEvents: {
      "click .continuar": "continuar",
      "click .cerrar": "cerrar",
    },
    initialize: function(options) {
      this.options = options;
      _.bindAll(this);
      $(this.el).html(this.template(this.model.toJSON()));
    },
    cerrar: function() {
      $('.modal:last').modal('hide');
    },
    continuar: function() {
      // Verificamos todos los campos
      for(var i=1; i<=8; i++) {
        if (isEmpty(this.$("#pres_clientes_analisis_"+i).val())) {
          alert("Por favor complete el campo.")
          this.$("#pres_clientes_analisis_"+i).focus();
          return;
        }
      }
      // No hay ningun error, concatenamos todo
      window.analisis_cliente = "";
      for(var i=1; i<=8; i++) {
        var et = this.$("#pres_clientes_analisis_label_"+i).text().trim();
        var va = this.$("#pres_clientes_analisis_"+i).val();
        window.analisis_cliente += et+": "+va+"\n";
      }
      $('.modal:last').modal('hide');
    },
  });

})(app.views, app.models);


(function ( views, models ) {

  views.PresClienteTimelineView = app.mixins.View.extend({

    template: _.template($("#pres_clientes_timeline_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .expand-link":function(){}, // Se sobreescribe para no ejecutarla dos veces
      "click #prestamos_link":function() {
        this.$(".streamline").hide();
      },
      "click #premios_canjeados_link":function() {
        this.$(".streamline").hide();
      },
      "click #seguimiento_link":function() {
        this.$(".streamline").show();
      },
      "click .estudio":function(e){
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var estudio = this.model.get("estudio");
        estudio = (estudio == 1)?0:1;
        self.model.set({"estudio":estudio});
        this.change_property({
          "table":"pres_clientes",
          "attribute":"estudio",
          "value":estudio,
          "id":self.model.id,
          "success":function(){
            location.reload();
          }
        });
        return false;
      },
      "click .nuevo_prestamo":function(){
        var self = this;
        var v = new app.views.PrestamoEditView({
          model: new app.models.Prestamo({
            "id_cliente":self.model.id
          }),
          view: self,
        });
//        workspace.abrir_calculadora_prestamos(self.model.id);
        crearLightboxHTML({
          "html":v.el,
          "width":1100,
          "height":140,
          "escapable":false,
          "callback":function() {
            //workspace.cerrar_calculadora_prestamos();
          }
        });
      },
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.titulo_modulo = (typeof options.titulo_modulo != "undefined" ? options.titulo_modulo : "");
      this.clase_modulo = (typeof options.clase_modulo != "undefined" ? options.clase_modulo : "");
      this.tab_activo = (typeof options.tab_activo != "undefined" ? options.tab_activo : "prestamos");
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
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var telefonos = new Array();
      if (!isEmpty(self.model.get("telefono"))) {
        telefonos.push(self.model.get("telefono"));
      }
      if (!isEmpty(self.model.get("telefono_2"))) {
        telefonos.push(self.model.get("telefono_2"));
      }
      if (!isEmpty(self.model.get("telefono_3"))) {
        telefonos.push(self.model.get("telefono_3"));
      }
      if (!isEmpty(self.model.get("telefono_4"))) {
        telefonos.push(self.model.get("telefono_4"));
      }
      if (!isEmpty(self.model.get("telefono_5"))) {
        telefonos.push(self.model.get("telefono_5"));
      }

      var modelo = new app.models.Consulta({
        "id_contacto":self.model.id,
        "fecha":moment().format("DD/MM/YYYY"),
        "hora":moment().format("HH:mm:ss"),
      });
      modelo.on("remove",this.actualizar_consultas,this);
      this.editor = new app.views.CrearConsultaTimeline({
        "model":modelo,
        "view":self,
        "nota":self.model.get("nota"),
        "alerta_celular":(telefonos.length == 0),
        "alerta_email":(isEmpty(self.model.get("email"))),
        "mostrar_sms":true,
        "mostrar_tarea":true,
        "telefonos":telefonos,
      });
      this.$("#pres_cliente_crear_consultas").html(this.editor.el);
      this.render_consultas();

      var tiene_garante = 0;
      var habilitar_paralelo = 0;
      self.prestamos = new app.collections.Prestamos();
      self.prestamosCancelados = new app.collections.Prestamos();

      this.deuda_vencida = 0;
      var activos = new Array();
      var cancelados = new Array();

      var dep = this.model.get("prestamos");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.Prestamo(dd);

        // Verificamos si algun prestamo tiene garante
        if (ddo.get("id_garante")>0) {
          tiene_garante = 1;
        }

        if (ddo.get("cantidad_cuotas") == ddo.get("cantidad_cuotas_pagas")) {
          cancelados.push(ddo);
        } else {
          activos.push(ddo);
        }

        ddo.deuda_vencida = parseFloat(ddo.get("deuda_vencida"));
        if (ddo.deuda_vencida > 0) this.deuda_vencida += ddo.deuda_vencida;

      }

      // Recorremos los cancelados
      for(var i=0;i<cancelados.length;i++) {
        var ddo = cancelados[i];
        self.prestamosCancelados.add(ddo);
      }

      // Recorremos los activos
      for(var i=0;i<activos.length;i++) {

        var ddo = activos[i];
        self.prestamos.add(ddo);

        if (this.deuda_vencida == 0) {
          // Controlamos si es posible la renovacion
          var cuotas_pagas = parseFloat(ddo.get("cantidad_cuotas_pagas"));
          var cuotas = parseFloat(ddo.get("cantidad_cuotas"));
          var ratio = (cuotas_pagas / cuotas);
          if (activos.length == 1 && cuotas_pagas > 1) {
            if (cuotas >= 6) {
              if (cuotas_pagas >= 4) habilitar_paralelo = 1;
            } else {
              if (ratio >= 0.5) habilitar_paralelo = 1;
            }
          }
        }
      }

      this.prestamosTable = new app.views.PrestamosTableView({
        collection: self.prestamos,
        view: self,
      });
      this.$("#pres_cliente_prestamos").html(this.prestamosTable.el);

      this.prestamosCanceladosTable = new app.views.PrestamosTableView({
        collection: self.prestamosCancelados,
        view: self,
      });
      this.$("#pres_cliente_prestamos_cancelados").html(this.prestamosCanceladosTable.el);

      if (habilitar_paralelo == 1) this.$("#pres_cliente_prestamos_habilitar_paralelo").show();
      if (tiene_garante == 1) this.$("#pres_cliente_prestamos_tiene_garante").show();

      if (this.tab_activo == "seguimiento") {
        this.$("#prestamos_link").removeClass("active");
        this.$("#premios_canjeados_link").removeClass("active");
        this.$("#seguimiento_link").addClass("active");
        this.$("#tab1_pres_cliente").removeClass("active");
        this.$("#tab3_pres_cliente").removeClass("active");
        this.$("#tab4_pres_cliente").addClass("active");
        this.$(".streamline").show();
      }

      return this;
    },

    actualizar_consultas: function() {
      var self = this;
      $.ajax({
        "url":"pres_clientes/function/get_consultas/",
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

    guardar_nota: function(nota){
      var self = this;
      this.model.save({
        "nota":nota,
      },{
        "success":function() {
          self.actualizar_consultas();
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
              location.href="app/#pres_clientes";
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





// -----------------------------------------
//   PRESTAMOS
// -----------------------------------------

(function ( models ) {
  models.Prestamo = Backbone.Model.extend({
    urlRoot: "pres_prestamos/",
    defaults: {
      plan: "",
      id_plan: 0,
      numero: 0,
      fecha: "",
      hora: "",
      observaciones: "",
      garante: "",
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      id_sucursal: ID_SUCURSAL,
      valor_cuota: 0,
      monto_prestado: 0,
      cantidad_cuotas: 0,
      cantidad_cuotas_pagas: 0,
      proximo_vencimiento: "",
      cuotas: [],
      habilitado_renovacion: 0,
    },
  });
})( app.models );

(function (collections, model) {
  collections.Prestamos = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.Prestamo);

(function ( app ) {

  app.views.PrestamosTableView = app.mixins.View.extend({

    template: _.template($("#prestamos_resultados_template").html()),
        
    myEvents: {
      "change #prestamos_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_cliente = (typeof this.options.id_cliente != "undefined") ? this.options.id_cliente : 0;
      this.view = options.view;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.PrestamosItemResultados({
        model: item,
        collection: self.collection,
        view: self.view,
      });
      this.$(".tbody").append(view.render().el);        
    },
            
  });

})(app);

(function ( app ) {
  app.views.PrestamosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#prestamos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .editar":function() {
        var self = this;
        //workspace.abrir_calculadora_prestamos(self.model.get("id_cliente"));
        self.model.fetch({
          "success":function(){
            var v = new app.views.PrestamoEditView({
              model:self.model,
              view: self.view,
            });
            var that = self;
            crearLightboxHTML({
              "html":v.el,
              "width":1100,
              "height":140,
              "callback":function() {
                //workspace.cerrar_calculadora_prestamos();
                self.view.model.fetch();
              }
            });
          }
        });
      },
      /*
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.set({
            "eliminado":1,
          })
          //this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
          this.view.numeros.numero_prestamo--;
        }
        return false;
      },*/
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.view = options.view;
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.deuda_vencida_total = this.view.deuda_vencida;
      console.log(obj.deuda_vencida_total);
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);


(function ( app ) {

  app.views.PrestamoEditView = app.mixins.View.extend({

    template: _.template($("#prestamo_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .imprimir_prestamo":"imprimir",
      "click .imprimir_seleccionadas":"imprimir_seleccionadas",
      "click .eliminar_prestamo":"eliminar_prestamo",
      "click .cerrar": "cerrar",
      "change #prestamo_planes_credito":"cambiar_plan",
      "change #prestamo_cantidad_cuotas":"calcular_cuota",
      "change #prestamo_monto_prestado":"calcular_cuota",
      "change #prestamo_fecha":"calcular_cuota",
      "click .liquidar_cuotas":"liquidar_cuotas",
      "click .renovar":"renovar_prestamo",
      "change #prestamo_paga_con":function() {
        var total = parseFloat(this.$("#prestamo_total_cobrado").val());
        if (isNaN(total)) total = 0;
        var paga_con = parseFloat(this.$("#prestamo_paga_con").val());
        if (isNaN(paga_con)) paga_con = 0;
        var vuelto = Number(paga_con - total).toFixed(2);
        this.$("#prestamo_vuelto").val(vuelto);
      }
    },
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.view = options.view;
      this.renovar = (typeof options.renovar != "undefined") ? options.renovar : null;
      this.saldo_renovacion = 0;
      this.guardando = 0;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;

      if (self.renovar != null) {
        var cuotas = self.renovar.get("cuotas");
        for(var i=0;i<cuotas.length;i++) {
          var e = cuotas[i];
          var s = parseFloat(e.saldo);
          if (isNaN(s)) s = 0;
          self.saldo_renovacion += s;
        }
      }

      var obj = { 
        "id":this.model.id,
        "saldo_renovacion":this.saldo_renovacion,
      }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      if (typeof this.model.id == "undefined") {
        new app.mixins.Select({
          modelClass: app.models.PresPlanCredito,
          url: "pres_planes_credito/",
          render: "#prestamo_planes_credito",
          fields:["dias_primera_cuota"],
          onComplete:function() {
            self.cambiar_plan();
          }
        });
      }

      createdatepicker($(this.el).find("#prestamo_fecha"),new Date());
      createdatepicker($(this.el).find("#prestamo_primera_cuota"),new Date());

      var input = this.$("#prestamo_garante");
      $(input).customcomplete({
        "url":"pres_clientes/function/get_by_nombre/?garante=-1&not_id="+self.model.get("id_cliente"),
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#prestamo_garante_id").val(item.id);
          self.$("#prestamo_garante").val(item.nombre);
        }
      });

      var monto_pagado_total = 0;
      var interes_pagado_total = 0;

      self.cuotas = new app.collections.PrestamosCuotas();
      var dep = this.model.get("cuotas");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.PrestamoCuota(dd);

        monto_pagado_total += parseFloat(dd.monto_pagado);
        interes_pagado_total += parseFloat(dd.interes_pagado);

        self.cuotas.add(ddo);
      }
      this.cuotasTable = new app.views.PrestamosCuotasTableView({
        collection: self.cuotas,
        view: self,
        sesion_cliente_prestamo: self.model.get("sesion_cliente_prestamo"),
        sesion_id_cliente_prestamo: self.model.get("sesion_id_cliente_prestamo"),
        sesion_total_cobrado: self.model.get("sesion_total_cobrado"),
      });
      this.$("#prestamo_cuotas").html(this.cuotasTable.el);

      // Ponemos como ultima fila los totales
      this.$("#prestamo_monto_pagado_total").val(Number(monto_pagado_total).toFixed(2));
      this.$("#prestamo_interes_pagado_total").val(Number(interes_pagado_total).toFixed(2));
      this.$("#prestamo_total_pagado").val(Number(monto_pagado_total + interes_pagado_total).toFixed(2));

      this.$("#prestamo_saldo_actual").val(Number(this.cuotasTable.saldo_actual).toFixed(2));
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    liquidar_cuotas: function() {
      var self = this;
      var saldo_pendiente = 0;
      var cuotas_seleccionadas = new Array();
      $(".check_cuota:checked").each(function(i,e){
        cuotas_seleccionadas.push($(e).val());
        saldo_pendiente += parseFloat($(e).data("saldo"));
      });
      if (cuotas_seleccionadas.length == 0) {
        alert("Por favor seleccione alguna cuota.");
        return;
      }
      saldo_pendiente = Number(saldo_pendiente).toFixed(2);
      var view = new app.views.LiquidarCuotasView({
        "model": new app.models.AbstractModel({
          "id_prestamo":self.model.id,
          "cuotas":cuotas_seleccionadas.join("-"),          
          "saldo": saldo_pendiente,
        }),
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":140,
      });
    },

    cambiar_plan: function() {
      var self = this;
      var id_plan_credito = this.$("#prestamo_planes_credito").val();
      $.ajax({
        "url":"pres_planes_credito/"+id_plan_credito,
        "dataType":"json",
        "success":function(r) {
          console.log(r);
          self.$("#prestamo_cantidad_cuotas").empty();
          for(var i=0;i<r.cuotas.length;i++) {
            var cuota = r.cuotas[i];
            self.$("#prestamo_cantidad_cuotas").append('<option data-tasa_interes="'+cuota.tasa_interes+'">'+cuota.cuota+'</option>');
          }
          self.calcular_cuota();
        }
      });
    },

    calcular_cuota: function() {
      var monto_prestado = parseFloat(this.$("#prestamo_monto_prestado").val());
      if (isNaN(monto_prestado)) monto_prestado = 0;
      var cantidad_cuotas = this.$("#prestamo_cantidad_cuotas").val();
      var tasa_interes = this.$("#prestamo_cantidad_cuotas option:selected").data("tasa_interes");
      var dias_primera_cuota = this.$("#prestamo_planes_credito option:selected").data("dias_primera_cuota");
      var cuota = Number(monto_prestado * (tasa_interes / 100)).toFixed(0);
      this.$("#prestamo_valor_cuota").val(cuota);
      var dias_primera_cuota = this.$("#prestamo_planes_credito option:selected").data("dias_primera_cuota");
      var fecha = this.$("#prestamo_fecha").val();
      var primera_cuota = moment(fecha,"DD/MM/YYYY").add(dias_primera_cuota,"days").format("DD/MM/YYYY");
      this.$("#prestamo_primera_cuota").val(primera_cuota);
    },

    eliminar_prestamo: function() {
      var self = this;
      if (confirmar("Realmente desea eliminar el prestamo?")) {
        $.ajax({
          "url":"pres_prestamos/function/eliminar_prestamo/"+self.model.id,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) location.reload();
            else alert("Hubo un error al eliminar el prestamo.");
          },
        })
      }
    },

    renovar_prestamo: function() {
      var self = this;
      if (confirmar("Desea hacer una renovacion de este prestamo y absorber las cuotas?")) {
        self.cerrar();
        //workspace.abrir_calculadora_prestamos(self.model.get("id_cliente"));
        var v = new app.views.PrestamoEditView({
          model: new app.models.Prestamo({
            "id_cliente":self.model.get("id_cliente")
          }),
          renovar: self.model,
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":1100,
          "height":140,
          "escapable":false,
          "callback":function() {
            //workspace.cerrar_calculadora_prestamos();
          },
        });
      }
    },

    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");

        var monto_prestado = parseFloat(this.$("#prestamo_monto_prestado").val());
        if (isNaN(monto_prestado)) monto_prestado = 0;
        if (monto_prestado == 0) {
          alert("Por favor ingrese un monto");
          this.$("#prestamo_monto_prestado").select();
          return false;
        }

        // Si estamos renovando un prestamo
        if (this.saldo_renovacion > 0) {
          // El monto prestado debe ser mayor al saldo del credito anterior
          if (monto_prestado < this.saldo_renovacion) {
            alert("El monto prestado debe ser mayor al saldo del prestamo por renovar.");
            this.$("#prestamo_monto_prestado").select();
            return false;            
          }
        }

        // Controlamos que haya elegido garante
        var id_garante = this.$("#prestamo_garante_id").val();
        /*
        if (isEmpty(id_garante) || id_garante == 0) {
          alert("Por favor seleccione un garante.");
          this.$("#prestamo_garante").select();
          return false;
        }
        */

        var dias_primera_cuota = this.$("#prestamo_planes_credito option:selected").data("dias_primera_cuota");
        var cantidad_cuotas = this.$("#prestamo_cantidad_cuotas").val();
        var cuota = this.$("#prestamo_valor_cuota").val();
        if (this.$("#prestamo_primera_cuota").is(":enabled")) { 
          var primera_cuota = this.$("#prestamo_primera_cuota").val();
        } else {
          var primera_cuota = moment().add(dias_primera_cuota,"days").format("DD/MM/YYYY");
        }

        var cuotas = new Array();
        for(var i=1; i<=cantidad_cuotas; i++) {
          var fecha_vencimiento = moment(primera_cuota,"DD/MM/YYYY").add(i-1,"months").format("DD/MM/YYYY");
          cuotas.push({
            "numero":i,
            "fecha_vencimiento":fecha_vencimiento,
            "monto":cuota,
          });
        }
        this.model.set({
          "fecha":self.$("#prestamo_fecha").val(),
          "hora":moment().format("HH:mm"),
          "id_plan":self.$("#prestamo_planes_credito").val(),
          "plan":self.$("#prestamo_planes_credito option:selected").text(),
          "monto_prestado":monto_prestado,
          "cantidad_cuotas":cantidad_cuotas,
          "valor_cuota":cuota,
          "cuotas":cuotas,
          "garante":self.$("#prestamo_garante").val(),
          "id_garante":id_garante,
        });

        // Si estamos renovando un prestamo
        if (this.saldo_renovacion > 0) {
          this.model.set({
            "id_prestamo_renovado":self.renovar.id,
            "saldo_renovacion":self.saldo_renovacion,
          });
        }

        // Si la sucursal es 0, ponemos ENSENADA
        if (this.model.get("id_sucursal") == 0) {
          this.model.set({"id_sucursal":42});
        }

        if (this.$("#prestamo_sucursales").length > 0) {
          var ii = this.$("#prestamo_sucursales").val();
          this.model.set({"id_sucursal":ii});
        }

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },

    guardar:function() {
      var self = this;
      if (this.guardando == 1) return;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.guardando = 1;
        this.model.save({},{
          "success":function(){
            self.guardando = 0;
            location.reload();
          },
          "error":function() {
            self.guardando = 0;
          },
        })
      }      
    },

    imprimir: function() {
      window.open("/sistema/pres_prestamos/function/imprimir_prestamo/"+this.model.id+"/"+ID_SUCURSAL,"_blank");
    },

    imprimir_seleccionadas: function() {
      var self = this;
      var cuotas_seleccionadas = new Array();
      $(".check_cuota:checked").each(function(i,e){
        cuotas_seleccionadas.push($(e).val());
      });
      if (cuotas_seleccionadas.length == 0) {
        alert("Por favor seleccione alguna cuota.");
        return;
      }
      cuotas_seleccionadas = cuotas_seleccionadas.join("-");
      window.open("/sistema/pres_prestamos/function/imprimir_seleccionadas/"+this.model.id+"/"+ID_SUCURSAL+"/"+cuotas_seleccionadas,"_blank");
    },
          
  });
})(app);




(function ( models ) {
  models.PrestamoCuota = Backbone.Model.extend({
    urlRoot: "pres_prestamos_cuotas",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_sucursal: ID_SUCURSAL,
      id_prestamo: 0,
      numero: 0,
      fecha_vencimiento: "",
      fecha_pago: "",
      estado: 0,
      monto: 0,
      monto_pagado: 0,
      saldo: 0,
      interes: 0,
      saldo_interes: 0,
      saldo_capital: 0,
      total: 0,
      observaciones: "",
      pagos: [],
      id_factura: 0,
      id_punto_venta: 0,
    },
  });
})( app.models );

(function (collections, model) {
  collections.PrestamosCuotas = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.PrestamoCuota);

(function ( app ) {

  app.views.PrestamosCuotasTableView = app.mixins.View.extend({

    template: _.template($("#prestamos_cuotas_resultados_template").html()),
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.view = options.view;
      this.sesion_cliente_prestamo = options.sesion_cliente_prestamo;
      this.sesion_id_cliente_prestamo = options.sesion_id_cliente_prestamo;
      this.sesion_total_cobrado = options.sesion_total_cobrado;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      this.saldo_actual = 0;
      this.estado_anterior = 1; // 1 indica que la anterior esta paga, que podemos cargar un pago a la actual
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      this.saldo_actual += parseFloat(item.get("saldo"));
      var view = new app.views.PrestamosCuotasItemResultados({
        model: item,
        collection: self.collection,
        view: self.view,
        estado_anterior: self.estado_anterior,
        sesion_cliente_prestamo : self.options.sesion_cliente_prestamo,
        sesion_id_cliente_prestamo : self.options.sesion_id_cliente_prestamo,
        sesion_total_cobrado : self.options.sesion_total_cobrado,
      });
      this.estado_anterior = item.get("estado");
      this.$(".tbody").append(view.render().el);        
    },
            
  });

})(app);

(function ( app ) {
  app.views.PrestamosCuotasItemResultados = app.mixins.View.extend({
        
    template: _.template($("#prestamos_cuotas_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .imprimir_cuota":"imprimir_cuota",
      "click .facturar_cuota":"facturar_cuota",
      "click .data":"seleccionar",
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.PrestamoCuotaEditView({
        model:self.model,
        collection:self.collection,
        estado_anterior: self.estado_anterior,
        sesion_cliente_prestamo : self.sesion_cliente_prestamo,
        sesion_id_cliente_prestamo : self.sesion_id_cliente_prestamo,
        sesion_total_cobrado : self.sesion_total_cobrado,
      });
      var that = self;
      crearLightboxHTML({
        "html":v.el,
        "width":1000,
        "height":140,
        "callback":function(){
          that.view.model.fetch({
            "success":function() {
              that.view.render();
            }
          });
        }
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.view = options.view;
      this.options = options;
      this.estado_anterior = options.estado_anterior;
      this.sesion_cliente_prestamo = options.sesion_cliente_prestamo;
      this.sesion_id_cliente_prestamo = options.sesion_id_cliente_prestamo;
      this.sesion_total_cobrado = options.sesion_total_cobrado;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    imprimir_cuota: function() {
      window.open("/sistema/pres_prestamos_cuotas/function/imprimir/"+this.model.id+"/"+ID_SUCURSAL,"_blank");
    },
    facturar_cuota: function(e) {
      var self = this;
      if ($(e.currentTarget).hasClass("active")) {
        window.open("app/#comprobante/"+self.model.get("id_factura")+"/"+self.model.get("id_punto_venta"),"_blank");
        return;
      }
      if (!confirm("Desea realizar una factura para esta cuota?")) return;
      var v = new app.views.PrestamoFacturarCuotaView({
        model:new app.models.AbstractModel({
          "id_prestamo":self.model.get("id_prestamo"),
          "id_sucursal":self.model.get("id_sucursal"),
          "id_cuota":self.model.id,
          "id_pago":0,
        }),
      });
      var that = self;
      crearLightboxHTML({
        "html":v.el,
        "width":480,
        "height":140,
      });
    },
  });
})(app);


(function ( app ) {

  app.views.PrestamoCuotaEditView = app.mixins.View.extend({

    template: _.template($("#prestamo_cuota_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "change #prestamo_cuota_saldo_capital": "calcular_saldo",
      "change #prestamo_cuota_saldo_interes": "calcular_saldo",
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      var edicion = false;
      self.sesion_cliente_prestamo = (typeof options.sesion_cliente_prestamo != "undefined") ? options.sesion_cliente_prestamo : 0;
      self.sesion_id_cliente_prestamo = (typeof options.sesion_id_cliente_prestamo != "undefined") ? options.sesion_id_cliente_prestamo : "";
      self.sesion_total_cobrado = (typeof options.sesion_total_cobrado != "undefined") ? options.sesion_total_cobrado : 0;
      var obj = { 
        "id":this.model.id,
        "sesion_cliente_prestamo" : self.sesion_cliente_prestamo,
        "sesion_id_cliente_prestamo" : self.sesion_id_cliente_prestamo,
        "sesion_total_cobrado" : self.sesion_total_cobrado,        
      };
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      this.guardando = 0;

      // Indica si la anterior cuota esta cancelada o no
      this.estado_anterior = options.estado_anterior;

      self.pagos = new app.collections.PrestamosCuotasPagos();
      self.pagos.on("all",function(e){
        var total = 0;
        self.pagos.each(function(p){
          if (p.id == 0) {
            // Solamente sumamos los pagos nuevos
            total += parseFloat(p.get("monto"));
          }
        });
        total += parseFloat(self.sesion_total_cobrado);
        self.$("#sesion_total").html("$ "+Number(total).toFixed(2));
      })

      var dep = this.model.get("pagos");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.PrestamoCuotaPago(dd);
        self.pagos.add(ddo);
      }
      this.pagosTable = new app.views.PrestamosCuotasPagosTableView({
        collection: self.pagos,
        view: self,
        saldo: self.model.get("saldo"),
        saldo_interes: self.model.get("saldo_interes"),
      });
      this.$("#prestamo_cuota_pagos").html(this.pagosTable.el);
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    calcular_saldo: function() {
      var saldo_capital = parseFloat(this.$("#prestamo_cuota_saldo_capital").val());
      if (isNaN(saldo_capital)) saldo_capital = 0;
      var saldo_interes = parseFloat(this.$("#prestamo_cuota_saldo_interes").val());
      if (isNaN(saldo_interes)) saldo_interes = 0;
      var saldo = saldo_capital + saldo_interes;
      this.$("#prestamo_cuota_saldo").val(saldo);
      this.pagosTable.saldo = saldo;
      this.pagosTable.saldo_interes = saldo_interes;
      this.pagosTable.saldo_capital = saldo_capital;
      this.pagosTable.render_saldo();
    },

    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");

        // Si estamos intentando asignar un pago a esta cuota, pero hay algunas anteriores que todavia no estan pagadas, tiramos un error
        if (self.estado_anterior != 1 && self.pagos.length > 0) {
          alert("ERROR: Existen cuotas anteriores que aun no han sido saldadas. Por favor cancele primero estas para asignar un pago.");
          return false;
        }

        this.model.set({
          "pagos":self.pagos.toJSON()
        });
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    guardar:function() {
      if (PERFIL == 1181) return;
      var self = this;
      if (this.guardando == 1) return;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.guardando = 1;
        this.model.save({},{
          "success":function() {
            self.guardando = 0;
            self.cerrar();
          },
          "error":function() {
            self.guardando = 0;
          }
        })
      }      
    },
          
  });
})(app);




(function ( models ) {
  models.PrestamoCuotaPago = Backbone.Model.extend({
    urlRoot: "pres_cajas_movimientos",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_prestamo: 0,
      id_cuota: 0,
      id_sucursal: ID_SUCURSAL,
      fecha: "",
      id_caja: 0,
      id_concepto: 0,
      observaciones: "",
      monto: 0,
      cancelacion_capital: 0,
      cancelacion_interes: 0,
      id_factura: 0,
      id_punto_venta: 0,
      descuento: 0,
    },
  });
})( app.models );

(function (collections, model) {
  collections.PrestamosCuotasPagos = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.PrestamoCuotaPago);

(function ( app ) {

  app.views.PrestamosCuotasPagosTableView = app.mixins.View.extend({

    template: _.template($("#prestamos_cuotas_pagos_resultados_template").html()),

    myEvents:{
      "click .agregar_pago":"agregar_pago",
      "change #prestamos_cuotas_pagos_monto":"dividir_pago",
      "change #prestamos_cuotas_pagos_aplica_descuento":"aplicar_descuento",
      //"change #prestamos_cuotas_pagos_capital":"dividir_pago",
      "change #prestamos_cuotas_pagos_interes":"sumar_pago",
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.view = options.view;
      this.saldo_interes = options.saldo_interes;
      this.saldo_capital = options.saldo_capital;
      this.saldo = options.saldo;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      createdatepicker($(this.el).find("#prestamos_cuotas_pagos_fecha"),new Date());
      this.render_saldo();
      return this;
    },

    aplicar_descuento: function() {
      var porcentaje = 0;
      if (this.$("#prestamos_cuotas_pagos_aplica_descuento").is(":checked")) {
        var porcentaje = this.$("#prestamos_cuotas_pagos_descuento_porcentaje").val();
        if (isNaN(porcentaje)) porcentaje = 0;
      }
      var monto = parseFloat(this.$("#prestamos_cuotas_pagos_monto").val());
      if (isNaN(monto)) monto = 0;
      var efectivo = (monto * ((100-porcentaje)/100));
      var descuento = (monto - efectivo);
      this.$("#prestamos_cuotas_pagos_pago_efectivo").val(Number(efectivo).toFixed(2));
      this.$("#prestamos_cuotas_pagos_descuento").val(Number(descuento).toFixed(2));
    },

    render_saldo: function() {
      this.$("#prestamos_cuotas_pagos_monto").val(this.saldo);
      this.dividir_pago();
    },

    dividir_pago: function() {
      // Primero cubrimos el interes, y lo que sobra pagamos el capital
      var pago = parseFloat(this.$("#prestamos_cuotas_pagos_monto").val());
      if (isNaN(pago)) pago = 0;
      var dif = parseFloat(pago - this.saldo_interes);
      if (isNaN(dif)) dif = 0;
      if (dif < 0) {
        this.$("#prestamos_cuotas_pagos_interes").val(Number(pago).toFixed(2));
        this.$("#prestamos_cuotas_pagos_capital").val(0);
      } else {
        this.$("#prestamos_cuotas_pagos_interes").val(Number(this.saldo_interes).toFixed(2));
        this.$("#prestamos_cuotas_pagos_capital").val(Number(dif).toFixed(2));
      }
      this.aplicar_descuento();
    },

    sumar_pago: function() {
      // El pago total lo conformamos con la suma del pago del capital mas el pago por el interes
      var pago_capital = parseFloat(this.$("#prestamos_cuotas_pagos_capital").val());
      if (isNaN(pago_capital)) pago_capital = 0;
      var pago_interes = parseFloat(this.$("#prestamos_cuotas_pagos_interes").val());
      if (isNaN(pago_interes)) pago_interes = 0;
      this.$("#prestamos_cuotas_pagos_monto").val(Number(pago_capital + pago_interes).toFixed(2));
    },
        
    addAll : function () {
      this.total_pagado = 0;
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
      $("#prestamos_cuotas_pagos_total_pagado").val(Number(this.total_pagado).toFixed(2));
    },
        
    addOne : function ( item ) {
      var self = this;
      this.total_pagado += parseFloat(item.get("monto"));
      var view = new app.views.PrestamosCuotasPagosItemResultados({
        model: item,
        collection: self.collection,
        view: self.view,
      });
      this.$(".tbody").append(view.render().el);        
    },

    agregar_pago: function() {
      var fecha = this.$("#prestamos_cuotas_pagos_fecha").val();
      if (isEmpty(fecha)) {
        alert("Por favor ingrese una fecha");
        this.$("#prestamos_cuotas_pagos_fecha").focus();
        return;
      }
      var monto = this.$("#prestamos_cuotas_pagos_monto").val();
      if (isNaN(monto)) monto = 0;
      if (isEmpty(monto)) {
        alert("Por favor ingrese un monto");
        this.$("#prestamos_cuotas_pagos_monto").focus();
        return;
      }

      var capital = this.$("#prestamos_cuotas_pagos_capital").val();
      if (isNaN(capital)) capital = 0;
      if (isEmpty(capital)) {
        alert("Por favor ingrese un valor");
        this.$("#prestamos_cuotas_pagos_capital").focus();
        return;
      }

      var interes = this.$("#prestamos_cuotas_pagos_interes").val();
      if (isNaN(interes)) interes = 0;
      if (isEmpty(interes)) {
        alert("Por favor ingrese un valor");
        this.$("#prestamos_cuotas_pagos_interes").focus();
        return;
      }

      var id_sucursal = ID_SUCURSAL;
      if (this.$("#prestamos_cuotas_pagos_sucursales").length > 0) {
        id_sucursal = this.$("#prestamos_cuotas_pagos_sucursales").val();
      }

      var descuento = this.$("#prestamos_cuotas_pagos_descuento").val();
      if (isNaN(descuento)) descuento = 0;

      var m = new app.models.PrestamoCuotaPago({
        id: 0,
        fecha: fecha,
        monto: monto,
        descuento: descuento,
        cancelacion_capital: capital,
        cancelacion_interes: interes,
        id_sucursal: id_sucursal,
      });
      console.log(m);
      this.collection.add(m);
    },
            
  });

})(app);

(function ( app ) {
  app.views.PrestamosCuotasPagosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#prestamos_cuotas_pagos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .borrar_pago":"borrar_pago",
      "click .eliminar_pago":"eliminar_pago",
      "click .imprimir_pago":"imprimir_pago",
      "click .facturar_pago":"facturar_pago",
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.view = options.view;
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    eliminar_pago: function() {
      this.model.destroy();  // Eliminamos el modelo
      $(this.el).remove();  // Lo eliminamos de la vista
    },
    borrar_pago: function() {
      if (!confirm("Realmente desea eliminar el pago?")) return;
      var self = this;
      var url = "pres_cajas_movimientos/function/borrar_pago/";
      url+=this.model.id+"/";
      url+=this.model.get("id_cuota")+"/";
      url+=this.model.get("id_sucursal")+"/";
      $.ajax({
        "url":url,
        "dataType":"json",
        "success":function(r) {
          location.reload();
        }
      });
    },
    imprimir_pago: function() {
      console.log(this.model);
      window.open("pres_cajas_movimientos/function/imprimir_pago/"+this.model.id+"/"+this.model.get("id_cuota")+"/"+this.model.get("id_sucursal"),"_blank");
    },
    facturar_pago: function(e) {
      var self = this;
      if ($(e.currentTarget).hasClass("active")) {
        window.open("app/#comprobante/"+self.model.get("id_factura")+"/"+self.model.get("id_punto_venta"),"_blank");
        return;
      }
      if (!confirm("Desea realizar una factura para esta cuota?")) return;
      var v = new app.views.PrestamoFacturarCuotaView({
        model:new app.models.AbstractModel({
          "id_prestamo":self.model.get("id_prestamo"),
          "id_cuota":self.model.get("id_cuota"),
          "id_sucursal":self.model.get("id_sucursal"),
          "id_pago":self.model.id,
        }),
      });
      crearLightboxHTML({
        "html":v.el,
        "width":480,
        "height":140,
      });      
    },    
  });
})(app);




(function ( models ) {
  models.EstadoLaboral = Backbone.Model.extend({
    urlRoot: "estados_laborales",
    defaults: {
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      id_estado_laboral: 0,
      fecha_inicio: "",
      fecha_fin: "",
      observaciones: "",
      telefono_1: "",
      telefono_2: "",
      orden: 0,
      ingreso: "",
      eliminado: 0,
      empresa: "",
      empresa_direccion: "",
      empresa_cuit: "",
      empresa_seccion: "",
      empresa_cargo: "",
      empresa_horario: "",
      empresa_legajo: "",
      institucion: "",
      numero_beneficio: "",
      categoria_monotributo: "",
    },
  });
})( app.models );

(function (collections, model) {
  collections.EstadosLaborales = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.EstadoLaboral);

(function ( app ) {

  app.views.EstadosLaboralesTableView = app.mixins.View.extend({

    template: _.template($("#estados_laborales_resultados_template").html()),
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_maquina = (typeof this.options.id_maquina != "undefined") ? this.options.id_maquina : 0;
      this.view = options.view;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      if (item.get("eliminado") == 0) {
        var view = new app.views.EstadosLaboralesItemResultados({
          model: item,
          collection: self.collection,
          view: self.view,
        });
        this.$(".tbody").append(view.render().el);        
      }
    },
            
  });

})(app);

(function ( app ) {
  app.views.EstadosLaboralesItemResultados = app.mixins.View.extend({
        
    template: _.template($("#estados_laborales_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.set({
            "eliminado":1,
          })
          //this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.EstadoLaboralEditView({
        model:self.model,
        collection:self.collection,
        view: self.view,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":600,
        "height":140,
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
      this.view = options.view;
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);


(function ( app ) {

  app.views.EstadoLaboralEditView = app.mixins.View.extend({

    template: _.template($("#estado_laboral_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "change #estado_laboral_tipos_estados_laborales":"cambiar_estados_laborales",
    },
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      var edicion = false;
      var obj = { "id":this.model.id }
      obj.edicion = (PERFIL == 1181) ? false : true;
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      this.view = options.view;

      var fecha_inicio = this.model.get("fecha_inicio");
      if (isEmpty(fecha_inicio)) fecha_inicio = new Date();
      createdatepicker($(this.el).find("#estado_laboral_fecha_inicio"),fecha_inicio);

      var fecha_fin = this.model.get("fecha_fin");
      if (isEmpty(fecha_fin)) fecha_fin = new Date();
      createdatepicker($(this.el).find("#estado_laboral_fecha_fin"),fecha_fin);

      this.$("#estado_laboral_tipos_estados_laborales").trigger('change');
    },

    cambiar_estados_laborales: function() {
      this.$(".campos").hide();
      var id_estado_laboral = this.$("#estado_laboral_tipos_estados_laborales").val();
      if (id_estado_laboral == 1) {
        this.$(".campos_relacion_dependencia").show();
      } else if (id_estado_laboral == 2 || id_estado_laboral == 3 || id_estado_laboral == 4) {
        this.$(".campos_monotributo").show();
      } else if (id_estado_laboral == 5 || id_estado_laboral == 6) {
        this.$(".campos_jubilados").show();
      } else {
        this.$(".campos_otros").show();
      }
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");

        this.model.set({
          "fecha_inicio":self.$("#estado_laboral_fecha_inicio").val(),
          "fecha_fin":self.$("#estado_laboral_fecha_fin").val(),
          "id_estado_laboral":self.$("#estado_laboral_tipos_estados_laborales").val(),
        })

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },

    guardar:function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          // NO PONEMOS ID = 0, PORQUE SINO NO AGREGA DOS ELEMENTOS CON EL MISMO ID
          var maxId = 0;
          this.collection.each(function(item){
            if (item.id > maxId) maxId = item.id;
          });
          maxId++;
          this.model.set({id:maxId});
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);


(function ( models ) {
  models.Documentacion = Backbone.Model.extend({
    urlRoot: "documentaciones",
    defaults: {
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      documentacion: "",
      id_documentacion: 0,
      fecha: "",
      observaciones: "",
      path_documentacion: "",
      eliminado: 0,
    },
  });
})( app.models );

(function (collections, model) {
  collections.Documentaciones = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.Documentacion);

(function ( app ) {

  app.views.DocumentacionesTableView = app.mixins.View.extend({

    template: _.template($("#documentaciones_resultados_template").html()),
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.view = options.view;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
        
    addOne : function ( item ) {
      var self = this;
      if (item.get("eliminado") == 0) {
        var view = new app.views.DocumentacionesItemResultados({
          model: item,
          collection: self.collection,
          view: self.view,
        });
        this.$(".tbody").append(view.render().el);
      }
    },
            
  });

})(app);

(function ( app ) {
  app.views.DocumentacionesItemResultados = app.mixins.View.extend({
        
    template: _.template($("#documentaciones_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.set({
            "eliminado":1,
          })
          //this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.DocumentacionEditView({
        model:self.model,
        collection:self.collection,
        view: self.view,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":600,
        "height":140,
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
      this.view = options.view;
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);


(function ( app ) {

  app.views.DocumentacionEditView = app.mixins.View.extend({

    template: _.template($("#documentacion_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
    },
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      var obj = { "id":this.model.id }
      obj.edicion = (PERFIL == 1181) ? false : true;
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      this.view = options.view;

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createdatepicker($(this.el).find("#documentacion_fecha"),fecha);
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");
        this.model.set({
          "path_documentacion": ((self.$("#hidden_path_documentacion").length > 0) ? self.$("#hidden_path_documentacion").val() : ""),
          "fecha":self.$("#documentacion_fecha").val(),
          "id_documentacion":self.$("#documentacion_tipos_documentaciones").val(),
          "documentacion":self.$("#documentacion_tipos_documentaciones option:selected").text(),
        });
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },

    guardar:function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          // NO PONEMOS ID = 0, PORQUE SINO NO AGREGA DOS ELEMENTOS CON EL MISMO ID
          var maxId = 0;
          this.collection.each(function(item){
            if (item.id > maxId) maxId = item.id;
          });
          maxId++;
          this.model.set({id:maxId});
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);






(function ( app ) {

  app.views.PrestamoSimuladorView = app.mixins.View.extend({

    template: _.template($("#prestamo_simulador_template").html()),
            
    myEvents: {
      "change #prestamo_simulador_planes_credito":"cambiar_plan",
      "change #prestamo_simulador_cantidad_cuotas":"calcular_cuota",
      "change #prestamo_simulador_monto_prestado":"calcular_cuota",
      "change #prestamo_simulador_fecha":"calcular_cuota",
      "click .cerrar": "cerrar",
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.view = options.view;
      this.saldo_renovacion = 0;
      this.guardando = 0;
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;

      var obj = { 
        "id":this.model.id,
      }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      if (typeof this.model.id == "undefined") {
        new app.mixins.Select({
          modelClass: app.models.PresPlanCredito,
          url: "pres_planes_credito/",
          render: "#prestamo_simulador_planes_credito",
          fields:["dias_primera_cuota"],
          onComplete:function() {
            self.cambiar_plan();
          }
        });
      }

      createdatepicker($(this.el).find("#prestamo_simulador_fecha"),new Date());
      createdatepicker($(this.el).find("#prestamo_simulador_primera_cuota"),new Date());
    },

    cambiar_plan: function() {
      var self = this;
      var id_plan_credito = this.$("#prestamo_simulador_planes_credito").val();
      $.ajax({
        "url":"pres_planes_credito/"+id_plan_credito,
        "dataType":"json",
        "success":function(r) {
          console.log(r);
          self.$("#prestamo_simulador_cantidad_cuotas").empty();
          for(var i=0;i<r.cuotas.length;i++) {
            var cuota = r.cuotas[i];
            self.$("#prestamo_simulador_cantidad_cuotas").append('<option data-tasa_interes="'+cuota.tasa_interes+'">'+cuota.cuota+'</option>');
          }
          self.calcular_cuota();
        }
      });
    },

    calcular_cuota: function() {
      var monto_prestado = parseFloat(this.$("#prestamo_simulador_monto_prestado").val());
      if (isNaN(monto_prestado)) monto_prestado = 0;
      var cantidad_cuotas = this.$("#prestamo_simulador_cantidad_cuotas").val();
      var tasa_interes = this.$("#prestamo_simulador_cantidad_cuotas option:selected").data("tasa_interes");
      var dias_primera_cuota = this.$("#prestamo_simulador_planes_credito option:selected").data("dias_primera_cuota");
      var cuota = Number(monto_prestado * (tasa_interes / 100)).toFixed(0);
      this.$("#prestamo_simulador_valor_cuota").val(cuota);
      var dias_primera_cuota = this.$("#prestamo_simulador_planes_credito option:selected").data("dias_primera_cuota");
      var fecha = this.$("#prestamo_simulador_fecha").val();
      var primera_cuota = moment(fecha,"DD/MM/YYYY").add(dias_primera_cuota,"days").format("DD/MM/YYYY");
      this.$("#prestamo_simulador_primera_cuota").val(primera_cuota);
    },

  });
})(app);


(function ( app ) {
  app.views.LiquidarCuotasView = app.mixins.View.extend({
    template: _.template($("#liquidar_cuotas_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_sucursal = this.$("#liquidar_cuotas_sucursales").val();
        var saldo = this.$("#liquidar_cuotas_saldo").val();
        if (isNaN(saldo)) {
          alert("Por favor ingrese un numero.");
          return;
        }
        if (!confirm("Desea liquidar las cuotas del prestamo por $"+saldo+"?")) return;
        $.ajax({
          "url":"pres_prestamos/function/liquidar_cuotas/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id_sucursal":id_sucursal,
            "id_prestamo":self.model.get("id_prestamo"),
            "cuotas":self.model.get("cuotas"),
            "saldo":saldo,
          },
          "success":function(res) {
            if (res.error == 0) location.reload();
            else alert(res.mensaje);
          },
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);


(function ( app ) {
  app.views.PrestamoFacturarCuotaView = app.mixins.View.extend({
    template: _.template($("#prestamo_facturar_cuota_template").html()),
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar":function() {
        var self = this;
        var id_sucursal = this.$("#prestamo_facturar_cuota_sucursales").val();
        $.ajax({
          "url":"pres_prestamos/function/facturar_cuota/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id_prestamo":self.model.get("id_prestamo"),
            "id_sucursal":id_sucursal,
            "id_cuota":self.model.get("id_cuota"),
            "id_pago":self.model.get("id_pago"),
          },
          "success":function(r) {
            if (r.error == 1) {
              alert(r.mensaje);
            } else {
              window.open("app/#comprobante/"+r.id_factura+"/"+r.id_punto_venta,"_blank");
              location.reload();
            }
          }
        });
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);


/*
(function ( app ) {
  app.views.CalculadoraPrestamos = app.mixins.View.extend({
    template: _.template($("#calculadora_prestamos_template").html()),
    myEvents: {
      "change #calculadora_prestamos_total_pagar":function(){
        var t = $("#calculadora_prestamos_total_pagar").val();
        var t = parseFloat(t);
        if (isNaN(t)) t = 0;
        window.total_a_pagar = t;
        this.calcular();
      },
      "change #calculadora_prestamos_paga_con":function(){
        var paga = $("#calculadora_prestamos_paga_con").val();
        if (isNaN(paga)) {
          alert("Por favor ingrese un numero");
          return false;
        }
        this.calcular();
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      if (typeof window.id_ultimo_cliente == "undefined") window.id_ultimo_cliente = this.model.get("id_cliente");
      if (window.id_ultimo_cliente != this.model.get("id_cliente")) {
        window.total_a_pagar = 0;
      }
      $(this.el).html(this.template());
    },
    sumar_total: function(total) {
      window.total_a_pagar += parseFloat(total);
      this.calcular();
    },
    calcular: function() {
      var paga = parseFloat($("#calculadora_prestamos_paga_con").val());
      //if (isNaN())
    }
  });
})(app);
*/