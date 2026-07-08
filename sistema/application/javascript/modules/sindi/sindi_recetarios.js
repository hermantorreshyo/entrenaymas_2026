(function ( models ) {

  models.SindiRecetario = Backbone.Model.extend({
    urlRoot: "sindi_recetarios/",
    defaults: {
      id_empresa: ID_EMPRESA,
      numero: 0,
      id_afiliado: 0,
      id_paciente: 0,
      porcentaje: 0,
      cantidad: 1,
      fecha: moment().format("DD/MM/YYYY"),
      nombreafiliado: "",
      anulada: 0,
      impresa: 0,
      id_condicion_especial: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiRecetarios = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_recetarios/function/buscar/"
    }
  });
})( app.collections, app.models.SindiRecetario, Backbone.Paginator);


(function ( app ) {
  app.views.SindiRecetarioItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_recetarios_item').html()),
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
      var obj = { permiso:
        this.permiso,
        small: ((typeof this.options.small != "undefined") ? this.options.small : 0),
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      var self = this;
      var sindi_recetario = new app.models.SindiRecetario({ "id": self.model.id });
      sindi_recetario.fetch({
        "success":function() {
          var that = self
          var vista = new app.views.SindiRecetarioEditView({
            model: sindi_recetario,
            permiso: control.check("sindi_recetarios")
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

  app.views.SindiRecetariosTableView = app.mixins.View.extend({

   template: _.template($("#sindi_recetarios_panel_template").html()),

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
          collection: self.collection,
        });

        // Creamos el buscador
        var search = new app.mixins.SearchView({
          collection: self.collection,
        });
      }

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = {
        permiso: this.permiso,
        small: small
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
      var vista = new app.views.SindiRecetarioEditView({
        model:new app.models.SindiRecetario(),
        permiso: control.check("sindi_recetarios"),
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
      var view = new app.views.SindiRecetarioItem({
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

  views.SindiRecetarioEditView = app.mixins.View.extend({

    template: _.template($("#sindi_recetarios_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",
      "click .nuevo": "limpiar",
      "click .anular": "anular",
      "click .imprimir":"imprimir",
    },

    anular: function() {
      var self = this;
      this.model.set({"anulada":"1"});
      this.model.save({
        "id_empresa":ID_EMPRESA,
      },{
        success: function(model,response) {
          $('.modal').modal('hide');
        }
      });
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
      var supero_limite_40_50 = 0;
      var supero_limite_70 = 0;
      var supero_limite_100 = 0;
      if (this.model.id == undefined && typeof window.afiliado != "undefined") {
        for(var i=0; i<window.afiliado.limites.length; i++) {
          var limite = window.afiliado.limites[i];
          if (limite.tipo == 2) {
            supero_limite_40_50 = (limite.recetarios_realizados >= limite.cantidad) ? 1 : 0;
          } else if (limite.tipo == 3) {
            supero_limite_70 = (limite.recetarios_70_realizados >= limite.cantidad) ? 1 : 0;         
          } else if (limite.tipo == 4) {
            supero_limite_100 = (limite.recetarios_100_realizados >= limite.cantidad) ? 1 : 0;
          }
        }
      }

      this.res45real = ((window.afiliado != undefined)?window.afiliado.limites[1].recetarios_realizados:"0"); 
      this.res45cant = ((window.afiliado != undefined)?window.afiliado.limites[1].cantidad:"0");
      this.res70real = ((window.afiliado != undefined)?window.afiliado.limites[2].recetarios_70_realizados:"0");
      this.res70cant = ((window.afiliado != undefined)?window.afiliado.limites[2].cantidad:"0");
      this.res100real = ((window.afiliado != undefined)?window.afiliado.limites[3].recetarios_100_realizados:"0");
      this.res100cant = ((window.afiliado != undefined)?window.afiliado.limites[3].cantidad:"0");

      var obj = {
        edicion: edicion,
        id:this.model.id,
        permiso: control.check("sindi_recetarios"),
        supero_limite_40_50: supero_limite_40_50,
        supero_limite_70: supero_limite_70,
        supero_limite_100: supero_limite_100,
        res45real: this.res45real,
        res45cant: this.res45cant,
        res70real: this.res70real,
        res70cant: this.res70cant,
        res100real: this.res100real,
        res100cant: this.res100cant,
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      // Creamos el select
      var id_paciente = (self.model.id == undefined) ? window.afiliado.id : self.model.get("id_paciente");
      new app.mixins.Select({
        modelClass: app.models.SindiCondicionEspecial, // MODELO
        url: "sindi_condiciones_especiales/function/buscar_por_afiliado/"+id_paciente, // URL DEL CONTROL
        render: "#sindi_recetarios_id_condicion_especial", // ID DEL SELECT
        firstOptions: ["<option value='1'>Ninguna</option>"],
        selected: self.model.get("id_condicion_especial"),
        disabled: (self.model.id != undefined),
        onComplete: function(c) {
          if (c.length > 0 && self.model.id == undefined) $("#condicion_especial_alerta").show();
        }
      });

      // Si es nuevo, mostramos la tabla
      if (this.model.id == undefined) {
        var coleccion = new app.collections.SindiRecetarios();
        coleccion.server_api = {
          id_afiliado: window.afiliado.id,
        };
        var tabla_footer_recetarios = new app.views.SindiRecetariosTableView({
          collection: coleccion,
          small: 1,
        });
        this.$("#tabla_footer_recetarios").html(tabla_footer_recetarios.el);
      }


      return this;
    },

    validar: function() {
      try {

        // Controlo si las cantidades
        var porcentaje = parseInt($("#sindi_recetarios_porcentaje").val());
        var cantidad = parseInt($("#sindi_recetarios_cantidad").val());

        var diferencia45 = parseInt(this.res45cant - this.res45real - cantidad);
        if ( (porcentaje == 40 || porcentaje == 50) && diferencia45 < 0) {
          alert("ERROR: No se pueden dar tantas recetarios. Cantidad disponible: "+(diferencia45+cantidad));
          return false;
        }

        var diferencia70 = parseInt(this.res70cant - this.res70real - cantidad);
        if ( porcentaje == 70 && diferencia70 < 0) {
          alert("ERROR: No se pueden dar tantas recetarios. Cantidad disponible: "+(diferencia70+cantidad));
          return false;
        }

        var diferencia100 = parseInt(this.res100cant - this.res100real - cantidad);
        if ( porcentaje == 100 && diferencia100 < 0) {
          alert("ERROR: No se pueden dar tantas recetarios. Cantidad disponible: "+(diferencia100+cantidad));
          return false;
        }

        // Validamos los campos que sean necesarios
        this.model.set({
          "fecha": moment(),
          "cantidad":cantidad,
          "id_afiliado":window.afiliado.id_titular,
          "id_paciente":window.afiliado.id,
          "id_condicion_especial": $("#sindi_recetarios_id_condicion_especial option:selected").val(),
          "porcentaje": $("#sindi_recetarios_porcentaje option:selected").val(),
        })
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
        var cantidad = this.$("#sindi_recetarios_cantidad").val();
        for(var i=0;i<cantidad;i++) {
          var modelo_clonado = self.model.clone();
          modelo_clonado.save({
            "id_empresa":ID_EMPRESA,
          },{
            success: function(model,response) {
              if (modelo_clonado.get("anulada") == 0) {
                window.open("sindi_recetarios/function/imprimir/"+model.id,"_blank");
              }
            }
          });          
        }
        $('.modal').modal('hide');
      }
    },

    imprimir: function() {
      var self = this;
      $('.modal').modal('hide');
      window.open("sindi_recetarios/function/imprimir/"+self.model.id,"_blank");
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');
    },

    limpiar : function() {
      this.model = new app.models.SindiRecetario();
      this.render();
    },

  });

})(app.views, app.models);