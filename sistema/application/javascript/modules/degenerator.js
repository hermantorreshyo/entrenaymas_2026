(function ( app ) {

app.views.Degenerator = Backbone.View.extend({

  template: _.template($("#degenerator_template").html()),
  
  events: {
    "click .enviar":"enviar",
  },

  enviar: function() {
    var self = this;
    $.ajax({
      "url":"app/degenerator/",
      "dataType":"json",
      "type":"post",
      "data": {
        "singular":self.$("#singular").val(),
        "plural":self.$("#plural").val(),
        "carpeta":self.$("#carpeta").val(),
        "tagfontawesome":self.$("#tagfontawesome").val(),
        "tagprincipal":self.$("#tagprincipal").val(),
        "tagnombre":self.$("#tagnombre").val(),
      },
      "success":function(r) {
        self.$("#salida").val(r.salida);
      },
    });
  },
  
  initialize: function() {
    $(this.el).html(this.template());
    this.render();
  },

});

})(app);