(function ( models ) {

  models.Inicio = Backbone.Model.extend({
    urlRoot: 'app/',
  });
	  
})( app.models );


(function ( app ) {

app.views.Inicio = Backbone.View.extend({

  template: _.template($("#inicio_template").html()),
  
  events: {
    "click #dashboard_buscar_button":"get_data",
  },
  
  initialize: function() {
    $(this.el).html(this.template());
    this.render();
  },

  render: function() {
		var self = this;
    var anterior = new Date();
    anterior.setDate(anterior.getDate()-30); 
    createdatepicker($(this.el).find("#dashboard_fecha_desde"),anterior);
    createdatepicker($(this.el).find("#dashboard_fecha_hasta"),new Date());
    this.get_data();
    return this;
  },
  
  get_data: function() {
    var self = this;
    var desde = $(this.el).find("#dashboard_fecha_desde").val();
    var hasta = $(this.el).find("#dashboard_fecha_hasta").val();
    if (isEmpty(desde)) {
      show("Por favor ingrese una fecha.");
      $(this.el).find("#dashboard_fecha_desde").focus();
      return;
    }
    if (isEmpty(hasta)) {
      show("Por favor ingrese una fecha.");
      $(this.el).find("#dashboard_fecha_hasta").focus();
      return;
    }
    desde = desde.replace(/\//g,"-");
    hasta = hasta.replace(/\//g,"-");
    $.ajax({
      "url":"/sistema/app/get_info_dashboard/",
      "dataType":"json",
      "type":"post",
      "data":{
        "desde":desde, "hasta":hasta,
      },
      "success":function(r) {
        
        // GRAFICOS
        self.render_grafico_facturacion(desde,r.facturacion)
        
        // CANTIDADES
        $("#dashboard_cantidad_clientes").html(Number(r.cantidad_clientes).toFixed(0));
        $("#dashboard_cantidad_productos").html(Number(r.cantidad_articulos).toFixed(0));
        
        // ACTIVIDADES RECIENTES
        $(self.el).find(".streamline").empty();
        for(var i=0;i<r.actividades.length;i++) {
          var o = r.actividades[i];
          var clase = "";
          if (o.importancia == 'D') clase = "b-danger";
          else if (o.importancia == 'W') clase = "b-warning";
          else if (o.importancia == 'I') clase = "b-info";
          else if (o.importancia == 'S') clase = "b-success";
          else if (o.importancia == 'P') clase = "b-primary";
          var tr = '<div class="sl-item b-l '+clase+'">';
          tr+= '<div class="m-l">';
          tr+= '<div class="text-muted">'+o.fecha_f+'</div>';
          tr+= '<p>';
          if (!isEmpty(o.nombre_usuario)) tr+= '<span class="text-info">'+o.nombre_usuario+'</span> ';
          tr+= o.texto;
          if (!isEmpty(o.link)) tr+=' <a class="text-info" href="'+o.link+'">Ver</a>';
          tr+='</p>';
          tr+= '</div>';
          tr+= '</div>';
          $(self.el).find(".streamline").append(tr);
        }
        
        // ULTIMOS COMPROBANTES
        $(self.el).find("#dashboard_ultimos_comprobantes").empty();
        for(var i=0;i<r.ultimos_comprobantes.length;i++) {
          var o = r.ultimos_comprobantes[i];
          var tr = '<li class="list-group-item with-thumb">';
          tr+='<span class="thumb-letter bg-info">'+o.letra+'</span>';
          tr+='<a href="app/#facturacion/'+o.id+'">'+o.cliente+'<br/>'+o.comprobante+' ('+o.fecha+')'+'</a>';
          tr+='</li>';
          $(self.el).find("#dashboard_ultimos_comprobantes").append(tr);
        }
      }
    });
  },
  
  render_grafico_facturacion: function(fecha_desde,a) {
    
    var anio = parseInt(fecha_desde.substr(6,4));
    var mes = parseInt(fecha_desde.substr(3,2));
    var dia = parseInt(fecha_desde.substr(0,2));
    var datos = new Array();
    var datos_cantidad = new Array();
    var total_cantidad = 0;
    var total = 0;
    for(var i=0; i<a.length;i++) {
      var e = a[i];
      var t = parseFloat(e.total);
      datos.push(t);
      datos_cantidad.push(parseInt(e.cantidad));
      total_cantidad = total_cantidad + parseInt(e.cantidad);
      total = total + t;
    }
    
    $("#dashboard_total_ventas").html(Number(total).toFixed(2));
    $("#dashboard_cantidad_facturas").html(Number(total_cantidad).toFixed(0));
    
    $('#facturacion_bar').highcharts({
      chart: {
        type: 'area',
        zoomType: 'x'
      },
      title: { text: null },
      legend: {
        floating: true,
        align: "right",
        verticalAlign: "top",
      },
      colors: ['#7266ba','#23b7e5'],
      xAxis: {
        type: 'datetime',
        dateTimeLabelFormats: {
          day: '%b %e',
          week: '%b %e'
        }      
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
        series: {
          pointStart: Date.UTC(anio, mes, dia),
          pointInterval: 24 * 3600 * 1000,
        }
      },
      series: [{
        name: 'Venta',
        data: datos,
      }]
    });
    /*
    $('#facturacion_cantidades_bar').highcharts({
      chart: {
        type: 'column',
      },
      title: { text: null },
      legend: {
        floating: true,
        align: "right",
        verticalAlign: "top",
      },
      colors: ['#7266ba','#23b7e5'],
      xAxis: {
        type: 'datetime',
        dateTimeLabelFormats: {
          day: '%b %e',
          week: '%b %e'
        }      
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
        series: {
          pointStart: Date.UTC(anio, mes, dia),
          pointInterval: 24 * 3600 * 1000,
        }
      },
      series: [{
        name: 'Cantidad',
        data: datos_cantidad,
      }]
    });
    */
    
  },
  
});

})(app);






(function ( app ) {

app.views.InicioShopvar = app.mixins.View.extend({

  template: _.template($("#shopvar_dashboard_template").html()),
  
  myEvents: {

    "click #sugerencia-llamar-atencion-cliente":function(){
      window.open("https://api.whatsapp.com/send?phone=5492215021999","_blank");
    },

    "click #sugerencia-llamar-soporte-tecnico":function(){
      window.open("https://api.whatsapp.com/send?phone=5492352444378","_blank");
    },

    "click #sugerencia-configurar-web":function() {
      location.href="app/#web_configuracion";
    },

    "click #sugerencia-primer-articulo":function() {
      location.href="app/#articulo";
    },

    "click #sugerencia-forma-pago":function() {
      location.href="app/#medios_pago_configuracion";
    },    

    "click #sugerencia-metodo-envio":function() {
      location.href="app/#formas_envio_configuracion";
    },    
  },
  
  save_property: function(attribute,value,callback) {
    $.ajax({
      "url":"/sistema/web_configuracion/function/save_attribute/",
      "dataType":"json",
      "type":"post",
      "data":{
        "attribute":attribute,
        "value":value,
      },
      "success":callback,
    });
  },
  
  initialize: function() {
    
    $(this.el).html(this.template(this.model.toJSON()));
    
    // Agregamos el form de ayuda
		var ayuda = new app.views.AyudaFormView({
			model: new Backbone.Model.extend(),
		});
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);


// PLANES DE PROYECTOS
(function ( app ) {
  app.views.Precios = app.mixins.View.extend({
    template: _.template($("#precios_template").html()),
    myEvents:{
      "click .contratar_plan":function(e){
        // NOTA:
        // Si la fecha de ultimo pago esta dentro del mes actual
        // no se vuelve a pagar, sino que cambia directamente el plan solicitado
        // y se va a facturar el nuevo plan el proximo mes
        //if (FECHA_ULTIMO_PAGO != '0000-00-00' && moment().diff(moment(FECHA_ULTIMO_PAGO),'days') < 30) {
          if (!confirm("Confirma cambiar al plan seleccionado? La proxima factura sera generada con el nuevo plan."));
          var id = $(e.currentTarget).data("id");
          $(".contratar_plan").attr("disabled","disabled");
          $.ajax({
            "url":"empresas/function/cambiar_plan/",
            "type":"post",
            "dataType":"json",
            "data":{
              "id_plan":id,
            },
            "success":function(r) {
              $(".contratar_plan").removeAttr("disabled");
              if (r.error == 0) {
                alert(r.mensaje);
                location.reload();
              } else {
                alert(r.mensaje);
              }
            },
            "error":function() {
              $(".contratar_plan").removeAttr("disabled");
            }
          });          
        //} else {
          // En cambio, si la fecha de ultimo pago es mayor a un mes
          // (este caso tambien aplica para cuando no pago nunca, es decir que viene de una cuenta gratuita)
          // Entonces primero mandamos a pagar el plan seleccionado
          // y una vez que paga ahi si se habilita la cuenta y el plan nuevo
        //}
      }
    },
    initialize: function() {
      $(this.el).html(this.template());
      this.render();
    },
    render: function() {
      return this;
    },
  });
})(app);



(function ( app ) {

app.views.InicioNewsvar = Backbone.View.extend({

  template: _.template($("#inforvar_dashboard_template").html()),
  
  events: {
    
    // QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_si":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.href="app/#web_configuracion";
      });
    },
    // NO QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_no":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_configurar_disenio").fadeOut();
      });
    },
    
    // QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_si":function() {
      this.save_property("subir_elemento",1,function(r){
        location.href="app/#entrada";
      });
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_no":function() {
      this.save_property("subir_elemento",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_primer_elemento").fadeOut();
      });
    },
    
    // QUIERE CONFIGURAR LA FORMA DE ENVIO
    "click .conf_envio_si":function() {
      location.href="app/#servicios_envio";
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .conf_envio_no":function() {
      this.save_property("sin_envios",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_conf_envios").fadeOut();
      });
    },
    
    // QUIERE CONFIGURAR LOS METODOS DE PAGO
    "click .conf_pagos_si":function() {
      location.href="app/#medios_pago_configuracion";
    },
    // NO QUIERE METODOS DE PAGO
    "click .conf_pagos_no":function() {
      this.save_property("sin_pagos",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_conf_pagos").fadeOut();
      });
    },  

    // QUIERE AGREGAR ELEMENTOS
    "click .datos_empresa_si":function() {
      this.save_property("datos_empresa",1,function(r){
        location.href="app/#mis_datos";
      });
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .datos_empresa_no":function() {
      this.save_property("datos_empresa",1,function(r){
        location.reload();
      });
    },    

  },
  
  save_property: function(attribute,value,callback) {
    $.ajax({
      "url":"/sistema/web_configuracion/function/save_attribute/",
      "dataType":"json",
      "type":"post",
      "data":{
        "attribute":attribute,
        "value":value,
      },
      "success":callback,
    });
  },
  
  initialize: function() {
    
    $(this.el).html(this.template(this.model.toJSON()));
    
    // Agregamos el form de ayuda
		var ayuda = new app.views.AyudaFormView({
			model: new Backbone.Model.extend(),
		});
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);


