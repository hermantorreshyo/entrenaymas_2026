(function ( models ) {
  models.PresGasto = Backbone.Model.extend({
    urlRoot: "pres_caja_diaria",
    defaults: {
      id_concepto : 0,
      concepto: "",
      monto: "",
      fecha: "",
      id_sucursal: 0,
      id_empresa: ID_EMPRESA,
      observaciones: "",
      usuario: "",
      tipo: "S", // E = Entrada, S = SALIDA
      id_prestamo: 0,
      estado_facturacion: 0,
    }
  });
})( app.models );

(function (collections, model, paginator) {
  collections.PresCajasDiarias = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 100,
    },        
    paginator_core: {
      url: "pres_caja_diaria/function/buscar/"
    }
  });
})( app.collections, app.models.PresGasto, Backbone.Paginator);


(function ( app ) {

  app.views.PresCajaDiariaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#pres_cajas_diarias_item').html()),
    myEvents:{
      "click .ver":function(){
        var self = this;
        this.model.fetch({
          "success":function() {
            var edicion = new app.views.PresGastoEditView({
              model: self.model,
              view: self.view,
            });
            crearLightboxHTML({
              "html":edicion.el,
              "width":500,
              "height":500,
            });
          }
        })
      }
    },
    initialize: function(options) {
      this.options = options;
      this.view = options.view;
      _.bindAll(this);
    },
    render: function() {
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));
      this.$('[data-toggle="tooltip"]').tooltip(); 
      return this;
    },
  });

})( app );



