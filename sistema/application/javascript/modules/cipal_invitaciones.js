(function ( models ) {

  models.CipalInvitacion = Backbone.Model.extend({
    urlRoot: "cipal_invitaciones/",
    defaults: {
      id_empresa: ID_EMPRESA,
      codigo: "",
      empresa: "",
      archivo: "",
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.CipalInvitaciones = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "cipal/function/ver_invitaciones/"
    }
  });
})( app.collections, app.models.CipalInvitacion, Backbone.Paginator);


(function ( app ) {
  app.views.CipalInvitacionItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cipal_invitaciones_item').html()),
    events: {
      "click .descargar": "descargar",
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
    descargar: function() {
      window.open("/sistema/cipal/function/ver_pdf_por_codigo/?codigo="+this.model.get("codigo"),"_blank");
    },
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.CipalInvitacionesTableView = app.mixins.View.extend({

    template: _.template($("#cipal_invitaciones_panel_template").html()),

    myEvents:{
      "click #cipal_invitaciones_generar":"generar",
    },

    generar: function() {
      var empresa = this.$("#cipal_invitaciones_empresas option:selected").text();
      var cantidad = this.$("#cipal_invitaciones_cantidad").val();
      if (isEmpty(cantidad)) cantidad = 1;
      for(var i=0;i<cantidad;i++) {
        window.open("/sistema/cipal/function/descargar_pdf_invitacion/?empresa="+encodeURIComponent(empresa));
      }
    },

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

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      $(this.el).find(".search_container").html(search.el);
      $(this.el).find(".pagination_container").html(pagination.el);

      new app.mixins.Select({
        modelClass: app.models.Entrada,
        url: "entradas/function/ver/?id_categoria=412&offset=99999",
        render: "#cipal_invitaciones_empresas",
        campoSelect: "titulo",
        onComplete:function(c) {
          crear_select2("cipal_invitaciones_empresas");
        }                    
      });

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CipalInvitacionItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);