(function ( app ) {

  app.mixins.SelectOption = Backbone.View.extend({

    template: _.template($("#select_option").html()),

    events: {
      "click .option": function(e) {
        this.$(".option").removeClass("selected");
        $(e.currentTarget).addClass("selected");
        this.$(".seleccionar").removeAttr("disabled");
      },
      "click .seleccionar": "seleccionar",
      "click .cerrar": function(e) {
        $('.modal:last').modal('hide');
      },
      "change .cupon_descuento": function(e) {
        var self = this;
        window.cupon_descuento = 0;
        var codigo = $(e.currentTarget).val();
        if (codigo == "") {
          $(".texto-cupon").addClass("dn");
          return false;
        } 

        $.ajax({
          "url": "cupones/function/verificar_cupon",
          "type": "post",
          "dataType": "json",
          "data": {
            "codigo": $(e.currentTarget).val(),
            "id_empresa": ID_EMPRESA,
          }, success:function(r) {
            if (r.error == 0) {
              $(".texto-cupon").removeClass("dn text-danger");
              $(".texto-cupon").addClass("text-success");

              // Guardamos el cupon en el usuario
              $.ajax({
                "url":"usuarios/function/change_property/",
                "dataType":"json",
                "type":"post",
                "data":{
                  "id":ID_USUARIO,
                  "table":"com_usuarios",
                  "attribute":"cupon",
                  "value":r.cupon,
                }
              });

              window.cupon_descuento = r.descuento;
            } else {
              $(".texto-cupon").removeClass("dn text-success");
              $(".texto-cupon").addClass("text-danger");              

              // Limpiamos el cupon del usuario
              $.ajax({
                "url":"usuarios/function/change_property/",
                "dataType":"json",
                "type":"post",
                "data":{
                  "id":ID_USUARIO,
                  "table":"com_usuarios",
                  "attribute":"cupon",
                  "value":"",
                }
              });
            }
            $(".texto-cupon").text(r.mensaje);
          },
        });
      },
    },
    
    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.callback = (this.options.callback !== undefined) ? this.options.callback : "";
      this.cupon_descuento = (this.options.cupon_descuento !== undefined) ? this.options.cupon_descuento : 0;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { id: this.model.id, cupon_descuento: this.cupon_descuento };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      
      return this;
    },


    seleccionar: function() {
      var self = this;
      var value = this.$(".option.selected").attr("data-value");
      $('.modal:last').modal('hide');
      window.openIframePaycomet(value, ID_USUARIO);      
    },
      
  });

})(app);