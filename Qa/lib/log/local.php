<?php
use Df\Qa\Failure\Exception as QE;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
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