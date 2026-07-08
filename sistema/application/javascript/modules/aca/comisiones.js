(function ( models ) {

  models.Comision = Backbone.Model.extend({
    urlRoot: "comisiones/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      anio: 0,
      id_carrera: 0,
      cantidad_alumnos: 0,
      alumnos: [],
      turno: "",
    }
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Comisiones = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "comisiones/"
    }
  });

})( app.collections, app.models.Comision, Backbone.Paginator);


(function ( app ) {

  app.views.ComisionItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#comisiones_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .enviar":"enviar",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
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
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#comision/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    enviar: function() {
      window.tabla_campania = "aca_alumnos",
      window.filtro_campania = this.model.id;
      location.href = "app/#campania_envio";
    },
  });

})( app );

(function ( app ) {

  app.views.ComisionesTableView = app.mixins.View.extend({
    template: _.template($("#comisiones_panel_template").html()),
    myEvents:{
      "click .buscar":"buscar",
      "keypress #comisiones_buscar":function(e) {
        if (e.which == 13) this.buscar();
      }
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.comisiones_filter = (typeof window.comisiones_filter != "undefined") ? window.comisiones_filter : "";
      window.comisiones_page = (typeof window.comisiones_page != "undefined") ? window.comisiones_page : 1;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
      }));
      $(this.el).find(".pagination_container").html(pagination.el);
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      if (window.comisiones_filter != this.$("#comisiones_buscar").val().trim()) {
        window.comisiones_filter = this.$("#comisiones_buscar").val().trim();
        cambio_parametros = true;
      }
      if (cambio_parametros) window.comisiones_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.comisiones_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.comisiones_page);
    },

    addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ComisionItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.ComisionEditView = app.mixins.View.extend({

    template: _.template($("#comisiones_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #comision_alumno_agregar":"agregar_alumno",
      "click .eliminar_alumno":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "click #materias_tab":function() {
        var self = this;
        setTimeout(function(){
            self.render_calendar();
        },200);            
      }
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      new app.mixins.Select({
        modelClass: app.models.Carrera,
        url: "carreras/",
        render: "#comision_carreras",
        name : "id_carrera",
        selected: this.model.get("id_carrera"),
      });

      var input = this.$("#comision_alumno_nombre");
      $(input).customcomplete({
        "url":"alumnos/function/get_by_nombre/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          if (item.id_comision != 0 && item.id_comision != self.model.id) {
            alert("El alumno ya pertenece a otra comision.");
            self.$("#comision_alumno_nombre").val("");
            self.$("#comision_alumno_nombre").focus();
            return;
          }
          self.$("#comision_alumno_id").val(item.id);
          self.$("#comision_alumno_numero_legajo").val(item.numero_legajo);
          self.$("#comision_alumno_cuit").val(item.cuit);
          self.agregar_alumno();
        }
      });

    },

    agregar_alumno: function() {
      var id = this.$("#comision_alumno_id").val();
      // Controlamos que no se haya agregado antes
      var encontro = false;
      $("#comision_alumnos_tabla tbody tr").each(function(i,e){
        if (id == $(e).find(".id").val()) {
          encontro = true; return;
        }
      });
      if (!encontro) {
        var nombre = this.$("#comision_alumno_nombre").val();
        var numero_legajo = this.$("#comision_alumno_numero_legajo").val();
        var cuit = this.$("#comision_alumno_cuit").val();
        if (id == 0) {
          alert("Por favor busque un alumno y luego seleccionelo de la lista.");
          return;
        }
        var tr = "<tr>";
        tr+="<input type='hidden' class='id' value='"+id+"'/>";
        tr+="<td><span class='text-info nombre'>"+nombre+"</span></td>";
        tr+="<td><span class='numero_legajo'>"+numero_legajo+"</span></td>";
        tr+="<td><span class='cuit'>"+cuit+"</span></td>";
        tr+='<td class="tar">';
        tr+='<button class="btn btn-sm btn-white eliminar_alumno"><i class="fa fa-trash"></i></button>';
        tr+='</td>';
        tr+="</tr>";
        this.$("#comision_alumnos_tabla tbody").append(tr);
      }
      this.$("#comision_alumno_id").val("0");
      this.$("#comision_alumno_nombre").val("");
      this.$("#comision_alumno_numero_legajo").val("");
      this.$("#comision_alumno_cuit").val("");
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("comision_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var id_carrera = self.$("#comision_carreras").val();
        if (id_carrera == 0) {
          alert("Por favor seleccione un plan de estudio para la comision.");
          self.$("#comision_carreras").focus();
          return false;
        }
        self.model.set({
          "id_carrera":id_carrera,
          "turno":self.$("#comision_turno").val(),
          "anio":self.$("#comision_anio").val(),
        });

        if (this.$("#comision_alumnos_tabla").length > 0) {
          var alumnos = new Array();
          $("#comision_alumnos_tabla tbody tr").each(function(i,e){
            alumnos.push({
              "id":$(e).find(".id").val(),
            });
          });
          this.model.set({"alumnos":alumnos});
        }

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
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
            "id_empresa":ID_EMPRESA,
          },{
          success: function(model,response) {
            // Se refresca la pagina porque tenemos cacheado un array de comisiones
            location.reload();
            //location.href="app/#comisiones";
          }
        });
      }
    },

  });

})(app.views, app.models);


