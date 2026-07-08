(function ( models ) {

  models.SindiReintegro = Backbone.Model.extend({
    urlRoot: "sindi_reintegros/",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_paciente: 0,
      numero: 0,
      fecha: moment().format("DD/MM/YYYY"),
      factura: "",
      recibo: "",
      fecha_documento: moment().format("DD/MM/YYYY"),
      importe_documento: 0,
      importe_reintegro: 0,
      id_tipo_reintegro: 0,
      id_delegacion: 0,
      id_os_sindi: 0,
      id_afiliado: 0,
      detalle: "",
      anulada: 0,
      codigoafiliado: "",
      identificadorafiliado: "",
      nombreafiliado: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiReintegros = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_reintegros/function/buscar"
    }
  });
})( app.collections, app.models.SindiReintegro, Backbone.Paginator);


(function ( app ) {
  app.views.SindiReintegroItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_reintegros_item').html()),
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
      var sindi_reintegro = new app.models.SindiReintegro({ "id": self.model.id });
      sindi_reintegro.fetch({
        "success":function() {
          var that = self
          var vista = new app.views.SindiReintegroEditView({
            model: sindi_reintegro,
            permiso: control.check("sindi_reintegros")
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

  app.views.SindiReintegrosTableView = app.mixins.View.extend({

   template: _.template($("#sindi_reintegros_panel_template").html()),

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
      var vista = new app.views.SindiReintegroEditView({
        model:new app.models.SindiReintegro(),
        permiso: control.check("sindi_reintegros"),
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
      self = this;
      var view = new app.views.SindiReintegroItem({
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

  views.SindiReintegroEditView = app.mixins.View.extend({

    template: _.template($("#sindi_reintegros_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",
      "click .nuevo": "limpiar",
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
      window.open("sindi_reintegros/function/imprimir/"+self.model.id,"_blank");
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
      self = this;
      if (this.options.permiso > 1) edicion = true;
      var obj = {
        edicion: edicion,
        id:this.model.id,
        permiso: control.check("sindi_reintegros")
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      new app.mixins.Select({
        modelClass: app.models.SindiTipoReintegro, // MODELO
        url: "sindi_tipos_reintegros", // URL DEL CONTROL
        render: "#sindi_reintegros_id_tipo_reintegro", // ID DEL SELECT
        selected: self.model.get("id_tipo_reintegro"),
        disabled: (self.model.id != undefined),
      });

      // Si es nuevo, mostramos la tabla
      if (this.model.id == undefined) {
        var coleccion = new app.collections.SindiReintegros();
        coleccion.server_api = {
          id_afiliado: window.afiliado.id,
        };
        var tabla_footer_reintegros = new app.views.SindiReintegrosTableView({
          collection: coleccion,
          small: 1,
        });
        this.$("#tabla_footer_reintegros").html(tabla_footer_reintegros.el);
      }

      return this;
    },

    cerrar: function(e) {
      $('.modal:last').modal('hide');

    },
    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        this.model.set({
          "fecha": moment(),
          "id_afiliado":window.afiliado.id_titular,
          "id_paciente":window.afiliado.id,
          "id_tipo_reintegro": $("#sindi_reintegros_id_tipo_reintegro option:selected").val(),
          "id_os_sindi": $("#sindi_reintegros_id_os_sindi option:selected").val(),
          "id_delegacion": $("#sindi_reintegros_id_delegacion option:selected").val(),
          "factura": $("#sindi_reintegros_factura").val(),
          "recibo": $("#sindi_reintegros_recibo").val(),
          "fecha_documento": $("#sindi_reintegros_fecha_documento").val(),
          "importe_documento": $("#sindi_reintegros_importe_documento").val(),
          "detalle": $("#sindi_reintegros_detalle").val(),
          "importe_reintegro": $("#sindi_reintegros_importe_reintegro").val(),
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
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            if (self.model.get("anulada") == 0) self.imprimir();
            else $('.modal').modal('hide');
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiReintegro();
      this.render();
    },

  });

})(app.views, app.models);