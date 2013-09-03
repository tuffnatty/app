define('wikia.uifactory.drawer', function drawer(){
	'use strict';

	function Drawer(side) {
		var that = this;
		this.element = $('#drawer-' + side);
		this.drawerBackground = getDrawerBackground();

		function getDrawerBackground() {
			var drawerBackground = $('#drawerBackground');
			if (!drawerBackground.exists()) {
				drawerBackground = $('<div id="drawerBackground" class="drawerBackground" />');
				$('body').append(drawerBackground);
			}
			drawerBackground.click($.proxy(function() {
				if (this.isOpen()) {
					this.close();
				}
			}, that));
			return drawerBackground;
		}
	}

	Drawer.prototype.open = function() {
		this.element
			.addClass('open')
			.trigger('drawer-open');
		this.drawerBackground.addClass('visible');
	};
	Drawer.prototype.close = function() {
		this.element
			.removeClass('open')
			.trigger('drawer-close');
		this.drawerBackground.removeClass('visible');
	};
	Drawer.prototype.isOpen = function() {
		return this.element.hasClass('open');
	};
	Drawer.prototype.getHTMLElement = function() {
		return this.element;
	}

	//Public API
	return {
		init: function(side) {
			return new Drawer(side);
		}
	}
});
