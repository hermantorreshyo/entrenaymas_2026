(function ( models ) {

  models.MantConfiguracion = Backbone.Model.extend({
    urlRoot: "mant_configuracion/",
    idAttribute: "id_empresa",
    defaults: {
      id_empresa: 0,
      mant_toneladas_mensuales: 0,
      mant_horas_mensuales: 0,
    }
  });

})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.MantConfiguracionEditView = app.mixins.View.extend({

		template: _.template($("#mant_configuracion_template").html()),

		myEvents: {
			"click .guardar": "guardar",
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
  	   // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      this.$("#mant_horario_desde").mask("99:99");
      this.$("#mant_horario_hasta").mask("99:99");
      return this;
    },        

    validar: function() {
      try {
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() 
    {
      var self = this;
      if (this.validar()) {
        this.model.save({},{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },		
  });

})(app.views, app.models);