<?php
namespace Df\Framework\Log;
use Monolog\Logger as L;
use \Exception as E;
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
	 */
	static function registered(E $e):bool {
		$v = self::$v;
		self::$v = null;
		return $v === $e->getMessage();
	}

	/**
	 * 2023-12-09
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 */
	static function register(Record $r):void {
		if (L::ERROR === $r->level()) {
			self::$v = $r->msg();
		}
	}

	/**
	 * 2023-12-09
	 * @used-by self::register()
	 * @var string|null
	 */
	private static $v;
}