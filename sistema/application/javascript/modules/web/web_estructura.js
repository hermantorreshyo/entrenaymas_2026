// -------------------------------
//   VISTA DEL PANEL DE EDICION
// -------------------------------
(function ( views, models ) {

	views.WebEstructuraEditView = app.mixins.View.extend({

		template: _.template($("#web_estructura_edit_panel_template").html()),

    className: "h100p",

		myEvents: {
			"click .guardar": "guardar",
      "click .header-accordion":function(e) {
        var header = $(e.currentTarget);
        var info = header.next(".info-accordion");
        this.$(".info-accordion").not(info).slideUp();
        this.$(".header-accordion").not(header).removeClass("active");
        info.slideToggle();
        header.toggleClass("active");
      },
      "click .eliminar_container":function(e) {
        $(e.currentTarget).parent().remove();
      },
      "click .editar_publicidad":function(e) {
      },
      "click .editar_entradas":function(e) {
        var elemento = $(e.currentTarget).parent();
        var modelo = new app.models.AbstractModel({
          "id_categoria":$(elemento).data("id_categoria"),
          "offset":$(elemento).data("offset"),
          "estilo":$(elemento).data("estilo"),
        });
        var view = new app.views.WebEstructuraConfigEntradas({
          model: modelo,
          objeto: elemento,
        });
        var d = $("<div/>").append(view.el);
        crearLightboxHTML({
          "html":d,
          "width":500,
          "height":500,
        });
      },
		},

    initialize: function() {
      _.bindAll(this);
      this.render();
    },

    // Empieza a analizar la estructura guardada
    render_estructura: function() {
      var self = this;
      var estructura = this.model.get("estructura");
      var array = new Array();
      try {
        array = JSON.parse(estructura);
      } catch(e) {
        return;
      }
      for(var i=0; i<array.length;i++) {
        var o = array[i];
        try {
          var elem = self["create_"+o.element](o);
          var $elem = $(elem);
          self.$('.main-form-container').append($elem);
          self.render_estructura_iterator($elem,o.children)
        } catch(e) {
          continue;
        }
      }
      this.$(".sortable").sortable({
        opacity: .35,
        placeholder: "sortable-placeholder",
        connectWith: ".sortable",
      }).disableSelection();
    },
    // Esta funcion es para que vaya iterando y analizando la estructura
    render_estructura_iterator: function(parent,array) {
      if (array.length == 0) return;
      var self = this;
      for(var i=0;i<array.length;i++) {
        var o = array[i];
        try {
          var elem = self["create_"+o.element](o);
          var $elem = $(elem);
          var columna = $(parent).children(".column")[o.position];
          $(columna).children(".form-container").first().append($elem);
          self.render_estructura_iterator($elem,o.children);
        } catch(e) {
          continue;
        }
      }
    },

    render: function() {
      // Creamos un objeto para agregarle las otras propiedades que no son el modelo
      var self = this;

      $(this.el).html(this.template(this.model.toJSON()));
      this.render_estructura();
      
      // Abrimos el primer elemento
      this.$(".header-accordion").first().trigger("click");

      this.new_id = 0;
      this.zindex = 100;

      this.$('.main-form-container').sortable({
        placeholder: "sortable-placeholder",
        opacity: .35,
        connectWith: ".sortable",
        stop: function (e, t) {
          $(t.item).addClass("w100p");
          $(".sortable").sortable({
            opacity: .35,
            placeholder: "sortable-placeholder",
            connectWith: ".sortable",
          }).disableSelection();
        }
      }).disableSelection();

      //init toolbox 
      this.$('.draggable_element').draggable({
        connectToSortable: ".main-form-container",
        helper: function () {
          // Llamamos a la funcion que crea el elemento correspondiente
          var component = $(this).data("component");
          return self["create_"+component]();
        },
        start  : function(event, ui){
          $(ui.helper).css("z-index",self.zindex++);
        },
        revert: "invalid",
      }).disableSelection();

      return this;
    },


    // Creadores de componentes

    create_entradas: function (config) {
      config = (typeof config != "undefined") ? config : {};
      config.offset = (typeof config.offset != "undefined") ? config.offset : 6;
      config.estilo = (typeof config.estilo != "undefined") ? config.estilo : 0;
      config.id_categoria = (typeof config.id_categoria != "undefined") ? config.id_categoria : 6;
      this.new_id++;
      var div = '';
      div+='<div data-element="entradas" data-offset="'+config.offset+'" data-id_categoria="'+config.id_categoria+'" data-estilo="'+config.estilo+'" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <i class="fa fa-pencil editar_container editar_entradas"></i>';
      div+='  <div class="column col-md-12">';
      div+='    <div class="sortable" style="width: 100%;">Bloque de Entradas</div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    create_publicidad: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="publicidad" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <i class="fa fa-pencil editar_container editar_publicidad"></i>';
      div+='  <div class="column col-md-12">';
      div+='    <div class="sortable" style="width: 100%;">Publicidad</div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    create_container_1: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="container_1" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <div class="column col-md-12">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    create_container_2: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="container_2" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <div class="column col-md-6">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-6">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },
    
    create_container_3: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="container_3" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <div class="column col-md-4">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-4">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-4">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    create_container_4: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="container_4" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <div class="column col-md-3">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-3">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-3">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-3">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    create_container_6: function () {
      this.new_id++;
      var div = '';
      div+='<div data-element="container_6" class="form-container element" id="'+this.new_id+'">';
      div+='  <i class="fa fa-times eliminar_container"></i>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='  <div class="column col-md-2">';
      div+='    <div class="form-container sortable" style="width: 100%;"></div>';
      div+='  </div>';
      div+='</div>';
      var el = $(div);
      return el;
    },

    buscar_hijos:function(columna) {
      var self = this;
      var array = new Array();
      $(columna).each(function(ii,ee){
        $(ee).children(".form-container").children(".element").each(function(i,e){
          var obj = { 
            "position":ii,
            "element":$(e).data("element"),
          }
          if (typeof $(e).data("id_categoria") != "undefined") obj.id_categoria = $(e).data("id_categoria");
          if (typeof $(e).data("offset") != "undefined") obj.offset = $(e).data("offset");
          if (typeof $(e).data("estilo") != "undefined") obj.estilo = $(e).data("estilo");
          obj.children = self.buscar_hijos($(e).children(".column"));
          array.push(obj);
        });
      });
      return array;
    },

    validar: function() {
      var self = this;
      try {

        var array = new Array();
        this.$(".main-form-container > .element").each(function(i,e){
          var obj = { 
            "position":i,
            "element":$(e).data("element"),
          }
          if (typeof $(e).data("id_categoria") != "undefined") obj.id_categoria = $(e).data("id_categoria");
          if (typeof $(e).data("offset") != "undefined") obj.offset = $(e).data("offset");
          if (typeof $(e).data("estilo") != "undefined") obj.estilo = $(e).data("estilo");
          var children = $(e).children(".column");
          if (children.length > 0) {
            obj.children = self.buscar_hijos(children);
          }
          array.push(obj);
        });
        this.model.set({
          "estructura":JSON.stringify(array),
        });
        return true;

      } catch(e) {
        return false;
      }
    },

    guardar: function() {
      var self = this;
      if (this.validar()) {
        this.model.save({},{
          success: function(model,response) {
            location.reload();
          }
        });
      }
  },  
  });

})(app.views, app.models);


