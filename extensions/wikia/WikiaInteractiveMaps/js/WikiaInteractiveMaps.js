require(['jquery', 'wikia.nirvana', 'wikia.leaflet', 'wikia.window'], function( $, nirvana, L, window) {
	'use strict';

	$(function (){
		var map = null,
			popup = null,
			setup = {},
			markers = [],
			defaultSetup = {
				container: 'interactive_map',
				mapType: 'openstreetmap',
				zoom: 2,
				minZoom: 0,
				maxZoom: 18,
				attribution: '',
				fullscreenControl: true,
				contextmenu: true,
				contextmenuItems: [
					{
						text: $.msg('wikia-interactive-maps-add-new-point'),
						callback: function (event) {
							addPoint(event);
						}
					}, {
						text: $.msg('wikia-interactive-maps-center-map-here'),
						callback: function(event) {
							map.panTo(event.latlng);
						}
					}, '-', {
						text: $.msg('wikia-interactive-maps-zoom-in'),
						callback: function() {
							map.zoomIn();
						}
					}, {
						text: $.msg('wikia-interactive-maps-zoom-out'),
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
			var marker = L.marker([ point.y, point.x ], {
				// FIXME: Add custom icons once we have them
				// icon: icons[poi.pointType || 0],
				riseOnHover: true
			})
				.bindPopup('<h3>' + point.title + '</h3>')
				.addTo(map);

			return marker;
		}

		function clearMarkers() {
			markers.forEach(function(marker){
				marker.unbindPopup();
				map.removeLayer(marker);
			});
			markers = [];
		}

		function onGetPointsError(data) {
			// TODO: Error on getting points
		}

		function getPoints(mapId) {
			clearMarkers();
			nirvana.sendRequest({
				controller: 'WikiaInteractiveMaps',
				method: 'getPoints',
				type: 'GET',
				format: 'json',
				data: {
					mapId: mapId
				},
				callback: function(data) {
					if (data.result == 'ok') {
						data.points.forEach(function(point) {
							markers.push(addPointOnMap(point));
						});
					}
				},
				onErrorCallback: onGetPointsError
			});
		}

		function addMapLayer(map, setup) {
			var mapTypes = {
				1: function() {
					// Custom map
					return L.tileLayer(setup.pathTemplate, {
						minZoom: setup.minZoom,
						maxZoom: setup.maxZoom,
						attribution: setup.attribution,
						tms: true,
						noWrap: true
					}).addTo(map);
				},
				2: function() {
					// Open Street Map
					return L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						attribution: setup.attribution || 'Map data OpenStreetMap contributors',
						minZoom: setup.minZoom || 0,
						maxZoom: setup.maxZoom || 12
					}).addTo(map);
				}
			}
			if (mapTypes[setup.mapType]) {
				return mapTypes[setup.mapType]();
			} else {
				throw 'Unknown map type: ' + setup.mapType + ' provided';
			}
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
			addMapLayer(map, setup);
			getPoints(setup.mapId);
		}

		init({
			mapId: window.mapMapId,
			minZoom: window.mapMinZoom,
			maxZoom: window.mapMaxZoom,
			width: window.mapWidth,
			height: window.mapHeight,
			mapType: window.mapMapType
		});
	});
})