(function ( app ) {

app.views.InicioDocvar = Backbone.View.extend({

  template: _.template($("#docvar_dashboard_template").html()),
  
  events: {
    
    // QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_si":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.href="app/#web_configuracion";
      });
    },
    // NO QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_no":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_configurar_disenio").fadeOut();
      });
    },
    
    // QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_si":function() {
      this.save_property("subir_elemento",1,function(r){
        location.href="app/#entrada";
      });
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_no":function() {
      this.save_property("subir_elemento",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_primer_elemento").fadeOut();
      });
    },
    
    // QUIERE CONFIGURAR LA FORMA DE ENVIO
    "click .conf_envio_si":function() {
      location.href="app/#servicios_envio";
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .conf_envio_no":function() {
      this.save_property("sin_envios",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_conf_envios").fadeOut();
      });
    },
    
    // QUIERE CONFIGURAR LOS METODOS DE PAGO
    "click .conf_pagos_si":function() {
      location.href="app/#medios_pago_configuracion";
    },
    // NO QUIERE METODOS DE PAGO
    "click .conf_pagos_no":function() {
      this.save_property("sin_pagos",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_conf_pagos").fadeOut();
      });
    },    
  },
  
  save_property: function(attribute,value,callback) {
    $.ajax({
      "url":"/sistema/web_configuracion/function/save_attribute/",
      "dataType":"json",
      "type":"post",
      "data":{
        "attribute":attribute,
        "value":value,
      },
      "success":callback,
    });
  },
  
  initialize: function() {
    
    $(this.el).html(this.template(this.model.toJSON()));
    
    // Agregamos el form de ayuda
		var ayuda = new app.views.AyudaFormView({
			model: new Backbone.Model.extend(),
		});
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);



