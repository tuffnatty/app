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
				init: {
					lat: -50,
					lon: 0,
					zoom: 2
				},
				contextmenu: true,
				contextmenuItems: [
					{
						text: $.msg('wikia-interactive-maps-center-map-here'),
						callback: function(event) {
							map.panTo(event.latlng);
						}
					}, {
						text: $.msg('wikia-interactive-maps-get-link'),
						callback: function() {
							var latlng = map.getCenter(),
								url = setup.url + '?' + [
								'x=' + latlng.lat.toFixed(6),
								'y=' + latlng.lng.toFixed(6),
								'z=' + map.getZoom()
							].join('&');
							window.prompt('', url);
						}
					}, {
						text: 'Show coordinates',
						callback: function showCoordinates (event) {
							alert(event.latlng);
						}
					},
					'-',
					{
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
						x: coordinates.lng,
						y: coordinates.lat,
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
				callback: function(response) {
					onCreatePointSuccess(response);
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

		function onCreatePointSuccess(response) {
			if (response.result.status === 'ok') {
				addPointOnMap(response.result.point);
				closePopup();
			}
		}

		function onCreatePointError(data) {
			//TODO: Show error message
		}

		function addPointOnMap(point) {
			var popupHtml = '<h3><a target="_blank" href="' + point.article + '">' + point.title + '</a></h3>' +
				'<p>' +point.desc + '</p>';
			if (setup.canEdit) {
				popupHtml += '<button class="edit_poi">' + $.msg('wikia-interactive-maps-edit-point') + '</button>' +
					'&nbsp;<button class="delete_poi">' + $.msg('wikia-interactive-maps-delete-point') + '</button>';
			}
			return L.marker([ point.y, point.x ], {
				icon: setup.defaultIcon,
				riseOnHover: true
			})
				.bindPopup(popupHtml)
				.addTo(map);
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
			if (setup.canEdit) {
				// Enable add point for users with privilegues
				setup.contextmenuItems.unshift({
					text: $.msg('wikia-interactive-maps-add-new-point'),
						callback: function (event) {
							addPoint(event);
						}
				});
			}

			map = L.map(setup.container, {
				center: [setup.init.lat, setup.init.lon],
				zoom: setup.init.zoom,
				fullscreenControl: setup.fullscreenControl,
				contextmenu: setup.contextmenu,
				contextmenuItems: setup.contextmenuItems
			});
			map.panTo(L.latLng(setup.init.lat, setup.init.lon));
			getPoints(setup.mapId);
			addMapLayer(map, setup);
		}

		init(window.interactiveMapSetup || {});
	});
});
