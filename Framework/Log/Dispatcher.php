<?php
namespace Df\Framework\Log;
use Df\Cron\Model\LoggerHandler as CronH;
use Df\Framework\Log\Handler\BrokenReference as BrokenReferenceH;
use Df\Framework\Log\Handler\Cookie as CookieH;
use Df\Framework\Log\Handler\Info as InfoH;
use Df\Framework\Log\Handler\JsMap as JsMapH;
use Df\Framework\Log\Handler\Maintenance as MaintenanceH;
use Df\Framework\Log\Handler\NoSuchEntity as NoSuchEntityH;
use Df\Framework\Log\Handler\PayPal as PayPalH;
use Df\Framework\Log\Handler\ReCaptcha as ReCaptchaH;
use Magento\Framework\App\Bootstrap as B;
use Magento\Framework\DataObject as O;
use Magento\Framework\Logger\Handler\System as _P;
use Monolog\Logger as L;
use Psr\Log\LoggerInterface as IL;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
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
	function handle(array $d):bool {/** @var bool $r */
		$rc = new Record($d); /** @var Record $rc */
		$willHandledLater = false; /** @var bool $willHandledLater */
		if (!($r =
			# 2024-03-04
			# "`\Df\Framework\Log\Dispatcher::handle()` should not log Cron errors
			# because they are logged by `\Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()`""
			# https://github.com/mage2pro/core/issues/357
			df_is_cron()
			|| BrokenReferenceH::p($rc)
			|| CronH::p($d)
			|| CookieH::p($rc)
			|| JsMapH::p($rc)
			|| NoSuchEntityH::p($rc)
			|| MaintenanceH::p($rc)
			|| PayPalH::p($rc)
		)) {
			# 2020-08-30
			# "Provide an ability to third-party modules to prevent a message to be logged to `system.log`":
			# https://github.com/mage2pro/core/issues/140
			# 2020-10-04
			# https://github.com/tradefurniturecompany/core/blob/0.3.1/etc/frontend/events.xml#L6-L12
			# https://github.com/tradefurniturecompany/core/blob/0.3.1/Observer/CanLog.php#L23-L34
			df_dispatch('df_can_log', [self::P_MESSAGE => $d, self::P_RESULT => ($o = new O)]); /** @var O $o */
			if (!($r = !!$o[self::V_SKIP])) {
				$e = df_caller_entry(0, function(array $e) {return
					!($c = dfa($e, 'class'))
					/**
					 * 2024-04-15
					 * 1) Previously, I mistakenly used `\Monolog\Logger::class` instead of `\Psr\Log\LoggerInterface::class`.
					 * It was wrong because $c is actually @see \Magento\Framework\Logger\LoggerProxy (in Magento 2.4.4)
					 * and `\Magento\Framework\Logger\LoggerProxy` is not inherited from @see \Monolog\Logger.
					 * 2) "`Df\Framework\Log\Dispatcher::handle()` mistakenly handles exceptions
					 * passed from `Magento\Framework\App\Bootstrap::run()` in Magento 2.4.4":
					 * https://github.com/mage2pro/core/issues/362
					 */
					|| !is_a($c, IL::class, true) && !is_a($c, __CLASS__, true)
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
				if (
					!($willHandledLater = B::class === $c && 'run' === $f)
					# 2024-02-11
					# 1) "The `Monolog\Logger::INFO`-level messages should not be logged as separate files":
					# https://github.com/mage2pro/core/issues/347
					# 2) "Log the ≤ `Monolog\Logger::INFO`-level messages to module-level separate files
					# (instead of `system.log`)": https://github.com/mage2pro/core/issues/348
					&& !InfoH::p($rc)
				) {
					$ef = $rc->ef(); /** @var Th|null $ef */
					$args = [null, $ef ?: $d, $ef ? $rc->extra() : []]; /** @var mixed  $args */
					# 2023-07-25
					# I intentionally do not pass these messages to Sentry
					# because I afraid that they could be too numerous in some third-party websites.
					df_log_l(...$args);
					# 2023-08-01
					# "`Df\Framework\Log\Dispatcher::handle()` should pass to Sentry the records
					# with level ≥ `Monolog\Logger::ERROR` (`ERROR`, `CRITICAL`, `ALERT`, `EMERGENCY`)":
					# https://github.com/mage2pro/core/issues/304
					if (L::ERROR <= $rc->level()) {
						# 2023-12-08
						# 1) Symmetric array destructuring requires PHP ≥ 7.1:
						#		[$a, $b] = [1, 2];
						# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
						# We should support PHP 7.0.
						# https://3v4l.org/3O92j
						# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
						# https://stackoverflow.com/a/28233499
						# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
						[$v, $extra] = [$args[1], $args[2]];
						# 2023-12-08
						# "Set a proper title instead of `[` for Sentry messages like
						# «Unable to proceed: the maintenance mode is enabled»": https://github.com/mage2pro/core/issues/339
						if (is_array($v) && !$extra && ($m = dfa($v, 'message')) && is_string($m)) {/** @var string|null $m */
							$extra = $v;
							$v = $m;
						}
						df_sentry(null, $v, $extra);
					}
				}
				$r = true; # 2020-09-24 The pevious code was: `$r = parent::handle($d);`
			}
		}
		/**
		 * 2023-12-09
		 * 1) Some errors are logged twice: by @see \Df\Framework\Log\Dispatcher::handle()
		 * and @see \Df\Framework\Plugin\AppInterface::beforeCatchException():
		 * https://github.com/mage2pro/core/issues/342
		 * 2) @see \Magento\Framework\App\Bootstrap::run():
		 * 		$this->objectManager->get(LoggerInterface::class)->error($e->getMessage());
		 * 		if (!$application->catchException($this, $e)) {
		 * 			throw $e;
		 * 		}
		 * https://github.com/magento/magento2/blob/2.4.7-beta2/lib/internal/Magento/Framework/App/Bootstrap.php#L269-L272
		 * 3) The call stack:
		 * 3.1) @see \Magento\Framework\App\Bootstrap::run()
		 * 		$this->objectManager->get(LoggerInterface::class)->error($e->getMessage());
		 * https://github.com/magento/magento2/blob/2.4.5/lib/internal/Magento/Framework/App/Bootstrap.php#L269
		 * 3.2) @see \Magento\Framework\Logger\LoggerProxy::error()
		 * 		$this->getLogger()->error($message, $context);
		 * https://github.com/magento/magento2/blob/2.4.5/lib/internal/Magento/Framework/Logger/LoggerProxy.php#L126
		 * 3.3) @see \Monolog\Logger::error()
		 * 		$this->addRecord(static::ERROR, (string) $message, $context);
		 * https://github.com/Seldaek/monolog/blob/2.9.2/src/Monolog/Logger.php#L650
		 * 3.4) @see \Monolog\Logger::addRecord()
		 * 		if (true === $handler->handle($record)) {
		 * https://github.com/Seldaek/monolog/blob/2.9.2/src/Monolog/Logger.php#L399
		 * 4) Magento ≥ 2.4.6 passes the exception to loggers:
		 * 		$context = $this->addExceptionToContext($message, $context);
		 * https://github.com/magento/magento2/blob/2.4.6/lib/internal/Magento/Framework/Logger/LoggerProxy.php#L129
		 * We can not use it because we need to support outdated Magento versions.
		 * 5) If @see \Df\Framework\Log\Dispatcher::handle() skips an exception,
		 * then @see \Df\Framework\Plugin\AppInterface::beforeCatchException() should skip it too:
		 * https://github.com/mage2pro/core/issues/343
		 */
		if (!$willHandledLater) {
			Latest::register($rc);
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