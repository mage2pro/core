define(['jquery', 'jquery/ui', 'Df_Core/Select2', 'domReady!'], function($) {return (
	/**
	 * 2016-03-08
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {Boolean} config.ordered
	 */
	function(config) {
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		$element.select2({
			createTag: function() {return undefined;}
			,width: '100%' // 2016-04-13 http://stackoverflow.com/a/21198924
		});
		if (config.ordered) {
			// 2017-09-23
			// It will make the options sortable:
			// https://github.com/select2/select2/issues/1190#issuecomment-151832727
			// «Provide an ability for a backend user to set a custom ordering
			// for a payment method's payment options on the frontend checkout page»:
			// https://github.com/mage2pro/core/issues/25
			var $ul = $element.next('.select2-container').first('ul.select2-selection__rendered');
			$ul.sortable({
				forcePlaceholderSize: true
				,items: 'li:not(.select2-search__field)'
				,placeholder: 'ui-state-highlight'
				,stop: function() {
					$($ul.find('.select2-selection__choice').get().reverse()).each(function() {
						var id = $(this).data('data').id;
						var option = $element.find('option[value="' + id + '"]')[0];
						$element.prepend(option);
					});
					console.log($element.val())
				}
				,tolerance: 'pointer'
			});
		}
	}
);});