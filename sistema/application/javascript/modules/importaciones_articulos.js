// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ImportacionArticulo = Backbone.Model.extend({
    urlRoot: "importaciones_articulos/",
    defaults: {
      fecha_alta: "",
      fecha_modif: "",
      id_usuario: ID_USUARIO,
      id_proveedor: 0,
      proveedor: "",
      usuario: "",
      id_empresa: ID_EMPRESA,
      observaciones: "",
      eliminado: 0,
      moneda: "$",
    }
  });

})( app.models );



(function ( models ) {

  models.ImportacionArticuloItem = Backbone.Model.extend({
    urlRoot: "importaciones_articulos/",
    defaults: {
      id_importacion: 0,
      id_articulo: 0,
      id_empresa: ID_EMPRESA,
      codigo: "",
      codigo_prov: "",
      nombre: "",
      costo_neto: 0,
      costo_final: 0,
      id_tipo_alicuota_iva: 0,
      porc_iva: 0,
      modif_costo_1: 0,
      modif_costo_2: 0,
      modif_costo_3: 0,
      modif_costo_4: 0,
      modif_costo_5: 0,
      id_moneda: 1,
      coeficiente: 0,
      modif_precio_1: 0,
      modif_precio_2: 0,
      modif_precio_3: 0,
      modif_precio_4: 0,
      modif_precio_5: 0,
      costo_anterior: 0,
      orden: 0,
      fecha_modif: "",
      estado: 0,
      precio_neto: 0,
      eliminado: 0,
      agregado: 0,
      bulto: 0,
      cantidad:1,
    }
  });

})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.ImportacionesArticulos = paginator.requestPager.extend({

    model: model,

    paginator_ui: {
      perPage: 10,
    },        

    paginator_core: {
      url: "importaciones_articulos/function/consulta/",
    },
  });

})( app.collections, app.models.ImportacionArticulo, Backbone.Paginator);


