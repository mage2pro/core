// 2015-12-28
define(['jquery', 'domReady!'], function($) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLFieldSetElement */
		var $element = $(document.getElementById(config.id));
		(function() {
			var $toolbar = $('<div/>').addClass('toolbar');
			$element.after($toolbar);
			var button = function(title, onClick) {
				var $result = $('<button>');
				$result.append($('<span/>').append(title));
				$result.click(function(event) {
					event.preventDefault();
					onClick();
				});
				$toolbar.append($result);
				return $result;
			};
			button('Add', function() {});
			button('Delete', function() {});
		})();
	}
);});