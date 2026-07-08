(function ( app ) {

  app.views.BarrasTableView = app.mixins.View.extend({

    template: _.template($("#barras_resultados_template").html()),
      
    myEvents: {
      "click .exportar":"exportar",
      "click .exportar_csv":"exportar_csv",
      "click .importar_csv":"importar",
      "change .buscar":"buscar",
      "click .buscar":"buscar",
      "click .enviar":"enviar",
      "click .barras_tipo_comprobante_check":"buscar",
      "click .nuevo":"nuevo",
    },

    nuevo: function() {
      // Mostramos para tomar el pedido
      var modelo = new app.models.PedidoMesa({
        titulo: "Barra",
        tipo: "B",
      });
      var view = new app.views.PedidoMesaEditView({
        "model":modelo
      });
      crearLightboxHTML({
        "html":view.el,
        "width":600,
        "height":140,
        "escapable":false,
      });
      $("#pedido_mesa_cliente").select();
    },
	
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.parent = (this.options.parent == undefined) ? false : this.options.parent;
			this.permiso = this.options.permiso;      
      
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
      }));
      
			// Creamos la lista de paginacion
			var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
				collection: this.collection
			});
      
      this.collection.off('sync');
      this.collection.on('sync', this.addAll, this);
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
      
      createdatepicker(this.$("#barras_desde"));
      createdatepicker(this.$("#barras_hasta"));
      
      this.buscar();
    },
    
    exportar_csv: function(obj) {
      var desde = $("#barras_desde").val();
      desde = desde.replace(/\//g,"-");
      var hasta = $("#barras_hasta").val();
      hasta = hasta.replace(/\//g,"-");
      window.open("facturas/function/exportar_csv/"+desde+"/"+hasta,"_blank");
    },
    
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"facturas"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":600,
        "height":140,
      });
    },
    
    buscar: function() {
      var self = this;
      var filtros = {};

      if (!isEmpty(this.$("#barras_listado_buscar").val())) 
        filtros.filter = this.$("#barras_listado_buscar").val();
      if (!isEmpty(this.$("#barras_listado_cliente").val())) 
        filtros.id_cliente = this.$("#barras_listado_cliente").val();
      if (!isEmpty(this.$("#barras_listado_numero").val())) 
        filtros.numero = this.$("#barras_listado_numero").val();
      
      filtros.tipo = "B";
      filtros.fecha_desde = moment().format("DD-MM-YYYY");
      filtros.fecha_hasta = moment().format("DD-MM-YYYY");
      if (SOLO_USUARIO == 1) filtros.id_usuario = ID_USUARIO; // Buscamos solo los productos de ese usuario
      filtros.id_proyecto = ID_PROYECTO;

      this.collection.server_api = filtros;
      this.collection.pager();      
    },
    
    exportar : function() {
      
      var fecha_desde = this.$("#barras_desde").val();
      if (isEmpty(fecha_desde)) {
        alert("Por favor seleccione una fecha");
        this.$("#barras_desde").focus();
        return;
      }
      var fecha_hasta = this.$("#barras_hasta").val();
      if (isEmpty(fecha_hasta)) {
        alert("Por favor seleccione una fecha");
        this.$("#barras_hasta").focus();
        return;
      }
      
      var url = "/sistema/facturas/function/exportar_excel/?";
      if (!isEmpty(this.$("#barras_listado_buscar").val()))
        url+="filter="+this.$("#barras_listado_buscar").val()+"&";
      if (!isEmpty(this.$("#barras_listado_cliente").val()))
        url+="id_cliente="+this.$("#barras_listado_cliente").val()+"&";
      if (!isEmpty(this.$("#barras_listado_numero").val()))
        url+="numero="+this.$("#barras_listado_numero").val()+"&";
        
      if (isEmpty(fecha_desde)) fecha = 0;
      else fecha_desde = fecha_desde.replace(/\//g,"-");
      if (!isEmpty(fecha_desde)) url+="desde="+fecha_desde+"&";
      
      if (isEmpty(fecha_hasta)) fecha = 0;
      else fecha_hasta = fecha_hasta.replace(/\//g,"-");
      if (!isEmpty(fecha_hasta)) url+="hasta="+fecha_hasta+"&";
      
      window.open(url,"_blank")
    },
    
    addAll : function () {
      this.$("#barras_tabla tbody tr").empty();
      this.collection.each(this.addOne);
    },
    
    addOne : function ( item ) {
      var view = new app.views.BarrasItemResultados({
        model: item,
        seleccionar: this.habilitar_seleccion,
        parent: this.parent,
      });
      this.$("#barras_tabla tbody").append(view.render().el);
    },
    
    enviar: function() {
      var checks = this.$("#barras_tabla .i-checks input[type=checkbox]:checked");
      if (checks.length == 0) {
        alert("Por favor seleccione algun elemento de la tabla.");
        return;
      }
      var links_adjuntos = new Array();
      $(checks).each(function(i,e){
        var id = $(e).val();
        var comprobante = $(e).parents("tr").find(".comprobante").html();
        links_adjuntos.push({
          tipo: TIPO_ADJUNTO_COMPROBANTE,
          id_objeto: id,
          nombre: comprobante,
        });
      });
      var email = new app.models.Consulta({
        links_adjuntos:links_adjuntos,
        asunto:"Factura Electronica",
      });
      workspace.nuevo_email(email);
    },
  });

})(app);



// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.BarrasItemResultados = app.mixins.View.extend({
    
    template: _.template($("#barras_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .data":"seleccionar",
      "click .edit":"editar",
      "click .anular":"anular",
      "click .delete":"borrar",
      "click .imprimir":function() {
        workspace.imprimir_comanda(this.model.id);
      },
      "click .verificar_comprobante":function() {
        var self = this;
        $.ajax({
          "url":"facturas/function/verificar/"+self.model.id,
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) location.reload();
            alert(r.mensaje);
          }
        });
      },
      "click .convertir_factura":function() {
        var self = this;
        $.ajax({
          "url":"facturas/function/convertir_factura/",
          "type":"post",
          "data":{
            "id_punto_venta":self.model.get("id_punto_venta"),
            "id_factura":self.model.id,
          },
          "dataType":"json",
          "success":function(r) {
            if (r.error == 0) {
              self.$(".comprobante").text(r.comprobante);
              self.$(".numero").text(r.numero);
            } else alert(r.mensaje);
          }
        });
      },      
    },
    seleccionar : function(e) {
      if (this.options.seleccionar) {
        console.log(this.model);
        window.factura_seleccionada = this.model;
        $('.modal:last').modal('hide');
        //this.parent.importar(this.model);
      }
    },
    editar : function() {
      var self = this;
      var modelo = new app.models.PedidoMesa({"id":self.model.id});
      modelo.fetch({
        "success":function() {
          var view = new app.views.PedidoMesaEditView({
            "model":modelo
          });
          crearLightboxHTML({
            "html":view.el,
            "width":600,
            "height":140,
            "escapable":false,
          });
          $("#pedido_mesa_cliente").select();
        }
      });
    },
    anular: function() {
      if (confirmar("Realmente desea anular este comprobante?")) {
        // Se debe ANULAR, NO BORRAR
        $.ajax({
          "url":"facturas/function/anular/"+this.model.id,
          "dataType":"json",
          "success":function(r){
            app.views.barrasTableView.buscar();
          }
        });                      
      }
    },
    borrar : function() {
      if (confirmar("Realmente desea eliminar este comprobante?")) {
        
        // Si es un pago
        if (this.model.get("tipo") == "P") {
          var url = "recibos/function/borrar_recibo/"+this.model.id;
          $.ajax({
            "url":url,
            "dataType": "json",
            "success": function() {
              show("El comprobante ha sido eliminado exitosamente.");
              app.views.barrasTableView.buscar();
            },
            "error" : function() {
              show("Error al eliminar el comprobante.");
            }
          });
          
        // Si es un REMITO
        } else if (this.model.get("estado") == 1) {
          // Se elimina directamente
					$.ajax({
						"url":"facturas/function/delete/"+this.model.id,
						"dataType":"json",
						"success":function(r){
              app.views.barrasTableView.buscar();
						}
					});

        // Sino, es una FA, FB, NC, ND
        } else {

          $.ajax({
            "url":"facturas/function/delete/"+this.model.id,
            "dataType":"json",
            "success":function(r){
              app.views.barrasTableView.buscar();
            }
          });
          
        }
      }
    },
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.seleccionar = (this.options.seleccionar != undefined) ? this.options.seleccionar : false;
      this.parent = (this.options.parent != undefined) ? this.options.parent : false;
      _.bindAll(this);
      this.render();
    },
    render: function() {
      var obj = this.model.toJSON();
      obj.id = this.model.id;
      obj.seleccionar = this.seleccionar;
      $(this.el).html(this.template(obj));
      return this;
    },
  });
})(app);