(function ( app ) {

  app.views.ImportacionArticuloEditView = app.mixins.View.extend({

    template: _.template($("#importacion_articulos_template").html()),
      
    myEvents: {
      "click .guardar":"guardar",
      "click .agregar_fila":"agregar_fila_nuevos",
    },
    
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
    },

    agregar_fila_nuevos: function() {
      var self = this;
      var r = {
        codigo: "",
        codigo_prov: "",
        coeficiente: 1,
        cantidad: 1,
        costo_anterior: "0.00",
        costo_final: "0.00",
        costo_neto: "0.00",
        costo_neto_inicial: "",
        costo_neto_inicial_dolar: "",
        eliminado: "0",
        estado: "1",
        fecha_modif: "",
        fue_modificado: "0",
        id_articulo: "0",
        id_empresa: ID_EMPRESA,
        id_importacion: "",
        id_moneda: "0",
        id: 0,
        id_tipo_alicuota_iva: "0",
        modif_costo_1: "0.00",
        modif_costo_2: "0.00",
        modif_costo_3: "0.00",
        modif_costo_4: "0.00",
        modif_costo_5: "0.00",
        modif_precio_1: "0.00",
        modif_precio_2: "0.00",
        modif_precio_3: "0.00",
        modif_precio_4: "0.00",
        modif_precio_5: "0.00",
        modifico_costo: 0,
        nombre: "",
        orden: "0",
        porc_ganancia: "0.00",
        porc_iva: "0.000",
        precio_final: "0.00",
        precio_neto: "0.00",
        tipo_modif: "",
        agregado: 1,
        bulto: 0,
      };
      var tr = new app.views.ImportacionArticulosItemView({
        model: new app.models.ImportacionArticuloItem(r),
        prefijo: "importacion_articulos_nuevos",
      });
      this.$("#importacion_articulos_nuevos tbody").append(tr.el);      
      this.insertados.push(tr);
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 

      this.insertados = new Array(); // Array donde se guardan los objetos insertados

      this.$("#importacion_articulos_nuevos tbody").empty();
      var nuevos = this.model.get("nuevos");
      console.log(nuevos);
      for(var i=0;i<nuevos.length;i++) {
        var r = nuevos[i];
        var tr = new app.views.ImportacionArticulosItemView({
          model: new app.models.AbstractModel(r),
          moneda: self.model.get("moneda"),
          prefijo: "importacion_articulos_nuevos",
        });
        this.$("#importacion_articulos_nuevos tbody").append(tr.el);
      }

      this.$("#importacion_articulos_modificaciones tbody").empty();
      var modificados = this.model.get("modificados");
      for(var i=0;i<modificados.length;i++) {
        var r = modificados[i];
        var tr = new app.views.ImportacionArticulosItemView({
          model: new app.models.AbstractModel(r),
          moneda: self.model.get("moneda"),
          prefijo: "importacion_articulos_modificaciones",
        });
        this.$("#importacion_articulos_modificaciones tbody").append(tr.el);
      }

      this.$("#importacion_articulos_no_modificados tbody").empty();
      var no_modificados = this.model.get("no_modificados");
      for(var i=0;i<no_modificados.length;i++) {
        var r = no_modificados[i];
        var tr = new app.views.ImportacionArticulosItemView({
          model: new app.models.AbstractModel(r),
          moneda: self.model.get("moneda"),
          prefijo: "importacion_articulos_no_modificados",
        });
        this.$("#importacion_articulos_no_modificados tbody").append(tr.el);
      }

      this.$("#importacion_articulos_eliminados tbody").empty();
      var eliminados = this.model.get("eliminados");
      for(var i=0;i<eliminados.length;i++) {
        var r = eliminados[i];
        var tr = new app.views.ImportacionArticulosItemView({
          model: new app.models.AbstractModel(r),
          moneda: self.model.get("moneda"),
          prefijo: "importacion_articulos_eliminados",
        });
        this.$("#importacion_articulos_eliminados tbody").append(tr.el);
      }
    },

    guardar: function() {
      var self = this;
      var nuevos_ant = self.model.get("nuevos");
      var modificados_ant = self.model.get("modificados");
      var no_modificados_ant = self.model.get("no_modificados");
      var eliminados_ant = self.model.get("eliminados");

      var insertados = new Array();
      for(var i=0;i<this.insertados.length;i++) {
        var o = this.insertados[i];
        if (isEmpty(o.model.get("codigo"))) continue;
        insertados.push(o.model.toJSON());
      }

      var nuevos = new Array();
      this.$("#importacion_articulos_nuevos .estado").each(function(i,e){
        if (!$(e).hasClass("agregado")) {
          var nuevo = {
            "id":$(e).find(".id").val(),
            "codigo":$(e).find(".codigo").val(),
            "costo_neto_inicial":$(e).find(".costo_neto_inicial").val(),
            "costo_neto_inicial_dolar":$(e).find(".costo_neto_inicial_dolar").val(),
            "modif_costo_1":$(e).find(".modif_costo_1").val(),
            "modif_costo_2":$(e).find(".modif_costo_2").val(),
            "modif_costo_3":$(e).find(".modif_costo_3").val(),
            "modif_costo_4":$(e).find(".modif_costo_4").val(),
            "modif_costo_5":$(e).find(".modif_costo_5").val(),
            "costo_neto":$(e).find(".costo_neto").val(),
            "costo_final":$(e).find(".costo_final").val(),
            "coeficiente":$(e).find(".coeficiente").val(),
            "cantidad":$(e).find(".cantidad").val(),
            "precio_final":$(e).find(".precio_final").val(),
            "porc_iva":$(e).find(".porc_iva").val(),
            "precio_neto":$(e).find(".precio_neto").val(),
            "estado":($(e).find(".check-row").is(":checked")?1:0),
            "fue_modificado":0,
          };
          var anterior = nuevos_ant[i];
          if (anterior.precio_final != $(e).find(".precio_final").val()) {
            // Cambio la fila
            nuevo.fue_modificado = 1;
          }
          nuevos.push(nuevo);
        }
      });

      var modificaciones = new Array();
      this.$("#importacion_articulos_modificaciones .estado").each(function(i,e){
        var modificado = {
          "id":$(e).find(".id").val(),
          "codigo":$(e).find(".codigo").val(),
          "costo_neto_inicial":$(e).find(".costo_neto_inicial").val(),
          "costo_neto_inicial_dolar":$(e).find(".costo_neto_inicial_dolar").val(),
          "modif_costo_1":$(e).find(".modif_costo_1").val(),
          "modif_costo_2":$(e).find(".modif_costo_2").val(),
          "modif_costo_3":$(e).find(".modif_costo_3").val(),
          "modif_costo_4":$(e).find(".modif_costo_4").val(),
          "modif_costo_5":$(e).find(".modif_costo_5").val(),
          "costo_neto":$(e).find(".costo_neto").val(),
          "costo_final":$(e).find(".costo_final").val(),
          "coeficiente":$(e).find(".coeficiente").val(),
          "cantidad":$(e).find(".cantidad").val(),
          "precio_final":$(e).find(".precio_final").val(),
          "porc_iva":$(e).find(".porc_iva").val(),
          "precio_neto":$(e).find(".precio_neto").val(),
          "costo_anterior":$(e).find(".costo_anterior").val(),
          "estado":($(e).find(".check-row").is(":checked")?1:0),
          "fue_modificado":0,
        };
        var anterior = modificados_ant[i];
        if (anterior.precio_final != $(e).find(".precio_final").val()) {
          // Cambio la fila
          modificado.fue_modificado = 1;
        }
        modificaciones.push(modificado);
      });

      var no_modificados = new Array();
      this.$("#importacion_articulos_no_modificados .estado").each(function(i,e){
        var no_modificado = {
          "id":$(e).find(".id").val(),
          "codigo":$(e).find(".codigo").val(),
          "costo_neto_inicial":$(e).find(".costo_neto_inicial").val(),
          "costo_neto_inicial_dolar":$(e).find(".costo_neto_inicial_dolar").val(),
          "modif_costo_1":$(e).find(".modif_costo_1").val(),
          "modif_costo_2":$(e).find(".modif_costo_2").val(),
          "modif_costo_3":$(e).find(".modif_costo_3").val(),
          "modif_costo_4":$(e).find(".modif_costo_4").val(),
          "modif_costo_5":$(e).find(".modif_costo_5").val(),
          "costo_neto":$(e).find(".costo_neto").val(),
          "costo_final":$(e).find(".costo_final").val(),
          "coeficiente":$(e).find(".coeficiente").val(),
          "cantidad":$(e).find(".cantidad").val(),
          "precio_final":$(e).find(".precio_final").val(),
          "porc_iva":$(e).find(".porc_iva").val(),
          "precio_neto":$(e).find(".precio_neto").val(),
          "costo_anterior":$(e).find(".costo_anterior").val(),
          "estado":($(e).find(".check-row").is(":checked")?1:0),
          "fue_modificado":0,
        };
        var anterior = no_modificados_ant[i];
        if (anterior.precio_final != $(e).find(".precio_final").val()) {
          // Cambio la fila
          no_modificado.fue_modificado = 1;
        }
        no_modificados.push(no_modificado);
      });

      var eliminados = new Array();
      this.$("#importacion_articulos_eliminados .estado").each(function(i,e){
        var eliminado = {
          "id":$(e).find(".id").val(),
          "codigo":$(e).find(".codigo").val(),
          "costo_neto_inicial":$(e).find(".costo_neto_inicial").val(),
          "costo_neto_inicial_dolar":$(e).find(".costo_neto_inicial_dolar").val(),
          "modif_costo_1":$(e).find(".modif_costo_1").val(),
          "modif_costo_2":$(e).find(".modif_costo_2").val(),
          "modif_costo_3":$(e).find(".modif_costo_3").val(),
          "modif_costo_4":$(e).find(".modif_costo_4").val(),
          "modif_costo_5":$(e).find(".modif_costo_5").val(),
          "costo_neto":$(e).find(".costo_neto").val(),
          "costo_final":$(e).find(".costo_final").val(),
          "coeficiente":$(e).find(".coeficiente").val(),
          "cantidad":$(e).find(".cantidad").val(),
          "precio_final":$(e).find(".precio_final").val(),
          "porc_iva":$(e).find(".porc_iva").val(),
          "precio_neto":$(e).find(".precio_neto").val(),
          "estado":($(e).find(".check-row").is(":checked")?1:0),
          "fue_modificado":0,
        };
        var anterior = eliminados_ant[i];
        if (anterior.precio_final != $(e).find(".precio_final").val()) {
          // Cambio la fila
          eliminado.fue_modificado = 1;
        }
        eliminados.push(eliminado);
      });

      $.ajax({
        "url":"importaciones_articulos/function/guardar/",
        "type":"post",
        "data":{
          "id_importacion":self.model.get("id_importacion"),
          "observaciones":self.$("#importacion_articulo_observaciones").val(),
          "nuevos":JSON.stringify(nuevos),
          "modificaciones":JSON.stringify(modificaciones),
          "no_modificados":JSON.stringify(no_modificados),
          "eliminados":JSON.stringify(eliminados),
          "insertados":JSON.stringify(insertados),
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) location.href="app/#importaciones_articulos";
        },
      })
    },

  });
})(app);


