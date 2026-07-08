(function ( models ) {

  models.PresBuenosClientes = Backbone.Model.extend({
    urlRoot: "pres_buenos_clientes/",
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PresBuenosClientes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "pres_prestamos/function/buenos_clientes/"
    },
    paginator_ui: {
      perPage: 50,
      order_by: 'dias_mora',
      order: 'asc',
    },
  });
})( app.collections, app.models.PresBuenosClientes, Backbone.Paginator);


(function ( app ) {
  app.views.PresBuenosClientesItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#pres_buenos_clientes_item').html()),
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

  app.views.PresBuenosClientesTableView = app.mixins.View.extend({

    template: _.template($("#pres_buenos_clientes_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #pres_buenos_clientes_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
      "change #pres_buenos_clientes_sucursales":"buscar",
      "click .imprimir":function() {
        var url = "pres_prestamos/function/buenos_clientes/?imprimir=1";
        url += "&id_sucursal="+window.pres_buenos_clientes_id_sucursal;
        url += "&texto="+encodeURIComponent(window.pres_buenos_clientes_filter);
        url += "&offset=99999999";
        workspace.imprimir_reporte(url);
      },
      "click .exportar":function() {
        var self = this;
        if (window.pres_buenos_clientes_filter != this.$("#pres_buenos_clientes_buscar").val().trim()) {
          window.pres_buenos_clientes_filter = this.$("#pres_buenos_clientes_buscar").val().trim();
        }
        if (window.pres_buenos_clientes_id_sucursal != this.$("#pres_buenos_clientes_sucursales").val()) {
          window.pres_buenos_clientes_id_sucursal = this.$("#pres_buenos_clientes_sucursales").val();
        }
        var datos = {
          //"texto":encodeURIComponent(window.pres_buenos_clientes_filter),
        };
        var url = "pres_prestamos/function/buenos_clientes/?id_sucursal="+window.pres_buenos_clientes_id_sucursal+"&exportar=1";
        window.open(url,"_blank");
        /*
        var array = new Array();
        $("#pres_buenos_clientes_table tbody tr").each(function(i,e){
          array.push({
            "nombre":$.trim($(e).find("td:eq(2) a").text()),
            "localidad":$.trim($(e).find("td:eq(3) span").text()),
            "telefono":$.trim($(e).find("td:eq(4) span").text()),
          });
        });
        var header = new Array("Nombre","Localidad","Telefono");
        this.exportar_excel({
          "filename":"Buenos Clientes",
          "title":"Listado de Buenos Clientes: ",
          "data":array,
          "header":header,
        }); 
        */
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

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      $(this.el).find(".search_container").html(search.el);
      $(this.el).find(".pagination_container").html(this.pagination.el);
      this.buscar();
    },

    buscar: function() {
      if (window.pres_buenos_clientes_filter != this.$("#pres_buenos_clientes_buscar").val().trim()) {
        window.pres_buenos_clientes_filter = this.$("#pres_buenos_clientes_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (window.pres_buenos_clientes_id_sucursal != this.$("#pres_buenos_clientes_sucursales").val()) {
        window.pres_buenos_clientes_id_sucursal = this.$("#pres_buenos_clientes_sucursales").val();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.pres_buenos_clientes_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "texto":encodeURIComponent(window.pres_buenos_clientes_filter),
        "id_sucursal":window.pres_buenos_clientes_id_sucursal,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pres_buenos_clientes_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PresBuenosClientesItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);