// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TurnoServicio = Backbone.Model.extend({
    urlRoot: "turnos_servicios/",
    defaults: {
      nombre: "",
      activo: 1,
      destacado: 0,
      path: "",
      texto: "",
      texto_en: "",
      texto_pt: "",
      link: "",
      duracion_turno: 15,
      horarios: [],
      hora_desde: "",
      hora_hasta: "",
      deshabilitado_desde: "",
      deshabilitado_hasta: "",
      color: "",
      id_usuario: 0,
      usuario: "",
      costo: 0,
      nuevo: 0, // Usado en lightbox
      direccion: "",
      localidad: "",
      id_localidad: 0,
      id_provincia: 0,
      provincia: "",
      pais: "",

      horario_lunes_1:"",
      horario_lunes_2:"",
      horario_martes_1:"",
      horario_martes_2:"",
      horario_miercoles_1:"",
      horario_miercoles_2:"",
      horario_jueves_1:"",
      horario_jueves_2:"",
      horario_viernes_1:"",
      horario_viernes_2:"",
      horario_sabado_1:"",
      horario_sabado_2:"",            
      horario_domingo_1:"",
      horario_domingo_2:"",

      horario_lunes_3:"",
      horario_lunes_4:"",
      horario_martes_3:"",
      horario_martes_4:"",
      horario_miercoles_3:"",
      horario_miercoles_4:"",
      horario_jueves_3:"",
      horario_jueves_4:"",
      horario_viernes_3:"",
      horario_viernes_4:"",
      horario_sabado_3:"",
      horario_sabado_4:"",            
      horario_domingo_3:"",
      horario_domingo_4:"",   

    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.TurnosServicios = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "turnos_servicios/function/ver/"
    },

    paginator_ui: {
      perPage: 10,
      order_by: 'nombre',
      order: 'asc',
    },

  });

})( app.collections, app.models.TurnoServicio, Backbone.Paginator);



