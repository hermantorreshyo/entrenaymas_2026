(function ( models ) {

  models.Compra = Backbone.Model.extend({
    urlRoot: 'compras/',
    defaults: {
      "fecha" : '',
      "id_proveedor": 0,
      "proveedor":"",
      "id_tipo_comprobante": 0,
      "netos":new Array(),
      "forma_pago":"C",

      "numero_1":"",
      "numero_2":"",

      "perc_ing_brutos":0,
      "perc_iva":0,
      "perc_agip":0,
      "perc_san_luis":0,
      "impuesto_interno":0,
      "no_gravado":0,
      "exento":0,

      "total_neto":0,
      "total_iva":0,
      "subtotal":0, // = total_neto + total_iva
      "total_regimenes_especiales":0,
      "total_general":0, // = subtotal + total_regimenes_especiales
      
      "movimiento":"",
      "compra_real":1,
      "incluido_libro_iva":1,
      "id_empresa":ID_EMPRESA,
      "estado":0,
      "observaciones":"",

      // Datos del proveedor que no se persisten
      "codigo_proveedor": "",
      "nombre_proveedor": "",
      "cuit_proveedor": "",
      "tipo_iva_proveedor": "",

      "id_sucursal": 0,
      "sucursal":"",
      "id_usuario": ID_USUARIO,
      "id_caja":0,
      "ver_en_cuenta":1,

    },
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Compras = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 30,
    },
    paginator_core: {
      url: "compras/function/consulta/",
    },
  });

})( app.collections, app.models.Compra, Backbone.Paginator);



(function ( app ) {

  app.views.ComprasListadoView = app.mixins.View.extend({

    template: _.template($("#compras_listado_template").html()),
    
    myEvents: {
      "click .buscar": "buscar",
      "click .exportar":"exportar",
      "keypress #compras_listado_buscar":function(e) {
        if (e.which == 13) this.buscar();
      },
      "click .iva_compras":"iva_compras",
      "click .retencion_iibb":"retencion_iibb",
      "click .retencion_ganancias":"retencion_ganancias",
      // Para configurar las columnas de la tabla
      "click .configurar_tabla":function() {
        var p = new app.views.ConfiguracionTablaView({
          titulo: "Compras",
          tabla: window.tabla_compras,
          model: new app.models.AbstractModel()
        });
        crearLightboxHTML({
          "html":p.el,
          "width":450,
          "height":140,
        });
      },
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.fecha = (this.options.fecha == undefined) ? "" : (this.options.fecha).replace(/\//g,"-");
      this.desde = (this.options.desde == undefined) ? "" : (this.options.desde).replace(/\-/g,"/");
      this.hasta = (this.options.hasta == undefined) ? "" : (this.options.hasta).replace(/\-/g,"/");
      window.compras_listado_fecha_desde = (typeof window.compras_listado_fecha_desde != "undefined") ? window.compras_listado_fecha_desde : ((isEmpty(this.desde)) ? this.fecha : this.desde);
      window.compras_listado_fecha_hasta = (typeof window.compras_listado_fecha_hasta != "undefined") ? window.compras_listado_fecha_hasta : ((isEmpty(this.hasta)) ? this.fecha : this.hasta);
      window.compras_listado_ver_todas = (typeof window.compras_listado_ver_todas != "undefined") ? window.compras_listado_ver_todas : 1;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
      this.mes = (this.options.mes == undefined) ? "" : this.options.mes;
      this.anio = (this.options.anio == undefined) ? moment().format("YYYY") : this.options.anio;
      this.id_concepto = (this.options.id_concepto == undefined) ? 0 : this.options.id_concepto;
      this.permiso = this.options.permiso;            

      $(this.el).html(this.template({
        "permiso":self.permiso,
        "seleccionar":self.habilitar_seleccion,
        "mes":self.mes,
        "anio":self.anio,
        "id_concepto":self.id_concepto,
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

      createdatepicker(this.$("#compras_desde"),window.compras_listado_fecha_desde);
      createdatepicker(this.$("#compras_hasta"),window.compras_listado_fecha_hasta);
      
      this.buscar();
    },

    buscar : function() {

      var self = this;
      var cambio_parametros = false;
      filtros = {};
      filtros.filter = $(this.el).find("#compras_listado_buscar").val();
      
      var tc = "";
      this.$(".compras_tipo_comprobante_check").each(function(i,e){
        if ($(e).is(":checked")) {
          if (isEmpty(tc)) tc = $(e).val();
          else tc = tc+"-"+$(e).val();
        }
      });
      filtros.tc = tc;

      filtros.ids_conceptos = this.$("#compras_conceptos option:selected").data("ids");
      if (typeof filtros.ids_conceptos == "undefined") filtros.ids_conceptos = "";

      var mes_movimiento = this.$("#compras_movimiento_mes").val();
      var anio_movimiento = this.$("#compras_movimiento_anio").val();
      filtros.movimiento = (mes_movimiento != "00" && !isEmpty(anio_movimiento)) ? String(mes_movimiento)+String(anio_movimiento).substr(2) : "";

      filtros.id_sucursal = 0;
      if (this.$("#compras_sucursales").length > 0) {
        filtros.id_sucursal = this.$("#compras_sucursales").val();
      }

      filtros.id_usuario = 0;
      if (PERFIL == 395) filtros.id_usuario = ID_USUARIO;

      if (this.$("#compras_desde").length > 0 && window.compras_listado_fecha_desde != this.$("#compras_desde").val().trim()) {
        window.compras_listado_fecha_desde = this.$("#compras_desde").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#compras_hasta").length > 0 && window.compras_listado_fecha_hasta != this.$("#compras_hasta").val().trim()) {
        window.compras_listado_fecha_hasta = this.$("#compras_hasta").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#compras_incluir_todas").length > 0 && window.compras_listado_ver_todas != this.$("#compras_incluir_todas").val().trim()) {
        window.compras_listado_ver_todas = this.$("#compras_incluir_todas").val().trim();
        cambio_parametros = true;
      }

      filtros.desde = (isEmpty(window.compras_listado_fecha_desde)) ? "" : window.compras_listado_fecha_desde.replace(/\//g,"-");
      filtros.hasta = (isEmpty(window.compras_listado_fecha_hasta)) ? "" : window.compras_listado_fecha_hasta.replace(/\//g,"-");
      filtros.ver_todas = window.compras_listado_ver_todas;

      this.usa_filtros = (!isEmpty(filtros.filter) || !isEmpty(filtros.movimiento) || !isEmpty(filtros.ids_conceptos) || !isEmpty(filtros.desde) || !isEmpty(filtros.hasta));
      if (this.usa_filtros) filtros.offset = 99999;
      if (filtros.id_sucursal != 0) this.usa_filtros = true; // Se pone aparte porque el offset no se debe aplicar cuando filtra por sucursal, ya que puede haber muchas
      this.collection.server_api = filtros;
      this.collection.pager();            
    },

    iva_compras: function() {
      var p = new app.views.IvaComprasView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":450,
        "height":140,
      });
    },

    retencion_iibb: function() {
      var p = new app.views.RetencionIIBBView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":450,
        "height":140,
      });
    },

    retencion_ganancias: function() {
      var p = new app.views.RetencionGananciasView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":p.el,
        "width":450,
        "height":140,
      });
    },

    addAll : function () {
      var self = this;
      var total = 0;
      var cantidad = 0;
      var iva = 0;
      var neto = 0;
      var reg_especiales = 0;
      this.$("#compras_tabla tbody").empty();
      this.collection.each(function(i){
        self.addOne(i);
        total += parseFloat(i.get("total_general"));
        iva += parseFloat(i.get("total_iva"));
        neto += parseFloat(i.get("total_neto"));
        reg_especiales += parseFloat(i.get("total_regimenes_especiales"));
        cantidad++;
      });

      // Agregamos una fila al final
      if (this.usa_filtros) {
        var colspan = this.$("#compras_tabla thead th:visible").length - 2;
        var tr = "<tr>";
        tr+="<td class='fila_alerta tar' colspan='"+colspan+"'><b>TOTALES:</b></td>";
        tr+="<td class='fila_alerta tar bold'>$ "+Number(total).format(2)+"</td>";
        tr+="<td class='fila_alerta'></td>";
        tr+="</tr>";
        this.$("#compras_tabla tbody").append(tr);
        this.$(".pagination_container").hide();

        // Tambien mostramos el resumen
        this.$("#compras_resumen_total").html("$ "+Number(total).format(2));
        this.$("#compras_resumen_cantidad").html(cantidad)
        this.$("#compras_resumen_neto").html("$ "+Number(neto).format(2));
        this.$("#compras_resumen_iva").html("$ "+Number(iva).format(2));
        this.$("#compras_resumen_reg_especiales").html("$ "+Number(reg_especiales).format(2));
        this.$(".resumen").show();
      } else {
        this.$(".resumen").hide();
      }
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var view = new app.views.ComprasItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#compras_tabla tbody").append(view.render().el);
    },

    exportar: function() {
      var self = this;
      var header = new Array();
      $(".table thead tr th").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t) && t!="Acciones") header.push(t);
      });
      // Acomodamos los datos
      var array = new Array();
      this.$(".table tbody tr").each(function(i,e){
        var obj = {};
        $(e).find(".data").each(function(ii,ee){
          var s = $(ee).text().trim();
          s = s.replace(/\./g,"");
          s = s.replace(/\,/g,".");
          s = s.replace(/\$/g,"");
          s = s.trim();
          obj["s"+ii] = s;
          console.log(s);
        });
        array.push(obj);
      });
      var fecha = (self.$("#compras_movimiento_mes").val() != "00") ? (self.$("#compras_movimiento_mes option:selected").text()+" "+self.$("#compras_movimiento_anio").val()) : "";
      this.exportar_excel({
        "filename":"compras",
        "title":"Listado de Compras",
        "data":array,
        "header":header,
        "date":fecha,
      });
    },

  });

})(app);


// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ComprasItemResultados = Backbone.View.extend({

    template: _.template($("#compras_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click .edit":"editar",
      "click .delete":"borrar",
      "click .checkbox":"seleccionar",
      "click .imprimir":"imprimir",
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
    },
    imprimir: function() {
      workspace.imprimir_reporte("compras/function/imprimir_remito/"+this.model.id);
    },
    editar : function() {
      window.open("app/#compras/"+this.model.id,"_blank");
    },
    borrar : function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
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


(function( app ) {

  app.views.RetencionIIBBView = app.mixins.View.extend({

    template: _.template($("#retencion_iibb_template").html()),
        
    myEvents: {
      "click .exportar":"exportar",
    },

    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template());
      var desde = moment().startOf("month").format("DD/MM/YYYY");
      var hasta = moment().endOf("month").format("DD/MM/YYYY");
      createdatepicker(this.$("#retencion_iibb_desde"),desde);
      createdatepicker(this.$("#retencion_iibb_hasta"),hasta);
    },
    
    exportar : function() {
      var fecha_desde = this.$("#retencion_iibb_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#retencion_iibb_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#retencion_iibb_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#retencion_iibb_hasta").focus();
        return;
      }
      fecha_desde = fecha_desde.replace(/\//g,"-");
      fecha_hasta = fecha_hasta.replace(/\//g,"-");
      var url = "/sistema/compras/function/exportar_retencion_ing_brutos/"+fecha_desde+"/"+fecha_hasta+"/";
      window.open(url,"_blank");
    },
  });

})(app);

