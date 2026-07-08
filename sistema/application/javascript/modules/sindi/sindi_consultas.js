(function ( models ) {

  models.SindiConsulta = Backbone.Model.extend({
    urlRoot: "sindi_consultas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      numero: 0,
      fecha: moment().format("DD/MM/YYYY"),
      id_afiliado: 0,
      id_paciente: 0,
      id_tipo_bono: 0,
      id_condicion_especial: 0,
      id_concepto: 0,
      anulada: 0,
      hospital: 0,
      importe: SINDI_VALOR_CONSULTA,
      nombreafiliado: "",
      condicionespecial: ""
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiConsultas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_consultas/function/buscar/"
    }
  });
})( app.collections, app.models.SindiConsulta, Backbone.Paginator);


(function ( app ) {
  app.views.SindiConsultaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_consultas_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar"
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.tabla = options.tabla;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = {
        permiso: this.permiso,
        small: ((typeof this.options.small != "undefined") ? this.options.small : 0),
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      var self = this;
      var sindi_consulta = new app.models.SindiConsulta({ "id": self.model.id });
      sindi_consulta.fetch({
        "success":function() {
          var that = self
          var vista = new app.views.SindiConsultaEditView({
            model: sindi_consulta,
            permiso: control.check("sindi_consultas")
          });
          crearLightboxHTML({
            "html":vista.el,
            "width":900,
            "height":140,
            "escapable":false,
            "callback":function() {
              that.tabla.collection.pager();
            },
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

  app.views.SindiConsultasTableView = app.mixins.View.extend({

   template: _.template($("#sindi_consultas_panel_template").html()),

    myEvents: {
      "click .nuevo":function() {
        var self = this;
        this.sindi_bonos.buscar_por_codigo(self.nuevo);
      },
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      var small = ((typeof this.options.small != "undefined") ? this.options.small : 0);
      this.sindi_bonos = ((typeof this.options.sindi_bonos != "undefined") ? this.options.sindi_bonos : 0);

      if (small == 0) {
        var pagination = new app.mixins.PaginationView({
          collection: self.collection
        });
        var search = new app.mixins.SearchView({
          collection: self.collection,
        });
      }

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = {
        permiso: this.permiso,
        small: small,
      };

      // Cargamos el template
      $(this.el).html(this.template(obj));
      if (small == 0) {
        $(this.el).find(".pagination_container").html(pagination.el);
        $(this.el).find(".search_container").html(search.el);
      }

      // Vamos a buscar los elementos y lo paginamos
      self.collection.pager();
    },

    nuevo: function() {
      if (window.afiliado == null) {
        alert("Por favor selecione un afiliado");
        return;
      }
      if (window.afiliado.estado_obra_social == 0) {
        if (!isEmpty(window.afiliado.fecha_baja_obra_social)) {
          var limite = moment(window.afiliado.fecha_baja_obra_social).add(90,'days');
          if (moment().isAfter(limite)) {
            alert("El afiliado esta dado de baja en la obra social.");
            return;
          } else {
            alert("El afiliado esta dado de baja, pero tiene cobertura hasta "+limite.format("DD/MM/YYYY")+".");
          }
        } else {
          alert("El afiliado esta dado de baja en la obra social.");
          return;
        }
      }
      var self = this;
      var vista = new app.views.SindiConsultaEditView({
        model:new app.models.SindiConsulta(),
        permiso: control.check("sindi_consultas"),
        view: self,
      });
      crearLightboxHTML({
        "html":vista.el,
        "width":900,
        "height":140,
        "escapable":false,
        "callback":function() {
          self.collection.pager();
        },
      });
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.SindiConsultaItem({
        model: item,
        permiso: this.permiso,
        tabla: self,
        small: ((typeof this.options.small != "undefined") ? this.options.small : 0),
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.SindiConsultaEditView = app.mixins.View.extend({

    template: _.template($("#sindi_consultas_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",
      "click .nuevo": "limpiar",
      "change #sindi_consultas_id_condicion_especial": "controlimporte",
      "click .anular": "anular",
      "click .imprimir":"imprimir",
    },

    anular: function() {
      this.model.set({"anulada":"1"});
      this.guardar();
    },

    imprimir: function() {
      var self = this;
      $('.modal').modal('hide');
      window.open("sindi_consultas/function/imprimir/"+self.model.id,"_blank");
    },

    controlimporte: function() {
      if (this.model.id != undefined) return;
      if (this.$("#sindi_consultas_id_condicion_especial option:selected").val() > 1) {
        this.$("#sindi_consultas_importe").removeAttr("disabled");
        this.$("#sindi_consultas_importe").val(0);
      } else {
        this.$("#sindi_consultas_importe").attr('disabled', true);
        this.$("#sindi_consultas_importe").val(SINDI_VALOR_CONSULTA);
      }
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
      var self = this;
      if (this.options.permiso > 1) edicion = true;

      // Controlamos si se paso el limite
      var supero_limite = 0;
      if (this.model.id == undefined && typeof window.afiliado != "undefined") {
        for(var i=0; i<window.afiliado.limites.length; i++) {
          var limite = window.afiliado.limites[i];
          if (limite.tipo == 1) {
            supero_limite = (limite.consultas_realizadas >= limite.cantidad) ? 1 : 0;
            break;
          }
        }
      }

      var obj = {
        edicion: edicion,
        id:this.model.id,
        permiso: control.check("sindi_consultas"),
        supero_limite: supero_limite,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // Creamos el select
      var id_afiliado = (self.model.id == undefined) ? window.afiliado.id : self.model.get("id_paciente");
      new app.mixins.Select({
        modelClass: app.models.SindiCondicionEspecial, // MODELO
        url: "sindi_condiciones_especiales/function/buscar_por_afiliado/"+id_afiliado, // URL DEL CONTROL
        render: "#sindi_consultas_id_condicion_especial", // ID DEL SELECT
        firstOptions: ["<option value='1'>Ninguna</option>"],
        selected: self.model.get("id_condicion_especial"),
        disabled: (self.model.id != undefined),
        onComplete: function(c) {
          if (c.length > 0 && self.model.id == undefined) $("#condicion_especial_alerta").show();
        }
      });

      // Si es nuevo, mostramos la tabla
      if (this.model.id == undefined) {
        var coleccion = new app.collections.SindiConsultas();
        coleccion.server_api = {
          id_afiliado: window.afiliado.id,
        };
        var tabla_footer = new app.views.SindiConsultasTableView({
          collection: coleccion,
          small: 1,
        });
        this.$("#tabla_footer").html(tabla_footer.el);
      }

      this.controlimporte();

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        this.model.set({
          "id_afiliado":window.afiliado.id_titular,
          "id_paciente":window.afiliado.id,
          "id_concepto": this.$("#sindi_consultas_id_concepto option:selected").val(),
          "id_tipo_bono": this.$("#sindi_consultas_id_tipo_bono option:selected").val(),
          "id_condicion_especial": this.$("#sindi_consultas_id_condicion_especial option:selected").val(),
          "hospital": this.$("#sindi_consultas_hospital").is(":checked")?"1":"0",
          "importe": this.$("#sindi_consultas_importe").val(),
          "fecha": moment(),
        })
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {

        var abrir_recetario = (this.$("#sindi_consultas_recetarios").is(":checked")?1:0);

        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            if (self.model.get("anulada") == 0) self.imprimir();
            else $('.modal').modal('hide');
            if (abrir_recetario == 1) {
              var vista = new app.views.SindiRecetarioEditView({
                model:new app.models.SindiRecetario({
                  "id_condicion_especial":self.model.get("id_condicion_especial"),
                }),
                permiso: control.check("sindi_recetarios"),
                view: self,
              });
              crearLightboxHTML({
                "html":vista.el,
                "width":900,
                "height":140,
                "escapable":false,
              });
            }
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiConsulta();
      this.render();
    },

  });

})(app.views, app.models);