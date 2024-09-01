<?php
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
 *
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