(function ( views, models ) {

  views.RatingView = Backbone.View.extend({
    template: _.template($("#rating_template").html()),
    events: {
      "mouseenter .star":function(e) {
        if (!this.selectable) return;
        var item = $(e.currentTarget).data("item");
        for(var i=1;i<=item;i++) {
          this.$(".star:eq("+(i-1)+")").addClass('temporal-active');
        }
        for (var j=(item+1);j<=this.stars;j++) {
          this.$(".star:eq("+(j-1)+")").removeClass('temporal-active'); 
        }
      },
      "mouseleave .calification": function(e) {
        if (!this.selectable) return;
        this.$(".star").removeClass('temporal-active');
      },
      "click .star":"seleccionar",
    },
    initialize: function(options) {
      _.bindAll(this);
      this.stars = (typeof options.stars != "undefined") ? options.stars : 5,
      this.value = (typeof options.value != "undefined") ? options.value : 0,
      this.selectable = (typeof options.selectable != "undefined") ? options.selectable : true,
      this.selected = (typeof options.selected != "undefined") ? options.selected : null,
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template({
        stars: self.stars,
        value: self.value,
      }));
      return this;
    },
    seleccionar: function(e) {
      if (!this.selectable) return;
      e.preventDefault();
      e.stopPropagation();
      var item = $(e.currentTarget).data("item");
      if (this.selected != null) this.selected(item);
      return false;
    },
  });

})(app.views, app.models);



(function ( views, models ) {

    views.AyudaView = Backbone.View.extend({
        template: _.template($("#ayuda_template").html()),
        events: {
            "click .cerrar": function() {
                $('.modal:last').modal('hide');
            },
        },
        initialize: function() {
            _.bindAll(this);
            this.render();
        },
        render: function() {
            $(this.el).html(this.template());
            return this;
        },
        setText: function(text) {
            this.$(".texto").html(text);            
        },
    });

})(app.views, app.models);



(function ( views, models ) {

    views.AyudaFormView = Backbone.View.extend({
        template: _.template($("#ayuda_form_template").html()),
        events: {
            "click .enviar": function() {
                var self = this;
                var asunto = this.$("#ayuda_form_asunto").val();
                var texto = this.$("#ayuda_form_texto").val();
                if (isEmpty(texto)) {
                    alert("Ingrese su consulta.");
                    this.$("#ayuda_form_texto").focus();
                    return;
                }
                $.ajax({
                    "url":"/sistema/consultas/function/enviar/",
                    "dataType":"json",
                    "type":"post",
                    "data":{
                        "asunto":asunto,
                        "nombre":NOMBRE,
                        "email":EMAIL,
                        "mensaje":texto,
                    },
                    "success":function(r) {
                        if (r.error == 0) {
                            alert("Su consulta ha sido enviada. Nos pondremos en contacto con Ud. a la mayor brevedad.");
                            self.render();
                        } else {
                            alert("Hubo un error al enviar su consulta.");
                        }
                    }
                });
            },
        },
        initialize: function() {
            _.bindAll(this);
            this.render();
        },
        render: function() {
            $(this.el).html(this.template());
            return this;
        },
    });

})(app.views, app.models);