<?php
use Df\Core\Exception as DFE;
use Exception as X;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase as P;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2023-08-02
 * @see df_is_x()
 * @used-by df_bt()
 * @used-by df_bt_inc()
 * @used-by df_error_create()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_try()
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by Df\Core\Exception::__construct()
 * @used-by Dfe\YandexKassa\W\Responder::error()
 * @used-by \Df\Framework\Log\Record::e()
 */
function df_is_th($v):bool {return $v instanceof T;}

/**
 * 2023-08-02
 * @see df_is_th()
 * @used-by df_lxts()
 * @used-by df_th2x()
 */
function df_is_x($v):bool {return $v instanceof X;}

/**
 * 2016-03-17
 * @used-by df_lxh()
 * @used-by df_lxts()
 * @used-by Df\Payment\Method::action()
 * @used-by Dfe\CheckoutCom\Method::leh()
 * @used-by Dfe\TwoCheckout\Method::api()
 */
function df_lx(T $t):LE {return $t instanceof LE ? $t : new LE(__(df_xts($t)), df_th2x($t));}

/**
 * 2016-03-17
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @return mixed
 * @throws LE
 */
function df_lxh(callable $f) {/** @var mixed $r */try {$r = $f();} catch (T $t) {throw df_lx($t);} return $r;}

/**
 * 2016-07-20
 * @used-by Df\Payment\W\Responder::defaultError()
 * @used-by Dfe\AllPay\W\Responder::error()
 * @param T|string $t
 * @return P|string
 */
function df_lxts($t) {return !df_is_x($t) ? __($t) : df_xts(df_lx($t));}

/**
 * 2023-08-03
 * @used-by df_lx()
 * @used-by Df\Core\Exception::__construct()
 * @used-by Df\Payment\PlaceOrderInternal::_place()
 */
function df_th2x(T $t):X {return df_is_x($t) ? $t : new X(df_xts($t), $t->getCode(), $t);}

/**
 * 2016-07-18
 * @used-by Df\Framework\Log\Record::ef()
 * @used-by Df\Payment\PlaceOrderInternal::message()
 * @used-by Df\Qa\Failure\Exception::trace()
 */
function df_xf(T $t):T {while ($t->getPrevious()) {$t = $t->getPrevious();} return $t;}

/**
 * @see \Df\Core\Exception::throw_()
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_th2x()
 * @used-by df_xml_x()
 * @used-by Df\API\Client::_p()
 * @used-by Df\Core\Exception::__construct()
 * @used-by Df\Config\Source\API::exception()
 * @used-by Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by Df\Framework\Console\Command::execute()
 * @used-by Df\Framework\Log\Record::emsg()
 * @used-by Df\Payment\PlaceOrderInternal::message()
 * @used-by Df\Payment\W\Handler::handle()
 * @used-by Df\Qa\Trace\Formatter::p()
 * @used-by Df\Xml\G::addChild()
 * @used-by Df\Xml\G::importString()
 * @used-by Dfe\CheckoutCom\Handler::p()
 * @used-by Dfe\GoogleFont\Font\Variant\Preview::box()
 * @used-by Dfe\Klarna\Exception::messageC()
 * @used-by Dfe\Sift\Controller\Index\Index::execute()
 * @used-by Dfe\Square\Source\Location::exception()
 * @used-by Dfe\Stripe\Exception::messageC()
 * @used-by Dfe\TwoCheckout\Handler::p()
 * @used-by Dfe\YandexKassa\Result::attributes()
 * @used-by Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param T|P|string $t
 */
function df_xts($t):string {return df_path_rel_g(
	!df_is_th($t) ? $t : ($t instanceof DFE ? $t->message() : $t->getMessage())
);}

/**
 * 2016-10-24
 * @used-by Df\Payment\PlaceOrderInternal::message()
 * @used-by Df\Sentry\Client::captureException()
 * @used-by Dfe\Klarna\Test\Charge::t01()
 * @used-by Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param T|string $t
 */
function df_xtsd($t):string {return df_path_rel_g(
	!df_is_th($t) ? $t : ($t instanceof DFE ? $t->messageD() : $t->getMessage())
);}