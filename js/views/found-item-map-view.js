/*global define*/
define([
	'underscore',
	'backbone',
	'jquery',
	'handlebars',
	'collections',
	'text!found-item-map-template',
	'async!https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'
], function(_, Backbone, $, Handlebars, Bases09, FoundItemMapTemplate, _Gmaps) {
	'use strict';

	var FoundItemMapView = Backbone.View.extend({
		// Bind application to the existing element
		el: '#map',

		// Total found items template
		itemTemplate: Handlebars.compile(FoundItemMapTemplate),

		initialize: function() {
			// Check if we have to show map (have results with coordinates)
			var showMap = false;

			for (var i = 0; i < Bases09.length; i++) {
				if (Bases09.models[i].get('lat')) {
					showMap = true;
					break;
				}
			}

			if (!showMap) {
				// We haven't got results with coordinates
				return false;
			}

			// Display map
			$('#map').show();

			// Initialize map
			var map = new google.maps.Map(document.getElementById('map'), {
			    zoom: 11,
			    center: new google.maps.LatLng(50.4505, 30.523),
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			});

			// Add markers on map
			// https://developers.google.com/maps/documentation/javascript/overlays?hl=ru
			for (var i = 0; i < Bases09.length; i++) {
				var model = Bases09.models[i],
					lat = parseFloat(model.get('lat')),
					lon = parseFloat(model.get('lon'));

				if (lat && lon) {
					var _this = this;

					// If we have several markers in same place we will se only last marker
					// So we will show them with some deviation
					lat = Math.random() < 0.5 ? (lat - this.getRandom()) : (lat + this.getRandom());
					lon = Math.random() < 0.5 ? (lon - this.getRandom()) : (lon + this.getRandom()); 

					(function () {
						var infoWindowContent = _this.itemTemplate(model.toJSON());

						var infoWindow = new google.maps.InfoWindow({
						    content: infoWindowContent,
						    maxWidth: 250
						});

						var latlng = new google.maps.LatLng(lat, lon);
						
						var marker = new google.maps.Marker({
						    position: latlng,
						    map: map,
						    title: model.get('phoneNumber')
						});

						google.maps.event.addListener(marker, 'click', function() {
							infoWindow.open(map, marker);
						});
					})();	
				}
			}
		},

		getRandom: function() {
			var minRandom = 0,
				maxRandom = 0.00002,
				random = Math.random() * (maxRandom - minRandom) + minRandom;

			return random;
		}
	});

	return FoundItemMapView;
});