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

		initialize: function() {
			// Add row to search results table
	    	this.$el.append(this.itemTemplate(this.model.toJSON()));

	    	return this;
		}
	});

	return FoundItemView;
});