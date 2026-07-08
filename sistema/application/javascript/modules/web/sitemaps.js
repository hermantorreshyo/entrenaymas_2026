// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Sitemap = Backbone.Model.extend({
        urlRoot: "sitemaps",
        defaults: {
            id_empresa: ID_EMPRESA,
            url: "",
            lastmod: "",
            priority: 0.5,
            changefreq: "",
            activo: 1,
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Sitemaps = paginator.requestPager.extend({
        
        model: model,
        
        paginator_ui: {
            perPage: 30,
            order_by: 'id',
            order: 'asc',
        },        
        
        paginator_core: {
            url: "sitemaps/function/ver",
        }
        
    });

})( app.collections, app.models.Sitemap, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.SitemapsTableView = app.mixins.View.extend({

        template: _.template($("#sitemaps_resultados_template").html()),
            
        myEvents: {
            "change #sitemaps_buscar":"buscar",
            "click .eliminar_lote":"eliminar_lote",
            "click .activar_lote":"activar_lote",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            
            this.render();
			this.collection.on('sync', this.addAll, this);
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
                "filter":this.filter,
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);            
        },
        
        buscar: function() {
            var self = this;
            
            var filter = this.$("#sitemaps_buscar").val();
            if (typeof filter != "undefined") filter = filter.trim();
            else filter = "";
            self.filter = filter;
            
            this.collection.server_api = {
                "filter":self.filter,
            };
            this.collection.pager();
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);
        },
        
        addOne : function ( item ) {
            var view = new app.views.SitemapsItemResultados({
                model: item,
                collection: this.collection,
                habilitar_seleccion: this.habilitar_seleccion, 
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
                
        eliminar_lote: function() {
            var checks = this.$("#sitemaps_tabla .check-row:checked");
            if (checks.length == 0) return;
            if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
                $(checks).each(function(i,e){
                    var id = $(e).val();
                    var art = sitemaps.get(id);
                    art.destroy();	// Eliminamos el modelo
                    $(e).parents(".seleccionado").remove(); // Lo eliminamos de la vista
                });
            }            
        },
        activar_lote: function() {
            
        },
	
    });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.SitemapsItemResultados = app.mixins.View.extend({
        
        template: _.template($("#sitemaps_item_resultados_template").html()),
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
                  "table":"web_sitemap",
                  "url":"sitemaps/function/change_property/",
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
                        "url":"sitemaps/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            sitemaps.add(d);
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
                window.codigo_sitemap_seleccionado = this.model.get("codigo");
                window.sitemap_seleccionado = this.model;
                $('.modal:last').modal('hide');                
            } else {
                location.href="app/#sitemap/"+this.model.id;
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

    app.views.SitemapEditView = app.mixins.View.extend({

        template: _.template($("#sitemap_template").html()),
            
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

            var lastmod = this.model.get("lastmod");
            if (isEmpty(lastmod)) lastmod = new Date();
            createdatepicker($(this.el).find("#lastmod"),lastmod);            
        },

        validar: function() {
            try {
                var self = this;
                validate_input("sitemap_url",IS_EMPTY,"Por favor, ingrese una URL.");
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
