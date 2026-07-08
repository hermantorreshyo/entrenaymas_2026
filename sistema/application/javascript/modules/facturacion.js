// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Factura = Backbone.Model.extend({
    urlRoot: "facturas/",
    defaults: {
      fecha: "",
      fecha_vto: "",
      tipo: "", // TODO: Indica si es Comprobante o Pago
      id_punto_venta: 0,
      punto_venta: 0,
      id_cliente: 0,
      id_vendedor: 0,
      id_empresa: 0,
      id_caja_diaria: 0,
      estado: 0,
      cliente: "Consumidor Final",
      numero: 0,
      subtotal: 0,
      total: 0,
      neto: 0,
      porc_descuento: 0,
      descuento: 0,
      iva: 0,
      id_tipo_comprobante: 0,
      efectivo: 0,
      cta_cte: 0,
      credito: 0,
      vuelto: 0,
      tarjeta: 0,
      cheque: 0,
      reparto: 1,
      pendiente: 0,
      fecha_reparto: "",
      items: [],
      ivas: [],
      tarjetas: [],
      cheques: [],
      creditos_personales: [],
      ofertas: [],
      bonificaciones: [],
      total_ofertas: 0,
      observaciones: "",
      gestiona_stock: 0, // TODO: este parametro debe ser configurable
      cotizacion_dolar: 0,
      id_remito: 0,
      numero_remito: 0,
      enviada: 0,
      visto: 0,
      percep_viajes: 0, // Percepcion sobre viajes al exterior 5% sobre pago efectivo
      impuesto_pais: 0, // Nueva percepcion FLAMINGO
      percepcion_ib: 0,
      direccion: "",
      localidad: "",
      id_localidad: 0,
      codigo_postal: "",
      interes: 0,
      id_tipo_estado: 6, // Por defecto, las ventas que se hacen desde el panel de control se finalizan
      id_sucursal:ID_SUCURSAL,
      sucursal: "",
      vendedor: "",
      usuario: NOMBRE_USUARIO,
      empresa: NOMBRE,
      tipo_punto_venta: "",
      tipo_comprobante: "",
      vencimiento: "",
      id_origen: 1, // 0 = LOCAL, 1 = WEB, 2 = ML, 3 = APP
      impresa: 0,
      nueva: 0,
      numero_paso: 0,
      forma_envio: "",
      coordinar_envio: 0,
      costo_neto: 0,
      costo_final: 0,
      tipo_pago: "E",

      custom_5: "", // Utilizado como comentarios privados
      custom_6: "", // Utilizado como "estado de envio"
      // "" = Pendiente
      // "1" = Proceso de armado
      // "2" = Listo para enviar
      // "3" = En transito
      // "4" = Entregado

      es_periodica: (typeof FACTURACION_PERIODICA != "undefined" &&  FACTURACION_PERIODICA == 1) ? 1 : 0,
      periodo_cantidad: 1,
      periodo_tipo: 'M',
      periodo_dia: 1,
      dias_vencimiento: 10,

      id_concepto: 0,
    }
  });
    
})( app.models );



(function ( models ) {

  models.FacturaItem = Backbone.Model.extend({
    urlRoot: "facturas_items/",
    defaults: {
      id_factura: 0,
      id_articulo: 0,
      tipo_cantidad: "",
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      neto: 0,  // Unitario
      precio: 0,  // Unitario
      nombre: "",
      descripcion: "",
      orden: 0,
      id_rubro: 0,
      iva: 0,
      total_sin_iva: 0, // Totales (unitario * cantidad)
      total_con_iva: 0,
      anulado: 0,
      costo_final: 0,
      id_variante: 0,
      variante: "",
    }
  });
    
})( app.models );





// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.FacturaEditView = app.mixins.View.extend({

  template: _.template($("#facturacion_edit_panel_template").html()),

  myEvents: {
    "click .aceptar": "aceptar",
    "click .anular": "anular",
    "click #facturacion_nuevo_cliente":"nuevo_cliente",
    "click .imprimir": function(){
      this.imprimir(this.model.id,this.model.get("id_punto_venta"),null,false);
    },
    "click .enviar":function() {
      var id = this.model.id;
      var links_adjuntos = new Array();
      links_adjuntos.push({
        tipo: (typeof TIPO_ADJUNTO_COMPROBANTE != "undefined" ? TIPO_ADJUNTO_COMPROBANTE : 0),
        id_objeto: id,
        nombre: this.model.get("comprobante"),
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Factura Electronica",
        texto: (typeof FACTURACION_TEXTO_EMAIL != "undefined" ? FACTURACION_TEXTO_EMAIL : ""),
      });
      workspace.nuevo_email(email);        
    },
    "click #facturacion_sincronizar_numero":"sincronizar_numero", 
    "click #facturacion_buscar_articulo":"ver_buscar_articulo",
    "click #facturacion_consultar_articulo":"ver_consultar_articulo",
    "click #agregar_item": "agregar_item",
    "change #facturacion_fecha_reparto":"cambiar_numero_reparto",

    "click .convertir_factura":function() {
      var self = this;
      var id = self.model.id;
      var id_punto_venta = self.model.get("id_punto_venta");
      $.ajax({
        "url":"facturas/function/convertir_factura/",
        "type":"post",
        "data":{
          "id_punto_venta":id_punto_venta,
          "id_factura":id,
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) {
            workspace.imprimir_factura(id,id_punto_venta,"E",function(){
              location.reload();
            });
          } else alert(r.mensaje);
        }
      });
    },

    "change #facturacion_tipo_pago":function(e){
      if (this.$("#facturacion_tipo_pago").val() == "E") {
        this.$("#facturacion_forma_pago_factura").html("Efectivo");
        this.model.set({"tipo_pago":"E"});
      } else if (this.$("#facturacion_tipo_pago").val() == "C") {
        this.$("#facturacion_forma_pago_factura").html("Cuenta Corriente");
        this.model.set({"tipo_pago":"C"});
      } else if (this.$("#facturacion_tipo_pago").val() == "T") {
        this.$("#facturacion_forma_pago_factura").html("Tarjeta");
        this.model.set({"tipo_pago":"T"});
      } else if (this.$("#facturacion_tipo_pago").val() == "H") {
        this.$("#facturacion_forma_pago_factura").html("Cheque");
        this.model.set({"tipo_pago":"H"});
      } else if (this.$("#facturacion_tipo_pago").val() == "B") {
        this.$("#facturacion_forma_pago_factura").html("Banco");
        this.model.set({"tipo_pago":"B"});
      } else if (this.$("#facturacion_tipo_pago").val() == "O") {
        this.$("#facturacion_forma_pago_factura").html("Otro");
        this.model.set({"tipo_pago":"O"});
      }
    },

    "change .eliminar":function(e) {
      // Marca la fila completa cuando el radio esta seleccionado
      $(e.currentTarget).parents("tbody").find("tr").removeClass("seleccionado");
      if ($(e.currentTarget).is(":checked")) {
        $(e.currentTarget).parents("tr").addClass("seleccionado"); 
      }
    },

    "change #facturacion_es_periodica":function(e) {
      if ($(e.currentTarget).is(":checked")) {
        this.$("#facturacion_periodica_opciones").slideDown();
      } else {
        this.$("#facturacion_periodica_opciones").slideUp();
      }
    },

    "change #facturacion_tipo":function() {
      this.set_numero_comprobante();
    },


      // PARA ELIMINAR LOS PRODUCTOS
      /*
      "click .eliminar_item":function(e) {
        // Cuando presiona ENTER, elimina la fila
        if (FACTURACION_CONSULTAR_ELIMINAR_ITEM == 1) {
          if (confirm("Realmente desea eliminar el elemento?")) {
            this.eliminar_item($(e.currentTarget).data("orden"));
          }
        } else this.eliminar_item($(e.currentTarget).data("orden"));
      },
      "focus .eliminar_item":function(e) {
        $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
    $(e.currentTarget).parents("tr").addClass("fila_roja");
    $(e.currentTarget).prop("checked",true);
    $("#tabla_items").parent().scrollTo($('.eliminar_item:checked').parent(),0);
      },
    "blur .eliminar_item":function(e) {
    $(e.currentTarget).parents("tbody").find("tr").removeClass("fila_roja");
    $(".eliminar_item").prop("checked",false);
    },
    */

    "change #facturacion_item_cantidad": function() {
      // Si es una distribuidora, al poner la cantidad negativa el precio unitario se pone 0 (porque es una devolucion)
      if (typeof DISTRIBUIDORA != "undefined" && DISTRIBUIDORA == 1) {
        var cant = this.$("#facturacion_item_cantidad").val();
        if (cant < 0) this.$("#facturacion_item_precio").val("0");
      }
      this.calcular_item();
    },
    "change #facturacion_item_neto": "calcular_item",
    "change #facturacion_item_precio": "calcular_item",
    "change #facturacion_alicuotas_iva": "calcular_item",
    "change #facturacion_item_bonificado": "calcular_item",
    "click #facturacion_agregar_item": "agregar_item",
    "change #facturacion_porc_percepcion_ib": function(){
      var porc_ib = this.$("#facturacion_porc_percepcion_ib").val();
      this.cliente.set({"percepcion_ib":porc_ib});
      this.calcular_totales();
    },
    
    "click .importar_factura": "ver_buscar_comprobantes",
    "click .importar_presupuesto": "ver_buscar_presupuestos",

    "focusin #facturacion_codigo_articulo":function() {
      $("#tabla_items tbody tr.seleccionado").removeClass('seleccionado');
      $("#tabla_items tbody tr .radio").prop("checked",false);
    },

    "keypress #facturacion_codigo_cliente":function(e) {
      if (e.which == 13) { this.buscar_cliente(); $("#facturacion_codigo_articulo").select(); }
    },
    "focusout #facturacion_codigo_cliente":function(e){
      if (typeof this.cliente != "undefined") {
        var nombre = this.cliente.get("nombre");
        var texto = $(e.currentTarget).val();
        if (nombre != texto) {
        // Blanqueamos el cliente para que no haya confusion
        $(e.currentTarget).val("");
        }
      }
    },
    
    "click #facturacion_buscar_cliente": "ver_buscar_cliente",
    "click #facturacion_agregar_cliente": "nuevo_cliente",

    "keyup #facturacion_reparto":function(e) {
      if (e.which == 13) { this.$("#facturacion_codigo_articulo").select(); }
    },
    
    "keypress #facturacion_codigo_articulo": function(e) {
      if (e.which == 13)  { this.buscar_articulo(); }
    },
    "keydown #facturacion_codigo_articulo": function(e) {
      if (typeof FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "pv" && e.which == 40) {
        e.preventDefault(); 
        e.stopPropagation();
        $("#tabla_items tbody tr:first .radio").prop("checked",true);
        $("#tabla_items tbody tr:first .radio").trigger("change");
        $("#tabla_items tbody tr:first .radio").focus();
        return false;
      }
      if ((typeof FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "pv") || ID_EMPRESA == 574 || ID_EMPRESA == 1326) {
        // F1 para buscar los comprobantes
        if (e.which == 112) {
          // Buscamos el comprobante para imprimir
          e.preventDefault();
          e.stopPropagation();
          var self = this;
          app.views.ventasTableView = new app.views.VentasTableView({
            collection: new app.collections.Ventas(),
            habilitar_seleccion: true,
            parent: self,
            tipos_comprobante: "999", // Solo los remitos
            fecha: moment().format("DD/MM/YYYY"), // de hoy
          });
          window.factura_seleccionada = null;
          var d = $("<div/>").append(app.views.ventasTableView.el);
          crearLightboxHTML({
            "html":d,
            "width":860,
            "height":500,
            "callback":function(){
              if (window.factura_seleccionada != null && typeof window.factura_seleccionada.id != "undefined") {
                var id = window.factura_seleccionada.id;
                $.ajax({
                  "url":"impresor_fiscal/imprimir/"+id+"/",
                  "dataType":"json",
                  "cache":false,
                  "error":function() {
                    alert("ERROR: Controlar si el impresor esta encendido o bien conectado.");
                  }
                });
              } else {
                $("#facturacion_codigo_articulo").focus();
              }
            }
          });
          $("#ventas_listado_buscar").focus();
          return false;
        }
      }
    },
    "keyup #facturacion_codigo_articulo": function(e) {
      if (e.which == 45 && FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "pv") {
        // EL INSERTAR ABRE EL CAJON 
        workspace.abrir_cajon();
      }
      if (e.which == 107 && controlador_fiscal != "") {
        this.aceptar(); this.$("#facturacion_codigo_articulo").val("");
      }
    },
    "keypress #facturacion_item_descripcion":function(e) {
      if (e.which == 13)  {
        $("#facturacion_item_cantidad").select();
      }
    },
    "keypress #facturacion_costo_final":function(e) {
      if (e.which == 13)  {
        $("#facturacion_item_precio").select();
      }
    },
    "keypress #facturacion_item_cantidad":function(e) {
      if (e.which == 13)  {
        if (this.$("#facturacion_variantes option").length > 0) {
          if (ID_EMPRESA == 868) {
            this.$("#facturacion_costo_final").select();
          } else {          
            this.$("#facturacion_variantes").focus();
          }
        } else {
          if (ID_EMPRESA == 868) {
            this.$("#facturacion_costo_final").select();
          } else if (typeof FACTURACION_SALTAR_PRECIO != "undefined" && FACTURACION_SALTAR_PRECIO == 1) {
            $("#facturacion_item_bonificado").select();
          } else if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
            $("#facturacion_item_neto").select();
          } else if (typeof FACTURACION_MODIFICAR_PRECIO != "undefined" && FACTURACION_MODIFICAR_PRECIO == 0) {
            $("#facturacion_item_bonificado").select();
          } else {
            if (this.discrimina_iva()) {
              $("#facturacion_item_neto").select();
            } else {
              $("#facturacion_item_precio").select();
            }
          }
        }
      }        
    },
    "keydown #facturacion_variantes":function(e) {
      if (e.which == 13) {
        e.preventDefault();
        if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
          $("#facturacion_item_neto").select();
        } else {
          if (this.discrimina_iva()) {
            $("#facturacion_item_neto").select();
          } else {
            $("#facturacion_item_precio").select();
          }
        }
      }
    },
      
    // CARTELES DE AYUDA
    "click .buscar_clientes_ayuda":function() {
      var ayuda = new app.views.AyudaView({
        model: new app.models.AbstractModel()
      });
      var html = "Es posible asignar un cliente a un comprobante de diferentes maneras: <br/>";
      html+= "<ul style='padding-left: 30px'>";
      html+= "<li>A trav&eacute;s de su c&oacute;digo interno, y luego presionar la tecla Enter.</li>";
      html+= "<li>Escribiendo parte de su nombre y seleccion&aacute;ndolo luego en la lista de sugerencias.</li>";
      html+= "<li>Si es un cliente nuevo, que a&uacute;n no esta cargado en su lista de contactos, puede escribir parte de su nombre y luego hacer click en el bot&oacute;n Nuevo que aparece en la lista de sugerencias. De esta manera podr&aacute; cargar r&aacute;pidamente un nuevo cliente sin tener que salir de la pantalla del comprobante.</li>";
      html+= "</ul>";
      ayuda.setText(html);
      crearLightboxHTML({
        "html":ayuda.el,
        "width":600,
        "height":300,
      });
    },
      
    "click .observaciones_ayuda":function() {
      var ayuda = new app.views.AyudaView({
        model: new app.models.AbstractModel()
      });
      var html = "Escriba aqu&iacute; la nota al pie de p&aacute;gina para sus comprobantes. Puede utilizar las siguientes variables que se reemplazar&aacute;n con los valores correspondientes: <br/>";
      html+= "<ul style='padding-left: 30px'>";
      html+= "<li>{{TOTAL_EN_LETRAS}} = Total de la factura expresado en letras.</li>";
      html+= "<li>{{TOTAL_EN_DOLARES}} = Valor correspondiente en dolares del total del comprobante.</li>";
      html+= "<li>{{TOTAL_EN_DOLARES_EN_LETRAS}} = Valor correspondiente en dolares del total del comprobante, pero expresado en letras.</li>";
      html+= "</ul>";
      ayuda.setText(html);
      crearLightboxHTML({
        "html":ayuda.el,
        "width":600,
        "height":300,
      });
    },
    
    // ACCIONES SOBRE EL FORMULARIO
    "keyup .action":function(e) {
      if (e.which == 120) { e.preventDefault(); e.stopPropagation(); this.ver_buscar_articulo(); return false; } // F9
      //if (e.which == 118) { this.anular(); } // F7
    },
    "keydown .action":function(e) {
      if (e.which == 113) { e.preventDefault(); e.stopPropagation(); workspace.cambiar_estado(); return false; } // F2
      if (e.which == 115) { e.preventDefault(); e.stopPropagation(); this.ver_buscar_cliente(); return false; } // F4
      if (e.which == 119) { e.preventDefault(); e.stopPropagation(); this.ver_mostrar_precio_articulo(); return false; } // F8
    },
      
    "keypress #facturacion_item_precio": function(e) {
      if (e.keyCode == 13) { this.$("#facturacion_item_bonificado").select(); }
    },
    "keypress #facturacion_item_neto": function(e) {
      if (e.keyCode == 13) { this.$("#facturacion_item_bonificado").select(); }
    },
    "keypress #facturacion_item_bonificado": function(e) {
      if (e.keyCode == 13) { this.agregar_item(); }
    },
    
    //"focusout #facturacion_porc_descuento": "controlar_descuento",
    "change #facturacion_porc_descuento": "controlar_descuento",
    
    "change #facturacion_puntos_venta":function(e) {
      this.$("#nombre_punto_venta").html($(e.currentTarget).find("option:selected").data("nombre"));
      this.buscar_numeros();
    },    

    "click .descuento_efectivo_mega":"descuento_efectivo_mega",
    "click .oferta_cubiertos_mega":"oferta_cubiertos_mega",
    "click .oferta_cubiertero_mega":"oferta_cubiertero_mega",
    "click .oferta_platos_mega":"oferta_platos_mega",
  },

  controlar_descuento: function() {
    if (ID_EMPRESA == 574 || ID_EMPRESA == 1326) {
      if (this.$("#facturacion_vendedores").length > 0) {
        var limite_descuento = parseFloat(this.$("#facturacion_vendedores option:selected").data("limite_descuento"));
        var porc_descuento = parseFloat(this.$("#facturacion_porc_descuento").val());
        if (isNaN(porc_descuento)) {
          alert("Por favor ingrese un numero");
          this.$("#facturacion_porc_descuento").select();
          return;
        }
        if (porc_descuento > limite_descuento) {
          alert("El descuento supera el limite permitido.");
          this.$("#facturacion_porc_descuento").select();
          return;            
        }
      }
    }
    this.calcular_totales();
  },
    
  imprimir: function(id,id_punto_venta,tipo_impresion,limpiar_despues) {
    var self = this;
    var lim = limpiar_despues;
    if (tipo_impresion == undefined) tipo_impresion = "E";
    if (ESTADO == 1) tipo_impresion = "P";
    if (lim == undefined) lim = false;
    workspace.imprimir_factura(id,id_punto_venta,tipo_impresion,function(){
      if (lim == true) {
        self.do_limpiar();
      }
    });
  },

  do_limpiar: function() {
    var self = this;
    self.limpiar();
    $("#facturacion_codigo_articulo").val("");
    if (typeof FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "pv") {
      $("#facturacion_codigo_articulo").focus();
    } else {
      $("#facturacion_codigo_cliente").select();  
    }
  },
    
  ver_buscar_cliente: function() {
    var self = this;
    var clientes = new app.collections.Clientes();
      app.views.buscarClientes = new app.views.ClientesTableView({
      collection: clientes,
      habilitar_seleccion: true,
    });
    delete window.codigo_cliente_seleccionado;
    var d = $("<div/>").append(app.views.buscarClientes.el);
    crearLightboxHTML({
      "html":d,
      "width":860,
      "height":500,
      "callback":function() {
        if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
          var that = self;
          var cliente = new app.models.Cliente({"id":window.cliente_seleccionado.id});
          cliente.fetch({
            "success":function(){
              that.seleccionar_cliente(cliente);
            },
          });
        }
        $("#facturacion_codigo_articulo").select();          
      }
    });
    $("#clientes_buscar").focus();
  },

  nuevo_cliente: function() {
    var self = this;
    var c = new app.views.ClienteEditViewMini({
      model: new app.models.Cliente({
        id_tipo_documento: (ID_EMPRESA == 1354 ? 99 : 80),
        id_tipo_iva: (ID_EMPRESA == 1354 ? 4 : 1),
        id_sucursal: ID_SUCURSAL,
      }),
      onSave: function(cli){
        self.seleccionar_cliente(cli);
        $('.modal:last').modal('hide');
      }
    });
    crearLightboxHTML({
      "html":c.el,
      "width":600,
      "height":500,
    });
    $("#clientes_mini_nombre").focus();
  },
    
  ver_buscar_comprobantes: function() {
    var self = this;
    window.factura_seleccionada = null;
    app.views.ventasTableView = new app.views.VentasTableView({
      collection: new app.collections.Ventas(),
      habilitar_seleccion: true,
      parent: self,
    });
    var d = $("<div/>").append(app.views.ventasTableView.el);
    crearLightboxHTML({
      "html":d,
      "width":860,
      "height":500,
      "callback":function() {
        if (window.factura_seleccionada == null) return;
        self.importar(window.factura_seleccionada);
      }
    });
    $("#ventas_listado_buscar").focus();
  },

  ver_buscar_presupuestos: function() {
    var self = this;
    window.presupuesto_seleccionado = null;
    app.views.presupuestosTableView = new app.views.PresupuestosTableView({
      collection: new app.collections.Presupuestos(),
      habilitar_seleccion: true,
      parent: self,
    });
    var d = $("<div/>").append(app.views.presupuestosTableView.el);
    crearLightboxHTML({
      "html":d,
      "width":860,
      "height":500,
      "callback":function() {
        if (window.presupuesto_seleccionado == null) return;
        self.importar_presupuesto(window.presupuesto_seleccionado);
      }
    });
    $("#ventas_listado_buscar").focus();
  },  
    
  importar: function(model) {
    var self = this;
    var factura = new app.models.Factura({
      "id": model.id,
    });
    factura.fetch({
      "success":function(){

        var that = self;
        var cliente = new app.models.Cliente({"id":factura.get("id_cliente")});
        cliente.fetch({
          "success":function() {
            self.seleccionar_cliente(cliente);
          },
        });

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.FacturaItem
        });
        var productos = factura.get("items");
        self.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          p.discrimina_iva = self.discrimina_iva();
          var fi = new app.models.FacturaItem(p);
          self.items.add(fi);
        }
        self.items.on('all', self.render_tabla_items, self);
        self.items.on('add', self.addItem, self);
        
        self.render();
        self.render_view();
        self.render_tabla_items();
        //self.set_numero_comprobante();
      }
    });
    
    /*
    this.model = model.clone();
    this.model.set({
      "ivas":[],
    });
    this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
    window.factura = this.model; // TODO: borrar esto desp
    
    // Creamos una nueva coleccion de items
    var ItemsCollection = Backbone.Collection.extend({
      model: app.models.FacturaItem,
    });
    this.items = new ItemsCollection();
    this.items.on('all', this.render_tabla_items, this);
    this.items.on('add', this.addItem, this);
    window.items = this.items;
    
    // Renderizamos y limpiamos
    this.render();
    if (control.check("repartos")>0 == 1) this.cambiar_numero_reparto();
    this.buscar_cliente();
    */
  },

  importar_presupuesto: function(model) {
    var self = this;
    var presupuesto = new app.models.Presupuesto({
      "id": model.id,
    });
    presupuesto.fetch({
      "success":function(){

        var that = self;
        var cliente = new app.models.Cliente({"id":presupuesto.get("id_cliente")});
        cliente.fetch({
          "success":function() {
            cliente.set({"descuento":presupuesto.get("porc_descuento")});
            self.seleccionar_cliente(cliente);
          },
        });

        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.FacturaItem
        });
        var productos = presupuesto.get("items");
        self.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          p.id_departamento = 0;
          p.tipo = "";
          p.percep_viajes = 0;
          p.custom_1 = "";
          p.custom_2 = "";
          p.custom_3 = "";
          p.custom_4 = "";
          p.id_proveedor = 0;
          p.variantes = [];
          p.discrimina_iva = self.discrimina_iva();
          p.total_sin_iva = p.precio * p.cantidad;
          p.total_con_iva = p.neto * p.cantidad;
          if (p.moneda == 2) {
            p.total_sin_iva = p.total_sin_iva * COTIZACION_DOLAR;
            p.total_con_iva = p.total_con_iva * COTIZACION_DOLAR;
            p.costo_final = p.costo_final * COTIZACION_DOLAR;
          }
          var fi = new app.models.FacturaItem(p);
          self.items.add(fi);
        }
        self.items.on('all', self.render_tabla_items, self);
        self.items.on('add', self.addItem, self);

        var porc_descuento = parseFloat(presupuesto.get("porc_descuento"));
        self.model.set({"porc_descuento":porc_descuento});

        self.render();
        self.render_view();
        self.render_tabla_items();
      }
    });
  },

  buscar_cliente : function() {
    var self = this;
    
    var codigo = this.$("#facturacion_codigo_cliente").val();
    if (isEmpty(codigo)) {
      codigo = 0;
      this.$("#facturacion_codigo_cliente").val(codigo);
    }
    // Es consumidor final, creamos el cliente directamente
    if (codigo == 0) {
      this.setear_consumidor_final();
    } else {
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
              self.$("#facturacion_codigo_cliente").select();
              self.$("#facturacion_codigo_cliente").focus();
              return;
            }
            var cliente = new app.models.Cliente(r);
            self.seleccionar_cliente(cliente);
          }
        });
      }
    }
    this.$("#facturacion_codigo_articulo").focus();  
  },
    
  setear_consumidor_final: function() {
    var cf = new app.models.Cliente({
      "id_tipo_iva":4,
      "nombre":"Consumidor Final",
      "cuit":"",
      "saldo":0,
      "email":"",
      "direccion":"",
      "percibe_ib":0,
      "descuento":0,
      "error":0,
      "id_vendedor":0,
      "lista":0,
      "tipo_pago":"E",
      "etiquetas":[],
    });      
    this.seleccionar_cliente(cf);
  },
    
  seleccionar_cliente: function(r) {
    var self = this;
    self.cliente = r; // Seteamos el cliente
    self.chequear_comprobantes();
    if (this.model.id == undefined || this.model.id == 0) self.set_numero_comprobante();
    
    // Recorremos los elementos de la coleccion
    if (this.items.size() > 0) {
      var discrimina_iva = this.discrimina_iva();
      this.items.each(function(e){
        e.set({ "discrimina_iva":discrimina_iva });
      });        
    }
    
    if (self.model.isNew()) {
      // Si es un comprobante nuevo, tomamos el descuento del cliente
      self.$("#facturacion_porc_descuento").val(self.cliente.get("descuento"));
      if (self.$("#facturacion_lista").length > 0) self.$("#facturacion_lista").val(self.cliente.get("lista"));
      if (self.cliente.get("id_vendedor") != 0) self.$("#facturacion_vendedores").val(self.cliente.get("id_vendedor"));
      // Seteamos el tipo de pago de acuerdo al configurado en el cliente
      var forma_pago = self.cliente.get("forma_pago");
      if (isEmpty(forma_pago)) forma_pago = "C";
      self.model.set({ "tipo_pago": forma_pago });
    } else {
      // Sino tomamos el descuento del comprobante, ya que puede haber sido cambiado
      self.$("#facturacion_porc_descuento").val(self.model.get("porc_descuento"));
    }

    if (isEmpty(self.model.get("localidad"))) {
      self.$("#facturacion_cliente_localidad").html(self.cliente.get("localidad"));
      self.$("#facturacion_localidad").val(self.cliente.get("localidad"));
      self.$("#facturacion_codigo_postal").val(self.cliente.get("codigo_postal"));
    }

    self.render_view();
    self.$("#facturacion_codigo_articulo").focus();

    // Por si el cliente tiene descuento, hay que llamar al calcular totales
    self.calcular_totales();

    // Para cerrar el customcomplete que se abre
    setTimeout(function(){
    self.$('#facturacion_codigo_cliente').trigger(jQuery.Event('keyup', {which: 27}));
    },500);
  },
    
  // Actualizamos la vista con los datos del modelo
  render_view: function() {

    var self = this;
    
    // Mostramos el nombre de comprobante que corresponde
    var id_tipo_comprobante = parseInt(this.model.get("id_tipo_comprobante"));
    switch (id_tipo_comprobante) {
      case 1:
        this.$(".invoice-type").html("Factura"); this.$(".letter").html("A"); break;
      case 2:
        this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("A"); break;
      case 3:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("A"); break;
      case 4:
        this.$(".invoice-type").html("Recibo"); this.$(".letter").html("A"); break;
      case 6:
        this.$(".invoice-type").html("Factura"); this.$(".letter").html("B"); break;
      case 7:
        this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("B"); break;
      case 8:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("B"); break;
      case 9:
        this.$(".invoice-type").html("Recibo"); this.$(".letter").html("B"); break;
      case 11:
        this.$(".invoice-type").html("Factura"); this.$(".letter").html("C"); break;
      case 12:
        this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("C"); break;
      case 13:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("C"); break;
      case 15:
        this.$(".invoice-type").html("Recibo"); this.$(".letter").html("C"); break;
      case 19:
        this.$(".invoice-type").html("Factura"); this.$(".letter").html("E"); break;
      case 20:
        this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("E"); break;
      case 21:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("E"); break;
      case 51:
        this.$(".invoice-type").html("Factura"); this.$(".letter").html("M"); break;
      case 52:
        this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("M"); break;
      case 53:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("M"); break;
      case 201:
        this.$(".invoice-type").html("Factura MiPyme"); this.$(".letter").html("A"); break;
      case 202:
        this.$(".invoice-type").html("Nota de D&eacute;bito MiPyme"); this.$(".letter").html("A"); break;
      case 203:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito MiPyme"); this.$(".letter").html("A"); break;
      case 206:
        this.$(".invoice-type").html("Factura MiPyme"); this.$(".letter").html("B"); break;
      case 207:
        this.$(".invoice-type").html("Nota de D&eacute;bito MiPyme"); this.$(".letter").html("B"); break;
      case 208:
        this.$(".invoice-type").html("Nota de Cr&eacute;dito MiPyme"); this.$(".letter").html("B"); break;
      case 998:
        this.$(".invoice-type").html("Presupuesto"); this.$(".letter").html("X"); break;
      case 999:
        this.$(".invoice-type").html("Remito"); this.$(".letter").html("X"); break;
    }

    // Si es una nota de debito, se podria cambiar el % de perc IIBB
    if (id_tipo_comprobante == 2 || id_tipo_comprobante == 7 || id_tipo_comprobante == 12 || id_tipo_comprobante == 52 || id_tipo_comprobante == 202 || id_tipo_comprobante == 207) {
      this.$("#facturacion_porc_percepcion_ib").removeAttr("disabled");
    } else {
      this.$("#facturacion_porc_percepcion_ib").attr("disabled","disabled");
    }
    
    self.$("#facturacion_numero").val(self.model.get("numero"));

    // Forma de Pago
    self.$("#facturacion_tipo_pago").val(self.model.get("tipo_pago"));
    
    // Mostamos los datos del cliente
    if (self.cliente != null && ID_EMPRESA != 228) {
      
      self.$("#facturacion_id_cliente").val(self.cliente.id);
      
      var id_tipo_iva = self.cliente.get("id_tipo_iva");
      if (id_tipo_iva == 1) self.$("#facturacion_cliente_iva").html("Responsable Inscripto");
      else if (id_tipo_iva == 2) self.$("#facturacion_cliente_iva").html("Monotributo");
      else if (id_tipo_iva == 3) self.$("#facturacion_cliente_iva").html("Exento");
      else if (id_tipo_iva == 4) self.$("#facturacion_cliente_iva").html("Consumidor Final");
      self.$("#facturacion_codigo_cliente").val(self.cliente.get("nombre"));
      self.$("#facturacion_cliente_factura").html(self.cliente.get("nombre"));
      self.$("#facturacion_cliente_cuit").html(self.cliente.get("cuit"));
      self.$("#facturacion_cliente_direccion").html(self.cliente.get("direccion"));
      self.$("#facturacion_cliente_localidad").html(self.cliente.get("localidad"));
      
      self.$("#facturacion_porc_percepcion_ib").val(self.cliente.get("percepcion_ib"));
      self.$("#facturacion_saldo_anterior").val(Number(self.cliente.get("saldo")).toFixed(2));
    }      
    
    // Fecha
    self.$("#facturacion_fecha_factura").html(self.model.get("fecha"));
          
    self.$("#tabla_impuestos tbody").empty();
    var ivas = this.model.get("ivas");
    for(var i=0;i<ivas.length;i++) {
      var ii = ivas[i];
      if (ii.neto > 0) {
        var tr = ""; var nombre_iva = "";
        switch(i) {
          case 3: nombre_iva = "Exento"; break;
          case 4: nombre_iva = "IVA 10.50 %"; break;
          case 5: nombre_iva = "IVA 21.00 %"; break;
          case 6: nombre_iva = "IVA 27.00 %"; break;
          case 8: nombre_iva = "IVA 5.00 %"; break;
          case 9: nombre_iva = "IVA 2.50 %"; break;
        }
        tr+='<tr>';
        tr+='<td>'+nombre_iva+'</td>';
        tr+='<td>'+Number(ii.neto).toFixed(2)+'</td>';
        tr+='<td>'+Number(ii.iva).toFixed(2)+'</td>';
        tr+='</tr>';
        self.$("#tabla_impuestos tbody").append(tr);
      }
    }

    var descuento = parseFloat(self.model.get("descuento"));
    self.$("#facturacion_subtotal_sin_dto_div").addClass("dn");
    
    // Si discrimina IVA
    if (this.discrimina_iva()) {
      self.$(".iva_container").show();
      if (typeof REMITOS_TOMAR_PRECIO_NETO == "undefined" || REMITOS_TOMAR_PRECIO_NETO == 0) {
        self.$("#facturacion_item_neto").removeClass("dn");
        self.$("#facturacion_item_precio").addClass("dn");          
      }
      var neto = parseFloat(self.model.get("neto"));
      // El subtotal es sin descuento
      self.$("#facturacion_subtotal").val(Number(neto).toFixed(2));
      // El otro subtotal visible se le suma el descuento
      //self.$("#facturacion_subtotal_sin_dto").val(Number(neto).toFixed(2));

    } else {
      self.$(".iva_container").hide();
      if (typeof REMITOS_TOMAR_PRECIO_NETO == "undefined" || REMITOS_TOMAR_PRECIO_NETO == 0) {
        self.$("#facturacion_item_precio").removeClass("dn");
        self.$("#facturacion_item_neto").addClass("dn");
      }
      var sub = self.model.get("subtotal");
      // El subtotal es sin descuento
      self.$("#facturacion_subtotal").val(Number(sub).toFixed(2));
      // El otro subtotal visible se le suma el descuento
      //self.$("#facturacion_subtotal_sin_dto").val(Number(sub + descuento).toFixed(2));
      //self.$("#facturacion_subtotal_sin_dto_div").removeClass("dn");
    }
    
    // Totales
    self.$("#facturacion_descuento").val(Number(descuento).toFixed(2));  
    self.$("#facturacion_iva").val(Number(self.model.get("iva")).toFixed(2));  
    self.$("#facturacion_percepcion_ib").val(Number(self.model.get("percepcion_ib")).toFixed(2));  

    var t = parseFloat(self.model.get("total")) + parseFloat(self.model.get("total_ofertas"));
    self.$("#facturacion_total").val(Number(t).toFixed(2));

    //var total_visible = t - parseFloat(self.model.get("descuento"));
    self.$("#facturacion_total_visible").val(Number(t).toFixed(2));

    self.$("#facturacion_tipo_pago").change();
  },
    
  set_numero_comprobante: function() {
    var self = this;
    var tipo_comprobante = parseInt(self.$("#facturacion_tipo").val());
    if (self.model.id == undefined && self.numeros != undefined) {
      self.model.set({ "numero":self.numeros[tipo_comprobante] });
    }
  },

  // Controla los comprobantes que puede realizar
  chequear_comprobantes: function() {
      
    var self = this;
    if (self.cliente == null || self.cliente == undefined) return;
    var iva_cliente = self.cliente.get("id_tipo_iva");
    if (iva_cliente == 0) iva_cliente = 4;
    var habilitados = [];
    this.$("#facturacion_tipo").empty();
    
    // Habilitamos el Remito y el Presupuesto
    if (ESTADO == 1) {
      habilitados.push(999);
    }      
    
    // Si la empresa es Responsable Inscripta
    if (ID_TIPO_CONTRIBUYENTE == 1) {
      
      // Y el cliente Monotributo o Exento o Consumidor Final
      if (iva_cliente == 2 || iva_cliente == 3 || iva_cliente == 4) {
        habilitados.push(6);
        habilitados.push(7);
        habilitados.push(8);
        habilitados.push(9);
        habilitados.push(206);
        habilitados.push(207);
        habilitados.push(208);
      
      // Y el cliente Responsable Inscripto 
      } else if (iva_cliente == 1) {
        habilitados.push(1);
        habilitados.push(2);
        habilitados.push(3);
        habilitados.push(4);
        habilitados.push(201);
        habilitados.push(202);
        habilitados.push(203);
      }
      
    // Si la empresa es Monotributista
    } else if (ID_TIPO_CONTRIBUYENTE == 2) {
      habilitados.push(11);
      habilitados.push(12);
      habilitados.push(13);
      habilitados.push(15);
    }

    for (j=0;j<habilitados.length;j++) {
      var o = habilitados[j];
      var comprobante = null;
      for(var i=0;i<comprobantes.length;i++) {
        comprobante = comprobantes[i];
        if (comprobante.id == o) break;
      }
      var option = "<option ";
      option+="value='"+comprobante.id+"' ";
      if (this.model.id != undefined && this.model.id != 0) option+=( comprobante.id == this.model.get("id_tipo_comprobante") )?"selected ":"";
      option+= ">"+comprobante.nombre;
      option+="</option>";
      this.$("#facturacion_tipo").append(option);
    }
          
    // Si es una factura nueva, seteamos el primero
    if (this.model.id == undefined || this.model.id == 0) {
      this.model.set("id_tipo_comprobante",this.$("#facturacion_tipo").val());
    }
  },
    
  buscar_numeros: function() {
    var self = this;
    var id_punto_venta = this.$("#facturacion_puntos_venta").val();
    $.ajax({
      "url":"facturas/function/next/"+id_punto_venta,
      "dataType":"json",
      "success":function(r) {
        self.numeros = r;
        self.set_numero_comprobante();
      }
    });      
  },    
    
    buscar_articulo : function() {
      
      var self = this;
      var codigo = $("#facturacion_codigo_articulo").val();
      codigo = codigo.trim();

      // Limpiamos el codigo para que no tenga ningun ' o "
      codigo = codigo.replace(/\'/g,"");
      codigo = codigo.replace(/\"/g,"");
      
      if (typeof FACTURACION_CODIGO_FINALIZAR != "undefined" && codigo == FACTURACION_CODIGO_FINALIZAR) {
        this.aceptar(); return;
      }

      var modif = codigo;
      var lista_precios = ((this.$("#facturacion_lista").length > 0) ? this.$("#facturacion_lista").val() : 0);
      
      // Si el metodo de ingreso es un unico campo, analizamos si
      // en el mismo se estan ingresando cantidades, etc..
      if (typeof FACTURACION_METODO_INGRESO != "undefined" && FACTURACION_METODO_INGRESO == "J") {
        
        // Si el codigo del articulo esta vacio, no ingresa nada
        if (isEmpty(codigo)) return;
        
        // FORMATO:
        // (codigo) (/cambio) (+bonif) (-desc) (*unidad)
        
        var primero = codigo.length;
        //var pos_men = codigo.indexOf("-");
        //if (pos_men >= 0 && pos_men < primero) primero = pos_men;
        var pos_mas = codigo.indexOf("+");
        if (pos_mas >= 0 && pos_mas < primero) primero = pos_mas;
        var pos_div = codigo.indexOf("/");
        if (pos_div >= 0 && pos_div < primero) primero = pos_div;
        var pos_por = codigo.indexOf("*");
        if (pos_por >= 0 && pos_por < primero) primero = pos_por;
        
        var cantidad = 1;
        if (pos_por > 0) {
          cantidad = Number(getField(modif,pos_por+1)).toFixed(FACTURACION_CANTIDAD_DECIMALES);
        } else {
          if (pos_men > 0 || pos_mas > 0 || pos_div > 0) cantidad = 0;
          else cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES);
        }
        this.$("#facturacion_item_cantidad").val(cantidad);
        
        codigo = codigo.substr(0,primero);
        /*
        if (pos_men != -1) {
          this.porc_descuento_item = getField(modif,pos_men+1);
        }*/
        if (pos_mas != -1) {
          this.bonificacion = getField(modif,pos_mas+1);
        }
        if (pos_div != -1) {
          this.devolucion = getField(modif,pos_div+1);
        }
      } else if (typeof FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "pv") {

        // FORMATO: [unidad *] (codigo)
        var pos_por = codigo.indexOf("*");
        if (pos_por > 0) {
          var decimales = (typeof FACTURACION_CANTIDAD_DECIMALES != "undefined") ? FACTURACION_CANTIDAD_DECIMALES : 2;
          cantidad = Number(getField(modif,0)).toFixed(decimales);
        }
        if (cantidad == 0) {
          alert("ERROR: La cantidad no puede ser cero.");
          return false;
        }
        codigo = getField(modif,pos_por+1,false);
        if (isEmpty(codigo)) return;

        this.$("#facturacion_item_cantidad").val(cantidad);

      } else {
        
        // Si el codigo del articulo esta vacio, simplemente saltamos a que escriba la descripcion
        if (isEmpty(codigo)) {
          //$("#facturacion_item_descripcion").select();
          return;
        }

        // No lo buscamos, porque no es un codigo, esta escribiendo una descripcion
        if (codigo.length > 15) {
          self.articulo = null;
          self.$("#facturacion_item_cantidad").select();  
          return;
        }

      }

      // Lo convertimos a numero
      if (MEGASHOP == 1 || ID_EMPRESA == 421) {
        try {
          codigo = String(codigo).replace("]C1","");
          // Si comienza con un numero, parseamos a entero
          if ($.isNumeric(codigo.substr(0,1))) {

            // Si comienza con 0000 y tiene 13 digitos, tenemos que sacar el ultimo
            if (codigo.substr(0,4) == "0000" && codigo.length == 13) {
              codigo = codigo.substr(0,12);
            }

            var codigo_ant = parseFloat(codigo);
            codigo = codigo_ant;
          }
        } catch(e){
          console.log("Error al convertir INT el codigo: '"+codigo+"'.");
        }
      }
      this.codigo_leido = codigo;

      if (CACHE_ARTICULOS == 1 || FACTURACION_USA_CACHE_ARTICULOS == 1) { 
      
        // Lo buscamos en el array
        var r = window.articulos.find(function(c){
          // Si esta configurado para usar numeros de plu en el codigo de barras
          if (FACTURACION_USA_NPLU == "1") {
            codigo = String(codigo);
            // Si el codigo comienza con el identificador de PLU
            if (codigo.substr(0,FACTURACION_IDENTIFICADOR_PLU.length) == FACTURACION_IDENTIFICADOR_PLU
              && codigo.length > FACTURACION_LARGO_PLU) {
              // Ignoramos los primeros caracteres, y el ultimo que es un checksum
              var ignorar = FACTURACION_IDENTIFICADOR_PLU.length + Number(FACTURACION_LARGO_PLU);
              // TOMAMOS EL PRECIO DE LA PROPIA ETIQUETA
              self.precio_fijo = Number(codigo.substr(ignorar,codigo.length-(ignorar+1))) / 100;
              codigo = parseInt(codigo.substr(FACTURACION_IDENTIFICADOR_PLU.length,FACTURACION_LARGO_PLU));
            }
          }

          // Si tenemos codigo de barra
          var encontro_codigo_barra = false;
          var codigos = c.get("codigos");
          for(var cc = 0; cc < codigos.length; cc++) {
            var codigo_barra = codigos[cc];
            // Si comienza con un numero, parseamos a entero
            if ($.isNumeric(codigo_barra)) {
              codigo_barra = parseInt(codigo_barra);
            }
            if (codigo_barra == codigo) {
              encontro_codigo_barra = true;
              break;
            }
          }
          if (encontro_codigo_barra) return true;

          // Sino buscamos por codigo o codigo de barra
          var cod = parseFloat(c.get("codigo"));
          return (cod == codigo);
        });
        if (typeof r === "undefined") {
          self.articulo = null;
          if (FACTURACION_TIPO == "pv") {
            alert("No se encuentra el articulo con codigo '"+codigo+"'.");
            this.$("#facturacion_codigo_articulo").select();
            return false;
          } else {
            this.$("#facturacion_item_cantidad").select();  
          }
        } else {
          this.seleccionar_articulo(r);
        }

      // Los articulos no se encuentran cacheados en un array de JS, por lo que hay que buscarlo con AJAX
      } else {
        codigo = encodeURIComponent(codigo);
        $.ajax({
          "url":"articulos/function/"+((MEGASHOP == 1 || ID_EMPRESA == 421) ? "get_by_codigo_pv" : "get_by_codigo")+"/"+codigo,
          "dataType":"json",
          "type":"post",
          "data":{
            "id_sucursal":ID_SUCURSAL,
            "lista_precios":lista_precios,
            "consultar_stock":((typeof FACTURACION_CONSULTAR_STOCK != "undefined") ? FACTURACION_CONSULTAR_STOCK : 0),
          },
          "success":function(result) {
            if (result.error == 1) {
              self.articulo = null;
              if (FACTURACION_TIPO == "pv" || (typeof FACTURACION_INGRESAR_SOLO_PRODUCTOS != "undefined" && FACTURACION_INGRESAR_SOLO_PRODUCTOS == 1)) {
                alert("No se encuentra el articulo con codigo '"+codigo+"'.");
                self.$("#facturacion_codigo_articulo").select();
              } else {
                self.$("#facturacion_item_cantidad").select();  
              }
            } else {
              var art = new app.models.Articulo(result.articulo);
              self.seleccionar_articulo(art);
            }
          }
        });

      }
    },
    
    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      self.calcular_item();
      if (FACTURACION_TIPO == "pv") {
        this.agregar_item();
      } else {
        this.$("#facturacion_item_cantidad").select();
      }
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      if (FACTURACION_TIPO == "pv" || ID_EMPRESA == 574 || ID_EMPRESA == 1326) {

        // Si estamos editando
        if (r.get("id_factura") != 0) return;

        window.acepto_supervisor = 0;
        var abstractModel = Backbone.Model.extend();
        var sup = new app.views.CodigoSupervisorView({
          model: new abstractModel()
        });
        crearLightboxHTML({
          "html":sup.el,
          "width":400,
          "height":200,
          "callback":function(){
            if (window.acepto_supervisor == 1) {
              // Si se acepto el codigo de supervisor, editamos el articulo
              var precio = prompt("Ingrese el nuevo precio unitario");
              try {
                var precio_nuevo = parseFloat(precio);
                if (!isNaN(precio_nuevo)) {

                  // IMPORT SHOW TIENE ALGUNOS DESCUENTOS ESPECIALES
                  // Si el cliente tiene la etiqueta "empleado"
                  if (ID_EMPRESA == 356) {
                    var es_empleado = self.check_etiqueta("empleado");
                    var es_encargado = self.check_etiqueta("encargado");
                    if (es_empleado || es_encargado) {
                      // Entonces aplicamos el descuento por departamento
                      var id_departamento = (self.$("#facturacion_departamento").length > 0) ? self.$("#facturacion_departamento").val() : 0;
                      for(var w=0;w<window.departamentos_comerciales.length; w++) {
                        var depto = window.departamentos_comerciales[w];
                        if (depto.id == id_departamento) {
                          var desc = parseFloat(depto.descuento);
                          if (es_encargado) {
                            if (desc == 15) desc = 25;
                            else if (desc == 20) desc = 35;
                          }
                          precio_nuevo = precio_nuevo * ((100-desc)/100);
                          break;
                        }
                      }
                    }
                  }

                  var cantidad = r.get("cantidad");
                  var porc_iva = r.get("porc_iva");
                  if (MEGASHOP == 1) {
                    if (ID_SUCURSAL == 23) var porc_iva = 0;
                    else var porc_iva = 21;
                  }
                  var costo_final_anterior = parseFloat(r.get("costo_final"));
                  var precio_final_anterior = parseFloat(r.get("precio_final_dto"));

                  // TODO: Hacer dinamico esto, si al cambiar el costo es proporcionable, o el costo final queda fijo
                  if (ID_EMPRESA == 287 || MEGASHOP == 1) {
                    // Espacio Virtual, Megashop el costo queda fijo
                    var costo_final_nuevo = costo_final_anterior;
                  } else {
                    var costo_final_nuevo = precio_nuevo;
                    try {
                      costo_final_nuevo = (precio_final_anterior != 0) ? (precio_nuevo * costo_final_anterior / precio_final_anterior) : precio_nuevo;
                    } catch(e) {
                      costo_final_nuevo = precio_nuevo;
                    }
                  }
                  var neto = precio_nuevo / ((100+porc_iva)/100);
                  r.set({
                    "costo_final":costo_final_nuevo,
                    "precio":precio_nuevo,
                    "neto":neto,
                    "total_sin_iva":neto*cantidad,
                    "total_con_iva":precio_nuevo*cantidad,
                    "tipo_cantidad":"X", // Marca para ver que el usuario cambio de precio
                  });
                  /*
                  if (MEGASHOP == 1) {
                    r.set({
                      "costo_final":(precio_nuevo / 2)
                    })
                  }
                  */
                  self.calcular_ofertas();
                  //self.calcular_totales();
                  self.$("#facturacion_codigo_articulo").focus();
                  self.item = undefined;
                }
              } catch(e) {
                console.log(e);
              }
            }
          }
        });
        $("#codigo_supervisor_texto").focus();
      } else {
        $("#facturacion_id_articulo").val(this.item.get("id_articulo"));
        $("#facturacion_codigo_articulo").val(this.item.get("nombre"));
        var cantidad = parseFloat(this.item.get("cantidad"))
        $("#facturacion_item_cantidad").val(cantidad);
        $("#facturacion_alicuotas_iva").val(this.item.get("id_tipo_alicuota_iva"));
        var costo_final = ((cantidad > 0) ? (parseFloat(this.item.get("costo_final")) / cantidad) : 0);
        $("#facturacion_costo_final").val(costo_final);
        $("#facturacion_tipo_item").val(this.item.get("tipo"));
        $("#facturacion_item_neto").val(this.item.get("neto"));
        $("#facturacion_item_precio").val(this.item.get("precio"));
        $("#facturacion_item_bonificado").val(this.item.get("bonificacion"));
        $("#facturacion_item_descripcion").val(this.item.get("descripcion"));
        if ($("#facturacion_item_percep_viajes").length > 0) $("#facturacion_item_percep_viajes").val(this.item.get("percep_viajes"));
        if ($("#facturacion_item_custom_2").length > 0) $("#facturacion_item_custom_2").val(this.item.get("custom_2"));
        if ($("#facturacion_item_custom_4").length > 0) $("#facturacion_item_custom_4").val(this.item.get("custom_4"));
        if ($("#facturacion_item_id_proveedor").length > 0) $("#facturacion_item_id_proveedor").val(this.item.get("id_proveedor"));
        if ($("#facturacion_variantes").length > 0) $("#facturacion_variantes").val(this.item.get("id_variante"));
        self.calcular_item();
        self.render_variantes(this.item.get("variantes"),this.item.get("id_variante"));
        this.$("#facturacion_item_cantidad").select();      
      }
    },

    ver_mostrar_precio_articulo: function() {
      var self = this;
      if (CACHE_ARTICULOS == 1) {
        var buscar = new app.views.ArticulosMostrarPrecioView({
          collection: articulos,
          habilitar_seleccion: true,
        });
        var d = $("<div/>").append(buscar.el);
        crearLightboxHTML({
          "html":d,
          "width":450,
          "height":300,
        });
        $("#articulos_mostrar_precio_buscar").select();
      }
    },
    
    ver_buscar_articulo : function() {
      if (typeof FACTURACION_OCULTAR_BUSCADOR != "undefined" && FACTURACION_OCULTAR_BUSCADOR == 1) return;
      var self = this;
      window.articulos_buscar_activo = 1;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: ((CACHE_ARTICULOS == 1) ? articulos : new app.collections.Articulos()),
        habilitar_seleccion: true,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":((ID_EMPRESA == 342)?1100:860),
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            if (typeof FACTURACION_TIPO != "undefined" && FACTURACION_TIPO == "") {
              // Si el formulario es tipo factura, se reemplaza el codigo del articulo seleccionado
              $("#facturacion_codigo_articulo").val(window.codigo_articulo_seleccionado);
            } else {
              // Si es un PV, se suma a lo que ya hay porque puede haber una cantidad
              $("#facturacion_codigo_articulo").val($("#facturacion_codigo_articulo").val()+window.codigo_articulo_seleccionado);  
            }
            self.buscar_articulo();
            $("#facturacion_codigo_articulo").focus();
          } else {
            $("#facturacion_codigo_articulo").focus();
          }          
        }
      });
      $("#articulos_buscar").focus();
    },
    
    ver_consultar_articulo : function() {
      var self = this;
      var view = new app.views.ArticulosMostrarPrecioView({
        collection: ((CACHE_ARTICULOS == 1) ? articulos : new app.collections.Articulos()),
        habilitar_seleccion: true,
      });   
      var d = $("<div/>").append(view.el);
      crearLightboxHTML({
        "html":d,
        "width":500,
        "height":500,
        "callback":function() {
          $("#articulos_mostrar_precio_buscar").select();
        }
      });
      $("#articulos_mostrar_precio_buscar").select();
    },

    mostrar_articulo : function() {

      // Mostramos el nombre
      if (FACTURACION_TIPO != "pv") {
        this.$("#facturacion_codigo_articulo").val(this.articulo.get("nombre"));
      } else {
        this.$("#facturacion_item_nombre").val(this.articulo.get("nombre"));
      }
      
      this.$("#facturacion_tipo_item").val(this.articulo.get("tipo"));

      // Si el articulo esta marcado como de CANASTA BASICA
      var disc_iva = this.discrimina_iva();
      /*
      if (this.articulo.get("custom_5") == "1" && this.cliente_canasta_basica && ID_EMPRESA != 249) {
        // Ponemos el IVA Alicuota 0%
        this.articulo.set({
          "id_tipo_alicuota_iva":3,
          "porc_iva":0,
          "costo_iva":0,
        });
      }*/
      this.$("#facturacion_alicuotas_iva").val(this.articulo.get("id_tipo_alicuota_iva"));
      this.$("#facturacion_id_articulo").val(this.articulo.id);
      this.$("#facturacion_costo_final").val(this.articulo.get("costo_final"));
      if (this.$("#facturacion_moneda").length > 0 && typeof this.articulo.get("moneda") != "undefined") this.$("#facturacion_moneda").val(this.articulo.get("moneda"));
      if (this.$("#facturacion_rubro").length > 0) this.$("#facturacion_rubro").val(this.articulo.get("id_rubro"));
      if (this.$("#facturacion_departamento").length > 0) this.$("#facturacion_departamento").val(this.articulo.get("id_departamento"));
      if (this.$("#facturacion_item_percep_viajes").length > 0) this.$("#facturacion_item_percep_viajes").val(this.articulo.get("percep_viajes"));
      if (this.$("#facturacion_item_custom_2").length > 0) {
        if (!isEmpty(this.articulo.get("custom_2"))) this.$("#facturacion_item_custom_2").val(this.articulo.get("custom_2"));
        else this.$("#facturacion_item_custom_2").val((this.articulo.get("porc_bonif") > 0)?"1":"");
      }

      if (ID_EMPRESA == 868) {
        // En MEGASHOP CENTRAL, guardamos en facturas_items.custom_4 el valor de articulos.custom_1
        this.$("#facturacion_item_custom_4").val(this.articulo.get("custom_1"));
      }

      // Guardamos el ID del primer proveedor
      if (this.$("#facturacion_item_id_proveedor").length > 0) {
        var proveedores = this.articulo.get("proveedores");
        if (typeof proveedores != undefined && proveedores.length > 0) {
          var prov = proveedores[0];
          this.$("#facturacion_item_id_proveedor").val(prov.id_proveedor);
        }
      }
      
      var lista = (this.$("#facturacion_lista").length > 0) ? this.$("#facturacion_lista").val() : 0;

      // Si estamos usando un numero de PLU, el precio es el de la etiqueta
      if (typeof this.precio_fijo != "undefined") {
        // El precio es final, le sacamos el iva
        var neto = Number(this.precio_fijo) / (1+(Number(this.articulo.get("porc_iva"))/100));
        this.$("#facturacion_item_neto").val(neto);
        this.$("#facturacion_item_precio").val(Number(this.precio_fijo));
      } else {
        // Dependiendo de la lista que estamos usando
        if (typeof FACTURACION_VENDER_AL_COSTO != "undefined" && FACTURACION_VENDER_AL_COSTO == 1) {
          this.$("#facturacion_item_neto").val(this.articulo.get("costo_neto"));
          this.$("#facturacion_item_precio").val(this.articulo.get("costo_final"));
        } else {
          if (lista == 0) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto"));  
          } else if (lista == 1) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto_2"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto_2"));
          } else if (lista == 2) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto_3"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto_3"));
          } else if (lista == 3) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto_4"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto_4"));
          } else if (lista == 4) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto_5"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto_5"));
          } else if (lista == 5) {
            this.$("#facturacion_item_neto").val(this.articulo.get("precio_neto_6"));
            this.$("#facturacion_item_precio").val(this.articulo.get("precio_final_dto_6"));
          }
        }
      }

      // Si es un articulo de la CANASTA BASICA
      /*
      if (this.articulo.get("custom_5") == "1" && this.cliente_canasta_basica) {
        // El precio que tenemos que vender es SIN IVA
        this.$("#facturacion_item_precio").val(this.$("#facturacion_item_neto").val());
      }
      */

      if (typeof FACTURACION_CONSULTAR_STOCK != "undefined" && FACTURACION_CONSULTAR_STOCK == 1) {
        this.$("#facturacion_item_stock").val(this.articulo.get("stock"));
      }

      this.render_variantes(this.articulo.get("variantes"),0);
    },

    limpiar_variantes: function() {
      this.$("#facturacion_variantes").empty();
      this.$("#facturacion_variantes").attr("disabled","disabled");
    },

    render_variantes: function(variantes,id_seleccionado) {
      this.limpiar_variantes();
      if (typeof variantes == undefined || variantes == null) return;
      if (typeof id_seleccionado == undefined || id_seleccionado == null) id_seleccionado = 0;
      if (variantes.length > 0) {
        this.$("#facturacion_variantes").removeAttr("disabled");
        for(var i=0; i<variantes.length; i++) {
          var v = variantes[i];
          var option = "<option "+((id_seleccionado == v.id)?"selected":"")+" value='"+v.id+"'>"+v.nombre+"</option>";
          this.$("#facturacion_variantes").append(option);
        }
      }
    },

    // Corrobora si en el cliente hay una etiqueta que sea REMITO
    check_etiqueta_remito: function() {
      return this.check_etiqueta("remito");
    },

    check_etiqueta: function(tag) {
      var self = this;
      tag = tag.toLowerCase();
      var salida = false;
      if (typeof self.cliente != undefined) {
        var etiquetas = self.cliente.get("etiquetas");
        if (typeof etiquetas != undefined) {
          if (etiquetas.length > 0) {
            for(var pp=0;pp<etiquetas.length;pp++) {
              var etiqueta = etiquetas[pp];
              etiqueta = etiqueta.toLowerCase()
              if (tag == etiqueta) {
                salida = true;
                break;
              }
            }
          }
        }
      }
      return salida;
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;
      if (FACTURACION_TIPO != "pv") {
        var codigo = this.$("#facturacion_codigo_articulo").val();
        if (isEmpty(codigo)) {
          alert("Por favor escriba o seleccione un articulo.");
          this.$("#facturacion_codigo_articulo").focus();
          return;
        }        
      } else {
        if (this.agregando == 1) return;
        this.agregando = 1;
        var codigo = this.$("#facturacion_item_nombre").val();
      }
      
      // Limite de productos en factura
      if (FACTURACION_CANTIDAD_ITEMS != 0 && this.items.size() == FACTURACION_CANTIDAD_ITEMS) {
        if (confirmar("Ha llegado al limite de productos para el comprobante. Desea cerrarlo?")) {
          self.aceptar();
        }
        return;
      }
      
      var id_rubro = (this.$("#facturacion_rubro").length > 0) ? this.$("#facturacion_rubro").val() : 0;
      var id_departamento = (this.$("#facturacion_departamento").length > 0) ? this.$("#facturacion_departamento").val() : 0;
      var id_tipo_alicuota_iva = this.$("#facturacion_alicuotas_iva").val();
      var tipo_item = this.$("#facturacion_tipo_item").val();
      var costo_final = (this.$("#facturacion_costo_final").length > 0) ? this.$("#facturacion_costo_final").val() : 0;
      var id_tipo_comprobante = this.$("#facturacion_tipo").val();
      var porc_iva = parseFloat(this.$("#facturacion_alicuotas_iva option:selected").data("porcentaje"));
      var id_articulo = this.$("#facturacion_id_articulo").val();

      if (typeof FACTURACION_INGRESAR_SOLO_PRODUCTOS != "undefined" && FACTURACION_INGRESAR_SOLO_PRODUCTOS == 1 && id_articulo == 0) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#facturacion_codigo_articulo").select();
        return;
      }
      if (TIPO_EMPRESA != undefined && TIPO_EMPRESA != 3 && (typeof FACTURACION_PERMITIR_EDICION_FACTURA == "undefined")) {
        if (id_articulo != 0 && !(this.model.id == undefined || this.model.id == 0)) {
          if (this.item == undefined) {
            alert("No se pueden agregar articulos a un comprobante ya guardado.");
          } else {
            alert("No se puede modificar un articulo de un comprobante ya guardado.");
          }
          this.$("#facturacion_codigo_articulo").select();
          return;
        }
      }
      
      var descripcion = this.$("#facturacion_item_descripcion").val();
      var percep_viajes = (this.$("#facturacion_item_percep_viajes").length > 0) ? this.$("#facturacion_item_percep_viajes").val() : 0;
      var custom_2 = (this.$("#facturacion_item_custom_2").length > 0) ? this.$("#facturacion_item_custom_2").val() : "";
      var custom_3 = (this.$("#facturacion_item_custom_3").length > 0) ? this.$("#facturacion_item_custom_3").val() : "";
      var custom_4 = (this.$("#facturacion_item_custom_4").length > 0) ? this.$("#facturacion_item_custom_4").val() : "";
      var id_proveedor = (this.$("#facturacion_item_id_proveedor").length > 0) ? this.$("#facturacion_item_id_proveedor").val() : 0;
      var id_variante = (this.$("#facturacion_variantes").length > 0) ? this.$("#facturacion_variantes").val() : 0;
      var variante = (this.$("#facturacion_variantes option").length > 0) ? this.$("#facturacion_variantes option:selected").text() : "";
      
      var cantidad = this.$("#facturacion_item_cantidad").val();
      if (MEGASHOP == 1) {
        // Megashop no permite decimales
        if (isEmpty(cantidad)) cantidad = 1;
        var cantidad_decimales = parseFloat(cantidad) + 0;
        var cantidad_sin_decimales = parseInt(Number(cantidad).toFixed(0));
        if (cantidad_decimales != cantidad_sin_decimales) {
          alert("ERROR: La cantidad no puede tener decimales.");
          this.$("#facturacion_item_cantidad").val("");
          this.$("#facturacion_codigo_articulo").select();
          self.agregando = 0;
          return;
        }
      } else {
        cantidad = cantidad.replace(",",".");  
      }
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES); }

      if (typeof FACTURACION_CONSULTAR_STOCK != "undefined" && FACTURACION_CONSULTAR_STOCK == 1) {
        var stock = parseFloat(this.$("#facturacion_item_stock").val());
        if (cantidad > stock) {
          alert("ATENCION: Se esta vendiendo una cantidad mayor al stock disponible: "+stock+".");
        }
      }
      
      var bonificacion = this.$("#facturacion_item_bonificado").val();
      if (bonificacion > 100) {
        alert("ERROR: La bonificacion no puede ser mayor a 100.");
        this.$("#facturacion_item_bonificado").select();
        self.agregando = 0;
        return;
      } else if (bonificacion < 0 && ID_EMPRESA != 574 && ID_EMPRESA != 1326) {
        alert("ERROR: La bonificacion no puede ser menor a 0.")
        this.$("#facturacion_item_bonificado").select();
        self.agregando = 0;
        return;
      }

      // IMPORT SHOW TIENE ALGUNOS DESCUENTOS ESPECIALES
      // Si el cliente tiene la etiqueta "empleado"
      if (ID_EMPRESA == 356) {
        var es_empleado = self.check_etiqueta("empleado");
        var es_encargado = self.check_etiqueta("encargado");
        if (es_empleado || es_encargado) {
          // Entonces aplicamos el descuento por departamento
          for(var w=0;w<window.departamentos_comerciales.length; w++) {
            var depto = window.departamentos_comerciales[w];
            if (depto.id == id_departamento) {
              var desc = parseFloat(depto.descuento);
              if (es_encargado) {
                if (desc == 15) desc = 25;
                else if (desc == 20) desc = 35;
              }
              bonificacion = desc;
              break;
            }
          }
        }
      }
      
      if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
        // DEBEMOS TOMAR EL NETO PARA REALIZAR EL REMITO (Lo usa CORRUGADOS)
        var precio = parseFloat(this.$("#facturacion_item_neto").val());
        var neto = precio; //precio / (1+(porc_iva / 100));
      } else {
        if (this.discrimina_iva()) {
          var neto = parseFloat(this.$("#facturacion_item_neto").val());
          var precio = neto * (1+(porc_iva / 100));
        } else {
          // El precio que figura es el FINAL
          var precio = parseFloat(this.$("#facturacion_item_precio").val());
          var neto = precio / (1+(porc_iva / 100));
        }
      }

      if (this.$("#facturacion_moneda").length > 0 && !isEmpty(this.$("#facturacion_moneda").val())) {
        if (this.$("#facturacion_moneda").val() == "2" && typeof COTIZACION_DOLAR != "undefined") {
          cotizacion = parseFloat(COTIZACION_DOLAR);
          neto = neto * cotizacion;
          precio = precio * cotizacion;
          costo_final = costo_final * cotizacion;
        }
      }

      // OFERTA -22% MORENO
      if (MEGASHOP == 1 && ID_SUCURSAL == 21 && moment().format("DD/MM/YYYY") == "22/09/2018" && custom_2 != "1" ) {
        bonificacion = 22;
        custom_2 = 1;
      }

      var iva = Math.round(neto * ((100-bonificacion)/100) * (porc_iva / 100) * cantidad * 100) / 100;
      var total_sin_iva = neto * ((100-bonificacion)/100) * cantidad;
      var total_con_iva = precio * ((100-bonificacion)/100) * cantidad;
      var costo_final = parseFloat(costo_final) * cantidad;
      
      var values = {
        "id_articulo":id_articulo,
        "neto":neto,
        "precio":precio,
        "nombre":codigo,
        "descripcion":descripcion,
        "id_rubro":id_rubro,
        "id_departamento":id_departamento,
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "tipo":tipo_item,
        "iva":iva,
        "porc_iva":porc_iva,
        "percep_viajes":percep_viajes,
        "total_sin_iva":total_sin_iva,
        "total_con_iva":total_con_iva,
        "discrimina_iva":self.discrimina_iva(),
        "costo_final":costo_final,
        "custom_1":((typeof FACTURACION_GUARDAR_LEIDO != "undefined" && FACTURACION_GUARDAR_LEIDO == 1 && typeof self.codigo_leido != "undefined") ? self.codigo_leido : ""),
        "custom_2":custom_2, // Si tiene promocion
        "custom_3":custom_3, // Si reservo stock
        "custom_4":custom_4,
        "id_proveedor":id_proveedor,
        "id_variante":id_variante,
        "variante":variante,
        "variantes":((typeof self.articulo != "undefined" && self.articulo != null) ? self.articulo.get("variantes") : new Array()),
      };

      var etiqueta_remito = self.check_etiqueta_remito();

      // Si estamos utilizando un controlador fiscal
      // En la variable window.controlador_fiscal tenemos el modelo que esta configurado
      if (ESTADO != 1 && FACTURACION_TIPO == "pv" && controlador_fiscal != "" && !etiqueta_remito) {

        // Indica si es el primer elemento del ticket o no
        values.comienzo = (this.items.length == 0) ? 1 : 0;
        // Datos del cliente
        values.id_cliente = (isEmpty($("#facturacion_id_cliente").val())) ? 0 : $("#facturacion_id_cliente").val();
        // Porcentaje de IVA
        values.porc_iva = porc_iva;
        // Tipo de comprobante
        values.id_tipo_comprobante = id_tipo_comprobante;

        // Si se debe ir imprimiendo a medida que se va vendiendo
        if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal != "") {
          // Encolamos el AJAX para que si se vende muy rapido, se facture siempre en el mismo orden
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/imprimir_item/",
            "dataType":"json",
            "type":"post",
            "data": values,
            "cache":false,
            "error":function() {
              alert("ERROR: Controlar si el impresor esta encendido o bien conectado.");
              self.anular_action();
              self.agregando = 0;
              // En caso de error, cancelar el ticket
            }
          });
        }
      }

      // Actualizamos o agregamos el item
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.FacturaItem(values);
        this.items.add(item);
      }

      // PROMOCION PARA MORENO
      /*
        var total_dto = values.total_con_iva * 0.22 * -1;
        var values = {
          "id_articulo":0,
          "neto":total_dto,
          "precio":total_dto,
          "nombre":"DESCUENTO -22%",
          "descripcion":"",
          "id_rubro":0,
          "id_tipo_alicuota_iva":3,
          "cantidad":1,
          "bonificacion":0,
          "tipo":tipo_item,
          "iva":0,
          "porc_iva":0,
          "percep_viajes":0,
          "total_sin_iva":total_dto,
          "total_con_iva":total_dto,
          "discrimina_iva":false,
          "costo_final":total_dto,
          "custom_1":"",
          "custom_2":"",
          "custom_3":"",
          "id_proveedor":0,
          "id_variante":0,
          "variante":"",
          "variantes":new Array(),
        };
        var item = new app.models.FacturaItem(values);
        this.items.add(item);
      }
      */
      
      delete this.precio_fijo;
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#facturacion_codigo_articulo").select();        

      this.calcular_ofertas();

      if (FACTURACION_TIPO == "pv") {
        // Movemos la tabla hacia abajo
        this.$('#tabla_items').parent().scrollTop(self.$('#tabla_items').parent()[0].scrollHeight);
      }      
    },

    descuento_efectivo_mega: function() {
      this.$("#facturacion_codigo_articulo").val("1111");
      var e = jQuery.Event("keypress");
      e.which = 13;
      this.$("#facturacion_codigo_articulo").trigger(e);
    },

    oferta_cubiertos_mega: function() {
      var self = this;

      // Primero corroboramos que no este aplicada ya
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        if (item.get("id_articulo") == "10294937") {
          return;
        }
      }

      // Limpiamos todos los precios de los siguientes articulos
      var encontro = false;
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        var codigo = item.get("id_articulo");
        if (codigo == "10272264" || codigo == "10272263" || codigo == "159345" || codigo == "10200874" || codigo == "10266873"
           || codigo == "300070" || codigo == "304945" || codigo == "153269" || codigo == "301411" || codigo == "304954" || codigo == "300071" || codigo == "153270" || codigo == "206641" || codigo == "300072" || codigo == "300073"
           || codigo == "300074" || codigo == "304944" || codigo == "153267" || codigo == "301412" || codigo == "304953" || codigo == "300075" || codigo == "153268" || codigo == "206640" || codigo == "300076" || codigo == "300077"
           || codigo == "300066" || codigo == "304943" || codigo == "153265" || codigo == "301410" || codigo == "304952" || codigo == "300067" || codigo == "153266" || codigo == "206639" || codigo == "300068" || codigo == "300069"
           || codigo == "300062" || codigo == "304942" || codigo == "153263" || codigo == "304950" || codigo == "304951" || codigo == "300063" || codigo == "153264" || codigo == "206638" || codigo == "300064" || codigo == "300065"
           || codigo == "10242298" || codigo == "10242299" || codigo == "10242300" || codigo == "10242301" || codigo == "10242302" || codigo == "10242303"
           || codigo == "10242292" || codigo == "10242293" || codigo == "10242294" || codigo == "10242295" || codigo == "10242296" || codigo == "10242297"
           || codigo == "10242286" || codigo == "10242287" || codigo == "10242288" || codigo == "10242289" || codigo == "10242290" || codigo == "10242291"
           || codigo == "10242280" || codigo == "10242281" || codigo == "10242282" || codigo == "10242283" || codigo == "10242284" || codigo == "10242285"
           || codigo == "10272266" || codigo == "159345" || codigo == "297336" || codigo == "10272266"
           || codigo == "10198043" || codigo == "297336" || codigo == "10272262" || codigo == "10272266"
           || codigo == "10226299" || codigo == "10226300"
        ) {
          item.set({
            "precio":0,
            "neto":0,
            "total_sin_iva":0,
            "total_con_iva":0,
            "tipo_cantidad":"X", // Marca para ver que el usuario cambio de precio
          });
          encontro = true;
        }
      };

      if (encontro) {
        this.$("#facturacion_codigo_articulo").val("11111");
        var e = jQuery.Event("keypress");
        e.which = 13;
        this.$("#facturacion_codigo_articulo").trigger(e);
      }
    },

    oferta_platos_mega: function() {
      var self = this;

      // Primero corroboramos que no este aplicada ya
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        if (item.get("id_articulo") == "10295784") {
          return;
        }
      }

      // Limpiamos todos los precios de los siguientes articulos
      var encontro = false;
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        var codigo = item.get("id_articulo");
        if (codigo == "306511" || codigo == "306516" || codigo == "306522" || codigo == "306528" || codigo == "306593" || codigo == "306594" || codigo == "306595"
          || codigo == "306512" || codigo == "306517" || codigo == "306523" || codigo == "306529" || codigo == "306596" || codigo == "306597" || codigo == "306598"
          || codigo == "306513" || codigo == "306518" || codigo == "306524" || codigo == "306530" || codigo == "306599" || codigo == "306600" || codigo == "306601"
          || codigo == "306514" || codigo == "306519" || codigo == "306525" || codigo == "306531" || codigo == "306602" || codigo == "306603" || codigo == "306604" || codigo == "10268239" || codigo == "10267537"
          || codigo == "306521" || codigo == "306520" || codigo == "306527" || codigo == "306533" || codigo == "306609" || codigo == "306611" || codigo == "306610"
        ) {
          item.set({
            "precio":0,
            "neto":0,
            "total_sin_iva":0,
            "total_con_iva":0,
            "tipo_cantidad":"X", // Marca para ver que el usuario cambio de precio
          });
          encontro = true;
        }
      };

      if (encontro) {
        this.$("#facturacion_codigo_articulo").val("11112");
        var e = jQuery.Event("keypress");
        e.which = 13;
        this.$("#facturacion_codigo_articulo").trigger(e);
      }
    },

    oferta_cubiertero_mega: function() {
      var self = this;

      // Primero corroboramos que no este aplicada ya
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        if (item.get("id_articulo") == "10295784") {
          return;
        }
      }

      // Limpiamos todos los precios de los siguientes articulos
      var encontro = false;
      for(var i=0; i< self.items.models.length; i++) {
        var item = self.items.models[i];
        var codigo = item.get("id_articulo");
        if (codigo == "109933"
           || codigo == "300070" || codigo == "304945" || codigo == "153269" || codigo == "301411" || codigo == "304954" || codigo == "300071" || codigo == "153270" || codigo == "206641" || codigo == "300072" || codigo == "300073"
           || codigo == "300074" || codigo == "304944" || codigo == "153267" || codigo == "301412" || codigo == "304953" || codigo == "300075" || codigo == "153268" || codigo == "206640" || codigo == "300076" || codigo == "300077"
           || codigo == "300066" || codigo == "304943" || codigo == "153265" || codigo == "301410" || codigo == "304952" || codigo == "300067" || codigo == "153266" || codigo == "206639" || codigo == "300068" || codigo == "300069"
           || codigo == "300062" || codigo == "304942" || codigo == "153263" || codigo == "304950" || codigo == "304951" || codigo == "300063" || codigo == "153264" || codigo == "206638" || codigo == "300064" || codigo == "300065"
           || codigo == "10242298" || codigo == "10242299" || codigo == "10242300" || codigo == "10242301" || codigo == "10242302" || codigo == "10242303"
           || codigo == "10242292" || codigo == "10242293" || codigo == "10242294" || codigo == "10242295" || codigo == "10242296" || codigo == "10242297"
           || codigo == "10242286" || codigo == "10242287" || codigo == "10242288" || codigo == "10242289" || codigo == "10242290" || codigo == "10242291"
           || codigo == "10242280" || codigo == "10242281" || codigo == "10242282" || codigo == "10242283" || codigo == "10242284" || codigo == "10242285"
        ) {
          item.set({
            "precio":0,
            "neto":0,
            "total_sin_iva":0,
            "total_con_iva":0,
            "tipo_cantidad":"X", // Marca para ver que el usuario cambio de precio
          });
          encontro = true;
        }
      };

      if (encontro) {
        this.$("#facturacion_codigo_articulo").val("11113");
        var e = jQuery.Event("keypress");
        e.which = 13;
        this.$("#facturacion_codigo_articulo").trigger(e);
      }
    },

    calcular_ofertas: function() {

      var self = this;

      if (typeof FACTURACION_VENDER_AL_COSTO != "undefined" && FACTURACION_VENDER_AL_COSTO == 1) return; 

      // OFERTA DE BAZAR
      // Cada 3 productos, 1 va bonificado (tenemos que ir eligiendolos a medida que se van ingresando)
      if (MEGASHOP == 1) {

        if (moment().isBetween('2018-12-10', '2019-01-06')) { 
          // En primer lugar, formamos un array con todos los items de bazar
          // en caso de que un item tenga cantidades, lo ponemos tantas veces como cantidad haya en el array
          // No debemos tener en cuenta anulados
          var items_bazar = new Array();
          var pos = 0;
          self.items.each(function(item){
            if (item.get("id_departamento") == 5 // De NAVIDAD
              && item.get("id_rubro") != 38 // Que no sean luces de navidad
              && item.get("anulado") == 0 // Que no este anulado
              && item.get("custom_2") == "" // Que no este en oferta
              && ID_SUCURSAL != 23
              && self.$("#facturacion_codigo_cliente").val() == "Consumidor Final") { // Que no sea un cliente especifico

              // Buscamos si ya existe en el array
              var encontro_array = false;
              for(var i=0;i<items_bazar.length;i++) {
                var bb = items_bazar[i];
                if (bb.id_articulo == item.get("id_articulo") && bb.monto == item.get("precio")) {
                  items_bazar[i].cantidad += parseFloat(item.get("cantidad"));
                  encontro_array = true;
                  break;
                }
              }
              if (!encontro_array) {
                var precio = parseFloat(item.get("precio"));
                if (isNaN(precio)) precio = 0;              
                items_bazar.push({
                  "id_articulo":item.get("id_articulo"),
                  "id_rubro":item.get("id_rubro"),
                  "id_proveedor":item.get("id_proveedor"),
                  "costo_final":item.get("costo_final"),
                  "nombre":item.get("nombre"),
                  "monto":precio,
                  "cantidad":parseFloat(item.get("cantidad")),
                  "posicion":pos,
                });
              }

            }
            pos++;
          });

          // Aplanamos el array, dejando 1 elemento por cada cantidad de articulo
          var items_bazar_2 = new Array();
          for(var i=0;i<items_bazar.length;i++) {
            var bb = items_bazar[i];
            if (bb.cantidad > 0) {
              for(var j=0;j<bb.cantidad;j++) {
                items_bazar_2.push({
                  "id_articulo":bb.id_articulo,
                  "id_rubro":bb.id_rubro,
                  "id_proveedor":bb.id_proveedor,
                  "costo_final":bb.costo_final,
                  "nombre":bb.nombre,
                  "monto":bb.monto,
                  "posicion":bb.posicion,
                });
              }            
            }
          }
          items_bazar = items_bazar_2;


          // Reordenamos los items de acuerdo al precio
          var bazar_ordenado = _.sortBy(items_bazar,"monto");

          // Sacamos todas los items anteriores
          this.$("#tabla_items tbody tr").removeClass("fila_roja_2");
          this.bonificaciones = new Array();

          // Hay que tomar tantos items como modulo 3
          for(var i=0;i<Math.floor(bazar_ordenado.length / 3);i++) {
            var b = bazar_ordenado[i];
            this.$("#tabla_items tbody tr:eq("+b.posicion+")").addClass("fila_roja_2");
            this.bonificaciones.push(b);
          }
          this.render_bonificaciones();
          if (this.bonificaciones.length > 0) return; // El descuento de bazar es prioritario, no se acumula con otras ofertas

          // Codigo 2222, aplica un 20% de descuento a la proxima compra
          /*
          var encontro_codigo = 0;
          self.items.each(function(item){
            if (item.get("id_articulo") == 10213586) {
              encontro_codigo = (item.get("anulado") == 0) ? 1 : 2;
              return;
            }
          });
          if (encontro_codigo == 1) {
            this.$("#facturacion_porc_descuento").val(20);
            this.model.set({"aplico_codigo_descuento":1});
            this.calcular_totales();
            return;
          } else if (encontro_codigo == 2) {
            this.$("#facturacion_porc_descuento").val(0);
            this.model.set({"aplico_codigo_descuento":0});
            this.calcular_totales();
            return;          
          }
          */
        }



        if (moment().isBetween('2019-01-19', '2019-05-01') && ID_SUCURSAL == 22) { 
        //if (moment().isBetween('2019-01-19', '2019-05-01')) { 
          // En primer lugar, formamos un array con todos los items de bazar
          // en caso de que un item tenga cantidades, lo ponemos tantas veces como cantidad haya en el array
          // No debemos tener en cuenta anulados
          var items_bazar = new Array();
          var pos = 0;
          self.items.each(function(item){
            if (item.get("id_departamento") == 4 // De TIENDA
              && item.get("id_rubro") != 16 // Que no sea lenceria
              && item.get("id_rubro") != 19 // Que no sea ropa interior hombre
              && item.get("id_rubro") != 20 // Que no sea textil
              && item.get("id_rubro") != 12 // Que no sea blanco
              && item.get("id_rubro") != 143 // Que no sea marroquineria
              && item.get("id_rubro") != 13 // Que no sea calzado
              && item.get("id_rubro") != 36 // Que no sea otros bazar
              && item.get("id_rubro") != 66 // Que no sea art para bano
              && item.get("id_rubro") != 35 // Que no sea cocina
              && item.get("id_rubro") != 31 // Que no sea linea plastico
              && item.get("id_rubro") != 5 // Que no sea rodados
              && item.get("id_rubro") != 41 // Que no sea mochilas
              && item.get("id_rubro") != 115 // Que no sea especieros
              && item.get("id_rubro") != 22 // Que no sea accesorios de mesa
              && item.get("id_rubro") != 140 // Que no sea otros
              && item.get("id_rubro") != 18 // Que no sea juegos
              && item.get("id_rubro") != 118 // Que no sea pavas
              && item.get("id_rubro") != 371 // Que no sea manteles
              && item.get("id_rubro") != 184 // Que no sea repasadores
              && item.get("id_rubro") != 294 // Que no sea paraguas
              && item.get("id_rubro") != 212 // Que no sea almohadon
              && item.get("id_rubro") != 353 // Que no sea ojotas
              && item.get("id_rubro") != 54 // Que no sea art de verano
              && item.get("anulado") == 0 // Que no este anulado
              && item.get("custom_2") == "" // Que no este en oferta
              && self.$("#facturacion_codigo_cliente").val() == "Consumidor Final") { // Que no sea un cliente especifico

              // Buscamos si ya existe en el array
              var encontro_array = false;
              for(var i=0;i<items_bazar.length;i++) {
                var bb = items_bazar[i];
                if (bb.id_articulo == item.get("id_articulo") && bb.monto == item.get("precio")) {
                  items_bazar[i].cantidad += parseFloat(item.get("cantidad"));
                  encontro_array = true;
                  break;
                }
              }
              if (!encontro_array) {
                var precio = parseFloat(item.get("precio"));
                if (isNaN(precio)) precio = 0;              
                items_bazar.push({
                  "id_articulo":item.get("id_articulo"),
                  "id_rubro":item.get("id_rubro"),
                  "id_proveedor":item.get("id_proveedor"),
                  "costo_final":item.get("costo_final"),
                  "nombre":item.get("nombre"),
                  "monto":precio,
                  "cantidad":parseFloat(item.get("cantidad")),
                  "posicion":pos,
                });
              }

            }
            pos++;
          });

          // Aplanamos el array, dejando 1 elemento por cada cantidad de articulo
          var items_bazar_2 = new Array();
          for(var i=0;i<items_bazar.length;i++) {
            var bb = items_bazar[i];
            if (bb.cantidad > 0) {
              for(var j=0;j<bb.cantidad;j++) {
                items_bazar_2.push({
                  "id_articulo":bb.id_articulo,
                  "id_rubro":bb.id_rubro,
                  "id_proveedor":bb.id_proveedor,
                  "costo_final":bb.costo_final,
                  "nombre":bb.nombre,
                  "monto":bb.monto,
                  "posicion":bb.posicion,
                });
              }            
            }
          }
          items_bazar = items_bazar_2;


          // Reordenamos los items de acuerdo al precio
          var bazar_ordenado = _.sortBy(items_bazar,"monto");

          // Sacamos todas los items anteriores
          this.$("#tabla_items tbody tr").removeClass("fila_roja_2");
          this.bonificaciones = new Array();

          // Hay que tomar tantos items como modulo 2
          for(var i=0;i<Math.floor(bazar_ordenado.length / 2);i++) {
            var b = bazar_ordenado[i];
            this.$("#tabla_items tbody tr:eq("+b.posicion+")").addClass("fila_roja_2");
            this.bonificaciones.push(b);
          }
          this.render_bonificaciones();
          if (this.bonificaciones.length > 0) return; // El descuento de bazar es prioritario, no se acumula con otras ofertas
        }



        // Codigo 1111, aplica un 10% de descuento a la compra
        var encontro_codigo = 0;
        self.items.each(function(item){
          if (item.get("id_articulo") == 10216830) {
            encontro_codigo = (item.get("anulado") == 0) ? 1 : 2;
            return;
          }
        });
        if (encontro_codigo == 1) {
          this.$("#facturacion_porc_descuento").val(20);
          this.model.set({"aplico_codigo_descuento":1});
          this.calcular_totales();
          return;
        } else if (encontro_codigo == 2) {
          this.$("#facturacion_porc_descuento").val(0);
          this.model.set({"aplico_codigo_descuento":0});
          this.calcular_totales();
          return;          
        }

      } // Fin MEGASHOP

      this.ofertas = new Array(); // Limpiamos las ofertas para volverlas a calcular

      // COMBOS DE ARTICULOS
      if (typeof window.reglas_ofertas != "undefined") { 

        this.ofertas = new Array();
        this.pila_items = new Array();

        // Armamos un array de items, con el ID del articulo y la cantidad actual
        self.items.each(function(item){
          var encontro = false;
          if (item.get("anulado") == 0) {
            for (var i = 0; i < self.pila_items.length; i++) {
              var it2 = self.pila_items[i];
              if ((it2.id_articulo == item.get("id_articulo"))) {
                self.pila_items[i].cantidad = parseFloat(it2.cantidad) + parseFloat(item.get("cantidad"));
                encontro = true;
              }
            }
            if (!encontro) {
              self.pila_items.push({
                "id_articulo":item.get("id_articulo"),
                "cantidad": parseFloat(item.get("cantidad")),
              });
            }
          }
        });

        // Recorremos las reglas y sumamos las cantidades de articulos que hay en cada caso
        _.each(window.reglas_ofertas,function(regla){

          regla.cantidad_minima = parseFloat(regla.cantidad_minima);

          // Recorremos las condiciones
          for (var i = 0; i < regla.articulos.length; i++) {
            // Recorremos los items, y buscamos si hay facturado ese articulo
            regla.cantidad_total = 0;
            _.each(self.pila_items,function(e){
              _.each(regla.articulos[i],function(art){
                if (e.id_articulo == art.id_articulo) regla.cantidad_total += parseFloat(e.cantidad);
              });
            });
          }

          var cantidad_veces_regla = 0;
          var corte = false;
          while(!corte) {
            var articulos_aplicados = new Array();
            var cantidad_condiciones_aceptadas = 0; // Controlamos la cantidad de veces que se aplica la regla
            for (var k = 0; k < regla.articulos.length; k++) {
              var aplica_condicion = false;
              var aplica_condicion = _.some(regla.articulos[k],function(cond){
                var item = _.find(self.pila_items,function(it){
                  return (cond.id_articulo == it.id_articulo);
                });
                if (typeof item == "undefined") return false;
                if (item.cantidad >= cond.minimo && cond.minimo > 0) {
                  articulos_aplicados.push({ "id_articulo":item.id_articulo, "cantidad": cond.minimo });
                  return true;
                }
              });
              if (aplica_condicion) {
                cantidad_condiciones_aceptadas++;
              }
            }

            // Tenemos que controlar la cantidad minima total de articulos
            if (regla.cantidad_minima > 0) {
              if (regla.cantidad_total >= regla.cantidad_minima) {
                regla.cantidad_total = regla.cantidad_total - regla.cantidad_minima;
                cantidad_veces_regla++;
              } else {
                corte = true;
              }
            } else {
              
              // Controlamos la cantidad de condiciones aceptadas por cada una de las reglas
              if (cantidad_condiciones_aceptadas == regla.articulos.length) {
                // Se aplica la regla
                cantidad_veces_regla++;

                // Descontamos los articulos de la cantidad
                _.each(articulos_aplicados,function(art){
                  for (var i = 0; i < self.pila_items.length; i++) {
                    if (self.pila_items[i].id_articulo == art.id_articulo) {
                      self.pila_items[i].cantidad = self.pila_items[i].cantidad - parseFloat(art.cantidad);
                    }
                  }
                });

              } else {
                corte = true;
              }
            }
          }

          if (cantidad_veces_regla > 0) {
            self.ofertas.push({
              "id_regla":regla.id,
              "nombre":regla.nombre,
              "monto":parseFloat(regla.descuento_fijo * cantidad_veces_regla),
              "cantidad":cantidad_veces_regla,
              "unitario":regla.descuento_fijo,
            });
          }

        });
      }
      this.render_ofertas();
    },

    render_ofertas: function() {
      this.$("#facturacion_ofertas_cont").empty();
      this.total_ofertas = 0;
      for (var i = 0; i < this.ofertas.length; i++) {
        var o = this.ofertas[i];
        var l = '';
        l+='<div class="form-group cb">';
        l+='<label class="control-label col-xs-6">'+o.nombre+'</label>';
        l+='<div class="col-xs-6"><input type="text" value="'+Number(o.monto).toFixed(2)+'" disabled class="no-input"/></div>';
        l+='</div>';
        this.$("#facturacion_ofertas_cont").append(l);
        this.total_ofertas += parseFloat(o.monto);
      }
      this.$("#facturacion_ofertas_cont").css("display",((this.ofertas.length>0)?"block":"none"));
      this.calcular_totales();
    },

    render_bonificaciones: function() {
      this.$("#facturacion_bonificaciones_container").empty();
      this.total_bonificaciones = 0;
      for (var i = 0; i < this.bonificaciones.length; i++) {
        var o = this.bonificaciones[i];
        var l = '';
        l+='<div class="form-group cb">';
        l+='<label class="control-label col-xs-8 fs14 text-muted">'+o.nombre+'</label>';
        l+='<div class="col-xs-4"><input type="text" value="'+Number(o.monto).toFixed(2)+'" disabled class="fs14 no-input"/></div>';
        l+='</div>';
        this.$("#facturacion_bonificaciones_container").append(l);
        this.total_bonificaciones += parseFloat(o.monto);
      }
      this.$("#facturacion_bonificaciones_cont").css("display",((this.bonificaciones.length>0)?"block":"none"));
      this.calcular_totales();
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#facturacion_item_cantidad").val();
      if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
        var precio_unit = this.$("#facturacion_item_neto").val();
      } else {
        if (this.discrimina_iva()) {
          var precio_unit = this.$("#facturacion_item_neto").val();
        } else {
          var precio_unit = this.$("#facturacion_item_precio").val();
        }
      }
      var bonificado = this.$("#facturacion_item_bonificado").val();
      var subtotal = Number( (cantidad * precio_unit) * ((100-bonificado)/100) ).toFixed(FACTURACION_CANTIDAD_DECIMALES);
      this.$("#facturacion_item_subtotal").val(subtotal);
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.agregando = 0;
      this.ofertas = new Array();
      this.bonificaciones = new Array();
      this.total_ofertas = 0;
      this.total_bonificaciones = 0;
      this.options = options;
      _.bindAll(this);
      this.bind("limpiar",this.limpiar);
      this.codigo_leido = "";

      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {
        this.limpiar();

      // Estamos editando
      } else {
        
        this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
        
        this.render();
        
        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.FacturaItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.FacturaItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);        
        
        // Buscamos el cliente y lo seteamos
        var id_cliente = self.model.get("id_cliente");
        if (id_cliente == 0) {
          this.setear_consumidor_final();
        } else {
          var cliente = new app.models.Cliente({"id":id_cliente});
          cliente.fetch({
            "success":function() {
              self.seleccionar_cliente(cliente);
            },
          });
        }

        // Si la factura estaba marcada como NUEVA, tenemos que ir al servidor y desmarcarla
        if (this.model.get("nueva") == 1) {
          $.ajax({
            "url":"facturas/function/marcar_visto/"+self.model.id+"/"+self.model.get("id_punto_venta")+"/",
            "dataType":"json",
          });
        }
      }
    },

    render: function() {
      
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;

      // Si la factura fue emitida y estamos editando, no se podria cambiar absolutamente nada
      var edicion = ( ((!(this.model.id == undefined || this.model.id == 0)) && (this.model.get("id_tipo_comprobante") < 900 || (typeof NO_EDITAR_FACTURA != "undefined") )) ? false : true);
      if (typeof FACTURACION_PERMITIR_EDICION_FACTURA != undefined) edicion = true;

      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      
      if (isEmpty(this.model.get("fecha"))) {
        this.model.set("fecha",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#facturacion_fecha"),this.model.get("fecha"));

      if (this.$("#facturacion_fecha_vto").length > 0) {
        createdatepicker(this.$("#facturacion_fecha_vto"),this.model.get("fecha_vto"));
      }
    
      this.limpiar_item();
      
      // Si esta habilitado el modulo y existe el componente
      if (control.check("vendedores")>0 && this.$("#facturacion_vendedores").length > 0) {

        var selected = ((this.model.id == undefined) ? ((typeof ID_VENDEDOR == undefined)?0:ID_VENDEDOR) : this.model.get("id_vendedor"));
        new app.mixins.Select({
          "modelClass": app.models.Vendedor,
          "url": "vendedores/",
          "render": "#facturacion_vendedores",
          "name" : "id_vendedor",
          "firstOptions": ["<option data-limite_descuento='0' value='0'>Vendedor</option>"],
          "selected": selected,
          "fields":["limite_descuento"],
        });
      }
      
      if (this.$("#facturacion_fecha_reparto").length > 0) {
        createdatepicker(this.$("#facturacion_fecha_reparto"),(isEmpty(this.model.get("fecha_reparto"))) ? new Date() : this.model.get("fecha_reparto"));
      }
      if (this.$("#facturacion_reparto").length > 0) {
        this.$("#facturacion_reparto").TouchSpin({
          verticalbuttons: true,
          min: 0,
        });
      }
      
      if (this.model.id == undefined || this.model.id == 0) {
        self.buscar_numeros();
      }
      
      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#facturacion_codigo_cliente");
      if (typeof FACTURACION_CREAR_CLIENTE != "undefined" && FACTURACION_CREAR_CLIENTE == 1) { 
        var form = new app.views.ClienteEditViewMini({
          "model": new app.models.Cliente(),
          "input": input,
          "onSave": self.seleccionar_cliente,
        });      
      } else {
        var form = null;
      }
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });
      
      var input = this.$("#facturacion_codigo_articulo");
      if (CACHE_ARTICULOS == 1) {
        $(input).customcomplete({
          "collection":articulos,
          "hideNoResults":true,
          "width":"300px",
          "label":"[nombre] ([codigo])",
          "onSelect":function(item){
            self.seleccionar_articulo(item.element);
          }
        });
      /*
      } else {
        $(input).customcomplete({
          "url":"articulos/function/get_by_descripcion/",
          "hideNoResults":true,
          "width":"300px",
          "label":"[nombre] ([codigo])",
          "onSelect":function(item){
            self.seleccionar_articulo(item.element);
          }
        });
      */
      }
      return this;
    },
    
    cambiar_numero_reparto: function() {
      if (this.$("#facturacion_reparto").length > 0) {
        var fecha = this.$("#facturacion_fecha_reparto").val();
        fecha = fecha.replace(/\//g,"-");
        $.ajax({
          "url":"facturas/function/ultimo_reparto/"+fecha,
          "dataType":"json",
          "success":function(r) {
            $("#facturacion_reparto").val(r.reparto);
          }
        });
      }
    },
    
    // SOLO LOS COMPROBANTES "A" DISCRIMINAN IVA
    discrimina_iva: function() {
      var t = parseInt(this.model.get("id_tipo_comprobante"));
      // Tipos de comprobantes "A" => 1,2,3,4.. y tambien tipo "M".. y tmb MiPyme
      return (t<=4 || t==51 || t==52 || t==53 || t==201 || t==202 || t==203); 
    },
    
    calcular_totales : function() {
      
      var neto = 0; var porc_descuento = 0; var total = 0; var iva = 0; var porc_ib = 0;
      var descuento = 0; var percepcion_ib = 0; var subtotal_neto = 0; var subtotal_final = 0;
      var total_ofertas = 0; var total_bonificaciones = 0;
      var tipo_iva_cliente = this.$("#facturacion_cliente_iva").val();
      var items = this.model.get("items");
      
      // Cada posicion en el array esta definido por id_tipo_alicuota_iva
      var limite_alicuotas = 0;
      _.each(alicuotas_iva,function(ai){
        if (ai.id > limite_alicuotas) limite_alicuotas = ai.id;
      });

      var alicuotas = [];
      for(var i=0;i<limite_alicuotas+1;i++) {
        alicuotas.push({
          "neto":0, "iva": 0,
        });
      }
      var porc_descuento = (this.$("#facturacion_porc_descuento").length > 0) ? parseFloat(this.$("#facturacion_porc_descuento").val()) : 0;
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      
      // Recorremos los items
      this.items.each(function(item){
        if (item.get("anulado") == 0) {
          var indice = item.get("id_tipo_alicuota_iva");
          try {
            alicuotas[indice].neto += Math.round(item.get("total_sin_iva") * pdesc * 100)/100;
            alicuotas[indice].iva += Math.round(item.get("iva") * pdesc * 100)/100;
          } catch(e) {
            console.log(alicuotas);
            console.log(e);
          }
          neto = neto + item.get("total_sin_iva") * pdesc;
          total = total + item.get("total_con_iva") * pdesc;
          subtotal_neto = subtotal_neto + parseFloat(item.get("total_sin_iva"));
          subtotal_final = subtotal_final + parseFloat(item.get("total_con_iva"));
          iva = iva + item.get("iva") * pdesc;          
        }
      });

      // Recorremos las ofertas
      for (var i = 0; i < this.ofertas.length; i++) {
        var o = this.ofertas[i];
        neto = neto - parseFloat(o.monto);
        total = total - parseFloat(o.monto);
        total_ofertas = parseFloat(o.monto);
        subtotal_neto = subtotal_neto - parseFloat(o.monto);
        subtotal_final = subtotal_final - parseFloat(o.monto);
      }

      // Recorremos las bonificaciones
      for (var i = 0; i < this.bonificaciones.length; i++) {
        var o = this.bonificaciones[i];
        neto = neto - parseFloat(o.monto);
        total = total - parseFloat(o.monto);
        total_bonificaciones = parseFloat(o.monto);
        subtotal_neto = subtotal_neto - parseFloat(o.monto);
        subtotal_final = subtotal_final - parseFloat(o.monto);
      }
      
      // Dependiendo si discrimina IVA o no, es el valor del subtotal
      /*if (FACTURACION_FORMA_PAGO == "M") {
        subtotal = subtotal_final;
      } else {*/
        if (this.discrimina_iva()) {
          subtotal = subtotal_neto;
        } else {
          subtotal = subtotal_final;
        }        
      //}
      window.comp = this;

      /*
      TODO: REVISAR EL TEMA DE LOS DESCUENTOS

      if (MEGASHOP == 1 && this.aplica_reglas == 1) {
        var descuento = this.model.get("descuento");
        subtotal = subtotal - descuento;
      } else {
      }
      */
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;
      if (descuento > 0) {
        if (this.discrimina_iva()) {
          // En el neto ya se calculo el descuento
          subtotal = neto + iva;
        } else {
          total = subtotal - descuento;
          //total = subtotal;
          //subtotal = subtotal - descuento;
        }
      }

      // Ingresos Brutos
      if (PERCIBE_IB == 1) {
        // Monto minimo de percepcion
        // TODO: Hacer esto variable
        if (subtotal >= 200) {
          porc_ib = parseFloat(this.$("#facturacion_porc_percepcion_ib").val());
          if (isNaN(porc_ib)) porc_ib = 0;
          var percepcion_ib = subtotal * (porc_ib / 100) * pdesc;
          this.$("#facturacion_percepcion_ib").val(Number(percepcion_ib).toFixed(2));
          total = total + percepcion_ib;        
        }
      } else {
        var percepcion_ib = 0;
        this.$("#facturacion_percepcion_ib").val(Number(0).toFixed(2));
      }

      // Si tenemos que sumarle algun interes
      var interes = parseFloat(this.model.get("interes"));
      total = total + interes;

      this.model.set({
        "iva":iva,
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "percepcion_ib":percepcion_ib,
        "porc_ib":porc_ib,
        "neto":neto,
        "subtotal":subtotal,
        "total":total,
        "ivas":alicuotas,
      });
    },
    
    limpiar_item: function() {
      this.$("#facturacion_id_articulo").val("0");
      this.$("#facturacion_item_descripcion").val("");
      this.$("#facturacion_item_cantidad").val("1");
      this.$("#facturacion_item_bonificado").val("0");
      this.$("#facturacion_item_neto").val("0.00");
      this.$("#facturacion_item_precio").val("0.00");
      this.$("#facturacion_item_subtotal").val("");
      this.$("#facturacion_codigo_articulo").val("");
      if (this.$("#facturacion_item_percep_viajes").length > 0) this.$("#facturacion_item_percep_viajes").val("");
      if (this.$("#facturacion_item_custom_2").length > 0) this.$("#facturacion_item_custom_2").val("");
      if (this.$("#facturacion_item_custom_4").length > 0) this.$("#facturacion_item_custom_4").val("");
      if (this.$("#facturacion_item_id_proveedor").length > 0) this.$("#facturacion_item_id_proveedor").val("");
      this.$("#facturacion_codigo_articulo").focus();
      this.codigo_leido = "";
      this.limpiar_variantes();
    },

    anular_action: function() {
      if (ESTADO != 1) {
        $.ajax({
          "url":"impresor_fiscal/cancelar/",
          "dataType":"json",
        });          
      }
      this.limpiar();
    },
    
    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      var self = this;
      item.set({ "discrimina_iva": this.discrimina_iva() });
      var view = new app.views.FacturaItem({
        "model": item,
        "view":this,
        // Puedo editar cuando es una factura nueva, o al editar un remito
        "edicion":(self.model.isNew() || self.model.get("id_tipo_comprobante")>900),
      });
      this.$("#tabla_items tbody").append(view.el);
      this.calcular_totales();
    },
    
    validar: function() {
      var self = this;
      var nombre_cliente = this.$("#facturacion_codigo_cliente").val().trim();
      if (isEmpty(nombre_cliente)) {
        this.$("#facturacion_codigo_cliente").focus();
        throw "ERROR: Ingrese un cliente."; 
      }
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al comprobante antes de guardar.";
      }
      if ((ID_EMPRESA == 574 || ID_EMPRESA == 1326) && this.$("#facturacion_vendedores").val() == 0) {
        throw "Error: seleccione un vendedor.";
      }

      if (this.$("#facturacion_conceptos").length > 0) {
        this.model.set({
          "id_concepto":self.$("#facturacion_conceptos").val()
        });
      }

      // Si es una Factura MiPyME si o si se tiene que poner una fecha de vencimiento
      var tipo_comprobante = this.$("#facturacion_tipo").val();
      if (this.$("#facturacion_fecha_vto").length > 0 && isEmpty(this.$("#facturacion_fecha_vto").val()) && tipo_comprobante ==  201) {
        this.$("#facturacion_fecha_vto").focus();
        throw "ERROR: Por favor ingrese una fecha de vencimiento para el pago de la factura.";
      }

      // Si estamos guardando una factura periodica
      if (typeof FACTURACION_PERIODICA != "undefined" &&  FACTURACION_PERIODICA == 1) {
        self.model.set({
          es_periodica: (self.$("#facturacion_es_periodica").is(":checked")?1:0),
          periodo_cantidad: self.$("#facturacion_periodo_cantidad").val(),
          periodo_tipo: self.$("#facturacion_periodo_tipo").val(),
          periodo_dia: self.$("#facturacion_periodo_dia").val(),
          dias_vencimiento: self.$("#facturacion_dias_vencimiento").val()          
        });
      }

    },
    
    anular: function() {
      if (confirmar("Desea anular el comprobante?")) {
        var self = this;
        this.model.set({
          "anulada":1,
          "tarjetas":[], // Nos aseguramos que no se guarden tarjetas si la venta es anulada
          "cheques":[],
        });
        this.view_to_model();
        if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal != "") {
          this.model.save({},{
          success: function(model,response) {
            self.anular_action();
          }
          });
        } else {
          this.model.save({},{
          success: function(model,response) {
            location.reload();
          }
          });        
        }
      }
    },
  
    limpiar: function() {
    
      // Guardamos las variables antes de renderizar
      var lista = (this.$("#facturacion_lista").length > 0) ? this.$("#facturacion_lista").val() : 0;
      var id_vendedor = this.model.get("id_vendedor");
      var cotizacion_dolar = (typeof FACTURACION_MOSTRAR_DOLAR != "undefined" && FACTURACION_MOSTRAR_DOLAR == 1) ? this.$("#facturacion_cotizacion_dolar").val() : 0;
      
      // Conservamos el cliente cuando se limpia
      if (typeof FACTURACION_CONSERVAR_CLIENTE_AL_GUARDAR != "undefined" && FACTURACION_CONSERVAR_CLIENTE_AL_GUARDAR == 1) {
        var id_cliente = this.model.get("id_cliente");
      } else {
        var id_cliente = 0;
      }

      // Guardamos el ultimo punto de venta utilizado para seguir facturando con ese
      var id_punto_venta = this.model.get("id_punto_venta");
      
      var numero_paso = 0;
      if (ID_EMPRESA == 574 || ID_EMPRESA == 1326) {
        // En GOLIA, utilizamos el numero de paso para indicar si estamos viendo un pedido o una factura normal
        numero_paso = this.model.get("numero_paso");
        if (numero_paso == 1) {
          // Ademas si estamos en pedidos, hardcodeamos el punto de venta
          id_punto_venta = 1583;
        }
      }

      this.model = new app.models.Factura({
        "ivas":[],
        "tarjetas":[],
        "cheques":[],
        "items":[],
        "id_punto_venta":id_punto_venta,
        "id_cliente":id_cliente,
        "id_vendedor":id_vendedor,
        "cotizacion_dolar":cotizacion_dolar,
        "numero_paso":numero_paso,
      });
      this.ofertas = new Array();
      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      window.factura = this.model; // TODO: borrar esto desp
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.FacturaItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
      if (control.check("repartos")>0 == 1) this.cambiar_numero_reparto();
      
      if (id_cliente == 0) this.setear_consumidor_final();
      else this.seleccionar_cliente(this.cliente);
      this.$("#facturacion_lista").val(lista);

      if ((ID_EMPRESA == 574 || ID_EMPRESA == 1326) && numero_paso == 1) {
        // En GOLIA, utilizamos el punto de venta 99 para indicar que es un pedido
        this.$("#facturacion_puntos_venta").val(1583).trigger("change");
      }
    },
    
    sincronizar_numero: function(e) {
      var self = this;
      var id = $("#facturacion_tipo").val();
      var punto_venta = this.$("#facturacion_puntos_venta option:selected").text();
      var tipo_impresion = this.$("#facturacion_puntos_venta option:selected").data("tipo_impresion");
      if (tipo_impresion != "E") { show("El punto de venta no es electronico."); return; }
      $.ajax({
      "url":"facturas/function/sincronizar_numero/"+punto_venta+"/"+id+"/",
      "dataType":"json",
      "success":function(r) {
        if (r.error == 1) {
        show(r.mensaje);
        } else {
          var numero = parseInt(r.numero);
          if (isNaN(numero)) numero = 0;
          numero = numero + 1;
          $("#facturacion_numero").val(numero);
          self.model.set({ "numero":numero });
        }
      }
      });
    }, 

    view_to_model: function() {
      var self = this;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      this.model.set({
        "items":self.items.toJSON(),
        "id_vendedor":(self.$("#facturacion_vendedores").length > 0) ? self.$("#facturacion_vendedores").val():0,
        "codigo_postal":(self.$("#facturacion_codigo_postal").length > 0) ? self.$("#facturacion_codigo_postal").val() : "",
        "direccion":(self.$("#facturacion_direccion").length > 0) ? self.$("#facturacion_direccion").val() : "",
        "localidad":(self.$("#facturacion_localidad").length > 0) ? self.$("#facturacion_localidad").val() : "",
        "fecha": (self.$("#facturacion_fecha").length > 0) ? self.$("#facturacion_fecha").val() : "",
        "fecha_vto": (self.$("#facturacion_fecha_vto").length > 0) ? self.$("#facturacion_fecha_vto").val() : "",
        "fecha_reparto":(self.$("#facturacion_fecha_reparto").length > 0) ? self.$("#facturacion_fecha_reparto").val() : "",
        "id_punto_venta": (self.$("#facturacion_puntos_venta").length > 0) ? self.$("#facturacion_puntos_venta").val() : 0,
        "punto_venta":self.$("#facturacion_puntos_venta option:selected").text(),
        "numero":self.$("#facturacion_numero").val(),
        "numero_remito": (self.$("#facturacion_numero_remito").length > 0) ? self.$("#facturacion_numero_remito").val() : 0,
        "observaciones": (self.$("#facturacion_observaciones").length > 0) ? self.$("#facturacion_observaciones").val() : "",
        "custom_5": (self.$("#facturacion_custom_5").length > 0) ? self.$("#facturacion_custom_5").val() : "", // Texto privado
        "reparto":(control.check("repartos")>0 == 1)?self.$("#facturacion_reparto").val():0,
        "estado": ESTADO,
        "id_empresa": ID_EMPRESA,
        "id_cliente": (self.$("#facturacion_id_cliente").length > 0) ? self.$("#facturacion_id_cliente").val() : 0,
        "id_tipo_comprobante": (self.$("#facturacion_tipo").length > 0) ? self.$("#facturacion_tipo").val() : 0,
        "ofertas":self.ofertas,
        "bonificaciones":self.bonificaciones,
      });
      if (this.$("#facturacion_tipo_pago").length > 0) {
        this.model.set({
          "tipo_pago":self.$("#facturacion_tipo_pago").val(),
        });
      }
      if (this.$("#facturacion_tipo_estado").length > 0) {
        var id_tipo_estado = self.$("#facturacion_tipo_estado").val();
        if (this.$("#facturacion_tipo_pago").val() == "C") {
          id_tipo_estado = (ID_EMPRESA == 1354 ? 3 : 0); // Pendiente
        }
        this.model.set({
          "id_tipo_estado":id_tipo_estado,
        })
      }
    },  
        
    aceptar: function() {
      
      var self = this;
      try {
        this.validar(); // Primero validamos
      } catch(e) {
        alert(e);
        return;
      }

      // Desactivamos el doble submiteo
      if (this.guardando == 1) return;
      this.guardando = 1;

      // Si estamos guardando una nueva factura, registramos la cotizacion del dolar
      if (this.model.isNew() && ID_EMPRESA != 46 && typeof COTIZACION_DOLAR != "undefined") {
        if (self.$("#facturacion_cotizacion_dolar").length > 0) {
          this.model.set({
            "cotizacion_dolar":self.$("#facturacion_cotizacion_dolar").val()
          });
        } else {
          this.model.set({
            "cotizacion_dolar":COTIZACION_DOLAR
          });
        }
      }
    
      if (FACTURACION_FORMA_PAGO != "M") {
        // Abrimos el dialogo para que el usuario espere
        workspace.esperar("Guardando comprobante...");
      } else if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 1) {
        //workspace.esperar("Imprimiendo...");
      }      
      
      /*
      var pendiente = this.model.get("pendiente");
      if (this.model.get("pendiente") == 1) {
        // El modelo ya esta guardado, pero no se pudo imprimir
        // entonces no guardamos, solo llamamos a obtener el CAE
        $.ajax({
          "url":"facturas/function/obtener_cae/"+this.model.id,
          "dataType":"json",
          "success":function(response){
            self.guardando = 0;
            if (response.error == 1) {
              show(response.mensaje);
              $('.modal:last').modal('hide');
            } else {
              // Indica que podemos mandar la orden para imprimir
              if (response.imprimir == 1) {
                self.imprimir(self.model.id,response.tipo_impresion,true);
              }
            }              
          }
        });
        return;
      }
      */
      this.view_to_model();
      var id_cliente = self.$("#facturacion_id_cliente").val();
      var id_tipo_comprobante = self.$("#facturacion_tipo").val();

      // TODO: Utilizar un array para los impuestos agregados
      // Si hay algun producto marcado para aplicar percepcion de viajes al exterior
      var base_percep_viajes = 0;
      if (id_tipo_comprobante != 999) {
        this.items.each(function(item){
          if (item.get("percep_viajes") == 1) {
            if (self.discrimina_iva()) {
              base_percep_viajes = base_percep_viajes + item.get("total_sin_iva");
            } else {
              base_percep_viajes = base_percep_viajes + item.get("total_con_iva");
            }            
          }
        });
      }

      // IMPUESTO PAIS
      // Solamente tomamos los servicios de transporte de pasajeros
      var base_impuesto_pais = 0;
      if (ID_EMPRESA == 135 && id_tipo_comprobante != 999) {
        this.items.each(function(item){
          if (item.get("id_articulo") == 1704) {
            if (self.discrimina_iva()) {
              base_impuesto_pais = base_impuesto_pais + item.get("total_sin_iva");
            } else {
              base_impuesto_pais = base_impuesto_pais + item.get("total_con_iva");
            }            
          }
        });
      }

      // Si estamos editando un comprobante, limpiamos los campos de tarjeta, cta_cte, para que no queden mal
      if (!this.model.isNew()) {
        this.model.set({
          "efectivo":0,
          "tarjeta":0,
          "vuelto":0,
          "cheque":0,
          "cta_cte":0,
        });
      }      

      // Abrimos el menu de pago si es un comprobante nuevo
      if (FACTURACION_FORMA_PAGO == "M" && (typeof this.model.id == "undefined" || this.model.id == 0)) {
        var metodoPagoView = new app.views.MetodoPagoView({
          "model": self.model,
          "base_percep_viajes": base_percep_viajes,
          "base_impuesto_pais": base_impuesto_pais,
          "etiqueta_remito": self.check_etiqueta_remito(),
        });
        crearLightboxHTML({
          "html":metodoPagoView.el,
          "width":460,
          "height":500,
          "callback":function(){
            if (window.facturacion_guardo) {

              self.guardando = 0;

              // No estamos usando un controlador fiscal
              if (controlador_fiscal == "") {

                if ((typeof FACTURACION_ABRIR_DIALOGO_IMPRIMIR != undefined) && FACTURACION_ABRIR_DIALOGO_IMPRIMIR == "1") {
                  self.imprimir(self.model.id,self.model.get("id_punto_venta"),"P",true);
                  self.limpiar();
                } else {
                  self.guardando = 0;
                  if (FACTURACION_TIPO == "pv" && controlador_fiscal == "Hasar") workspace.abrir_cajon();
                  self.limpiar();

                  // Cerramos el lightbox de imprimir
                  $('.modal:last').modal('hide');

                  $("#facturacion_codigo_articulo").val("");
                  if (FACTURACION_TIPO == "pv") {
                    $("#facturacion_codigo_articulo").focus();
                  } else {
                    $("#facturacion_codigo_cliente").select();  
                  }
                }

              // Estamos usando un controlador
              } else {

                if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 1 && FACTURACION_TIPO != "pv") {
                  // Imprimimos el ticket
                  self.imprimir_ticket(window.id_ultima_factura);

                } else if (FACTURACION_TIPO == "pv") {

                  // Si tenemos que imprimir un REMITO con el controlador fiscal
                  if (self.check_etiqueta_remito()) {

                    var that = self;
                    $.ajaxq("cola_items",{
                      "url":"impresor_fiscal/imprimir_remito/"+self.model.id,
                      "dataType":"json",
                      "success":function(r) {
                        if (r.result == 0) {
                          alert("Hubo un error al imprimir el comprobante.");
                        }
                        that.guardando = 0;
                        $('.modal:last').modal('hide');
                      }
                    });

                  // Si tenemos que imprimir el ITEM al final
                  } else if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 1 && ESTADO == 0) {
                    self.imprimir_ticket(self.model.id);

                  // Si se vendio con tarjeta se debe imprimir el ticket
                  } else if (ESTADO == 1) {

                    var es_sindicato = false;
                    if (ID_EMPRESA == 356) {
                      es_sindicato = self.check_etiqueta("sindicato");
                    }
                    if (!es_sindicato) {
                      var tarjeta = parseFloat(self.model.get("tarjeta"));
                      if (isNaN(tarjeta)) tarjeta = 0;
                      if (tarjeta != 0) {
                        self.imprimir_ticket(self.model.id);
                      }
                    }
                  }
                }

                self.guardando = 0;
                if (FACTURACION_TIPO == "pv" && controlador_fiscal == "Hasar") workspace.abrir_cajon();
                self.limpiar();

                // Cerramos el lightbox de imprimir
                $('.modal:last').modal('hide');

                $("#facturacion_codigo_articulo").val("");
                if (FACTURACION_TIPO == "pv") {
                  $("#facturacion_codigo_articulo").focus();
                } else {
                  $("#facturacion_codigo_cliente").select();  
                }
              }

            } else {
              // Cerro sin guardar
              $('.modal:last').modal('hide');
              self.guardando = 0;
              $("#facturacion_codigo_articulo").select();
            }
          },
        });

        // Si la forma de pago es Cuenta Corriente
        if (self.model.get("tipo_pago") == "C") {
          $("#metodo_pago_efectivo").val(Number(0).toFixed(2));
          $("#metodo_pago_cta_cte").val(self.model.get("total"));
          $("#metodo_pago_cta_cte").focus();
        } else {
          // Sino preseteamos efectivo
          $("#metodo_pago_efectivo").focus();
        }
        
      // Guardamos directamente
      } else {
        
        // Dependiendo de la forma de pago
        var tipo_pago = ($("#facturacion_tipo_pago").length>0) ? $("#facturacion_tipo_pago").val() : "E";
        if (tipo_pago == "C") {
          this.model.set({ "cta_cte":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        } else if (tipo_pago == "E") {
          this.model.set({ "efectivo":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        } else if (tipo_pago == "T") {
          this.model.set({ "tarjeta":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        } else if (tipo_pago == "H") {
          this.model.set({ "cheque":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        } else if (tipo_pago == "B") {
          this.model.set({ "tarjeta":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        } else if (tipo_pago == "O") {
          this.model.set({ "efectivo":$("#facturacion_total").val(), "tipo_pago":tipo_pago });
        }

        this.model.save({},{
          success: function(model,response) {
            $('.modal:last').modal('hide');
            self.guardando = 0; // Habilitamos el boton
            if (response.id != undefined) {
              self.model.id = response.id;
            }
            if (response.error == 1) {
              show(response.mensaje);
            } else {
              // Indica que podemos mandar la orden para imprimir
              if (response.imprimir == 1) {

                if (LOCAL == 1 && (ID_EMPRESA == 574 || ID_EMPRESA == 1326)) {
                  // TATO manda a imprimir con la EPSON TMU220
                  var that = self;
                  $.ajaxq("cola_items",{
                    "url":"facturas/function/imprimir_epson/"+self.model.id+"/"+self.model.get("id_punto_venta"),
                    "dataType":"json",
                    "success":function(r) {
                      that.guardando = 0;
                      $('.modal:last').modal('hide');
                      that.do_limpiar();
                    },
                    "error":function() {
                      that.guardando = 0;
                      $('.modal:last').modal('hide');
                      that.do_limpiar();                      
                    }
                  });
                } else {
                  self.imprimir(self.model.id,self.model.get("id_punto_venta"),response.tipo_impresion,true);
                }
              } else {
                location.href="app/#ventas_listado";
              }
            }
          },
        });           
      }
    },

    imprimir_ticket: function(id) {
      var self = this;
      if (controlador_fiscal != "") {
        if (!self.check_etiqueta_remito()) {
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/imprimir/"+id,
            "dataType":"json",
            "success":function(r) {
              if (r.result == 0) {
                alert("Hubo un error al imprimir el comprobante.");
              }
              self.guardando = 0;
              $('.modal:last').modal('hide');
            }
          });
        } else {
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/imprimir_remito/"+self.model.id,
            "dataType":"json",
            "success":function(r) {
              if (r.result == 0) {
                alert("Hubo un error al imprimir el comprobante.");
              }
              self.guardando = 0;
              $('.modal:last').modal('hide');
            }
          });
        }
      }
    },
  
  });

})(app.views, app.models);



(function ( app ) {
  app.views.FacturaItem = app.mixins.View.extend({
  template: _.template($("#factura_item_template").html()),
  tagName: "tr",
  className: function() {
    // Ocultamos la fila que esta bonificada por una oferta (en la base de datos se guarda asi)
    var id_articulo = this.model.get("id_articulo");
    var cantidad = this.model.get("cantidad");
    var tipo_cantidad = this.model.get("tipo_cantidad");
    return ((id_articulo != 0 && cantidad == 0 && (tipo_cantidad == "" || tipo_cantidad == "X")) ? "dn" : "");
  },
  myEvents: {
    "click .reservado":function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      if (typeof this.model.id == "undefined") return;
      if (!confirm("Desea pasar estos productos del stock reservado al actual?")) return;
      var cantidad = parseFloat(self.model.get("cantidad"));
      if (isNaN(cantidad)) cantidad = 0;
      $.ajax({
        "url":"facturas_items/function/desreservar/",
        "dataType":"json",
        "type":"get",
        "data":{
          "id":self.model.id,
          "id_articulo":self.model.get("id_articulo"),
          "id_punto_venta":self.model.get("id_punto_venta"),
          "id_variante":self.model.get("id_variante"),
          "cantidad":cantidad,
        },
        "success":function(res){
          if (res.error == 0) location.reload();
        }
      });
    },
    "click .editar":function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();
      this.options.view.editar_articulo(this.model);
    },
    "click .do_eliminar":"do_eliminar",
    "keydown .eliminar":function(e) {
      if (e.which == 13) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();

        // Si estamos utilizando un controlador fiscal
        if (ESTADO != 1 && FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal != "" && !(self.options.view.check_etiqueta_remito())) {
          // Encolamos el AJAX para que si se vende muy rapido, se facture siempre en el mismo orden
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/eliminar_item/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id_tipo_comprobante": $("#facturacion_tipo").val(),
              "nombre":self.model.get("nombre"),
              "cantidad":(self.model.get("cantidad")),
              "precio":-(self.model.get("precio")),
              "porc_iva":(self.model.get("porc_iva")),
              "comienzo":1,
              "id_cliente":((isEmpty($("#facturacion_id_cliente").val())) ? 0 : $("#facturacion_id_cliente").val()),
            },
            "cache":false,
            "success":function() {
              self.do_eliminar();
            },
          });
        } else {
          this.do_eliminar();
        }
        return false;
      } else if (e.which == 27) {
        $("#facturacion_codigo_articulo").focus();
      }
    },
  },
  initialize: function(options) {
    var self = this;
    _.bindAll(this);
    this.options = options;
    this.edicion = (typeof options.edicion != undefined) ? options.edicion : true;
    this.model.on("change",this.render,this);
    this.render();
  },
  do_eliminar: function() {
    if (FACTURACION_TIPO == "pv") {
      this.model.set({
        "precio":0,
        "neto":0,
        "iva":0,
        "total_sin_iva":0,
        "total_con_iva":0,
        "costo_final":0,
        "ganancia":0,
        "anulado":1,
      });
    } else {
      this.model.destroy();  // Eliminamos el modelo      
      $(this.el).remove();  // Lo eliminamos de la vista
    }
    this.options.view.calcular_ofertas();
    $("#facturacion_codigo_articulo").focus();
  },
  render: function() {
    var m = this.model.toJSON();
    m.edicion = ((typeof FACTURACION_PERMITIR_EDICION_FACTURA != undefined) ? true : this.options.edicion);
    console.log(m);
    $(this.el).html(this.template(m));
    if (this.model.get("anulado") == 1 && control.check("estadisticas_ventas")>=3) {
      // Si somos el administrador general, las filas anuladas debemos mostrarlas
      $(this.el).addClass("fila_roja_2");
    } else if (this.model.get("anulado") == 1) {
      // Si somos un cajero comun, las filas anuladas se deben ocultar
      $(this.el).hide();
    } else if (this.model.get("custom_2") == "1" && ID_PROYECTO == 1) {
      // Si el producto esta en OFERTA, lo marcamos
      $(this.el).addClass("fila_roja");
    }
    return this;
  },
  });
})(app);





