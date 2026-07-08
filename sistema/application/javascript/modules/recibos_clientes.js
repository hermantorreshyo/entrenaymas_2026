// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ReciboCliente = Backbone.Model.extend({
    defaults: {
      "id_empresa":ID_EMPRESA,
      "id_cliente":0,
      "id_usuario":ID_USUARIO,
      "id_punto_venta":0,
      "fecha":"",
      "numero":"",
      "punto_venta":0,
      "efectivo": 0,
      "descuento":0,
      "vuelto":0,
      "total_comprobantes": 0,
      "total_cheques": 0,
      "total_depositos": 0,
      "total_tarjetas":0,
      "total":0,
      "cheques": [],
      "depositos": [],
      "movimientos_efectivo":[],
      "tarjetas":[],
      "comprobantes":[],
      "cotizacion_dolar":(typeof COTIZACION_DOLAR != "undefined" ? COTIZACION_DOLAR : 0),
      "retencion_iibb":0,
      "retencion_ganancias":0,
      "retencion_suss": 0,
      "retencion_iva": 0,
      "retencion_otras": 0,
      "observaciones":"",
    },
    urlRoot : "recibos",
  });
    
})( app.models );



// -----------------------------------------
//   RECIBO DE CLIENTES
// -----------------------------------------
(function ( app ) {

app.views.ReciboClientes = app.mixins.View.extend({

  template: _.template($("#recibo_clientes_template").html()),
  
  myEvents: {
    "click .guardar":"guardar",
    "change .total_comprobante":"cambiar_total_comprobante",
    "click #recibo_cheques":"abrir_cheques",
    "click #recibo_cheques_agregar_item":"nuevo_cheque",
    "click #recibo_cheques_nuevo":"nuevo_cheque",
    "click .imprimir_recibo":"imprimir_recibo",

    // ENTER PARA QUE VAYA PASANDO EL FOCO
    "keydown #recibo_cheques_fecha_emision":function(e){
      if (e.which == 13) $("#recibo_cheques_fecha_cobro").select();
    },
    "keydown #recibo_cheques_fecha_cobro":function(e){
      if (e.which == 13) $("#recibo_cheques_bancos").focus();
    },
    "change #recibo_cheques_bancos":function(e){
      $("#recibo_cheques_numero").select();
    },
    "keydown #recibo_cheques_numero":function(e){
      if (e.which == 13) $("#recibo_cheques_titular").select();
    },
    "keydown #recibo_cheques_titular":function(e){
      if (e.which == 13) $("#recibo_cheques_monto").select();
    },
    "keydown #recibo_cheques_monto":function(e){
      if (e.which == 13) { this.nuevo_cheque(); }
    },
    "click #recibo_cheques_agregar_item":function() {
      this.nuevo_cheque();
    },
    
    "click #recibo_clientes_depositos_agregar": "agregar_deposito",
    "click .eliminar_deposito":"eliminar_deposito",
    "keypress #recibo_clientes_depositos_monto":function(e) {
      if (e.which == 13) this.agregar_deposito();
    },

    "click #recibo_clientes_movimientos_efectivo_agregar": "agregar_efectivo",
    "click .eliminar_efectivo":"eliminar_efectivo",
    "keypress #recibo_clientes_movimientos_efectivo_monto":function(e) {
      if (e.which == 13) this.agregar_efectivo();
    },

    "click #recibo_clientes_tarjetas_agregar": "agregar_tarjeta",
    "click .eliminar_tarjeta":"eliminar_tarjeta",
    "click #tab_tarjetas":function() {
      var self = this;
      new app.mixins.Select({
        modelClass: app.models.Tarjeta,
        url: "tarjetas/",
        render: "#recibo_tarjetas",
        fisrsOptions: ["<option value='0'>Seleccione</option>"],
        onComplete:function() {
          $("#recibo_tarjetas").focus();
        }
      });
    },
    "change #recibo_tarjetas":function() {
      this.$("#recibo_tarjeta_lote").select();
    },
    "keydown #recibo_tarjeta_lote":function(e){
      if (e.which == 13) this.$("#recibo_tarjeta_cupon").select();
    },
    "keydown #recibo_tarjeta_cupon":function(e){
      if (e.which == 13) this.$("#recibo_tarjeta_importe").select();
    },
    "keydown #recibo_tarjeta_importe":function(e){
      if (e.which == 13) this.$("#recibo_tarjeta_cuotas").focus();
    },
    "change #recibo_tarjeta_cuotas":function() {
      this.$("#recibo_tarjeta_agregar_item").focus();
    },
    "click #recibo_tarjeta_agregar_item":"agregar_tarjeta",

    /*
    "keydown #recibo_efectivo":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "efectivo" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    */

    "keydown #recibo_descuento":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "descuento" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_retencion_iibb":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "retencion_iibb" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_retencion_ganancias":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "retencion_ganancias" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_retencion_suss":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "retencion_suss" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_retencion_iva":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "retencion_iva" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_retencion_otras":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "retencion_otras" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },
    "keydown #recibo_vuelto":function(e) {
      if (e.which == 13) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
        
        this.model.set({
          "vuelto" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      }
    },    
    
    // AL MODIFICAR LOS VALORES
    /*
    "focusout #recibo_efectivo":function(e) {
      var valor = $(e.currentTarget).val();
      if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
      if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
      
      this.model.set({
        "efectivo" : parseFloat($(e.currentTarget).val())
      });
      this.calcular_totales();
    },
    */

    // AL MODIFICAR LOS VALORES
    "focusout #recibo_descuento":function(e) {
      var valor = $(e.currentTarget).val();
      if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
      if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }
      
      this.model.set({
        "descuento" : parseFloat($(e.currentTarget).val())
      });
      this.calcular_totales();
    },
  },
  
  initialize: function(options) {
    _.bindAll(this);
    var self = this;
    this.bind("eliminar_fila",this.eliminar_cheque);
    this.mostrar_comprobantes = (typeof options.mostrar_comprobantes !== "undefined") ? options.mostrar_comprobantes : 1;
    this.mostrar_fecha = (typeof options.mostrar_fecha !== "undefined") ? options.mostrar_fecha : 1;
    this.mostrar_numero = (typeof options.mostrar_numero !== "undefined") ? options.mostrar_numero : 1;
    this.mostrar_efectivo = (typeof options.mostrar_efectivo !== "undefined") ? options.mostrar_efectivo : 1;
    this.mostrar_descuento = (typeof options.mostrar_descuento !== "undefined") ? options.mostrar_efectivo : 1;
    this.mostrar_depositos = (typeof options.mostrar_depositos !== "undefined") ? options.mostrar_depositos : 1;
    this.mostrar_cheques = (typeof options.mostrar_cheques !== "undefined") ? options.mostrar_cheques : 1;
    this.mostrar_tarjetas = (typeof options.mostrar_tarjetas !== "undefined") ? options.mostrar_tarjetas : 1;
    this.guardando = 0;
    this.id_depositos = 1;
    this.id_movimientos_efectivo = 1;
    this.id_tarjetas = 1;
    this.render();
  },
    
  render: function() {
    
    var self = this;
    var obj = { 
      id:this.model.id,
      mostrar_comprobantes: this.mostrar_comprobantes,
      mostrar_fecha: this.mostrar_fecha,
      mostrar_numero: this.mostrar_numero,
      mostrar_efectivo: this.mostrar_efectivo,
      mostrar_descuento: this.mostrar_descuento,
      mostrar_depositos: this.mostrar_depositos,
      mostrar_cheques: this.mostrar_cheques,
      mostrar_tarjetas: this.mostrar_tarjetas,
    };
    $.extend(obj,this.model.toJSON());
    $(this.el).html(this.template(obj));

    var fecha = (isEmpty(this.model.get("fecha"))) ? moment().format("DD/MM/YYYY") : this.model.get("fecha");
    createdatepicker(this.$("#recibo_clientes_fecha"),fecha);

    createdatepicker(this.$("#recibo_cheques_fecha_emision"),fecha);
    createdatepicker(this.$("#recibo_cheques_fecha_cobro"),fecha);
    
    if (this.model.id == undefined) {
      $.ajax({
        "url":"recibos/function/next/",
        "dataType":"json",
        "success":function(r) {
          $("#recibo_clientes_numero").val(r.numero);
        }
      });      
    }
    
    // Renderizamos el listado de comprobantes
    this.render_tabla_comprobantes();
    
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
        }
      });
      var item = new app.views.ChequeReciboItem({
        "model": new Cheq(),
        "tabla": this,
        "solo_lectura":true,
      });
      this.calcular_totales_cheques();
      $(this.el).find("#recibo_cheques_table").append(item.el);
    }
    
    this.render_tabla_depositos();
    this.render_tabla_movimientos_efectivo();
    this.render_tabla_tarjetas();

    return this;
  },

  imprimir_recibo: function() {
    workspace.imprimir_reporte("recibos/function/imprimir_recibo/"+this.model.id+"/"+this.model.get("id_punto_venta"));
  },
  
  // ==================
  // DEPOSITOS  
  // ==================

  agregar_deposito: function() {
    
    var id_caja = this.$("#recibo_clientes_depositos_cajas").val();
    var caja = this.$("#recibo_clientes_depositos_cajas option:selected").text();
    if (id_caja == 0) {
      alert("Por favor seleccione una caja.");
      this.$("#recibo_clientes_depositos_cajas").focus();
      return;
    }
    var monto = this.$("#recibo_clientes_depositos_monto").val();
    monto = Number(monto);
    if (isNaN(monto) || monto == 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_clientes_depositos_monto").focus();
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
    this.$("#recibo_clientes_depositos_monto").val("");
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
    this.$("#recibo_depositos_table").empty();
    var depositos = this.model.get("depositos");
    var total = 0;
    for(var i=0;i<depositos.length;i++) {
      var d = depositos[i];
      var tr = "<tr data-id='"+d.id+"'>";
      tr+="<td>"+d.caja+"</td>";
      tr+="<td class='tar'>$ "+d.monto+"</td>";
      tr+="<td>";
      if (self.model.id == undefined) tr+="<i class='glyphicon glyphicon-remove eliminar_deposito text-danger' />";
      tr+="</td>";
      tr+="</tr>";
      this.$("#recibo_depositos_table").append(tr);
      total = total + parseFloat(d.monto);
    }
    this.model.set({ "total_depositos":total });
    this.$("#recibo_depositos_total").text("$ "+Number(total).toFixed(2));
    this.calcular_totales();
  },


  // ==================
  // EFECTIVO  
  // ==================

  agregar_efectivo: function() {
    
    var id_caja = this.$("#recibo_clientes_movimientos_efectivo_cajas").val();
    var caja = this.$("#recibo_clientes_movimientos_efectivo_cajas option:selected").text();
    if (id_caja == 0) {
      alert("Por favor seleccione una caja.");
      this.$("#recibo_clientes_movimientos_efectivo_cajas").focus();
      return;
    }
    var monto = this.$("#recibo_clientes_movimientos_efectivo_monto").val();
    monto = Number(monto);
    if (isNaN(monto) || monto == 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_clientes_movimientos_efectivo_monto").focus();
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
    this.$("#recibo_clientes_movimientos_efectivo_monto").val("");
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
    this.$("#recibo_movimientos_efectivo_table").empty();
    var movimientos_efectivo = this.model.get("movimientos_efectivo");
    var total = 0;
    for(var i=0;i<movimientos_efectivo.length;i++) {
      var d = movimientos_efectivo[i];
      var tr = "<tr data-id='"+d.id+"'>";
      tr+="<td>"+d.caja+"</td>";
      tr+="<td class='tar'>$ "+d.monto+"</td>";
      tr+="<td>";
      if (self.model.id == undefined) tr+="<i class='glyphicon glyphicon-remove eliminar_efectivo text-danger' />";
      tr+="</td>";
      tr+="</tr>";
      this.$("#recibo_movimientos_efectivo_table").append(tr);
      total = total + parseFloat(d.monto);
    }
    this.model.set({ "efectivo":total });
    this.$("#recibo_movimientos_efectivo_total").text("$ "+Number(total).toFixed(2));
    this.calcular_totales();
  },


  // ==================
  // TARJETAS  
  // ==================

  agregar_tarjeta: function() {
    
    var id_tarjeta = this.$("#recibo_tarjetas").val();
    var tarjeta = this.$("#recibo_tarjetas option:selected").text();
    if (id_tarjeta == 0) {
      alert("Por favor seleccione una tarjeta.");
      this.$("#recibo_tarjetas").focus();
      return;
    }
    var lote = this.$("#recibo_tarjeta_lote").val();
    lote = Number(lote);
    if (isNaN(lote) || lote <= 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_tarjeta_lote").select();
      return;
    }
    var cupon = this.$("#recibo_tarjeta_cupon").val();
    cupon = Number(cupon);
    if (isNaN(cupon) || cupon <= 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_tarjeta_cupon").select();
      return;
    }
    var importe = this.$("#recibo_tarjeta_importe").val();
    importe = Number(importe);
    if (isNaN(importe) || importe <= 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_tarjeta_importe").select();
      return;
    }
    var cuotas = this.$("#recibo_tarjeta_cuotas").val();
    var tarjeta = {
      "id":this.id_tarjetas,
      "id_tarjeta":id_tarjeta,
      "tarjeta":tarjeta,
      "lote":lote,
      "cupon":cupon,
      "importe":importe,
      "cuotas":cuotas,
    };
    this.model.get("tarjetas").push(tarjeta);
    this.id_tarjetas = this.id_tarjetas + 1;
    this.render_tabla_tarjetas();
    this.$("#recibo_tarjeta_lote").val("");
    this.$("#recibo_tarjeta_cupon").val("");
    this.$("#recibo_tarjeta_importe").val("");
    this.$("#recibo_tarjeta_cuotas").val(1);
    this.$("#recibo_tarjetas").focus();
  },  

  eliminar_tarjeta: function(e) {
    var id = $(e.currentTarget).parents("tr").data("id");
    var tarjetas2 = _.filter(this.model.get("tarjetas"),function(c){
      return (c.id != id);
    });
    this.model.set({ "tarjetas":tarjetas2 });
    this.render_tabla_tarjetas();
  },  
  
  render_tabla_tarjetas: function() {
    var self = this;
    this.$("#recibo_tarjetas_table").empty();
    var tarjetas = this.model.get("tarjetas");
    var total = 0;
    for(var i=0;i<tarjetas.length;i++) {
      var d = tarjetas[i];
      var tr = "<tr data-id='"+d.id+"'>";
      tr+="<td>"+d.tarjeta+"</td>";
      tr+="<td>"+d.lote+"</td>";
      tr+="<td>"+d.cupon+"</td>";
      tr+="<td>"+d.cuotas+"</td>";
      tr+="<td>$ "+d.importe+"</td>";
      tr+="<td>";
      if (self.model.id == undefined) tr+="<i class='glyphicon glyphicon-remove eliminar_tarjeta text-danger' />";
      tr+="</td>";
      tr+="</tr>";
      this.$("#recibo_tarjetas_table").append(tr);
      total = total + parseFloat(d.importe);
    }
    this.model.set({ "total_tarjetas":total });
    this.$("#recibo_tarjetas_total").text("$ "+Number(total).toFixed(2));
    this.calcular_totales();
  },

  
  render_tabla_comprobantes: function() {
    var self = this;
    var total_saldo = 0;
    var total_debe = 0;
    var total_haber = 0;
    var comprobantes = this.model.get("comprobantes");
    console.log(comprobantes);
    for(var i=0;i<comprobantes.length;i++) { 

      var comprobante = comprobantes[i];

      // Al haber le agregamos lo pagado
      comprobante.haber = parseFloat(comprobante.haber) + parseFloat(comprobante.total_pagado);

      var saldo = Number(comprobante.debe-comprobante.haber).toFixed(2);
      var tr = "<tr>";
      tr+="<td>"+comprobante.fecha+"</td>";
      tr+="<td>"+comprobante.tipo_comprobante+"</td>";
      tr+="<td>"+comprobante.comprobante+"</td>";

      // NUEVO RECIBO
      if (typeof this.model.id == "undefined") {
        var input = "<input "+((comprobante.negativo==1)?"disabled":"")+" type='text' data-id='"+comprobante.id+"' data-min='0' data-max='"+saldo+"' class='form-control dib w80 total_comprobante' value='"+Number(saldo).toFixed(2)+"' />";
        tr+="<td class='tar'>$ "+Number(comprobante.debe).toFixed(2)+"</td>";
        tr+="<td class='tar'>$ "+Number(comprobante.haber).toFixed(2)+"</td>";
        tr+="<td class='tar'>$ "+input+"</td>";      
        total_saldo += parseFloat(saldo);
        total_debe += parseFloat(comprobante.debe);
        total_haber += parseFloat(comprobante.haber);

        // Actualizamos el total del comprobante, luego si se modifica a mano, se hace
        // a traves del metodo cambiar_total_comprobante()
        comprobante.total = saldo;         

      // ESTAMOS VIENDO UN RECIBO
      } else {
        tr+="<td class='tar'>$ "+Number(comprobante.debe).toFixed(2)+"</td>";
        tr+="<td class='tar'>$ "+Number(comprobante.haber).toFixed(2)+"</td>";
        total_debe += parseFloat(comprobante.debe);
        total_saldo += parseFloat(comprobante.haber);
      }
      tr+="</tr>";

      /*
      // El total puede ser negativo si es una NC
      var total = Number(comprobante.total - comprobante.pago).toFixed(2);
      // El total pagado tambien puede ser negativo si es una NC
      var pago = Number(comprobante.total_pagado).toFixed(2);
      var saldo = Number(total-pago).toFixed(2);
      var tr = "<tr>";
      tr+="<td>"+comprobante.fecha+"</td>";
      tr+="<td>"+comprobante.tipo_comprobante+"</td>";
      tr+="<td>"+comprobante.comprobante+"</td>";
      var input = "<input "+((this.model.id != undefined || comprobante.negativo==1)?"disabled":"")+" type='text' data-id='"+comprobante.id+"' data-min='0' data-max='"+saldo+"' class='form-control dib w80 total_comprobante' value='"+saldo+"' />";
      tr+="<td class='tar'>$ "+total+"</td>";
      tr+="<td class='tar'>$ "+pago+"</td>";
      tr+="<td class='tar'>$ "+input+"</td>";
      tr+="</tr>";
      total_comprobantes += parseFloat(total - pago);
      total_pagado += parseFloat(pago);
      total_full += (comprobante.negativo == 1) ? parseFloat(-comprobante.total) : parseFloat(comprobante.total);
      */
      this.$("#recibo_clientes_tabla_comprobantes").append(tr);
    }
    this.model.set({ 
      "comprobantes":comprobantes,
      "total_comprobantes":total_saldo,
    });
    this.$("#recibo_clientes_total_debe").html("$ "+Number(total_debe).toFixed(2));
    this.$("#recibo_clientes_total_haber").html("$ "+Number(total_haber).toFixed(2));
    this.$("#recibo_clientes_total").html("$ "+Number(total_saldo).toFixed(2));
  },

  cambiar_total_comprobante:function(e) {

    var input = $(e.currentTarget);
    var id = input.data("id");
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

    var comprobantes = this.model.get("comprobantes");
    for(var i=0;i<comprobantes.length;i++) { 
      if (comprobantes[i].id == id) {
        comprobantes[i].total = value;
      }
    }
    this.model.set({"comprobantes":comprobantes});
    console.log(this.model.get("comprobantes"));

    // Calculamos los totales
    var total_comprobantes = 0;
    this.$(".total_comprobante").each(function(i,e){
      total_comprobantes += parseFloat($(e).val());
    });
    this.model.set({ "total_comprobantes":total_comprobantes });
    this.$("#recibo_clientes_total").html("$ "+Number(total_comprobantes).toFixed(2));

    this.calcular_totales();
  },

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
      "width":700,
      "height":450,
      "callback":function() {
        self.agregar_cheque(window.cheque);
      },
    });
  },
  
  // ABRIMOS UN LIGHTBOX CON LOS CHEQUES DE TERCEROS
  nuevo_cheque : function() {
  
    var self = this;
    window.cheque = null;
    app.views.chequeEditView = new app.views.ChequeEditView({
      model: new app.models.Cheque({
        "id_cliente":self.model.get("id_cliente"),
        "cliente":$("#cuentas_corrientes_clientes_datos_nombre").html(),
        "titular":$("#cuentas_corrientes_clientes_datos_nombre").html(),
        "cuit_titular":$("#cuentas_corrientes_clientes_datos_cuit").html(),
      }),
      lightbox: true,
      permiso: 3,
      id_modulo: "cheques",
    });
    crearLightboxHTML({
      "html":app.views.chequeEditView.el,
      "width":700,
      "height":450,
      "callback":function() {
        self.agregar_cheque(window.cheque);
      },
    });
  },  

  nuevo_cheque: function() {
    var self = this;
    var cheque = new app.models.Cheque({
      "id_cliente":self.model.get("id_cliente"),
      "id_banco":self.$("#recibo_cheques_bancos").val(),
      "banco":self.$("#recibo_cheques_bancos option:selected").text(),
      "numero":self.$("#recibo_cheques_numero").val(),
      "fecha_emision":self.$("#recibo_cheques_fecha_emision").val(),
      "fecha_cobro":self.$("#recibo_cheques_fecha_cobro").val(),
      "monto":self.$("#recibo_cheques_monto").val(),
      "titular":self.$("#recibo_cheques_titular").val(),
      "tipo":"T",
    });
    // Guardamos el cheque y luego lo agregamos
    cheque.save({},{
      "success":function(model){
        self.agregar_cheque(model);
        self.limpiar_cheque();        
      }
    })
  },

  limpiar_cheque: function() {
    this.$("#recibo_cheques_bancos").val("0");
    this.$("#recibo_cheques_numero").val("");
    this.$("#recibo_cheques_fecha_emision").val(moment().format("DD/MM/YYYY"));
    this.$("#recibo_cheques_fecha_cobro").val(moment().format("DD/MM/YYYY"));
    this.$("#recibo_cheques_monto").val("");
    this.$("#recibo_cheques_titular").val($("#cuentas_corrientes_clientes_datos_nombre").html());
    this.$("#recibo_cheques_fecha_emision").select();
  },
    
  // AGREGAMOS EL CHEQUE SELECCIONADO A LA TABLA
  // (PUEDE SER PROPIO O DE TERCERO)
  agregar_cheque : function(cheque) {
    if (cheque == null) return;
    var item = new app.views.ChequeReciboItem({
      "model": cheque,
      "tabla": this,
      "solo_lectura":false,
    });
    
    // Controlamos que el cheque no exista
    for (var i=0; i<this.model.get("cheques").length; i++) {
      var c = this.model.get("cheques")[i];
      if (c.id == cheque.id && cheque.id != 0) {
        show("ERROR. El cheque ya fue ingresado al pago.");
        return;
      }
    }
    
    // Agregamos el cheque a la lista
    this.model.get("cheques").push(cheque.toJSON());
    this.calcular_totales_cheques();
    $(this.el).find("#recibo_cheques_table").append(item.el);
  },
  
  eliminar_cheque : function(id) {
    var cheques = _.filter(this.model.get("cheques"),function(e){
      return (e.id != id);
    });
    this.model.set({ "cheques":cheques });
    this.calcular_totales_cheques();
  },
  
  calcular_totales_cheques : function() {
      
    var montoTotal = 0;
    for (var i=0; i<this.model.get("cheques").length; i++) {
      var c = this.model.get("cheques")[i];
      montoTotal = parseFloat(montoTotal) + parseFloat(c.monto);
    }
    $(this.el).find("#recibo_cheque_total").text("$ "+Number(montoTotal).toFixed(2));
    this.model.set({ "total_cheques":montoTotal });
    this.calcular_totales();
  },
  
  calcular_totales : function() {
    // Calculamos todos los valores entregados
    var t = parseFloat(this.model.get("efectivo"));
    t = t + parseFloat(this.model.get("descuento"));
    t = t + parseFloat(this.model.get("retencion_iibb"));
    t = t + parseFloat(this.model.get("retencion_ganancias"));
    t = t + parseFloat(this.model.get("retencion_suss"));
    t = t + parseFloat(this.model.get("retencion_iva"));
    t = t + parseFloat(this.model.get("retencion_otras"));
    t = t + parseFloat(this.model.get("total_cheques"));
    t = t + parseFloat(this.model.get("total_depositos"));
    t = t + parseFloat(this.model.get("total_tarjetas"));
    t = t - parseFloat(this.model.get("vuelto"));
    t = Number(t).toFixed(2);
    $(this.el).find("#recibo_total_valores_entregados").val(t);
    var d = (this.model.get("total_comprobantes") - t);
    $(this.el).find("#recibo_total_diferencia").val(Number(d).toFixed(2));
    this.model.set({
      "total":t,
    });
  },
  
  // --------------------------------------
  //   GUARDAMOS EL RECIBO
  // --------------------------------------
  guardar : function() {
    var self = this;
    try {

      if (this.mostrar_fecha == 1) {
        var fecha = validate_input("recibo_clientes_fecha",IS_EMPTY,"Por favor ingrese una fecha.");  
        this.model.set({
          "fecha":fecha,
          "id_proyecto":ID_PROYECTO,
        });
      }
      if (this.mostrar_numero == 1) {
        var numero = $("#recibo_clientes_numero").val();
        this.model.set({
          "numero":numero,
        });
      }

      if (this.$("#recibo_clientes_punto_venta_numero").length > 0) {
        var punto_venta = $("#recibo_clientes_punto_venta_numero").val();
        this.model.set({
          "punto_venta":punto_venta,
        });
      }

      // Controlamos que lo que se esta pagando sea igual a la suma de los comprobantes
      // (Que en realidad se puede pagar menos, ya que el usuario puede modificar
      // cuanto desea pagar de cada comprobante)        
      var comprobantes = parseFloat(this.model.get("comprobantes"));
      var total_recibido = (parseFloat(this.model.get("total")));
      var total_comprobantes = (parseFloat(this.model.get("total_comprobantes")));

      console.log("TOTAL RECIBIDO: ");
      console.log(total_recibido);
      console.log("TOTAL COMPROBANTES: ");
      console.log(total_comprobantes);
      window.total_recibido = total_recibido;
      window.total_comprobantes = total_comprobantes;
      
      // Si tiene seleccionado comprobantes
      // (porque podria ser un pago de entrega de efectivo por ejemplo, sin tener comprobantes seleccionados)
      if ((total_comprobantes - total_recibido) > 1) {
        alert("ERROR: Si desea imputar pagos parciales, modifique los saldos de los comprobantes incluidos.");
        this.$(".total_comprobante").first().select();
        return;
      }

      // Controlamos si la diferencia es negativa
      if ( total_comprobantes < 0) {
        alert("ERROR: El total de comprobantes no puede ser negativo. Asocie las notas de credito a las facturas compensatorias.");
        return;
      }

      if (this.guardando == 1) return;
      this.guardando = 1;

      this.model.set({
        "id_usuario":ID_USUARIO,
        "id_punto_venta":((self.$("#recibo_clientes_punto_venta").length > 0) ? self.$("#recibo_clientes_punto_venta").val() : 0),
        "observaciones":self.$("#recibo_observaciones").val(),
      });

    } catch(e) {
      return;
    }
    this.model.save({},{
      "success" : function() {
        if (typeof app.views.cuentas_corrientes_clientesResultados !== "undefined") {
          app.views.cuentas_corrientes_clientesResultados.buscar();
        }
        window.id_recibo = self.model.id;
        $('.modal:last').modal('hide');
        self.guardando = 0;
      },
      "error" : function() {
        show("Ocurrio un error cuando se estaba guardando el recibo.");
        self.guardando = 0;
      }
    });
  },
  
});

})(app);

// -----------------------------------------
//   ITEM DE LA TABLA DE RECIBOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesClientesItemRecibo = Backbone.View.extend({

    template: _.template($("#cuentas_corrientes_clientes_item_recibo_template").html()),
    
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


// -----------------------------------------------------
//   ITEM DE LA TABLA DE CHEQUES DEL RECIBO DE CLIENTE
// -----------------------------------------------------
(function ( app ) {

  app.views.ChequeReciboItem = Backbone.View.extend({

    template: _.template($("#cuentas_corrientes_clientes_item_cheques_recibo_template").html()),
    
    tagName: "tr",
    
    events : {
      "click .eliminar" : "borrar",
    },    
    
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    
    borrar : function() {
      $(this.el).remove();
      this.options.tabla.trigger("eliminar_fila",this.model.id);
    },    
    
    render: function() {
      var obj = this.model.toJSON();
      obj.solo_lectura = this.options.solo_lectura;
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
