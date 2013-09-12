define(['collections'], function(Bases09) {

	return describe('Tests for Bases09 collection', function() {

	  	it('Can add Model instances as objects and arrays.', function() {
			expect(Bases09.length).toBe(0);
			Bases09.add({phoneNumber: '0000001'});
			expect(Bases09.length).toBe(1);
			Bases09.add([
				{phoneNumber: '0000002'},
				{phoneNumber: '0000003'}
			]);
			// how many are there in total now?
			expect(Bases09.length).toBe(3);
		});

	});

});