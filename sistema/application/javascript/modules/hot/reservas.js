// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Reserva = Backbone.Model.extend({
    urlRoot: "reservas",
    defaults: {
      huespedes: [],
      id_habitacion: 0,
      habitacion: "",
      cliente: {
        "id":0,
        "nombre":"",
        "telefono":"",
        "email":"",
      },
      tipo_habitacion: {
        "compartida":0,
        "capacidad_maxima":0,
      },
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      fecha_desde: "",
      fecha_hasta: "",
      precio: 0,
      comentario: "",
      id_estado: 0,
      personas: 1,
      estado: "",
      numero: 0,
    },
  });
	  
})( app.models );



(function (collections, model, paginator) {

  collections.Reservas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },    
    paginator_core: {
      url: "reservas/function/buscar/",
    },
  });

})( app.collections, app.models.Reserva, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ReservasTableView = app.mixins.View.extend({

    template: _.template($("#reservas_template").html()),
      
    myEvents: {
      "click .eliminar_reserva":function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var id = $(e.currentTarget).data("id");
        var modelo = new app.models.Reserva({
          "id":id,
        });
        modelo.destroy({
          "success":function(){
            $('#calendar').fullCalendar('refetchEvents');   
          }
        });
        return false;
      },
    },
    
    initialize : function (options) {
      
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
      }));
      
      setTimeout(function(){
        self.render();
      },200);
    },
    
    render: function() {

      var self = this;
      self.dayClick = false;
      var todayDate = moment().startOf('day');
      var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
      var TODAY = todayDate.format('YYYY-MM-DD');
      var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

      $('#calendar').fullCalendar({
        locale: "es",
        resourceAreaWidth: 230,
        editable: true,
        aspectRatio: 1.8,
        scrollTime: '00:00',
        customButtons: {
          promptResource: {
            text: '+ room',
            click: function() {
              var title = prompt('Room name');
              if (title) {
                $('#calendar').fullCalendar(
                  'addResource',
                  { title: title },
                  true // scroll to the new resource?
                );
              }
            }
          }
        },
        eventRender: function(event, element, view) {
          if (event.id_estado == 0) {
            $(element).css("backgroundColor","#e06559");  
            $(element).css("borderColor","#e06559");  
          } else if (event.id_estado == 2) {
            $(element).css("backgroundColor","#28b492");  
            $(element).css("borderColor","#28b492");  
          }
          $(element).find(".fc-time").hide();
        },
        selectable: true,
        select: function( start, end, jsEvent, view, resource) {
          if (self.dayClick) { self.dayClick = false; return; }
          var fin = end.clone();
          //fin.subtract(1,'days'); // Le restamos un dia a la fecha
          var modelo = new app.models.Reserva({
            fecha_desde: start.format("DD/MM/YYYY"),
            fecha_hasta: fin.format("DD/MM/YYYY"),
            id_habitacion: resource.id,
          });
          var view = new app.views.ReservaView({
            "model":modelo
          });
          crearLightboxHTML({
            "html":view.el,
            "width":700,
            "height":140,
          });
        },
        dayClick: function(date, jsEvent, view, resource) {
          var modelo = new app.models.Reserva({
            fecha_desde: date.format("DD/MM/YYYY"),
            fecha_hasta: date.format("DD/MM/YYYY"),
            id_habitacion: resource.id,
          });
          modelo.save({},{
            success: function(model,response) {
              $('#calendar').fullCalendar('refetchEvents');
            }
          });
          self.dayClick = true;
        },
        eventClick: function(calEvent, jsEvent, view) {
          var self = this;
          // Creamos un modelo con esos datos
          var modelo = new app.models.Reserva({
            id: calEvent.id,
            fecha: calEvent.start.format("DD/MM/YYYY"),
            id_empresa: calEvent.id_empresa,
          });
          modelo.fetch({
            "success":function(){
              var view = new app.views.ReservaView({
                "model":modelo
              });
              crearLightboxHTML({
                "html":view.el,
                "width":700,
                "height":140,
              });              
            }
          })
        },
        header: {
          left: 'today prev,next',
          center: 'title',
          right: 'timelineMyWeek,timeline2Weeks,timelineMonth'
        },
        defaultView: 'timeline2Weeks',
        views: {
          timelineMyWeek: {
            type: 'timelineWeek',
            slotDuration: '24:00:00',
          },
          timeline2Weeks: {
            type: 'timelineWeek',
            slotDuration: '24:00:00',
            duration: { days: 15 },
            buttonText: 'Quincena'
          },
        },
        resourceColumns: [
          {
            labelText: 'Habitacion',
            field: 'nombre'
          },
          {
            labelText: 'Tipo',
            field: 'tipo'
          }
        ],
        //resourceLabelText: '',
        resources: {
          url: '/sistema/habitaciones/function/get_all_min/',
        },
        events: {
          url: '/sistema/reservas/function/calendario/',
        },
      });
      this.$("#calendar").fullCalendar('render');
    },
    
  });

})(app);


