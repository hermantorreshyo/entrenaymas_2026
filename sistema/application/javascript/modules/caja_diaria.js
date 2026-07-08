(function ( models ) {

  models.CajaDiaria = Backbone.Model.extend({
    urlRoot: "caja_diaria/",
    defaults: {
      id_punto_venta: 0,
      id_empresa: ID_EMPRESA,
      id_usuario: 0,
      fecha: moment().format("DD/MM/YYYY"),
      hora: "",
      estado: "X", // X = Todavia no se abrio; A = Abierta; C = Cerrada; 
      efectivo: 0,
      efectivo_real: 0,
      tarjetas: 0,
      cheques: 0,
      efectivo_inicial: 0,
      salida_efectivo: 0,
      salida_cheques: 0,
      retiro: 0,
      intereses: 0,
      punto_venta: "",
      pago_efectivo: 0,
      pago_cheques: 0,
      pago_tarjetas: 0,
      confirmada: 0,
    },
  });
        
})( app.models );

(function (collections, model, paginator) {
  collections.CajasDiarias = paginator.requestPager.extend({
    model: model,
    modelId: function (attrs) {
      return attrs.id+"-"+attrs.id_punto_venta;
    },
    paginator_core: {
      url: "caja_diaria/function/buscar/"
    }
  });
})( app.collections, app.models.CajaDiaria, Backbone.Paginator);


(function ( app ) {

  app.views.CajaDiariaItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#cajas_diarias_item').html()),
    events: {
      "click .ver": "editar",
    },
    initialize: function(options) {
      this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);
      this.options = options;
      this.permiso = this.options.permiso;
      _.bindAll(this);
    },
    render: function() {
      var obj = { permiso: this.permiso };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#caja_diaria/"+this.model.id+"/"+this.model.get("id_punto_venta")+"/";
    },
  });

})( app );