(function ( app ) {

  app.views.PresCajasDiariasTableView = app.mixins.View.extend({

    template: _.template($("#pres_cajas_diarias_panel_template").html()),

    myEvents: {
      "click .buscar":"buscar",
      "click .exportar_excel":"exportar",
      "change #pres_cajas_diarias_sucursales":"buscar",
      "change #pres_cajas_diarias_conceptos":"buscar",
      "click .nuevo_gasto": "nuevo_gasto",
      "click #listado_caja":"buscar",
      "click #ver_resumen_caja":function(e){
        e.stopPropagation();
        e.preventDefault();
        this.ver_resumen_caja();
      },
    },

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
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
      $(this.el).find(".pagination_container").html(pagination.el);

      createdatepicker(this.$("#pres_cajas_diarias_desde"),new Date());
      createdatepicker(this.$("#pres_cajas_diarias_hasta"),new Date());

      if (ID_SUCURSAL != 0) this.buscar();
    },

    nuevo_gasto: function() {
      var self = this;

      var id_sucursal = this.$("#pres_cajas_diarias_sucursales").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        this.$("#pres_cajas_diarias_sucursales").focus();
        return;
      }

      var edicion = new app.views.PresGastoEditView({
        model: new app.models.PresGasto({
          "fecha":self.fecha,
          "id_sucursal":id_sucursal,
          "id_punto_venta":self.id_punto_venta,
          "id_pres_caja_diaria":self.id_pres_caja_diaria,
        }),
        view: self,
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "callback":function(){
          self.collection.pager();
        }
      });
    },

    addAll : function () {
      var self = this;
      this.total_otorgaciones = 0;
      this.total_gastos = 0;
      this.total_retiros = 0;
      this.total_ingresos = 0;
      this.total_pagos = 0;
      this.total_otros = 0;
      this.total_descuento = 0;

      $(this.el).find("tbody").empty();
      this.saldo = parseFloat(this.collection.meta("saldo_inicial"));
      var elementos = new Array();
      this.collection.each(function(item){
        elementos.push(self.addOne(item));
      });

      var tr = '<tr><td class="bg-important p10 fs16" colspan="4"></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold">SUBTOTALES:</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-success">'+"$ "+Number(this.total_ingresos).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-success">'+"$ "+Number(this.total_pagos).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-info">'+"$ "+Number(this.total_descuento).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-danger">'+"$ "+Number(this.total_otorgaciones).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-danger">'+"$ "+Number(this.total_retiros).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-danger">'+"$ "+Number(this.total_gastos).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"><span class="bold text-danger">'+"$ "+Number(this.total_otros).format()+'</span></td>';
      tr+='<td class="tar bg-important p10 fs16"></td></tr>';
      $(this.el).find("tbody").append(tr);

      $(this.el).find("tbody").append("<tr><td colspan='5'></td><td colspan='6' class='tar'>Saldo Inicial</td><td class='tar'>$ "+Number(this.collection.meta("saldo_inicial")).toFixed(2)+"</td></tr>");

      for(var i=0; i< elementos.length; i++) {
        var e = elementos[i];
        $(this.el).find("tbody").append(e);
      }
    },

    addOne : function ( item ) {
      var self = this;
      var m = parseFloat(item.get("monto")) - parseFloat(item.get("descuento"));

      // Calculamos el saldo parcial
      if (item.get("tipo") == "S") {
        var saldo_parcial = this.saldo - m;
      } else if (item.get("tipo") == "E") {
        var saldo_parcial = this.saldo + m;
      }
      item.set({
        "saldo":saldo_parcial,
      });
      this.saldo = saldo_parcial;

      // Subtotales segun el concepto
      if (item.get("id_concepto") == 242) {
        this.total_retiros += m;
      } else if (item.get("id_concepto") == 271) {
        this.total_otorgaciones += m;
      } else if (item.get("id_concepto") == 373) {
        this.total_gastos += m;        
      } else if (item.get("id_concepto") == 241) {
        this.total_pagos += m;
      } else if (item.get("id_concepto") == 272) {
        this.total_ingresos += m;
      } else {
        this.total_otros += m;
      }
      this.total_descuento += parseFloat(item.get("descuento"));

      var view = new app.views.PresCajaDiariaItem({
        model: item,
        view: self,
      });
      return view.render().el;
    },

    exportar: function() {
      var desde = this.$("#pres_cajas_diarias_desde").val();
      desde = (isEmpty(desde)) ? "" : desde.replace(/\//g,"-");
      var hasta = this.$("#pres_cajas_diarias_hasta").val();
      hasta = (isEmpty(hasta)) ? "" : hasta.replace(/\//g,"-");
      var id_sucursal = this.$("#pres_cajas_diarias_sucursales").val();
      var id_concepto = this.$("#pres_cajas_diarias_conceptos").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        this.$("#pres_cajas_diarias_sucursales").focus();
        return;
      }
      window.open("pres_caja_diaria/function/exportar/?id_sucursal="+id_sucursal+"&id_concepto="+id_concepto+"&desde="+desde+"&hasta="+hasta+"&limit=0&offset=999999");
    },

    buscar: function() {

      if (this.$("#ver_resumen_caja").parent().hasClass("active")) {
        this.ver_resumen_caja();
        return;
      }
      var desde = this.$("#pres_cajas_diarias_desde").val();
      desde = (isEmpty(desde)) ? "" : desde.replace(/\//g,"-");
      var hasta = this.$("#pres_cajas_diarias_hasta").val();
      hasta = (isEmpty(hasta)) ? "" : hasta.replace(/\//g,"-");
      var id_sucursal = this.$("#pres_cajas_diarias_sucursales").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        this.$("#pres_cajas_diarias_sucursales").focus();
        return;
      }
      var id_concepto = this.$("#pres_cajas_diarias_conceptos").val();
      var filtros = {
        "desde":desde,
        "hasta":hasta,
        "id_sucursal":id_sucursal,
        "id_concepto":id_concepto,
      }
      if (!isEmpty(desde)) filtros.offset = 99999;
      this.collection.server_api = filtros;
      this.collection.pager();
    },

    ver_resumen_caja: function(e) {
      var self = this;
      var desde = this.$("#pres_cajas_diarias_desde").val();
      desde = (isEmpty(desde)) ? "" : desde;
      var hasta = this.$("#pres_cajas_diarias_hasta").val();
      hasta = (isEmpty(hasta)) ? "" : hasta;
      var id_sucursal = this.$("#pres_cajas_diarias_sucursales").val();
      if (id_sucursal == 0) {
        alert("Por favor seleccione una sucursal");
        this.$("#pres_cajas_diarias_sucursales").focus();
        return;
      }
      this.$(".nav-tabs-2 .active").removeClass("active");
      this.$(".tab-pane.active").removeClass("active");
      this.$("#ver_resumen_caja").parents("li").addClass("active");
      this.$("#tab_caja_diaria_1").addClass("active");
      $.ajax({
        "url":"pres_cajas_movimientos/function/resumen_compras_arbol/",
        "dataType":"json",
        "type":"post",
        "data":{
          "desde":desde,
          "hasta":hasta,
          "id_sucursal":id_sucursal,
        },
        "success":function(r) {
          self.$("#pres_cajas_diarias_table_resumen tbody").empty();
          for(var i=0;i< r.results.length; i++) {
            var c1 = r.results[i];
            self.append_table(c1,0);
            if (c1.children.length > 0) {
              for(var j=0;j< c1.children.length; j++) {
                var c2 = c1.children[j];
                self.append_table(c2,1);
                if (c2.children.length > 0) {
                  for(var k=0;k< c2.children.length; k++) {
                    var c3 = c2.children[k];
                    self.append_table(c3,2);
                  }
                }
              }
            }
          }
        }
      })
    },

    append_table: function(obj,nivel) {
      var tr = "<tr>";
      tr+="<td>";
      if (nivel == 0) tr+="<span class='text-info'>";
      if (nivel == 1) tr+="&nbsp;&nbsp;-&nbsp;&nbsp;";
      else if (nivel == 2) tr+="&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;";
      tr+=obj.nombre;
      if (nivel == 0) tr+="</span>";
      tr+="</td>";
      tr+="<td class='tar'>";
      if (nivel == 0) tr+="<span class='text-info'>";
      tr+= Number(obj.total).toFixed(2);
      if (nivel == 0) tr+="</span>";
      tr+="</td>";
      this.$("#pres_cajas_diarias_table_resumen tbody").append(tr);
    },

  });
})(app);


