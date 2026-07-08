(function ( app ) {

  app.views.GaleriaView = app.mixins.View.extend({
    template: _.template($("#galeria_template").html()),
    myEvents: {
      "click .cerrar":"cerrar",
      "click .buscar_productos":"buscar_productos",
    },
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      var lista = this.collection;
      this.options = options;
      this.view = options.view;
      this.render();
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template({}));
    },

    buscar_productos: function() {
      var self = this;
      var filter = this.$("#galeria_buscador").val();
      $.ajax({
        "url":"articulos/function/get_images/",
        "type":"get",
        "data":{
          "filter":filter,
        },
        "dataType":"json",
        "success":function(res) {
          self.render_table(res);
        }
      })
    },

    render_table: function(res) {
      var self = this;
      this.$("#galeria_container").empty();
      for(var i=0;i<res.results.length;i++) {
        var r = res.results[i];
        var v = new app.views.GaleriaItemView({
          "model":new app.models.AbstractModel(r),
          "model_orig":self.model,
          "view":self,
        });
        this.$("#galeria_container").append(v.el);
      }
    },

  });
})(app);

(function ( app ) {
  app.views.GaleriaItemView = app.mixins.View.extend({
    template: _.template($("#galeria_item_template").html()),
    className: "col-xs-6 col-md-4",
    myEvents: {
      "click .aceptar":"aceptar",
    },
    initialize : function (options) {
      this.view = options.view;
      // El modelo original (por ej Articulo, Entrada)
      this.model_orig = options.model_orig,
      this.render();
    },
    aceptar:function() {
      var im = this.model_orig.get("images").push(this.model.get("path"));
      this.model_orig.trigger("change_table");
      this.view.cerrar();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });
})(app);