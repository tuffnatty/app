require([
	'jquery',
	'wikia.nirvana',
	'wikia.leaflet',
	'wikia.window',
	'wikia.mustache'
], function(
	$,
	nirvana,
	L,
	window,
	mustache
) {
	'use strict';

	$(function (){
		var map = null,
			popup = null,
			setup = {},
			markers = [],
			defaultSetup = {
				container: 'interactive_map',
				mapType: 'openstreetmap',
				fullscreenControl: true,
				zoom: 2,
				contextmenu: true,
				contextmenuItems: [
					{
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
				],
				formTemplate: '<form class="add_poi">' +
					'<p><label>{{ label.article }}<br/><input name="article" value ="{{ article }}"/></label></p>' +
					'<p><label>{{ label.description }}<br/><textarea name="description" value ="{{ description }}"/></textarea></label></p>' +
					'<p><label>{{ label.poiType }}<br/><select name="point_type">{{#pointTypes}}<option value="{{ id }}">{{ name}}</option>{{/pointTypes}}</select></label></p>'+
					'<button>{{label.button}}</button></form>',
				defaultIcon: L.icon({
					iconUrl: window.wgResourceBasePath + '/extensions/wikia/WikiaInteractiveMaps/js/leaflet/images/marker-icon.png',
					iconRetinaUrl: window.wgResourceBasePath + '/extensions/wikia/WikiaInteractiveMaps/js/leaflet/images/marker-icon@2x.png',
					iconSize: [25, 41],
					iconAnchor: [12, 41],
					popupAnchor: [1, -34],
					shadowSize: [41, 41],
					shadowUrl: window.wgResourceBasePath + '/extensions/wikia/WikiaInteractiveMaps/js/leaflet/images/marker-shadow.png',
				})
			};

		function addPoint(event) {
			popup = L.popup({
				closeButton: true,
				closeOnClick: false
			})
				.setLatLng(event.latlng)
				.setContent(mustache.render(setup.formTemplate, {
					label: {
						article: $.msg('wikia-interactive-maps-article'),
						description: $.msg('wikia-interactive-maps-description'),
						poiType: $.msg('wikia-interactive-maps-poi-type'),
						button: $.msg('wikia-interactive-maps-add-point')
					},
					pointTypes: [
						{
							id: 1,
							name: 'Something'
						},
						{
							id: 2,
							name: 'Something else'
						}
					]
				}))
				.openOn(map);
		}

		$(document.body).on('submit', '.add_poi', function(event) {
			if (popup) {
				var $this = $(this),
					coordinates = popup.getLatLng(),
					formData = {
						mapId: setup.mapId,
						y: coordinates.lat,
						x: coordinates.lng,
						title: $this.find('[name=article]').val(),
						desc: $this.find('[name=description]').val(),
						flag: $this.find('[name=point_type]').val()
					};
				submitPoint(formData);
			}
			event.preventDefault();
		});


		function submitPoint(formData) {
			nirvana.sendRequest({
				controller: 'WikiaInteractiveMaps',
				method: 'createPoint',
				data: formData,
				callback: function(result) {
					onCreatePointSuccess(result, formData);
				},
				onErrorCallback: onCreatePointError
			});
		}

		function closePopup() {
			if (popup) {
				map.closePopup(popup);
				popup = false;
			}
		}

		function onCreatePointSuccess(result, formData) {
			if (result.status === 'ok') {
				addPointOnMap(formData);
				closePopup();
			}
		}

		function onCreatePointError(data) {
			//TODO: Show error message
		}

		function addPointOnMap(point) {
			var marker = L.marker([ point.y, point.x ], {
				icon: setup.defaultIcon,
				riseOnHover: true
			})
				.bindPopup('<h3><a target="_blank" href="' + point.article + '">' + point.title + '</a></h3><p>' +
					point.desc + '</p>')
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
					if (data.result.status === 'ok') {
						data.result.points.forEach(function(point) {
							markers.push(addPointOnMap(point));
						});
					}
				},
				onErrorCallback: onGetPointsError
			});
		}

		function addMapLayer(map, setup) {
			return L.tileLayer(setup.pathTemplate, setup.mapSetup).addTo(map);
		}

		function init(customSetup) {
			setup = $.extend(false, defaultSetup, customSetup );
			var contextmenuItems = setup.contextmenuItems;
			if (setup.mapCanEdit) {
				// Enable add point for users with privilegues
				contextmenuItems.unshift({
					text: $.msg('wikia-interactive-maps-add-new-point'),
						callback: function (event) {
							addPoint(event);
						}
				});
			}
			map = L.map(setup.container, {
				center: [-50, 0],
				zoom: setup.zoom,
				fullscreenControl: setup.fullscreenControl,
				contextmenu: setup.contextmenu,
				contextmenuItems: contextmenuItems
			});
			addMapLayer(map, setup);
			getPoints(setup.mapId);
		}

		init(window.interactiveMapSetup || {});
	});
});
