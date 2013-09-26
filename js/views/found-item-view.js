/*global define*/
define([
	'underscore',
	'backbone',
	'jquery',
	'handlebars',
	'text!found-item-template'
], function(_, Backbone, $, Handlebars, FoundItemTemplate) {
	'use strict';

	var FoundItemView = Backbone.View.extend({
		// Bind application to the existing element
		el: '#found-items',

		// Total found items template
		itemTemplate: Handlebars.compile(FoundItemTemplate),

		// The DOM events specific to an item
		events: {
			'click tr': 'goToPhonePage'
		},

		initialize: function() {
			// Add row to search results table
	    	this.$el.append(this.itemTemplate(this.model.toJSON()));

	    	return this;
		},

		goToPhonePage: function(e) {
			e.preventDefault();
			// Get phone number of clicked row
			var phoneNumber = $(e.target).parent().find(':first').html(),
				href = '/044' + phoneNumber + '.html';

			// Go to phone page
			Backbone.history.navigate(href, true);
		}
	});

	return FoundItemView;
});