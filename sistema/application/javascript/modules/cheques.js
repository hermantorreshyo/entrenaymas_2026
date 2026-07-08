// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Cheque = Backbone.Model.extend({
    urlRoot: "cheques/",
    defaults: {
      id_banco: 0,
      id_orden_pago: 0,
      id_factura: 0,
      id_caja_origen: 0,
      id_caja_depositado: 0,
      banco: "",
      caja_depositado: "",
      caja_origen: "",
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
      comprobante: "",
      id_empresa: ID_EMPRESA,
    }
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Cheques = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 20,
      order_by: 'C.fecha_emision',
      order: 'desc',
    },
    
    paginator_core: {
      url: "cheques/function/buscar/",
    }

  });

})( app.collections, app.models.Cheque, Backbone.Paginator);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.ChequeItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#cheques_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .orden_pago": "ver_orden_pago",
      "click .depositar": "depositar",
      "click .eliminar_deposito":"eliminar_deposito",
      "click .debitar": "debitar",
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
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
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
        var view = new app.views.ChequeEditView({
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
        window.cheque = this.model;
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
            "width":800,
            "height":565,
          });
        }
      });
    },
    eliminar_deposito:function() {
      if (!confirm("Realmente desea eliminar la operacion?")) return;
      var self = this;
      $.ajax({
        "url":"cheques/function/eliminar_deposito/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_cheque":self.model.id,
        },
        "success":function() {
          self.view.buscar();
        }
      })
    },
    depositar: function() {
      var self = this;
      var view = new app.views.DepositarChequeView({
        model: self.model,
        tipo: "T",
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":300,
        "callback":function(){
          self.view.buscar();
        }
      });
    },
    debitar: function() {
      var self = this;
      var view = new app.views.DepositarChequeView({
        model: self.model,
        titulo: "Debitar de Cuenta",
        tipo: "P",
      });
      crearLightboxHTML({
        "html":view.el,
        "width":450,
        "height":300,
        "callback":function(){
          self.view.buscar();
        }
      });
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.ChequesTableView = app.mixins.View.extend({

    template: _.template($("#cheques_panel_template").html()),

    myEvents : {
      "click .exportar_excel":function() {
        var array = new Array();
        $("#cheques_table tbody tr").each(function(i,e){
          array.push({
            "banco":$.trim($(e).find("td:eq(0) span").html()),
            "numero":$.trim($(e).find("td:eq(1) span").html()),
            "titular":$.trim($(e).find("td:eq(2) span").html()),
            "orden_pago":$.trim($(e).find("td:eq(3) span").html()),
            "proveedor":$.trim($(e).find("td:eq(4) span").html()),
            "emision":$(e).find("td:eq(5) span").html(),
            "cobro":$(e).find("td:eq(6) span").html(),
            "debitado":$(e).find("td:eq(7) span").html(),
            "monto":$(e).find("td:eq(8) span").html(),
          });
        });
        var header = new Array("Banco","Numero","Titular","Orden Pago","Proveedor","Emision","Cobro","Debitado","Monto");
        this.exportar_excel({
          "filename":"listado",
          "title":"Listado de cheques: ",
          "data":array,
          "header":header,
        }); 
      },
      "click #tab1_link":"buscar",
      "click #tab2_link":"ver_calendario",
      "change #cheques_mostrar_tipo":function() {
        if ($("#tab2_link").parent().hasClass("active")) this.ver_calendario();
        else this.buscar();
      },
      "change #cheques_calendario_agrupado":"ver_calendario",
      "click .buscar" : "buscar",
      "change #cheques_tipos":"buscar",
      "click .nuevo_propio": function() {
        var self = this;
        var view = new app.views.ChequeEditView({
          model: new app.models.Cheque({
            "tipo":"P",
          }),
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
      "click .nuevo_tercero": function() {
        var self = this;
        var view = new app.views.ChequeEditView({
          model: new app.models.Cheque({
            "tipo":"T",
          }),
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
      "keypress #cheques_texto": function(e){
        if (e.which == 13) this.buscar();
      },
      "change #cheques_fecha_comparacion":function(e) {
        var c = $(e.currentTarget).val();
        if (c==0) {
          this.$("#cheques_desde").attr("disabled","disabled");
          this.$("#cheques_hasta").attr("disabled","disabled");
        } else {
          this.$("#cheques_desde").removeAttr("disabled");
          this.$("#cheques_hasta").removeAttr("disabled");
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

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista,
      });

      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);

      var obj = {
        permiso: this.permiso,
        lightbox: this.lightbox
      };
      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(pagination.el);

      createdatepicker($(this.el).find("#cheques_desde"));
      createdatepicker($(this.el).find("#cheques_hasta"));

      this.buscar();
    },

    ver_calendario: function() {
      var self = this;
      self.cantidad_items = 0;
      var agrupado = this.$("#cheques_calendario_agrupado").val();
      var mostrar_tipo = this.$("#cheques_mostrar_tipo").val();
      var tipo = this.$("#cheques_tipos").val();
      this.$("#cheques_calendario").fullCalendar("destroy");
      setTimeout(function(){
        var that = self;
        $(self.el).find("#cheques_calendario").fullCalendar({
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
              "ver_cheque": {name: "Editar cheque" },
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
                } else if (key == "ver_cheque") {
                  that.ver_cheque(event.id);
                }
              },
              items: items_menu,
            });
            that.cantidad_items++;
          },
          //weekends: false,
          eventSources : [{
            url: "cheques_propios/function/get_by_date/",
            data: {
              "agrupado":agrupado,
              "mostrar_tipo":mostrar_tipo,
              "tipo":tipo,
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

    ver_cheque: function(id_cheque) {
      var self = this;
      var cheque = new app.models.Cheque({
        "id":id_cheque
      });
      cheque.fetch({
        "success":function(){
          var view = new app.views.ChequeEditView({
            model: cheque,
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
      var id_banco = this.$("#cheques_bancos").val();
      var filtro = this.$("#cheques_texto").val();
      var tipo = this.$("#cheques_tipos").val();
      var titular = this.$("#cheques_titular").val();
      var mostrar_tipo = this.$("#cheques_mostrar_tipo").val();
      var id_sucursal = ((this.$("#cheques_sucursales").length > 0) ? this.$("#cheques_sucursales").val() : 0);
      var fecha_comparacion = this.$("#cheques_fecha_comparacion").val();
      if (fecha_comparacion == 0) fecha_comparacion = "";
      else if (fecha_comparacion == "E") fecha_comparacion = "C.fecha_emision";
      else if (fecha_comparacion == "C") fecha_comparacion = "C.fecha_cobro";
      else if (fecha_comparacion == "D") fecha_comparacion = "C.fecha_debitado";
      var desde = this.$("#cheques_desde").val();
      var hasta = this.$("#cheques_hasta").val();
      var server_api = {
        "filter":filtro,
        "id_banco":id_banco,
        "id_sucursal":id_sucursal,
        "mostrar_tipo":mostrar_tipo,
        "fecha_comparacion":fecha_comparacion,
        "desde":desde,
        "hasta":hasta,
        "tipo":tipo,
        "titular":titular,
      };        
      this.collection.server_api = server_api;
      this.collection.pager();      
    },

    addAll : function () {
      this.$("tbody").empty();
      this.collection.each(this.addOne);
      var total = this.collection.meta("total");
      this.$("#cheques_total").html("$ "+Number(total).toFixed(2));
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ChequeItem({
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

  views.ChequeEditView = app.mixins.View.extend({

    template: _.template($("#cheques_edit_panel_template").html()),

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
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
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
          render: "#cheques_clientes",
          name : "id_cliente",
          firstOptions: ["<option value='0'>Seleccione una opcion</option>"],
          selected: this.model.get("id_cliente"),
        });
      }

      createdatepicker($(this.el).find("#cheques_fecha_emision"),self.model.get("fecha_emision"));
      createdatepicker($(this.el).find("#cheques_fecha_cobro"),self.model.get("fecha_cobro"));
      createdatepicker($(this.el).find("#cheques_fecha_debitado"),self.model.get("fecha_debitado"));

      return this;
    },

    validar: function() {
      var self = this;
      try {

        validate_input("cheques_numero",IS_EMPTY,"Por favor, ingrese un numero.");

        if ($(self.el).find("#cheques_bancos").val() == 0) {
          show("Por favor seleccione un banco.");
          $(self.el).find("#cheques_bancos").focus();
          return false;
        }

        validate_input("cheques_monto",NOT_EMPTY_INTEGER_OR_DECIMAL,"Por favor, ingrese un numero.");
        validate_input("cheques_fecha_emision",IS_EMPTY,"Por favor, ingrese una fecha.");
        validate_input("cheques_fecha_cobro",IS_EMPTY,"Por favor, ingrese una fecha.");

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
          id_cliente : $(self.el).find("#cheques_clientes").val(),
          cliente : $(self.el).find("#cheques_clientes option:checked").text(),        
          id_banco : $(self.el).find("#cheques_bancos").val(),
          banco : $(self.el).find("#cheques_bancos option:checked").text(),
          fecha_emision: $(self.el).find("#cheques_fecha_emision").val(),
          fecha_cobro: $(self.el).find("#cheques_fecha_cobro").val(),
          fecha_debitado: $(self.el).find("#cheques_fecha_debitado").val(),
        });

        this.model.save({},{
          success: function(model,response) {
            if (self.lightbox) {
              // Debemos seleccionar un elemento cuando se hace click
              window.cheque = self.model;
            }
            $(".modal").last().trigger("click");
          }
        });
      }
    },
  });
})(app.views, app.models);





(function ( views, models ) {

  views.DepositarChequeView = app.mixins.View.extend({

    template: _.template($("#depositar_cheque_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.view = this.options.view;
      this.titulo = (typeof options.titulo != "undefined" ? options.titulo : "Depositar Cheque");
      this.tipo = (typeof options.tipo != "undefined" ? options.tipo : "T");
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id, titulo: self.titulo };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      createdatepicker($(this.el).find("#depositar_cheque_fecha_debitado"),self.model.get("fecha_debitado"));
      return this;
    },

    guardar: function() {
      var self = this;
      $.ajax({
        "url":"cheques/function/depositar/",
        "type":"post",
        "data":{
          "tipo":self.tipo,
          "id":self.model.id,
          "fecha_debitado": self.$("#depositar_cheque_fecha_debitado").val(),
          "id_caja_depositado": self.$("#depositar_cheque_cajas").val(),
        },
        "dataType":"json",
        "success": function(r) {
          if (r.error == 1) alert(r.mensaje);
          else $(".modal").last().trigger("click");
        }
      });
    },
  });
})(app.views, app.models);
