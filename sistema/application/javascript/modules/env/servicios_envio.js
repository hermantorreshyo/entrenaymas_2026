// -----------
//   MODELO
// -----------

(function ( models ) {

    models.ServicioEnvio = Backbone.Model.extend({
        urlRoot: "servicios_envio/",
        defaults: {
            nombre: "",
            empresa: "",
            activo: 1,
            limite_peso: 0,
            limite_distancia: 0,
            pesos: [],
            distancias: [],
            excedentes: [],
            costos: [],
            seguro_minimo: 0,
            seguro_porcentaje: 0,
            coef_aforado: 0,
            test: 0,
            test_cliente: "",
            test_usuario: "",
            test_password: "",
            test_contrato: "",
            prod_cliente: "",
            prod_usuario: "",
            prod_password: "",
            prod_contrato: "",
        }
    });
	    
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

	collections.ServiciosEnvio = paginator.requestPager.extend({

		model: model,

		paginator_core: {
			url: "servicios_envio/"
		}
		
	});

})( app.collections, app.models.ServicioEnvio, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ServicioEnvioItem = Backbone.View.extend({
        tagName: "tr",
        attributes: function() {
            return {
                id: this.model.id // Es necesario hacer esto para reordenar
            }
        },
        template: _.template($('#servicios_envio_item').html()),
      	events: {
    		"click .edit": "editar",
    		"click .delete": "borrar",
    		"click .duplicar": "duplicar"
    	},
        initialize: function(options) {
            this.model.bind("change",this.render,this);
            this.model.bind("destroy",this.render,this);
            this.options = options;
            this.permiso = this.options.permiso;
            _.bindAll(this);
        },
        render: function()
        {
        	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
        	var obj = { permiso: this.permiso };
        	// Extendemos el objeto creado con el modelo de datos
        	$.extend(obj,this.model.toJSON());

            $(this.el).html(this.template(obj));
            return this;
        },
        editar: function() {
        	// Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
        	location.href="app/#servicio_envio/"+this.model.id;
        },
        borrar: function(e) {
            if (confirmar("Realmente desea eliminar este elemento?")) {
                this.model.destroy();	// Eliminamos el modelo
            	$(this.el).remove();	// Lo eliminamos de la vista
            }
            e.stopPropagation();
        },
        duplicar: function(e) {
        	var clonado = this.model.clone();
        	clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
        	clonado.save({},{
        		success: function(model,response) {
        			model.set({id:response.id});
        		}
        	});
        	this.model.collection.add(clonado);
            e.stopPropagation();
        }
    });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

    app.views.ServiciosEnvioTableView = app.mixins.View.extend({

    	template: _.template($("#servicios_envio_panel_template").html()),

		initialize : function (options) {

			_.bindAll(this); // Para que this pueda ser utilizado en las funciones

			var lista = this.collection;
            this.options = options;
			this.permiso = this.options.permiso;

			// Creamos el buscador
			var search = new app.mixins.SearchView({
				collection: lista
			});

			lista.on('add', this.addOne, this);
			lista.on('reset', this.addAll, this);
			lista.on('all', this.render, this);

			
			// Renderizamos por primera vez la tabla:
			// ----------------------------------------
			var obj = { permiso: this.permiso };
			
			$(this.el).html(this.template(obj));
			$(this.el).find(".search_container").html(search.el);

			// Vamos a buscar los elementos y lo servicio_enviomos
			lista.pager();
		},

		addAll : function () {
			$(this.el).find("tbody").empty();
			this.collection.each(this.addOne);
		},

		addOne : function ( item ) {
			var view = new app.views.ServicioEnvioItem({
				model: item,
				permiso: this.permiso,
			});
			$(this.el).find("tbody").append(view.render().el);
		}

	});
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.ServicioEnvioEditView = app.mixins.View.extend({

		template: _.template($("#servicios_envio_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
			"click .nuevo": "limpiar",
            "click .nuevo_peso":"nuevo_peso",
            "click .nueva_distancia":"nueva_distancia",
            "click .eliminar_fila":function(e) {
                if (confirm("Realmente desea eliminar esta fila?")) {
                    var peso = $(e.currentTarget).data("peso");
                    var pesos = _.filter(this.model.get("pesos"),function(n){
                        return n != peso;
                    });
                    this.model.set({"pesos":pesos});
                    $(e.currentTarget).parents("tr").remove();
                    
                    // Eliminamos los costos
                    var costos = this.model.get("costos");
                    costos = _.filter(costos,function(j){
                        return j.peso != peso;
                    });
                    this.model.set({"costos":costos});
                }
            },
            "click .eliminar_columna":function(e) {
                if (confirm("Realmente desea eliminar esta columna?")) {
                    var distancia = $(e.currentTarget).data("distancia");
                    var distancias = _.filter(this.model.get("distancias"),function(n){
                        return n != distancia;
                    });
                    this.model.set({"distancias":distancias});
                    
                    var numero = $(e.currentTarget).parents("tr").children().index($(e.currentTarget).parent());
                    $(e.currentTarget).parents("th").remove();
                    $("#costos_envio_tabla tbody tr").each(function(i,e){
                        $(e).find("td").eq(numero).remove();
                    });
                    
                    // Eliminamos los costos
                    var costos = this.model.get("costos");
                    costos = _.filter(costos,function(j){
                        return j.distancia != distancia;
                    });
                    this.model.set({"costos":costos});                    
                }
            },
            "change .costo":function(e) {
                // Trasladamos el valor del input al modelo de costos
                var distancia = $(e.currentTarget).data("distancia");
                var peso = $(e.currentTarget).data("peso");
                _.each(this.model.get("costos"),function(item){
                    if (item.distancia == distancia && item.peso == peso) {
                        item.costo = $(e.currentTarget).val();
                    }
                });
            },
            "change .excedente":function(e) {
                // Trasladamos el valor del input al modelo de costos
                var distancia = $(e.currentTarget).data("distancia");
                _.each(this.model.get("excedentes"),function(item){
                    if (item.distancia == distancia) {
                        item.costo = $(e.currentTarget).val();
                    }
                });
            },            
		},
        
        nuevo_peso: function() {
            var peso = prompt("Peso hasta (kg.):");
            if (!peso) return;
            peso = parseFloat(peso);
            if (isNaN(peso)) {
                alert("Por favor ingrese un numero");
                return;
            }
            var pesos = this.model.get("pesos");
            pesos.push(peso);
            pesos = _.sortBy(pesos,function(e){return e;});
            this.model.set({"pesos":pesos});
            
            var costos = this.model.get("costos");
            var distancias = this.model.get("distancias");
            for(var i=0;i<distancias.length;i++) {
                var distancia = distancias[i];
                costos.push({
                    "peso":peso,
                    "distancia":distancia,
                    "costo":0,
                });
            }
            this.model.set({"costos":costos});
            
            this.render_tabla();
        },
        
        nueva_distancia: function() {
            var distancia = prompt("Distancia hasta (km.):");
            if (!distancia) return;
            distancia = parseFloat(distancia);
            if (isNaN(distancia)) {
                alert("Por favor ingrese un numero");
                return;
            }
            var distancias = this.model.get("distancias");
            distancias.push(distancia);
            distancias = _.sortBy(distancias,function(e){return e;});
            this.model.set({"distancias":distancias});
            
            var costos = this.model.get("costos");
            var pesos = this.model.get("pesos");
            for(var i=0;i<pesos.length;i++) {
                var peso = pesos[i];
                costos.push({
                    "peso":peso,
                    "distancia":distancia,
                    "costo":0,
                });
            }
            this.model.set({"costos":costos});
            
            var excedentes = this.model.get("excedentes");
            excedentes.push({
                "peso":-1,
                "distancia":distancia,
                "costo":0,
            });
            this.model.set({"excedentes":excedentes});
            
            this.render_tabla();
        },

        initialize: function(options) {
            this.model.bind("destroy",this.render,this);
            _.bindAll(this);
            this.options = options;
            this.render();
            this.render_tabla();
        },

        render: function() {
        	var edicion = false;
            if (this.options.permiso > 1) edicion = true;
            var obj = { edicion: edicion, id:this.model.id };
        	$.extend(obj,this.model.toJSON());
        	$(this.el).html(this.template(obj));
            return this;
        },
        
        render_tabla: function() {
            $(this.el).find("#costos_envio_tabla").empty();
            var distancias = this.model.get("distancias");
            var pesos = this.model.get("pesos");
            var costos = this.model.get("costos");
            var excedentes = this.model.get("excedentes");
            
            var tr = "<thead><tr><th>Peso | Distancia</th>";
            for(var i=0; i<distancias.length; i++) {
                tr+="<th>< "+distancias[i]+" km.";
                tr+="<i data-distancia='"+distancias[i]+"' class='eliminar_columna m-l cp text-danger glyphicon glyphicon-remove'></i>";
                tr+="</th>";
            }
            tr+="<th style='width:50px'></th>";
            tr+="</tr></thead>";
            $(this.el).find("#costos_envio_tabla").append(tr);
            
            var maximo_peso = 0;
            for(var i=0; i<pesos.length; i++) {
                maximo_peso = pesos[i];
                var tr = "<tr>";
                tr+="<td>< ";
                tr+=pesos[i]+" kg.";
                tr+="</td>";
                
                for(var j=0; j<distancias.length; j++) {
                    tr+="<td>";
                    var val = _.find(costos,function(e){
                        return (e.peso == pesos[i] && e.distancia == distancias[j]);
                    });
                    tr+="<input type='number' ";
                    tr+="data-distancia='"+distancias[j]+"' ";
                    tr+="data-peso='"+pesos[i]+"' ";
                    tr+="class='costo form-control' value='"+((val == undefined) ? 0 : val.costo)+"' />";
                    tr+="</td>";
                }
                
                // Columna para eliminar la fila completa
                tr+="<td>";
                tr+="<i data-peso='"+pesos[i]+"' class='eliminar_fila text-danger glyphicon glyphicon-remove'></i>";
                tr+="</td>";
                
                tr+= "</tr>";
                $(this.el).find("#costos_envio_tabla").append(tr);
            }
            
            // La ultima fila son los precios excedentes
            // Esto se calcula por KG que pase el maximo
            var tr = "<tr>";
            tr+="<td>Excedente x kg.";
            tr+="</td>";
            for(var j=0; j<distancias.length; j++) {
                tr+="<td>";
                var val = _.find(excedentes,function(e){
                    return (e.distancia == distancias[j]);
                });
                tr+="<input type='number' data-distancia='"+distancias[j]+"' class='excedente form-control' value='"+val.costo+"' />";
                tr+="</td>";
            }
            tr+= "<td></td></tr>";
            $(this.el).find("#costos_envio_tabla").append(tr);
        },

        validar: function() {
            var self = this;
            try {
                // Validamos los campos que sean necesarios
                validate_input("servicios_envio_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                return true;                
                
            } catch(e) {
                return false;
            }
        },
        
        guardar: function() {
            var self = this;
            if (this.validar()) {
                this.model.save({},{
                    success: function(model,response) {
                        show("Los datos han sido guardados correctamente.");
                        location.reload();
                    }
                });                 
            }
		},
        
        limpiar : function() {
            this.model = new app.models.ServicioEnvio()
            this.render();
        },
		
	});

})(app.views, app.models);