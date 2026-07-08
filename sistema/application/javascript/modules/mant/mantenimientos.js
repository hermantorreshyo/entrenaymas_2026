(function ( models ) {
  models.Mantenimiento = Backbone.Model.extend({
    urlRoot: "mantenimientos/",
    defaults: {
      id_tipo_mantenimiento: 0,
      numero: 0,
      fecha: "",
      hora: "",
      id_empresa: ID_EMPRESA,
      id_usuario: ID_USUARIO,
      observaciones: "",
      realizado: 0,
      duracion_aprox_cantidad: 1,
      duracion_aprox_tipo: "H",
      ordenes_trabajo: [],
    }
  });
})( app.models );


(function ( views, models ) {

	views.MantenimientosView = app.mixins.View.extend({

		template: _.template($("#mantenimientos_template").html()),

    myEvents: {
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.marcar_mantenimientos = true;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template({}));
      setTimeout(function(){
        self.render_calendar();
      },200);            
    },

    render_calendar: function() {
      var self = this;
      var duracion_mantenimiento = 60;

      this.$("#calendar").fullCalendar("destroy");
      var configCalendar = {
        allDaySlot: false,
        height: 600,
        selectable: true,
        lang: "es",
        defaultView: 'agendaWeek',
        editable: false,
        slotDuration: moment.duration(duracion_mantenimiento,'minutes'), // Cuanto dura cada celda
        slotLabelInterval: moment.duration(duracion_mantenimiento,'minutes'), // Cada cuanto muestra la etiqueta
        slotLabelFormat: "HH:mm",
        displayEventTime: false,
        minTime: MANT_HORARIO_DESDE,
        maxTime: MANT_HORARIO_HASTA,
        eventSources:{
          "url": "mantenimientos/function/calendario/",
          "data": {
            //"id_servicio":$("#mantenimientos_servicios").val(),
            //"servicio":$("#mantenimientos_servicios option:selected").text(),
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
          if (self.marcar_mantenimientos) {
            // Creamos un mantenimiento y abrimos el lightbox
            var modelo = new app.models.Mantenimiento({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
            });
            app.views.mantenimiento = new app.views.MantenimientoEditView({
              "model":modelo,
            });
            var that = self;
            crearLightboxHTML({
              "html":app.views.mantenimiento.el,
              "width":700,
              "height":140,
              "escapable":false,
              "callback":function() {
                $("#calendar").fullCalendar('refetchEvents');
              }
            });
          } else {
            // Hay que inhabilitar ese mantenimiento
            var modelo = new app.models.Mantenimiento({
              "fecha": day.format("DD/MM/YYYY"),
              "hora": day.format("HH:mm:ss"),
              "realizado":-1,
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
            "url":"mantenimientos/function/cambiar_fecha/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id":event.id,
              "duracion_cantidad":event.duracion_cantidad,
              "duracion_tipo":event.duracion_tipo,
              "id_tipo_mantenimiento":event.id_tipo_mantenimiento,
              "fecha":nueva.format("YYYY-MM-DD"),
              "hora":nueva.format("HH:mm:SS"),
            },
          });
        },
        eventClick: function(calEvent, jsEvent, view) {
          var that = self;
          if (self.marcar_mantenimientos) {
            // Creamos un modelo con esos datos
            var modelo = new app.models.Mantenimiento({
              "id":calEvent.id,
            });
            modelo.fetch({
              "success":function() {                    
                app.views.mantenimiento = new app.views.MantenimientoEditView({
                  "model":modelo,
                });
                crearLightboxHTML({
                  "html":app.views.mantenimiento.el,
                  "width":700,
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
          if (!self.marcar_mantenimientos) {
            // Calculamos la diferencia entre la hora final e inicial
            var diferencia = end.diff(start,'minutes');
            var modelo = new app.models.Mantenimiento({
              "fecha": start.format("DD/MM/YYYY"),
              "hora": start.format("HH:mm:ss"),
              "duracion_cantidad":diferencia,
              "duracion_tipo":"M",
              "realizado":-1,
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
      this.$("#calendar").fullCalendar(configCalendar);
      this.$("#calendar").fullCalendar('render');
      return this;
    },

  });

})(app.views, app.models);



(function ( app ) {

  app.views.MantenimientoEditView = app.mixins.View.extend({

    template: _.template($("#mantenimiento_edit_panel_template").html()),
        
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar":"eliminar",
      "click .imprimir":"imprimir",
      "click .cerrar":"cerrar",
      "click .nueva_orden_trabajo":function(){
        var self = this;
        var v = new app.views.OrdenTrabajoEditView({
          model: new app.models.OrdenTrabajo({
            "numero":self.numeros.numero_orden_trabajo++,
          }),
          collection: self.ordenes_trabajo,
          view: self,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "escapable":false,
          "callback":function() {
            console.log(self.ordenes_trabajo);
          }
        });
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

      createdatepicker($(this.el).find("#mantenimiento_fecha"),this.model.get("fecha"));
      createdatepicker($(this.el).find("#mantenimiento_fecha_hasta"),new Date());
      this.$("#mantenimiento_hora").mask("99:99");

      self.ordenes_trabajo = new app.collections.OrdenesTrabajo();
      var dep = this.model.get("ordenes_trabajo");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.OrdenTrabajo(dd);
        self.ordenes_trabajo.add(ddo);
      }
      this.ordenes_trabajoTable = new app.views.OrdenesTrabajoTableView({
        collection: self.ordenes_trabajo,
        view: self,
      });
      this.$("#mantenimiento_ordenes_trabajo").html(this.ordenes_trabajoTable.el);

      $.ajax({
        "url":"mantenimientos/function/next/",
        "dataType":"json",
        "success":function(r) {
          self.numeros = r;
          if (self.model.id == undefined) {
            self.$("#mantenimiento_numero").val(r.numero);
          }
        }
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    validar: function() {
      var self = this;
      try {
        if (typeof self.ordenes_trabajo.models == "undefined" || self.ordenes_trabajo.models.length == 0) {
          alert("Ingrese al menos una orden de trabajo");
          return false;
        }

        var ordenes_trabajo = new Array();
        self.ordenes_trabajo.each(function(t){
          ordenes_trabajo.push(t.attributes);
        });

        this.model.set({
          "id_tipo_mantenimiento":self.$("#mantenimiento_tipos_mantenimiento").val(),
          "numero":self.$("#mantenimiento_numero").val(),
          "fecha":self.$("#mantenimiento_fecha").val(),
          "hora":self.$("#mantenimiento_hora").val(),
          "realizado":(self.$("#mantenimiento_realizado").is(":checked")?1:0),
          "observaciones":self.$("#mantenimiento_observaciones").val(),
          "ordenes_trabajo":ordenes_trabajo,
        });
        $(".error").removeClass("error");
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
      workspace.imprimir_reporte("/sistema/mantenimientos/function/ver_pdf/"+this.model.id);
    },

    eliminar: function() {
      if (confirm("Realmente desea eliminar el elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        this.cerrar();
      }
    },
  
  });
})(app);


// -----------------------------------------
//   ORDENES DE TRABAJO
// -----------------------------------------

(function ( models ) {
  models.OrdenTrabajo = Backbone.Model.extend({
    urlRoot: "ordenes_trabajo",
    defaults: {
      tipo_orden_trabajo: "",
      id_tipo_orden_trabajo: 0,
      numero: 0,
      precauciones: "",
      observaciones: "",
      id_empresa: ID_EMPRESA,
      id_empresa_tercerizada: 0,
      id_mantenimiento: 0,
      orden: 0,
      cantidad_personas: 1,
      responsable: "",
      tareas: [],
      duracion_cantidad: 1,
      duracion_tipo: "H",
      eliminado: 0,
    },
  });
})( app.models );

(function (collections, model) {
  collections.OrdenesTrabajo = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.OrdenTrabajo);

(function ( app ) {

  app.views.OrdenesTrabajoTableView = app.mixins.View.extend({

    template: _.template($("#ordenes_trabajo_resultados_template").html()),
        
    myEvents: {
      "change #ordenes_trabajo_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
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
        var view = new app.views.OrdenesTrabajoItemResultados({
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
  app.views.OrdenesTrabajoItemResultados = app.mixins.View.extend({
        
    template: _.template($("#ordenes_trabajo_item_resultados_template").html()),
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
          this.view.numeros.numero_orden_trabajo--;
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.OrdenTrabajoEditView({
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

  app.views.OrdenTrabajoEditView = app.mixins.View.extend({

    template: _.template($("#orden_trabajo_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "click .nueva_tarea":function(){
        var self = this;
        var v = new app.views.TareaEditView({
          model: new app.models.Tarea(),
          collection: self.tareas,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "escapable":false,
          "callback":function() {
            console.log(self.tareas);
          }
        });
      },
      "click .agregar_empresa_tercerizada":function(e) {
        var self = this;
        if ($(".empresas_tercerizadas_mini_nombre").length > 0) return;
        var form = new app.views.EmpresaTercerizadaMiniEditView({
          "model": new app.models.EmpresaTercerizada(),
          "callback":function(m){
            self.model.set({ "id_empresa_tercerizada":m });
            self.cargar_empresas_tercerizadas();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete empresas_tercerizadas_mini_nombre'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#empresas_tercerizadas_mini_nombre").focus();
      },
    },
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      var obj = { "id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      this.view = options.view;

      self.tareas = new app.collections.Tareas();
      var dep = this.model.get("tareas");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.Tarea(dd);
        self.tareas.add(ddo);
      }
      this.tareasTable = new app.views.TareasTableView({
        collection: self.tareas
      });
      this.$("#orden_trabajo_tareas").html(this.tareasTable.el);

      self.cargar_empresas_tercerizadas();
    },

    cerrar: function() {
      if (this.model.id == null) {
        this.view.numeros.numero_orden_trabajo--;
      }
      $('.modal:last').modal('hide');
    },

    cargar_empresas_tercerizadas: function() {
      if (control.check("empresas_tercerizadas")>0) {
        var self = this;
        new app.mixins.Select({
          modelClass: app.models.EmpresaTercerizada,
          url: "empresas_tercerizadas/",
          render: "#orden_trabajo_empresas_tercerizadas",
          firstOptions: ["<option value='0'>-</option>"],
          selected: self.model.get("id_empresa_tercerizada"),
        });
      }
    },

    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");

        if (typeof self.tareas.models == "undefined" || self.tareas.models.length == 0) {
          alert("Ingrese al menos una tarea");
          return false;
        }

        var tareas = new Array();
        self.tareas.each(function(t){
          tareas.push(t.attributes);
        });

        this.model.set({
          "id_empresa_tercerizada":self.$("#orden_trabajo_empresas_tercerizadas").val(),
          "id_tipo_orden_trabajo":self.$("#orden_trabajo_tipos_ordenes_trabajo").val(),
          "tipo_orden_trabajo":self.$("#orden_trabajo_tipos_ordenes_trabajo option:selected").text(),
          "duracion_cantidad":self.$("#orden_trabajo_duracion_cantidad").val(),
          "duracion_tipo":self.$("#orden_trabajo_duracion_tipo").val(),
          "tareas":tareas,
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
          this.view.numeros.numero_orden_trabajo++;
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);



// -----------------------------------------
//   TAREAS
// -----------------------------------------

(function ( models ) {
  models.Tarea = Backbone.Model.extend({
    urlRoot: "tareas",
    defaults: {
      tipo_tarea: "",
      id_tipo_tarea: 0,
      id_maquina: 0,
      maquina: "",
      id_parte: 0,
      parte: "",
      cantidad_desgaste: 0,
      tipo_desgaste: "H",
      observaciones: "",
      eliminado: 0,
      articulos: [],
    },
  });
})( app.models );

(function (collections, model) {
  collections.Tareas = Backbone.Collection.extend({
    model: model,
  });
})( app.collections, app.models.Tarea);

(function ( app ) {

  app.views.TareasTableView = app.mixins.View.extend({

    template: _.template($("#tareas_resultados_template").html()),
        
    myEvents: {
      "change #tareas_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_maquina = (typeof this.options.id_maquina != "undefined") ? this.options.id_maquina : 0;
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
        var view = new app.views.TareasItemResultados({
          model: item,
          collection: self.collection,
        });
        this.$(".tbody").append(view.render().el);        
      }
    },
            
  });

})(app);

(function ( app ) {
  app.views.TareasItemResultados = app.mixins.View.extend({
        
    template: _.template($("#tareas_item_resultados_template").html()),
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
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.TareaEditView({
        model:self.model,
        collection:self.collection,
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
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);


(function ( app ) {

  app.views.TareaEditView = app.mixins.View.extend({

    template: _.template($("#tarea_template").html()),
            
    myEvents: {
      "click #tarea_articulo_agregar":"agregar_articulo",
      "click .editar_articulo":"editar_articulo",
      "click .eliminar_articulo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #tarea_articulo_cantidad":function(e) {
        if (e.which == 13) this.agregar_articulo();
      },
      "click .guardar": "guardar",
      "click .cerrar": "cerrar",
      "change #tarea_maquinas":function(){
        this.cargar_partes();
      },
      "click .agregar_tipo_tarea":function(e) {
        var self = this;
        if ($(".tipos_tareas_mini_nombre").length > 0) return;
        var form = new app.views.TipoTareaMiniEditView({
          "model": new app.models.TipoTarea(),
          "callback":function(m){
            self.model.set({ "id_tipo_tarea":m });
            self.cargar_tipos_tareas();
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete tipos_tareas_mini_nombre'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#tipos_tareas_mini_nombre").focus();
      },
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      var obj = { "id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      self.cargar_tipos_tareas();
      self.cargar_maquinas();
      self.cargar_articulos();
      this.item_articulo = null;
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    cargar_tipos_tareas: function() {
      if (control.check("tipos_tareas")>0) {
        var self = this;
        new app.mixins.Select({
          modelClass: app.models.TipoTarea,
          url: "tipos_tareas/",
          render: "#tarea_tipos_tareas",
          firstOptions: ["<option value='0'>-</option>"],
          selected: self.model.get("id_tipo_tarea"),
        });
      }
    },

    cargar_maquinas: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Maquina,
        url: "maquinas/function/ver/",
        render: "#tarea_maquinas",
        selected: self.model.get("id_maquina"),
        onComplete: function() {
          self.cargar_partes();
        }
      });
    },

    cargar_partes: function() {
      var self = this;
      var id_maquina = this.$("#tarea_maquinas").val();
      new app.mixins.Select({
        modelClass: app.models.Maquina,
        url: "maquinas/function/ver_partes/"+id_maquina,
        render: "#tarea_partes",
        firstOptions: ["<option value='0'>Completa</option>"],
        selected: self.model.get("id_parte"),
      });
    },

    cargar_articulos: function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Articulo,
        url: "articulos/",
        render: "#tarea_articulos",
      });
    },

    agregar_articulo: function() {

      var id_articulo = $("#tarea_articulos").val();
      var descripcion = $("#tarea_articulo_descripcion").val();
      var articulo = $("#tarea_articulos option:selected").text();
      var cantidad = parseFloat($("#tarea_articulo_cantidad").val());
      if (isNaN(cantidad) || cantidad <= 0) {
        alert("Por favor ingrese una cantidad");
        $("#tarea_articulo_cantidad").select();
        return;
      }

      var tr = "<tr>";
      tr+="<td class='id_articulo editar_articulo dn'>"+id_articulo+"</td>";
      tr+="<td class='articulo editar_articulo'><span class='text-info'>"+articulo+"</td>";
      tr+="<td class='descripcion dn editar_articulo'>"+descripcion+"</td>";
      tr+="<td class='cantidad tar editar_articulo'>"+Number(cantidad).toFixed(2)+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_articulo'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_articulo == null) {
        $("#tarea_articulos_tabla tbody").append(tr);
      } else {
        $(this.item_articulo).replaceWith(tr);
        this.item_articulo = null;
      }
      $("#tarea_articulo_cantidad").val("");
      $("#tarea_articulo_descripcion").val("");
    },
    
    editar_articulo: function(e) {
      this.item_articulo = $(e.currentTarget).parents("tr");
      $("#tarea_articulos").val($(this.item_articulo).find(".id_articulo").text());
      $("#tarea_articulo_descripcion").val($(this.item_articulo).find(".descripcion").text());
      $("#tarea_articulo_cantidad").val($(this.item_articulo).find(".cantidad").text());
    },


    validar: function() {
      try {
        var self = this;
        $(".error").removeClass("error");

        var id_tipo_tarea = self.$("#tarea_tipos_tareas").val();
        if (id_tipo_tarea == 0) {
          alert("Por favor seleccione una tarea");
          return false;
        }
        var tipo_tarea = self.$("#tarea_tipos_tareas option:selected").text();

        var articulos = new Array();
        $("#tarea_articulos_tabla tbody tr").each(function(i,e){
          articulos.push({
            "id_articulo": $(e).find(".id_articulo").text(),
            "articulo": $(e).find(".articulo").text(),
            "descripcion": $(e).find(".descripcion").text(),
            "cantidad": $(e).find(".cantidad").text(),
          });
        });
        this.model.set({"articulos":articulos});

        this.model.set({
          "id_maquina":self.$("#tarea_maquinas").val(),
          "maquina":self.$("#tarea_maquinas option:selected").text(),
          "id_parte":self.$("#tarea_partes").val(),
          "parte":self.$("#tarea_partes option:selected").text(),
          "tipo_tarea":tipo_tarea,
          "id_tipo_tarea":id_tipo_tarea,
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