//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.TurnoServicioItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#turnos_servicios_item').html()),
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar": "borrar",
      "click .duplicar": "duplicar",
      "keyup .radio":function(e) {
        if (e.which == 13) { this.seleccionar(); }
      },
      "focus .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
      },
      "blur .radio":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
      },
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"med_turnos_servicios",
          "attribute":"activo",
          "value":activo,
          "url":"turnos_servicios/function/change_property/",
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"med_turnos_servicios",
          "attribute":"destacado",
          "value":destacado,
          "url":"turnos_servicios/function/change_property/",
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      if (this.lightbox) {
        var self = this;
        var v = new app.views.TurnoServicioEditView({
          model:self.model,
          collection:self.collection,
          lightbox: true,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
        });
      } else {
        if (!this.lightbox) location.href="app/#turno_servicio/"+this.model.id;
      }
    },
    initialize: function(options) {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.lightbox = (this.options.lightbox == undefined || this.options.lightbox == false) ? false : true;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso, lightbox: this.lightbox };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista                
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



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.TurnosServiciosTableView = app.mixins.View.extend({

    template: _.template($("#turnos_servicios_panel_template").html()),

    myEvents: {
      "keydown #turnos_servicios_table tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $(".basic_search").focus(); }
      },
      "click .exportar_excel":"exportar",
      "click .importar_excel":"importar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar_csv",
      "change #turnos_servicios_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.lightbox = (this.options.lightbox == undefined || this.options.lightbox == false) ? false : true;
      this.id_usuario = (options.id_usuario == undefined ? 0 : options.id_usuario);
      this.permiso = this.options.permiso;
      window.turnos_servicios_filter = (typeof window.turnos_servicios_filter != "undefined") ? window.turnos_servicios_filter : "";
      window.turnos_servicios_page = (typeof window.turnos_servicios_page != "undefined") ? window.turnos_servicios_page : 1;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.buscar();
    },

    render: function() {
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "lightbox":this.lightbox
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);
    },

    buscar: function() {
      var cambio_parametros = false;
      if (this.$("#turnos_servicios_buscar").length > 0) {
        if (window.turnos_servicios_filter != this.$("#turnos_servicios_buscar").val().trim()) {
          window.turnos_servicios_filter = this.$("#turnos_servicios_buscar").val().trim();
          cambio_parametros = true;
        }
      }
      if (cambio_parametros) window.turnos_servicios_page = 1;
      var datos = {
        "term":encodeURIComponent(window.turnos_servicios_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO;
      if (this.id_usuario != 0) datos.id_usuario = this.id_usuario;
      this.collection.server_api = datos;
      this.collection.goTo(window.turnos_servicios_page);
    },

    exportar: function() {
      this.exportar_excel({
        "filename":"turnos_servicios",
        "table":"turnos_servicios",
      });            
    },

    importar: function() {
    },

    exportar_csv: function(obj) {
      window.open("turnos_servicios/function/exportar_csv/","_blank");
    },

    importar_csv: function() {
      app.views.importar = new app.views.Importar({
        "table":"turnos_servicios",
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    }, 

    addAll : function () {
      window.turnos_servicios_page = this.pagination.getPage();
      this.$("#turnos_servicios_table tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (this.collection.length > 0) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        } else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.TurnoServicioItem({
        model: item,
        permiso: this.permiso,
        collection: this.collection,
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

  views.TurnoServicioEditView = app.mixins.View.extend({

    template: _.template($("#turnos_servicios_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click #horario_agregar":"agregar_horario",
      "click .editar_horario":"editar_horario",
      "click .eliminar_horario":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #turnos_servicios_horario_hasta":function(e) {
        if (e.which == 13) this.agregar_horario();
      },
      "change #turno_servicio_provincia":"cargar_localidades",
    },

    cargar_localidades: function() {
      if (ID_EMPRESA != 1319) return;
      var id_departamento = this.$("#turno_servicio_provincia").val();
      var id_localidad = this.model.get("id_localidad");
      new app.mixins.Select({
        modelClass: app.models.Localidad,
        url: "/sistema/localidades/function/get_by_departamento/"+id_departamento,
        render: "#turno_servicio_localidad",
        selected: id_localidad,
        onComplete:function(c) {
          crear_select2("turno_servicio_localidad");
        }
      });      
    },

    initialize: function(options) {
      this.options = options;
      this.model.bind("destroy",this.render,this);
      this.guardando = 0;
      this.lightbox = (options.lightbox != undefined) ? options.lightbox : false;
      _.bindAll(this);
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion, "id":this.model.id, "lightbox": self.options.lightbox };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      $(this.el).find("#turnos_servicios_horario_desde").mask("99:99");
      $(this.el).find("#turnos_servicios_horario_hasta").mask("99:99");

      createdatepicker(this.$("#turnos_servicios_deshabilitado_desde"),this.model.get("deshabilitado_desde"));
      createdatepicker(this.$("#turnos_servicios_deshabilitado_hasta"),this.model.get("deshabilitado_hasta"));

      this.cargar_localidades();

      this.$(".color").colorpicker({
        format: "rgba"
      });

      // AUTOCOMPLETE DE LOCALIDADES
      if (this.$("#turno_servicio_localidad").length > 0) {
        $(this.el).find("#turno_servicio_localidad").autocomplete({
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
      }

      return this;
    },

    agregar_horario: function() {
      // Controlamos los valores
      var desde = $("#turnos_servicios_horario_desde").val();
      if (isEmpty(desde)) {
        alert("Por favor ingrese una fecha");
        $("#turnos_servicios_horario_desde").focus();
        return;
      }
      var hasta = $("#turnos_servicios_horario_hasta").val();
      if (isEmpty(hasta)) {
        alert("Por favor ingrese una fecha");
        $("#turnos_servicios_horario_hasta").focus();
        return;
      }
      var dia = $("#turnos_servicios_horario_dia").val();
      var nombre_dia = $("#turnos_servicios_horario_dia option:selected").text();
      var tr = "<tr>";
      tr+="<td class='dn dia'>"+dia+"</td>";
      tr+="<td class='editar_horario'><span class='text-info'>"+nombre_dia+"</td>";
      tr+="<td class='desde editar_horario'>"+desde+"</td>";
      tr+="<td class='hasta editar_horario'>"+hasta+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_horario'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_horario == null) {
        $("#turnos_servicios_horarios_tabla tbody").append(tr);
      } else {
        $(this.item_horario).replaceWith(tr);
        this.item_horario = null;
      }
      $("#turnos_servicios_horario_desde").val("");
      $("#turnos_servicios_horario_hasta").val("");
      $("#turnos_servicios_horario_dia").focus();
    },
    
    editar_horario: function(e) {
      this.item_horario = $(e.currentTarget).parents("tr");
      $("#turnos_servicios_horario_dia").val($(this.item_horario).find(".dia").text());
      $("#turnos_servicios_horario_desde").val($(this.item_horario).find(".desde").text());
      $("#turnos_servicios_horario_hasta").val($(this.item_horario).find(".hasta").text());
    },

    validar: function() {
      try {
        validate_input("turnos_servicios_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        $(".error").removeClass("error");

        var k = 0;
        var horarios = new Array();
        $("#turnos_servicios_horarios_tabla tbody tr").each(function(i,e){
          horarios.push({
            "dia": $(e).find(".dia").text(),
            "desde": $(e).find(".desde").text(),
            "hasta": $(e).find(".hasta").text(),
          });
          k++;
        });
        this.model.set({"horarios":horarios});

        if (this.$(".color").length > 0) {
          var color = this.$(".color").colorpicker('getValue');
          this.model.set({
            "color":color,
          });
        }

        if (this.$("#turnos_servicios_usuarios").length > 0) {
          this.model.set({
            "id_usuario":self.$("#turnos_servicios_usuarios").val(),
          });
        }

        if (this.$("#turno_servicio_provincia").length > 0) {
          this.model.set({
            "id_provincia":self.$("#turno_servicio_provincia").val(),
            "provincia":self.$("#turno_servicio_provincia option:selected").text(),
            "pais":self.$("#turno_servicio_pais").val(),
          });
        }

        if (ID_EMPRESA == 1319) {
          this.model.set({
            "id_localidad":self.$("#turno_servicio_localidad").val(),
            "localidad":self.$("#turno_servicio_localidad option:selected").text(),
          });          
        }

        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() 
    {
      var self = this;
      if (this.validar() && this.guardando == 0) {
        this.guardando = 1;

        if (this.lightbox) {
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

        } else {

          if (this.model.id == null) {
            this.model.set({id:0});
          }
          this.model.save({},{
            success: function(model,response) {
              if (response.error == 1) {
                show(response.mensaje);
              } else {
                location.reload(true);
                //location.href="app/#turnos_servicios";
              }
              self.guardando = 0;
            },
            error: function() {
              show("Ocurrio un error al guardar los datos.");
              self.guardando = 0;
            }
          });
        }
      }
    },

    limpiar : function() {
      this.model = new app.models.TurnoServicio();
      this.render();
    },
  });

})(app.views, app.models);