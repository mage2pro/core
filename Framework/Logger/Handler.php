<?php
namespace Df\Framework\Logger;
use Df\Cron\Model\LoggerHandler as H;
use Exception as E;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject as O;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\Logger\Handler\System as _P;
use Monolog\Logger as L;
/**
 * 2019-10-13
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * 1) "Disable the logging of «Add of item with id %s was processed» messages to `system.log`":
 * https://github.com/kingpalm-com/core/issues/36
 * 2) @see \Magento\Backend\Model\Menu::add()
 * 3) It is impossible to write a plugin to any of this:
 * @see \Magento\Framework\Logger\Handler\System
 * @see \Magento\Framework\Logger\Handler\Base
 * @see \Monolog\Handler\AbstractProcessingHandle
 * @see \Psr\Log\LoggerInterface
 * It leads to the error: «Circular dependency:
 * Magento\Framework\Logger\Monolog depends on Magento\Framework\Cache\InvalidateLogger and vice versa.»
 * Magento 2 does not allow to write plugins to «objects that are instantiated
 * before @see \Magento\Framework\Interception is bootstrapped»:
 * https://devdocs.magento.com/guides/v2.3/extension-dev-guide/plugins.html#limitations
 * 2020-02-08
 * "The https://github.com/royalwholesalecandy/core/issues/57 solution works with Magento 2.2.5,
 * but does not work with Magento 2.3.2.":
 * https://github.com/tradefurniturecompany/core/issues/25#issuecomment-583734975
 * @see \Df\Cron\Model\LoggerHandler
 * 2020-08-31 Despite of the name, this handler processes the messages of all levels by default (including exceptions).
 */
class Handler extends _P {
	/**
	 * 2019-10-13
	 * @override
	 * @see \Monolog\Handler\AbstractProcessingHandler::handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	function handle(array $d) {
		if (!($r = H::p($d) || $this->cookie($d) || $this->nse($d) || $this->paypal($d))) {
			# 2020-08-30
			# "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
			# https://github.com/mage2pro/core/issues/140
			df_dispatch('df_can_log', [self::P_MESSAGE => $d, self::P_RESULT => ($o = new O)]); /** @var O $o */
			if (!($r = !!$o[self::V_SKIP])) {
				$e = df_caller_entry(0, function(array $e) {return
					!($c = dfa($e, 'class')) || !is_a($c, L::class, true) && !is_a($c, __CLASS__, true)
				;}); /** @var array(string => int) $e */
				df_log_l(dfa($e, 'class'), df_clean($d), dfa($e, 'function'),
					dfa($d, 'context/exception') ? 'exception' : dfa($d, 'level_name')
				);
				$r = true; # 2020-09-24 The pevious code was: `$r = parent::handle($d);`
			}
		}
		return $r;
	}

	/**
	 * 2020-02-18, 2020-02-21
	 * "Prevent Magento from logging the «Unable to send the cookie. Maximum number of cookies would be exceeded.» message":
	 * https://github.com/tradefurniturecompany/site/issues/53
	 * @see \Magento\Framework\Stdlib\Cookie\PhpCookieManager::checkAbilityToSendCookie()
	 * @used-by handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	private function cookie(array $d) {return df_starts_with(dfa($d, 'message'),
		'Unable to send the cookie. Maximum number of cookies would be exceeded.'
	);}

	/**
	 * 2020-02-21
	 * 1) "\Magento\Checkout\Model\Session::getQuote() should not log
	 * the «No such entity with customerId = ...» exception because it occurs in an expected normal case":
	 * https://github.com/tradefurniturecompany/site/issues/17
	 * 2) @see \Magento\Framework\Exception\NoSuchEntityException::singleField():
	 *		public static function singleField($fieldName, $fieldValue) {
	 *			return new self(
	 *				new Phrase('No such entity with %fieldName = %fieldValue', [
	 *					'fieldName' => $fieldName,
	 *					'fieldValue' => $fieldValue
	 *				])
	 *			);
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Exception/NoSuchEntityException.php#L29-L47
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/Exception/NoSuchEntityException.php#L41-L59
	 * 3) The problem is fixed in Magento 2.3.4 by the commit: https://github.com/magento/magento2/commit/5f3b86ab
	 * Magento 2.3.4 does not log the exception @see \Magento\Checkout\Model\Session::getQuote():
	 * https://github.com/magento/magento2/blob/2.3.4/app/code/Magento/Checkout/Model/Session.php#L280-L282
	 * 4) @see \Magento\Checkout\Model\Session::getQuote() in Magento < 2.3.4 logs the
	 * @see \Magento\Framework\Exception\NoSuchEntityException exception:
	 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Model/Session.php#L276-L278
	 * @used-by handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	private function nse(array $d) {return /** @var NSE|E|null $e */
		($e = dfa($d, 'context/exception')) instanceof NSE && df_find(function(array $d) {return
			Session::class === dfa($d, 'class') && 'getQuote' === dfa($d, 'function')
		;}, $e->getTrace())
	;}

	/**
	 * 2020-06-24
	 * @used-by handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	private function paypal(array $d) {return df_starts_with(dfa($d, 'message'), [
		/**
		 * 2020-06-24
		 * "Prevent logging the PayPal 10486 error «Exception message: We can't initialize Express Checkout.»
		 * to `system.log`": https://github.com/mage2pro/core/issues/101
		 * @see \Magento\Framework\Message\Manager::addExceptionMessage():
		 *		$message = sprintf(
		 *			'Exception message: %s%sTrace: %s',
		 *			$exception->getMessage(),
		 *			"\n",
		 *			Debug::trace(
		 *				$exception->getTrace(),
		 *				true,
		 *				true,
		 *				(bool)getenv('MAGE_DEBUG_SHOW_ARGS')
		 *			)
		 *		);
		 *		$this->logger->critical($message);
		 * https://github.com/magento/magento2/blob/2.3.5-p1/lib/internal/Magento/Framework/Message/Manager.php#L293-L305
		 */
		"Exception message: We can't initialize Express Checkout."
		/**
		 * 2020-06-24
		 * "Prevent logging the PayPal 10486 error «Please redirect your customer to PayPal» to `system.log`":
		 * https://github.com/mage2pro/core/issues/100
		 * @see \Magento\Paypal\Model\Api\Nvp::_handleCallErrors():
		 *		$exceptionLogMessage = sprintf(
		 *			'PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.',
		 *			$errorMessages,
		 *			isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
		 *			isset($response['VERSION']) ? $response['VERSION'] : ''
		 *		);
		 *		$this->_logger->critical($exceptionLogMessage);
		 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/Paypal/Model/Api/Nvp.php#L1281-L1287
		 */
		,"PayPal NVP gateway errors: This transaction couldn't be completed. Please redirect your customer to PayPal (#10486: This transaction couldn't be completed)"
	]);}

	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const P_MESSAGE = 'message';
	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const P_RESULT = 'result';
	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const V_SKIP = 'skip';
}