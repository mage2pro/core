<?php
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase as P;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2023-08-02
 * @see df_is_th()
 * @used-by df_lxts()
 * @used-by df_th_as_prev()
 * @used-by \Df\Core\Exception::__construct()
 */
function df_is_x($v):bool {return $v instanceof E;}

/**
 * 2023-08-02
 * @see df_is_x()
 * @used-by df_bt()
 * @used-by df_bt_inc()
 * @used-by df_error_create()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by \Df\Core\Exception::__construct()
 * @used-by \Dfe\YandexKassa\W\Responder::error()
 */
function df_is_th($v):bool {return $v instanceof T;}

/**
 * 2016-03-17
 * @used-by df_lxh()
 * @used-by df_lxts()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\TwoCheckout\Method::api()
 */
function df_lx(T $t):LE {return $t instanceof LE ? $t : new LE(__(df_xts($t)), df_th_as_prev($t));}

/**
 * 2016-03-17
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @return mixed
 * @throws LE
 */
function df_lxh(callable $f) {/** @var mixed $r */try {$r = $f();} catch (T $t) {throw df_lx($t);} return $r;}

/**
 * 2016-07-20
 * @used-by \Df\Payment\W\Responder::defaultError()
 * @used-by \Dfe\AllPay\W\Responder::error()
 * @param T|string $e
 * @return P|string
 */
function df_lxts($e) {return !df_is_x($e) ? __($e) : df_xts(df_lx($e));}

/**
 * 2023-08-03
 * @used-by df_lx()
 * @used-by \Df\Payment\PlaceOrderInternal::_place()
 * @return E|null
 */
function df_th_as_prev(T $t) {return df_is_x($t) ? $t : null;}

/**
 * 2023-07-25
 * @used-by df_log_l()
 * @used-by df_x_module()
 */
function df_x_entry(T $t):array {return df_caller_entry($t, function(array $a):bool {return
	($c = dfa($a, 'class')) && df_module_enabled($c)
;});}

/**
 * 2023-07-25
 * @used-by df_log()
 * @used-by df_sentry()
 */
function df_x_module(T $t):string {return df_module_name(dfa(df_x_entry($t), 'class'));}

/**
 * 2016-07-18
 * @used-by \Df\Framework\Log\Record::ef()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Qa\Failure\Exception::trace()
 */
function df_xf(T $t):T {while ($t->getPrevious()) {$t = $t->getPrevious();} return $t;}

/**
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_parse()
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Core\Exception::__construct()
 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Payment\W\Handler::handle()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Xml\X::addChild()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Dfe\Square\Source\Location::exception()
 * @used-by \Dfe\YandexKassa\Result::attributes()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param T|P|string $t
 */
function df_xts($t):string {return df_adjust_paths_in_message(
	!df_is_th($t) ? $t : ($t instanceof DFE ? $t->message() : $t->getMessage())
);}

/**
 * 2016-10-24
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Klarna\Test\Charge::t01()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param T|string $t
 */
function df_xtsd($t):string {return df_adjust_paths_in_message(
	!df_is_th($t) ? $t : ($t instanceof DFE ? $t->messageD() : $t->getMessage())
);}