// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CuentasCorrientesProveedores = Backbone.Model.extend({
    urlRoot: function() {
      var s = "proveedores/function/cuentas_corrientes";
      s=s+"/"+this.get("fecha_desde");
      s=s+"/"+this.get("fecha_hasta");
      s=s+"/0"; // Codigo
      s=s+"/"+ID_EMPRESA;
      s=s+"/"+this.get("id_proveedor");
      s=s+"/"+this.get("id_sucursal");
      return s;
    },
    defaults: {
      "fecha_desde" : '',
      "fecha_hasta" : '',
      "id_proveedor": "",
      "id_sucursal":0,
      "datos": new Array()
    },
  });

})( app.models );


(function ( app ) {

  app.views.CuentasCorrientesProveedoresResultados = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_proveedores_resultados_template").html()),

    myEvents: {
      "click .agregar_orden_pago": "agregar_orden_pago",
      "click .exportar": "exportar",
      "click .imprimir": "imprimir",
      "click #checkTodos": "seleccionar_todos",
      "click .buscar":"buscar",
      "keypress #cuentas_corrientes_codigo_proveedor":function(e) {
        if (e.which == 13) { this.buscar_proveedor(); }
      },
    },

    imprimir: function() {

      var desde = self.$("#cuentas_corrientes_proveedores_fecha_desde").val();
      var hasta = self.$("#cuentas_corrientes_proveedores_fecha_hasta").val();
      var nombre = $("#cuentas_corrientes_proveedores_datos_nombre").text();

      var form = document.createElement("form");
      form.setAttribute("method","post");
      form.setAttribute("target","_blank");
      form.setAttribute("action","exportar/table_to_report/");

      var html = this.$("#cuentas_corrientes_proveedores_table").wrap("<p/>").parent().html();
      $(html).find("table").addClass("tabla");

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","tabla");
      hidden.setAttribute("value",html);
      form.appendChild(hidden);

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","titulo");
      hidden.setAttribute("value",nombre);
      form.appendChild(hidden);

      var hidden = document.createElement("input");
      hidden.setAttribute("type","hidden");
      hidden.setAttribute("name","fechas");
      hidden.setAttribute("value",desde+" - "+hasta);
      form.appendChild(hidden);

      $(form).css("display","none");
      document.body.appendChild(form);
      form.submit();
    },

    exportar : function() {

      var self = this;
      var id_proveedor = this.model.get("id_proveedor");
      if (id_proveedor == 0 || id_proveedor == null) {
        show("Por favor seleccione un cliente");
        $(this.el).find("#cuentas_corrientes_proveedores_codigo").focus();
        return;
      }
      var nombre = $("#cuentas_corrientes_proveedores_datos_nombre").text();
      var desde = $("#cuentas_corrientes_proveedores_fecha_desde").val();
      var hasta = $("#cuentas_corrientes_proveedores_fecha_hasta").val();

      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "fecha":$(e).find("td:eq(3)").html(),
          "comprobante":$(e).find("td:eq(4)").html(),
          "numero":$(e).find("td:eq(5) .numero_comprobante").text(),
          "debe":$(e).find("td:eq(6)").html(),
          "haber":$(e).find("td:eq(7)").html(),
          "saldo":$(e).find("td:eq(8)").html(),
          "observaciones":$(e).find("td:eq(2) .observaciones").val(),
        });
      });
      var header = new Array("Fecha","Comprobante","Numero","Debe","Haber","Saldo","Observaciones");
      this.exportar_excel({
        "filename":nombre,
        "title":"Resumen de Cuenta: "+nombre,
        "date":desde+" - "+hasta,
        "data":array,
        "header":header,
      });	        
    },	

    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();

      // Si tenemos que mostrar la cuenta de un cliente especifico
      if (this.model.get("id_proveedor") != 0) {
        this.id_proveedor = this.model.get("id_proveedor");
        this.buscar_proveedor_por_id(this.model.get("id_proveedor"));
      } else {
        this.id_proveedor = -1;    
      }
      
      this.bind("actualizar",this.mostrar_resultados);
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));

      var input = this.$("#cuentas_corrientes_codigo_proveedor");
      $(input).customcomplete({
        "url":"proveedores/function/get_by_nombre/",
        "form":null,
        "onSelect":function(item){
          var proveedor = new app.models.Proveedor({"id":item.id});
          proveedor.fetch({
            "success":function(){
              self.seleccionar_proveedor(proveedor);
            },
          });
        }
      });     

      // Toma un mes anterior, el mes actual, y el mes siguiente
      var fecha_desde = new Date();
      var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
      fecha_desde = new Date(y, m, 1);
      fecha_hasta = new Date(y, m + 1, 0);                        
      
      createdatepicker($(this.el).find("#cuentas_corrientes_proveedores_fecha_desde"),fecha_desde);
      createdatepicker($(this.el).find("#cuentas_corrientes_proveedores_fecha_hasta"),fecha_hasta);

      return this;
    },

    seleccionar_proveedor: function(c) {
      var self = this;
      this.$("#cuentas_corrientes_proveedores_datos_nombre").text(c.get("nombre"));
      this.$("#cuentas_corrientes_proveedores_datos_direccion").text(c.get("direccion")+" "+c.get("localidad"));
      this.$("#cuentas_corrientes_proveedores_datos_telefono").text(c.get("telefono"));
      this.$("#cuentas_corrientes_proveedores_datos_email").text(c.get("email"));
      this.$("#cuentas_corrientes_proveedores_datos_cuit").text(c.get("cuit"));
      this.$("#cuentas_corrientes_proveedores_datos_observaciones").text(c.get("observaciones"));
      var id_tipo_iva = c.get("id_tipo_iva");
      if (id_tipo_iva == 1) this.$("#cuentas_corrientes_proveedores_datos_iva").html("Responsable Inscripto");
      else if (id_tipo_iva == 2) this.$("#cuentas_corrientes_proveedores_datos_iva").html("Monotributo");
      else if (id_tipo_iva == 3) this.$("#cuentas_corrientes_proveedores_datos_iva").html("Exento");
      else if (id_tipo_iva == 4) this.$("#cuentas_corrientes_proveedores_datos_iva").html("Consumidor Final");
      this.model.set({ 
        "id_proveedor":c.id,
        "porc_ret_ib":c.get("porc_ret_ib"),
        "nombre_proveedor":c.get("nombre"),
        "direccion_proveedor":c.get("direccion"),
        "cuit_proveedor":c.get("cuit"),
        "aplica_ret_ganancias":c.get("aplica_ret_ganancias"),
      });
      this.buscar();

      self.$('#cuentas_corrientes_codigo_proveedor').val(c.get("nombre"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#cuentas_corrientes_codigo_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    seleccionar_todos : function(e) {
      var checked = $(e.currentTarget).is(":checked");
      if (checked) {
        $(this.el).find(".tbody .text-danger .checkbox").parents("tr").addClass("seleccionado");
      } else {
        $(this.el).find(".tbody .text-danger .checkbox").parents("tr").removeClass("seleccionado");
      }
      $(this.el).find(".tbody .text-danger .checkbox").attr("checked",checked);
    },

     buscar_proveedor : function() {
      var self = this;
      var codigo = this.$("#cuentas_corrientes_codigo_proveedor").val();

      // Buscamos el proveedor por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
      codigo = parseInt(codigo);
      if (!isNaN(codigo)) {
        $.ajax({
          "url":"proveedores/function/get_by_codigo/",
          "data":{
            "codigo":codigo,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.length == 0) {
              show("No existe un proveedor con el codigo: '"+codigo+"'");
              self.$("#cuentas_corrientes_codigo_proveedor").select();
              return;
            }
            var proveedor = new app.models.Proveedor(r);
            self.seleccionar_proveedor(proveedor);
          }
        });
      }
    },

    buscar_proveedor_por_id: function(id) {
      var self = this;
      var proveedor = new app.models.Proveedor({"id":id});
      proveedor.fetch({
        "success":function() {
          self.seleccionar_proveedor(proveedor);        
        },
        "error":function() {
          show("No existe un proveedor con el ID: '"+id+"'");
          self.$("#cuentas_corrientes_codigo_proveedor").select();
          return;
        }
      });
    },

    buscar : function() {

      var self = this;
      var fecha_desde = $(this.el).find("#cuentas_corrientes_proveedores_fecha_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#cuentas_corrientes_proveedores_fecha_hasta").val().replace(/\//g,"-");

      var id_proveedor = this.model.get("id_proveedor");
      if (id_proveedor == 0 || id_proveedor == null) {
        show("Por favor seleccione un proveedor");
        $(this.el).find("#cuentas_corrientes_codigo_proveedor").focus();
        return;
      }
      
      if (isEmpty(fecha_desde)) {
        show("Por favor seleccione una fecha");
        $(this.el).find("#cuentas_corrientes_proveedores_fecha_desde").focus();
        return;                
      }
      if (isEmpty(fecha_hasta)) {
        show("Por favor seleccione una fecha");
        $(this.el).find("#cuentas_corrientes_proveedores_fecha_hasta").focus();
        return;                
      }            
      
      this.model.set({
        "fecha_desde": fecha_desde,
        "fecha_hasta": fecha_hasta,
      });

      if (this.$("#cuentas_corrientes_sucursales").length > 0) {
        this.model.set({
          "id_sucursal":self.$("#cuentas_corrientes_sucursales").val(),
        });
      }

      this.model.fetch({
        "success":function(modelo) {
          self.mostrar_resultados(modelo);
        }
      });
    },


  	/**
  	 * MOSTRAMOS LOS RESULTADOS EN LA TABLA
  	 *
  	 */
    mostrar_resultados: function(model) {

      this.id_proveedor = this.model.get("id_proveedor");

      // Limpiamos la tabla
      $(this.el).find(".tbody").empty();

      this.comprobantes = new Array();
      var totalMonto = 0;
      var totalPago = 0;
      var saldo = 0;
      var saldoParcial = 0;

      var length = model.get("datos").length;

      // CARGAMOS EL SALDO INICIAL EN LA PRIMERA FILA
      var saldoParcial = model.get("saldo_inicial");
      var Item = Backbone.Model.extend({
        defaults: {
          "mostrar_checkbox": false,
          "fecha": "",
          "tipo_comprobante": "",
          "proveedor": "Saldo Inicial",
          "observaciones": "",
          "id_proveedor": 0,
          "numero": "",
          "monto": 0,
          "pago": 0,
          "compra_real":1,
          "pagada": 1,
          "saldo": saldoParcial,
          "efectivo":0,
          "progreso":0,
          "pendiente":0,
        },
      });
      var item = new app.views.CuentasCorrientesProveedoresItemResultados({
        model: new Item()
      });
      // La agregamos a la tabla
      $(this.el).find(".tbody").append(item.el);

      // Recorremos los resultados
      for(i=0;i<length;i++) {
        var m = model.get("datos")[i];

        // Tiene pago en efectivo
        if (m.pago != 0) {

          totalPago = parseFloat(m.pago);
          totalMonto = parseFloat(m.total_general);
          saldoParcial = parseFloat(saldoParcial) + totalMonto - totalPago;

        } else {

          if (m.total_general < 0) {
            totalPago = parseFloat(m.total_general);
            totalMonto = 0;
          } else {
            totalPago = 0;
            totalMonto = parseFloat(m.total_general);
          }
          saldoParcial = parseFloat(saldoParcial) + parseFloat(m.total_general);

        }                    
        
        // A las ordenes de pago no se les pone el checkbox
        if (m.id_tipo_comprobante > 0) {
          var mostrar_checkbox = true;
        } else {
          var mostrar_checkbox = false;
        }

        var progreso = (m.total_general != 0) ? (Number(m.cancelado) / Number(m.total_general) * 100) : 100;
        if (progreso == 100) mostrar_checkbox = false;

        // Creamos una fila nueva
        var Item = Backbone.Model.extend({
          defaults: {
            "id":m.id,
            "mostrar_checkbox": mostrar_checkbox,
            "fecha": m.fecha,
            "observaciones": m.observaciones,
            "tipo_comprobante": m.tipo_comprobante,
            "id_tipo_comprobante": m.id_tipo_comprobante,
            "numero": m.numero_1+"-"+m.numero_2,
            "monto": totalMonto,
            "pago": totalPago,
            "pagada": m.pagada,
            "saldo": ((m.compra_real == "1") ? saldoParcial : 0),
            "compra_real": m.compra_real,
            "total_iva": m.total_iva,
            "total_neto": m.total_neto,
            "proveedor": m.proveedor,
            "id_proveedor": m.id_proveedor,
            "id_sucursal": m.id_sucursal,
            "efectivo": Math.abs(m.efectivo),
            "cancelado": m.cancelado,
            "progreso": progreso,
            "pendiente":m.pendiente,
          }
        });
        var modelo = new Item();

        this.comprobantes.push(modelo);

        var item = new app.views.CuentasCorrientesProveedoresItemResultados({
          model: modelo
        });
        
        // La agregamos a la tabla
        $(this.el).find(".tbody").append(item.el);
      }
      $('[data-toggle="tooltip"]').tooltip();
    },

    agregar_orden_pago : function() {

      if (this.id_proveedor == -1) {
        show("Por favor seleccione un proveedor.");
        $("#cuentas_corrientes_codigo_proveedor").focus();
        return;
      }
      var self = this;
      var facturas_seleccionadas = new Array();
      var alguna_pagada = false;

      // AGREGAMOS LAS FACTURAS A LA ORDEN DE PAGO
      $(this.el).find(".checkbox:checked").each(function(i,e){
        var id = $(e).attr("id").replace("check","");
        facturas_seleccionadas.push(_.find(self.comprobantes,function(m){
          if (id == m.id && m.get("pagada") == 1) alguna_pagada = true;
          return (id == m.id);
        }).toJSON());
      });

      var id_proveedor = self.model.get("id_proveedor");
      var nombre_proveedor = self.model.get("nombre_proveedor");

      // Controlamos que todas las facturas seleccionadas sean del mismo proveedor
      if (facturas_seleccionadas.length > 0) {
        var id_primer_proveedor = facturas_seleccionadas[0].id_proveedor;
        for(var i=1; i<facturas_seleccionadas.length; i++) {
          var fac = facturas_seleccionadas[i];
          if (fac.id_proveedor != id_primer_proveedor) {
            alert("Los comprobantes seleccionados son de distintas razones sociales. Para realizar un pago, seleccione todos iguales.");
            return;
          }
        }
        id_proveedor = id_primer_proveedor;
        nombre_proveedor = facturas_seleccionadas[0].proveedor;
      }

      // Calculamos el total a pagar
      var total_pagar = 0;
      for(var i=0; i<facturas_seleccionadas.length; i++) {
        var f = facturas_seleccionadas[i];
        if (f.id_tipo_comprobante == 3 || f.id_tipo_comprobante == 8 || f.id_tipo_comprobante == 13 || f.id_tipo_comprobante == 21 || f.id_tipo_comprobante == 53 || f.id_tipo_comprobante == 203 || f.id_tipo_comprobante == 208) {
          f.por_cancelar = parseFloat(f.pago) - parseFloat(f.cancelado);  
        } else {
          f.por_cancelar = parseFloat(f.monto) - parseFloat(f.cancelado);  
        }
        f.resto = f.por_cancelar; // Variable auxiliar que tiene el monto maximo que se puede poner en el input
        total_pagar += f.por_cancelar;
      }

      // Comprobamos que los comprobantes seleccionados
      // no se hayan pagado antes en otra orden de pago
      if (alguna_pagada) {
        var aceptar = confirmar("Existen comprobantes seleccionados que ya fueron pagados por otra orden de pago. ¿Desea incluirlos igualmente en esta nueva orden de pago?");
      } else var aceptar = true;

      if (aceptar) {
        var ordenPago = new app.models.OrdenPago();
        ordenPago.set({
          "id_empresa":ID_EMPRESA,
          "id_sucursal":self.$("#cuentas_corrientes_sucursales").val(),
          "id_proveedor":id_proveedor,
          "proveedor": nombre_proveedor,
          "porc_ret_ib": self.model.get("porc_ret_ib"),
          "aplica_ret_ganancias": self.model.get("aplica_ret_ganancias"),
          "cuit": self.model.get("cuit_proveedor"),
          "direccion": self.model.get("direccion_proveedor"),
          "comprobantes":facturas_seleccionadas,
          "cheques":[],
          "depositos":[],
          "movimientos_efectivo":[],
          "total_pagar":total_pagar,
        });
        app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
          "model": ordenPago,
        });

        // Abrimos el lightbox de pagos
        crearLightboxHTML({
          "html":app.views.ordenPagoProveedores.el,
          "width":900,
          "height":565,
          "escapable":false,
        });
      }
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesProveedoresItemResultados = Backbone.View.extend({

    template: _.template($("#cuentas_corrientes_proveedores_item_resultados_template").html()),

    tagName: "tr",

    events: {
      "click .delete":"borrar",
      "click .edit":"editar",
      "click .checkbox":"seleccionar",
    },

    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("fila_roja");
      } else {
        $(this.el).removeClass("fila_roja");
      }
    },

    borrar : function() {
      if (confirmar("¿Realmente desea eliminar este comprobante?")) {
        // Comprobamos si es una compra o una orden de pago
        if (this.model.get("tipo_comprobante") == "Orden de Pago") {
          if (confirmar("¿Los cheques asociados a la orden de pago se deben anular?")) {
            var entregar = "A";
          } else {
            var entregar = "E";
          }
          var url = "ordenes_pago/"+this.model.id+"/?entregar="+entregar;
        } else {
          var url = "compras/"+this.model.id+"/?entregar="+entregar;
        }
        $.ajax({
          "url":url,
          "dataType": "json",
          "type": "DELETE",
          "success": function() {
            app.views.cuentas_corrientes_proveedoresResultados.buscar();
          },
          "error" : function() {
            show("Error al eliminar el comprobante.");
          }
        });
      }
    },

    editar : function() {
      if (this.model.get("tipo_comprobante") == "Orden de Pago") {
        var orden = new app.models.OrdenPago({
          id:this.model.id
        });
        orden.fetch({
          "success":function(){
            app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
              model: orden
            });
            crearLightboxHTML({
              "html":app.views.ordenPagoProveedores.el,
              "width":900,
              "height":565,
            });
          }
        });
      } else {
        window.open("app/#compras/"+this.model.id,"_blank");
      }
    },
    
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







