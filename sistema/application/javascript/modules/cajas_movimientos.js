(function ( models ) {
  models.CajaMovimiento = Backbone.Model.extend({
    urlRoot: "cajas_movimientos/",
    defaults: {
      id_concepto : 0,
      uploaded: 0,
      id_usuario: ID_USUARIO,
      id_factura: 0,
      id_orden_pago: 0,
      monto: 0,
      fecha: "",
      concepto: "",
      tipo: 0, // 0 = INGRESO, 1 = EGRESO, 2 = AJUSTE
      estado: 0, // 0 = COBRADO, 1 = PENDIENTE
      id_caja: 0,
      id_sucursal: ID_SUCURSAL,
      id_empresa: ID_EMPRESA,
      observaciones: "",
      subtotal: 0, // Atributo calculado para dar el subtotal del saldo
      path: "",
    }
  });
})( app.models );

(function ( views, models ) {

  views.EditarCajaMovimientoView = app.mixins.View.extend({

    template: _.template($("#editar_caja_movimiento_template").html()),
    
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar": "guardar",
      "click .agregar_concepto":function(e) {
        var self = this;
        var totaliza_en = "G";
        if (this.model.get("tipo") == 0) {
          var totaliza_en = "V";
        }
        if (ID_EMPRESA == 641) totaliza_en = "";
        if ($(".concepto_edit_mini").length > 0) return;
        var form = new app.views.TipoGastoMiniEditView({
          "model": new app.models.TipoGasto({
            "totaliza_en":totaliza_en,
          }),
          "callback":function(m){
            var that = self;
            self.model.set({ "id_concepto":m });
            $.ajax({
              "url":"conceptos/function/get_arbol/",
              /*
              "data":{
                "totaliza_en":totaliza_en
              },*/
              "type":"get",
              "dataType":"json",
              "success":function(r){
                that.cargar_conceptos(r);
              },
            });
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete concepto_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#concepto_mini_nombre").focus();
      },
    },

    initialize: function() {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.render();
    },

    cargar_conceptos: function(r) {
      var self = this;
      var r = workspace.crear_select(r,"",self.model.get("id_concepto"));
      this.$("#cajas_movimientos_tipo").html(r);
      this.$("#select2-cajas_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#cajas_movimientos_tipo").select2({});
    },

    render: function() {
      var self = this;
      var obj = {
        id:this.model.id,
        editar:((ID_EMPRESA == 249 && !(PERFIL == 302 || PERFIL == 1457) && !this.model.isNew() && this.model.get("id_concepto") == 1231)?0:1),
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker(this.$("#cajas_movimientos_fecha"),fecha);

      this.$("#select2-cajas_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#cajas_movimientos_tipo").select2({});
      return this;
    },

    // Rellena los campos con el modelo pasado por parametro
    // Luego la vista mostrara los datos para editar o solamente para ver
    ver: function(model) {
      this.model = model;
      this.render();
    },
    
    validar: function() {
      try {
        var self = this;
        if (this.$("#cajas_movimientos_monto").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#cajas_movimientos_monto").focus();
          return false;
        }

        if (this.model.get("tipo") != 2) {
          var id_concepto = self.$("#cajas_movimientos_tipo").val();
          if (id_concepto == 0) {
            alert("Por favor seleccione un concepto");
            return false;
          }
        }

        // ARREGLO MEGA: QUE NO PUEDAN CREAR NUEVOS PAGOS A PROVEEDORES
        // QUE SIEMPRE LO HAGAN DESDE EL DEPOSITO
        if (this.model.isNew() && ID_EMPRESA == 249 && id_concepto == 1231 && PERFIL != 302) {
          alert("ERROR: NO ES POSIBLE CARGAR UN PAGO A PROVEEDOR DIRECTAMENTE EN LA CAJA. SOLO PUEDE CARGARLO EL DEPOSITO.");
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
        this.model.set({
          "fecha":self.$("#cajas_movimientos_fecha").val(),
          "id_concepto":$(self.el).find("#cajas_movimientos_tipo").val(),
          "monto":$(self.el).find("#cajas_movimientos_monto").val(),
          "estado":(self.$("#cajas_movimientos_estado").is(":checked")?1:0),
          "path":self.$("#hidden_path").val(),
        });
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            model.set({id:response.id});
            $('.modal:last').modal('hide');
          }
        });
      }
    },

  });

})(app.views, app.models);


(function ( app ) {

  app.views.ListadoCajasMovimientosView = app.mixins.View.extend({

    template: _.template($("#listado_cajas_movimientos_panel_template").html()),

    myEvents: {
      "change .check-row2":"sumar",
      "click .nuevo_caja_movimiento":"nuevo_caja_movimiento",
      "click .nuevo_gasto":"nuevo_gasto",
      "click .nuevo_ajuste":"nuevo_ajuste",
      "click .nuevo_ingreso":"nuevo_ingreso",
      "click .nuevo_ingreso_cheque":"nuevo_ingreso_cheque",
      "click .nuevo_egreso_cheque":"nuevo_egreso_cheque",
      "change #cajas_movimientos_desde":"render_cajas_movimientos",
      "change #cajas_movimientos_hasta":"render_cajas_movimientos",
      "change #cajas_movimientos_conceptos":"render_cajas_movimientos",
      "change #cajas_movimientos_estado":"render_cajas_movimientos",
      "change #cajas_movimientos_orden_pago":"render_cajas_movimientos",
      "click .buscar":"render_cajas_movimientos",
      "click .exportar":"exportar",
      "click .transferencia":"transferencia",
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      }
    },

    sumar : function(e) {
      e.stopPropagation();
      e.preventDefault();
      var el = e.currentTarget;
      if (el.type != "checkbox") return;
      if ($(el).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
      var marcado = false;
      var total = 0;
      var j = 0;
      this.$(".check-row2").each(function(i,e){
        if ($(e).is(":checked")) {
          marcado = true;
          total += parseFloat($(e).data("total"));
          j++;
        }
      });
      if (marcado) this.$(".bulk_action").slideDown();
      else this.$(".bulk_action").slideUp();

      this.$("#cajas_movimientos_monto").html("$ "+Number(total).format(2));
      this.$("#cajas_movimientos_cantidad").html(j);
      return false;
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.tipo = (typeof options.tipo == "undefined") ? 0 : options.tipo;
      this.titulo = (typeof window.cajas_movimientos_titulo == "undefined") ? "" : window.cajas_movimientos_titulo;
      this.estado = (typeof options.estado == "undefined") ? -1 : options.estado;
      this.orden_pago = (typeof options.orden_pago == "undefined") ? -1 : options.orden_pago;
      this.id_caja = (typeof options.id_caja == "undefined") ? 0 : options.id_caja;
      this.id_sucursal = (typeof options.id_sucursal == "undefined") ? 0 : options.id_sucursal;
      this.id_concepto = (typeof options.id_concepto == "undefined") ? 0 : options.id_concepto;
      this.ver_saldos = (typeof options.ver_saldos == "undefined") ? 0 : options.ver_saldos;
      window.caja_movimientos_desde = (typeof window.caja_movimientos_desde == "undefined") ? moment().format("DD/MM/YYYY") : window.caja_movimientos_desde;
      window.caja_movimientos_hasta = (typeof window.caja_movimientos_hasta == "undefined") ? moment().format("DD/MM/YYYY") : window.caja_movimientos_hasta;
      this.titulo = "";
      for(var i=0;i<window.cajas.length;i++) {
        var c = window.cajas[i];
        if (c.id == this.id_caja) {
          this.titulo = c.nombre;
          this.id_sucursal = c.id_sucursal;
          break;
        }
      }
      var obj = { 
        permiso: this.permiso,
        estado: this.estado,
        orden_pago: this.orden_pago,
        tipo: this.tipo,
        id_caja: this.id_caja,
        id_concepto: this.id_concepto,
        ver_saldos: this.ver_saldos,
        desde: window.caja_movimientos_desde,
        hasta: window.caja_movimientos_hasta,
        titulo: this.titulo,
      };
      $(this.el).html(this.template(obj));

      createdatepicker(this.$("#cajas_movimientos_desde"),window.caja_movimientos_desde);
      createdatepicker(this.$("#cajas_movimientos_hasta"),window.caja_movimientos_hasta);

      this.render_cajas_movimientos()
    },

    render_cajas_movimientos: function() {
      var self = this;

      if (this.$("#cajas_movimientos_desde").length > 0 && window.caja_movimientos_desde != this.$("#cajas_movimientos_desde").val().trim()) {
        window.caja_movimientos_desde = this.$("#cajas_movimientos_desde").val().trim();
      }

      if (this.$("#cajas_movimientos_hasta").length > 0 && window.caja_movimientos_hasta != this.$("#cajas_movimientos_hasta").val().trim()) {
        window.caja_movimientos_hasta = this.$("#cajas_movimientos_hasta").val().trim();
      }      

      $.ajax({
        "url":"cajas_movimientos/function/listado/",
        "data":{
          "estado":self.$("#cajas_movimientos_estado").val(),
          "orden_pago":self.$("#cajas_movimientos_orden_pago").val(),
          "id_concepto":self.$("#cajas_movimientos_conceptos").val(),
          "desde":window.caja_movimientos_desde,
          "hasta":window.caja_movimientos_hasta,
          "tipo":self.tipo,
          "ver_saldos":self.ver_saldos,
          "id_caja":self.id_caja,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          var monto = 0;
          var cantidad = 0;
          var saldo_inicial = parseFloat(r.saldo_inicial);
          $(self.el).find("tbody").empty();
          if (self.ver_saldos == 1) {
            self.$("tbody").append("<tr><td colspan='2' class='ver'></td><td class='ver'><span class='text-info'>Saldo Inicial</span></td><td colspan='3' class='ver'></td><td class='tar ver number'>$ "+Number(saldo_inicial).format(2)+"</td><td></td><tr>");
          }
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            if (self.ver_saldos == 1) {
              if (o.estado == 0) {
                if (o.tipo == 0) saldo_inicial = saldo_inicial + parseFloat(o.monto);
                else if (o.tipo == 1) saldo_inicial = saldo_inicial - parseFloat(o.monto);                
                else if (o.tipo == 2) saldo_inicial = parseFloat(o.monto);
              }
              o.subtotal = saldo_inicial;
            } else {
              monto += parseFloat(o.monto);  
            }
            o.ver_saldos = self.ver_saldos;
            var item = new app.views.ListadoCajaMovimientoItem({
              "model":new app.models.CajaMovimiento(o),
              "tabla":self,
            });
            $(self.el).find("tbody").append(item.el);
            cantidad++;
          }
          window.monto_cajas_movimientos = saldo_inicial - r.saldo_inicial;
        }
      });
    },

    exportar: function() {
      var self = this;
      var header = new Array();
      $(".table thead tr th.exportable").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) {
          header.push(t);
        }
      });
      // Acomodamos los datos
      var array = new Array();
      this.$(".table tbody tr").each(function(i,e){
        var obj = {};
        $(e).find("td.exportable").each(function(ii,ee){
          var s = $(ee).text().trim();
          if ($(ee).hasClass("number")) {
            s = s.replace(/\./g,"");
            s = s.replace(/\,/g,".");
            s = s.replace(/\$/g,"");
            s = parseFloat(s.trim());
          }
          obj["s"+ii] = s;
        });
        array.push(obj);
      })
      this.exportar_excel({
        "filename":(isEmpty(self.titulo) ? "exportacion" : self.titulo),
        "title":self.titulo,
        "data":array,
        "header":header,
      });
    },    

    transferencia: function() {
      var self = this;
      var edicion = new app.views.CajaTransferenciaView({
        model: new app.models.AbstractModel(),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },    

    nuevo_caja_movimiento: function() {
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: self.tipo,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },

    nuevo_ajuste: function() {
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: 2,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },

    nuevo_gasto: function() {
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: 1,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },

    nuevo_ingreso_cheque : function() {
      var self = this;
      window.cheque = null;
      var view = new app.views.ChequesTableView({
        collection: new app.collections.Cheques(),
        lightbox: true,
        permiso: 1
      });
      crearLightboxHTML({
        "html":view.el,
        "width":800,
        "height":360,
        "callback":function(){
          self.agregar_ingreso_cheque(window.cheque);
        }
      });
    },

    nuevo_egreso_cheque : function() {
      var self = this;
      window.cheque = null;
      var view = new app.views.ChequesTableView({
        collection: new app.collections.Cheques(),
        lightbox: true,
        permiso: 1
      });
      crearLightboxHTML({
        "html":view.el,
        "width":800,
        "height":360,
        "callback":function(){
          self.agregar_egreso_cheque(window.cheque);
        }
      });
    },

    agregar_ingreso_cheque: function() {
      if (window.cheque == null) return;
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: 0,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
          monto: window.cheque.get("monto"),
          id_cheque: window.cheque.id,
          observaciones: "Ingreso cheque "+window.cheque.get("numero"),
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },

    agregar_egreso_cheque: function() {
      if (window.cheque == null) return;
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: 1,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
          monto: window.cheque.get("monto"),
          id_cheque: window.cheque.id,
          observaciones: "Egreso cheque "+window.cheque.get("numero"),
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },
    nuevo_ingreso: function() {
      var self = this;
      var edicion = new app.views.EditarCajaMovimientoView({
        model: new app.models.CajaMovimiento({
          tipo: 0,
          id_caja: self.id_caja,
          id_sucursal: self.id_sucursal,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_cajas_movimientos();
        }
      });
    },

  });
})(app);