(function ( views, models ) {

  views.PresGastoEditView = app.mixins.View.extend({

    template: _.template($("#pres_gastos_edit_panel_template").html()),
    
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "click .ver_prestamo": "ver_prestamo",
    },

    initialize: function(options) {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      this.view = options.view;
      this.guardando = 0;
      _.bindAll(this);
      this.render();
    },

    ver_prestamo: function() {
      var self = this;
      var id_prestamo = self.model.get("id_prestamo");
      if (id_prestamo == 0) return;
      var modelo = new app.models.Prestamo({"id":id_prestamo});
      modelo.fetch({
        "success":function(){
          var v = new app.views.PrestamoEditView({
            model: modelo,
          });
          crearLightboxHTML({
            "html":v.el,
            "width":1100,
            "height":140,
          });
        }
      });
    },

    render: function() {
      var self = this;
      var obj = {
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker($(this.el).find("#pres_gastos_fecha"),fecha);
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

        if (this.$("#pres_gastos_tipo").val()==0) {
          alert("Por favor ingrese un concepto.");
          this.$("#pres_gastos_tipo").focus();
          return false;          
        }
        if (this.$("#pres_gastos_monto").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#pres_gastos_monto").focus();
          return false;
        }
        return true;
      } catch(e) {
        return false;
      }
    },

    eliminar: function() {
      if (!confirm("Realmente desea borrar el movimiento?")) return;
      var self = this;
      $.ajax({
        "url":"pres_caja_diaria/function/borrar_movimiento/",
        "type":"post",
        "data":{
          "id":self.model.id,
          "id_sucursal":self.model.get("id_sucursal"),
        },
        "dataType":"json",
        "success":function(r){
          $('.modal:last').modal('hide');
          if (r.error == 0) self.view.buscar();
        },
        "error":function() {
          $('.modal:last').modal('hide');
        }
      });
    },

    guardar: function() {
      var self = this;
      if (this.guardando == 1) return;
      if (this.validar()) {

        var tipo = $(self.el).find("#pres_gastos_tipo option:selected").data("totaliza_en");
        if (tipo != "E" && tipo != "S") {
          alert("Por favor ingrese un concepto.");
          this.$("#pres_gastos_tipo").focus();
          return false;          
        }

        // Si es un CAJERO, tiene solamente dos operaciones permitidas
        if (PERFIL == 342) {
          var id_concepto = self.$("#pres_gastos_tipo").val();
          if (id_concepto != 241 && id_concepto != 271) {
            alert("Las unicas operaciones permitidas son COBRO DE CUOTAS y OTORGACIONES.");
            return false;
          }
        }

        // Si es un cajero de MAR DEL PLATA, no puede hacer SALIDAS
        if (PERFIL == 342 && ID_SUCURSAL == 44 && tipo == "S") {
          alert("Operacion no permitida.");
          return false;
        }

        this.model.set({
          "id_concepto":$(self.el).find("#pres_gastos_tipo").val(),
          "fecha":$(self.el).find("#pres_gastos_fecha").val(),
          "monto":$(self.el).find("#pres_gastos_monto").val(),
          "tipo":tipo,
        });
        if (this.model.id == null) {
          this.model.set({
            "id":0,
            "id_usuario": ID_USUARIO,
          });
        } else {
          // Sacamos el id_usuario para no volverlo a mandar
          this.model.unset("id_usuario",{"silent":true});
        }
        this.guardando = 1;
        this.model.save({},{
          success: function(model,response) {
            self.guardando = 0;
            model.set({id:response.id});
            $('.modal:last').modal('hide');
            self.view.buscar();
          }
        });
      }
    },

  });

})(app.views, app.models);