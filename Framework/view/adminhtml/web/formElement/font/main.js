define(['jquery', 'jquery/ui', 'domReady!'], function($) {return (
	/**
	 * 2015-11-23
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLFieldSetElement */
		var $font = $(document.getElementById(config.id));
		(function(){
			/** @type {jQuery} HTMLSelectElement */
			var $enabled = $('input.df-name-enabled', $font);
			var updateEnabledStatus = function() {
				/** @type {jQuery} HTMLSelectElement */
				var $this = $(this);
				$this.closest('.df-field').siblings().toggleClass('df-hidden', !$this.is(':checked'));
			};
			$enabled.each(updateEnabledStatus);
			$enabled.change(updateEnabledStatus);
		})();
		// 2015-11-24
		// https://jqueryui.com/button/#checkbox
		/** @type {jQuery} HTMLInputElement */
		var $checkboxes = $('fieldset.df-checkboxes > span.df-checkbox', $font);
		$checkboxes.wrapAll($('<div/>').addClass('df-button-set'));
		//$checkboxes.parent().buttonset();
		$('input.df-name-bold').button({icons:{primary:'icon-bold'}, text: false});
		$('input.df-name-italic').button({icons:{primary:'icon-italic'}, text: false});
		$('input.df-name-underline').button({icons:{primary:'icon-underline'}, text: false});
		// http://stackoverflow.com/a/2515119
	}
);});