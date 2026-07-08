(function ( models ) {
  models.Maquina = Backbone.Model.extend({
    urlRoot: "maquinas",
    defaults: {
      partes: [],
      id_sector: 0,
      nombre: "",
      codigo: "",
      modelo: "",
      fecha_adquisicion: "",
      garantia: "",
      observaciones: "",
    },
  });
      
})( app.models );


(function (collections, model, paginator) {

  collections.Maquinas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 20,
      order_by: 'nombre',
      order: 'asc',
    },
    paginator_core: {
      url: "maquinas/function/ver",
    }
  });

})( app.collections, app.models.Maquina, Backbone.Paginator);


(function ( app ) {

  app.views.MaquinasTableView = app.mixins.View.extend({

    template: _.template($("#maquinas_resultados_template").html()),
        
    myEvents: {
      "change #maquinas_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      // Filtros de la maquina
      window.maquinas_id_sector = (typeof window.maquinas_id_sector != "undefined") ? window.maquinas_id_sector : 0;
      window.maquinas_filter = (typeof window.maquinas_filter != "undefined") ? window.maquinas_filter : "";
      window.maquinas_page = (typeof window.maquinas_page != "undefined") ? window.maquinas_page : 1;
      window.maquinas_marcadas = new Array();

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
      this.$(".pagination_container").html(this.pagination.el);
      this.$("#maquinas_buscar_sectores").select2();
      return this;
    },
        
    buscar: function() {

      var cambio_parametros = false;

      if (window.maquinas_id_sector != this.$("#maquinas_buscar_sectores").select2("val")) {
        window.maquinas_id_sector = this.$("#maquinas_buscar_sectores").select2("val");  
        cambio_parametros = true;
      }
      if (window.maquinas_filter != this.$("#maquinas_buscar").val()) {
        window.maquinas_filter = this.$("#maquinas_buscar").val();  
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.maquinas_page = 1;

      var datos = {
        "filter":window.maquinas_filter,
        "id_sector":window.maquinas_id_sector,
      };
      this.collection.server_api = datos;
      this.collection.goTo(window.maquinas_page);
    },

    addAll : function () {
      window.maquinas_page = this.pagination.getPage();
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
      var view = new app.views.MaquinasItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },
  });

})(app);



(function ( app ) {
  app.views.MaquinasItemResultados = app.mixins.View.extend({
        
    template: _.template($("#maquinas_item_resultados_template").html()),
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
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"inm_maquinas",
          "url":"maquinas/function/change_property/",
          "attribute":"activo",
          "value":activo,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
        return false;
      },
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"maquinas/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
            },
          });                    
        }
        return false;
      },
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    marcar: function(e) {
      var self = this;
      e.stopPropagation();
      e.preventDefault();

      var el = e.currentTarget;
      if ($(el).is(":checked")) {
        $(this.el).addClass("seleccionado");
        window.maquinas_marcadas.push(this.model.id);
      } else {
        $(this.el).removeClass("seleccionado");
        window.maquinas_marcadas = _.reject(window.maquinas_marcadas,function(m){
          return (m == self.model.id);
        });
      }
      $(".cantidad_seleccionados").html(window.maquinas_marcadas.length);

      // Si hay alguno marcado
      var marcado = false;
      $(".check-row").each(function(i,e){
        if ($(e).is(":checked")) marcado = true;
      });
      if (marcado) $(".bulk_action").slideDown();
      else $(".bulk_action").slideUp();
      return false;
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.codigo_maquina_seleccionado = this.model.get("codigo");
        window.maquina_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#maquina/"+this.model.id;
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
      var self = this;
      var obj = { seleccionar: this.habilitar_seleccion };
      $.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));

      // Controlamos si el articulos fue marcado o no
      if (typeof window.maquinas_marcadas != "undefined" && window.maquinas_marcadas.length > 0) {
        var res = _.find(window.maquinas_marcadas,function(m){
          return (m == self.model.id)
        });
        if (typeof res != "undefined") {
          $(this.el).addClass("seleccionado");
          $(this.el).find(".check-row").prop("checked",true);
        }
      }
      return this;
    },
  });
})(app);



