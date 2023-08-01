<?php
namespace Df\Framework\Log;
use Df\Cron\Model\LoggerHandler as CronH;
use Df\Framework\Log\Handler\Cookie as CookieH;
use Df\Framework\Log\Handler\NoSuchEntity as NoSuchEntityH;
use Df\Framework\Log\Handler\PayPal as PayPalH;
use Exception as E;
use Magento\Framework\App\Bootstrap as B;
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
	 * 2021-21-21
	 * 1) "«Declaration of Df\Framework\Log\Dispatcher::handle(array $d)
	 * must be compatible with Monolog\Handler\AbstractProcessingHandler::handle(array $record): bool» in Magento 2.4.3":
	 * https://github.com/mage2pro/core/issues/166
	 * 2) @see \Df\Cron\Model\LoggerHandler::handle()
	 * @override
	 * @see \Monolog\Handler\AbstractProcessingHandler::handle()
	 * @param array(string => mixed) $d
	 */
	function handle(array $d):bool {
		$rc = new Record($d); /** @var Record $rc */
		if (!($r = CronH::p($d) || CookieH::p($rc) || NoSuchEntityH::p($rc) || PayPalH::p($rc))) {
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
				$c = dfa($e, 'class'); /** @var string|null c */
				$f = dfa($e, 'function'); /** @var string|null $f */
				/**
				 * 2021-10-04
				 * 1) @see \Magento\Framework\App\Bootstrap::run():
				 *		$this->objectManager->get(LoggerInterface::class)->error($e->getMessage());
				 * It is handled in @see \Df\Framework\Plugin\AppInterface::beforeCatchException()
				 * 2) "The backtrace is not logged for «no class registered for scheme» errors":
				 * https://github.com/mage2pro/core/issues/160
				 */
				if (B::class != $c || 'run' !== $f) {
					# 2023-07-25
					# I intentionally do not pass these messages to Sentry
					# because I afraid that they could be too numerous in some third-party websites.
					df_log_l(
						$c
						/** @var E|null $prev */
						,df_clean($d) + (!$rc->e(AlreadyExists::class) || !($prev = $rc->e()->getPrevious()) ? [] : [
							'prev' => $prev->getMessage()
						])
						,$f
						,$rc->e() ? 'exception' : dfa($d, 'level_name')
					);
					/**
					 * 2023-08-01
					 * "`Df\Framework\Log\Dispatcher::handle()` should pass to Sentry the records
					 * with level ≥ @see \Monolog\Logger::ERROR (`ERROR`, `CRITICAL`, `ALERT`, `EMERGENCY`)":
					 * https://github.com/mage2pro/core/issues/304
					 */
					if (L::ERROR <= $rc->level()) {
						df_sentry(null, ($ef = $rc->ef()) ?: $d, $ef ? $rc->extra() : []); /** @var E $ef */
					}
				}
				$r = true; # 2020-09-24 The pevious code was: `$r = parent::handle($d);`
			}
		}
		return $r;
	}

	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by self::handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const P_MESSAGE = 'message';
	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by self::handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const P_RESULT = 'result';
	/**
	 * 2020-08-30
	 * "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
	 * https://github.com/mage2pro/core/issues/140
	 * @used-by self::handle()
	 * @used-by \TFC\Core\Observer\CanLog::execute()
	 */
	const V_SKIP = 'skip';
}