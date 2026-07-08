// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Stock = Backbone.Model.extend({
    urlRoot: "stock",
    defaults: {
      id_stock: 0,
      id_sucursal: 0,
      id_proveedor: 0,
      movimiento: "",
      saldo: 0,
      fecha: "",
      codigo: "",
      uxb: "",
      nombre: "",
      items: [],
      fecha_ult_compra: "",
      fecha_ult_venta: "",
    },
  });
	  
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {
  collections.Stocks = paginator.requestPager.extend({
  	model: model,
  	paginator_ui: {
  	  perPage: 10,
  	},
  	paginator_core: {
  	  url: "stock/function/ver",
  	}
  });
})( app.collections, app.models.Stock, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.StocksResultados = app.mixins.View.extend({

    template: _.template($("#stocks_resultados_template").html()),
      
    myEvents: {
      "click .nuevo":"nuevo",
      "click .buscar":"buscar",
      "click .exportar":"exportar",
      "click .imprimir":function() { this.imprimir(1); },
      "click .imprimir_mov":function() { this.imprimir(2); },
      "click .generar_pedido": "generar_pedido",
      "keypress #stocks_texto":function(e) {
        if (e.which == 13) { this.buscar(); }
      },
      "keypress #stocks_buscar_codigo_prov":function(e) {
        if (e.which == 13) { this.buscar(); }
      },
      "change #stocks_moneda":function(e) {
        if ($(e.currentTarget).val() == "$") {
          this.$("#stocks_costo_total_dolares").addClass("dn");
          this.$("#stocks_venta_total_dolares").addClass("dn");
          this.$("#stocks_costo_total").removeClass("dn");
          this.$("#stocks_venta_total").removeClass("dn");
        } else {
          this.$("#stocks_costo_total").addClass("dn");
          this.$("#stocks_venta_total").addClass("dn");
          this.$("#stocks_costo_total_dolares").removeClass("dn");
          this.$("#stocks_venta_total_dolares").removeClass("dn");
        }
      },
    },
	
    initialize: function() {
      var self = this;
      _.bindAll(this);
      var lista = this.collection;
	  
      var pagination = new app.mixins.PaginationView({
        collection: lista,
        ver_numeros_pagina: false
      });
      
      lista.on('add', this.addOne, this);
      lista.on('all', this.addAll, this);
      
      $(this.el).html(this.template());
      this.render();
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
    },
    
    render: function() {
      
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#stocks_proveedores",
        firstOptions: ["<option value='0'>Proveedor</option>"],
        selected: 0,
        onComplete:function(c) {
          $("#stocks_proveedores").select2({});
        }        
      });
      
      new app.mixins.Select({
        modelClass: app.models.Rubro,
        url: "rubros/",
        render: "#stocks_rubros",
        firstOptions: ["<option value='0'>Rubro</option>"],
        selected: 0,
        onComplete:function(c) {
          $("#stocks_rubros").select2({});
        }        
      });
      
      new app.mixins.Select({
        modelClass: app.models.Marca,
        url: "marcas/",
        render: "#stocks_marcas",
        firstOptions: ["<option value='0'>Marca</option>"],
        selected: 0,
        onComplete:function(c) {
          $("#stocks_marcas").select2({});
        }        
      });   

      createdatepicker(this.$("#stocks_desde"));

      this.buscar();     
    },
    
    buscar : function() {
      var self = this;
      var id_proveedor = (this.$("#stocks_proveedores").length > 0) ? this.$("#stocks_proveedores").val() : 0;
      var codigo_prov = (this.$("#stocks_buscar_codigo_prov").length > 0) ? this.$("#stocks_buscar_codigo_prov").val() : "";
      var id_rubro = this.$("#stocks_rubros").val();
      var id_marca = this.$("#stocks_marcas").val();
      var id_sucursal = this.$("#stocks_almacenes").val();
      var desde = this.$("#stocks_desde").val();
      var texto = this.$("#stocks_texto").val();
      var filtro_stock = this.$("#stocks_filtro_cantidades").val();
      this.collection.server_api = {
        "filter":texto,
        "id_proveedor":id_proveedor,
        "id_rubro":id_rubro,
        "id_marca":id_marca,
        "filtro_stock":filtro_stock,
        "id_sucursal":id_sucursal,
        "desde":desde,
        "codigo_prov":codigo_prov,
      }
      this.collection.pager();
    },    
			
    addAll : function () {
      this.$("#stocks_tabla .tbody").empty();
      this.collection.each(this.addOne);
      this.$("#stocks_cantidad_total").html(Number(this.collection.meta("total_unidades")).format());
      var costo = Number(this.collection.meta("total_costo")).format();
      var venta = Number(this.collection.meta("total_precio")).format()
      var cotizacion = (typeof COTIZACION_DOLAR != "undefined") ? COTIZACION_DOLAR : 0;
      this.$("#stocks_costo_total").html("$ "+costo);
      this.$("#stocks_venta_total").html("$ "+venta);

      // Mostramos en dolares
      if (cotizacion > 0) {
        var costo_dolares = Number(costo / cotizacion).toFixed(2);
        var venta_dolares = Number(venta / cotizacion).toFixed(2);
        this.$("#stocks_costo_total_dolares").html("USD "+costo_dolares);
        this.$("#stocks_venta_total_dolares").html("USD "+venta_dolares);
      }

      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var view = new app.views.StocksItemResultados({
        model: item,
      });
      this.$("#stocks_tabla .tbody").append(view.render().el);
      // Si el item tiene variantes
      var variantes = ((typeof item.get("variantes") != "undefined") ? item.get("variantes") : new Array());
      if (variantes.length > 0) {
        for(var i=0;i<variantes.length;i++) {
          var variante = variantes[i];
          variante.id_articulo = item.id;
          var v = new app.views.StocksVarianteItemResultados({
            "model":new app.models.AbstractModel(variante),
          });
          this.$("#stocks_tabla .tbody").append(v.render().el);
        }
      }
    },
    
    nuevo: function() {
      var self = this;
      var id_sucursal = this.$("#stocks_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione un almacen para realizar movimientos.");
        this.$("#stocks_almacenes").focus();
        return;
      }
      var titulo = this.$("#stocks_almacenes option:selected").text();
      app.views.stockEditView = new app.views.StockEditView({
        titulo: titulo,
        id_sucursal: id_sucursal,
        model: new app.models.Stock({
          items: [],
          id_sucursal: id_sucursal
        }),
      });
      crearLightboxHTML({
        "html":app.views.stockEditView.el,
        "width":800,
        "height":400,
        "escapable":false,
        "callback":function() {
          app.views.stocksResultados.buscar();
        }
      });
    },
    
    generar_pedido: function() {
      var self = this;
      var id_proveedor = this.$("#stocks_proveedores").val();
      var id_rubro = this.$("#stocks_rubros").val();
      var id_sucursal = this.$("#stocks_almacenes").val();
      var texto = this.$("#stocks_texto").val();
      $.ajax({
        "url":"stock/function/pedido/",
        "data":{
          "id_proveedor":id_proveedor,
          "id_rubro":id_rubro,
          "id_sucursal":id_sucursal,
          "filter":texto
        },
        "type":"get",
        "dataType":"json",
        "success":function(res){
          if (res.mensajes.length == 0) {
            show("El pedido se ha generado correctamente con el numero: "+res.numero);
          } else {
            var s = "";
            for (var i = 0; i < res.mensajes.length; i++) {
              var m = res.mensajes[i];
              s += m+"\r\n";
            }
            show(s);
          }
        }
      });
    },   
      
    armar_form: function(url) {
      
      var f = document.createElement("form");
      f.setAttribute('method',"post");
      f.setAttribute('action',url);
      f.setAttribute('target',"reporte");
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_proveedor");
      i.setAttribute('value',$("#stocks_proveedores").val());
      f.appendChild(i);
            
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_rubro");
      i.setAttribute('value',$("#stocks_rubros").val());
      f.appendChild(i);
      
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"id_sucursal");
      i.setAttribute('value',$("#stocks_almacenes").val());
      f.appendChild(i);
      
      var texto = $("#stocks_texto").val();
      var i = document.createElement("input");
      i.setAttribute('type',"hidden");
      i.setAttribute('name',"texto");
      i.setAttribute('value',texto);
      f.appendChild(i);

      $(f).css("display","none");
      document.body.appendChild(f);
          
      f.submit();	  
    },
    
    imprimir: function(tipo) {
      var url = "stock/function/imprimir/?p="+tipo;
      if (this.$("#stocks_proveedores").length > 0) {
        var id_proveedor = $("#stocks_proveedores").val();
        if (id_proveedor != 0) url+="&id_proveedor="+id_proveedor;
      }
      if (this.$("#stocks_buscar_codigo_prov").length > 0) {
        var codigo_prov = $("#stocks_buscar_codigo_prov").val();
        if (codigo_prov != 0) url+="&codigo_prov="+codigo_prov;
      }
      if (this.$("#stocks_almacenes").length > 0) {
        var id_sucursal = $("#stocks_almacenes").val();
        if (id_sucursal != 0) url+="&id_sucursal="+id_sucursal;
      }
      if (this.$("#stocks_rubros").length > 0) {
        var id_rubro = $("#stocks_rubros").val();
        if (id_rubro != 0) url+="&id_rubro="+id_rubro;
      }
      if (this.$("#stocks_marcas").length > 0) {
        var id_marca = $("#stocks_marcas").val();
        if (id_marca != 0) url+="&id_marca="+id_marca;
      }
      if (this.$("#stocks_texto").length > 0) {
        var texto = $("#stocks_texto").val();
        if (texto != 0) url+="&texto="+texto;
      }
      if (this.$("#stocks_desde").length > 0) {
        var desde = $("#stocks_desde").val().replace(/\//g,"-");
        if (!isEmpty(desde)) url+="&desde="+desde;
      }
      if (this.$("#stocks_filtro_cantidades").length > 0) {
        url+="&filtro_stock=" + $("#stocks_filtro_cantidades").val();
      }
      workspace.imprimir_reporte(url);
    },

    exportar: function() {
      var url = "stock/function/exportar_excel/?i=1";
      if (this.$("#stocks_proveedores").length > 0) {
        var id_proveedor = $("#stocks_proveedores").val();
        if (id_proveedor != 0) url+="&id_proveedor="+id_proveedor;
      }
      if (this.$("#stocks_buscar_codigo_prov").length > 0) {
        var codigo_prov = $("#stocks_buscar_codigo_prov").val();
        if (codigo_prov != 0) url+="&codigo_prov="+codigo_prov;
      }
      if (this.$("#stocks_almacenes").length > 0) {
        var id_sucursal = $("#stocks_almacenes").val();
        if (id_sucursal != 0) url+="&id_sucursal="+id_sucursal;
      }
      if (this.$("#stocks_rubros").length > 0) {
        var id_rubro = $("#stocks_rubros").val();
        if (id_rubro != 0) url+="&id_rubro="+id_rubro;
      }
      if (this.$("#stocks_marcas").length > 0) {
        var id_marca = $("#stocks_marcas").val();
        if (id_marca != 0) url+="&id_marca="+id_marca;
      }
      if (this.$("#stocks_filtro_cantidades").length > 0) {
        var filtro_stock = this.$("#stocks_filtro_cantidades").val();
        if (filtro_stock != 0) url+="&filtro_stock="+filtro_stock;
      }
      if (this.$("#stocks_texto").length > 0) {
        var texto = $("#stocks_texto").val();
        if (texto != 0) url+="&texto="+texto;
      }
      if (this.$("#stocks_desde").length > 0) {
        var desde = $("#stocks_desde").val().replace(/\//g,"-");
        if (!isEmpty(desde)) url+="&desde="+desde;
      }
      if (this.$("#stocks_filtro_cantidades").length > 0) {
        url+="&filtro_stock=" + $("#stocks_filtro_cantidades").val();
      }
      window.open(url,"_blank");
    },

  });
})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.StocksItemResultados = Backbone.View.extend({
    
    template: _.template($("#stocks_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click .ver_variantes":function() {
        $(".variante_articulo_"+this.model.id).toggleClass("dn");
      },
      "click .edit":"editar",
      "click .view":"detalle",
      "click .editar_stock_minimo":"modificar_stock",
      "click .checkbox":"seleccionar",
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
    },
    detalle : function() {
      var self = this;
      var id_sucursal = $("#stocks_almacenes").val();
      app.views.stockDetalleView = new app.views.StockDetalleView({
        model: new app.models.AbstractModel({
          "id_articulo": self.model.get("id_articulo"),
          "id_sucursal": id_sucursal,
          "nombre": self.model.get("nombre"),
          "codigo": self.model.get("codigo"),
        }),
      });
      crearLightboxHTML({
        "html":app.views.stockDetalleView.el,
        "width":670,
        "height":370,
      });
    },
    editar : function() {
      var self = this;
      var articulo = new app.models.Articulo({
        "id":self.model.get("id_articulo")
      });
      articulo.fetch({
        "success":function() {
          app.views.articuloEditView = new app.views.ArticuloEditView({
            model: articulo
          });
          crearLightboxHTML({
            "html":app.views.articuloEditView.el,
            "width":780,
            "height":500,
          });
        }
      });
    },
    modificar_stock: function() {
      var self = this;
        app.views.modificarStockView = new app.views.ModificarStockView({
        model: self.model
      });
      crearLightboxHTML({
        "html":app.views.modificarStockView.el,
        "width":350,
        "height":150,
      });
      $("#stock_modificar_minimo").select();
    },
    /*
    editar_stock_minimo: function() {
      var s = prompt("Stock minimo: ");
      if (!s) return;
      s = parseFloat(s);
      if (isNaN(s)) {
        alert("Por favor ingrese un numero");
        return;
      }
      var self = this;
      $.ajax({
        "url":"stock/function/editar_stock_minimo/",
        "type":"post",
        "data":{
          "id_sucursal":$("#stocks_almacenes").val(),
          "id_articulo":self.model.id,
          "stock_minimo":s,
        },
        "dataType":"json",
        "success":function(){
          app.views.stocksResultados.buscar();
        }
      });
    },
    */
    initialize: function() {
      var self = this;
      _.bindAll(this);
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.StocksVarianteItemResultados = Backbone.View.extend({
    template: _.template($("#stocks_item_variante_resultados_template").html()),
    tagName: "tr",
    className: function() {
      return "dn variante_articulo variante_articulo_"+this.model.get("id_articulo");
    },
    initialize: function() {
      var self = this;
      _.bindAll(this);
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);


// -----------------------------------------
//   MODIFICACION DE STOCK
// -----------------------------------------
(function ( app ) {

  app.views.StockEditView = app.mixins.View.extend({

	template: _.template($("#stock_template").html()),
		
	myEvents: {
    "click .cerrar":function() {
      if (this.$("#stocks_stock_tabla tbody tr").length > 0) {
        if (confirm("Hay movimientos cargados, desea cerrar sin guardar?")) {
          $('.modal:last').modal('hide');
        }
      } else {
        $('.modal:last').modal('hide');
      }
    },
    "change #stock_medida":function() {
      var medida = this.$("#stock_medida").val();
      if (medida == "U") {
        this.$("#stock_bultos_cont").hide();
        this.$("#stock_unidades_cont").show();
      } else if (medida == "B") {
        this.$("#stock_unidades_cont").hide();
        this.$("#stock_bultos_cont").show();
      }
    },
	  "change input[type=radio]": "onchange_radio",
	  "focusout .numerico": "es_numero",
	  "click .guardar": "guardar",
	  "click #stock_agregar":"agregar_articulo",
	  "click #stock_buscar":"ver_buscar_articulo",
	  "click #stocks_stock_tabla .delete":"eliminar_articulo",
	  "click #stocks_stock_tabla .edit":"editar_articulo",

    "change #stock_variantes":"change_variante",
	  
	  // BUSQUEDA DE ARTICULOS
	  "keypress #stock_codigo_articulo": function(e) {
		if (e.keyCode == 13) { this.buscar_articulo(); }
	  },
	  "keypress #stock_bultos": function(e) {
		if (e.keyCode == 13) { this.calcular_unidades(); $("#stock_uxb").select(); $("#stock_uxb").focus(); }
	  },	  
	  "keypress #stock_uxb": function(e) {
		if (e.keyCode == 13) { this.calcular_unidades(); $("#stock_cantidad").select(); $("#stock_cantidad").focus(); }
	  },	  
	  "keypress #stock_cantidad": function(e) {
		if (e.keyCode == 13) { this.agregar_articulo(); }
	  },	  
    "keydown #stock_variantes": function(e) {
      if (e.keyCode == 13) { e.preventDefault(); this.$("#stock_cantidad").select(); }
    },
	},
	
	calcular_unidades : function() {
	  var bultos = parseFloat($("#stock_bultos").val());
	  var uxb = parseFloat($("#stock_uxb").val());
	  var unidades = bultos * uxb;
	  $("#stock_cantidad").val(Number(unidades).toFixed(2));
	},
	
	onchange_radio: function (e) {
	  var id = $(e.currentTarget).attr("name");
	  var value = $(e.currentTarget).val();
	  var objInst = new Object();
	  objInst[id] = value;
	  this.model.set(objInst);
	},	
	
	buscar_articulo : function() {
	  var self = this;	  
	  var codigo = $("#stock_codigo_articulo").val();
	  if (isEmpty(codigo)) return;
    codigo = encodeURIComponent(codigo);
	  $.ajax({
      "url":"stock/function/consulta/"+codigo+"/"+self.id_sucursal,
      "dataType":"json",
      "success":function(res) {
        if (res.error == 0) {
          var articulo = _.filter(self.model.get("items"),function(item){
            return (item.codigo == codigo && item.id_variante == 0);
          });
          if (articulo.length == 0) {
            $("#stock_nombre_articulo").val(res.articulo.nombre);
            $("#stock_stock").val(res.articulo.stock);
            $("#stock_bultos").val(1);
            $("#stock_uxb").val(res.articulo.uxb);
            self.calcular_unidades();
            self.articulo = res.articulo;
          } else {
            self.articulo = articulo[0];
            self.mostrar_articulo();
          }	
          self.render_variantes();

          // Dependiendo si estamos ingresando por bultos o por unidades
          if (ID_PROYECTO != 2) {
            if (self.$("#stock_medida").val() == "U") {
              self.$("#stock_cantidad").select();
            } else {
              self.$("#stock_bultos").select();
            }            
          } else {
            if (self.articulo.variantes.length > 0) self.$("#stock_variantes").focus();
            else self.$("#stock_cantidad").select();
          }
          
        } else {
          show(res.mensaje);
          self.articulo = null;
        }
      }
	  });
	},

  buscar_articulo_por_id : function() {
    var self = this;    
    var id = this.$("#stock_id_articulo").val();
    $.ajax({
      "url":"stock/function/consulta_por_id/"+id+"/"+self.id_sucursal,
      "dataType":"json",
      "success":function(res) {
        if (res.error == 0) {
          var articulo = _.filter(self.model.get("items"),function(item){
            return (item.id == id && item.id_variante == 0);
          });
          if (articulo.length == 0) {
            $("#stock_nombre_articulo").val(res.articulo.nombre);
            $("#stock_stock").val(res.articulo.stock);
            $("#stock_bultos").val(1);
            $("#stock_uxb").val(res.articulo.uxb);
            self.calcular_unidades();
            self.articulo = res.articulo;
          } else {
            self.articulo = articulo[0];
            self.mostrar_articulo();
          } 
          self.render_variantes();

          // Dependiendo si estamos ingresando por bultos o por unidades
          if (ID_PROYECTO != 2) {
            if (self.$("#stock_medida").val() == "U") {
              self.$("#stock_cantidad").select();
            } else {
              self.$("#stock_bultos").select();
            }            
          } else {
            if (self.articulo.variantes.length > 0) self.$("#stock_variantes").focus();
            else self.$("#stock_cantidad").select();
          }
          
        } else {
          show(res.mensaje);
          self.articulo = null;
        }
      }
    });
  },

  change_variante: function() {
    if (this.$("#stock_variantes option").length == 0) return;
    var stock = this.$("#stock_variantes option:selected").data("stock");
    this.$("#stock_stock").val(stock);
  },

  render_variantes: function() {
    this.limpiar_variantes();
    if (this.articulo == null || (typeof this.articulo == undefined)) return;
    if (typeof this.articulo.variantes == undefined) return;
    if (this.articulo.variantes.length > 0) {
      this.$("#stock_variantes").removeAttr("disabled");
      for(var i=0; i<this.articulo.variantes.length; i++) {
        var v = this.articulo.variantes[i];
        var option = "<option data-stock='"+v.stock+"' "+((v.id == this.articulo.id_variante)?"selected":"")+" value='"+v.id+"'>"+v.nombre+"</option>";
        this.$("#stock_variantes").append(option);
      }
      this.change_variante();
    }
  },

  ver_buscar_articulo : function() {
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
      "width":860,
      "height":500,
      "callback":function() {
        if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
          $("#stock_codigo_articulo").val($("#stock_codigo_articulo").val()+window.codigo_articulo_seleccionado);
          self.buscar_articulo();
        } else {
          $("#stock_codigo_articulo").focus();
        }                    
      }
    });
    $("#articulos_buscar").focus();
  },	
	
	es_numero:function(e) {
	  var valor = $(e.currentTarget).val();
	  if (isEmpty(valor)) {
		valor = "0";
		$(e.currentTarget).val("0");
	  }
	  if (!(isInteger(valor) || isDecimal(valor))) {
		show("Por favor ingrese un numero.");
		$(e.currentTarget).focus();
	  }
	},
	
  initialize: function(options) {
    var self = this;
    _.bindAll(this);
    this.articulo = null;
    console.log(options);
    this.id_sucursal = options.id_sucursal;
    this.codigo_default = ((typeof options.codigo_default != undefined) ? options.codigo_default : "");
    var obj = { 
      "titulo":options.titulo,
    };
    _.extend(obj,this.model.toJSON());
    $(this.el).html(this.template(obj));
    this.render();
  },
	
	render : function() {
	  
	  var self = this;
	  
	  var fecha = new Date();
	  this.$("#stock_fecha").datepicker({
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
      "defaultDate":fecha
	  });
	  this.$("#stock_fecha").val($.datepicker.formatDate("dd/mm/yy",new Date()));

    if (CACHE_ARTICULOS == 0) {
      var input = this.$("#stock_codigo_articulo");
      $(input).customcomplete({
        "url":"articulos/function/get_by_descripcion/",
        "form":null, // No quiero que se creen nuevos productos
        "width":400,
        "onSelect":function(item){
          self.$("#stock_id_articulo").val(item.id);
          self.buscar_articulo_por_id();
        }
      });
    }
	  
    // Creamos el select
    new app.mixins.Select({
      modelClass: app.models.Proveedor,
      url: "proveedores/",
      firstOptions: ["<option value='0'>Proveedor</option>"],
      render: "#stock_proveedores",
    });

    if (!isEmpty(this.codigo_default)) {
      setTimeout(function(){
        self.$("#stock_codigo_articulo").val(self.codigo_default);
        self.$("#stock_codigo_articulo").select();
      },500);       
    }

	},
	
	agregar_articulo : function() {
	  
	  var self = this;
	  if (this.articulo == null) {
      show("Por favor ingrese un articulo.");
      return;
	  }
	  
	  var cantidad = $("#stock_cantidad").val();
	  this.articulo.cantidad = cantidad;
	  
    var id_variante = 0;
	  var uxb = $("#stock_uxb").val();
	  var bultos = $("#stock_bultos").val();
	  this.articulo.uxb = uxb;
	  this.articulo.bultos = bultos;

    var stock_actual = parseFloat(self.$("#stock_stock").val());
    if (isNaN(stock_actual)) stock_actual = 0;

    if (this.$("#stock_variantes option").length > 0) {
      this.articulo.id_variante = this.$("#stock_variantes").val();
      this.articulo.variante = this.$("#stock_variantes option:selected").text();
    } else {
      this.articulo.id_variante = 0;
      this.articulo.variante = "";
    }

	  var movimiento = $("#stock_movimiento").val();
	  this.articulo.movimiento = movimiento;

	  // Controlamos que el articulo no exista ya en la lista de items
	  var art = _.filter(this.model.get("items"),function(item){
      return (item.id == self.articulo.id && item.id_variante == self.articulo.id_variante);
	  });
	  if (art.length == 0) {
      // El articulo no se encuentra en la lista,
      // debemos agregarlo
      this.model.get("items").push(this.articulo);
      
      // Actualizamos la vista
      var a = this.articulo;
      var tr_id = "articulo_"+a.id+"_"+a.id_variante;
      var tr = "<tr id='"+tr_id+"'>";
      tr+= "<td>"+a.codigo+"</td>";
      tr+= "<td>"+a.nombre+"</td>";
      tr+= "<td class='"+((ID_PROYECTO==2)?"":"dn")+"'>"+a.variante+"</td>";
      
      if (movimiento == "B") tr+= "<td>Baja</td>";
      if (movimiento == "A") tr+= "<td>Alta</td>";
      if (movimiento == "I") tr+= "<td>Inicial</td>";
      if (movimiento == "R") tr+= "<td>Rotura</td>";
      if (movimiento == "M") tr+= "<td>Ajuste</td>";
      
      tr+= "<td>"+Number(stock_actual).format()+"</td>";
      tr+= "<td>"+Number(cantidad).format()+"</td>";
      
      if (movimiento == "B" || movimiento == "R") {
        tr+= "<td>"+Number(parseFloat(stock_actual) - parseFloat(cantidad)).format()+"</td>";
      } else if (movimiento == "A") {
        tr+= "<td>"+Number(parseFloat(stock_actual) + parseFloat(cantidad)).format()+"</td>";
      } else {
        tr+= "<td>"+Number(cantidad).format()+"</td>";
      }
      
      tr+= "<td><i title='Editar' class='fa fa-file-text-o edit text-dark'></i></td>";
      tr+= "<td><i title='Eliminar' class='glyphicon glyphicon-remove delete text-danger'></i></td>";
      tr+= "</tr>";
      this.$("#stocks_stock_tabla tbody").append(tr);
      
      // Movemos el contenedor hasta el final
      this.$("#stocks_stock_tabla").scrollTo('+=30px');
  		
	  } else {
      // El articulo ya se encuentra en la lista
      var a = art[0];
      a.cantidad = self.articulo.cantidad;

      var id_tr = "#articulo_"+self.articulo.id+"_"+self.articulo.id_variante;
      // Actualizamos la vista
      $(id_tr).find("td:eq(5)").html(Number(a.cantidad).format());
      if (movimiento == "B" || movimiento == "R") {
        stock = parseFloat(stock_actual) - parseFloat(cantidad);
      } else if (movimiento == "A") {
        stock = parseFloat(stock_actual) + parseFloat(cantidad);
      } else {
        stock = parseFloat(cantidad);
      }
      $(id_tr).find("td:eq(2)").html(self.articulo.variante);
      
      if (movimiento == "B") $(id_tr).find("td:eq(3)").html("Baja");
      if (movimiento == "R") $(id_tr).find("td:eq(3)").html("Rotura");
      if (movimiento == "A") $(id_tr).find("td:eq(3)").html("Alta");
      if (movimiento == "I") $(id_tr).find("td:eq(3)").html("Inicial");
      if (movimiento == "M") $(id_tr).find("td:eq(3)").html("Ajuste");
      
      $(id_tr).find("td:eq(5)").html(Number(cantidad).format());
      $(id_tr).find("td:eq(6)").html(Number(stock).format());
	  }
	  
	  this.limpiar_articulo();
	},
	
	eliminar_articulo : function(e) {
	  var id = $(e.currentTarget).parent().parent().attr("id");
	  id = id.replace("articulo_","");
	  var ids = id.split("_");
	  var id = ids[0];
	  
	  // Lo eliminamos de la lista
	  $(e.currentTarget).parent().parent().remove();
	  
	  // Lo eliminamos del array
	  var items = this.model.get("items");
	  var items2 = _.filter(items,function(item){
		return !(item.id == id);
	  });
	  this.model.set({ "items":items2 });
	},
	
	editar_articulo : function(e) {
	  var id = $(e.currentTarget).parent().parent().attr("id");
	  id = id.replace("articulo_","");
	  var ids = id.split("_");
	  var id = ids[0];
    var id_variante = ids[1];
	  
	  // Buscamos el articulo
	  var articulo = _.find(this.model.get("items"),function(item){
      return (item.id == id && item.id_variante == id_variante);
	  });
	  this.articulo = articulo;
	  this.mostrar_articulo();
	},
	
  limpiar_variantes: function() {
    this.$("#stock_variantes").empty();
    this.$("#stock_variantes").attr("disabled","disabled");
  },

	limpiar_articulo : function() {
	  this.articulo = null;
	  $("#stock_codigo_articulo").val("");
	  $("#stock_nombre_articulo").val("");
	  $("#stock_stock").val("");
	  $("#stock_uxb").val("");
	  $("#stock_bultos").val("");
	  $("#stock_cantidad").val("1");
	  $("#stock_codigo_articulo").focus();
    this.limpiar_variantes();
	},
	
	mostrar_articulo : function() {
	  this.$("#stock_codigo_articulo").val(this.articulo.codigo);
	  this.$("#stock_nombre_articulo").val(this.articulo.nombre);
	  this.$("#stock_bultos").val(this.articulo.bultos);
	  this.$("#stock_uxb").val(this.articulo.uxb);
	  this.$("#stock_cantidad").val(this.articulo.cantidad);
    this.$("#stock_stock").val(this.articulo.stock);
    this.render_variantes();
	  this.$("#stock_cantidad").select();	  
	},
		
  validar: function() {
    try {
      var self = this;
      
      validate_input("stock_fecha",IS_EMPTY,"Por favor ingrese una fecha.");
      var fecha = $("#stock_fecha").val();
      var id_proveedor = $("#stock_proveedores").val();
          
      if (this.model.get("items").length == 0) {
        show("Por favor ingrese algun articulo en el stock.");
        return false;
      }
      
      this.model.set({
        "fecha":fecha,
        "id_proveedor":id_proveedor,
      });
  
      $(".error").removeClass("error");
      return true;
    } catch(e) {
      return false;
    }
  },	
	
	guardar:function() {
	  var self = this;
    if (this.validar()) {
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      this.model.save({},{
        success: function(model,response) {
          if (response.error == 1) {
            show(response.mensaje);
            return;
          } else {
            // model.set({id:response.id});
            $('.modal:last').modal('hide');
          }
        }
      });
    }
	},
	
  });
})(app);



// -----------------------------------------
//   MODIFICAR STOCK MINIMO
// -----------------------------------------
(function ( app ) {

  app.views.ModificarStockView = Backbone.View.extend({
	
	events: {
	  "click .guardar":"guardar",
    "keypress #stock_modificar_minimo":function(e) {
      if (e.which == 13) this.guardar();
    }
	},
	
	template: _.template($("#stock_modificar_template").html()),
	
  initialize: function() {
    var self = this;
    _.bindAll(this);
    $(this.el).html(this.template(self.model.toJSON()));
  },

	guardar:function() {
	  var self = this;
	  var stock_minimo = this.$("#stock_modificar_minimo").val();
	  if (isEmpty(stock_minimo)) {
      show("Por favor ingrese un numero.");
      return;
	  }
	  $.ajax({
      "url":"stock/function/modificar_stock_minimo/",
      "dataType":"json",
      "data":{
        "id_articulo":self.model.get("id_articulo"),
        "stock_minimo":stock_minimo,
        "id_sucursal":$("#stocks_almacenes").val(),
      },
      "type":"post",
      "success":function() {
        $('.modal:last').modal('hide');
        app.views.stocksResultados.buscar();
      }
	  });	  
	},
	
  });
})(app);



// -----------------------------------------
//   DETALLE DEL STOCK
// -----------------------------------------
(function ( app ) {

  app.views.StockDetalleView = app.mixins.View.extend({

    template: _.template($("#stock_detalle_template").html()),
    
    events: {
      "click .cerrar": "cerrar",
      "click .render_tabla":"render_tabla",
      "click .render_grafico":"render_grafico",
      "click .buscar":"buscar",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.articulo = null;
      this.options = options;
      this.tab_default = (typeof options.tab_default != "undefined") ? options.tab_default : "tabla";
      var obj = this.model.toJSON();
      obj.tab_default = this.tab_default;
      $(this.el).html(this.template(obj));
      this.render();
    },

    render : function() {
      var self = this;
      var desde = (typeof this.model.get("desde") == "undefined") ? moment().startOf('month').toDate() : this.model.get("desde");
      var hasta = (typeof this.model.get("hasta") == "undefined") ? moment().toDate() : this.model.get("hasta");
      createdatepicker($(this.el).find("#evolucion_stock_fecha_desde"),desde);
      createdatepicker($(this.el).find("#evolucion_stock_fecha_hasta"),hasta);
      if (this.tab_default == "grafico") this.render_grafico();
      else this.render_tabla();
    },
    
    cerrar: function() {
      $('.modal:last').modal('hide');
    },    

    buscar: function() {
      if (this.$("#tab_detalle_stock1").hasClass("active")) this.render_tabla();
      else this.render_grafico();
    }, 
  
    render_tabla : function() {
      var self = this;
      this.$("#stock_detalle_tabla tbody").empty();
      $.ajax({
        "url":"stock/function/detalle/",
        "data":{
          "id_articulo":self.model.get("id_articulo"),
          "id_sucursal":self.model.get("id_sucursal"),
          "desde":self.$("#evolucion_stock_fecha_desde").val(),
          "hasta":self.$("#evolucion_stock_fecha_hasta").val(),
        },
        "type":"post",
        "dataType":"json",
        "success":function(r){
          var items = r.results;
          for(var i=0;i<items.length;i++ ) {
            var item = items[i];
            var tr = "<tr>";
            tr+="<td>"+item.fecha+"</td>";
            tr+="<td>"+item.movimiento+"</td>";
            tr+="<td>"+item.cantidad+"</td>";
            tr+="<td>"+item.saldo+"</td>";
            tr+="<td>"+((!isEmpty(item.proveedor)) ? item.proveedor : item.detalle)+"</td>";
            tr+="</tr>";
            self.$("#stock_detalle_tabla tbody").append(tr);
          }
        },
      });
    },

    render_grafico: function() {
      var self = this;
      var params = {
        "id_sucursal":self.model.get("id_sucursal"),
        "id_articulo":self.model.get("id_articulo"),
        "desde":self.$("#evolucion_stock_fecha_desde").val(),
        "hasta":self.$("#evolucion_stock_fecha_hasta").val(),
      };
      $.ajax({
        "url":"estadisticas/function/evolucion_stock/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){

          var desde = self.$("#evolucion_stock_fecha_desde").val();
          var desde_anio = desde.substr(6);
          var desde_mes = desde.substr(3,2)-1;
          var desde_dia = desde.substr(0,2);
          var plotOptionsSeries = {
            pointStart: Date.UTC(desde_anio,desde_mes,desde_dia),
            pointInterval: 24 * 3600 * 1000,
          };

          self.$('#evolucion_stock_grafico').highcharts({
            title: { text: null },
            xAxis: {
              type: 'datetime'
            },
            chart: {
              type: 'area',
            },
            legend: {
              floating: true,
              align: "right",
              verticalAlign: "top",
            },
            colors: ['#28b492','#19a9d5'],
            yAxis: {
              allowDecimals: false,
              gridLineColor: '#f9f9f9',
              title: {
                text: 'Stock'
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
            series: [{
              type: "area",
              name: "Evolucion de Stock",
              data: r.results,
            }]
          });

        },
      });
    },
  });
})(app);