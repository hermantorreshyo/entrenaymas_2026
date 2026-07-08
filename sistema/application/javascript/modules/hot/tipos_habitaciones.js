// -----------
//   MODELO
// -----------

(function ( models ) {

  models.TipoHabitacion = Backbone.Model.extend({
    urlRoot: "tipos_habitaciones",
    defaults: {
      nombre: "",
      images: [],
      precios: [],
      promociones: [],
      caracteristicas: "",
      texto: "",
      video: "",
      path: "",
      capacidad_maxima: 1,
      capacidad_maxima_menores: 0,
      publica_precio: 1,
      precio: 0,
      moneda: "",
      id_empresa: ID_EMPRESA,
      activo: 1,
      compartida: 0,
      nombre_en: "",
      nombre_pt: "",
      texto_en: "",
      texto_pt: "",
      caracteristicas_en: "",
      caracteristicas_pt: "",
    },
  });
	  
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.TiposHabitaciones = paginator.requestPager.extend({

    model: model,
    
    paginator_core: {
      url: "tipos_habitaciones/"
    }
  
  });

})( app.collections, app.models.TipoHabitacion, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.TiposHabitacionesTableView = app.mixins.View.extend({

    template: _.template($("#tipos_habitaciones_resultados_template").html()),
      
    myEvents: {
      "change #tipos_habitaciones_buscar":"buscar",
      "click .buscar":"buscar",
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;

      // Filtros de la tipo_habitacion
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
      this.filter = this.$("#tipos_habitaciones_buscar").val().trim();
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
      var view = new app.views.TiposHabitacionesItemResultados({
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
  app.views.TiposHabitacionesItemResultados = app.mixins.View.extend({
    
    template: _.template($("#tipos_habitaciones_item_resultados_template").html()),
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
      "click .duplicar":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea duplicar el elemento?")) {
          $.ajax({
            "url":"tipos_habitaciones/function/duplicar/"+self.model.id,
            "dataType":"json",
            "success":function(r){
              location.reload();
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
        window.codigo_tipo_habitacion_seleccionado = this.model.get("codigo");
        window.tipo_habitacion_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#tipo_habitacion/"+this.model.id;
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

  app.views.TipoHabitacionEditView = app.mixins.View.extend({

    template: _.template($("#tipo_habitacion_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",
      "click #precio_agregar":"agregar_precio",
      "click #promocion_agregar":"promocion_agregar",
      "click .editar_precio":"editar_precio",
      "click .eliminar_precio":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "click #link_tab2":function() {
        if (typeof CKEDITOR.instances["tipo_habitacion_texto_en"] == "undefined") { 
          workspace.crear_editor('tipo_habitacion_texto_en',{
            "toolbar":"Basic"
          });
        }
      },
      "click #link_tab3":function() {
        if (typeof CKEDITOR.instances["tipo_habitacion_texto_pt"] == "undefined") {
          workspace.crear_editor('tipo_habitacion_texto_pt',{
            "toolbar":"Basic"
          });
        }
      },
      "change #tipo_habitacion_capacidad_maxima":function(e){
        var cant = $(e.currentTarget).val();
        $("#precio_cantidad").empty();
        $("#promocion_cantidad").empty();
        for(var i=1; i<=cant; i++) {
          $("#precio_cantidad").append("<option value='"+i+"'>"+i+"</option>");
          $("#promocion_cantidad").append("<option value='"+i+"'>"+i+"</option>");
        }
      }
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
      
      this.$("#tipo_habitacion_reservas_estado").select2({});
      
      this.$("#tipo_habitacion_caracteristicas select").select2({
        tags: true
      });
      this.$("#tipo_habitacion_caracteristicas_en select").select2({
        tags: true
      });
      this.$("#tipo_habitacion_caracteristicas_pt select").select2({
        tags: true
      });

      createdatepicker(this.$("#precio_fecha_desde"),new Date());
      createdatepicker(this.$("#precio_fecha_hasta"),new Date());
      createdatepicker(this.$("#promocion_fecha_desde"),new Date());
      createdatepicker(this.$("#promocion_fecha_hasta"),new Date());
      
      this.listenTo(this.model, 'change_table', self.render_tabla_fotos);
      this.render_tabla_fotos();
      this.$("#images_tabla").sortable();
      this.item = null;

      var texto_en = CKEDITOR.instances["tipo_habitacion_texto_en"];
      if (texto_en) CKEDITOR.remove(texto_en);
      var texto_pt = CKEDITOR.instances["tipo_habitacion_texto_pt"];
      if (texto_pt) CKEDITOR.remove(texto_pt);
    },
    
    render_tabla_fotos: function() {
      var images = this.model.get("images");
      this.$("#images_tabla").empty();
      if (images.length > 0) {
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
        this.$("#images_container").show();
      } else {
        this.$("#images_container").hide();
      }
    },
    
    agregar_precio: function() {
      // Controlamos los valores
      var fecha_desde = $("#precio_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor ingrese una fecha");
        $("#precio_fecha_desde").focus();
        return;
      }
      var fecha_hasta = $("#precio_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor ingrese una fecha");
        $("#precio_fecha_hasta").focus();
        return;
      }
      var monto = parseFloat($("#precio_monto").val());
      if (isNaN(monto) || monto <= 0) {
        alert("Por favor ingrese un monto");
        $("#precio_monto").focus();
        return;
      }    
      var cantidad = $("#precio_cantidad").val();
      var tr = "<tr>";
      tr+="<td>"+fecha_desde+"</td>";
      tr+="<td>"+fecha_hasta+"</td>";
      tr+="<td>"+cantidad+"</td>";
      tr+="<td>"+Number(monto).toFixed(2)+"</td>";
      tr+="<td><i class='fa fa-pencil editar_precio cp'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_precio text-danger cp'></i></td>";
      tr+="</tr>";
      
      if (this.item == null) {
        $("#precios_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      
      $("#precio_monto").val("");
    },

    promocion_agregar: function() {
      // Controlamos los valores
      var fecha_desde = $("#promocion_fecha_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor ingrese una fecha");
        $("#promocion_fecha_desde").focus();
        return;
      }
      var fecha_hasta = $("#promocion_fecha_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor ingrese una fecha");
        $("#promocion_fecha_hasta").focus();
        return;
      }
      var monto = parseFloat($("#promocion_monto").val());
      if (isNaN(monto) || monto <= 0) {
        alert("Por favor ingrese un monto");
        $("#promocion_monto").focus();
        return;
      }    
      var cantidad = $("#promocion_cantidad").val();
      var tr = "<tr>";
      tr+="<td>"+fecha_desde+"</td>";
      tr+="<td>"+fecha_hasta+"</td>";
      tr+="<td>"+cantidad+"</td>";
      tr+="<td>"+Number(monto).toFixed(2)+"</td>";
      tr+="<td><i class='fa fa-pencil editar_precio cp'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_precio text-danger cp'></i></td>";
      tr+="</tr>";
      
      if (this.item == null) {
        $("#promociones_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      
      $("#promocion_monto").val("");
    },

    editar_precio: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      if ($(this.item).parents("table").attr("id") == "precios_tabla") {
        $("#precio_fecha_desde").val($(this.item).find("td:eq(0)").text());
        $("#precio_fecha_hasta").val($(this.item).find("td:eq(1)").text());
        $("#precio_cantidad").val($(this.item).find("td:eq(2)").text());
        $("#precio_monto").val($(this.item).find("td:eq(3)").text());      
      } else {
        $("#promocion_fecha_desde").val($(this.item).find("td:eq(0)").text());
        $("#promocion_fecha_hasta").val($(this.item).find("td:eq(1)").text());
        $("#promocion_cantidad").val($(this.item).find("td:eq(2)").text());
        $("#promocion_monto").val($(this.item).find("td:eq(3)").text());
      }
    },

    validar: function() {
      try {
        var self = this;
        
        validate_input("tipo_habitacion_nombre",IS_EMPTY,"Por favor, ingrese un titulo.");
        
        // Las caracteristicas van todas juntas separadas por ;;;
        if (self.$("#tipo_habitacion_caracteristicas select").length > 0) {
          var c = self.$("#tipo_habitacion_caracteristicas select").select2("val");
          if (c != null) this.model.set({ "caracteristicas":c.join(";;;") });
        }
        if (self.$("#tipo_habitacion_caracteristicas_en select").length > 0) {
          var c = self.$("#tipo_habitacion_caracteristicas_en select").select2("val");
          if (c != null) this.model.set({ "caracteristicas_en":c.join(";;;") });
        }
        if (self.$("#tipo_habitacion_caracteristicas_pt select").length > 0) {
          var c = self.$("#tipo_habitacion_caracteristicas_pt select").select2("val");
          if (c != null) this.model.set({ "caracteristicas_pt":c.join(";;;") });
        }

        this.model.set({
          "moneda":self.$("#tipo_habitacion_monedas").val(),
          "precio":self.$("#tipo_habitacion_precio").val(),
          "path":self.$("#hidden_path").val(),
          "publica_precio":(self.$("#tipo_habitacion_publica_precio").is(":checked")?1:0),
          "compartida":(self.$("#tipo_habitacion_compartida").is(":checked")?1:0),
        });

        // Guardamos los precios
        var precios = new Array();
        $("#precios_tabla tbody tr").each(function(i,e){
          precios.push({
            "fecha_desde": $(e).find("td:eq(0)").html(),
            "fecha_hasta": $(e).find("td:eq(1)").html(),
            "cantidad": $(e).find("td:eq(2)").html(),
            "monto": $(e).find("td:eq(3)").html(),     
          });
        });
        this.model.set({"precios":precios});
        
        // Guardamos las promociones
        var promociones = new Array();
        $("#promociones_tabla tbody tr").each(function(i,e){
          promociones.push({
            "fecha_desde": $(e).find("td:eq(0)").html(),
            "fecha_hasta": $(e).find("td:eq(1)").html(),
            "cantidad": $(e).find("td:eq(2)").html(),
            "monto": $(e).find("td:eq(3)").html(),     
          });
        });
        this.model.set({"promociones":promociones});
        
        // Listado de Imagenes
        var images = new Array();
        this.$("#images_tabla .list-group-item .filename").each(function(i,e){
          images.push($(e).text());
        });
        self.model.set({"images":images});

        // Texto del tipo_habitacion
        var texto = CKEDITOR.instances['tipo_habitacion_texto'].getData();

        var texto_en = "";
        if (typeof CKEDITOR.instances["tipo_habitacion_texto_en"] != "undefined") { 
          texto_en = CKEDITOR.instances['tipo_habitacion_texto_en'].getData();
        }

        var texto_pt = "";
        if (typeof CKEDITOR.instances["tipo_habitacion_texto_pt"] != "undefined") { 
          texto_pt = CKEDITOR.instances['tipo_habitacion_texto_pt'].getData();
        }

        self.model.set({
          "texto":texto,
          "texto_en":texto_en,
          "texto_pt":texto_pt,
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
              history.back();
            }
          }
        });
      }	  
    },
    	
  });
})(app);