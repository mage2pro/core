define(['jquery', 'Df_Core/Select2', 'domReady!'], function($) {return (
	/**
	 * 2015-11-28
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String} config.dataSource
	 */
	function(config) {
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		$element.css('width', '100%');
		/**
		 * 2015-11-28
		 * Чтобы можно было делать такие асинхронные запросы к другому домену,
		 * я добавил в настройках Nginx:
		 * add_header 'Access-Control-Allow-Origin' '*';
		 * http://enable-cors.org/server_nginx.html
		 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
		 */
		debugger;
		$.getJSON(config.dataSource, function(data) {
			$element.select2({
				data: $.map(data, function(item){
					return {id: item, text: item};
				})
			});
		});
		//debugger;
	}
);});