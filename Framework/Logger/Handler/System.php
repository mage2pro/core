<?php
namespace Df\Framework\Logger\Handler;
use Df\Cron\Model\LoggerHandler as H;
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
 * Magento 2 does not allow to write plugins to «objects that are instantiated
 * before @see \Magento\Framework\Interception is bootstrapped»:
 * https://devdocs.magento.com/guides/v2.3/extension-dev-guide/plugins.html#limitations
 * 2020-02-08
 * "The https://github.com/royalwholesalecandy/core/issues/57 solution works with Magento 2.2.5,
 * but does not work with Magento 2.3.2.":
 * https://github.com/tradefurniturecompany/core/issues/25#issuecomment-583734975
 * @see \Df\Cron\Model\LoggerHandler
 */
class System extends _P {
	/**
	 * 2019-10-13
	 * @override
	 * @see \Monolog\Handler\AbstractProcessingHandler::handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	function handle(array $d) {return
		H::p($d)
		/**
		 * 2020-02-18
		 * "Prevent Magento from logging the «Unable to send the cookie.
		 * Maximum number of cookies would be exceeded.» message":
		 * https://github.com/tradefurniturecompany/site/issues/53 
		 * @see \Magento\Framework\Stdlib\Cookie\PhpCookieManager::checkAbilityToSendCookie()
		 */
		|| df_starts_with(dfa($d, 'message'), 
			'Unable to send the cookie. Maximum number of cookies would be exceeded.'
		)
		|| parent::handle($d)
	;}
}