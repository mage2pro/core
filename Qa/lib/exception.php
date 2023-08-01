<?php
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase as P;

/**
 * 2016-03-17
 * @used-by df_lxh()
 * @used-by df_lxts()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\TwoCheckout\Method::api()
 */
function df_lx(E $e):LE {return $e instanceof LE ? $e : new LE(__(df_xts($e)), $e);}

/**
 * 2016-03-17
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @return mixed
 * @throws LE
 */
function df_lxh(callable $f) {/** @var mixed $r */try {$r = $f();} catch (E $e) {throw df_lx($e);} return $r;}

/**
 * 2016-07-20
 * @used-by \Df\Payment\W\Responder::defaultError()
 * @used-by \Dfe\AllPay\W\Responder::error()
 * @param E|string $e
 * @return P|string
 */
function df_lxts($e) {return !$e instanceof E ? __($e) : df_xts(df_lx($e));}

/**
 * 2023-07-25
 * @used-by df_log_l()
 * @used-by df_x_module()
 */
function df_x_entry(E $e):array {return df_caller_entry($e, function(array $a):bool {return
	($c = dfa($a, 'class')) && df_module_enabled($c)
;});}

/**
 * 2023-07-25
 * @used-by df_log()
 * @used-by df_sentry()
 */
function df_x_module(E $e):string {return df_module_name(dfa(df_x_entry($e), 'class'));}

/**
 * 2016-07-18
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Qa\Failure\Exception::trace()
 */
function df_xf(E $e):E {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

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
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param E|P|string $e
 */
function df_xts($e):string {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage())
);}

/**
 * 2016-10-24
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Klarna\Test\Charge::t01()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param E|string $e
 */
function df_xtsd($e):string {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->messageD() : $e->getMessage())
);}