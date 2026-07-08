// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Pedido = Backbone.Model.extend({
    urlRoot: "pedidos/",
    defaults: {
      fecha: "",
      id_cliente: 0,
      id_proveedor: 0,
      proveedor: "",
      id_vendedor: 0,
      id_empresa: ID_EMPRESA,
      id_tipo_estado: 0,
      cliente: "Consumidor Final",
      numero: 0,
      subtotal: 0,
      costo_envio: 0,
      comision_vendedor: 0,
      porc_descuento: 0,
      descuento: 0,
      total: 0,
      reparto: 0,
      fecha_reparto: "",
      items: [],
      observaciones: "",
      retirar_envio: 0,
      numero_envio: "",
      link_envio: "",
      direccion: "",
      id_localidad: 0,
      localidad: "",
      iva: 0,
    }
  });

})( app.models );



(function ( models ) {

  models.PedidoItem = Backbone.Model.extend({
    urlRoot: "pedidos/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      precio: 0,
      nombre: "",
      codigo: "",
      orden: 0,
      bonificacion: 0,
      total: 0,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Pedidos = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 10,
    },        

    paginator_core: {
      url: "pedidos/function/consulta/",
    },
  });

})( app.collections, app.models.Pedido, Backbone.Paginator);


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.PedidoEditView = app.mixins.View.extend({

		template: _.template($("#pedidos_edit_panel_template").html()),

		myEvents: {
      "click .aceptar": "aceptar",
      "click .anular": "anular",
      "click .imprimir": function(){
        this.imprimir(this.model.id,null,false);
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
      "click .editar":function(){

      },
      "click #pedidos_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "change #pedidos_fecha_reparto":"cambiar_numero_reparto",
      
      "change #pedidos_lista":function(e) {
        if (this.articulo != null && this.articulo != undefined) {
          var lista = parseInt($(e.currentTarget).val());
          var valor = 0;
          if (lista == 0) {
            valor = this.model.get("precio_final");
          } else if (lista == 1) {
            valor = this.model.get("precio_final_2");
          } else if (lista == 2) {
            valor = this.model.get("precio_final_3");
          }
          $("#pedidos_item_precio").val(valor);
          this.calcular_item();
        }
      },

      "change #pedidos_item_cantidad": "calcular_item",
      "change #pedidos_item_precio": "calcular_item",
      "change #pedidos_item_bonificado": "calcular_item",
      "click #pedidos_agregar_item": "agregar_item",
      
      "click .importar_pedido": "ver_buscar_comprobantes",
      
      // Buscamos el cliente por codigo
      "keyup #pedidos_codigo_cliente": function(e) {
        if (e.keyCode == 113) { this.ver_buscar_cliente(); }
      },
      "keypress #pedidos_codigo_cliente":function(e) {
        if (e.which == 13) { this.buscar_cliente(); $("#pedidos_codigo_articulo").select(); }
      },
      
      "click #pedidos_buscar_cliente": "ver_buscar_cliente",

      "keyup #pedidos_reparto":function(e) {
        if (e.which == 13) { this.$("#pedidos_codigo_articulo").select(); }
      },
      
      "keypress #pedidos_codigo_articulo": function(e) {
        if (e.which == 13)  {
          this.buscar_articulo();    
        }
      },
      "keyup #pedidos_codigo_articulo": function(e) {
				if (e.which == 113) { $("#pedidos_codigo_cliente").select(); e.preventDefault(); } // F2
        if (e.which == 45) { this.aceptar(); this.$("#pedidos_codigo_articulo").val(""); }
      },
      "keypress #pedidos_item_cantidad":function(e) {
        if (e.which == 13)  {
          $("#pedidos_item_precio").select();
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
            
			// ACCIONES SOBRE EL FORMULARIO
			"keyup .action":function(e) {
				if (e.which == 120) { this.ver_buscar_articulo(); e.preventDefault(); }
        if (e.which == 118) { this.anular(); } // F7
      },

      "keypress #pedidos_item_precio": function(e) {
        if (e.keyCode == 13) { this.$("#pedidos_item_bonificado").select(); }
      },
      "keypress #pedidos_item_bonificado": function(e) {
        if (e.keyCode == 13) { this.agregar_item(); }
      },

      "change #pedidos_porc_descuento": function() {
        this.calcular_totales();
      },
      "change #pedidos_costo_envio": function() {
        this.calcular_totales();
      },
    },

    imprimir: function(id,limpiar_despues) {
      var self = this;
      workspace.imprimir_pedido(id,function(){
        if (limpiar_despues == true) {
          self.limpiar();
          $("#pedidos_codigo_articulo").val("");
          $("#pedidos_codigo_cliente").select();                                                                
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
        "callback":function() {
          if (window.codigo_cliente_seleccionado != undefined && window.codigo_cliente_seleccionado != -1) {
            $("#pedidos_codigo_cliente").val(window.codigo_cliente_seleccionado);
          }
          $("#pedidos_codigo_cliente").select();                    
        }
      });
      $(".search_input").select();
    },

    ver_buscar_comprobantes: function() {
      var self = this;
      app.views.pedidosTableView = new app.views.PedidosTableView({
        collection: new app.collections.Pedidos(),
        habilitar_seleccion: true,
        parent: self,
      });
      var d = $("<div/>").append(app.views.pedidosTableView.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
      });
    },

    importar: function(model) {
      var self = this;
      this.model = new app.models.Pedido({
        "id": model.id,
      });
      this.model.fetch({
        "success":function(){
          self.model.id = 0;
          self.listenTo(self.model,"change",self.render_view); // Si el modelo cambia, renderizamos la vista
          
          // Creamos una nueva coleccion de items
          var ItemsCollection = Backbone.Collection.extend({
            model: app.models.PedidoItem
          });
          var productos = self.model.get("items");
          self.items = new ItemsCollection();
          for(var i=0;i<productos.length;i++) {
            var p = productos[i];
            var fi = new app.models.PedidoItem(p);
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

      var codigo = this.$("#pedidos_codigo_cliente").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#pedidos_codigo_cliente").val(codigo);
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
                self.$("#pedidos_codigo_cliente").select();
                self.$("#pedidos_codigo_cliente").focus();
                return;
              }
              var cliente = new app.models.Cliente(r);
              self.seleccionar_cliente(cliente);
            }
          });
        }
      }
      this.$("#pedidos_codigo_articulo").focus();    
    },

    setear_consumidor_final: function() {
      var cf = new app.models.Cliente({
        "id_tipo_iva":4,
        "nombre":"Consumidor Final",
        "cuit":"",
        "email":"",
        "direccion":"",
        "localidad":"",
        "codigo_postal":"",
        "descuento":0,
        "id_vendedor":0,
        "lista":0,
      });            
      this.seleccionar_cliente(cf);
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
    
      if (self.model.id == null) {
        // Si es un comprobante nuevo, tomamos el descuento del cliente
        self.$("#pedidos_porc_descuento").val(self.cliente.get("descuento"));
      } else {
        // Sino tomamos el descuento del comprobante, ya que puede haber sido cambiado
        self.$("#pedidos_porc_descuento").val(self.model.get("porc_descuento"));
      }

      self.render_view();
      self.$("#pedidos_codigo_articulo").focus();
    },

        // Actualizamos la vista con los datos del modelo
        render_view: function() {

          var self = this;

            // Mostamos los datos del cliente
            if (self.cliente != null) {

              self.$("#pedidos_id_cliente").val(self.cliente.id);

              var id_tipo_iva = self.cliente.get("id_tipo_iva");
              if (id_tipo_iva == 1) self.$("#pedidos_cliente_iva").html("Responsable Inscripto");
              else if (id_tipo_iva == 2) self.$("#pedidos_cliente_iva").html("Monotributo");
              else if (id_tipo_iva == 3) self.$("#pedidos_cliente_iva").html("Exento");
              else if (id_tipo_iva == 4) self.$("#pedidos_cliente_iva").html("Consumidor Final");
              self.$("#pedidos_codigo_cliente").val(self.cliente.get("nombre"));
              self.$("#pedidos_cliente_pedido").html(self.cliente.get("nombre"));
              self.$("#pedidos_cliente_cuit").html(self.cliente.get("cuit"));
              if (!isEmpty(self.model.get("localidad"))) {
                self.$("#pedidos_cliente_localidad").html(self.model.get("localidad"));
                self.$("#pedidos_cliente_direccion").html(self.model.get("direccion"));
              } else {
                self.$("#pedidos_cliente_localidad").html(self.cliente.get("localidad"));
                self.$("#pedidos_cliente_direccion").html(self.cliente.get("direccion"));                    
              }

              self.$("#pedidos_lista").val(self.cliente.get("lista"));
              self.$("#pedidos_vendedores").val(self.cliente.get("id_vendedor"));
            }
            
            // Fecha
            self.$("#pedidos_fecha_pedido").html(self.model.get("fecha"));
            
            self.$("#pedidos_costo_envio").val(Number(self.model.get("costo_envio")).toFixed(2));            
            self.$("#pedidos_subtotal").val(Number(self.model.get("subtotal")).toFixed(2));
            self.$("#pedidos_descuento").val(Number(self.model.get("descuento")).toFixed(2));    
            self.$("#pedidos_total").val(Number(self.model.get("total")).toFixed(2));
          },

          buscar_articulo : function() {

            var self = this;
            var codigo = $("#pedidos_codigo_articulo").val();
            codigo = codigo.trim();
            
            // Si el metodo de ingreso es un unico campo, analizamos si
            // en el mismo se estan ingresando cantidades, etc..
            /*if (FACTURACION_METODO_INGRESO == "J") {
                
                var modif = codigo;
                // Si el codigo del articulo esta vacio, no ingresa nada
                if (isEmpty(codigo)) return;
                
                // FORMATO:
                // (codigo) (/cambio) (+bonif) (-desc) (*unidad)
                
                var primero = codigo.length;
                var pos_men = codigo.indexOf("-");
                if (pos_men >= 0 && pos_men < primero) primero = pos_men;
                var pos_mas = codigo.indexOf("+");
                if (pos_mas >= 0 && pos_mas < primero) primero = pos_mas;
                var pos_div = codigo.indexOf("/");
                if (pos_div >= 0 && pos_div < primero) primero = pos_div;
                var pos_por = codigo.indexOf("*");
                if (pos_por >= 0 && pos_por < primero) primero = pos_por;
                
                if (pos_por > 0) {
                    this.cantidad = Number(getField(modif,pos_por+1)).toFixed(3);
                } else {
                    if (pos_men > 0 || pos_mas > 0 || pos_div > 0) this.cantidad = 0;
                    else this.cantidad = Number(1).toFixed(3);
                }
                
                codigo = codigo.substr(0,primero);
                
                if (pos_men != -1) {
                    this.porc_descuento_item = getField(modif,pos_men+1);
                }
                if (pos_mas != -1) {
                    this.bonificacion = getField(modif,pos_mas+1);
                }
                if (pos_div != -1) {
                    this.devolucion = getField(modif,pos_div+1);
                }
              } else {*/

                // Si el codigo del articulo esta vacio, simplemente saltamos a que escriba la descripcion
                if (isEmpty(codigo)) {
                    //$("#pedidos_item_descripcion").select();
                    return;
                  }                
            //}
            
            // Lo buscamos en el array
            var r = window.articulos.find(function(c){
              return (c.get("codigo") == codigo);
            });
            if (typeof r === "undefined") {
              self.articulo = null;
              this.$("#pedidos_item_cantidad").select();
            } else {
              this.seleccionar_articulo(r);
            }
          },

          seleccionar_articulo : function(r) {
            var self = this;
            self.articulo = r;
            self.mostrar_articulo();
            self.calcular_item();
            this.$("#pedidos_item_cantidad").select();
          },

          editar_articulo: function(r) {
            var self = this;
            self.item = r;
            $("#pedidos_codigo_articulo").val(this.item.get("nombre"));
            $("#pedidos_item_cantidad").val(this.item.get("cantidad"));
            $("#pedidos_tipo_item").val(this.item.get("tipo"));
            $("#pedidos_item_precio").val(this.item.get("precio"));
            $("#pedidos_item_bonificado").val(this.item.get("bonificacion"));
            self.calcular_item();
            this.$("#pedidos_item_cantidad").select();            
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
                  $("#pedidos_codigo_articulo").val($("#pedidos_codigo_articulo").val()+window.codigo_articulo_seleccionado);
                        //self.buscar_articulo();
                        $("#pedidos_codigo_articulo").focus();
                      } else {
                        $("#pedidos_codigo_articulo").focus();
                      }                    
                    }
                  });
            $("#articulos_texto").select();
          },


          mostrar_articulo : function() {
            $("#pedidos_codigo_articulo").val(this.articulo.get("nombre"));
            $("#pedidos_tipo_item").val(this.articulo.get("tipo"));
            var lista = $("#pedidos_lista").val();
            // Dependiendo de la lista que estamos usando
            if (lista == 0) {
              $("#pedidos_item_precio").val(this.articulo.get("precio_final"));    
            } else if (lista == 1) {
              $("#pedidos_item_precio").val(this.articulo.get("precio_final_2"));
            } else if (lista == 2) {
              $("#pedidos_item_precio").val(this.articulo.get("precio_final_3"));    
            }
          },

        // Agrega el item a la lista
        agregar_item : function() {

          var self = this;

          var codigo = this.$("#pedidos_codigo_articulo").val();
          if (isEmpty(codigo)) {
            alert("Por favor escriba o seleccione un articulo.");
            this.$("#pedidos_codigo_articulo").focus();
            return;
          }

          var id_articulo = (this.articulo != undefined) ? this.articulo.id : 0;
          var cantidad = this.$("#pedidos_item_cantidad").val();
          cantidad = parseFloat(cantidad);
          if (isNaN(cantidad)) { cantidad = Number(1).toFixed(3); }

          var bonificacion = this.$("#pedidos_item_bonificado").val();

            // El precio que figura es el FINAL
            var precio = parseFloat(this.$("#pedidos_item_precio").val());
            var total = precio * ((100-bonificacion)/100) * cantidad;
            
            var values = {
              "id_articulo":id_articulo,
              "precio":precio,
              "nombre":codigo,
              "cantidad":cantidad,
              "bonificacion":bonificacion,
              "total_con_iva":total,
            };
            
            if (this.item != undefined) {
              this.item.set(values);
            } else {
              var item = new app.models.PedidoItem(values);
              this.items.add(item);
            }
            
            this.item = undefined;
            this.limpiar_item();
            this.$("#pedidos_codigo_articulo").select();              
          },

          calcular_item: function() {
            // TODO: Controlar los campos cuando no son numericos
            var self = this;
            var cantidad = this.$("#pedidos_item_cantidad").val();
            var precio_unit = this.$("#pedidos_item_precio").val();
            var bonificado = this.$("#pedidos_item_bonificado").val();
            var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(2);
            this.$("#pedidos_item_subtotal").val(subtotal);
          },

          initialize: function(options) 
          {
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
                  model: app.models.PedidoItem
                });
                var productos = this.model.get("items");
                this.items = new ItemsCollection();
                for(var i=0;i<productos.length;i++) {
                  var p = productos[i];
                  var fi = new app.models.PedidoItem(p);
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
                
                this.render_tabla_items();
                $("#tabla_items tbody").empty();
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
          createdatepicker(this.$("#pedidos_fecha"),this.model.get("fecha"));

          this.limpiar_item();

          if (control.check("vendedores")>0) {
            new app.mixins.Select({
              modelClass: app.models.Vendedor,
              url: "vendedores/",
              render: "#pedidos_vendedores",
              name : "id_vendedor",
              firstOptions: ["<option value='0'>Vendedor</option>"],
              selected: this.model.get("id_vendedor"),
            });                
          }

          if (control.check("repartos")>0) {
            createdatepicker(this.$("#pedidos_fecha_reparto"),(isEmpty(this.model.get("fecha_reparto"))) ? new Date() : this.model.get("fecha_reparto"));
            this.$("#pedidos_reparto").TouchSpin({
              verticalbuttons: true,
              min: 0,
            });                
          }

            // AUTOCOMPLETE DE CLIENTES
            // ------------------------
            var input = this.$("#pedidos_codigo_cliente");
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
            
            var input = this.$("#pedidos_codigo_articulo");
            $(input).customcomplete({
              "collection":articulos,
              "hideNoResults":true,
              "onSelect":function(item){
                self.seleccionar_articulo(item.element);
              }
            });
            return this;
          },

          cambiar_numero_reparto: function() {
            var fecha = this.$("#pedidos_fecha_reparto").val();
            fecha = fecha.replace(/\//g,"-");
            $.ajax({
              "url":"pedidos/function/ultimo_reparto/"+fecha,
              "dataType":"json",
              "select":function(r) {
                $("#pedidos_reparto").val(r.reparto);
              }
            });
          },

          calcular_totales : function() {

            var porc_descuento = 0; var total = 0;
            var descuento = 0; var subtotal = 0;
            var items = this.model.get("items");
            
            var porc_descuento = parseFloat(this.$("#pedidos_porc_descuento").val());
            if (isNaN(porc_descuento)) porc_descuento = 0;
            var pdesc = ((100-porc_descuento) / 100);
            this.items.each(function(item){
              total = total + parseFloat(item.get("total_con_iva")) * pdesc;
              subtotal = subtotal + parseFloat(item.get("total_con_iva"));
            });
            
            var descuento = subtotal * parseFloat(porc_descuento / 100);
            if (isNaN(descuento)) descuento = 0;
            
            var costo_envio = parseFloat(this.$("#pedidos_costo_envio").val());
            if (isNaN(costo_envio)) costo_envio = 0;
            total = total + costo_envio;

            var iva = 0;
            if (this.$("#pedidos_iva").length > 0) {
              iva = parseFloat(this.$("#pedidos_iva").val());
            }
            total = total + iva;
            
            this.model.set({
              "porc_descuento":porc_descuento,
              "descuento":descuento,
              "subtotal":subtotal,
              "costo_envio":costo_envio,
              "total":total,
            });
          },

          limpiar_item: function() {
            this.$("#pedidos_item_cantidad").val("1");
            this.$("#pedidos_item_bonificado").val("0");
            this.$("#pedidos_item_precio").val("0.00");
            this.$("#pedidos_item_subtotal").val("");
            this.$("#pedidos_codigo_articulo").val("");
            this.$("#pedidos_codigo_articulo").focus();
          },

          render_tabla_items : function () {
            this.$("#tabla_items tbody").empty();
            this.items.each(this.addItem);
            this.calcular_totales();
          },

          addItem : function ( item ) {
            var view = new app.views.PedidoItem({
              "model": item,
              "view":this,
            });
            this.$("#tabla_items tbody").append(view.render().el);
            this.calcular_totales();
          },        

          validar: function() {
            if (this.items.size() == 0) {
              throw "ERROR: Ingrese al menos un item al pedido antes de guardar.";
            }
          },

          anular: function() {
            if (confirmar("Desea limpiar el pedido?")) {
              this.limpiar();
              $("#pedidos_codigo_articulo").focus();
            }                
          },

          limpiar : function() {

            // Guardamos las variables antes de renderizar
            var lista = $("#pedidos_lista").val();
            var id_vendedor = this.model.get("id_vendedor");
            var id_cliente = this.model.get("id_cliente");
            
            this.model = new app.models.Pedido({
              "id_cliente":id_cliente,
              "id_vendedor":id_vendedor,
            });
            this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
            
            // Creamos una nueva coleccion de items
            var ItemsCollection = Backbone.Collection.extend({
              model: app.models.PedidoItem,
            });
            this.items = new ItemsCollection();
            this.items.on('all', this.render_tabla_items, this);
            this.items.on('add', this.addItem, this);
            
            // Renderizamos y limpiamos
            this.render();
            if (control.check("repartos")>0 == 1) this.cambiar_numero_reparto();
            this.buscar_cliente();
            $("#pedidos_lista").val(lista);
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

            var codigo_postal = this.cliente.get("codigo_postal");
            
            if (this.model.id == null) {
              this.model.set({
                id:0,
                id_usuario: ID_USUARIO,
              });
            }
            this.model.set({
              "codigo_postal":codigo_postal,
              "items":self.items.toJSON(),
              "fecha":self.$("#pedidos_fecha").val(),
              "fecha_reparto":self.$("#pedidos_fecha_reparto").val(),
              "observaciones":self.$("#pedidos_observaciones").val(),
              "reparto":(control.check("repartos")>0 == 1)?self.$("#pedidos_reparto").val():0,
              "estado": ESTADO,
              "id_empresa": ID_EMPRESA,
              "id_cliente":self.$("#pedidos_id_cliente").val(),
              "id_vendedor":(control.check("vendedores")>0)?self.$("#pedidos_vendedores").val():0,
              "id_tipo_estado":self.$("#pedidos_tipo_estado").val(),
              "retirar_envio":(self.$("#pedidos_retirar_envio").is(":checked")?1:0),
            });
            this.model.save({},{
              success: function(model,response) {
                    self.guardando = 0; // Habilitamos el boton
                    if (response.id != undefined) {
                      self.model.id = response.id;
                    }
                    location.href="app/#pedidos";
                  },
                });                     
          },

        });

})(app.views, app.models);



