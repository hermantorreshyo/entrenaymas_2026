// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CampaniaEnvio = Backbone.Model.extend({
    urlRoot: "campanias_envios/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
      estado: "A",
      comienzo_ejecucion: "",
      fin_ejecucion: "",
      resultado_ejecucion: "",
      metodo: "E",
      destinatarios: "",
      filtros: "",
      filtros_array: [],
      fecha: "",
      fecha_inicio: "",
      fecha_fin: "",
      hora: "08:00",
      id_email_template: 0,
      asunto: "",
      texto: "",
      total_por_enviar: 0,
      total_enviados: 0,

      lunes: 1,
      martes: 1,
      miercoles: 1,
      jueves: 1,
      viernes: 1,
      sabado: 1,
      domingo: 1,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.CampaniasEnvios = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "campanias_envios/"
    }
    
  });

})( app.collections, app.models.CampaniaEnvio, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.CampaniaEnvioItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#campanias_envios_item').html()),
    events: {
      "click .ver": "editar",
      "click .eliminar": "borrar",
      "click .duplicar": "duplicar"
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
      location.href="app/#campania_envio/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar la etiqueta?")) {
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

  app.views.CampaniasEnviosTableView = app.mixins.View.extend({

   template: _.template($("#campanias_envios_panel_template").html()),

   initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: lista
      });

      lista.on('add', this.addOne, this);
      lista.on('all', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CampaniaEnvioItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.CampaniaEnvioEditView = app.mixins.View.extend({

    template: _.template($("#campanias_envios_edit_panel_template").html()),

    myEvents: {
      "click .cargar_plantilla":"cargar_plantilla",
      "click .guardar_plantilla":"guardar_plantilla",
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click .agregar_filtro": function(){
        this.agregar_filtro({
          "table":"aca_alumnos",
          "filtro":"",
          "primero":0,
        });
      },
      "change .tipo_envio":function(e) {
        var tipo_envio = this.$("input[name=tipo_envio]:checked").val();
        if (tipo_envio == "unico") {
          this.$(".tipo_envio_multiple").hide();
          this.$(".tipo_envio_unico").show();
          this.$("#campanias_envios_fecha_inicio").val("").trigger("change");
          this.$("#campanias_envios_fecha_fin").val("").trigger("change");
        } else if (tipo_envio == "multiple") {
          this.$(".tipo_envio_unico").hide();
          this.$(".tipo_envio_multiple").show();
          this.$("#campanias_envios_fecha").val("").trigger("change");
        }
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
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var fecha = this.model.get("fecha");
      createdatepicker($(this.el).find("#campanias_envios_fecha"),fecha);

      var fecha_inicio = this.model.get("fecha_inicio");
      createdatepicker($(this.el).find("#campanias_envios_fecha_inicio"),fecha_inicio);

      var fecha_fin = this.model.get("fecha_fin");
      createdatepicker($(this.el).find("#campanias_envios_fecha_fin"),fecha_fin);

      this.$(".hora").mask("99:99");

      var filtros = this.model.get("filtros");
      if (isEmpty(filtros)) {
        this.agregar_filtro({
          table: ((typeof window.tabla_campania != undefined) ? window.tabla_campania : "aca_alumnos"),
          filtro: ((typeof window.filtro_campania != undefined) ? window.filtro_campania : ""),
          primero: 1,
        });  
      } else {
        filtros = filtros.replace(/\'/g,'"');
        filtros = JSON.parse(filtros);
        for(var i=0;i<filtros.length;i++) {
          var f = filtros[i];
          this.agregar_filtro({
            table: f.table,
            filtro: f.filtro,
            primero: (i==0)?1:0,
          });  
        }
      }
      return this;
    },

    cargar_plantilla: function() {
      var self = this;
      window.email_template_seleccionado = null;
      var lista = new app.views.EmailsTemplatesTableView({
        collection: new app.collections.EmailsTemplates(),
        habilitar_seleccion: true,
      });
      crearLightboxHTML({
        "html":lista.el,
        "width":450,
        "height":140,
        "callback":function() {
          // Si selecciono algun template
          if (window.email_template_seleccionado != null) {
            var texto = window.email_template_seleccionado.get("texto");
            var nombre = window.email_template_seleccionado.get("nombre");
            CKEDITOR.instances['campanias_envios_texto'].setData(texto);
            self.$("#campanias_envios_nombre").val(nombre);
          }
        }
      });
    },
    guardar_plantilla: function() {
      var self = this;
      var nombre = this.$("#campanias_envios_nombre").val();
      if (isEmpty(nombre)) {
        alert("Por favor ingrese un asunto para guardar la plantilla.");
        this.$("#campanias_envios_nombre").focus();
        return;
      }
      var texto = CKEDITOR.instances['campanias_envios_texto'].getData();
      if (isEmpty(texto)) {
        alert("Por favor ingrese algun texto en la plantilla que desea guardar.");
        return;
      }
      var template = new app.models.EmailTemplate({
        "nombre":nombre,
        "texto":texto,
      });
      template.save({},{
        "success":function() {
          alert("La plantilla se ha guardado con exito.");
        }
      })
    },

    render_destinatarios: function() {
    },

    agregar_filtro: function(obj) {
      var d = new app.views.CampaniaEnvioDestinatarioView({
        model: new app.models.AbstractModel(obj)
      });
      this.$("#campania_envio_destinatarios").append(d.el);
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios
        validate_input("campanias_envios_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Tomamos los destinatarios
        var error = false;
        var filtros = new Array();
        this.$("#campania_envio_destinatarios .filtro_row").each(function(i,e){
          var h = $(e).find(".destinatarios option:selected").data("habilitar_filtro");
          var v = $(e).find(".filtro").val();
          if (h == 1 && v == null) { // Si tiene el filtro seleccionado, pero sin valor
            error = true;
            alert("Por favor seleccione un valor");
            $(e).find(".filtro").focus();
            return false; // Corta el each
          }
          filtros.push({
            "table":$(e).find(".destinatarios").val(),
            "filtro":(h==1)?v.join("-"):0,
          });
        });
        if (error) return false;
        this.model.set({
          "filtros":JSON.stringify(filtros),
        });
        // Texto del articulo
        if (self.$("#campanias_envios_texto").length > 0) {
          var cktext = CKEDITOR.instances['campanias_envios_texto'].getData();
          self.model.set({"texto":cktext});
        }

        self.model.set({
          "id_usuario":ID_USUARIO,
          "id_empresa":ID_EMPRESA,
        })

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
        this.model.save({},{
          success: function(model,response) {
            location.href="app/#campanias_envios";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.CampaniaEnvio()
      this.render();
    },

  });

})(app.views, app.models);


(function ( views, models ) {

  views.CampaniaEnvioDestinatarioView = app.mixins.View.extend({
    template: _.template($("#campanias_envios_destinatarios_template").html()),
    myEvents: {
      "change .destinatarios":function(e) {
        var h = $(e.currentTarget).find("option:selected").data("habilitar_filtro");
        this.$(".filtro").prop("disabled",(h==0));
        if (h == 0) this.$(".filtro").val([]).trigger("change");
      },
      "click .eliminar_filtro": function() {
        $(this.el).remove();
      }
    },
    initialize: function(options) {
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      this.$(".destinatarios").trigger("change");
      this.$(".filtro").select2({
        placeholder: "Comisiones",
      }).trigger("change");
    }
  });

})(app.views, app.models);