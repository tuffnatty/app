require(['jquery', 'wikia.nirvana', 'wikia.leaflet'], function( $, nirvana, L) {
	'use strict';

	$(function (){
		var map = null,
			popup = null,
			setup = {},
			defaultSetup = {
				container: 'interactive_map',
				mapType: 'openstreetmap',
				zoom: 2,
				fullscreenControl: true,
				contextmenu: true,
				contextmenuItems: [
					{
						text: 'Add new point',
						callback: function (event) {
							addPoint(event);
						}
					}, {
						text: 'Center map here',
						callback: function(event) {
							map.panTo(event.latlng);
						}
					}, '-', {
						text: 'Zoom in',
						callback: function() {
							map.zoomIn();
						}
					}, {
						text: 'Zoom out',
						callback: function() {
							map.zoomOut();
						}
					}
				]
			};

		function addPoint(event) {
			popup = L.popup({
				closeButton: true,
				closeOnClick: false
			})
				.setLatLng(event.latlng)
				.setContent($('#add_poi_template').html())
				.openOn(map);
		}

		function submitPoint(formData) {
			nirvana.sendRequest({
				controller: 'WikiaInteractiveMaps',
				method: 'createPoint',
				data: formData,
				callback: onCreatePointSuccess,
				onErrorCallback: onCreatePointError
			});
		}

		function onCreatePointSuccess(data) {
			//TODO: Add point on the map
		}

		function onCreatePointError(data) {
			//TODO: Show error message
		}

		function addPointOnMap(point) {
			var marker = L.marker([ poi.y, poi.x ], {
				icon: icons[poi.pointType || 0],
				riseOnhover: true
			})
				.bindPopup('<h3>' + poi.title + '</h3>')
				.addTo(map);

			return marker;
		}

		function loadMarkers(mapId) {
			clearMarkers();
			$.get('/marker/' + mapId, function (data) {
				data.forEach(function(point) {
					markers.push(addPointOnMap(point));
				});
			});
		}

		function init(customSetup) {
			setup = $.extend(false, defaultSetup, customSetup );
			map = L.map(setup.container, {
				center: [-50, 0],
				zoom: setup.zoom,
				fullscreenControl: setup.fullscreenControl,
				contextmenu: setup.contextmenu,
				contextmenuItems: setup.contextmenuItems
			});
		}

		init({});
	});
})