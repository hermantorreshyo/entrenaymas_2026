(function ( models ) {

  models.EstadisticaGasto = Backbone.Model.extend({
    urlRoot: 'cajas_movimientos/',
    defaults: {
      "codigo":"",
      "nombre":"",
      "total":0,
    },
  });

})( app.models );

// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.EstadisticasGastosResultados = app.mixins.View.extend({

    template: _.template($("#estadisticas_gastos_resultados_template").html()),

    myEvents: {
      "click .generar": "buscar",
      "change #estadisticas_gastos_fecha_desde":"buscar",
      "change #estadisticas_gastos_fecha_hasta":"buscar",
      "click .exportar":function(){
        this.exportar(0);
      },
      "click .exportar_seleccionados":function(){
        this.exportar(1);
      },
      "click .exportar_con_valores":function(){
        this.exportar(2);
      },
      "click .expand": "expand",
    },

    buscar : function() {
      var self = this;
      var mes = this.$("#estadisticas_gastos_mes").val();
      var anio = (this.$("#estadisticas_gastos_anio").val().length > 0) ? this.$("#estadisticas_gastos_anio").val().substr(2) : "";
      var movimiento = mes+""+anio;
      var desde = this.$("#estadisticas_gastos_fecha_desde").val();
      var hasta = this.$("#estadisticas_gastos_fecha_hasta").val();
      var id_sucursal = this.$("#estadisticas_gastos_sucursales").val();
      var incluir = this.$("#estadisticas_gastos_incluir_todas").val();
      $.ajax({
        "url":"cajas_movimientos/function/resumen_arbol/",
        "dataType":"json",
        "type":"post",
        "data":{
          "movimiento":movimiento,
          "desde":desde,
          "hasta":hasta,
          "id_sucursal":id_sucursal,
          "incluir":incluir,
        },
        "success":function(r){
          self.addAll(r.results);
        }
      });
    },

    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.expanded = 0;
      window.estadisticas_gastos_mes_fiscal = (typeof window.estadisticas_gastos_mes_fiscal != "undefined") ? window.estadisticas_gastos_mes_fiscal : "";
      window.estadisticas_gastos_anio_fiscal = (typeof window.estadisticas_gastos_anio_fiscal != "undefined") ? window.estadisticas_gastos_anio_fiscal : "";
      window.estadisticas_gastos_fecha_desde = (typeof window.estadisticas_gastos_fecha_desde != "undefined") ? window.estadisticas_gastos_fecha_desde : "";
      window.estadisticas_gastos_fecha_hasta = (typeof window.estadisticas_gastos_fecha_hasta != "undefined") ? window.estadisticas_gastos_fecha_hasta : "";

      $(this.el).html(this.template());

      createdatepicker(this.$("#estadisticas_gastos_fecha_desde"),window.estadisticas_gastos_fecha_desde);
      createdatepicker(this.$("#estadisticas_gastos_fecha_hasta"),window.estadisticas_gastos_fecha_hasta);
    },

    exportar : function(solo_seleccionados) {

      var self = this;
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });
      // Acomodamos los datos
      var array = new Array();
      this.$("#estadisticas_gastos_tabla tbody tr:visible").each(function(i,e){
        var total = $(e).find("td:eq(5)").html();
        total = total.replace(/\./g,"");
        total = total.replace(/\,/g,".");
        total = total.replace(/\$/g,"");
        total = parseFloat(total.trim());

        var nombre = $(e).find(".nombre").html().replace(/\&nbsp\;/g," ");
        var nivel = $(e).find(".nombre").data("nivel");
        for(var w=0;w<nivel;w++) {
          nombre = "    "+nombre;
        }
        if (solo_seleccionados == 1) {
          if ($(e).find(".i-checks input[type='checkbox']").is(":checked")) {
            array.push({
              "concepto": nombre,
              "total":total,
            });
          }
        } else if (solo_seleccionados == 2) {
          if (total != 0) {
            array.push({
              "concepto": nombre,
              "total":total,
            });
          }
        } else {
          array.push({
            "concepto": nombre,
            "total":total,
          });
        }
      });
      if (array.length == 0) return;
      var fecha = (self.$("#estadisticas_gastos_mes").val() != "00") ? (self.$("#estadisticas_gastos_mes option:selected").text()+" "+self.$("#estadisticas_gastos_anio").val()) : "";
      this.exportar_excel({
        "filename":"resumen_gastos",
        "title":"Resumen de gastos",
        "date":fecha,
        "data":array,
        "header":header,
      });     
    },

    addAll: function(results) {
      var self = this;
      self.$("#estadisticas_gastos_tabla tbody").empty();
      self.total = 0;
      for(var i=0;i<results.length;i++) {
        var o = new app.models.EstadisticaGasto(results[i]);
        self.addOne(o,1);
      }
      self.$("#estadisticas_gastos_total").html("$ "+Number(self.total).format(2));
    },

    addOne : function (item,nivel) {
      var view = new app.views.EstadisticasGastosItemResultados({
        model: item,
        nivel: nivel,
      });
      this.$("#estadisticas_gastos_tabla tbody").append(view.render().el);

      var children = item.get("children");
      if (children.length > 0) {
        for(var i=0;i<children.length;i++) {
          var r = children[i];
          var resumen = new app.models.EstadisticaGasto(r);
          var nombre = resumen.get("nombre");
          resumen.set({ "nombre": nombre });
          var proximo_nivel = nivel + 1;
          this.addOne(resumen,proximo_nivel);
        }
      }

      if (item.get("id_padre") == 0) {
        this.total = this.total + Number(item.get("total"));
      }
    },

    expand: function() {
      if (this.expanded == 0) {
        this.$("#estadisticas_gastos_tabla tbody tr.child").show();
        this.$(".icon").removeClass("fa-plus");
        this.$(".icon").addClass("fa-minus");
        this.expanded = 1;
      } else {
        this.$("#estadisticas_gastos_tabla tbody tr.child").hide();
        this.$(".icon").removeClass("fa-minus");
        this.$(".icon").addClass("fa-plus");
        this.expanded = 0;
      }
    }

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.EstadisticasGastosItemResultados = Backbone.View.extend({
    template: _.template($("#estadisticas_gastos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click td":"expand",
      "click .ver_listado":function() {
        var mes = $("#estadisticas_gastos_mes").val();
        var anio = $("#estadisticas_gastos_anio").val();
        var desde = $("#estadisticas_gastos_fecha_desde").val().replace(/\//g,"-");
        var hasta = $("#estadisticas_gastos_fecha_hasta").val().replace(/\//g,"-");
        if (!isEmpty(desde)) mes = desde;
        if (!isEmpty(hasta)) anio = hasta;
        var url = "app/#gastos/"+mes+"/"+anio+"/"+this.model.id;
        window.open(url,"_blank");
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.nivel = options.nivel;
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.nivel = this.nivel;
      $(this.el).html(this.template(obj));

      // Ponemos como data el propio id
      $(this.el).data("id",this.model.id);

      var id_padre = this.model.get("id_padre");
      if (this.nivel == 2) {
        $(this.el).hide();
        this.$("td").addClass("bg-light");
        this.$("td").addClass("dk");
        $(this.el).addClass("child");
        $(this.el).addClass("id_padre_"+id_padre);
      } else if (this.nivel == 3) {
        $(this.el).hide();
        this.$("td").addClass("bg-light");
        this.$("td").addClass("dker");
        $(this.el).addClass("child");
        $(this.el).addClass("id_padre_"+id_padre);
      }
      return this;
    },
    expand: function() {
      this.$(".icon").toggleClass("fa-minus");
      $(".id_padre_"+this.model.id).toggle();
    }
  });
})(app);