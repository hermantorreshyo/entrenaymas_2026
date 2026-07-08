(function ( models ) {
  models.Gasto = Backbone.Model.extend({
    urlRoot: "gastos/",
    defaults: {
      id_tipo_gasto : 0,
      total: 0,
      id_proveedor: 0,
      fecha: "",
      proveedor: "",
      tipo_gasto: "",
      id_caja_diaria: 0,
      id_punto_venta: 0,
      id_empresa: ID_EMPRESA,
      observaciones: "",
    }
  });
})( app.models );

(function ( views, models ) {

  views.EditarGastoView = app.mixins.View.extend({

    template: _.template($("#editar_gasto_template").html()),
    
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
        modelClass: app.models.TipoGasto,
        url: "tipos_gastos/",
        render: "#gastos_tipo",
        firstOptions: ["<option value='0'>Sin especificar</option>"],
        name: "id_tipo_gasto",
        selected : self.model.get("id_tipo_gasto"),
      });

      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createdatepicker(this.$("#gastos_fecha"),fecha);

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

  app.views.ListadoGastosView = app.mixins.View.extend({

    template: _.template($("#listado_gastos_panel_template").html()),

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
        "dataType":"json",
        "success":function(r) {
          var total = 0;
          $(self.el).find("tbody").empty();
          for(var i=0;i<r.results.length;i++) {
            var o = r.results[i];
            var item = new app.views.ListadoGastoItem({
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
      var edicion = new app.views.EditarGastoView({
        model: new app.models.Gasto(),
      });
      crearLightboxHTML({
        "html":edicion.el,
        "width":500,
        "height":500,
        "callback":function(){
          self.render_gastos();
        }
      });
    }

  });
})(app);



(function ( app ) {

  app.views.ListadoGastoItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#listado_gastos_item').html()),
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