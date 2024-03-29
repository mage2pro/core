<?php
namespace Df\Framework\Plugin\Session;
use Magento\Framework\Session\SessionManager as Sb;
# 2017-05-08
final class SessionManager {
	/**
	 * 2017-05-08
	 * Our purpose is to fix the expired session files auto-cleaning if needed.
	 * The auto-cleaning is broken when the filesystem is used to save the sessions
	 * (it is by default, another option is the Magento database),
	 * and these files are stored inside the Magento installation folder (it is by default too).
	 * If the session files are stored in a global operating-system-defined location like «/var/lib/php/sessions»,
	 * then the auto-cleaning is not broken because it is done by the operating system:
	 * 1) «The garbage collection is set up to be done by cron jobs on some systems (i.e. Ubuntu/Debian»
	 * http://stackoverflow.com/a/2952173
	 * 2) «As Ubuntu has its own garbage cleaning via cron (/etc/cron.d/php5) <...>»: http://stackoverflow.com/a/8003905
	 * My previous solution is described here:
	 * https://github.com/magento-russia/3/blob/2016-11-22/app/code/local/Df/Core/Boot.php?ts=4#L155-L188
	 * @see \Magento\Framework\Session\SessionManager::start()
	 * https://github.com/magento/magento2/blob/2.1.6/lib/internal/Magento/Framework/Session/SessionManager.php#L161-L199
	 * «How is the session initialized?» https://mage2.pro/t/3916
	 */
	function beforeStart(Sb $sb):void {
		# 2017-12-30
		# "PHP 7.2: «Warning: ini_set(): A session is active. You cannot change the session module's ini settings at this time
		# in mage2pro/core/Framework/Plugin/Session/SessionManager.php on line 47»":
		# https://github.com/mage2pro/core/issues/64
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
			if ('files' === dfa($o, 'session.save_handler')) {
				# 2023-08-30
				# 1) «Argument 1 passed to df_path_is_internal() must be of the type string, null given,
				# called in vendor/mage2pro/core/Framework/Plugin/Session/SessionManager.php on line 50»:
				# https://github.com/mage2pro/core/issues/226
				# 2) The `session.save_path` option can be absent:
				# https://php.net/manual/session.configuration.php#session.configuration
				# 3) The absent or empty `session.save_path` option means `/tmp` (so it is not an internal Magento path):
				# https://php.net/manual//session.configuration.php#ini.session.save-path
				# 4) The `session.save_path` option supports a format with `;`:
				# https://php.net/manual//session.configuration.php#ini.session.save-path
				/** @var string $p */
				if (($p = df_last(explode(';', dfa($o, 'session.save_path', '')))) && df_path_is_internal($p)) {
					# 2022-11-27 https://php.net/manual/session.configuration.php#ini.session.gc-probability
					ini_set('session.gc_probability', 1);
					# 2022-11-27 https://php.net/manual/session.configuration.php#ini.session.gc-divisor
					ini_set('session.gc_divisor', 100);
				}
			}
		}
	}
}