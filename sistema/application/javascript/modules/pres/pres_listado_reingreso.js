(function ( models ) {

  models.PresListadoReingreso = Backbone.Model.extend({
    urlRoot: "pres_listado_reingreso/",
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PresListadoReingreso = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "pres_prestamos/function/listado_reingreso/"
    },
    paginator_ui: {
      perPage: 9999999,
    },
  });
})( app.collections, app.models.PresListadoReingreso, Backbone.Paginator);


(function ( app ) {
  app.views.PresListadoReingresoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#pres_listado_reingreso_item').html()),
    events: {
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
  });

})( app );


(function ( app ) {

  app.views.PresListadoReingresoTableView = app.mixins.View.extend({

    template: _.template($("#pres_listado_reingreso_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #pres_listado_reingreso_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
      "change #pres_listado_reingreso_sucursales":"buscar",
      "click .imprimir":function() {
        var url = "pres_prestamos/function/listado_reingreso/?imprimir=1";
        url += "&id_sucursal="+window.pres_listado_reingreso_id_sucursal;
        url += "&texto="+encodeURIComponent(window.pres_listado_reingreso_filter);
        workspace.imprimir_reporte(url);
      },
      "click .exportar":function() {
        var self = this;
        var array = new Array();
        $("#pres_listado_reingreso_table tbody tr").each(function(i,e){
          array.push({
            "nombre":$.trim($(e).find("td:eq(2) a").text()),
            "localidad":$.trim($(e).find("td:eq(3) span").text()),
            "telefono":$.trim($(e).find("td:eq(4) span").text()),
          });
        });
        var header = new Array("Nombre","Localidad","Telefono");
        this.exportar_excel({
          "filename":"Listado de Reingresos",
          "title":"Listado de Reingresos: ",
          "data":array,
          "header":header,
        }); 
      },
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      var search = new app.mixins.SearchView({
        collection: lista
      });

      this.collection.on('sync', this.addAll, this);

      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      $(this.el).find(".search_container").html(search.el);
      this.buscar();
    },

    buscar: function() {
      if (window.pres_listado_reingreso_filter != this.$("#pres_listado_reingreso_buscar").val().trim()) {
        window.pres_listado_reingreso_filter = this.$("#pres_listado_reingreso_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (window.pres_listado_reingreso_id_sucursal != this.$("#pres_listado_reingreso_sucursales").val()) {
        window.pres_listado_reingreso_id_sucursal = this.$("#pres_listado_reingreso_sucursales").val();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.pres_listado_reingreso_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "texto":encodeURIComponent(window.pres_listado_reingreso_filter),
        "id_sucursal":window.pres_listado_reingreso_id_sucursal,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pres_listado_reingreso_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PresListadoReingresoItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);