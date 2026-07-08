(function ( app ) {

  app.views.DashboardViajes = app.mixins.View.extend({

    template: _.template($("#viajes_dashboard_template").html()),
    
    myEvents: {
      // QUIERE CONFIGURAR DISEÑO
      "click .conf_disenio_si":function() {
        this.save_property("configurar_disenio",1,function(r){
          location.href="app/#web_configuracion";
        });
      },
      // NO QUIERE CONFIGURAR DISEÑO
      "click .conf_disenio_no":function() {
        this.save_property("configurar_disenio",1,function(r){
          location.reload();
        });
      },
      
      // QUIERE AGREGAR ELEMENTOS
      "click .subir_elemento_si":function() {
        this.save_property("subir_elemento",1,function(r){
          location.href="app/#propiedad";
        });
      },
      // NO QUIERE AGREGAR ELEMENTOS
      "click .subir_elemento_no":function() {
        this.save_property("subir_elemento",1,function(r){
          location.reload();
        });
      },        

      // QUIERE CONFIGURAR DATOS EMPRESA
      "click .conf_empresa_si":function() {
        this.save_property("datos_empresa",1,function(r){
          location.href="app/#empresa";
        });
      },
      // NO QUIERE AGREGAR ELEMENTOS
      "click .conf_empresa_no":function() {
        this.save_property("datos_empresa",1,function(r){
          location.reload();
        });
      },        
    },
    
    save_property: function(attribute,value,callback) {
      $.ajax({
        "url":"/sistema/web_configuracion/function/save_attribute/",
        "dataType":"json",
        "type":"post",
        "data":{
          "attribute":attribute,
          "value":value,
        },
        "success":callback,
      });
    },
    
    initialize: function() {
      
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      
      // Agregamos el form de ayuda
      var ayuda = new app.views.AyudaFormView({
       model: new Backbone.Model.extend(),
     });
      this.$("#dashboard_ayuda").append(ayuda.render().el);		
    },	
  });

})(app);
