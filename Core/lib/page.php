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
		!df_starts_with($resource, 'http')
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
	return
		is_array($resource)
		? df_concat_n(array_map(__FUNCTION__, $resource))
		: df_tag('link', ['rel' => 'stylesheet', 'type' => 'text/css',
			'href' => df_asset_create($resource)->getUrl()
		])
	;
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


