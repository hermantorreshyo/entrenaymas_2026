(function ( models ) {

  models.CompraResumen = Backbone.Model.extend({
    urlRoot: 'compras/',
    defaults: {
      "codigo":"",
      "nombre":"",
      "total":0,
      "iva":0,
      "neto":0
    },
  });

})( app.models );

/*
(function (collections, model, paginator) {

  collections.ComprasResumen = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 10,
    },
    
    paginator_core: {
      url: function() {
        var movimiento = ((this.meta("movimiento") == undefined) ? 0 : this.meta("movimiento"));
        var id_sucursal = ((this.meta("id_sucursal") == undefined) ? 0 : this.meta("id_sucursal"));
        var s = "compras/function/resumen_arbol_compras";
        s=s+"/"+movimiento;
        s=s+"/"+id_sucursal;
        return s;
      }
    },
  });

})( app.collections, app.models.CompraResumen, Backbone.Paginator);
*/


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ComprasResumenResultados = app.mixins.View.extend({

    template: _.template($("#compras_resumen_resultados_template").html()),

    myEvents: {
      "click .generar": "buscar",
      "change #compras_resumen_fecha_desde":"buscar",
      "change #compras_resumen_fecha_hasta":"buscar",
      "click .imprimir":function() {
        var mes = this.$("#compras_resumen_mes").val();
        var anio = (this.$("#compras_resumen_anio").val().length > 0) ? this.$("#compras_resumen_anio").val().substr(2) : "";
        var movimiento = mes+""+anio;
        var desde = this.$("#compras_resumen_fecha_desde").val();
        var hasta = this.$("#compras_resumen_fecha_hasta").val();
        var id_sucursal = this.$("#compras_resumen_sucursales").val();
        var id_razon_social = this.$("#compras_resumen_razones_sociales").val();
        var incluir = this.$("#compras_resumen_incluir_todas").val();
        var url = "compras/function/imprimir_resumen_arbol_compras/?";
        url+="movimiento="+movimiento+"&";
        url+="desde="+desde+"&";
        url+="hasta="+hasta+"&";
        url+="id_sucursal="+id_sucursal+"&";
        url+="id_razon_social="+id_razon_social+"&";
        url+="incluir="+incluir+"&";
        workspace.imprimir_reporte(url);
      },
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
      var mes = this.$("#compras_resumen_mes").val();
      var anio = (this.$("#compras_resumen_anio").val().length > 0) ? this.$("#compras_resumen_anio").val().substr(2) : "";
      var movimiento = mes+""+anio;
      var desde = this.$("#compras_resumen_fecha_desde").val();
      var hasta = this.$("#compras_resumen_fecha_hasta").val();
      var id_sucursal = this.$("#compras_resumen_sucursales").val();
      var id_razon_social = this.$("#compras_resumen_razones_sociales").val();
      var incluir = this.$("#compras_resumen_incluir_todas").val();
      $.ajax({
        "url":"compras/function/resumen_arbol_compras/",
        "dataType":"json",
        "type":"post",
        "data":{
          "movimiento":movimiento,
          "desde":desde,
          "hasta":hasta,
          "id_sucursal":id_sucursal,
          "id_razon_social":id_razon_social,
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
      window.compras_resumen_mes_fiscal = (typeof window.compras_resumen_mes_fiscal != "undefined") ? window.compras_resumen_mes_fiscal : "";
      window.compras_resumen_anio_fiscal = (typeof window.compras_resumen_anio_fiscal != "undefined") ? window.compras_resumen_anio_fiscal : "";
      window.compras_resumen_fecha_desde = (typeof window.compras_resumen_fecha_desde != "undefined") ? window.compras_resumen_fecha_desde : "";
      window.compras_resumen_fecha_hasta = (typeof window.compras_resumen_fecha_hasta != "undefined") ? window.compras_resumen_fecha_hasta : "";

      $(this.el).html(this.template());

      createdatepicker(this.$("#compras_resumen_fecha_desde"),window.compras_resumen_fecha_desde);
      createdatepicker(this.$("#compras_resumen_fecha_hasta"),window.compras_resumen_fecha_hasta);
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
      this.$("#compras_resumen_tabla tbody tr:visible").each(function(i,e){
        var neto = $(e).find("td:eq(2)").html();
        neto = neto.replace(/\./g,"");
        neto = neto.replace(/\,/g,".");
        neto = neto.replace(/\$/g,"");
        neto = parseFloat(neto.trim());
        var iva = $(e).find("td:eq(3)").html();
        iva = iva.replace(/\./g,"");
        iva = iva.replace(/\,/g,".");
        iva = iva.replace(/\$/g,"");
        iva = parseFloat(iva.trim());
        var reg_especiales = $(e).find("td:eq(4)").html();
        reg_especiales = reg_especiales.replace(/\./g,"");
        reg_especiales = reg_especiales.replace(/\,/g,".");
        reg_especiales = reg_especiales.replace(/\$/g,"");
        reg_especiales = parseFloat(reg_especiales.trim());
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
              "neto":neto,
              "iva":iva,
              "reg_especiales":reg_especiales,
              "total":total,
            });
          }
        } else if (solo_seleccionados == 2) {
          if (total != 0) {
            array.push({
              "concepto": nombre,
              "neto":neto,
              "iva":iva,
              "reg_especiales":reg_especiales,
              "total":total,
            });
          }
        } else {
          array.push({
            "concepto": nombre,
            "neto":neto,
            "iva":iva,
            "reg_especiales":reg_especiales,
            "total":total,
          });
        }
      });
      if (array.length == 0) return;
      var fecha = (self.$("#compras_resumen_mes").val() != "00") ? (self.$("#compras_resumen_mes option:selected").text()+" "+self.$("#compras_resumen_anio").val()) : "";
      this.exportar_excel({
        "filename":"resumen_compras",
        "title":"Resumen de compras",
        "date":fecha,
        "data":array,
        "header":header,
      });
    },

    addAll: function(results) {
      var self = this;
      self.$("#compras_resumen_tabla tbody").empty();
      self.total_neto = 0;
      self.total_iva = 0;
      self.total_reg_especiales = 0;
      self.total = 0;
      for(var i=0;i<results.length;i++) {
        var o = new app.models.CompraResumen(results[i]);
        self.addOne(o,1);
      }
      self.$("#compras_resumen_total_neto").html("$ "+Number(self.total_neto).format(2));
      self.$("#compras_resumen_total_iva").html("$ "+Number(self.total_iva).format(2));
      self.$("#compras_resumen_total_reg_especiales").html("$ "+Number(self.total_reg_especiales).format(2));
      self.$("#compras_resumen_total").html("$ "+Number(self.total).format(2));
    },

    addOne : function (item,nivel) {
      var view = new app.views.ComprasResumenItemResultados({
        model: item,
        nivel: nivel,
      });
      this.$("#compras_resumen_tabla tbody").append(view.render().el);

      var children = item.get("children");
      if (children.length > 0) {
        for(var i=0;i<children.length;i++) {
          var r = children[i];
          var resumen = new app.models.CompraResumen(r);
          var nombre = resumen.get("nombre");
          resumen.set({ "nombre": nombre });
          var proximo_nivel = nivel + 1;
          this.addOne(resumen,proximo_nivel);
        }
      }

      if (item.get("id_padre") == 0) {
        this.total_neto = this.total_neto + Number(item.get("neto"));
        this.total_iva = this.total_iva + Number(item.get("iva"));
        this.total_reg_especiales = this.total_reg_especiales + Number(item.get("reg_especiales"));
        this.total = this.total + Number(item.get("total")) + Number(item.get("reg_especiales"));
      }
    },

    expand: function() {
      if (this.expanded == 0) {
        this.$("#compras_resumen_tabla tbody tr.child").show();
        this.$(".icon").removeClass("fa-plus");
        this.$(".icon").addClass("fa-minus");
        this.expanded = 1;
      } else {
        this.$("#compras_resumen_tabla tbody tr.child").hide();
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
  app.views.ComprasResumenItemResultados = Backbone.View.extend({
    template: _.template($("#compras_resumen_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click td":"expand",
      "click .ver_listado":function() {
        var mes = $("#compras_resumen_mes").val();
        var anio = $("#compras_resumen_anio").val();
        var desde = $("#compras_resumen_fecha_desde").val().replace(/\//g,"-");
        var hasta = $("#compras_resumen_fecha_hasta").val().replace(/\//g,"-");
        if (!isEmpty(desde)) mes = desde;
        if (!isEmpty(hasta)) anio = hasta;
        var url = "app/#compras_listado/"+mes+"/"+anio+"/"+this.model.id;
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