(function ( views, models ) {

  views.WebEstructuraConfigEntradas = app.mixins.View.extend({

    template: _.template($("#web_estructura_config_entradas_panel_template").html()),
    myEvents: {
      "click .guardar":"guardar",
      "click .cerrar": "cerrar",
      "keypress .tab":function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          $(e.currentTarget).parent().next().find(".tab").focus();
        }
      },
      "keyup .tab":function(e) {
        if (e.which == 27) this.cerrar();
      },
      "keypress .guardar":function(e) {
        if (e.keyCode == 13) this.guardar();
      },
    },
    initialize:function(options) {
      _.bindAll(this);
      this.options = options;
      $(this.el).html(this.template(this.model.toJSON()));
      this.cargar_categorias_entradas();
    },
    cargar_categorias_entradas: function() {
      var self = this;
      var r = workspace.crear_select(categorias_noticias,"",self.model.get("id_categoria"));
      this.$("#web_estructura_config_entradas_categorias").html(r);
    },
    guardar: function() {
      console.log(this.options.objeto);
      $(this.options.objeto).data("id_categoria",this.model.get("id_categoria"));
      $(this.options.objeto).data("offset",this.model.get("offset"));
      $(this.options.objeto).data("estilo",this.model.get("estilo"));
      this.cerrar();
    },
    cerrar: function() {
      $('.modal:last').modal('hide');
    },
  });
})(app.views, app.models);