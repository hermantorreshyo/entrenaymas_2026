(function ( models ) {

  models.Cocina = Backbone.Model.extend({
    urlRoot: "cocinas/",
    defaults: {}
  });

})( app.models );

(function (collections, model, paginator) {

  collections.Cocinas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 99999,
    },        
    paginator_core: {
      url: "cocinas/function/consulta/",
    },
  });

})( app.collections, app.models.Cocina, Backbone.Paginator);


(function ( app ) {

  app.views.CocinasView = app.mixins.View.extend({

    template: _.template($("#cocinas_template").html()),

    myEvents: {
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());

      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);

      window.timer = setInterval(function(){
        app.collections.cocinas.pager();
      },10000);

      this.buscar();
    },

    buscar: function() {
      var self = this;
      /*
      var filtros = {};

      if (!isEmpty(this.$("#deliveries_listado_buscar").val())) 
          filtros.filter = this.$("#deliveries_listado_buscar").val();
      if (!isEmpty(this.$("#deliveries_listado_cliente").val())) 
          filtros.id_cliente = this.$("#deliveries_listado_cliente").val();
      if (!isEmpty(this.$("#deliveries_listado_numero").val())) 
          filtros.numero = this.$("#deliveries_listado_numero").val();
      
      filtros.tipo = "D";
      filtros.fecha_desde = moment().format("DD-MM-YYYY");
      filtros.fecha_hasta = moment().format("DD-MM-YYYY");
      if (SOLO_USUARIO == 1) filtros.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      filtros.id_proyecto = ID_PROYECTO;

      this.collection.server_api = filtros;
      */
      this.collection.pager();            
    },

    addAll : function () {
      this.$("#cocinas_en_proceso").empty();
      this.$("#cocinas_finalizados").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.CocinaItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      if (item.get("id_tipo_estado") == 0) {
        this.$("#cocinas_en_proceso").append(view.render().el);
      } else if (item.get("id_tipo_estado") == 1) {
        this.$("#cocinas_finalizados").append(view.render().el);
      }
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.CocinaItemResultados = app.mixins.View.extend({

    template: _.template($("#cocinas_item_template").html()),
    myEvents: {
      "click .terminar":"terminar",
      "change .entregado_por":"entregado_por",
    },
    terminar : function(e) {
      var self = this;
      var id = $(e.currentTarget).data("id");
      var items = this.model.get("items");
      var items2 = new Array();
      for(var i=0;i<items.length;i++) {
        var item = items[i];
        if (item.id == id) item.tipo = ((item.tipo==0)?1:0);
        items2.push(item);
      }
      this.model.set({
        "items":items2,
      });
      $.ajax({
        "url":"cocinas/function/cambiar_estado_item/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id":id,
        },
        "success":function() {
          self.render();
        }
      })
    },
    entregado_por: function(e) {
      var self = this;
      var id_usuario = $(e.currentTarget).val();
      this.model.set({
        "id_usuario":id_usuario,
      });
      $.ajax({
        "url":"cocinas/function/cambiar_usuario/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id":self.model.id,
          "id_usuario":id_usuario,
        },
        "success":function() {
          self.render();
        },
      });
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