(function ( app ) {

  app.views.CajasDiariasTableView = app.mixins.View.extend({

    template: _.template($("#cajas_diarias_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "click .imprimir_agrupado":"imprimir_agrupado",
      "click .recalcular":"recalcular",
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      window.caja_diaria_fecha_desde = (typeof window.caja_diaria_fecha_desde != "undefined") ? window.caja_diaria_fecha_desde : "";
      window.caja_diaria_fecha_hasta = (typeof window.caja_diaria_fecha_hasta != "undefined") ? window.caja_diaria_fecha_hasta : "";
      window.caja_diaria_punto_venta = (typeof window.caja_diaria_punto_venta != "undefined") ? window.caja_diaria_punto_venta : 0;
      window.caja_diaria_page = (typeof window.caja_diaria_page != "undefined") ? window.caja_diaria_page : 1;

      // Creamos la lista de paginacion
      this.pagination = new app.mixins.PaginationView({
        collection: lista
      });

      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      
      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker(this.$("#cajas_diarias_desde"),window.caja_diaria_fecha_desde);
      createdatepicker(this.$("#cajas_diarias_hasta"),window.caja_diaria_fecha_hasta);

      // Vamos a buscar los elementos y lo paginamos
      this.buscar();
    },

    addAll : function () {
      window.caja_diaria_page = this.pagination.getPage();
      var desde = this.$("#cajas_diarias_desde").val();
      var self = this;
      $(this.el).find("tbody").empty();
      var total = 0; var efectivo = 0; var tarjetas = 0; var intereses = 0; var salida_efectivo = 0; var efectivo_real = 0;
      this.collection.each(function(i){
        self.addOne(i);
        efectivo += parseFloat(i.get("efectivo"));
        tarjetas += parseFloat(i.get("tarjetas"));
        intereses += parseFloat(i.get("intereses"));
        salida_efectivo += parseFloat(i.get("salida_efectivo"));
        efectivo_real += parseFloat(i.get("efectivo_real"));
        total += parseFloat(i.get("efectivo")) + parseFloat(i.get("tarjetas")) - parseFloat(i.get("intereses"));
      });
      // Agregamos una fila al final
      if (!isEmpty(desde)) {
        var tr = "<tr>";
        tr+="<td class='fila_alerta tar' colspan='5'><b>TOTALES:</b></td>";
        tr+="<td class='fila_alerta'>$ "+Number(efectivo).toFixed(2)+"</td>";
        tr+="<td class='fila_alerta'>$ "+Number(salida_efectivo).toFixed(2)+"</td>";
        tr+="<td class='fila_alerta'></td>";
        tr+="<td class='fila_alerta'>$ "+Number(efectivo_real).toFixed(2)+"</td>";
        tr+="<td class='fila_alerta'></td>";
        tr+="<td class='fila_alerta'>$ "+Number(tarjetas-intereses).toFixed(2)+"</td>";
        tr+="<td class='fila_alerta'>$ "+Number(intereses).toFixed(2)+"</td>";
        tr+="<td class='fila_alerta'>$ "+Number(total).toFixed(2)+"</td>";
        tr+="</tr>";
        this.$("tbody").append(tr);
        this.$(".pagination_container").hide();
      }
    },

    addOne : function ( item ) {
      var view = new app.views.CajaDiariaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    },

    buscar: function() {

      var self = this;
      var cambio_parametros = false;

      if (window.caja_diaria_fecha_desde != this.$("#cajas_diarias_desde").val().trim()) {
        window.caja_diaria_fecha_desde = this.$("#cajas_diarias_desde").val().trim();
        cambio_parametros = true;
      }

      if (window.caja_diaria_fecha_hasta != this.$("#cajas_diarias_hasta").val().trim()) {
        window.caja_diaria_fecha_hasta = this.$("#cajas_diarias_hasta").val().trim();
        cambio_parametros = true;
      }

      if (this.$("#cajas_diarias_puntos_venta").length > 0) {
        if (window.caja_diaria_punto_venta != this.$("#cajas_diarias_puntos_venta").val()) {
          window.caja_diaria_punto_venta = this.$("#cajas_diarias_puntos_venta").val();
          cambio_parametros = true;
        }
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.caja_diaria_page = 1;

      var filtros = {
        "desde":(isEmpty(window.caja_diaria_fecha_desde)) ? "" : window.caja_diaria_fecha_desde.replace(/\//g,"-"),
        "hasta":(isEmpty(window.caja_diaria_fecha_hasta)) ? "" : window.caja_diaria_fecha_hasta.replace(/\//g,"-"),
        "id_punto_venta":window.caja_diaria_punto_venta,
        "id_sucursal":ID_SUCURSAL,
      }
      if (!isEmpty(window.caja_diaria_fecha_desde)) filtros.offset = 99999;
      this.collection.server_api = filtros;
      this.collection.goTo(window.caja_diaria_page);
    },

    imprimir_agrupado: function() {
      var checks = this.$("#cajas_diarias_table tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione al menos un elemento de la tabla.");
        return;
      }
      var ids = new Array();
      $(checks).each(function(i,e){
        ids.push({
          "id":$(e).val(),
          "id_punto_venta":$(e).data("id_punto_venta"),
        });
      });
      var ids_s = JSON.stringify(ids);
      var url = "caja_diaria/function/imprimir_agrupado/"+encodeURIComponent(ids_s);
      workspace.imprimir_reporte(url);
    },

    recalcular: function() {
      var self = this;
      var checks = this.$("#cajas_diarias_table tbody .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione al menos un elemento de la tabla.");
        return;
      }
      var id = 0;
      var id_punto_venta = 0;
      $(checks).each(function(i,e){
        id = $(e).val();
        id_punto_venta = $(e).data("id_punto_venta");
      });
      $.ajax({
        "url":"caja_diaria/function/recalcular/",
        "dataType":"json",
        "type":"get",
        "data":{
          "id_caja_diaria":id,
          "id_punto_venta":id_punto_venta,
        },
        "success":function() {
          self.collection.goTo(window.caja_diaria_page);
        }
      })
    },

  });
})(app);



(function ( app ) {

  app.views.CajaDiaria = app.mixins.View.extend({

    template: _.template($("#caja_diaria_template").html()),

    myEvents: {
      "click .guardar":"guardar",
      "click .confirmar":"confirmar",
      "click .cerrar":"cerrar",
      "click #nuevo_gasto": "nuevo_gasto",
      "click .imprimir":"imprimir",
      "click .imprimir_x":"imprimir_x",
      "click .imprimir_z":"imprimir_z",
      "change #caja_diaria_fecha": "buscar",
      "change .calcular":"calcular",
      "keypress #caja_diaria_efectivo_inicial":function(e) {
        if (e.which == 13) { this.$("#caja_diaria_efectivo_real").focus(); }
      },

      // Mostramos los cupones
      "click #caja_diaria_tarjetas_boton": function() {
        var self = this;
        var cupones = new app.views.CajaDiariaTarjetasTableView({
          "id_caja_diaria":self.model.get("id"),
          "id_punto_venta":self.model.get("id_punto_venta"),
        });
        crearLightboxHTML({
          "html":cupones.el,
          "width":1000,
          "height":500,
        });
      },

      // Mostramos los gastos
      "click #caja_diaria_salida_efectivo_boton": function() {
        if (this.model.get("estado")=="X") {
          alert("Es necesario primero abrir la caja para cargar gastos en efectivo.");
          this.$("#caja_diaria_efectivo_inicial").focus();
          return;
        }
        var self = this;
        window.total_gastos = 0;
        var gastos = new app.views.CajaDiariaGastosTableView({
          "id_caja_diaria":self.model.get("id"),
          "id_punto_venta":self.model.get("id_punto_venta"),
          "fecha":self.model.get("fecha"),
        });
        crearLightboxHTML({
          "html":gastos.el,
          "width":1000,
          "height":500,
          "callback":function() {
            self.$("#caja_diaria_salida_efectivo").val(Number(window.total_gastos).toFixed(2));
            self.calcular();
          }
        });
      },
            
      "click #caja_diaria_cheques_boton": function() {
        var self = this;
        var fecha = $(this.el).find("#caja_diaria_fecha").val();
        fecha = fecha.replace(/\//g,"-");
        var cheques = new app.views.ChequesTableView({
          "fecha_desde":fecha,
          "fecha_hasta":fecha,
        });
        crearLightboxHTML({
          "html":cheques.el,
          "width":600,
          "height":500,
        });
      }            
    },

    calcular: function() {
      var efectivo_inicial = parseFloat(this.$("#caja_diaria_efectivo_inicial").val());
      var efectivo = parseFloat(this.$("#caja_diaria_efectivo").val())
      var tarjetas = parseFloat(this.$("#caja_diaria_tarjetas").val());
      var cheques = parseFloat(this.$("#caja_diaria_cheques").val());
      var pago_efectivo = parseFloat(this.$("#caja_diaria_pago_efectivo").val());
      var pago_cheques = parseFloat(this.$("#caja_diaria_pago_cheques").val());
      var pago_tarjetas = parseFloat(this.$("#caja_diaria_pago_tarjetas").val());
      var salida_efectivo = parseFloat(this.$("#caja_diaria_salida_efectivo").val());
      var total_entradas = efectivo+tarjetas+cheques+pago_efectivo+pago_cheques+pago_tarjetas;
      var efectivo_real = parseFloat(this.$("#caja_diaria_efectivo_real").val());
      var efectivo_total = efectivo_inicial + efectivo - salida_efectivo + pago_efectivo;
      var total = total_entradas - salida_efectivo;
      var diferencia = efectivo_real - efectivo_total;
      var retiro = parseFloat(this.$("#caja_diaria_retiro").val());
      
      this.model.set({
        "salida_efectivo":salida_efectivo,
        "efectivo_inicial":efectivo_inicial,
        "efectivo_real":efectivo_real,
        "total":total,
        "diferencia":diferencia,
        "retiro":retiro,
      });
      
      this.$("#caja_diaria_efectivo_total").val(Number(efectivo_total).toFixed(2));
      this.$("#caja_diaria_subtotal_entradas").val(Number(total_entradas).toFixed(2));
      this.$("#caja_diaria_total").val(Number(total).toFixed(2));
      this.$("#caja_diaria_diferencia").val(Number(diferencia).toFixed(2));
    },
    
    initialize: function() {
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.guardando = 0;
      this.render();
      this.buscar();
    },

    imprimir: function() {
      workspace.imprimir_reporte("caja_diaria/function/imprimir/"+this.model.id+"/"+this.model.get("id_punto_venta"));
    },
        
    imprimir_x: function() {
      if (!confirm("Desea imprimir un reporte X?")) return;
      if (MEGASHOP == 1 || ID_EMPRESA == 356) {
        $.ajax({
          "url":"impresor_fiscal/imprimir_cierre_epson/X/",
          "dataType":"json",
        });
      } else {
        $.ajax({
          "url":"impresor_fiscal/imprimir_x/",
          "dataType":"json",
        });
      }
    },

    imprimir_z: function() {
      if (!confirm("Desea imprimir un reporte Z?")) return;
      if (MEGASHOP == 1 || ID_EMPRESA == 356) {
        $.ajax({
          "url":"impresor_fiscal/imprimir_cierre_epson/Z/",
          "dataType":"json",
        });
      } else {
        $.ajax({
          "url":"impresor_fiscal/imprimir_z/",
          "dataType":"json",
        });
      }
    },

    guardar: function() {
      var self = this;
      if (this.guardando == 1) return;
      this.guardando = 1;
      if (this.model.id == null) {
        this.model.set({id:0});
      }

      // Si la caja diaria todavia no se abrio
      if (this.model.get("estado") == "X") {
        // Abrimos la caja diaria
        this.model.set({
          "estado":"A",
        });
      }

      this.model.save({},{
        "success":function() {
          location.reload();
        },
        "error":function() {
          self.guardando = 0;
        },
      });
    },

    cerrar: function() {
      var self = this;
      if (this.guardando == 1) return;
      if (!confirm("Realmente desea cerrar la caja?")) return;
      this.guardando = 1;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      this.model.save({
        "estado":"C",
      },{
        "success":function() {
          location.reload();
        },
        "error":function() {
          self.guardando = 0;
        },
      });
    },

    confirmar: function() {
      var self = this;

      // Controlamos que se ingrese efectivo
      var efectivo_real = this.$("#caja_diaria_efectivo_real").val();
      if (isEmpty(efectivo_real) || efectivo_real == 0) {
        alert("Por favor ingrese el efectivo real de la caja.");
        return;
      }

      if (this.guardando == 1) return;
      if (!confirm("Realmente desea confirmar la caja y retirar el efectivo?")) return;
      this.guardando = 1;
      this.model.save({},{
        "success":function() {
          var that = self;
          $.ajax({
            "url":"caja_diaria/function/confirmar/",
            "dataType":"json",
            "type":"get",
            "data":{
              "id_caja_diaria":that.model.id,
              "id_punto_venta":that.model.get("id_punto_venta"),
            },
            "success":function(r) {
              that.guardando = 0;
              alert(r.mensaje);
              if (r.error == 0) window.history.back();
            },
            "error":function() {
              that.guardando = 0;
            }
          });
        },
        "error":function() {
          self.guardando = 0;
        },
      });
    },
        
    buscar: function() {
      var self = this;
      // La caja aun esta abierta
      if (this.model.get("estado") != "C") {
        //var fecha = this.model.get("fecha");
        //fecha = fecha.replace(/\//g,"-");
        $.ajax({
          "url":"caja_diaria/function/ver/",
          "dataType":"json",
          "type":"get",
          "data": {
            "id_sucursal":ID_SUCURSAL,
          },
          "success":function(r) {
            self.model.set(r);
            self.calcular();
          }
        });                
      } else {
        // Estamos viendo una caja ya cerrada
        self.calcular();
      }
    },
        
    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });

})(app);



(function ( app ) {

  app.views.CajaDiariaTarjetasTableView = app.mixins.View.extend({

    template: _.template($("#caja_diaria_tarjetas_panel_template").html()),

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.id_punto_venta = options.id_punto_venta;
      this.id_caja_diaria = options.id_caja_diaria;
      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      var self = this;
      $.ajax({
        "url":"tarjetas/function/listado/",
        "type":"post",
        "data":{
          "id_punto_venta":self.id_punto_venta,
          "id_caja_diaria":self.id_caja_diaria,
        },
        "dataType":"json",
        "success":function(r) {
          var importe = 0, interes = 0, total = 0, contador = 0;
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            var tr = "<tr>";
            tr+="<td><label class='i-checks m-b-none'><input type='checkbox'><i></i></label></td>";
            tr+="<td>"+o.fecha+"</td>";
            tr+="<td>"+o.comprobante+"</td>";
            tr+="<td>"+o.tarjeta+"</td>";
            tr+="<td>"+o.lote+"</td>";
            tr+="<td>"+o.cupon+"</td>";
            tr+="<td>"+o.cuotas+"</td>";
            tr+="<td>"+Number(o.importe).toFixed(2)+"</td>";
            tr+="<td>"+Number(o.interes).toFixed(2)+"</td>";
            tr+="<td>"+Number(o.total).toFixed(2)+"</td>";
            tr+="</tr>";
            importe += parseFloat(o.importe);
            interes += parseFloat(o.interes);
            total += parseFloat(o.total);
            contador++;
            $(self.el).find("tbody").append(tr);
          }
          $(self.el).find("#caja_diaria_cantidad_cupones").html("Cantidad de cupones: "+contador);
          $(self.el).find("#caja_diaria_total_importe").html(Number(importe).toFixed(2));
          $(self.el).find("#caja_diaria_total_intereses").html(Number(interes).toFixed(2));
          $(self.el).find("#caja_diaria_total").html(Number(total).toFixed(2));
        }
      });
    },
  });
})(app);


(function ( views, models ) {

  views.GastoEditView = app.mixins.View.extend({

    template: _.template($("#gastos_edit_panel_template").html()),
    
    myEvents: {
      "click .guardar": "guardar",
    },

    initialize: function() {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.render();
    },

    render: function() {
      var self = this;
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));
      
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        render: "#gastos_proveedor",
        firstOptions: ["<option value='0'>Sin especificar</option>"],
        name: "id_proveedor",
        selected : self.model.get("id_proveedor"),
      });
      new app.mixins.Select({
        modelClass: app.models.TipoGasto,
        url: "tipos_gastos/",
        render: "#gastos_tipo",
        firstOptions: ["<option value='0'>Sin especificar</option>"],
        name: "id_tipo_gasto",
        selected : self.model.get("id_tipo_gasto"),
      });
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
        if (this.$("#gastos_total").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#gastos_total").focus();
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
          "id_tipo_gasto":$(self.el).find("#gastos_tipo").val(),
          "id_proveedor":$(self.el).find("#gastos_proveedor").val(),
          "total":$(self.el).find("#gastos_total").val(),
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

  app.views.CajaDiariaGastosTableView = app.mixins.View.extend({

    template: _.template($("#caja_diaria_gastos_panel_template").html()),

    myEvents: {
      "click .nuevo_gasto":"nuevo_gasto",
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      }
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.id_punto_venta = options.id_punto_venta;
      this.id_caja_diaria = (typeof options.id_caja_diaria == "undefined") ? 0 : options.id_caja_diaria;
      this.fecha = options.fecha;
      var obj = { permiso: this.permiso };
      $(this.el).html(this.template(obj));
      this.render_gastos()
    },

    render_gastos: function() {
      var self = this;
      $.ajax({
        "url":"gastos/function/listado/",
        "type":"post",
        "data":{
          "id_punto_venta":self.id_punto_venta,
          "id_caja_diaria":self.id_caja_diaria,
        },
        "dataType":"json",
        "success":function(r) {
          var total = 0;
          $(self.el).find("tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            var item = new app.views.GastoItem({
              "model":new app.models.Gasto(o),
              "tabla":self,
            });
            total += parseFloat(o.total);
            $(self.el).find("tbody").append(item.el);
          }
          window.total_gastos = total;
          $(self.el).find("#caja_diaria_gastos_total").html("$ "+Number(total).toFixed(2));
        }
      });
    },

    nuevo_gasto: function() {
      var self = this;
      var edicion = new app.views.GastoEditView({
        model: new app.models.Gasto({
          "fecha":self.fecha,
          "id_punto_venta":self.id_punto_venta,
          "id_caja_diaria":self.id_caja_diaria,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":360,
        "height":500,
        "callback":function(){
          self.render_gastos();
        }
      });
    }

  });
})(app);



(function ( app ) {

  app.views.GastoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#gastos_item').html()),
    myEvents: {
      "click .edit": "editar",
      "click .ver": "editar",
      "click .delete": "borrar",
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
      var edicion = new app.views.GastoEditView({
        model: self.model
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":360,
        "height":500,
        "callback":function(){
          self.tabla.render_gastos();
        },
      });
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy(); // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        this.tabla.render_gastos();
      }
    },
  });

})( app );