// -----------
//   MODELO
// -----------

(function ( models ) {

  models.SeoUrl = Backbone.Model.extend({
    urlRoot: "seo_urls",
    defaults: {
      id_empresa: ID_EMPRESA,
      url: "",
      title: "",
      description: "",
      h1: "",
      h2: "",
      texto: "",
      parametros: [],
      texto_comercial: "",
    },
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.SeoUrls = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "seo_urls/function/buscar/"
    },
  });

})( app.collections, app.models.SeoUrl, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.SeoUrlsTableView = app.mixins.View.extend({

    template: _.template($("#seo_urls_resultados_template").html()),

    myEvents: {
      "change #seo_urls_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      window.seo_urls_filter = (typeof window.seo_urls_filter != "undefined") ? window.seo_urls_filter : "";
      window.seo_urls_buscar_activo = (typeof window.seo_urls_buscar_activo != "undefined") ? window.seo_urls_buscar_activo : (ID_EMPRESA == 135 ? 1 : -1);
      window.seo_urls_fecha = (typeof window.seo_urls_fecha != "undefined") ? window.seo_urls_fecha : "";
      window.seo_urls_page = (typeof window.seo_urls_page != "undefined") ? window.seo_urls_page : 1;
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
      this.$(".pagination_container").html(this.pagination.el);

      return this;
    },

    buscar: function() {
      var cambio_parametros = false;

      if (window.seo_urls_filter != this.$("#seo_urls_buscar").val().trim()) {
        window.seo_urls_filter = this.$("#seo_urls_buscar").val().trim();
        cambio_parametros = true;
      }
      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.seo_urls_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.seo_urls_filter),
      }
      this.collection.server_api = datos;
      this.collection.goTo(window.seo_urls_page);
    },

    addAll : function () {
      window.seo_urls_page = this.pagination.getPage();
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);            
    },

    addOne : function ( item ) {
      var view = new app.views.SeoUrlsItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },

  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.SeoUrlsItemResultados = app.mixins.View.extend({

    template: _.template($("#seo_urls_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"seo_urls/function/duplicar/"+self.model.id,
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
  seleccionar: function() {
    location.href="app/#seo_url/"+this.model.id;
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


(function ( app ) {

  app.views.SeoUrlEditView = app.mixins.View.extend({

    template: _.template($("#seo_url_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .atras": function() {
        window.history.back();
      },

      "click #parametro_agregar":"agregar_parametro",
      "click .editar_parametro":"editar_parametro",
      "click .eliminar_parametro":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #seo_url_parametro_valor":function(e) {
        if (e.which == 13) this.agregar_parametro();
      },
    }, 

    agregar_parametro: function() {
      // Controlamos los valores
      var orden = $("#seo_url_parametro_orden").val();
      if (isEmpty(orden)) {
        alert("Por favor ingrese un valor");
        $("#seo_url_parametro_orden").focus();
        return;
      }
      var campo = $("#seo_url_parametro_campo").val();
      if (isEmpty(campo)) {
        alert("Por favor ingrese un valor");
        $("#seo_url_parametro_campo").focus();
        return;
      }
      var valor = $("#seo_url_parametro_valor").val();
      if (isEmpty(valor)) {
        alert("Por favor ingrese un valor");
        $("#seo_url_parametro_valor").focus();
        return;
      }

      var tr = "<tr>";
      tr+="<td class='orden editar_parametro'>"+orden+"</td>";
      tr+="<td class='campo editar_parametro'>"+campo+"</td>";
      tr+="<td class='valor editar_parametro'>"+valor+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_parametro'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_parametro == null) {
        $("#seo_url_parametros_tabla tbody").append(tr);
      } else {
        $(this.item_parametro).replaceWith(tr);
        this.item_parametro = null;
      }
      $("#seo_url_parametro_orden").val("");
      $("#seo_url_parametro_campo").val("");
      $("#seo_url_parametro_valor").val("");
    },
    
    editar_parametro: function(e) {
      this.item_parametro = $(e.currentTarget).parents("tr");
      $("#seo_url_parametro_orden").val($(this.item_parametro).find(".orden").text());
      $("#seo_url_parametro_campo").val($(this.item_parametro).find(".campo").text());
      $("#seo_url_parametro_valor").val($(this.item_parametro).find(".valor").text());
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
    },

    validar: function() {
      try {
        var self = this;

        validate_input("seo_url_url",IS_EMPTY,"Por favor, ingrese un valor.");

        /*
        this.model.set({
          "nombre":((self.$("#seo_url_nombre").length>0) ? self.$("#seo_url_nombre").val() : ""),
          "nombre_en":((self.$("#seo_url_nombre_en").length>0) ? self.$("#seo_url_nombre_en").val() : ""),
          "nombre_pt":((self.$("#seo_url_nombre_pt").length>0) ? self.$("#seo_url_nombre_pt").val() : ""),
          "observaciones":((self.$("#seo_url_observaciones").length>0) ? self.$("#seo_url_observaciones").val() : ""),
          "observaciones_en":((self.$("#seo_url_observaciones_en").length>0) ? self.$("#seo_url_observaciones_en").val() : ""),
          "observaciones_pt":((self.$("#seo_url_observaciones_pt").length>0) ? self.$("#seo_url_observaciones_pt").val() : ""),
          "subtitulo":((self.$("#seo_url_subtitulo").length>0) ? self.$("#seo_url_subtitulo").val() : ""),
          "subtitulo_en":((self.$("#seo_url_subtitulo_en").length>0) ? self.$("#seo_url_subtitulo_en").val() : ""),
          "subtitulo_pt":((self.$("#seo_url_subtitulo_pt").length>0) ? self.$("#seo_url_subtitulo_pt").val() : ""),
          "fecha":self.$("#seo_url_fecha").val(),
          "fecha_llegada":self.$("#seo_url_fecha_llegada").val(),
          "id_promocion":((self.$("#seo_url_promociones").length>0) ? self.$("#seo_url_promociones").val() : 0),
          "id_categoria":((self.$("#seo_url_categorias").length>0) ? self.$("#seo_url_categorias").val() : 0),
          "categoria":((self.$("#seo_url_categorias").length>0) ? self.$("#seo_url_categorias option:selected").text() : ""),
        });
        */

        // Guardamos los parametros
        var parametros = new Array();
        if (this.$("#seo_url_parametros_tabla").length > 0) {
          var k = 0;
          $("#seo_url_parametros_tabla tbody tr").each(function(i,e){
            parametros.push({
              "orden": $(e).find(".orden").text(),
              "campo": $(e).find(".campo").text(),
              "valor": $(e).find(".valor").text(),
            });
            k++;
          });
          this.model.set({"parametros":parametros});      
          this.model.set({"texto": CKEDITOR.instances['seo_url_texto'].getData()});  
          this.model.set({"texto_comercial": $("#seo_url_texto_comercial").val()});  
        }

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