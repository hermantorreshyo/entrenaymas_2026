// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Pelicula = Backbone.Model.extend({
        urlRoot: "peliculas",
        defaults: {
            // Atributos que no se persisten directamente
            images: [],
            path: "",
            nombre: "",
            link: "",
            descripcion: "",
            texto: "",
            genero: "",
            edad: "",
            activo: 1,
            valido_desde: "",
            valido_hasta: "",
            lugar: "",
            fecha_evento: "",
        },
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Peliculas = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'nombre',
            order: 'asc',
        },
    
        paginator_core: {
            url: "peliculas/function/ver/",
        }
	    
    });

})( app.collections, app.models.Pelicula, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.PeliculasTableView = app.mixins.View.extend({

        template: _.template($("#peliculas_resultados_template").html()),
            
        myEvents: {
            "change #peliculas_buscar":"buscar",
            "keydown #peliculas_tabla tbody tr .radio:first":function(e) {
                // Si estamos en el primer elemento y apretamos la flechita de arriba
                if (e.which == 38) { e.preventDefault(); $("#peliculas_texto").focus(); }
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

            this.collection.on('sync', this.addAll, this);
			
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
            var view = new app.views.PeliculasItemResultados({
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
    app.views.PeliculasItemResultados = app.mixins.View.extend({
        
        template: _.template($("#peliculas_item_resultados_template").html()),
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
                  "table":"inf_cartelera",
                  "url":"peliculas/function/change_property/",
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
                        "url":"peliculas/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            peliculas.add(d);
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
                window.codigo_pelicula_seleccionado = this.model.get("codigo");
                window.pelicula_seleccionado = this.model;
                $('.modal:last').modal('hide');
            } else {
                location.href="app/#pelicula/"+this.model.id;
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

    app.views.PeliculaEditView = app.mixins.View.extend({

        template: _.template($("#pelicula_template").html()),
            
        myEvents: {
            "click .guardar": "guardar",
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
            
            var valido_desde = this.model.get("valido_desde");
            if (isEmpty(valido_desde)) valido_desde = moment().startOf('month').toDate();
            createtimepicker($(this.el).find("#pelicula_valido_desde"),valido_desde);
            
            var valido_hasta = this.model.get("valido_hasta");
            if (isEmpty(valido_hasta)) valido_hasta = moment().endOf('month').toDate();
            createtimepicker($(this.el).find("#pelicula_valido_hasta"),valido_hasta);                        
        },
        
        validar: function() {
            try {
                var self = this;
                
                validate_input("pelicula_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                this.model.set({
                    "path":$(self.el).find("#hidden_path").val(),
                    "nombre":self.$("#pelicula_nombre").val(),
                    "descripcion":self.$("#pelicula_descripcion").val(),
                    "genero":self.$("#pelicula_genero").val(),
                    "edad":self.$("#pelicula_edad").val(),
                    "valido_desde":$(this.el).find("#pelicula_valido_desde").val(),
                    "valido_hasta":$(this.el).find("#pelicula_valido_hasta").val(),                    
                });
                
                // Texto del articulo
                var cktext = CKEDITOR.instances['pelicula_texto'].getData();
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
                            location.href="app/#peliculas";
                        }
                    }
                });
            }	    
        },
	
    });
})(app);
