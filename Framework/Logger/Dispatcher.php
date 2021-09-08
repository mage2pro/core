<?php
namespace Df\Framework\Logger;
use Df\Cron\Model\LoggerHandler as H;
use Df\Framework\Logger\Handler\Cookie as CookieH;
use Df\Framework\Logger\Handler\NoSuchEntity as NoSuchEntityH;
use Df\Framework\Logger\Handler\PayPal as PayPalH;
use Magento\Framework\DataObject as O;
# 2021-09-08 https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Exception/AlreadyExistsException.php
use Magento\Framework\Exception\AlreadyExistsException as AlreadyExists;
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
class Dispatcher extends _P {
	/**
	 * 2019-10-13
	 * @override
	 * @see \Monolog\Handler\AbstractProcessingHandler::handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	function handle(array $d) {
		if (!($r = H::p($d) || CookieH::p($d) || NoSuchEntityH::p($d) || PayPalH::p($d))) {
			# 2020-08-30
			# "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
			# https://github.com/mage2pro/core/issues/140
			# 2020-10-04
			# https://github.com/tradefurniturecompany/core/blob/0.3.1/etc/frontend/events.xml#L6-L12
			# https://github.com/tradefurniturecompany/core/blob/0.3.1/Observer/CanLog.php#L23-L34
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