// -----------
//   MODELO
// -----------

(function ( models ) {

    models.LandingPage = Backbone.Model.extend({
        urlRoot: "landing_pages",
        defaults: {
            // Atributos que no se persisten directamente
            images: [],
            path: "",
            activo: 1,
            nombre: "",
            link: "",
            subtitulo: "",
            breve: "",
            texto: "",
            precio_final: 0,
            porc_dto: 0,
            precio_final_dto: 0,
            nuevo: 0,
            caracteristicas: "",
            texto_boton: "",
            mostrar_form_contacto: 1,
            email_form_contacto: EMAIL_USUARIO,
            codigos: "",
            path: "",
            link_landing: "",
        },
    });
	    
})( app.models );


// -----------
//   MODELO
// -----------

(function ( models ) {

    models.LandingPageImpresion = Backbone.Model.extend({
        urlRoot: "landing_pages",
        defaults: {
            activo: 0,
            nombre: "",
            impresiones: 0,
            promedio_impresiones_dia: 0,
            clicks: 0,
            contactos: 0,
        },
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.LandingPages = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'nombre',
            order: 'asc',
        },
    
        paginator_core: {
            url: "landing_pages/function/ver/",
        }
	    
    });

})( app.collections, app.models.LandingPage, Backbone.Paginator);


(function (collections, model, paginator) {

    collections.LandingPagesImpresiones = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'nombre',
            order: 'asc',
        },
    
        paginator_core: {
            url: "landing_pages/function/impresiones/",
        }
	    
    });

})( app.collections, app.models.LandingPageImpresion, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.LandingPagesTableView = app.mixins.View.extend({

        template: _.template($("#landing_pages_resultados_template").html()),
            
        myEvents: {
            "change #landing_pages_buscar":"buscar",
            "keydown #landing_pages_tabla tbody tr .radio:first":function(e) {
                // Si estamos en el primer elemento y apretamos la flechita de arriba
                if (e.which == 38) { e.preventDefault(); $("#landing_pages_texto").focus(); }
            },
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
            
            this.collection.pager();
        },
        
        addAll : function () {
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);    
        },
        
        addOne : function ( item ) {
            var view = new app.views.LandingPagesItemResultados({
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
    app.views.LandingPagesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#landing_pages_item_resultados_template").html()),
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
                  "table":"landing_pages",
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
                        "url":"landing_pages/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            landing_pages.add(d);
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
                window.codigo_landing_page_seleccionado = this.model.get("codigo");
                window.landing_page_seleccionado = this.model;
                $('.modal:last').modal('hide');
            } else {
                location.href="app/#landing_page/"+this.model.id;
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

    app.views.LandingPageEditView = app.mixins.View.extend({

        template: _.template($("#landing_page_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
            
            "change #landing_page_tipos":function(e) {
                var id = $(e.currentTarget).val();
                this.$(".landing_page_tipo_container").hide();
                this.$("#landing_page_tipo_"+id).show();
            },
            
            "click .importar_articulos":"importar_articulos",
            
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
            this.render();
        },
        
        render: function() {
            this.render_tabla_fotos();
            $(this.el).find("#landing_pages_tabla").sortable();
            this.$("#landing_page_tipos").change();
        },
        
        render_tabla_fotos: function() {
            var images = this.model.get("images");
            for(var i=0;i<images.length;i++) {
                var path = images[i];
                var pth = path+"?t="+parseInt(Math.random()*100000);
                var li = "";
                li+="<li class='list-group-item'>";
                li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
                li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
                li+=" <span class='filename'>"+path+"</span>";
                li+=" <span class='cp pull-right m-t eliminar_foto'><i class='fa fa-fw fa-times'></i> </span>";
                li+=" <span data-id='propiedades' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
                li+="</li>";
                this.$("#landing_pages_tabla").append(li);
            }
        },        
        
        validar: function() {
            try {
                var self = this;
                
                validate_input("landing_page_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                this.model.set({
                    "path":$(self.el).find("#hidden_path").val(),
                    "nombre":self.$("#landing_page_nombre").val(),
                    "subtitulo":self.$("#landing_page_subtitulo").val(),
                    "breve":self.$("#landing_page_breve").val(),
                    "nuevo":self.$("#landing_page_nuevo").prop("checked")?1:0,
                    "precio_final":self.$("#landing_page_precio_final").val(),
                    "porc_dto":self.$("#landing_page_porc_dto").val(),
                    "precio_final_dto":self.$("#landing_page_precio_final_dto").val(),
                    "link":self.$("#landing_page_link").val(),
                });
                
                // Listado de Imagenes
                var images = new Array();
                $(this.el).find("#landing_pages_tabla .list-group-item .filename").each(function(i,e){
                    images.push($(e).text());
                });
                self.model.set({"images":images});
                
                // Texto del articulo
                var cktext = CKEDITOR.instances['landing_page_texto'].getData();
                self.model.set({"texto":cktext});                
                
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                console.log(e);
                return false;
            }
        },	
	
        guardar:function() {
            var self = this;
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
                            location.href="app/#landing_pages";
                        }
                    }
                });
            }	    
        },
        
        importar_articulos: function() {
            var self = this;
            app.views.articulosTableView = new app.views.ArticulosTableView({
                collection: articulos,
                habilitar_seleccion: true,
                permiso: 1
            });
            crearLightboxHTML({
                "html":app.views.articulosTableView.el,
                "width":800,
                "height":350,
                "callback":function() {
                    if (typeof window.articulo_seleccionado === "undefined") return;
                    // Llenamos los campos
                    var a = window.articulo_seleccionado;
                    self.$("#landing_page_nombre").val(a.get("nombre"));
                    self.$("#landing_page_subtitulo").val(a.get("rubro"));
                    self.$("#landing_page_breve").val(a.get("breve"));
                    self.$("#landing_page_nuevo").prop("checked",(a.get("nuevo")==1));
                    self.$("#landing_page_precio_final").val(a.get("moneda")+" "+a.get("precio_final"));
                    self.$("#landing_page_porc_dto").val(a.get("porc_bonif"));
                    self.$("#landing_page_precio_final_dto").val(a.get("moneda")+" "+a.get("precio_final_dto"));
                    self.$("#landing_page_link").val(DOMINIO+a.get("link"));
                    self.set_image("path","/sistema/"+a.get("path"));
                },
            });
        },        
	
    });
})(app);





// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.LandingPagesImpresionesView = app.mixins.View.extend({

        template: _.template($("#landing_pages_impresiones_resultados_template").html()),
            
        myEvents: {
            "click .fa-search":function() {
                this.collection.pager();
            },
        },
        
        initialize : function (options) {
            
            var self = this;
            _.bindAll(this); // Para que this pueda ser utilizado en las funciones
            this.options = options;
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
            }));
            
            // Cargamos el paginador
            $(this.el).find(".pagination_container").html(pagination.el);
        },
        
        addAll : function () {
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);
        },
        
        addOne : function (item) {
            var view = new app.views.LandingPagesImpresionesItemResultados({
                model: item,
            });
            $(this.el).find(".tbody").append(view.render().el);
        },
        
    });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
    app.views.LandingPagesImpresionesItemResultados = app.mixins.View.extend({
        
        template: _.template($("#landing_pages_impresiones_item_resultados_template").html()),
        tagName: "tr",
        myEvents: {
            
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

