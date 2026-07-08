// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Email = Backbone.Model.extend({
        urlRoot: "emails",
        defaults: {
            id_empresa: ID_EMPRESA,
            id_usuario: 0,
            email_from: "",
            email_to: "",
            email_bc: "",
            email_bcc: "",
            fecha: "",
            asunto: "",
            texto: "",
            adjuntos: [],
            archivo: "",
        },
    });
	    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Emails = paginator.clientPager.extend({

        model: model,
        
        paginator_core: {
            url: "emails/function/ver",
        }
	    
    });

})( app.collections, app.models.Email, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.EmailsTableView = app.mixins.View.extend({

        template: _.template($("#emails_resultados_template").html()),
            
        myEvents: {
            "change #emails_buscar":"buscar",
            "click #emails_buscar_avanzada_btn":"buscar_avanzada",
        },
        
		initialize : function (options) {
            
            var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
            this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;

			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
                ver_filas_pagina: true,
				collection: this.collection
			});

			this.collection.on('add', this.addOne, this);
            this.collection.on('pager', this.addAll, this);
			
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion
            }));
            
			// Cargamos el paginador
			$(this.el).find(".pagination_container").html(pagination.el);
            
            if (control.check("marcas")>0) {
                new app.mixins.Select({
                    modelClass: app.models.Marca,
                    url: "marcas/",
                    render: "#emails_buscar_marcas",
                    firstOptions: ["<option value='0'>Marca</option>"],
                    selected: self.collection.getFilter("id_marca"),
                    success:function(c) {
                        $("#emails_buscar_marcas").select2({
                        }).on("change",function(e){
                            var c = $(self.el).find("#emails_buscar_marcas").select2("val");
                            if (c == 0) self.collection.removeFilter("id_marca");
                            else self.collection.addFilter("id_marca",c);
                        });
                    }
                });
            }
            
            // BUSQUEDA AVANZADA POR CATEGORIAS
            new app.mixins.Select({
                modelClass: app.models.Rubro,
                url: "rubros/",
                render: "#emails_buscar_categorias",
                firstOptions: ["<option value='0'>Categoria</option>"],
                selected: self.collection.getFilter("id_rubro"),
                success:function(c) {
                    $("#emails_buscar_categorias").select2({
                    }).on("change",function(e){
                        var c = $(self.el).find("#emails_buscar_categorias").select2("val");
                        if (c == 0) self.collection.removeFilter("id_rubro");
                        else self.collection.addFilter("id_rubro",c);
                    });
                }
            });
            
            // BUSQUEDA AVANZADA POR PROVEEDORES
            new app.mixins.Select({
                modelClass: app.models.Proveedor,
                url: "proveedores/",
                render: "#emails_buscar_proveedores",
                firstOptions: ["<option value='0'>Proveedor</option>"],
                selected: self.collection.getFilter("id_proveedor"),
                success:function(c) {
                    $("#emails_buscar_proveedores").select2({
                    }).on("change",function(e){
                        var c = $(self.el).find("#emails_buscar_proveedores").select2("val");
                        if (c == 0) self.collection.removeFilter("id_proveedor");
                        else self.collection.addFilter("id_proveedor",c);
                    });
                }
            });            

            this.collection.pager();
		},
        
        buscar: function() {
            var filter = $("#emails_buscar").val().trim();
            this.collection.setFilter(["nombre","codigo"],filter);
        },
        
        buscar_avanzada: function() {
        },
        
        addAll : function () {
            $(this.el).find(".tbody").empty();
            var coleccion = this.collection.getFilterCollection();
            if (coleccion.size() == 0) {
                $(this.el).find(".tbody").append("<tr><td colspan='12'>No se encontraron resultados.</td></tr>");
            } else {
                coleccion.each(this.addOne);    
            }
        },
        
        addOne : function ( item ) {
            var view = new app.views.EmailsItemResultados({
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
    app.views.EmailsItemResultados = app.mixins.View.extend({
        
        template: _.template($("#emails_item_resultados_template").html()),
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
                e.stopPropagation();
                e.preventDefault();
                var destacado = this.model.get("destacado");
                destacado = (destacado == 1)?0:1;
                this.model.save({
                    "destacado":destacado
                });
                this.render();
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

    app.views.EmailView = app.mixins.View.extend({

        template: _.template($("#email_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            "click .eliminar_adjunto":"eliminar_adjunto",
            "click .adjuntar_archivos": "adjuntar_archivos",
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
                validate_input("email_nombre",IS_EMPTY,"Por favor, ingrese un destinatario del email.");
                
                var cktext = CKEDITOR.instances['email_texto'].getData();
                self.model.set({
                    "texto":cktext,
                    "email_from":$("#email_from").val(),
                    "archivo":self.$("#hidden_archivo").val(),
                });
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
                            //emails.add(model);
                            $('.modal:last').modal('hide');
                        }
                    }
                });
            }	    
        },
        
        adjuntar_archivos: function() {
            
        },
        
        eliminar_adjunto: function(e) {
            var adjuntos = this.model.get("adjuntos");
            var pos = $(e.currentTarget).data("position");
            if (pos <= 0) return;
            this.model.set("adjuntos",adjuntos.splice(pos,1));
            $(e.currentTarget).parent().remove();
        },
    });
})(app);
