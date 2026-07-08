// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Bobina = Backbone.Model.extend({
    urlRoot: "bobinas/",
    defaults: {
      ancho: 0,
      fecha_alta: "",
      fecha_baja: "",
      gramaje: 0,
      numero: 0,
      peso: 0,
      id_tipo_bobina: 0,
      procedencia: "",
      codigo_proveedor: "",
      observaciones: "",
    }
  });
      
})( app.models );


// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Bobinas = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "bobinas/function/buscar/"
    }
    
  });

})( app.collections, app.models.Bobina, Backbone.Paginator);



// ------------------------------
//   VISTA DE ITEM DE LA TABLA
// ------------------------------

(function ( app ) {

  app.views.BobinaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#bobinas_item_resultados_template').html()),
    myEvents: {
      "click .data": "editar",
      "click .eliminar": "borrar",
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
      location.href="app/#bobina/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realimente desea eliminar este elemento?")) {
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

  app.views.BobinasTableView = app.mixins.View.extend({

    template: _.template($("#bobinas_resultados_template").html()),

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

      lista.off('sync');
      lista.on('sync', this.addAll, this);
      
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
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.BobinaItem({
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

  views.BobinaEditView = app.mixins.View.extend({

    template: _.template($("#bobina_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
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

      var fecha_alta = this.model.get("fecha_alta");
      createdatepicker($(this.el).find("#bobina_fecha_alta"),fecha_alta);

      var fecha_baja = this.model.get("fecha_baja");
      createdatepicker($(this.el).find("#bobina_fecha_baja"),fecha_baja);

      new app.mixins.Select({
        modelClass: app.models.TipoBobina,
        url: "tipos_bobinas/",
        render: "#bobina_tipos",
        selected: self.model.get("id_tipo_bobina"),
      });

      return this;
    },

    validar: function() {
      try {
        // No hay ningun error
        $(".error").removeClass("error");
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
            "id_tipo_bobina":self.$("#bobina_tipos").val(),
          },{
          success: function(model,response) {
            location.href="app/#bobinas";
          }
        });
      }
    },
    
    limpiar : function() {
      this.model = new app.models.Bobina();
      this.render();
    },
    
  });

})(app.views, app.models);



(function ( views, models ) {

  views.CargarBobinasView = app.mixins.View.extend({

    template: _.template($("#cargar_bobinas_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .editar_bobina":"editar_bobina",
      "click .eliminar_bobina":function(e){
        $(e.currentTarget).parents("tr").remove();
        this.calcular_totales();
      },
      "keydown #cargar_bobina_tipos":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_numero").select();
      },
      "keydown #cargar_bobina_numero":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_ancho").select();
      },
      "keydown #cargar_bobina_ancho":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_gramaje").select();
      },
      "keydown #cargar_bobina_gramaje":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_peso").select();
      },
      "keydown #cargar_bobina_peso":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_fecha_alta").select();
      },
      "keydown #cargar_bobina_fecha_alta":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_procedencia").select();
      },
      "keydown #cargar_bobina_procedencia":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_codigo_proveedor").select();
      },
      "keydown #cargar_bobina_codigo_proveedor":function(e) {
        if (e.which == 13) this.$("#cargar_bobina_observaciones").select();
      },
      "keydown #cargar_bobina_observaciones":function(e) {
        if (e.which == 13) this.agregar_bobina();
      },
    },

    initialize: function(options) {
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var edicion = false;
      $(this.el).html(this.template());

      createdatepicker($(this.el).find("#cargar_bobina_fecha_alta"),new Date());

      new app.mixins.Select({
        modelClass: app.models.TipoBobina,
        url: "tipos_bobinas/",
        render: "#cargar_bobina_tipos",
      });

      this.$("#cargar_bobina_tipos").focus();

      return this;
    },

    agregar_bobina: function() {
      // Controlamos los valores
      var fecha_alta = $("#cargar_bobina_fecha_alta").val();
      if (isEmpty(fecha_alta)) {
        alert("Por favor ingrese una fecha");
        $("#cargar_bobina_fecha_alta").focus();
        return;
      }
      var id_tipo_bobina = $("#cargar_bobina_tipos").val();
      var tipo_bobina = $("#cargar_bobina_tipos option:selected").text();
      var numero = parseInt($("#cargar_bobina_numero").val());
      if (isNaN(numero)) numero = 0;
      var ancho = $("#cargar_bobina_ancho").val();
      var gramaje = $("#cargar_bobina_gramaje").val();
      var peso = $("#cargar_bobina_peso").val();
      var fecha_alta = $("#cargar_bobina_fecha_alta").val();
      var procedencia = $("#cargar_bobina_procedencia").val();
      var codigo_proveedor = $("#cargar_bobina_codigo_proveedor").val();
      var observaciones = $("#cargar_bobina_observaciones").val();

      var tr = "<tr>";
      tr+="<td class='id_tipo_bobina editar_bobina dn'>"+id_tipo_bobina+"</td>";
      tr+="<td class='tipo_bobina editar_bobina'><span class='text-info'>"+tipo_bobina+"</td>";
      tr+="<td class='numero editar_bobina'>"+numero+"</td>";
      tr+="<td class='ancho editar_bobina'>"+ancho+"</td>";
      tr+="<td class='gramaje editar_bobina'>"+gramaje+"</td>";
      tr+="<td class='peso editar_bobina'>"+peso+"</td>";
      tr+="<td class='codigo_proveedor editar_bobina'>"+codigo_proveedor+"</td>";
      tr+="<td class='tar'>";
      tr+="<input type='hidden' class='fecha_alta' value='"+fecha_alta+"'/>";
      tr+="<input type='hidden' class='procedencia' value='"+procedencia+"'/>";
      tr+="<input type='hidden' class='observaciones' value='"+observaciones+"'/>";
      tr+="<button class='btn btn-sm btn-white eliminar_bobina'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_bobina == null) {
        $("#cargar_bobinas_tabla tbody").append(tr);
      } else {
        $(this.item_bobina).replaceWith(tr);
        this.item_bobina = null;
      }
      this.$("#cargar_bobina_numero").val(numero + 1);
      this.$("#cargar_bobina_ancho").val("");
      this.$("#cargar_bobina_gramaje").val("");
      this.$("#cargar_bobina_peso").val("");
      this.$("#cargar_bobina_codigo_proveedor").val("");
      this.$("#cargar_bobina_observaciones").val("");
      this.calcular_totales();
      this.$("#cargar_bobina_tipos").focus();
    },
    
    editar_bobina: function(e) {
      this.item_bobina = $(e.currentTarget).parents("tr");
      $("#cargar_bobina_tipos").val($(this.item_bobina).find(".id_tipo_bobina").text());
      $("#cargar_bobina_numero").val($(this.item_bobina).find(".numero").text());
      $("#cargar_bobina_ancho").val($(this.item_bobina).find(".ancho").text());
      $("#cargar_bobina_gramaje").val($(this.item_bobina).find(".gramaje").text());
      $("#cargar_bobina_peso").val($(this.item_bobina).find(".peso").text());
      $("#cargar_bobina_fecha_alta").val($(this.item_bobina).find(".fecha_alta").val());
      $("#cargar_bobina_procedencia").val($(this.item_bobina).find(".procedencia").val());
      $("#cargar_bobina_codigo_proveedor").val($(this.item_bobina).find(".codigo_proveedor").text());
      $("#cargar_bobina_observaciones").val($(this.item_bobina).find(".observaciones").val());
    },

    calcular_totales: function() {
      var peso_total = 0;
      $("#cargar_bobinas_tabla .peso").each(function(i,e){
        var peso = parseFloat($(e).text());
        if (isNaN(peso)) peso = 0;
        peso_total += peso;
      });
      this.$("#cargar_bobinas_peso_total").html(Number(peso_total).toFixed(2));
    },
        
    guardar: function() {
      var self = this;
      var bobinas = new Array();
      $("#cargar_bobinas_tabla tbody tr").each(function(i,e){
        bobinas.push({
          "id":0,
          "id_tipo_bobina":$(e).find(".id_tipo_bobina").text(),
          "numero":$(e).find(".numero").text(),
          "ancho":$(e).find(".ancho").text(),
          "gramaje":$(e).find(".gramaje").text(),
          "peso":$(e).find(".peso").text(),
          "fecha_alta":$(e).find(".fecha_alta").val(),
          "procedencia":$(e).find(".procedencia").val(),
          "codigo_proveedor":$(e).find(".codigo_proveedor").text(),
          "observaciones":$(e).find(".observaciones").val(),
          "id_empresa":ID_EMPRESA,
        });
      });
      $.ajax({
        "url":"bobinas/function/cargar/",
        "data": "listado="+JSON.stringify(bobinas),
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 0) {
            alert("Los datos se han guardado con exito.");
            location.href="app/#bobinas";
          }
        }
      })
    },
    
  });

})(app.views, app.models);