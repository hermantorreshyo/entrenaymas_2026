(function (collections, model, paginator) {
  collections.ReservasViajes = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "reservas_asientos/function/buscar/"
    }
  });
})( app.collections, app.models.ReservaAsiento, Backbone.Paginator);

(function ( app ) {

  app.views.ReservasViajesTableView = app.mixins.View.extend({

    template: _.template($("#reservas_viajes_resultados_template").html()),

    myEvents: {
      "change #reservas_viajes_buscar":"buscar",
      "click .buscar":"buscar",
      "click .nuevo":"nuevo",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      // Filtros de la viaje
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
      this.render();
      this.collection.on('sync', this.addAll, this);

      this.collection.server_api = {
        "filter":this.filter,
      };            
      this.collection.goTo(this.pagina);
    },

    render: function() {
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);

      return this;
    },

    nuevo: function() {
      var reserva = new app.models.ReservaAsiento({
        "id_vehiculo":0,
        "id_viaje":0,
        "asientos":[],
        "pagos":[],
      });
      var view = new app.views.ReservaAsientoEditView({
        "model":reserva,
      })
      crearLightboxHTML({
        "html":view.el,
        "width":1000,
        "height":500,
      });
    },

    buscar: function() {
      this.filter = this.$("#reservas_viajes_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
      };
      this.collection.pager();            
    },

    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);            
    },

    addOne : function ( item ) {
      console.log(item);
      var view = new app.views.ReservasViajesItemResultados({
        model: item,
      });
      this.$(".tbody").append(view.render().el);
    },

  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ReservasViajesItemResultados = app.mixins.View.extend({

    template: _.template($("#reservas_viajes_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      // Editamos la reserva
      var self = this;
      var reserva = new app.models.ReservaAsiento({
        "id":self.model.id,
      });
      reserva.fetch({
        "success":function(){
          var view = new app.views.ReservaAsientoEditView({
            "model":reserva,
          })
          crearLightboxHTML({
            "html":view.el,
            "width":1000,
            "height":500,
          });                        
        }
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);
