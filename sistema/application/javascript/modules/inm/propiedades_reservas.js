// -----------
//   MODELO
// -----------

(function ( models ) {

  models.PropiedadReserva = Backbone.Model.extend({
    urlRoot: "propiedades_reservas",
    defaults: {
      huespedes: [],
      cliente: {
        "id":0,
        "nombre":"",
        "telefono":"",
        "celular":"",
        "email":"",
        "custom_3":1,
      },
      propiedad: {
        "compartida":0,
        "capacidad_maxima":0,
        "capacidad_maxima_menores":0,
      },
      pagos: [],
      id_cliente: 0,
      id_empresa: ID_EMPRESA,
      fecha_desde: "",
      fecha_hasta: "",
      precio: 0,
      total_pagado: 0,
      comentario: "",
      id_estado: 0,
      personas: 1,
      estado: "",
      numero: 0,
      eliminada: 0, // Puede que se elimine pero sigue estando para la caja
    },
  });
	  
})( app.models );



(function (collections, model, paginator) {

  collections.PropiedadesReservas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },    
    paginator_core: {
      url: "propiedades_reservas/function/buscar/",
    },
  });

})( app.collections, app.models.PropiedadReserva, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.PropiedadesReservasTableView = app.mixins.View.extend({

    template: _.template($("#propiedades_reservas_template").html()),
      
    myEvents: {
      "click .eliminar_reserva":function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var id = $(e.currentTarget).data("id");
        var modelo = new app.models.PropiedadReserva({
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
            // Reservada
            $(element).css("backgroundColor","#e06559");  
            $(element).css("borderColor","#e06559");  
          } else if (event.id_estado == 2) {
            // Pago Completo
            $(element).css("backgroundColor","#28b492");  
            $(element).css("borderColor","#28b492");  
          } else if (event.id_estado == 3) {
            // A coordinar
            $(element).css("backgroundColor","#e0a959");
            $(element).css("borderColor","#e0a959");
          }
          $(element).attr("title",event.comentario);
          $(element).find(".fc-time").hide();
          $(element).tooltip();
        },
        selectable: true,
        select: function( start, end, jsEvent, view, resource) {
          if (self.dayClick) { self.dayClick = false; return; }
          var fin = end.clone();
          //fin.subtract(1,'days'); // Le restamos un dia a la fecha
          var modelo = new app.models.PropiedadReserva({
            fecha_desde: start.format("DD/MM/YYYY"),
            fecha_hasta: fin.format("DD/MM/YYYY"),
            id_propiedad: resource.id,
          });
          var view = new app.views.PropiedadReservaView({
            "model":modelo
          });
          crearLightboxHTML({
            "html":view.el,
            "width":700,
            "height":140,
          });
        },
        dayClick: function(date, jsEvent, view, resource) {
          var modelo = new app.models.PropiedadReserva({
            fecha_desde: date.format("DD/MM/YYYY"),
            fecha_hasta: date.format("DD/MM/YYYY"),
            id_propiedad: resource.id,
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
          var modelo = new app.models.PropiedadReserva({
            id: calEvent.id,
            fecha: calEvent.start.format("DD/MM/YYYY"),
            id_empresa: calEvent.id_empresa,
          });
          modelo.fetch({
            "success":function(){
              var view = new app.views.PropiedadReservaView({
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
            labelText: 'Codigo',
            field: 'codigo'
          },        
          {
            labelText: 'Propiedad',
            field: 'nombre'
          },
        ],
        //resourceLabelText: '',
        resources: {
          url: '/sistema/propiedades_reservas/function/get_alquileres_temporarios/',
        },
        events: {
          url: '/sistema/propiedades_reservas/function/calendario/',
        },
      });
      this.$("#calendar").fullCalendar('render');
    },
    
  });

})(app);


(function ( app ) {

  app.views.PropiedadReservaView = app.mixins.View.extend({

    template: _.template($("#propiedad_reserva_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "click .imprimir": "imprimir",
      "click #propiedad_reserva_agregar_pago": "agregar_pago",
      "keypress #propiedad_reserva_total_pago": function(e){
        if (e.which == 13) this.agregar_pago();
      },
      "change #propiedad_reserva_personas": "render_huespedes",
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

      this.tr_item = null;
      
      var fecha_desde = (isEmpty(this.model.get("fecha_desde"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha_desde");
      createdatepicker(this.$("#propiedad_reserva_fecha_desde"),fecha_desde);

      var fecha_hasta = (isEmpty(this.model.get("fecha_hasta"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha_hasta");
      createdatepicker(this.$("#propiedad_reserva_fecha_hasta"),fecha_hasta);

      createdatepicker(this.$("#propiedad_reserva_fecha_pago"),moment().format("DD/MM/YYYY"));      

      new app.mixins.Select({
        modelClass: app.models.Propiedad,
        url: "propiedades/",
        render: "#propiedad_reserva_propiedades",
        fields: ["capacidad_maxima"],
        selected: self.model.get("id_propiedad"),
      });

      var input = this.$("#propiedad_reserva_clientes");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":null,
        "width":"300px",
        "onSelect":function(item){
          $("#propiedad_reserva_id_cliente").val(item.id);
          $("#propiedad_reserva_clientes").val(item.nombre);
          $("#propiedad_reserva_cliente_email").val(item.email);
          $("#propiedad_reserva_cliente_telefono").val(item.telefono);
        }
      });

      this.render_huespedes();
    },

    agregar_pago: function() {

      var fecha_pago = this.$("#propiedad_reserva_fecha_pago").val();
      var metodo_pago = this.$("#propiedad_reserva_metodo_pago").val();
      var observaciones = this.$("#propiedad_reserva_pago_observaciones").val();
      observaciones = observaciones.replace(/\"/g,"");
      observaciones = observaciones.replace(/\'/g,"");
      var pago = parseFloat(this.$("#propiedad_reserva_total_pago").val());
      if (isNaN(pago)) {
        alert("Por favor ingrese un valor valido.");
        this.$("#propiedad_reserva_total_pago").select();
        return;
      }
      var tr = "<tr data-fecha_pago='"+fecha_pago+"' data-metodo_pago='"+metodo_pago+"' data-pago='"+pago+"' data-observaciones='"+observaciones+"'>";
      tr+="<td class='editar_pago'>"+fecha_pago+"</td>";
      tr+="<td class='editar_pago'>"+metodo_pago+"</td>";
      tr+="<td class='editar_pago'>"+observaciones+"</td>";
      tr+="<td class='editar_pago'>"+pago+"</td>";
      tr+='<td><i class="fa fa-times eliminar_pago text-danger"></i></td>';
      tr+="</tr>";
      if (this.tr_item == null) {
        // Estamos agregando un nuevo item
        this.$("#tabla_pagos tbody").append(tr);
      } else {
        // Estamos editando, entonces reemplazamos el TR
        $(this.tr_item).replaceWith(tr);
      }
      this.tr_item = null;
      this.calcular_pagos();
      return false;
    },

    editar_pago:function(e) {
      var tr = $(e.currentTarget).parents("tr");
      this.$("#propiedad_reserva_fecha_pago").val($(tr).data("fecha_pago"));
      this.$("#propiedad_reserva_metodo_pago").val($(tr).data("metodo_pago"));
      this.$("#propiedad_reserva_pago_observaciones").val($(tr).data("observaciones"));
      this.$("#propiedad_reserva_total_pago").val($(tr).data("pago"));
      this.tr_item = tr;
    },

    eliminar_pago:function(e) {
      $(e.currentTarget).parents("tr").remove();
      this.deshabilitar_tipo_practica();
    },   

    calcular_pagos: function() {
      var total_pagos = 0;
      this.$("#tabla_pagos tbody tr").each(function(i,e){
        var t = parseFloat($(e).data("pago"));
        total_pagos += t;
      });
      this.$("#propiedad_reserva_subtotal_pagos").html(Number(total_pagos).format());
    },

    render_huespedes: function() {
      var cantidad = this.$("#propiedad_reserva_personas").val();
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
          custom_3: 1,
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
          "id_propiedad":self.$("#propiedad_reserva_propiedades").val(),
          "id_estado":self.$("#propiedad_reserva_estados").val(),
        });

        // Tomamos los pagos
        var pagos = new Array();
        var total_pagos = 0;
        this.$("#tabla_pagos tbody tr").each(function(i,e){
          var t = parseFloat($(e).data("pago"));
          total_pagos += t;
          pagos.push({
            "fecha_pago":$(e).data("fecha_pago"),
            "metodo_pago":$(e).data("metodo_pago"),
            "observaciones":$(e).data("observaciones"),
            "pago":$(e).data("pago"),
          });
        });        
        this.model.set({
          "pagos":pagos,
          "total_pagado":total_pagos,
        });

        var cliente = this.model.get("cliente");
        cliente.nombre = self.$("#propiedad_reserva_clientes").val();
        cliente.email = self.$("#propiedad_reserva_cliente_email").val();
        cliente.telefono = self.$("#propiedad_reserva_cliente_telefono").val();
        cliente.celular = self.$("#propiedad_reserva_cliente_celular").val();
        this.model.set({
          "cliente":cliente,
          "id_cliente":self.$("#propiedad_reserva_id_cliente").val(),
          "propiedad":{
            "capacidad_maxima":self.$("#propiedad_reserva_propiedades option:selected").data("capacidad_maxima"),
          }
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
      var self = this;
      self.model.set({"eliminada":1});
      this.change_property({
        "table":"inm_propiedades_reservas",
        "url":"propiedades_reservas/function/change_property/",
        "attribute":"eliminada",
        "value":1,
        "id":self.model.id,
        "success":function(){
          $('.modal:last').modal('hide');
          $('#calendar').fullCalendar('refetchEvents');
        }
      });
    },

    imprimir: function() {
      workspace.imprimir_reporte("/sistema/propiedades_reservas/function/imprimir/"+this.model.id);
    }
    
  });
})(app);




// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.PropiedadesReservasListadoView = app.mixins.View.extend({

    template: _.template($("#propiedades_reservas_listado_template").html()),
      
    myEvents: {
      "click .buscar":"buscar",
      "click .nueva_reserva":function(){
        var modelo = new app.models.PropiedadReserva({
          fecha_desde: moment().format("DD/MM/YYYY"),
          fecha_hasta: moment().format("DD/MM/YYYY"),
        });
        var view = new app.views.PropiedadReservaView({
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

      window.propiedades_reservas_listado_fecha_desde = (typeof window.propiedades_reservas_listado_fecha_desde != "undefined") ? window.propiedades_reservas_listado_fecha_desde : this.fecha;
      window.propiedades_reservas_listado_fecha_hasta = (typeof window.propiedades_reservas_listado_fecha_hasta != "undefined") ? window.propiedades_reservas_listado_fecha_hasta : this.fecha;
      window.propiedades_reservas_listado_filter = (typeof window.propiedades_reservas_listado_filter != "undefined") ? window.propiedades_reservas_listado_filter : "";
      window.propiedades_reservas_listado_tipo_estado = (typeof window.propiedades_reservas_listado_tipo_estado != "undefined") ? window.propiedades_reservas_listado_tipo_estado : -1;
      window.propiedades_reservas_listado_in_tipos_estados = (typeof window.propiedades_reservas_listado_in_tipos_estados != "undefined") ? window.propiedades_reservas_listado_in_tipos_estados : "";
      window.propiedades_reservas_listado_page = (typeof window.propiedades_reservas_listado_page != "undefined") ? window.propiedades_reservas_listado_page : 1;
      
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
      
      createdatepicker(this.$("#propiedades_reservas_desde"),window.propiedades_reservas_listado_fecha_desde);
      createdatepicker(this.$("#propiedades_reservas_hasta"),window.propiedades_reservas_listado_fecha_hasta);
      
      this.buscar();
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      var filtros = {};

      if (!isEmpty(this.$("#propiedades_reservas_listado_cliente").val())) 
        filtros.id_cliente = this.$("#propiedades_reservas_listado_cliente").val();
      if (!isEmpty(this.$("#propiedades_reservas_listado_numero").val())) 
        filtros.numero = this.$("#propiedades_reservas_listado_numero").val();        
      
      if (this.$("#propiedades_reservas_tipo_estado").length > 0) {
        if (window.propiedades_reservas_listado_tipo_estado != this.$("#propiedades_reservas_tipo_estado").val().trim()) {
          window.propiedades_reservas_listado_tipo_estado = this.$("#propiedades_reservas_tipo_estado").val().trim();
          cambio_parametros = true;
        }
      }

      if (this.$("#propiedades_reservas_listado_buscar").length > 0 && window.propiedades_reservas_listado_filter != this.$("#propiedades_reservas_listado_buscar").val().trim()) {
        window.propiedades_reservas_listado_filter = this.$("#propiedades_reservas_listado_buscar").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#propiedades_reservas_desde").length > 0 && window.propiedades_reservas_listado_fecha_desde != this.$("#propiedades_reservas_desde").val().trim()) {
        window.propiedades_reservas_listado_fecha_desde = this.$("#propiedades_reservas_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#propiedades_reservas_hasta").length > 0 && window.propiedades_reservas_listado_fecha_hasta != this.$("#propiedades_reservas_hasta").val().trim()) {
        window.propiedades_reservas_listado_fecha_hasta = this.$("#propiedades_reservas_hasta").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.propiedades_reservas_listado_page = 1;

      filtros.desde = (isEmpty(window.propiedades_reservas_listado_fecha_desde)) ? "" : window.propiedades_reservas_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.propiedades_reservas_listado_fecha_hasta)) ? "" : window.propiedades_reservas_listado_fecha_hasta.replace(/\//g,"-");
      filtros.filter = window.propiedades_reservas_listado_filter;
      filtros.tipo_estado = window.propiedades_reservas_listado_tipo_estado;
      filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : ((this.$("#propiedades_reservas_usuarios").length > 0) ? this.$("#propiedades_reservas_usuarios").val() : 0);
      filtros.in_tipos_estados = window.propiedades_reservas_listado_in_tipos_estados;
      this.collection.server_api = filtros;
      this.collection.goTo(window.propiedades_reservas_listado_page);
    },
    
    addAll : function () {
      window.propiedades_reservas_listado_page = this.pagination.getPage();
      this.$("#propiedades_reservas_tabla tbody tr").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.PropiedadesReservasItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: self,
      });
      this.$("#propiedades_reservas_tabla tbody").append(view.render().el);
    },
    
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.PropiedadesReservasItemResultados = app.mixins.View.extend({
    template: _.template($("#propiedades_reservas_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":function() {
        var self = this;
        var modelo = new app.models.PropiedadReserva({
          id: self.model.id,
        });
        modelo.fetch({
          "success":function(){
            var that = self;
            var view = new app.views.PropiedadReservaView({
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
      "click .delete": "eliminar",
      "click .eliminar_definitivo": "eliminar_definitivo",
      "click .imprimir":function() {
        workspace.imprimir_reporte("/sistema/propiedades_reservas/function/imprimir/"+this.model.id);
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
    eliminar: function() {
      if (!confirm("Realmente desea eliminar esta reserva?")) return;
      var self = this;
      self.model.set({"eliminada":1});
      this.change_property({
        "table":"inm_propiedades_reservas",
        "url":"propiedades_reservas/function/change_property/",
        "attribute":"eliminada",
        "value":1,
        "id":self.model.id,
        "success":function(){
          location.reload();
        }
      });
    },

    eliminar_definitivo: function() {
      if (!confirm("Realmente desea eliminar esta reserva definitivamente?")) return;
      $.ajax({
        "url":"propiedades_reservas/"+this.model.id,
        "type":"delete",
        "success":function() {
          location.reload();
        }
      })
    },    
  });
})(app);
