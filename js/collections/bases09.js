var app = app || {};

(function() {
	'use strict';

	// Collections
	var Bases09 = Backbone.Collection.extend({
		model: app.Base09,
		url: '/action.php?action=performSearch'
	});

	app.bases09 = new Bases09();

})();