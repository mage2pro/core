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
		// 2015-11-28
		// https://select2.github.io/examples.html#responsive
		$element.css('width', '100%');
		/**
		 * 2015-11-28
		 * Чтобы можно было делать такие асинхронные запросы к другому домену,
		 * я добавил в настройках Nginx:
		 * add_header 'Access-Control-Allow-Origin' '*';
		 * http://enable-cors.org/server_nginx.html
		 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
		 */
		$.getJSON(config.dataSource, function(data) {$element.select2({
			data: $.map(data,
				/**
				 * 2015-11-28
				 * @param {Object} item
				 * @param {String} item.family
				 * @param {String[]} item.variants
				 * @returns {Object}
				 */
				function(item) {return {
					id: item.family
					,text: item.family
					// 2015-11-28
					// http://stackoverflow.com/a/17621060
					,children: $.map(item.variants,
					   /**
					 	* 2015-11-28
						* @param {String} variant
					 	* @returns {Object}
					 	*/
						function(variant) {return {
							// https://developers.google.com/fonts/docs/getting_started#Syntax
							// http://fonts.googleapis.com/css?family=Tangerine:bold
							id: [item.family, variant].join(':')
						   ,text: variant
						}}
					)
				};}
			),
			/**
			 * 2015-11-28
			 * http://stackoverflow.com/a/33971933
			 * @param {Object} item
			 * @param {Boolean} item.disabled
			 * @param {HTMLOptionElement} item.element
			 * @param {String} item.id		Например: "ABeeZee:regular"
			 * @param {Boolean} item.selected
			 * @param {String} item.text	Например: "regular"
			 * @returns {*}
			 */
			templateSelection: function(item) {
				/** @type {jQuery} HTMLOptionElement */
				var $option = $(item.element);
				var $optGroup = $option.parent();
				return $optGroup.attr('label') + ' (' + item.text + ')';
			}
		});});
		//debugger;
	}
);});