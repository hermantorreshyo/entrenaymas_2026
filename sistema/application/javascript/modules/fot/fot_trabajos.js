// -----------
//   MODELO
// -----------

(function ( models ) {

  models.FotTrabajo = Backbone.Model.extend({
    urlRoot: "fot_trabajos",
    defaults: {
      images: [],
      titulo: "",
      titulo_pt: "",
      titulo_en: "",
      path: "",
      fecha: "",
      id_usuario: ID_USUARIO,
      id_empresa: ID_EMPRESA,
      id_cliente: 0,
      link: "",
      activo: 1,
      destacado: 0,
      texto: "",
      texto_en: "",
      texto_pt: "",
      id_categoria: 0,
    },
  });
      
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.FotTrabajos = paginator.requestPager.extend({
      
    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'A.fecha',
      order: 'desc',
    },
    
    paginator_core: {
      url: "fot_trabajos/function/ver",
    }
      
  });

})( app.collections, app.models.FotTrabajo, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.FotTrabajosTableView = app.mixins.View.extend({

    template: _.template($("#fot_trabajos_resultados_template").html()),
            
    myEvents: {
      "change #fot_trabajos_buscar":"buscar",
      "keydown #fot_trabajos_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#fot_trabajos_texto").focus(); }
      },
    },
        
    initialize : function (options) {
            
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;
      window.fot_trabajos_filter = (typeof window.fot_trabajos_filter != "undefined") ? window.fot_trabajos_filter : "";
      window.fot_trabajos_page = (typeof window.fot_trabajos_page != "undefined") ? window.fot_trabajos_page : 1;
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

      if (window.fot_trabajos_filter != this.$("#fot_trabajos_buscar").val().trim()) {
        window.fot_trabajos_filter = this.$("#fot_trabajos_buscar").val().trim();
        cambio_parametros = true;
      }

      // Si se cambiaron los parametros, debemos volver a pagina 1
      if (cambio_parametros) window.fot_trabajos_page = 1;
      var datos = {
        "filter":encodeURIComponent(window.fot_trabajos_filter),
      };
      if (SOLO_USUARIO == 1) datos.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      this.collection.server_api = datos;
      this.collection.goTo(window.fot_trabajos_page);
    },
        
    addAll : function () {
      window.fot_trabajos_page = this.pagination.getPage();
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
      var view = new app.views.FotTrabajosItemResultados({
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
  app.views.FotTrabajosItemResultados = app.mixins.View.extend({
        
    template: _.template($("#fot_trabajos_item_resultados_template").html()),
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
          "table":"fot_trabajos",
          "url":"fot_trabajos/function/change_property/",
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
          "table":"fot_trabajos",
          "url":"fot_trabajos/function/change_property/",
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
            "url":"fot_trabajos/function/duplicar/"+self.model.id,
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
        window.fot_trabajo_seleccionado = this.model;
        $('.modal:last').modal('hide');                
      } else {
        location.href="app/#fot_trabajo/"+this.model.id;
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

  app.views.FotTrabajoEditView = app.mixins.View.extend({

    template: _.template($("#fot_trabajo_template").html()),
            
    myEvents: {
      "click .guardar": "guardar",
      "click .guardar_borrador": "guardar_borrador",
      "click .previsualizar": function(){
        this.previsualizar(false);
      },

      "click .agregar_categoria":function(e) {
        var self = this;
        if ($(".categoria_edit_mini").length > 0) return;
        var form = new app.views.CategoriaEntradaMiniEditView({
          "model": new app.models.CategoriaEntrada(),
          "callback":function(m){
            var that = self;
            self.model.set({ "id_categoria":m });
            $.ajax({
              "url":"categorias_entradas/function/get_arbol/",
              "dataType":"json",
              "success":function(r){
                categorias_noticias = r;
                that.cargar_categorias_entradas();
              },
            });
          },
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete categoria_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#categorias_entradas_mini_nombre").focus();
      },

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "fot_trabajos/function/upload_images/",
          "view": self,
        });
      },

      "click #fot_trabajo_link_2":function() {
        if (typeof CKEDITOR.instances["fot_trabajo_texto_en"] == "undefined") { 
          workspace.crear_editor('fot_trabajo_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #fot_trabajo_link_3":function() {
        if (typeof CKEDITOR.instances["fot_trabajo_texto_pt"] == "undefined") {
          workspace.crear_editor('fot_trabajo_texto_pt',{
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
        
      var fecha = this.model.get("fecha");
      if (isEmpty(fecha)) fecha = new Date();
      createdatepicker($(this.el).find("#fot_trabajo_fecha"),fecha);

      // Cuando cambian las imagens, renderizamos la tabla
      this.stopListening();
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();            
        
      $(this.el).find("#images_tabla").sortable();

      this.cargar_categorias_entradas();

      new app.mixins.Select({
        modelClass: app.models.Cliente,
        url: "clientes/",
        render: "#fot_trabajo_clientes",
        firstOptions: ["<option value='0'>-</option>"],
        selected: self.model.get("id_cliente"),
        onComplete:function(c) {
          crear_select2("fot_trabajo_clientes");
        }                    
      });  
      this.$("#fot_trabajo_categorias").trigger("change");
    },

    cargar_categorias_entradas: function() {
      var self = this;
      var r = workspace.crear_select(categorias_noticias,"",self.model.get("id_categoria"),function(item){
        if ((MILLING == 1) && (PERFIL == 344 || PERFIL == 349)) {
          return (item.title == "Blog");
        } else return true;
      });
      this.$("#fot_trabajo_categorias").html(r);
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
        
        var titulo = this.$("#fot_trabajo_titulo").val();
        if (isEmpty(titulo)) {
          if (silence) return false;
          else {
            alert("Por favor, ingrese un titulo.");
            this.$("#fot_trabajo_titulo").focus();
            return false;
          }
        }
        titulo = titulo.replace(/\'/g,"&#039;");
        titulo = titulo.replace(/\"/g,"&quot;");
        this.model.set({
          "titulo":titulo,
        });
          
        var id_categoria = self.$("#fot_trabajo_categorias").val();
        if (id_categoria == null) id_categoria = 0;
        this.model.set({
          "id_categoria":id_categoria,
          "path":self.$("#hidden_path").val(),
          "path_2":self.$("#hidden_path_2").val(),
          "id_cliente": self.$("#fot_trabajo_clientes").val(),
          "tipo": (self.$("#fot_trabajo_tipo").length > 0) ? self.$("#fot_trabajo_tipo").val() : "",
          "fecha": ((self.$("#fot_trabajo_fecha").length > 0) ? self.$("#fot_trabajo_fecha").val() : ""),
        });
        var fecha = self.model.get("fecha");
        self.model.set({"fecha":fecha});

        // Listado de Imagenes
        var images = new Array();
        $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});
        
        // Texto del fot_trabajo
        self.model.set({
          "texto":CKEDITOR.instances['fot_trabajo_texto'].getData(),
        });
        if (typeof CKEDITOR.instances['fot_trabajo_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['fot_trabajo_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['fot_trabajo_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['fot_trabajo_texto_pt'].getData(),
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
