(function ( models ) {

  models.ReposicionAsistida = Backbone.Model.extend({
    urlRoot: "reposicion_asistida/",
    defaults: {
      results: []
    }
  });
      
})( app.models );

(function (collections, model, paginator) {
  collections.ReposicionAsistida = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },
    paginator_core: {
      url: "reposicion_asistida/function/ver_listado/",
    },
  });

})( app.collections, app.models.ReposicionAsistida, Backbone.Paginator);


(function ( views, models ) {

  views.ReposicionAsistidaEditView = app.mixins.View.extend({

    template: _.template($("#reposicion_asistida_edit_panel_template").html()),

    myEvents: {
      "click .generar":"generar",
    },

    initialize: function(options) {
      var self = this;
      this.options = options;
      this.id_proveedor = options.id_proveedor;
      this.id_sucursal = options.id_sucursal;
      _.bindAll(this);
      $.ajax({
        "url":"reposicion_asistida/function/ver_pedido_sugerido/",
        "dataType":"json",
        "data":{
          "id_sucursal": self.id_sucursal,
          "id_proveedor": self.id_proveedor,
        },
        "success":function(r) {
          self.resultado = r;
          self.render();
        }
      });
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.resultado));
      return this;
    },

    generar:function() {
      var self = this;
      var items = new Array();
      this.$(".pedido_item").each(function(i,e){
        items.push({
          "id_articulo":$(e).data("id"),
          "cantidad":$(e).val(),
        });
      });
      $.ajax({
        "url":"reposicion_asistida/function/generar_pedido/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_proveedor":self.id_proveedor,
          "id_sucursal":self.id_sucursal,
          "items":JSON.stringify(items),
        },
        "success":function(r) {
          window.open("app/#pedido_proveedor/"+r.id);
        }
      })
    }

  });

})(app.views, app.models);


(function ( app ) {
  app.views.ReposicionAsistidaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#reposicion_asistida_item').html()),
    events: {
      "click .ver": "editar",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      var id_sucursal = $("#reposicion_asistida_buscar_sucursales").val();
      location.href="app/#reposicion_asistida/"+this.model.id+"/"+id_sucursal;
    },
  });

})( app );



(function ( app ) {

  app.views.ReposicionAsistidaTableView = app.mixins.View.extend({

    template: _.template($("#reposicion_asistida_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.reposicion_asistida_filter = (typeof window.reposicion_asistida_filter != "undefined") ? window.reposicion_asistida_filter : "";
      window.reposicion_asistida_id_sucursal = (typeof window.reposicion_asistida_id_sucursal != "undefined") ? window.reposicion_asistida_id_sucursal : 0;
      window.reposicion_asistida_estado = (typeof window.reposicion_asistida_estado != "undefined") ? window.reposicion_asistida_estado : -1;
      window.reposicion_asistida_page = (typeof window.reposicion_asistida_page != "undefined") ? window.reposicion_asistida_page : 1;
      this.permiso = this.options.permiso;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      $(this.el).find(".pagination_container").html(this.pagination.el);
      return this;
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.reposicion_asistida_filter != this.$("#reposicion_asistida_buscar").val().trim()) {
        window.reposicion_asistida_filter = this.$("#reposicion_asistida_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#reposicion_asistida_buscar_sucursales").length > 0) {
        if (window.reposicion_asistida_id_sucursal != this.$("#reposicion_asistida_buscar_sucursales").val().trim()) {
          window.reposicion_asistida_id_sucursal = this.$("#reposicion_asistida_buscar_sucursales").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#reposicion_asistida_buscar_estados").length > 0) {
        if (window.reposicion_asistida_estado != this.$("#reposicion_asistida_buscar_estados").val()) {
          window.reposicion_asistida_estado = this.$("#reposicion_asistida_buscar_estados").val();
          cambio_parametros = true;
        }
      }
      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.reposicion_asistida_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.reposicion_asistida_filter),
        "id_sucursal":window.reposicion_asistida_id_sucursal,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.reposicion_asistida_page);
    },

    addAll : function () {
      window.reposicion_asistida_page = this.pagination.getPage();
      this.$("#reposicion_asistida_table tbody").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ReposicionAsistidaItem({
        model: item,
        collection: self.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
        permiso: this.permiso,
      });
      this.$("#reposicion_asistida_table tbody").append(view.render().el);
    },

  });
})(app);