(function ( views, models ) {

  views.ComisionCalendarioView = app.mixins.View.extend({
    template: _.template($("#comisiones_calendario_template").html()),
    myEvents: {
    },
    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(window.comision.toJSON()));
      setTimeout(function(){
        self.render_calendar();
      },200);            
    },
    render_calendar: function() {
      var self = this;
      this.$("#calendar").fullCalendar({
        allDaySlot: false,
        height: 500,
        lang: "es",
        defaultView: 'agendaWeek',
        editable: false,
        //eventStartEditable: true,
        //eventDurationEditable: true,
        eventSources : [{
          "url": "clases/function/calendario/",
          "data": {
            "id_comision":window.comision.id,
          },
        }],
        eventRender: function(event, element) {
          element.find('.fc-title').append("<br/>"+event.description);
        },
        dayClick: function(day, jsEvent, view) {
          var modelo = new app.models.Clase({
            fecha: day.format("DD/MM/YYYY"),
            hora: day.format("HH:mm:ss"),
            id_comision: window.comision.id,
          });
          app.views.clase = new app.views.ClaseEditView({
            "model":modelo,
            "id_carrera": window.comision.get("id_carrera"),
            "anio": window.comision.get("anio"),
            "mostrar_docentes":true,
          });
          var that = self;
          crearLightboxHTML({
            "html":app.views.clase.el,
            "width":500,
            "height":140,
            "callback":function() {
              $("#calendar").fullCalendar('refetchEvents');
            }
          });
        },
        eventClick: function(calEvent, jsEvent, view) {
          var that = self;
          // Creamos un modelo con esos datos
          var modelo = new app.models.Clase({"id":calEvent.id});
          modelo.fetch({
            "success":function() {                    
              app.views.clase = new app.views.ClaseEditView({
                "model":modelo,
                "mostrar_docentes":true,
                "anio": window.comision.get("anio"),
              });
              crearLightboxHTML({
                "html":app.views.clase.el,
                "width":500,
                "height":140,
                "callback":function() {
                  $("#calendar").fullCalendar('refetchEvents');
                }
              });
            }
          });
        },
        dayNames : [ "Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado" ],
        dayNamesShort : [ "Dom","Lun","Mar","Mie","Jue","Vie","Sab" ],
        monthNames : [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
        monthNamesShort : [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
      });
      this.$("#calendar").fullCalendar('render');
      return this;
    },
  });
})(app.views, app.models);

