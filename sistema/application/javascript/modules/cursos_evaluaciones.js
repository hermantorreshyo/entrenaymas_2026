// -----------
//   MODELO
// -----------

(function ( models ) {

  models.cursos_evaluaciones = Backbone.Model.extend({
    urlRoot: "cursos_evaluaciones",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      curso: "",
      id_curso: 0,
      clase: "",
      id_clase: 0,
      fecha: "",
      estado: "",
      etiqueta: "",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {
  collections.CursosEvaluaciones = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cursos_evaluaciones/function/buscar/"
    }
  });
})( app.collections, app.models.cursos_evaluaciones, Backbone.Paginator);




// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.CursosEvaluacionesView = app.mixins.View.extend({

    template: _.template($("#cursos_evaluaciones_table_view").html()),

    myEvents: {
      "change #cursos_evaluaciones_clientes":"buscar",
      "change #cursos_evaluaciones_cursos":"buscar",
      "change #cursos_evaluaciones_estados":"buscar",
      "change #cursos_evaluaciones_clientes_etiquetas":"buscar",
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
        modelClass: app.models.ClienteEtiqueta,
        url: "clientes_etiquetas/",
        render: "#cursos_evaluaciones_clientes_etiquetas",
        firstOptions: ["<option value='0'>Grupo</option>"],
        onComplete: function() {
          self.$("#cursos_evaluaciones_clientes_etiquetas").select2();
        },        
      });      

      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        render: "#cursos_evaluaciones_clientes",
        firstOptions: ["<option value='0'>Usuario</option>"],
        onComplete: function() {
          self.$("#cursos_evaluaciones_clientes").select2();
        },        
      });      

      new app.mixins.Select({
        modelClass: app.models.Curso,
        url: "cursos/",
        render: "#cursos_evaluaciones_cursos",
        firstOptions: ["<option value='0'>Curso</option>"],
        onComplete: function() {
          self.$("#cursos_evaluaciones_cursos").select2();
        },        
      });      

      new app.mixins.Select({
        modelClass: app.models.Curso,
        url: "cursos/",
        render: "#cursos_evaluaciones_cursos",
        firstOptions: ["<option value='0'>Curso</option>"],
        onComplete: function() {
          self.$("#cursos_evaluaciones_cursos").select2();
        },        
      });      

      this.buscar();
    },

    buscar: function() {
      var filtros = {};
      filtros.id_usuario = this.$("#cursos_evaluaciones_clientes").val();
      filtros.id_etiqueta = this.$("#cursos_evaluaciones_clientes_etiquetas").val();
      filtros.id_curso = this.$("#cursos_evaluaciones_cursos").val();
      filtros.estado = this.$("#cursos_evaluaciones_estados").val();
      this.collection.server_api = filtros;
      this.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CursosEvaluacionItem({
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
  app.views.CursosEvaluacionItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cursos_evaluaciones_item').html()),
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
