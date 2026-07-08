// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PresTarea = Backbone.Model.extend({
    urlRoot: "pres_tareas/",
    defaults: {
      id_banco: 0,
      id_orden_pago: 0,
      banco: "",
      numero: "",
      fecha_emision: $.datepicker.formatDate("dd/mm/yy",new Date()),
      fecha_cobro: $.datepicker.formatDate("dd/mm/yy",new Date()),
      fecha_debitado: $.datepicker.formatDate("dd/mm/yy",new Date()),
      cliente: "",
      id_cliente: 0,
      proveedor: "",
      id_proveedor: 0,
      monto: 0,
      sucursal: "",
      titular: "",
      motivo: "",
      devuelto: 0,
      entregado: 0,
      anulado: 0,
      cuit_titular: "",
      orden_pago: "",
      id_empresa: ID_EMPRESA,
    }
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.PresTareas = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 20,
      order_by: 'C.fecha_emision',
      order: 'desc',
    },
    
    paginator_core: {
      url: "pres_tareas/function/buscar/",
    }

  });

})( app.collections, app.models.PresTarea, Backbone.Paginator);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.PresTareaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#pres_tareas_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .orden_pago": "ver_orden_pago",
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.options = options;
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
      this.permiso = this.options.permiso;
      this.view = this.options.view;
      _.bindAll(this);
    },
    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras pres_tareas que no son el modelo
      var obj = {
        id: self.model.id,
        permiso: self.permiso,
        lightbox: self.lightbox        
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      if (this.lightbox == false) {
        // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
        var self = this;
        var view = new app.views.PresTareaEditView({
          model: self.model,
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":300,
          "callback":function(){
            self.view.buscar();
          }
        });
      } else {
        // Debemos seleccionar un elemento cuando se hace click
        window.pres_tarea = this.model;
        $(".modal").last().trigger("click");
      }
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
    },
    ver_orden_pago : function() {
      var self = this;
      var ordenPago = new app.models.OrdenPago({
        "id": self.model.get("id_orden_pago")
      });
      ordenPago.fetch({
        "success":function() {
          app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
            model: ordenPago
          });
          // Abrimos el lightbox de pagos
          crearLightboxHTML({
            "html":app.views.ordenPagoProveedores.el,
            "width":620,
            "height":565,
          });
        }
      });
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.PresTareasTableView = app.mixins.View.extend({

    template: _.template($("#pres_tareas_panel_template").html()),

    myEvents : {
      "click #tab1_link":"buscar",
      "click #tab2_link":"ver_calendario",
      "change #pres_tareas_mostrar_tipo":function() {
        if ($("#tab2_link").parent().hasClass("active")) this.ver_calendario();
        else this.buscar();
      },
      "change #pres_tareas_calendario_agrupado":"ver_calendario",
      "click .buscar" : "buscar",
      "click .nuevo": function() {
        var self = this;
        var view = new app.views.PresTareaEditView({
          model: new app.models.PresTarea(),
        });
        crearLightboxHTML({
          "html":view.el,
          "width":450,
          "height":300,
          "callback":function(){
            self.buscar();
          }
        });
      },
      "keypress #pres_tareas_texto": function(e){
        if (e.which == 13) this.buscar();
      },
      "change #pres_tareas_fecha_comparacion":function(e) {
        var c = $(e.currentTarget).val();
        if (c==0) {
          this.$("#pres_tareas_desde").attr("disabled","disabled");
          this.$("#pres_tareas_hasta").attr("disabled","disabled");
        } else {
          this.$("#pres_tareas_desde").removeAttr("disabled");
          this.$("#pres_tareas_hasta").removeAttr("disabled");
        }
      }
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;

      // Guardamos las referencias
      this.options = options;
      this.permiso = this.options.permiso;
      this.editView = this.options.editView;
      this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
      window.pres_tareas_page = (typeof window.pres_tareas_page != "undefined") ? window.pres_tareas_page : 1;

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_numeros_pagina : false
        collection: lista,
      });

      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);

      var obj = {
        permiso: this.permiso,
        lightbox: this.lightbox
      };
      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(this.pagination.el);
      //this.buscar();
    },

    ver_calendario: function() {
      var self = this;
      self.cantidad_items = 0;
      var agrupado = this.$("#pres_tareas_calendario_agrupado").val();
      var mostrar_tipo = this.$("#pres_tareas_mostrar_tipo").val();
      this.$("#pres_tareas_calendario").fullCalendar("destroy");
      setTimeout(function(){
        var that = self;
        $(self.el).find("#pres_tareas_calendario").fullCalendar({
          defaultView: 'month',
          header: {
            left: 'title',
            right: 'today prev,next'
          },
          eventRender: function(event,element) {
            var cant = that.cantidad_items;
            $(element).addClass("context-menu");
            $(element).attr("id","context-menu-"+cant);
            $(element).attr("title",event.descripcion);
            $(element).tooltip();

            var items_menu = {
              "ver_pres_tarea": {name: "Editar pres_tarea" },
            };
            if (control.check("cuentas_corrientes_proveedores")>0 && event.id_orden_pago > 0) { 
              items_menu.ver_orden_pago = {
                name: "Ver orden de pago"
              };
            }
            $.contextMenu({
              selector: "#context-menu-"+cant, 
              callback: function(key, options) {
                if (key == "ver_orden_pago") {
                  that.ver_orden_pago(event.id_orden_pago);
                } else if (key == "ver_pres_tarea") {
                  that.ver_pres_tarea(event.id);
                }
              },
              items: items_menu,
            });
            that.cantidad_items++;
          },
          //weekends: false,
          eventSources : [{
            url: "pres_tareas_propios/function/get_by_date/",
            data: {
              "agrupado":agrupado,
              "mostrar_tipo":mostrar_tipo,
            },
          }],
          buttonText : {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'Dia',
          },
          eventStartEditable: false,
          dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
          dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
          monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
          monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
        });
      },50);
    },

    ver_orden_pago: function(id_orden_pago) {
      var orden = new app.models.OrdenPago({
        "id":id_orden_pago
      });
      orden.fetch({
        "success":function(){
          app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
            model: orden
          });
          crearLightboxHTML({
            "html":app.views.ordenPagoProveedores.el,
            "width":800,
            "height":565,
          });
        }
      });
    },

    ver_pres_tarea: function(id_pres_tarea) {
      var self = this;
      var pres_tarea = new app.models.PresTarea({
        "id":id_pres_tarea
      });
      pres_tarea.fetch({
        "success":function(){
          var view = new app.views.PresTareaEditView({
            model: pres_tarea,
          });
          crearLightboxHTML({
            "html":view.el,
            "width":450,
            "height":300,
            "callback":function() {
              self.ver_calendario();
            },
          });
        }
      });
    },

    buscar : function() {
      var id_banco = this.$("#pres_tareas_bancos").val();
      var filtro = this.$("#pres_tareas_texto").val();
      var mostrar_tipo = this.$("#pres_tareas_mostrar_tipo").val();
      var fecha_comparacion = this.$("#pres_tareas_fecha_comparacion").val();
      if (fecha_comparacion == 0) fecha_comparacion = "";
      else if (fecha_comparacion == "E") fecha_comparacion = "C.fecha_emision";
      else if (fecha_comparacion == "C") fecha_comparacion = "C.fecha_cobro";
      else if (fecha_comparacion == "D") fecha_comparacion = "C.fecha_debitado";
      var desde = this.$("#pres_tareas_desde").val();
      var hasta = this.$("#pres_tareas_hasta").val();
      var server_api = {
        "filter":filtro,
        "id_banco":id_banco,
        "mostrar_tipo":mostrar_tipo,
        "fecha_comparacion":fecha_comparacion,
        "desde":desde,
        "hasta":hasta,
      };        
      this.collection.server_api = server_api;
      this.collection.goTo(window.pres_tareas_page);
    },

    addAll : function () {
      window.pres_tareas_page = this.pagination.getPage();
      console.log(window.pres_tareas_page);
      this.$("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.PresTareaItem({
        model: item,
        view: self,
        permiso: this.permiso,
        editView: this.editView,
        lightbox: this.lightbox,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

  });

})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.PresTareaEditView = app.mixins.View.extend({

    template: _.template($("#pres_tareas_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
      this.view = this.options.view;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras pres_tareas que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id, lightbox:this.lightbox };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      if (!this.lightbox) {
        new app.mixins.Select({
          modelClass: app.models.Cliente,
          url: "clientes/",
          render: "#pres_tareas_clientes",
          name : "id_cliente",
          firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
          selected: this.model.get("id_cliente"),
        });
      }

      createdatepicker($(this.el).find("#pres_tareas_fecha_emision"),self.model.get("fecha_emision"));
      createdatepicker($(this.el).find("#pres_tareas_fecha_cobro"),self.model.get("fecha_cobro"));
      createdatepicker($(this.el).find("#pres_tareas_fecha_debitado"),self.model.get("fecha_debitado"));

      return this;
    },

    validar: function() {
      var self = this;
      try {

        validate_input("pres_tareas_numero",IS_EMPTY,"Por favor, ingrese un numero.");

        if ($(self.el).find("#pres_tareas_bancos").val() == 0) {
          show("Por favor seleccione un banco.");
          $(self.el).find("#pres_tareas_bancos").focus();
          return false;
        }

        validate_input("pres_tareas_monto",NOT_EMPTY_INTEGER_OR_DECIMAL,"Por favor, ingrese un numero.");
        validate_input("pres_tareas_fecha_emision",IS_EMPTY,"Por favor, ingrese una fecha.");
        validate_input("pres_tareas_fecha_cobro",IS_EMPTY,"Por favor, ingrese una fecha.");

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
        if (this.model.id == null || this.model.id == 0) {
          this.model.set({id:0});
        }
        this.model.set({
          id_cliente : $(self.el).find("#pres_tareas_clientes").val(),
          cliente : $(self.el).find("#pres_tareas_clientes option:checked").text(),        
          id_banco : $(self.el).find("#pres_tareas_bancos").val(),
          banco : $(self.el).find("#pres_tareas_bancos option:checked").text(),
          fecha_emision: $(self.el).find("#pres_tareas_fecha_emision").val(),
          fecha_cobro: $(self.el).find("#pres_tareas_fecha_cobro").val(),
          fecha_debitado: $(self.el).find("#pres_tareas_fecha_debitado").val(),
        });

        this.model.save({},{
          success: function(model,response) {
            if (self.lightbox) {
              // Debemos seleccionar un elemento cuando se hace click
              window.pres_tarea = self.model;
            }
            $(".modal").last().trigger("click");
          }
        });
      }
    },
  });
})(app.views, app.models);
