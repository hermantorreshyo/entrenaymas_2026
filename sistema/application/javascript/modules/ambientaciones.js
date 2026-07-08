// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Ambientacion = Backbone.Model.extend({
        urlRoot: "ambientaciones",
        defaults: {
            images: [],
            articulos: [], // Productos articulos
            nombre: "",
            categoria: "",
            activo: 1,
            texto: "",
            seo_title: "",
            seo_keywords: "",
            seo_description: "",
            path: "",
            link: "",
            caracteristicas: "",
            orden: 0,
            destacado: 0,
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Ambientaciones = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 50,
            order_by: 'nombre',
            order: 'asc',
        },
    
        paginator_core: {
            url: "ambientaciones/function/ver/",
        },
	    
    });

})( app.collections, app.models.Ambientacion, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.AmbientacionesTableView = app.mixins.View.extend({

        template: _.template($("#ambientaciones_resultados_template").html()),
            
        myEvents: {
            "change #ambientaciones_buscar":"buscar",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
            this.render();
            this.collection.on('sync', this.addAll, this);
            this.buscar();
		},

        render: function() {
            // Creamos la lista de paginacion
            var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
                collection: this.collection
            });

            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion
            }));
            
            // Cargamos el paginador
            $(this.el).find(".pagination_container").html(pagination.el);
            return this;
        },

        buscar: function() {
            var self = this;
            var datos = {}
            datos.texto = encodeURIComponent(self.$("#ambientaciones_buscar").val().trim());
            this.collection.server_api = datos;
            this.collection.pager();
        },
        
        addAll : function () {
            this.$("#ambientaciones_tabla tbody").empty();
            console.log(this.collection);
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
            var self = this;
            var view = new app.views.AmbientacionesItemResultados({
                model: item,
                collection: self.collection,
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
    app.views.AmbientacionesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#ambientaciones_item_resultados_template").html()),
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
                e.stopPropagation();
                e.preventDefault();
                var self = this;
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"ambientaciones",
                  "attribute":"activo",
                  "value":activo,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
                return false;
            },
            "click .destacado":function(e) {
                e.stopPropagation();
                e.preventDefault();
                var self = this;
                var destacado = this.model.get("destacado");
                destacado = (destacado == 1)?0:1;
                self.model.set({"destacado":destacado});
                this.change_property({
                  "table":"ambientaciones",
                  "attribute":"destacado",
                  "value":destacado,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
                return false;
            },
            "change .ordenador":function(e) {
                e.stopPropagation();
                e.preventDefault();
                var self = this;
                var orden = $(e.currentTarget).val();
                self.model.set({"orden":orden});
                this.change_property({
                  "table":"ambientaciones",
                  "attribute":"orden",
                  "value":orden,
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
                        "url":"ambientaciones/function/duplicar/"+self.model.id,
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
                    this.model.destroy();	// Eliminamos el modelo
                    $(this.el).remove();	// Lo eliminamos de la vista
                }
                return false;
            },
        },
        seleccionar: function() {
            if (this.habilitar_seleccion) {
                window.codigo_ambientacion_seleccionado = this.model.get("codigo");
                window.ambientacion_seleccionado = this.model;
                $('.modal:last').modal('hide');                
            } else {
                location.href="app/#ambientacion/"+this.model.id;
            }
        },
        initialize: function(options) {
            var self = this;
            _.bindAll(this);
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
            this.collection = this.options.collection;
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

    app.views.AmbientacionEditView = app.mixins.View.extend({

        template: _.template($("#ambientacion_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click #ambientacion_eliminado":function(e) {
                $("#ambientacion_fecha_eliminado").attr("disabled",(!$(e.currentTarget).is(":checked")));
            },
            "click .eliminar_relacionado":function(e) {
                if (confirm("Realmente desea eliminar la relacion?")) {
                    $(e.currentTarget).parents("li").remove();
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
        
            // AUTOCOMPLETE DE ARTICULOS
            // -------------------------
            var input = $(this.el).find("#ambientaciones_buscar_productos");
            $(input).customcomplete({
                "url":"/sistema/articulos/function/get_by_descripcion/",
                "form":null, // No quiero que se creen nuevos productos
                "info":"descripcion",
                "image_field":"path",
                "image_path":"/sistema",
                "onSelect":function(item){
                    var tr = "";
                    tr+="<li class='list-group-item'>";
                    tr+="<span class='id dn'>"+item.value+"</span>";
                    tr+="<span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
                    tr+="<img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/"+item.path+"'/>";
                    tr+="<span class='filename'>"+item.label+"</span>";
                    tr+="<span class='pull-right m-t eliminar_relacionado'><i class='fa fa-fw fa-times'></i> </span>";
                    tr+="</li>";
                    $("#ambientaciones_tabla_articulos").append(tr);
                    self.$("#ambientaciones_buscar_productos").val("");
                }
            });           
            
            // Cuando cambian las imagens, renderizamos la tabla
            this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
            this.render_tabla_fotos();            

            $(this.el).find("#ambientacion_caracteristicas").select2({
              tags: true
            });
            
            $(this.el).find("#images_tabla").sortable();
            $(this.el).find("#ambientaciones_tabla_articulos").sortable();
        },

        render_tabla_fotos: function() {
            var images = this.model.get("images");
            this.$("#images_tabla").empty();
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
        },
        
        
        validar: function() {
            try {
                var self = this;

                this.model.set({
                    "path": ((self.$("#hidden_path").length > 0) ? self.$("#hidden_path").val() : ""),
                    "categoria": self.$("#ambientacion_categoria").val(),
                });
                // Listado de Imagenes
                var images = new Array();
                $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
                    images.push($(e).text());
                });
                self.model.set({"images":images});

                // Las caracteristicas van todas juntas separadas por ;;;
                if (self.$("#ambientacion_caracteristicas").length > 0) {
                  var c = self.$("#ambientacion_caracteristicas").select2("val");
                  if (c != null) {
                    this.model.set({
                      "caracteristicas":c.join(";;;"),
                    });
                  }
                }
                    
                var articulos = new Array();
                $(this.el).find("#ambientaciones_tabla_articulos .list-group-item").each(function(i,e){
                    articulos.push({
                        "id":$(e).find(".id").text(),
                    });
                });
                self.model.set({"articulos":articulos});
                    
                // Texto del ambientacion
                if (self.$("#ambientacion_texto").length > 0) {
                    var cktext = CKEDITOR.instances['ambientacion_texto'].getData();
                    self.model.set({"texto":cktext});
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
                            if (typeof ambientaciones != "undefined") ambientaciones.fetch();
                            location.href="app/#ambientaciones";
                        }
                    }
                });
            }	    
        },        
	
    });
})(app);