// -----------
//   MODELO
// -----------

(function ( models ) {

    models.Sorteo = Backbone.Model.extend({
        urlRoot: "sorteos",
        defaults: {
			id_empresa: ID_EMPRESA,
			titulo: "",
			texto: "",			
            valida_desde: "",
            valida_hasta: "",
			mensaje_gracias: "",
            activo: 1,
            usuarios:[],
            path: "",
            destacado:0,
        },
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

    collections.Sorteos = paginator.requestPager.extend({

        model: model,
        
        paginator_ui: {
            perPage: 10,
            order_by: 'titulo',
            order: 'asc',
        },
    
        paginator_core: {
            url: "sorteos/function/ver/",
        }
	    
    });

})( app.collections, app.models.Sorteo, Backbone.Paginator);


// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

    app.views.SorteosTableView = app.mixins.View.extend({

        template: _.template($("#sorteos_resultados_template").html()),
            
        myEvents: {
            "change #sorteos_buscar":"buscar",
            "keydown #sorteos_tabla tbody tr .radio:first":function(e) {
                // Si estamos en el primer elemento y apretamos la flechita de arriba
                if (e.which == 38) { e.preventDefault(); $("#sorteos_texto").focus(); }
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
            
            this.collection.off('sync');
            this.collection.on('sync', this.addAll, this);
			
            $(this.el).html(this.template({
                "permiso":this.permiso,
                "seleccionar":this.habilitar_seleccion
            }));
            
            // Cargamos el paginador
            $(this.el).find(".pagination_container").html(pagination.el);
            
            this.collection.pager();
        },
        
        buscar: function() {
            var filtros = {}
            filtros.filter = $("#sorteos_buscar").val().trim();
            this.collection.server_api = filtros;
            this.collection.pager();
        },
        
        addAll : function () {
            $(this.el).find(".tbody").empty();
            this.collection.each(this.addOne);    
        },
        
        addOne : function ( item ) {
            var view = new app.views.SorteosItemResultados({
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
    app.views.SorteosItemResultados = app.mixins.View.extend({
        
        template: _.template($("#sorteos_item_resultados_template").html()),
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
                  "table":"sorteos",
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
                var self = this;
                e.stopPropagation();
                e.preventDefault();
                var destacado = this.model.get("destacado");
                destacado = (destacado == 1)?0:1;
                self.model.set({"destacado":destacado});
                this.change_property({
                  "table":"sorteos",
                  "attribute":"destacado",
                  "value":destacado,
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
                        "url":"sorteos/function/duplicar/"+self.model.id,
                        "dataType":"json",
                        "success":function(r){
                            var d = self.model.clone();
                            d.set("id",r.id);
                            sorteos.add(d);
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
                window.codigo_sorteo_seleccionado = this.model.get("codigo");
                window.sorteo_seleccionado = this.model;
                $('.modal:last').modal('hide');
            } else {
                location.href="app/#sorteo/"+this.model.id;
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

    app.views.SorteoEditView = app.mixins.View.extend({

        template: _.template($("#sorteo_template").html()),
            
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
            
            this.id_opcion = 0;
            this.render();
        },
        
        render: function() {
            
            var valida_desde = this.model.get("valida_desde");
            if (isEmpty(valida_desde)) valida_desde = moment().startOf('month').toDate();
            createtimepicker($(this.el).find("#sorteo_valida_desde"),valida_desde);
            
            var valida_hasta = this.model.get("valida_hasta");
            if (isEmpty(valida_hasta)) valida_hasta = moment().endOf('month').toDate();
            createtimepicker($(this.el).find("#sorteo_valida_hasta"),valida_hasta);
            
        },
        
        validar: function() {
            try {
                var self = this;
                
                validate_input("sorteo_titulo",IS_EMPTY,"Por favor, ingrese un titulo.");
                
                this.model.set({
                    "valida_desde":$(this.el).find("#sorteo_valida_desde").val(),
                    "valida_hasta":$(this.el).find("#sorteo_valida_hasta").val(),
                    "path":$(self.el).find("#hidden_path").val(),
                });                
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
                            location.href="app/#sorteos";
                        }
                    }
                });
            }	    
        },        
	
    });
})(app);
