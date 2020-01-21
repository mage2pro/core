<?php
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
/**
 * 2015-09-20
 * @used-by df_area_code()
 * @used-by df_area_code_set_b()
 * @used-by df_area_code_set_f()
 * @used-by df_is_backend()
 * @used-by df_is_dev()
 * @return State
 */
function df_app_state() {return df_o(State::class);}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function df_is_ajax() {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}

/**
 * 2015-12-09
 * https://mage2.pro/t/299
 * @return bool
 */
function df_is_dev() {return State::MODE_DEVELOPER === df_app_state()->getMode();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-12-22
 * @return bool
 */
function df_is_windows() {return dfcf(function() {return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));});}

/**
 * 2016-06-25 https://mage2.pro/t/543
 * 2018-04-17
 * 1) «Magento 2.3 has removed its version information from the `composer.json` files since 2018-04-05»:
 * https://mage2.pro/t/5480
 * 2) Now Magento 2.3 (installed with Git) returns the «dev-2.3-develop» string from the
 * @see \Magento\Framework\App\ProductMetadata::getVersion() method.
 * @used-by df_sentry()
 */
function df_magento_version() {return dfcf(function() {return df_trim_text_left(
	df_magento_version_m()->getVersion()
, 'dev-');});}

/**
 * 2016-08-24
 * @param string $version
 * @return bool
 */
function df_magento_version_ge($version) {return version_compare(df_magento_version(), $version, 'ge');}

/**
 * 2016-06-25
 * https://mage2.pro/t/543
 */
function df_magento_version_full() {
	/** @var ProductMetadata|ProductMetadataInterface $v */
	$v = df_magento_version_m();
	return df_cc_s($v->getName(), $v->getEdition(), 'Edition', $v->getVersion());
}

/**
 * 2016-06-25
 * @return ProductMetadata|ProductMetadataInterface
 */
function df_magento_version_m() {return df_o(ProductMetadataInterface::class);}

/**
 * 2017-05-13 https://mage2.pro/t/2615
 * @param string $url
 * @return string
 */
function df_magento_version_remote($url) {return dfcf(function($url) {return df_try(function() use($url) {
	/** @var string[] $a */
	$a = explode(' ', df_string_clean(df_trim_text_left(file_get_contents(
		"$url/magento_version"
	), 'Magento/'), '(', ')'));
	return 2 !== count($a) ? [] : array_combine(['version', 'edition'], $a);
});}, [df_trim_ds_right($url)]);}

/**
 * 2017-04-17
 * @used-by \Df\PaypalClone\W\Exception\InvalidSignature::message()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()   
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @return bool
 */
function df_my() {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost. 
 * @used-by df_webhook()
 * @used-by \Df\Framework\Logger\Handler\System::handle()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Inkifi\Pwinty\Event::s()
 * @used-by \Justuno\M2\Response::p()
 * @used-by \Magento\Framework\View\Asset\Bundle::fillContent() (Frugue)
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 * @return bool
 */
function df_my_local() {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}

/**
 * 2015-10-31
 * @used-by df_controller()
 * @used-by \Df\Core\Observer\ControllerActionPredispatch::execute()
 * @used-by \Df\Core\Observer\LayoutGenerateBlocksAfter::execute()
 * @used-by \Df\Core\Observer\LayoutGenerateBlocksBefore::execute()
 * @used-by \Df\Eav\Plugin\Model\Entity\Attribute\Frontend\AbstractFrontend::afterGetLabel()
 * @used-by \Df\Eav\Plugin\Model\ResourceModel\Entity\Attribute::aroundLoad()
 * @used-by \Df\Eav\Plugin\Model\ResourceModel\Entity\Attribute\Collection::beforeAddItem()
 * @used-by \Df\Framework\Plugin\View\Layout::aroundRenderNonCachedElement()
 * @used-by \Df\Framework\Plugin\View\Page\Title::aroundGet()
 * @used-by \Df\Framework\Plugin\View\TemplateEngineInterface::aroundRender()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForAttribute()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForBlock()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForComponent()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForController()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForFormElement()
 * @used-by \Dfr\Core\Realtime\Dictionary::translate()
 * @return \Df\Core\State
 */
function df_state() {static $r; return $r ? $r : $r = \Df\Core\State::s();}