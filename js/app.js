'use strict';

require.config({
	baseUrl: 'js',
	// Disable files caching
	urlArgs: 'bust=' + (new Date()).getTime(),
	// If loded modules don't use "define" then throw error
	// enforceDefine: true,
	paths: {
		'text':                    'libs/require/text',
		'async':                   'libs/require/async',
		'underscore':              'libs/underscore-min',
		'backbone':                'libs/backbone-min',
		'jquery':                  '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min',
		'validate':                'libs/jquery.validate.min',
		'autocomplete':            'libs/jquery.autocomplete.min',
		'handlebars':              'libs/handlebars',
		'bootstrap':               'libs/bootstrap.min',
		//'gmaps':                   'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false',
		'models':                  'models/base09',
		'collections':             'collections/bases09',
		'app-view':                'views/app-view',
		'found-item-view':         'views/found-item-view',
		'found-item-map-view':     'views/found-item-map-view',
		'router':                  'routers/router',
		'found-item-template':     'templates/found-item-template.html',
		'total-found-template':    'templates/total-found-template.html',
		'found-item-map-template': 'templates/found-item-map-template.html'
	},
	shim: {
		'underscore': {
			exports: '_'
		},
		'backbone': {
			deps: [
				'underscore',
				'jquery'
			],
			exports: 'Backbone'
		},
		'validate': {
			deps: ['jquery'],
			exports: 'jQuery.fn.validate'
		},
		'autocomplete': {
			deps: ['jquery'],
			exports: 'jQuery.fn.autocomplete'
		},
		'handlebars': {
			exports: 'Handlebars'
		}
	}
});

require(['underscore', 'backbone', 'jquery', 'router'], function(_, Backbone, $, Router) {
//define(['underscore', 'backbone', 'jquery', 'app-view', 'router'], function(_, Backbone, $, AppView, Router) {
	'use strict';

	var router = new Router();

	// Start using History API, use / instead of #
	Backbone.history.start({pushState: true});

});