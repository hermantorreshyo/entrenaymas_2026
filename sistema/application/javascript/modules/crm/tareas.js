// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Tarea = Backbone.Model.extend({
    urlRoot: "tareas/",
    defaults: {
      id_contacto: 0,
      nombre: "",
      id_asunto: 0,
      asunto: "",
      fecha: "",
      texto: "",
      id_empresa: ID_EMPRESA,
      estado: 0,
    }
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Tareas = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 20,
      order_by: 'C.fecha_emision',
      order: 'desc',
    },
    
    paginator_core: {
      url: "tareas/function/buscar/",
    }

  });

})( app.collections, app.models.Tarea, Backbone.Paginator);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.TareasItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#tareas_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
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
        var view = new app.views.TareaEditView({
          model: self.model,
        });
        crearLightboxHTML({
          "html":view.el,
          "width":650,
          "height":300,
          "callback":function(){
            self.view.buscar();
          }
        });
      } else {
        // Debemos seleccionar un elemento cuando se hace click
        window.tarea = this.model;
        $(".modal").last().trigger("click");
      }
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
    },
    /*
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
    },*/
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.TareasTableView = app.mixins.View.extend({

    template: _.template($("#tareas_panel_template").html()),

    myEvents : {
      "click .buscar" : function(){
        $("#tareas_calendario").fullCalendar('refetchEvents');
      },
      "click .nuevo": function() {
        var self = this;
        var view = new app.views.TareaEditView({
          model: new app.models.Tarea(),
        });
        crearLightboxHTML({
          "html":view.el,
          "width":650,
          "height":300,
          "escapable":false,
          "callback":function(){
            $("#tareas_calendario").fullCalendar('refetchEvents');
          }
        });
      },
      "keypress #tareas_texto": function(e){
        if (e.which == 13) this.buscar();
      },
      "change #tareas_fecha_comparacion":function(e) {
        var c = $(e.currentTarget).val();
        if (c==0) {
          this.$("#tareas_desde").attr("disabled","disabled");
          this.$("#tareas_hasta").attr("disabled","disabled");
        } else {
          this.$("#tareas_desde").removeAttr("disabled");
          this.$("#tareas_hasta").removeAttr("disabled");
        }
      }
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;

      window.tareas_page = (typeof window.tareas_page != "undefined") ? window.tareas_page : 1;

      // Guardamos las referencias
      this.options = options;
      this.permiso = this.options.permiso;
      this.editView = this.options.editView;
      this.lightbox = (this.options.lightbox != undefined) ? this.options.lightbox : false;
      var obj = {
        permiso: this.permiso,
        lightbox: this.lightbox
      };
      $(this.el).html(this.template(obj));

      var input = this.$("#tareas_buscar");
      if (ID_EMPRESA == 228) {
        $(input).customcomplete({
          "url":"/sistema/pres_clientes/function/get_by_descripcion/",
          "form":null, // No quiero que se creen nuevos productos
          "width":400,
          "closable":false,
          "disableNumber":false,
          "info":"descripcion",
          "image_field":"path",
          "image_path":"/sistema",
          "onSelect":function(item){
            var that = self;
            var cliente = new app.models.PresCliente({"id":item.id});
            cliente.fetch({
              "success":function(){
                that.seleccionar_cliente(cliente);
              },
            });
          }
        });
      } else {
        $(input).customcomplete({
          "url":"clientes/function/get_by_nombre/",
          "form":null, // No quiero que se creen nuevos productos
          "width":"300px",
          "onSelect":function(item){
            var cliente = new app.models.Cliente({"id":item.id});
            cliente.fetch({
              "success":function(){
                self.seleccionar_cliente(cliente);
              },
            });
          }
        });
      }
      this.buscar();
    },

    buscar: function() {
      this.ver_calendario();
      this.ver_tareas_vencidas();
    },

    seleccionar_cliente: function(r) {
      this.$("#tareas_id_contacto").val(r.id);
      if (typeof r.get("apellido") != "undefined") {
        this.$("#tareas_buscar").val(r.get("apellido")+" "+r.get("nombre"));  
      } else {
        this.$("#tareas_buscar").val(r.get("nombre"));
      }
    },

    ver_tareas_vencidas: function() {
      var self = this;
      $.ajax({
        "url":"tareas/function/vencidas/",
        "dataType":"json",
        "success":function(r) {
          self.$("#tareas_vertical").empty();
          for(var i=0; i<r.results.length;i++) {
            var t = r.results[i];
            var tarea = new app.models.Tarea(t);
            var item = new app.views.TareaVerticalItemView({
              model: tarea,
              view: self,
            });
            self.$("#tareas_vertical").append(item.el);
          }
        }
      });
    },

    ver_calendario: function() {
      var self = this;
      self.cantidad_items = 0;
      this.$("#tareas_calendario").fullCalendar("destroy");
      setTimeout(function(){
        var that = self;
        $(self.el).find("#tareas_calendario").fullCalendar({
          defaultView: 'agendaWeek',
          allDaySlot: false,
          header: {
            left: 'agendaWeek,month,listWeek',
            center: 'title',
            right: 'prev,next today'
          },
          selectable: true,
          eventStartEditable: true,
          eventRender: function(event, element, view) {
            if (view.type == "listWeek") {
              // Si es la lista de eventos
              if (event.estado == 0 && event.start.isBefore(moment()) ) {
                var label = '<span class="label bg-danger inline m-l-sm pr t-2">Vencida</span>';
              } else{
                var label = (event.estado == 1) ? '<span class="label bg-success inline m-l-sm pr t-2">Realizado</span>' : '<span class="label bg-warning inline m-l-sm pr t-2">Pendiente</span>';
              }
              $(element).find(".fc-list-item-title a").after(label);
              $(element).find(".fc-list-item-title a").tooltip({title: event.texto});
            } else {
              if (event.estado == 0 && event.start.isBefore(moment()) ) {
                // Controlamos si la tarea esta vencida
                $(element).css("borderColor","#e06559");
                $(element).css("backgroundColor","#e06559");
              } else if (event.estado == 1) {
                // Si la tarea fue hecha
                $(element).css("opacity",0.5);
              }
              $(element).tooltip({title: event.texto});
            }
          },
          eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
            var nueva = moment(event.desde).add(delta);
            $.ajax({
              "url":"tareas/function/cambiar_fecha/",
              "dataType":"json",
              "type":"post",
              "data": {
                "id":event.id,
                "fecha":nueva.format("YYYY-MM-DD"),
                "hora":nueva.format("HH:mm:SS"),
              },
            });
          },
          eventClick: function(calEvent, jsEvent, view) {
            var that = self;
            var modelo = new app.models.Tarea({
              "id":calEvent.id,
            });
            modelo.fetch({
              "success":function() {                    
                app.views.tarea = new app.views.TareaEditView({
                  "model":modelo,
                });
                crearLightboxHTML({
                  "html":app.views.tarea.el,
                  "width":650,
                  "height":140,
                  "escapable":false,
                  "callback":function() {
                    $("#tareas_calendario").fullCalendar('refetchEvents');
                  }
                });
              }
            });
          },
          events: function(start, end, timezone, callback) {
            var that = self;
            $.ajax({
              url: "tareas/function/get_by_date/",
              dataType: 'json',
              data: {
                "start": start.format("YYYY-MM-DD"),
                "end": end.format("YYYY-MM-DD"),
                "id_usuario":((SOLO_USUARIO == 1) ? ID_USUARIO : that.$("#tareas_usuarios").val()),
                "estado":that.$("#tareas_estados").val(),
                "id_contacto":((isEmpty(that.$("#tareas_buscar").val())) ? 0 : that.$("#tareas_id_contacto").val()),
              },
              success: function(res) {
                callback(res);
              }
            });
          },
          buttonText : {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'Dia',
            list:     'Lista', 
          },
          timezone: "local",
          dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
          dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
          monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
          monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
        });
      },50);
    },

    ver_tarea: function(id_tarea) {
      var self = this;
      var tarea = new app.models.Tarea({
        "id":id_tarea
      });
      tarea.fetch({
        "success":function(){
          var view = new app.views.TareaEditView({
            model: tarea,
          });
          crearLightboxHTML({
            "html":view.el,
            "width":650,
            "height":300,
            "callback":function() {
              self.buscar();
            },
          });
        }
      });
    },
  });

})(app);




