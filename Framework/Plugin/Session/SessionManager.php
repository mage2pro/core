<?php
namespace Df\Framework\Plugin\Session;
use Magento\Framework\Session\SessionManager as Sb;
// 2017-05-08
final class SessionManager {
	/**
	 * 2017-05-08
	 * Our purpose is to fix the expired session files autocleaning if needed.
	 * The autocleaning is broken when filesystem is used to save the sessions
	 * (is is by default, another option is the Magento database),
	 * and these files are store inside the Magento installation folder (it is by default too).
	 * If the session files are stored in a global operating system defined location
	 * like «/var/lib/php/sessions», then the autocleaning is not broken
	 * because it is doing by the operating system:
	 * 1) «the garbage collection is set up to be done by cron jobs on some systems (i.e. Ubuntu/Debian»
	 * http://stackoverflow.com/a/2952173
	 * 2) «As Ubuntu has it's own garbage cleaning via cron (/etc/cron.d/php5) <...>»
	 * http://stackoverflow.com/a/8003905
	 * My previous solution is described here:
	 * https://github.com/magento-russia/3/blob/2016-11-22/app/code/local/Df/Core/Boot.php?ts=4#L155-L188
	 * @see \Magento\Framework\Session\SessionManager::start()
	 * https://github.com/magento/magento2/blob/2.1.6/lib/internal/Magento/Framework/Session/SessionManager.php#L161-L199
	 * «How is the session initialized?» https://mage2.pro/t/3916
	 * @param Sb $sb
	 */
	function beforeStart(Sb $sb) {
		/**
		 * 2017-12-30
		 * "PHP 7.2: «Warning: ini_set(): A session is active.
		 * You cannot change the session module's ini settings at this time
		 * in mage2pro/core/Framework/Plugin/Session/SessionManager.php on line 47»":
		 * https://github.com/mage2pro/core/issues/64
		 */
		if (!$sb->isSessionExists()) {
			/**
			 * 2017-05-08
			 * @uses \Magento\Framework\Session\Config::getOptions() returns an array like:
			 *	{
			 *		"session.cookie_domain": "localhost.com",
			 *		"session.cookie_httponly": true,
			 *		"session.cookie_lifetime": 3600,
			 *		"session.cookie_path": "/",
			 *		"session.cookie_secure": true,
			 *		"session.save_handler": "files",
			 *		"session.save_path": "C:/work/portal/code/var/session/"
			 *	}
			 * The «session.save_handler» option is set here:
			 * @see \Magento\Framework\Session\SaveHandler::setSaveHandler()
			 * 		$this->getConfig()->setOption('session.save_handler', $saveHandler);
			 * https://github.com/magento/magento2/blob/2.1.6/lib/internal/Magento/Framework/Session/SaveHandler.php#L157
			 * @var array(string => string|int|bool) $o
			 */
			$o = df_session_config()->getOptions();
			if ('files' === dfa($o, 'session.save_handler') && df_path_is_internal(dfa($o, 'session.save_path'))) {
				ini_set('session.gc_probability', 1);
				ini_set('session.gc_divisor', 100);
			}
		}
	}
}

