// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CajaReparto = Backbone.Model.extend({
    urlRoot: "repartos/",
    defaults: {
      "pagos":[],
      "cobranzas":[],
      "gastos":[],
      "total":0,
      "efectivo_inicial":0,
      "total_gastos":0,
      "total_pagos":0,
      "total_cobranzas":0,
      "fecha":"",
      "numero":0,
      "estado":"",
      "efectivo_1":0,
      "efectivo_2":0,
      "diferencia":0,
    }
  });
	  
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.ProcesarPagosResultados = app.mixins.View.extend({

    template: _.template($("#procesar_pagos_resultados_template").html()),
      
    myEvents: {
      "click .buscar":"buscar",
      "click .confirmar":"confirmar",
      
      "click #repartos_buscar_conceptos": "abrir_busqueda_concepto",
      "keypress #repartos_concepto_codigo": function(e) { if (e.keyCode == 13) { this.buscar_concepto(); } },
      "focusout #repartos_concepto_codigo" : "buscar_concepto",
      "keypress #repartos_gasto": function(e) { if (e.keyCode == 13) { this.agregar_gasto(); } },
      "click #repartos_agregar_gasto": "agregar_gasto",
			"click .delete_gasto":"eliminar_gasto",
			"click .edit_gasto":"editar_gasto",
      
      "click #repartos_buscar_cliente": function(e) { },
      "keypress #repartos_codigo_cliente": function(e) { if (e.keyCode == 13) { this.buscar_cliente(); } },
      "keypress #repartos_cliente_total": function(e) { if (e.keyCode == 13) { this.agregar_cobranza(); } },
      "click #repartos_agregar_cobranza": "agregar_cobranza",
   			"click .delete_cobranza":"eliminar_cobranza",
			"click .edit_cobranza":"editar_cobranza",

			"change #repartos_total_efectivo_inicial":"calcular_total_caja",
      "change #repartos_efectivo_1":"calcular_total_caja",
      "change #repartos_efectivo_2":"calcular_total_caja",
    },
    
    buscar_cliente : function() {
      var self = this;
      var id_cliente = toInteger($(this.el).find("#repartos_codigo_cliente").val());
      if (id_cliente == 0) {
        show("Ingrese el numero de cliente.");
        $(this.el).find("#repartos_codigo_cliente").select();
        return;
      }
      $.ajax({
        "url":"clientes/function/get_info/"+id_cliente+"/"+ID_EMPRESA+"/",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            show("No existe un cliente con el codigo '"+id_cliente+"'");
            $("#repartos_codigo_cliente").select();
            return;
          }
          $("#repartos_cliente_nombre").val(r.nombre);
          $("#repartos_cliente_total").select();
        }
      });
    },
    
    
    abrir_busqueda_concepto : function() {
      var self = this;
      app.views.gastosTreeView = new app.views.GastosTreeView({
        "lightbox":true
      });
      crearLightboxHTML({
        "html":app.views.gastosTreeView.el,
        "width":600,
        "height":400,
        "callback":function() {
          // Ponemos el codigo en el input
          $(self.el).find("#repartos_concepto_codigo").val(window.codigo_concepto);
          // Ahora con el codigo, buscamos el concepto
          self.buscar_concepto();
          // Enviamos el foco al total
          $(self.el).find("#repartos_gasto").select();
        }
      });
    },
    
    buscar_concepto : function() {
      var self = this;
      var codigo = $(this.el).find("#repartos_concepto_codigo").val();
      if (isEmpty(codigo)) return;
      $.ajax({
        "url":"tipos_gastos/function/unique_find_by_codigo/"+codigo,
        "dataType":"json",
        "type":"post",
        "success":function(e) {
          if (e.total == "1") {
            var m = e.results[0];
						_.each(self.model.get("gastos"),function(g){
							if (m.id == g.id_concepto) {
								$(self.el).find("#repartos_gasto").val(g.total);
							}
						});
            $(self.el).find("#repartos_concepto_nombre").val(m.nombre);
            $(self.el).find("#repartos_concepto_id").val(m.id);
						$(self.el).find("#repartos_gasto").select();
          } else {
            show("No existe un concepto con el codigo ingresado.");
            $(self.el).find("#repartos_concepto_nombre").val("");
            $(self.el).find("#repartos_concepto_id").val("");
            $(self.el).find("#repartos_concepto_nombre").val("");
            $(self.el).find("#repartos_concepto_nombre").select();
          }
        }
      });
    },
    
    agregar_gasto: function() {
      var concepto = $("#repartos_concepto_nombre").val();
      var total = $("#repartos_gasto").val();
      var id_concepto = $("#repartos_concepto_id").val();
			var codigo = $("#repartos_concepto_codigo").val();
      if (isEmpty(concepto)) {
        show("Por favor ingrese un concepto.");
        $("#repartos_concepto_codigo").focus();
        return;
      }
      if (isEmpty(total) || total <= 0) {
        show("Por favor ingrese un monto.");
        $("#repartos_gasto").select();
        return;
      }
      total = Number(total).toFixed(2);
			var encontro = false;
			_.each(this.model.get("gastos"),function(g){
				if (id_concepto == g.id_concepto) {
					g.total = total;
					encontro = true;
				}
			});
			if (!encontro) {
				var gastos = this.model.get("gastos");
				gastos.push({
					"id_concepto":id_concepto,
					"concepto":concepto,
					"total":total,
					"codigo":codigo,
				});				
			}
			this.limpiar_gastos();
      this.render_gastos();
    },
    
    agregar_cobranza: function() {
      var codigo = $("#repartos_codigo_cliente").val();
      var total = toFloat($("#repartos_cliente_total").val());
      var cliente = $("#repartos_cliente_nombre").val();
      if (isEmpty(codigo)) {
        show("Por favor ingrese un cliente.");
        $("#repartos_codigo_cliente").focus();
        return;
      }
      if (total <= 0) {
        show("Por favor ingrese un monto.");
        $("#repartos_cliente_total").select();
        return;
      }
      total = Number(total).toFixed(2);
			var encontro = false;
			_.each(this.model.get("cobranzas"),function(g){
				if (codigo == g.codigo) {
					g.total = total;
					encontro = true;
				}
			});
			if (!encontro) {
				var cobranzas = this.model.get("cobranzas");
				cobranzas.push({
					"codigo":codigo,
					"cliente":cliente,
					"total":total,
				});				
			}
			this.limpiar_cobranzas();
      this.render_cobranzas();
    },
    
    
    render_gastos: function() {
			
			// Controlamos si la caja esta abierta o no
			var abierta = (this.model.get("estado") != "C");
			
			$("#repartos_concepto_codigo").prop("disabled",!abierta);
			$("#repartos_buscar_conceptos").prop("disabled",!abierta);
			$("#repartos_gasto").prop("disabled",!abierta);
			$("#repartos_agregar_gasto").prop("disabled",!abierta);
			
      var gastos = this.model.get("gastos");
			var total_gastos = 0;
      $("#repartos_tabla_gastos tbody").empty();
      for(var i=0;i<gastos.length;i++) {
        var g = gastos[i];
        var tr = "<tr>";
        tr+="<td>"+g.concepto+"</td>";
        tr+="<td>"+g.total+"</td>";
				if (abierta) {
					tr+="<td><i class='fa fa-file-text-o edit_gasto text-dark' data-id='"+g.id_concepto+"' /></td>";
					tr+="<td><i class='glyphicon glyphicon-remove delete_gasto text-danger' data-id='"+g.id_concepto+"' /></td>";					
				} else {
					tr+="<td></td>";
					tr+="<td></td>";
				}
        tr+="</tr>";
        $("#repartos_tabla_gastos tbody").append(tr);
        total_gastos += toFloat(g.total);
      }
      $("#repartos_tabla_total_gastos").html(Number(total_gastos).toFixed(2));
			$("#repartos_total_gastos").val(Number(total_gastos).toFixed(2));
			this.model.set({ "total_gastos":total_gastos });
			this.calcular_total_caja();
    },
    
    
    render_cobranzas: function() {
			
			// Controlamos si la caja esta abierta o no
			var abierta = (this.model.get("estado") != "C");
			
			$("#repartos_codigo_cliente").prop("disabled",!abierta);
			$("#repartos_cliente_total").prop("disabled",!abierta);
			$("#repartos_agregar_cobranza").prop("disabled",!abierta);
			
      var cobranzas = this.model.get("cobranzas");
			var total_cobranzas = 0;
      $("#repartos_tabla_cobranzas tbody").empty();
      for(var i=0;i<cobranzas.length;i++) {
        var g = cobranzas[i];
        var tr = "<tr>";
        tr+="<td>"+g.codigo+"</td>";
        tr+="<td>"+g.cliente+"</td>";
        tr+="<td>"+g.total+"</td>";
				if (abierta) {
					tr+="<td><i class='fa fa-file-text-o edit_cobranza text-dark' data-id='"+g.codigo+"' /></td>";
					tr+="<td><i class='glyphicon glyphicon-remove delete_cobranza text-danger' data-id='"+g.codigo+"' /></td>";					
				} else {
					tr+="<td></td>";
					tr+="<td></td>";
				}
        tr+="</tr>";
        $("#repartos_tabla_cobranzas tbody").append(tr);
        total_cobranzas += toFloat(g.total);
      }
      $("#repartos_tabla_total_cobranzas").html(Number(total_cobranzas).toFixed(2));
			$("#repartos_total_cobranzas").val(Number(total_cobranzas).toFixed(2));
			this.model.set({ "total_cobranzas":total_cobranzas });
      console.log("Render:"+this.model.get("total_cobranzas"));
			this.calcular_total_caja();
    },
    
		
		editar_gasto: function(e) {
			var id_concepto = $(e.currentTarget).data("id");
			var gasto = _.find(this.model.get("gastos"),function(g){
				return (g.id_concepto == id_concepto);
			});
			$("#repartos_concepto_codigo").val(gasto.codigo);
			$("#repartos_concepto_nombre").val(gasto.concepto);
			$("#repartos_concepto_id").val(gasto.id_concepto);
			$("#repartos_gasto").val(gasto.total);
			$("#repartos_gasto").select();			
		},
    
		editar_cobranza: function(e) {
			var codigo = $(e.currentTarget).data("id");
			var cobranza = _.find(this.model.get("cobranzas"),function(g){
				return (g.codigo == codigo);
			});
			$("#repartos_codigo_cliente").val(cobranza.codigo);
			$("#repartos_cliente_nombre").val(cobranza.cliente);
			$("#repartos_cliente_total").val(cobranza.total);
			$("#repartos_cliente_total").select();			
		},    
		
		eliminar_gasto: function(e) {
			var id_concepto = $(e.currentTarget).data("id");
			var gastos_2 = new Array();
			_.each(this.model.get("gastos"),function(g){
				if (g.id_concepto != id_concepto) gastos_2.push(g);
			});
			this.model.set({ "gastos":gastos_2 });
			this.render_gastos();
		},
    
		eliminar_cobranza: function(e) {
			var codigo = $(e.currentTarget).data("id");
			var gastos_2 = new Array();
			_.each(this.model.get("cobranzas"),function(g){
				if (g.codigo != codigo) gastos_2.push(g);
			});
			this.model.set({ "cobranzas":gastos_2 });
			this.render_cobranzas();
		},    
		
		limpiar_gastos: function() {
			$("#repartos_concepto_codigo").val("");
			$("#repartos_concepto_nombre").val("");
			$("#repartos_concepto_id").val("");
			$("#repartos_gasto").val("");
			$("#repartos_concepto_codigo").focus();
		},
    
		limpiar_cobranzas: function() {
			$("#repartos_codigo_cliente").val("");
			$("#repartos_cliente_nombre").val("");
			$("#repartos_cliente_total").val("");
			$("#repartos_codigo_cliente").focus();
		},    
	
    initialize: function() {
      var self = this;
      _.bindAll(this);
			$(this.el).html(this.template(this.model.toJSON()));
      
      createdatepicker($(this.el).find("#procesar_pagos_fecha"),new Date());
      
      $(this.el).find("#procesar_pagos_numero").TouchSpin({
        verticalbuttons: true,
        min: 0,
      });
    },
    
    buscar: function() {
      var self = this;
      var fecha = $("#procesar_pagos_fecha").val();
      if (isEmpty(fecha)) { show("Por favor seleccione una fecha"); return; }
      fecha = fecha.replace(/\//g,"-");
      var numero = $("#procesar_pagos_numero").val();
      var id_punto_venta = this.$("#procesar_pagos_puntos_venta").val();
	    $.ajax({
        "url":"repartos/function/get/"+fecha+"/"+numero+"/"+id_punto_venta,
        "dataType":"json",
        "success":function(r) {
          self.model.set(r);
          self.render();
        }
      });
    },
    
    render : function () {
      this.total_facturacion = 0;
      this.id_cliente = -1;
      this.total_cliente = 0;
			this.total_cliente_pago = 0;
			
			var abierta = (this.model.get("estado") != "C");
			$("#repartos_total_efectivo_inicial").prop("disabled",!abierta);
      $("#repartos_efectivo_1").prop("disabled",!abierta);
      $("#repartos_efectivo_2").prop("disabled",!abierta);
			$(".confirmar").prop("disabled",!abierta);
			
			$("#repartos_total_efectivo_inicial").val(this.model.get("efectivo_inicial"));
      $("#repartos_efectivo_1").val(this.model.get("efectivo_1"));
      $("#repartos_efectivo_2").val(this.model.get("efectivo_2"));
      $("#repartos_diferencia").val(this.model.get("diferencia"));
      
      $(this.el).find("#procesar_pagos_tabla .tbody").empty();
      if (this.model.get("pagos").length == 0) {
        $(this.el).find("#procesar_pagos_tabla .tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");
      } else {
        var pagos = this.model.get("pagos");
        for(var i=0;i<pagos.length;i++) {
          this.addOne(pagos[i]);
        }
      }
      
      
			$(this.el).find("#repartos_codigo_cliente").autocomplete({
				"source":"clientes/function/get_by_nombre/",
				"minLength":3,
				"select":function(event,ui) {
					$("#repartos_codigo_cliente").val(ui.item.value);
          $("#repartos_cliente_nombre").val(ui.item.nombre);
				}
			});      
      
      
      // Tiramos el total del ultimo cliente
      if (this.id_cliente != -1) {
        var tr = '<tr class="active">';
        tr+='<td></td>';
        tr+='<td class="col-xs-0"></td>';
        tr+='<td class="col-xs-0"></td>';
        tr+='<td class="col-xxs-0"></td>';
        tr+='<td class="col-xs-0"></td>';
        tr+='<td class="col-xs-0"></td>';
        tr+='<td class="tar bold">'+Number(this.total_cliente).toFixed(2)+'</td>';
        tr+='<td class="tac"><input type="text" value="'+Number(this.total_cliente_pago).toFixed(2)+'" id="total_'+this.id_cliente+'" class="form-control total_cliente bold tar" disabled /></td>';
        tr+='</tr>';
        $(this.el).find(".tbody").append(tr);
      }
      
      $("#reparto_total_facturacion").html("$ "+Number(this.total_facturacion).toFixed(2));
      this.calcular_efectivo();
      
      // Renderizamos la tabla de gastos
      this.render_gastos();
    },
    
    addOne : function ( item ) {
      var self = this; 
      if (this.id_cliente != item.id_cliente) {
        
        // Tiramos el total
        if (this.id_cliente != -1) {
          var tr = '<tr class="active">';
          tr+='<td></td>';
          tr+='<td class="col-xs-0"></td>';
          tr+='<td class="col-xs-0"></td>';
          tr+='<td class="col-xxs-0"></td>';
          tr+='<td class="col-xs-0"></td>';
          tr+='<td class="col-xs-0"></td>';
          tr+='<td class="tar bold">'+Number(this.total_cliente).toFixed(2)+'</td>';
          tr+='<td class="tac"><input type="text" class="form-control total_cliente bold tar" value="'+Number(this.total_cliente_pago).toFixed(2)+'" id="total_'+this.id_cliente+'" disabled /></td>';
          tr+='</tr>';
          $(this.el).find(".tbody").append(tr);
          this.total_cliente = 0;
					this.total_cliente_pago = 0;
        }
        
        // Ponemos el encabezado del nuevo cliente
        var tr = '<tr>';
        //tr+='<td><label class="i-checks m-b-none"><input tabindex="-1" type="checkbox"><i></i></label></td>';
				tr+="<td></td>";
        tr+='<td colspan="20" class="bold">'+item.cliente+'</td>';
        tr+='</tr>';
        $(this.el).find(".tbody").append(tr);
        this.id_cliente = item.id_cliente;
      }
      
			this.total_cliente += Number(item.total);
			
			if (this.model.get("estado") == "C") {
				this.total_cliente_pago += Number(item.pago);	
			} else {
				this.total_cliente_pago += Number(item.total);	
			}
      
      this.total_facturacion += Number(item.total);
      
			item.estado = this.model.get("estado");
      var modelo = Backbone.Model.extend({
        defaults: item,
      });
      var view = new app.views.ProcesarPagosItemResultados({
        model: new modelo(),
        resultados: self
      });
      $(this.el).find("#procesar_pagos_tabla .tbody").append(view.render().el);
    },
    
    confirmar: function() {
      var self = this;
      if (!confirmar("Desea confirmar los pagos?")) return;
      
      this.calcular_efectivo();
      
      var facturas = new Array();
      // Recorremos todo el efectivo, y formamos el array que luego se persiste
      $(".efectivo").each(function(i,e){
        var id_cliente = $(e).data("id_cliente");
        var id_factura = $(e).data("id_factura");
        var id_empresa = $(e).data("id_empresa");
        var efectivo = toFloat($(e).val());
        var pagada = $(e).parent().parent().find("input[type=checkbox]").is(":checked") ? 1 : 0;
        facturas.push({
          "id_factura":id_factura,
          "id_cliente":id_cliente,
          "id_empresa":id_empresa,
          "efectivo":efectivo,
          "pagada":pagada,
        });
      });
      
      var fecha = $("#procesar_pagos_fecha").val();
      var numero = $("#procesar_pagos_numero").val();
      
      $.ajax({
        "url":"repartos/function/guardar_procesar_pagos/",
        "dataType":"json",
        "type":"post",
        "data": {
          "facturas":JSON.stringify(facturas),
					"gastos":JSON.stringify(self.model.get("gastos")),
          "cobranzas":JSON.stringify(self.model.get("cobranzas")),
          "fecha":fecha,
          "numero":numero,
					"efectivo_inicial":self.model.get("efectivo_inicial"),
          "efectivo_1":self.model.get("efectivo_1"),
          "efectivo_2":self.model.get("efectivo_2"),
          "diferencia":self.model.get("diferencia"),
					"total_pagos":self.model.get("total_pagos"),
					"total_gastos":self.model.get("total_gastos"),
          "total_cobranzas":self.model.get("total_cobranzas"),
					"total":self.model.get("total"),
          "id_usuario":ID_USUARIO,
        },
        "success":function(r){
          if (r.error == 0) {
            show("La caja de reparto se ha guardado correctamente.");
            window.location.reload();
          } else {
						show("Ocurrio un error al guardar.");
					}
        }
      });
    },
    
    calcular_efectivo: function() {
      var total = 0;
      $(".efectivo").each(function(i,e){
        var v = toFloat($(e).val());
        total += ((isNaN(v)) ? 0 : v);
      });
			total = Number(total).toFixed(2);
			this.model.set({ "total_pagos":total });
      $("#reparto_total_efectivo").html("$ "+total);
			$("#repartos_total_pagos").val(total);
			this.calcular_total_caja();
    },
		
		calcular_total_caja: function() {
			
      var inicial = toFloat($("#repartos_total_efectivo_inicial").val());
      var total_pagos = toFloat(this.model.get("total_pagos"));
      var total_cobranzas = toFloat(this.model.get("total_cobranzas"));
      var total_gastos = toFloat(this.model.get("total_gastos"));
			var total = inicial + total_pagos + total_cobranzas - total_gastos;
      
      var efectivo_1 = toFloat($("#repartos_efectivo_1").val());
      var efectivo_2 = toFloat($("#repartos_efectivo_2").val());
      var diferencia = (efectivo_1 + efectivo_2) - total;
      $("#repartos_diferencia").val(Number(diferencia).toFixed(2));
      
			this.model.set({
        "efectivo_1":efectivo_1,
        "efectivo_2":efectivo_2,
        "diferencia":diferencia,
				"efectivo_inicial":inicial,
				"total":total
			});
			$("#repartos_total").val(Number(total).toFixed(2));
		},
	
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ProcesarPagosItemResultados = Backbone.View.extend({
    
    template: _.template($("#procesar_pagos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "change .efectivo":function() {
        var id_cliente = this.model.get("id_cliente");
        var total = 0;
        $(".cliente_"+id_cliente).each(function(i,e){
          var v = toFloat($(e).val());
          total += (isNaN(v) ? 0 : v);
        });
        $("#total_"+id_cliente).val(Number(total).toFixed(2));
        this.options.resultados.calcular_efectivo();
      },
			"change input[type=checkbox]":function(e) {
				var ef = $(e.currentTarget).parents("tr").find(".efectivo");
				if ($(e.currentTarget).is(":checked")) {
					$(ef).val(Number(this.model.get("total")).toFixed(2));	
				} else {
					$(ef).val("0.00");	
				}
				$(ef).change();
			},
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
