/*global define*/
define([
	'underscore',
	'backbone',
	'models'
], function(_, Backbone, Models) {
	'use strict';

	// Collections
	var Bases09 = Backbone.Collection.extend({
		model: Models,
		url: '/action.php?action=performSearch'
	});

	return new Bases09();
});