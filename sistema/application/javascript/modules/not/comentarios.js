// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Comentario = Backbone.Model.extend({
        urlRoot: "comentarios",
        defaults: {
            id_usuario: 0,
            usuario: "",
            id_entrada: 0,
            entrada: "",
            fecha: "",
            texto: "",
            destacado: 0,
            estado: 1,
            nombre: "",
            email: "",
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Comentarios = paginator.requestPager.extend({
        
        model: model,
        
        paginator_ui: {
            perPage: 100,
            order_by: 'A.fecha',
            order: 'desc',
        },        
        
        paginator_core: {
            url: "comentarios/function/ver",
        }
        
    });

})( app.collections, app.models.Comentario, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.ComentariosTableView = app.mixins.View.extend({

        template: _.template($("#comentarios_resultados_template").html()),
            
        myEvents: {
            "change #comentarios_buscar":"buscar",
            "click #comentarios_buscar_avanzada_btn":"buscar_avanzada",
            "click .exportar": "exportar",
            "click .exportar": "exportar",
            "click .importar_csv": "importar",
            "click .exportar_csv": "exportar_csv",
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
            this.id_usuario = (typeof this.options.id_usuario != "undefined") ? this.options.id_usuario : 0;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            
            this.render();
			this.collection.on('sync', this.addAll, this);
            
            this.collection.server_api = {
                "id_usuario":this.id_usuario,
            };            
            this.collection.goTo(this.pagina);
		},
        
        render: function() {
            
			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
				collection: this.collection
			});
            
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion,
                "id_usuario":this.id_usuario,
                "filter":this.filter,
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
            
        },
        
        buscar: function() {
            var self = this;
            
            var filter = this.$("#comentarios_buscar").val();
            if (typeof filter != "undefined") filter = filter.trim();
            else filter = "";
            self.filter = filter;
            
            this.collection.server_api = {
                "filter":self.filter,
                "id_usuario":self.id_usuario,
            };
            this.collection.pager();
        },
        
        buscar_avanzada: function() {
            var self = this;
            // Buscamos por usuario
            var c = self.$("#comentarios_buscar_usuarios").val();
            self.id_usuario = c;
            this.buscar();
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);    
        },
        
        addOne : function ( item ) {
            var self = this;
            var view = new app.views.ComentariosItemResultados({
                model: item,
                collection: self.collection,
                habilitar_seleccion: this.habilitar_seleccion, 
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
                
        
        importar: function() {
            app.views.importar = new app.views.Importar({
                "table":"comentarios"
            });
            crearLightboxHTML({
                "html":app.views.importar.el,
                "width":450,
                "height":140,
            });
        },        
        
        exportar: function(obj) {
            // Reemplazamos el ver por el exportar
            var url = this.collection.url;
            url = url.replace("/ver/","/exportar/");
            // Los parametros de orden se envian por GET
            url += "?order="+this.collection.paginator_ui.order+"&order_by="+this.collection.paginator_ui.order_by;
            window.open(url,"_blank");
        },
        
        exportar_csv: function(obj) {
            window.open("comentarios/function/exportar_csv/","_blank");
        },        
        
        eliminar_lote: function() {
            var checks = this.$("#comentarios_tabla .check-row:checked");
            if (checks.length == 0) return;
            if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
                $(checks).each(function(i,e){
                    var id = $(e).val();
                    var art = comentarios.get(id);
                    art.destroy();	// Eliminamos el modelo
                    $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
                });
            }            
        },
        activar_lote: function() {
            
        },
        destacar_lote: function() {
            
        },
	
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.ComentariosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#comentarios_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            "click .data":"seleccionar",
            "click .link":function(e) {
                e.stopPropagation();
                e.preventDefault();
                location.href = $(e.currentTarget).attr("href");
            },
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
                  "table":"not_entradas_comentarios",
                  "url":"comentarios/function/change_property/",
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
                var estado = this.model.get("estado");
                estado = (estado == 1)?0:1;
                self.model.set({"estado":estado});
                this.change_property({
                  "table":"not_entradas_comentarios",
                  "url":"comentarios/function/change_property/",
                  "attribute":"estado",
                  "value":estado,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
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
            this.model.fetch({
                "success":function() {
                    var c = new app.views.ComentarioEditView({
                        model: self.model,
                        permiso: 3
                    });
                    crearLightboxHTML({
                        "html":c.el,
                        "width":600,
                        "height":400,
                        "callback":function() {
                            self.collection.pager();    
                        }
                    });
                }
            });            
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

    app.views.ComentarioEditView = app.mixins.View.extend({

        template: _.template($("#comentario_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
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
        },

        validar: function() {
            try {
                var self = this;
                validate_input("comentario_texto",IS_EMPTY,"Por favor, ingrese un texto.");
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
                            $('.modal:last').modal('hide');
                        }
                    }
                });
            }	    
        },        
	
    });
})(app);
