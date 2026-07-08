// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Docente = Backbone.Model.extend({
    urlRoot: "docentes/",
    defaults: {
      activo: 1,
      id_empresa: ID_EMPRESA,
      nombre: "",
      apellido: "",
      id_cliente: 0,
      cuit: "",
      email: "",
      telefono: "",
      celular: "",
      direccion: "",
      id_localidad: 0,
      localidad: "",
      id_departamento: 0,
      departamento: "",
      password: "",
      titulo: "",
      cuenta_bancaria: "",
      banco: "",
      fecha_ingreso: "",
      fecha_egreso: "",
      obra_social: "",
      numero_obra_social: "",
      inicio_docencia: "",
      telefono: "",
      celular: "",
      fecha_nac: "",
      path: "",
    }
  });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Docentes = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
      order_by: 'nombre',
      order: 'asc',
    },        
    paginator_core: {
      url: "docentes/function/ver"
    }
  });

})( app.collections, app.models.Docente, Backbone.Paginator);


// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.DocentesTableView = app.mixins.View.extend({

    template: _.template($("#docentes_panel_template").html()),

    myEvents: {
      "change #docentes_buscar":"buscar",
      "click #docentes_buscar_avanzada_btn":"buscar_avanzada",
    },        

    initialize : function (options) {
  		_.bindAll(this); // Para que this pueda ser utilizado en las funciones
  		var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      window.docentes_filter = (typeof window.docentes_filter != "undefined") ? window.docentes_filter : "";
      window.docentes_id_departamento = (typeof window.docentes_id_departamento != "undefined") ? window.docentes_id_departamento : 0;
      window.docentes_page = (typeof window.docentes_page != "undefined") ? window.docentes_page : 1;
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
      if (window.docentes_id_departamento != 0) this.$(".advanced-search-btn").trigger("click");
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;
      if (window.docentes_filter != this.$("#docentes_buscar").val().trim()) {
        window.docentes_filter = this.$("#docentes_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#docentes_buscar_comisiones").val() != null && window.docentes_id_departamento != this.$("#docentes_buscar_comisiones").val()) {
        window.docentes_id_departamento = this.$("#docentes_buscar_comisiones").val();
        cambio_parametros = true;
      }
      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.docentes_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.docentes_filter),
        "id_departamento":window.docentes_id_departamento,
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.docentes_page);
    },        

		addAll : function () {
      if (this.$(".seccion_vacia").is(":visible")) this.render();
      $(this.el).find(".tbody").empty();
      this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.DocenteItem({
				model: item,
        collection: this.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		},

    buscar_avanzada: function() {
      var self = this;
      self.id_departamento = self.$("#docentes_buscar_departamentos").val();
      this.buscar();
    },        

    open_advanced_search: function() {
      if (typeof this.advanced_search_opened === "undefined") this.cargar_departamentos();
    }, 

    cargar_departamentos: function() {
      var self = this;
      new app.mixins.Select({
          modelClass: app.models.Departamento,
          url: "departamentos/",
          render: "#docentes_buscar_departamentos",
          firstOptions: ["<option value='0'>Comision</option>"],
          selected: self.id_departamento,
          onComplete:function(c) {
            crear_select2("docentes_buscar_departamentos");
          }                    
      });            
    },      

	});
})(app);


// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.DocenteItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#docentes_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"clientes",
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
      location.href="app/#docente_acciones/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();   // Eliminamos el modelo
        $(this.el).remove();    // Lo eliminamos de la vista
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



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.DocenteEditView = app.mixins.View.extend({

		template: _.template($("#docentes_edit_panel_template").html()),
    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click #materias_tab":function() {
        var self = this;
        setTimeout(function(){
            self.render_calendar();
        },200);            
      },          
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var fecha_ingreso = this.model.get("fecha_ingreso");
      createdatepicker($(this.el).find("#docente_fecha_ingreso"),fecha_ingreso);
      var fecha_egreso = this.model.get("fecha_egreso");
      createdatepicker($(this.el).find("#docente_fecha_egreso"),fecha_egreso);
      var fecha_nac = this.model.get("fecha_nac");
      createdatepicker($(this.el).find("#docente_fecha_nac"),fecha_nac);

      // AUTOCOMPLETE DE LOCALIDADES
      $(this.el).find("#docente_localidad").autocomplete({
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
          self.model.set({
            "id_localidad":ui.item.id,
            "localidad":ui.item.label,
          });
        },
      });
            
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Departamento,
        url: "departamentos/",
        firstOptions: ["<option value='0'>Sin Definir</option>"],
        render: "#docente_departamentos",
        selected: self.model.get("id_departamento"),
      });      
      return this;
    },

        render_calendar: function() {
            var self = this;
            this.$("#calendar").fullCalendar({
                allDaySlot: false,
                height: 500,
                lang: "es",
                defaultView: 'agendaWeek',
                editable: false,
                eventSources : [{
                    "url": "clases/function/calendario/",
                    "data": {
                        "id_docente":self.model.id,
                    },
                }],
                eventRender: function(event, element) {
                    element.find('.fc-title').append("<br/>"+event.description);
                },                
                dayClick: function(day, jsEvent, view) {
                    var modelo = new app.models.Clase({
                        fecha: day.format("DD/MM/YYYY"),
                        hora: day.format("HH:mm:ss"),
                        id_docente: self.model.id,
                    });
                    app.views.clase = new app.views.ClaseEditView({
                        "model":modelo,
                        "mostrar_comisiones":true,
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
                                "mostrar_comisiones":true,
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

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("docente_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        validate_input("docente_apellido",IS_EMPTY,"Por favor, ingrese un apellido.");
        validate_input("docente_email",IS_EMAIL,"Por favor, ingrese un email.");

        this.model.set({
          "id_departamento":$("#docente_departamentos").val(),
          "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
        });

        var password_1 = $("#docente_password").val();
        var password_2 = $("#docente_password_2").val();

        // Si estamos insertando uno nuevo, el password es obligatorio
        if (this.model.id == null && isEmpty(password_1)) {
          show("Por favor ingrese una clave.");
          $("#docente_password").focus();
          return false;
        }

        if (password_1 != password_2) {
          show("ERROR: Las claves no coinciden. Ingrese nuevamente.");
          $("#docente_password_2").focus();
          return false;
        }
        if (!isEmpty(password_1)) {
          password_1 = hex_md5(password_1);
          this.model.set({
            "password":password_1
          });                    
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
            location.href="app/#docentes";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Docente();
      this.render();
    },
		
	});

})(app.views, app.models);






(function ( app ) {

  app.views.AsistenciasDocentesTableView = app.mixins.View.extend({
    template: _.template($("#asistencias_docentes_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
      "change #asistencias_docentes_buscar_fecha":"buscar",
      "click .guardar":"guardar",
      "click .imprimir":function() {
        var self = this;
        self.id_materia = (ASISTENCIA_DOCENTE_POR_MATERIA == 1) ? this.$("#asistencias_docentes_buscar_materias").val() : 0;
        self.fecha = this.$("#asistencias_docentes_buscar_fecha").val().replace(/\//g,"-");
        var url = "asistencias_docentes/function/imprimir/";
        url+="?id_comision="+window.comision.id;
        url+="&id_materia="+self.id_materia;
        url+="&fecha="+self.fecha;
        workspace.imprimir_reporte(url);
      },
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.id_materia = (typeof this.options.id_materia != "undefined") ? this.options.id_materia : 0;
      this.id_clase = 0;
      this.render();
      this.buscar();
    },

    render: function() {
      var self = this;
      var self = this;
      var obj = {};
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      obj.id_clase = this.id_clase;
      $(this.el).html(this.template(obj));
      createdatepicker($(this.el).find("#asistencias_docentes_buscar_fecha"),new Date());
    },

    render_materias: function() {
      // El filtro por materias NO es obligatorio
      var self = this;
      var id_carrera = window.comision.get("id_carrera");
      var anio = window.comision.get("anio");
      $.ajax({
        "url":"materias/function/get_select/",
        "dataType":"json",
        "data": {
          "id_carrera":id_carrera,
          "anio":anio,
          //"id_docente":ID_DOCENTE, // Si esta logueado un profesor, filtramos solo por sus materias
        },
        "success":function(res) {
          var r = "";
          for(var i=0; i<res.results.length;i++) {
            var o = res.results[i];
            r += "<option "+((o.id == self.id_materia) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#asistencias_docentes_buscar_materias").html(r);
        },
      });
    },

    calcular: function() {
      var asistencias_docentes = 0;
      var inasistencias_docentes = 0;
      var total = $("#asistencias_docentes_table tbody tr").length;
      $("#asistencias_docentes_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") asistencias_docentes++;
          else if (condicion == "A" || condicion == "J") inasistencias_docentes++;
        }
      });
      var porc_in = ((total > 0) ? (inasistencias_docentes / total)*100 : 0);
      $("#asistencias_docentes_inasistencia").html(inasistencias_docentes+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (asistencias_docentes / total)*100 : 0);
      $("#asistencias_docentes_asistencia").html(asistencias_docentes+" ("+Number(porc_as).toFixed(2)+" %)");
    },

    buscar: function() {
      var self = this;
      self.fecha = this.$("#asistencias_docentes_buscar_fecha").val().replace(/\//g,"-");
      $.ajax({
        "url":"asistencias/function/buscar_docentes/",
        "dataType":"json",
        "type":"get",
        "data": {
          "fecha":self.fecha,
        },
        "success":function(res) {
          if (res.error == 0) {
            self.id_clase = res.id_clase;
            self.render_table(res.results);
            self.calcular();
          } else {
            var tr = "<tr><td colspan='20'>No hay clases cargadas en esta fecha.</td></tr>";
            self.$("#asistencias_docentes_table .tbody").empty();
            self.$("#asistencias_docentes_table .tbody").append(tr);
          }
        },
      });
    },  

    render_table: function(res) {
      var self = this;
      this.$("#asistencias_docentes_table .tbody").empty();
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.AsistenciasDocentesItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
        });
        this.$("#asistencias_docentes_table .tbody").append(view.el);
      }
    },

    guardar: function() {
      var self = this;
      var error = false;
      var asistencias_docentes = new Array();
      $("#asistencias_docentes_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length == 0) {
          alert("Por favor seleccione un tipo de asistencia");
          error = true;
          return false;
        }
        asistencias_docentes.push({
          "id_docente":$(e).find(".id_docente").val(),
          "fecha":$(e).find(".fecha").val(),
          "observaciones":$(e).find(".observaciones").val(),
          "condicion":$(e).find(".condicion.active").data("valor"),
        });
      });
      if (error) return false;
      $.ajax({
        "url":"asistencias/function/guardar/",
        "dataType":"json",
        "type":"post",
        "data": {
          "asistencias":JSON.stringify(asistencias_docentes),
          "id_clase": self.id_clase,
        },
        "success":function(r) {
          if (r.error == 0) {
            window.history.back();
          } else {
            alert("Hubo un error al guardar las asistencias_docentes.");
          }
        },
        "error":function() {
          alert("Hubo un error al guardar las asistencias_docentes.");
        },
      });
    },

  });
})(app);

