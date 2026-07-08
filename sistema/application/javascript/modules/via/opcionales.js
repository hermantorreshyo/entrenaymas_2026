// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Opcional = Backbone.Model.extend({
    urlRoot: "opcionales",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      nombre_en: "",
      nombre_pt: "",
      path: "",
      id_categoria: 0,
      categoria: "",
      texto: "",
      texto_en: "",
      texto_pt: "",
      precio: 0,
      precio_en: 0,
      precio_pt: 0,
      activo: 1,
      precios: [],
    },
  });

})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Opcionales = paginator.requestPager.extend({

    model: model,

    paginator_core: {
      url: "opcionales/function/buscar/"
    }
    
  });

})( app.collections, app.models.Opcional, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.OpcionalesTableView = app.mixins.View.extend({

    template: _.template($("#opcionales_resultados_template").html()),

    myEvents: {
      "change #opcionales_buscar":"buscar",
      "click .buscar":"buscar",
    },

    initialize : function (options) {

      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      // Filtros de la opcional
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;
      this.render();
      this.collection.on('sync', this.addAll, this);

      this.collection.server_api = {
        "filter":this.filter,
      };            
      this.collection.goTo(this.pagina);
    },

    render: function() {
      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);

      return this;
    },

    buscar: function() {
      this.filter = this.$("#opcionales_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
      };
      this.collection.pager();            
    },

    addAll : function () {
      $(this.el).find(".tbody").empty();
      // Mostramos u ocultamos la parte de "No tenes ningun elemento...", solo la primera vez
      if (!this.$(".seccion_vacia").is(":visible") && !this.$(".seccion_llena").is(":visible")) {
        if (this.collection.length > 0) {
          this.$(".seccion_vacia").hide();
          this.$(".seccion_llena").show();
        } else {
          this.$(".seccion_llena").hide();
          this.$(".seccion_vacia").show();
        }
      }
      // Renderizamos cada elemento del array
      if (this.collection.length > 0) this.collection.each(this.addOne);            
    },

    addOne : function ( item ) {
      var view = new app.views.OpcionalesItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },

  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.OpcionalesItemResultados = app.mixins.View.extend({

    template: _.template($("#opcionales_item_resultados_template").html()),
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
      "click .eliminar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Realmente desea eliminar el elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
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
        "table":"via_opcionales",
        "url":"opcionales/function/change_property/",
        "attribute":"activo",
        "value":activo,
        "id":self.model.id,
        "success":function(){
          self.render();
        }
      });
      return false;
    },
  },
  seleccionar: function() {
    if (this.habilitar_seleccion) {
      window.codigo_opcional_seleccionado = this.model.get("codigo");
      window.opcional_seleccionado = this.model;
      $('.modal:last').modal('hide');
    } else {
      location.href="app/#opcional/"+this.model.id;
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
    var obj = { 
      seleccionar: this.habilitar_seleccion,
      id:this.model.id,
    };
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

  app.views.OpcionalEditView = app.mixins.View.extend({

    template: _.template($("#opcional_template").html()),

    myEvents: {
      "click .guardar": "guardar",

      "click #precio_agregar":"agregar_precio",
      "click .editar_precio":"editar_precio",
      "click .eliminar_precio":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #opcional_precio_monto":function(e) {
        if (e.which == 13) this.agregar_precio();
      },

      "click #opcional_link_2":function() {
        if (typeof CKEDITOR.instances["opcional_texto_en"] == "undefined") { 
          workspace.crear_editor('opcional_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #opcional_link_3":function() {
        if (typeof CKEDITOR.instances["opcional_texto_pt"] == "undefined") {
          workspace.crear_editor('opcional_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
      "click .nueva_categoria":function(e) {
        var self = this;
        if ($(".categoria_opcional_edit_mini").length > 0) return;
        var form = new app.views.CategoriaOpcionalEditViewMini({
          "model": new app.models.CategoriaOpcional(),
          "callback":self.cargar_categorias,
        });
        var width = 350;
        var position = $(e.currentTarget).offset();
        var top = position.top + $(e.currentTarget).outerHeight();
        var container = $("<div class='customcomplete categoria_opcional_edit_mini'/>");
        $(container).css({
          "top":top+"px",
          "left":(position.left - width + $(e.currentTarget).outerWidth())+"px",
          "display":"block",
          "width":width+"px",
        });
        $(container).append("<div class='new-container'></div>");
        $(container).find(".new-container").append(form.el);
        $("body").append(container);
        $("#categorias_opcionales_mini_nombre").focus();
      },
      "click #opcional_mapa_expand_link":function() {
        var self = this;
        setTimeout(function(){
          if (self.map == undefined) self.render_map();
          google.maps.event.trigger(self.map, "resize");
          self.map.setCenter(self.coor);
        },100);
      },
    },    

    cargar_categorias: function() {
      var self = this;
      var r = workspace.crear_select(categorias_opcionales,"",self.model.get("id_categoria"));
      this.$("#opcional_categorias").html(r).select2({});
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

      createdatepicker(this.$("#opcional_precio_fecha_desde"),new Date());
      createdatepicker(this.$("#opcional_precio_fecha_hasta"),new Date());

      self.cargar_categorias();
    },

    agregar_precio: function() {
      // Controlamos los valores
      var fecha_desde = $("#opcional_precio_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor ingrese una fecha");
        $("#opcional_precio_fecha_desde").focus();
        return;
      }
      var fecha_hasta = $("#opcional_precio_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor ingrese una fecha");
        $("#opcional_precio_fecha_hasta").focus();
        return;
      }
      var edad_desde = $("#opcional_precio_edad_desde").val();
      if (isEmpty(edad_desde)) {
        alert("Por favor ingrese una edad");
        $("#opcional_precio_edad_desde").focus();
        return;
      }
      var edad_hasta = $("#opcional_precio_edad_hasta").val();
      if (isEmpty(edad_hasta)) {
        alert("Por favor ingrese una edad");
        $("#opcional_precio_edad_hasta").focus();
        return;
      }
      var id_tipo_tarifa = $("#opcional_precio_tarifas").val();
      var tarifa = $("#opcional_precio_tarifas option:selected").text();
      var moneda = $("#opcional_precio_moneda").val();
      var monto = parseFloat($("#opcional_precio_monto").val());
      var cantidad = $("#precio_cantidad").val();
      var tr = "<tr>";
      tr+="<td class='id_tipo_tarifa editar_precio dn'>"+id_tipo_tarifa+"</td>";
      tr+="<td class='tarifa editar_precio'><span class='text-info'>"+tarifa+"</td>";
      tr+="<td class='fecha_desde editar_precio'>"+fecha_desde+"</td>";
      tr+="<td class='fecha_hasta editar_precio'>"+fecha_hasta+"</td>";
      tr+="<td class='edad_desde editar_precio'>"+edad_desde+"</td>";
      tr+="<td class='edad_hasta editar_precio'>"+edad_hasta+"</td>";
      tr+="<td class='moneda tar pr0 editar_precio'>"+moneda+"</td>";
      tr+="<td class='precio'>"+Number(monto).toFixed(2)+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_precio'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_precio == null) {
        $("#opcional_precios_tabla tbody").append(tr);
      } else {
        $(this.item_precio).replaceWith(tr);
        this.item_precio = null;
      }
      $("#opcional_precio_edad_desde").val("");
      $("#opcional_precio_edad_hasta").val("");
      $("#opcional_precio_fecha_desde").val("");
      $("#opcional_precio_fecha_hasta").val("");
      $("#opcional_precio_monto").val("");
    },
    
    editar_precio: function(e) {
      this.item_precio = $(e.currentTarget).parents("tr");
      $("#opcional_precio_tarifas").val($(this.item_precio).find(".id_tipo_tarifa").text());
      $("#opcional_precio_fecha_desde").val($(this.item_precio).find(".fecha_desde").text());
      $("#opcional_precio_fecha_hasta").val($(this.item_precio).find(".fecha_hasta").text());
      $("#opcional_precio_edad_desde").val($(this.item_precio).find(".edad_desde").text());
      $("#opcional_precio_edad_hasta").val($(this.item_precio).find(".edad_hasta").text());
      $("#opcional_precio_moneda").val($(this.item_precio).find(".moneda").text());
      $("#opcional_precio_monto").val($(this.item_precio).find(".precio").text());
    },

    validar: function() {
      try {
        var self = this;

        validate_input("opcional_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");

        var k = 0;
        var precios = new Array();
        $("#opcional_precios_tabla tbody tr").each(function(i,e){
          var precio = $(e).find(".precio").text();
          precios.push({
            "id_tipo_tarifa": $(e).find(".id_tipo_tarifa").text(),
            "fecha_desde": $(e).find(".fecha_desde").text(),
            "fecha_hasta": $(e).find(".fecha_hasta").text(),
            "edad_desde": $(e).find(".edad_desde").text(),
            "edad_hasta": $(e).find(".edad_hasta").text(),
            "moneda": $(e).find(".moneda").text(),
            "precio": precio,
          });
          if (k==0) self.model.set({"precio":precio});
          k++;
        });
        this.model.set({"precios":precios});

        this.model.set({
          "nombre":((self.$("#opcional_nombre").length>0) ? self.$("#opcional_nombre").val() : ""),
          "nombre_en":((self.$("#opcional_nombre_en").length>0) ? self.$("#opcional_nombre_en").val() : ""),
          "nombre_pt":((self.$("#opcional_nombre_pt").length>0) ? self.$("#opcional_nombre_pt").val() : ""),
          "id_categoria":((self.$("#opcional_categorias").length>0) ? self.$("#opcional_categorias").val() : 0),
          "categoria":((self.$("#opcional_categorias").length>0) ? self.$("#opcional_categorias option:selected").text() : ""),
        });
        self.model.set({
          "path":self.$("#hidden_path").val(),
        });
        var cktext = CKEDITOR.instances['opcional_texto'].getData();
        self.model.set({"texto":cktext});
        if (typeof CKEDITOR.instances['opcional_texto_en'] != "undefined") {
          self.model.set({
            "texto_en":CKEDITOR.instances['opcional_texto_en'].getData(),
          });
        }
        if (typeof CKEDITOR.instances['opcional_texto_pt'] != "undefined") {
          self.model.set({
            "texto_pt":CKEDITOR.instances['opcional_texto_pt'].getData(),
          });
        }

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
              location.reload();
            }
          }
        });
      }      
    },

  });
})(app);