(function ( app ) {
  app.views.PedidoItem = app.mixins.View.extend({
    template: _.template($("#pedido_item_template").html()),
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
        this.model.destroy();	// Eliminamos el modelo
        $(this.el).remove();	// Lo eliminamos de la vista
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




// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.PedidosTableView = app.mixins.View.extend({

    template: _.template($("#pedidos_resultados_template").html()),

    myEvents: {
      "click .exportar":"exportar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar",
      "change .buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.permiso = this.options.permiso;            

      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion
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
            
            new app.mixins.Select({
              modelClass: app.models.Cliente,
              url: "clientes/",
              render: "#pedidos_listado_cliente",
              firstOptions: ["<option value='0'>Clientes</option>"],
            });
            
            if (control.check("vendedores")>0) {
              new app.mixins.Select({
                modelClass: app.models.Vendedor,
                url: "vendedores/",
                render: "#pedidos_listado_vendedor",
                firstOptions: ["<option value='0'>Vendedores</option>"],
              });                
            }

            createdatepicker(this.$("#pedidos_desde"));
            createdatepicker(this.$("#pedidos_hasta"));
            this.buscar();
          },

          exportar_csv: function(obj) {
            var desde = $("#pedidos_desde").val();
            desde = desde.replace(/\//g,"-");
            var hasta = $("#pedidos_hasta").val();
            hasta = hasta.replace(/\//g,"-");
            window.open("pedidos/function/exportar_csv/"+desde+"/"+hasta,"_blank");
          },

          importar: function() {
            app.views.importar = new app.views.Importar({
              "table":"pedidos"
            });
            crearLightboxHTML({
              "html":app.views.importar.el,
              "width":450,
              "height":140,
            });
          },

          buscar: function() {
            var self = this;
            var filtros = {};
            filtros.id_usuario = (SOLO_USUARIO == 1) ? ID_USUARIO : 0;
            if (!isEmpty(this.$("#pedidos_listado_cliente").val())) 
              filtros.id_cliente = this.$("#pedidos_listado_cliente").val();
            if (!isEmpty(this.$("#pedidos_listado_vendedor").val())) 
              filtros.id_vendedor = this.$("#pedidos_listado_vendedor").val();
            if (!isEmpty(this.$("#pedidos_listado_numero").val())) 
              filtros.numero = this.$("#pedidos_listado_numero").val();
            
            var fecha_desde = this.$("#pedidos_desde").val();
            if (isEmpty(fecha_desde)) fecha = 0;
            else fecha_desde = fecha_desde.replace(/\//g,"-");
            if (!isEmpty(fecha_desde)) filtros.desde = fecha_desde;
            
            var fecha_hasta = this.$("#pedidos_hasta").val();
            if (isEmpty(fecha_hasta)) fecha = 0;
            else fecha_hasta = fecha_hasta.replace(/\//g,"-");
            if (!isEmpty(fecha_hasta)) filtros.hasta = fecha_hasta;
            
            this.collection.server_api = filtros;
            this.collection.pager();            
          },

          exportar : function() {

            var self = this;
            var header = new Array();
            $(".table thead tr th").each(function(i,e){
              var t = $(e).text();
              if (!isEmpty(t)) header.push(t);
            });
			// Acomodamos los datos
			var array = new Array();
			_.each(self.collection.models,function(m){
				array.push({
					"fecha": m.get("fecha"),
					"cliente": (isEmpty(m.get("cliente"))?"Consumidor Final":m.get("cliente"))+(m.get("anulada") == 1?"(ANULADA)":""),
					"reparto": m.get("reparto")+" - "+m.get("fecha_reparto"),
					"vendedor":m.get("vendedor"),
					"total":Number(m.get("total")).toFixed(2),
				});
			});
      this.exportar_excel({
        "filename":"pedidos",
        "title":"Listado de Pedidos",
        "date":$("#pedidos_desde").val()+" - "+$("#pedidos_hasta").val(),
        "data":array,
        "header":header,
      });			
    },

    addAll : function () {
      this.$("#pedidos_tabla tbody tr").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PedidosItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#pedidos_tabla tbody").append(view.render().el);
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.PedidosItemResultados = Backbone.View.extend({

    template: _.template($("#pedidos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click":"seleccionar",
      "click .edit":"editar",
      "click .delete":"borrar",
      "click .print":"imprimir",
    },
    seleccionar : function(e) {
      if (this.options.seleccionar && this.parent != undefined) {
        $('.modal:last').modal('hide');
        this.parent.importar(this.model);
      } else this.editar();
    },
    editar : function() {
      location.href="app/#pedido/"+this.model.id;
    },
    borrar : function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (confirmar("Realmente desea eliminar este pedido?")) {
        $.ajax({
          "url":"pedidos/function/delete/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.pedidosTableView.buscar();
          }
        });                
      }
    },
    imprimir: function() {
      window.open("pedidos/function/imprimir/"+this.model.id,"_blank");
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
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