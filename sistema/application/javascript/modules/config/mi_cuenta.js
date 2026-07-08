(function ( app ) {
  app.views.MiCuenta = app.mixins.View.extend({
    template: _.template($("#mi_cuenta_template").html()),
    initialize: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      this.render();
    },
    render: function() {
      return this;
    },
  });
})(app);