(function ( app ) {

  app.views.ListadoCajaMovimientoItem = app.mixins.View.extend({
    tagName: "tr",
    className: function() {
      return ((this.model.get("tipo") == 2) ? "fila_roja" : "");
    },
    template: _.template($('#listado_cajas_movimientos_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .orden_pago": function() {
        var self = this;
        var ordenPago = new app.models.OrdenPago({
          "id": self.model.get("id_orden_pago")
        });
        ordenPago.fetch({
          "success":function() {
            app.views.ordenPagoProveedores = new app.views.OrdenPagoProveedores({
              model: ordenPago
            });
            // Abrimos el lightbox de pagos
            crearLightboxHTML({
              "html":app.views.ordenPagoProveedores.el,
              "width":800,
              "height":565,
            });
          }
        });
      },

      "click .editar_estado":function(e) {
        var self = this;
        var estado = $(e.currentTarget).data("estado");
        self.model.set({"estado":estado});
        $.ajax({
          "url":"cajas_movimientos/function/cambiar_estado/"+estado+"/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id":self.model.id,
            "id_caja":self.model.get("id_caja"),
            "id_sucursal":self.model.get("id_sucursal"),
          },
          "success":function(r) {
            if (r.error == 0) self.render();
          },
        });
      }
    },
    initialize: function(options) {
      _.bindAll(this);
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.tabla = this.options.tabla;
      this.render();
    },
    render: function() {
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      var self = this;
      if (self.model.get("tipo") == 2) return; // Los ajustes no se pueden editar
      var edicion = new app.views.EditarCajaMovimientoView({
        model: self.model
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "callback":function(){
          self.tabla.render_cajas_movimientos();
        },
      });
    },
    borrar: function() {
      if (this.model.get("id_orden_pago") != 0) {
        alert("No se puede eliminar el movimiento ya que esta incluido en una Orden de Pago.");
        return;
      }
      if (this.model.get("id_factura") != 0) {
        alert("No se puede eliminar el movimiento ya que esta incluido en un Recibo de Cliente.");
        return;
      }
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy(); // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        this.tabla.render_cajas_movimientos();
      }
    },
  });

})( app );



