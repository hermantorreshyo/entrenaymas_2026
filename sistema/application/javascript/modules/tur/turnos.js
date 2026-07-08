(function ( models ) {
  models.Turno = Backbone.Model.extend({
    urlRoot: "turnos/",
    defaults: {
      id_servicio: 0,
      servicio: "",
      id_cliente: 0,
      cliente: "",
      duracion_cantidad: 15,
      duracion_tipo: "M", // H (Hora) o M (Minutos)
      fecha: "",
      hora: "",
      sin_horario: 0,
      id_empresa: ID_EMPRESA,
      id_usuario: ID_USUARIO,
      observaciones: "",
      estado: 0, // -1 = HORARIO NO HABILITADO; 0 = PENDIENTE; 1 = REALIZADO;
    }
  });
})( app.models );


(function (collections, model, paginator) {
  collections.Turnos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "turnos/function/listado/"
    }
  });

})( app.collections, app.models.Turno, Backbone.Paginator);


(function ( app ) {
  app.views.TurnoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#turnos_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.view = this.options.view;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      var self = this;
      // Creamos un modelo con esos datos
      var modelo = new app.models.Turno({
        "id":self.model.id,
      });
      modelo.fetch({
        "success":function() {                    
          var that = self;
          app.views.turno = new app.views.TurnoEditView({
            "model":modelo,
          });
          crearLightboxHTML({
            "html":app.views.turno.el,
            "width":600,
            "height":140,
            "escapable":false,
            "callback":function() {
              that.view.collection.pager();
            }
          });
        }
      });
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );


(function ( app ) {

  app.views.TurnosTableView = app.mixins.View.extend({

    template: _.template($("#turnos_panel_template").html()),

    myEvents: {
      "click .nuevo":function() {
        var self = this;
        // Creamos un turno y abrimos el lightbox
        var modelo = new app.models.Turno({
          "fecha": moment().format("DD/MM/YYYY"),
          "hora": moment().format("HH:mm:ss"),
          "id_servicio":$("#turnos_servicios").val(),
          "servicio":$("#turnos_servicios option:selected").text(),
          "duracion_cantidad":$("#turnos_servicios option:selected").data("duracion_turno"),
        });
        app.views.turno = new app.views.TurnoEditView({
          "model":modelo,
        });
        var that = self;
        crearLightboxHTML({
          "html":app.views.turno.el,
          "width":600,
          "height":140,
          "escapable":false,
          "callback":function() {
            self.collection.pager();
          }
        });
      },
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      this.$("#turnos_servicios").select2();

      // Vamos a buscar los elementos y lo paginamos
      var datos = {};
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO;
      this.collection.server_api = datos;
      self.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.TurnoItem({
        model: item,
        view: this,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

	views.TurnosView = app.mixins.View.extend({

		template: _.template($("#turnos_template").html()),

    myEvents: {
      "change #turnos_servicios":function(){
        this.render_calendar();
      },
      "click #turnos_servicios_turnor_turnos":function() {
        this.$("#turnos_servicios_turnor_no_disponible").removeClass("active");
        this.$("#turnos_servicios_turnor_turnos").addClass("active");
        this.marcar_turnos = true;
      },
      "click #turnos_servicios_marcar_no_disponible":function() {
        this.$("#turnos_servicios_marcar_turnos").removeClass("active");
        this.$("#turnos_servicios_marcar_no_disponible").addClass("active");
        this.marcar_turnos = false;
      }
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.marcar_turnos = true;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template({}));
      self.$("#turnos_servicios").select2();
      setTimeout(function(){
        self.render_calendar();
      },200);            
    },

    render_calendar: function() {
      if (window.turnos_servicios.length == 0) return;
      var self = this;
      var id_servicio = self.$("#turnos_servicios").val();
      if (id_servicio == 0) {
        this.servicio = {
          "horarios":[
            { dia: 1,"desde":"08:00:00","hasta":"17:00:00" },
            { dia: 2,"desde":"08:00:00","hasta":"17:00:00" },
            { dia: 3,"desde":"08:00:00","hasta":"17:00:00" },
            { dia: 4,"desde":"08:00:00","hasta":"17:00:00" },
            { dia: 5,"desde":"08:00:00","hasta":"17:00:00" },
          ],
        };
      } else {
        this.servicio = _.find(window.turnos_servicios,function(item){
          return (id_servicio == item.id);
        });
      }
      var duracion_turno = self.$("#turnos_servicios option:selected").data("duracion_turno");
      //if (duracion_turno == 0) duracion_turno = 60;
      // TODO: Hacer configurable si se quiere el calendario por la duracion del turno o por intervalos de tiempo predefinidos
      duracion_turno = 60;
      var hora_desde = self.$("#turnos_servicios option:selected").data("hora_desde");
      if (isEmpty(hora_desde)) hora_desde = "07:00:00";
      var hora_hasta = self.$("#turnos_servicios option:selected").data("hora_hasta");
      if (isEmpty(hora_hasta) || hora_hasta == "00:00:00") hora_hasta = "24:00:00";

      this.$("#calendar").fullCalendar("destroy");
      var configCalendar = {
        allDaySlot: false,
        height: 600,
        selectable: true,
        lang: "es",
        defaultView: 'agendaWeek',
        editable: false,
        slotDuration: moment.duration(duracion_turno,'minutes'), // Cuanto dura cada celda
        slotLabelInterval: moment.duration(duracion_turno,'minutes'), // Cada cuanto muestra la etiqueta
        slotLabelFormat: "HH:mm",
        displayEventTime: false,
        minTime: hora_desde,
        maxTime: hora_hasta,
        eventSources:{
          "url": "turnos/function/calendario/",
          "data": {
            "id_servicio":$("#turnos_servicios").val(),
            "servicio":$("#turnos_servicios option:selected").text(),
          },
        },
        eventRender: function(event, element) {
          $(element).tooltip({title: event.title});
        },
        //eventStartEditable: true,
        //eventDurationEditable: true,
        dayClick: function(day, jsEvent, view) {
          console.log(jsEvent);
          /*
          if (jsEvent.target.classList.contains('fc-bgevent')) {
            alert('Click Background Event Area');
          }
          */
          if (self.marcar_turnos) {
            // Creamos un turno y abrimos el lightbox
            var modelo = new app.models.Turno({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
              "id_servicio":$("#turnos_servicios").val(),
              "servicio":$("#turnos_servicios option:selected").text(),
              "duracion_cantidad":$("#turnos_servicios option:selected").data("duracion_turno"),
            });
            app.views.turno = new app.views.TurnoEditView({
              "model":modelo,
            });
            var that = self;
            crearLightboxHTML({
              "html":app.views.turno.el,
              "width":600,
              "height":140,
              "escapable":false,
              "callback":function() {
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          } else {
            // Hay que inhabilitar ese turno
            var modelo = new app.models.Turno({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
              "id_servicio":$("#turnos_servicios").val(),
              "servicio":$("#turnos_servicios option:selected").text(),
              "duracion_cantidad":$("#turnos_servicios option:selected").data("duracion_turno"),
              "estado":-1,
            });
            modelo.save({},{
              "success":function(){
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          }
        },
        /*
        eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
          var nueva = moment(event.desde).add(delta);
          $.ajax({
            "url":"turnos/function/cambiar_fecha/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id":event.id,
              "duracion_cantidad":event.duracion_cantidad,
              "duracion_tipo":event.duracion_tipo,
              "id_cliente":event.id_cliente,
              "id_servicio":event.id_servicio,
              "fecha":nueva.format("YYYY-MM-DD"),
              "hora":nueva.format("HH:mm:SS"),
            },
          });
        },
        */
        eventClick: function(calEvent, jsEvent, view) {
          var that = self;
          if (self.marcar_turnos) {
            // Creamos un modelo con esos datos
            var modelo = new app.models.Turno({
              "id":calEvent.id,
            });
            modelo.fetch({
              "success":function() {                    
                app.views.turno = new app.views.TurnoEditView({
                  "model":modelo,
                });
                crearLightboxHTML({
                  "html":app.views.turno.el,
                  "width":600,
                  "height":140,
                  "escapable":false,
                  "callback":function() {
                    $("#calendar").fullCalendar('refetchEvents');
                  }
                });
              }
            });
          }
        },
        select: function(start, end, jsEvent, view) {
          if (!self.marcar_turnos) {
            // Calculamos la diferencia entre la hora final e inicial
            var diferencia = end.diff(start,'minutes');
            var modelo = new app.models.Turno({
              "fecha": start.format("DD/MM/YYYY"),
              "hora": start.format("HH:mm:ss"),
              "id_servicio":$("#turnos_servicios").val(),
              "servicio":$("#turnos_servicios option:selected").text(),
              "duracion_cantidad":diferencia,
              "duracion_tipo":"M",
              "estado":-1,
            });
            modelo.save({},{
              "success":function(){
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          }
        },
        dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
        dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
        monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
        monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
      };

      var horarioLaboral = this.crear_horarios_laborales();
      if (horarioLaboral.length > 0) {
        configCalendar.businessHours = horarioLaboral;
        configCalendar.selectConstraint = "businessHours";
        configCalendar.eventConstraint = "businessHours";
      }
      this.$("#calendar").fullCalendar(configCalendar);
      this.$("#calendar").fullCalendar('render');
      return this;
    },


    crear_horarios_laborales: function() {
      var self = this;
      // Creamos el array de horarios laborales
      var horarioLaboral = new Array();
      for(var i=0; i< this.servicio.horarios.length; i++) {
        var horario = this.servicio.horarios[i];
        horarioLaboral.push({
          dow: [parseInt(horario.dia)],
          start: horario.desde,
          end: horario.hasta,
        });        
      }
      console.log(horarioLaboral);
      return horarioLaboral;
    }

  });

})(app.views, app.models);




(function ( app ) {

  app.views.TurnoEditView = app.mixins.View.extend({

    template: _.template($("#turno_edit_panel_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar":"eliminar",
      "click .imprimir":"imprimir",
      "click .cerrar":"cerrar",
      "change #turno_fecha":"buscar_horarios",
      "click .setear_hora":function(e) {
        this.$("#turno_hora").val($(e.currentTarget).text());
      },
    },
    
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        "edicion": edicion,
        "id":this.model.id,
      }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      createdatepicker($(this.el).find("#turno_fecha"),this.model.get("fecha"));
      createdatepicker($(this.el).find("#turno_fecha_hasta"),new Date());
      this.$("#turno_hora").mask("99:99");
      this.buscar_horarios();

      // AUTOCOMPLETE DE CLIENTES
      // -------------------------
      var input = this.$("#turno_clientes");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
        "tipo_formulario":"contacto",
      });            
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });
      setTimeout(function(){
        self.$("#turno_clientes").focus();
      },500);
    },

    buscar_horarios: function() {
      var self = this;
      var fecha = this.$("#turno_fecha").val();
      if (isEmpty(fecha)) return;
      $.ajax({
        "url":"turnos/function/disponibles/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_empresa":ID_EMPRESA,
          "id_servicio":self.model.get("id_servicio"),
          "fecha":fecha,
        },
        "success":function(r) {
          if (typeof r.error != "undefined") {
            alert(r.error);
          } else {
            self.$("#turno_horarios").empty();
            for(var i=0; i<r.disponibles.length; i++) {
              var d = r.disponibles[i];
              self.$("#turno_horarios").append('<li><a href="javascript:void(0)" class="setear_hora">'+d.hora+'</a></li>');
            }
          }
        }
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente      
      self.$("#turno_servicios").focus();
      self.$("#turno_id_cliente").val(r.id);
      self.$("#turno_clientes").val(r.get("nombre"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#turno_clientes').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    validar: function() {
      var self = this;
      try {
        var id_cliente = self.$("#turno_id_cliente").val();
        if (id_cliente == 0) {
          alert("Por favor seleccione un cliente");
          self.$("#turno_clientes").select();
          return false;
        }
        if (!self.$("#turno_sin_horario").is(":checked")) {
          // Controlamos que se haya elegido un horario
          if (self.$("#turno_hora").val() == 0) {
            alert("Por favor seleccione un horario");
            self.$("#turno_hora").select();
            return false;            
          }
        }
        this.model.set({
          "id_cliente":id_cliente,
          "fecha":self.$("#turno_fecha").val(),
          "hora":self.$("#turno_hora").val(),
          "sin_horario":(self.$("#turno_sin_horario").is(":checked")?1:0),
          "observaciones":self.$("#turno_observaciones").val(),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },  
  
    guardar:function() {
      var self = this;
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
              self.cerrar();
            }
          },
        });
      }       
    },

    imprimir: function() {
      workspace.imprimir_reporte("/sistema/turnos/function/ver_pdf/"+this.model.id+"/"+ID_EMPRESA);
    },

    eliminar: function() {
      if (confirm("Realmente desea eliminar el elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        this.cerrar();
      }
    },
  
  });
})(app);