(function ( app ) {

  app.views.MaquinaEditView = app.mixins.View.extend({

    template: _.template($("#maquina_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo_parte":function(){
        var self = this;
        var v = new app.views.MaquinaParteEditView({
          model: new app.models.MaquinaParte(),
          collection: self.partes,
        });
        crearLightboxHTML({
          "html":v.el,
          "width":600,
          "height":140,
          "callback":function() {
            console.log(self.partes);
          }
        });
      },
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      this.options = options;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
      this.$("#maquina_sectores").select2({});

      self.partes = new app.collections.MaquinasPartes();
      var dep = this.model.get("partes");
      for(var i=0;i<dep.length;i++) {
        var dd = dep[i];
        var ddo = new app.models.MaquinaParte(dd);
        self.partes.add(ddo);
      }
      this.partesTable = new app.views.MaquinasPartesTableView({
        collection: self.partes
      });
      this.$("#maquina_partes").html(this.partesTable.el);
    },

    validar: function() {
      try {
        var self = this;
        validate_input("maquina_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
          
        this.model.set({
          "codigo":self.$("#maquina_codigo").val(),
          "id_sector":self.$("#maquina_sectores").val(),
        });
        
        // Listado de partes
        var partes = new Array();
        self.partes.each(function(dpto){
          partes.push(dpto.toJSON());
        });
        self.model.set({"partes":partes});
        
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    guardar:function() {
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error == 1) {
              show(response.mensaje);
              return;
            } else {
              history.back();
            }
          }
        });
      }      
    },
          
  });
})(app);



(function ( models ) {

  models.MaquinaParte = Backbone.Model.extend({
    urlRoot: "partes",
    defaults: {
      nombre: "",
      codigo: "",
      observaciones: "",
      id_empresa: ID_EMPRESA,
      activo: 1,
      id_maquina: 0,
      orden: 0,
    },
  });
      
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model) {

  collections.MaquinasPartes = Backbone.Collection.extend({
    model: model,
  });

})( app.collections, app.models.MaquinaParte);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.MaquinasPartesTableView = app.mixins.View.extend({

    template: _.template($("#maquinas_partes_resultados_template").html()),
        
    myEvents: {
      "change #partes_buscar":"buscar",
      "click .buscar":"buscar",
    },
        
    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.id_maquina = (typeof this.options.id_maquina != "undefined") ? this.options.id_maquina : 0;
      this.render();
      this.collection.on('all', this.addAll, this);
      this.addAll();
    },

    render: function() {
      $(this.el).html(this.template());
      return this;
    },
        
    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);
    },
        
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.MaquinasPartesItemResultados({
        model: item,
        collection: self.collection,
      });
      this.$(".tbody").append(view.render().el);
    },
            
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.MaquinasPartesItemResultados = app.mixins.View.extend({
        
    template: _.template($("#maquinas_partes_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
          this.model.destroy();  // Eliminamos el modelo
          $(this.el).remove();  // Lo eliminamos de la vista
        }
        return false;
      },
    },
    seleccionar: function() {
      var self = this;
      var v = new app.views.MaquinaParteEditView({
        model:self.model,
        collection:self.collection,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":600,
        "height":140,
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.render();
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.MaquinaParteEditView = app.mixins.View.extend({

    template: _.template($("#maquina_parte_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
    },    
                
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      
      var edicion = false;
      var obj = { "id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
    },

    validar: function() {
      try {
        var self = this;
        validate_input("parte_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
        this.model.set({
          "nombre":self.$("#parte_nombre").val(),
          "activo":(self.$("#parte_activo").is(":checked")?1:0),
        });
        $(".error").removeClass("error");
        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },  

    guardar:function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          // NO PONEMOS ID = 0, PORQUE SINO NO AGREGA DOS ELEMENTOS CON EL MISMO ID
          var maxId = 0;
          this.collection.each(function(item){
            if (item.id > maxId) maxId = item.id;
          });
          maxId++;
          this.model.set({id:maxId});
        }
        this.collection.add(this.model);
        $('.modal:last').modal('hide');
      }      
    },
          
  });
})(app);