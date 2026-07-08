// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Libro = Backbone.Model.extend({
        urlRoot: "libros",
        defaults: {
            isbn:0,
            id_empresa: ID_EMPRESA,
            nombre: "",
            id_autor: 0,
            stock: 0,
            disponibles: 0,
            sinopsis:"",
            path:"",
            anio: "",
            editorial: "",
            numero_edicion: "",
            archivo: "",
            activo: 1,
            
            autor: "",
            etiquetas: [],
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Libros = paginator.requestPager.extend({
        
        model: model,
        
        paginator_ui: {
            perPage: 30,
            order_by: 'nombre',
            order: 'asc',
        },        
        
        paginator_core: {
            url: "libros/function/ver",
        }
        
    });

})( app.collections, app.models.Libro, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.LibrosTableView = app.mixins.View.extend({

        template: _.template($("#libros_resultados_template").html()),
            
        myEvents: {
            "change #libros_buscar":"buscar",
            "click #libros_buscar_avanzada_btn":"buscar_avanzada",
            "click .eliminar_lote":"eliminar_lote",
            "click .destacar_lote":"destacar_lote",
            "click .activar_lote":"activar_lote",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
            this.id_autor = (typeof this.options.id_autor != "undefined") ? this.options.id_autor : 0;
            this.id_etiqueta = (typeof this.options.id_etiqueta != "undefined") ? this.options.id_etiqueta : 0;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            
            this.render();
			this.collection.on('sync', this.addAll, this);
            
            this.collection.server_api = {
                "id_autor":this.id_autor,
                "id_etiqueta":this.id_etiqueta,
            };            
            this.collection.goTo(this.pagina);
		},
        
        render: function() {
            
            var self = this;
            
			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
				collection: this.collection
			});
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion,
                "id_autor":this.id_autor,
                "id_etiqueta":this.id_etiqueta,
                "filter":this.filter,
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);            
        },
        
        buscar: function() {
            var self = this;
            
            var filter = this.$("#libros_buscar").val();
            if (typeof filter != "undefined") filter = filter.trim();
            else filter = "";
            self.filter = filter;
            
            this.collection.server_api = {
                "filter":self.filter,
                "id_autor":self.id_autor,
                "id_etiqueta":self.id_etiqueta,
            };
            this.collection.pager();
        },
        
        buscar_avanzada: function() {
            var self = this;
            self.id_autor = self.$("#libros_buscar_autores").val();
            self.id_etiqueta = self.$("#libros_buscar_etiquetas").val();
            this.buscar();
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);
        },
        
        addOne : function ( item ) {
            var view = new app.views.LibrosItemResultados({
                model: item,
                collection: this.collection,
                habilitar_seleccion: this.habilitar_seleccion, 
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
                
        eliminar_lote: function() {
            var checks = this.$("#libros_tabla .check-row:checked");
            if (checks.length == 0) return;
            if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
                $(checks).each(function(i,e){
                    var id = $(e).val();
                    var art = libros.get(id);
                    art.destroy();	// Eliminamos el modelo
                    $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
                });
            }            
        },
        activar_lote: function() {
            
        },
        destacar_lote: function() {
            
        },

        open_advanced_search: function() {
            // Cargamos los autores cuando se abre el cuadro de busqueda avanzada (solo la primera vez)
            if (typeof this.advanced_search_opened === "undefined") {
                var self = this;
                new app.mixins.Select({
                    modelClass: app.models.Autor,
                    url: "autores/",
                    render: "#libros_buscar_autores",
                    firstOptions: ["<option value='0'>Autor</option>"],
                    selected: self.id_autor,
                    onComplete:function(c) {
                      crear_select2("libros_buscar_autores");
                    }                    
                });            
                new app.mixins.Select({
                    modelClass: app.models.LibroEtiqueta,
                    url: "libros_etiquetas/",
                    render: "#libros_buscar_etiquetas",
                    firstOptions: ["<option value='0'>Etiqueta</option>"],
                    selected: self.id_etiqueta,
                    onComplete:function(c) {
                      crear_select2("libros_buscar_etiquetas");
                    }                    
                });            
            }
        },
	
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.LibrosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#libros_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            "click .data":"seleccionar",
            "click .prestar":"prestar",
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
            "click .nuevo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var nuevo = this.model.get("nuevo");
                nuevo = (nuevo == 1)?0:1;
                self.model.set({"nuevo":nuevo});
                this.change_property({
                  "table":"biblio_libros",
                  "url":"libros/function/change_property/",
                  "attribute":"nuevo",
                  "value":nuevo,
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
                  "table":"biblio_libros",
                  "url":"libros/function/change_property/",
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
                        "url":"libros/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            libros.add(d);
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
        prestar: function(e) {
            var self = this;
            e.stopPropagation();
            e.preventDefault();
            var libro_prestamoView = new app.views.LibroPrestamoEditView({
                model: new app.models.LibroPrestamo(),
                collection: self.collection,
            });
            libro_prestamoView.seleccionar_libro({
                "id":self.model.id,
                "label":self.model.get("nombre"),
            });
            var d = $("<div/>").append(libro_prestamoView.el);
            crearLightboxHTML({
                "html":d,
                "width":600,
                "height":500,
            });
        },
        seleccionar: function() {
            if (this.habilitar_seleccion) {
                window.codigo_libro_seleccionado = this.model.get("codigo");
                window.libro_seleccionado = this.model;
                $('.modal:last').modal('hide');                
            } else {
                location.href="app/#libro/"+this.model.id;
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

    app.views.LibroEditView = app.mixins.View.extend({

        template: _.template($("#libro_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click #historial_tab":"cargar_historial",
            "click .nuevo_autor":function(e){
                var self = this;
                if ($(".autor_edit_mini").length > 0) return;
                var form = new app.views.AutorEditViewMini({
                    "model": new app.models.Autor(),
                    "callback":self.cargar_autores,
                });
                var width = 350;
                var position = $(e.currentTarget).offset();
                var top = position.top + $(e.currentTarget).outerHeight();
                var container = $("<div class='customcomplete autor_edit_mini'/>");
                $(container).css({
                    "top":top+"px",
                    "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
                    "display":"block",
                    "width":width+"px",
                });
                $(container).append("<div class='new-container'></div>");
                $(container).find(".new-container").append(form.el);
                $("body").append(container);
                $("#autores_mini_nombre").focus();
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
            
            if (self.model.get("etiquetas").length>0) {
                $(this.el).find("#libro_etiquetas").val(self.model.get("etiquetas").join(","));                
            }
            // Cargamos las etiquetas con AJAX
            $.ajax({
                "url":"libros_etiquetas/",
                "dataType":"json",
                "success":function(r) {
                  var etiquetas = self.model.get("etiquetas");
                  for(var i=0;i<r.results.length;i++) {
                    var a = r.results[i];
                    var encontro = false;
                    for(var j=0;j<etiquetas.length;j++) {
                      var et = etiquetas[j];
                      if (et == a.nombre) encontro = true;
                    }
                    if (!encontro) $("#libro_etiquetas").append("<option>"+a.nombre+"</option>");
                  }
                  $("#libro_etiquetas").trigger("change");
                }
            });
            
            this.cargar_autores();
        },

        cargar_historial: function() {
            var self = this;
            console.log(self.model.id);
            var prestamos = new app.views.LibrosPrestamosTableView({
                collection: new app.collections.LibrosPrestamos(),
                habilitar_seleccion: true,
                permiso: 3,
                id_libro: self.model.id,
            });
            this.$("#libros_historial").html(prestamos.el);
        },
        
        cargar_autores: function(id_autor) {
            var self = this;
            // Si se manda por parametro un ID, hay que poner ese nuevo en el modelo
            if (id_autor != undefined) {
                this.model.set({ "id_autor": id_autor });
            }            
            // Creamos el select
            new app.mixins.Select({
                modelClass: app.models.Autor,
                url: "autores/",
                firstOptions: ["<option value='0'>Sin Definir</option>"],
                render: "#libro_autores",
                selected: self.model.get("id_autor"),
                onComplete:function(c) {
                  crear_select2("libro_autores");
                }
            });
            
        },        

        validar: function() {
            try {
                var self = this;
                
                validate_input("libro_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                // Las etiquetas se tratan como array porque son entidades separadas
                if (self.$("#libro_etiquetas").length > 0) {
                  var c = self.$("#libro_etiquetas").select2("val");
                  this.model.set({ "etiquetas":((c==null)?[]:c) });
                }
                var id_autor = self.$("#libro_autores").val();
                if (id_autor == null) id_autor = 0;
                this.model.set({
                    "id_autor":id_autor,
                    "autor":self.$("#libro_autores option:selected").text(),
                    "path":self.$("#hidden_path").val(),
                    "archivo":$(self.el).find("#hidden_archivo").val(),
                });
                
                // Texto del libro
                var cktext = CKEDITOR.instances['libro_sinopsis'].getData();
                self.model.set({"sinopsis":cktext});
                
                $(".error").removeClass("error");
                return true;
            } catch(e) {
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
