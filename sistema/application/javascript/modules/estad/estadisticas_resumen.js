(function ( app ) {

  app.views.EstadisticasResumenView = app.mixins.View.extend({

    template: _.template($("#estadisticas_resumen_template").html()),

    myEvents: {
      "click .buscar":"buscar",
    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      $(this.el).html(this.template(this.model.toJSON()));

      var fecha_desde = moment().startOf("month").subtract(1,'year').format("DD/MM/YYYY");
      createdatepicker($(this.el).find("#estadisticas_resumen_fecha_desde"),fecha_desde);
      var fecha_hasta = moment().endOf("month").format("DD/MM/YYYY");
      createdatepicker($(this.el).find("#estadisticas_resumen_fecha_hasta"),fecha_hasta);

      this.buscar();
    },

    buscar: function() {

      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_resumen_fecha_desde").val(),
      params.hasta = self.$("#estadisticas_resumen_fecha_hasta").val(),
      $.ajax({
        "url":"estadisticas/function/resumen/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.render_graph(r);
          self.render_table(r);
        },
      });
    },

    render_table: function(r) {
      var meses = r.meses;
      var tr = "";
      tr+="<tr><th style='min-width:150px'>Concepto</th>";
      for(var i=0;i<meses.length;i++) {
        var mes = meses[i];
        tr+="<th style='min-width:100px' class='tac' colspan='2'>"+mes+"</th>";
      }
      tr+="</tr>";
      $("#estadisticas_resumen_tabla thead").html(tr);

      tr = "";
      tr+="<tr><td>Stock Inicial</td>";
      var stock_inicial = r.series[5].data;
      for(var i=0;i<stock_inicial.length;i++) {
        var s_in = stock_inicial[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(s_in).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Ventas</td>";
      var ventas = r.series[0].data;
      for(var i=0;i<ventas.length;i++) {
        var venta = ventas[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(venta).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Compras</td>";
      var compras = r.series[1].data;
      for(var i=0;i<compras.length;i++) {
        var compra = compras[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(compra).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'>";
        if (ID_EMPRESA == 134) { 
          var a = this.translate_date(meses[i]);
          tr+="<a href='app/#compras_listado/"+a[0]+"/"+a[1]+"/30' target='_blank'><i class='fa fa-search m-l-xs'></i></a>";
        }
        tr+="</td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Gastos</td>";
      var gastos = r.series[2].data;
      for(var i=0;i<gastos.length;i++) {
        var gasto = gastos[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(gasto).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'>";
        if (ID_EMPRESA == 134) { 
          var a = this.translate_date(meses[i]);
          tr+="<a href='app/#compras_listado/"+a[0]+"/"+a[1]+"/37' target='_blank'><i class='fa fa-search m-l-xs'></i></a>";
        }
        tr+="</td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Pagos Efectivo</td>";
      var pagos_efec = r.series[3].data;
      for(var i=0;i<pagos_efec.length;i++) {
        var pago_efec = pagos_efec[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(pago_efec).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Pagos Cheques</td>";
      var pagos_ch = r.series[4].data;
      for(var i=0;i<pagos_ch.length;i++) {
        var pago_ch = pagos_ch[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(pago_ch).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      tr+="<tr><td>Stock Final</td>";
      var stock_final = r.series[6].data;
      for(var i=0;i<stock_final.length;i++) {
        var s_fin = stock_final[i];
        tr+="<td class='tar pr0'>";
        tr+=Number(s_fin).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      tr+="<tr><td class='bold'>Ganancia Bruta</td>";
      for(var i=0;i<meses.length;i++) {
        var venta = ventas[i];
        var compra = compras[i];
        var gasto = gastos[i];
        tr+="<td class='tar pr0 bold'>";
        tr+=Number(venta - compra - gasto).toFixed(2);
        tr+="</td>";
        tr+="<td class='pl0 pr0'></td>";
      }
      tr+="</tr>";

      /*
      var acumulado = 0;
      tr+="<tr><td>Acumulado</td>";
      for(var i=0;i<meses.length;i++) {
        var venta = ventas[i];
        var compra = compras[i];
        var gasto = gastos[i];
        acumulado += (venta - compra - gasto);
        tr+="<td class='tar'>";
        tr+=Number(acumulado).toFixed(2);
        tr+="</td>";
      }
      tr+="</tr>";
      */

      $("#estadisticas_resumen_tabla tbody").html(tr);
    },

    translate_date: function(d) {
      var a = d.split(" ");
      var mes = a[0];
      if (mes == "Ene") mes = "01";
      else if (mes == "Feb") mes = "02";
      else if (mes == "Mar") mes = "03";
      else if (mes == "Abr") mes = "04";
      else if (mes == "May") mes = "05";
      else if (mes == "Jun") mes = "06";
      else if (mes == "Jul") mes = "07";
      else if (mes == "Ago") mes = "08";
      else if (mes == "Sep") mes = "09";
      else if (mes == "Oct") mes = "10";
      else if (mes == "Nov") mes = "11";
      else if (mes == "Dic") mes = "12";
      var anio = "20"+a[1];
      return [mes,anio];
    },

    render_graph: function(r) {
      var array = new Array();
      array.push(r.series[0]);
      array.push(r.series[1]);
      array.push(r.series[2]);

      // VISION GENERAL
      this.$('.grafico').highcharts({
        chart: {
          type: 'column',
          zoomType: 'x'
        },
        title: { text: null },
        legend: {
          floating: true,
          align: "right",
          verticalAlign: "top",
        },
        colors: ['#28b492','#e06559','#31708f'],
        xAxis: {
          categories: r.meses
        },
        yAxis: {
          allowDecimals: false,
          gridLineColor: '#f9f9f9',
          title: {
            text: null
          }
        },
        plotOptions: {
          area: {
            marker: {
              enabled: false,
              symbol: 'circle',
              radius: 2,
              states: {
                hover: { enabled: true }
              }
            }
          },
        },
        series: array
      }); 
    },

  });
})(app);