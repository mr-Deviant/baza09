/*global define*/
define([
	'underscore',
	'backbone'
], function(_, Backbone) {
	'use strict';

	var Base09 = Backbone.Model.extend({
		// Default attribute values
		defaults: {
			phoneNumber: '',
			secondName:  '',
			firstName:   '',
			middleName:  '',
			street:      '',
			house:       '',
			room:        '',
			lat:         '',
			lon:         ''
		}
	});

	return Base09;
});