// -----------------------------------------
//   VISTA DE PARAMETROS
// -----------------------------------------
(function ( app ) {

  app.views.RetencionGananciasView = app.mixins.View.extend({

    template: _.template($("#retencion_ganancias_template").html()),
    myEvents: {
      "click .generar": "generar",
    },
    initialize: function() {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var self = this;
      $(this.el).html(this.template());
      var desde = moment().startOf("month").format("DD/MM/YYYY");
      var hasta = moment().endOf("month").format("DD/MM/YYYY");
      createdatepicker(this.$("#retencion_ganancias_desde"),desde);
      createdatepicker(this.$("#retencion_ganancias_hasta"),hasta);
      return this;
    },
    generar: function() {
      var self = this;
      var fecha_desde = this.$("#retencion_ganancias_desde").val().replace(/\//g,"-");
      var fecha_hasta = this.$("#retencion_ganancias_hasta").val().replace(/\//g,"-");
      var quincena = (this.$("input[name=2da_quincena]").is(":checked") ? 2:1);
      
      if (isEmpty(fecha_desde)) {
        show("Por favor seleccione una fecha");
        this.$("#retencion_ganancias_desde").focus();
        return;                
      }
      if (isEmpty(fecha_hasta)) {
        show("Por favor seleccione una fecha");
        this.$("#retencion_ganancias_hasta").focus();
        return;                
      }            
      // Lo abrimos en otra pestaña
      var s = "compras/function/exportar_retencion_ganancias";
      s=s+"/"+fecha_desde;
      s=s+"/"+fecha_hasta;
      s=s+"/"+quincena;
      window.open(s,"_blank");
    }
  
  });

})(app);



(function ( app ) {

  app.views.IvaComprasView = app.mixins.View.extend({

    template: _.template($("#iva_compras_template").html()),
      
    myEvents: {
      "click .imprimir":"imprimir",
      "click .citi":"citi",
      "click .citi_comprobantes":"citi_comprobantes",
      "click .citi_alicuotas":"citi_alicuotas",
      "click .iva_excel":"iva_excel",
    },
  
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      var d = new Date();
      $(this.el).html(this.template({
        "anio":d.getFullYear(),
        "mes":d.getMonth(),
      }));
      if (control.check("razones_sociales")>0) {
        new app.mixins.Select({
          modelClass: app.models.RazonSocial,
          url: "razones_sociales/",
          render: "#iva_compras_razones_sociales",
        });
      }
    },
    validar: function() {
      try {
        validate_input("iva_compras_movimiento_anio",IS_EMPTY,"Por favor ingrese un año.");
        return true;
      } catch(e) {
        return false;
      }
    },
    imprimir : function() {
      if (!this.validar()) return;
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var numero = $(this.el).find("#iva_compras_desde").val();
      var cerrar = ($(this.el).find("#iva_compras_cerrar").is(":checked") ? 1:0);
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      workspace.imprimir_reporte("iva/function/compras/"+mes+anio+"/"+cerrar+"/"+numero+"/"+id_razon_social);
    },
    citi: function() {
      if (!this.validar()) return;
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      window.open("compras/function/regimen_informacion/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
      window.open("compras/function/regimen_informacion_alicuotas/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
    },
    citi_comprobantes: function() {
      if (!this.validar()) return;
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      window.open("compras/function/regimen_informacion/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
    },
    citi_alicuotas: function() {
      if (!this.validar()) return;
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      window.open("compras/function/regimen_informacion_alicuotas/"+anio+"/"+mes+"/"+id_razon_social,"_blank");
    },
    iva_excel: function() {
      if (!this.validar()) return;
      var mes = $(this.el).find("#iva_compras_movimiento_mes").val();
      var anio = $(this.el).find("#iva_compras_movimiento_anio").val();
      anio = anio.replace("20","");
      var numero = $(this.el).find("#iva_compras_desde").val();
      var cerrar = ($(this.el).find("#iva_compras_cerrar").is(":checked") ? 1:0);
      var id_razon_social = 0;
      if (control.check("razones_sociales")>0) {
        id_razon_social = $(this.el).find("#iva_compras_razones_sociales").val();
      }
      window.open("iva/function/compras/"+mes+anio+"/"+cerrar+"/"+numero+"/"+id_razon_social+"?excel=1","_blank");
    },        

  });

})(app);


(function ( app ) {

  app.views.CargarCompras = app.mixins.View.extend({

    template: _.template($("#cargar_compras_template").html()),

    myEvents: {
      "click .limpiar": "limpiar",
      "click .guardar": "guardar",

      "change #cargar_compras_tipo":function(e) {
        var id_tipo_comprobante = $(e.currentTarget).val();
        this.model.set({
          "id_tipo_comprobante":id_tipo_comprobante,
        });
        if (id_tipo_comprobante == 999) {
          $("#cargar_compras_porc_iva").val(0);
        }
        this.render_view();
      },

      "keypress #cargar_compras_fecha":function(e) {
        if (e.which == 13) this.$("#cargar_compras_tipo").focus();
      },
      "keypress #cargar_compras_tipo":function(e) {
        if (e.which == 13) {
          e.preventDefault();
          this.$("#cargar_compras_numero_1").focus();
          return false;
        }
      },
      "keypress #cargar_compras_numero_1":function(e) {
        if (e.which == 13) this.$("#cargar_compras_numero_2").focus();
      },
      "keypress #cargar_compras_numero_2":function(e) {
        if (e.which == 13) this.$("#cargar_compras_concepto_codigo").focus();
      },


      "keypress #cargar_compras_perc_ing_brutos":function(e) {
        if (e.which == 13) this.$("#cargar_compras_perc_iva").select();
      },
      "keypress #cargar_compras_perc_iva":function(e) {
        if (e.which == 13) this.$("#cargar_compras_perc_agip").select();
      },
      "keypress #cargar_compras_perc_agip":function(e) {
        if (e.which == 13) this.$("#cargar_compras_impuesto_interno").select();
      },
      "keypress #cargar_compras_impuesto_interno":function(e) {
        if (e.which == 13) this.$("#cargar_compras_no_gravado").select();
      },
      "keypress #cargar_compras_no_gravado":function(e) {
        if (e.which == 13) this.$("#cargar_compras_exento").select();
      },
      "keypress #cargar_compras_exento":function(e) {
        if (e.which == 13) this.$(".guardar").focus();
      },


      "keypress .enterToNext":function(e) {
        if (e.which != 13) return;
        var tabindex = parseInt($(e.currentTarget).attr("tabindex"));
        $("*[tabindex="+(tabindex+1)+"]").select();
      },
      "keypress #cargar_compras_neto":function(e) {
        if (e.which == 13) this.$("#cargar_compras_agregar_iva").focus();  
      },

      // Buscamos el proveedor por codigo
      "keypress #cargar_compras_codigo_proveedor": function(e) { if (e.keyCode == 13) { this.buscar_proveedor(); } },
      "click #cargar_compras_buscar_proveedor": "abrir_busqueda_proveedor",

      "click #cargar_compras_buscar_conceptos": "abrir_busqueda_concepto",
      
      // Buscamos el concepto por codigo
      "keypress #cargar_compras_concepto_codigo": function(e) { if (e.keyCode == 13) { this.buscar_concepto(); } },
      
      // Rellenamos con ceros los numeros de comprobante
      "focusout #cargar_compras_numero_1": function(e) {
        var valor = $(e.currentTarget).val();
        if (!isInteger(valor)) {
          $(e.currentTarget).select();
          return false;
        }
        $(e.currentTarget).val(zeroFill(valor,4));
        return true;
      },

      "focusout #cargar_compras_numero_2": function(e) {
        var self = this;
        var valor = $(e.currentTarget).val();
        if (!isInteger(valor)) {
          //show("Por favor ingrese un numero.");
          $(e.currentTarget).select();
          return false;
        }
        $(e.currentTarget).val(zeroFill(valor,8));

        // Chequear si el comprobante existe
        var id_proveedor = this.$("#cargar_compras_id_proveedor").val();
        var id_tipo_comp = this.$("#cargar_compras_tipo").val();
        
        // Si se selecciono proveedor y no es un remito
        if (!isEmpty(id_proveedor) && id_tipo_comp != 999) {
          
          var numero_1 = this.$("#cargar_compras_numero_1").val();
          var numero_2 = this.$("#cargar_compras_numero_2").val();
          $.ajax({
            "url" : "compras/function/comprobar_existe_comprobante/",
            "data":{
              "id_proveedor":id_proveedor,
              "id_tipo_comprobante":id_tipo_comp,
              "numero_1":numero_1,
              "numero_2":numero_2,
            },
            "type":"post",
            "dataType": "json",
            "success":function(e) {
              if (e.error == 1) show(e.mensaje);
            }
          });
        }
        return true;
      },

      // Controlamos que el campo ingresado sea numerico
      "focusout .numerico": "es_numero",
      
      // ABM de las filas de la tabla de netos
      "click #cargar_compras_agregar_iva": "agregar_fila_iva",
      "click .editar_fila_neto": "modificar_fila_iva",
      "click .eliminar_fila_neto": "eliminar_fila_iva",
      
      // Al cambiar la forma de pago, lo persistimos en el modelo
      "change #cargar_compras_forma_pago":function(e){
        var forma_pago = $(e.currentTarget).val();
        this.model.set({
          "forma_pago":forma_pago
        });
        // Si la forma de pago es en Cuenta Corriente, no elige la caja
        // porque se elige al momento de preparar el pago
        if (forma_pago == "C") {
          this.$("#cargar_compras_cajas").val(0);
          this.$("#cargar_compras_cajas").attr("disabled","disabled");
        } else {
          this.$("#cargar_compras_cajas").removeAttr("disabled");
        }
        this.render_view();
      },

      "change #cargar_compras_cajas":function(e) {
        this.model.set({
          "id_caja":$(e.currentTarget).val()
        });
      },

      "change #cargar_compras_porc_iva":function(e) {
        var porc_iva = parseFloat(this.$("#cargar_compras_porc_iva option:selected").data("porcentaje"));
        var neto = parseFloat(this.$("#cargar_compras_neto").val());
        var porc_dto = parseFloat(this.$("#cargar_compras_porcentaje_descuento").val());
        var descuento = parseFloat(neto*(porc_dto/100));
        var iva = ((neto-descuento) * (porc_iva/100));
        iva = redondear(iva);
        var descuento = neto-descuento;
        if (descuento == 0) descuento = "";
        this.$("#cargar_compras_iva").val(iva);
        this.$("#cargar_compras_descuento").val(descuento);            
      },    
    },

    initialize: function(options) {
      var self = this;
      this.guardando = 0;
      this.options = options;

      _.bindAll(this);
      this.bind("limpiar",this.limpiar);

      // Creamos una estructura plana para poder utilizarlo en el buscador
      window.tipos_gastos_plana = workspace.flatten(window.tipos_gastos);
      if (window.tipos_gastos_plana == null) window.tipos_gastos_plana = new Array();
      
      // Estamos creando uno nuevo
      if (this.model.id == undefined || this.model.id == 0) {
        this.render();

      // Estamos editando
      } else {

        this.listenTo(this.model,"change",this.render_view); // Si el modelo cambia, renderizamos la vista
        
        this.render();
        
        // Buscamos el cliente y lo seteamos
        var id_proveedor = self.model.get("id_proveedor");
        var proveedor = new app.models.Proveedor({"id":id_proveedor});
        proveedor.fetch({
          "success":function() {
            self.seleccionar_proveedor(proveedor);        
          },
        });
      }
    },
        
    render: function() {

      var self = this;
      var obj = { id: this.model.id }
      obj = _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // AUTOCOMPLETE DE PROVEEDORES
      // ---------------------------
      var input = this.$("#cargar_compras_codigo_proveedor");
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

      // AUTOCOMPLETE DE CONCEPTOS
      // -------------------------
      var input = this.$("#cargar_compras_concepto_codigo");
      var form = new app.views.TipoGastoMiniEditView({
        "model": new app.models.TipoGasto(),
        "input": input,
        "onSave": function(item) {
          self.seleccionar_concepto(item.toJSON());
        },
      });
      $(input).customcomplete({
        "array":window.tipos_gastos_plana,
        "label":"[nombre]",
        "id":"nombre",
        "width":300,
        "form":form,
        "onSelect":function(item){
          self.seleccionar_concepto(item.element);
        }
      });
      
      var fecha = "";
      if (typeof this.model.id == "undefined") {
        fecha = $.datepicker.formatDate("dd/mm/yy",new Date());
        var mes = fecha.substr(3,2);
        var anio = "20"+fecha.substr(8,2);
      } else {
        fecha = this.model.get("fecha");
        var mes = this.model.get("movimiento").substr(0,2);
        var anio = "20"+this.model.get("movimiento").substr(2,2);
      }
      this.$("#cargar_compras_movimiento_mes").val(mes);
      this.$("#cargar_compras_movimiento_anio").val(anio);
      this.$("#cargar_compras_observaciones").val(this.model.get("observaciones"));

      if (isEmpty(this.model.get("fecha"))) {
        this.model.set("fecha",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#cargar_compras_fecha"),this.model.get("fecha"));
      return this;
    },
    
    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.model.set({
        "id_proveedor": self.proveedor.id,
        "codigo_proveedor":self.proveedor.get("codigo"),
        "tipo_iva_proveedor":self.proveedor.get("tipo_iva"),
        "nombre_proveedor":self.proveedor.get("nombre"),
        "cuit_proveedor":self.proveedor.get("cuit"),
        "direccion_proveedor":self.proveedor.get("direccion"),
        "porc_ret_ib":self.proveedor.get("porc_ret_ib"),
        "aplica_ret_ganancias":self.proveedor.get("aplica_ret_ganancias"),
      });
      self.$("#cargar_compras_id_proveedor").val(self.proveedor.id);
      self.$("#cargar_compras_codigo_proveedor").val(self.proveedor.get("nombre"));

      self.$("#cargar_compras_proveedor_nombre_factura").text(self.proveedor.get("nombre"));
      self.$("#cargar_compras_proveedor_direccion_factura").text(self.proveedor.get("direccion"));
      self.$("#cargar_compras_proveedor_tipo_contribuyente_factura").text(self.proveedor.get("tipo_iva"));
      self.$("#cargar_compras_proveedor_cuit_factura").text("CUIT/DNI: "+self.proveedor.get("cuit"));

      self.chequear_comprobantes();
      self.$("#cargar_compras_tipo").change();
      self.render_view();

      // Para cerrar el customcomplete que se abre
      setTimeout(function(){
        self.$('#cargar_compras_codigo_proveedor').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    // Controla los comprobantes que puede realizar
    chequear_comprobantes : function() {

      var self = this;
      if (self.proveedor == null || self.proveedor == undefined) return;
      var iva_proveedor = self.proveedor.get("id_tipo_iva");
      var habilitados = [];
      this.$("#cargar_compras_tipo").empty();        

      if (iva_proveedor == 2) {
        habilitados = [11,12,13,15];
      } else if (iva_proveedor == 1){
        if (ID_TIPO_CONTRIBUYENTE == 1) {
          habilitados = [1,2,3,4,51,52,53,201,202,203];
        } else {
          habilitados = [6,7,8,9,206,207,208];
        }
      } else {
        habilitados = [1,2,3,4,6,7,8,9,11,12,13,15,51,52,53,201,202,203,206,207,208];
      }

        // Habilitamos el Remito y el Presupuesto
        //if (ESTADO == 1) {
          habilitados.push(999);
        //}

        for(var i=0;i<comprobantes.length;i++) {
          var c = comprobantes[i];
          var encontro = false;
          for (j=0;j<habilitados.length;j++) {
            var o = habilitados[j];
            if (c.id == o) {
              encontro = true;
              break;
            }
          }
          if (encontro) {
            var option = "<option ";
            option+="value='"+c.id+"' ";
            if (this.model.id != undefined && this.model.id != 0) option+=( c.id == this.model.get("id_tipo_comprobante") )?"selected ":"";
            option+= ">"+c.nombre;
            option+="</option>";
            this.$("#cargar_compras_tipo").append(option);
          }
        }
      },

      render_view: function() {
        // Mostramos el nombre de comprobante que corresponde
        var id_tipo_comprobante = parseInt(this.model.get("id_tipo_comprobante"));
        switch (id_tipo_comprobante) {
          case 1:
          $(".invoice-type").html("Factura"); $(".letter").html("A"); break;
          case 2:
          $(".invoice-type").html("Nota de D&eacute;bito"); $(".letter").html("A"); break;
          case 3:
          $(".invoice-type").html("Nota de Cr&eacute;dito"); $(".letter").html("A"); break;
          case 4:
          $(".invoice-type").html("Recibo"); $(".letter").html("A"); break;
          case 6:
          $(".invoice-type").html("Factura"); $(".letter").html("B"); break;
          case 7:
          $(".invoice-type").html("Nota de D&eacute;bito"); $(".letter").html("B"); break;
          case 8:
          $(".invoice-type").html("Nota de Cr&eacute;dito"); $(".letter").html("B"); break;
          case 9:
          $(".invoice-type").html("Recibo"); $(".letter").html("B"); break;
          case 11:
          $(".invoice-type").html("Factura"); $(".letter").html("C"); break;
          case 12:
          $(".invoice-type").html("Nota de D&eacute;bito"); $(".letter").html("C"); break;
          case 13:
          $(".invoice-type").html("Nota de Cr&eacute;dito"); $(".letter").html("C"); break;
          case 15:
          $(".invoice-type").html("Recibo"); $(".letter").html("C"); break;
          case 19:
          this.$(".invoice-type").html("Factura"); this.$(".letter").html("E"); break;
          case 20:
          this.$(".invoice-type").html("Nota de D&eacute;bito"); this.$(".letter").html("E"); break;
          case 21:
          this.$(".invoice-type").html("Nota de Cr&eacute;dito"); this.$(".letter").html("E"); break;
          case 51:
          $(".invoice-type").html("Factura M"); $(".letter").html("M"); break;
          case 52:
          $(".invoice-type").html("Nota de D&eacute;bito M"); $(".letter").html("M"); break;
          case 53:
          $(".invoice-type").html("Nota de Cr&eacute;dito M"); $(".letter").html("M"); break;
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
          $(".invoice-type").html("Presupuesto"); $(".letter").html("X"); break;
          case 999:
          $(".invoice-type").html("Remito"); $(".letter").html("X"); break;
        }

        if (this.model.get("forma_pago") == "E") {
          this.$("#cargar_compras_forma_pago_factura").text("Efectivo");
        } else if (this.model.get("forma_pago") == "C") {
          this.$("#cargar_compras_forma_pago_factura").text("Cuenta Corriente");
        }
      },


      buscar_proveedor : function() {

        var self = this;
        
        var codigo = this.$("#cargar_compras_codigo_proveedor").val();
        if (isEmpty(codigo)) {
          codigo = 0;
          this.$("#cargar_compras_codigo_proveedor").val(codigo);
        }
        // Buscamos el cliente por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
        codigo = parseInt(codigo);
        if (codigo == 0) return;
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
                self.$("#cargar_compras_codigo_proveedor").select();
                self.$("#cargar_compras_codigo_proveedor").focus();
                return;
              }
              var proveedor = new app.models.Proveedor(r);
              self.seleccionar_proveedor(proveedor);
              self.$("#cargar_compras_fecha").focus();
            }
          });
        }
        this.$("#cargar_compras_fecha").focus();    
      },


      buscar_concepto : function() {
        var self = this;
        var codigo = this.$("#cargar_compras_concepto_codigo").val();
        if (isEmpty(codigo)) return;
        codigo = codigo.toUpperCase();
        var concepto = _.find(window.tipos_gastos_plana,function(e){
          return (e.codigo.toUpperCase() == codigo || e.nombre.toUpperCase() == codigo);
        });
        if (typeof concepto == "undefined") {
          show("No existe un concepto con el codigo ingresado.");
          self.$("#cargar_compras_concepto_nombre").val("");
          self.$("#cargar_compras_concepto_id").val("");
          self.$("#cargar_compras_concepto_codigo").val("");
          self.$("#cargar_compras_concepto_codigo").select();
        } else {
          self.seleccionar_concepto(concepto);
        }
      },

      seleccionar_concepto: function(concepto) {
        this.$("#cargar_compras_concepto_nombre").val(concepto.nombre);
        this.$("#cargar_compras_concepto_id").val(concepto.id);
        this.$("#cargar_compras_concepto_codigo").val(concepto.nombre);
        if (this.$("#cargar_compras_tipo").val()==999) {
          this.$("#cargar_compras_porc_iva").val(0);
        } else {
          this.$("#cargar_compras_porc_iva").val(concepto.id_tipo_alicuota_iva);    
        }
        this.$("#cargar_compras_neto").select();

        // Para cerrar el customcomplete que se abre
        setTimeout(function(){
          self.$('#cargar_compras_concepto_codigo').trigger(jQuery.Event('keyup', {which: 27}));
        },500);
      },

      abrir_busqueda_proveedor : function() {

        var self = this;
        var proveedores = new app.collections.Proveedores();
        var view = new app.views.ProveedoresTableView({
         collection: proveedores,
         habilitar_seleccion: true,
         permiso: 1
       });
        crearLightboxHTML({
          "html":view.el,
          "width":800,
          "height":350,
          "callback":function() {
            self.seleccionar_proveedor(window.proveedor_seleccionado);
            self.$("#cargar_compras_fecha").focus();                
          }
        });
        $(".basic_search").select();
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
            if (typeof window.gasto_seleccionado != "undefined") { 
              self.$("#cargar_compras_concepto_codigo").val(window.gasto_seleccionado.get("nombre"));
              // Ahora con el codigo, buscamos el concepto
              self.buscar_concepto();
              // Enviamos el foco al neto
              self.$("#cargar_compras_neto").select();
            }
          },
        });
      },  

      limpiar : function() {

        // Tomamos los datos que queremos que no se limpien
        /*
        var id_proveedor = this.model.get("id_proveedor");
        var numero_1 = this.model.get("numero_1");
        var codigo_proveedor = this.model.get("codigo_proveedor");
        var nombre_proveedor = this.model.get("nombre_proveedor");
        var cuit_proveedor = this.model.get("cuit_proveedor");
        var tipo_iva_proveedor = this.model.get("tipo_iva_proveedor");
        */

        // Creamos un nuevo modelo, pero le ponemos los datos anteriores
        /*this.model = new app.models.Compra({
            //"id_proveedor":id_proveedor,
            //"numero_1":numero_1,
            //"codigo_proveedor":codigo_proveedor,
            //"cuit_proveedor":cuit_proveedor,
            //"nombre_proveedor":nombre_proveedor,
            //"tipo_iva_proveedor":tipo_iva_proveedor,
            "netos":[],
          });*/
        /*
        this.model = new app.models.Compra();
        this.render();
        this.$("#cargar_compras_codigo_proveedor").select();
        */
        location.reload();
      },

      es_numero:function(e) {
        var valor = $(e.currentTarget).val();
        
        if (isEmpty(valor)) {
          valor = "0";
          $(e.currentTarget).val("0");
        }
        
        if (!(isInteger(valor) || isDecimal(valor))) {
          show("Por favor ingrese un numero.");
          $(e.currentTarget).select();
        }
        
        // FOCUSOUT ESPECIALES
        var id = $(e.currentTarget).attr("id");
        
        // Si salimos del IVA, lo calculamos y agregamos
        // al total para agregar a la fila
        if (id == "cargar_compras_porc_iva" || id == "cargar_compras_neto" || id == "cargar_compras_porcentaje_descuento") {
          var porc_iva = parseFloat(this.$("#cargar_compras_porc_iva option:selected").data("porcentaje"));
          var neto = parseFloat(this.$("#cargar_compras_neto").val());
          var porc_dto = parseFloat(this.$("#cargar_compras_porcentaje_descuento").val());
          var descuento = parseFloat(neto*(porc_dto/100));
          var iva = ((neto-descuento) * (porc_iva/100));
          iva = redondear(iva);
          var descuento = neto-descuento;
          if (descuento == 0) descuento = "";
          this.$("#cargar_compras_iva").val(iva);
          this.$("#cargar_compras_descuento").val(descuento);
        }
        else if (id == "cargar_compras_perc_ing_brutos" ||
          id == "cargar_compras_perc_iva" ||
          id == "cargar_compras_perc_agip" ||
          id == "cargar_compras_perc_san_luis" ||
          id == "cargar_compras_impuesto_interno" ||
          id == "cargar_compras_no_gravado" ||
          id == "cargar_compras_exento") {

          var total = 0;
        var perc_ing_brutos = parseFloat(this.$("#cargar_compras_perc_ing_brutos").val());
        var perc_iva = parseFloat(this.$("#cargar_compras_perc_iva").val());
        var perc_agip = parseFloat(this.$("#cargar_compras_perc_agip").val());
        var perc_san_luis = parseFloat(this.$("#cargar_compras_perc_san_luis").val());
        var impuesto_interno = parseFloat(this.$("#cargar_compras_impuesto_interno").val());
        var no_gravado = parseFloat(this.$("#cargar_compras_no_gravado").val());
        var exento = parseFloat(this.$("#cargar_compras_exento").val());
        total = perc_ing_brutos + perc_iva + impuesto_interno + exento + no_gravado + perc_agip + perc_san_luis;
        
        this.$("#cargar_compras_subtotal_regimenes").val(redondear(total));

        this.model.set({
          "perc_ing_brutos":perc_ing_brutos,
          "perc_iva":perc_iva,
          "perc_agip":perc_agip,
          "perc_san_luis":perc_san_luis,
          "impuesto_interno":impuesto_interno,
          "no_gravado":no_gravado,
          "exento":exento,
          "total_regimenes_especiales":total
        });
        this.calcular_totales();
      }
      else if (id == "cargar_compras_total_general") {
        var total_general = parseFloat(this.$("#cargar_compras_total_general").val());
        this.model.set({
          "total_general":total_general
        });
      }
      else if (id == "cargar_compras_porcentaje_descuento") {
        var porcentaje_descuento = valor;

        this.model.set({
          "porcentaje_descuento":porcentaje_descuento,
          "descuento":descuento,
        });
        this.$("#cargar_compras_descuento").val(redondear(descuento));
        this.calcular_totales_fila_iva();
      }
    },

    agregar_fila_iva: function() {

      var neto = this.$("#cargar_compras_neto").val();
      var neto_dto = this.$("#cargar_compras_descuento").val();
      var porc_dto = this.$("#cargar_compras_porcentaje_descuento").val();
      var porc_iva = this.$("#cargar_compras_porc_iva option:selected").data("porcentaje");
      var id_tipo_alicuota_iva = this.$("#cargar_compras_porc_iva").val();
      var iva = this.$("#cargar_compras_iva").val();
      var subtotal = parseFloat(neto_dto) + parseFloat(iva);

      if (isEmpty(neto) || neto==0) {
        this.$("#cargar_compras_neto").select();
        show("Por favor ingrese un valor neto.");
        return;
      }

      var id_concepto = this.$("#cargar_compras_concepto_id").val();
      var codigo_concepto = this.$("#cargar_compras_concepto_codigo").val();
      var nombre_concepto = this.$("#cargar_compras_concepto_nombre").val();

      if (isEmpty(nombre_concepto)) {
        show("Por favor ingrese un concepto.");
        this.$("#cargar_compras_concepto_codigo").select();
        return;
      }

      var netos = this.model.get("netos");      

      var id = this.$("#cargar_compras_netos_id").val();
      if (id == "-1") {
            // Estamos cargando una nueva fila
            
            id = this.$("#cargar_compras_netos_table tbody tr").length;
            
            // Agregamos el elemento en el array
            var o = {
              "id": id,
              "id_tipo_alicuota_iva":id_tipo_alicuota_iva,
              "id_concepto":id_concepto,
              "neto":parseFloat(neto),
              "neto_dto":parseFloat(neto_dto),
              "porc_dto":parseFloat(porc_dto),
              "porc_iva":parseFloat(porc_iva),
              "iva":parseFloat(iva),
              "nombre_concepto":nombre_concepto,
              "codigo_concepto":codigo_concepto,
            };
            netos.push(o);
            
            // Agregamos el elemento en la tabla
            var tr = "<tr id='fila"+id+"'>";
            tr=tr+"<td>"+nombre_concepto+"</td>";
            tr=tr+"<td class='tar'>"+redondear(neto_dto)+"</td>";
            tr=tr+"<td class='tar'>"+redondear(porc_iva)+"</td>";
            tr=tr+"<td class='tar'>"+redondear(iva)+"</td>";
            tr=tr+"<td class='tar'>"+redondear(subtotal)+"</td>";
            tr=tr+"<td><i class='fa fa-file-text-o editar_fila_neto text-dark' /></td>";
            tr=tr+"<td><i class='glyphicon glyphicon-remove eliminar_fila_neto text-danger' /></td>";
            tr=tr+"</tr>";
            this.$("#cargar_compras_netos_table tbody").append(tr);

          } else {
            // Estamos editando una fila
            
            // Actualizamos el array
            _.each(this.model.get("netos"),function(e){
              if (e.id == id) {
                e.neto = parseFloat(neto);
                e.neto_dto = parseFloat(neto_dto);
                e.porc_dto = parseFloat(porc_dto);
                e.porc_iva = parseFloat(porc_iva);
                e.iva = parseFloat(iva);
                e.id_concepto = id_concepto;
                e.nombre_concepto = nombre_concepto;
                e.codigo_concepto = codigo_concepto;
              }
            });
            
            // Actualizamos la vista
            this.$("#fila"+id+" td:eq(0)").text(nombre_concepto);
            this.$("#fila"+id+" td:eq(1)").text(Number(neto_dto).toFixed(2));
            this.$("#fila"+id+" td:eq(2)").text(Number(porc_iva).toFixed(2));
            this.$("#fila"+id+" td:eq(3)").text(Number(iva).toFixed(2));
            this.$("#fila"+id+" td:eq(4)").text(Number(subtotal).toFixed(2));
          }

          this.limpiar_fila_iva();
          this.calcular_totales_fila_iva();

          this.$("#cargar_compras_perc_ing_brutos").select();
        },

        limpiar_fila_iva: function() {
          this.$("#cargar_compras_neto").val("0");
          if (this.$("#cargar_compras_tipo").val() == 999) {
            this.$("#cargar_compras_porc_iva").val(0);    
          } else {
            this.$("#cargar_compras_porc_iva").val(5);    
          }
          this.$("#cargar_compras_iva").val("0");
          this.$("#cargar_compras_porcentaje_descuento").val("0");
          this.$("#cargar_compras_descuento").val("0");
          this.$("#cargar_compras_netos_id").val("-1");
        },
        
        modificar_fila_iva: function(e) {

          var id = $(e.currentTarget).parents("tr").attr("id");
          id = id.replace("fila","");
          var o = _.filter(this.model.get("netos"),function(o){
            return (o.id == id);
          });

        o = o[0]; // Nos devuelve un array

        // Marcamos que estamos editando una fila, para que no agregue otra
        this.$("#cargar_compras_netos_id").val(o.id);
        
        // Cargamos todos los valores en los campos
        this.$("#cargar_compras_neto").val(redondear(o.neto));
        this.$("#cargar_compras_porc_iva").val(o.id_tipo_alicuota_iva);
        this.$("#cargar_compras_iva").val(redondear(o.iva));
        this.$("#cargar_compras_porcentaje_descuento").val(redondear(o.porc_dto));
        this.$("#cargar_compras_descuento").val(redondear(o.neto_dto));
        this.$("#cargar_compras_concepto_codigo").val(o.codigo_concepto);
        this.$("#cargar_compras_concepto_nombre").val(o.nombre_concepto);
        
        // Ponemos el foco en el NETO
        this.$("#cargar_compras_neto").select();

      },

      eliminar_fila_iva : function(e) {
        var id = $(e.currentTarget).parents("tr").attr("id");

        // Eliminamos la fila de la vista
        this.$("#"+id).remove();
        
        // Lo borramos del array de netos
        id = id.replace("fila","");
        var a = _.filter(this.model.get("netos"),function(o){
          return (o.id != id);
        });
        this.model.set({ "netos":a });
        
        // Volvemos a calcular los totales
        this.calcular_totales_fila_iva();
      },

      calcular_totales_fila_iva : function() {

        // Calculamos los totales de la tabla
        var total_neto = 0;
        var total_iva = 0;
        for (var i = 0; i<this.model.get("netos").length; i++) {
          var fila = this.model.get("netos")[i];
          total_neto = parseFloat(total_neto) + parseFloat(fila.neto_dto);
          total_iva = parseFloat(total_iva) + parseFloat(fila.iva);
        }
        
        // Calculamos el descuento
        var subtotal_iva = (total_iva + total_neto);
        
        this.model.set({
          "total_neto":total_neto,
          "total_iva":total_iva,
          "subtotal":subtotal_iva,
        });
        
        this.$("#cargar_compras_neto_total").val(redondear(total_neto));
        this.$("#cargar_compras_iva_total").val(redondear(total_iva));
        this.$("#cargar_compras_subtotal").val(redondear(subtotal_iva));
        
        this.calcular_totales();
      },

      calcular_totales : function() {
        var total = parseFloat(this.model.get("subtotal")) + parseFloat(this.model.get("total_regimenes_especiales"));
        this.$("#cargar_compras_total_general").val(redondear(total));
        this.model.set({
          "total_general":total
        });
      },

      validar : function() {
        var self = this;
        try {
          // Controlamos el proveedor
          if (this.model.get("id_proveedor") == 0) {
            show("Por favor seleccione un proveedor.");
            this.$("#cargar_compras_codigo_proveedor").select();
            return false;
          }

          // Si el pago es en efectivo, tenemos que si o si seleccionar una caja
          if (this.$("#cargar_compras_forma_pago").val() == "E" && this.$("#cargar_compras_cajas").length > 0 && this.$("#cargar_compras_cajas").val() == 0) {
            show("Si el pago es en efectivo, por favor seleccione una caja.");
            this.$("#cargar_compras_cajas").select();
            return false;
          }

          for (var i = 0; i<this.model.get("netos").length; i++) {
            var fila = this.model.get("netos")[i];
            if (fila.id_concepto == 0) {
              // Control para que no guarde id_concepto en cero por las dudas
              alert("Por favor seleccione un concepto de la lista: "+fila.nombre_concepto);
              return false;
            }
          }
          
          var numero_1 = this.$("#cargar_compras_numero_1").val();
          var numero_2 = this.$("#cargar_compras_numero_2").val();
          if ($("#cargar_compras_tipo").val() != 2) {
            // Controlamos los numeros de factura
            if (numero_1 == "0000" || isEmpty(numero_1)) {
              show("Por favor ingrese un numero de factura.");
              this.$("#cargar_compras_numero_1").select();
              return false;
            }
            if (numero_2 == "00000000" || isEmpty(numero_2)) {
              show("Por favor ingrese un numero de factura.");
              this.$("#cargar_compras_numero_2").select();
              return false;
            }
          }

          // Controlamos la fecha
          var fecha = this.$("#cargar_compras_fecha").val();
          if (isEmpty(fecha)) {
            show("Por favor ingrese una fecha.");
            this.$("#cargar_compras_fecha").select();
            return false;
          }
          
          // Numero de movimiento
          var mes = this.$("#cargar_compras_movimiento_mes").val();
          var anio = this.$("#cargar_compras_movimiento_anio").val().substr(2,2);
          var movimiento = String(mes)+String(anio);
          var id_tipo_comprobante = self.$("#cargar_compras_tipo").val();
          
          // Agregar al libro de iva
          if (this.$("input[name=incluido_libro_iva]").length > 0) {
            var incluido_libro_iva = this.$("input[name=incluido_libro_iva]").is(":checked") ? 1 : 0;
          } else {
            var incluido_libro_iva = (id_tipo_comprobante < 900) ? 1 : 0;
          }

          // Controlamos que se haya ingresado al menos un concepto
          if (this.model.get("netos").length == 0) {
            show("Por favor ingrese al menos un concepto al comprobante.");
            return false;
          }

          if (this.$("#cargar_compras_sucursales").length > 0) {
            var id_sucursal = this.$("#cargar_compras_sucursales").val();
            if (id_sucursal == null) id_sucursal = 0;
            this.model.set({
              "id_sucursal":id_sucursal,
            });
          } else {
            this.model.set({
              "id_sucursal":ID_SUCURSAL,
            });            
          }
          
          // Seteamos en el modelo los valores que faltan
          this.model.set({
            "id_tipo_comprobante": id_tipo_comprobante,
            "numero_1": numero_1,
            "numero_2": numero_2,
            "proveedor": self.$("#cargar_compras_codigo_proveedor").val(),
            "fecha":fecha,
            "movimiento":movimiento,
            "observaciones": self.$("#cargar_compras_observaciones").val(),
            "incluido_libro_iva":incluido_libro_iva,
            "compra_real":((this.$("input[name=compra_real]").length > 0) ? (this.$("input[name=compra_real]").is(":checked") ? 1 : 0) : 1),
            "ver_en_cuenta":((this.$("input[name=ver_en_cuenta]").length > 0) ? (this.$("input[name=ver_en_cuenta]").is(":checked") ? 1 : 0) : 1),
            "id_empresa":ID_EMPRESA,
            "id_caja":((this.$("#cargar_compras_cajas").length > 0) ? this.$("#cargar_compras_cajas").val() : 0),
          });
          
          return true;
        } catch(e) {
          return false;
        }
      },

      guardar : function() {
        var self = this;
        if (this.validar()) {      
          this.model.save({},{
            success: function(model,response) {
              if (response.error == "0") {
                self.limpiar();
              } else {
                show(response.mensaje);
              }
            }
          });
        }
      }
      
    });

})(app);