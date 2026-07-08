// -----------
//   MODELO
// -----------

(function ( models ) {

  models.FormaEnvioConfiguracion = Backbone.Model.extend({
    urlRoot: "formas_envio_configuracion/",
    idAttribute:"id_empresa",
    defaults: {
      forma_envio: "",
      andreani_cliente: "",
      andreani_usuario: "",
      andreani_password: "",
      andreani_contrato: "",
      andreani_test: 0,
      oca_cliente: "",
      oca_password: "",
      id_empresa: 0,
      retiro_sucursal: 1,
      convenir_envio: 1,
      excepciones:[], // Excepciones a MercadoEnvios
      valores: [], // Tabla de valores de reparto propio
      uso_excepciones: 0, // Usado en GASTROBER para determinar si se aplica cuando el producto esta marcado como COORDINAR ENVIO
      costo_envio_fijo: 0,
      excepciones_fragiles: "",
    }
  });
	  
})( app.models );


// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.FormaEnvioConfiguracionEditView = app.mixins.View.extend({

		template: _.template($("#formas_envio_configuracion_edit_panel_template").html()),

		myEvents: {
			"click .guardar": "guardar",
      "change .forma_envio":function(e){
        var v = $(e.currentTarget).val();
        this.model.set({
          "forma_envio":v
        });
      },
      "keypress #excepcion_codigo_postal":function(e) {
        if (e.which == 13) this.$("#excepcion_costo_envio").select();
      },
      "click #excepcion_agregar":"agregar_excepcion",
      "keypress #excepcion_monto_desde":function(e) {
        if (e.which == 13) {
          this.agregar_excepcion();
          this.$("#excepcion_codigo_postal").focus();
        }
      },
      "keypress #excepcion_costo_envio":function(e) {
        if (e.which == 13) {
          this.$("#excepcion_monto_desde").select();
        }
      },
      "click .editar_excepcion":"editar_excepcion",

      "click .eliminar_excepcion":function(e){
        var tr = $(e.currentTarget).parents("tr");
        $(tr).remove();
      },

      "click #valor_agregar":"agregar_valor",
      "keypress #valor_zona":function(e) {
        if (e.which == 13) this.$("#valor_costo_envio").select();
      },
      "keypress #valor_monto_desde":function(e) {
        if (e.which == 13) {
          this.agregar_valor();
          this.$("#valor_zona").focus();
        }
      },
      "keypress #valor_costo_envio":function(e) {
        if (e.which == 13) {
          this.$("#valor_monto_desde").select();
        }
      },
      "click .editar_valor":"editar_valor",

      "click .eliminar_valor":function(e){
        var tr = $(e.currentTarget).parents("tr");
        $(tr).remove();
      },
		},

    agregar_excepcion: function() {
      // Controlamos los valores
      var codigo_postal = $("#excepcion_codigo_postal").val();
      if (isEmpty(codigo_postal)) {
        alert("Por favor ingrese un codigo postal");
        $("#excepcion_codigo_postal").focus();
        return;
      }
      var costo_envio = $("#excepcion_costo_envio").val();
      if (isNaN(costo_envio) || costo_envio < 0) {
        alert("Por favor ingrese un valor");
        $("#excepcion_costo_envio").focus();
        return;
      }
      var monto_desde = $("#excepcion_monto_desde").val();
      if (isNaN(monto_desde)) monto_desde = 0;
      var tr = "<tr>";
      tr+="<td>"+codigo_postal+"</td>";
      tr+="<td>"+Number(costo_envio).toFixed(2)+"</td>";
      tr+="<td>"+Number(monto_desde).toFixed(2)+"</td>";
      tr+="<td><a href='javascript:void(0)' class='btn btn-white'><i class='fa fa-pencil cp editar_excepcion'></i></a></td>";
      tr+="<td><a href='javascript:void(0)' class='btn btn-white'><i class='fa fa-times eliminar_excepcion cp'></i></a></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#excepciones_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#excepcion_codigo_postal").val("");
      $("#excepcion_costo_envio").val("");
      $("#excepcion_monto_desde").val("");
    },

    editar_excepcion: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#excepcion_codigo_postal").val($(this.item).find("td:eq(0)").text());
      $("#excepcion_costo_envio").val($(this.item).find("td:eq(1)").text());
      $("#excepcion_monto_desde").val($(this.item).find("td:eq(2)").text());
    },


    agregar_valor: function() {
      // Controlamos los valores
      var codigo_postal = $("#valor_zona").val();
      if (isEmpty(codigo_postal)) {
        alert("Por favor ingrese un valor");
        $("#valor_zona").focus();
        return;
      }
      var costo_envio = $("#valor_costo_envio").val();
      if (isNaN(costo_envio) || costo_envio < 0) {
        alert("Por favor ingrese un valor");
        $("#valor_costo_envio").focus();
        return;
      }
      var monto_desde = $("#valor_monto_desde").val();
      if (isNaN(monto_desde)) monto_desde = 0;
      var tr = "<tr>";
      tr+="<td>"+codigo_postal+"</td>";
      tr+="<td>"+Number(costo_envio).toFixed(2)+"</td>";
      tr+="<td>"+Number(monto_desde).toFixed(2)+"</td>";
      tr+="<td><a href='javascript:void(0)' class='btn btn-white'><i class='fa fa-pencil cp editar_valor'></i></a></td>";
      tr+="<td><a href='javascript:void(0)' class='btn btn-white'><i class='fa fa-times eliminar_valor cp'></i></a></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#valores_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }
      $("#valor_zona").val("");
      $("#valor_costo_envio").val("");
      $("#valor_monto_desde").val("");
    },

    editar_valor: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#valor_zona").val($(this.item).find("td:eq(0)").text());
      $("#valor_costo_envio").val($(this.item).find("td:eq(1)").text());
      $("#valor_monto_desde").val($(this.item).find("td:eq(2)").text());
    },    

    initialize: function() {
      // Si el modelo cambia, debemos renderizar devuelta el elemento
      //this.model.bind("change",this.render,this);
      this.model.bind("destroy",this.render,this);

      this.bind("ver",this.ver,this); // Mostramos el objeto
      _.bindAll(this);

      this.render();
    },

    render: function() {
    	// Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;
      var obj = { id:this.model.id };
    	// Extendemos el objeto creado con el modelo de datos
    	$.extend(obj,this.model.toJSON());
    	$(this.el).html(this.template(obj));
      return this;
    },

    validar: function() {
      var self = this;
      try {
        $(".error").removeClass("error");

        // Guardamos las excepciones a MercadoLibre
        var excepciones = new Array();
        $("#excepciones_tabla tbody tr").each(function(i,e){
          excepciones.push({
            "codigo_postal": $(e).find("td:eq(0)").html(),
            "costo_envio": $(e).find("td:eq(1)").html(),
            "monto_desde": $(e).find("td:eq(2)").html(),
            "tipo":0,
          });
        });
        this.model.set({"excepciones":excepciones});        

        // Guardamos las excepciones a MercadoLibre
        var valores = new Array();
        $("#valores_tabla tbody tr").each(function(i,e){
          valores.push({
            "codigo_postal": $(e).find("td:eq(0)").html(),
            "costo_envio": $(e).find("td:eq(1)").html(),
            "monto_desde": $(e).find("td:eq(2)").html(),
            "tipo":1,
          });
        });
        this.model.set({"valores":valores});        

        // Sin forma de envio definida
        var forma_envio = "";

        // Si esta habilitado MercadoEnvios
        if (this.$("#forma_envio_mercadoenvios").is(":checked")) forma_envio = "MERCADOENVIOS";

        // Esta explicitamente marcado como reparto propio
        if (this.$("#forma_envio_reparto").is(":checked")) forma_envio = "REPARTO";

        this.model.set({
          "forma_envio":forma_envio,
        });

        return true;
      } catch(e) {
        console.log(e);
        return false;
      }
    },
    

    guardar: function() {
      var self = this;
      if (this.validar()) {
        if (this.model.id == null) {
          this.model.set({id:0});
        }
        this.model.save({},{
          success: function(model,response) {
            if (response.error != undefined && response.error == true) {
              show("Hubo un error al guardar los datos.");
            } else {
              window.location.reload();
            }
          }
        });	
      }
		},
		
    limpiar : function() {
      this.model = new app.models.FormaEnvioConfiguracion();
      this.render();
    },
		
	});

})(app.views, app.models);