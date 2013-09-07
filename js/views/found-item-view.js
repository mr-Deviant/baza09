var app = app || {};

(function($){
	'use strict';

	app.FoundItemView = Backbone.View.extend({
		// Bind application to the existing element
		el: '#found-items',

		// Total found items template
		itemTemplate: Handlebars.compile($('#found-item-template').html()),

		initialize: function() {
			// Add row to search results table
	    	this.$el.append(this.itemTemplate(this.model.toJSON()));

	    	return this;
		}
	});
})(jQuery);