(function ( app ) {

  app.views.ImportacionArticulosItemView = app.mixins.View.extend({

    template: _.template($("#importacion_articulos_item_template").html()),
    tagName: "tr",
    className: function() {
      return "no-padding estado "+((this.model.get("fue_modificado")==1)?"fila_roja":"")+" "+((this.model.get("agregado")==1)?"agregado":"");
    },
    myEvents: {

      "change .costo_neto_inicial_dolar":function(e){
        this.calcular_costo_neto_inicial_dolar();
        this.$(".costo_neto_inicial").select();
      },

      "change .costo_neto_inicial":function(e){
        this.calcular_costo_neto_inicial();
        this.$(".modif_costo_1").select();
      },

      "keyup .flechas":function(e) {
        // Tecla para abajo
        if (e.which == 40) $(e.currentTarget).parents("tr").next("tr").find("."+$(e.currentTarget).data("campo")).select();
        // Tecla para arriba
        else if (e.which == 38) $(e.currentTarget).parents("tr").prev("tr").find("."+$(e.currentTarget).data("campo")).select();
      },

      "modif_1 .modif_costo_1":"calcular_modif_costo_1",
      "change .modif_costo_1":function(e){
        var self = this;
        this.calcular_modif_costo_1();
        if ($("#"+this.prefijo+"_modif_costo_1_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".modif_costo_1").val($(cur).val()).trigger("modif_1");
          });
        }
        this.$(".modif_costo_2").select();
      },

      "modif_2 .modif_costo_2":"calcular_modif_costo_2",
      "change .modif_costo_2":function(e){
        var self = this;
        this.calcular_modif_costo_2();
        if ($("#"+this.prefijo+"_modif_costo_2_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".modif_costo_2").val($(cur).val()).trigger("modif_2");
          });
        }
        this.$(".modif_costo_3").select();
      },

      "modif_3 .modif_costo_3":"calcular_modif_costo_3",
      "change .modif_costo_3":function(e){
        var self = this;
        this.calcular_modif_costo_3();
        if ($("#"+this.prefijo+"_modif_costo_3_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".modif_costo_3").val($(cur).val()).trigger("modif_3");
          });
        }
        this.$(".modif_costo_4").select();
      },

      "modif_4 .modif_costo_4":"calcular_modif_costo_4",
      "change .modif_costo_4":function(e){
        var self = this;
        this.calcular_modif_costo_4();
        if ($("#"+this.prefijo+"_modif_costo_4_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".modif_costo_4").val($(cur).val()).trigger("modif_4");
          });
        }
        this.$(".modif_costo_5").select();
      },

      "modif_5 .modif_costo_5":"calcular_modif_costo_5",
      "change .modif_costo_5":function(e){
        var self = this;
        this.calcular_modif_costo_5();
        if ($("#"+this.prefijo+"_modif_costo_5_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".modif_costo_5").val($(cur).val()).trigger("modif_5");
          });
        }
        this.$(".coeficiente").focus();
      },

      "coeficiente .coeficiente":"calcular_precios",
      "change .coeficiente":function(e){
        var self = this;
        this.calcular_precios();
        if ($("#"+this.prefijo+"_coeficiente_check").is(":checked")) {
          var cur = e.currentTarget;
          $(cur).parents("tr").nextAll().each(function(i,e){
            $(e).find(".coeficiente").val($(cur).val()).trigger("coeficiente");
          });
        }
        this.$(".cantidad").focus();
      },

      "change .cantidad":function(e){
        var self = this;
        this.calcular_costo_neto_inicial();
        this.$(".modif_costo_1").focus();
      },

      "change .porc_iva":function(e) {
        this.calcular_precios();
      },
    },

    calcular_modif_costo_1: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;
      var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
      if (isNaN(modif_costo_2)) modif_costo_2 = 0;
      var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
      if (isNaN(modif_costo_3)) modif_costo_3 = 0;
      var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
      if (isNaN(modif_costo_4)) modif_costo_4 = 0;
      var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      if (isNaN(modif_costo_5)) modif_costo_5 = 0;
      var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_modif_costo_2: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;
      var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
      if (isNaN(modif_costo_2)) modif_costo_2 = 0;
      var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
      if (isNaN(modif_costo_3)) modif_costo_3 = 0;
      var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
      if (isNaN(modif_costo_4)) modif_costo_4 = 0;
      var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      if (isNaN(modif_costo_5)) modif_costo_5 = 0;
      var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_modif_costo_3: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;
      var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
      if (isNaN(modif_costo_2)) modif_costo_2 = 0;
      var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
      if (isNaN(modif_costo_3)) modif_costo_3 = 0;
      var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
      if (isNaN(modif_costo_4)) modif_costo_4 = 0;
      var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      if (isNaN(modif_costo_5)) modif_costo_5 = 0;
      var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_modif_costo_4: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;
      var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
      if (isNaN(modif_costo_2)) modif_costo_2 = 0;
      var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
      if (isNaN(modif_costo_3)) modif_costo_3 = 0;
      var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
      if (isNaN(modif_costo_4)) modif_costo_4 = 0;
      var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      if (isNaN(modif_costo_5)) modif_costo_5 = 0;
      var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_modif_costo_5: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;
      var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
      if (isNaN(modif_costo_2)) modif_costo_2 = 0;
      var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
      if (isNaN(modif_costo_3)) modif_costo_3 = 0;
      var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
      if (isNaN(modif_costo_4)) modif_costo_4 = 0;
      var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      if (isNaN(modif_costo_5)) modif_costo_5 = 0;
      var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_costo_neto_inicial_dolar: function() {
      var costo_neto_inicial_dolar = parseFloat(this.$(".costo_neto_inicial_dolar").val());
      if (isNaN(costo_neto_inicial_dolar)) costo_neto_inicial_dolar = 0;
      costo_neto_inicial_dolar = Number(costo_neto_inicial_dolar).toFixed(2);
      var costo_neto_inicial = parseFloat(COTIZACION_DOLAR) * costo_neto_inicial_dolar;
      this.$(".costo_neto_inicial").val(Number(costo_neto_inicial).toFixed(2));
      this.model.set({
        "costo_neto_inicial_dolar":costo_neto_inicial_dolar,
      });
      this.calcular_costo_neto_inicial();
    },

    calcular_costo_neto_inicial: function() {

      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      if (isNaN(costo_neto_inicial)) costo_neto_inicial = 0;

      // Si tenemos costo en dolares
      if (this.$(".costo_neto_inicial_dolar").length > 0 && COTIZACION_DOLAR > 0) {
        var costo_neto_inicial_dolar = Number(costo_neto_inicial / COTIZACION_DOLAR).toFixed(2);
        this.$(".costo_neto_inicial_dolar").val(costo_neto_inicial_dolar);
        this.model.set({
          "costo_neto_inicial_dolar":costo_neto_inicial_dolar,
        });        
      }

      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (isNaN(modif_costo_1)) modif_costo_1 = 0;

      if (this.$(".modif_costo_2").length > 0) {
        var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
        if (isNaN(modif_costo_2)) modif_costo_2 = 0;
        var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
        if (isNaN(modif_costo_3)) modif_costo_3 = 0;
        var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
        if (isNaN(modif_costo_4)) modif_costo_4 = 0;
        var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
        if (isNaN(modif_costo_5)) modif_costo_5 = 0;
        var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100)  * ((100 - modif_costo_2) / 100)  * ((100 - modif_costo_3) / 100)  * ((100 - modif_costo_4) / 100)  * ((100 - modif_costo_5) / 100);
      } else {
        var costo_neto = parseFloat(costo_neto_inicial) * ((100 - modif_costo_1) / 100);
      }
      this.$(".costo_neto").val(Number(costo_neto).toFixed(3));
      this.calcular_precios();
    },

    calcular_precios: function() {
      var costo_neto_inicial = parseFloat(this.$(".costo_neto_inicial").val());
      var modif_costo_1 = parseFloat(this.$(".modif_costo_1").val());
      if (this.$(".modif_costo_2").length > 0) {
        var modif_costo_2 = parseFloat(this.$(".modif_costo_2").val());
        var modif_costo_3 = parseFloat(this.$(".modif_costo_3").val());
        var modif_costo_4 = parseFloat(this.$(".modif_costo_4").val());
        var modif_costo_5 = parseFloat(this.$(".modif_costo_5").val());
      }
      var costo_neto = parseFloat(this.$(".costo_neto").val());
      if (isNaN(costo_neto)) costo_neto = 0;
      var costo_final = costo_neto;

      var cantidad = this.$(".cantidad").val();
      if (isNaN(cantidad)) cantidad = 1;      

      var costo_final_cantidad = costo_final * cantidad;
      
      var coeficiente = this.$(".coeficiente").val();
      if (isNaN(coeficiente)) coeficiente = 0;

      var precio_final = parseFloat(costo_neto) * coeficiente;

      var porc_iva = parseFloat(this.$(".porc_iva").val());
      if (isNaN(porc_iva)) porc_iva = 0;
      var precio_neto = Number(precio_final / ((100+porc_iva)/100)).toFixed(2);

      if (this.$(".modif_costo_2").length > 0) {
        this.model.set({
          "modif_costo_2":Number(modif_costo_2).toFixed(2),
          "modif_costo_3":Number(modif_costo_3).toFixed(2),
          "modif_costo_4":Number(modif_costo_4).toFixed(2),
          "modif_costo_5":Number(modif_costo_5).toFixed(2),
        });
      }
      
      this.model.set({
        "costo_neto_inicial":Number(costo_neto_inicial).toFixed(3),
        "modif_costo_1":Number(modif_costo_1).toFixed(2),
        "costo_neto":Number(costo_neto).toFixed(3),
        "costo_final":Number(costo_final).toFixed(2),
        "coeficiente":Number(coeficiente).toFixed(4),
        "cantidad":Number(cantidad).toFixed(4),
        "precio_neto":Number(precio_neto).toFixed(2),
        "precio_final":Number(precio_final).toFixed(2),
      });

      this.$(".costo_final").val(Number(costo_final).toFixed(2));
      this.$(".precio_neto").val(Number(precio_neto).toFixed(2));
      this.$(".precio_final").val(Number(precio_final).toFixed(2));
      this.$(".costo_final_cantidad").val(Number(costo_final_cantidad).toFixed(2));
    },

    initialize: function(options) {
      var self = this;
      this.options = options;
      this.prefijo = options.prefijo;
      this.moneda = options.moneda;
      this.render();
    },
    render: function() {

      // Calculamos los costos en dolares
      var obj = this.model.toJSON();
      obj.moneda = this.moneda;
      /*
      obj.costo_neto_inicial_dolar = obj.costo_neto_inicial;
      var dolar = parseFloat(COTIZACION_DOLAR);
      if (dolar > 0) {
        if (obj.moneda == "$") {
          obj.costo_neto_inicial_dolar = obj.costo_neto_inicial / dolar;
        } else if (obj.moneda == "U$S") {
          obj.costo_neto_inicial_dolar = obj.costo_neto_inicial;
          obj.costo_neto_inicial = obj.costo_neto_inicial_dolar * dolar;
        }        
      }
      */
      $(this.el).html(this.template(obj));
      this.calcular_costo_neto_inicial();
      $('[data-toggle="tooltip"]').tooltip(); 
    },

  });
})(app);




