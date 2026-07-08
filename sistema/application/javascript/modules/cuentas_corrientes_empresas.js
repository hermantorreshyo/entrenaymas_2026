// -----------
//   MODELO
// -----------

(function ( models ) {

  models.CuentasCorrientesEmpresas = Backbone.Model.extend({
    urlRoot: function() {
      var s = "empresas/function/cuentas_corrientes";
      s=s+"/"+this.get("fecha_desde");
      s=s+"/"+this.get("fecha_hasta");
      s=s+"/"+this.get("codigo");
      s=s+"/"+this.get("id_empresa");
      s=s+"/"+this.get("id_empresa");
      return s;
    },
    defaults: {
      "fecha_desde": 0,
      "fecha_hasta": 0,
      "codigo": 0,
      "id_empresa": 0,
      "id_empresa": "",
      "datos": new Array()
    },
  });

})( app.models );


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesEmpresasResultados = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_empresas_resultados_template").html()),

    myEvents: {
      "click .agregar_recibo": "agregar_recibo",
      "click #checkTodos": "seleccionar_todos",
      "click .buscar":"buscar",
      "click #cuentas_corrientes_empresas_buscar_empresa":"ver_buscar_empresa",
      "click .exportar":"exportar",
      "keypress #cuentas_corrientes_empresas_codigo":function(e) {
        if (e.which == 13) { this.buscar_empresa(); }
      },            
      "click #cuentas_corrientes_empresas_datos_email":function(e) {
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

      // Si tenemos que mostrar la cuenta de un empresa especifico
      if (this.model.get("id_empresa") != 0) {
        this.buscar_empresa_por_id(this.model.get("id_empresa"));
      }
    },

    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      var self = this;
      
      var input = this.$("#cuentas_corrientes_empresas_codigo");
      $(input).customcomplete({
        "url":"empresas/function/get_by_nombre/",
        "form":null,
        "onSelect":function(item){
          var empresa = new app.models.Empresa({"id":item.id});
          empresa.fetch({
            "success":function(){
              self.seleccionar_empresa(empresa);
            },
          });
        }
      });            
      
      // Toma un mes anterior, el mes actual, y el mes siguiente
      var fecha_desde = new Date();
      var y = fecha_desde.getFullYear(), m = fecha_desde.getMonth();
      fecha_desde = new Date(y, m, 1);
      fecha_hasta = new Date(y, m + 1, 0);						
      
      createdatepicker($(this.el).find("#cuentas_corrientes_empresas_desde"),fecha_desde);
      createdatepicker($(this.el).find("#cuentas_corrientes_empresas_hasta"),fecha_hasta);            
      
      return this;
    },

    ver_buscar_empresa: function() {
      var self = this;
      var empresas = new app.collections.Empresas();
      app.views.buscarEmpresas = new app.views.EmpresasTableView({
        collection: empresas,
        habilitar_seleccion: true,
      });
      var d = $("<div/>").append(app.views.buscarEmpresas.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_empresa_seleccionado != undefined && window.codigo_empresa_seleccionado != -1) {
            self.seleccionar_empresa(window.empresa_seleccionado);
          }
        }
      });
      $(".search_input").select();
    },

    buscar_empresa : function() {
      var self = this;
      var codigo = this.$("#cuentas_corrientes_empresas_codigo").val();

      // Buscamos el empresa por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
      codigo = parseInt(codigo);
      if (!isNaN(codigo)) {
        $.ajax({
          "url":"empresas/function/get_by_codigo/",
          "data":{
            "codigo":codigo,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.length == 0) {
              show("No existe un empresa con el codigo: '"+codigo+"'");
              self.$("#cuentas_corrientes_empresas_codigo").select();
              return;
            }
            var empresa = new app.models.Empresa(r);
            self.seleccionar_empresa(empresa);
          }
        });
      }
    },

    buscar_empresa_por_id: function(id) {
      var self = this;
      var empresa = new app.models.Empresa({"id":id});
      empresa.fetch({
        "success":function() {
          self.seleccionar_empresa(empresa);        
        },
        "error":function() {
          show("No existe un empresa con el ID: '"+id+"'");
          self.$("#cuentas_corrientes_empresas_codigo").select();
          return;
        }
      });
    },

    seleccionar_empresa: function(c) {
      var self = this;
      this.$("#cuentas_corrientes_empresas_datos_nombre").text(c.get("nombre"));
      this.$("#cuentas_corrientes_empresas_datos_direccion").text(c.get("direccion")+" "+c.get("localidad"));
      this.$("#cuentas_corrientes_empresas_datos_telefono").text(c.get("telefono"));
      this.$("#cuentas_corrientes_empresas_datos_email").text(c.get("email"));
      this.$("#cuentas_corrientes_empresas_datos_cuit").text(c.get("cuit"));
      this.$("#cuentas_corrientes_empresas_datos_observaciones").text(c.get("observaciones"));
      var id_tipo_iva = c.get("id_tipo_iva");
      if (id_tipo_iva == 1) this.$("#cuentas_corrientes_empresas_datos_iva").html("Responsable Inscripto");
      else if (id_tipo_iva == 2) this.$("#cuentas_corrientes_empresas_datos_iva").html("Monotributo");
      else if (id_tipo_iva == 3) this.$("#cuentas_corrientes_empresas_datos_iva").html("Exento");
      else if (id_tipo_iva == 4) this.$("#cuentas_corrientes_empresas_datos_iva").html("Consumidor Final");
      this.model.set({ "id_empresa":c.id });
      this.buscar();

      self.$('#cuentas_corrientes_empresas_codigo').val(c.get("nombre"));

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#cuentas_corrientes_empresas_codigo').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    buscar : function() {

      var self = this;
      var fecha_desde = $(this.el).find("#cuentas_corrientes_empresas_desde").val().replace(/\//g,"-");
      var fecha_hasta = $(this.el).find("#cuentas_corrientes_empresas_hasta").val().replace(/\//g,"-");

      var id_empresa = this.model.get("id_empresa");
      if (id_empresa == 0 || id_empresa == null) {
        show("Por favor seleccione un empresa");
        $(this.el).find("#cuentas_corrientes_empresas_codigo").focus();
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
      });
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
      var id_empresa = this.model.get("id_empresa");
      if (id_empresa == 0 || id_empresa == null) {
        show("Por favor seleccione un empresa");
        $(this.el).find("#cuentas_corrientes_empresas_codigo").focus();
        return;
      }

      var nombre = $("#cuentas_corrientes_empresas_datos_nombre").text();
      var desde = $("#cuentas_corrientes_empresas_desde").val();
      var hasta = $("#cuentas_corrientes_empresas_hasta").val();
      var array = new Array();
      $(".table tbody tr").each(function(i,e){
        array.push({
          "fecha":$.trim($(e).find("td:eq(1)").html()),
          "comprobante":$.trim($(e).find("td:eq(2)").html()),
          "numero":$.trim($(e).find("td:eq(3)").html()),
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
          "mostrar_checkbox": false,
          "fecha": "Saldo Inicial",
          "comprobante": "",
          "tipo":"",
          "pagada":1,
          "tipo_pago":"",
          "tipo_punto_venta": "",
          "tipo_comprobante": "",
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
      var item = new app.views.CuentasCorrientesEmpresasItemResultados({
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
            progreso = (totalComprobante>0) ? Math.abs(m.total_pagado) / Math.abs(totalComprobante) * 100 : 0;
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
            }
          });
          var modelo = new Item();
          
          this.comprobantes.push(modelo);
          
          var item = new app.views.CuentasCorrientesEmpresasItemResultados({
            model: modelo
          });
          
          // La agregamos a la tabla
          $(this.el).find(".tbody").append(item.el);
        }
      },

      agregar_recibo : function() {
        var self = this;
        var id_empresa = self.model.get("id_empresa");
        if (id_empresa == undefined || id_empresa == 0) {
          alert("Por favor seleccione un empresa.");
          this.$("#cuentas_corrientes_empresas_codigo").focus();
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

        var reciboEmpresa = new app.models.ReciboEmpresa({
          "id_empresa":0,
          "id_empresa":id_empresa,
          "cheques": [],
          "depositos": [],
          "tarjetas": [],
          "comprobantes": comprobantes,
        });
        app.views.reciboEmpresas = new app.views.ReciboEmpresas({
          model: reciboEmpresa
        });

      // Abrimos el lightbox de pagos
      crearLightboxHTML({
        "html":app.views.reciboEmpresas.el,
        "width":900,
        "height":500,
      });
    },        

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesEmpresasItemResultados = app.mixins.View.extend({

    template: _.template($("#cuentas_corrientes_empresas_item_resultados_template").html()),

    tagName: "tr",

    myEvents: {
      "click .delete":"borrar",
      "click .anular":"anular",
      "click .edit":"editar",
      "click .ver_recibo":"ver_recibo",
      "click .checkbox":"seleccionar",
      "click .imprimir":function() {
        if (this.model.get("tipo_comprobante")=="Remito") {
          workspace.imprimir_remito(this.model.id,this.model.get("id_punto_venta"));
        } else {
          workspace.imprimir_factura(this.model.id,this.model.get("id_punto_venta"));
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
          $.ajax({
            "url":"facturas/function/anular/"+this.model.id,
            "dataType":"json",
            "success":function(r){
              app.views.cuentas_corrientes_empresasResultados.buscar();
            }
          });                                            
        }
      },        
      
      borrar : function() {
        if (confirmar("Realmente desea eliminar este comprobante?")) {

          // Si es un pago
          if (this.model.get("tipo") == "P") {
            var url = "recibos/function/borrar_recibo/"+this.model.id;
            $.ajax({
              "url":url,
              "dataType": "json",
              "success": function() {
                show("El comprobante ha sido eliminado exitosamente.");
                app.views.cuentas_corrientes_empresasResultados.buscar();
              },
              "error" : function() {
                show("Error al eliminar el comprobante.");
              }
            });

          // Si es un REMITO
        } else if (this.model.get("estado") == 1) {
              // Se elimina directamente
              $.ajax({
                "url":"facturas/function/delete/"+this.model.id,
                "dataType":"json",
                "success":function(r){
                 app.views.cuentas_corrientes_empresasResultados.buscar();
               }
             });                    

          // Sino, es una FA, FB, NC, ND
        } else {
          $.ajax({
            "url":"facturas/function/delete/"+this.model.id,
            "dataType":"json",
            "success":function(r){
              app.views.cuentas_corrientes_empresasResultados.buscar();
            }
          });                  
        }
      }
    },

    editar : function() {
      if (this.model.get("tipo_comprobante")=="Remito") {
        window.open("app/#remitos/"+this.model.id,"_blank");    
      } else {
        window.open("app/#facturacion/"+this.model.id,"_blank");    
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

    ver_recibo: function(e) {
      var reciboEmpresa = new app.models.ReciboEmpresa({
        "id":$(e.currentTarget).data("id")
      });
      reciboEmpresa.fetch({
        "success":function(modelo){
          app.views.reciboEmpresas = new app.views.ReciboEmpresas({
            model: modelo
          });
          crearLightboxHTML({
            "html":app.views.reciboEmpresas.el,
            "width":900,
            "height":500,
          });                              
        }
      });
    },
  });
})(app);



// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ReciboEmpresa = Backbone.Model.extend({
    defaults: {
      "id_empresa":0,
      "id_empresa":0,
      "id_usuario":0,
      "fecha":"",
      "numero":"",
      "efectivo": 0,
      "vuelto":0,
      "total_comprobantes": 0,
      "total_cheques": 0,
      "total_depositos": 0,
      "total_tarjetas":0,
      "total":0,
      "cheques": [],
      "depositos": [],
      "tarjetas":[],
      "comprobantes":[],
    },
    urlRoot : "recibos",
  });

})( app.models );



// -----------------------------------------
//   RECIBO DE CLIENTES
// -----------------------------------------
(function ( app ) {

  app.views.ReciboEmpresas = app.mixins.View.extend({

    template: _.template($("#recibo_empresas_template").html()),

    myEvents: {
      "click .guardar":"guardar",
      "change .total_comprobante":"cambiar_total_comprobante",
      "click #recibo_cheques":"abrir_cheques",
      "click #recibo_cheques_agregar_item":"nuevo_cheque",
      "click #recibo_cheques_nuevo":"nuevo_cheque",

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
        
        "click #recibo_empresas_depositos_agregar": "agregar_deposito",
        "click .eliminar_deposito":"eliminar_deposito",
        "click #recibo_empresas_tarjetas_agregar": "agregar_tarjeta",
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
      "keydown #recibo_vuelto":function(e) {
        if (e.which == 13) {
          var valor = $(e.currentTarget).val();
          if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
          if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }

          this.model.set({
            "vuelto" : parseFloat($(e.currentTarget).val())
          });
          this.calcular_totales();
        },
      },
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
    

      // AL MODIFICAR LOS VALORES
      "focusout #recibo_efectivo":function(e) {
        var valor = $(e.currentTarget).val();
        if (isEmpty(valor)) { $(e.currentTarget).val("0"); }
        if (!isInteger(valor) && !isDecimal(valor)) { $(e.currentTarget).val("0"); }

        this.model.set({
          "efectivo" : parseFloat($(e.currentTarget).val())
        });
        this.calcular_totales();
      },
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
    this.mostrar_descuento = (typeof options.mostrar_descuento !== "undefined") ? options.mostrar_descuento : 1;
    this.mostrar_depositos = (typeof options.mostrar_depositos !== "undefined") ? options.mostrar_depositos : 1;
    this.mostrar_cheques = (typeof options.mostrar_cheques !== "undefined") ? options.mostrar_cheques : 1;
    this.mostrar_tarjetas = (typeof options.mostrar_tarjetas !== "undefined") ? options.mostrar_tarjetas : 1;
    this.id_depositos = 1;
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
    createdatepicker(this.$("#recibo_empresas_fecha"),fecha);

    createdatepicker(this.$("#recibo_cheques_fecha_emision"),fecha);
    createdatepicker(this.$("#recibo_cheques_fecha_cobro"),fecha);
    
    if (this.model.id == undefined) {
      $.ajax({
        "url":"recibos/function/next/",
        "dataType":"json",
        "success":function(r) {
          $("#recibo_empresas_numero").val(r.numero);
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

    this.render_tabla_tarjetas();

    return this;
  },

  agregar_deposito: function() {

    var id_cuenta = this.$("#recibo_empresas_depositos_cuentas").val();
    var cuenta = this.$("#recibo_empresas_depositos_cuentas option:selected").text();
    if (id_cuenta == 0) {
      alert("Por favor seleccione una cuenta.");
      this.$("#recibo_empresas_depositos_cuentas").focus();
      return;
    }
    var monto = this.$("#recibo_empresas_depositos_monto").val();
    monto = Number(monto);
    if (isNaN(monto) || monto <= 0) {
      alert("Valor incorrecto.");
      this.$("#recibo_empresas_depositos_monto").focus();
      return;
    }
    var deposito = {
      "id":this.id_depositos,
      "id_cuenta":id_cuenta,
      "cuenta":cuenta,
      "monto":monto.toFixed(2),
    };
    this.model.get("depositos").push(deposito);
    this.id_depositos = this.id_depositos + 1;
    this.render_tabla_depositos();
    this.$("#recibo_empresas_depositos_cuentas").val(0);
    this.$("#recibo_empresas_depositos_monto").val(0);
  },

  eliminar_deposito: function(e) {
    var id = $(e.currentTarget).parents("tr").data("id");
    var depositos2 = _.filter(this.model.get("depositos"),function(c){
      return (c.id != id);
    });
    this.model.set({ "depositos":depositos2 });
    this.render_tabla_depositos();
  },

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

  render_tabla_depositos: function() {
    var self = this;
    this.$("#recibo_depositos_table").empty();
    var depositos = this.model.get("depositos");
    var total = 0;
    for(var i=0;i<depositos.length;i++) {
      var d = depositos[i];
      var tr = "<tr data-id='"+d.id+"'>";
      tr+="<td>"+d.cuenta+"</td>";
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
        this.$("#recibo_empresas_tabla_comprobantes").append(tr);
      }
      this.model.set({ 
        "comprobantes":comprobantes,
        "total_comprobantes":total_saldo,
      });
      this.$("#recibo_empresas_total_debe").html("$ "+Number(total_debe).toFixed(2));
      this.$("#recibo_empresas_total_haber").html("$ "+Number(total_haber).toFixed(2));
      this.$("#recibo_empresas_total").html("$ "+Number(total_saldo).toFixed(2));
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
      this.$("#recibo_empresas_total").html("$ "+Number(total_comprobantes).toFixed(2));

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
          "id_empresa":self.model.get("id_empresa"),
          "empresa":$("#cuentas_corrientes_empresas_datos_nombre").html(),
          "titular":$("#cuentas_corrientes_empresas_datos_nombre").html(),
          "cuit_titular":$("#cuentas_corrientes_empresas_datos_cuit").html(),
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
        "id_empresa":self.model.get("id_empresa"),
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
      this.$("#recibo_cheques_titular").val($("#cuentas_corrientes_empresas_datos_nombre").html());
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
        t = t + parseFloat(this.model.get("total_cheques"));
        t = t + parseFloat(this.model.get("total_depositos"));
        t = t + parseFloat(this.model.get("total_tarjetas"));
        t = t - parseFloat(this.model.get("vuelto"));
        t = t - parseFloat(this.model.get("descuento"));
        t = Number(t).toFixed(2);
        $(this.el).find("#recibo_total_valores_entregados").val(t);
        var d = (this.model.get("total_comprobantes") - t);
        $(this.el).find("#recibo_total_diferencia").val(Number(d).toFixed(2));
        this.model.set({
          "total":t,
        });
      },

    // --------------------------------------
    //     GUARDAMOS EL RECIBO
    // --------------------------------------
    guardar : function() {
      var self = this;
      try {
        if (this.mostrar_fecha == 1) {
          var fecha = validate_input("recibo_empresas_fecha",IS_EMPTY,"Por favor ingrese una fecha.");    
          this.model.set({
            "fecha":fecha,
            "id_proyecto":ID_PROYECTO,
          });
        }
        if (this.mostrar_numero == 1) {
          var numero = $("#recibo_empresas_numero").val();
          this.model.set({
            "numero":numero,
          });
        }

            // Controlamos que lo que se esta pagando sea igual a la suma de los comprobantes
            // (Que en realidad se puede pagar menos, ya que el usuario puede modificar
            // cuanto desea pagar de cada comprobante)                
            var comprobantes = parseFloat(this.model.get("comprobantes"));
            var total_recibido = parseFloat(this.model.get("total"));
            var total_comprobantes = parseFloat(this.model.get("total_comprobantes"));
            
            // Si tiene seleccionado comprobantes
            // (porque podria ser un pago de entrega de efectivo por ejemplo, sin tener comprobantes seleccionados)
            if (total_recibido < total_comprobantes) {
              alert("ERROR: No se esta pagando la totalidad de los comprobantes seleccionados. Si desea imputar pagos parciales por un total menor, modifique los valores de los comprobantes incluidos.");
              return;
            }

            // Controlamos si la diferencia es negativa
            if ( total_comprobantes < 0) {
              alert("ERROR: El total de comprobantes no puede ser negativo. Asocie las notas de credito a las facturas compensatorias.");
              return;
            }

            this.model.set({
              "id_usuario":ID_USUARIO,
            });
          } catch(e) {
            return;
          }
          this.model.save({},{
            "success" : function() {
              show("El recibo se ha guardado correctamente.");
              if (typeof app.views.cuentas_corrientes_empresasResultados !== "undefined") {
                app.views.cuentas_corrientes_empresasResultados.buscar();
              }
              window.id_recibo = self.model.id;
              $(".modal").last().trigger("click");
            },
            "error" : function() {
              show("Ocurrio un error cuando se estaba guardando el recibo.");
            }
          });
        },

      });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RECIBOS
// -----------------------------------------
(function ( app ) {

  app.views.CuentasCorrientesEmpresasItemRecibo = Backbone.View.extend({

    template: _.template($("#cuentas_corrientes_empresas_item_recibo_template").html()),

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

    template: _.template($("#cuentas_corrientes_empresas_item_cheques_recibo_template").html()),

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
