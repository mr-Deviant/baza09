/*global define*/
define([
	'underscore',
	'backbone',
	'jquery',
	'app-view'
], function(_, Backbone, $, AppView) {
	'use strict';

	var Router = Backbone.Router.extend({

		initialize: function() {console.log('router init');
			//$(document).ready(function() {
			new AppView();
			//});
		},

		routes: {
			'':       'index',
	    	':phone': 'phone'
		},

		index: function() {
			$('html').removeClass('phone-number').addClass('index');
	    	$('#phone-number').val('');
		},

		phone: function(phone) { // Phone example: 0441234567
			console.log('phone router');
	    	phone = phone.match(/^044(\d+)\.html/)[1];
	    	
	    	$('html').removeClass('index').addClass('phone-number');
	    	$('#phone-number').val(phone);
	    	$('#searchButton').click();
		}

	});

	return Router;
});