(function ( app ) {

  app.views.ImportacionesArticulosTableView = app.mixins.View.extend({

    template: _.template($("#importaciones_articulos_resultados_template").html()),

    myEvents: {
      "click .nuevo":"nuevo",
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
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#importaciones_articulos_listado_proveedor",
        firstOptions: ["<option value='0'>Proveedores</option>"],
      });
      
      createdatepicker(this.$("#importaciones_articulos_desde"));
      createdatepicker(this.$("#importaciones_articulos_hasta"));
      this.buscar();
    },

    nuevo: function() {
      app.views.importar = new app.views.Importar({
        "url":"articulos/function/importar_excel/",
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
      if (!isEmpty(this.$("#importaciones_articulos_listado_proveedor").val())) 
        filtros.id_proveedor = this.$("#importaciones_articulos_listado_proveedor").val();
      
      var fecha_desde = this.$("#importaciones_articulos_desde").val();
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) filtros.desde = fecha_desde;
      
      var fecha_hasta = this.$("#importaciones_articulos_hasta").val();
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) filtros.hasta = fecha_hasta;
      
      this.collection.server_api = filtros;
      this.collection.pager();            
    },

    addAll : function () {
      this.$("#importaciones_articulos_tabla tbody tr").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ImportacionesArticulosItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
        tabla: self,
      });
      this.$("#importaciones_articulos_tabla tbody").append(view.render().el);
    },

  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.ImportacionesArticulosItemResultados = Backbone.View.extend({

    template: _.template($("#importaciones_articulos_item_resultados_template").html()),
    tagName: "tr",
    events: {
      "click":"seleccionar",
      "click .edit":"editar",
      "click .delete":"borrar",
      "click .print":"imprimir",
      "click .exportar":"exportar",
      "click .marcar_cargado":"marcar_cargado",
      "click .verlog":"verlog",
    },
    verlog:function(e) {
      e.preventDefault();
      e.stopPropagation();
      var self=this;
      $.ajax({
        "url":"importaciones_articulos/function/verlog/"+self.model.id,
        "dataType":"json",
        "success":function(res) {
          var view = new app.views.ImportacionesArticulosLog({
            model: new app.models.AbstractModel(res)
          });
          crearLightboxHTML({
            "html":view.el,
            "width":900,
            "height":140,
          });
        }
      });
    },
    marcar_cargado:function(e) {
      var self = this;
      e.preventDefault();
      e.stopPropagation();
      $.ajax({
        "url":"importaciones_articulos/function/marcar_cargado/"+self.model.id,
        "dataType":"json",
        "success":function() {
          self.tabla.buscar();
        }
      });
      return false;
    },
    exportar: function(e) {
      e.preventDefault();
      e.stopPropagation();
      var self = this;
      window.open("importaciones_articulos/function/exportar_excel/"+this.model.id,"_blank");
      setTimeout(function(){
        self.tabla.buscar();
      },1000);
      return false;
    },
    seleccionar : function(e) {
      if (this.options.seleccionar && this.parent != undefined) {
        $('.modal:last').modal('hide');
        this.parent.importar(this.model);
      } else this.editar();
    },
    editar : function() {
      location.href="app/#importacion_articulo/"+this.model.id;
    },
    borrar : function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (confirmar("Realmente desea eliminar esta importacion?")) {
        $.ajax({
          "url":"importaciones_articulos/function/delete/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.importaciones_articulosTableView.buscar();
          }
        });                
      }
    },
    imprimir: function() {
      window.open("importaciones_articulos/function/imprimir/"+this.model.id,"_blank");
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      this.tabla = options.tabla;
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




(function ( app ) {
  app.views.ImportacionesArticulosLog = app.mixins.View.extend({

    template: _.template($("#importaciones_articulos_log_template").html()),
    myEvents: {
      "click .cerrar":"cerrar",
    },
    initialize: function(options) {
      _.bindAll(this);
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      console.log(this.model);
      return this;
    },
  });
})(app);