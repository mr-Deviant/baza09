define(['jquery', 'jasmine-html', 'jasmine-jquery', 'app-view'], function($, Jasmine, JasmineJquery, AppView) {

	// Change fixtures path
	Jasmine.getFixtures().fixturesPath = 'fixtures/';

	// Store in $.ajax.lastCall property last ajax request (emulate jasmine mostRecentCall property)
	$(document).ajaxComplete(function(ev, jqXHR, settings) {
	    $.ajax.lastCall = jqXHR;
	});

	return describe('App-view view', function() {

		beforeEach(function() {

			// Add missing markup
			loadFixtures('app-fixture.html');

			// Init view
			new AppView();

		});

		// Check suggestions list

	  	it('Should perform ajax request when user type beginning of second name', function() {

	  		// spyOn($, 'ajax'); get error: $.ajax is undefined

	  		// spyOn($, 'ajax').andCallThrough(); get incorrect response, responseText is html code of fixture

	  	    // Trigger real ajax request
			$('#second-name').val('Пет').keyup();

			// Wait for a second, hope it would be enought for ajax request
			waits(1000);

			runs(function() {
				expect($.ajax.lastCall.status).toEqual(200);
			});
			
		});

		it('Ajax response should contain json list with second names', function() {
			//spyOn($, 'ajax').andCallFake(...); get error $.ajax.done is undefined

			expect($.parseJSON($.ajax.lastCall.responseText).suggestions.length).toBeGreaterThan(0);
		});

		it('Suggestions list should be inserted into DOM', function() {

			// Suggestions list must exists, because it was added to body element ans wasn't deleted automatically
			expect($('.autocomplete-suggestions')).toBeVisible();

			// Remove suggestions list
			$('.autocomplete-suggestions').remove();

		});

		// Check form validation

		it('Empty form shouldn\'t be submitted & error message should be displayed', function() {

			// All fields are empty by default
			// Perform form submit
			$('#searchButton').click();

			expect($('#phone-number').closest('.form-group')).toHaveClass('has-error');

		});

		it('Form with phone number should be successfully sended', function() {

			// Fill phone number field with valid number
			$('#phone-number').val('2000000');

			var spyEvent = spyOnEvent('#form', 'submit');

			// Perform form submit
			$('#searchButton').click();

			expect(spyEvent).toHaveBeenTriggered();

		});

		// Check search

		// Not ideal, but i can't make spyOn make work

		it('Should return results when user search form by second name', function() {

		    // Fill phone number field with valid number
			$('#second-name').val('Пєтров');

			// Perform form submit
			$('#searchButton').click();

			// Wait for a 2 seconds, hope it would be enought for ajax request
			waits(2000);

			runs(function() {
				expect($.parseJSON($.ajax.lastCall.responseText).length).toBeGreaterThan(0);
			});
			
		});

		// it('Loader should be hidden after displaying results', function() {

		// 	expect($('.loader')).toBeHidden();
			
		// });

		// it('Table with results should be updated', function() {

		// 	// Fill phone number field with valid number
		// 	$('#second-name').val('Пєтров');

		// 	// Perform form submit
		// 	$('#searchButton').click();

		// 	// Wait for a 2 seconds, hope it would be enought for ajax request
		// 	waits(2000);

		// 	runs(function() {
		// 		expect($('#found-items tr').length).toBeGreaterThan(0);
		// 	});
			
		// });

	});

});