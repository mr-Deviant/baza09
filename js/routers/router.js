/*global define*/
define([
	'underscore',
	'backbone',
	'jquery',
	'app-view'
], function(_, Backbone, $, AppView) {
	'use strict';

	var Router = Backbone.Router.extend({

		routes: {
			'':       'index',
	    	':phone': 'phone'
		},

		index: function() {
			$('html').removeClass('phone-number').addClass('index');
	    	$('#phone-number').val('');
		},

		phone: function(phone) { // Phone example: 0441234567
	    	phone = phone.match(/^044(\d+)\.html/)[1];
	    	
	    	$('html').removeClass('index').addClass('phone-number');
	    	$('#phone-number').val(phone);
	    	$('#searchButton').click();
		}

	});

	return Router;
});