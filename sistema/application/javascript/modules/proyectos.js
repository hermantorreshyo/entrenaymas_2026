// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Proyecto = Backbone.Model.extend({
    urlRoot: "proyectos/",
    defaults: {
      nombre: "",
      modulos: [],
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Proyectos = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "proyectos/"
    },

    paginator_ui: {
      perPage: 10000,
    },
    
  });

})( app.collections, app.models.Proyecto, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

    app.views.ProyectoItem = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#proyectos_item').html()),
        events: {
        "click": "editar",
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
          location.href="app/#proyecto/"+this.model.id;
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

    app.views.ProyectosTableView = Backbone.View.extend({

      template: _.template($("#proyectos_panel_template").html()),

    initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
            this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });

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
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
    },

    addOne : function ( item ) {
      var view = new app.views.ProyectoItem({
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

  views.ProyectoEditView = app.mixins.View.extend({

    template: _.template($("#proyectos_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click #modulo_agregar":"agregar_modulo",
      "click .editar_modulo":"editar_modulo",
      "click .eliminar_modulo":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keydown #proyecto_modulo_clase":function(e) {
        if (e.which == 13) this.agregar_modulo();
      },
    },

    agregar_modulo: function() {
      
      var nombre_es = $("#proyecto_modulo_nombre_es").val();
      if (isEmpty(nombre_es)) {
        alert("Por favor ingrese un nombre");
        $("#proyecto_modulo_nombre_es").focus();
        return;
      }
      var id_modulo = $("#proyecto_modulo_modulos").val();
      var modulo = $("#proyecto_modulo_modulos option:selected").text();
      var orden_2 = ($("#proyecto_modulo_orden_2").val());
      var orden_1 = ($("#proyecto_modulo_orden_1").val());
      if (isEmpty(orden_1)) orden_1 = 0;
      if (isEmpty(orden_2)) orden_2 = 0;
      var estado = $("#proyecto_modulo_estado").val();
      var clase = $("#proyecto_modulo_clase").val();
      var nombre_estado = (estado==1)?"Habilitado" : ((estado==2)?"Por defecto":"");

      var tr = "<tr>";
      tr+="<td class='id_modulo dn'>"+id_modulo+"</td>";
      tr+="<td class='estado dn'>"+estado+"</td>";
      tr+="<td class='clase dn'>"+clase+"</td>";
      tr+="<td class='orden_1 editar_modulo'>"+orden_1+"</td>";
      tr+="<td class='orden_2 editar_modulo'>"+orden_2+"</td>";
      tr+="<td class='editar_modulo'><i class='"+clase+"'></i></td>";
      tr+="<td class='editar_modulo'><span class='text-info'>"+((orden_1 != 0 && orden_2 != 0)?"<span class='dib w30'>-></span>":"")+" <span class='nombre_es'>"+nombre_es+"</span></span></td>";
      tr+="<td class='editar_modulo'>"+modulo+"</td>";
      tr+="<td class='editar_modulo'>"+nombre_estado+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_modulo'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_modulo == null) {
        if ($("#proyecto_modulos_tabla tbody").length == 0) {
          $("#proyecto_modulos_tabla tbody").append(tr);  
        } else {
          // Buscamos el lugar donde insertarlo
          orden_1 = parseInt(orden_1);
          orden_2 = parseInt(orden_2);
          var inserto = false;
          $("#proyecto_modulos_tabla tbody tr").each(function(i,e){
            var o1 = parseInt($(e).find(".orden_1").text());
            var o2 = parseInt($(e).find(".orden_2").text());
            if (o1 < orden_1) return true; // Continue
            else if (o1 == orden_1) {
              if (o2 < orden_2) return true; // Continue
              else {
                inserto = true;
                $(e).before(tr);
                return false; // BREAK
              }
            } else {
              inserto = true;
              $(e).before(tr);
              return false; // BREAK
            }
          });
          if (!inserto) $("#proyecto_modulos_tabla tbody").append(tr);
        }
        
      } else {
        $(this.item_modulo).replaceWith(tr);
        this.item_modulo = null;
      }
      $("#proyecto_modulo_nombre_es").val("");
      $("#proyecto_modulo_modulos").val(0).trigger('change');
      $("#proyecto_modulo_orden_2").val("");
      $("#proyecto_modulo_orden_1").val("");
      $("#proyecto_modulo_clase").val("");
      $("#proyecto_modulo_estado").val(1);
    },
    
    editar_modulo: function(e) {
      this.item_modulo = $(e.currentTarget).parents("tr");
      $("#proyecto_modulo_modulos").val($(this.item_modulo).find(".id_modulo").text()).trigger('change');
      $("#proyecto_modulo_nombre_es").val($(this.item_modulo).find(".nombre_es").text());
      $("#proyecto_modulo_orden_2").val($(this.item_modulo).find(".orden_2").text());
      $("#proyecto_modulo_orden_1").val($(this.item_modulo).find(".orden_1").text());
      $("#proyecto_modulo_clase").val($(this.item_modulo).find(".clase").text());
      $("#proyecto_modulo_estado").val($(this.item_modulo).find(".estado").text());
      $("#proyecto_modulo_nombre_es").select();
    },

    initialize: function(options) {
      _.bindAll(this);
      this.model.bind("destroy",this.render,this);
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

      this.$("#proyecto_modulo_modulos").select2({});

      /*
        var id = this.model.id;
        if (id == undefined) id = 0;
        $.ajax({
            "url":'proyectos/function/get_modulos/'+id,
            "dataType":"json",
            "success":function(r){
                var t = self.crear_nestable(r);
                $("#modulos_tree").html(t);
                $("#modulos_tree").nestable();
                $("#modulos_tree").nestable('collapseAll');
            }
        });
        return this;
      */
    },

        crear_nestable: function(array, config) {
            var self = this;
            if (typeof config === "undefined") config = {};
            config.seleccionar = (config.seleccionar || false);
            if (typeof config.ordenable === "undefined") config = {};
            if (typeof array === "undefined") return "";
            if (array.length == 0) return "";
            var r = '<ol class="dd-list">';
            for(var i=0;i<array.length;i++) {
                var o = array[i];
                var p = o.estado;

                r+='<li class="dd-item dd3-item" data-id="'+o.id+'">';
                if (!config.seleccionar) r+='<div class="dd-handle dd3-handle">Drag</div>';
                r+='<div data-id="'+o.id+'" class="dd3-content">';
                r+='<input type="text" class="nombre no-model" value="'+o.title+'"/>';
                r+='<span>('+o.id+')</span>';
                r+='<span class="pull-right">';
                r+="<select class='estado fr no-model'>";
                r+="<option value='0' "+((p==0)?"selected":"")+">-</option>";
                r+="<option value='1' "+((p==1)?"selected":"")+">Habilitado</option>";
                r+="<option value='2' "+((p==2)?"selected":"")+">Por Defecto</option>";
                r+="<select>";
                r+='</span>';
                r+='</div>';
                r+=self.crear_nestable(o.children);
                r+='</li>';
            }
            r+='</ol>';
            return r;
        },

        reorder: function() {
            var self = this;
            var serialize = this.$('.dd').nestable('serialize');
            $.ajax({
                "url":"proyectos/function/reorder/"+self.model.id,
                "type":"post",
                "dataType":"json",
                "data":{
                    "datos":serialize,
                },
                "success":function() {
                    location.reload();
                }
            });
        },

        validar: function() {
            try {
                // Validamos los campos que sean necesarios
                validate_input("proyectos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");
                // No hay ningun error
                $(".error").removeClass("error");
                return true;
            } catch(e) {
                return false;
            }
        },
        

        guardar: function() 
        {
            var self = this;
            if (this.validar()) {

            var modulos = new Array();
            if (this.$("#proyecto_modulos_tabla").length > 0) {
              $("#proyecto_modulos_tabla tbody tr").each(function(i,e){
                modulos.push({
                  "nombre_es": $(e).find(".nombre_es").text(),
                  "id_modulo": $(e).find(".id_modulo").text(),
                  "orden_1": $(e).find(".orden_1").text(),
                  "estado": $(e).find(".estado").text(),
                  "clase": $(e).find(".clase").text(),
                  "orden_2": $(e).find(".orden_2").text(),
                });
              });
              this.model.set({"modulos":modulos});
            }
                
                // Obtenemos los IDS de los permisos seleccionados por el usuario
                /*
                var ids = new Array();
                $("#modulos_tree .dd3-content").each(function(i,e){
                    var id = $(e).data("id");
                    var permiso = $(e).find(".estado").val();
                    if (permiso == null) permiso = 0;
                    var nombre = $(e).find(".nombre").val();
                    var o = { "id":id, "estado":permiso, "nombre":nombre };
                    ids.push(o);
                });
                */
                
                // Lo agregamos el modelo
                //this.model.set({"modulos":ids});
                
                if (this.model.id == null) {
                    this.model.set({id:0});
                }
                this.model.save({},{
                    success: function(model,response) {
                      location.reload();
                    }
                });
            }
    },
    
        limpiar : function() {
            this.model = new app.models.Proyecto()
            this.render();
        },
    
  });

})(app.views, app.models);