(function ( app ) {

  app.views.TareaVerticalItemView = app.mixins.View.extend({

    template: _.template($("#tareas_item_vertical").html()),
    className: "sl-item",
    myEvents: {
      "click":"ver_tarea",
    },
    ver_tarea: function() {
      var self = this;
      var tarea = new app.models.Tarea({
        "id":self.model.id,
      });
      tarea.fetch({
        "success":function(){
          var view = new app.views.TareaEditView({
            model: tarea,
          });
          crearLightboxHTML({
            "html":view.el,
            "width":650,
            "height":300,
            "callback":function() {
              self.view.buscar();
            },
          });
        }
      });
    },
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.view = options.view;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
        
  });

})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.TareaEditView = app.mixins.View.extend({

    template: _.template($("#tareas_edit_panel_template").html()),

    myEvents: {
      "click .nuevo_cliente": "nuevo_cliente",
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "click .realizada": function() {
        this.model.set({
          "estado":1,
        });
        this.guardar();
      },
      "click .no_realizada": function() {
        this.model.set({
          "estado":0,
        });
        this.guardar();
      },
      "click .cerrar":"cerrar",
      "click .agregar_asunto":function(e) {
        var self = this;
        if ($(".asunto_edit_mini").length > 0) return;
        var form = new app.views.AsuntoMiniEditView({
          "model": new app.models.Asunto(),
          "callback":function(m){
            self.model.set({ "id_asunto":m });
            self.cargar_asuntos();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete asunto_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#asuntos_mini_nombre").focus();
      },
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
      var obj = { id:this.model.id, lightbox:this.lightbox };
      $.extend(obj,this.model.toJSON());
      console.log(obj)
      $(this.el).html(this.template(obj));

      var f = this.model.get("fecha");
      if (isEmpty(f)) f = new Date();
      else f = moment(f,"DD/MM/YYYY HH:mm:ss").toDate()
      createtimepicker($(this.el).find("#tarea_fecha"),f);

      var f = this.model.get("fecha_visto");
      if (isEmpty(f)) f = new Date();
      else f = moment(f,"DD/MM/YYYY HH:mm:ss").toDate()
      createtimepicker($(this.el).find("#tarea_fecha_visto"),f);

      this.cargar_asuntos();

      var input = this.$("#tarea_cliente");
      if (ID_EMPRESA == 228) {
        $(input).customcomplete({
          "url":"/sistema/pres_clientes/function/get_by_descripcion/",
          "form":null, // No quiero que se creen nuevos productos
          "width":400,
          "closable":false,
          "disableNumber":false,
          "info":"descripcion",
          "image_field":"path",
          "image_path":"/sistema",
          "onSelect":function(item){
            var that = self;
            var cliente = new app.models.PresCliente({"id":item.id});
            cliente.fetch({
              "success":function(){
                that.seleccionar_cliente(cliente);
              },
            });
          }
        });
      } else {
        var form = new app.views.ClienteEditViewMini({
          "model": new app.models.Cliente(),
          "input": input,
          "onSave": self.seleccionar_cliente,
        });      
        $(input).customcomplete({
          "url":"clientes/function/get_by_nombre/",
          "form":form,
          "width":"300px",
          "onSelect":function(item){
            var cliente = new app.models.Cliente({"id":item.id});
            cliente.fetch({
              "success":function(){
                self.seleccionar_cliente(cliente);
              },
            });
          }
        });
      }
      return this;
    },

    cargar_asuntos: function() {
      var s = "";
      var id_asunto = this.model.get("id_asunto");
      for(var i=0;i< window.asuntos.length;i++) {
        var o = window.asuntos[i]; 
        s+='<option '+((o.id==id_asunto)?"selected":"")+' value="'+o.id+'">'+o.nombre+'</option>';
      }
      this.$("#tarea_asuntos").html(s);
    },

    seleccionar_cliente: function(r) {
      this.$("#tarea_id_contacto").val(r.id);
      window.objeto = r;
      if (typeof r.get("apellido") != "undefined") {
        this.$("#tarea_cliente").val(r.get("apellido")+" "+r.get("nombre"));  
      } else {
        this.$("#tarea_cliente").val(r.get("nombre"));
      }
      this.model.set({
        "id_contacto":r.id,
        "nombre":r.nombre,
      })
    },

    nuevo_cliente: function() {
      var self = this;
      var c = new app.views.ClienteEditViewMini({
        model: new app.models.Cliente({
          id_tipo_documento: 80,
          id_tipo_iva: 4,
          id_sucursal: ID_SUCURSAL,
          tipo: 1,
        }),
        onSave: function(cli){
          self.seleccionar_cliente(cli);
          $('.modal:last').modal('hide');
        }
      });
      crearLightboxHTML({
        "html":c.el,
        "width":600,
        "height":500,
      });
      $("#clientes_mini_nombre").focus();
    },

    eliminar: function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      if (confirm("Desea borrar la tarea?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        this.cerrar();
      }
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    guardar: function() {
      var self = this;

      // Controlamos el texto
      var texto = this.$("#tarea_texto").val();
      var id_asunto = this.$("#tarea_asuntos").val();
      var asunto = this.$("#tarea_asuntos option:selected").text();
      var fecha = this.$("#tarea_fecha").val();
      if (isEmpty(fecha)) {
        alert("Por favor ingrese una fecha.");
        this.$("#tarea_fecha").focus();
        return;
      }
      var fecha_visto = this.$("#tarea_fecha_visto").val();
      if (isEmpty(fecha_visto)) {
        alert("Por favor ingrese una fecha.");
        this.$("#tarea_fecha_visto").focus();
        return;
      }
      if (this.model.id == null || this.model.id == 0) {
        this.model.set({
          "id":0,
          "id_usuario":ID_USUARIO,
        });
      }
      this.model.save({
        "texto":texto,
        "tipo":1, // Estamos ENVIANDO
        "id_origen":17, // TAREA
        //"id_contacto": $(self.el).find("#tareas_clientes").val(),
        //"contacto": $(self.el).find("#tareas_clientes option:checked").text(),
        "id_empresa":ID_EMPRESA,
        "id_asunto":id_asunto,
        "asunto":asunto,
        "fecha_visto":moment(fecha_visto,"DD/MM/YYYY HH:mm:ss").format("YYYY-MM-DD HH:mm:ss"),
        "fecha":moment(fecha,"DD/MM/YYYY").format("YYYY-MM-DD"),
        "hora":moment(fecha,"DD/MM/YYYY HH:mm").format("HH:mm:ss"),
      },{
        success: function(model,response) {
          if (self.lightbox) {
            // Debemos seleccionar un elemento cuando se hace click
            window.tarea = self.model;
          }
          self.cerrar();
        }
      })
    },
  });
})(app.views, app.models);







(function ( app ) {

  app.views.TareasListadoView = app.mixins.View.extend({

    template: _.template($("#tareas_listado_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #tareas_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
      "change #tareas_sucursales":"buscar",
      "change #tareas_usuarios":"buscar",
      "change #tareas_buscar":"buscar",
      "change #tareas_desde":"buscar",
      "change #tareas_hasta":"buscar",
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      var search = new app.mixins.SearchView({
        collection: lista
      });

      window.tareas_listado_fecha_desde = (typeof window.tareas_listado_fecha_desde != "undefined") ? window.tareas_listado_fecha_desde : moment().toDate();
      window.tareas_listado_fecha_hasta = (typeof window.tareas_listado_fecha_hasta != "undefined") ? window.tareas_listado_fecha_hasta : moment().toDate();
      window.tareas_filtro_fecha = (typeof window.tareas_filtro_fecha != "undefined") ? window.tareas_filtro_fecha : 0;
      window.tareas_id_usuario = (typeof window.tareas_id_usuario != "undefined") ? window.tareas_id_usuario : 0;
      window.tareas_page = (typeof window.tareas_page != "undefined") ? window.tareas_page : 1;

      this.collection.on('sync', this.addAll, this);

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      $(this.el).find(".search_container").html(search.el);
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker(this.$("#tareas_desde"),window.tareas_listado_fecha_desde);
      createdatepicker(this.$("#tareas_hasta"),window.tareas_listado_fecha_hasta);

      this.buscar();
    },

    buscar: function() {
      if (window.tareas_filter != this.$("#tareas_buscar").val().trim()) {
        window.tareas_filter = this.$("#tareas_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (window.tareas_id_sucursal != this.$("#tareas_sucursales").val()) {
        window.tareas_id_sucursal = this.$("#tareas_sucursales").val();
        this.cambio_parametros = true;
      }
      if (window.tareas_id_usuario != this.$("#tareas_usuarios").val()) {
        window.tareas_id_usuario = this.$("#tareas_usuarios").val();
        this.cambio_parametros = true;
      }
      /*if (this.cambio_parametros) {
        window.tareas_page = 1;
        this.cambio_parametros = false;
      }*/

      if (this.$("#tareas_desde").length > 0 && window.tareas_listado_fecha_desde != this.$("#tareas_desde").val().trim()) {
        window.tareas_listado_fecha_desde = this.$("#tareas_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#tareas_hasta").length > 0 && window.tareas_listado_fecha_hasta != this.$("#tareas_hasta").val().trim()) {
        window.tareas_listado_fecha_hasta = this.$("#tareas_hasta").val().trim();
        cambio_parametros = true;
      }
      if (window.tareas_filtro_fecha != this.$("#tareas_filtro_fecha").val()) {
        window.tareas_filtro_fecha = this.$("#tareas_filtro_fecha").val();
        this.cambio_parametros = true;
      }

      var filtros = {
        "filter":encodeURIComponent(window.tareas_filter),
        "id_sucursal":window.tareas_id_sucursal,
        "id_usuario":window.tareas_id_usuario,
      };
      filtros.desde = (isEmpty(window.tareas_listado_fecha_desde)) ? "" : window.tareas_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.tareas_listado_fecha_hasta)) ? "" : window.tareas_listado_fecha_hasta.replace(/\//g,"-");
      filtros.filtro_fecha = window.tareas_filtro_fecha;
      this.collection.server_api = filtros;
      this.collection.goTo(window.tareas_page);
    },

    addAll : function () {
      window.tareas_page = this.pagination.getPage();
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.TareasItem({
        model: item,
        permiso: this.permiso,
        view: self,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);