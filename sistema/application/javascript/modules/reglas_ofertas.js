(function ( models ) {

  models.ReglaOferta = Backbone.Model.extend({
    urlRoot: "reglas_ofertas/",
    defaults: {
      id_empresa: ID_EMPRESA,
      sucursales: [],
      articulos: [],
      nombre: "",
      desde: "",
      hasta: "",
      descuento_porcentaje: 0,
      descuento_fijo: 0,
      activo: 1,
      cantidad_minima: 0,
      semana: "",
      accion: "",
      hora_desde_1: "",
      hora_desde_2: "",
      hora_hasta_1: "",
      hora_hasta_2: "",
      id_usuario: 0,
      cantidad_minima_pesos: 0,
      codigo_especial: "",
      codigo_limite_maximo: 0,
      codigo_cantidad_veces: 0,
      id_etiqueta: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.ReglasOfertas = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "reglas_ofertas/function/ver/",
    }
  });
})( app.collections, app.models.ReglaOferta, Backbone.Paginator);


(function ( app ) {
  app.views.ReglaOfertaItem = app.mixins.View.extend({
    tagName: "tr",
    template: _.template($('#reglas_ofertas_item').html()),
    myEvents: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .activo":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        var activo = this.model.get("activo");
        activo = (activo == 1)?0:1;
        self.model.set({"activo":activo});
        this.change_property({
          "table":"reglas_ofertas",
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
      if (MEGASHOP == 1) {
        location.href="app/#regla_oferta/"+this.model.id;  
      } else {
        location.href="app/#regla_oferta_2/"+this.model.id;
      }
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
  });

})( app );



(function ( app ) {

  app.views.ReglasOfertasTableView = app.mixins.View.extend({

   template: _.template($("#reglas_ofertas_panel_template").html()),

   initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var self = this;
      this.options = options;
      this.permiso = this.options.permiso;

      var pagination = new app.mixins.PaginationView({
        collection: self.collection
      });

      var search = new app.mixins.SearchView({
        collection: self.collection
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      $(this.el).find(".pagination_container").html(pagination.el);
      $(this.el).find(".search_container").html(search.el);

      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;

      this.collection.server_api = {
        "filter":this.filter,
      };            
      this.collection.goTo(this.pagina);
    },

    addAll : function () {
      $(this.el).find("#reglas_ofertas_table tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.ReglaOfertaItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("#reglas_ofertas_table tbody").append(view.render().el);
    }

  });
})(app);


(function ( views, models ) {

  views.ReglaOfertaEditView = app.mixins.View.extend({

    template: _.template($("#reglas_ofertas_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",
      "click #reglas_ofertas_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #reglas_ofertas_agregar_item": "agregar_item",
      "keypress #reglas_ofertas_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #reglas_ofertas_item_orden":function(e) {
        if (e.which == 13) { this.$("#reglas_ofertas_cantidad_minima").select(); }
      },
      "keypress #reglas_ofertas_cantidad_minima":function(e) {
        if (e.which == 13) this.agregar_item();
      },
      "click .eliminar_articulo":function(e){
        var id_articulo = $(e.currentTarget).data("id_articulo");
        var orden = $(e.currentTarget).data("orden");
        var salida = new Array();
        for (var i = 0; i < this.model.get("articulos").length; i++) {
          var array = this.model.get("articulos")[i];
          var s = _.filter(array,function(a){
            return ((a.id_articulo == id_articulo && a.orden == orden)?false:true);
          });
          if (s.length > 0) salida.push(s);
        }
        this.model.set({"articulos":salida});
        console.log(salida);
        $(e.currentTarget).parents("tr").remove();
      },
      "change .descuento_sucursal":function(e) {
        var elem = $(e.currentTarget).parents(".row_sucursal");
        var precio_total = parseFloat($(elem).find(".precio_total").val());
        var descuento_sucursal = parseFloat($(elem).find(".descuento_sucursal").val());
        var oferta_total = Number(precio_total - descuento_sucursal).toFixed(2);
        $(elem).find(".oferta_total").val(oferta_total);
      },
      "change .oferta_total":function(e) {
        var elem = $(e.currentTarget).parents(".row_sucursal");
        var precio_total = parseFloat($(elem).find(".precio_total").val());
        var oferta_total = parseFloat($(elem).find(".oferta_total").val());
        var descuento_sucursal = Number(precio_total - oferta_total).toFixed(2);
        $(elem).find(".descuento_sucursal").val(descuento_sucursal);        
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
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var desde = this.model.get("desde");
      if (isEmpty(desde)) desde = moment().startOf('month').toDate();
      createtimepicker(this.$("#reglas_ofertas_desde"),desde);
      
      var hasta = this.model.get("hasta");
      if (isEmpty(hasta)) hasta = moment().endOf('month').toDate();
      createtimepicker(this.$("#reglas_ofertas_hasta"),hasta);    

      this.render_tabla_articulos();
      
      return this;
    },

    ver_buscar_articulo : function() {
      var self = this;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: new app.collections.Articulos(),
        habilitar_seleccion: true,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            self.$("#reglas_ofertas_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#reglas_ofertas_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    buscar_articulo : function() {
      var self = this;
      var codigo = $("#reglas_ofertas_codigo_articulo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      $.ajax({
        "url":"articulos/function/get_by_codigo/"+codigo,
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            var a = new app.models.Articulo(r.articulo);
            self.seleccionar_articulo(a);
          }
        }
      });
    },

    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      this.$("#reglas_ofertas_item_orden").select();
    },

    mostrar_articulo : function() {
      this.$("#reglas_ofertas_item_nombre").val(this.articulo.get("nombre"));
    },

    agregar_item : function() {
      var self = this;

      if (typeof this.articulo == "undefined") {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#reglas_ofertas_codigo_articulo").focus();
        return;
      }
      
      var cantidad = this.$("#reglas_ofertas_cantidad_minima").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) {
        alert("Por favor ingrese una cantidad minima.");
        this.$("#reglas_ofertas_cantidad_minima").select();
        return;
      }

      var orden = this.$("#reglas_ofertas_item_orden").val();
      orden = parseFloat(orden);
      if (isNaN(orden)) orden = 1;

      var articulos = this.model.get("articulos");
      articulos.push([{
        "id_articulo":self.articulo.id,
        "minimo":cantidad,
        "orden":orden,
        "codigo":self.articulo.get("codigo"),
        "nombre":self.articulo.get("nombre"),
      }]);
      this.model.set({"articulos":articulos});
      this.render_tabla_articulos();
      this.limpiar_item();
    },

    render_tabla_articulos: function() {
      this.$("#reglas_ofertas_tabla_items tbody").empty();
      var articulos = this.model.get("articulos");
      for (var i = 0; i < articulos.length; i++) {
        var articulo = articulos[i];
        if (jQuery.isArray(articulo)) {
          for (var j = 0; j < articulo.length; j++) {
            var art = articulo[j];
            var tr = "<tr>";
            tr+="<td><span class='codigo editar'>"+art.codigo+"</span></td>";
            tr+="<td><span class='text-info nombre editar'>"+art.nombre+"</span></td>";
            tr+="<td><span class='orden editar'>"+art.orden+"</span></td>";
            tr+="<td><span class='cantidad editar'>"+art.minimo+"</span></td>";
            tr+='<td class="tar">';
            tr+='<button data-id_articulo="'+art.id_articulo+'" data-orden="'+art.orden+'" class="btn btn-sm btn-white eliminar_articulo"><i class="fa fa-trash"></i></button>';
            tr+='</td>';
            tr+="</tr>";
            this.$("#reglas_ofertas_tabla_items tbody").append(tr);
          }
        }
      }
    },

    limpiar_item: function() {
      this.$("#reglas_ofertas_cantidad_minima").val("");
      this.$("#reglas_ofertas_codigo_articulo").val("");
      this.$("#reglas_ofertas_item_nombre").val("");
      this.$("#reglas_ofertas_item_orden").val("");
      this.$("#reglas_ofertas_codigo_articulo").select();
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("reglas_ofertas_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        // Guardamos las sucursales
        var sucursales = new Array();
        if (this.$("#reglas_ofertas_sucursales").length > 0) {
          this.$(".check_sucursal").each(function(i,e){
            if ($(e).is(":checked")) {
              var desc = $(e).parents(".row_sucursal").find(".descuento_sucursal").val();
              sucursales.push({
                "id_sucursal":$(e).val(),
                "descuento_fijo":desc
              })
            }
          });
        }
        this.model.set({
          "sucursales":sucursales,
        });

        // Cambiamos el array de articulos para que sea mas facil guardarlo
        var art2 = new Array();
        var articulos = this.model.get("articulos");
        for (var i = 0; i < articulos.length; i++) {
          var articulo = articulos[i];
          if (jQuery.isArray(articulo)) {
            for (var j = 0; j < articulo.length; j++) {
              var art = articulo[j];
              art2.push(art);
            }
          }
        }
        this.model.set({"articulos":art2});

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
        },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.ReglaOferta();
      this.render();
    },

  });

})(app.views, app.models);



(function ( views, models ) {

  views.ReglaOfertaSinArticulosEditView = app.mixins.View.extend({

    template: _.template($("#reglas_ofertas_sin_articulos_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click #agregar_item": "agregar_item",
      "click #reglas_ofertas_sin_articulo_buscar_articulo":"ver_buscar_articulo",
      "click #reglas_ofertas_sin_articulo_agregar_item": "agregar_item",
      "keypress #reglas_ofertas_sin_articulos_item_codigo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #reglas_ofertas_sin_articulos_item_orden":function(e) {
        if (e.which == 13) { this.$("#reglas_ofertas_sin_articulos_item_cantidad_minima").select(); }
      },
      "keypress #reglas_ofertas_sin_articulos_item_cantidad_minima":function(e) {
        if (e.which == 13) this.$("#reglas_ofertas_sin_articulos_item_descuento").select();
      },      
      "keypress #reglas_ofertas_sin_articulos_item_descuento":function(e) {
        if (e.which == 13) this.agregar_item();
      },  
      "click .eliminar_articulo":function(e){
        var id_articulo = $(e.currentTarget).data("id_articulo");
        var orden = $(e.currentTarget).data("orden");
        var salida = new Array();
        for (var i = 0; i < this.model.get("articulos").length; i++) {
          var array = this.model.get("articulos")[i];
          var s = _.filter(array,function(a){
            return ((a.id_articulo == id_articulo && a.orden == orden)?false:true);
          });
          if (s.length > 0) salida.push(s);
        }
        this.model.set({"articulos":salida});
        console.log(salida);
        $(e.currentTarget).parents("tr").remove();
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
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var desde = this.model.get("desde");
      if (isEmpty(desde)) desde = moment().startOf('month').toDate();
      createtimepicker(this.$("#reglas_ofertas_sin_articulos_desde"),desde);
      
      var hasta = this.model.get("hasta");
      if (isEmpty(hasta)) hasta = moment().endOf('month').toDate();
      createtimepicker(this.$("#reglas_ofertas_sin_articulos_hasta"),hasta);    

      var semana = this.model.get("semana");
      if (semana.indexOf("L") != -1) this.$("#reglas_ofertas_sin_articulos_lunes").prop("checked",true);
      if (semana.indexOf("M") != -1) this.$("#reglas_ofertas_sin_articulos_martes").prop("checked",true);
      if (semana.indexOf("X") != -1) this.$("#reglas_ofertas_sin_articulos_miercoles").prop("checked",true);
      if (semana.indexOf("J") != -1) this.$("#reglas_ofertas_sin_articulos_jueves").prop("checked",true);
      if (semana.indexOf("V") != -1) this.$("#reglas_ofertas_sin_articulos_viernes").prop("checked",true);
      if (semana.indexOf("S") != -1) this.$("#reglas_ofertas_sin_articulos_sabado").prop("checked",true);
      if (semana.indexOf("D") != -1) this.$("#reglas_ofertas_sin_articulos_domingo").prop("checked",true);

      this.$("#reglas_ofertas_sin_articulos_hora_desde_1").mask("99:99");
      this.$("#reglas_ofertas_sin_articulos_hora_desde_2").mask("99:99");
      this.$("#reglas_ofertas_sin_articulos_hora_hasta_1").mask("99:99");
      this.$("#reglas_ofertas_sin_articulos_hora_hasta_2").mask("99:99");

      this.render_tabla_articulos();

      return this;
    },

    ver_buscar_articulo : function() {
      var self = this;
      var buscar = new app.views.ArticulosBuscarTableView({
        collection: new app.collections.Articulos(),
        habilitar_seleccion: true,
      });
      delete window.codigo_articulo_seleccionado;
      var d = $("<div/>").append(buscar.el);
      crearLightboxHTML({
        "html":d,
        "width":860,
        "height":500,
        "callback":function() {
          if (window.codigo_articulo_seleccionado != undefined && window.codigo_articulo_seleccionado != -1) {
            self.$("#reglas_ofertas_sin_articulos_item_codigo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#reglas_ofertas_sin_articulos_item_codigo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    buscar_articulo : function() {
      var self = this;
      var codigo = $("#reglas_ofertas_sin_articulos_item_codigo").val();
      codigo = codigo.trim();
      if (isEmpty(codigo)) { return; }
      $.ajax({
        "url":"articulos/function/get_by_codigo/"+codigo,
        "type":"post",
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
          } else {
            var a = new app.models.Articulo(r.articulo);
            self.seleccionar_articulo(a);
          }
        }
      });
    },

    seleccionar_articulo : function(r) {
      var self = this;
      self.articulo = r;
      self.mostrar_articulo();
      this.$("#reglas_ofertas_sin_articulos_item_orden").select();
    },

    mostrar_articulo : function() {
      this.$("#reglas_ofertas_sin_articulos_item_nombre").val(this.articulo.get("nombre"));
    },

    agregar_item : function() {
      var self = this;

      if (typeof this.articulo == "undefined") {
        alert("Por favor escriba o seleccione un articulo.");
        this.$("#reglas_ofertas_sin_articulos_item_codigo").focus();
        return;
      }
      
      var cantidad = this.$("#reglas_ofertas_sin_articulos_item_cantidad_minima").val();
      cantidad = parseFloat(cantidad);
      if (isNaN(cantidad)) {
        alert("Por favor ingrese una cantidad minima.");
        this.$("#reglas_ofertas_sin_articulos_item_cantidad_minima").select();
        return;
      }

      var orden = this.$("#reglas_ofertas_sin_articulos_item_orden").val();
      orden = parseFloat(orden);
      if (isNaN(orden)) orden = 1;

      var descuento = this.$("#reglas_ofertas_sin_articulos_item_descuento").val();
      descuento = parseFloat(descuento);
      if (isNaN(descuento)) descuento = 0;

      var articulos = this.model.get("articulos");
      articulos.push([{
        "id_articulo":self.articulo.id,
        "minimo":cantidad,
        "orden":orden,
        "descuento":descuento,
        "codigo":self.articulo.get("codigo"),
        "nombre":self.articulo.get("nombre"),
      }]);
      this.model.set({"articulos":articulos});
      this.render_tabla_articulos();
      this.limpiar_item();
    },

    render_tabla_articulos: function() {
      this.$("#reglas_ofertas_sin_articulos_tabla_items tbody").empty();
      var articulos = this.model.get("articulos");
      for (var i = 0; i < articulos.length; i++) {
        var articulo = articulos[i];
        if (jQuery.isArray(articulo)) {
          for (var j = 0; j < articulo.length; j++) {
            var art = articulo[j];
            var tr = "<tr>";
            tr+="<td><span class='codigo editar'>"+art.codigo+"</span></td>";
            tr+="<td><span class='text-info nombre editar'>"+art.nombre+"</span></td>";
            tr+="<td><span class='orden editar'>"+art.orden+"</span></td>";
            tr+="<td><span class='cantidad editar'>"+art.minimo+"</span></td>";
            tr+="<td><span class='descuento editar'>"+art.descuento+"</span></td>";
            tr+='<td class="tar">';
            tr+='<button data-id_articulo="'+art.id_articulo+'" data-orden="'+art.orden+'" class="btn btn-sm btn-white eliminar_articulo"><i class="fa fa-trash"></i></button>';
            tr+='</td>';
            tr+="</tr>";
            this.$("#reglas_ofertas_sin_articulos_tabla_items tbody").append(tr);
          }
        }
      }
    },

    limpiar_item: function() {
      this.$("#reglas_ofertas_sin_articulos_item_cantidad_minima").val("");
      this.$("#reglas_ofertas_sin_articulos_item_codigo").val("");
      this.$("#reglas_ofertas_sin_articulos_item_nombre").val("");
      this.$("#reglas_ofertas_sin_articulos_item_orden").val("");
      this.$("#reglas_ofertas_sin_articulos_item_descuento").val("");
      this.$("#reglas_ofertas_sin_articulos_item_codigo").select();
    },    

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("reglas_ofertas_sin_articulos_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var self = this;
        var semana = "";
        if (this.$("#reglas_ofertas_sin_articulos_lunes").is(":checked")) semana+="L";
        if (this.$("#reglas_ofertas_sin_articulos_martes").is(":checked")) semana+="M";
        if (this.$("#reglas_ofertas_sin_articulos_miercoles").is(":checked")) semana+="X";
        if (this.$("#reglas_ofertas_sin_articulos_jueves").is(":checked")) semana+="J";
        if (this.$("#reglas_ofertas_sin_articulos_viernes").is(":checked")) semana+="V";
        if (this.$("#reglas_ofertas_sin_articulos_sabado").is(":checked")) semana+="S";
        if (this.$("#reglas_ofertas_sin_articulos_domingo").is(":checked")) semana+="D";
        this.model.set({
          "semana":semana,
          "accion":self.$("#reglas_ofertas_sin_articulos_accion").val(),
          "id_usuario":self.$("#reglas_ofertas_sin_articulos_usuarios").val(),
          "hora_desde_1":self.$("#reglas_ofertas_sin_articulos_hora_desde_1").val(),
          "hora_desde_2":self.$("#reglas_ofertas_sin_articulos_hora_desde_2").val(),
          "hora_hasta_1":self.$("#reglas_ofertas_sin_articulos_hora_hasta_1").val(),
          "hora_hasta_2":self.$("#reglas_ofertas_sin_articulos_hora_hasta_2").val(),
        });

        // Cambiamos el array de articulos para que sea mas facil guardarlo
        var art2 = new Array();
        var articulos = this.model.get("articulos");
        for (var i = 0; i < articulos.length; i++) {
          var articulo = articulos[i];
          if (jQuery.isArray(articulo)) {
            for (var j = 0; j < articulo.length; j++) {
              var art = articulo[j];
              art2.push(art);
            }
          }
        }
        this.model.set({"articulos":art2});        

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
        },{
          success: function(model,response) {
            location.reload();
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.ReglaOferta();
      this.render();
    },

  });

})(app.views, app.models);