(function ( views, models ) {

  views.MetodoPagoView = app.mixins.View.extend({

    template: _.template($("#metodo_pago_panel_template").html()),
      
    myEvents: {
      "change #metodo_pago_impuesto_pais_check":function(e) {
        var imp = 0;
        if ($("#metodo_pago_impuesto_pais_check").is(":checked")) {
          // El impuesto se paga solamente si se paga en PESOS, por lo tanto el usuario tiene que habilitarlo o no
          imp = parseFloat(this.base_impuesto_pais * 0.3);
          if (isNaN(imp)) imp = 0;
        }
        this.$("#metodo_pago_impuesto_pais").val(Number(imp).toFixed(2));
        this.render_calcular_intereses();
        this.calcular();
      },
      "click .guardar":"guardar",
      "keypress #metodo_pago_efectivo":function(e) {
        if (e.which == 13) { 
          this.calcular_percepcion_viajes_exterior();
          this.calcular(); 
          $("#metodo_pago_aceptar").focus(); 
        }
      },
      "keypress #metodo_pago_cta_cte":function(e) {
        if (e.which == 13) { this.calcular(); $("#metodo_pago_aceptar").focus(); }
      },
      "click #metodo_pago_tarjetas_boton_0":function() {
        this.abrirTarjetas(0);
      },
      "click #metodo_pago_tarjetas_boton_1":function() {
        this.abrirTarjetas(1);
      },
      "click #metodo_pago_cheques_boton":function() {
        this.abrirCheques();
      },
      "click #metodo_pago_creditos_personales_boton":function() {
        this.abrirCreditosPersonales();
      },
      "keydown .keyp":function(e) {
        // T = Tarjetas 1
        if (e.which == 84) { this.abrirTarjetas(0); e.preventDefault(); }
        // T = Tarjetas 2
        if (e.which == 89) { this.abrirTarjetas(1); e.preventDefault(); }
        // C = Cheques
        if (e.which == 67) { this.abrirCheques(); e.preventDefault(); }
      },
      "keypress #metodo_pago_aceptar":function(e) {
        if (e.which == 9) { e.preventDefault(); $("#metodo_pago_efectivo").select(); }
      },
    },

    calcular_percepcion_viajes_exterior: function() {
      // Si debemos calcular la percepcion de viajes al exterior
      var efectivo = this.$("#metodo_pago_efectivo").val();
      if (this.base_percep_viajes > 0 && efectivo >= this.base_percep_viajes) {
        // 5% del pago en efectivo
        $("#metodo_pago_percep_viajes").val(this.base_percep_viajes * 0.05);

        // Cambiamos el total sumandole la percepcion
        // var total_general = Number(this.model.get("total") + percep_viajes).toFixed(2);
        // $("#metodo_pago_total").val(total_general);
      }
    },
      
    abrirTarjetas: function(posicion) {
      var self = this;
      var tarjetaModel = Backbone.Model.extend();
      window.eliminar_tarjeta = 0;

      // Por defecto en la tarjeta ponemos el resto que queda por pagar
      var resto = parseFloat($("#metodo_pago_vuelto").val());
      if (isNaN(resto)) resto = self.model.get("total");
      else resto = -resto;

      if (typeof self.model.get("tarjetas")[posicion] == "undefined") {        
        tarjeta = new tarjetaModel({
          "id_tarjeta":0,
          "lote":0,
          "cuotas":1,
          "cupon":0,
          "importe":resto,
          "interes":0,
          "total":resto,
        });
      } else {
        tarjeta = new tarjetaModel(self.model.get("tarjetas")[posicion]);
      }
      app.views.metodoPagoTarjetaView = new app.views.MetodoPagoTarjetaView({
        "model": tarjeta,
        "posicion":posicion,
      });
      crearLightboxHTML({
        "html":app.views.metodoPagoTarjetaView.el,
        "width":400,
        "height":500,
        "callback":function(){
          if (window.eliminar_tarjeta == 1) {
            // Eliminamos el elemento del array
            var tar = self.model.get("tarjetas");
            tar.splice(posicion,1);
            self.model.set({"tarjetas":tar});
            self.$("#metodo_pago_tarjetas_"+posicion).val(0);
            if (tar.length > 0) {
              // Si hay otra segunda tarjeta, subimos al primer lugar
              $("#metodo_pago_tarjetas_0").val($("#metodo_pago_tarjetas_1").val());
              $("#metodo_pago_tarjetas_1").val(0);
            } else {
              $("#metodo_pago_tarjetas_0").val(0);
            }
            self.render_calcular_intereses();
            self.calcular();
          } else {
            if (tarjeta.get("id_tarjeta") == 0) { // Se cancelo
              $("#metodo_pago_efectivo").select();
            } else {
              var tar = self.model.get("tarjetas");
              tar[posicion] = tarjeta.toJSON();
              self.model.set({"tarjetas":tar}); // Reemplazamos
              $("#metodo_pago_tarjetas_"+posicion).val(tarjeta.get("total"));
              self.render_calcular_intereses();
              self.calcular();

              var vuelto = parseFloat(self.$("#metodo_pago_vuelto").val());
              if (vuelto == 0) $("#metodo_pago_aceptar").focus();  
              else $("#metodo_pago_efectivo").focus();  
            }
          }
        },
      });
    },
    render_calcular_intereses: function() {
      var interes = 0;
      _.each(this.model.get("tarjetas"),function(t){
        interes += parseFloat(t.interes);
      });
      this.$("#metodo_pago_interes").val(Number(interes).toFixed(2));
      this.model.set({
        "interes":interes
      })

      if (ID_EMPRESA == 121) {
        var subtotal = parseFloat(this.$("#metodo_pago_total").val());
        if (isNaN(subtotal)) { subtotal = 0; }
      } else {        
      	var subtotal = parseFloat(this.model.get("subtotal"));
      }
      var descuento = parseFloat(this.model.get("descuento"));
      if (isNaN(subtotal)) { subtotal = 0; }
      var total = Number(subtotal - descuento + interes).toFixed(2);

      if (this.$("#metodo_pago_impuesto_pais_check").length > 0 && this.$("#metodo_pago_impuesto_pais_check").is(":checked")) {
        var impuesto_pais = parseFloat(this.$("#metodo_pago_impuesto_pais").val());
        if (isNaN(impuesto_pais)) impuesto_pais = 0;
        total = parseFloat(total);
        total = total + impuesto_pais;
        total = Number(total).toFixed(2);
      }
      this.$("#metodo_pago_total").val(total);

      this.$("#metodo_pago_subtotal_container").css("display",((interes>0)?"block":"none"));
      this.$("#metodo_pago_recargo_tarjetas_container").css("display",((interes>0)?"block":"none"));
    },
      
    abrirCheques: function() {
      var self = this;
      var lista = new app.views.MetodoPagoListaChequesView({
        "model":self.model,
      });
      crearLightboxHTML({
        "html":lista.el,
        "width":600,
        "height":500,
        "callback":function(){
          var total_cheques = 0;
          var cheques = self.model.get("cheques");
          for(var i=0; i< cheques.length; i++) {
            var c = cheques[i];
            total_cheques += parseFloat(c.importe);
          }
          $("#metodo_pago_cheques").val(Number(total_cheques).toFixed(2));
          self.calcular();
          $("#metodo_pago_aceptar").focus();
        },
      });
    },   

    abrirCreditosPersonales: function() {
      var self = this;
      var creditoModel = Backbone.Model.extend();
      window.eliminar_credito_personal = 0;
      window.aceptar_credito_personal = 0;

      if (this.model.get("creditos_personales").length == 0) {

        // Por defecto en la tarjeta ponemos el resto que queda por pagar
        var resto = parseFloat($("#metodo_pago_vuelto").val());
        if (isNaN(resto)) resto = self.model.get("total");
        else resto = -resto;
        credito_personal = new creditoModel({
          "tope_credito": 1000, // TODO: Depende del cliente
          "importe":resto,
          "cuotas":[],
          "cantidad_cuotas":2,
          "maximo_cuotas":12,
          "primera_cuota":"",
          "valor_cuota":0,
          "recargo":0,
          //"proxima_cuota":moment().add(1,"month").format("DD/MM/YYYY"),
          "proxima_cuota":moment().format("DD/MM/YYYY"),
        });
      } else {
        credito_personal = new creditoModel(self.model.get("creditos_personales")[0]);
      }
      app.views.metodoPagoCreditoPersonalView = new app.views.MetodoPagoCreditoPersonalView({
        model: credito_personal
      });
      crearLightboxHTML({
        "html":app.views.metodoPagoCreditoPersonalView.el,
        "width":450,
        "height":500,
        "callback":function(){
          if (window.eliminar_credito_personal == 1) {
            // Tenemos que eliminarlo
            self.model.set({"creditos_personales":[]});
            self.calcular();
          
          } else if (window.aceptar_credito_personal == 1) {

            self.model.set({"creditos_personales":[credito_personal.toJSON()]}); // Reemplazamos
            // TODO: lo que se financia es el TOTAL - la primer cuota (que debe pagar si o si)
            var credito = parseFloat(credito_personal.get("importe")) + parseFloat(credito_personal.get("recargo")); // - parseFloat(credito_personal.get("valor_cuota"));
            $("#metodo_pago_creditos_personales").val(Number(credito).toFixed(2));

            /*
            // En el efectivo, se pone el valor de la cuota, porque la primera te la paga si o si
            var efectivo = parseFloat($("#metodo_pago_efectivo").val());
            if (isNaN(efectivo)) efectivo = 0;
            var valor_cuota = parseFloat(credito_personal.get("valor_cuota"))
            $("#metodo_pago_efectivo").val(Number(efectivo + valor_cuota).toFixed(2));
            */

            var recargo = credito_personal.get("recargo")
            self.$("#metodo_pago_interes").val(Number(recargo).toFixed(2));
            var subtotal = parseFloat(self.$("#metodo_pago_subtotal").val());
            if (isNaN(subtotal)) { subtotal = 0; }

            var total = Number(subtotal + recargo).toFixed(2);
            self.$("#metodo_pago_total").val(total);

            $("#facturacion_observaciones").val("Nro Cuotas: "+credito_personal.get("cantidad_cuotas"));

            self.$("#metodo_pago_subtotal_container").css("display",((recargo>0)?"block":"none"));
            self.$("#metodo_pago_recargo_tarjetas_container").css("display",((recargo>0)?"block":"none"));

            self.calcular();
            $("#metodo_pago_aceptar").focus();
          }
        },
      });
    },  

    calcular: function() {
      var total = parseFloat(this.$("#metodo_pago_total").val());
      if (isNaN(total)) { total = 0; }
      var efectivo = parseFloat(this.$("#metodo_pago_efectivo").val());
      if (isNaN(efectivo)) { efectivo = 0; }
      var cta_cte = parseFloat(this.$("#metodo_pago_cta_cte").val());
      if (isNaN(cta_cte)) { cta_cte = 0; }
      var tarjeta_1 = parseFloat(this.$("#metodo_pago_tarjetas_0").val());
      if (isNaN(tarjeta_1)) { tarjeta_1 = 0; }
      var tarjeta_2 = parseFloat(this.$("#metodo_pago_tarjetas_1").val());
      if (isNaN(tarjeta_2)) { tarjeta_2 = 0; }
      var cheque = parseFloat(this.$("#metodo_pago_cheques").val());
      if (isNaN(cheque)) { cheque = 0; }
      var credito = parseFloat(this.$("#metodo_pago_creditos_personales").val());
      if (isNaN(credito)) { credito = 0; }
      var vuelto = Number(-(total - efectivo - cta_cte - tarjeta_1 - tarjeta_2 - cheque - credito)).toFixed(2);
      if (isNaN(vuelto)) { vuelto = 0; }
      this.$("#metodo_pago_vuelto").val(vuelto);
    },
      
    initialize: function(options)  {
      _.bindAll(this);
      this.base_percep_viajes = (typeof options.base_percep_viajes != "undefined") ? options.base_percep_viajes : 0;
      this.base_impuesto_pais = (typeof options.base_impuesto_pais != "undefined") ? options.base_impuesto_pais : 0;
      this.etiqueta_remito = (typeof options.etiqueta_remito != "undefined") ? options.etiqueta_remito : false;
      window.facturacion_guardo = false; // Flag que indica si guardo o no
      this.guardando = 0;
      this.render();
    },

    render: function() {
      var self = this;
      var tarjetas = self.model.get("tarjetas");
      var tarjetas_0 = 0, tarjetas_1 = 0;
      if (typeof tarjetas[0] !== "undefined") {
        tarjetas_0 = tarjetas[0].total;
      }
      if (typeof tarjetas[1] !== "undefined") {
        tarjetas_1 = tarjetas[1].total;
      }
      var obj = {
        "base_percep_viajes":self.base_percep_viajes,
        "tarjetas_0":tarjetas_0,
        "tarjetas_1":tarjetas_1,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      this.render_calcular_intereses();
      this.calcular_percepcion_viajes_exterior();
      this.calcular();
      return this;
    },
    
    validar: function() {
      try {
        var self = this;

        var total = parseFloat($("#metodo_pago_total").val());
        var vuelto = parseFloat($("#metodo_pago_vuelto").val());
        var id_tipo_comprobante = parseInt($("#facturacion_tipo").val());

        if (id_tipo_comprobante != 3 && id_tipo_comprobante != 8 && id_tipo_comprobante != 13 && id_tipo_comprobante != 21 && id_tipo_comprobante != 53 && vuelto < 0) {
          show("Error: el pago no completa la totalidad del comprobante.");
          $("#metodo_pago_efectivo").select();
          return false;
        }
        if (vuelto > 1000 && total > 0) {
          show("Error: el vuelto no puede ser mayor a $1000. Revise el efectivo ingresado.");
          $("#metodo_pago_efectivo").select();
          return false;          
        }

        var total_tarjetas = parseFloat($("#metodo_pago_tarjetas_0").val()) + parseFloat($("#metodo_pago_tarjetas_1").val());
        if (total_tarjetas < 0) {
          show("Error: los cupones de tarjetas no pueden ser negativos.");
          return false;
        }

        var permitir_negativo = ((typeof FACTURACION_PERMITIR_TOTAL_NEGATIVO != "undefined") ? FACTURACION_PERMITIR_TOTAL_NEGATIVO : 0);
        if (permitir_negativo == 0 && total < 0) {
          show("Error: el total es negativo.");
          $("#metodo_pago_efectivo").select();
          return false;
        }

        // Puede darse el caso, de que por error del cajero, la tarjeta cubre el total y ademas le agrega un pago en efectivo
        // Por ahi se olvido de borrar la tarjeta 
        if (total_tarjetas > 0 && total_tarjetas == total && vuelto > 0) {
          show("ATENCION: Esta pagando con tarjeta y con efectivo el mismo monto. Revise los datos ingresados y deje solo el correcto.");
          return false;
        }

        return true;
      } catch(e) {
        return false;
      }
    },    
      
    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) this.model.set({id:0});
        
        if (this.guardando != 0) return;
        this.guardando = 1;

        var total_tarjetas = parseFloat($("#metodo_pago_tarjetas_0").val()) + parseFloat($("#metodo_pago_tarjetas_1").val());
        var id_tipo_comprobante = parseInt($("#facturacion_tipo").val());

        var interes = (($("#metodo_pago_interes").length > 0) ? $("#metodo_pago_interes").val() : 0);
        var interes_neto = interes / 1.21;
        var iva_interes = interes * 0.21;

        // Si se debe ir imprimiendo a medida que se va vendiendo
        if (FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal != "" && self.model.get("tarjetas").length > 0 && !self.etiqueta_remito) {
          // Encolamos el AJAX para que si se vende muy rapido, se facture siempre en el mismo orden
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/imprimir_item/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id_articulo":0,
              "neto":interes_neto,
              "precio":interes,
              "nombre":"Recargo interes tarjeta",
              "descripcion":"",
              "id_rubro":0,
              "id_tipo_alicuota_iva":5,
              "cantidad":1,
              "bonificacion":0,
              "tipo":"",
              "iva":iva_interes,
              "porc_iva":21,
              "percep_viajes":0,
              "total_sin_iva":interes_neto,
              "total_con_iva":interes,
              "discrimina_iva":false,
              "costo_final":0,
              "custom_1":"",
              "custom_2":"", // Si tiene promocion
              "custom_3":"", // Si reservo stock
              "custom_4":"",
              "id_proveedor":0,
              "id_variante":0,
              "variante":"",
              "variantes":new Array(),
            },
            "cache":false,
            "error":function() {
              alert("ERROR: Controlar si el impresor esta encendido o bien conectado.");
              self.anular_action();
              self.agregando = 0;
              // En caso de error, cancelar el ticket
            }
          });
        }

        if (ESTADO == 0 && FACTURACION_TIPO == "pv" && FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal != "" && !self.etiqueta_remito) {
          // Encolamos el AJAX para que si se vende muy rapido, se facture siempre en el mismo orden
          $.ajaxq("cola_items",{
            "url":"impresor_fiscal/cerrar/",
            "dataType":"json",
            "type":"post",
            "data": {
              "id_tipo_comprobante":id_tipo_comprobante,
              "efectivo":$("#metodo_pago_efectivo").val(),
              "tarjeta":total_tarjetas,
              "cheque":$("#metodo_pago_cheques").val(),
              "cta_cte":$("#metodo_pago_cta_cte").val(),
              "credito":$("#metodo_pago_creditos_personales").val(),
              "perc_ib":$("#facturacion_percepcion_ib").val(),
              "interes": (($("#metodo_pago_interes").length > 0) ? $("#metodo_pago_interes").val() : 0),
              "percep_viajes":(($("#metodo_pago_percep_viajes").length > 0) ? $("#metodo_pago_percep_viajes").val() : 0),
              "impuesto_pais":(($("#metodo_pago_impuesto_pais").length > 0) ? $("#metodo_pago_impuesto_pais").val() : 0),
            },
            "cache":false,
          });
        }
        self.guardar_modelo();
      }
    },
    
    guardar_modelo: function() {
      var self = this;
      var total_tarjetas = parseFloat($("#metodo_pago_tarjetas_0").val()) + parseFloat($("#metodo_pago_tarjetas_1").val());
      var tipo_pago = "";
      var efectivo = this.$("#metodo_pago_efectivo").val();
      var cta_cte = this.$("#metodo_pago_cta_cte").val();
      var cheque = this.$("#metodo_pago_cheques").val();
      if (efectivo > 0) tipo_pago = "E";
      else if (cta_cte > 0) tipo_pago = "C";
      else if (total_tarjetas > 0) tipo_pago = "T";

      var impuesto_pais = (($("#metodo_pago_impuesto_pais").length > 0) ? $("#metodo_pago_impuesto_pais").val() : 0);

      this.model.save({
        "efectivo":efectivo,
        "cta_cte":cta_cte,
        "tarjeta":total_tarjetas,
        "tipo_pago":tipo_pago,
        "cheque":cheque,
        "vuelto":$("#metodo_pago_vuelto").val(),
        "percep_viajes": (($("#metodo_pago_percep_viajes").length > 0) ? $("#metodo_pago_percep_viajes").val() : 0),
        "impuesto_pais": impuesto_pais,
        "interes": (($("#metodo_pago_interes").length > 0) ? $("#metodo_pago_interes").val() : 0),
        "total":$("#metodo_pago_total").val(),
        "observaciones":(($("#facturacion_observaciones").length > 0) ? $("#facturacion_observaciones").val() : ""),
        // TODO: Agregar los creditos personales
      },{
        success: function(model,response) {
          self.guardando = 0;
          window.facturacion_guardo = true;
          window.id_ultima_factura = self.model.id;
          self.enviar_cambiar_numero(self.model.id,self.model.get("id_punto_venta"));
          $('.modal:last').modal('hide');
        },
        "error":function() {
          self.guardando = 0;
        }
      });      
    },

    enviar_cambiar_numero: function(id,id_punto_venta) {
      if (ESTADO == 0 && FACTURACION_TIPO == "pv" && FACTURACION_IMPRIMIR_ITEM_AL_FINAL == 0 && controlador_fiscal == "Hasar" && !self.etiqueta_remito) {
        $.ajaxq("cola_items",{
          "url":"impresor_fiscal/cambiar_numero/",
          "dataType":"json",
          "type":"post",
          "data": {
            "id":id,
            "id_punto_venta":id_punto_venta,
            "id_empresa":ID_EMPRESA,
          },
          "cache":false,
        });
      }
    },

  });
  
})(app.views, app.models);



