// -----------
//   MODELO
// -----------

(function ( models ) {

  models.MedioPagoConfiguracion = Backbone.Model.extend({
    urlRoot: "medios_pago_configuracion/",
    idAttribute:"id_empresa",
    defaults: {
      mp_client_id: "",
      mp_client_secret: "",
      sms_gateway_user: "",
      sms_gateway_password: "",
      paypal_email: "",
      habilitar_stripe: 0,
      stripe_secret: "",
      stripe_public: "",
      id_empresa: 0,
      habilitar_mp: 1,
      habilitar_paypal: 0,
      habilitar_banco: 0,
      habilitar_a_convenir: 1,
      habilitar_pago_sucursal: 1,
      habilitar_contrarrembolso: 0,
      mp_moneda: "ARS",
      id_email_pago_mp: 0,
      id_email_pago_paypal: 0,
      id_email_pago_banco: 0,
      id_email_pago_contrarrembolso: 0,
      id_email_pago_convenir: 0,
      id_email_pago_sucursal: 0,
    }
  });

})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.MedioPagoConfiguracionEditView = app.mixins.View.extend({

    template: _.template($("#medios_pago_configuracion_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function() {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      //this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);

      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);

      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      try {
        $(".error").removeClass("error");

        var self = this;
        this.model.set({
          "mp_moneda":self.$("#medios_pago_configuracion_monedas").val(),
        });

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },
    

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error != undefined && response.error == true) {
              show("Hubo un error al guardar los datos.");
            } else {
              window.location.reload();                        
            }
          }
        });  
      }
    },

    limpiar : function() {
      this.model = new app.models.MedioPagoConfiguracion()
      this.render();
    },

  });

})(app.views, app.models);