(function ( app ) {

app.views.InicioColvar = Backbone.View.extend({

  template: _.template($("#colvar_dashboard_template").html()),
  
  events: {
    
    // QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_si":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.href="app/#web_configuracion";
      });
    },
    // NO QUIERE CONFIGURAR DISEÑO
    "click .conf_disenio_no":function() {
      this.save_property("configurar_disenio",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_configurar_disenio").fadeOut();
      });
    },
    
    // QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_si":function() {
      this.save_property("subir_elemento",1,function(r){
        location.href="app/#entrada";
      });
    },
    // NO QUIERE AGREGAR ELEMENTOS
    "click .subir_elemento_no":function() {
      this.save_property("subir_elemento",1,function(r){
        location.reload();
        //$("#dashboard_shopvar_primer_elemento").fadeOut();
      });
    },    
  },
  
  save_property: function(attribute,value,callback) {
    $.ajax({
      "url":"/sistema/web_configuracion/function/save_attribute/",
      "dataType":"json",
      "type":"post",
      "data":{
        "attribute":attribute,
        "value":value,
      },
      "success":callback,
    });
  },
  
  initialize: function() {
    
    $(this.el).html(this.template(this.model.toJSON()));
    
    // Agregamos el form de ayuda
		var ayuda = new app.views.AyudaFormView({
			model: new Backbone.Model.extend(),
		});
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);




