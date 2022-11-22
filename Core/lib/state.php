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
 */
function df_app_state():State {return df_o(State::class);}

/**
 * 2015-08-15
 * https://mage2.pro/t/59
 * https://mage2.ru/t/94
 * @see df_is_backend()
 * @see df_is_frontend()
 * @see df_is_rest()
 * @used-by df_is_backend()
 * @used-by df_is_frontend()
 */
function df_is_ajax():bool {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}

/**
 * 2015-12-09 https://mage2.pro/t/299
 * 2022-11-22 @deprecated It is unused.
 */
function df_is_dev():bool {return State::MODE_DEVELOPER === df_app_state()->getMode();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * @used-by df_my_local()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 */
function df_is_localhost():bool {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-12-22
 * 2020-06-27 @deprecated It is unused.
 */
function df_is_windows():bool {return dfcf(function() {return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));});}

/**
 * 2016-06-25 https://mage2.pro/t/543
 * 2018-04-17
 * 1) «Magento 2.3 has removed its version information from the `composer.json` files since 2018-04-05»:
 * https://mage2.pro/t/5480
 * 2) Now Magento 2.3 (installed with Git) returns the «dev-2.3-develop» string from the
 * @see \Magento\Framework\App\ProductMetadata::getVersion() method.
 * @used-by df_context()
 * @used-by df_sentry()
 */
function df_magento_version():string {return dfcf(function() {return df_trim_text_left(
	df_magento_version_m()->getVersion(), 'dev-'
);});}

/**
 * 2016-08-24
 * @used-by \Df\Intl\Js::_toHtml()
 */
function df_magento_version_ge(string $v):bool {return version_compare(df_magento_version(), $v, 'ge');}

/**
 * 2016-06-25 https://mage2.pro/t/543
 */
function df_magento_version_full() {
	$v = df_magento_version_m(); /** @var ProductMetadata|ProductMetadataInterface $v */
	return df_cc_s($v->getName(), $v->getEdition(), 'Edition', $v->getVersion());
}

/**
 * 2016-06-25
 * @used-by df_magento_version()
 * @used-by df_magento_version_full()
 * @return ProductMetadata|ProductMetadataInterface
 */
function df_magento_version_m() {return df_o(ProductMetadataInterface::class);}

/**
 * 2017-05-13 https://mage2.pro/t/2615
 * 2022-10-14 @deprecated It is unused. And it is slow.
 */
function df_magento_version_remote(string $url):string {return dfcf(function($url) {return df_try(function() use($url) {
	/** @var string[] $a */
	$a = df_explode_space(df_string_clean(df_trim_text_left(df_file_read("$url/magento_version"), 'Magento/'), '(', ')'));
	return 2 !== count($a) ? [] : array_combine(['version', 'edition'], $a);
});}, [df_trim_ds_right($url)]);}

/**
 * 2017-04-17
 * @used-by df_my_local()
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
 * @used-by ikf_endpoint()	inkifi.com
 * @used-by \Df\Cron\Model\LoggerHandler::p()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Inkifi\Pwinty\Event::s()
 * @used-by \Magento\Framework\View\Asset\Bundle::fillContent() (Frugue)
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 * @return bool
 */
function df_my_local() {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}