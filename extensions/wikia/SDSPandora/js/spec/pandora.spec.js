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
		resp = {},
		deferred = jQuery.Deferred,
		pandora = define.getModule(deferred, nirvanaMock);

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

	resp = {
		data: [],
		success: true
	}

	async.it('Returned Suggestions for given type and query', function(done) {
		var nirvanaMock = mockNirvanaGetSuggestions(resp),
			pandora = define.getModule(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY).then(function(resp) {
			expect(resp.data instanceof Array).toBe(true);
			expect(resp.success).toBe(true);
			done();
		});
	});

	resp = {
		success: false,
		message: 'Error message'
	}

	async.it('Returned error message', function(done) {
		var nirvanaMock = mockNirvanaGetSuggestions(resp),
			pandora = define.getModule(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY).then(function(resp) {
			expect(resp.success).toBe(false);
			expect(resp.message instanceof String).toBe(true);
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

	resp = {
		data: [1,2,3,4,5],
		success: true
	}

	async.it('Returned Suggestions with limited number of results', function(done) {
		var nirvanaMock = mockNirvanaGetSuggestionsWithLimit(resp),
			pandora = define.getModule(deferred, nirvanaMock);

		pandora.getSuggestions(TYPE, QUERY, LIMIT).then(function(resp) {
			expect(resp.data instanceof Array).toBe(true);
			expect(resp.data.length).toBe(5);
			expect(resp.success).toBe(true);
			done();
		});
	});

});