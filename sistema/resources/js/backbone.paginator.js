/*globals Backbone:true, _:true, jQuery:true*/
Backbone.Paginator = (function ( Backbone, _, $ ) {
  "use strict";

  var Paginator = {};
  Paginator.version = "0.15";
  
  // @name: clientPager
  //
  // @tagline: Paginator for client-side data
  //
  // @description:
  // This paginator is responsible for providing pagination
  // and sort capabilities for a single payload of data
  // we wish to paginate by the UI for easier browsering.
  //
  Paginator.clientPager = Backbone.Collection.extend({
  
        //DEFAULT PAGINATOR UI VALUES
        defaults_ui: {
            firstPage: 0,
            currentPage: 1,
            perPage: 10,
            totalPages: 0
        },
    
    // Default values used when sorting and/or filtering.
    initialize: function(){
      this._meta = {};
            this.setDefaults(); 
    },
    
    meta: function(prop, value) {
      if (value === undefined) {
        return this._meta[prop]
      } else {
        this._meta[prop] = value;
      }
    },    
    
    // fields: puede ser un string (Ej: "nombre") o un array (Ej: ["nombre","codigo"])
    // filter: string de filtro
    setFilter: function ( fields, filter ) {
      if( fields !== undefined && filter !== undefined ){
        this.filterFields = fields;
        this.lastFilterExpression = this.filterExpression;
        this.filterExpression = filter;
        this.pager();
      }
    },
    
    // Agrega un filtro (setFilter sigue siendo el principal)
    // field: campo del modelo
    // filter: valor de comparacion
    // method: metodo de comparacion. Ej: =, >, <, >=, <=, !=
    addFilter: function(field,filter,method) {
      if( field !== undefined && filter !== undefined ){
        if (method == undefined) method = "=";
        var f = _.find(this.filters,function(e){
          return (e.field == field);
        });
        this.currentPage = 1;
        if (f == undefined) {
          this.filters.push({
            "field":field,
            "filter":filter,
            "method":method,
          });          
        } else {
          f.filter = filter;
          f.method = method;
        }
        this.pager();
      }      
    },
    
    // Borra un filtro especifico
    removeFilter: function(field) {
      var newFilters = _.filter(this.filters,function(e){
        return (e.field != field);
      });
      this.currentPage = 1;
      this.filters = newFilters;
      this.pager();
    },
    
    // Obtiene el valor de un filtro dado
    getFilter: function(field) {
      var f = _.find(this.filters,function(e){
        return (e.field == field);
      });
      return (f !== undefined) ? f.filter : false;
    },
    
    // Borra todos los filtros
    removeAllFilters: function() {
      this.currentPage = 1;
      this.filters = new Array();
      this.pager();
    },
    
    // Devuelve si se estan aplicando filtros o no
    hasAdvancedFilters: function() {
      return (this.filters.length > 0);
    },
    
    // column: nombre de la columna por el cual se ordena
    // direction: "asc" o "desc"
    setSort: function ( column, direction ) {
      if(column !== undefined && direction !== undefined){
        this.lastSortColumn = this.sortColumn;
        this.sortColumn = column;
        this.sortDirection = direction;
        this.pager();
      }
    },    
        
        setDefaults: function() {
            // SET DEFAULT UI SETTINGS
      var self = this;      
      if (!("paginator_ui" in self)) self.paginator_ui = {};  
      
            var options = _.defaults(this.paginator_ui, this.defaults_ui);
      this.filterExpression = "";
      this.totalRecords = 0;
      this.filters = [];
      this.server_api = {};
            
            //UPDATE GLOBAL UI SETTINGS
            _.defaults(this, options); 
        }, 

    sync: function ( method, model, options ) {
        var self = this; 
        
            // SET DEFAULT VALUES
            this.setDefaults(); 
      
      // Some values could be functions, let's make sure
      // to change their scope too and run them
      var queryAttributes = {};
      _.each(self.server_api, function(value, key){
        if( _.isFunction(value) ) {
          value = _.bind(value, self);
          value = value();
        }
        queryAttributes[key] = value;
      });
      
      var queryOptions = _.clone(self.paginator_core);
      _.each(queryOptions, function(value, key){
        if( _.isFunction(value) ) {
          value = _.bind(value, self);
          value = value();
        }
        queryOptions[key] = value;
      });
      
      // Create default values if no others are specified
      queryOptions = _.defaults(queryOptions, {
        timeout: 25000,
        cache: false,
        type: 'GET',
        dataType: 'json'
      });

      queryOptions = _.extend(queryOptions, {
        data: decodeURIComponent($.param(queryAttributes)),
        processData: false,
        url: _.result(queryOptions, 'url')
      }, options);
      
      return $.ajax( queryOptions );
    },

    nextPage: function () {
            if(this.currentPage < this.information.totalPages) {
                this.currentPage = ++this.currentPage;
                this.pager();
      }
    },

    previousPage: function () {
      this.currentPage = --this.currentPage || 1;
      this.pager();
    },

    goTo: function ( page ) {
      if(page !== undefined){
        this.currentPage = parseInt(page, 10);
        this.pager();
      }
    },

    howManyPer: function ( perPage ) {
      if(perPage !== undefined){
        var lastPerPage = this.perPage;
        this.perPage = parseInt(perPage, 10);
        this.currentPage = Math.ceil( ( lastPerPage * ( this.currentPage - 1 ) + 1 ) / perPage);
        this.pager();
      }
    },
    
    // IMPLEMENTAMOS EL PARSE POR DEFECTO
    parse: function (response) {
      if (response.meta != undefined) {
        _.extend(this._meta,response.meta);
      }
      return response.results;
    },
    
    pager: function () {
      var self = this,
        disp = this.perPage,
        start = (self.currentPage - 1) * disp,
        stop = start + disp;
        
      // No utilizamos la coleccion original, sino una copia
      self.sortedAndFilteredModels = self.models;
      
      // Si se esta ordenando con setSort
      if ( this.sortColumn !== "" ) {
        self.sortedAndFilteredModels = self._sort(self.sortedAndFilteredModels, this.sortColumn, this.sortDirection);
      }
      
      // Si se esta aplicando un filtro con setFilter
      if ( this.filterExpression !== "" || this.filters.length > 0) {
        self.sortedAndFilteredModels = self._filter(self.sortedAndFilteredModels, this.filterFields, this.filterExpression);
      }
      this.totalRecords = self.sortedAndFilteredModels.length;
      
      // If the sorting or the filtering was changed go to the first page
      if ( this.lastSortColumn !== this.sortColumn || this.lastFilterExpression !== this.filterExpression || !_.isEqual(this.fieldFilterRules, this.lastFieldFilterRules) ) {
        start = 0;
        stop = start + disp;
        self.currentPage = 1;

        this.lastSortColumn = this.sortColumn;
        this.lastFieldFilterRules = this.fieldFilterRules;
        this.lastFilterExpression = this.filterExpression;
      }      
        
      self.sortedAndFilteredModels = self.sortedAndFilteredModels.slice(start,stop);
      self.info();
      self.trigger("pager");
    },
    
    getFilterCollection: function() {
      // Creamos una coleccion con el array de modelos filtrados
      return new Backbone.Collection(this.sortedAndFilteredModels);
    },

    // You shouldn't need to call info() as this method is used to
    // calculate internal data as first/prev/next/last page...
    info: function () {
      var self = this, info = {}, totalPages = Math.ceil(this.totalRecords / self.perPage);

      info = {
        totalRecords: self.totalRecords,
        currentPage: self.currentPage,
        perPage: this.perPage,
        totalPages: totalPages,
        firstPage: 1,
        lastPage: totalPages,
        previous: false,
        next: false,
      };

      if (self.currentPage > 1) {
        info.previous = self.currentPage - 1;
      }

      if (self.currentPage < info.totalPages) {
        info.next = self.currentPage + 1;
      }

      self.information = info;
      return info;
    },
    
    _filter: function ( models, fields, filter ) {
    
      //  For example, if you had a data model containing cars like { color: '', description: '', hp: '' },
      //  your fields was set to ['color', 'description', 'hp'] and your filter was set
      //  to "Black Mustang 300", the word "Black" will match all the cars that have black color, then
      //  "Mustang" in the description and then the HP in the 'hp' field.
      //  NOTE: "Black Musta 300" will return the same as "Black Mustang 300"

      // We accept fields to be a string, an array or an object
      // but if string or array is passed we need to convert it
      // to an object.
      
      var self = this;
      
      var obj_fields = {};
      
      if( _.isString( fields ) ) {
        obj_fields[fields] = {cmp_method: 'regexp'};
      }else if( _.isArray( fields ) ) {
        _.each(fields, function(field){
          obj_fields[field] = {cmp_method: 'regexp'};
        });
      }else{
        _.each(fields, function( cmp_opts, field ) {
          obj_fields[field] = _.defaults(cmp_opts, { cmp_method: 'regexp' });
        });
      }
      
      fields = obj_fields;
      
      //Remove diacritic characters if diacritic plugin is loaded
      if( _.has(Backbone.Paginator, 'removeDiacritics') && self.useDiacriticsPlugin ){
        filter = Backbone.Paginator.removeDiacritics(filter);
      }
      
      // 'filter' can be only a string.
      // If 'filter' is string we need to convert it to 
      // a regular expression. 
      // For example, if 'filter' is 'black dog' we need
      // to find every single word, remove duplicated ones (if any)
      // and transform the result to '(black|dog)'
      if( (filter === '' || !_.isString(filter)) && self.filters.length == 0 ) {
        return models;
      } else {
        var words = _.map(filter.match(/\w+/ig), function(element) { return element.toLowerCase(); });
        var pattern = "(" + _.uniq(words).join("|") + ")";
        var regexp = new RegExp(pattern, "igm");
      }
      
      var filteredModels = [];

      // We need to iterate over each model
      _.each( models, function( model ) {

        var matchesPerModel = [];
        
        // Primero aplicamos los filtros adicionales
        if (self.filters.length > 0) {
          var pasoFiltro = true;
          for(var i=0;i<self.filters.length;i++) {
            var f = self.filters[i];
            if (f.method == "=") {
              if (model.get(f.field) != f.filter) {
                pasoFiltro = false;
                break;
              }
            }
          }
          if (!pasoFiltro) return;
        }

        // and over each field of each model
        _.each( fields, function( cmp_opts, field ) {

          var value = model.get(field);

          if( value ) {
          
            // The regular expression we created earlier let's us detect if a
            // given string contains each and all of the words in the regular expression
            // or not, but in both cases match() will return an array containing all 
            // the words it matched.
            var matchesPerField = [];
            
            if( _.has(Backbone.Paginator, 'removeDiacritics') && self.useDiacriticsPlugin ){
              value = Backbone.Paginator.removeDiacritics(value.toString());
            }else{
              value = value.toString();
            }

            // Levenshtein cmp
            if( cmp_opts.cmp_method === 'levenshtein' && _.has(Backbone.Paginator, 'levenshtein') && self.useLevenshteinPlugin ) {
              var distance = Backbone.Paginator.levenshtein(value, filter);

              _.defaults(cmp_opts, { max_distance: 0 });

              if( distance <= cmp_opts.max_distance ) {
                matchesPerField = _.uniq(words);
              }

            // Default (RegExp) cmp
            }else{
              matchesPerField = value.match( regexp );
            }

            matchesPerField = _.map(matchesPerField, function(match) {
              return match.toString().toLowerCase();
            });

            _.each(matchesPerField, function(match){
              matchesPerModel.push(match);
            });

          }

        });

        // We just need to check if the returned array contains all the words in our
        // regex, and if it does, it means that we have a match, so we should save it.
        matchesPerModel = _.uniq( _.without(matchesPerModel, "") );

        if(  _.isEmpty( _.difference(words, matchesPerModel) ) ) {
          filteredModels.push(model);
        }
        
      });

      return filteredModels;
    },
    
    _sort: function ( models, sort, direction ) {
      if (direction == "desc") {
        var salida = _.sortBy(models,function(e){
          return (isNaN(e.get(sort)) ? e.get(sort) : parseInt(e.get(sort)));
        });
        return salida.reverse();
      } else {
        return _.sortBy(models,function(e){
          return (isNaN(e.get(sort)) ? e.get(sort) : parseInt(e.get(sort)));
        });
      }
      
    },    

  });  

  // @name: requestPager
  //
  // Paginator for server-side data being requested from a backend/API
  //
  // @description:
  // This paginator is responsible for providing pagination
  // and sort capabilities for requests to a server-side
  // data service (e.g an API)
  //
  Paginator.requestPager = Backbone.Collection.extend({
    
    initialize: function() {
      this._meta = {};
            //this.server_api = {};
    },

    sync: function ( method, model, options ) {

      var self = this;
      
      if (!("paginator_ui" in self)) self.paginator_ui = {};  

      // Create default values if no others are specified
      _.defaults(self.paginator_ui, {
        firstPage: 1,
        currentPage: 1,
        perPage: 10,
        totalPages: 10
      });

      // Change scope of 'paginator_ui' object values
      _.each(self.paginator_ui, function(value, key) {
        if( _.isUndefined(self[key]) ) {
          self[key] = self.paginator_ui[key];
        }
      });

      // Some values could be functions, let's make sure
      // to change their scope too and run them
      if (!("server_api" in self)) self.server_api = {};  
      var queryAttributes = {
        filter: '',
        limit: function() { 
          return (self.currentPage-1) * self.perPage 
        },
        offset: function() { 
          return self.perPage; 
        },
        order_by: (self.order_by != undefined) ? self.order_by : "",
        order: (self.order != undefined) ? self.order : "",
      };
      _.each(self.server_api, function(value, key){
        if( _.isFunction(value) ) {
          value = _.bind(value, self);
          value = value();
        }
        queryAttributes[key] = value;
      });
      
      var queryOptions = _.clone(self.paginator_core);
      _.each(queryOptions, function(value, key){
        if( _.isFunction(value) ) {
          value = _.bind(value, self);
          value = value();
        }
        queryOptions[key] = value;
      });
      
      // Create default values if no others are specified
      queryOptions = _.defaults(queryOptions, {
        timeout: 25000,
        cache: false,
        type: 'GET',
        dataType: 'json'
      });

      // Allows the passing in of {data: {foo: 'bar'}} at request time to overwrite server_api defaults
      if( options.data ){
        options.data = decodeURIComponent($.param(_.extend(queryAttributes,options.data)));
      }else{
        options.data = decodeURIComponent($.param(queryAttributes));
      }

      queryOptions = _.extend(queryOptions, {
        processData: false,
        url: _.result(queryOptions, 'url')
      }, options);
      
      this.url = queryOptions.url;
      
      return $.ajax( queryOptions );

    },

    nextPage: function ( options ) {
      if ( this.currentPage !== undefined ) {
        this.currentPage += 1;
        return this.pager( options );
      } else {
        var response = new $.Deferred();
        response.reject();
        return response.promise();
      }
    },

    previousPage: function ( options ) {
      if ( this.currentPage !== undefined ) {
        this.currentPage -= 1;
        return this.pager( options );
      } else {
        var response = new $.Deferred();
        response.reject();
        return response.promise();
      }
    },

    updateOrder: function ( column ) {
      if (column !== undefined) {
        this.sortField = column;
        this.pager();
      }

    },

    goTo: function ( page, options ) {
      if ( page !== undefined ) {
        this.currentPage = parseInt(page, 10);
        if (options == undefined) options = {};
        return this.pager( options );
      } else {
        var response = new $.Deferred();
        response.reject();
        return response.promise();
      }
    },

    howManyPer: function ( count ) {
      if( count !== undefined ){
        this.currentPage = this.firstPage;
        this.perPage = count;
        this.pager();        
      }
    },

    setManyPer: function(count) {
      if( count !== undefined ){
        this.currentPage = this.firstPage;
        this.perPage = count;
      }
    },
    
    setSort: function (column,direction) {
      this.order_by = column;
      this.order = direction;
    },

    info: function () {

      var info = {
        // If parse() method is implemented and totalRecords is set to the length
        // of the records returned, make it available. Else, default it to 0
        totalRecords: this.totalRecords || 0,

        currentPage: this.currentPage,
        firstPage: this.firstPage,
        totalPages: this.totalPages,
        lastPage: this.totalPages,
        perPage: this.perPage
      };

      this.information = info;
      return info;
    },
    
    meta: function(prop, value) {
      if (value === undefined) {
        return this._meta[prop]
      } else {
        this._meta[prop] = value;
      }
    },    

    // fetches the latest results from the server
    pager: function ( options ) {
      if ( !_.isObject(options) ) {
        options = {};
      }
      options.timeout = 9999999;
      return this.fetch( options );
    },

    // IMPLEMENTAMOS EL PARSE POR DEFECTO
    parse: function (response) {
      var lista = response.results;
      this.totalPages = Math.ceil(response.total / this.perPage);
      this.totalRecords = parseInt(response.total);
      if (response.meta != undefined) {
        _.extend(this._meta,response.meta);
      }
      this.trigger("pager");
      return lista;
    }

  });

  return Paginator;

}( Backbone, _, jQuery ));

Backbone.Router.prototype.before = function () {};
Backbone.Router.prototype.after = function () {};

Backbone.Router.prototype.route = function (route, name, callback) {
  if (!_.isRegExp(route)) route = this._routeToRegExp(route);
  if (_.isFunction(name)) {
    callback = name;
    name = '';
  }
  if (!callback) callback = this[name];

  var router = this;

  Backbone.history.route(route, function(fragment) {
    var args = router._extractParameters(route, fragment);

    router.before.apply(router, arguments);
    callback && callback.apply(router, args);
    router.after.apply(router, arguments);

    router.trigger.apply(router, ['route:' + name].concat(args));
    router.trigger('route', name, args);
    Backbone.history.trigger('route', router, name, args);
  });
  return this;
};
