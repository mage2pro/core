<?php
/**
 * 2015-10-27
 * @return \Magento\Framework\View\Asset\Repository
 */
function df_asset() {return df_o('Magento\Framework\View\Asset\Repository');}

/**
 * @param string $resource
 * @return \Magento\Framework\View\Asset\File
 */
function df_asset_create($resource) {
	return
		/** http://stackoverflow.com/questions/4659345 */
		!df_starts_with($resource, 'http') && !df_starts_with($resource, '//')
		? df_asset()->createAsset($resource)
		: df_asset()->createRemoteAsset($resource, df_a(
			['css' => 'text/css', 'js' => 'application/javascript']
			, df_file_ext($resource)
		))
	;
}

/**
 * 2015-10-27
 * @used-by df_form_element_init()
 * @used-by \Dfe\Markdown\FormElement::getBeforeElementHtml()
 * @param string|string[] $resource
 * @return string
 */
function df_link_inline($resource) {
	if (1 < func_num_args()) {
		$resource = func_get_args();
	}
	/** @var string $result */
	if (is_array($resource)) {
		$result = df_concat_n(array_map(__FUNCTION__, $resource));
	}
	else {
		/**
		 * 2015-12-11
		 * Не имеет смысла несколько раз загружать на страницу один и тот же файл CSS.
		 * Как оказалось, браузер при наличии на странице нескольких тегов link с одинаковым адресом
		 * применяет одни и те же правила несколько раз (хотя, видимо, не делает повторных обращений к серверу
		 * при включенном в браузере кэшировании браузерных ресурсов).
		 */
		/** @var string[] $cache */
		static $cache;
		if (isset($cache[$resource])) {
			$result = '';
		}
		else {
			$result = df_tag('link', ['rel' => 'stylesheet', 'type' => 'text/css',
				'href' => df_asset_create($resource)->getUrl()
			]);
			$cache[$resource] = true;
		}
	}
	return $result;
}

/**
 * 2015-10-05
 * @return \Magento\Framework\View\Page\Config
 */
function df_page() {return df_o('Magento\Framework\View\Page\Config');}

/**
 * 2015-10-05
 * @param string $name
 * @param string|null $value
 * @return void
 */
function df_metadata($name, $value) {
	if (!is_null($value) && '' !== $value) {
		df_page()->setMetadata($name, $value);
	}
}


