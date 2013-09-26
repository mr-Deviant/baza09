/*global define*/
define([
	'underscore',
	'backbone',
	'jquery',
	'validate',
	'autocomplete',
	'handlebars',
	'collections',
	'found-item-view',
	'found-item-map-view',
	'text!total-found-template'
], function(_, Backbone, $, _Validate, _Autocomplete, Handlebars, Bases09, FoundItemView, FoundItemMapView, TotalFoundTemplate) {
	'use strict';

	var AppView = Backbone.View.extend({
		// Backbone will bind events to this element
		el: '.container',

		// Template for total found statistics
		totalFoundTemplate: Handlebars.compile(TotalFoundTemplate),

		// The DOM events specific to an item
		events: {
			'click #searchButton': 'performSearch',
			'click #up': 'scrollUp'
		},

		// Bind to the relevant events on the collection, when items are changed
		initialize: function() {console.log('app init');
			// Add event is fired for each model in collection
			// window.app.bases09.on('add', function(model) {
			// 	this.addOne(model);
			// 	//this.render();
			// }, this);
			this.listenTo(Bases09, 'add', this.addOne);

			//window.app.bases09.on('reset', this.reset, this);
			this.listenTo(Bases09, 'reset', this.reset);

			// Set fields autocomplete
			this.setFieldsAutocomplete();

			// Set form validation rules
			this.setFormValidator();
		},

		reset: function() {
			$('#found-items').html('');
		},

		// Update total found statistics
		render: function() {
			var total = Bases09.length;
			
			// Hide loading image below Search button
			$('.loader').hide();

			if (total) {
				// Update total found statistics
				$('#total-found').html(this.totalFoundTemplate({
					total: total
				}));
				$('#results-table, #total-found').show();

				var foundItemMapView = new FoundItemMapView();
			} else {
				$('#no-results').show();
			}
		},

		// Add one found result
		addOne: function(model) {
			var foundItemView = new FoundItemView({model: model});
		},

		// Make server call for search results
		performSearch: function(e) {
			var _this = this;

			e.preventDefault();

			// Check form if all fields are filled
			if (!$('#form').valid()) {
				return false;
			}

			// Call form submitHandler function for normalazing form
			$('#form').submit();

			// Hide previous results
			$('#results-table, #total-found, #map, #no-results').hide();

			// Show loading image below Search button
			$('.loader').show();

			// Get inputted data
			var data = $('form').serialize();

			// Delete previous models from collection
			Bases09.reset();

			// Send data to server
			Bases09.fetch({
				data: data,
				success: function(){
					_this.render();
				}
			});
		},

		scrollUp: function(e) {
			e.preventDefault();

			scrollTo(0, 0);
		},

		setFieldsAutocomplete: function() {
			$(document).ready(function() {
			   $('#second-name').autocomplete({
			   		serviceUrl: '/action.php?action=secondNameAutocomplete',
			   		minChars: 3
			   	});

			   $('#street').autocomplete({
			   		serviceUrl: '/action.php?action=streetAutocomplete',
			   		minChars: 3
			   	});
			}); 
		},

		setFormValidator: function() {
			//$(document).ready(function() {
				var $phoneNumber = $('#phone-number'),
					$secondName  = $('#second-name'),
					$firstName   = $('#first-name'),
					$middleName  = $('#middle-name'),
					$street      = $('#street'),
					$house       = $('#house'),
					$room        = $('#room');

				$('form').validate({
					rules: {
						'phone-number': {
							required: {
								depends: function(element) {
									// All fields are empty
									return $phoneNumber.val() == ''
										&& $secondName.val()  == ''
										&& $firstName.val()   == ''
										&& $middleName.val()  == ''
										&& $street.val()      == ''
										&& $house.val()       == ''
										&& $room.val()        == ''
								}
							}
						},
						'second-name': {
							required: {
								depends: function(element) {
									// Phone number & second name are empty, but first or middle name are filled
									return //$phoneNumber.val() == '' && 
										$secondName.val()        == ''
										&& (
											$firstName.val()     != ''
											|| $middleName.val() != ''
										)
								}
							}
						},
						'street': {
							required: {
								depends: function(element) {
									// Phone number & street name are empty, but house or room num are filled
									return //$phoneNumber.val()    == '' &&
										$street.val()      == ''
										&& (
											$house.val()   != ''
											|| $room.val() != ''
										)
								}
							}
						},
						'house': {
							required: {
								depends: function(element) {
									// Phone number & house num are empty, but room num is filled
									return //$phoneNumber.val() == '' &&
										$house.val()   == ''
										&& $room.val() != ''
								}
							}
						}
					},
					messages: {
						'phone-number': 'Введите номер телефона',
						'second-name': 'Введите фамилию',
						'street': 'Введите название улицы',
						'house': 'Введите номер дома'
					},
					errorClass: 'help-block',
					errorElement: 'div',
					errorPlacement: function(error, element) {
						if (element.parent().hasClass('input-group')) {
							error.insertAfter(element.parent());	
						} else {
							error.insertAfter(element);
						}
					},
					highlight: function(element) {
						$(element).closest('.form-group').addClass('has-error');
					},
					unhighlight: function (element, errorClass, validClass) {
					    $(element).closest('.form-group').removeClass('has-error');
					},
					// Unhighlight method doesn't fires for not required fields
					// so we must reset this fields manually
					submitHandler: function(form) {
						var validator = $('form').data('validator');

						$('form input').each(function(index) {
							validator.settings.unhighlight(this);
						});
					},
					invalidHandler: function(event, validator) {
						validator.settings.submitHandler();
					},
					onfocusout: false,
			        onkeyup: false
				});
			//});
		}
	});

	return AppView;
});