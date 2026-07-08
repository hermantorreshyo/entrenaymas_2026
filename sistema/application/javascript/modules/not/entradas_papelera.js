(function ( app ) {

  app.views.PapeleraReciclajeEntradasView = app.mixins.View.extend({

    template: _.template($("#papelera_reciclaje_entradas_template").html()),
            
    myEvents: {
      "change #entradas_buscar":"buscar",
      "click #entradas_buscar_avanzada_btn":"buscar_avanzada",
      "keydown #entradas_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#entradas_texto").focus(); }
      },
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.entradas_filter = (typeof window.entradas_filter != "undefined") ? window.entradas_filter : "";
      window.entradas_id_categoria = (typeof window.entradas_id_categoria != "undefined") ? window.entradas_id_categoria : 0;
      window.entradas_fecha = (typeof window.entradas_fecha != "undefined") ? window.entradas_fecha : "";
      window.entradas_page = (typeof window.entradas_page != "undefined") ? window.entradas_page : 1;
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
            
      var r = workspace.crear_select(categorias_noticias,"",window.entradas_id_categoria);
      this.$("#entradas_buscar_categorias").html(r).select2({}).change(function(){
        window.entradas_id_categoria = $(this).val();
      });
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.entradas_filter != this.$("#entradas_buscar").val().trim()) {
        window.entradas_filter = this.$("#entradas_buscar").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.entradas_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.entradas_filter),
        "id_categoria":window.entradas_id_categoria,
        "eliminada":1,
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.entradas_page);
    },
        
    buscar_avanzada: function() {
      var self = this;
      // Buscamos por categoria
      var c = self.$("#entradas_buscar_categorias").val();
      self.id_categoria = c;
      this.buscar();
    },
        
    addAll : function () {
      window.entradas_page = this.pagination.getPage();
      $(this.el).find(".tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (this.collection.length > 0) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        } else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var view = new app.views.PapeleraReciclajeEntradaItem({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
    eliminar_lote: function() {
      var self = this;
      var checks = this.$("#entradas_tabla .check-row:checked");
      if (checks.length == 0) return;
      if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
        $(checks).each(function(i,e){
          var id = $(e).val();
          var art = self.collection.get(id);
          art.destroy();  // Eliminamos el modelo
          $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
        });
      }            
    },

  });

})(app);


(function ( app ) {
  app.views.PapeleraReciclajeEntradaItem = app.mixins.View.extend({
        
    template: _.template($("#papelera_reciclaje_entrada_item_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "keyup .radio":function(e) {
        if (e.which == 13) { this.seleccionar(); }
        e.stopPropagation();
      },
      "focus .radio":function(e) {
        $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(e.currentTarget).parents("tr").addClass("fila_roja");
        $(e.currentTarget).prop("checked",true);
        e.stopPropagation();
        e.preventDefault();
        return false;
      },
      "blur .radio":function(e) {
        $(e.currentTarget).parents(".tbody").find("tr").removeClass("fila_roja");
        $(".radio").prop("checked",false);
        e.stopPropagation();
        e.preventDefault();
        return false;
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          $.ajax({
            "url":"entradas/function/borrar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
            },
          });
        }
        return false;
      },
      "click .restaurar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        self.model.set({"eliminada":0});
        this.change_property({
          "table":"not_entradas",
          "url":"entradas/function/change_property/",
          "attribute":"eliminada",
          "value":0,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_entrada_seleccionado = this.model.get("codigo");
        window.entrada_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#entrada/"+this.model.id;
      }
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.render();
    },
    render: function() {
      var obj = { seleccionar: this.habilitar_seleccion };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);