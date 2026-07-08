(function ( app ) {

  app.views.Importar = Backbone.View.extend({

    template: _.template($("#importar_template").html()),

    events: {
      "click .aceptar": "aceptar",
      "change .inputFile":function(e) {
        $(e.currentTarget).next(".bootstrap-filestyle").find("input[type=text]").val($(e.currentTarget).val());
      },
    },
    
    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.url = (typeof this.options.url != "undefined") ? this.options.url : this.options.table+"/function/importar/";
      this.titulo = (typeof this.options.titulo != "undefined") ? this.options.titulo : "Importaci&oacute;n de datos";
      this.texto = (typeof this.options.texto != "undefined") ? this.options.texto : "Seleccione el archivo que desea importar";
      var self = this;
      this.render();
    },
    
    render: function() {
      var self = this;
      $(this.el).html(this.template({
        "titulo":self.titulo,
        "texto":self.texto,
      }));
      return this;
    },
    
    aceptar: function() {
      var f = document.createElement("form");
      f.setAttribute('method',"post");
      f.setAttribute('enctype',"multipart/form-data");
      f.setAttribute('action',this.url);
      $(f).html($("#file")); // Agregamos el archivo
      $(f).css("display","none");
      document.body.appendChild(f);
      $(f).submit();
    },
      
  });

})(app);