(function ( app ) {

app.views.InicioClienApp = Backbone.View.extend({

  template: _.template($("#clienapp_dashboard_template").html()),
  
  events: {

    "click .copy-to-clipboard":function() {
      var textArea = document.createElement("textarea");
      var texto = this.$(".show-code").text();
      textArea.value = texto;
      document.body.appendChild(textArea);
      textArea.select();
      try {
        var successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        if (successful) alert("El codigo se ha copiado correctamente. Ahora debe pegarlo en su sitio web antes de la etiqeuta </HEAD>.");
      } catch (err) {
        console.log('Oops, unable to copy');
        document.body.removeChild(textArea);
      }
    },

    "click .guardar_dominio": function() {
      var dominio = this.$("#clienapp_dominio").val();
      if (isEmpty(dominio)) {
        alert("Por favor ingrese la direccion de su pagina web");
        this.$("#clienapp_dominio").focus();
        return;
      }
      if (dominio.indexOf(".") == -1) {
        alert("El nombre del sitio no es valido");
        this.$("#clienapp_dominio").focus();
        return;    
      }
      $.ajax({
        "url":"empresas/function/guardar_dominio/",
        "dataType":"json",
        "data":{
          "dominio":dominio
        },
        "type":"post",
        "success":function(r) {
          if (r.error == 1) alert(r.mensaje);
          else location.reload();
        }
      })
    },
  },
  
  save_property: function(attribute,value,callback) {
    $.ajax({
      "url":"/sistema/web_configuracion/function/save_attribute/",
      "dataType":"json",
      "type":"post",
      "data":{
        "attribute":attribute,
        "value":value,
      },
      "success":callback,
    });
  },
  
  initialize: function() {
    
    $(this.el).html(this.template(this.model.toJSON()));
    
    // Agregamos el form de ayuda
    var ayuda = new app.views.AyudaFormView({
      model: new Backbone.Model.extend(),
    });
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);



(function ( app ) {

app.views.InicioTripvar = Backbone.View.extend({
  template: _.template($("#tripvar_dashboard_template").html()),
  initialize: function() {
    $(this.el).html(this.template(this.model.toJSON()));
    var ayuda = new app.views.AyudaFormView({
      model: new Backbone.Model.extend(),
    });
    this.$("#dashboard_ayuda").append(ayuda.render().el);    
  },
  
});

})(app);



(function ( app ) {

app.views.ToqueDashboard = app.mixins.View.extend({
  template: _.template($("#toque_dashboard_template").html()),
  myEvents: {
    "change #toque_dashboard_fecha_desde":"buscar",
    "change #toque_dashboard_fecha_hasta":"buscar",
  },
  initialize: function() {

    $(this.el).html(this.template(this.model.toJSON()));

    var fecha_desde = moment().subtract(1,'month').format("DD/MM/YYYY");
    createdatepicker($(this.el).find("#toque_dashboard_fecha_desde"),fecha_desde);
    var fecha_hasta = moment(). format("DD/MM/YYYY");
    createdatepicker($(this.el).find("#toque_dashboard_fecha_hasta"),fecha_hasta);

    this.buscar();
    /*
    var desde = moment().subtract(7,"days");
    var plotOptionsSeries = {
      pointStart: desde.unix(),
      pointInterval: 24 * 3600 * 1000,
    };

    // VISION GENERAL
    this.$('#toque_dashboard_graficos').highcharts({
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
      series: [
        {
          "name":"Pedidos",
          "data":[15,18,13,25,30,12,17],
        }
      ]
    }); 
    */  
  },

  buscar: function() {
    var self = this;
    var params = {};
    params.desde = self.$("#toque_dashboard_fecha_desde").val();
    params.hasta = self.$("#toque_dashboard_fecha_hasta").val();
    if (PERFIL == 661) params.id_usuario = ID_USUARIO;
    $.ajax({
      "url":"toque/function/estadisticas_ventas/",
      "dataType":"json",
      "data":params,
      "type":"post",
      "success":function(r){
        self.$("#toque_dashboard_total_ventas").html("$"+Number(r.total_ventas).format(0));
        self.$("#toque_dashboard_cantidad_operaciones").html(Number(r.cantidad_operaciones).format(0));
        self.$("#toque_dashboard_ticket_promedio").html("$"+Number(r.venta_promedio).format(0));
        self.$("#toque_dashboard_venta_promedio").html("$"+Number(r.venta_promedio_por_dia).format(0));
        self.render_grafico(r.grafico);
      },
    });
  },

  render_grafico: function(r) {
    var desde_anio = r.desde.substr(6);
    var desde_mes = r.desde.substr(3,2)-1;
    var desde_dia = r.desde.substr(0,2);

    if (r.intervalo == "W") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointInterval: 24 * 3600 * 1000 * 7,
      };
    } else if (r.intervalo == "D") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointInterval: 24 * 3600 * 1000,
      };
    } else if (r.intervalo == "M") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointIntervalUnit: 'month',
      };
    }
    
    // VISION GENERAL
    this.$('#toque_dashboard_graficos').highcharts({
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
      series: r.series
    });   

  },

});

})(app);



