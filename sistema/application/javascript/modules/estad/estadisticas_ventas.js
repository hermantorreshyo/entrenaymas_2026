(function ( models ) {

  models.EstadisticasVentas = Backbone.Model.extend({
    url: "estadisticas",
    defaults: {
      id_empresa: ID_EMPRESA,
      fecha_desde: moment().format("DD/MM/YYYY"),
      fecha_hasta: moment().format("DD/MM/YYYY"),
      id_punto_venta: -1,
      id_sucursal: 0,
      total_ventas: 0,
      total_clientes: 0,
      cantidad_operaciones: 0,
      venta_promedio: 0,
      venta_promedio_por_dia: 0,
      productos_mas_vendidos: [],
      productos_mayor_ganancia: [],
      efectivo: 0,
      tarjetas: 0,
      cuenta_corriente: 0,
      costo_mercaderia_vendida: 0,
      ganancia_bruta: 0,
      marcacion_promedio: 0,
      total_descuentos: 0,
      total_ofertas: 0,
      reparto: "",
    },
  });
	    
})( app.models );

(function ( app ) {

  app.views.EstadisticasVentasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.parametro = "T"; //this.$("#estadisticas_ventas_parametro").val();

      // Llenamos con los IDs de los filtros que corresponden
      /*
      var array = ["rubros","articulos","vendedores","clientes","proveedores"];
      for(var i=0;i<array.length;i++) {
          var o = array[i];
          params[o] = new Array();
          if (this.$("#estadisticas_ventas_"+o).length == 0) continue;
          this.$("#estadisticas_ventas_"+o+"_opciones span").each(function(ii,ee){
              params[o].push({
                  "id":$(ee).data("id"),
                  "label":$(ee).data("label"),
              });
          });
      }
      */

      params.desde = self.$("#estadisticas_ventas_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_fecha_hasta").val();
      if (self.$("#estadisticas_ventas_puntos_venta").length > 0) { 
        params.id_punto_venta = self.$("#estadisticas_ventas_puntos_venta").val();
      }
      params.id_sucursal = (self.$("#estadisticas_ventas_sucursales").length > 0) ? self.$("#estadisticas_ventas_sucursales").val() : ID_SUCURSAL;
      params.id_proyecto = ID_PROYECTO;
      params.id_usuario = ((SOLO_USUARIO == 1)?ID_USUARIO:0);

      if (self.$("#estadisticas_ventas_reparto").length > 0) { 
        params.reparto = self.$("#estadisticas_ventas_reparto").val();
      }

      if (ID_EMPRESA == 224 && ID_SUCURSAL == 0 && params.id_sucursal == 0) {
        params.in_sucursales = "4-26-9-6-36";
      }

      $.ajax({
        "url":"estadisticas/function/ventas_2/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){
          self.model = new app.models.EstadisticasVentas(r);
          self.render();

          // Renderizamos el grafico de barras
          $("#estadisticas_ventas_graficos").empty();
          for(var i=0;i<r.grafico.length;i++) {
            var result = r.grafico[i];
            var grafico = new app.views.EstadisticasVentasGraficoView(result);
            $("#estadisticas_ventas_graficos").html(grafico.el);
          }

          // Renderizamos el grafico de tortas
          self.$('#dispositivos_bar').highcharts({
            chart: {
              plotBackgroundColor: null,
              plotShadow: false
            },
            title: { text: null },
            tooltip: {
              pointFormat: '<b>{point.percentage:.1f}%</b>'
            },
            colors: ['#28b492', '#19a9d5', '#fad733'],
            plotOptions: {
              pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: { enabled: false }
              }
            },
            series: [{
              type: 'pie',
              data: [
                ['Efectivo', self.model.get("efectivo")],
                ['Tarjetas', self.model.get("tarjetas")],
                ['Cuenta corriente', self.model.get("cuenta_corriente")],
              ]
            }]
          });

        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var fecha_desde = this.model.get("fecha_desde");
      createdatepicker($(this.el).find("#estadisticas_ventas_fecha_desde"),fecha_desde);
      var fecha_hasta = this.model.get("fecha_hasta");
      createdatepicker($(this.el).find("#estadisticas_ventas_fecha_hasta"),fecha_hasta);
    },

    imprimir: function() {
      var desde = moment(this.$("#estadisticas_ventas_fecha_desde").val(),"DD/MM/YYYY");
      var hasta = moment(this.$("#estadisticas_ventas_fecha_hasta").val(),"DD/MM/YYYY");
      if (IDIOMA == "en") {
        this.$(".printer-title").html("Sales");
        this.$(".printer-subtitle").html("Report from: "+desde.format("MM-DD-YYYY")+" to: "+hasta.format("MM-DD-YYYY"));
      } else {
        this.$(".printer-title").html("Estadisticas de ventas");
        this.$(".printer-subtitle").html("Reporte desde: "+desde.format("DD/MM/YYYY")+" hasta: "+hasta.format("DD/MM/YYYY"));
      }
      window.print();
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasGraficoView = app.mixins.View.extend({

      template: _.template($("#estadisticas_ventas_graficos_template").html()),

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


/*

(function ( app ) {

  app.views.EstadisticasVentasView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",

      // Agrega un rango de fechas
      "click .agregar_fecha":function() {
        var fechas = new app.views.EstadisticasVentasFechasView({
          "numero":this.$(".fechas").length+1,
        });
        this.$("#estadisticas_ventas_fechas_opciones").append(fechas.el);
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
      "change #estadisticas_ventas_rubros":function(e) {
        var iden = "rubro_";
        var id = this.$("#estadisticas_ventas_rubros").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_ventas_rubros option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs cp fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_ventas_rubros_opciones").append(h);
          this.render_check_comparar("estadisticas_ventas_rubros");
        }
      },

      // Filtro por articulos
      "change #estadisticas_ventas_articulos":function(e) {
        var iden = "articulo_";
        var id = this.$("#estadisticas_ventas_articulos").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_ventas_articulos option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_ventas_articulos_opciones").append(h);    
          this.render_check_comparar("estadisticas_ventas_articulos");
        }
      },

      // Filtro por clientes
      "change #estadisticas_ventas_clientes":function(e) {
        var iden = "cliente_";
        var id = this.$("#estadisticas_ventas_clientes").val();
        if (id == 0) return;
        var etiqueta = this.$("#estadisticas_ventas_clientes option:selected").text();
        var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
        if ($("#"+iden+id).length == 0) {
          this.$("#estadisticas_ventas_clientes_opciones").append(h);    
          this.render_check_comparar("estadisticas_ventas_clientes");
        }
      },

            // Filtro por proveedores
            "change #estadisticas_ventas_proveedores":function(e) {
                var iden = "cliente_";
                var id = this.$("#estadisticas_ventas_proveedores").val();
                if (id == 0) return;
                var etiqueta = this.$("#estadisticas_ventas_proveedores option:selected").text();
                var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
                if ($("#"+iden+id).length == 0) {
                    this.$("#estadisticas_ventas_proveedores_opciones").append(h);    
                    this.render_check_comparar("estadisticas_ventas_proveedores");
                }
            },

            // Filtro por vendedores
            "change #estadisticas_ventas_vendedores":function(e) {
                var iden = "vendedor_";
                var id = this.$("#estadisticas_ventas_vendedores").val();
                if (id == 0) return;
                var etiqueta = this.$("#estadisticas_ventas_vendedores option:selected").text();
                var h = '<span id="'+iden+id+'" data-label="'+etiqueta+'" data-id="'+id+'" class="label bg-light dk m-r-sm">'+etiqueta+'<i class="borrar_opcion m-l-xs fa fa-times"></i></span>';
                if ($("#"+iden+id).length == 0) {
                    this.$("#estadisticas_ventas_vendedores_opciones").append(h);    
                    this.render_check_comparar("estadisticas_ventas_vendedores");
                }
            },

        },
        
        initialize: function(options) {
            _.bindAll(this);
            var self = this;
            this.options = options;
            $(this.el).html(this.template(this.model.toJSON()));

            new app.mixins.Select({
                modelClass: app.models.Articulo,
                url: "articulos/",
                render: "#estadisticas_ventas_articulos",
                firstOptions: ["<option value='0'>Articulos</option>"],
                onComplete:function(c) {
                    $("#estadisticas_ventas_articulos").select2({});
                },
            });

            new app.mixins.Select({
                modelClass: app.models.Rubro,
                url: "rubros/",
                render: "#estadisticas_ventas_rubros",
                firstOptions: ["<option value='0'>Rubros</option>"],
                onComplete:function(c) {
                    $("#estadisticas_ventas_rubros").select2({});
                },
            });

            new app.mixins.Select({
                modelClass: app.models.Cliente,
                url: "clientes/",
                render: "#estadisticas_ventas_clientes",
                firstOptions: ["<option value='0'>Clientes</option>"],
                onComplete:function(c) {
                    $("#estadisticas_ventas_clientes").select2({});
                },
            });

            if (control.check("proveedores")>0) {
                new app.mixins.Select({
                    modelClass: app.models.Proveedor,
                    url: "proveedores/",
                    render: "#estadisticas_ventas_proveedores",
                    firstOptions: ["<option value='0'>Proveedores</option>"],
                    onComplete:function(c) {
                        $("#estadisticas_ventas_proveedores").select2({});
                    },
                });
            }

            if (control.check("vendedores")>0) {
                new app.mixins.Select({
                    modelClass: app.models.Vendedor,
                    url: "vendedores/",
                    render: "#estadisticas_ventas_vendedores",
                    firstOptions: ["<option value='0'>Vendedores</option>"],
                    onComplete:function(c) {
                        $("#estadisticas_ventas_vendedores").select2({});
                    },
                });
            }

            var fechas = new app.views.EstadisticasVentasFechasView({
                "numero":1,
            });
            this.$("#estadisticas_ventas_fecha_inicial").html(fechas.el);

            this.model.fetch({
                "success":function(){ self.render() }
            });
        },

        buscar: function() {

            var self = this;
            var params = {};
            params.parametro = this.$("#estadisticas_ventas_parametro").val();
            params.intervalo = this.$("#estadisticas_ventas_intervalo").val();
            params.comparar = this.$(".comparar:checked").val();
            if (params.comparar == undefined) params.comparar = "";

            // Llenamos con los IDs de los filtros que corresponden
            var array = ["rubros","articulos","vendedores","clientes","proveedores"];
            for(var i=0;i<array.length;i++) {
                var o = array[i];
                params[o] = new Array();
                if (this.$("#estadisticas_ventas_"+o).length == 0) continue;
                this.$("#estadisticas_ventas_"+o+"_opciones span").each(function(ii,ee){
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
                "url":"estadisticas/function/ventas/",
                "dataType":"json",
                "data":params,
                "type":"post",
                "success":function(r){
                    console.log(r);
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
          $("#estadisticas_ventas_graficos").empty();
          $("#estadisticas_ventas_listados").empty();
          for(var i=0;i<r.results.length;i++) {
              var result = r.results[i];
              var grafico = new app.views.EstadisticasVentasGraficoView(result);
              $("#estadisticas_ventas_graficos").html(grafico.el);

              // Creamos la tabla
              var t = "<table>";
              t+="</table>";
              t+="<tbody>";
              var total = 0; var total_costo = 0;
              for(var j=0;j<result.series[0].data.length;j++) {
                var o = result.series[0].data[j];
                var c = result.series[1].data[j];
                total += o;
                total_costo += c;
              }
              t+="<tfoot>";
              t+="</tfoot>";
              t+="</table>";
              //$("#estadisticas_ventas_listados").append(t);
              total = Number(total).toFixed(2);
              total_costo = Number(total_costo).toFixed(2);
              porc_marc = (total_costo == 0) ? Number(0).toFixed(2) : Number(((total / total_costo) - 1) * 100).toFixed(2);
              $("#estadisticas_ventas_total").text("$ "+total);
              $("#estadisticas_ventas_total_costo").text("$ "+total_costo);
              $("#estadisticas_ventas_ganancia").text("$ "+Number(total - total_costo).toFixed(2));
              $("#estadisticas_ventas_porc_marc_promedio").text(porc_marc+" %");
          }
        },
        
    });
})(app);



(function ( app ) {

    app.views.EstadisticasVentasFechasView = app.mixins.View.extend({

        template: _.template($("#estadisticas_ventas_fechas_template").html()),

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
*/