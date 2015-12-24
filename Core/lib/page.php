<?php
/**
 * 2015-10-27
 * @return \Magento\Framework\View\Asset\Repository
 */
function df_asset() {return df_o(\Magento\Framework\View\Asset\Repository::class);}

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
 * 2015-10-05
 * @return \Magento\Framework\View\Page\Config
 */
function df_page() {return df_o(\Magento\Framework\View\Page\Config::class);}

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


