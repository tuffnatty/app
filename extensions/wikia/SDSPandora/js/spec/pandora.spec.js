/*
 @test-require-asset resources/wikia/libraries/define.mock.js
 @test-require-asset resources/jquery/jquery-1.8.2.js
 @test-require-asset extensions/wikia/SDSPandora/js/modules/pandora.js
 */

describe("pandora", function () {
	'use strict';

	var TYPE = 'game',
		QUERY = 'DOOM',
		LIMIT = 5,
		async = new AsyncSpec(this),
		nirvanaMock = {},
		deferred = jQuery.Deferred,
		pandora = modules.pandora(deferred, nirvanaMock);

	it('registers AMD module', function() {
		expect(typeof pandora).toBe('object');
		expect(typeof pandora.getSuggestions).toBe('function');
	});

	function mockNirvanaGetSuggestions(resp) {
		return {
			getJson: function(controllerName, method, params, callback) {
				// type and query passed successfully
				expect(params.type).toBe(TYPE);
				expect(params.query).toBe(QUERY);
				callback(resp);
			}
		};
	}

	async.it('Returned Suggestions for given type and query', function(done) {
		var resp = {
			data: [],
			success: true
		},
			nirvanaMock = mockNirvanaGetSuggestions(resp),
			pandora = modules.pandora(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY).then(function(data) {
			expect(data instanceof Array).toBe(true);
			done();
		});
	});


	async.it('Returned error message', function(done) {
		var resp = {
			success: false,
			message: 'Error message'
		},
			nirvanaMock = mockNirvanaGetSuggestions(resp),
			pandora = modules.pandora(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY).fail(function(message) {
			dump(message);
			expect(message ).toEqual('Error message');
			done();
		});
	});

	function mockNirvanaGetSuggestionsWithLimit(resp) {
		return {
			getJson: function(controllerName, method, params, callback) {
				// type and query passed successfully
				expect(params.type).toBe(TYPE);
				expect(params.query).toBe(QUERY);
				expect(params.limit).toBe(LIMIT);
				callback(resp);
			}
		};
	}

	async.it('Returned Suggestions with limited number of results', function(done) {
		var resp = {
			data: [1,2,3,4,5],
			success: true
		},
			nirvanaMock = mockNirvanaGetSuggestionsWithLimit(resp),
			pandora = modules.pandora(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY, LIMIT).then(function(data) {
			expect(data instanceof Array).toBe(true);
			expect(data.length).toBe(5);
			done();
		});
	});

});