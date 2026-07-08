(function ( app ) {

  app.views.EstadisticasComprasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_compras_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",

      // Agrega un rango de fechas
      "click .agregar_fecha":function() {
        var fechas = new app.views.EstadisticasComprasFechasView({
          "numero":this.$(".fechas").length+1,
        });
        this.$("#estadisticas_compras_fechas_opciones").append(fechas.el);
      },

      // Borra una opcion de algun filtro
      "click .borrar_opcion":function(e) {
        var id = $(e.currentTarget).parent().parent().attr("id");
        id = id.replace("_opciones","");
        $(e.currentTarget).parent().remove();
        this.render_check_comparar(id);
      },

      // Cuando compara un elemento, tiene que desactivar todas las otras comparaciones
      "click .comparar":function(e) {
        $(".comparar").not(e.currentTarget).prop("checked",false);
      },

      // Filtro por rubros
      "change #estadisticas_compras_rubros":function(e) {
        var iden = "rubro_";
        var id = this.$("#estadisticas_compras_rubros").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_compras_rubros option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs cp fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_compras_rubros_opciones").append(h);
          this.render_check_comparar("estadisticas_compras_rubros");
        }
      },

      // Filtro por articulos
      "change #estadisticas_compras_articulos":function(e) {
        var iden = "articulo_";
        var id = this.$("#estadisticas_compras_articulos").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_compras_articulos option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_compras_articulos_opciones").append(h);    
          this.render_check_comparar("estadisticas_compras_articulos");
        }
      },

      // Filtro por clientes
      "change #estadisticas_compras_clientes":function(e) {
        var iden = "cliente_";
        var id = this.$("#estadisticas_compras_clientes").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_compras_clientes option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_compras_clientes_opciones").append(h);    
          this.render_check_comparar("estadisticas_compras_clientes");
        }
      },

      // Filtro por proveedores
      "change #estadisticas_compras_proveedores":function(e) {
        var iden = "cliente_";
        var id = this.$("#estadisticas_compras_proveedores").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_compras_proveedores option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_compras_proveedores_opciones").append(h);    
          this.render_check_comparar("estadisticas_compras_proveedores");
        }
      },

      // Filtro por vendedores
      "change #estadisticas_compras_vendedores":function(e) {
        var iden = "vendedor_";
        var id = this.$("#estadisticas_compras_vendedores").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_compras_vendedores option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_compras_vendedores_opciones").append(h);    
          this.render_check_comparar("estadisticas_compras_vendedores");
        }
      },

    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      this.tipo_proveedor = (typeof options.tipo_proveedor != "undefined") ? options.tipo_proveedor : 0;
      this.id_concepto_padre = (typeof options.id_concepto_padre != "undefined") ? options.id_concepto_padre : 0;

      $(this.el).html(this.template({
        "tipo_proveedor":self.tipo_proveedor,
        "fecha_desde":moment().subtract(1,'months').format("DD/MM/YYYY"),
        "fecha_hasta": moment().format("DD/MM/YYYY"),
      }));

      /*
      new app.mixins.Select({
          modelClass: app.models.Articulo,
          url: "articulos/",
          render: "#estadisticas_compras_articulos",
          firstOptions: ["<option value='0'>Articulos</option>"],
          onComplete:function(c) {
              $("#estadisticas_compras_articulos").select2({});
          },
      });

      new app.mixins.Select({
          modelClass: app.models.Rubro,
          url: "rubros/",
          render: "#estadisticas_compras_rubros",
          firstOptions: ["<option value='0'>Rubros</option>"],
          onComplete:function(c) {
              $("#estadisticas_compras_rubros").select2({});
          },
      });

      new app.mixins.Select({
          modelClass: app.models.Cliente,
          url: "clientes/",
          render: "#estadisticas_compras_clientes",
          firstOptions: ["<option value='0'>Clientes</option>"],
          onComplete:function(c) {
              $("#estadisticas_compras_clientes").select2({});
          },
      });

      if (control.check("proveedores")>0) {
          new app.mixins.Select({
              modelClass: app.models.Proveedor,
              url: "proveedores/",
              render: "#estadisticas_compras_proveedores",
              firstOptions: ["<option value='0'>Proveedores</option>"],
              onComplete:function(c) {
                  $("#estadisticas_compras_proveedores").select2({});
              },
          });
      }

      if (control.check("vendedores")>0) {
          new app.mixins.Select({
              modelClass: app.models.Vendedor,
              url: "vendedores/",
              render: "#estadisticas_compras_vendedores",
              firstOptions: ["<option value='0'>Vendedores</option>"],
              onComplete:function(c) {
                  $("#estadisticas_compras_vendedores").select2({});
              },
          });
      }
      */

      var fechas = new app.views.EstadisticasComprasFechasView({
        "numero":1,
      });
      this.$("#estadisticas_compras_fecha_inicial").html(fechas.el);

      /*
      this.model.fetch({
          "success":function(){ self.render() }
      });
      */
    },

    buscar: function() {

      var self = this;
      var params = {};
      params.parametro = this.$("#estadisticas_compras_parametro").val();
      params.intervalo = this.$("#estadisticas_compras_intervalo").val();
      params.comparar = this.$(".comparar:checked").val();
      params.tipo_proveedor = this.tipo_proveedor;
      params.id_concepto_padre = this.id_concepto_padre;
      if (params.comparar == undefined) params.comparar = "";

      // Llenamos con los IDs de los filtros que corresponden
      var array = ["rubros","articulos","vendedores","clientes","proveedores"];
      for(var i=0;i<array.length;i++) {
        var o = array[i];
        params[o] = new Array();
        if (this.$("#estadisticas_compras_"+o).length == 0) continue;
        this.$("#estadisticas_compras_"+o+"_opciones span").each(function(ii,ee){
          params[o].push({
            "id":$(ee).data("id"),
            "label":$(ee).data("label"),
          });
        });
      }

      // Tomamos los periodos
      params.fechas = new Array();
      this.$(".fechas").each(function(ii,ee){
        params.fechas.push({
          "desde":$(ee).find(".fecha_desde").val(),
          "hasta":$(ee).find(".fecha_hasta").val(),
        });
      });

      $.ajax({
        "url":"estadisticas/function/compras/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.render_graph(r);
        },
      });
      
    },

    render_check_comparar: function(id) {
      if (this.$("#"+id+"_opciones span").length > 1) {
        this.$("#"+id+"_comparar").show();
      } else {
        this.$("#"+id+"_comparar").hide();
      }
    },

    render_graph: function(r) {
      $("#estadisticas_compras_graficos").empty();
      $("#estadisticas_compras_listados").empty();
      for(var i=0;i<r.results.length;i++) {
        var result = r.results[i];
        var grafico = new app.views.EstadisticasComprasGraficoView(result);
        $("#estadisticas_compras_graficos").html(grafico.el);

        // Creamos la tabla
        var t = "<table>";
        t+="</table>";
        t+="<tbody>";
        var total = 0;
        for(var j=0;j<result.series[0].data.length;j++) {
          var o = result.series[0].data[j];
          total += o;
        }
        t+="<tfoot>";
        t+="</tfoot>";
        t+="</table>";
        //$("#estadisticas_compras_listados").append(t);
        $("#estadisticas_compras_total").text("$ "+Number(total).toFixed(2));
      }
    },

    imprimir: function() {
      var self = this;
      var pagina = $("#estadisticas_compras_container");
      workspace.createPDF([pagina],{
        "titulo":"Estadistica de "+((self.tipo_proveedor == "C")?"compras":"gastos"),
      });
    },


  });
})(app);



(function ( app ) {

  app.views.EstadisticasComprasFechasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_compras_fechas_template").html()),

    myEvents:{
      "click .eliminar_fecha":function(){
        this.$(".fechas").parent().remove();
      },
    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      $(this.el).html(this.template(this.options));

      var fecha_desde = (typeof this.options.fecha_desde != "undefined") ? this.options.fecha_desde : moment().subtract(1,'months').format("DD/MM/YYYY");
      createdatepicker($(this.el).find(".fecha_desde"),fecha_desde);

      var fecha_hasta = (typeof this.options.fecha_hasta != "undefined") ? this.options.fecha_hasta : moment().format("DD/MM/YYYY");
      createdatepicker($(this.el).find(".fecha_hasta"),fecha_hasta);
    },

  });

})(app);





(function ( app ) {

  app.views.EstadisticasComprasGraficoView = app.mixins.View.extend({

    template: _.template($("#estadisticas_compras_graficos_template").html()),

    myEvents:{
    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      $(this.el).html(this.template(this.options));

      var desde_anio = this.options.desde.substr(6);
      var desde_mes = this.options.desde.substr(3,2)-1;
      var desde_dia = this.options.desde.substr(0,2);

      if (this.options.intervalo == "W") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointInterval: 24 * 3600 * 1000 * 7,
        };
      } else if (this.options.intervalo == "D") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointInterval: 24 * 3600 * 1000,
        };
      } else if (this.options.intervalo == "M") {
        var plotOptionsSeries = {
          pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
          pointIntervalUnit: 'month',
        };
      }

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
        colors: ['#28b492','#19a9d5'],
        xAxis: {
          type: 'datetime',
              /*dateTimeLabelFormats: {
                  day: '%b %e',
                  week: '%b %e'
                } */           
              },
              yAxis: {
                allowDecimals: false,
                gridLineColor: '#f9f9f9',
                title: {
                  text: null
                }
              },
              tooltip: {
                dateTimeLabelFormats: {
                  day: '%e/%m/%Y',
                  week: '%e/%m/%Y',
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
                series: plotOptionsSeries
              },
              series: self.options.series
            });   
    },

  });

})(app);