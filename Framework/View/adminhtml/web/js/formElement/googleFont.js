define(['jquery', 'Df_Core/Select2', 'domReady!'], function($) {return (
	/**
	 * 2015-11-28
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		debugger;
		/**
		 * 2015-11-28
		 * Чтобы можно было делать такие асинхронные запросы к другому домену,
		 * я добавил в настройках Nginx:
		 * add_header 'Access-Control-Allow-Origin' '*';
		 * http://enable-cors.org/server_nginx.html
		 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
		 */
		$.getJSON('https://mage2.pro/google-fonts.json', function(data) {
			$element.select2({
				data: $.map(data.items, function(item){
					return {id: item.family, text: item.family};
				})
			});
		});
		//debugger;
	}
);});