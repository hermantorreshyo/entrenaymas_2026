(function ( app ) {

  app.views.ImportacionView = app.mixins.View.extend({

    template: _.template($("#importacion_template").html()),
      
    myEvents: {
      "click .continuar":"continuar",
      "change .select_columna":function(e) {
        if ($(e.currentTarget).find("option:selected").data("obligatorio") == "1") {
          $(e.currentTarget).parents("th").find(".check_columna").prop("checked",true);
        }
      },
      "change .check_columna":function(e) {
        $(e.currentTarget).parents("th").find(".select_columna").trigger("change");
      }
    },
		
    initialize: function(options) {
      var self = this;
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      $(this.el).html(this.template(this.model.toJSON()));
      $('[data-toggle="tooltip"]').tooltip(); 
      if (self.model.get("tabla") == "articulos" || self.model.get("tabla") == "importaciones_articulos_items") {
        self.cargar_proveedores();
      }
      createdatepicker(this.$("#importacion_fecha_stock"),new Date());
    },

    cargar_proveedores: function(id_proveedor) {
      var self = this;
      id_proveedor = (id_proveedor || 0);
      // Creamos el select
      new app.mixins.Select({
        modelClass: app.models.Proveedor,
        url: "proveedores/",
        firstOptions: ["<option value='0'>Proveedor</option>"],
        render: "#importacion_proveedores",
        selected: id_proveedor,
        onComplete:function(c) {
          crear_select2("importacion_proveedores");
        }                    
      });
    },

    continuar: function() {
      var self = this;
      var columnas = new Array();
      this.$(".select_columna").each(function(i,e){
        if (!isEmpty($(e).val())) {
          var oblig = ($(e).parents("th").find(".check_columna").is(":checked") ? 1 : 0);
          columnas.push({
            "columna":$(e).data("col"),
            "campo":$(e).val(),
            "tipo":$(e).find("option:selected").data("tipo"),
            "obligatoria":oblig,
          });
        }
      });

      // Si tenemos que ignorar la primera fila
      var ignorar_primera_fila = (this.$("#importacion_ignorar_primera_fila").is(":checked")?1:0);
      var solo_actualizar = (this.$("#importacion_solo_actualizar").is(":checked")?1:0);
      var prefijo_codigo = this.$("#importacion_prefijo_codigo").val();
      var id_proveedor = ((self.$("#importacion_proveedores").length > 0) ? self.$("#importacion_proveedores").val() : 0);
      var id_sucursal = ((self.$("#importacion_sucursales").length > 0) ? self.$("#importacion_sucursales").val() : 0);
      var moneda = ((self.$("#importacion_moneda").length > 0) ? self.$("#importacion_moneda").val() : "$");
      var fecha_stock = this.$("#importacion_fecha_stock").val();
      fecha_stock = fecha_stock.replaceAll("/","-");

      if (ID_EMPRESA == 444 && id_proveedor == 0) {
        alert("Por favor seleccione un proveedor.");
        return;
      }
      if (moneda == -1) {
        alert("Por favor seleccione una moneda.");
        return;        
      }

      workspace.esperar("Actualizando...");
      $.ajax({
        "url":"importar/function/procesar_archivo/",
        "type":"post",
        "data":{
          "id":self.model.id,
          "tabla":self.model.get("tabla"),
          "campos":columnas,
          "ignorar_primera_fila":ignorar_primera_fila,
          "solo_actualizar":solo_actualizar,
          "prefijo_codigo":prefijo_codigo,
          "id_sucursal":id_sucursal,
          "id_proveedor":id_proveedor,
          "id_usuario":ID_USUARIO,
          "fecha_stock":fecha_stock,
          "moneda":moneda,
        },
        "timeout":0,
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
            $(".modal:last").trigger('click');
          } else if (r.error == 0) {
            if (ID_EMPRESA == 444) {
              $(".modal:last").trigger('click');
              location.href="app/#importaciones_articulos";
            } else {
              location.reload();
            }
          }
        }
      });   
    },
    
  });
})(app);