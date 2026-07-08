(function ( models ) {
  models.TurnoMedico = Backbone.Model.extend({
    urlRoot: "turnos_medicos/",
    defaults: {
      id_profesional: 0,
      profesional: "",
      id_paciente: 0,
      paciente: "",
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


(function ( views, models ) {

	views.TurnosMedicosView = app.mixins.View.extend({

		template: _.template($("#turnos_medicos_template").html()),

    myEvents: {
      "change #turnos_profesionales":function(){
        this.render_calendar();
      },
      "click #turnos_profesionales_marcar_turnos":function() {
        this.$("#turnos_profesionales_marcar_no_disponible").removeClass("active");
        this.$("#turnos_profesionales_marcar_turnos").addClass("active");
        this.marcar_turnos = true;
      },
      "click #turnos_profesionales_marcar_no_disponible":function() {
        this.$("#turnos_profesionales_marcar_turnos").removeClass("active");
        this.$("#turnos_profesionales_marcar_no_disponible").addClass("active");
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
      self.$("#turnos_profesionales").select2();
      setTimeout(function(){
        self.render_calendar();
      },200);            
    },

    render_calendar: function() {
      if (window.profesionales.length == 0) return;
      var self = this;
      var id_profesional = self.$("#turnos_profesionales").val();
      this.profesional = _.find(window.profesionales,function(item){
        return (id_profesional == item.id);
      })
      var duracion_turno = self.$("#turnos_profesionales option:selected").data("duracion_turno");
      if (duracion_turno == 0) duracion_turno = 60;
      var hora_desde = self.$("#turnos_profesionales option:selected").data("hora_desde");
      if (isEmpty(hora_desde)) hora_desde = "07:00:00";
      var hora_hasta = self.$("#turnos_profesionales option:selected").data("hora_hasta");
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
          "url": "turnos_medicos/function/calendario/",
          "data": {
            "id_profesional":$("#turnos_profesionales").val(),
            "profesional":$("#turnos_profesionales option:selected").text(),
          },
        },
        eventRender: function(event, element) {
          $(element).tooltip({title: event.title});
        },
        eventStartEditable: true,
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
            var modelo = new app.models.TurnoMedico({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
              "id_profesional":$("#turnos_profesionales").val(),
              "profesional":$("#turnos_profesionales option:selected").text(),
              "duracion_cantidad":$("#turnos_profesionales option:selected").data("duracion_turno"),
            });
            app.views.turno_medico = new app.views.TurnoMedicoEditView({
              "model":modelo,
            });
            var that = self;
            crearLightboxHTML({
              "html":app.views.turno_medico.el,
              "width":600,
              "height":140,
              "escapable":false,
              "callback":function() {
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          } else {
            // Hay que inhabilitar ese turno
            var modelo = new app.models.TurnoMedico({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
              "id_profesional":$("#turnos_profesionales").val(),
              "profesional":$("#turnos_profesionales option:selected").text(),
              "duracion_cantidad":$("#turnos_profesionales option:selected").data("duracion_turno"),
              "estado":-1,
            });
            modelo.save({},{
              "success":function(){
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          }
        },
        eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
          var nueva = moment(event.desde).add(delta);
          $.ajax({
            "url":"turnos_medicos/function/cambiar_fecha/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id":event.id,
              "duracion_cantidad":event.duracion_cantidad,
              "duracion_tipo":event.duracion_tipo,
              "id_paciente":event.id_paciente,
              "id_profesional":event.id_profesional,
              "fecha":nueva.format("YYYY-MM-DD"),
              "hora":nueva.format("HH:mm:SS"),
            },
          });
        },
        eventClick: function(calEvent, jsEvent, view) {
          var that = self;
          if (self.marcar_turnos) {
            // Creamos un modelo con esos datos
            var modelo = new app.models.TurnoMedico({
              "id":calEvent.id,
            });
            modelo.fetch({
              "success":function() {                    
                app.views.turno_medico = new app.views.TurnoMedicoEditView({
                  "model":modelo,
                });
                crearLightboxHTML({
                  "html":app.views.turno_medico.el,
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
            var modelo = new app.models.TurnoMedico({
              "fecha": start.format("DD/MM/YYYY"),
              "hora": start.format("HH:mm:ss"),
              "id_profesional":$("#turnos_profesionales").val(),
              "profesional":$("#turnos_profesionales option:selected").text(),
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
      if (this.profesional.horario_lunes_1 != "00:00:00" && this.profesional.horario_lunes_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[1],
          start: self.profesional.horario_lunes_1,
          end: self.profesional.horario_lunes_2,
        });
      }
      if (self.profesional.horario_lunes_3 != "00:00:00" && self.profesional.horario_lunes_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[1],
          start: self.profesional.horario_lunes_3,
          end: self.profesional.horario_lunes_4,
        });
      }
      if (self.profesional.horario_martes_1 != "00:00:00" && self.profesional.horario_martes_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[2],
          start: self.profesional.horario_martes_1,
          end: self.profesional.horario_martes_2,
        });
      }
      if (self.profesional.horario_martes_3 != "00:00:00" && self.profesional.horario_martes_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[2],
          start: self.profesional.horario_martes_3,
          end: self.profesional.horario_martes_4,
        });
      }
      if (self.profesional.horario_miercoles_1 != "00:00:00" && self.profesional.horario_miercoles_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[3],
          start: self.profesional.horario_miercoles_1,
          end: self.profesional.horario_miercoles_2,
        });
      }
      if (self.profesional.horario_miercoles_3 != "00:00:00" && self.profesional.horario_miercoles_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[3],
          start: self.profesional.horario_miercoles_3,
          end: self.profesional.horario_miercoles_4,
        });
      }
      if (self.profesional.horario_jueves_1 != "00:00:00" && self.profesional.horario_jueves_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[4],
          start: self.profesional.horario_jueves_1,
          end: self.profesional.horario_jueves_2,
        });
      }
      if (self.profesional.horario_jueves_3 != "00:00:00" && self.profesional.horario_jueves_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[4],
          start: self.profesional.horario_jueves_3,
          end: self.profesional.horario_jueves_4,
        });
      }
      if (self.profesional.horario_viernes_1 != "00:00:00" && self.profesional.horario_viernes_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[5],
          start: self.profesional.horario_viernes_1,
          end: self.profesional.horario_viernes_2,
        });
      }
      if (self.profesional.horario_viernes_3 != "00:00:00" && self.profesional.horario_viernes_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[5],
          start: self.profesional.horario_viernes_3,
          end: self.profesional.horario_viernes_4,
        });
      }
      if (self.profesional.horario_sabado_1 != "00:00:00" && self.profesional.horario_sabado_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[6],
          start: self.profesional.horario_sabado_1,
          end: self.profesional.horario_sabado_2,
        });
      }
      if (self.profesional.horario_sabado_3 != "00:00:00" && self.profesional.horario_sabado_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[6],
          start: self.profesional.horario_sabado_3,
          end: self.profesional.horario_sabado_4,
        });
      }
      if (self.profesional.horario_domingo_1 != "00:00:00" && self.profesional.horario_domingo_2 != "00:00:00") {
        horarioLaboral.push({
          dow:[7],
          start: self.profesional.horario_domingo_1,
          end: self.profesional.horario_domingo_2,
        });
      }
      if (self.profesional.horario_domingo_3 != "00:00:00" && self.profesional.horario_domingo_4 != "00:00:00") {
        horarioLaboral.push({
          dow:[7],
          start: self.profesional.horario_domingo_3,
          end: self.profesional.horario_domingo_4,
        });
      }
      return horarioLaboral;
    }

  });

})(app.views, app.models);




