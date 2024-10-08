<?php
use Df\Core\Exception as DFE;
use Magento\Framework\View\Asset\AssetInterface as IAsset;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Remote;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\Source;
/**
 * 2015-10-27
 * @used-by df_asset_create()
 * @used-by df_asset_url()
 * @used-by df_phtml_exists()
 */
function df_asset():Repository {return df_o(Repository::class);}

/**
 * 2015-10-27 http://stackoverflow.com/questions/4659345
 * 2023-03-02
 * «Return value of df_asset_create() must be an instance of Magento\Framework\View\Asset\File,
 * instance of Magento\Framework\View\Asset\Remote returned»: https://github.com/mage2pro/core/issues/214
 * @used-by df_asset_exists()
 * @used-by df_resource_inline()
 * @used-by Dfe\Phone\Js::_toHtml()
 * @used-by Dfe\Customer\Block::_toHtml()
 * @used-by Dfe\Moip\ConfigProvider::config()
 * @return IAsset|File|Remote
 */
function df_asset_create(string $u):IAsset {$a = df_asset(); return !df_is_url_absolute($u)
	? $a->createAsset($u)
	: $a->createRemoteAsset($u, dfa(['css' => 'text/css', 'js' => 'application/javascript'], df_file_ext($u)))
;}

/**
 * 2015-12-29
 * 1) By analogy with @see \Magento\Framework\View\Asset\File::getSourceFile():
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/File.php#L147-L156
 * 2) $name could be:
 * 		1) a short name;
 * 		2) a full name composed with @see df_asset_name()
 * @used-by df_fe_init()
 */
function df_asset_exists(string $name, string $m = '', string $ext = ''):bool {return !!df_asset_source()->findSource(
	df_asset_create(df_asset_name($name, $m, $ext))
);}

/**
 * 2015-12-29
 * $name could be:
 * 		1) a short name;
 * 		2) a full name composed with @see df_asset_name(). In this case, the function returns $name without changes.
 * @used-by df_asset_exists()
 * @used-by df_block_output()
 * @used-by df_fe_init()
 * @used-by BlushMe\Checkout\Block\Extra::getTemplate()
 * @used-by BlushMe\Checkout\Block\Extra\Item::getTemplate()
 * @used-by Dfe\Klarna\Button::_toHtml()
 * @used-by Dfe\Portal\Block::m()
 * @used-by Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by SayItWithAGift\Options\Frontend::_toHtml()
 * @param string|object|null $m [optional]
 */
function df_asset_name(string $name = '', $m = null, string $ext = ''):string {return df_ccc(
	'.', df_ccc('::', $m ? df_module_name($m) : null, $name ?: 'main'), $ext
);}

/**
 * 2015-12-29
 * @used-by df_asset_exists()
 * @used-by df_asset_url()
 */
function df_asset_source():Source {return df_o(Source::class);}

/**
 * 2016-09-06
 * @used-by Df\Framework\Form\Element\Color::onFormInitialized()
 * @used-by Df\Framework\Form\Element\GoogleFont::onFormInitialized()
 * @used-by Df\Framework\Form\Element\Multiselect::onFormInitialized()
 * @used-by Df\Framework\Form\Element\Select2::onFormInitialized()
 * @used-by Df\Framework\Form\Element\Table::onFormInitialized()
 * @used-by Dfe\Markdown\FormElement::css()
 * @used-by Dfe\SalesSequence\Config\Matrix\Element::onFormInitialized()
 */
function df_asset_third_party(string $localPath):string {return "Df_Core::thirdParty/$localPath";}

/**
 * 2019-02-11 $n: 'AllesWunder_Core::i/aw-logo.png'
 * @used-by Df\Payment\BankCardNetworks::url()
 * @used-by Inkifi\Map\HTML::tiles()
 * @used-by TFC\Core\B\Home\Slider::i()
 * @used-by vendor/alleswunder/core/view/frontend/templates/aw-logo.phtml
 * @used-by vendor/inkifi/map/view/frontend/templates/index.phtml
 * @param bool|Closure|mixed $onE [optional]
 * @return string|null
 * @throws DFE
 */
function df_asset_url(string $n, $onE = null) {
	$f = df_asset()->createAsset($n); /** @var File $f */
	return df_try(function() use($f, $n) {return df_asset_source()->findSource($f)
		? $f->getUrl() : df_error("The asset $n does not exist.")
	;}, $onE);
}