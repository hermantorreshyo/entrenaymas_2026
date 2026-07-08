// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Notificacion = Backbone.Model.extend({
    urlRoot: "notificaciones/",
    defaults: {
      texto: "",
      texto_2: "",
      link: "",
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.Notificaciones = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "notificaciones/function/consultar/"+ID_EMPRESA+"/"+ID_SUCURSAL
		}
		
	});

})( app.collections, app.models.Notificacion, Backbone.Paginator);


(function ( app ) {

  app.views.NotificacionItem = app.mixins.View.extend({
    template: _.template($('#notificacion_item_template').html()),
    className: "media list-group-item",
    myEvents: {
      "click .link": function(e) {
        var self = this;
        var link = this.model.get("link");
        e.preventDefault();
        e.stopPropagation();
        $.ajax({
          "url":"notificaciones/function/limpiar/"+ID_EMPRESA+"/"+self.model.id,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0 && !isEmpty(link)) {
              location.href = link;
            }
            $(self.el).remove();
          }
        })
      },
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });

})( app );