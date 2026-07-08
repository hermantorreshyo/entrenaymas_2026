(function (collections, model, paginator) {
  collections.ArticulosVentas = paginator.requestPager.extend({
    model: model,
    paginator_ui: {
      perPage: 100,
      order_by: 'cantidad',
      order: 'desc',
    },
    paginator_core: {
      url: "estadisticas/function/articulos_vendidos/",
    },
  });
})( app.collections, app.models.Articulo, Backbone.Paginator);

(function ( app ) {

  app.views.ArticulosVentas = app.mixins.View.extend({

    template: _.template($("#articulos_ventas_template").html()),
      
    myEvents: {
			"click .buscar":"buscar",
      "click .exportar":"exportar",
      "click #articulos_ventas_ver_filtros_link":function(){
        if (this.$("#articulos_ventas_ver_filtros").is(":visible")) {
          this.$("#articulos_ventas_ver_filtros").slideUp();
          this.$("#articulos_ventas_ver_filtros_link .link").html("Ver filtros");
        } else {
          this.$("#articulos_ventas_ver_filtros").slideDown();
          this.$("#articulos_ventas_ver_filtros_link .link").html("Ocultar filtros");          
        }
      },
      "click #articulos_ventas_buscar_proveedores": "abrir_busqueda_proveedor",
      "keypress #articulos_ventas_proveedores":function(e){
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

      var desde = moment().subtract(1, 'month').toDate();
      createdatepicker($(this.el).find("#articulos_ventas_desde"),desde);
      createdatepicker($(this.el).find("#articulos_ventas_hasta"),new Date());

      createdatepicker($(this.el).find("#articulos_ventas_desde_2"));
      createdatepicker($(this.el).find("#articulos_ventas_hasta_2"));
      
      if (control.check("vendedores")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Vendedor,
          url: "vendedores/",
          render: "#articulos_ventas_vendedores",
          firstOptions:["<option value='0'>Vendedor</option>"],
          onComplete: function() {
            crear_select2("articulos_ventas_vendedores");
          }
        });
      }

      if (control.check("rubros")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Rubro,
          url: "rubros/function/get_select/",
          render: "#articulos_ventas_rubros",
          firstOptions: ["<option value='0'>Rubro</option>"],
          fields: ["id_padre"],
          onComplete: function() {
            crear_select2("articulos_ventas_rubros");
          }
        });
      }

      if (control.check("departamentos_comerciales")>0) {
        new app.mixins.Select({
          modelClass: app.models.DepartamentoComercial,
          url: "departamentos_comerciales/",
          render: "#articulos_ventas_departamentos_comerciales",
          firstOptions: ["<option value='0'>Departamento</option>"],
          onComplete:function(c) {
            crear_select2("articulos_ventas_departamentos_comerciales");
          }
        });
      }      

      if (control.check("marcas")>0) { 
        new app.mixins.Select({
          modelClass: app.models.Marca,
          url: "marcas/",
          render: "#articulos_ventas_marcas",
          firstOptions: ["<option value='0'>Marca</option>"],
          onComplete: function() {
            crear_select2("articulos_ventas_marcas");
          }
        });
      }

      var input = this.$("#articulos_ventas_clientes");
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

      var input = this.$("#articulos_ventas_proveedores");
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
      var codigo = this.$("#articulos_ventas_proveedores").val();
      if (isEmpty(codigo)) {
        codigo = 0;
        this.$("#articulos_ventas_proveedores").val(codigo);
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
              self.$("#articulos_ventas_proveedores").select();
              self.$("#articulos_ventas_proveedores").focus();
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
      $("#articulos_ventas_tabla tbody tr").each(function(i,e){
        array.push({
          "codigo":$(e).find("td:eq(0)").html(),
          "ean":$(e).find("td:eq(1)").html().replaceAll('<br>'," | "),
          "codigo_prov":$(e).find("td:eq(2)").html(),
          "nombre":$(e).find("td:eq(3) span").text(),
          "cantidad":$(e).find("td:eq(4)").html(),
          "devolucion":$(e).find("td:eq(5)").html(),
          "bonificado":$(e).find("td:eq(6)").html(),
          "cmv":$(e).find("td:eq(7)").html(),
          "venta":$(e).find("td:eq(8)").html(),
          "ganancia":$(e).find("td:eq(9)").html(),
          "prov":$(e).find("td:eq(10)").html(),
          "stock":$(e).find("td:eq(11)").html(),
          "dias_stock":$(e).find("td:eq(12)").html(),
        });
      });
      var header = new Array("Codigo","EAN","Prov.","Nombre","Cantidad","Devolucion","Bonificado","CMV","Venta","Ganancia","Prov.","Stock","Dias Stock");
      this.exportar_excel({
        "filename":"estadisticas",
        "title":"Estadisticas de ventas",
        "data":array,
        "header":header,
      });
    },

    seleccionar_cliente: function(r) {
      var self = this;
      self.cliente = r; // Seteamos el cliente
      self.$("#articulos_ventas_clientes").val(self.cliente.get("nombre"));
      setTimeout(function(){
        self.$('#articulos_ventas_clientes').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },

    seleccionar_proveedor: function(r) {
      var self = this;
      self.proveedor = r; // Seteamos el proveedor
      self.$("#articulos_ventas_proveedores").val(self.proveedor.get("nombre"));
      setTimeout(function(){
        self.$('#articulos_ventas_proveedores').trigger(jQuery.Event('keyup', {which: 27}));
      },500);
    },
    
    buscar: function() {
      var self = this;

      var desde = $("#articulos_ventas_desde").val();
      if (isEmpty(desde)) { show("Por favor seleccione una fecha"); return; }
      desde = desde.replace(/\//g,"-");
			
      var hasta = $("#articulos_ventas_hasta").val();
      if (isEmpty(hasta)) { show("Por favor seleccione una fecha"); return; }
      hasta = hasta.replace(/\//g,"-");
			
			var id_vendedor = this.$("#articulos_ventas_vendedores").val();
      var id_sucursal = this.$("#articulos_ventas_sucursales").val();
      var id_rubro = this.$("#articulos_ventas_rubros").val();
      var id_departamento = ((this.$("#articulos_ventas_departamentos_comerciales").length > 0) ? this.$("#articulos_ventas_departamentos_comerciales").val() : 0);
      var reparto = ((this.$("#articulos_ventas_repartos").length > 0) ? this.$("#articulos_ventas_repartos").val() : 0);
      var id_marca = this.$("#articulos_ventas_marcas").val();
      var agrupado = this.$("#articulos_ventas_agrupado_por").val();
      var articulos = this.$("#articulos_ventas_articulos").val();
      var incluir_stock = (this.$("#articulos_ventas_incluir_stock").is(":checked") ? 1 : 0);
      articulos = articulos.replace(/\,/g,"-");

      if (isEmpty(this.$("#articulos_ventas_proveedores").val())) {
        self.proveedor = null;
      }
      var id_punto_venta = (this.$("#articulos_ventas_puntos_venta").length > 0) ? this.$("#articulos_ventas_puntos_venta").val() : 0;
      var en_oferta = (this.$("#articulos_ventas_en_oferta").is(":checked")?1:0);

      this.collection.server_api = {
        "desde":desde,
        "hasta":hasta,
        "agrupado":agrupado,
        "id_vendedor":id_vendedor,
        "id_sucursal":id_sucursal,
        "id_usuario":((SOLO_USUARIO == 1)?ID_USUARIO:0),
        "id_punto_venta":id_punto_venta,
        "id_rubro":id_rubro,
        "id_departamento":id_departamento,
        "reparto":reparto,
        "id_marca":id_marca,
        "id_cliente":(self.cliente != null) ? self.cliente.id : 0,
        "id_proveedor":(self.proveedor != null) ? self.proveedor.id : 0,
        "articulos":articulos,
        "en_oferta":en_oferta,
        "incluir_stock":incluir_stock,
        "not_in_estados":(ID_EMPRESA == 42)?"0-7":"",
      };
      this.collection.pager();
    },
    
    addAll : function () {
      $("#articulos_ventas_tabla tbody").empty();
      if (this.collection.length > 0) {
        this.collection.each(this.addOne);
      } else {
        $("#articulos_ventas_tabla tbody").append("<tr><td colspan='20'>No se encontraron resultados.</td></tr>");  
      }
      var total_final = Number(this.collection._meta.total_final).toFixed(2);
      var costo_final = Number(this.collection._meta.costo_final).toFixed(2);
      var cantidad = Number(this.collection._meta.cantidad).toFixed(2);
      var bonificado = Number(this.collection._meta.bonificado).toFixed(2);
      var total_bonificado = Number(this.collection._meta.total_bonificado).toFixed(2);
      this.$("#articulos_ventas_total_vendido").text("$ "+Number(total_final).format());
      this.$("#articulos_ventas_cmv").text("$ "+Number(costo_final).format());
      this.$("#articulos_ventas_ganancia").text("$ "+Number(total_final - costo_final).format());
      if (costo_final != 0) {
        this.$("#articulos_ventas_marcacion").text(Number(((total_final / costo_final)-1)*100).toFixed(2)+"%");
      } else {
        this.$("#articulos_ventas_marcacion").text(Number(0).toFixed(2)+"%");
      }
      this.$("#articulos_ventas_cantidad_total").text(Number(cantidad).format());
      this.$("#articulos_ventas_bonificado").text(Number(bonificado).format());
      this.$("#articulos_ventas_total_bonificado").text("$ "+Number(total_bonificado).format());

      // Si estamos buscando articulos y la fecha hasta es igual a hoy
      if (this.$("#articulos_ventas_incluir_stock").is(":checked")) {
        $(".mostrar_si_stock").show();
      } else {
        $(".mostrar_si_stock").hide();
      }
    },
    
    addOne : function ( item ) {
      var self = this;
      var view = new app.views.ArticulosVentasItemResultados({
        model: item,
        collection: self.collection,
        resultados: self
      });
      $(this.el).find(".tbody").append(view.render().el);
    },
    
  });

})(app);


(function ( app ) {
  app.views.ArticulosVentasItemResultados = app.mixins.View.extend({
    template: _.template($("#articulos_ventas_item_resultados_template").html()),
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
