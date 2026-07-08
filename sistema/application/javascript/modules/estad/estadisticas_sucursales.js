(function ( app ) {

  app.views.EstadisticasSucursalesView = app.mixins.View.extend({

    template: _.template($("#estadisticas_sucursales_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir":"imprimir",
      "click .guardar_saldos":"guardar_saldos",
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
      this.buscando = 0;
    },

    guardar_saldos: function() {
      var self = this;
      var id_sucursal = (self.$("#estadisticas_sucursales_sucursales").length > 0) ? self.$("#estadisticas_sucursales_sucursales").val() : ID_SUCURSAL;
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        self.$("#estadisticas_sucursales_sucursales").focus();
        return false;        
      }
      var desde = self.$("#estadisticas_sucursales_fecha_desde").val();
      var hasta = self.$("#estadisticas_sucursales_fecha_hasta").val();
      if (isEmpty(desde)) {
        alert("Por favor seleccione una fecha");
        self.$("#estadisticas_sucursales_fecha_desde").focus();
        return false;
      }
      if (isEmpty(hasta)) {
        alert("Por favor seleccione una fecha");
        self.$("#estadisticas_sucursales_fecha_hasta").focus();
        return false;
      }
      var banco_inicial = self.$("#estadisticas_sucursales_banco_inicial").val();
      var banco_final = self.$("#estadisticas_sucursales_banco_final").val();
      if (isEmpty(banco_inicial)) {
        alert("Por favor ingrese un valor");
        self.$("#estadisticas_sucursales_banco_inicial").focus();
        return false;
      }
      if (isEmpty(banco_final)) {
        alert("Por favor ingrese un valor");
        self.$("#estadisticas_sucursales_banco_final").focus();
        return false;
      }
      var efectivo_inicial = self.$("#estadisticas_sucursales_efectivo_inicial").val();
      var efectivo_final = self.$("#estadisticas_sucursales_efectivo_final").val();
      if (isEmpty(efectivo_inicial)) {
        alert("Por favor ingrese un valor");
        self.$("#estadisticas_sucursales_efectivo_inicial").focus();
        return false;
      }
      if (isEmpty(efectivo_final)) {
        alert("Por favor ingrese un valor");
        self.$("#estadisticas_sucursales_efectivo_final").focus();
        return false;
      }
      //var cargas_sociales_b = ((ID_EMPRESA == 249) ? self.$("#estadisticas_sucursales_cargas_sociales_b").val() : 0);
      $.ajax({
        "url":"estadisticas/function/guardar_saldos_bancarios/",
        "dataType":"json",
        "type":"post",
        "data":{
          "id_sucursal":id_sucursal,
          "id_empresa":ID_EMPRESA,
          "desde":desde,
          "hasta":hasta,
          "banco_inicial":banco_inicial,
          "banco_final":banco_final,
          "efectivo_inicial":efectivo_inicial,
          "efectivo_final":efectivo_final,
          //"cargas_sociales_b":cargas_sociales_b,
        },
        "success":function(r) {
          if (r.error == 0) alert("Los datos han sido guardados correctamente");
        },
      });
    },

    imprimir: function() {
      var self = this;
      var url = "estadisticas/function/resumen_sucursal/?imprimir=1";
      url += "&desde="+self.$("#estadisticas_sucursales_fecha_desde").val();
      url += "&hasta="+self.$("#estadisticas_sucursales_fecha_hasta").val();
      var id_sucursal = (self.$("#estadisticas_sucursales_sucursales").length > 0) ? self.$("#estadisticas_sucursales_sucursales").val() : ID_SUCURSAL;
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        self.$("#estadisticas_sucursales_sucursales").focus();
        return false;
      }
      url += "&id_sucursal="+id_sucursal;
      url += "&id_proyecto="+ ID_PROYECTO;
      window.open(url,"_blank");
    },

    buscar: function() {
      if (this.buscando == 1) return;
      this.buscando = 1;
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_sucursales_fecha_desde").val();
      params.hasta = self.$("#estadisticas_sucursales_fecha_hasta").val();
      params.id_sucursal = (self.$("#estadisticas_sucursales_sucursales").length > 0) ? self.$("#estadisticas_sucursales_sucursales").val() : ID_SUCURSAL;
      if (params.id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        self.$("#estadisticas_sucursales_sucursales").focus();
        return false;
      }
      params.imprimir = 0;
      params.id_proyecto = ID_PROYECTO;
      $.ajax({
        "url":"estadisticas/function/resumen_sucursal/",
        "dataType":"json",
        "data":params,
        "type":"get",
        "error":function() {
          self.buscando = 0;
        },
        "success":function(r){

          self.buscando = 0;
          var t_cantidad = 0;
          var t_efectivo = 0;
          var t_tarjetas = 0;
          var t_intereses = 0;
          var t_total = 0;
          var t_costo = 0;
          var t_ganancia = 0;

          // VENTAS
          self.$("#estadisticas_sucursales_ventas_table tbody").empty();
          for(var i=0;i<r.ventas.length;i++) {
            var elem = r.ventas[i];

            t_cantidad += parseFloat(elem.cantidad);
            t_efectivo += parseFloat(elem.efectivo);
            t_tarjetas += parseFloat(elem.tarjetas) - parseFloat(elem.intereses);
            t_intereses += parseFloat(elem.intereses);
            t_total += parseFloat(elem.total);
            t_costo += parseFloat(elem.costo);
            t_ganancia += parseFloat(elem.total - elem.costo);

            var item = new app.views.EstadisticasSucursalesVentasItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_sucursales_ventas_table tbody").append(item.el);
          }

          self.$("#estadisticas_sucursales_ventas_anterior").html("$ "+Number(r.venta_anterior).format());
          self.$("#estadisticas_sucursales_stock_inicial").html("$ "+Number(r.stock_inicial).format());
          self.$("#estadisticas_sucursales_stock_final").html("$ "+Number(r.stock_final).format());          

          // Variacion de Stock
          var variacion_stock = (r.stock_inicial > 0) ? (((r.stock_final - r.stock_inicial) / r.stock_inicial) * 100) : 0;
          if (variacion_stock > 0) {
            var icono = "<i class='fa fa-arrow-up text-success'></i>";
            self.$("#estadisticas_sucursales_stock_final_variacion").html("("+Number(variacion_stock).format()+"% "+icono+")");
          } else if (variacion_stock < 0) {
            var icono = "<i class='fa fa-arrow-down text-danger'></i>";
            self.$("#estadisticas_sucursales_stock_final_variacion").html("("+Number(variacion_stock).format()+"% "+icono+")");
          }

          // Variacion de Ventas
          var variacion = (r.venta_anterior > 0) ? (((t_total - r.venta_anterior) / r.venta_anterior) * 100) : 0;
          if (variacion > 0) {
            var icono = "<i class='fa fa-arrow-up text-success'></i>";
            self.$("#estadisticas_sucursales_ventas_anterior_variacion").html("("+Number(variacion).format()+"% "+icono+")");
          } else if (variacion < 0) {
            var icono = "<i class='fa fa-arrow-down text-danger'></i>";
            self.$("#estadisticas_sucursales_ventas_anterior_variacion").html("("+Number(variacion).format()+"% "+icono+")");
          }

          // Totalizamos
          self.$("#estadisticas_sucursales_tickets").html(Number(t_cantidad).format());
          self.$("#estadisticas_sucursales_ventas_efectivo").html("$ "+Number(t_efectivo).format());
          self.$("#estadisticas_sucursales_efectivo").html("$ "+Number(t_efectivo).format());
          self.$("#estadisticas_sucursales_tarjetas").html("$ "+Number(t_tarjetas).format());
          self.$("#estadisticas_sucursales_ventas_tarjetas").html("$ "+Number(t_tarjetas).format());
          self.$("#estadisticas_sucursales_intereses").html("$ "+Number(t_intereses).format());
          self.$("#estadisticas_sucursales_venta").html("$ "+Number(t_total).format());
          self.$("#estadisticas_sucursales_ventas_total").html("$ "+Number(t_total).format());
          self.$("#estadisticas_sucursales_cmv").html("$ "+Number(t_costo).format());
          self.$("#estadisticas_sucursales_ventas_cmv").html("$ "+Number(t_costo).format());
          self.$("#estadisticas_sucursales_ganancia").html("$ "+Number(t_ganancia).format());
          var t_margen = (t_costo > 0) ? ( ((t_total-t_costo)/t_costo) * 100) : 0;
          self.$("#estadisticas_sucursales_margen").html(Number(t_margen).format()+" %");
          var porc_efectivo = (t_total > 0) ? (t_efectivo / t_total * 100) : 0;
          self.$("#estadisticas_sucursales_ventas_efectivo_porc").html("("+Number(porc_efectivo).format()+"%)");
          var porc_tarjetas = (t_total > 0) ? (t_tarjetas / t_total * 100) : 0;
          self.$("#estadisticas_sucursales_ventas_tarjetas_porc").html("("+Number(porc_tarjetas).format()+"%)");
          var ticket_promedio = ((t_cantidad > 0) ? (t_total / t_cantidad) : 0);
          self.$("#estadisticas_sucursales_ventas_ticket_promedio").html("$ "+Number(ticket_promedio).format());

          if (self.$("#estadisticas_sucursales_ventas_efectivo_caja").length > 0) {
            self.$("#estadisticas_sucursales_ventas_efectivo_caja").html("$ "+Number(r.ventas_por_caja).format());
            var diferencia_efectivo = t_efectivo - parseFloat(r.ventas_por_caja);
            self.$("#estadisticas_sucursales_ventas_efectivo_diferencia").html("$ "+Number(diferencia_efectivo).format());
          }

          // INGRESOS
          var t_ingresos = 0;
          self.$("#estadisticas_sucursales_ingresos_table tbody").empty();
          for(var i=0;i<r.ingresos.length;i++) {
            var elem = r.ingresos[i];
            t_ingresos += parseFloat(elem.total);
            var item = new app.views.EstadisticasSucursalesIngresosItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_sucursales_ingresos_table tbody").append(item.el);
          }
          self.$("#estadisticas_sucursales_ingresos").html("$ "+Number(t_ingresos).format());

          // DEUDA PROVEEDORES
          var deudaView = new app.views.EstadisticasSucursalesDeudaTabla({
            model: new app.models.AbstractModel(r),
          });
          self.$("#tab_estad_suc_4").empty();
          self.$("#tab_estad_suc_4").append(deudaView.el);

          // GASTOS
          var gastosView = new app.views.EstadisticasSucursalesGastosTabla({
            model: new app.models.AbstractModel({
              "id_sucursal":params.id_sucursal,
              "gastos":r.gastos,
              "total_ventas":t_total,
            }),
          });
          self.$("#tab_estad_suc_3").empty();
          self.$("#tab_estad_suc_3").append(gastosView.el);

          // INGRESOS
          var ingresosView = new app.views.EstadisticasSucursalesIngresosTabla({
            model: new app.models.AbstractModel({
              "id_sucursal":params.id_sucursal,
              "ingresos":r.ingresos_cajas,
              "total_ventas":t_total,
            }),
          });
          self.$("#tab_estad_suc_8").empty();
          self.$("#tab_estad_suc_8").append(ingresosView.el);

          // PAGOS A PROVEEDORES
          var pagosView = new app.views.EstadisticasSucursalesPagosTabla({
            model: new app.models.AbstractModel(r),
          });
          self.$("#tab_estad_suc_6").empty();
          self.$("#tab_estad_suc_6").append(pagosView.el);

          self.$("#estadisticas_sucursales_socio_1_efectivo").html("$ "+Number(r.socio_1_efectivo).format());
          self.$("#estadisticas_sucursales_socio_2_efectivo").html("$ "+Number(r.socio_2_efectivo).format());
          self.$("#estadisticas_sucursales_socio_3_efectivo").html("$ "+Number(r.socio_3_efectivo).format());

          self.$("#estadisticas_sucursales_socio_1_banco").html("$ "+Number(r.socio_1_banco).format());
          self.$("#estadisticas_sucursales_socio_2_banco").html("$ "+Number(r.socio_2_banco).format());
          self.$("#estadisticas_sucursales_socio_3_banco").html("$ "+Number(r.socio_3_banco).format());

          self.$("#estadisticas_sucursales_banco_inicial").val(r.banco_inicial);
          self.$("#estadisticas_sucursales_banco_final").val(r.banco_final);

          self.$("#estadisticas_sucursales_efectivo_inicial").val(r.efectivo_inicial);
          self.$("#estadisticas_sucursales_efectivo_final").val(r.efectivo_final);
          self.$("#estadisticas_sucursales_ahorro_cargas_sociales").val(r.ahorro_cargas_sociales);

          if (ID_EMPRESA == 249) {
            var efectivo_inicial = Number(r.efectivo_inicial);
            self.$("#estadisticas_sucursales_cuenta_efectivo_inicial").html(efectivo_inicial.format());

            var ventas_por_caja = Number(r.ventas_por_caja);
            self.$("#estadisticas_sucursales_cuenta_venta_efectivo").html(ventas_por_caja.format());

            var ingresos = Number(ingresosView.total_efectivo);
            self.$("#estadisticas_sucursales_cuenta_otros_ingresos").html(ingresos.format());

            var pagos = 0;
            for(var i=0;i< r.ordenes_pago.length;i++) { var o = r.ordenes_pago[i]; pagos += Number(o.efectivo); }
            self.$("#estadisticas_sucursales_cuenta_pago_prov").html(Number(pagos).format());

            var gastos = Number(gastosView.total_efectivo);
            self.$("#estadisticas_sucursales_cuenta_gastos").html(gastos.format());

            var retiros = Number(r.socio_1_efectivo + r.socio_2_efectivo + r.socio_3_efectivo);
            self.$("#estadisticas_sucursales_cuenta_retiros").html(retiros.format());

            var ahorro = Number(r.ahorro_cargas_sociales);
            self.$("#estadisticas_sucursales_cuenta_carca_sociales_b").html(ahorro.format());

            var final = Number(efectivo_inicial + ventas_por_caja + ingresos - pagos - gastos - retiros - ahorro);
            self.$("#estadisticas_sucursales_cuenta_final").html(final.format());

            var final_declarado = Number(r.efectivo_final);
            self.$("#estadisticas_sucursales_cuenta_final_declarado").html(final_declarado.format());

            var diferencia_final = final - final_declarado;
            self.$("#estadisticas_sucursales_cuenta_final_diferencia").html(diferencia_final.format());
          }

          $('[data-toggle="tooltip"]').tooltip(); 
        },
      });
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      var fecha_desde = moment().subtract(1, 'month').startOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_sucursales_fecha_desde"),fecha_desde);
      var fecha_hasta = moment().subtract(1, 'month').endOf("month").toDate();
      createdatepicker($(this.el).find("#estadisticas_sucursales_fecha_hasta"),fecha_hasta);
    },

  });
})(app);


