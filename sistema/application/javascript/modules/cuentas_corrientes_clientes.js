// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CuentasCorrientesClientes = Backbone.Model.extend({
    urlRoot: function() {
      var s = "clientes/function/cuentas_corrientes";
      s=s+"/"+this.get("fecha_desde");
      s=s+"/"+this.get("fecha_hasta");
      s=s+"/"+this.get("codigo");
      s=s+"/"+this.get("id_empresa");
      s=s+"/"+this.get("id_cliente");
      s=s+"/"+this.get("id_sucursal");
      s=s+"/"+this.get("moneda");
      return s;
    },
    defaults: {
      "fecha_desde": 0,
      "fecha_hasta": 0,
      "codigo": 0,
      "id_empresa": ID_EMPRESA,
      "id_cliente": "",
      "id_sucursal": 0,
      "moneda": "ARS",
      "datos": new Array()
    },
  });
	  
})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesClientesResultados = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_clientes_resultados_template").html()),
      
    myEvents: {
      "click .agregar_recibo": "agregar_recibo",
      "click #checkTodos": "seleccionar_todos",
      "click .buscar":"buscar",
      "click #cuentas_corrientes_clientes_buscar_cliente":"ver_buscar_cliente",
      "click .exportar":"exportar",
      "click .reasignar_pagos":"reasignar_pagos",
      "click .imprimir_resumen":function() {
        this.imprimir_resumen(0);
      },
      "click .imprimir_resumen_detalle":function() {
        this.imprimir_resumen(1);
      },
      "keypress #cuentas_corrientes_clientes_codigo":function(e) {
        if (e.which == 13) { this.buscar_cliente(); }
      },      
      "click #cuentas_corrientes_clientes_datos_email":function(e) {
        var email = $(e.currentTarget).text();
        if (!isEmpty(email)) {
        workspace.nuevo_email(new app.models.Consulta({
          "email":email,
        }));
        }
      }
    },
      
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
      this.bind("actualizar",this.mostrar_resultados);
      
      // Si tenemos que mostrar la cuenta de un cliente especifico
      if (this.model.get("id_cliente") != 0) {
        this.buscar_cliente_por_id(this.model.get("id_cliente"));
      }
    },

    render: function() {
      this.model.set({"active": "cuentas_corrientes_clientes"});
      $(this.el).html(this.template(this.model.toJSON()));
      var self = this;
      
      var input = this.$("#cuentas_corrientes_clientes_codigo");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":null,
        "onSelect":function(item){
          $.ajax({
            "url":"clientes/"+item.id+"?id_sucursal="+item.id_sucursal,
            "dataType":"json",
            "type":"get",
            "success":function(r) {
              var cliente = new app.models.Cliente(r);
              self.seleccionar_cliente(cliente);
            }
          });
        }
      });      
      
      // Toma un mes anterior, el mes actual, y el mes siguiente
      var fecha_desde = new Date();
      var y = fecha_desde.getFullYear();
      var m = fecha_desde.getMonth();
      fecha_desde = new Date(y-1, m, 1);
      fecha_hasta = new Date(y, m + 1, 0);						
      
      createdatepicker($(this.el).find("#cuentas_corrientes_clientes_desde"),fecha_desde);
      createdatepicker($(this.el).find("#cuentas_corrientes_clientes_hasta"),fecha_hasta);      
      
      return this;
    },
    
    ver_buscar_cliente: function() {
      var self = this;
      var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
        collection: clientes,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarClientes.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
        }
      });
      $(".search_input").select();
    },

    buscar_cliente : function() {
      var self = this;
      var codigo = this.$("#cuentas_corrientes_clientes_codigo").val();

      // Buscamos el cliente por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
      codigo = parseInt(codigo);
      if (!isNaN(codigo)) {
        $.ajax({
          "url":"clientes/function/get_by_codigo/",
          "data":{
            "codigo":codigo,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.length == 0) {
              show("No existe un cliente con el codigo: '"+codigo+"'");
              self.$("#cuentas_corrientes_clientes_codigo").select();
              return;
            }
            var cliente = new app.models.Cliente(r);
            self.seleccionar_cliente(cliente);
          }
        });
      }
    },
    
    buscar_cliente_por_id: function(id) {
      var self = this;
      var cliente = new app.models.Cliente({"id":id});
      cliente.fetch({
        "success":function() {
          self.seleccionar_cliente(cliente);    
        },
        "error":function() {
          show("No existe un cliente con el ID: '"+id+"'");
          self.$("#cuentas_corrientes_clientes_codigo").select();
          return;
        }
      });
    },
    
    seleccionar_cliente: function(c) {
      var self = this;
      this.$("#cuentas_corrientes_clientes_datos_nombre").text(c.get("nombre"));
      this.$("#cuentas_corrientes_clientes_datos_direccion").text(c.get("direccion")+" "+c.get("localidad"));
      this.$("#cuentas_corrientes_clientes_datos_telefono").text(c.get("telefono"));
      this.$("#cuentas_corrientes_clientes_datos_email").text(c.get("email"));
      this.$("#cuentas_corrientes_clientes_datos_cuit").text(c.get("cuit"));
      if (ID_EMPRESA != 287) this.$("#cuentas_corrientes_clientes_datos_id_sucursal").val(c.get("id_sucursal"));
      this.$("#cuentas_corrientes_clientes_datos_observaciones").text(c.get("observaciones"));
      var id_tipo_iva = c.get("id_tipo_iva");
      if (id_tipo_iva == 1) this.$("#cuentas_corrientes_clientes_datos_iva").html("Responsable Inscripto");
      else if (id_tipo_iva == 2) this.$("#cuentas_corrientes_clientes_datos_iva").html("Monotributo");
      else if (id_tipo_iva == 3) this.$("#cuentas_corrientes_clientes_datos_iva").html("Exento");
      else if (id_tipo_iva == 4) this.$("#cuentas_corrientes_clientes_datos_iva").html("Consumidor Final");
      this.model.set({ "id_cliente":c.id });
      this.buscar();

      self.$('#cuentas_corrientes_clientes_codigo').val(c.get("nombre"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#cuentas_corrientes_clientes_codigo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    imprimir_resumen: function(con_detalle) {
      var self = this;
      var fecha_desde = $(this.el).find("#cuentas_corrientes_clientes_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#cuentas_corrientes_clientes_hasta").val().replace(/\//g,"-");

      var id_cliente = this.model.get("id_cliente");
      if (id_cliente == 0 || id_cliente == null) {
        show("Por favor seleccione un cliente");
        $(this.el).find("#cuentas_corrientes_clientes_codigo").focus();
        return;
      }
      if (isEmpty(fecha_desde)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_desde").focus();
        return;        
      }
      if (isEmpty(fecha_hasta)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_hasta").focus();
        return;        
      }
      var obj = {
        "detalle_items":con_detalle,
        "fecha_desde":fecha_desde,
        "fecha_hasta":fecha_hasta,
        "id_cliente":id_cliente,
        "id_sucursal":self.$("#cuentas_corrientes_clientes_datos_id_sucursal").val(),
        "moneda":((self.$("#cuentas_corrientes_clientes_moneda").length > 0) ? self.$("#cuentas_corrientes_clientes_moneda").val() : "ARS"),
      };
      var url = "clientes/function/imprimir_detalle_cuentas_corrientes/?"+$.param(obj);
      workspace.imprimir_reporte(url);
    },
    
    buscar : function() {
	  
      var self = this;
      var fecha_desde = $(this.el).find("#cuentas_corrientes_clientes_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#cuentas_corrientes_clientes_hasta").val().replace(/\//g,"-");

      var id_cliente = this.model.get("id_cliente");
      if (id_cliente == 0 || id_cliente == null) {
        show("Por favor seleccione un cliente");
        $(this.el).find("#cuentas_corrientes_clientes_codigo").focus();
        return;
      }
      
      if (isEmpty(fecha_desde)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_desde").focus();
        return;        
      }
      if (isEmpty(fecha_hasta)) {
        show("Por favor seleccione una fecha");
        $(this.el).find(".fecha_hasta").focus();
        return;        
      }            
      this.model.set({
        "fecha_desde": fecha_desde,
        "fecha_hasta": fecha_hasta,
        "codigo": 0,
        "id_sucursal":self.$("#cuentas_corrientes_clientes_datos_id_sucursal").val(),
      });
      if (this.$("#cuentas_corrientes_clientes_moneda").length > 0) {
        this.model.set({
          "moneda":self.$("#cuentas_corrientes_clientes_moneda").val()
        }) 
      }
      this.model.fetch({
        "success":function(modelo) {
          self.mostrar_resultados(modelo);
        }
      });
    },
    
      
    seleccionar_todos : function(e) {
      var checked = $(e.currentTarget).is(":checked");
      if (checked) {
      $(this.el).find(".tbody .fila_roja .checkbox").parents("tr").addClass("seleccionado");
      } else {
        $(this.el).find(".tbody .fila_roja .checkbox").parents("tr").removeClass("seleccionado");
      }
      $(this.el).find(".tbody .fila_roja .checkbox").attr("checked",checked);
    },
    
    exportar : function() {
      
      var self = this;
      var id_cliente = this.model.get("id_cliente");
      if (id_cliente == 0 || id_cliente == null) {
        show("Por favor seleccione un cliente");
        $(this.el).find("#cuentas_corrientes_clientes_codigo").focus();
        return;
      }

      var nombre = $("#cuentas_corrientes_clientes_datos_nombre").text();
      var desde = $("#cuentas_corrientes_clientes_desde").val();
      var hasta = $("#cuentas_corrientes_clientes_hasta").val();
      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "fecha":$.trim($(e).find("td:eq(1)").html()),
          "comprobante":$.trim($(e).find("td:eq(2)").html()),
          "numero":$.trim($(e).find("td:eq(3) span").html()),
          "debe":$(e).find("td:eq(4)").html(),
          "haber":$(e).find("td:eq(5)").html(),
          "saldo":$(e).find("td:eq(6)").html(),
        });
      });
      var header = new Array("Fecha","Comprobante","Numero","Debe","Haber","Saldo");
      this.exportar_excel({
        "filename":nombre,
        "title":"Resumen de Cuenta: "+nombre,
        "date":desde+" - "+hasta,
        "data":array,
        "header":header,
      });    
    },	
    
      
    /**
     * MOSTRAMOS LOS RESULTADOS EN LA TABLA
     *
     */
    mostrar_resultados: function(model) {
      
      // Limpiamos la tabla
      $(this.el).find(".tbody").empty();
      
      this.comprobantes = new Array();
      var saldo = 0;
      var debe = 0;
      var haber = 0;
      var saldoParcial = 0;
      
      var length = model.get("datos").length;
          
      // CARGAMOS EL SALDO INICIAL EN LA PRIMERA FILA
      var saldoParcial = model.get("saldo_inicial");
      var Item = Backbone.Model.extend({
        defaults: {
          "id":0,
          "id_punto_venta":0,
          "mostrar_checkbox": false,
          "fecha": "Saldo Inicial",
          "comprobante": "",
          "tipo":"",
          "pagada":1,
          "tipo_pago":"",
          "tipo_punto_venta": "",
          "tipo_comprobante": "",
          "observaciones":"",
          "anulada": 0,
          "debe": 0,
          "haber": 0,
          "total":0,
          "pago":0,
          "saldo": saldoParcial,
          "progreso":0,
          "total_pagado":0,
          "negativo":0,
        },
      });
      var item = new app.views.CuentasCorrientesClientesItemResultados({
        model: new Item()
      });
      // La agregamos a la tabla
      $(this.el).find(".tbody").append(item.el);
          
      // Recorremos los resultados
      for(i=0;i<length;i++) {
        var m = model.get("datos")[i];
        
        var total = parseFloat(m.total);
        var totalComprobante = total;   
        var pago = parseFloat(m.pago);
        var progreso = 0;
        
        // El pago debe ser solamente para los recibos, sino se descuenta dos veces
        //if (m.id_tipo_comprobante != 0) pago = 0;        
        
        if (m.negativo == 1) { // Nota de Credito
          // Invertimos los valores
          var aux = total;
          total = pago;
          pago = -aux;
          
        } else if (m.negativo == 0 && total < 0) {
          // Remito negativo
          var aux = pago;
          pago = total;
          total = aux;
        }
        
        //var pagoFactura = pago;        
        if (total < 0) {
          haber = Math.abs(total);
        } else {
          debe = total;
        }
        
        if (pago > 0) {
          debe += pago;
        } else {
          haber = Math.abs(pago);
        }        
        
        // Si la factura esta anulada, no se cuenta NADA
        if (m.anulada == 1) {
          debe = haber;
        } else {
          saldoParcial = parseFloat(saldoParcial) + debe - haber;
        }
        
        if (m.id_tipo_comprobante != 0) {
          progreso = (totalComprobante>0) ? (Math.abs(m.total_pagado) + Math.abs(m.pago)) / Math.abs(totalComprobante) * 100 : 0;
        }
        
        // Creamos una fila nueva
        var Item = Backbone.Model.extend({
          defaults: {
            "id":m.id,
            "fecha": m.fecha,
            "comprobante": m.comprobante,
            "nombre": m.nombre,
            "anulada": m.anulada,
            "pagada": m.pagada,
            "tipo_pago": m.tipo_pago,
            "observaciones": m.observaciones,
            "debe": debe,
            "haber": haber,
            "tipo_punto_venta": m.tipo_punto_venta,
            "saldo": saldoParcial,
            "tipo": m.tipo, // INDICA SI ES PAGO O NO
            "tipo_comprobante": m.tipo_comprobante,
            "total":Math.abs(total),
            "pago":Math.abs(pago),
            "progreso":progreso,
            "negativo":m.negativo,
            "total_pagado":m.total_pagado,
            "id_punto_venta":m.id_punto_venta,
          }
        });
        var modelo = new Item();
        this.comprobantes.push(modelo);
        
        var item = new app.views.CuentasCorrientesClientesItemResultados({
          model: modelo
        });
        
        // La agregamos a la tabla
        $(this.el).find(".tbody").append(item.el);
      }

      $('[data-toggle="tooltip"]').tooltip();
    },

    reasignar_pagos: function() {
      var comprobantes = new Array();
      this.$(".table tbody .check-row:checked").each(function(i,e){
        var id = $(e).val();
        var comprobante = _.find(self.comprobantes,function(c){
          return (c.id == id);
        });
        comprobantes.push(comprobante.toJSON());
      });
      var view = new app.views.ReasignarPagosView({
        model: new app.models.AbstractModel({
          "comprobantes": comprobantes,
        })
      });
      // Abrimos el lightbox de pagos
      crearLightboxHTML({
        "html":view.el,
        "width":600,
        "height":500,
        "escapable":false,
      });    
    },
          
    agregar_recibo : function() {
      var self = this;
      var id_cliente = self.model.get("id_cliente");
      if (id_cliente == undefined || id_cliente == 0) {
        alert("Por favor seleccione un cliente.");
        this.$("#cuentas_corrientes_clientes_codigo").focus();
        return;
      }
      
      var comprobantes = new Array();
      this.$(".table tbody .check-row:checked").each(function(i,e){
        var id = $(e).val();
        var comprobante = _.find(self.comprobantes,function(c){
          return (c.id == id);
        });
        comprobantes.push(comprobante.toJSON());
      });
      
      var reciboCliente = new app.models.ReciboCliente({
        "cotizacion_dolar":COTIZACION_DOLAR,
        "id_empresa":ID_EMPRESA,
        "id_sucursal":ID_SUCURSAL,
				"id_cliente":id_cliente,
        "id_usuario":ID_USUARIO,
				"cheques": [],
        "depositos": [],
        "tarjetas": [],
        "movimientos_efectivo":[],
				"comprobantes": comprobantes,
      });
      app.views.reciboClientes = new app.views.ReciboClientes({
        model: reciboCliente
      });
      
      // Abrimos el lightbox de pagos
      crearLightboxHTML({
        "html":app.views.reciboClientes.el,
        "width":900,
        "height":500,
        "escapable":false,
      });
    },    
		
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesClientesItemResultados = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_clientes_item_resultados_template").html()),
    
    tagName: "tr",
		
    myEvents: {
      "click .delete":"borrar",
      "click .anular":"anular",
      "click .edit":"editar",
      "click .ver_recibo":"ver_recibo",
      "click .checkbox":"seleccionar",
      "click .imprimir":function() {
        if (this.model.get("tipo") == "P") {
          workspace.imprimir_reporte("recibos/function/imprimir_recibo/"+this.model.id+"/"+this.model.get("id_punto_venta"));
        } else {
          /*if (this.model.get("tipo_comprobante")=="Remito") {
            workspace.imprimir_remito(this.model.id,this.model.get("id_punto_venta"));
          } else {*/
            workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));
          //}
        }
      }      
    },
    
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
    },
    
    anular: function() {
      if (confirmar("Realmente desea anular este comprobante?")) {
        // Se debe ANULAR, NO BORRAR
        $.ajax({
          "url":"facturas/function/anular/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.cuentas_corrientes_clientesResultados.buscar();
          }
        });                      
      }
    },    
      
    borrar : function() {
      if (confirmar("Realmente desea eliminar este comprobante?")) {
        var self = this;

        // Si es un pago
        if (this.model.get("tipo") == "P") {
          var url = "recibos/function/borrar_recibo/"+this.model.id+"/"+this.model.get("id_punto_venta");
          $.ajax({
            "url":url,
            "dataType": "json",
            "success": function() {
              show("El comprobante ha sido eliminado exitosamente.");
              app.views.cuentas_corrientes_clientesResultados.buscar();
            },
            "error" : function() {
              show("Error al eliminar el comprobante.");
            }
          });
          
        // Si es un REMITO
        } else if (this.model.get("estado") == 1) {
          // Se elimina directamente
					$.ajax({
            "url":"facturas/function/borrar_factura/"+self.model.id+"/"+self.model.get("id_punto_venta"),
						"dataType":"json",
						"success":function(r){
							app.views.cuentas_corrientes_clientesResultados.buscar();
						}
					});          

        // Sino, es una FA, FB, NC, ND
        } else {
          $.ajax({
            "url":"facturas/function/borrar_factura/"+self.model.id+"/"+self.model.get("id_punto_venta"),
            "dataType":"json",
            "success":function(r){
              app.views.cuentas_corrientes_clientesResultados.buscar();
            }
          });          
        }
      }
    },
      
    editar : function() {
      /*if (this.model.get("tipo_comprobante")=="Remito") {
        window.open("app/#remitos/"+this.model.id,"_blank");  
      } else {*/
        window.open("app/#comprobante/"+this.model.id+"/"+this.model.get("id_punto_venta"),"_blank");
      //}
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
    
    ver_recibo: function(e) {
      var reciboCliente = new app.models.ReciboCliente({
        "id":$(e.currentTarget).data("id")
      });
      reciboCliente.fetch({
        "success":function(modelo){
          app.views.reciboClientes = new app.views.ReciboClientes({
            model: modelo
          });
          crearLightboxHTML({
            "html":app.views.reciboClientes.el,
            "width":900,
            "height":500,
            "escapable":false,
          });                
        }
      });
    },
  });
})(app);







