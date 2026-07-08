(function ( app ) {

  app.views.SindiBonosView = app.mixins.View.extend({

   template: _.template($("#sindi_bonos_template").html()),

    myEvents: {
      "keypress #buscador_codigo":function(e) {
        if (e.which == 13) this.buscar_por_codigo();
      },
      "click #consultas_link":"cargar_consultas",
      "click #practicas_link":"cargar_practicas",
      "click #recetarios_link":"cargar_recetarios",
      "click #reintegros_link":"cargar_reintegros",
      "click .btnbuscar":"buscar2",
    },

    buscar_por_codigo: function(callback) {
      callback = (typeof callback != "undefined") ? callback : null;
      var codigo = this.$("#buscador_codigo").val();
      window.afiliado = null;
      $.ajax({
        "url":"sindi_afiliados/function/buscar_por_codigo/",
        "type":"post",
        "data":{
          "codigo":codigo
        },
        "dataType":"json",
        "success":function(r) {
          if (r.error == 1) {
            alert(r.mensaje);
            return;
          }
          $("#buscador_nombre").val(r.nombre);
          window.afiliado = r;
          if (callback != null) callback();
        }
      })
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      $(this.el).html(this.template());
      this.render();
    },

    render: function() {
      this.vista_activa = null;
      this.cargar_consultas();
      if (typeof window.afiliado != "undefined" || window.afiliado != null) {
        this.$("#buscador_nombre").val(window.afiliado.nombre);
        this.$("#buscador_codigo").val(window.afiliado.codigo+"-"+window.afiliado.identificador);
      }
    },

    cargar_consultas: function() {
      var self = this;
      var permiso = control.check("sindi_bonos");
      var consultas_tab = new app.views.SindiConsultasTableView({
        collection: new app.collections.SindiConsultas(),
        permiso: permiso,
        sindi_bonos: self,
      });
      this.$("#consultas_tab").html(consultas_tab.el);
      this.vista_activa = consultas_tab;
    },

    cargar_practicas: function() {
      var self = this;
      var permiso = control.check("sindi_bonos");
      var practicas_tab = new app.views.SindiPracticasTableView({
        collection: new app.collections.SindiPracticas(),
        permiso: permiso,
        sindi_bonos: self,
      });
      this.$("#practicas_tab").html(practicas_tab.el);
      this.vista_activa = practicas_tab;
    },

    cargar_reintegros: function() {
      var self = this;
      var permiso = control.check("sindi_bonos");
      var reintegros_tab = new app.views.SindiReintegrosTableView({
        collection: new app.collections.SindiReintegros(),
        permiso: permiso,
        sindi_bonos: self,
      });
      this.$("#reintegros_tab").html(reintegros_tab.el);
      this.vista_activa = reintegros_tab;
    },

    cargar_recetarios: function() {
      var self = this;
      var permiso = control.check("sindi_bonos");
      var recetarios_tab = new app.views.SindiRecetariosTableView({
        collection: new app.collections.SindiRecetarios(),
        permiso: permiso,
        sindi_bonos: self,
      });
      this.$("#recetarios_tab").html(recetarios_tab.el);
      this.vista_activa = recetarios_tab;
    },

    buscar2: function() {
      var self = this;
      var coleccion = new app.collections.SindiAfiliados();
      var v = new app.views.SindiAfiliadosBuscarView({
        collection: coleccion,
        permiso: control.check("sindi_afiliados"),
        view: self,
      });
      crearLightboxHTML({
        "html":v.el,
        "width":900,
        "height":140,
        "callback":function() {
 
        }
      });
    }

  });
})(app);