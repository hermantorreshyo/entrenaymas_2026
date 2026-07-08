// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Remito = Backbone.Model.extend({
    urlRoot: "remitos/",
    defaults: {
      fecha: "",
      tipo: "", // TODO: Indica si es Comprobante o Pago
      id_punto_venta: 0,
      punto_venta: 0,
      id_cliente: 0,
      id_vendedor: 0,
      id_empresa: 0,
      estado: 0,
      cliente: "Consumidor Final",
      numero: 0,
      subtotal: 0,
      total: 0,
      neto: 0,
      porc_descuento: 0,
      descuento: 0,
      iva: 0,
      id_tipo_comprobante: 999,
      efectivo: 0,
      cta_cte: 0,
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
      observaciones: "",
      gestiona_stock: 0, // TODO: este parametro debe ser configurable
      cotizacion_dolar: 0,
      id_remito: 0,
      numero_remito: 0,
      enviada: 0,
      visto: 0,
      direccion: "",
      localidad: "",
      id_tipo_estado: 0,
      costo_envio: 0,
      comision_vendedor: 0,
      retirar_envio: 0,
      numero_envio: "",
      link_envio: "",
      id_localidad: 0,
      codigo_postal: "",
    }
  });

})( app.models );



(function ( models ) {

  models.RemitoItem = Backbone.Model.extend({
    urlRoot: "facturas_items/",
    defaults: {
      id_articulo: 0,
      tipo_cantidad: "",
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      neto: 0,    // Unitario
      precio: 0,  // Unitario
      nombre: "",
      orden: 0,
      id_rubro: 0,
      iva: 0,
      total_sin_iva: 0, // Totales (unitario * cantidad)
      total_con_iva: 0,
      costo_final: 0,
    }
  });

})( app.models );





// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.RemitoEditView = app.mixins.View.extend({

    template: _.template($("#remito_edit_panel_template").html()),

    myEvents: {
      "click .aceptar": "aceptar",
      "click .anular": "anular",
      "click .config": "configurar",
      "click .imprimir": function(){
        this.imprimir(this.model.id,null,false);
      },
      "click .enviar":function() {
        var id = this.model.id;
        var links_adjuntos = new Array();
        links_adjuntos.push({
          tipo: TIPO_ADJUNTO_COMPROBANTE,
          id_objeto: id,
          nombre: this.model.get("comprobante"),
        });
        var email = new app.models.Consulta({
          links_adjuntos:links_adjuntos,
          asunto:"Remito",
          texto: (typeof FACTURACION_TEXTO_EMAIL != "undefined") ? FACTURACION_TEXTO_EMAIL : "",
        });
        workspace.nuevo_email(email);                
      },
      "click #remito_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "change #remito_fecha_reparto":"cambiar_numero_reparto",

      "change #remito_lista":function(e) {
        if (this.articulo != null && this.articulo != undefined) {
          var lista = parseInt($(e.currentTarget).val());
          var valor = 0;
          if (lista == 0) {
            valor = this.articulo.get("precio_final");
          } else if (lista == 1) {
            valor = this.articulo.get("precio_final_2");
          } else if (lista == 2) {
            valor = this.articulo.get("precio_final_3");
          }
          $("#remito_item_precio").val(valor);
          this.calcular_item();
        }
      },

      "change #remito_item_cantidad": "calcular_item",
      "change #remito_item_neto": "calcular_item",
      "change #remito_item_precio": "calcular_item",
      "change #remito_alicuotas_iva": "calcular_item",
      "change #remito_item_bonificado": "calcular_item",
      "click #remito_agregar_item": "agregar_item",

      "change #remito_direccion":function() {
        $("#remito_cliente_direccion").html($("#remito_direccion").val());
      },
      "change #remito_localidad":function() {
        $("#remito_cliente_localidad").html($("#remito_localidad").val());
      },

      "click .importar_remito": "ver_buscar_comprobantes",

      // Buscamos el cliente por codigo
      "keyup #remito_codigo_cliente": function(e) {
        if (e.keyCode == 113) { this.ver_buscar_cliente(); }
      },
      "keypress #remito_codigo_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); $("#remito_codigo_articulo").select(); }
      },
      
      "click #remito_buscar_cliente": "ver_buscar_cliente",

      "keyup #remito_reparto":function(e) {
        if (e.which == 13) { this.$("#remito_codigo_articulo").select(); }
      },
      
      "keypress #remito_codigo_articulo": function(e) {
        if (e.which == 13)  {
          this.buscar_articulo();    
        }
      },
      "keyup #remito_codigo_articulo": function(e) {
        if (e.which == 113) { $("#remito_codigo_cliente").select(); e.preventDefault(); } // F2
        if (e.which == 45) { this.aceptar(); this.$("#remito_codigo_articulo").val(""); }
      },
      "keypress #remito_item_cantidad":function(e) {
        if (e.which == 13)  {
          if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
            $("#remito_item_neto").select();
          } else {
            $("#remito_item_precio").select();
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
        html+= "<li>{{TOTAL_EN_LETRAS}} = Total de la remito expresado en letras.</li>";
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
        if (e.which == 120) { this.ver_buscar_articulo(); e.preventDefault(); }
        if (e.which == 118) { this.anular(); } // F7
      },

      "keypress #remito_item_precio": function(e) {
        if (e.keyCode == 13) { this.$("#remito_item_bonificado").select(); }
      },
      "keypress #remito_item_neto": function(e) {
        if (e.keyCode == 13) { this.$("#remito_item_bonificado").select(); }
      },
      "keypress #remito_item_bonificado": function(e) {
        if (e.keyCode == 13) { this.agregar_item(); }
      },

      "change #remito_porc_descuento": function() {
        this.calcular_totales();
      },
      "change #remito_costo_envio": function() {
        this.calcular_totales();
      },

      "change #remito_puntos_venta":function() {
        this.buscar_numeros();
      },

      // Tenemos el numero de envio, pero por algun motivo no le link de envio
      "click .obtener_link_envio":function() {
        var numero = this.model.get("numero_envio");
        $.ajax({
          "url":"/sistema/servicios_envio/function/link_andreani/"+numero+"/",
          "dataType":"json",
          "success":function(r){
            if (r.error == 0) {
              window.open(r.link,"_blank");
            } else {
              alert(r.mensaje);
            }
          }
        })
      },
      // No tenemos el numero de envio, pero el pedido esta finalizado
      "click .enviar_andreani": function() {
        var self = this;
        $.ajax({
          "url":"/sistema/servicios_envio/function/enviar_andreani/"+self.model.id+"/",
          "dataType":"json",
          "success":function(r){
            if (r.error == 0) {
              alert("El pedido ha sido enviado correctamente.");
              location.reload();
            } else {
              alert(r.mensaje);
            }
          }
        })
      },            
      
    },

    imprimir: function(id,limpiar_despues) {
      var self = this;
      var lim = limpiar_despues;
      if (lim == undefined) lim = false;
      var id_punto_venta = this.model.get("id_punto_venta");
      workspace.imprimir_factura(id,id_punto_venta,"",function(){
        if (lim == true) {
          self.limpiar();
          $("#remito_codigo_articulo").val("");
          $("#remito_codigo_cliente").select();                                                                
        }
      });
    },

    configurar : function() {
      var configuracion = new app.models.ConfiguracionFacturacion({ "id": ID_EMPRESA });
      configuracion.fetch({
        "success":function() {
          app.views.configuracionRemitocionView = new app.views.ConfiguracionFacturacionView({
            model: configuracion,
          });
          var d = $("<div/>").append(app.views.configuracionRemitocionView.el);
          crearLightboxHTML({
            "html":d,
            "width":860,
            "height":500,
          });
        }
      });
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
        "success":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            self.seleccionar_cliente(window.cliente_seleccionado);
          }
          $("#remito_codigo_articulo").select();                    
        }
      });
      $(".search_input").select();
    },

    ver_buscar_comprobantes: function() {
      var self = this;
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
      });
    },

    importar: function(model) {
      var self = this;
      this.model = new app.models.Remito({
        "id": model.id,
      });
      this.model.fetch({
        "success":function(){
          self.model.id = 0;
          self.listenTo(self.model,"change",self.render_view); // Si el modelo cambia, renderizamos la vista
          
          // Creamos una nueva coleccion de items
          var ItemsCollection = Backbone.Collection.extend({
            model: app.models.RemitoItem
          });
          var productos = self.model.get("items");
          self.items = new ItemsCollection();
          for(var i=0;i<productos.length;i++) {
            var p = productos[i];
            p.discrimina_iva = self.discrimina_iva();
            var fi = new app.models.RemitoItem(p);
            self.items.add(fi);
          }
          self.items.on('all', self.render_tabla_items, self);
          self.items.on('add', self.addItem, self);
          
          self.render();
          self.render_view();
          self.render_tabla_items();
        }
      });
    },

    buscar_cliente : function() {
      var self = this;
      
      var codigo = this.$("#remito_codigo_cliente").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#remito_codigo_cliente").val(codigo);
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
                self.$("#remito_codigo_cliente").select();
                self.$("#remito_codigo_cliente").focus();
                return;
              }
              var cliente = new app.models.Cliente(r);
              self.seleccionar_cliente(cliente);
            }
          });
        }
      }
      this.$("#remito_codigo_articulo").focus();    
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
      });            
      this.seleccionar_cliente(cf);
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      if (this.model.id == undefined || this.model.id == 0) self.set_numero_comprobante();
      
      // Recorremos los elementos de la coleccion
      if (this.items.size() > 0) {
        var discrimina_iva = this.discrimina_iva();
        this.items.each(function(e){
          e.set({ "discrimina_iva":discrimina_iva });
        });                
      }
      
      if (self.model.id == null) {
        // Si es un comprobante nuevo, tomamos el descuento del cliente
        self.$("#remito_porc_descuento").val(self.cliente.get("descuento"));
      } else {
        // Sino tomamos el descuento del comprobante, ya que puede haber sido cambiado
        self.$("#remito_porc_descuento").val(self.model.get("porc_descuento"));
      }

      self.render_view();
      self.$("#remito_codigo_articulo").focus();

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#remito_codigo_cliente').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    // Actualizamos la vista con los datos del modelo
    render_view: function() {

      var self = this;

      // Mostramos el nombre de comprobante que corresponde
      var id_tipo_comprobante = parseInt(this.model.get("id_tipo_comprobante"));
      $(".invoice-type").html("Remito");
      $(".letter").html("R");
      
      self.$("#remito_numero").val(self.model.get("numero"));
      
      // Mostamos los datos del cliente
      if (self.cliente != null) {

        self.$("#remito_id_cliente").val(self.cliente.id);

        var id_tipo_iva = self.cliente.get("id_tipo_iva");
        if (id_tipo_iva == 1) self.$("#remito_cliente_iva").html("Responsable Inscripto");
        else if (id_tipo_iva == 2) self.$("#remito_cliente_iva").html("Monotributo");
        else if (id_tipo_iva == 3) self.$("#remito_cliente_iva").html("Exento");
        else if (id_tipo_iva == 4) self.$("#remito_cliente_iva").html("Consumidor Final");
        self.$("#remito_codigo_cliente").val(self.cliente.get("nombre"));
        self.$("#remito_cliente_remito").html(self.cliente.get("nombre"));
        self.$("#remito_cliente_cuit").html(self.cliente.get("cuit"));

        if (isEmpty(self.model.get("direccion"))) {
          self.$("#remito_cliente_direccion").html(self.cliente.get("direccion"));
          self.$("#remito_direccion").val(self.cliente.get("direccion"));
        }
        if (isEmpty(self.model.get("localidad"))) {
          self.$("#remito_cliente_localidad").html(self.cliente.get("localidad"));
          self.$("#remito_localidad").val(self.cliente.get("localidad"));
          self.$("#remito_codigo_postal").val(self.cliente.get("codigo_postal"));
        }

        self.$("#remito_lista").val(self.cliente.get("lista"));
        if (self.cliente.get("id_vendedor") != 0) self.$("#remito_vendedores").val(self.cliente.get("id_vendedor"));
        self.$("#remito_saldo_anterior").val(Number(self.cliente.get("saldo")).toFixed(2));
      }
      
      // Forma de Pago
      if (self.model.get("tipo_pago") == "E") {
        self.$("#remito_forma_pago_remito").html("Efectivo");
      } else {
        self.$("#remito_forma_pago_remito").html("Cuenta Corriente");
      }
      
      // Fecha
      self.$("#remito_fecha_remito").html(self.model.get("fecha"));

      self.$("#remito_costo_envio").val(Number(self.model.get("costo_envio")).toFixed(2));
      
      // Totales
      self.$("#remito_subtotal").val(Number(self.model.get("subtotal")).toFixed(2));
      self.$("#remito_descuento").val(Number(self.model.get("descuento")).toFixed(2));    
      self.$("#remito_total").val(Number(self.model.get("total")).toFixed(2));
    },

    set_numero_comprobante: function() {
      var self = this;
      var tipo_comprobante = 999;
      if (self.model.id == undefined && self.numeros != undefined) {
        self.model.set({ "numero":self.numeros[tipo_comprobante] });
      }
    },

    buscar_numeros: function() {
      var self = this;
      var id_punto_venta = this.$("#remito_puntos_venta").val();
      $.ajax({
        "url":"remitos/function/next/"+id_punto_venta,
        "dataType":"json",
        "success":function(r) {
          self.numeros = r;
          self.set_numero_comprobante();
        }
      });            
    },


    buscar_articulo : function() {

      var self = this;
      var codigo = $("#remito_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      
      if (typeof FACTURACION_CODIGO_FINALIZAR != "undefined" && codigo == FACTURACION_CODIGO_FINALIZAR) {
        this.aceptar(); return;
      }

      if (CACHE_ARTICULOS == 1 || FACTURACION_USA_CACHE_ARTICULOS == 1) { 
      
        // Lo buscamos en el array
        var r = window.articulos.find(function(c){

          // Si tenemos codigo de barra
          var encontro_codigo_barra = false;
          var codigos = c.get("codigos");
          for(var cc = 0; cc < codigos.length; cc++) {
            var codigo_barra = codigos[cc];
            if (codigo_barra == codigo) {
              encontro_codigo_barra = true;
              break;
            }
          }
          if (encontro_codigo_barra) return true;

          // Sino buscamos por codigo o codigo de barra
          return (c.get("codigo") == codigo);
        });
        if (typeof r === "undefined") {
          self.articulo = null;
          self.$("#remito_item_cantidad").select();
        } else {
          this.seleccionar_articulo(r);
        }

      // Los articulos no se encuentran cacheados en un array de JS, por lo que hay que buscarlo con AJAX
      } else {

        $.ajax({
          "url":"articulos/function/get_by_codigo/"+codigo,
          "dataType":"json",
          "type":"post",
          "data":{
            "id_sucursal":ID_SUCURSAL,
          },
          "success":function(result) {
            if (result.error == 1) {
              self.articulo = null;
              self.$("#remito_item_cantidad").select();
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
      this.$("#remito_item_cantidad").select();
    },

    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#remito_id_articulo").val(this.item.get("id_articulo"));
      $("#remito_codigo_articulo").val(this.item.get("nombre"));
      var cantidad = parseFloat(this.item.get("cantidad"))
      $("#remito_item_cantidad").val(cantidad);
      $("#remito_tipo_item").val(this.item.get("tipo"));
      $("#remito_item_neto").val(this.item.get("neto"));
      $("#remito_item_precio").val(this.item.get("precio"));
      var costo_final = ((cantidad > 0) ? (parseFloat(this.item.get("costo_final")) / cantidad) : 0);
      $("#remito_costo_final").val(costo_final);
      $("#remito_item_bonificado").val(this.item.get("bonificacion"));
      self.calcular_item();
      this.$("#remito_item_cantidad").select();            
    },

    ver_buscar_articulo : function() {
      var self = this;
      app.views.buscarArticulosResultados = new app.views.ArticulosTableView({
        collection: articulos,
        habilitar_seleccion: true,
      });    
      var d = $("<div/>").append(app.views.buscarArticulosResultados.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            $("#remito_codigo_articulo").val($("#remito_codigo_articulo").val()+window.codigo_articulo_seleccionado);
            //self.buscar_articulo();
            $("#remito_codigo_articulo").focus();
          } else {
            $("#remito_codigo_articulo").focus();
          }                    
        }
      });
      $("#articulos_texto").select();
    },

    mostrar_articulo : function() {
      this.$("#remito_codigo_articulo").val(this.articulo.get("nombre"));
      this.$("#remito_tipo_item").val(this.articulo.get("tipo"));
      this.$("#remito_alicuotas_iva").val(this.articulo.get("id_tipo_alicuota_iva"));
      this.$("#remito_costo_final").val(this.articulo.get("costo_final"));
      this.$("#remito_porc_iva").val(this.articulo.get("porc_iva"));
      this.$("#remito_id_articulo").val(this.articulo.id);
      var lista = this.$("#remito_lista").val();
      // Dependiendo de la lista que estamos usando
      if (REMITOS_TOMAR_COSTO == 1) {
        this.$("#remito_item_neto").val(this.articulo.get("costo_neto"));
        this.$("#remito_item_precio").val(this.articulo.get("costo_final"));                    
      } else {
        if (lista == 0) {
          this.$("#remito_item_neto").val(this.articulo.get("precio_neto"));
          this.$("#remito_item_precio").val(this.articulo.get("precio_final"));    
        } else if (lista == 1) {
          this.$("#remito_item_neto").val(this.articulo.get("precio_neto_2"));
          this.$("#remito_item_precio").val(this.articulo.get("precio_final_2"));
        } else if (lista == 2) {
          this.$("#remito_item_neto").val(this.articulo.get("precio_neto_3"));    
          this.$("#remito_item_precio").val(this.articulo.get("precio_final_3"));    
        }                
      }
    },

    // Agrega el item a la lista
    agregar_item : function() {

      var self = this;

      var codigo = this.$("#remito_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#remito_codigo_articulo").focus();
        return;
      }

      var id_articulo = this.$("#remito_id_articulo").val();
      var id_rubro = (this.articulo != undefined) ? this.articulo.get("id_rubro") : 0;
      var cantidad = this.$("#remito_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }

      var id_tipo_alicuota_iva = this.$("#remito_alicuotas_iva").val();
      var porc_iva = parseFloat(this.$("#remito_porc_iva").val());
      if (id_tipo_alicuota_iva == 0) {
        id_tipo_alicuota_iva = 5;
        porc_iva = 21;
      }

      var bonificacion = this.$("#remito_item_bonificado").val();

      if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
        // DEBEMOS TOMAR EL NETO PARA REALIZAR EL REMITO (Lo usa CORRUGADOS)
        var precio = parseFloat(this.$("#remito_item_neto").val());
        var neto = precio / (1+(porc_iva / 100));
      } else {
        // El precio que figura es el FINAL
        var precio = parseFloat(this.$("#remito_item_precio").val());
        var neto = precio / (1+(porc_iva / 100));
      }

      var iva = neto * ((100-bonificacion)/100) * (porc_iva / 100) * cantidad;
      var total_sin_iva = neto * ((100-bonificacion)/100) * cantidad;
      var total_con_iva = precio * ((100-bonificacion)/100) * cantidad;
      var costo_final = (this.$("#remito_costo_final").length > 0) ? this.$("#remito_costo_final").val() : 0;
      costo_final = parseFloat(costo_final) * cantidad;

      var values = {
        "id_articulo":id_articulo,
        "precio":precio,
        "neto":neto,
        "iva":iva,
        "nombre":codigo,
        "costo_final":costo_final,
        "cantidad":cantidad,
        "bonificacion":bonificacion,
        "id_rubro":id_rubro,
        "porc_iva":porc_iva,
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "total_sin_iva":total_sin_iva,
        "total_con_iva":total_con_iva,
      };
      console.log(values);

      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.RemitoItem(values);
        this.items.add(item);
      }

      this.item = undefined;
      this.limpiar_item();
      this.$("#remito_codigo_articulo").select();            
    },

    calcular_item: function() {
      var self = this;
      var cantidad = this.$("#remito_item_cantidad").val();
      if (typeof REMITOS_TOMAR_PRECIO_NETO != "undefined" && REMITOS_TOMAR_PRECIO_NETO == 1) {
        var precio_unit = this.$("#remito_item_neto").val();
      } else {
        var precio_unit = this.$("#remito_item_precio").val();
      }
      var bonificado = this.$("#remito_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
      this.$("#remito_item_subtotal").val(subtotal);
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.options = options;

      _.bindAll(this);
      this.bind("limpiar",this.limpiar);

      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {
        this.limpiar();

      // Estamos editando
      } else {

        this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
        
        this.render();
        
        // Creamos una nueva coleccion de items
        var ItemsCollection = Backbone.Collection.extend({
          model: app.models.RemitoItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.RemitoItem(p);
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
      }
    },

    render: function() {

      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      if (isEmpty(this.model.get("fecha"))) {
        this.model.set("fecha",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#remito_fecha"),this.model.get("fecha"));

      this.limpiar_item();

      if (control.check("repartos")>0) {
        createdatepicker(this.$("#remito_fecha_reparto"),(isEmpty(this.model.get("fecha_reparto"))) ? new Date() : this.model.get("fecha_reparto"));
        this.$("#remito_reparto").TouchSpin({
          verticalbuttons: true,
          min: 0,
        });                
      }

      if (this.model.id == undefined || this.model.id == 0) {
        self.buscar_numeros();
      }

      // AUTOCOMPLETE DE CLIENTES
      // ------------------------
      var input = this.$("#remito_codigo_cliente");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });            
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
      
      var input = this.$("#remito_codigo_articulo");
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
      } else {
        $(input).customcomplete({
          "url":"/sistema/articulos/function/get_by_descripcion/",
          "form":null, // No quiero que se creen nuevos productos
          "hideNoResults":true,
          "width":"300px",
          "onSelect":function(item){
            self.$("#remito_codigo_articulo").val(item.codigo);
            self.buscar_articulo();
          }
        });        
      }
      
      return this;
    },

    cambiar_numero_reparto: function() {
      var fecha = this.$("#remito_fecha_reparto").val();
      fecha = fecha.replace(/\//g,"-");
      $.ajax({
        "url":"facturas/function/ultimo_reparto/"+fecha,
        "dataType":"json",
        "success":function(r) {
          $("#remito_reparto").val(r.reparto);
        }
      });
    },

    // SOLO LOS COMPROBANTES "A" DISCRIMINAN IVA
    discrimina_iva: function() {
      var t = parseInt(this.model.get("id_tipo_comprobante"));
      return (t<=4); // Tipos de comprobantes "A" => 1,2,3,4
    },

    calcular_totales : function() {

      var porc_descuento = 0; var total = 0;
      var descuento = 0; var subtotal = 0;
      var items = this.model.get("items");
      
      var porc_descuento = parseFloat(this.$("#remito_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);
      this.items.each(function(item){
        total = total + parseFloat(item.get("total_con_iva")) * pdesc;
        subtotal = subtotal + parseFloat(item.get("total_con_iva"));
      });
      
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;

      var costo_envio = parseFloat(this.$("#remito_costo_envio").val());
      if (isNaN(costo_envio)) costo_envio = 0;
      total = total + costo_envio;
      
      this.model.set({
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "subtotal":subtotal,
        "costo_envio":costo_envio,
        "total":total,
      });
    },

    limpiar_item: function() {
      this.$("#remito_id_articulo").val("0");
      this.$("#remito_porc_iva").val("0");
      this.$("#remito_alicuotas_iva").val("0");
      this.$("#remito_item_cantidad").val("1");
      this.$("#remito_item_bonificado").val("0");
      this.$("#remito_item_precio").val("0.00");
      this.$("#remito_item_subtotal").val("");
      this.$("#remito_codigo_articulo").val("");
      this.$("#remito_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },

    addItem : function ( item ) {
      item.set({ "discrimina_iva": this.discrimina_iva() });
      var view = new app.views.RemitoItem({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.render().el);
      this.calcular_totales();
    },        

    validar: function() {
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al comprobante antes de guardar.";
      }
      this.model.set({
        "direccion":$("#remito_direccion").val(),
        "localidad":$("#remito_localidad").val(),
        "id_vendedor":(self.$("#remito_vendedores").length > 0) ? self.$("#remito_vendedores").val():0,
      });
    },

    anular: function() {
      if (confirmar("Desea limpiar el comprobante?")) {
        this.limpiar();
        $("#remito_codigo_articulo").focus();
      }                
    },

    limpiar : function() {

      // Guardamos las variables antes de renderizar
      var lista = $("#remito_lista").val();
      var id_vendedor = this.model.get("id_vendedor");
      var cotizacion_dolar = this.$("#remito_cotizacion_dolar").val();
      var reparto = this.$("#remito_reparto").val();
      var reparto_fecha = this.$("#remito_reparto_fecha").val();
      
      // Conservamos el cliente cuando se limpia
      if (typeof FACTURACION_CONSERVAR_CLIENTE_AL_GUARDAR != "undefined" && FACTURACION_CONSERVAR_CLIENTE_AL_GUARDAR == 1) {
        var id_cliente = this.model.get("id_cliente");
      } else {
        var id_cliente = 0;
      }
      
      this.model = new app.models.Remito({
        "id_cliente":id_cliente,
        "id_vendedor":id_vendedor,
        "cotizacion_dolar":cotizacion_dolar,
      });
      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      window.remito = this.model; // TODO: borrar esto desp
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.RemitoItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
      if (control.check("repartos")>0 == 1) this.cambiar_numero_reparto();
      
      if (id_cliente == 0) this.setear_consumidor_final();
      else this.seleccionar_cliente(this.cliente);
      
      this.$("#remito_lista").val(lista);
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
      
      // Abrimos el dialogo para que el usuario espere
      workspace.esperar("Guardando comprobante...");
      
      if (this.model.id == null) {
        this.model.set({id:0});

        /*
        // Buscamos el punto de venta por defecto
        if (typeof window.puntos_venta != "undefined") { 
          var pv_default = false;
          for(var i=0;i<window.puntos_venta.length;i++) {
            var pv = window.puntos_venta[i];
            if (pv.por_default == 1) {
              pv_default = pv; break;
            }
          }
          if (pv_default != false) {
            this.model.set({
              "id_punto_venta":pv_default.id,
              "punto_venta":pv_default.numero,
            })
          }
        }
        */

      }

      this.model.set({
        "items":self.items.toJSON(),
        "codigo_postal":self.$("#remito_codigo_postal").val(),
        "fecha":self.$("#remito_fecha").val(),
        "fecha_reparto":self.$("#remito_fecha_reparto").val(),
        "tipo_pago":self.$("#remito_tipo_pago").val(),
        "numero":self.$("#remito_numero").val(),
        "observaciones":self.$("#remito_observaciones").val(),
        "cotizacion_dolar":(self.$("#remito_cotizacion_dolar").val() === undefined ? 0 : self.$("#remito_cotizacion_dolar").val()),
        "id_tipo_estado":(self.$("#remito_tipo_estado").val() === undefined ? 0 : self.$("#remito_tipo_estado").val()),
        "retirar_envio":( (self.$("#remito_retirar_envio").length == 0) ? 0 : (self.$("#remito_retirar_envio").is(":checked")?1:0)),
        "reparto":(control.check("repartos")>0 == 1)?self.$("#remito_reparto").val():0,
        "id_punto_venta": (self.$("#remito_puntos_venta").length > 0) ? self.$("#remito_puntos_venta").val() : 0,
        "estado": ESTADO,
        "id_empresa": ID_EMPRESA,
        "id_cliente":self.$("#remito_id_cliente").val(),
        "id_vendedor":(control.check("vendedores")>0)?self.$("#remito_vendedores").val():0,
      });

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
              self.imprimir(self.model.id,true);
            }
          }
        },
      });    
                       
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.RemitoItem = app.mixins.View.extend({

    template: _.template($("#remito_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        return false;
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);