// -----------------------------------------
//   LISTADO DE ORDENES DE PAGO
// -----------------------------------------


(function (collections, model, paginator) {

  collections.RecibosClientes = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },
    paginator_core: {
      url: "recibos/function/consulta/",
    },
  });

})( app.collections, app.models.ReciboCliente, Backbone.Paginator);



(function ( app ) {

  app.views.RecibosClientesListadoView = app.mixins.View.extend({

    template: _.template($("#recibos_clientes_listado_template").html()),
    
    myEvents: {
      "click .exportar_excel":"exportar",
      "click .buscar": "buscar",
      "keypress #recibos_clientes_listado_buscar":function(e) {
        if (e.which == 13) this.buscar();
      },
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.recibos_clientes_listado_fecha_desde = (typeof window.recibos_clientes_listado_fecha_desde != "undefined") ? window.recibos_clientes_listado_fecha_desde : this.fecha;
      window.recibos_clientes_listado_fecha_hasta = (typeof window.recibos_clientes_listado_fecha_hasta != "undefined") ? window.recibos_clientes_listado_fecha_hasta : this.fecha;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;            

      $(this.el).html(this.template({
        "permiso":self.permiso,
        "seleccionar":self.habilitar_seleccion,
        "active":"recibos_clientes",
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

      createdatepicker(this.$("#recibos_clientes_desde"),window.recibos_clientes_listado_fecha_desde);
      createdatepicker(this.$("#recibos_clientes_hasta"),window.recibos_clientes_listado_fecha_hasta);
      
      this.buscar();
    },

    exportar: function() {
      var self = this;
      var nombre = "Recibos de Clientes";
      var desde = $("#recibos_clientes_desde").val();
      var hasta = $("#recibos_clientes_hasta").val();
      var array = new Array();
      this.$(".table tbody tr").each(function(i,e){
        array.push({
          "sucursal":$.trim($(e).find("td:eq(1)").data("exp")),
          "fecha":$.trim($(e).find("td:eq(2)").data("exp")),
          "cliente":$.trim($(e).find("td:eq(3)").data("exp")),
          "comprobante":$(e).find("td:eq(4)").data("exp"),
          "efectivo":$(e).find("td:eq(5)").data("exp"),
          "tarjetas":$(e).find("td:eq(6)").data("exp"),
          "cheques":$(e).find("td:eq(7)").data("exp"),
          "depositos":$(e).find("td:eq(8)").data("exp"),
          "descuento":$(e).find("td:eq(9)").data("exp"),
          "retenciones":$(e).find("td:eq(10)").data("exp"),
          "total":$(e).find("td:eq(11)").data("exp"),
        });
      });
      var header = new Array("Sucursal","Fecha","Cliente","Comprobante","Efectivo","Tarjetas","Cheques","Depositos","Descuento","Retenciones","Total");
      this.exportar_excel({
        "filename":nombre,
        "title":nombre,
        "date":desde+" - "+hasta,
        "data":array,
        "header":header,
      });   
    },

    buscar : function() {

      var self = this;
      var cambio_parametros = false;
      filtros = {};
      filtros.filter = $(this.el).find("#recibos_clientes_listado_buscar").val();
      
      filtros.id_sucursal = 0;
      if (this.$("#recibos_clientes_sucursales").length > 0) {
        filtros.id_sucursal = this.$("#recibos_clientes_sucursales").val();
      }

      if (this.$("#recibos_clientes_desde").length > 0 && window.recibos_clientes_listado_fecha_desde != this.$("#recibos_clientes_desde").val().trim()) {
        window.recibos_clientes_listado_fecha_desde = this.$("#recibos_clientes_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#recibos_clientes_hasta").length > 0 && window.recibos_clientes_listado_fecha_hasta != this.$("#recibos_clientes_hasta").val().trim()) {
        window.recibos_clientes_listado_fecha_hasta = this.$("#recibos_clientes_hasta").val().trim();
        cambio_parametros = true;
      }

      filtros.desde = (isEmpty(window.recibos_clientes_listado_fecha_desde)) ? "" : window.recibos_clientes_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.recibos_clientes_listado_fecha_hasta)) ? "" : window.recibos_clientes_listado_fecha_hasta.replace(/\//g,"-");

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
      var efectivo = 0;
      var tarjeta = 0;
      var cheque = 0;
      var cantidad = 0;
      this.$("#recibos_clientes_tabla tbody").empty();
      this.collection.each(function(i){
        self.addOne(i);
        total += (parseFloat(i.get("pago")) * -1);
        efectivo += parseFloat(i.get("efectivo"));
        tarjeta += parseFloat(i.get("total_tarjetas"));
        cheque += parseFloat(i.get("total_cheques"));
        cantidad++;
      });

      // Agregamos una fila al final
      if (this.usa_filtros) {
        this.$(".pagination_container").hide();
        this.$("#recibos_clientes_resumen_total").html("$ "+Number(total).toFixed(2));
        this.$("#recibos_clientes_resumen_efectivo").html("$ "+Number(efectivo).toFixed(2));
        this.$("#recibos_clientes_resumen_tarjeta").html("$ "+Number(tarjeta).toFixed(2));
        this.$("#recibos_clientes_resumen_cantidad").html(cantidad);
        this.$(".resumen").show();
      } else {
        this.$(".resumen").hide();
      }
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var view = new app.views.RecibosClientesItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#recibos_clientes_tabla tbody").append(view.render().el);
    },

  });

})(app);

(function ( app ) {
  app.views.RecibosClientesItemResultados = app.mixins.View.extend({

    template: _.template($("#recibos_clientes_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .edit":"editar",
      "click .checkbox":"seleccionar",
      "click .ver_cta_cte":"ver_cta_cte",
    },
    ver_cta_cte: function() {
      var url = "app/#cuentas_corrientes_clientes/"+this.model.get("id_cliente");
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
      var id = this.model.id;
      var reciboCliente = new app.models.ReciboCliente({
        "id":id,
      });
      reciboCliente.fetch({
        "success":function(modelo){
          app.views.reciboClientes = new app.views.ReciboClientes({
            model: modelo
          });
          crearLightboxHTML({
            "html":app.views.reciboClientes.el,
            "width":900,
            "height":500,
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
