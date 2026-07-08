(function ( models ) {

  models.PresListadoMora = Backbone.Model.extend({
    urlRoot: "pres_listado_mora/",
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PresListadoMora = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "pres_prestamos/function/listado_mora/"
    },
    paginator_ui: {
      perPage: 50,
      order_by: 'dias_mora',
      order: 'asc',
    },
  });
})( app.collections, app.models.PresListadoMora, Backbone.Paginator);


(function ( app ) {
  app.views.PresListadoMoraItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#pres_listado_mora_item').html()),
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

  app.views.PresListadoMoraTableView = app.mixins.View.extend({

    template: _.template($("#pres_listado_mora_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "keypress #pres_listado_mora_buscar":function(e){
        if (e.which == 13) this.buscar();
      },
      "change #pres_listado_mora_sucursales":"buscar",
      "change #pres_listado_mora_planes":"buscar",
      "click .imprimir":function() {
        var url = "pres_prestamos/function/listado_mora/?imprimir=1";
        url += "&id_sucursal="+window.pres_listado_mora_id_sucursal;
        url += "&texto="+encodeURIComponent(window.pres_listado_mora_filter);
        workspace.imprimir_reporte(url);
      },
      "click .exportar":function() {
        var self = this;
        var array = new Array();
        $("#pres_listado_mora_table tbody tr").each(function(i,e){
          array.push({
            "nombre":$.trim($(e).find("td:eq(2) a").text()),
            "localidad":$.trim($(e).find("td:eq(3) span").text()),
            "telefono":$.trim($(e).find("td:eq(4) span").text()),
            "plan":$.trim($(e).find("td:eq(5) span").text()),
            "numero":$.trim($(e).find("td:eq(6) span").text()),
            "valor":$.trim($(e).find("td:eq(7) span").text()),
            "cuotas":$.trim($(e).find("td:eq(8) span").text()),
            "ult_pago":$.trim($(e).find("td:eq(9) span").text()),
            "mora":$.trim($(e).find("td:eq(10) span").text()),
            "deuda":$.trim($(e).find("td:eq(11) span").text()),
          });
        });
        var header = new Array("Nombre","Localidad","Telefono","Plan","Numero","Valor","Cuotas","Ult. Pago","Mora","Deuda");
        this.exportar_excel({
          "filename":"Listado de Mora",
          "title":"Resumen de Cuenta: ",
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

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      $(this.el).find(".search_container").html(search.el);
      $(this.el).find(".pagination_container").html(this.pagination.el);

      new app.mixins.Select({
        modelClass: app.models.PresPlanCredito,
        url: "pres_planes_credito/",
        render: "#pres_listado_mora_planes",
        firstOptions: ["<option value='0'>Plan</option>"],
      });

      this.buscar();
    },

    buscar: function() {
      if (window.pres_listado_mora_filter != this.$("#pres_listado_mora_buscar").val().trim()) {
        window.pres_listado_mora_filter = this.$("#pres_listado_mora_buscar").val().trim();
        this.cambio_parametros = true;
      }
      if (window.pres_listado_mora_id_sucursal != this.$("#pres_listado_mora_sucursales").val()) {
        window.pres_listado_mora_id_sucursal = this.$("#pres_listado_mora_sucursales").val();
        this.cambio_parametros = true;
      }
      if (window.pres_listado_mora_id_plan != this.$("#pres_listado_mora_planes").val()) {
        window.pres_listado_mora_id_plan = this.$("#pres_listado_mora_planes").val();
        this.cambio_parametros = true;
      }
      if (this.cambio_parametros) {
        window.pres_listado_mora_page = 1;
        this.cambio_parametros = false;
      }
      var datos = {
        "texto":encodeURIComponent(window.pres_listado_mora_filter),
        "id_sucursal":window.pres_listado_mora_id_sucursal,
        "id_plan":window.pres_listado_mora_id_plan,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.pres_listado_mora_page);
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PresListadoMoraItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);