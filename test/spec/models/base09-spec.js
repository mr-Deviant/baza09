define(['models', 'jasmine'], function(base09, Jasmine) {

	return describe('Tests for Base09 model', function() {

	  	it('Can be created with default values for its attributes.', function () {
			var Base09 = new base09();
			expect(Base09.get('phoneNumber')).toBe('');
		});

		it('Fires a custom event when the state changes.', function() {
			var spy = Jasmine.createSpy('-change event callback-');
			var Base09 = new base09();
			Base09.on('change', spy);
			Base09.set({phoneNumber: '0000000'});
			expect(spy).toHaveBeenCalled();
		});

	});

});