(function ( app ) {
  app.views.AsistenciasDocentesItemView = app.mixins.View.extend({
    template: _.template($("#asistencias_docentes_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .condicion":function(e){
        this.$(".condicion.active").removeClass("btn-primary");
        this.$(".condicion.active").removeClass("btn-danger");
        this.$(".condicion.active").removeClass("btn-warning");
        this.$(".condicion.active").removeClass("btn-success");
        this.$(".condicion").removeClass("active");
        var condicion = $(e.currentTarget).data("valor");
        $(e.currentTarget).addClass("active");
        if (condicion == "P") $(e.currentTarget).addClass('btn-success');
        else if (condicion == "T") $(e.currentTarget).addClass('btn-warning');
        else if (condicion == "A") $(e.currentTarget).addClass('btn-danger');
        else if (condicion == "J") $(e.currentTarget).addClass('btn-primary');
        this.view.calcular();
      },
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);


// ===============================================
//
// ASISTENCIAS ENTRE FECHAS


(function ( app ) {

  app.views.ReporteAsistenciasDocentesTableView = app.mixins.View.extend({
    template: _.template($("#asistencias_docentes_reporte_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
      "change .trimestre_select":function(e) {
        var s = this.$(e.currentTarget).find("option:selected");
        this.$("#asistencias_docentes_reporte_fecha_desde").val($(s).data("desde"));
        this.$("#asistencias_docentes_reporte_fecha_hasta").val($(s).data("hasta"));
        if ($(e.currentTarget).val() == 0) { 
          this.$("#asistencias_docentes_reporte_fecha_desde").parent().show();
          this.$("#asistencias_docentes_reporte_fecha_hasta").parent().show();
        } else {
          this.$("#asistencias_docentes_reporte_fecha_desde").parent().hide();
          this.$("#asistencias_docentes_reporte_fecha_hasta").parent().hide();
        }
        this.buscar();
      },
      "change #asistencias_docentes_reporte_fecha_desde":"buscar",
      "change #asistencias_docentes_reporte_fecha_hasta":"buscar",
      "click .imprimir":function() {
        var self = this;
        self.id_materia = (ASISTENCIA_DOCENTE_POR_MATERIA == 1) ? this.$("#asistencias_docentes_buscar_materias").val() : 0;
        //self.fecha_desde = this.$("#asistencias_docentes_reporte_fecha_desde").val();
        //self.fecha_hasta = this.$("#asistencias_docentes_reporte_fecha_hasta").val();
        var fechas = new Array();
        this.$(".check_fecha:checked").each(function(i,e){
          var v = $(e).val();
          v = v.replace(/\//g,"-");
          fechas.push(v);
        });
        if (fechas.length == 0) {
          alert("Por favor marque las fechas que desea imprimir.");
          return;
        }
        if (fechas.length > 5) {
          alert("Por favor marque un maximo de hasta 5 fechas para imprimir.");
          return;          
        }
        var url = "asistencias/function/imprimir_docentes_entre_fechas/";
        url+="?fechas="+fechas.join("--");
        workspace.imprimir_reporte(url);
      },
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      this.id_materia = (typeof this.options.id_materia != "undefined") ? this.options.id_materia : 0;
      this.clases = new Array();
      this.render();
    },

    render: function() {
      var self = this;
      var obj = {};
      obj.id = 0;
      obj.permiso = this.permiso;
      obj.seleccionar = this.habilitar_seleccion;
      obj.clases = this.clases;
      obj.fecha_desde = this.fecha_desde;
      obj.fecha_hasta = this.fecha_hasta;
      $(this.el).html(this.template(obj));

      this.$(".trimestre_select").trigger("change");
      this.fecha_desde = this.$("#asistencias_docentes_reporte_fecha_desde").val();
      this.fecha_hasta = this.$("#asistencias_docentes_reporte_fecha_hasta").val();

      createdatepicker($(this.el).find("#asistencias_docentes_reporte_fecha_desde"),moment(this.fecha_desde,"DD/MM/YYYY").toDate());
      createdatepicker($(this.el).find("#asistencias_docentes_reporte_fecha_hasta"),moment(this.fecha_hasta,"DD/MM/YYYY").toDate());
    },

    render_materias: function() {
      // El filtro por materias NO es obligatorio
      var self = this;
      $.ajax({
        "url":"materias/function/get_select/",
        "dataType":"json",
        "data": {
          "id_carrera":window.comision.get("id_carrera"),
          "anio":window.comision.get("anio"),
          //"id_docente":ID_DOCENTE, // Si esta logueado un profesor, filtramos solo por sus materias
        },
        "success":function(res) {
          var r = "";
          for(var i=0; i<res.results.length;i++) {
            var o = res.results[i];
            r += "<option "+((o.id == self.id_materia) ? "selected":"")+" value='"+o.id+"'>"+o.nombre+"</option>";
          }
          $("#asistencias_docentes_reporte_materias").html(r);
        },
      });
    },

    calcular: function() {
      var asistencias_docentes = 0;
      var inasistencias_docentes = 0;
      var total = $("#asistencias_docentes_table tbody tr").length;
      $("#asistencias_docentes_table tbody tr").each(function(i,e){
        if ($(e).find(".condicion.active").length > 0) {
          var condicion = $(e).find(".condicion.active").first().data("valor");
          if (condicion == "P" || condicion == "T") asistencias_docentes++;
          else if (condicion == "A" || condicion == "J") inasistencias_docentes++;
        }
      });
      var porc_in = ((total > 0) ? (inasistencias_docentes / total)*100 : 0);
      $("#asistencias_docentes_inasistencia").html(inasistencias_docentes+" ("+Number(porc_in).toFixed(2)+" %)");
      var porc_as = ((total > 0) ? (asistencias_docentes / total)*100 : 0);
      $("#asistencias_docentes_asistencia").html(asistencias_docentes+" ("+Number(porc_as).toFixed(2)+" %)");
    },

    buscar: function() {
      var self = this;
      self.id_materia = (ASISTENCIA_DOCENTE_POR_MATERIA == 1) ? this.$("#asistencias_docentes_buscar_materias").val() : 0;
      self.fecha_desde = this.$("#asistencias_docentes_reporte_fecha_desde").val();
      self.fecha_hasta = this.$("#asistencias_docentes_reporte_fecha_hasta").val();
      $.ajax({
        "url":"asistencias/function/buscar_docentes_fechas/",
        "dataType":"json",
        "type":"get",
        "data": {
          "id_materia":self.id_materia,
          "fecha_desde":self.fecha_desde.replace(/\//g,"-"),
          "fecha_hasta":self.fecha_hasta.replace(/\//g,"-"),
        },
        "success":function(res) {
          if (res.error == 0) {
            var table = new app.views.AsistenciasDocentesReporteTablaView({
              model: new app.models.AbstractModel(res),
              clases: res.clases,
            });
            self.$("#asistencias_docentes_reporte_tabla").html(table.el);

            var t = "";
            for(var i=0; i<res.results.length;i++) {
              var alumno = res.results[i];
              t+='<tr class="h64">';
              t+='<td class="ver hidden-xs">'+alumno.numero+'</td>';
              t+='<td class="ver hidden-xs">';
              if (!isEmpty(alumno.path)) {
                t+='<img src="/sistema/'+alumno.path+'" class="customcomplete-image"/>';
              } else {
                t+='<span class="avatar xs avatar-texto bg-info %> pull-left">';
                t+= isEmpty(alumno.nombre) ? "" : alumno.nombre.toUpperCase().substr(0,1);
                t+='</span>';
              }
              t+='</td>';
              t+='<td class="ver"><span class="text-info">'+alumno.nombre.ucwords()+'</span></td>';
              t+='</tr>';
            }
            self.$("#reporte_asistencias_docentes_table_nombres tbody").html(t);

            $('[data-toggle="tooltip"]').tooltip(); 
          }
        },
      });
    },  

  });
})(app);

(function ( app ) {
  app.views.AsistenciasDocentesReporteTablaView = app.mixins.View.extend({
    template: _.template($("#asistencias_docentes_reporte_tabla_template").html()),
    initialize : function (options) {
      this.view = options.view;
      this.clases = options.clases;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template({
        "clases":self.clases,
      }));

      this.$("#reporte_asistencias_docentes_table .tbody").empty();
      var res = this.model.get("results");
      for(var i=0;i<res.length;i++) {
        var r = res[i];
        r.numero = i+1;
        var view = new app.views.AsistenciasDocentesReporteItemView({
          "model":new app.models.AbstractModel(r),
          "view":self,
          "clases":self.clases,
        });
        this.$("#reporte_asistencias_docentes_table .tbody").append(view.el);
      }
    },
  });
})(app);

(function ( app ) {
  app.views.AsistenciasDocentesReporteItemView = app.mixins.View.extend({
    template: _.template($("#asistencias_docentes_reporte_item_template").html()),
    tagName: "tr",
    className: "h64",
    myEvents: {
    },
    initialize : function (options) {
      this.view = options.view;
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);