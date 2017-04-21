<?php
/**
 * 2015-10-27
 * @return \Magento\Framework\View\Asset\Repository
 */
function df_asset() {return df_o(\Magento\Framework\View\Asset\Repository::class);}

/**
 * 2016-09-06
 * @used-by \Df\Framework\Form\Element\Color::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\GoogleFont::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Multiselect::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Table::onFormInitialized()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\SalesSequence\Config\Matrix\Element::onFormInitialized()
 * @param string $localPath
 * @return string
 */
function df_asset_third_party($localPath) {return "Df_Core::thirdParty/$localPath";}

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
function df_asset_exists($name, $moduleName = null, $extension = null) {return dfcf(
	function($name, $moduleName = null, $extension = null) {return
		!!df_asset_source()->findSource(df_asset_create(df_asset_name($name, $moduleName, $extension)))
	;}
, func_get_args());}

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
	return df_ccc('.', df_ccc('::', $moduleName, $name), $extension);
}

/**
 * 2015-12-29
 * @return \Magento\Framework\View\Asset\Source
 */
function df_asset_source() {return df_o(\Magento\Framework\View\Asset\Source::class);}

