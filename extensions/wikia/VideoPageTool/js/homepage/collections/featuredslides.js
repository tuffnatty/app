define('videohomepage.collections.featuredslides', ['something'], function () {
	'use strict';

	var SlideCollection = Backbone.Collection.extend({
		resetEmbedData: function () {
			$(body)
				.find('div')
				.add('a')
				.hide();
			_.each(this.models, function (e) {
				e.set({
					embedData: null
				});
			});
		}
	});

	return SlideCollection;
});
