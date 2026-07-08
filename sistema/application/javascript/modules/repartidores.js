(function ( models ) {

  models.Repartidor = Backbone.Model.extend({
    urlRoot: "repartidores/",
    defaults: {
      nombre: "",
      email: "",
      id_empresa: ID_EMPRESA,
      telefono: "",
      comision: 0,
      cuit: "",
      path: "",
      password: "",
      activo: 1,
      efectivo: 0,
      limite_efectivo: 0,
      metros_por_minuto: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.Repartidores = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "repartidores/function/buscar/"
    }
  });
})( app.collections, app.models.Repartidor, Backbone.Paginator);


(function ( app ) {
  app.views.RepartidorItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#repartidores_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"repartidores",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
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
      location.href="app/#repartidor/"+this.model.id;
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

  app.views.RepartidoresTableView = app.mixins.View.extend({

   template: _.template($("#repartidores_panel_template").html()),

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection,
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      self.collection.server_api = {}
      if (SOLO_USUARIO == 1) self.collection.server_api.id_usuario = ID_USUARIO;
      self.collection.server_api.buscar_saldo = 1;

      // Vamos a buscar los elementos y lo paginamos
      self.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.RepartidorItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.RepartidorEditView = app.mixins.View.extend({

    template: _.template($("#repartidores_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .nuevo": "limpiar",
    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      return this;
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        
        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
          "path":$("#hidden_path").val(),
        },{
          success: function(model,response) {
            location.href="app/#repartidores";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.Repartidor();
      this.render();
    },

  });

})(app.views, app.models);




// =================================================================
// CUENTA CORRIENTE DE REPARTIDORES


(function ( models ) {
  models.RepartidorCajaMovimiento = Backbone.Model.extend({
    urlRoot: "repartidores_cajas_movimientos/",
    defaults: {
      id_concepto : 0,
      id_usuario: ID_USUARIO,
      id_factura: 0,
      id_punto_venta: 0,
      monto: 0,
      fecha: "",
      concepto: "",
      tipo: 0, // 0 = INGRESO, 1 = EGRESO
      estado: 0, // 0 = REALIZADO, 1 = PENDIENTE
      id_repartidor: 0,
      id_empresa: ID_EMPRESA,
      observaciones: "",
      subtotal: 0, // Atributo calculado para dar el subtotal del saldo
      path: "",
    }
  });
})( app.models );

(function ( views, models ) {

  views.RepartidorCajaMovimientoView = app.mixins.View.extend({

    template: _.template($("#repartidor_caja_movimiento_template").html()),
    
    myEvents: {
      "click .cerrar":function() {
        $('.modal:last').modal('hide');
      },
      "click .guardar": "guardar",
      "click .agregar_concepto":function(e) {
        var self = this;
        var totaliza_en = "G";
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

    initialize: function(options) {
      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);
      this.permiso = (typeof options.permiso == "undefined") ? 0 : options.permiso;
      this.render();
    },

    cargar_conceptos: function(r) {
      var self = this;
      var r = workspace.crear_select(r,"",self.model.get("id_concepto"));
      this.$("#repartidores_cajas_movimientos_tipo").html(r);
      this.$("#select2-repartidores_cajas_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#repartidores_cajas_movimientos_tipo").select2({});
    },

    render: function() {
      var self = this;
      var obj = {
        permiso: self.permiso,
        id:this.model.id,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createtimepicker(this.$("#repartidores_cajas_movimientos_fecha"),fecha);

      this.$("#select2-repartidores_cajas_movimientos_tipo-container").parents(".select2-container").remove();
      this.$("#repartidores_cajas_movimientos_tipo").select2({});
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
        if (this.$("#repartidores_cajas_movimientos_monto").val()==0) {
          alert("Por favor ingrese un monto.");
          this.$("#repartidores_cajas_movimientos_monto").focus();
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
          "fecha":self.$("#repartidores_cajas_movimientos_fecha").val(),
          "id_concepto":$(self.el).find("#repartidores_cajas_movimientos_tipo").val(),
          "monto":$(self.el).find("#repartidores_cajas_movimientos_monto").val(),
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

  app.views.RepartidoresCajasMovimientosView = app.mixins.View.extend({

    template: _.template($("#repartidores_cajas_movimientos_panel_template").html()),

    myEvents: {
      "change .check-row2":"sumar",
      "click .nuevo_gasto":"nuevo_gasto",
      "click .nuevo_ingreso":"nuevo_ingreso",
      "change #repartidores_cajas_movimientos_desde":"render_repartidores_cajas_movimientos",
      "change #repartidores_cajas_movimientos_hasta":"render_repartidores_cajas_movimientos",
      "change #repartidores_cajas_movimientos_conceptos":"render_repartidores_cajas_movimientos",
      "change #repartidores_cajas_movimientos_repartidores":function() {
        this.id_repartidor = this.$("#repartidores_cajas_movimientos_repartidores").val();
        this.render_repartidores_cajas_movimientos();
      },
      "click .buscar":"render_repartidores_cajas_movimientos",
      "click .exportar":"exportar",
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

      this.$("#repartidores_cajas_movimientos_monto").html("$ "+Number(total).format(2));
      this.$("#repartidores_cajas_movimientos_cantidad").html(j);
      return false;
    },

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.tipo = (typeof options.tipo == "undefined") ? 0 : options.tipo;
      this.id_repartidor = (typeof options.id_repartidor == "undefined") ? 0 : options.id_repartidor;
      this.id_concepto = (typeof options.id_concepto == "undefined") ? 0 : options.id_concepto;
      this.permiso = (typeof options.permiso == "undefined") ? 0 : options.permiso;
      window.repartidor_caja_movimientos_desde = (typeof window.repartidor_caja_movimientos_desde == "undefined") ? moment().format("DD/MM/YYYY") : window.repartidor_caja_movimientos_desde;
      window.repartidor_caja_movimientos_hasta = (typeof window.repartidor_caja_movimientos_hasta == "undefined") ? moment().format("DD/MM/YYYY") : window.repartidor_caja_movimientos_hasta;
      var obj = { 
        permiso: this.permiso,
        tipo: this.tipo,
        id_repartidor: this.id_repartidor,
        id_concepto: this.id_concepto,
        desde: window.repartidor_caja_movimientos_desde,
        hasta: window.repartidor_caja_movimientos_hasta,
      };
      $(this.el).html(this.template(obj));

      createdatepicker(this.$("#repartidores_cajas_movimientos_desde"),window.repartidor_caja_movimientos_desde);
      createdatepicker(this.$("#repartidores_cajas_movimientos_hasta"),window.repartidor_caja_movimientos_hasta);

      var pars = "";
      if (SOLO_USUARIO == 1) pars = "?id_usuario="+ID_USUARIO;
      new app.mixins.Select({
        modelClass: app.models.Repartidor,
        url: "repartidores/function/buscar/"+pars,
        render: "#repartidores_cajas_movimientos_repartidores",
        selected: self.id_repartidor,
      });      

      this.render_repartidores_cajas_movimientos()
    },

    render_repartidores_cajas_movimientos: function() {
      var self = this;

      if (this.$("#repartidores_cajas_movimientos_desde").length > 0 && window.repartidor_caja_movimientos_desde != this.$("#repartidores_cajas_movimientos_desde").val().trim()) {
        window.repartidor_caja_movimientos_desde = this.$("#repartidores_cajas_movimientos_desde").val().trim();
      }

      if (this.$("#repartidores_cajas_movimientos_hasta").length > 0 && window.repartidor_caja_movimientos_hasta != this.$("#repartidores_cajas_movimientos_hasta").val().trim()) {
        window.repartidor_caja_movimientos_hasta = this.$("#repartidores_cajas_movimientos_hasta").val().trim();
      }      

      $.ajax({
        "url":"repartidores_cajas_movimientos/function/listado/",
        "data":{
          "id_concepto":self.$("#repartidores_cajas_movimientos_conceptos").val(),
          "desde":window.repartidor_caja_movimientos_desde,
          "hasta":window.repartidor_caja_movimientos_hasta,
          "tipo":self.tipo,
          "id_repartidor":self.id_repartidor,
        },
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          var monto = 0;
          var cantidad = 0;
          var saldo_inicial = parseFloat(r.saldo_inicial);
          $(self.el).find("tbody").empty();
          self.$("tbody").append("<tr><td colspan='2' class='ver'></td><td class='ver'><span class='text-info'>Saldo Inicial</span></td><td colspan='2' class='ver'></td><td class='tar ver number'>$ "+Number(saldo_inicial).format(2)+"</td><td></td><tr>");
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            if (o.estado == 0) {
              if (o.tipo == 0) saldo_inicial = saldo_inicial + parseFloat(o.monto);
              else saldo_inicial = saldo_inicial - parseFloat(o.monto);                
            }
            o.subtotal = saldo_inicial;
            var item = new app.views.RepartidorCajaMovimientoItem({
              "permiso": self.permiso,
              "model":new app.models.RepartidorCajaMovimiento(o),
              "tabla":self,
            });
            $(self.el).find("tbody").append(item.el);
            cantidad++;
          }
          window.monto_repartidores_cajas_movimientos = saldo_inicial - r.saldo_inicial;
        }
      });
    },

    exportar: function() {
      var self = this;
      var titulo = this.$("#repartidores_cajas_movimientos_repartidores option:selected").text();
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
        "filename":(isEmpty(titulo) ? "exportacion" : titulo),
        "title":titulo,
        "data":array,
        "header":header,
      });
    },    

    nuevo_gasto: function() {
      var self = this;
      var edicion = new app.views.RepartidorCajaMovimientoView({
        permiso: self.permiso,
        model: new app.models.RepartidorCajaMovimiento({
          tipo: 1,
          id_repartidor: self.id_repartidor,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_repartidores_cajas_movimientos();
        }
      });
    },

    nuevo_ingreso: function() {
      var self = this;
      var edicion = new app.views.RepartidorCajaMovimientoView({
        permiso: self.permiso,
        model: new app.models.RepartidorCajaMovimiento({
          tipo: 0,
          id_repartidor: self.id_repartidor,
        }),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "escapable":false,
        "callback":function(){
          self.render_repartidores_cajas_movimientos();
        }
      });
    },

  });
})(app);



(function ( app ) {

  app.views.RepartidorCajaMovimientoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#repartidores_cajas_movimientos_item').html()),
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
      this.permiso = this.options.permiso;
      this.render();
    },
    render: function() {
      var obj = {
        id:this.model.id,
        permiso:this.permiso,
      };
      $.extend(obj,this.model.toJSON()); // Extendemos el objeto creado con el modelo de datos
      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      var self = this;
      var edicion = new app.views.RepartidorCajaMovimientoView({
        permiso: self.permiso,
        model: self.model
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "callback":function(){
          self.tabla.render_repartidores_cajas_movimientos();
        },
      });
    },
    borrar: function() {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy(); // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
        this.tabla.render_repartidores_cajas_movimientos();
      }
    },
  });

})( app );