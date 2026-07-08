// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Campania = Backbone.Model.extend({
        urlRoot: "campanias",
        defaults: {
            id_publicidad: 0,
            publicidad: "",
            fecha_desde: "",
            fecha_hasta: "",
            id_empresa: ID_EMPRESA,
            lunes: 1,
            martes: 1,
            miercoles: 1,
            jueves: 1,
            viernes: 1,
            sabado: 1,
            domingo: 1,
            hora_desde_1: "",
            hora_desde_2: "",
            hora_desde_3: "",
            hora_desde_4: "",
            hora_hasta_1: "",
            hora_hasta_2: "",
            hora_hasta_3: "",
            hora_hasta_4: "",
            total_impresiones: 10000,
            impresiones_disponibles: 0,
            activo: 1,
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Campanias = paginator.requestPager.extend({
        
        model: model,
        
        paginator_ui: {
            perPage: 30,
            order_by: 'fecha_desde',
            order: 'desc',
        },        
        
        paginator_core: {
            url: "campanias/function/ver",
        }
        
    });

})( app.collections, app.models.Campania, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.CampaniasTableView = app.mixins.View.extend({

        template: _.template($("#campanias_resultados_template").html()),
            
        myEvents: {
            "click .buscar":"buscar_avanzada",
            "click .exportar": "exportar",
            "click .importar_csv": "importar",
            "click .exportar_csv": "exportar_csv",
            "click .eliminar_lote":"eliminar_lote",
            "click .destacar_lote":"destacar_lote",
            "click .activar_lote":"activar_lote",
            "click .nuevo":"nuevo",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;
            this.id_publicidad = (typeof this.options.id_publicidad != "undefined") ? this.options.id_publicidad : 0;
            this.id_alumno = (typeof this.options.id_alumno != "undefined") ? this.options.id_alumno : 0;
            this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
            this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
            
            this.render();            

			this.collection.on('sync', this.addAll, this);
            
            this.collection.server_api = {
                "id_publicidad":this.id_publicidad,
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
                "id_publicidad":this.id_publicidad,
                "filter":this.filter,
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
            
            // SELECT DE LIBROS
            new app.mixins.Select({
                modelClass: app.models.Publicidad,
                url: "publicidades/",
                render: "#campanias_buscar_publicidades",
                firstOptions: ["<option value='0'>Publicidad</option>"],
                selected: self.id_publicidad,
                success:function(c) {
                    self.$("#campanias_buscar_publicidades").select2({});
                },
            });
        },
        
        nuevo: function() {
            var self = this;
            var campaniaView = new app.views.CampaniaEditView({
                model: new app.models.Campania(),
                collection: self.collection,
            });
            var d = $("<div/>").append(campaniaView.el);
            crearLightboxHTML({
                "html":d,
                "width":750,
                "height":500,
            });
        },
        
        buscar: function() {
            var self = this;
            this.collection.server_api = {
                "id_publicidad":self.id_publicidad,
            };
            this.collection.pager();
        },
        
        buscar_avanzada: function() {
            var self = this;
            // Buscamos por categoria
            self.id_publicidad = self.$("#campanias_buscar_publicidades").val();
            this.buscar();
        },
        
        addAll : function () {
            if (this.$(".seccion_vacia").is(":visible")) this.render();
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);    
        },
        
        addOne : function ( item ) {
            var view = new app.views.CampaniasItemResultados({
                collection: this.collection,
                model: item,
                habilitar_seleccion: this.habilitar_seleccion, 
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
                
        importar: function() {
            app.views.importar = new app.views.Importar({
                "table":"campanias"
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
            window.open("campanias/function/exportar_csv/","_blank");
        },        
                
        eliminar_lote: function() {
            var checks = this.$("#campanias_tabla .check-row:checked");
            if (checks.length == 0) return;
            if (confirm("Realmente desea eliminar los elementos seleccionados?")) {
                $(checks).each(function(i,e){
                    var id = $(e).val();
                    var art = campanias.get(id);
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
    app.views.CampaniasItemResultados = app.mixins.View.extend({
        
        template: _.template($("#campanias_item_resultados_template").html()),
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
            "click .activo":function(e) {
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var activo = this.model.get("activo");
                activo = (activo == 1)?0:1;
                self.model.set({"activo":activo});
                this.change_property({
                  "table":"crm_campanias",
                  "url":"campanias/function/change_property/",
                  "attribute":"activo",
                  "value":activo,
                  "id":self.model.id,
                  "success":function(){
                    self.render();
                  }
                });
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
            var campaniaView = new app.views.CampaniaEditView({
                collection: self.collection,
                model: self.model
            });
            var d = $("<div/>").append(campaniaView.el);
            crearLightboxHTML({
                "html":d,
                "width":750,
                "height":500,
            });
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



(function ( app ) {

    app.views.CampaniaEditView = app.mixins.View.extend({

        template: _.template($("#campania_template").html()),
            
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
            
            new app.mixins.Select({
                modelClass: app.models.Publicidad,
                url: "publicidades/",
                render: "#campania_publicidades",
                selected: self.model.get("id_publicidad"),
                campoSelect: "nombre,categoria",
                onComplete:function(c) {
                    self.$("#campania_publicidades").removeClass("form-control");
                    self.$("#campania_publicidades").select2({});
                }
            });            
            
            var fecha_desde = this.model.get("fecha_desde");
            if (isEmpty(fecha_desde)) fecha_desde = new Date();
            createdatepicker($(this.el).find("#campania_fecha_desde"),fecha_desde);

            var fecha_hasta = this.model.get("fecha_hasta");
            if (isEmpty(fecha_hasta)) fecha_hasta = new Date();
            createdatepicker($(this.el).find("#campania_fecha_hasta"),fecha_hasta);
            
            this.$("#campania_hora_desde_1").mask("99:99");
            this.$("#campania_hora_desde_2").mask("99:99");
            this.$("#campania_hora_desde_3").mask("99:99");
            this.$("#campania_hora_desde_4").mask("99:99");
            this.$("#campania_hora_hasta_1").mask("99:99");
            this.$("#campania_hora_hasta_2").mask("99:99");
            this.$("#campania_hora_hasta_3").mask("99:99");
            this.$("#campania_hora_hasta_4").mask("99:99");
        },
        
        validar: function() {
            try {
                var self = this;
                
                var id_publicidad = self.$("#campania_publicidades").val();
                if (id_publicidad == 0) {
                    alert("Por favor seleccione un publicidad");
                    self.$("#campania_publicidades").focus();
                    return false;
                }
                var fecha_desde = self.$("#campania_fecha_desde").val();
                if (isEmpty(fecha_desde)) {
                    alert("Por favor elija una fecha");
                    self.$("#campania_fecha_desde").focus();
                    return false;
                }
                var fecha_hasta = self.$("#campania_fecha_hasta").val();
                if (isEmpty(fecha_desde)) {
                    alert("Por favor elija una fecha");
                    self.$("#campania_fecha_hasta").focus();
                    return false;
                }

                this.model.set({
                    "id_publicidad":id_publicidad,
                    "publicidad":self.$("#campania_publicidades option:selected").text(),
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
                    var total = this.model.get("total_impresiones");
                    this.model.set({
                        id:0,
                        impresiones_disponibles:total,
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