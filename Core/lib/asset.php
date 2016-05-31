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
		// http://stackoverflow.com/questions/4659345
		!df_starts_with($resource, 'http') && !df_starts_with($resource, '//')
		? df_asset()->createAsset($resource)
		: df_asset()->createRemoteAsset($resource, dfa(
			['css' => 'text/css', 'js' => 'application/javascript']
			, df_file_ext($resource)
		))
	;
}

/**
 * 2015-12-29
 * Метод реализован по аналогии с @see \Magento\Framework\View\Asset\File::getSourceFile():
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/File.php#L147-L156
 * @param string $name
 * Обратите внимание, что в качестве $name можно передавать:
 * 1) короткое имя;
 * 2) уже собранное посредством @see df_asset_name() полное имя ассета;
 * @param string|null $moduleName [optional]
 * @param string|null $extension [optional]
 * @return bool
 */
function df_asset_exists($name, $moduleName = null, $extension = null) {
	/** @var array(string => array(string => array(string => bool))) $cache */
	static $cache;
	if (!isset($cache[$name][$moduleName][$extension])) {
		$cache[$name][$moduleName][$extension] = !!df_asset_source()->findSource(df_asset_create(
			df_asset_name($name, $moduleName, $extension)
		));
	}
	return $cache[$name][$moduleName][$extension];
}

/**
 * 2015-12-29
 * @param string $name
 * Обратите внимание, что в качестве $name можно передавать:
 * 1) Короткое имя.
 * 2) Уже собранное посредством @see df_asset_name() полное имя ассета.
 * В этом случае функция возвращает аргумент $name без изменения.
 * @param string|null $moduleName [optional]
 * @param string|null $extension [optional]
 * @return string
 */
function df_asset_name($name, $moduleName = null, $extension = null) {
	return df_cc_clean('.', df_cc_clean('::', $moduleName, $name), $extension);
}

/**
 * 2015-12-29
 * @return \Magento\Framework\View\Asset\Source
 */
function df_asset_source() {return df_o(\Magento\Framework\View\Asset\Source::class);}

