// -----------
//   MODELO
// -----------

(function ( models ) {

  models.Alquiler = Backbone.Model.extend({
    urlRoot: "alquileres",
    defaults: {
      id_empresa: ID_EMPRESA,
      id_punto_venta: 0,
      contrato: "",
      fecha_inicio: "",
      fecha_fin: "",
      fecha_cancelacion_contrato: "",
      motivo_cancelacion_contrato: "",
      estado: "A", // A = Activo | R = Reservado | C = Cancelado | F = Finalizado
      tipo_facturacion: "R", // R = Recibo | F = Factura
      id_cliente: 0,
      cliente: "",
      id_propiedad: 0,
      propiedad: "",
      propiedad_codigo: "",
      propiedad_localidad: "",
      propiedad_path: "",
      incluir_expensas: 0,
      cuotas: [],
      dias_para_vencimiento: 0,
      enviar_recordatorios: 0, // TODO: Esto puede cambiar despues
      dia_vencimiento: 1,
      mes_vencimiento: "A", // A = Mes actual | P = Mes proximo
      venc_prox_cuota: "",
      expensas: [],
    },
  });
	  
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.Alquileres = paginator.requestPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: 10,
      order_by: 'fecha_inicio',
      order: 'desc',
    },
  
    paginator_core: {
      url: "alquileres/function/ver",
    }
	  
  });

})( app.collections, app.models.Alquiler, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.AlquileresTableView = app.mixins.View.extend({

    template: _.template($("#alquileres_resultados_template").html()),
      
    myEvents: {
      "change #alquileres_buscar":"buscar",
      "click .buscar":"buscar",
      "click .exportar": "exportar",
      "click .importar_csv": "importar",
      "click .exportar_csv": "exportar_csv",
      "keydown #alquileres_tabla tbody tr .radio:first":function(e) {
        // Si estamos en el primer elemento y apretamos la flechita de arriba
        if (e.which == 38) { e.preventDefault(); $("#alquileres_texto").focus(); }
      },
    },
    
		initialize : function (options) {
      
      var self = this;
			_.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
			this.permiso = this.options.permiso;

      this.id_propiedad = (typeof this.options.id_propiedad != "undefined") ? this.options.id_propiedad : 0;
      this.estado = (typeof this.options.estado != "undefined") ? this.options.estado : 0;
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;

      this.render();
      this.collection.on('sync', this.addAll, this);
      this.buscar();
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
        "active":"alquileres",
      }));
      
      // Cargamos el paginador
      this.$(".pagination_container").html(pagination.el);
      this.$("#alquileres_buscar_estados").select2();

      // AUTOCOMPLETE DE PROPIEDADES
      var input1 = this.$("#alquileres_buscar_propiedades");
      $(input1).customcomplete({
        "url":"/sistema/propiedades/function/ver/",
        "form":null, // No quiero que se creen nuevos productos
        "info":"localidad",
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#alquileres_buscar_propiedades").val(item.label);
          self.$("#alquileres_buscar_id_propiedad").val(item.id);
        }
      });

      return this;
    },
    
    buscar: function() {
      var self = this;
      this.estado = this.$("#alquileres_buscar_estados").select2("val");
      var prop = self.$("#alquileres_buscar_propiedades").val().trim();
      if (isEmpty(prop)) this.$("#alquileres_buscar_id_propiedad").val(0);
      this.id_propiedad = this.$("#alquileres_buscar_id_propiedad").val();
      this.filter = this.$("#alquileres_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
        "estado":this.estado,
        "id_propiedad":this.id_propiedad,
      };
      this.collection.pager();      
    },

    addAll : function () {
      $(this.el).find(".tbody").empty();
      if (this.collection.length > 0) this.collection.each(this.addOne);      
    },
    
    addOne : function ( item ) {
      var view = new app.views.AlquileresItemResultados({
        model: item,
        habilitar_seleccion: this.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },
        
    importar: function() {
      app.views.importar = new app.views.Importar({
        "table":"alquileres"
      });
      crearLightboxHTML({
        "html":app.views.importar.el,
        "width":450,
        "height":140,
      });
    },    
    
    exportar: function(obj) {
      // Reemplazamos el ver por el exportar
      var url = this.collection.url;
      url = url.replace("/ver/","/exportar/");
      // Los parametros de orden se envian por GET
      url += "?order="+this.collection.paginator_ui.order+"&order_by="+this.collection.paginator_ui.order_by;
      window.open(url,"_blank");
    },
    
    exportar_csv: function(obj) {
      window.open("alquileres/function/exportar_csv/","_blank");
    },
    
  });

})(app);




// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.AlquileresItemResultados = app.mixins.View.extend({
    
    template: _.template($("#alquileres_item_resultados_template").html()),
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
          this.model.destroy();	// Eliminamos el modelo
          $(this.el).remove();	// Lo eliminamos de la vista
        }
        return false;
      },
      "click .rescindir":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea rescindir el contrato con "+this.model.get("cliente")+"?")) {
          var alquiler = new app.models.Alquiler({ "id": self.model.id });
          alquiler.fetch({
            "success":function() {
              var rescindirAlquilerView = new app.views.RescindirAlquilerView({
                model: alquiler,
              });
              var d = $("<div/>").append(rescindirAlquilerView.el);
              crearLightboxHTML({
                "html":d,
                "width":400,
                "height":500,
              });
            }
          });
        }
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.alquiler_seleccionado = this.model;
        $('.modal:last').modal('hide');
      } else {
        location.href="app/#alquiler/"+this.model.id;
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

  app.views.AlquilerEditView = app.mixins.View.extend({

    template: _.template($("#alquiler_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",   

      "keypress #expensa_monto":function(e) {
        if (e.which == 13) {
          this.agregar_expensa();
          this.$("#expensa_nombre").focus();
        }
      },
      "click #expensa_agregar":"agregar_expensa",
      "click .editar_expensa":"editar_expensa",
      "click .eliminar_expensa":function(e){
        var tr = $(e.currentTarget).parents("tr");
        $(tr).remove();
      },

      // Si se cambian las fechas o el dia de vencimiento
      "change #alquiler_fecha_inicio":"change_fechas",
      "change #alquiler_fecha_fin":"change_fechas",
      "change #alquiler_dia_vencimiento":"change_fechas",

      "change .cuota":function(e) {
        // Si estamos creando un nuevo contrato
        if (this.model.id == undefined || this.model.id == 0) {
          var valor = $(e.currentTarget).val();
          // Modificamos todos los que estan abajo
          $(e.currentTarget).parent().parent().nextAll("tr").each(function(index, el) {
            $(el).find(".cuota").val(valor);
          });
        }
      },

      "keydown #alquiler_fecha_inicio":function(e) {
        if (e.which == 13) $("#alquiler_fecha_fin").select();
      },
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
      
      this.$("#alquiler_tipos_estado").select2({});
      this.$("#alquiler_tipos_operacion").select2({});
      this.$("#alquiler_tipos_inmueble").select2({});
      this.$("#alquiler_usuarios").select2({});

      if (isEmpty(this.model.get("fecha_inicio"))) {
        this.model.set("fecha_inicio",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#alquiler_fecha_inicio"),this.model.get("fecha_inicio"));

      if (isEmpty(this.model.get("fecha_fin"))) {
        this.model.set("fecha_fin",moment().add(2,'years').format("DD/MM/YYYY"));
      }       
      createdatepicker(this.$("#alquiler_fecha_fin"),this.model.get("fecha_fin"));

      // AUTOCOMPLETE DE PROPIEDADES
      var input1 = this.$("#alquiler_propiedades");
      $(input1).customcomplete({
        "url":"/sistema/propiedades/function/ver/",
        "form":null, // No quiero que se creen nuevos productos
        "info":"localidad",
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#alquiler_propiedades").val(item.label);
          self.model.set({"id_propiedad":item.id});
          self.$("#alquiler_fecha_inicio").select();
        }
      });      

      // AUTOCOMPLETE DE CLIENTES
      var input = this.$("#alquiler_clientes");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });      
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
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

      self.change_fechas();
    },

    seleccionar_cliente: function(r) {
      this.model.set({"id_cliente":r.id});
      this.$("#alquiler_clientes").val(r.get("nombre"));
      this.$("#alquiler_propiedades").select();
    },

    change_fechas: function() {
      moment.locale("es",{
        months : "Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre".split("_"),
      });
      var fecha_inicio = this.$("#alquiler_fecha_inicio").val();
      var fecha_fin = this.$("#alquiler_fecha_fin").val();
      var inicio = moment(fecha_inicio,"DD/MM/YYYY");
      var fin = moment(fecha_fin,"DD/MM/YYYY");

      var numero_venc = this.$("#alquiler_dia_vencimiento").val();
      var mes_venc = "A"; //this.$("#alquiler_mes_vencimiento").val();
      var cuotas = this.model.get("cuotas");
      
      // Calcula la diferencia en meses entre la fecha de inicio y la de fin
      var meses = (fin.diff(inicio,'months'))+1;

      this.$("#cuotas_tabla tbody").empty();
      for(var i=0;i<meses;i++) {

        var icono_pagada = "";
        var monto = 0;
        
        if (typeof cuotas[i] != "undefined") {
          var cuota = cuotas[i];
          monto = cuota.monto;
          if (cuota.pagada == 1) {
            // Si la cuota esta paga, ponemos un iconito y deshabilitamos el campo
            icono_pagada = "<i class='fa fa-check text-success'></i>";
          }
        }
        
        var venc = inicio.clone();
        if (numero_venc == 31) {
          venc.endOf('month');
        } else {
          venc.date(numero_venc);  
        }
        if (mes_venc == "P") venc.add(1,'months');

        var corresponde_a = inicio.format("MMMM YYYY");
        var tr = "<tr>";
        tr+= "<td>Cuota "+(i+1)+"</td>";
        tr+= "<td>"+corresponde_a+"</td>";
        tr+= "<td>"+venc.format("DD/MM/YYYY")+"</td>";
        tr+= "<td><input "+(!isEmpty(icono_pagada) ? "disabled":"")+" type='number' min='0' ";
        tr+= " data-numero='"+(i+1)+"' class='form-control no-model cuota' ";
        tr+= " data-pagada='"+(!isEmpty(icono_pagada) ? "1":"0")+"' ";
        tr+= " data-corresponde_a='"+corresponde_a+"' ";
        tr+= " data-vencimiento='"+venc.format("YYYY-MM-DD")+"' value='"+monto+"' /></td>";
        tr+= "<td class='tac'>"+icono_pagada+"</td>";
        tr+="</tr>";
        this.$("#cuotas_tabla tbody").append(tr);
        inicio.add(1,'month');
      }
      this.calcular_totales();
    },

    calcular_totales: function() {
      var total_adeudado = 0;
      var total_pagado = 0;
      this.$(".cuota").each(function(i,e){
        var monto = $(e).val();
        var pagada = $(e).data("pagada");
        if (pagada == 1) {
          total_pagado += parseFloat(monto);
        } else {
          total_adeudado += parseFloat(monto);
        }
      });
      this.$().val();
      this.$().val();
    },

    agregar_expensa: function() {
      // Controlamos los valores
      var nombre = $("#expensa_nombre").val();
      if (isEmpty(nombre)) {
        alert("Por favor ingrese un nombre");
        $("#expensa_nombre").focus();
        return;
      }
      var monto = $("#expensa_monto").val();
      if (isNaN(monto) || monto < 0) {
        alert("Por favor ingrese un valor");
        $("#expensa_monto").focus();
        return;
      }
      var tr = "<tr>";
      tr+="<td>"+nombre+"</td>";
      tr+="<td>"+Number(monto).toFixed(2)+"</td>";
      tr+="<td><i class='fa fa-pencil cp editar_expensa'></i></td>";
      tr+="<td><i class='fa fa-times eliminar_expensa text-danger cp'></i></td>";
      tr+="</tr>";

      if (this.item == null) {
        $("#expensas_tabla tbody").append(tr);
      } else {
        $(this.item).replaceWith(tr);
        this.item = null;
      }

      $("#expensa_nombre").val("");
      $("#expensa_monto").val("");
    },

    editar_expensa: function(e) {
      this.item = $(e.currentTarget).parents("tr");
      $("#expensa_nombre").val($(this.item).find("td:eq(0)").text());
      $("#expensa_monto").val($(this.item).find("td:eq(1)").text());
    },


    validar: function() {
      try {
        var self = this;  

        // Guardamos los expensas
        var expensas = new Array();
        $("#expensas_tabla tbody tr").each(function(i,e){
          expensas.push({
            "nombre": $(e).find("td:eq(0)").html(),
            "monto": $(e).find("td:eq(1)").html(),
          });
        });
        this.model.set({"expensas":expensas});

        if (this.model.get("id_cliente") == 0) {
          alert("Por favor seleccione un cliente.");
          $("#alquiler_clientes").select();
          return false;
        }

        if (this.model.get("id_propiedad") == 0) {
          alert("Por favor seleccione una propiedad.");
          $("#alquiler_propiedades").select();
          return false;
        }

        var cuotas = new Array();
        this.$(".cuota").each(function(i,e){
          var monto = $(e).val();
          var numero = $(e).data("numero");
          var vencimiento = $(e).data("vencimiento");
          var pagada = $(e).data("pagada");
          var corresponde_a = $(e).data("corresponde_a");
          cuotas.push({
            "monto":monto,
            "numero":numero,
            "vencimiento":vencimiento,
            "pagada":pagada,
            "corresponde_a":corresponde_a,
          });
        });

        this.model.set({
          "contrato":self.$("#hidden_contrato").val(),
          "fecha_inicio":self.$("#alquiler_fecha_inicio").val(),
          "fecha_fin":self.$("#alquiler_fecha_fin").val(),
          "dia_vencimiento":self.$("#alquiler_dia_vencimiento").val(),
          "enviar_recordatorios":(self.$("#alquiler_enviar_recordatorios").is(":checked")?1:0),
          "cuotas":cuotas,
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



(function ( app ) {

  app.views.RescindirAlquilerView = app.mixins.View.extend({

    template: _.template($("#rescindir_alquiler_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",      
    },    
        
    initialize: function(options) {
      var self = this;
      _.bindAll(this);
      $(this.el).html(this.template(this.model.toJSON()));

      if (isEmpty(this.model.get("fecha_cancelacion_contrato"))) {
        this.model.set("fecha_cancelacion_contrato",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#rescindir_alquier_fecha"),this.model.get("fecha_cancelacion_contrato"));
    },

    validar: function() {
      try {
        var self = this;        
        this.model.set({
          "fecha_cancelacion_contrato":self.$("#rescindir_alquier_fecha").val(),
          "motivo_cancelacion_contrato":self.$("#rescindir_alquiler_motivo_cancelacion_contrato").val(),
          "estado":"C",
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
              location.reload();
            }
          }
        });
      }     
    },
      
  });
})(app);




// -----------
//   MODELO
// -----------

(function ( models ) {

  models.ReciboAlquiler = Backbone.Model.extend({
    urlRoot: "recibos_alquileres",
    defaults: {
    },
  });
    
})( app.models );



// ----------------------
//   COLECCION PAGINADA
// ----------------------

(function (collections, model, paginator) {

  collections.RecibosAlquileres = paginator.requestPager.extend({

    model: model,
    
    paginator_ui: {
      perPage: 30,
    },
  
    paginator_core: {
      url: "alquileres/function/listado_recibos",
    }
    
  });

})( app.collections, app.models.ReciboAlquiler, Backbone.Paginator);



// -----------------------------------------
//   TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {

  app.views.RecibosAlquileresTableView = app.mixins.View.extend({

    template: _.template($("#recibos_alquileres_resultados_template").html()),
      
    myEvents: {
      "change #recibos_alquileres_buscar":"buscar",
      "change #recibos_alquileres_meses":"buscar",
      "change #recibos_alquileres_anio":"buscar",
      "click .buscar":"buscar",
    },
    
    initialize : function (options) {
      
      var self = this;
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      this.options = options;
      this.habilitar_seleccion = (this.options.habilitar_seleccion == undefined || this.options.habilitar_seleccion == false) ? false : true;
      this.permiso = this.options.permiso;

      this.estado = (typeof this.options.estado != "undefined") ? this.options.estado : 0;
      this.filter = (typeof this.options.filter != "undefined") ? this.options.filter : "";
      this.pagina = (typeof this.options.pagina != "undefined") ? this.options.pagina : 1;

      this.render();
      this.collection.on('sync', this.addAll, this);
      this.buscar();
    },

    render: function() {
      var self = this;
      var pagination = new app.mixins.PaginationView({
        ver_filas_pagina: true,
        collection: this.collection
      });
      $(this.el).html(this.template({
        "permiso":this.permiso,
        "seleccionar":this.habilitar_seleccion,
        "estado":self.estado,
        "active":(self.estado == 0) ? "recibos_alquileres_adeudados" : "recibos_alquileres_pagados",
      }));
      this.$(".pagination_container").html(pagination.el);
      return this;
    },
    
    buscar: function() {
      this.mes = this.$("#recibos_alquileres_meses").val();
      this.anio = this.$("#recibos_alquileres_anio").val();
      this.filter = this.$("#recibos_alquileres_buscar").val().trim();
      this.collection.server_api = {
        "filter":this.filter,
        "estado":this.estado,
        "id_propiedad":this.id_propiedad,
        "mes":this.mes,
        "anio":this.anio,
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
      var self = this;
      var view = new app.views.RecibosAlquileresItemResultados({
        model: item,
        collection: self.collection,
        habilitar_seleccion: self.habilitar_seleccion, 
      });
      this.$(".tbody").append(view.render().el);
    },        
    
  });

})(app);


// -----------------------------------------
//   ITEM DE LA TABLA DE RESULTADOS
// -----------------------------------------
(function ( app ) {
  app.views.RecibosAlquileresItemResultados = app.mixins.View.extend({
    
    template: _.template($("#recibos_alquileres_item_resultados_template").html()),
    tagName: "tr",
    myEvents: {
      "click .enviar_whatsapp":function(){
        var telefono = this.model.get("celular");
        if (isEmpty(telefono)) {
          if (confirm("ERROR: El cliente no tiene celular cargado. Desea editarlo?")) {
            location.href = "app/#cliente/"+this.model.get("id_cliente");
          }
          return;
        }
        if (telefono.length != 10) {
          if (confirm("ERROR: El formato de telefono del cliente es incorrecto. Debe ser sin 0 ni 15. Desea editarlo?")) {
            location.href = "app/#cliente/"+this.model.get("id_cliente");
          }
          return;
        }
        telefono = "549"+telefono;
        var link_completo = 'Hola '+this.model.get("cliente")+', te enviamos el cupon de pago del alquiler de este mes: https://www.varcreative.com/sistema/alquileres/function/cupon_pago/'+this.model.get("hash");
        var salida = "https://wa.me/"+telefono+"?text="+encodeURIComponent(link_completo);
        window.open(salida,"_blank");        
      },
      "click .data":"seleccionar",
      "click .agregar_pago":"agregar_pago",
      "click .imprimir":"imprimir",
      "click .imprimir_cupon_pago":"imprimir_cupon_pago",
      "click .ver_contrato":"ver_contrato",
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
          $.ajax({
            "url":"alquileres/function/borrar_recibo/"+self.model.id,
            "type":"post",
            "data":{
              "corresponde_a":self.model.get("corresponde_a"),
              "id_alquiler":self.model.get("id_alquiler"),
            },
            "dataType":"json",
            "success":function(){
              location.reload();
            },
          })
        }
        return false;
      },
      "click .rescindir":function(e) {
        var self = this;
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Desea rescindir el contrato con "+this.model.get("cliente")+"?")) {
          var recibo_alquiler = new app.models.ReciboAlquiler({ "id": self.model.id });
          recibo_alquiler.fetch({
            "success":function() {
              var rescindirReciboAlquilerView = new app.views.RescindirReciboAlquilerView({
                model: recibo_alquiler,
              });
              var d = $("<div/>").append(rescindirReciboAlquilerView.el);
              crearLightboxHTML({
                "html":d,
                "width":400,
                "height":500,
              });
            }
          });
        }
        return false;
      },
    },
    seleccionar: function() {
      if (this.habilitar_seleccion) {
        window.recibo_alquiler_seleccionado = this.model;
        $('.modal:last').modal('hide');
      }
    },
    agregar_pago : function() {
      var self = this;
      var comprobantes = new Array();
      comprobantes.push({
        "id": self.model.get("id"),
        "id_punto_venta": self.model.get("id_punto_venta"),
        "fecha": self.model.get("fecha"),
        "comprobante": self.model.get("comprobante"),
        "nombre": self.model.get("cliente"),
        "anulada": 0,
        "pagada": self.model.get("pagada"),
        "tipo_pago": "E",
        "debe": self.model.get("total"),
        "haber": 0,
        "tipo_punto_venta": 0,
        "saldo": 0,
        "tipo": 0, // INDICA SI ES PAGO O NO
        "tipo_comprobante": "Recibo",
        "total":self.model.get("total"),
        "pago":0,
        "progreso":0,
        "negativo":0,
        "total_pagado":0,
      });

      var reciboCliente = new app.models.ReciboCliente({
        "id_empresa":ID_EMPRESA,
        "id_cliente":self.model.get("id_cliente"),
        "cheques": [],
        "depositos": [],
        "tarjetas": [],
        "comprobantes": comprobantes,
      });
      app.views.reciboClientes = new app.views.ReciboClientes({
        model: reciboCliente
      });

      window.id_recibo = 0;
      
      // Abrimos el lightbox de pagos
      crearLightboxHTML({
        "html":app.views.reciboClientes.el,
        "width":900,
        "height":500,
        "callback":function() {
          self.collection.pager();
          if(window.id_recibo != 0) self.imprimir();          
        }
      });
    },
    imprimir: function() {
      var id = this.model.id;
      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='alquileres/function/imprimir/"+id+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      //iframe+='<button class="btn btn-info btn-addon m-r">';
      //iframe+='<i class="fa fa-send"></i><span>Enviar</span>';
      //iframe+='</button>';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":860,
        "height":600,
      });
    },
    imprimir_cupon_pago: function() {
      $('.modal:last').modal('hide'); // Cerramos si hay otro lightbox abierto
      var iframe = "<iframe style='width:100%; border:none; height:600px;' src='alquileres/function/cupon_pago/"+ID_EMPRESA+"/"+this.model.id+"/"+this.model.get("id_punto_venta")+"'></iframe>";
      iframe+='<div class="text-right wrapper">';
      //iframe+='<button class="btn btn-info btn-addon m-r">';
      //iframe+='<i class="fa fa-send"></i><span>Enviar</span>';
      //iframe+='</button>';
      iframe+='<button class="btn btn-default" onclick="workspace.cerrar_impresion()">Cerrar</button>';
      iframe+='</div>';
      crearLightboxHTML({
        "html":iframe,
        "width":860,
        "height":600,
      });
    },
    ver_contrato: function() {
      location.href="app/#alquiler/"+this.model.get("id_alquiler");
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

/*

// -----------------------------------------
//   DETALLE DEL ARTICULO
// -----------------------------------------
(function ( app ) {

  app.views.ReciboAlquilerEditView = app.mixins.View.extend({

    template: _.template($("#recibo_alquiler_template").html()),
      
    myEvents: {
      "click .guardar": "guardar",      

      // Si se cambian las fechas o el dia de vencimiento
      "change #recibo_alquiler_fecha_inicio":"change_fechas",
      "change #recibo_alquiler_fecha_fin":"change_fechas",
      "change #recibo_alquiler_dia_vencimiento":"change_fechas",
      "change #recibo_alquiler_mes_vencimiento":"change_fechas",

      "change .cuota":function() {

      },

      "keydown #recibo_alquiler_fecha_inicio":function(e) {
        if (e.which == 13) $("#recibo_alquiler_fecha_fin").select();
      },
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
      
      this.$("#recibo_alquiler_tipos_estado").select2({});
      this.$("#recibo_alquiler_tipos_operacion").select2({});
      this.$("#recibo_alquiler_tipos_inmueble").select2({});
      this.$("#recibo_alquiler_usuarios").select2({});

      if (isEmpty(this.model.get("fecha_inicio"))) {
        this.model.set("fecha_inicio",moment().format("DD/MM/YYYY"));
      }
      createdatepicker(this.$("#recibo_alquiler_fecha_inicio"),this.model.get("fecha_inicio"));

      if (isEmpty(this.model.get("fecha_fin"))) {
        this.model.set("fecha_fin",moment().add(2,'years').format("DD/MM/YYYY"));
      }       
      createdatepicker(this.$("#recibo_alquiler_fecha_fin"),this.model.get("fecha_fin"));

      // AUTOCOMPLETE DE PROPIEDADES
      var input1 = this.$("#recibo_alquiler_propiedades");
      $(input1).customcomplete({
        "url":"/sistema/propiedades/function/ver/",
        "form":null, // No quiero que se creen nuevos productos
        "info":"localidad",
        "width":400,
        "image_field":"path",
        "image_path":"/sistema",
        "onSelect":function(item){
          self.$("#recibo_alquiler_propiedades").val(item.label);
          self.model.set({"id_propiedad":item.id});
          self.$("#recibo_alquiler_fecha_inicio").select();
        }
      });      

      // AUTOCOMPLETE DE CLIENTES
      var input = this.$("#recibo_alquiler_clientes");
      var form = new app.views.ClienteEditViewMini({
        "model": new app.models.Cliente(),
        "input": input,
        "onSave": self.seleccionar_cliente,
      });      
      $(input).customcomplete({
        "url":"clientes/function/get_by_nombre/",
        "form":form,
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

      self.change_fechas();
    },

    seleccionar_cliente: function(r) {
      this.model.set({"id_cliente":r.id});
      this.$("#recibo_alquiler_clientes").val(r.get("nombre"));
      this.$("#recibo_alquiler_propiedades").select();
    },

    change_fechas: function() {
      moment.locale("es",{
        months : "Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre".split("_"),
      });
      var fecha_inicio = this.$("#recibo_alquiler_fecha_inicio").val();
      var fecha_fin = this.$("#recibo_alquiler_fecha_fin").val();
      var inicio = moment(fecha_inicio,"DD/MM/YYYY");
      var fin = moment(fecha_fin,"DD/MM/YYYY");

      var numero_venc = this.$("#recibo_alquiler_dia_vencimiento").val();
      var mes_venc = this.$("#recibo_alquiler_mes_vencimiento").val();
      var cuotas = this.model.get("cuotas");
      
      // Calcula la diferencia en meses entre la fecha de inicio y la de fin
      var meses = (fin.diff(inicio,'months'))+1;

      this.$("#cuotas_tabla tbody").empty();
      for(var i=0;i<meses;i++) {

        var icono_pagada = "";
        var monto = 0;
        
        if (typeof cuotas[i] != "undefined") {
          var cuota = cuotas[i];
          monto = cuota.monto;
          if (cuota.pagada == 1) {
            // Si la cuota esta paga, ponemos un iconito y deshabilitamos el campo
            icono_pagada = "<i class='fa fa-check text-success'></i>";
          }
        }
        
        var venc = inicio.clone();
        venc.date(numero_venc);
        if (mes_venc == "P") venc.add(1,'months');

        var corresponde_a = inicio.format("MMMM YYYY");
        var tr = "<tr>";
        tr+= "<td>Cuota "+(i+1)+"</td>";
        tr+= "<td>"+corresponde_a+"</td>";
        tr+= "<td>"+venc.format("DD/MM/YYYY")+"</td>";
        tr+= "<td><input "+(!isEmpty(icono_pagada) ? "disabled":"")+" type='number' min='0' ";
        tr+= " data-numero='"+(i+1)+"' class='form-control no-model cuota' ";
        tr+= " data-pagada='"+(!isEmpty(icono_pagada) ? "1":"0")+"' ";
        tr+= " data-corresponde_a='"+corresponde_a+"' ";
        tr+= " data-vencimiento='"+venc.format("YYYY-MM-DD")+"' value='"+monto+"' /></td>";
        tr+= "<td class='tac'>"+icono_pagada+"</td>";
        tr+="</tr>";
        this.$("#cuotas_tabla tbody").append(tr);
        inicio.add(1,'month');
      }
      this.calcular_totales();
    },

    calcular_totales: function() {
      var total_adeudado = 0;
      var total_pagado = 0;
      this.$(".cuota").each(function(i,e){
        var monto = $(e).val();
        var pagada = $(e).data("pagada");
        if (pagada == 1) {
          total_pagado += parseFloat(monto);
        } else {
          total_adeudado += parseFloat(monto);
        }
      });
      this.$().val();
      this.$().val();
    },

    validar: function() {
      try {
        var self = this;        

        if (this.model.get("id_cliente") == 0) {
          alert("Por favor seleccione un cliente.");
          $("#recibo_alquiler_clientes").select();
          return false;
        }

        if (this.model.get("id_propiedad") == 0) {
          alert("Por favor seleccione una propiedad.");
          $("#recibo_alquiler_propiedades").select();
          return false;
        }

        var cuotas = new Array();
        this.$(".cuota").each(function(i,e){
          var monto = $(e).val();
          var numero = $(e).data("numero");
          var vencimiento = $(e).data("vencimiento");
          var pagada = $(e).data("pagada");
          var corresponde_a = $(e).data("corresponde_a");
          cuotas.push({
            "monto":monto,
            "numero":numero,
            "vencimiento":vencimiento,
            "pagada":pagada,
            "corresponde_a":corresponde_a,
          });
        });

        this.model.set({
          "contrato":self.$("#hidden_contrato").val(),
          "fecha_inicio":self.$("#recibo_alquiler_fecha_inicio").val(),
          "fecha_fin":self.$("#recibo_alquiler_fecha_fin").val(),
          "tipo_facturacion":self.$("#recibo_alquiler_tipo_facturacion").val(),
          "dia_vencimiento":self.$("#recibo_alquiler_dia_vencimiento").val(),
          "mes_vencimiento":self.$("#recibo_alquiler_mes_vencimiento").val(),
          "cuotas":cuotas,
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
*/