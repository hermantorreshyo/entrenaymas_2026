// -----------
//   MODELO
// -----------

(function ( models ) {

  models.clientes_log = Backbone.Model.extend({
    urlRoot: "clientes_log",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      id_usuario: 0,
      fecha: "",
      accion: "",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {
  collections.ClientesLog = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "clientes_log/function/buscar/"
    }
  });
})( app.collections, app.models.clientes_log, Backbone.Paginator);




// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.ClientesLogTableView = app.mixins.View.extend({

    template: _.template($("#clientes_log_table_view").html()),

    myEvents: {
      "change #clientes_log_id":"buscar",
      "change #clientes_log_accion":"buscar",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: this.collection
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: this.collection
      });
            
      this.collection.on('add', this.addOne, this);
      this.collection.on('all', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        render: "#clientes_log_id",
        firstOptions: ["<option value='0'>Cliente</option>"],
        onComplete: function() {
          self.$("#clientes_log_id").select2();
        },        
      });      
      this.buscar();
    },

    buscar: function() {
      var filtros = {};
      filtros.id_usuario = this.$("#clientes_log_id").val();
      this.collection.server_api = filtros;
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ClientesLogItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);

// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {
  app.views.ClientesLogItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#clientes_log_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
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
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
/*
(function ( views, models ) {

  views.CrearDNSView = app.mixins.View.extend({

    template: _.template($("#crear_dns").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "change #dns_tipo":"cambiar_contenido"
    },

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.options = options;
            this.render();
        },

        render: function()
        {
          // Creamos un objeto para agregarle las otras propiedades que no son el modelo
          var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
          // Extendemos el objeto creado con el modelo de datos
          $.extend(obj,this.model.toJSON());

          $(this.el).html(this.template(obj));
          return this;
        },
        cambiar_contenido: function(events) {
          var tipo = $('#dns_tipo').val();
          switch (tipo){
            case 'A':
              $('#dns_contenido').attr('value', '3.14.51.17');
              $('#dns_contenido').attr('disabled', 'disabled');
              $('#dns_prioridad').attr('disabled', 'disabled');
              break;
            case 'CNAME':
              $('#dns_contenido').attr('value', 'WWW');
              $('#dns_contenido').attr('disabled', 'disabled');
              $('#dns_prioridad').attr('disabled', 'disabled');
              break;
            case 'MX':
              $('#dns_contenido').attr('value', '');
              $('#dns_contenido').removeAttr('disabled');
              $('#dns_prioridad').removeAttr('disabled');
              break;
            case 'TXT':
              $('#dns_contenido').attr('value', '');
              $('#dns_contenido').removeAttr('disabled');
              $('#dns_prioridad').attr('disabled', 'disabled');
              break;
          }
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("dns_contenido",IS_EMPTY,"Por favor, ingrese un contenido.");
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },
        

        guardar: function() 
        {
            var self = this;
            var dominio = $("#dns_dominio").val()
            var parametro = $("#dns_contenido").val()
            var prioridad = $("#dns_prioridad").val()
            var tipo = $("#dns_tipo").val()
            this.model.save({dominio:dominio, parametro:parametro, prioridad:prioridad, tipo:tipo},{
              success: function(model,response) {
                show("Los datos han sido guardados correctamente.");
                location.href="app/#dns";
              }
            });
      },
      
        limpiar : function() {
            this.model = new app.models.Dns()
            this.render();
        },
    
  });

})(app.views, app.models);
*/