(function ( app ) {

  app.views.EstadisticasSucursalesVentasItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_sucursales_ventas_item_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );


(function ( app ) {

  app.views.EstadisticasSucursalesIngresosItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_sucursales_ingresos_item_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );


(function ( app ) {

  app.views.EstadisticasSucursalesGastosTabla = app.mixins.View.extend({
    template: _.template($('#estadisticas_sucursales_gastos_tabla_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      var cajas_gastos = new Array();
      for (var i = 0; i< window.cajas.length; i++) {
        var o = window.cajas[i];
        if (o.id_sucursal == this.model.get("id_sucursal")) {
          cajas_gastos.push({ "id":o.id, "total":0, "nombre":o.nombre, "tipo":o.tipo });
        }
      }
      self.total_efectivo = 0;
      var total_banco = 0;
      var total_gastos = 0;
      var gastos = this.model.get("gastos");
      for(var i=0;i< gastos.length; i++) {
        var g = gastos[i];
        for(var j=0;j< g.cajas.length; j++) {
          var c = g.cajas[j];
          if (c.tipo == 0) self.total_efectivo += c.total;
          else if (c.tipo == 1) total_banco += c.total;
          total_gastos += c.total;
        }
      }
      var obj = {
        "cajas_gastos":cajas_gastos,
        "total_efectivo":self.total_efectivo,
        "total_banco":total_banco,
        "total_gastos":total_gastos,
      }
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );


(function ( app ) {

  app.views.EstadisticasSucursalesIngresosTabla = app.mixins.View.extend({
    template: _.template($('#estadisticas_sucursales_ingresos_tabla_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      var cajas_gastos = new Array();
      for (var i = 0; i< window.cajas.length; i++) {
        var o = window.cajas[i];
        if (o.id_sucursal == this.model.get("id_sucursal")) {
          cajas_gastos.push({ "id":o.id, "total":0, "nombre":o.nombre, "tipo":o.tipo });
        }
      }
      self.total_efectivo = 0;
      var total_banco = 0;
      var total_ingresos = 0;
      var ingresos = this.model.get("ingresos");
      for(var i=0;i< ingresos.length; i++) {
        var g = ingresos[i];
        for(var j=0;j< g.cajas.length; j++) {
          var c = g.cajas[j];
          if (c.tipo == 0) self.total_efectivo += c.total;
          else if (c.tipo == 1) total_banco += c.total;
          total_ingresos += c.total;
        }
      }
      var obj = {
        "cajas_gastos":cajas_gastos,
        "total_efectivo":self.total_efectivo,
        "total_banco":total_banco,
        "total_ingresos":total_ingresos,
      }
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );

(function ( app ) {

  app.views.EstadisticasSucursalesPagosTabla = app.mixins.View.extend({
    template: _.template($('#estadisticas_sucursales_pagos_tabla_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );


(function ( app ) {

  app.views.EstadisticasSucursalesDeudaTabla = app.mixins.View.extend({
    template: _.template($('#estadisticas_sucursales_deuda_tabla_template').html()),
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      var t_deuda_vencida = 0;
      var t_adelantos = 0;
      var t_deuda_prov = 0;
      var t_deuda_prov_30 = 0;
      var t_deuda_prov_60 = 0;
      var t_deuda_prov_90 = 0;
      var t_deuda_prov_mas_90 = 0;
      self.$("#estadisticas_sucursales_deuda_table tbody").empty();
      var deuda_proveedores = this.model.get("deuda_proveedores");
      for(var i=0;i<deuda_proveedores.length;i++) {
        var elem = deuda_proveedores[i];
        if (elem.saldo < 0) {
          t_adelantos += Math.abs(elem.saldo);
        } else {
          t_deuda_prov += parseFloat(elem.saldo);  
        }

        elem.saldo_mas_90 = parseFloat(elem.saldo_mas_90);
        elem.saldo_90 = parseFloat(elem.saldo_90);
        elem.saldo_60 = parseFloat(elem.saldo_60);
        elem.saldo_30 = parseFloat(elem.saldo_30);
        elem.saldo = parseFloat(elem.saldo);

        // Dependiendo si la deuda esta vencida
        if (elem.dias_pago == 90) {
          t_deuda_vencida = (elem.saldo_mas_90 > 0 ? elem.saldo_mas_90 : 0);
        } else if (elem.dias_pago == 60) {
          t_deuda_vencida = (elem.saldo_mas_90 > 0 ? elem.saldo_mas_90 : 0);
          t_deuda_vencida = (elem.saldo_90 > 0 ? elem.saldo_90 : 0);
        } else if (elem.dias_pago == 30) {
          t_deuda_vencida = (elem.saldo_mas_90 > 0 ? elem.saldo_mas_90 : 0);
          t_deuda_vencida = (elem.saldo_90 > 0 ? elem.saldo_90 : 0);          
          t_deuda_vencida = (elem.saldo_60 > 0 ? elem.saldo_60 : 0);
        } else if (elem.dias_pago == 0) {
          t_deuda_vencida += (elem.saldo > 0) ? elem.saldo : 0;
        }

        t_deuda_prov_30 += elem.saldo_30;
        t_deuda_prov_60 += elem.saldo_60;
        t_deuda_prov_90 += elem.saldo_90;
        t_deuda_prov_mas_90 += elem.saldo_mas_90;
        var view = new app.views.DeudaProveedoresItemResultados({
          model: new app.models.AbstractModel(elem),
        });
        self.$("#estadisticas_sucursales_deuda_table tbody").append(view.el);
      }
      self.$("#estadisticas_sucursales_deuda_proveedores").html("$ "+Number(t_deuda_prov).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_adelantos").html("$ "+Number(t_adelantos).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_total_saldo_mas_90").html("$ "+Number(t_deuda_prov_mas_90).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_total_saldo_90").html("$ "+Number(t_deuda_prov_90).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_total_saldo_60").html("$ "+Number(t_deuda_prov_60).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_total_saldo_30").html("$ "+Number(t_deuda_prov_30).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_total_saldo").html("$ "+Number(t_deuda_prov - t_adelantos).format());
      self.$("#estadisticas_sucursales_deuda_proveedores_deuda_vencida").html("$ "+Number(t_deuda_vencida).format());

      // DEUDA EN CHEQUES
      var total_deuda_cheques = this.model.get("total_deuda_cheques");
      self.$("#estadisticas_sucursales_deuda_cheques").html("$ "+Number(total_deuda_cheques).format());

      self.$("#estadisticas_sucursales_total_deuda").html("$ "+Number(t_deuda_prov + total_deuda_cheques).format());
      $('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );