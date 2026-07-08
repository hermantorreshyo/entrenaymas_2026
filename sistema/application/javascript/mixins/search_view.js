(function ( mixins ) {

	mixins.SearchView = Backbone.View.extend({

		basic_search_mark: "",

		template: _.template($('#basic_search_template').html()),

		initialize: function(options) {
			this.options = options;
			this.render();
		},

		events: {
			"keypress .basic_search": "searchOnEnter",
			"click button": "buscar",
            // Si estamos buscando articulos, y hacemos con la flechita de abajo
            "keyup .basic_search":function(e) {
                if (e.which == 40)  { $("tbody tr .radio").first().focus() }
            }            
		},

		render: function() {
			var html = this.template({
				"basic_search_mark": this.options.basic_search_mark
			});
			this.$el.html(html);
		},

		buscar: function() {
			var value = this.$(".basic_search").val();
			this.collection.server_api.filter = value;
			this.collection.pager();
		},

		searchOnEnter: function(e) {
			if (e.keyCode == 13) this.buscar();
		},

	});
})(app.mixins);