(function ( views, models ) {

  views.MetodoPagoTarjetaView = app.mixins.View.extend({

  template: _.template($("#metodo_pago_tarjeta_panel_template").html()),
    
    myEvents: {
      "click .guardar":"guardar",
      "click .eliminar":"eliminar",
      "change #metodo_pago_tarjeta_importe":"calcular_intereses",
      "change #metodo_pago_tarjeta_cuotas":"calcular_intereses",
      "change #metodo_pago_tarjeta_select":"calcular_intereses",

      "keydown #metodo_pago_tarjeta_aceptar":function(e) {
        if(e.which == 9) { e.preventDefault(); $("#metodo_pago_tarjeta_select").focus(); }
      },
      "keydown #metodo_pago_tarjeta_select":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_tarjeta_cuotas").focus(); }
      },
      "keydown #metodo_pago_tarjeta_cuotas":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_tarjeta_cupon").select(); }
      },
      "keydown #metodo_pago_tarjeta_cupon":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_tarjeta_lote").select(); }
      },
      "keydown #metodo_pago_tarjeta_lote":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_tarjeta_importe").select(); }
      },
      "keydown #metodo_pago_tarjeta_importe":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_tarjeta_aceptar").focus(); }
      },
    },
    
    initialize: function(options)  {
      _.bindAll(this);
      this.posicion = options.posicion;
      this.render();
    },

    calcular_intereses: function() {
      var self = this;
      var id_tarjeta = $("#metodo_pago_tarjeta_select").val();
      var cuotas = $("#metodo_pago_tarjeta_cuotas").val()
      $.ajax({
      "url":"tarjetas/function/calcular_intereses/"+id_tarjeta+"/"+cuotas,
      "dataType":"json",
      "success":function(r) {
        var importe = parseFloat($("#metodo_pago_tarjeta_importe").val());
        var importe_con_interes = Number(importe * r.interes).toFixed(2);
        var interes = Number(importe_con_interes - importe).toFixed(2);
        $("#metodo_pago_tarjeta_interes").val(interes);
        $("#metodo_pago_tarjeta_total").val(importe_con_interes);
        self.model.set({
          "total":importe_con_interes,
          "interes":interes
        });
      }
      });
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      new app.mixins.Select({
        modelClass: app.models.Tarjeta,
        url: "tarjetas/",
        render: "#metodo_pago_tarjeta_select",
        name: "id_tarjeta",
        selected : self.model.get("id_tarjeta"),
        onComplete:function() {
          $("#metodo_pago_tarjeta_select").focus();
        }
      });
      return this;
    },
    
    eliminar: function() {
      window.eliminar_tarjeta = 1;
      $('.modal:last').modal('hide');
    },
    
    guardar: function() {
      var self = this;
      window.eliminar_tarjeta = 0;

      // Corroboramos los valores
      try {
        var cupon = parseInt($("#metodo_pago_tarjeta_cupon").val());
        if (MEGASHOP == 1 || ID_EMPRESA == 421) {
          if (isNaN(cupon) || cupon == 0) {
            alert("Por favor ingrese un cupon");
            $("#metodo_pago_tarjeta_cupon").select();
            return false;
          }
          var lote = parseInt($("#metodo_pago_tarjeta_lote").val());
          if (isNaN(lote) || lote == 0) {
            alert("Por favor ingrese un lote");
            $("#metodo_pago_tarjeta_lote").select();
            return false;
          }
        }
        var importe = parseFloat($("#metodo_pago_tarjeta_importe").val());
        if (isNaN(importe) || importe <= 0) {
          alert("Por favor ingrese un importe valido");
          $("#metodo_pago_tarjeta_importe").select();
          return false;
        }
        var interes = parseFloat($("#metodo_pago_tarjeta_interes").val());
        if (isNaN(interes) || interes < 0) {
          alert("Por favor ingrese un interes valido");
          $("#metodo_pago_tarjeta_interes").select();
          return false;
        }
        var total = parseFloat($("#metodo_pago_tarjeta_total").val());
        if (isNaN(total) || total <= 0) {
          alert("Por favor ingrese un total valido");
          $("#metodo_pago_tarjeta_total").select();
          return false;
        }
      } catch(e) {
        return false;
      }

      var id_tarjeta = $("#metodo_pago_tarjeta_select").val();
      this.model.set({
        "id_tarjeta":id_tarjeta,
        "cupon":cupon,
        "lote":lote,
        "cuotas":$("#metodo_pago_tarjeta_cuotas").val(),
        "importe":importe,
        "interes":interes,
        "total":total,
        "status":((ID_EMPRESA == 356 && id_tarjeta == 51) ? 1 : 0),
      });
      $('.modal:last').modal('hide');
    },    

  });
  
})(app.views, app.models);



