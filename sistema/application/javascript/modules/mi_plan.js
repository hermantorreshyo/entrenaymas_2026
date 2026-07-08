(function ( app ) {

  app.views.MiPlanView = app.mixins.View.extend({
    template: _.template($('#mi_plan_template').html()),
    myEvents: {
      "click .cambiar_plan":function(e) {
        var self = this;
        var id_plan = $(e.currentTarget).parent().parent().attr("data-id");

        if (id_plan == PERFIL) {
          alert ("Error");
          return false;
        }

        var opciones = new Array();
        opciones.push(
          {
            "id": "1",
            "value": "30",
            "title": "Valor Mensual",
            "text": "4.90 €"
          },
          {
            "id": "2",
            "value": "365",
            "title": "Valor Anual",
            "text": "49.00 €"
          },
        );

        //Cambia de normal a premium
        if (id_plan == 1358) {

          var view = new app.mixins.SelectOption({
            model: new app.models.AbstractModel({
              "titulo": "Cambiar a Perfil Premium",
              "opciones": opciones,
              "cupon_descuento": 1,
            }),
          });
          crearLightboxHTML({
            "html":view.el,
            "width":600,
            "height":400,
          });

        } else if (id_plan == 1357) {
          //Debemos buscar si tiene una subscripcion activa y cancelarla
          $.ajax({
            "url": "paycomet/function/endSubscription",
            "type": "post",
            "dataType": "json",
            "data": {
              "id_usuario": ID_USUARIO,
              "id_empresa": ID_EMPRESA,
            },
            "success": function(r) {
              alert (r.mensaje);
              if (r.error == 0) {
                location.reload();
              }  
            },
          })
        }
      },

      "click .verificar_perfil":function(e) {
        var self = this;
        var view = new app.views.VerificarPerfilView({
          model: new app.models.AbstractModel(),
        })
        crearLightboxHTML({
          "html":view.el,
          "width":600,
          "height":400,
        });
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





(function ( views, models ) {

  views.VerificarPerfilView = app.mixins.View.extend({

    template: _.template($("#verificar_perfil_view").html()),

    myEvents: {
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
      this.$("#user_id").val(ID_USUARIO);
    },

    render: function() {
      var permiso = false;
      var obj = { edicion: permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      $.ajax({
        "url": "/sistema/paycomet/function/verificar_perfil_iframe",
        "dataType": "json",
        "type":"post",
        "data":{
          "user_id":ID_USUARIO,
        },
        "success": function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
            return;
          }
          $("#verificar_perfil_cont").html("<iframe style='width:100%; border:none; height:320px;' src='"+r.redirect+"'></iframe>");
        },
      });

      return this;
    },
  
  });
})(app.views, app.models);


(function ( views, models ) {

  views.MuchasGraciasView = app.mixins.View.extend({

    template: _.template($("#muchas_gracias_view").html()),

    myEvents: {
    },

    initialize: function() {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      //this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.bind("ver",this.ver,this); // Mostramos el objeto
      this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
      _.bindAll(this);
      this.render();
      $('.modal:last').modal('hide');
    },

    render: function() {
      var permiso = false;
      var obj = { edicion: permiso };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
  
  });
})(app.views, app.models);


(function ( views, models ) {

  views.PagoRechazadoView = app.mixins.View.extend({

    template: _.template($("#pago_rechazado_view").html()),

    myEvents: {
    },

    initialize: function() {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      //this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.bind("ver",this.ver,this); // Mostramos el objeto
      this.bind("limpiar",this.limpiar,this); // Limpiamos el objeto
      _.bindAll(this);
      this.render();
      $('.modal:last').modal('hide');
    },

    render: function() {
      var permiso = false;
      var obj = { edicion: permiso };
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
  
  });
})(app.views, app.models);
