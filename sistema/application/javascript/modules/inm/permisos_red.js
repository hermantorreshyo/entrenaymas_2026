(function ( views, models ) {

	views.PermisosRedView = app.mixins.View.extend({

		template: _.template($("#permisos_red_template").html()),

		myEvents: {
			"click .guardar": "guardar",
      "click .invitar_colega":function() {
        var p = new app.views.InvitarColegaView({
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });
      },
      "click .estado_1":function(e) {
        $(e.currentTarget).toggleClass("btn-success");
        if (!$(e.currentTarget).hasClass("btn-success")) {
          $(e.currentTarget).parent().parent().find(".estado_2").removeClass("btn-info");
        }
      },
      "click .estado_2":function(e) {
        $(e.currentTarget).toggleClass("btn-info");
        if ($(e.currentTarget).hasClass("btn-info")) {
          $(e.currentTarget).parent().parent().find(".estado_1").addClass("btn-success");
        }
      },
      "click .enviar_whatsapp":function(e) {
        var tel = String($(e.currentTarget).data("telefono"));
        tel = tel.replace(/[^\d.-]/g, '');
        tel = tel.replace(/\-/g, "");
        var link_ws = "https://wa.me/"+tel;
        window.open(link_ws,"_blank");      
      }
		},

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
        
    guardar: function() {
      var self = this;
      var datos = new Array();
      this.$("#permisos_red_tabla tbody tr").each(function(i,e){
        var id = $(e).data("id");
        var estado = 0;
        if ($(e).find(".estado_1").hasClass("btn-success")) estado = 1;
        if ($(e).find(".estado_2").hasClass("btn-info")) estado = 2;
        datos.push({
          "id_empresa_compartida":id,
          "estado":estado,
        });
      });
      $.ajax({
        "url":"permisos_red/function/guardar/",
        "dataType":"json",
        "type":"post",
        "data":{
          "datos":datos,
        },
        "success":function() {
          location.reload();
        },
      });
		},		
	});

})(app.views, app.models);


(function ( views, models ) {

  views.InvitarColegaView = app.mixins.View.extend({

    template: _.template($("#invitar_colega_template").html()),

    myEvents: {
      "click .enviar":function() {
        var email = this.$("#invitar_colega_email").val();
        var inmobiliaria = this.$("#invitar_colega_inmobiliaria").val();
        if (isEmpty(inmobiliaria)) {
          alert("Por favor ingrese el nombre del colega.");
          return;
        }        
        if (!validateEmail(email)) {
          alert("Por favor ingrese un email.");
          return;
        }
        $.ajax({
          "url":"propiedades/function/invitar_colega/",
          "type":"post",
          "dataType":"json",
          "data":{
            "id_empresa":ID_EMPRESA,
            "email":email,
            "inmobiliaria":inmobiliaria,
          },
          "success":function(r) {
            if (r.error == 0) {
              alert("Tu invitacion ha sido enviada. Muchas gracias!");
            } else {
              alert(r.mensaje);
            }
            $(".modal:last").trigger('click');
          }
        });
      },
    },

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },

  });

})(app.views, app.models);