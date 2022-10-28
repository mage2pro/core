<?php
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase as P;
/**
 * 2016-07-18
 * @used-by \Df\Qa\Failure\Exception::trace()
 * @param E $e
 * @return E
 */
function df_ef(E $e) {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

/**
 * @used-by df_le()
 * @used-by df_lets()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_parse()
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Core\Exception::__construct()
 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Payment\W\Handler::handle()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \Df\Xml\X::addChild()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Dfe\Square\Source\Location::exception()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param E|P|string $e
 * @return P|string
 */
function df_ets($e) {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage())
);}

/**
 * 2016-10-24
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Klarna\Test\Charge::t01()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param E|string $e
 */
function df_etsd($e):string {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->messageD() : $e->getMessage())
);}

/**
 * 2016-03-17
 * @param E $e
 * @return LE
 */
function df_le(E $e) {return $e instanceof LE ? $e : new LE(__(df_ets($e)), $e);}

/**
 * 2016-07-20
 * @used-by \Df\Payment\W\Responder::defaultError()
 * @used-by \Dfe\AllPay\W\Responder::error()
 * @param E|string $e
 * @return P|string
 */
function df_lets($e) {return !$e instanceof E ? __($e) : df_ets(df_le($e));}

/**
 * 2016-03-17             
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @param callable $f
 * @return mixed
 * @throws LE
 */
function df_leh(callable $f) {/** @var mixed $r */try {$r = $f();} catch (E $e) {throw df_le($e);} return $r;}