(function ( app ) {

app.views.DashboardPropiedades = app.mixins.View.extend({

  template: _.template($("#propiedades_dashboard_template").html()),
	
	myEvents: {

    "click #sugerencia-llamar-atencion-cliente":function(){
      window.open("https://api.whatsapp.com/send?phone=5492215021999","_blank");
    },

    "click #sugerencia-llamar-soporte-tecnico":function(){
      window.open("https://api.whatsapp.com/send?phone=5492352444378","_blank");
    },

    "click #sugerencia-configurar-web":function() {
      location.href="app/#web_configuracion";
    },

    "click #sugerencia-primera-propiedad":function() {
      location.href="app/#propiedad";
    },

    "click #sugerencia-primer-contacto":function() {
      location.href="app/#contactos";
    },
  },
	
  initialize: function() {
		var self = this;
    $(this.el).html(this.template(this.model.toJSON()));    
  },	
});

})(app);
