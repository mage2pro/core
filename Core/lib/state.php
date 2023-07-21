<?php
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
 * 2017-04-17
 * @used-by df_my_local()
 * @used-by \Df\PaypalClone\W\Exception\InvalidSignature::message()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()   
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 */
function df_my():bool {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost. 
 * @used-by df_webhook()
 * @used-by ikf_endpoint()	inkifi.com
 * @used-by \Df\Cron\Model\LoggerHandler::p()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Inkifi\Pwinty\Event::s()
 * @used-by \Magento\Framework\View\Asset\Bundle::fillContent() (Frugue)
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 */
function df_my_local():bool {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}