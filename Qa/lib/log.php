<?php
use Df\Qa\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\DataObject as _DO;
/**
 * @used-by ikf_endpoint()	inkifi.com
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\GoogleFont\Fonts\Png::url()
 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Qa\Failure::log()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Dfe\Sift\Observer::f()
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null):void {df_log_l($m, $v); df_sentry($m, $v);}

/**
 * 2017-01-11
 * 2020-09-09 We need `df_caller_f(1)` (I checked it) because it is nested inside `df_log_l()`.
 * @used-by df_error()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by \Df\Framework\Plugin\AppInterface::beforeCatchException() (https://github.com/mage2pro/core/issues/160)
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \CanadaSatellite\Bambora\Model\PaymentManagement::savePaymentInformationAndPlaceOrder() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @param string|object|null $m [optional]
 * @param string|mixed[] $d [optional]
 * @param string|bool|null $suf [optional]
 */
function df_log_e(E $e, $m = null, $d = [], $suf = null):void {df_log_l($m, $e, $d, $suf ?: df_caller_f(1));}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by df_log_e()
 * @used-by dfp_report()
 * @used-by \Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \Amasty\Checkout\Model\Optimization\LayoutJsDiffProcessor::moveArray(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/120)
 * @used-by \Auctane\Api\Model\Action\Export::addXmlElement(caremax.com.au, https://github.com/caremax-com-au/site/issues/1)
 * @used-by \CanadaSatellite\Bambora\Facade::api() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \Customweb\RealexCw\Helper\InvoiceItem::getInvoiceItems()	tradefurniturecompany.co.uk
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @used-by \Df\Framework\Plugin\View\Asset\Source::aroundGetContent()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \Df\Store\Plugin\Model\App\Emulation::beforeStartEnvironmentEmulation()
 * @used-by \Dfe\Klarna\Api\Checkout::_html()
 * @used-by \Hotlink\Brightpearl\Model\Api\Transport::_submit() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/site/issues/122)
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/27)
 * @used-by \Mageside\CanadaPostShipping\Model\Carrier::_doRatesRequest() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/25)
 * @used-by \Wyomind\SimpleGoogleShopping\Model\Observer::checkToGenerate(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/26)
 * @param string|object|null $m
 * @param string|mixed[]|E $p2
 * @param string|mixed[]|E $p3 [optional]
 * @param string|bool|null $p4 [optional]
 */
function df_log_l($m, $p2, $p3 = [], $p4 = null) {
	/** @var E|null $e */ /** @var array|string|mixed $d */ /** @var string|null $suf */ /** @var string|null $pref */
	list($e, $d, $suf, $pref) = $p2 instanceof E ? [$p2, $p3, $p4, null] : [null, $p2, $p3, $p4];
	if (!$m && $e) {
		/** @var array(string => string) $en */
		$en = df_caller_entry($e, function(array $e) {return ($c = dfa($e, 'class')) && df_module_enabled($c);});
		list($m, $suf) = [dfa($en, 'class'), dfa($en, 'function', 'exception')];
	}
	$suf = $suf ?: df_caller_f();
	if (is_array($d)) {
		$d = df_extend($d, ['Mage2.PRO' => df_context()]);
	}
	$d = !$d ? null : (is_string($d) ? $d : df_json_encode($d));
	df_report(
		df_ccc('--', 'mage2.pro/' . df_ccc('-', df_report_prefix($m, $pref), '{date}--{time}'), $suf) .  '.log'
		,df_cc_n($d, !$e ? null : ['EXCEPTION', QE::i($e)->report(), "\n\n"], df_bt_s(1))
	);
}

/**
 * 2017-04-03
 * 2017-04-22
 * С не-строковым значением $m @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Inkifi\Mediaclip\H\Logger::l()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 */
function df_report(string $f, string $m, bool $append = false):void {
	if (!df_es($m)) {
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}

/**
 * 2020-01-31
 * @used-by df_log_l()
 * @param string|object|null $m [optional]
 * @param string|null $pref [optional]
 * @return string|null
 */
function df_report_prefix($m = null, $pref = null) {return df_ccc('--',
	mb_strtolower($pref), !$m ? null : df_cts_lc_camel($m, '-')
);}