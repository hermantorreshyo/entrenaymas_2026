// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TipoGasto = Backbone.Model.extend({
    urlRoot: "tipos_gastos/",
    defaults: {
      nombre: "",
      codigo: "",
      descripcion: "",
      id_padre: 0,
      id_tipo_alicuota_iva: 0,
      totaliza_en: "",
      activo: 1,
    }
  });
	  
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.TiposGastos = paginator.requestPager.extend({

	model: model,

	paginator_core: {
	  url: "tipos_gastos/"
	}
	  
  });

})( app.collections, app.models.TipoGasto, Backbone.Paginator);



// ----------------------
//   VISTA DEL ARBOL
// ----------------------

(function ( app ) {

  app.views.GastosTreeView = app.mixins.View.extend({

    template: _.template($("#gastos_tree_panel_template").html()),
  
    myEvents: {
      "click .dd3-item":function(e) {
        var self = this;
        if (this.lightbox) {
          var id = $(e.currentTarget).data("id");
          var cat = new app.models.TipoGasto({ id: id });
          cat.fetch({
            "success":function(){
              self.seleccionar(cat);
            }
          });
        }
      },
      "click .editar":function(e) {
        var self = this;
        e.preventDefault();
        if (this.lightbox) {
          self.seleccionar(cat);
        } else {
          var id = $(e.currentTarget).parents(".dd-item").data("id");
          var cat = new app.models.TipoGasto({ id: id });
          cat.fetch({
            "success":function(){
              self.ver(cat);
            }
          });
        }
      },
      "click .nuevo":function() {
        var modelo = new app.models.TipoGasto();
        this.ver(modelo);
      },
    },

    ver: function(modelo) {
      var categoria = new app.views.TipoGastoEditView({
        model: modelo,
        permiso: 3,
      });
      var d = $("<div/>").append(categoria.el);
      crearLightboxHTML({
        "html":d,
        "width":600,
        "height":500,
      });
    },

    seleccionar: function(modelo) {
      window.gasto_seleccionado = modelo;
      $('.modal:last').modal('hide');
    },
  
    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.lightbox = (this.options.lightbox == undefined) ? false : this.options.lightbox;
      this.render();
    },
    
    render : function() {
      
      var self = this;
      var obj = { lightbox: this.lightbox };
      $(this.el).html(this.template(obj));

      this.$('.dd').nestable();
      this.$('.dd').on('change',this.reorder);      

      return this;    
    }, 

    reorder: function() {
      var serialize = this.$('.dd').nestable('serialize');
      $.ajax({
        "url":"tipos_gastos/function/reorder/",
        "type":"post",
        "dataType":"json",
        "data":{
          "datos":serialize,
        }
      });
    },

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.TipoGastoEditView = app.mixins.View.extend({

    template: _.template($("#tipos_gastos_edit_panel_template").html()),
  
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.lightbox = (this.options.lightbox == undefined) ? false : this.options.lightbox;
      this.render();
    },
    
    eliminar : function() {
      if (!confirmar("Realmente desea eliminar este elemento?")) return;
      var self = this;	  
      var tipoGasto = new app.models.TipoGasto({
        "id":self.model.id
      });
      tipoGasto.destroy();
      tipoGasto.fetch({
        "success":function() {
          location.reload();
        }
      });
    },

    render: function() {
      var self = this;
  
    	var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
    	$.extend(obj,this.model.toJSON());
    	$(this.el).html(this.template(obj));      
      $(this.el).find("#tipos_gastos_nombre").focus();
      return this;
    },

    guardar: function() {
      var self = this;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      
      var id_padre = $(this.el).find("#tipos_gastos_padre").val();
      if (isEmpty(id_padre)) id_padre = 0;
      this.model.set({
        "id_padre" : id_padre,
        "id_tipo_alicuota_iva": $(this.el).find("#tipos_gastos_iva").val(),
        "totaliza_en": $(this.el).find("#tipos_gastos_totaliza_en").val(),
        "activo":1,
      });
      
      this.model.save({
          "id_empresa":ID_EMPRESA
        },{
        success: function(model,response) {
          if (self.lightbox) {
            $('.modal:last').modal('hide');
          } else {
            location.reload();
          }
        }
      });
    },
	
    validar: function() {
      try {
        var self = this;
        validate_input("tipos_gastos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },
	
  });

})(app.views, app.models);



(function ( views, models ) {

  views.TipoGastoMiniEditView = app.mixins.View.extend({

    template: _.template($("#tipo_gasto_edit_mini_panel_template").html()),
  
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          $(e.currentTarget).parent().next().find(".tab").focus();
        }
      },
      "click .cerrar":"cerrar",
      "keyup .tab":function(e) {
        if (e.which == 27) this.cerrar();
      },
      "keypress .guardar":function(e) {
        if (e.keyCode == 13) this.guardar();
      },
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.input = this.options.input;
      this.onSave = this.options.onSave;
      this.callback = this.options.callback;
      this.render();
    },

    focus: function() {
      $(this.el).find("#tipo_gasto_mini_nombre").focus();
    },
    
    render: function() {
      var self = this;
  
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));      
      $(this.el).find("#tipo_gasto_mini_nombre").focus();
      return this;
    },

    guardar: function() {
      var self = this;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      
      var id_padre = $(this.el).find("#tipo_gasto_mini_padre").val();
      if (isEmpty(id_padre)) id_padre = 0;
      this.model.set({
        "id_padre" : id_padre,
        "id_tipo_alicuota_iva": $(this.el).find("#tipo_gasto_mini_iva").val(),
        "totaliza_en": $(this.el).find("#tipos_gastos_mini_totaliza_en").val(),
      });
      
      this.model.save({
          "id_empresa":ID_EMPRESA
        },{
        success: function(model,response) {
          if (typeof self.onSave != "undefined") self.onSave(model);
          if (typeof self.callback != "undefined") self.callback(model.id);
          model.set({"children":[]});

          // Tenemos que actualizar el array de window.tipos_gastos
          if (typeof window.tipos_gastos_plana != "undefined") window.tipos_gastos_plana.push(model.toJSON());
          if (typeof window.tipos_gastos != "undefined") window.tipos_gastos.push(model.toJSON());
          self.cerrar();
        }
      });
    },
  
    validar: function() {
      try {
        var self = this;
        validate_input("tipo_gasto_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },

    cerrar: function() {
      $(this.el).parents(".customcomplete").remove();
    },
  
  });

})(app.views, app.models);