(function ( app ) {

  app.views.TurnoMedicoEditView = app.mixins.View.extend({

    template: _.template($("#turno_medico_edit_panel_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar":"eliminar",
      "click .cerrar":"cerrar",
    },
    
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.id_profesional = (typeof this.options.id_profesional != "undefined") ? this.options.id_profesional : 0;
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

      createdatepicker($(this.el).find("#turno_medico_fecha"),this.model.get("fecha"));
      createdatepicker($(this.el).find("#turno_medico_fecha_hasta"),new Date());
      this.$("#turno_medico_hora").mask("99:99");

      // AUTOCOMPLETE DE PACIENTES
      // -------------------------
      var input = this.$("#turno_medico_pacientes");
      var form = new app.views.PacienteEditViewMini({
        "model": new app.models.Paciente(),
        "input": input,
        "onSave": self.seleccionar_paciente,
      });            
      $(input).customcomplete({
        "url":"pacientes/function/get_by_nombre/",
        "form":form,
        "onSelect":function(item){
          var paciente = new app.models.Paciente({"id":item.id});
          paciente.fetch({
            "success":function(){
              self.seleccionar_paciente(paciente);
            },
          });
        }
      });
      setTimeout(function(){
        self.$("#turno_medico_pacientes").focus();
      },500);
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    seleccionar_paciente: function(r) {
      var self = this;
      self.paciente = r; // Seteamos el paciente      
      self.$("#turno_medico_profesionales").focus();
      self.$("#turno_medico_id_paciente").val(r.id);
      self.$("#turno_medico_pacientes").val(r.get("nombre"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#turno_medico_pacientes').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    validar: function() {
      var self = this;
      try {
        var id_paciente = self.$("#turno_medico_id_paciente").val();
        if (id_paciente == 0) {
          alert("Por favor seleccione un paciente");
          self.$("#turno_medico_pacientes").select();
          return false;
        }
        this.model.set({
          "id_paciente":id_paciente,
          "fecha":self.$("#turno_medico_fecha").val(),
          "hora":self.$("#turno_medico_hora").val(),
          "sin_horario":(self.$("#turno_medico_sin_horario").is(":checked")?1:0),
          "observaciones":self.$("#turno_medico_observaciones").val(),
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

    eliminar: function() {
      if (confirm("Realmente desea eliminar el elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        this.cerrar();
      }
    },
  
  });
})(app);
