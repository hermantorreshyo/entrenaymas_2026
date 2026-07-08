(function ( app ) {

  app.views.SindiLimitesView = app.mixins.View.extend({

   template: _.template($("#sindi_auditor_limites_template").html()),

    myEvents: {
      "click #tab_consulta_link":"cargar_ca",
      "click #tab_recetario_link":"cargar_ra",
      "click #tab_practica_link":"cargar_la",

      "click #ra_link":"cargar_ra",
      "click #ra70_link":"cargar_ra70",
      "click #ra100_link":"cargar_ra100",
      "click #la_link":"cargar_la",
      "click #lce_link":"cargar_lce",
      "click #ltp_link":"cargar_ltp",
    },

    initialize : function (options) {
      _.bindAll(this); // Para que this pueda ser utilizado en las funciones
      $(this.el).html(this.template());
      this.render();
    },

    render: function() {
      this.vista_activa_limites = null;
      this.cargar_ca();
    },

    cargar_ca: function() {
      var coleccion = new app.collections.SindiLimitesAfiliados();
      coleccion.server_api = {
        tipo: 1,
      };
      var permiso = control.check("sindi_limites");
      var ca_tab = new app.views.SindiLimitesAfiliadosTableView({
        collection: coleccion,
        permiso: permiso,
        subtab_activo: 1,
        tipo: 1,
      });
      this.$("#tab1_limites").html(ca_tab.el);
      this.vista_activa_limites = ca_tab;
    },

    cargar_ra: function() {
      var coleccion = new app.collections.SindiLimitesAfiliados();
      coleccion.server_api = {
        tipo: 2,
      };
      var permiso = control.check("sindi_limites");
      var ra_tab = new app.views.SindiLimitesAfiliadosTableView({
        collection: coleccion,
        permiso: permiso,
        tipo: 2,
        subtab_activo: 2,
      });
      this.$("#tab2_limites").html(ra_tab.el);
      this.vista_activa_limites = ra_tab;
    },

    cargar_ra70: function() {
      var coleccion = new app.collections.SindiLimitesAfiliados();
      coleccion.server_api = {
        tipo: 3,
      };
      var permiso = control.check("sindi_limites");
      var ra70_tab = new app.views.SindiLimitesAfiliadosTableView({
        collection: coleccion,
        permiso: permiso,
        tipo: 3,
        subtab_activo: 2,
      });
      this.$("#tab3_limites").html(ra70_tab.el);
      this.vista_activa_limites = ra70_tab;
    },

    cargar_ra100: function() {
      var coleccion = new app.collections.SindiLimitesAfiliados();
      coleccion.server_api = {
        tipo: 4,
      };
      var permiso = control.check("sindi_limites");
      var ra100_tab = new app.views.SindiLimitesAfiliadosTableView({
        collection: coleccion,
        permiso: permiso,
        tipo: 4,
        subtab_activo: 2,
      });
      this.$("#tab4_limites").html(ra100_tab.el);
      this.vista_activa_limites = ra100_tab;
    },

    cargar_la: function() {
      var coleccion = new app.collections.SindiLimitesAfiliados();
      coleccion.server_api = {
        tipo: 5,
      };
      var permiso = control.check("sindi_limites");
      var la_tab = new app.views.SindiLimitesAfiliadosTableView({
        collection: coleccion,
        permiso: permiso,
        tipo: 5,
        subtab_activo: 5,
      });
      this.$("#tab5_limites").html(la_tab.el);
      this.vista_activa_limites = la_tab;
    },

    cargar_lce: function() {
      var permiso = control.check("sindi_limites");
      var lce_tab = new app.views.SindiLimitesCondicionesEspecialesTableView({
        collection: new app.collections.SindiLimitesCondicionesEspeciales(),
        permiso: permiso
      });
      this.$("#tab6_limites").html(lce_tab.el);
      this.vista_activa_limites = lce_tab;
    },

    cargar_ltp: function() {
      var permiso = control.check("sindi_limites");
      var ltp_tab = new app.views.SindiLimitesTiposPracticasTableView({
        collection: new app.collections.SindiLimitesTiposPracticas(),
        permiso: permiso
      });
      this.$("#tab7_limites").html(ltp_tab.el);
      this.vista_activa_limites = ltp_tab;
    },


  });
})(app);