(function ( app ) {

  app.views.ReservaView = app.mixins.View.extend({

    template: _.template($("#reserva_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "click .imprimir": "imprimir",
      "change #reserva_personas": "render_huespedes",
    },
		
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      var fecha_desde = (isEmpty(this.model.get("fecha_desde"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha_desde");
      createdatepicker(this.$("#reserva_fecha_desde"),fecha_desde);

      var fecha_hasta = (isEmpty(this.model.get("fecha_hasta"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha_hasta");
      createdatepicker(this.$("#reserva_fecha_hasta"),fecha_hasta);

      new app.mixins.Select({
        modelClass: app.models.Habitacion,
        url: "habitaciones/",
        render: "#reserva_habitaciones",
        selected: self.model.get("id_habitacion"),
      });

      var input = this.$("#reserva_clientes");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":null,
        "width":"300px",
        "onSelect":function(item){
          $("#reserva_id_cliente").val(item.id);
          $("#reserva_clientes").val(item.nombre);
          $("#reserva_cliente_email").val(item.email);
          $("#reserva_cliente_telefono").val(item.telefono);
        }
      });

      this.render_huespedes();
    },

    render_huespedes: function() {
      var cantidad = this.$("#reserva_personas").val();
      this.$("#huespedes_tabla tbody").empty();
      var huespedes = this.model.get("huespedes");
      for(var i=0;i<cantidad;i++) {

        if (typeof huespedes[i] != "undefined") {
          var huesped = huespedes[i];
        } else {
          var huesped = {
            nombre: "",
            dni: "",
          };
        }
        var tr = "<tr>";
        tr+= "<td>"+(i+1)+"</td>";
        tr+= "<td><input type='text' value='"+huesped.nombre+"' class='form-control no-model huesped' /></td>";
        tr+= "<td><input type='text' value='"+huesped.dni+"' class='form-control no-model huesped' /></td>";
        tr+="</tr>";
        this.$("#huespedes_tabla tbody").append(tr);
      }
    },

    nuevo_cliente: function() {
      var self = this;
      var c = new app.views.ClienteEditViewMini({
        model: new app.models.Cliente({
          id_tipo_documento: 80,
        }),
        onSave: function(cli){
          self.model.set({
            "cliente":cli,
          })
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
      
    validar: function() {
      try {
        var self = this;
        this.model.set({
          "id_habitacion":self.$("#reserva_habitaciones").val(),
          "id_estado":self.$("#reserva_estados").val(),
        });

        var cliente = this.model.get("cliente");
        cliente.nombre = self.$("#reserva_clientes").val();
        cliente.email = self.$("#reserva_cliente_email").val();
        cliente.telefono = self.$("#reserva_cliente_telefono").val();
        this.model.set({
          "cliente":cliente,
          "id_cliente":self.$("#reserva_id_cliente").val(),
        });

        return true;
      } catch(e) {
        return false;
      }
    },	
	
    guardar:function() {
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              $('.modal:last').modal('hide');
              $('#calendar').fullCalendar('refetchEvents');
            }
          }
        });
      }	  
    },
    
    eliminar: function() {
      if (!confirm("Realmente desea eliminar esta reserva?")) return;
      $.ajax({
        "url":"reservas/"+this.model.id,
        "type":"delete",
        "success":function() {
          $('.modal:last').modal('hide');
          $('#calendar').fullCalendar('refetchEvents');
        }
      })
    },

    imprimir: function() {
      workspace.imprimir_reporte("/sistema/reservas/function/imprimir/"+this.model.id);
    }
    
  });
})(app);




// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ReservasListadoView = app.mixins.View.extend({

    template: _.template($("#reservas_listado_template").html()),
      
    myEvents: {
      "click .buscar":"buscar",
      "click .nueva_reserva":function(){
        var modelo = new app.models.Reserva({
          fecha_desde: moment().format("DD/MM/YYYY"),
          fecha_hasta: moment().format("DD/MM/YYYY"),
        });
        var view = new app.views.ReservaView({
          "model":modelo
        });
        crearLightboxHTML({
          "html":view.el,
          "width":700,
          "height":140,
        });
      }
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.fecha = (this.options.fecha == undefined) ? "" : (this.options.fecha).replace(/\//g,"-");
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;      

      window.reservas_listado_fecha_desde = (typeof window.reservas_listado_fecha_desde != "undefined") ? window.reservas_listado_fecha_desde : this.fecha;
      window.reservas_listado_fecha_hasta = (typeof window.reservas_listado_fecha_hasta != "undefined") ? window.reservas_listado_fecha_hasta : this.fecha;
      window.reservas_listado_filter = (typeof window.reservas_listado_filter != "undefined") ? window.reservas_listado_filter : "";
      window.reservas_listado_tipo_estado = (typeof window.reservas_listado_tipo_estado != "undefined") ? window.reservas_listado_tipo_estado : -1;
      window.reservas_listado_in_tipos_estados = (typeof window.reservas_listado_in_tipos_estados != "undefined") ? window.reservas_listado_in_tipos_estados : "";
      window.reservas_listado_page = (typeof window.reservas_listado_page != "undefined") ? window.reservas_listado_page : 1;
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
        
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      
      // Cargamos el paginador
      this.$(".pagination_container").html(this.pagination.el);
      
      createdatepicker(this.$("#reservas_desde"),window.reservas_listado_fecha_desde);
      createdatepicker(this.$("#reservas_hasta"),window.reservas_listado_fecha_hasta);
      
      this.buscar();
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      var filtros = {};

      if (!isEmpty(this.$("#reservas_listado_cliente").val())) 
        filtros.id_cliente = this.$("#reservas_listado_cliente").val();
      if (!isEmpty(this.$("#reservas_listado_numero").val())) 
        filtros.numero = this.$("#reservas_listado_numero").val();        
      
      if (this.$("#reservas_tipo_estado").length > 0) {
        if (window.reservas_listado_tipo_estado != this.$("#reservas_tipo_estado").val().trim()) {
          window.reservas_listado_tipo_estado = this.$("#reservas_tipo_estado").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#reservas_listado_buscar").length > 0 && window.reservas_listado_filter != this.$("#reservas_listado_buscar").val().trim()) {
        window.reservas_listado_filter = this.$("#reservas_listado_buscar").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#reservas_desde").length > 0 && window.reservas_listado_fecha_desde != this.$("#reservas_desde").val().trim()) {
        window.reservas_listado_fecha_desde = this.$("#reservas_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#reservas_hasta").length > 0 && window.reservas_listado_fecha_hasta != this.$("#reservas_hasta").val().trim()) {
        window.reservas_listado_fecha_hasta = this.$("#reservas_hasta").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.reservas_listado_page = 1;

      filtros.desde = (isEmpty(window.reservas_listado_fecha_desde)) ? "" : window.reservas_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.reservas_listado_fecha_hasta)) ? "" : window.reservas_listado_fecha_hasta.replace(/\//g,"-");
      filtros.filter = window.reservas_listado_filter;
      filtros.tipo_estado = window.reservas_listado_tipo_estado;
      filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : ((this.$("#reservas_usuarios").length > 0) ? this.$("#reservas_usuarios").val() : 0);
      filtros.in_tipos_estados = window.reservas_listado_in_tipos_estados;
      this.collection.server_api = filtros;
      this.collection.goTo(window.reservas_listado_page);
    },
    
    addAll : function () {
      window.reservas_listado_page = this.pagination.getPage();
      this.$("#reservas_tabla tbody tr").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ReservasItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: self,
      });
      this.$("#reservas_tabla tbody").append(view.render().el);
    },
    
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ReservasItemResultados = app.mixins.View.extend({
    template: _.template($("#reservas_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":function() {
        var self = this;
        var modelo = new app.models.Reserva({
          id: self.model.id,
        });
        modelo.fetch({
          "success":function(){
            var that = self;
            var view = new app.views.ReservaView({
              "model":modelo
            });
            crearLightboxHTML({
              "html":view.el,
              "width":700,
              "height":140,
              "callback":function() {
                that.parent.buscar();
              }
            });              
          }
        });
      },
      "click .delete": function() {
        if (confirmar("Realmente desea eliminar este comprobante?")) {
          var self = this;
          $.ajax({
            "url":"reservas/"+self.model.id,
            "type":"delete",
            "success":function() {
              self.parent.buscar();
            }
          });
        }
      },
      "click .imprimir":function() {
        workspace.imprimir_reporte("/sistema/reservas/function/imprimir/"+this.model.id);
      },
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
