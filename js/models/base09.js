var app = app || {};

(function(){
	'use strict';

	app.Base09 = Backbone.Model.extend({
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
})();