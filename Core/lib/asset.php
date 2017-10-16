<?php
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\Source;
/**
 * 2015-10-27
 * @used-by df_phtml_exists()
 * @return Repository
 */
function df_asset() {return df_o(Repository::class);}

/**
 * 2016-09-06
 * @used-by \Df\Framework\Form\Element\Color::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\GoogleFont::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Multiselect::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Table::onFormInitialized()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\SalesSequence\Config\Matrix\Element::onFormInitialized()
 * @param string $localPath
 * @return string
 */
function df_asset_third_party($localPath) {return "Df_Core::thirdParty/$localPath";}

/**
 * 2015-10-27
 * http://stackoverflow.com/questions/4659345
 * @used-by df_asset_exists()
 * @used-by df_resource_inline()
 * @used-by \Df\Phone\Js::_toHtml()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Moip\ConfigProvider::config()
 * @param string $u
 * @return File
 */
function df_asset_create($u) {$a = df_asset(); return !df_check_url_absolute($u)
	? $a->createAsset($u)
	: $a->createRemoteAsset($u, dfa(['css' => 'text/css', 'js' => 'application/javascript'], df_file_ext($u)))
;}

/**
 * 2015-12-29
 * Метод реализован по аналогии с @see \Magento\Framework\View\Asset\File::getSourceFile():
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/File.php#L147-L156
 * @param string $name
 * Обратите внимание, что в качестве $name можно передавать:
 * 1) короткое имя;
 * 2) уже собранное посредством @see df_asset_name() полное имя ассета;
 * @param string|null $m [optional]
 * @param string|null $ext [optional]
 * @return bool
 */
function df_asset_exists($name, $m = null, $ext = null) {return dfcf(
	function($name, $m = null, $ext = null) {return
		!!df_asset_source()->findSource(df_asset_create(df_asset_name($name, $m, $ext)))
	;}
, func_get_args());}

/**
 * 2015-12-29
 * @used-by df_asset_exists()
 * @used-by df_fe_init()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()  
 * @param string|null $name [optional]
 * Обратите внимание, что в качестве $name можно передавать:
 * 1) Короткое имя.
 * 2) Уже собранное посредством @see df_asset_name() полное имя ассета.
 * В этом случае функция возвращает аргумент $name без изменения.
 * @param string|object|null $m [optional]
 * @param string|null $extension [optional]
 * @return string
 */
function df_asset_name($name = null, $m = null, $extension = null) {return df_ccc(
	'.', df_ccc('::', $m ? df_module_name($m) : null, $name ?: 'main'), $extension
);}

/**
 * 2015-12-29
 * @return Source
 */
function df_asset_source() {return df_o(Source::class);}