// -----------
//   MODELO
// -----------

(function ( models ) {

  models.OrdenPago = Backbone.Model.extend({
    defaults: {
      "numero_1":0,
      "numero_2":0,
      "numero":0,
      "id_proveedor":0,
      "id_sucursal":0,
      "sucursal":"",
      "fecha":"",
      "proveedor": "",
      "cuit": "",
      "direccion": "",
      "cheques": [],
      "movimientos_efectivo":[],  // Estos dos se obtienen de la misma tabla
      "depositos": [],            // pero filtrando por tipo
      "tarjetas":[],
      "comprobantes":[],
      "efectivo":0,
      "total_cheques":0,
      "total_depositos":0,
      "descuento":0,
      "rotura":0,
      "porc_ret_ib": 0,
      "ret_ing_brutos":0,
      "aplica_ret_ganancias": 0,
      "ret_ganancias":0,
      "total_pagar":0,
      "total_valores_entregados":0,
      "diferencia":0,
      "observaciones":"",
      "compra_real":1,
      "numero_certificado_ret_ganancias":0,
      "numero_certificado_ret_ib":0,
      "id_empresa": ID_EMPRESA,
      "pendiente":0,
    },
    urlRoot : "ordenes_pago",
  });

})( app.models );



// -----------------------------------------
//   ORDEN DE PAGOS
// -----------------------------------------
(function ( app ) {

  app.views.OrdenPagoProveedores = app.mixins.View.extend({

    template: _.template($("#orden_pago_proveedores_template").html()),

    myEvents: {

      "click .guardar":"guardar",

      "click .imprimir_orden_pago":"imprimir_orden_pago",
      "click .imprimir_ret_ib":"imprimir_ret_ib",
      "click .imprimir_ret_ganancias":"imprimir_ret_ganancias",
      "click .imprimir_ret_ib_simple":"imprimir_ret_ib_simple",
      "click .imprimir_ret_ganancias_simple":"imprimir_ret_ganancias_simple",

      "click #orden_pago_cheques_terceros":"abrir_cheques",
      "click #orden_pago_cheques_agregar_item":"agregar_cheque_propio",
      
      "click #orden_pago_depositos_agregar":"agregar_deposito",
      "click .eliminar_deposito":"eliminar_deposito",

      "click #orden_pago_movimientos_efectivo_agregar":"agregar_efectivo",
      "click .eliminar_efectivo":"eliminar_efectivo",

      "keyup #orden_pago_cheques_tipo":function(e) {
        if (e.which == 13) this.$("#orden_pago_cheques_bancos").select();
      },
      "keyup #orden_pago_cheques_bancos":function(e) {
        if (e.which == 13) this.$("#orden_pago_cheques_fecha_emision").select();
      },
      "keydown #orden_pago_cheques_fecha_emision":function(e) {
        if (e.which == 13) this.$("#orden_pago_cheques_fecha_cobro").select();
      },
      "keydown #orden_pago_cheques_fecha_cobro":function(e) {
        if (e.which == 13) this.$("#orden_pago_cheques_numero").select();
      },
      "keydown #orden_pago_cheques_numero":function(e) {
        if (e.which == 13) this.$("#orden_pago_cheques_monto").select();
      },
      "keydown #orden_pago_cheques_monto":function(e) {
        if (e.which == 13) this.agregar_cheque_propio();
      },

      "change #orden_pago_proveedores_fecha":"calcular_ret_ganancias",

      "keypress #orden_pago_efectivo":function(e) {
        if (e.which == 13) $(e.currentTarget).trigger("focusout");
      },

      "keypress #orden_pago_depositos_monto":function(e) {
        if (e.which == 13) this.agregar_deposito();
      },
      "keypress #orden_pago_movimientos_efectivo_monto":function(e) {
        if (e.which == 13) this.agregar_efectivo();
      },

      // AL MODIFICAR LOS VALORES
      /*
      "focusout #orden_pago_efectivo":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }

        this.model.set({
          "efectivo" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      },
      */
      "focusout #orden_pago_descuento":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }

        this.model.set({
          "descuento" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      },
      "focusout #orden_pago_devolucion":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }

        this.model.set({
          "rotura" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      },
      "focusout #orden_pago_porc_ret_ib":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        this.model.set({
          "porc_ret_ib" : parseFloat(valor)
        });
        this.calcular_ret_ib();
        this.calcular_totales();
      },
      "focusout #orden_pago_ret_ganancias":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        this.model.set({
          "ret_ganancias" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      },		
    },

    initialize: function(options) {
      _.bindAll(this);
      var self = this;
      this.options = options;
      this.mostrar_comprobantes = (typeof options.mostrar_comprobantes !== "undefined") ? options.mostrar_comprobantes : 1;
      this.mostrar_fecha = (typeof options.mostrar_fecha !== "undefined") ? options.mostrar_fecha : 1;
      this.mostrar_numero = (typeof options.mostrar_numero !== "undefined") ? options.mostrar_numero : 1;
      this.mostrar_efectivo = (typeof options.mostrar_efectivo !== "undefined") ? options.mostrar_efectivo : 1;
      //this.mostrar_depositos = (typeof options.mostrar_depositos !== "undefined") ? options.mostrar_depositos : 1;
      this.mostrar_depositos = 1; //(control.check("cajas")>0);
      //this.mostrar_cheques = (typeof options.mostrar_cheques !== "undefined") ? options.mostrar_cheques : 1;
      this.mostrar_cheques = (control.check("cheques")>0);
      this.mostrar_tarjetas = (typeof options.mostrar_tarjetas !== "undefined") ? options.mostrar_tarjetas : 1;

      this.id_depositos = 0;
      this.id_movimientos_efectivo = 0;
      this.bind("eliminar_fila",this.eliminar_cheque);
      this.render();
    },

    render: function() {

      var self = this;
      var obj = {
        "id":this.model.id,
        "mostrar_comprobantes": this.mostrar_comprobantes,
        "mostrar_fecha": this.mostrar_fecha,
        "mostrar_numero": this.mostrar_numero,
        "mostrar_efectivo": this.mostrar_efectivo,
        "mostrar_depositos": this.mostrar_depositos,
        "mostrar_cheques": this.mostrar_cheques,
        "mostrar_tarjetas": this.mostrar_tarjetas,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      var fecha = (isEmpty(this.model.get("fecha"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha");
      createdatepicker(this.$("#orden_pago_proveedores_fecha"),fecha);

      createdatepicker(this.$("#orden_pago_cheques_fecha_emision"),fecha);
      createdatepicker(this.$("#orden_pago_cheques_fecha_cobro"),fecha);

      this.render_tabla_comprobantes();
      
      if (this.model.isNew()) {

        // Calculamos la retencion de ingresos brutos
        this.calcular_ret_ib();
        
        // Calculamos la retencion de ganancias
        this.calcular_ret_ganancias();
        
      } else {
        $(this.el).find("#orden_pago_porc_ret_ib").val(this.model.get("perc_ing_brutos"));
        $(this.el).find("#orden_pago_retencion_ib").val(this.model.get("ret_ing_brutos"));
        $(this.el).find("#orden_pago_ret_ganancias").val(this.model.get("ret_ganancias"));
      }

      // Renderizamos la lista de cheques
      for(var i = 0; i < this.model.get("cheques").length; i++) {
        var cheque = this.model.get("cheques")[i];
        var Cheq = Backbone.Model.extend({
          "defaults" : {
            "id":cheque.id,
            "id_banco":cheque.id_banco,
            "banco": cheque.banco,
            "numero":cheque.numero,
            "fecha_emision":cheque.fecha_emision,
            "fecha_cobro":cheque.fecha_cobro,
            "monto":cheque.monto,
            "titular":cheque.titular,
            "tipo":cheque.tipo,
          }
        });
        var item = new app.views.ChequeOrdenPagoItem({
          "model": new Cheq(),
          "tabla": this,
              "edicion": (typeof self.model.id == "undefined"), // Solo editar OP nueva
            });
        this.calcular_totales_cheques();
        $(this.el).find("#orden_pago_cheques_table").append(item.el);
      }

      this.render_tabla_depositos();
      this.render_tabla_movimientos_efectivo();

      this.calcular_totales();
      return this;
    },   

    render_tabla_comprobantes: function() {

      var self = this;
      this.total_neto = 0;
      this.calcular_retenciones = true;
      this.id_empresa = 0;
      var totalIva = 0;
      var totalGral = 0;
      $(this.el).find("#orden_pago_comprobantes").empty();

      console.log(this.model);
      
      // Agregamos la fila de la factura
      for (var i = 0; i < this.model.get("comprobantes").length; i++) {

        var m = this.model.get("comprobantes")[i];

        if (m.id_tipo_comprobante == 3 || m.id_tipo_comprobante == 8 || m.id_tipo_comprobante == 13 || m.id_tipo_comprobante == 21 || m.id_tipo_comprobante == 53 || m.id_tipo_comprobante == 203 || m.id_tipo_comprobante == 208) {
          // Si es alguna Nota de credito
          m.total = (m.pago < 0) ? m.pago : -m.pago;
          m.neto = (m.total_neto < 0) ? m.total_neto : -m.total_neto;
          m.iva = (m.total_neto < 0) ? m.total_iva : -m.total_iva;
        } else {
          m.total = m.monto;
          m.neto = m.total_neto;
          m.iva = m.total_iva;
        }

        if (m.tipo_comprobante == "Remito") {
          this.calcular_retenciones = false;
        }

        if (this.model.get("id_empresa") != 0) this.id_empresa = this.model.get("id_empresa");
        else this.id_empresa = m.id_empresa;

        this.total_neto = parseFloat(this.total_neto) + parseFloat(m.neto);
        totalIva = parseFloat(totalIva) + parseFloat(m.iva);
        totalGral = parseFloat(totalGral) + parseFloat(m.por_cancelar);

        // Creamos una fila nueva
        var Item = Backbone.Model.extend();
          /*defaults: {
            "id":m.id,
            "fecha": m.fecha,
            "tipo_comprobante": m.tipo_comprobante,
            "numero": m.numero,
            "neto": neto,
            "iva": iva,
            "total": total,
            "monto": total,
          }*/
        //});
        m.posicion = i;
        m.id_orden_pago = ((typeof self.model.id != "undefined") ? self.model.id : 0);
        var item = new Item(m);
        var itemView = new app.views.CuentasCorrientesProveedoresItemOrdenPago({
          model: item,
          parent: self,
        });
        
        $(this.el).find("#orden_pago_comprobantes").append(itemView.el);
      }

      $(this.el).find("#orden_pago_total_neto").html(Number(this.total_neto).toFixed(2));
      $(this.el).find("#orden_pago_total_iva").html(Number(totalIva).toFixed(2));
      $(this.el).find("#orden_pago_total_gral").html(Number(totalGral).toFixed(2));

      this.model.set({
        "total_pagar" : totalGral,
        "id_empresa" : this.id_empresa
      }); 
    },

    //====================
    // TRANSFERENCIAS
    //====================    

    agregar_deposito: function() {

      var id_caja = this.$("#orden_pago_depositos_cajas").val();
      var caja = this.$("#orden_pago_depositos_cajas option:selected").text();
      if (id_caja == 0) {
        alert("Por favor seleccione una caja.");
        this.$("#orden_pago_depositos_cajas").focus();
        return;
      }
      var monto = this.$("#orden_pago_depositos_monto").val();
      monto = Number(monto);
      if (isNaN(monto) || monto == 0) {
        alert("Valor incorrecto.");
        this.$("#orden_pago_depositos_monto").focus();
        return;
      }
      var deposito = {
        "id":this.id_depositos,
        "id_caja":id_caja,
        "caja":caja,
        "monto":monto.toFixed(2),
      };
      this.model.get("depositos").push(deposito);
      this.id_depositos = this.id_depositos + 1;
      this.render_tabla_depositos();
      this.$("#orden_pago_depositos_monto").val("");
    },

    eliminar_deposito: function(e) {
      var id = $(e.currentTarget).parents("tr").data("id");
      var depositos2 = _.filter(this.model.get("depositos"),function(c){
        return (c.id != id);
      });
      this.model.set({ "depositos":depositos2 });
      this.render_tabla_depositos();
    },

    render_tabla_depositos: function() {
      var self = this;
      this.$("#orden_pago_depositos_table").empty();
      var depositos = this.model.get("depositos");
      console.log(depositos);
      var total = 0;
      for(var i=0;i<depositos.length;i++) {
        var d = depositos[i];
        var tr = "<tr data-id='"+d.id+"'>";
        tr+="<td>"+d.caja+"</td>";
        tr+="<td>"+(!isEmpty(d.path) ? "<a href='/sistema/"+d.path+"' target='_blank'><i class='fa fa-file-o'></i></a>" : "")+"</td>";
        tr+="<td class='tar'>$ "+d.monto+"</td>";
        tr+="<td>";
        if (self.model.id == undefined) tr+="<i class='glyphicon glyphicon-remove eliminar_deposito text-danger' />";
        tr+="</td>";
        tr+="</tr>";
        this.$("#orden_pago_depositos_table").append(tr);
        total = total + parseFloat(d.monto);
      }
      this.model.set({ "total_depositos":total });
      this.$("#orden_pago_depositos_total").text("$ "+Number(total).toFixed(2));
      this.calcular_totales();
    },

    //====================
    // EFECTIVO
    //====================

    agregar_efectivo: function() {

      var id_caja = this.$("#orden_pago_movimientos_efectivo_cajas").val();
      var caja = this.$("#orden_pago_movimientos_efectivo_cajas option:selected").text();
      if (id_caja == 0) {
        alert("Por favor seleccione una caja.");
        this.$("#orden_pago_movimientos_efectivo_cajas").focus();
        return;
      }
      var monto = this.$("#orden_pago_movimientos_efectivo_monto").val();
      monto = Number(monto);
      if (isNaN(monto) || monto == 0) {
        alert("Valor incorrecto.");
        this.$("#orden_pago_movimientos_efectivo_monto").focus();
        return;
      }
      var efectivo = {
        "id":this.id_movimientos_efectivo,
        "id_caja":id_caja,
        "caja":caja,
        "monto":monto.toFixed(2),
      };
      this.model.get("movimientos_efectivo").push(efectivo);
      this.id_movimientos_efectivo = this.id_movimientos_efectivo + 1;
      this.render_tabla_movimientos_efectivo();
      this.$("#orden_pago_movimientos_efectivo_monto").val("");
    },

    eliminar_efectivo: function(e) {
      var id = $(e.currentTarget).parents("tr").data("id");
      var movimientos_efectivo2 = _.filter(this.model.get("movimientos_efectivo"),function(c){
        return (c.id != id);
      });
      this.model.set({ "movimientos_efectivo":movimientos_efectivo2 });
      this.render_tabla_movimientos_efectivo();
    },

    render_tabla_movimientos_efectivo: function() {
      var self = this;
      this.$("#orden_pago_movimientos_efectivo_table").empty();
      var movimientos_efectivo = this.model.get("movimientos_efectivo");
      console.log(movimientos_efectivo);
      var total = 0;
      for(var i=0;i<movimientos_efectivo.length;i++) {
        var d = movimientos_efectivo[i];
        var tr = "<tr data-id='"+d.id+"'>";
        tr+="<td>"+d.caja+"</td>";
        tr+="<td>"+(!isEmpty(d.path) ? "<a href='/sistema/"+d.path+"' target='_blank'><i class='fa fa-file-o'></i></a>" : "")+"</td>";
        tr+="<td class='tar'>$ "+d.monto+"</td>";
        tr+="<td>";
        if (self.model.id == undefined) tr+="<i class='glyphicon glyphicon-remove eliminar_efectivo text-danger' />";
        tr+="</td>";
        tr+="</tr>";
        this.$("#orden_pago_movimientos_efectivo_table").append(tr);
        total = total + parseFloat(d.monto);
      }
      this.model.set({ "efectivo":total });
      this.$("#orden_pago_movimientos_efectivo_total").text("$ "+Number(total).toFixed(2));
      this.calcular_totales();
    },


    //====================
    // RETENCIONES
    //====================

    calcular_ret_ib : function() {
      if (RETIENE_IB == 1 && this.calcular_retenciones) {
        if (this.total_neto >= 2000) {
          var retencion_ib = parseFloat(this.total_neto * this.model.get("porc_ret_ib") / 100);
          $(this.el).find("#orden_pago_retencion_ib").val(Number(retencion_ib).toFixed(2));
        } else {
          var retencion_ib = 0;
          $(this.el).find("#orden_pago_retencion_ib").val("0.00");
        }
        this.model.set({ "ret_ing_brutos" : retencion_ib });		    
      }
    },

    //====================
    // CHEQUES
    //====================

    // ABRIMOS UN LIGHTBOX CON LOS CHEQUES DE TERCEROS
    abrir_cheques : function() {
      var self = this;
      window.cheque = null;
      app.collections.cheques = new app.collections.Cheques();

      app.views.chequesTableView = new app.views.ChequesTableView({
        collection: app.collections.cheques,
        lightbox: true,
        permiso: 1
      });
      crearLightboxHTML({
        "html":app.views.chequesTableView.el,
        "width":800,
        "height":360,
        "callback":function(){
          self.agregar_cheque(window.cheque);
        }
      });
    },

    agregar_cheque_propio: function() {
      var self = this;
      var id_banco = self.$("#orden_pago_cheques_bancos").val();
      if (id_banco == 0) {
        alert("Por favor seleccione un banco.");
        self.$("#orden_pago_cheques_bancos").focus();
        return false;
      }
      var fecha_emision = self.$("#orden_pago_cheques_fecha_emision").val();
      if (isEmpty(fecha_emision)) {
        alert("Por favor seleccione una fecha.");
        self.$("#orden_pago_cheques_fecha_emision").focus();
        return false;
      }
      var fecha_cobro = self.$("#orden_pago_cheques_fecha_cobro").val();
      if (isEmpty(fecha_cobro)) {
        alert("Por favor seleccione una fecha.");
        self.$("#orden_pago_cheques_fecha_cobro").focus();
        return false;
      }
      var numero = self.$("#orden_pago_cheques_numero").val();
      if (isEmpty(numero)) {
        alert("Por favor ingrese el numero de cheque.");
        self.$("#orden_pago_cheques_numero").focus();
        return false;
      }
      var monto = self.$("#orden_pago_cheques_monto").val();
      if (isEmpty(monto)) {
        alert("Por favor ingrese un monto.");
        self.$("#orden_pago_cheques_monto").focus();
        return false;
      }
      var ch = {
        "id_banco":id_banco,
        "banco":self.$("#orden_pago_cheques_bancos option:selected").text(),
        "fecha_emision":fecha_emision,
        "fecha_cobro":fecha_cobro,
        "numero":numero,
        "monto":monto,
        "titular":NOMBRE,
        "cuit_titular":CUIT,
        "id_empresa":ID_EMPRESA,
        "tipo":self.$("#orden_pago_cheques_tipo").val(),
      };
      var cheque = new app.models.Cheque(ch);
      $.ajax({
        "url":"cheques/function/exists/",
        "dataType":"json",
        "data":ch,
        "type":"post",
        "success":function(r){
          if (r.existe == 1) {
            alert(r.mensaje);
          } else {
            self.agregar_cheque(cheque);
            self.limpiar_cheque();
          }          
        }
      });
    },

    // AGREGAMOS EL CHEQUE SELECCIONADO A LA TABLA
    // (PUEDE SER PROPIO O DE TERCERO)
    agregar_cheque : function(cheque) {
      if (cheque == null) return;
      var self = this;
      var item = new app.views.ChequeOrdenPagoItem({
        "model": cheque,
        "tabla": self,
        "edicion": true,
      });
      // Controlamos que el cheque no exista
      for (var i=0; i<this.model.get("cheques").length; i++) {
        var c = this.model.get("cheques")[i];
        if (c.numero == cheque.numero && c.id_banco == cheque.id_banco) {
          show("ERROR. El cheque ya fue ingresado al pago.");
          return;
        }
      }
      // Agregamos el cheque a la lista
      this.model.get("cheques").push(cheque.toJSON());
      this.calcular_totales_cheques();
      $(this.el).find("#orden_pago_cheques_table").append(item.el);
    },

    eliminar_cheque: function(ch) {
      var cheques = new Array();
      var array = this.model.get("cheques");
      for(var i=0;i<array.length;i++) {
        var e = array[i];
        if (!(e.numero == ch.get("numero") && e.id_banco == ch.get("id_banco"))) {
          cheques.push(e);
        }
      }
      this.model.set({ "cheques":cheques });
      this.calcular_totales_cheques();
    },

    limpiar_cheque: function() {
      this.$("#orden_pago_cheques_numero").val("");
      this.$("#orden_pago_cheques_monto").val("");
    },

    //====================
    // TOTALES
    //====================    

    calcular_totales_cheques : function() {
      var montoTotal = 0;
      for (var i=0; i<this.model.get("cheques").length; i++) {
        var c = this.model.get("cheques")[i];
        montoTotal = parseFloat(montoTotal) + parseFloat(c.monto);
      }
      $(this.el).find("#orden_pago_cheque_total").text(Number(montoTotal).toFixed(2));
      
      this.model.set({ "total_cheques":montoTotal });
      
      this.calcular_totales();
    },

    calcular_ret_ganancias : function() {
      var self = this;
      if (RETIENE_GANANCIAS == 1 && this.calcular_retenciones) {
        var fecha = $(this.el).find("#orden_pago_proveedores_fecha").val();
        fecha = fecha.replace(/\//g,"-");
        var id_proveedor = this.model.get("id_proveedor");            
        var id_empresa = this.model.get("id_empresa");
        $.ajax({
          "url":"ordenes_pago/function/calcular_ret_ganancias/"+id_proveedor+"/"+fecha+"/"+id_empresa,
          "dataType":"json",
          "success":function(e) {
            if (e.calcula == 1) {
              var neto = parseFloat(e.total_neto) + parseFloat(self.total_neto);
              if (e.tipo_proveedor == 1) {
                neto = neto - 224000;
                var alicuota = 0.02;
              } else if (e.tipo_proveedor == 2) {
                neto = neto - 16830;
                var alicuota = 0.06;
              }                       
              if (neto <= 0) {
                self.model.set({"ret_ganancias":0});
                $(self.el).find("#orden_pago_ret_ganancias").val(Number(0).toFixed(2));
                self.calcular_totales();
                return;
              }
              var ganancias = neto * alicuota;
              ganancias = ganancias - parseFloat(e.total_ret_ganancias);
              self.model.set({"ret_ganancias":ganancias});
              $(self.el).find("#orden_pago_ret_ganancias").val(Number(ganancias).toFixed(2));
            } else {
              self.model.set({"ret_ganancias":e.total_ret_ganancias});
              $(self.el).find("#orden_pago_ret_ganancias").val(Number(e.total_ret_ganancias).toFixed(2));
            }
            self.calcular_totales();
          }
        });
      }
    },

    calcular_totales : function() {

      // Ponemos el total a pagar
      $(this.el).find("#orden_pago_total_pagar").val(Number(this.model.get("total_pagar")).toFixed(2));
      
      // Calculamos todos los valores entregados
      var t = parseFloat(this.model.get("efectivo"));
      t = t + parseFloat(this.model.get("ret_ing_brutos"));
      t = t + parseFloat(this.model.get("ret_ganancias"));
      t = t + parseFloat(this.model.get("descuento"));
      t = t + parseFloat(this.model.get("rotura"));
      t = t + parseFloat(this.model.get("total_cheques"));
      t = t + parseFloat(this.model.get("total_depositos"));
      $(this.el).find("#orden_pago_total_valores_entregados").val(Number(t).toFixed(2));
      
      this.model.set({
        "total_valores_entregados":t
      })
      
      // Calculamos la diferencia entre lo que hay que pagar
      // y lo que se va a pagar
      $(this.el).find("#orden_pago_total_diferencia").val(Number( this.model.get("total_pagar") - t).toFixed(2));
    },

    // --------------------------------------
    //     GUARDAMOS LA ORDEN DE PAGO
    // --------------------------------------
    guardar : function() {
      try {

        // Si la diferencia es positiva, entonces hay que ajustar los montos de los comprobantes
        // En cambio si es negativa, se esta haciendo un pago a cuenta o saldo a favor del cliente
        var diferencia = parseFloat(this.$("#orden_pago_total_diferencia").val());
        if (diferencia > 0) {
          alert("ERROR: No se esta pagando la totalidad de los saldos de los comprobantes.");
          return;
        }

        // TODO: si la orden de pago es solo de remitos
        // se debe cambiar el estado
        var self = this;
        var fecha = validate_input("orden_pago_proveedores_fecha",IS_EMPTY,"Por favor ingrese una fecha.");
        this.model.set({
          "fecha":fecha,
          "estado":ESTADO,
        });
      } catch(e) {
        return;
      }
      this.model.save({},{
        "success" : function() {
          app.views.cuentas_corrientes_proveedoresResultados.buscar();
          $('.modal:last').modal('hide');
        },
        "error" : function() {
          show("Ocurrio un error cuando se estaba guardando la orden de pago.");
        }
      });
    },

    imprimir_orden_pago : function() {
      window.open("ordenes_pago/function/imprimir/"+this.model.id,"_blank");
    },

    imprimir_ret_ib : function() {
      window.open("ordenes_pago/function/imprimir_ret_ib/"+this.model.id,"_blank");
    },

    imprimir_ret_ganancias : function() {
      window.open("ordenes_pago/function/imprimir_ret_ganancias/"+this.model.id+"/0/"+this.model.get("id_empresa"),"_blank");
    },

    imprimir_ret_ib_simple : function() {
      window.open("ordenes_pago/function/imprimir_ret_ib/"+this.model.id+"/1","_blank");
    },

    imprimir_ret_ganancias_simple : function() {
      window.open("ordenes_pago/function/imprimir_ret_ganancias/"+this.model.id+"/1/"+this.model.get("id_empresa"),"_blank");
    },    
  
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE ORDENES DE PAGO
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesProveedoresItemOrdenPago = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_proveedores_item_orden_pago_template").html()),
    
    tagName: "tr",

    myEvents: {
      "change .input_saldo":function(e) {
        var input = $(e.currentTarget);
        var value = parseFloat(input.val());
        if (isNaN(value)) {
          alert("Por favor ingrese un numero");
          input.focus();
          return;
        }
        var max_value = parseFloat(input.data("max"));
        var min_value = parseFloat(input.data("min"));
        if (min_value > value || value > max_value) {      
          alert("Error: El monto total del comprobante debe estar entre $"+min_value+" y $"+max_value);
          input.focus();
          return;
        }
        this.parent.model.attributes.comprobantes[this.model.get("posicion")].por_cancelar = value;
        this.parent.render_tabla_comprobantes();
        this.parent.calcular_totales();
      },
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.parent = options.parent;
      this.render();
    },

    render: function() {
    	$(this.el).html(this.template(this.model.toJSON()));
      return this;
    },

  });
})(app);





// -----------------------------------------------------
//   ITEM DE LA TABLA DE CHEQUES DE LA ORDENES DE PAGO
// -----------------------------------------------------
(function ( app ) {

  app.views.ChequeOrdenPagoItem = Backbone.View.extend({

    template: _.template($("#cuentas_corrientes_proveedores_item_cheques_orden_pago_template").html()),

    tagName: "tr",

    events : {
      "click .eliminar" : "borrar",
    },		

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.edicion = options.edicion;
      this.options = options;
      this.render();
    },

    borrar : function() {
      $(this.el).remove();
      this.options.tabla.trigger("eliminar_fila",this.model);
    },		

    render: function() {
      var obj = { "edicion": this.edicion };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);





// -----------------------------------------
//   LISTADO DE ORDENES DE PAGO
// -----------------------------------------


(function (collections, model, paginator) {

  collections.OrdenesPago = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },
    paginator_core: {
      url: "compras/function/consulta_ordenes_pago/",
    },
  });

})( app.collections, app.models.OrdenPago, Backbone.Paginator);



(function ( app ) {

  app.views.OrdenesPagoListadoView = app.mixins.View.extend({

    template: _.template($("#ordenes_pago_listado_template").html()),
    
    myEvents: {
      "click .buscar": "buscar",
      "keypress #ordenes_pago_listado_buscar":function(e) {
        if (e.which == 13) this.buscar();
      },
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.ordenes_pago_listado_fecha_desde = (typeof window.ordenes_pago_listado_fecha_desde != "undefined") ? window.ordenes_pago_listado_fecha_desde : this.fecha;
      window.ordenes_pago_listado_fecha_hasta = (typeof window.ordenes_pago_listado_fecha_hasta != "undefined") ? window.ordenes_pago_listado_fecha_hasta : this.fecha;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;            

      $(this.el).html(this.template({
        "permiso":self.permiso,
        "seleccionar":self.habilitar_seleccion,
      }));

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
      this.usa_filtros = false;

      createdatepicker(this.$("#ordenes_pago_desde"),window.ordenes_pago_listado_fecha_desde);
      createdatepicker(this.$("#ordenes_pago_hasta"),window.ordenes_pago_listado_fecha_hasta);
      
      this.buscar();
    },

    buscar : function() {

      var self = this;
      var cambio_parametros = false;
      filtros = {};
      filtros.filter = $(this.el).find("#ordenes_pago_listado_buscar").val();
      
      filtros.id_sucursal = 0;
      if (this.$("#ordenes_pago_sucursales").length > 0) {
        filtros.id_sucursal = this.$("#ordenes_pago_sucursales").val();
      }

      if (this.$("#ordenes_pago_desde").length > 0 && window.ordenes_pago_listado_fecha_desde != this.$("#ordenes_pago_desde").val().trim()) {
        window.ordenes_pago_listado_fecha_desde = this.$("#ordenes_pago_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#ordenes_pago_hasta").length > 0 && window.ordenes_pago_listado_fecha_hasta != this.$("#ordenes_pago_hasta").val().trim()) {
        window.ordenes_pago_listado_fecha_hasta = this.$("#ordenes_pago_hasta").val().trim();
        cambio_parametros = true;
      }

      filtros.desde = (isEmpty(window.ordenes_pago_listado_fecha_desde)) ? "" : window.ordenes_pago_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.ordenes_pago_listado_fecha_hasta)) ? "" : window.ordenes_pago_listado_fecha_hasta.replace(/\//g,"-");

      this.usa_filtros = (!isEmpty(filtros.filter) || !isEmpty(filtros.desde) || !isEmpty(filtros.hasta));
      if (this.usa_filtros) {
        filtros.limit = 0;
        filtros.offset = 99999;
      }
      //if (filtros.id_sucursal != 0) this.usa_filtros = true; // Se pone aparte porque el offset no se debe aplicar cuando filtra por sucursal, ya que puede haber muchas
      this.collection.server_api = filtros;
      this.collection.pager();            
    },

    addAll : function () {
      var self = this;
      var total = 0;
      var total_efectivo = 0;
      var total_cheques = 0;
      var total_depositos = 0;
      var total_otros = 0;
      var cantidad = 0;
      this.$("#ordenes_pago_tabla tbody").empty();
      this.collection.each(function(i){
        console.log(i);
        self.addOne(i);
        total += parseFloat(i.get("total_general"));
        total_efectivo += parseFloat(i.get("efectivo"));
        total_cheques += parseFloat(i.get("total_cheques"));
        total_depositos += parseFloat(i.get("total_depositos"));
        total_otros += parseFloat(i.get("descuento"));
        cantidad++;
      });

      // Agregamos una fila al final
      if (this.usa_filtros) {
        this.$(".pagination_container").hide();
        this.$("#ordenes_pago_resumen_total").html("$ "+Number(total).format());
        this.$("#ordenes_pago_resumen_total_efectivo").html("$ "+Number(total_efectivo).format());
        this.$("#ordenes_pago_resumen_total_cheques").html("$ "+Number(total_cheques).format());
        this.$("#ordenes_pago_resumen_total_depositos").html("$ "+Number(total_depositos).format());
        this.$("#ordenes_pago_resumen_total_otros").html("$ "+Number(total_otros).format());
        this.$("#ordenes_pago_resumen_cantidad").html(cantidad);
        this.$(".resumen").show();
      } else {
        this.$(".resumen").hide();
      }
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var view = new app.views.OrdenesPagoItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#ordenes_pago_tabla tbody").append(view.render().el);
    },

  });

})(app);

(function ( app ) {
  app.views.OrdenesPagoItemResultados = app.mixins.View.extend({

    template: _.template($("#ordenes_pago_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .edit":"editar",
      "click .checkbox":"seleccionar",
      "click .ver_cta_cte":"ver_cta_cte",
    },
    ver_cta_cte: function() {
      var url = "app/#cuentas_corrientes_proveedores/"+this.model.get("id_proveedor");
      window.open(url,"_blank");
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
    },
    editar : function() {
      var orden = new app.models.OrdenPago({
        id:this.model.id
      });
      orden.fetch({
        "success":function(){
          app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
            model: orden
          });
          crearLightboxHTML({
            "html":app.views.ordenPagoProveedores.el,
            "width":900,
            "height":565,
          });
        }
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.seleccionar = (options.seleccionar == undefined) ? false : options.seleccionar;
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