(function ( app ) {

app.views.ToqueDashboardComercio = app.mixins.View.extend({
  template: _.template($("#toque_dashboard_comercio_template").html()),

  myEvents: {
    "change #toque_dashboard_comercio_fecha_desde":"buscar",
    "change #toque_dashboard_comercio_fecha_hasta":"buscar",
  },
  initialize: function() {
    $(this.el).html(this.template(this.model.toJSON()));
    var fecha_desde = moment().subtract(1,'month').format("DD/MM/YYYY");
    createdatepicker($(this.el).find("#toque_dashboard_comercio_fecha_desde"),fecha_desde);
    var fecha_hasta = moment(). format("DD/MM/YYYY");
    createdatepicker($(this.el).find("#toque_dashboard_comercio_fecha_hasta"),fecha_hasta);
    this.buscar();
  },

  buscar: function() {
    var self = this;
    var params = {};
    params.desde = self.$("#toque_dashboard_comercio_fecha_desde").val();
    params.hasta = self.$("#toque_dashboard_comercio_fecha_hasta").val();
    if (PERFIL == 661) params.id_usuario = ID_USUARIO;
    $.ajax({
      "url":"toque/function/estadisticas_ventas/",
      "dataType":"json",
      "data":params,
      "type":"post",
      "success":function(r){
        self.$("#toque_dashboard_comercio_total_ventas").html("$"+Number(r.total_ventas).format(0));
        self.$("#toque_dashboard_comercio_cantidad_operaciones").html(Number(r.cantidad_operaciones).format(0));
        self.$("#toque_dashboard_comercio_ticket_promedio").html("$"+Number(r.venta_promedio).format(0));
        self.$("#toque_dashboard_comercio_venta_promedio").html("$"+Number(r.venta_promedio_por_dia).format(0));
        self.$("#toque_dashboard_comercio_efectivo").html("$"+Number(r.efectivo).format(0));
        self.$("#toque_dashboard_comercio_tarjeta").html("$"+Number(r.tarjetas).format(0));
        self.render_grafico(r.grafico);
        self.render_tabla(r.mas_vendidos);
      },
    });
  },
  render_tabla: function(mas_vendidos) {
    this.$("#toque_dashboard_comercios_table").empty();
    for(var i=0;i< mas_vendidos.length;i++) {
      var r = mas_vendidos[i];
      var tr = "<tr>";
      tr+= "<td><span class='text-info'>"+r.nombre+"</span></td>";
      tr+= "<td>"+Number(r.cantidad).toFixed(2)+"</td>";
      tr+= "<td>$ "+Number(r.total).toFixed(2)+"</td>";
      tr+= "</tr>";
      this.$("#toque_dashboard_comercios_table").append(tr);
    }
  },
  render_grafico: function(r) {
    var desde_anio = r.desde.substr(6);
    var desde_mes = r.desde.substr(3,2)-1;
    var desde_dia = r.desde.substr(0,2);

    if (r.intervalo == "W") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointInterval: 24 * 3600 * 1000 * 7,
      };
    } else if (r.intervalo == "D") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointInterval: 24 * 3600 * 1000,
      };
    } else if (r.intervalo == "M") {
      var plotOptionsSeries = {
        pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
        pointIntervalUnit: 'month',
      };
    }
    
    // VISION GENERAL
    this.$('#toque_dashboard_comercio_graficos').highcharts({
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
      series: r.series
    });   

  },

});

})(app);