(function ( views, models ) {

  views.MetodoPagoCreditoPersonalView = app.mixins.View.extend({

  template: _.template($("#metodo_pago_credito_personal_panel_template").html()),
    
    myEvents: {
      "click .guardar":"guardar",
      "click .eliminar":"eliminar",
      "keydown #metodo_pago_credito_personal_importe":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_credito_personal_cantidad_cuotas").select(); }
      },
      "keydown #metodo_pago_credito_personal_cantidad_cuotas":function(e) {
        if (e.which == 13) { e.preventDefault(); e.stopPropagation(); $("#metodo_pago_credito_personal_aceptar").focus(); }
      },
      "change #metodo_pago_credito_personal_cantidad_cuotas":"calcular_cuota",
    },
    
    initialize: function(options)  {
      _.bindAll(this);
      this.posicion = options.posicion;
      this.render();
    },

    calcular_cuota: function() {
      var nro_cuotas = parseFloat(this.$("#metodo_pago_credito_personal_cantidad_cuotas").val());
      var importe = parseFloat(this.$("#metodo_pago_credito_personal_importe").val());
      var recargo = 0;
      // Espacio Virtual
      if (ID_EMPRESA == 287) {
        if (nro_cuotas == 2) recargo =  0.10;
        else if (nro_cuotas == 3) recargo =  0.15;
        else if (nro_cuotas == 4) recargo =  0.20;
        else if (nro_cuotas == 5) recargo =  0.25;
        else if (nro_cuotas == 6) recargo =  0.30;
        else if (nro_cuotas == 7) recargo =  0.35;
        else if (nro_cuotas == 8) recargo =  0.40;
        else if (nro_cuotas == 9) recargo =  0.45;
        else if (nro_cuotas == 10) recargo =  0.50;
        else if (nro_cuotas == 11) recargo =  0.55;
        else if (nro_cuotas == 12) recargo =  0.60;
      }
      this.model.set({
        "recargo": (importe * recargo),
      });
      importe = importe * (1+recargo);
      var cuota = importe / nro_cuotas;
      this.$("#metodo_pago_credito_personal_valor_cuota").val(Number(cuota).toFixed(2));
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      this.$("#metodo_pago_credito_personal_importe").focus();
      this.calcular_cuota();
      return this;
    },
    
    validar: function() {
      try {
        
        // TODO: La primera cuota podria ser dentro de un mes por ejemplo
        //var primera_cuota = moment().add(dias_primera_cuota,"days").format("DD/MM/YYYY");
        var primera_cuota = moment().format("DD/MM/YYYY");

        var cantidad_cuotas = parseFloat(this.$("#metodo_pago_credito_personal_cantidad_cuotas").val());
        var cuota = Number(this.$("#metodo_pago_credito_personal_valor_cuota").val()).toFixed(2);

        var cuotas = new Array();
        for(var i=1; i<=cantidad_cuotas; i++) {
          var fecha_vencimiento = moment(primera_cuota,"DD/MM/YYYY").add(i-1,"months").format("DD/MM/YYYY");
          cuotas.push({
            "numero":i,
            "fecha_vencimiento":fecha_vencimiento,
            "monto":cuota,
          });
        }
        this.model.set({
          "cuotas":cuotas,
        });

        return true;
      } catch(e) {
        return false;
      }
    },

    eliminar: function() {
      window.eliminar_tarjeta = 1;
      $('.modal:last').modal('hide');
    },
    
    guardar: function() {
      var self = this;
      if (this.validar()) {
        window.eliminar_credito_personal = 0;
        window.aceptar_credito_personal = 1;
        this.model.set({
          "importe":$("#metodo_pago_credito_personal_importe").val(),
          "cantidad_cuotas":$("#metodo_pago_credito_personal_cantidad_cuotas").val(),
          "valor_cuota":$("#metodo_pago_credito_personal_valor_cuota").val(),
          "primera_cuota":$("#metodo_pago_credito_personal_primera_cuota").val(),
        });
        $('.modal:last').modal('hide');
      }
  },    

  });
  
})(app.views, app.models);



