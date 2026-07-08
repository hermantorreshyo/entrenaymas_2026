(function ( models ) {

  models.PresPlanCredito = Backbone.Model.extend({
    urlRoot: "pres_planes_credito/",
    defaults: {
      id_empresa: ID_EMPRESA,
      nombre: "",
      codigo: "",
      activo: 1,
      maximo_cuotas: 12,
      hab_monotributista: 0,
      hab_dependencia: 0,
      hab_jubilado: 0,
      hab_otro_estado_laboral: 0,
      documentacion: [],
      cuotas: [],
      dias_primera_cuota: 0,
      tasa_interes: 0,
      coeficiente_punitorio: 0,
    }
  });

})( app.models );


(function (collections, model, paginator) {
  collections.PresPlanesCredito = paginator.requestPager.extend({
    model: model,
    paginator_core: {
      url: "pres_planes_credito/"
    }
  });
})( app.collections, app.models.PresPlanCredito, Backbone.Paginator);


(function ( app ) {
  app.views.PresPlanCreditoItem = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#pres_planes_credito_item').html()),
    events: {
      "click .ver": "editar",
      "click .delete": "borrar",
      "click .duplicar": "duplicar"
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
      location.href="app/#pres_plan_credito/"+this.model.id;
    },
    borrar: function(e) {
      if (confirmar("Realmente desea eliminar este elemento?")) {
        this.model.destroy();  // Eliminamos el modelo
        $(this.el).remove();  // Lo eliminamos de la vista
      }
      e.stopPropagation();
    },
    duplicar: function(e) {
      var clonado = this.model.clone();
      clonado.set({id:null}); // Ponemos el ID como NULL para que se cree un nuevo elemento
      clonado.save({},{
        success: function(model,response) {
          model.set({id:response.id});
        }
      });
      this.model.collection.add(clonado);
      e.stopPropagation();
    }
  });

})( app );



// ----------------------
//   VISTA DE LA TABLA
// ----------------------

(function ( app ) {

  app.views.PresPlanesCreditoTableView = app.mixins.View.extend({

   template: _.template($("#pres_planes_credito_panel_template").html()),

   initialize : function (options) {

      _.bindAll(this); // Para que this pueda ser utilizado en las funciones

      var lista = this.collection;
      this.options = options;
      this.permiso = this.options.permiso;

      // Creamos la lista de paginacion
      var pagination = new app.mixins.PaginationView({
        collection: lista
      });

      // Creamos el buscador
      var search = new app.mixins.SearchView({
        collection: lista
      });

      this.collection.on('sync', this.addAll, this);

      // Renderizamos por primera vez la tabla:
      // ----------------------------------------
      var obj = { permiso: this.permiso };
      
      // Cargamos el template
      $(this.el).html(this.template(obj));
      // Cargamos el paginador
      $(this.el).find(".pagination_container").html(pagination.el);
      // Cargamos el buscador
      $(this.el).find(".search_container").html(search.el);

      // Vamos a buscar los elementos y lo paginamos
      lista.pager();
    },

    addAll : function () {
      $(this.el).find("tbody").empty();
      this.collection.each(this.addOne);
    },

    addOne : function ( item ) {
      var view = new app.views.PresPlanCreditoItem({
        model: item,
        permiso: this.permiso,
      });
      $(this.el).find("tbody").append(view.render().el);
    }

  });
})(app);



// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

  views.PresPlanCreditoEditView = app.mixins.View.extend({

    template: _.template($("#pres_planes_credito_edit_panel_template").html()),

    myEvents: {
      "click .guardar": "guardar",
      "click .nuevo": "limpiar",

      "click #cuota_agregar":"agregar_cuota",
      "click .editar_cuota":"editar_cuota",
      "click .eliminar_cuota":function(e){
        $(e.currentTarget).parents("tr").remove();
      },
      "keypress #pres_planes_credito_tasa_interes":function(e) {
        if (e.which == 13) this.agregar_cuota();
      },

    },

    initialize: function(options) {
      this.model.bind("destroy",this.render,this);
      _.bindAll(this);
      this.options = options;
      this.render();
    },

    render: function() {
      var self = this;
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var edicion = false;
      if (this.options.permiso > 1) edicion = true;
      var obj = { edicion: edicion, id:this.model.id };
      // Extendemos el objeto creado con el modelo de datos
      $.extend(obj,this.model.toJSON());

      $(this.el).html(this.template(obj));

      var documentacion = new Array();
      _.each(this.model.get("documentacion"),function(elem){
        documentacion.push(elem.id);
      });

      new app.mixins.Select({
        modelClass: app.models.PresDocumentacion,
        url: "pres_documentaciones/",
        render: "#pres_planes_credito_documentacion",
        multiple: true,
        selected: documentacion,
        onComplete:function(c) {
          self.$("#pres_planes_credito_documentacion").select2({
            tags: true,
          });
        }                    
      });

      return this;
    },

    agregar_cuota: function() {
      // Controlamos los valores
      var cuota = $("#pres_planes_credito_cuota").val();
      if (isEmpty(cuota)) {
        alert("Por favor ingrese una cuota");
        $("#pres_planes_credito_cuota").focus();
        return;
      }
      var tasa_interes = $("#pres_planes_credito_tasa_interes").val();
      if (isEmpty(tasa_interes)) {
        alert("Por favor ingrese una tasa de interes");
        $("#pres_planes_credito_tasa_interes").focus();
        return;
      }
      var tr = "<tr>";
      tr+="<td class='editar_cuota'><span class='text-info cuota'>"+cuota+"</td>";
      tr+="<td class='tasa_interes editar_cuota'>"+tasa_interes+"</td>";
      tr+="<td class='tar'>";
      tr+="<button class='btn btn-sm btn-white eliminar_cuota'><i class='fa fa-trash'></i></button>";
      tr+="</td>";
      tr+="</tr>";
      if (this.item_cuota == null) {
        $("#pres_planes_credito_cuotas_tabla tbody").append(tr);
      } else {
        $(this.item_cuota).replaceWith(tr);
        this.item_cuota = null;
      }
      $("#pres_planes_credito_cuota").val("");
      $("#pres_planes_credito_tasa_interes").val("");
    },
    
    editar_cuota: function(e) {
      this.item_cuota = $(e.currentTarget).parents("tr");
      $("#pres_planes_credito_cuota").val($(this.item_cuota).find(".cuota").text());
      $("#pres_planes_credito_tasa_interes").val($(this.item_cuota).find(".tasa_interes").text());
    },

    validar: function() {
      try {
        // Validamos los campos que sean necesarios
        validate_input("pres_planes_credito_nombre",IS_EMPTY,"Por favor, ingrese un nombre.");

        var cuotas = new Array();
        $("#pres_planes_credito_cuotas_tabla tbody tr").each(function(i,e){
          cuotas.push({
            "cuota": $(e).find(".cuota").text(),
            "tasa_interes": $(e).find(".tasa_interes").text(),
          });
        });
        this.model.set({"cuotas":cuotas});

        var c = self.$("#pres_planes_credito_documentacion").select2("val");
        if (c != null) this.model.set({ "documentacion":c });

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
            location.href="app/#pres_planes_credito";
          }
        });
      }
    },

    limpiar : function() {
      this.model = new app.models.PresPlanCredito();
      this.render();
    },

  });

})(app.views, app.models);