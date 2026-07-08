(function ( models ) {

  models.PetipsProducto = Backbone.Model.extend({
    urlRoot: "petips_productos/",
    defaults: {
      nombre: "",
      texto: "",
      id_marca: 0,
      id_animal: 0,
      id_segmento: 0,
      id_tipo_alimento: 0,
      id_fabricante: 0,
      path: "",
      images: "",
      proteina: 0,
      grasa: 0,
      humedad: 0,
      fibra: 0,
      cenizas: 0,
      calcio: 0,
      fosforo: 0,
      carbohidratos: 0,
      id_empresa: ID_EMPRESA,
      ingredientes: [],
      claims: [],
      edades: [],
      es_hipoalergenico: 0,
      es_natural: 0,
      reputacion_mercado: 0,
      opiniones_clientes: 0,
      nutricionalmente_completo: 0,
      id_especialidad: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PetipsProductos = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "petips_productos/"
    }
  });
})( app.collections, app.models.PetipsProducto, Backbone.Paginator);


(function ( app ) {
  app.views.PetipsProductoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#petips_productos_item').html()),
    events: {
      "click .ver": "editar",
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
    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var obj = { permiso: this.permiso };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));
      return this;
    },
    editar: function() {
      // Cuando editamos un elemento, indicamos a la vista que lo cargue en los campos
      location.href="app/#petips_producto/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
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

  app.views.PetipsProductosTableView = app.mixins.View.extend({

   template: _.template($("#petips_productos_panel_template").html()),

    initialize : function (options) {
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: self.collection,
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: self.collection,
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      self.collection.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PetipsProductoItem({
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

  views.PetipsProductoEditView = app.mixins.View.extend({

    template: _.template($("#petips_productos_edit_panel_template").html()),

    myEvents: {
      "submit form": "guardar",
      "click .nuevo": "limpiar",

      "click #ingrediente_agregar":"agregar_ingrediente",
      "click .eliminar_ingrediente":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      "click #claim_agregar":"agregar_claim",
      "click .eliminar_claim":function(e){
        $(e.currentTarget).parents("tr").remove();
      },

      // ABRIMOS MODAL PARA UPLOAD MULTIPLE
      "click .upload_multiple":function(e) {
        var self = this;
        this.open_multiple_upload({
          "model": self.model,
          "url": "articulos/function/upload_images/",
          "view": self,
        });
      },

    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      $(this.el).find("#images_tabla").sortable();

      new app.mixins.Select({
        modelClass: app.models.PetipsIngrediente,
        url: "petips_ingredientes/",
        render: "#articulo_ingredientes",
        firstOptions: ["<option value='0'>Ingrediente</option>"],
        onComplete:function(c) {
          $("#articulo_ingredientes").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsClaim,
        url: "petips_claims/",
        render: "#articulo_claims",
        firstOptions: ["<option value='0'>Claim</option>"],
        onComplete:function(c) {
          $("#articulo_claims").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsEdad,
        url: "petips_edades/",
        render: "#articulo_edades",
        firstOptions: ["<option value='0'>Edad</option>"],
        onComplete:function(c) {
          $("#articulo_edades").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsTipoAlimento,
        url: "petips_tipos_alimentos/",
        selected: self.model.get("id_tipo_alimento"),
        render: "#articulo_tipos_alimentos",
        firstOptions: ["<option value='0'>Tipo de Alimento</option>"],
        onComplete:function(c) {
          $("#articulo_tipos_alimentos").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsAnimal,
        url: "petips_animales/",
        selected: self.model.get("id_animal"),
        render: "#articulo_animales",
        firstOptions: ["<option value='0'>Especie</option>"],
        onComplete:function(c) {
          $("#articulo_animales").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsFabricante,
        url: "petips_fabricantes/",
        selected: self.model.get("id_fabricante"),
        render: "#articulo_fabricantes",
        firstOptions: ["<option value='0'>Fabricante</option>"],
        onComplete:function(c) {
          $("#articulo_fabricantes").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsEspecialidad,
        url: "petips_especialidades/",
        selected: self.model.get("id_especialidad"),
        render: "#articulo_especialidades",
        firstOptions: ["<option value='0'>Especialidad</option>"],
        onComplete:function(c) {
          $("#articulo_especialidades").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsSegmento,
        url: "petips_segmentos/",
        selected: self.model.get("id_segmento"),
        render: "#articulo_segmentos",
        firstOptions: ["<option value='0'>Segmento</option>"],
        onComplete:function(c) {
          $("#articulo_segmentos").select2({})
        }
      });

      new app.mixins.Select({
        modelClass: app.models.PetipsMarca,
        url: "petips_marcas/",
        selected: self.model.get("id_marca"),
        render: "#articulo_marcas",
        firstOptions: ["<option value='0'>Marca</option>"],
        onComplete:function(c) {
          $("#articulo_marcas").select2({})
        }
      });

      return this;
    },

    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length == 0) {
        this.$("#images_container").removeClass('tiene');
      } else {
        this.$("#images_container").addClass('tiene');
        for(var i=0;i<images.length;i++) {
          var path = images[i];
          var pth = path+"?t="+parseInt(Math.random()*100000);
          var li = "";
          li+="<li class='list-group-item'>";
          li+=" <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>";
          li+=" <img style='margin-left: 10px; margin-right:10px; max-height:50px' class='img_preview' src='"+pth+"'/>";
          li+=" <span class='filename'>"+path+"</span>";
          li+=" <span class='cp pull-right m-t eliminar_foto' data-property='images'><i class='fa fa-fw fa-times'></i> </span>";
          li+=" <span data-id='images' class='cp m-r pull-right m-t editar_foto_multiple'><i class='fa fa-pencil'></i> </span>";
          li+="</li>";
          this.$("#images_tabla").append(li);
        }                
      }
    },    

    agregar_ingrediente: function() {
        
      var id_ingrediente = $("#articulo_ingredientes").val();
      if (id_ingrediente == 0) {
        alert("Por favor seleccione un ingrediente");
        $("#articulo_ingredientes").focus();
        return;
      }
      var ingrediente = $("#articulo_ingredientes option:selected").text();

      var tr = "<tr data-id='"+id_ingrediente+"'>";
      tr+="<td>"+ingrediente+"</td>";
      tr+="<td><i class='fa fa-times eliminar_ingrediente text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#ingredientes_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#articulo_ingredientes").focus();
    },

    agregar_claim: function() {
        
      var id_claim = $("#articulo_claims").val();
      if (id_claim == 0) {
        alert("Por favor seleccione un claim");
        $("#articulo_claims").focus();
        return;
      }
      var claim = $("#articulo_claims option:selected").text();

      var tr = "<tr data-id='"+id_claim+"'>";
      tr+="<td>"+claim+"</td>";
      tr+="<td><i class='fa fa-times eliminar_claim text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#claims_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#articulo_claims").focus();
    },

    validar: function() {
      var self = this;
      try {
        // Validamos los campos que sean necesarios

        // Listado de Imagenes
        if ($(this.el).find("#images_tabla").length > 0) {
          var images = new Array();
          $(this.el).find("#images_tabla .list-group-item .filename").each(function(i,e){
            if (i==0) self.model.set({"path":$(e).text()});
            images.push($(e).text());
          });
          self.model.set({"images":images});
        }        
        
        // Guardamos los ingredientes
        var ingredientes = new Array();
        $("#ingredientes_tabla tbody tr").each(function(i,e){
          var nombre = $(e).find("td:eq(0)").html();
          nombre = nombre.replace(/\"/g,"");
          nombre = nombre.replace(/\'/g,"");
          nombre = nombre.replace(/\`/g,"");
          nombre = nombre.replace(/\´/g,"");
          ingredientes.push({
            "id_ingrediente": $(e).data("id"),
            "nombre": nombre,
          });
        });
        this.model.set({"ingredientes":ingredientes});

        // Guardamos los claims
        var claims = new Array();
        $("#claims_tabla tbody tr").each(function(i,e){
          var nombre = $(e).find("td:eq(0)").html();
          nombre = nombre.replace(/\"/g,"");
          nombre = nombre.replace(/\'/g,"");
          nombre = nombre.replace(/\`/g,"");
          nombre = nombre.replace(/\´/g,"");
          claims.push({
            "id_claim": $(e).data("id"),
            "nombre": nombre,
          });
        });
        this.model.set({"claims":claims});        

        this.model.set({
          "es_hipoalergenico":(this.$("#articulo_es_hipoalergenico").is(":checked")?1:0),
          "es_natural":(this.$("#articulo_es_natural").is(":checked")?1:0),
          "nutricionalmente_completo":(this.$("#articulo_nutricionalmente_completo").is(":checked")?1:0),
          "id_marca":(self.$("#articulo_marcas").val()),
          "id_segmento":(self.$("#articulo_segmentos").val()),
          "id_animal":(self.$("#articulo_animales").val()),
          "id_fabricante":(self.$("#articulo_fabricantes").val()),
          "id_tipo_alimento":(self.$("#articulo_tipos_alimentos").val()),
        });        

        return true;
      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({
          "id_empresa":ID_EMPRESA,
        },{
          success: function(model,response) {
            location.href="app/#petips_productos";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.PetipsProducto();
      this.render();
    },

  });

})(app.views, app.models);