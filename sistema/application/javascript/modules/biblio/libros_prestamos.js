// -----------
//   MODELO
// -----------

(function ( models ) {

    models.LibroPrestamo = Backbone.Model.extend({
        urlRoot: "libros_prestamos",
        defaults: {
            ids_libros: [],
            id_libro: 0,
            libro: "",
            id_alumno: 0,
            alumno: "",
            alumno_email: "",
            fecha_desde: "",
            fecha_hasta: "",
            fecha_devuelto: "",
            devuelto: 0,
            observaciones: "",
            dias_atraso: 0,
            autor: "",
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.LibrosPrestamos = paginator.requestPager.extend({
        
        model: model,
        
        paginator_ui: {
            perPage: 30,
            order_by: 'fecha_desde',
            order: 'desc',
        },        
        
        paginator_core: {
            url: "libros_prestamos/function/ver",
        }
        
    });

})( app.collections, app.models.LibroPrestamo, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.LibrosPrestamosTableView = app.mixins.View.extend({

        template: _.template($("#libros_prestamos_resultados_template").html()),
            
        myEvents: {
            "change #libros_prestamos_buscar":"buscar_avanzada",
            "click .buscar":"buscar_avanzada",
            "click .eliminar_lote":"eliminar_lote",
            "click .devolver_lote":"devolver_lote",
            "click .nuevo":"nuevo",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
            this.id_libro = (typeof this.options.id_libro != "undefined") ? this.options.id_libro : 0;
            this.id_alumno = (typeof this.options.id_alumno != "undefined") ? this.options.id_alumno : 0;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            
            this.render();            

			this.collection.on('sync', this.addAll, this);
            
            this.collection.server_api = {
                "filter":this.filter,
                "id_libro":this.id_libro,
                "id_alumno":this.id_alumno,
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
                "id_libro":this.id_libro,
                "id_alumno":this.id_alumno,
                "filter":this.filter,
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);            
        },
        
        nuevo: function() {
            var self = this;
            var libro_prestamoView = new app.views.LibroPrestamoEditView({
                model: new app.models.LibroPrestamo(),
                collection: self.collection,
            });
            var d = $("<div/>").append(libro_prestamoView.el);
            crearLightboxHTML({
                "html":d,
                "width":600,
                "height":500,
            });
        },
        
        buscar: function() {
            var self = this;
            this.collection.server_api = {
                "filter":self.filter,
                "id_libro":self.id_libro,
                "id_alumno":self.id_alumno,                
            };
            this.collection.pager();
        },
        
        buscar_avanzada: function() {
            var self = this;
            self.filter = self.$("#libros_prestamos_buscar").val();
            this.buscar();
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);    
        },
        
        addOne : function ( item ) {
            var view = new app.views.LibrosPrestamosItemResultados({
                collection: this.collection,
                model: item,
                habilitar_seleccion: this.habilitar_seleccion, 
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
                
        eliminar_lote: function() {
            var checks = this.$("#libros_prestamos_tabla .check-row:checked");
            if (checks.length == 0) return;
            if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
                $(checks).each(function(i,e){
                    var id = $(e).val();
                    var art = libros_prestamos.get(id);
                    art.destroy();	// Eliminamos el modelo
                    $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
                });
            }            
        },
        devolver_lote: function() {
            var self = this;
            var checks = this.$("#libros_prestamos_tabla .check-row:checked");
            if (checks.length == 0) return;
            var ids_libros = new Array();
            $(checks).each(function(i,e){
                var id = $(e).val();
                var libro = $(e).data("libro");
                ids_libros.push({
                    "id":id,
                    "libro":libro,
                });
            });
            if (ids_libros.length > 0) {
                var libro_prestamo_devolucion_masiva_View = new app.views.LibroPrestamoDevolucionMasivaView({
                    collection: self.collection,
                    ids_libros: ids_libros,
                });
                var d = $("<div/>").append(libro_prestamo_devolucion_masiva_View.el);
                crearLightboxHTML({
                    "html":d,
                    "width":600,
                    "height":500,
                });
            }
        },
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.LibrosPrestamosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#libros_prestamos_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            "click .enviar_email":"enviar_email",
            "click .data":"seleccionar",
            "click .devolucion":"devolucion",
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
                    this.model.destroy();	// Eliminamos el modelo
                    $(this.el).remove();	// Lo eliminamos de la vista
                }
                return false;
            },
        },
        seleccionar: function() {
            var self = this;
            // Se editan solamente los libros que no fueron devueltos aun
            if (self.model.get("devuelto") == 0) {
                var libro_prestamoView = new app.views.LibroPrestamoEditView({
                    collection: self.collection,
                    model: self.model
                });
                var d = $("<div/>").append(libro_prestamoView.el);
                crearLightboxHTML({
                    "html":d,
                    "width":600,
                    "height":500,
                });
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
        devolucion: function() {
            var self = this;
            var libro_prestamo_devolucion_View = new app.views.LibroPrestamoDevolucionEditView({
                collection: this.collection,
                model: this.model,
            });
            var d = $("<div/>").append(libro_prestamo_devolucion_View.el);
            crearLightboxHTML({
                "html":d,
                "width":600,
                "height":500,
            });
        },
        enviar_email: function() {
            var self = this;
            var email = new app.models.Consulta({
                asunto: "Biblioteca",
                email: self.model.get("alumno_email"),
            });
            workspace.nuevo_email(email);
        },
    });
})(app);



(function ( app ) {

    app.views.LibroPrestamoEditView = app.mixins.View.extend({

        template: _.template($("#libro_prestamo_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .label_libro_remove":function(e) {
                $(e.currentTarget).parent().remove();
                if (this.$(".label_libro").length == 0) {
                    $(".modal").last().trigger("click");
                }
            }
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

            var input_libros = this.$("#libro_prestamo_libros");
            $(input_libros).customcomplete({
                "url":"libros/function/get_by_nombre/",
                "info":"",
                "onSelect":function(item){
                    self.seleccionar_libro(item);
                    $(input_libros).val("");
                }
            });

            var input_alumnos = this.$("#libro_prestamo_alumnos");
            $(input_alumnos).customcomplete({
                "url":"alumnos/function/get_by_nombre/",
                "info":"",
                "onSelect":function(item){
                    self.seleccionar_alumno(item);
                    $(input_alumnos).val(item.label);
                }
            });

            if (this.model.get("id_libro") != 0) {
                this.seleccionar_libro({
                    "id":self.model.get("id_libro"),
                    "label":self.model.get("libro"),
                });
            }

            var fecha_desde = this.model.get("fecha_desde");
            if (isEmpty(fecha_desde)) fecha_desde = new Date();
            createdatepicker($(this.el).find("#libro_prestamo_fecha_desde"),fecha_desde);

            var fecha_hasta = this.model.get("fecha_hasta");
            if (isEmpty(fecha_hasta)) fecha_hasta = new Date();
            createdatepicker($(this.el).find("#libro_prestamo_fecha_hasta"),fecha_hasta);            
        },

        seleccionar_libro: function(item) {
            if (item.disponibles <= 0) {
                alert("No hay copias disponibles de este libro.");
                return;
            }
            var s = '<span data-id='+item.id+' class="label_libro label fs14 bg-light dk mr5">'+item.label+' <i class="label_libro_remove cp fa fa-times"></i></span>';
            this.$("#libro_prestamo_libros_cont").append(s);
        },

        seleccionar_alumno: function(item) {
            this.model.set({
                "id_alumno":item.id,
            })
        },
        
        validar: function() {
            try {
                var self = this;
                
                if (this.model.get("id_alumno") == 0) {
                    alert("Por favor busque primero un alumno.");
                    self.$("#libro_prestamo_alumnos").focus();
                    return false;
                }

                if (typeof this.model.id === "undefined" || this.model.id == 0) { 
                    var ids_libros = new Array();
                    self.$("#libro_prestamo_libros_cont .label_libro").each(function(i,e){
                        ids_libros.push($(e).data("id"));
                    })
                    if (ids_libros.length == 0) {
                        alert("Por favor seleccione al menos un libro para realizar el prestamo.");
                        self.$("#libro_prestamo_libros").focus();
                        return false;
                    }
                }
                
                var fecha_desde = self.$("#libro_prestamo_fecha_desde").val();
                if (isEmpty(fecha_desde)) {
                    alert("Por favor elija una fecha");
                    self.$("#libro_prestamo_fecha_desde").focus();
                    return false;
                }
                var fecha_hasta = self.$("#libro_prestamo_fecha_hasta").val();
                if (isEmpty(fecha_desde)) {
                    alert("Por favor elija una fecha");
                    self.$("#libro_prestamo_fecha_hasta").focus();
                    return false;
                }

                this.model.set({
                    "ids_libros":ids_libros,
                    "fecha_desde":fecha_desde,
                    "fecha_hasta":fecha_hasta,
                });
                
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },	
	
        guardar:function() {
            var self = this;
            if (this.validar()) {
                if (this.model.id == null) {
                    this.model.set({id:0});
                } else {
                    // Estamos editando, la disponibilidad NO se mueve
                    this.model.set({
                        "modificar_disponibilidad":0,
                    });
                }
                this.model.save({},{
                    success: function(model,response) {
                        if (response.error == 1) {
                            show(response.mensaje);
                            return;
                        } else {
                            self.collection.fetch();
                            $(".modal").last().trigger("click");
                        }
                    }
                });
            }	    
        },        
	
    });
})(app);





(function ( app ) {

    app.views.LibroPrestamoDevolucionEditView = app.mixins.View.extend({

        template: _.template($("#libro_prestamo_devolucion_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .cancelar_devolucion":"cancelar_devolucion",
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

            var fecha_devolucion = this.model.get("fecha_devolucion");
            if (isEmpty(fecha_devolucion)) fecha_devolucion = new Date();
            createdatepicker($(this.el).find("#libro_prestamo_devolucion_fecha_devolucion"),fecha_devolucion);
        },

        validar: function() {
            try {
                var self = this;
                
                var fecha_devolucion = self.$("#libro_prestamo_devolucion_fecha_devolucion").val();
                if (isEmpty(fecha_devolucion)) {
                    alert("Por favor elija una fecha");
                    self.$("#libro_prestamo_devolucion_fecha_devolucion").focus();
                    return false;
                }
                this.model.set({
                    "modificar_disponibilidad":1,
                    "fecha_devuelto":fecha_devolucion,
                    "devuelto":1,
                    "observaciones":self.$("#libro_prestamo_devolucion_observaciones").val(),
                });
                
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },	
	
        guardar:function() {
            if (this.validar()) this.do_guardar();
        },

        do_guardar: function() {
            var self = this;
            if (this.model.id == null) {
                this.model.set({id:0});
            }
            this.model.save({},{
                success: function(model,response) {
                    if (response.error == 1) {
                        show(response.mensaje);
                        return;
                    } else {
                        self.collection.fetch();
                        $(".modal").last().trigger("click");
                    }
                }
            });            
        },
        
        cancelar_devolucion: function() {
            this.model.set({
                "devuelto":0,
                "modificar_disponibilidad":1,
            });
            this.do_guardar();
        }
	
    });
})(app);


(function ( app ) {

    app.views.LibroPrestamoDevolucionMasivaView = app.mixins.View.extend({

        template: _.template($("#libro_prestamo_devolucion_masiva_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .label_libro_remove":function(e) {
                $(e.currentTarget).parent().remove();
                if (this.$(".label_libro").length == 0) {
                    $(".modal").last().trigger("click");
                }                
            },            
        },
        
        initialize: function(options) {
            var self = this;
            this.options = options;
            _.bindAll(this);            
            $(this.el).html(this.template());

            if (typeof this.options.ids_libros !== "undefined") {
                var ids_libros = new Array();
                for(var i=0;i<this.options.ids_libros.length;i++) {
                    var o = this.options.ids_libros[i];
                    this.seleccionar_libro({
                        "id":o.id,
                        "label":o.libro,
                    });
                    ids_libros.push(o.id);
                }
            }
            
            createdatepicker($(this.el).find("#libro_prestamo_devolucion_fecha_devolucion"),new Date());
        },

        seleccionar_libro: function(item) {
            var s = '<span data-id='+item.id+' class="label_libro label fs14 bg-light dk mr5">'+item.label+' <i class="label_libro_remove cp fa fa-times"></i></span>';
            this.$("#libro_prestamo_libros_cont").append(s);
        },        
        
        guardar: function() {
            try {
                var self = this;
                
                var fecha_devolucion = self.$("#libro_prestamo_devolucion_fecha_devolucion").val();
                if (isEmpty(fecha_devolucion)) {
                    alert("Por favor elija una fecha");
                    self.$("#libro_prestamo_devolucion_fecha_devolucion").focus();
                    return false;
                }

                var ids_libros = new Array();
                self.$("#libro_prestamo_libros_cont .label_libro").each(function(i,e){
                    ids_libros.push($(e).data("id"));
                })
                if (ids_libros.length == 0) {
                    alert("Por favor seleccione al menos un libro para realizar el prestamo.");
                    self.$("#libro_prestamo_libros").focus();
                    return false;
                }      

                $.ajax({
                    "url":"libros_prestamos/function/devolver/",
                    "dataType":"json",
                    "type":"post",
                    "data":{
                        "ids":ids_libros,
                        "fecha_devuelto":fecha_devolucion,
                        "devuelto":1,
                        "observaciones":self.$("#libro_prestamo_devolucion_observaciones").val(),
                    },
                    "success":function(response){
                        if (response.error == 1) {
                            show(response.mensaje);
                            return;
                        } else {
                            self.collection.fetch();
                            $(".modal").last().trigger("click");
                        }
                    }
                });
                
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },  
    });
})(app);
