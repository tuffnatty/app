define('pandora', ['wikia.deferred', 'wikia.nirvana'], function(deferred, nirvana) {
	'use strict';

	var xhrRequest;

	/**
	 *  Abort previous XHR request
	 **/
	function abortRequest() {
		if (xhrRequest && xhrRequest.readyState !== 4) {
			xhrRequest.abort();
		}
	}

	/**
	 *	Search based on query for object of selected type
	 *
	 *  'type' (string) - type of the object
	 *  'query' (string) - query match
	 *  'limit' (number) - max number of returned results OPTIONAL (default = 10)
	 **/
	function getSuggestions(type,query,limit) {
		var dfd = new deferred();

		abortRequest();

		xhrRequest = nirvana.getJson('Pandora', 'getSuggestions', {
			type: type,
			query: query,
			limit: limit
		}, function(resp) {
			if (resp.success === false) {
				dfd.reject(resp.message);
			}
			else {
				dfd.resolve(resp.data);
			}
		}, function() {
			dfd.reject();
		});
		return dfd.promise();
	}

	// API
	return {
		getSuggestions: getSuggestions
	}
});
