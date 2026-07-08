(function( $ ) {
  $.fn.customcomplete = function(options) {

    var settings = $.extend({
        
      // OBLIGATORIAS: Estas dos opciones son excluyentes, tiene que estar una de las dos si o si
      "collection":null,          // Coleccion en donde buscar
      "array": null,
      "url": null,                  // URL en donde buscar
      
      // OPCIONALES
      "only_options":true,        // Si es true, solamente permite elegir de las opciones (no se puede escribir nada nuevo)
      "form":null,                // Si es null, no se pueden crear nuevos
      "closable":true,            // Si se cierra cuando cambia de pagina
      "width":"",                 // Ancho del componente (vacio 100%)
      "height":"",                // Alto del componente (180px default)
      "offsetTop":0,              // Distancia con el input
      "minLength":3,              // Cantidad minima que es necesario escribir
      "label":"[nombre]",         // Texto que se muestra. Entre corchetes toma los campos del modelo
      "image_field":null,         // Atributo para poner una imagen
      "image_path":"",            // Path fijo que completa la imagen
      "info":"",                  // Informacion adicional de los resultados
      "id":"id",                  // Atributo identificador del objeto que se obtiene como valor
      "disableNumber":true,       // Si es true, deshabilita que se escriba solo numeros
      "hideNoResults":false,      // Si es true, oculta el mensaje "No se encuentran resultados.."
      "onSelect":function(item){},  // Funcion que se llama cuando se selecciona un elemento
      "close":function(){},       // Funcion que se llama cuando se cierra
    },options);
    
    var id = $(".customcomplete").length; // Contador para identificar los elementos
    
    // A cada input le ponemos el autocomplete de JqueryUI
    this.filter("input[type=text]").each(function() {

      var input = $(this);
      var className = (settings.closable)?"closable":"";
      var container = $("<div class='customcomplete "+className+"' id='customcomplete_"+id+"'/>");
      
      // Paramos la propagacion para que, al hacer click en cualquier parte, se cierre el autocomplete
      $(input).click(function(e){
        $(e.currentTarget).select();
        e.stopPropagation();
      });
      $(container).click(function(e){
          e.stopPropagation();
      });
      
      // Creamos el boton
      if (settings.form != null) {
        var btn = $("<button/>")
        .addClass("btn btn-default btn-block btn-crear")
        .click(function(){
          if (settings.form != null && settings.form != undefined) {
            // Abrimos el formulario
            $(container).find("ul").hide();
            $(container).find("button").hide();

            // Como nombre ponemos lo que se escribio
            settings.form.model.set({"nombre":$(input).val()});
            
            $(container).find(".new-container").html(settings.form.render().el);
            settings.form.focus();
            settings.form.close = self.close;
          }
        })
        .keydown(function(e){
          if (e.which == 38) {
            // Flechita para arriba, volvemos al ultimos
            e.stopPropagation();
            e.preventDefault();
            var obj = $(container).find("ul li:last").find("a").focus();
            if (obj.length == 0) $(input).focus();
            return false;
          } else if (e.which == 40) {
            // La flechita para abajo la deshabilitamos
            e.stopPropagation();
            e.preventDefault();
            return false;
          }
        });                
      } else {
        var btn = $("<div/>")
      }
      
      var ul = $("<ul/>");
      $(ul).css("max-height",((settings.height != "") ? settings.height+"px" : "180px"));

      var cerrable = $("<div/>");
      $(cerrable).append("<i class='fa fa-times cp customcomplete-close' style='position:absolute;right:5px;top:3px;'></i>");
      $(cerrable).find(".customcomplete-close").on("click",function(){
        self.close();
      });
      
      $(container).append(cerrable).append(ul).append(btn).append("<div class='new-container'></div>");
      $("body").append(container);
      
      var self = this;
      $(container).on("keydown",function(e){
        if (e.which == 27) self.close();
      });

      var delay = (function(){
        var timer_custom = 0;
        return function(callback, ms){
          clearTimeout(timer_custom);
          timer_custom = setTimeout(callback, ms);
        };
      })();            
      
      // Cuando se escribe sobre el input
      input.on("keyup",function(e){

        // Si se apreto F2, se debe obviar
        if (e.which == 113) return;

        // Si se apreto escape, se cierra todo
        if (e.which == 27) {
          e.preventDefault();
          e.stopPropagation();
          self.close();
          return false;
        }
        
        // Si se hace con la flechita para abajo sobre el INPUT
        else if (e.which == 40) {
          e.preventDefault();
          e.stopPropagation();
          var obj = $(container).find("ul").first().find("li:first a:first");
          // No hay coincidencias, seleccionamos el boton
          if (obj.length == 0) $(container).find("button").focus();
          else $(obj).focus()
          return false;
        }

        // Esta funcion permite reducir el numero de consultas que se hacen
        // ya que retrasa la ejecucion hasta que el usuario haya terminado
        // de escribir en el input
        delay(function(){

          // ------------------------------
          var do_filter = function(filter) {
            console.log(filter);
            // Si no se encuentran resultados, y NO debemos mostrar el form
            if (filter.length == 0 && settings.form == null) {
              if (!settings.hideNoResults) {
                $(btn).html("<div style='padding:10px'>No se encuentran resultados para: <b>'"+term+"'</b></div>");    
              }
            } else {
              if (settings.form != null) $(btn).html("Crear: <b>'"+term+"'</b>");
              else $(btn).html("");
            }

            if (filter.length == 0) $(cerrable).hide();
            else $(cerrable).show();
              
            _.each(filter,function(item){
                
              // Creamos un nuevo elemento
              var li = "<li>";
              li+="<a href='javascript:void(0)'>";
              if (item.path != null && !isEmpty(item.path)) {
                li+="<img class='customcomplete-image' src='"+settings.image_path+"/"+item.path+"' class='autocomplete_logo'/>";    
              }
              li+= "<span class='customcomplete-label'>";
              li+= item.label;
              li+= "</span>";
              if (typeof item.info != "undefined" && item.info != "") li+="<br><span>"+item.info+"</span>";
              li+="</a>";
              li+="</li>";
              var li = $(li);
                  
              // Teclas sobre los <LI> de resultados
              $(li).keydown(function(e){
                e.stopPropagation();
                e.preventDefault();
                if (e.which == 40) {
                  // Hacemos con la flechita para abajo
                  var obj = $(e.currentTarget).next("li").find("a").focus();
                  // No hay coincidencias, seleccionamos el boton
                  if (obj.length == 0) $(container).find("button").focus();
                } else if (e.which == 38) {
                  // Hacemos con la flechita para arriba
                  var obj = $(e.currentTarget).prev("li").find("a").focus();
                  if (obj.length == 0) $(input).focus();
                } else if (e.which == 27) {
                  // ESC: cerramos el cuadro
                  self.close();
                } else if (e.which == 13) {
                  // Enter: ejecutamos el callback
                  self.onSelect(input,item);
                  // Y cerramos
                  self.close();
                }
              });
              // Si hacemos click en un resultado
              $(li).find("a").click(function(){
                // Ejecutamos el callback
                self.onSelect(input,item);
                // Y cerramos
                self.close();
              });
              // Lo agregamos a la lista
              $(ul).append(li);
            });
              
            // Mostramos la sugerencia
            var position = $(input).offset();
            var top = position.top + $(input).outerHeight() + settings.offsetTop;
            $(container).css({
              "z-index":8000,
              "top":top+"px",
              "left":position.left+"px",
              "display":"block",
              "width":(settings.width != "") ? settings.width : $(input).outerWidth()+"px"
            });
          }
              
          // Tomamos lo que se escribio en el input
          var term = $(input).val().trim();
          if (isEmpty(term)) self.close();
          $(ul).empty();
          if (term.length < settings.minLength) return false; // Minimo de escritura
          var filter = new Array();
          
          // Si no comenzo con numeros, autocompletamos
          if (!settings.disableNumber || !term.match(/^\d+$/)) {
              
            // Debemos buscar en la coleccion
            if (settings.collection != null) {
              settings.collection.each(function(item){

                // Reemplazamos todo lo que esta encerrado entre corchetes
                // por los valores actuales del modelo
                var label = settings.label;
                var replaces = settings.label.match(/[^[\]]+(?=])/g);
                for(var i=0;i<replaces.length;i++) {
                  var r = replaces[i];
                  label = label.replace("["+r+"]",item.get(r));
                }

                // Si el termino esta en algun lugar del campo
                if (label.toUpperCase().indexOf(term.toUpperCase()) >= 0) {
                  filter.push({
                    label: label,
                    info: (!isEmpty(settings.info)) ? item.get(settings.info) : "",
                    path: (settings.image_field != null) ? item.get(settings.image_field) : null,
                    value:item.id,
                    element: item,
                    result:true,
                  });
                }
              });
              do_filter(filter);
            }
              
            // Sino consultamos por AJAX
            else if (settings.url != null) {

              var url_f = (typeof settings.url == "function") ? settings.url() : settings.url;
              var r = $.ajax({
                "data": "term="+term,
                "dataType": "json",
                "type":"get",
                "url":url_f,
                "success":do_filter,
              });
            }

            // Y sino es un array
            else if (settings.array != null) {
              _.each(settings.array,function(item){

                // Reemplazamos todo lo que esta encerrado entre corchetes
                // por los valores actuales del modelo
                var label = settings.label;
                var replaces = settings.label.match(/[^[\]]+(?=])/g);
                for(var i=0;i<replaces.length;i++) {
                  var r = replaces[i];
                  label = label.replace("["+r+"]",item[r]);
                }

                // Si el termino esta en algun lugar del campo
                if (label.toUpperCase().indexOf(term.toUpperCase()) >= 0) {
                  filter.push({
                    label: label,
                    info: (!isEmpty(settings.info)) ? item[settings.info] : "",
                    path: (settings.image_field != null) ? item[settings.image_field] : null,
                    value:item.id,
                    element: item,
                    result:true,
                  });
                }
              });
              do_filter(filter);
            }
          }

        }, 500 );
          
      });
      
      this.onSelect = function(input,item) {
        input.attr("data-id",item.id);
        input.attr("data-value",item.value);
        input.val(item.value);
        settings.onSelect(item); // Ejecutamos el callback
      }
      
      this.close = function() {
        $(container).find(".new-container").empty();
        $(container).find("ul").show();
        $(container).find("button").show();
        $(container).hide();
        //$(input).focus();
        settings.close();                
      }
      var these = this;
      $("html").click(function(){
        these.close();
      });
      
      id++;
    });
    return this;
  };
}( jQuery ));