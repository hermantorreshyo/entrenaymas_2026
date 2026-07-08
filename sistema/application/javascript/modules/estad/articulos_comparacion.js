(function (collections, model, paginator) {
  collections.ArticulosComparacion = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 100,
      order_by: 'cantidad',
      order: 'desc',
    },
    paginator_core: {
      url: "estadisticas/function/comparacion/",
    },
  });
})( app.collections, app.models.Articulo, Backbone.Paginator);

(function ( app ) {

  app.views.ArticulosComparacion = app.mixins.View.extend({

    template: _.template($("#articulos_comparacion_template").html()),
      
    myEvents: {
			"click .buscar":"buscar",
      "click .exportar":"exportar",
      "click #articulos_comparacion_ver_filtros_link":function(){
        if (this.$("#articulos_comparacion_ver_filtros").is(":visible")) {
          this.$("#articulos_comparacion_ver_filtros").slideUp();
          this.$("#articulos_comparacion_ver_filtros_link .link").html("Ver filtros");
        } else {
          this.$("#articulos_comparacion_ver_filtros").slideDown();
          this.$("#articulos_comparacion_ver_filtros_link .link").html("Ocultar filtros");          
        }
      },
      "click #articulos_comparacion_buscar_proveedores": "abrir_busqueda_proveedor",
      "keypress #articulos_comparacion_proveedores":function(e){
        if (e.which == 13) this.buscar_proveedor();
      },
    },
    
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      this.render();
      this.cliente = null;
      this.proveedor = null;
    },

    render: function() {

      var self = this;
      this.pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template());
      $(this.el).find(".pagination_container").html(this.pagination.el);

      createdatepicker($(this.el).find("#articulos_comparacion_desde"));
      createdatepicker($(this.el).find("#articulos_comparacion_hasta"));
      createdatepicker($(this.el).find("#articulos_comparacion_desde_2"));
      createdatepicker($(this.el).find("#articulos_comparacion_hasta_2"));
      
      if (control.check("vendedores")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Vendedor,
          url: "vendedores/",
          render: "#articulos_comparacion_vendedores",
          firstOptions:["<option value='0'>Vendedor</option>"],
          onComplete: function() {
            crear_select2("articulos_comparacion_vendedores");
          }
        });
      }

      if (control.check("rubros")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Rubro,
          url: "rubros/function/get_select/",
          render: "#articulos_comparacion_rubros",
          firstOptions: ["<option value='0'>Rubro</option>"],
          fields: ["id_padre"],
          onComplete: function() {
            crear_select2("articulos_comparacion_rubros");
          }
        });
      }

      if (control.check("marcas")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Marca,
          url: "marcas/",
          render: "#articulos_comparacion_marcas",
          firstOptions: ["<option value='0'>Marca</option>"],
          onComplete: function() {
            crear_select2("articulos_comparacion_marcas");
          }
        });
      }

      var input = this.$("#articulos_comparacion_clientes");
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":null,
        "width":"300px",
        "onSelect":function(item){
          var cliente = new app.models.Cliente({"id":item.id});
          cliente.fetch({
            "success":function(){
              self.seleccionar_cliente(cliente);
            },
          });
        }
      });

      var input = this.$("#articulos_comparacion_proveedores");
      $(input).customcomplete({
        "url":"proveedores/function/get_by_nombre/",
        "form":null,
        "width":"300px",
        "onSelect":function(item){
          var proveedor = new app.models.Proveedor({"id":item.id});
          proveedor.fetch({
            "success":function(){
              self.seleccionar_proveedor(proveedor);
            },
          });
        }
      });

    },

    buscar_proveedor : function() {
      var self = this;
      var codigo = this.$("#articulos_comparacion_proveedores").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#articulos_comparacion_proveedores").val(codigo);
      }
      // Buscamos el cliente por al codigo (EL CODIGO DEBE SER SOLO NUMERICO)
      codigo = parseInt(codigo);
      if (codigo == 0) return;
      if (!isNaN(codigo)) {
        $.ajax({
          "url":"proveedores/function/get_by_codigo/",
          "data":{
            "codigo":codigo,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.length == 0) {
              show("No existe un proveedor con el codigo: '"+codigo+"'");
              self.$("#articulos_comparacion_proveedores").select();
              self.$("#articulos_comparacion_proveedores").focus();
              return;
            }
            var proveedor = new app.models.Proveedor(r);
            self.seleccionar_proveedor(proveedor);
          }
        });
      }
    },

    abrir_busqueda_proveedor : function() {
      var self = this;
      var proveedores = new app.collections.Proveedores();
      var view = new app.views.ProveedoresTableView({
        collection: proveedores,
        habilitar_seleccion: true,
        permiso: 1
      });
      crearLightboxHTML({
        "html":view.el,
        "width":800,
        "height":350,
        "callback":function() {
          self.seleccionar_proveedor(window.proveedor_seleccionado);
        }
      });
      $(".basic_search").select();
    },    

    exportar: function() {
      var array = new Array();
      var header = new Array();
      $("#articulos_comparacion_tabla thead .titulo-tabla").each(function(i,e){
        var t = $(e).text();
        if (!isEmpty(t)) header.push(t);
      });

      $("#articulos_comparacion_tabla tbody tr").each(function(i,e){
        var a = {};
        $(e).find("td").each(function(ii,ee){
          a[ii] = $(ee).text();
        });
        array.push(a);
      });
      this.exportar_excel({
        "filename":"comparacion",
        "title":"Comparacion de Ventas",
        "data":array,
        "header":header,
      });
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      self.$("#articulos_comparacion_clientes").val(self.cliente.get("nombre"));
      setTimeout(function(){
        self.$('#articulos_comparacion_clientes').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.$("#articulos_comparacion_proveedores").val(self.proveedor.get("nombre"));
      setTimeout(function(){
        self.$('#articulos_comparacion_proveedores').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },
    
    buscar: function() {
      var self = this;

      var desde = $("#articulos_comparacion_desde").val();
      if (isEmpty(desde)) { show("Por favor seleccione una fecha"); return; }
      desde = desde.replace(/\//g,"-");
			
      var hasta = $("#articulos_comparacion_hasta").val();
      if (isEmpty(hasta)) { show("Por favor seleccione una fecha"); return; }
      hasta = hasta.replace(/\//g,"-");

      var desde_2 = $("#articulos_comparacion_desde_2").val();
      if (isEmpty(desde_2)) { show("Por favor seleccione una fecha"); return; }
      desde_2 = desde_2.replace(/\//g,"-");
      
      var hasta_2 = $("#articulos_comparacion_hasta_2").val();
      if (isEmpty(hasta_2)) { show("Por favor seleccione una fecha"); return; }
      hasta_2 = hasta_2.replace(/\//g,"-");
			
			var id_vendedor = this.$("#articulos_comparacion_vendedores").val();
      var id_sucursal = this.$("#articulos_comparacion_sucursales").val();
      var id_rubro = this.$("#articulos_comparacion_rubros").val();
      var id_marca = this.$("#articulos_comparacion_marcas").val();
      var agrupado = this.$("#articulos_comparacion_agrupado_por").val();
      var articulos = this.$("#articulos_comparacion_articulos").val();
      articulos = articulos.replace(/\,/g,"-");

      if (isEmpty(this.$("#articulos_comparacion_proveedores").val())) {
        self.proveedor = null;
      }
      var id_punto_venta = (this.$("#articulos_comparacion_puntos_venta").length > 0) ? this.$("#articulos_comparacion_puntos_venta").val() : 0;
      var filtrar_en_cero = (this.$("#articulos_comparacion_filtrar_en_cero").is(":checked")?1:0);

      this.collection.server_api = {
        "desde":desde,
        "hasta":hasta,
        "desde_2":desde_2,
        "hasta_2":hasta_2,
        "agrupado":agrupado,
        "id_vendedor":id_vendedor,
        "id_sucursal":id_sucursal,
        "id_punto_venta":id_punto_venta,
        "id_rubro":id_rubro,
        "id_marca":id_marca,
        "id_cliente":(self.cliente != null) ? self.cliente.id : 0,
        "id_proveedor":(self.proveedor != null) ? self.proveedor.id : 0,
        "articulos":articulos,
        "filtrar_en_cero":filtrar_en_cero,
        "not_in_estados":(ID_EMPRESA == 42)?"0-7":"",
      };
      workspace.esperar("Por favor espere..");
      this.collection.pager();
    },
    
    addAll : function () {
      $("#articulos_comparacion_tabla tbody").empty();
      if (this.collection.length > 0) {
        this.collection.each(this.addOne);
      } else {
        $("#articulos_comparacion_tabla tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");  
      }
      this.$("#articulos_comparacion_cantidad_1").html(Number(this.collection._meta.cantidad_1).format(2));
      this.$("#articulos_comparacion_devolucion_1").html(Number(this.collection._meta.devolucion_1).format(2));
      this.$("#articulos_comparacion_bonificado_1").html(Number(this.collection._meta.bonificado_1).format(2));
      this.$("#articulos_comparacion_costo_final_1").html(Number(this.collection._meta.costo_final_1).format(2));
      this.$("#articulos_comparacion_total_final_1").html(Number(this.collection._meta.total_final_1).format(2));
      this.$("#articulos_comparacion_cantidad_2").html(Number(this.collection._meta.cantidad_2).format(2));
      this.$("#articulos_comparacion_devolucion_2").html(Number(this.collection._meta.devolucion_2).format(2));
      this.$("#articulos_comparacion_bonificado_2").html(Number(this.collection._meta.bonificado_2).format(2));
      this.$("#articulos_comparacion_costo_final_2").html(Number(this.collection._meta.costo_final_2).format(2));
      this.$("#articulos_comparacion_total_final_2").html(Number(this.collection._meta.total_final_2).format(2));
      this.$("#articulos_comparacion_variacion_cantidad_2").html("<span class='"+((this.collection._meta.variacion_cantidad_2 > 0)?'text-success':(this.collection._meta.variacion_cantidad_2 < 0 ? 'text-danger' : ''))+"'>"+Number(this.collection._meta.variacion_cantidad_2).format(2)+"</span>");
      this.$("#articulos_comparacion_variacion_total_final_2").html("<span class='"+((this.collection._meta.variacion_total_final_2 > 0)?'text-success':(this.collection._meta.variacion_total_final_2 < 0 ? 'text-danger' : ''))+"'>"+Number(this.collection._meta.variacion_total_final_2).format(2)+"</span>");
      $(".modal:last").trigger("click");
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ArticulosComparacionItemResultados({
        model: item,
        collection: self.collection,
        resultados: self
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
    
  });

})(app);


(function ( app ) {
  app.views.ArticulosComparacionItemResultados = app.mixins.View.extend({
    template: _.template($("#articulos_comparacion_item_resultados_template").html()),
    tagName: "tr",
    initialize: function() {
      var self = this;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
