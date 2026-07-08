(function ( models ) {

  models.SindiNomenclador = Backbone.Model.extend({
    urlRoot: "sindi_nomencladores/",
    defaults: {
      nombre: "",
      id_empresa: ID_EMPRESA,
      codigo: 0,
      id_tipo_practica: 0,
      importe: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.SindiNomencladores = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "sindi_nomencladores/function/buscar/",
    }
  });
})( app.collections, app.models.SindiNomenclador, Backbone.Paginator);


(function ( app ) {
  app.views.SindiPracticasView = app.mixins.View.extend({
    template: _.template($('#sindi_practicas_template').html()),
    myEvents: {
      "click .guardarconsulta":"guardar",
    },
    initialize: function(options) {
      this.options = options;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template());
      $("#sindi_nomencladores_importe_consulta").val(SINDI_VALOR_CONSULTA);

      var nomencladores = new app.views.SindiNomencladoresTableView({
        collection: new app.collections.SindiNomencladores(),
      });

      var tipos_practicas = new app.views.SindiTiposPracticasTableView({
        collection: new app.collections.SindiTiposPracticas(),
        tablaNomencladores: nomencladores,
      });

      this.$("#lista_tipos").html(tipos_practicas.el);
      this.$("#lista_nomenclador").html(nomencladores.el);



      return this;
    },
    guardar: function() {
      var self = this;
      valor = $("#sindi_nomencladores_importe_consulta").val();
      if (confirm('Seguro desea modificar el valor de la consulta a $ '+valor)) {
        $.ajax({
        "url":"sindi_nomencladores/function/valor_consulta/",
        "dataType":"json",
        "type":"post",
        "data":{
          "valor_consulta":valor,
        },
        "success":function() {
          location.reload();
        }
      });
      } else {
        return;
      }

    }
  });
})( app );



(function ( app ) {
  app.views.SindiNomencladorItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#sindi_nomencladores_item').html()),
    events: {
      "click .editar": "editar",
      "click .delete": "borrar",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      this.tabla = options.tabla;
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
      var permiso = control.check("sindi_nomencladores");
      if (permiso < 2) return;
      var self = this;
      var modelo = new app.models.SindiNomenclador({ "id": self.model.id });
      modelo.fetch({
        "success":function() {
          var view = new app.views.SindiNomencladorEditView({
            model: modelo,
            permiso: permiso
          });
          crearLightboxHTML({
            "html":view.el,
            "width":600,
            "height":500,
            "escapable":false,
            "callback":function() {
              workspace.ver_sindi_nomencladores();
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



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.SindiNomencladoresTableView = app.mixins.View.extend({

    template: _.template($("#sindi_nomencladores_panel_template").html()),

    myEvents: {
      "click .nuevo":"nuevo",
    },

    nuevo: function() {
      var self = this;
      var permiso = control.check("sindi_nomencladores");
      if (permiso < 2) return;
      var view = new app.views.SindiNomencladorEditView({
        model: new app.models.SindiNomenclador(),
        permiso: permiso,
      });
      crearLightboxHTML({
        "html":view.el,
        "width":600,
        "height":500,
        "escapable":false,
        "callback":function() {
          self.collection.pager();
        }
      });
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

      this.buscar(0);
    },

    buscar: function(id_tipo_practica) {
      // Vamos a buscar los elementos y lo paginamos
      this.collection.server_api = {
        "id_tipo_practica":id_tipo_practica,
      };
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.SindiNomencladorItem({
        model: item,
        tabla: self,
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

  views.SindiNomencladorEditView = app.mixins.View.extend({

    template: _.template($("#sindi_nomencladores_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .cerrar": "cerrar",
      "click .nuevo": "limpiar",
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

      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.SindiTipoPractica, // MODELO
        url: "sindi_tipos_practicas/", // URL DEL CONTROL
        firstOptions: ["<option value=''>Sin Definir</option>"], // OPCIONES POR DEFECTO
        render: "#sindi_nomencladores_tipos", // ID DEL SELECT
        selected: self.model.get("id_tipo_practica"), // VALOR SELECCIONADO
        required: true,
        name: "id_tipo_practica", // NOMBRE DEL CAMPO EN LA TABLA
      });

      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios

        // No hay ningun error

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
            $('.modal:last').modal('hide');
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.SindiNomenclador()
      this.render();
    },

    cerrar: function(e) {
      if (confirm('Si continua cerrando los cambios no se guardaran!')) {
        $('.modal:last').modal('hide');
      }
    },

  });

})(app.views, app.models);