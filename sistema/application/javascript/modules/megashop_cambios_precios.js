
(function ( models ) {

  models.IngresoProveedorItem = Backbone.Model.extend({
    urlRoot: "ingresos_proveedores_items/",
    defaults: {
      id_articulo: 0,
      cantidad: 0,
      porc_iva: 0,
      id_tipo_alicuota_iva: 0,
      costo_neto: 0,    // Unitario
      costo_final: 0,  // Unitario
      precio_neto: 0,
      precio_final: 0,
      nombre: "",
      orden: 0,
      codigo: "",
      codigo_proveedor: "",
      codigo_barra: "",
      total_neto: 0, // Totales (unitario * cantidad)
      total_final: 0,
      tipo_etiqueta: 0,
      dto_prov: 0,
      dto_prov_2: 0,
      dto_prov_3: 0,
      dto_prov_4: 0,
      dto_prov_5: 0,
      bonificado: 0,
      no_editar_precios: 0,
      no_editar_stock: 0,
    }
  });
      
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.MegashopCambiosPreciosView = app.mixins.View.extend({

    template: _.template($("#megashop_cambios_precios_edit_panel_template").html()),

    myEvents: {
      "click .aceptar": function() {
        this.model.set({
          "estado":0,
        });
        this.aceptar();
      },
      "click .confirmar": function() {
        if (!confirm("Desea confirmar el ingreso?")) return;
        this.model.set({
          "estado":1,
        })
        this.aceptar();
      },
      "click #megashop_cambios_precios_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #megashop_cambios_precios_agregar_item": "agregar_item",

      "click .imprimir": function(){
        this.imprimir(this.model.id);
      },

      "focusin #megashop_cambios_precios_codigo_articulo":function() {
        $("#tabla_items tbody tr.seleccionado").removeClass('seleccionado');
        $("#tabla_items tbody tr .radio").prop("checked",false);
      },

      "click #megashop_cambios_precios_buscar_proveedor": "ver_buscar_proveedor",

      "keypress #megashop_cambios_precios_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },

      "keypress #megashop_cambios_precios_codigo_proveedor":function(e) {
        if (e.which == 13) { this.buscar_proveedor(); $("#megashop_cambios_precios_codigo_articulo").select(); }
      },
      "focusout #megashop_cambios_precios_codigo_proveedor":function(e){
        if (typeof this.proveedor != "undefined") {
          var nombre = this.proveedor.get("nombre");
          var texto = $(e.currentTarget).val();
          if (nombre != texto) {
            // Blanqueamos el proveedor para que no haya confusion
            $(e.currentTarget).val("");
          }
        }
      },

      "keypress #megashop_cambios_precios_item_costo_neto_inicial":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;

          if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
            var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
            if (isNaN(dto_prov_2)) dto_prov_2 = 0;
            var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
            if (isNaN(dto_prov_3)) dto_prov_3 = 0;
            var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
            if (isNaN(dto_prov_4)) dto_prov_4 = 0;
            var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
            if (isNaN(dto_prov_5)) dto_prov_5 = 0;
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          } else {
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100);
          }
          this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#megashop_cambios_precios_item_dto_prov").select();
        }
      },

      "keypress #megashop_cambios_precios_item_dto_prov":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
            var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
            if (isNaN(dto_prov_2)) dto_prov_2 = 0;
            var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
            if (isNaN(dto_prov_3)) dto_prov_3 = 0;
            var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
            if (isNaN(dto_prov_4)) dto_prov_4 = 0;
            var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
            if (isNaN(dto_prov_5)) dto_prov_5 = 0;
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
            this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
            this.calcular_precios();
            this.$("#megashop_cambios_precios_item_dto_prov_2").select();
          } else {
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100);
            this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
            this.calcular_precios();
            this.$("#megashop_cambios_precios_alicuotas_iva").focus();            
          }
        }
      },

      "keypress #megashop_cambios_precios_item_dto_prov_2":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#megashop_cambios_precios_item_dto_prov_3").select();
        }
      },

      "keypress #megashop_cambios_precios_item_dto_prov_3":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#megashop_cambios_precios_item_dto_prov_4").select();
        }
      },

      "keypress #megashop_cambios_precios_item_dto_prov_4":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#megashop_cambios_precios_item_dto_prov_5").select();
        }
      },

      "keypress #megashop_cambios_precios_item_dto_prov_5":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#megashop_cambios_precios_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#megashop_cambios_precios_alicuotas_iva").focus();
        }
      },

      "keydown #megashop_cambios_precios_alicuotas_iva":function(e) {
        if (e.which == 13) {
          e.preventDefault(); e.stopPropagation();
          this.$(".porc_ganancia:first").select();
        }
      },

      "keydown .porc_ganancia":function(e) {
        if (e.which == 13) {
          $(e.currentTarget).parents(".sucursal").find(".precio_final").select();
          this.calcular_precios();
        }
      },      

      "keydown .precio_final":function(e) {
        if (e.which == 13) {
          this.calcular_precios();
          if ($(e.currentTarget).parents(".sucursal").next(".sucursal").length > 0) {
            $(e.currentTarget).parents(".sucursal").next(".sucursal").find(".porc_ganancia").select();
          } else {
            this.agregar_item();
          }
        }
      },      

      "change #megashop_cambios_precios_alicuotas_iva":function() {
        if (typeof this.articulo == "undefined") return;
        var porc_iva = this.$("#megashop_cambios_precios_alicuotas_iva option:selected").data("porcentaje");
        var id_tipo_alicuota_iva = this.$("#megashop_cambios_precios_alicuotas_iva").val();
        this.articulo.set({ "porc_iva":porc_iva, "id_tipo_alicuota_iva":id_tipo_alicuota_iva });
        this.calcular_precios();
      },
      
      "keypress #megashop_cambios_precios_item_costo_neto":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#megashop_cambios_precios_alicuotas_iva").focus();
        }
      },

      "keypress #megashop_cambios_precios_item_porc_ganancia":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#megashop_cambios_precios_precio_final").select();
        }
      },

      // Se modifican los precios, se calculan los costos
      "keypress #megashop_cambios_precios_precio_final":function(e){
        if (e.keyCode == 13) {
          if (typeof this.articulo == "undefined") return;
          this.editar_precio_final();
          this.agregar_item();
        }
      },

    },

    imprimir: function(id) {
      workspace.imprimir_reporte("ingresos_proveedores/function/imprimir/"+id);
    },

    calcular_precios: function() {
      if (typeof this.articulo == "undefined") return;
      var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
      }
      var costo_neto = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto").val());
      var porc_iva = parseFloat(this.$("#megashop_cambios_precios_alicuotas_iva option:selected").data("porcentaje"));
      var id_tipo_alicuota_iva = this.$("#megashop_cambios_precios_alicuotas_iva").val();
      if (isNaN(costo_neto)) costo_neto = 0;
      if (isNaN(porc_iva)) porc_iva = 0;
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var costo_final = parseFloat(costo_neto) * (1+(porc_iva / 100));

      // Recorremos los porcentajes de ganancia

      var porc_ganancia = this.$("#megashop_cambios_precios_item_porc_ganancia").val();
      if (isNaN(porc_ganancia)) porc_ganancia = 0;
      var ganancia = costo_final * (porc_ganancia / 100);
      var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
      var precio_final = parseFloat(costo_final) * (1+(porc_ganancia / 100));

      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        this.articulo.set({
          "dto_prov_2":Number(dto_prov_2).toFixed(2),
          "dto_prov_3":Number(dto_prov_3).toFixed(2),
          "dto_prov_4":Number(dto_prov_4).toFixed(2),
          "dto_prov_5":Number(dto_prov_5).toFixed(2),
        });
      }
      
      this.articulo.set({
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "costo_iva":Number(costo_iva).toFixed(2),
        "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
        "dto_prov":Number(dto_prov).toFixed(2),
        "costo_neto":Number(costo_neto).toFixed(3),
        "costo_final":Number(costo_final).toFixed(2),
        "ganancia":Number(ganancia).toFixed(2),
        "porc_iva":Number(porc_iva).toFixed(2),
      });
      this.mostrar_articulo();
      this.calcular_item();
    },
    
    editar_precio_final: function() {

      var precio_final = parseFloat(this.$("#megashop_cambios_precios_precio_final").val());
      var porc_iva = this.$("#megashop_cambios_precios_alicuotas_iva option:selected").data("porcentaje");
      var id_tipo_alicuota_iva = this.$("#megashop_cambios_precios_alicuotas_iva").val();
      var costo_neto = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
      }
      var costo_final = parseFloat(this.$("#megashop_cambios_precios_item_costo_final").val());
      
      // Si el costo final es distinto de cero, entonces cambiamos el PORCENTAJE DE GANANCIA
      if (costo_final != 0) {
        var costo_iva = costo_neto * (porc_iva / 100);
        var porc_ganancia = parseFloat( ((precio_final / costo_final) - 1) * 100);
        var ganancia = costo_final * (porc_ganancia / 100);
        var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
          
      // Si el costo final es igual a cero, entonces lo ponemos igual al precio final
      } else {
          
        var porc_ganancia = 0;
        var ganancia = 0;
        costo_final = precio_final;
        var precio_neto = Number(precio_final / (1+(porc_iva / 100))).toFixed(2);
        costo_neto = precio_neto;
        costo_neto_inicial = costo_neto;
        var costo_iva = precio_neto * (porc_iva / 100);
      }

      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        this.articulo.set({
          "dto_prov_2":Number(dto_prov_2).toFixed(2),
          "dto_prov_3":Number(dto_prov_3).toFixed(2),
          "dto_prov_4":Number(dto_prov_4).toFixed(2),
          "dto_prov_5":Number(dto_prov_5).toFixed(2),
        });
      }
      
      this.articulo.set({
        "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
        "dto_prov":Number(dto_prov).toFixed(2),
        "costo_iva":Number(costo_iva).toFixed(2),
        "costo_neto":Number(costo_neto).toFixed(3),
        "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
        "costo_final":Number(costo_final).toFixed(2),
        "porc_ganancia":Number(porc_ganancia).toFixed(4),
        "ganancia":Number(ganancia).toFixed(2),
        "precio_neto":Number(precio_neto).toFixed(2),
        "precio_final":Number(precio_final).toFixed(2),
        "porc_iva":Number(porc_iva).toFixed(2),
      });
      this.mostrar_articulo();
      this.calcular_item();
    },
        
    ver_buscar_proveedor: function() {
      var self = this;
      var proveedores = new app.collections.Proveedores();
      app.views.buscarProveedores = new app.views.ProveedoresTableView({
        collection: proveedores,
        habilitar_seleccion: true,
      });
      delete window.codigo_proveedor_seleccionado;
      var d = $("<div/>").append(app.views.buscarProveedores.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_proveedor_seleccionado != undefined && window.codigo_proveedor_seleccionado != -1) {
            self.seleccionar_proveedor(window.proveedor_seleccionado);
          }
          $("#megashop_cambios_precios_codigo_articulo").select();                    
        }
      });
      $("#proveedores_buscar").focus();
    },
        
    buscar_proveedor : function() {
      var self = this;
      
      var codigo = this.$("#megashop_cambios_precios_codigo_proveedor").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#megashop_cambios_precios_codigo_proveedor").val(codigo);
      }
      // Es consumidor final, creamos el proveedor directamente
      if (codigo != 0) {
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
                self.$("#megashop_cambios_precios_codigo_proveedor").select();
                self.$("#megashop_cambios_precios_codigo_proveedor").focus();
                return;
              }
              var proveedor = new app.models.Proveedor(r);
              self.seleccionar_proveedor(proveedor);
            }
          });
        }
      }
      this.$("#megashop_cambios_precios_codigo_articulo").focus();    
    },
        
    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.model.set({
        "id_proveedor": self.proveedor.id,
      });
      self.$('#megashop_cambios_precios_codigo_proveedor').val(self.proveedor.get("nombre"));
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#megashop_cambios_precios_codigo_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },
        
    buscar_articulo : function() {
      var self = this;

      // Primero controlamos que haya seleccionado la sucursal que quiere
      var id_sucursal = this.$("#megashop_cambios_precios_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#megashop_cambios_precios_almacenes").focus();
        return;
      }

      var codigo = $("#megashop_cambios_precios_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }

      var tipo_codigo = this.$("input[name=tipo_codigo]:checked").val();
      if (tipo_codigo == "INTERNO") {
        $.ajax({
          "url":"articulos/function/get_by_codigo/"+codigo,
          "type":"post",
          "data":{
            "id_sucursal":id_sucursal,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.error == 1) {
              alert(r.mensaje);
            } else {
              var a = new app.models.Articulo(r.articulo);
              self.seleccionar_articulo(a);
            }
          }
        });
      } else if (tipo_codigo == "PROVEEDOR") {
        // Lo buscamos por codigo de proveedor
        if (typeof self.proveedor == "undefined") {
          alert("Por favor seleccione un proveedor");
          return;
        }
        $.ajax({
          "url":"articulos/function/get_by_codigo_proveedor/",
          "type":"post",
          "dataType":"json",
          "data":{
            "codigo":codigo,
            "id_proveedor":self.proveedor.id,
            "id_sucursal":id_sucursal,
          },
          "success":function(r) {
            if (r.error == 1) {
              alert(r.mensaje);
            } else {
              var a = new app.models.Articulo(r.articulo);
              self.seleccionar_articulo(a);
            }
          }
        });
      }
    },
    
    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.articulo.set({
        "dto_prov_2":0,
        "dto_prov_3":0,
        "dto_prov_4":0,
        "dto_prov_5":0,
      });
      self.mostrar_articulo();
      self.controlar_stock_sucursal();
      self.calcular_item();
      this.$("#megashop_cambios_precios_item_costo_neto_inicial").select();
    },

    controlar_stock_sucursal:function() {
      if (typeof this.articulo == "undefined" || this.articulo == null) return;
      var stock_almacenes = this.articulo.get("stock_almacenes");
      if (typeof stock_almacenes == "undefined" || stock_almacenes == null) return;
      var id_sucursal = this.$("#megashop_cambios_precios_almacenes").val();
      for(var i=0;i<stock_almacenes.length;i++) {
        var s = stock_almacenes[i];
        if (s.id_sucursal == id_sucursal) {
          if (s.stock_actual < 0) {
            // El stock esta en negativo, debemos informar
            alert("ATENCION: El stock de este producto esta en negativo.");
          }
          break;
        }
      }
    },
    
    editar_articulo: function(r) {
      var self = this;
      self.item = r;
      $("#megashop_cambios_precios_id_articulo").val(this.item.get("id_articulo"));
      $("#megashop_cambios_precios_codigo_articulo").val(this.item.get("codigo"));
      $("#megashop_cambios_precios_item_nombre").val(this.item.get("nombre"));
      $("#megashop_cambios_precios_item_costo_neto").val(Number(this.item.get("costo_neto")).toFixed(3));
      $("#megashop_cambios_precios_item_costo_neto_inicial").val(Number(this.item.get("costo_neto_inicial")).toFixed(3));
      $("#megashop_cambios_precios_item_dto_prov").val(Number(this.item.get("dto_prov")).toFixed(2));
      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        $("#megashop_cambios_precios_item_dto_prov_2").val(Number(this.item.get("dto_prov_2")).toFixed(2));
        $("#megashop_cambios_precios_item_dto_prov_3").val(Number(this.item.get("dto_prov_3")).toFixed(2));
        $("#megashop_cambios_precios_item_dto_prov_4").val(Number(this.item.get("dto_prov_4")).toFixed(2));
        $("#megashop_cambios_precios_item_dto_prov_5").val(Number(this.item.get("dto_prov_5")).toFixed(2));
      }
      $("#megashop_cambios_precios_item_costo_final").val(Number(this.item.get("costo_final")).toFixed(2));
      $("#megashop_cambios_precios_item_porc_ganancia").val(Number(this.item.get("porc_ganancia")).toFixed(4));
      $("#megashop_cambios_precios_precio_final").val(Number(this.item.get("precio_final")).toFixed(2));
      $("#megashop_cambios_precios_alicuotas_iva").val(this.item.get("id_tipo_alicuota_iva"));
      $("#megashop_cambios_precios_item_descripcion").val(this.item.get("descripcion"));
      $("#megashop_cambios_precios_item_bonificado").val(this.item.get("bonificado"));
      $("#megashop_cambios_precios_item_no_editar_precios").val(this.item.get("no_editar_precios"));
      $("#megashop_cambios_precios_item_no_editar_stock").val(this.item.get("no_editar_stock"));

      this.articulo = new app.models.AbstractModel({
        "id":this.item.get("id_articulo"),
        "nombre":this.item.get("nombre"),
        "costo_neto":this.item.get("costo_neto"),
        "costo_neto_inicial":this.item.get("costo_neto_inicial"),
        "dto_prov":this.item.get("dto_prov"),
        "dto_prov_2": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_2") : 0,
        "dto_prov_3": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_3") : 0,
        "dto_prov_4": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_4") : 0,
        "dto_prov_5": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_5") : 0,
        "costo_final":this.item.get("costo_final"),
        "porc_ganancia":this.item.get("porc_ganancia"),
        "precio_neto":this.item.get("precio_neto"),
        "precio_final":this.item.get("precio_final"),
        "id_tipo_alicuota_iva":this.item.get("id_tipo_alicuota_iva"),
        "porc_iva":this.item.get("porc_iva"),
        "codigo":this.item.get("codigo"),
        "bonificado":this.item.get("bonificado"),
        "no_editar_precios":this.item.get("no_editar_precios"),
        "no_editar_stock":this.item.get("no_editar_stock"),
      });

      self.calcular_item();
    },
    
    ver_buscar_articulo : function() {
      var self = this;
      var id_sucursal = this.$("#megashop_cambios_precios_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#megashop_cambios_precios_almacenes").focus();
        return;
      }
      window.articulos_buscar_id_proveedor = self.$("#megashop_cambios_precios_codigo_proveedor").data("id");
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: new app.collections.Articulos(),
        habilitar_seleccion: true,
        id_sucursal: id_sucursal,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            self.$("#megashop_cambios_precios_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#megashop_cambios_precios_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    mostrar_articulo : function() {
      this.$("#megashop_cambios_precios_item_nombre").val(this.articulo.get("nombre"));
      this.$("#megashop_cambios_precios_alicuotas_iva").val(this.articulo.get("id_tipo_alicuota_iva"));
      this.$("#megashop_cambios_precios_id_articulo").val(this.articulo.id);
      this.$("#megashop_cambios_precios_item_costo_neto").val(Number(this.articulo.get("costo_neto")).toFixed(3));
      this.$("#megashop_cambios_precios_item_costo_neto_inicial").val(Number(this.articulo.get("costo_neto_inicial")).toFixed(3));
      this.$("#megashop_cambios_precios_item_dto_prov").val(Number(this.articulo.get("dto_prov")).toFixed(2));
      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        this.$("#megashop_cambios_precios_item_dto_prov_2").val(Number(this.articulo.get("dto_prov_2")).toFixed(2));
        this.$("#megashop_cambios_precios_item_dto_prov_3").val(Number(this.articulo.get("dto_prov_3")).toFixed(2));
        this.$("#megashop_cambios_precios_item_dto_prov_4").val(Number(this.articulo.get("dto_prov_4")).toFixed(2));
        this.$("#megashop_cambios_precios_item_dto_prov_5").val(Number(this.articulo.get("dto_prov_5")).toFixed(2));
      }
      this.$("#megashop_cambios_precios_item_costo_final").val(Number(this.articulo.get("costo_final")).toFixed(2));
      this.$("#megashop_cambios_precios_item_porc_ganancia").val(Number(this.articulo.get("porc_ganancia")).toFixed(4));
      this.$("#megashop_cambios_precios_precio_neto").val(Number(this.articulo.get("precio_neto")).toFixed(2));
      this.$("#megashop_cambios_precios_precio_final").val(Number(this.articulo.get("precio_final")).toFixed(2));
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;

      var codigo = this.$("#megashop_cambios_precios_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#megashop_cambios_precios_codigo_articulo").focus();
        return;
      }                

      // Si ya existe el codigo ingresado, tenemos que t
      var bonificacion = 0;
      var id_articulo = this.$("#megashop_cambios_precios_id_articulo").val();
      var porc_iva = parseFloat(this.$("#megashop_cambios_precios_alicuotas_iva option:selected").data("porcentaje"));
      var costo_final = parseFloat(this.$("#megashop_cambios_precios_item_costo_final").val());
      var costo_neto = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#megashop_cambios_precios_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov").val());
      if (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#megashop_cambios_precios_item_dto_prov_5").val());
      }
      var porc_ganancia = parseFloat(this.$("#megashop_cambios_precios_item_porc_ganancia").val());
      var precio_neto = parseFloat(this.$("#megashop_cambios_precios_precio_neto").val());
      var precio_final = parseFloat(this.$("#megashop_cambios_precios_precio_final").val());
      
      var values = {
        "id_articulo":id_articulo,
        "costo_neto_inicial":costo_neto_inicial,
        "dto_prov":dto_prov,
        "dto_prov_2": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? dto_prov_2 : 0,
        "dto_prov_3": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? dto_prov_3 : 0,
        "dto_prov_4": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? dto_prov_4 : 0,
        "dto_prov_5": (this.$("#megashop_cambios_precios_item_dto_prov_2").length > 0) ? dto_prov_5 : 0,
        "costo_neto":costo_neto,
        "costo_final":costo_final,
        "codigo":codigo,
        "nombre":this.$("#megashop_cambios_precios_item_nombre").val(),
        "porc_iva":porc_iva,
        "precio_neto":precio_neto,
        "precio_final":precio_final,
        "porc_ganancia":porc_ganancia,
        "total_neto":total_neto,
        "total_final":total_final,
        "id_tipo_alicuota_iva":this.$("#megashop_cambios_precios_alicuotas_iva").val(),
        "bonificado":this.$("#megashop_cambios_precios_item_bonificado").val(),
        "no_editar_precios":this.$("#megashop_cambios_precios_item_no_editar_precios").val(),
        "no_editar_stock":this.$("#megashop_cambios_precios_item_no_editar_stock").val(),
      };            

      // Actualizamos o agregamos el item
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.IngresoProveedorItem(values);
        this.items.add(item);
      }
        
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#megashop_cambios_precios_codigo_articulo").select();              

      this.$('#tabla_items').parent().scrollTop(self.$('#tabla_items').parent()[0].scrollHeight);
    },
    
    calcular_item: function() {
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.agregando = 0;
      this.options = options;
      _.bindAll(this);
      this.bind("limpiar",this.limpiar);
      this.limpiar();
    },

    render: function() {
        
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        megashop_sucursales: [
          {"id": 7, "nombre": "Chacabuco"},
          {"id": 23, "nombre": "Rio Grande"},
          {"id": 14, "nombre": "Mercedes"},
          {"id": 19, "nombre": "Viedma"},
          {"id": 21, "nombre": "Moreno"},
          {"id": 223, "nombre": "Salta"},
        ],
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      this.limpiar_item();
        
      // AUTOCOMPLETE DE PROVEEDORES
      // ---------------------------
      var input = this.$("#megashop_cambios_precios_codigo_proveedor");
      var form = new app.views.ProveedorEditViewMini({
        "model": new app.models.Proveedor(),
        "input": input,
        "onSave": function(item) {
          self.seleccionar_proveedor(item);
          self.$("#cargar_compras_fecha").focus();
        },
      });            
      $(input).customcomplete({
        "url":"proveedores/function/get_by_nombre/",
        "width":300,
        "form":form,
        "onSelect":function(item){
          var proveedor = new app.models.Proveedor({"id":item.id});
          proveedor.fetch({
            "success":function(){
              self.seleccionar_proveedor(proveedor);
              self.$("#cargar_compras_fecha").focus();
            },
          });
        }
      });            
      var input = this.$("#megashop_cambios_precios_codigo_articulo");
      $(input).customcomplete({
        "collection":articulos,
        "hideNoResults":true,
        "width":"300px",
        "label":"[nombre] ([codigo])",
        "onSelect":function(item){
          self.seleccionar_articulo(item.element);
        }
      });
      return this;
    },

    render_view: function() {
      var self = this;
      self.$("#megashop_cambios_precios_subtotal_neto").val(Number(self.model.get("neto")).toFixed(2));
      self.$("#megashop_cambios_precios_total").val(Number(self.model.get("total")).toFixed(2));
    },
    
    calcular_totales : function() {
        
      var neto = 0; var porc_descuento = 0; var total = 0; var iva = 0;
      var descuento = 0; var subtotal_neto = 0; var subtotal_final = 0;
      var tipo_iva_proveedor = this.$("#megashop_cambios_precios_proveedor_iva").val();
      var items = this.model.get("items");
        
      var porc_descuento = 0; /*parseFloat(this.$("#megashop_cambios_precios_porc_descuento").val());
      if (isNaN(porc_descuento)) porc_descuento = 0;
      var pdesc = ((100-porc_descuento) / 100);*/
      var pdesc = 1;
        
      this.items.each(function(item){
        if (item.get("bonificado") == 0) {
          neto = neto + item.get("total_neto") * pdesc;
          total = total + item.get("total_final") * pdesc;
          subtotal_neto = subtotal_neto + parseFloat(item.get("total_neto"));
          subtotal_final = subtotal_final + parseFloat(item.get("total_final"));
          iva = iva + item.get("iva") * pdesc;
        }
      });
      
      subtotal = subtotal_final;
      var descuento = subtotal * parseFloat(porc_descuento / 100);
      if (isNaN(descuento)) descuento = 0;
        
      this.model.set({
        "iva":iva,
        "porc_descuento":porc_descuento,
        "descuento":descuento,
        "neto":neto,
        "subtotal":subtotal,
        "total":total,
      });
    },
    
    limpiar_item: function() {
      this.$("#megashop_cambios_precios_id_articulo").val("0");
      this.$("#megashop_cambios_precios_item_nombre").val("");
      this.$("#megashop_cambios_precios_item_descripcion").val("");
      this.$("#megashop_cambios_precios_item_cantidad").val("1");
      this.$("#megashop_cambios_precios_item_bonificado").val("0");
      this.$("#megashop_cambios_precios_item_no_editar_precios").val("0");
      this.$("#megashop_cambios_precios_item_no_editar_stock").val("0");
      this.$("#megashop_cambios_precios_item_costo_neto").val("0.00");
      this.$("#megashop_cambios_precios_item_costo_neto_inicial").val("0.00");
      this.$("#megashop_cambios_precios_item_dto_prov").val("0.00");
      this.$("#megashop_cambios_precios_item_dto_prov_2").val("0.00");
      this.$("#megashop_cambios_precios_item_dto_prov_3").val("0.00");
      this.$("#megashop_cambios_precios_item_dto_prov_4").val("0.00");
      this.$("#megashop_cambios_precios_item_dto_prov_5").val("0.00");
      this.$("#megashop_cambios_precios_item_costo_final").val("0.00");
      this.$("#megashop_cambios_precios_precio_final").val("0.00");
      this.$("#megashop_cambios_precios_item_porc_ganancia").val("");
      this.$("#megashop_cambios_precios_item_subtotal").val("");
      this.$("#megashop_cambios_precios_codigo_articulo").val("");
      this.$("#megashop_cambios_precios_codigo_articulo").focus();
    },

    render_tabla_items : function () {
      this.$("#tabla_items tbody").empty();
      this.items.each(this.addItem);
      this.calcular_totales();
    },
    
    addItem : function ( item ) {
      var view = new app.views.IngresoProveedorItemTabla({
        "model": item,
        "view":this,
      });
      this.$("#tabla_items tbody").append(view.el);
      this.calcular_totales();
    },
    
    validar: function() {
      if (this.items.size() == 0) {
        throw "ERROR: Ingrese al menos un item al comprobante antes de guardar.";
      }
    },
    
    limpiar: function() {
      var id_proveedor = this.model.get("id_proveedor");
      this.model = new app.models.IngresoProveedor({
        "items":[],
        "id_proveedor":id_proveedor,
      });

      this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
      
      // Creamos una nueva coleccion de items
      var ItemsCollection = Backbone.Collection.extend({
        model: app.models.IngresoProveedorItem,
      });
      this.items = new ItemsCollection();
      this.items.on('all', this.render_tabla_items, this);
      this.items.on('add', this.addItem, this);
      
      // Renderizamos y limpiamos
      this.render();
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
      this.model.save({
        "items":self.items.toJSON(),
      },{
        success: function(model,response) {
          $('.modal:last').modal('hide');
          self.guardando = 0; // Habilitamos el boton
          if (response.id != undefined) {
            self.model.id = response.id;
          }
          if (response.error == 1) {
            show(response.mensaje);
            self.guardando = 0;
          } else {
            location.href = "app/#ingresos_proveedores";
          }
        },
      });
    },

  });

})(app.views, app.models);



(function ( app ) {
  app.views.IngresoProveedorItemTabla = app.mixins.View.extend({
    template: _.template($("#megashop_cambios_precios_item_tabla_template").html()),
    tagName: "tr",
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        this.options.view.editar_articulo(this.model);
      },
      "click .eliminar_flechita":"do_eliminar",
      "keydown .eliminar":function(e) {
        if (e.which == 13) {
          var self = this;
          e.stopPropagation();
          e.preventDefault();
          this.do_eliminar();
          return false;
        } else if (e.which == 27) {
          $("#megashop_cambios_precios_codigo_articulo").focus();
        }
      },
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.model.on("change",this.render,this);
      this.render();
    },
    do_eliminar: function() {
      this.model.destroy();  // Eliminamos el modelo
      $(this.el).remove();  // Lo eliminamos de la vista
      $("#megashop_cambios_precios_codigo_articulo").focus();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);