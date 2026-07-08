/**
 * Este mixin representa un SELECT HTML que se carga con AJAX
 *
 * Parametros:
 * -----------
 *
 *   @param String url URL donde se toman los datos
 *   @param Backbone.Collection coleccion Coleccion donde se toman los datos
 *   
 *   @param String options String en formato URL que sirve para enviar datos al servidor
 *   @param String selected Indica cuales Ids estan seleccionados
 *   @param String modelClass Clase del modelo de Backbone
 *   @param String campoSelect Indica que campo del modelo se muestra como texto
 *   @param String separador String que separa los campos que se muestran como texto del select
 *   @param String name Name del Select
 *   @param String renderIn Nombre del DIV donde se va a renderizar el select
 *   @param String render Nombre del SELECT donde se va a renderizar
 *   @param Array firstOptions Array con las primeras opciones del select. Sirve por ejemplo para poner: Todos, Ninguno, etc.
 *   @param Boolean multiple Indica si el select es multiple o no. Por defecto: false
 *   @param Function success Ajax Callback cuando se completa la operacion
 *   @param Function error Ajax Callback cuando falla la operacion
 *   @param String height Alto del select
 *   @param String width Ancho del select
 *   @param String id_field Nombre del campo ID de la tabla
 *   @param Function change Funcion que se llama cuando se cambia el select
 *   @param Function onComplete Funcion que se llama cuando termina todo el render del select
 *   @param Array fields Array con el nombre de los campos a incluir como data de las opciones
 */
(function (app) {

  app.mixins.Select = Backbone.View.extend({
    tagName: "select",
    attributes: {
      class: "select"
    },
    events: {
      "change":"selectionChanged"
    },
    selectionChanged: function() {
      if (this.options.change != undefined) {
        this.options.change(this.collection);
      }
    },
    onComplete: function() {
      if (this.options.onComplete != undefined) {
        this.options.onComplete(this.collection);
      }
    },
    initialize: function(options) {
      
      _.bindAll(this);
      var self = this;
      self.options = options;
      self.model = this.options.modelClass;
      self.url = this.options.url;
      
      // Creamos la coleccion
      Nueva_Coleccion = Backbone.Collection.extend({
        model : self.model,
        url : function() {
          if (self.options.extend != undefined) {
            return self.url+"?"+self.options.extend;
          } else {
            return self.url;
          }
        },
        initialize: function() {
          var success = (self.options.success != undefined) ? self.options.success : "";
          var error = (self.options.error != undefined) ? self.options.error : "";
          this.fetch({
            "success": success,
            "error": error
          });
        },
        parse: function(response) {
          return response.results;
        }
      });
      
      // Creamos la nueva coleccion
      this.collection = new Nueva_Coleccion();
      this.collection.bind("sync",this.render,this);
    },
    render: function() {
      
      if (this.options.multiple != undefined) $(this.el).attr("multiple","multiple");
      if (this.options.width != undefined) $(this.el).css("width",this.options.width);
      if (this.options.height != undefined) $(this.el).css("height",this.options.height);
      if (this.options.disabled != undefined) $(this.el).prop("disabled",this.options.disabled);
      if (this.options.required != undefined) $(this.el).prop("required",this.options.required);
      
    $(this.el).empty();
      
      // Primero cargamos las primeras opciones
      if (this.options.firstOptions != undefined) {
        for(var i=0; i<this.options.firstOptions.length; i++) {
          $(this.el).append(this.options.firstOptions[i]);
        }        
      }
      
      var sep = (this.options.separador != undefined) ? this.options.separador : " - ";
      var id_field = (this.options.id_field != undefined) ? this.options.id_field : "id";
      
      for(var i=0; i<this.collection.length; i++) {
        
        var e = this.collection.models[i];
        var selected = "";
        
        // Si selected es un array, buscamos en el array el id
        if (Array.isArray(this.options.selected)) {
          for(var k=0;k<this.options.selected.length;k++) {
            var s = this.options.selected[k];
            if (s == e.get(id_field)) {
              selected = "selected";
            }
          }
        } else {
          // Sino directamente comparamos
          selected = (this.options.selected == e.get(id_field) ) ? 'selected' : '';  
        }
        var id = e.get(id_field);
        
        // Comenzamos la etiqueta option
        var s = "<option value='"+id+"' "+selected;
        
        // Si tiene campos opcionales para mostrar como data_{field}
        if (this.options.fields != undefined) {
          for(var k=0;k<this.options.fields.length;k++) {
            var field = this.options.fields[k];
            s+=" data-"+field+"='"+e.get(field)+"' ";
          }
        }
        
        s+=">";
        
        // En this.options.campoSelect tenemos un string con todos los campos
        // que se quieren mostrar en las opciones del select. Es un string
        // separado por comas
        if (this.options.campoSelect != undefined) {
          // Separamos el string
          var campos = this.options.campoSelect.split(",");
          // Vamos recorriendo cada campo
          for(j=0;j<campos.length;j++) {
            s=s+e.get(campos[j]); // Concatenamos el campo
            if (campos[j+1] != undefined) s=s+sep; // Separamos con guiones
          }
        } else {
          // Sino, por defecto mostramos el "nombre"
          s=s+e.get("nombre");
        }
        s=s+"</option>";
        $(this.el).append(s);
      }
      
      $(this.el).attr("name",this.options.name);
      
      if (this.options.renderIn != undefined) {
        $(this.options.renderIn).html($(this.el));
      } else {
        $(this.el).addClass($(this.options.render).attr("class"));
        $(this.el).attr("id",$(this.options.render).attr("id"));
        $(this.el).attr("tabindex",$(this.options.render).attr("tabindex"));
        $(this.options.render).replaceWith($(this.el));
      }
      this.selectionChanged();
      this.onComplete();
      
      return this;
    }
  });

})(app);