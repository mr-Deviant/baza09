require.config({
	baseUrl: '../js',
	// Disable files caching
	urlArgs: 'cb=' + Math.random(),
	paths: {
		'jasmine':                 '../test/lib/jasmine/jasmine',
		'jasmine-html':            '../test/lib/jasmine/jasmine-html',
		'jasmine-jquery':          '../test/lib/jasmine-jquery',

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
		'found-item-template':     'templates/found-item-template.html',
		'total-found-template':    'templates/total-found-template.html',
		'found-item-map-template': 'templates/found-item-map-template.html'
	},
	shim: {
		'jasmine': {
			exports: 'jasmine'
		},
		'jasmine-html': {
			deps: ['jasmine'],
			exports: 'jasmine'
		},
		'jasmine-jquery': {
			deps: ['jquery', 'jasmine-html']
		},
		'jasmine-ajax': {
			deps: ['jquery', 'jasmine-html']
		},

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

require(['jquery', 'jasmine-html'], function($, jasmine) {

	var jasmineEnv = jasmine.getEnv();
	jasmineEnv.updateInterval = 1000;

	var htmlReporter = new jasmine.HtmlReporter();

	jasmineEnv.addReporter(htmlReporter);

	jasmineEnv.specFilter = function(spec) {
		return htmlReporter.specFilter(spec);
	};

	var specs = [];

	// specs.push('../test/spec/models/base09-spec');
	// specs.push('../test/spec/collections/bases09-spec');
	specs.push('../test/spec/views/app-view-spec');

	$(function() {
		require(specs, function() {
			jasmineEnv.execute();
		});
	});
});