// -----------
//   MODELO
// -----------

(function ( models ) {

  models.IngresoProveedor = Backbone.Model.extend({
    urlRoot: "ingresos_proveedores/",
    defaults: {
      fecha: "",
      proveedor: "",
      id_proveedor: 0,
      id_empresa: ID_EMPRESA,
      total: 0,
      valor: 0,
      neto: 0,
      items: [],
      observaciones: "",
      numero_remito: "",
      almacen: "",
      id_almacen: 0,
      estado: 0, // 0 = PENDIENTE, 1 = CONFIRMADO
    }
  });
      
})( app.models );

(function (collections, model, paginator) {
  collections.IngresosProveedores = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 10,
      order_by: 'fecha',
      order: 'desc',
    },
    paginator_core: {
      url: "ingresos_proveedores/function/buscar/",
    },
  });

})( app.collections, app.models.IngresoProveedor, Backbone.Paginator);


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
      porc_ganancia_sucursal: 0,
      precio_final_central: 0,
    }
  });
      
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.IngresoProveedorEditView = app.mixins.View.extend({

    template: _.template($("#ingreso_proveedor_edit_panel_template").html()),

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
      "click #ingreso_proveedor_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #ingreso_proveedor_agregar_item": "agregar_item",

      "click .imprimir": function(){
        this.imprimir(this.model.id);
      },

      "focusin #ingreso_proveedor_codigo_articulo":function() {
        $("#tabla_items tbody tr.seleccionado").removeClass('seleccionado');
        $("#tabla_items tbody tr .radio").prop("checked",false);
      },

      "click #ingreso_proveedor_buscar_proveedor": "ver_buscar_proveedor",

      "keypress #ingreso_proveedor_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #ingreso_proveedor_item_cantidad": function(e) {
        if (e.which == 13) { 
          if (MEGASHOP == 1) {
            this.$("#ingreso_proveedor_item_costo_neto_inicial").select();
          } else {
            this.$("#ingreso_proveedor_item_costo_final").select();
          }
        }
      },

      "keypress #ingreso_proveedor_codigo_proveedor":function(e) {
        if (e.which == 13) { this.buscar_proveedor(); $("#ingreso_proveedor_codigo_articulo").select(); }
      },
      "focusout #ingreso_proveedor_codigo_proveedor":function(e){
        if (typeof this.proveedor != "undefined") {
          var nombre = this.proveedor.get("nombre");
          var texto = $(e.currentTarget).val();
          if (nombre != texto) {
            // Blanqueamos el proveedor para que no haya confusion
            $(e.currentTarget).val("");
          }
        }
      },

      "keypress #ingreso_proveedor_item_costo_neto_inicial":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;

          if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
            var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
            if (isNaN(dto_prov_2)) dto_prov_2 = 0;
            var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
            if (isNaN(dto_prov_3)) dto_prov_3 = 0;
            var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
            if (isNaN(dto_prov_4)) dto_prov_4 = 0;
            var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
            if (isNaN(dto_prov_5)) dto_prov_5 = 0;
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          } else {
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100);
          }
          this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_dto_prov").select();
        }
      },

      "keypress #ingreso_proveedor_item_dto_prov":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
            var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
            if (isNaN(dto_prov_2)) dto_prov_2 = 0;
            var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
            if (isNaN(dto_prov_3)) dto_prov_3 = 0;
            var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
            if (isNaN(dto_prov_4)) dto_prov_4 = 0;
            var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
            if (isNaN(dto_prov_5)) dto_prov_5 = 0;
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
            this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
            this.calcular_precios();
            this.$("#ingreso_proveedor_item_dto_prov_2").select();
          } else {
            var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100);
            this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
            this.calcular_precios();
            this.$("#ingreso_proveedor_alicuotas_iva").focus();            
          }
        }
      },

      "keypress #ingreso_proveedor_item_dto_prov_2":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_dto_prov_3").select();
        }
      },

      "keypress #ingreso_proveedor_item_dto_prov_3":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_dto_prov_4").select();
        }
      },

      "keypress #ingreso_proveedor_item_dto_prov_4":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_dto_prov_5").select();
        }
      },

      "keypress #ingreso_proveedor_item_dto_prov_5":function(e){
        if (e.keyCode == 13) {
          var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
          if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
          var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
          if (isNaN(dto_prov)) dto_prov = 0;
          var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
          if (isNaN(dto_prov_2)) dto_prov_2 = 0;
          var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
          if (isNaN(dto_prov_3)) dto_prov_3 = 0;
          var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
          if (isNaN(dto_prov_4)) dto_prov_4 = 0;
          var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
          if (isNaN(dto_prov_5)) dto_prov_5 = 0;
          var costo_neto = parseFloat(costo_neto_inicial) * ((100 - dto_prov) / 100)  * ((100 - dto_prov_2) / 100)  * ((100 - dto_prov_3) / 100)  * ((100 - dto_prov_4) / 100)  * ((100 - dto_prov_5) / 100);
          this.$("#ingreso_proveedor_item_costo_neto").val(Number(costo_neto).toFixed(3));
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_porc_ganancia").select();
        }
      },

      "keydown #ingreso_proveedor_alicuotas_iva":function(e) {
        if (e.which == 13) {
          e.preventDefault(); e.stopPropagation();
          this.$("#ingreso_proveedor_item_costo_final").select();
        }
      },

      "change #ingreso_proveedor_alicuotas_iva":function() {
        if (typeof this.articulo == "undefined") return;
        var porc_iva = this.$("#ingreso_proveedor_alicuotas_iva option:selected").data("porcentaje");
        var id_tipo_alicuota_iva = this.$("#ingreso_proveedor_alicuotas_iva").val();
        this.articulo.set({ "porc_iva":porc_iva, "id_tipo_alicuota_iva":id_tipo_alicuota_iva });
        this.calcular_precios();
      },
      
      "keypress #ingreso_proveedor_item_costo_neto":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#ingreso_proveedor_alicuotas_iva").select();
        }
      },
      "keypress #ingreso_proveedor_item_porc_ganancia":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          if (ID_EMPRESA == 868) this.$("#ingreso_proveedor_precio_final_central").select();
          else this.$("#ingreso_proveedor_precio_final").select();
        }
      },
      "keypress #ingreso_proveedor_precio_final_central":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").select();
        }
      },
      "keypress #ingreso_proveedor_item_porc_ganancia_sucursal":function(e){
        if (e.keyCode == 13) {
          this.calcular_precios();
          this.$("#ingreso_proveedor_precio_final").select();
        }
      },

      // Se modifica el COSTO FINAL
      "keypress #ingreso_proveedor_item_costo_final":function(e){
        if (e.keyCode == 13) {
          if (typeof this.articulo != "undefined") {
            
            // En base al precio neto, calculamos los costos
            var costo_final = this.$("#ingreso_proveedor_item_costo_final").val();
            var porc_iva = this.$("#ingreso_proveedor_alicuotas_iva option:selected").data("porcentaje");
            var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
            if (isNaN(dto_prov)) dto_prov = 0;
            var id_tipo_alicuota_iva = this.$("#ingreso_proveedor_alicuotas_iva").val();
            var costo_neto = costo_final / parseFloat(1+(porc_iva/100));
            var costo_neto_inicial = (costo_neto * 100 / (100-dto_prov));
            var porc_ganancia = this.$("#ingreso_proveedor_item_porc_ganancia").val();
            var costo_iva = costo_neto * (porc_iva / 100);
            var ganancia = costo_final * (porc_ganancia / 100);
            var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
            var precio_final = parseFloat(precio_neto) * (1+(porc_iva/100));

            this.articulo.set({
              "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
              "porc_iva":Number(porc_iva).toFixed(2),
              "dto_prov":Number(dto_prov).toFixed(2),
              "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
              "costo_iva":Number(costo_iva).toFixed(2),
              "costo_neto":Number(costo_neto).toFixed(3),
              "costo_final":Number(costo_final).toFixed(2),
              "porc_ganancia":Number(porc_ganancia).toFixed(4),
              "ganancia":Number(ganancia).toFixed(2),
              "precio_neto":Number(precio_neto).toFixed(2),
              "precio_final":Number(precio_final).toFixed(2),
            });
            this.mostrar_articulo();
            this.calcular_item();
          }
          this.$("#ingreso_proveedor_item_porc_ganancia").select();
        }
      },
      
      // Se modifican los precios, se calculan los costos
      "keypress #ingreso_proveedor_precio_final":function(e){
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
      var self = this;
      if (typeof this.articulo == "undefined") return;
      var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
      }
      var costo_neto = parseFloat(this.$("#ingreso_proveedor_item_costo_neto").val());
      var porc_iva = parseFloat(this.$("#ingreso_proveedor_alicuotas_iva option:selected").data("porcentaje"));
      var id_tipo_alicuota_iva = this.$("#ingreso_proveedor_alicuotas_iva").val();
      if (isNaN(costo_neto)) costo_neto = 0;
      if (isNaN(porc_iva)) porc_iva = 0;
      var costo_iva = costo_neto * (porc_iva / 100);
      
      var costo_final = parseFloat(costo_neto) * (1+(porc_iva / 100));
      var porc_ganancia = this.$("#ingreso_proveedor_item_porc_ganancia").val();
      if (isNaN(porc_ganancia)) porc_ganancia = 0;
      var ganancia = costo_final * (porc_ganancia / 100);
      var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
      var precio_final = parseFloat(costo_final) * (1+(porc_ganancia / 100));

      if (ID_EMPRESA == 868) {
        var precio_final_central = precio_final;
        var porc_ganancia_sucursal = this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").val();
        if (isNaN(porc_ganancia_sucursal)) porc_ganancia_sucursal = 0;
        precio_final = parseFloat(precio_final_central) * (1+(porc_ganancia_sucursal / 100));
        this.articulo.set({
          "precio_final_central":precio_final_central,
          "porc_ganancia_sucursal":porc_ganancia_sucursal,
        });        
      }

      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
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
        "porc_ganancia":Number(porc_ganancia).toFixed(4),
        "ganancia":Number(ganancia).toFixed(2),
        "precio_neto":Number(precio_neto).toFixed(2),
        "precio_final":Number(precio_final).toFixed(2),
        "porc_iva":Number(porc_iva).toFixed(2),
      });

      this.mostrar_articulo();
      this.calcular_item();
    },
    
    editar_precio_final: function() {

      var precio_final = parseFloat(this.$("#ingreso_proveedor_precio_final").val());
      var porc_iva = this.$("#ingreso_proveedor_alicuotas_iva option:selected").data("porcentaje");
      var id_tipo_alicuota_iva = this.$("#ingreso_proveedor_alicuotas_iva").val();
      var costo_neto = parseFloat(this.$("#ingreso_proveedor_item_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
      }
      var costo_final = parseFloat(this.$("#ingreso_proveedor_item_costo_final").val());

      // Si el costo final es distinto de cero, entonces cambiamos el PORCENTAJE DE GANANCIA
      if (costo_final != 0) {

        if (ID_EMPRESA == 868) {
          var precio_final_central = parseFloat(this.$("#ingreso_proveedor_precio_final_central").val());
          if (precio_final_central != 0) {
            var porc_ganancia_sucursal = parseFloat( ((precio_final / precio_final_central) - 1) * 100);
            this.articulo.set({
              "porc_ganancia_sucursal":porc_ganancia_sucursal,
              "precio_final_central":precio_final_central,
            });
            var porc_ganancia = parseFloat(this.$("#ingreso_proveedor_item_porc_ganancia").val());
            var precio_neto = parseFloat(this.$("#ingreso_proveedor_precio_neto").val());
            var costo_iva = costo_neto * (porc_iva / 100);
            var ganancia = costo_final * (porc_ganancia / 100);
            var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
          }
          
        } else {
          var costo_iva = costo_neto * (porc_iva / 100);
          var porc_ganancia = parseFloat( ((precio_final / costo_final) - 1) * 100);
          var ganancia = costo_final * (porc_ganancia / 100);
          var precio_neto = parseFloat(costo_neto) * (1+(porc_ganancia / 100));
        }
          
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

      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
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
          $("#ingreso_proveedor_codigo_articulo").select();                    
        }
      });
      $("#proveedores_buscar").focus();
    },
        
    buscar_proveedor : function() {
      var self = this;
      
      var codigo = this.$("#ingreso_proveedor_codigo_proveedor").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#ingreso_proveedor_codigo_proveedor").val(codigo);
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
                self.$("#ingreso_proveedor_codigo_proveedor").select();
                self.$("#ingreso_proveedor_codigo_proveedor").focus();
                return;
              }
              var proveedor = new app.models.Proveedor(r);
              self.seleccionar_proveedor(proveedor);
            }
          });
        }
      }
      this.$("#ingreso_proveedor_codigo_articulo").focus();    
    },
        
    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.model.set({
        "id_proveedor": self.proveedor.id,
      });
      self.$('#ingreso_proveedor_codigo_proveedor').val(self.proveedor.get("nombre"));
      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#ingreso_proveedor_codigo_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },
        
    buscar_articulo : function() {
      var self = this;

      // Primero controlamos que haya seleccionado la sucursal que quiere
      var id_sucursal = this.$("#ingreso_proveedor_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#ingreso_proveedor_almacenes").focus();
        return;
      }

      var codigo = $("#ingreso_proveedor_codigo_articulo").val();
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
      if (ID_EMPRESA == 868) {
        var id_sucursal = this.$("#ingreso_proveedor_almacenes").val();
        // Si es MEGASHOP CENTRAL
        // Recorremos los precios de las sucursales
        var precios_sucursales = this.articulo.get("precios_sucursales");
        for(var i=0;i<precios_sucursales.length;i++) {
          var suc = precios_sucursales[i];
          if (suc.id_sucursal == 531) {
            // CENTRAL
            this.articulo.set({
              "costo_neto":Number(suc.costo_neto).toFixed(3),
              "costo_neto_inicial":Number(suc.costo_neto_inicial).toFixed(3),
              "dto_prov":Number(suc.dto_prov).toFixed(2),
              "costo_final":Number(suc.costo_final).toFixed(2),
              "porc_ganancia":Number(suc.porc_ganancia).toFixed(4),
              "precio_final_central":Number(suc.precio_final).toFixed(2),
            });
          } else if (suc.id_sucursal == id_sucursal) {
            // La sucursal que estamos viendo
            this.articulo.set({
              "porc_ganancia_sucursal":Number(suc.porc_ganancia).toFixed(4),
              "precio_neto":Number(suc.precio_neto).toFixed(2),
              "precio_final":Number(suc.precio_final).toFixed(2),
            });
          }
        }
      }
      self.mostrar_articulo();
      self.controlar_stock_sucursal();
      self.calcular_item();
      this.$("#ingreso_proveedor_item_cantidad").select();
    },

    controlar_stock_sucursal:function() {
      if (typeof this.articulo == "undefined" || this.articulo == null) return;
      var stock_almacenes = this.articulo.get("stock_almacenes");
      if (typeof stock_almacenes == "undefined" || stock_almacenes == null) return;
      var id_sucursal = this.$("#ingreso_proveedor_almacenes").val();
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
      $("#ingreso_proveedor_id_articulo").val(this.item.get("id_articulo"));
      $("#ingreso_proveedor_codigo_articulo").val(this.item.get("codigo"));
      $("#ingreso_proveedor_item_nombre").val(this.item.get("nombre"));
      $("#ingreso_proveedor_item_cantidad").val(this.item.get("cantidad"));
      $("#ingreso_proveedor_item_costo_neto").val(Number(this.item.get("costo_neto")).toFixed(3));
      $("#ingreso_proveedor_item_costo_neto_inicial").val(Number(this.item.get("costo_neto_inicial")).toFixed(3));
      $("#ingreso_proveedor_item_dto_prov").val(Number(this.item.get("dto_prov")).toFixed(2));
      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
        $("#ingreso_proveedor_item_dto_prov_2").val(Number(this.item.get("dto_prov_2")).toFixed(2));
        $("#ingreso_proveedor_item_dto_prov_3").val(Number(this.item.get("dto_prov_3")).toFixed(2));
        $("#ingreso_proveedor_item_dto_prov_4").val(Number(this.item.get("dto_prov_4")).toFixed(2));
        $("#ingreso_proveedor_item_dto_prov_5").val(Number(this.item.get("dto_prov_5")).toFixed(2));
      }
      $("#ingreso_proveedor_item_costo_final").val(Number(this.item.get("costo_final")).toFixed(2));
      $("#ingreso_proveedor_item_porc_ganancia").val(Number(this.item.get("porc_ganancia")).toFixed(4));
      $("#ingreso_proveedor_precio_final").val(Number(this.item.get("precio_final")).toFixed(2));
      $("#ingreso_proveedor_alicuotas_iva").val(this.item.get("id_tipo_alicuota_iva"));
      $("#ingreso_proveedor_item_descripcion").val(this.item.get("descripcion"));
      $("#ingreso_proveedor_item_bonificado").val(this.item.get("bonificado"));
      $("#ingreso_proveedor_item_no_editar_precios").val(this.item.get("no_editar_precios"));
      $("#ingreso_proveedor_item_no_editar_stock").val(this.item.get("no_editar_stock"));

      var v = {
        "id":this.item.get("id_articulo"),
        "nombre":this.item.get("nombre"),
        "costo_neto":this.item.get("costo_neto"),
        "costo_neto_inicial":this.item.get("costo_neto_inicial"),
        "dto_prov":this.item.get("dto_prov"),
        "dto_prov_2": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_2") : 0,
        "dto_prov_3": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_3") : 0,
        "dto_prov_4": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_4") : 0,
        "dto_prov_5": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? this.item.get("dto_prov_5") : 0,
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
      };
      if (ID_EMPRESA == 868) {
        this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").val(this.item.get("porc_ganancia_sucursal"));
        this.$("#ingreso_proveedor_precio_final_central").val(this.item.get("precio_final_central"));
        v.porc_ganancia_sucursal = this.item.get("porc_ganancia_sucursal");
        v.precio_final_central = this.item.get("precio_final_central");
      }
      this.articulo = new app.models.AbstractModel(v);

      self.calcular_item();
      this.$("#ingreso_proveedor_item_cantidad").select();            
    },
    
    ver_buscar_articulo : function() {
      var self = this;
      var id_sucursal = this.$("#ingreso_proveedor_almacenes").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal.");
        this.$("#ingreso_proveedor_almacenes").focus();
        return;
      }
      window.articulos_buscar_id_proveedor = self.$("#ingreso_proveedor_codigo_proveedor").data("id");
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
            var that = self;
            self.$("#ingreso_proveedor_codigo_articulo").val(window.codigo_articulo_seleccionado);
            $.ajax({
              "url":"articulos/function/get_by_codigo/"+window.codigo_articulo_seleccionado,
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
                  that.seleccionar_articulo(a);
                }
              }
            });
          } else {
            self.$("#ingreso_proveedor_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    mostrar_articulo : function() {
      this.$("#ingreso_proveedor_item_nombre").val(this.articulo.get("nombre"));
      this.$("#ingreso_proveedor_alicuotas_iva").val(this.articulo.get("id_tipo_alicuota_iva"));
      this.$("#ingreso_proveedor_id_articulo").val(this.articulo.id);
      this.$("#ingreso_proveedor_item_costo_neto").val(Number(this.articulo.get("costo_neto")).toFixed(3));
      this.$("#ingreso_proveedor_item_costo_neto_inicial").val(Number(this.articulo.get("costo_neto_inicial")).toFixed(3));
      this.$("#ingreso_proveedor_item_dto_prov").val(Number(this.articulo.get("dto_prov")).toFixed(2));
      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
        this.$("#ingreso_proveedor_item_dto_prov_2").val(Number(this.articulo.get("dto_prov_2")).toFixed(2));
        this.$("#ingreso_proveedor_item_dto_prov_3").val(Number(this.articulo.get("dto_prov_3")).toFixed(2));
        this.$("#ingreso_proveedor_item_dto_prov_4").val(Number(this.articulo.get("dto_prov_4")).toFixed(2));
        this.$("#ingreso_proveedor_item_dto_prov_5").val(Number(this.articulo.get("dto_prov_5")).toFixed(2));
      }
      this.$("#ingreso_proveedor_item_costo_final").val(Number(this.articulo.get("costo_final")).toFixed(2));
      this.$("#ingreso_proveedor_item_porc_ganancia").val(Number(this.articulo.get("porc_ganancia")).toFixed(4));
      this.$("#ingreso_proveedor_precio_neto").val(Number(this.articulo.get("precio_neto")).toFixed(2));
      this.$("#ingreso_proveedor_precio_final").val(Number(this.articulo.get("precio_final")).toFixed(2));
      if (ID_EMPRESA == 868) {
        this.$("#ingreso_proveedor_precio_final_central").val(Number(this.articulo.get("precio_final_central")).toFixed(2));
        this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").val(Number(this.articulo.get("porc_ganancia_sucursal")).toFixed(2));
      }
    },
    
    // Agrega el item a la lista
    agregar_item : function() {
      var self = this;

      var codigo = this.$("#ingreso_proveedor_codigo_articulo").val();
      if (isEmpty(codigo)) {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#ingreso_proveedor_codigo_articulo").focus();
        return;
      }                

      var cantidad = this.$("#ingreso_proveedor_item_cantidad").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) { cantidad = Number(1).toFixed(FACTURACION_CANTIDAD_DECIMALES); }

      // Si ya existe el codigo ingresado, tenemos que t

      var bonificacion = 0;
      var id_articulo = this.$("#ingreso_proveedor_id_articulo").val();
      var porc_iva = parseFloat(this.$("#ingreso_proveedor_alicuotas_iva option:selected").data("porcentaje"));
      var costo_final = parseFloat(this.$("#ingreso_proveedor_item_costo_final").val());
      var costo_neto = parseFloat(this.$("#ingreso_proveedor_item_costo_neto").val());
      var costo_neto_inicial = parseFloat(this.$("#ingreso_proveedor_item_costo_neto_inicial").val());
      var dto_prov = parseFloat(this.$("#ingreso_proveedor_item_dto_prov").val());
      if (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) {
        var dto_prov_2 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_2").val());
        var dto_prov_3 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_3").val());
        var dto_prov_4 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_4").val());
        var dto_prov_5 = parseFloat(this.$("#ingreso_proveedor_item_dto_prov_5").val());
      }
      var total_neto = costo_neto * cantidad;
      var total_final = costo_final * cantidad;
      var porc_ganancia = parseFloat(this.$("#ingreso_proveedor_item_porc_ganancia").val());
      var precio_neto = parseFloat(this.$("#ingreso_proveedor_precio_neto").val());
      var precio_final = parseFloat(this.$("#ingreso_proveedor_precio_final").val());
      
      var values = {
        "id_articulo":id_articulo,
        "costo_neto_inicial":costo_neto_inicial,
        "dto_prov":dto_prov,
        "dto_prov_2": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? dto_prov_2 : 0,
        "dto_prov_3": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? dto_prov_3 : 0,
        "dto_prov_4": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? dto_prov_4 : 0,
        "dto_prov_5": (this.$("#ingreso_proveedor_item_dto_prov_2").length > 0) ? dto_prov_5 : 0,
        "costo_neto":costo_neto,
        "costo_final":costo_final,
        "codigo":codigo,
        "nombre":this.$("#ingreso_proveedor_item_nombre").val(),
        "cantidad":cantidad,
        "porc_iva":porc_iva,
        "precio_neto":precio_neto,
        "precio_final":precio_final,
        "porc_ganancia":porc_ganancia,
        "total_neto":total_neto,
        "total_final":total_final,
        "id_tipo_alicuota_iva":this.$("#ingreso_proveedor_alicuotas_iva").val(),
        "bonificado":this.$("#ingreso_proveedor_item_bonificado").val(),
        "no_editar_precios":this.$("#ingreso_proveedor_item_no_editar_precios").val(),
        "no_editar_stock":this.$("#ingreso_proveedor_item_no_editar_stock").val(),
      };      

      if (ID_EMPRESA == 868) {
        values.porc_ganancia_sucursal = this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").val();
        values.precio_final_central = this.$("#ingreso_proveedor_precio_final_central").val();
      }

      // Actualizamos o agregamos el item
      if (this.item != undefined) {
        this.item.set(values);
      } else {
        var item = new app.models.IngresoProveedorItem(values);
        this.items.add(item);
      }
      console.log(this.items);
        
      this.item = undefined;
      this.limpiar_item();
      this.agregando = 0;
      this.$("#ingreso_proveedor_codigo_articulo").select();              

      this.$('#tabla_items').parent().scrollTop(self.$('#tabla_items').parent()[0].scrollHeight);
    },
    
    calcular_item: function() {
      // TODO: Controlar los campos cuando no son numericos
      var self = this;
      var cantidad = this.$("#ingreso_proveedor_item_cantidad").val();
      var precio_unit = this.$("#ingreso_proveedor_item_costo_final").val();
      var bonificado = 0; //this.$("#ingreso_proveedor_item_bonificado").val();
      var subtotal = Number((cantidad * precio_unit) * ((100-bonificado)/100)).toFixed(FACTURACION_CANTIDAD_DECIMALES);
      this.$("#ingreso_proveedor_item_subtotal").val(subtotal);
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.agregando = 0;
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
          model: app.models.IngresoProveedorItem
        });
        var productos = this.model.get("items");
        this.items = new ItemsCollection();
        for(var i=0;i<productos.length;i++) {
          var p = productos[i];
          var fi = new app.models.IngresoProveedorItem(p);
          this.items.add(fi);
        }
        this.items.on('all', this.render_tabla_items, this);
        this.items.on('add', this.addItem, this);

        this.render_tabla_items();
      }

      // Buscamos el proveedor y lo seteamos
      var id_proveedor = self.model.get("id_proveedor");
      if (id_proveedor != 0) {
        var proveedor = new app.models.Proveedor({"id":id_proveedor});
        proveedor.fetch({
          "success":function() {
            self.seleccionar_proveedor(proveedor);
          },
        });
      }
    },

    render: function() {
        
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { 
        edicion: edicion, 
        id:this.model.id,
        permiso: this.options.permiso 
      };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
        
      if (isEmpty(this.model.get("fecha"))) this.model.set("fecha",moment().format("DD/MM/YYYY"));
      createdatepicker(this.$("#ingreso_proveedor_fecha"),this.model.get("fecha"));
  
      this.limpiar_item();
        
      // AUTOCOMPLETE DE PROVEEDORES
      // ---------------------------
      var input = this.$("#ingreso_proveedor_codigo_proveedor");
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
      var input = this.$("#ingreso_proveedor_codigo_articulo");
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
      self.$("#ingreso_proveedor_subtotal_neto").val(Number(self.model.get("neto")).toFixed(2));
      self.$("#ingreso_proveedor_total").val(Number(self.model.get("total")).toFixed(2));
    },
    
    calcular_totales : function() {
        
      var neto = 0; var porc_descuento = 0; var total = 0; var iva = 0;
      var descuento = 0; var subtotal_neto = 0; var subtotal_final = 0;
      var tipo_iva_proveedor = this.$("#ingreso_proveedor_proveedor_iva").val();
      var items = this.model.get("items");
        
      var porc_descuento = 0; /*parseFloat(this.$("#ingreso_proveedor_porc_descuento").val());
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
      this.$("#ingreso_proveedor_id_articulo").val("0");
      this.$("#ingreso_proveedor_item_nombre").val("");
      this.$("#ingreso_proveedor_item_descripcion").val("");
      this.$("#ingreso_proveedor_item_cantidad").val("1");
      this.$("#ingreso_proveedor_item_bonificado").val("0");
      this.$("#ingreso_proveedor_item_no_editar_precios").val("0");
      this.$("#ingreso_proveedor_item_no_editar_stock").val("0");
      this.$("#ingreso_proveedor_item_costo_neto").val("0.00");
      this.$("#ingreso_proveedor_item_costo_neto_inicial").val("0.00");
      this.$("#ingreso_proveedor_item_dto_prov").val("0.00");
      this.$("#ingreso_proveedor_item_dto_prov_2").val("0.00");
      this.$("#ingreso_proveedor_item_dto_prov_3").val("0.00");
      this.$("#ingreso_proveedor_item_dto_prov_4").val("0.00");
      this.$("#ingreso_proveedor_item_dto_prov_5").val("0.00");
      this.$("#ingreso_proveedor_item_costo_final").val("0.00");
      this.$("#ingreso_proveedor_precio_final").val("0.00");
      this.$("#ingreso_proveedor_item_porc_ganancia").val("");
      this.$("#ingreso_proveedor_item_porc_ganancia_sucursal").val("");
      this.$("#ingreso_proveedor_precio_final_central").val("");
      this.$("#ingreso_proveedor_item_subtotal").val("");
      this.$("#ingreso_proveedor_codigo_articulo").val("");
      this.$("#ingreso_proveedor_codigo_articulo").focus();
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
      var nombre_proveedor = this.$("#ingreso_proveedor_codigo_proveedor").val().trim();
      if (isEmpty(nombre_proveedor)) {
        this.$("#ingreso_proveedor_codigo_proveedor").focus();
        throw "ERROR: Ingrese un proveedor."; 
      }
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
        "fecha":self.$("#ingreso_proveedor_fecha").val(),
        "id_almacen":((self.$("#ingreso_proveedor_almacenes").length > 0) ? self.$("#ingreso_proveedor_almacenes").val() : 0),
        "numero_remito":self.$("#ingreso_proveedor_numero").val(),
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
    template: _.template($("#ingreso_proveedor_item_tabla_template").html()),
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
          $("#ingreso_proveedor_codigo_articulo").focus();
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
      $("#ingreso_proveedor_codigo_articulo").focus();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



(function ( app ) {
  app.views.IngresoProveedorItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#ingresos_proveedores_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .imprimir":function() {
        var id = this.model.id;
        workspace.imprimir_reporte("ingresos_proveedores/function/imprimir/"+id);
      },
      "click .imprimir_sin_costo": function(){
        var id = this.model.id;
        workspace.imprimir_reporte("ingresos_proveedores/function/imprimir/"+id+"?con_precio=0");
      },
      "click .imprimir_remito": function() {
        var id = this.model.id;
        workspace.imprimir_reporte("ingresos_proveedores/function/imprimir_remito/"+id);
      },
      "click .etiquetas":function() {
        var self = this;
        this.model.fetch({
          "success":function(){
            var salida = new Array();
            var items = self.model.get("items");
            for(var i=0;i<items.length;i++) {
              var item = items[i];
              var model = new app.models.AbstractModel({
                "id":item.id_articulo,
                "cantidad":item.cantidad,
                "codigo":item.codigo,
                "nombre":item.nombre,
                "costo_neto":item.costo_neto,
                "costo_final":item.costo_final,
                "porc_iva":item.porc_iva,
                "precio_neto":item.precio_neto,
                "precio_final":item.precio_final,
                "id_alicuota_iva":item.id_alicuota_iva,
              });
              salida.push(model);
            }
            var view = new app.views.ArticuloImprimirEtiquetasEditView({
              model: new app.models.AbstractModel({
                items: salida,
              })
            });
            crearLightboxHTML({
              "html":view.el,
              "width":700,
              "height":400,
              "escapable":false,
            });
            $("#articulo_imprimir_etiquetas_codigo").focus();
          }
        });
      },
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#ingreso_proveedor/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    duplicar: function(e) {
      var clonado = this.model.clone();
      clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
      clonado.save({},{
        success: function(model,response) {
          model.set({id:response.id});
        }
      });
      this.model.collection.add(clonado);
      e.stopPropagation();
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.IngresosProveedoresTableView = app.mixins.View.extend({

    template: _.template($("#ingresos_proveedores_panel_template").html()),
    myEvents: {
      "click .buscar":"buscar",
      "click .calcular_ventas":"calcular_ventas",
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      window.ingresos_proveedores_filter = (typeof window.ingresos_proveedores_filter != "undefined") ? window.ingresos_proveedores_filter : "";
      window.ingresos_proveedores_id_sucursal = (typeof window.ingresos_proveedores_id_sucursal != "undefined") ? window.ingresos_proveedores_id_sucursal : 0;
      window.ingresos_proveedores_estado = (typeof window.ingresos_proveedores_estado != "undefined") ? window.ingresos_proveedores_estado : -1;
      window.ingresos_proveedores_id_proveedor = (typeof window.ingresos_proveedores_id_proveedor != "undefined") ? window.ingresos_proveedores_id_proveedor : 0;
      window.ingresos_proveedores_desde = (typeof window.ingresos_proveedores_desde != "undefined") ? window.ingresos_proveedores_desde : "";
      window.ingresos_proveedores_hasta = (typeof window.ingresos_proveedores_hasta != "undefined") ? window.ingresos_proveedores_hasta : "";
      window.ingresos_proveedores_page = (typeof window.ingresos_proveedores_page != "undefined") ? window.ingresos_proveedores_page : 1;
      window.ingresos_proveedores_codigo_articulo = (typeof window.ingresos_proveedores_codigo_articulo != "undefined") ? window.ingresos_proveedores_codigo_articulo : "";
      this.permiso = this.options.permiso;
      this.render();
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });

      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker($(this.el).find("#ingresos_proveedores_desde"),window.ingresos_proveedores_desde);
      createdatepicker($(this.el).find("#ingresos_proveedores_hasta"),window.ingresos_proveedores_hasta);
      
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#ingresos_proveedores_buscar_proveedores",
        firstOptions: ["<option value='0'>Proveedor</option>"],
        selected: window.ingresos_proveedores_id_proveedor,
        onComplete:function(c) {
          $("#ingresos_proveedores_buscar_proveedores").select2({}).change(function(){
            window.ingresos_proveedores_page = 1;
            window.ingresos_proveedores_id_proveedor = $(this).val();
          });
        }                
      });
      return this;
    },

    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.ingresos_proveedores_filter != this.$("#ingresos_proveedores_buscar").val().trim()) {
        window.ingresos_proveedores_filter = this.$("#ingresos_proveedores_buscar").val().trim();
        cambio_parametros = true;
      }
      if (this.$("#ingresos_proveedores_desde").length > 0) {
        if (window.ingresos_proveedores_desde != this.$("#ingresos_proveedores_desde").val().trim()) {
          window.ingresos_proveedores_desde = this.$("#ingresos_proveedores_desde").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#ingresos_proveedores_hasta").length > 0) {
        if (window.ingresos_proveedores_hasta != this.$("#ingresos_proveedores_hasta").val().trim()) {
          window.ingresos_proveedores_hasta = this.$("#ingresos_proveedores_hasta").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#ingresos_proveedores_buscar_sucursales").length > 0) {
        if (window.ingresos_proveedores_id_sucursal != this.$("#ingresos_proveedores_buscar_sucursales").val().trim()) {
          window.ingresos_proveedores_id_sucursal = this.$("#ingresos_proveedores_buscar_sucursales").val().trim();
          cambio_parametros = true;
        }
      }
      if (this.$("#ingresos_proveedores_buscar_estados").length > 0) {
        if (window.ingresos_proveedores_estado != this.$("#ingresos_proveedores_buscar_estados").val()) {
          window.ingresos_proveedores_estado = this.$("#ingresos_proveedores_buscar_estados").val();
          cambio_parametros = true;
        }
      }
      if (this.$("#ingresos_proveedores_codigo_articulo").length > 0) {
        if (window.ingresos_proveedores_codigo_articulo != this.$("#ingresos_proveedores_codigo_articulo").val().trim()) {
          window.ingresos_proveedores_codigo_articulo = this.$("#ingresos_proveedores_codigo_articulo").val().trim();
          cambio_parametros = true;
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.ingresos_proveedores_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.ingresos_proveedores_filter),
        "desde": (isEmpty(window.ingresos_proveedores_desde)) ? "" : window.ingresos_proveedores_desde.replace(/\//g,"-"),
        "hasta": (isEmpty(window.ingresos_proveedores_hasta)) ? "" : window.ingresos_proveedores_hasta.replace(/\//g,"-"),
        "id_proveedor":window.ingresos_proveedores_id_proveedor,
        "id_sucursal":window.ingresos_proveedores_id_sucursal,
        "codigo_articulo":window.ingresos_proveedores_codigo_articulo,
        "estado":((control.check("ingresos_proveedores")==3)?(window.ingresos_proveedores_estado):1),
      };
      if (!isEmpty(window.ingresos_proveedores_desde)) {
        this.collection.setManyPer(99999999999);
      }
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.ingresos_proveedores_page);
    },

    addAll : function () {
      this.total = 0;
      window.ingresos_proveedores_page = this.pagination.getPage();
      this.$("#ingresos_proveedores_table tbody").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
      this.$("#ingresos_proveedores_total").html(Number(this.total).toFixed(2));
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.IngresoProveedorItem({
        model: item,
        collection: self.collection,
        habilitar_seleccion: this.habilitar_seleccion, 
        permiso: this.permiso,
      });
      this.total += parseFloat(item.get("total"));
      this.$("#ingresos_proveedores_table tbody").append(view.render().el);
    },

    calcular_ventas: function() {
      var ingresos = new Array();
      var primer_fecha_desde = moment();
      $("#ingresos_proveedores_table .check-row:checked").each(function(i,e){
        ingresos.push($(e).val());
        var fecha = moment($(e).data("fecha"),"DD/MM/YYYY");
        console.log(fecha);
        if (fecha.isBefore(primer_fecha_desde)) primer_fecha_desde = fecha;
      });
      if (ingresos.length == 0) {
        alert("Por favor seleccione al menos un ingreso de la lista");
        return;
      }
      var modelo = new app.models.AbstractModel({
        "ingresos":ingresos,
      });
      var estad = new app.views.EstadisticasVentasPorIngresoView({
        model:modelo,
        desde: primer_fecha_desde,
      });
      crearLightboxHTML({
        "html":estad.el,
        "width":1200,
        "height":500,
        "escapable":false,
      });
    },

  });
})(app);



(function ( app ) {

  app.views.EstadisticasVentasPorIngresoView = app.mixins.View.extend({

    template: _.template($("#estadisticas_ventas_por_ingreso_template").html()),
            
    myEvents: {
      "click .buscar":"buscar",
      "click .exportar":"exportar",
      "click .cerrar":"cerrar",
      "click .sorting":function(e) {
        var asc = $(e.currentTarget).hasClass("sorting_asc");
        var desc = $(e.currentTarget).hasClass("sorting_desc");
        $(".sorting").removeClass("sorting_asc");
        $(".sorting").removeClass("sorting_desc");
        if (asc) $(e.currentTarget).addClass("sorting_desc");
        else if (desc) $(e.currentTarget).addClass("sorting_asc");
        else $(e.currentTarget).addClass("sorting_desc");

        var sort_by = $(e.currentTarget).data("sort-by");
        if (sort_by == undefined) return;
        var sort = (desc)?"desc":"asc";
        this.order_by = sort_by;
        this.order = sort;
        this.buscar();
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.desde = (typeof options.desde != "undefined") ? moment(options.desde).toDate() : moment().startOf("month").toDate();
      this.hasta = (typeof options.hasta != "undefined") ? moment(options.hasta).toDate() : new Date();
      this.order_by = (typeof options.order_by != "undefined") ? options.order_by : "nombre";
      this.order = (typeof options.order != "undefined") ? options.order : "asc";
      this.render();
    },

    buscar: function() {
      var self = this;
      var params = {};
      params.desde = self.$("#estadisticas_ventas_por_ingreso_fecha_desde").val();
      params.hasta = self.$("#estadisticas_ventas_por_ingreso_fecha_hasta").val();
      params.ingresos = self.model.get("ingresos").join("-");
      params.order = this.order;
      params.order_by = this.order_by;
      $.ajax({
        "url":"estadisticas/function/ventas_por_ingresos/",
        "dataType":"json",
        "data":params,
        "type":"post",
        "success":function(r){

          // Recorremos los resultados
          self.$("#estadisticas_ventas_por_ingreso_table tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var elem = r.results[i];
            var item = new app.views.EstadisticasVentasPorIngresoItem({
              model: new app.models.AbstractModel(elem),
            });
            self.$("#estadisticas_ventas_por_ingreso_table tbody").append(item.el);
          }
          /*
          self.$("#estadisticas_ventas_por_ingreso_cantidad").html(Number(r.meta.cantidad).toFixed(0));
          self.$("#estadisticas_ventas_por_ingreso_costo_final").html("$ "+Number(r.meta.costo_final).toFixed(2));
          self.$("#estadisticas_ventas_por_ingreso_total_final").html("$ "+Number(r.meta.total_final).toFixed(2));
          var ganancia = parseFloat(r.meta.total_final) - parseFloat(r.meta.costo_final);
          self.$("#estadisticas_ventas_por_ingreso_ganancia").html("$ "+Number(ganancia).toFixed(2));
          */
        },
      });
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },
        
    render: function() {
      $(this.el).html(this.template({}));
      createdatepicker($(this.el).find("#estadisticas_ventas_por_ingreso_fecha_desde"),this.desde);
      createdatepicker($(this.el).find("#estadisticas_ventas_por_ingreso_fecha_hasta"),this.hasta);
    },

    exportar: function() {
      var array = new Array();
      $("#estadisticas_ventas_por_ingreso_table tbody tr").each(function(i,e){
        array.push({
          "sucursal":$(e).find("td:eq(0)").html(),
          "codigo":$(e).find("td:eq(1)").html(),
          "ean":$(e).find("td:eq(2)").html().replaceAll('<br>'," | "),
          "codigo_prov":$(e).find("td:eq(3)").html(),
          "nombre":$(e).find("td:eq(4) span").text(),
          "costo":$(e).find("td:eq(5)").html(),
          "precio":$(e).find("td:eq(6)").html(),
          "margen":$(e).find("td:eq(7)").html(),
          "compra":$(e).find("td:eq(8)").html(),
          "total_compra":$(e).find("td:eq(9)").html(),
          "ult_compra":$(e).find("td:eq(10)").html(),
          "venta":$(e).find("td:eq(11)").html(),
          "total_venta":$(e).find("td:eq(12)").html(),
          "ult_venta":$(e).find("td:eq(13)").html(),
          "diferencia":$(e).find("td:eq(14)").html(),
          "porcentaje":$(e).find("td:eq(15)").html(),
          "stock":$(e).find("td:eq(16)").html(),
        });
      });
      var header = new Array("Sucursal","Codigo","EAN","Cod. Prov.","Nombre","Costo","Precio","Margen","Compra","$","Ult. Compra","Venta","$","Ult. Venta","Diferencia","% Vendido","Stock");
      this.exportar_excel({
        "filename":"estadisticas",
        "title":"Ventas de Ingresos",
        "data":array,
        "header":header,
      });
    },
        
  });
})(app);


(function ( app ) {

  app.views.EstadisticasVentasPorIngresoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#estadisticas_ventas_por_ingreso_item_template').html()),
    myEvents: {
      "click .ver_detalle":"ver_detalle",
    },
    initialize: function(options) {
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
      this.render();
    },
    ver_detalle: function() {
      var self = this;
      var detalle = new app.views.StockDetalleView({
        tab_default: "grafico",
        model:new app.models.AbstractModel({
          "desde":$("#estadisticas_ventas_por_ingreso_fecha_desde").val(),
          "hasta":$("#estadisticas_ventas_por_ingreso_fecha_hasta").val(),
          "nombre":self.model.get("nombre"),
          "codigo":self.model.get("codigo"),
          "id_articulo":self.model.get("id_articulo"),
          "id_sucursal":self.model.get("id_almacen"),
        }),
      });        
      crearLightboxHTML({
        "html":detalle.el,
        "width":800,
        "height":500,
        "escapable":false,
      });
    },
    render: function() {
      var obj = { 
        permiso: this.permiso,
        total_general: this.total_general,
      };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });

})( app );