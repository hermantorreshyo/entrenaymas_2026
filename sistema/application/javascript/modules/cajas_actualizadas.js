(function ( models ) {

  models.CajaActualizada = Backbone.Model.extend({
    urlRoot: "caja/",
    defaults: {
      sucursal: "",
      numero: "",
      nombre: "",
      fecha: "",
      version_git: "",
      version_db: "",
    }
  });
	  
})( app.models );


(function (collections, model, paginator) {
	collections.CajasActualizadas = paginator.requestPager.extend({
		model: model,
		paginator_core: {
			url: "caja/function/listado_actualizadas/",
		}
	});
})( app.collections, app.models.CajaActualizada, Backbone.Paginator);


(function ( app ) {

  app.views.CajaActualizadaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cajas_actualizadas_item').html()),
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
  });

})( app );


(function ( app ) {

  app.views.CajasActualizadasTableView = app.mixins.View.extend({

  	template: _.template($("#cajas_actualizadas_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
      this.options = options;
			this.permiso = this.options.permiso;

			var search = new app.mixins.SearchView({
				collection: lista
			});

			lista.on('add', this.addOne, this);
			lista.on('reset', this.addAll, this);
			lista.on('all', this.render, this);

			var obj = { permiso: this.permiso };
			$(this.el).html(this.template(obj));
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.CajaActualizadaItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);