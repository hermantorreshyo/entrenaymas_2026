// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Descuento = Backbone.Model.extend({
    urlRoot: "descuentos",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_articulo: 0,
      id_sucursal: 0,
      codigo: "",
      codigo_barra: "",
      custom_10: "",
      nombre: "",
      porc_bonif: 0,
      precio_final: 0,
      desde: "",
      hasta: "",
    },
  });
    
})( app.models );

(function (collections, model, paginator) {
  collections.Descuentos = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 50,
    },
    paginator_core: {
      url: "descuentos/function/ver",
    }
  });
})( app.collections, app.models.Stock, Backbone.Paginator);


(function ( app ) {

  app.views.DescuentosResultados = app.mixins.View.extend({

    template: _.template($("#descuentos_resultados_template").html()),
      
    myEvents: {
      "click .nuevo":"nuevo",
      "click .buscar":"buscar",
      "click .exportar": "exportar",
      "click .imprimir": "imprimir",
      "keypress #descuentos_texto":function(e) {
        if (e.which == 13) { this.buscar(); }
      },
      "click .eliminar_multiple":"eliminar_multiple",
    },
  
    initialize: function() {
      var self = this;
      _.bindAll(this);
      var lista = this.collection;
    
      var pagination = new app.mixins.PaginationView({
        collection: lista,
        ver_numeros_pagina: false
      });
      
      lista.on('add', this.addOne, this);
      lista.on('all', this.addAll, this);
      
      $(this.el).html(this.template());
      this.render();
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
    },
    
    render: function() {
      createdatepicker(this.$("#descuentos_fecha"));
      this.buscar();     
    },
    
    buscar : function() {
      var self = this;
      var id_sucursal = this.$("#descuentos_almacenes").val();
      var texto = this.$("#descuentos_texto").val();
      var fecha = (!isEmpty(this.$("#descuentos_fecha").val())) ? String(encodeURI(this.$("#descuentos_fecha").val())).replace(/\//g,"-") : "";
      this.collection.server_api = {
        "filter":texto,
        "fecha":fecha,
        "id_sucursal":id_sucursal,
      }
      this.collection.pager();
    },    
      
    addAll : function () {
      this.$("#descuentos_tabla .tbody").empty();
      this.collection.each(this.addOne);
      $('[data-toggle="tooltip"]').tooltip();
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.DescuentosItemResultados({
        model: item,
        view: self,
      });
      this.$("#descuentos_tabla .tbody").append(view.render().el);
    },

    imprimir: function(id) {
      var url = "descuentos/function/imprimir/";
      url += "?order=nombre";
      if (!isEmpty(this.$("#descuentos_texto").val())) url+="&filter="+encodeURI(this.$("#descuentos_texto").val());
      if (!isEmpty(this.$("#descuentos_fecha").val())) url+="&fecha="+String(encodeURI(this.$("#descuentos_fecha").val())).replace(/\//g,"-");
      if (this.$("#descuentos_almacenes").length > 0) url+="&id_sucursal="+this.$("#descuentos_almacenes").val();
      workspace.imprimir_reporte(url);
    },

    exportar: function(obj) {
      var url = "descuentos/function/exportar/";
      url += "?order=nombre";
      if (!isEmpty(this.$("#descuentos_texto").val())) url+="&filter="+encodeURI(this.$("#descuentos_texto").val());
      if (!isEmpty(this.$("#descuentos_fecha").val())) url+="&fecha="+String(encodeURI(this.$("#descuentos_fecha").val())).replace(/\//g,"-");
      if (this.$("#descuentos_almacenes").length > 0) url+="&id_sucursal="+this.$("#descuentos_almacenes").val();
      window.open(url,"_blank");
    },

    eliminar_multiple: function() {
      var checks = this.$("#descuentos_tabla tbody .check-row:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      if (!confirm("Realmente desea eliminar estos elementos?")) return;
      var ids = new Array();
      $(checks).each(function(i,e){
        ids.push($(e).val());
      });
      if (ids.length == 0) return;
      var self = this;
      $.ajax({
        "url":"descuentos/function/eliminar_multiple/",
        "type":"post",
        "data":{
          "ids":JSON.stringify(ids),
        },
        "dataType":"json",
        "success":function(e) {
          if (e.error == 0) self.buscar();
          else alert(e.mensaje);
        },
      });
    },
    
    nuevo: function() {
      var self = this;
      var view = new app.views.DescuentosMultipleView({
        model: new app.models.AbstractModel()
      });
      crearLightboxHTML({
        "html":view.el,
        "width":900,
        "height":400,
        "escapable":false,
      });
    },
  });
})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.DescuentosItemResultados = app.mixins.View.extend({
    
    template: _.template($("#descuentos_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .edit":"editar",
      "click .delete":"eliminar",
      "click .checkbox":"seleccionar",
    },
    seleccionar : function(e) {
      if ($(e.currentTarget).is(":checked")) {
        $(this.el).addClass("seleccionado");
      } else {
        $(this.el).removeClass("seleccionado");
      }
    },
    eliminar: function() {
      var self = this;
      if (!confirm("Realmente desea eliminar el elemento?")) return;
      var id = this.model.id;
      $.ajax({
        "url":"descuentos/function/eliminar_multiple/",
        "type":"post",
        "data":{
          "ids":JSON.stringify([id]),
        },
        "dataType":"json",
        "success":function(e) {
          if (e.error == 0) self.view.buscar();
          else alert(e.mensaje);
        },
      })
    },
    editar : function() {
      var self = this;
      var descuento = new app.models.Descuento({
        "id":self.model.id
      });
      descuento.fetch({
        "success":function() {
          app.views.descuentoEditView = new app.views.DescuentoEditView({
            model: descuento
          });
          crearLightboxHTML({
            "html":app.views.descuentoEditView.el,
            "width":780,
            "height":500,
          });
        }
      });
    },
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.view = options.view;
    },
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
  });
})(app);



// -----------------------------------------
//   MODIFICACION DE STOCK
// -----------------------------------------
(function ( app ) {

  app.views.DescuentosMultipleView = app.mixins.View.extend({

    template: _.template($("#descuentos_multiple_template").html()),
      
    myEvents: {
      "click .cerrar":function() {
        if (this.$("#descuentos_tabla_items tbody tr").length > 0) {
          if (confirm("Hay movimientos cargados, desea cerrar sin guardar?")) {
            $('.modal:last').modal('hide');
          }
        } else {
          $('.modal:last').modal('hide');
        }
      },
      "click #descuentos_tabla_items .delete":"eliminar_articulo",
      "click #descuentos_tabla_items .edit":"editar_articulo",
      "click .guardar": "guardar",
      "click #descuentos_buscar_articulo":"ver_buscar_articulo",
      "click #agregar_item": "agregar_item",
      "click #descuentos_agregar_item": "agregar_item",
      "keypress #descuentos_codigo_articulo": function(e) {
        if (e.which == 13) { this.buscar_articulo(); }
      },
      "keypress #descuentos_desde":function(e) {
        if (e.which == 13) { this.$("#descuentos_item_precio_final").select(); }
      },
      "keypress #descuentos_item_precio_final":function(e) {
        if (e.which == 13) this.agregar_item();
      },
    },
    
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.articulo = null;
      $(this.el).html(this.template());
      this.render();
    },
    
    render : function() {
      createtimepicker(this.$("#descuentos_desde"),moment().startOf('month').toDate());
      createtimepicker(this.$("#descuentos_hasta"),moment().endOf('month').toDate());
      this.items = new Array();
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
            self.$("#descuentos_codigo_articulo").val(window.codigo_articulo_seleccionado);
            self.seleccionar_articulo(window.articulo_seleccionado);
          } else {
            self.$("#descuentos_codigo_articulo").focus();
          }
        }
      });
      $("#articulos_buscar").focus();
    },

    buscar_articulo : function() {
      var self = this;
      var codigo = $("#descuentos_codigo_articulo").val();
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
      this.$("#descuentos_item_precio_final").select();
    },

    mostrar_articulo : function() {
      this.$("#descuentos_id_articulo").val(this.articulo.id);
      this.$("#descuentos_item_nombre").val(this.articulo.get("nombre"));
    },

    agregar_item : function() {
      var self = this;

      var desde = this.$("#descuentos_desde").val();
      if (isEmpty(desde)) {
        alert("Por favor seleccione una fecha.");
        this.$("#descuentos_desde").focus();
        return;
      }
      var hasta = this.$("#descuentos_hasta").val();
      if (isEmpty(hasta)) {
        alert("Por favor seleccione una fecha.");
        this.$("#descuentos_hasta").focus();
        return;
      }

      var precio_final = this.$("#descuentos_item_precio_final").val();
      if (isEmpty(precio_final)) {
        alert("Por favor ingrese un nuevo precio.");
        this.$("#descuentos_item_precio_final").focus();
        return;
      }

      var id_articulo = this.$("#descuentos_id_articulo").val();
      if (isEmpty(id_articulo)) {
        alert("Por favor seleccione un articulo");
        this.$("#descuentos_codigo_articulo").focus();
        return;
      }

      var nombre = this.$("#descuentos_item_nombre").val();
      var codigo = this.$("#descuentos_codigo_articulo").val();

      var item = {
        "id_articulo":id_articulo,
        "precio_final":precio_final,
        "desde":desde,
        "hasta":hasta,
        "nombre":nombre,
        "codigo":codigo,
      };

      // Controlamos que el articulo no exista ya en la lista de items
      var art = _.filter(this.items,function(item){
        return (item.id_articulo == id_articulo);
      });
      if (art.length == 0) {
        // El articulo no se encuentra en la lista,
        // debemos agregarlo
        this.items.push(item);
        
        // Actualizamos la vista
        var tr = "<tr id='articulo_"+item.id_articulo+"'>";
        tr+= "<td>"+item.codigo+"</td>";
        tr+= "<td>"+item.nombre+"</td>";
        tr+= "<td>"+item.desde+"</td>";
        tr+= "<td>"+item.hasta+"</td>";
        tr+= "<td>"+Number(item.precio_final).toFixed(2)+"</td>";
        tr+= "<td><i title='Editar' class='fa fa-file-text-o edit text-dark'></i></td>";
        tr+= "<td><i title='Eliminar' class='glyphicon glyphicon-remove delete text-danger'></i></td>";
        tr+= "</tr>";
        this.$("#descuentos_tabla_items tbody").append(tr);
        
        // Movemos el contenedor hasta el final
        this.$("#descuentos_tabla_items").scrollTo('+=30px');
        
      } else {
        // El articulo ya se encuentra en la lista
        var a = art[0];
        a.desde = desde;
        a.hasta = hasta;
        a.precio_final = precio_final;
        var id_tr = "#articulo_"+id_articulo;
        $(id_tr).find("td:eq(2)").html(desde);
        $(id_tr).find("td:eq(3)").html(hasta);
        $(id_tr).find("td:eq(4)").html(precio_final);
      }
      console.log(this.items);
      this.limpiar_item();
    },

    eliminar_articulo : function(e) {
      var id = $(e.currentTarget).parent().parent().attr("id");
      id = id.replace("articulo_","");
      var ids = id.split("_");
      var id = ids[0];
      
      // Lo eliminamos de la lista
      $(e.currentTarget).parent().parent().remove();
      
      // Lo eliminamos del array
      var items2 = _.filter(this.items,function(item){
        return !(item.id_articulo == id);
      });
      this.items = items2;
      console.log(this.items);
    },

    editar_articulo : function(e) {
      var id = $(e.currentTarget).parent().parent().attr("id");
      id = id.replace("articulo_","");
      var ids = id.split("_");
      var id = ids[0];
      
      // Buscamos el articulo
      var a = _.find(this.items,function(item){
        return (item.id_articulo == id);
      });
      this.$("#descuentos_id_articulo").val(a.id_articulo);
      this.$("#descuentos_item_nombre").val(a.nombre);
      this.$("#descuentos_codigo_articulo").val(a.codigo);
      this.$("#descuentos_desde").val(a.desde);
      this.$("#descuentos_hasta").val(a.hasta);
      this.$("#descuentos_item_precio_final").val(a.precio_final);
      this.$("#descuentos_item_precio_final").select();
    },

    limpiar_item: function() {
      this.$("#descuentos_item_precio_final").val("");
      this.$("#descuentos_codigo_articulo").val("");
      this.$("#descuentos_id_articulo").val("");
      this.$("#descuentos_item_nombre").val("");
      this.$("#descuentos_codigo_articulo").select();
    },
    
    guardar:function() {
      var self = this;

      var sucursales = new Array();
      if (this.$("#descuentos_sucursales").length > 0) {
        this.$(".check_sucursal").each(function(i,e){
          if ($(e).is(":checked")) {
            sucursales.push({
              "id_sucursal":$(e).val(),
            })
          }
        });
      }
      if (sucursales.length == 0) {
        alert("Por favor marque al menos una sucursal.");
        return;
      }
      if (self.items.length == 0) {
        alert("Por favor ingrese al menos un articulo.");
        return;
      }

      var datos = {
        "items":self.items,
        "sucursales":sucursales,
      }

      $.ajax({
        "url":"descuentos/function/guardar_multiple/",
        "dataType":"json",
        "type":"post",
        "data":{
          "datos":JSON.stringify(datos),
        },
        "success":function(r) {
          if (r.error == 0) {
            location.reload();
          } else {
            alert(r.mensaje);
          }
        }
      });
    },
  
  });
})(app);