(function ( views, models ) {

  views.CajaTransferenciaView = app.mixins.View.extend({

    template: _.template($("#caja_transferencia_template").html()),
    
    myEvents: {
      "click .cerrar": "cerrar",
      "click .guardar": "guardar",
    },

    cerrar: function() {
      $('.modal:last').modal('hide');
    },

    initialize: function() {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template());
      createtimepicker(this.$("#caja_transferencia_fecha"),new Date());
      return this;
    },

    validar: function() {
      try {
        var self = this;
        if (this.$("#caja_transferencia_monto").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#caja_transferencia_monto").focus();
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
        var id_caja_desde = $(self.el).find("#caja_transferencia_caja_desde").val();
        var id_caja_hasta = $(self.el).find("#caja_transferencia_caja_hasta").val();
        if (id_caja_desde == id_caja_hasta) {
          alert("ERROR: Las cajas no pueden ser las mismas.");
          return;
        }
        $.ajax({
          "url":"cajas_movimientos/function/transferencia/",
          "dataType":"json",
          "type":"post",
          "data":{
            "id_sucursal":ID_SUCURSAL,
            "id_usuario":ID_USUARIO,
            "fecha":self.$("#caja_transferencia_fecha").val(),
            "id_caja_desde":id_caja_desde,
            "id_caja_hasta":id_caja_hasta,
            "monto":$(self.el).find("#caja_transferencia_monto").val(),
            "observaciones":self.$("#caja_transferencia_observaciones").val(),
          },
          "success":function(r) {
            if (r.error == 1) alert(r.mensaje);
            else self.cerrar();
          },
        });
      }
    },

  });

})(app.views, app.models);