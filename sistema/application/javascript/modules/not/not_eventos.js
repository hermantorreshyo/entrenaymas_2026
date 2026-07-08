// -----------
//   MODELO
// -----------

(function ( models ) {

  models.NotEvento = Backbone.Model.extend({
    urlRoot: "not_eventos",
    defaults: {
      images: [],
      titulo: "",
      titulo_pt: "",
      titulo_en: "",
      path: "",
      fecha_desde: "",
      fecha_hasta: "",
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
      id_organizador: 0,
      link: "",
      activo: 1,
      con_acuerdo: 0,
      destacado: 0,
      texto: "",
      texto_en: "",
      texto_pt: "",
      web: "",
      lugar: "",
      seo_title: "",
      seo_keywords: "",
      seo_description: "",
      path_2: "",
      contacto_nombre: "",
      contacto_telefono: "",
      contacto_email: "",
      tipo: "",
      categoria: 0,
    },
  });
      
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.NotEventos = paginator.requestPager.extend({
      
    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'A.fecha_desde',
      order: 'desc',
    },
    
    paginator_core: {
      url: "not_eventos/function/ver",
    }
      
  });

})( app.collections, app.models.NotEvento, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.NotEventosTableView = app.mixins.View.extend({

    template: _.template($("#not_eventos_resultados_template").html()),
            
    myEvents: {
      "change #not_eventos_buscar":"buscar",
      "keydown #not_eventos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#not_eventos_texto").focus(); }
      },
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.not_eventos_filter = (typeof window.not_eventos_filter != "undefined") ? window.not_eventos_filter : "";
      window.not_eventos_page = (typeof window.not_eventos_page != "undefined") ? window.not_eventos_page : 1;
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
    },
    
    buscar: function() {
      var self = this;
      var cambio_parametros = false;

      if (window.not_eventos_filter != this.$("#not_eventos_buscar").val().trim()) {
        window.not_eventos_filter = this.$("#not_eventos_buscar").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.not_eventos_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.not_eventos_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.not_eventos_page);
    },
        
    addAll : function () {
      window.not_eventos_page = this.pagination.getPage();
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
      var view = new app.views.NotEventosItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
                
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.NotEventosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#not_eventos_item_resultados_template").html()),
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
      "click .destacado":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var destacado = this.model.get("destacado");
        destacado = (destacado == 1)?0:1;
        self.model.set({"destacado":destacado});
        this.change_property({
          "table":"not_eventos",
          "url":"not_eventos/function/change_property/",
          "attribute":"destacado",
          "value":destacado,
          "id":self.model.id,
          "success":function(){
            self.render();
          }
        });
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
          "table":"not_eventos",
          "url":"not_eventos/function/change_property/",
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
            "url":"not_eventos/function/duplicar/"+self.model.id,
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
      if (this.habilitar_seleccion) {
        window.not_evento_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#not_evento/"+this.model.id;
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


// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.NotEventoEditView = app.mixins.View.extend({

    template: _.template($("#not_evento_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .guardar_borrador": "guardar_borrador",
      "click .previsualizar": function(){
        this.previsualizar(false);
      },

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "not_eventos/function/upload_images/",
          "view": self,
        });
      },

      "click #not_evento_link_2":function() {
        if (typeof CKEDITOR.instances["not_evento_texto_en"] == "undefined") { 
          workspace.crear_editor('not_evento_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #not_evento_link_3":function() {
        if (typeof CKEDITOR.instances["not_evento_texto_pt"] == "undefined") {
          workspace.crear_editor('not_evento_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
    },
        
    initialize: function(options) {
      var self = this;
      this.options = options;
      _.bindAll(this);
        
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { "edicion": edicion,"id":this.model.id }
      _.extend(obj,this.model.toJSON());
      $(this.el).html(this.template(obj));
        
      var fecha_desde = this.model.get("fecha_desde");
      if (isEmpty(fecha_desde)) fecha_desde = new Date();
      createdatepicker($(this.el).find("#not_evento_fecha_desde"),fecha_desde);

      var fecha_hasta = this.model.get("fecha_hasta");
      if (isEmpty(fecha_hasta)) fecha_hasta = new Date();
      createdatepicker($(this.el).find("#not_evento_fecha_hasta"),fecha_hasta);

      // Cuando cambian las imagens, renderizamos la tabla
      this.stopListening();
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();            
        
      $(this.el).find("#images_tabla").sortable();

      new app.mixins.Select({
        modelClass: app.models.OrganizadorEvento,
        url: "organizadores_eventos/",
        render: "#not_evento_organizadores",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_organizador"),
        onComplete:function(c) {
          crear_select2("not_evento_organizadores");
        }                    
      });  
    },

    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length == 0) {
        this.$("#images_container").removeClass('tiene');
      } else {
        this.$("#images_container").addClass('tiene');
        for(var i=0;i<images.length;i++) {
          var path = images[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
        }
      }
    }, 

    validar: function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      try {
        var self = this;
        
        var titulo = this.$("#not_evento_titulo").val();
        if (isEmpty(titulo)) {
          if (silence) return false;
          else {
            alert("Por favor, ingrese un titulo.");
            this.$("#not_evento_titulo").focus();
            return false;
          }
        }
        titulo = titulo.replace(/\'/g,"&#039;");
        titulo = titulo.replace(/\"/g,"&quot;");
        this.model.set({
          "titulo":titulo,
        });
          
        this.model.set({
          "path":self.$("#hidden_path").val(),
          "path_2":self.$("#hidden_path_2").val(),
          "id_organizador": self.$("#not_evento_organizadores").val(),
          "tipo": (self.$("#not_evento_tipo").length > 0) ? self.$("#not_evento_tipo").val() : "",
          "seo_title": (self.$("#not_evento_seo_title").length > 0) ? self.$("#not_evento_seo_title").val() : "",
          "seo_description": (self.$("#not_evento_seo_description").length > 0) ? self.$("#not_evento_seo_description").val() : "",
          "seo_keywords": (self.$("#not_evento_seo_keywords").length > 0) ? self.$("#not_evento_seo_keywords").val() : "",
          "fecha_desde": ((self.$("#not_evento_fecha_desde").length > 0) ? self.$("#not_evento_fecha_desde").val() : ""),
          "fecha_hasta": ((self.$("#not_evento_fecha_hasta").length > 0) ? self.$("#not_evento_fecha_hasta").val() : ""),
        });
        var fecha_desde = self.model.get("fecha_desde")+":00";
        self.model.set({"fecha_desde":fecha_desde});
        var fecha_hasta = self.model.get("fecha_hasta")+":00";
        self.model.set({"fecha_hasta":fecha_hasta});

        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
        
        // Texto del not_evento
        self.model.set({
          "texto":CKEDITOR.instances['not_evento_texto'].getData(),
        });
        if (typeof CKEDITOR.instances['not_evento_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['not_evento_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['not_evento_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['not_evento_texto_pt'].getData(),
          });
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
        this.model.set({
          "activo":1,
        });
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

    guardar_borrador:function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      if (this.validar()) {
        var activo = 0;
        if (this.model.id == null) {
          this.model.set({ id:0 });
        } else {
          activo = this.model.get("activo");
        }
        this.model.save({
          "activo":activo,
        },{
          success: function(model,response) {
            if (!silence) {
              if (response.error == 1) {
                show(response.mensaje);
                return;
              } else {
                history.back();
              }
            }
          }
        });
      }      
    },      

    previsualizar:function(silence) {
      silence = (typeof silence == "undefined") ? false : silence;
      var self = this;
      if (this.validar(silence)) {
        var activo = 0;
        if (this.model.id == null) {
          this.model.set({
            "id":0,
            "activo":0,
          });
        } else {
          activo = this.model.get("activo");
        }
        this.model.save({
          "activo":activo,
        },{
          success: function(model,response) {
            if (!silence) {
              if (response.error == 1) {
                show(response.mensaje);
                return;
              } else {
                var link = "http://"+String(DOMINIO+''+self.model.get("link")+'?preview=1').replace('//','/');
                window.open(link,"_blank");
              }
            }
          }
        });
      }      
    },
  
  });
})(app);
