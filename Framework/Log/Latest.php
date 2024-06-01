<?php
namespace Df\Framework\Log;
use Df\Framework\Log\Latest\O;
use Df\Framework\Log\Latest\Record as LR;
use Df\Framework\Log\Latest\Throwable as LT;
use \Throwable As T;
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
 */
final class Latest {
	/**
	 * 2023-12-09
	 * @used-by \Df\Framework\Plugin\AppInterface::beforeCatchException()
	 * @param T|Record $o
	 */
	static function registered($o):bool {
		$v = self::$v;
		self::$v = null;
		return !df_nes($v) && $v === self::o($o)->id();
	}

	/**
	 * 2023-12-09
	 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun() (https://github.com/mage2pro/core/issues/397)
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @used-by \Df\Widget\Plugin\Block\Adminhtml\Widget\Options::aroundAddFields()
	 * @param T|Record $o
	 */
	static function register($o):void {self::$v = self::o($o)->id();}

	/**
	 * 2024-03-04
	 * @used-by self::register()
	 * @used-by self::registered()
	 * @param T|Record $o
	 */
	private static function o($o):O {return $o instanceof T ? new LT($o) : ($o instanceof Record ? new LR($o) : df_error());}

	/**
	 * 2023-12-09
	 * @used-by self::register()
	 * @used-by self::registered()
	 * @var string|null
	 */
	private static $v;
}