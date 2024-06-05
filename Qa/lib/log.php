<?php
use Df\Qa\Failure\Exception as QE;
use Magento\Framework\DataObject as _DO;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * @used-by df_caller_m()
 * @used-by df_error()
 * @used-by ikf_endpoint()	inkifi.com
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by \Df\Framework\Plugin\AppInterface::beforeCatchException() (https://github.com/mage2pro/core/issues/160)
 * @used-by \Df\Framework\Plugin\App\Response\HttpInterface::beforeSetBody()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Paypal\Plugin\Model\Payflow\Service\Response\Validator\ResponseValidator::log()
 * @used-by \Df\Qa\Failure::log()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Widget\Plugin\Block\Adminhtml\Widget\Options::aroundAddFields() (https://github.com/mage2pro/core/issues/397)
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @used-by \Dfe\GoogleFont\Fonts\Png::url()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Dfe\Sift\Observer::f()
 * @param _DO|mixed[]|mixed|T $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null, array $d = [], string $suf = ''):void {
	$isT = df_is_th($v); /** @var bool $isT */
	$m = $m ? df_module_name($m) : ($isT ? df_caller_module($v) : df_caller_module());
	df_log_l($m, ...($isT ? [$v, $d] : [!$d ? $v : (dfa_merge_r($d, is_array($v) ? $v : ['message' => $v])), $suf]));
	df_sentry($m, ...($isT || !is_array($v) ? [$v, $d] : ['', dfa_merge_r($d, $v)]));
}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by dfp_report()
 * @used-by \Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \Amasty\Checkout\Model\Optimization\LayoutJsDiffProcessor::moveArray(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/120)
 * @used-by \Auctane\Api\Model\Action\Export::addXmlElement(caremax.com.au, https://github.com/caremax-com-au/site/issues/1)
 * @used-by \CanadaSatellite\Bambora\Facade::api() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \Customweb\RealexCw\Helper\InvoiceItem::getInvoiceItems()	tradefurniturecompany.co.uk
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @used-by \Df\Framework\Plugin\View\Asset\Source::aroundGetContent()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Sentry\Client::send_http()
 * @used-by \Df\Store\Plugin\Model\App\Emulation::beforeStartEnvironmentEmulation()
 * @used-by \Dfe\Klarna\Api\Checkout::_html()
 * @used-by \Hotlink\Brightpearl\Model\Api\Transport::_submit() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/site/issues/122)
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/27)
 * @used-by \Mageside\CanadaPostShipping\Model\Carrier::_doRatesRequest() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/25)
 * @used-by \Wyomind\SimpleGoogleShopping\Model\Observer::checkToGenerate(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/26)
 * @param string|object|null $m
 * @param string|mixed[]|T $p2
 * @param string|mixed[]|T $p3 [optional]
 */
function df_log_l($m, $p2, $p3 = [], string $p4 = ''):void {
	/** @var T|null $t */ /** @var array|string|mixed $d */ /** @var string $suf */ /** @var string $pref */
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$t, $d, $suf, $pref] = df_is_th($p2) ? [$p2, $p3, $p4, ''] : [null, $p2, df_ets($p3), $p4];
	$m = $m ?: ($t ? df_caller_module($t) : df_caller_module());
	if (!$suf) {
		# 2023-07-26
		# 1) "If `df_log_l()` is called from a `*.phtml`,
		# then the `*.phtml`'s base name  should be used as the log file name suffix instead of `df_log_l`":
		# https://github.com/mage2pro/core/issues/269
		# 2) 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
		$entry = $t ? df_caller_entry_m($t) : df_caller_entry(0, null, ['df_log']); /** @var array(string => string|int) $entry */
		$suf = df_bt_entry_is_phtml($entry) ? basename(df_bt_entry_file($entry)) : df_bt_entry_func($entry);
	}
	$c = df_context(); /** @var array(string => mixed) $c */
	df_report(
		df_ccc('--', 'mage2.pro/' . df_ccc('-', df_report_prefix($m, $pref), '{date}--{time}'), $suf) .  '.log'
		# 2023-07-26
		# "`df_log_l()` should use the exception's trace instead of `df_bt_s(1)` for exceptions":
		# https://github.com/mage2pro/core/issues/261
		,df_cc_n(
			# 2023-07-28
			# "`df_log_l` does not log the context if the message is not an array":
			# https://github.com/mage2pro/core/issues/289
			/** @uses df_dump_ds() */
			df_map('df_dump_ds', !$d ? [$c] : (is_array($d) ? [dfa_merge_r($d, ['Mage2.PRO' => $c])] : [$d, $c]))
			,!$t ? '' : ['EXCEPTION', QE::i($t)->report()]
			,$t ? null : "\n" . df_bt_s(1)
		)
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
 * 2023-07-19
 * «mb_strtolower(): Passing null to parameter #1 ($string) of type string is deprecated
 * in vendor/mage2pro/core/Qa/lib/log.php on line 122»: https://github.com/mage2pro/core/issues/233
 * @used-by df_log_l()
 * @used-by \Df\Framework\Log\Handler\Info::lb()
 * @param string|object|null $m [optional]
 */
function df_report_prefix($m = null, string $pref = ''):string {return df_ccc('--',
	mb_strtolower($pref), !$m ? null : df_module_name_lc($m, '-')
);}