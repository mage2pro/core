<?php
namespace Df\Framework\Logger\Handler;
use Magento\Framework\Logger\Handler\System as _P;
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
 * Magento 2 does not allow to write plugins to «objects that are instantiated before \
 * Magento\Framework\Interception is bootstrapped»:
 * https://devdocs.magento.com/guides/v2.3/extension-dev-guide/plugins.html#limitations -->
 */
class System extends _P {
	/**
	 * 2019-10-13
	 * @override
	 * @see \Monolog\Handler\AbstractProcessingHandler::handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	function handle(array $d) {
		$m = dfa($d, 'message'); /** @var string $m */
		return
			df_starts_with($m, 'Add of item with id') && df_ends_with($m, 'was processed')
			|| df_starts_with($m, 'Item') && df_ends_with($m, 'was removed')
			/**
			 * 2019-10-13
			 * I return `true` to prevent bubling to other loggers:
			 * @see \Monolog\Logger::addRecord():
			 *		while ($handler = current($this->handlers)) {
			 *			if (true === $handler->handle($record)) {
			 *				break;
			 *			}
			 *			next($this->handlers);
			 *		}
			 */
			? true 
			: parent::handle($d)
		;
	}
}