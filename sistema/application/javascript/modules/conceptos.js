(function ( models ) {

  models.Concepto = Backbone.Model.extend({
    urlRoot: "conceptos/",
    defaults: {
      nombre: "",
      codigo: "",
      descripcion: "",
      id_padre: 0,
      id_tipo_alicuota_iva: 0,
      totaliza_en: "", // C = Compras, G = Gastos, V = Ventas
    }
  });
    
})( app.models );


(function (collections, model, paginator) {

  collections.Conceptos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "conceptos/"
    }
  });

})( app.collections, app.models.Concepto, Backbone.Paginator);


(function ( app ) {

  app.views.ConceptosTreeView = app.mixins.View.extend({

    template: _.template($("#conceptos_tree_panel_template").html()),
  
    myEvents: {
      "click .editar":function(e) {
        var self = this;
        e.preventDefault();
        if (this.lightbox) {
          self.seleccionar(cat);
        } else {
          var id = $(e.currentTarget).parents(".dd-item").data("id");
          var cat = new app.models.Concepto({ id: id });
          cat.fetch({
            "success":function(){
              self.ver(cat);
            }
          });
        }
      },
      "click .nuevo":function() {
        var self = this;
        var modelo = new app.models.Concepto({
          "totaliza_en":self.totaliza_en,
        });
        this.ver(modelo);
      },
    },

    ver: function(modelo) {
      var categoria = new app.views.ConceptoEditView({
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
      this.totaliza_en = (this.options.totaliza_en == undefined) ? "G" : this.options.totaliza_en;
      this.render();
    },
    
    render : function() {
      
      var self = this;
      var obj = { lightbox: this.lightbox, totaliza_en: this.totaliza_en };
      $(this.el).html(this.template(obj));

      this.$('.dd').nestable();
      this.$('.dd').on('change',this.reorder);      

      return this;    
    }, 

    reorder: function() {
      var serialize = this.$('.dd').nestable('serialize');
      $.ajax({
        "url":"conceptos/function/reorder/",
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

  views.ConceptoEditView = app.mixins.View.extend({

    template: _.template($("#conceptos_edit_panel_template").html()),
  
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
      var concepto = new app.models.Concepto({
        "id":self.model.id
      });
      concepto.destroy();
      concepto.fetch({
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
      $(this.el).find("#conceptos_nombre").focus();
      return this;
    },

    guardar: function() {
      var self = this;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      
      var id_padre = $(this.el).find("#conceptos_padre").val();
      if (isEmpty(id_padre)) id_padre = 0;
      this.model.set({
        "id_padre" : id_padre,
        "id_tipo_alicuota_iva": $(this.el).find("#conceptos_iva").val(),
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
        validate_input("conceptos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        return false;
      }
    },
  
  });

})(app.views, app.models);



(function ( views, models ) {

  views.ConceptoMiniEditView = app.mixins.View.extend({

    template: _.template($("#concepto_edit_mini_panel_template").html()),
  
    myEvents: {
      "click .guardar": "guardar",
      "click .eliminar": "eliminar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          $(e.currentTarget).parent().next().find(".tab").focus();
        }
      },
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
      $(this.el).find("#concepto_mini_nombre").focus();
    },
    
    render: function() {
      var self = this;
  
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));      
      $(this.el).find("#concepto_mini_nombre").focus();
      return this;
    },

    guardar: function() {
      var self = this;
      if (this.model.id == null) {
        this.model.set({id:0});
      }
      
      var id_padre = $(this.el).find("#concepto_mini_padre").val();
      if (isEmpty(id_padre)) id_padre = 0;
      this.model.set({
        "id_padre": id_padre,
      });
      
      this.model.save({
          "id_empresa":ID_EMPRESA
        },{
        success: function(model,response) {
          if (typeof self.onSave != "undefined") self.onSave(model);
          if (typeof self.callback != "undefined") self.callback(model.id);
          self.cerrar();
          // Tenemos que actualizar el array de window.conceptos
          // window.tipos_gastos_plana.push(model.toJSON());
        }
      });
    },
  
    validar: function() {
      try {
        var self = this;
        validate_input("concepto_mini_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
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