(function ( views, models ) {

  views.MetodoPagoListaChequesView = app.mixins.View.extend({

    template: _.template($("#metodo_pago_lista_cheques_panel_template").html()),
    
    myEvents: {
      "click .aceptar_cheques":"cerrar",
      "click .agregar_cheque":"agregar_cheque",
      "click .editar_cheque":"editar_cheque",
      "click .eliminar_cheque":"eliminar_cheque",
    },
    
    initialize: function()  {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    eliminar_cheque: function(e) {
      var self = this;
      var pos = $(e.currentTarget).data("pos");
      var cheques2 = new Array();
      var cheques = self.model.get("cheques");
      for(var i=0;i<cheques.length;i++) {
        var c = cheques[i];
        if (i!=pos) cheques2.push(c);
      }
      self.model.set({"cheques":cheques2});
      self.render();
    },

    editar_cheque: function(e) {
      var self = this;
      var pos = $(e.currentTarget).data("pos");
      var chequeModel = Backbone.Model.extend();
      cheque = new chequeModel(self.model.get("cheques")[pos]);
      var detalle = new app.views.MetodoPagoChequeView({
        model: cheque
      });
      crearLightboxHTML({
        "html":detalle.el,
        "width":450,
        "height":500,
        "callback":function(){
          var cheques = self.model.get("cheques");
          cheques[pos] = cheque.toJSON();
          self.model.set("cheques",cheques);
          self.render();
        },
      });
    },

    agregar_cheque: function() {
      var self = this;
      var chequeModel = Backbone.Model.extend();
      cheque = new chequeModel({
        "id_banco":0,
        "numero":0,
        "cliente":"",
        "fecha_emision":"",
        "fecha_cobro":"",
        "importe":0,
      });
      var detalle = new app.views.MetodoPagoChequeView({
        model: cheque
      });
      crearLightboxHTML({
        "html":detalle.el,
        "width":450,
        "height":500,
        "callback":function(){
          var cheques = self.model.get("cheques");
          cheques.push(cheque.toJSON());
          self.model.set("cheques",cheques);
          self.render();
        },
      });

    },
  });

})(app.views, app.models);


(function ( views, models ) {

  views.MetodoPagoChequeView = app.mixins.View.extend({

    template: _.template($("#metodo_pago_cheque_panel_template").html()),
    
    myEvents: {
      "click .guardar":"guardar",
    },
    
    initialize: function()  {
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      
      // Agregamos las fechas
      var fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
      
      this.$("#metodo_pago_cheque_fecha_emision").datepicker({
        "dateFormat":"dd/mm/yy",
        "currentText":"Hoy",
        "buttonImage": "resources/images/datepicker.png",
        "buttonImageOnly": true,
        "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
        "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
        "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
        "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
        "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "nextText":"Proximo",
        "prevText":"Anterior",
      });
      this.$("#metodo_pago_cheque_fecha_emision").mask("99/99/9999");
      this.$("#metodo_pago_cheque_fecha_emision").val(fecha);
      this.$("#metodo_pago_cheque_fecha_cobro").datepicker({
        "dateFormat":"dd/mm/yy",
        "currentText":"Hoy",
        "buttonImage": "resources/images/datepicker.png",
        "buttonImageOnly": true,
        "dayNames":["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
        "dayNamesMin":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
        "dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
        "monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
        "monthNamesShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "nextText":"Proximo",
        "prevText":"Anterior",
      });
      this.$("#metodo_pago_cheque_fecha_cobro").mask("99/99/9999");
      this.$("#metodo_pago_cheque_fecha_cobro").val(fecha);
      return this;
    },
    
    validar: function() {
      try {
        // TODO: controlamos que se haya pagado completa
        return true;
      } catch(e) {
        return false;
      }
    },    
    
    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.set({
          "id_banco":$("#metodo_pago_banco_select").val(),
          "banco":$("#metodo_pago_banco_select option:selected").text(),
          "numero":$("#metodo_pago_cheque_numero").val(),
          "cliente":$("#metodo_pago_cheque_cliente").val(),
          "fecha_emision":$("#metodo_pago_cheque_fecha_emision").val(),
          "fecha_cobro":$("#metodo_pago_cheque_fecha_cobro").val(),
          "importe":$("#metodo_pago_cheque_importe").val(),
        });
        $('.modal:last').modal('hide');
      }
    },    

  });
  
})(app.views, app.models);