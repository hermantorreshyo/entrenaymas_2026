(function ( app ) {

  app.views.LiquidacionChoferesResultados = app.mixins.View.extend({

    template: _.template($("#liquidacion_choferes_resultados_template").html()),
        
    myEvents: {
      "click .buscar": "buscar",
      "click .exportar":"exportar",
    },
        
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));

      new app.mixins.Select({
        modelClass: app.models.Tripulante,
        url: "tripulantes/",
        firstOptions: ["<option value='0'>Seleccione</option>"],
        render: "#liquidacion_choferes_tripulantes",
      });

      var mes = moment().format("MM");
      this.$("#liquidacion_choferes_mes").val(mes);

      var anio = moment().format("YYYY");
      this.$("#liquidacion_choferes_anio").val(anio);

      //this.buscar();
      return this;
    },

    buscar : function() {
      var self = this;
      var id_tripulante = this.$("#liquidacion_choferes_tripulantes").val();
      if (id_tripulante == 0) {
        alert("Por favor seleccione un chofer.");
        return;
      }
      var mes = this.$("#liquidacion_choferes_mes").val();
      var anio = this.$("#liquidacion_choferes_anio").val();
      if (isEmpty(anio)) {
        alert("Por favor seleccione un año.");
        this.$("#liquidacion_choferes_anio").select();
        return;
      }

      $.ajax({
        "url":"tripulantes/function/liquidacion/",
        "dataType":"json",
        "type":"post",
        "data":{
          "mes":mes,
          "anio":anio,
          "id_tripulante":id_tripulante,
        },
        "success":function(r) {
          self.mostrar_resultados(r);
        }
      });
    },
        
    mostrar_resultados: function(result) {

      $(this.el).find(".tbody").empty();
      var total = 0;
      for(i=0;i<result.length;i++) {
        var m = result[i];
        total += parseFloat(m.monto);
        var item = new app.views.LiquidacionChoferesItemResultados({
          model: new app.models.AbstractModel(m)
        });
        $(this.el).find(".tbody").append(item.el);
      }
      this.$("#liquidacion_choferes_total").html("$ "+Number(total).toFixed(2));
    },
    
    exportar: function() {
      var self = this;
      var nombre = this.$("#liquidacion_choferes_tripulantes option:selected").text();
      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "concepto":$.trim($(e).find("td:eq(0) .text-info").html()),
          "base":$.trim($(e).find("td:eq(1)").html()),
          "porcentaje":$.trim($(e).find("td:eq(2)").html()),
          "monto":$.trim($(e).find("td:eq(3)").html()),
        });
      });
      var header = new Array("Concepto","Base","Porcentaje","Subtotal");
      this.exportar_excel({
        "filename":"liquidacion",
        "title":"Liquidacion de "+nombre,
        "data":array,
        "header":header,
      });
    },
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.LiquidacionChoferesItemResultados = Backbone.View.extend({
    template: _.template($("#liquidacion_choferes_item_resultados_template").html()),
    tagName: "tr",
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);
