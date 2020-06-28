<?php
namespace Df\Sentry\Breadcrumbs;
use Df\Sentry\Client as C;
final class ErrorHandler {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::registerDefaultBreadcrumbHandlers()
	 * @param C $c
	 */
	function __construct(C $c) {$this->_c = $c;}

	/**
	 * 2020-06-28
	 * @used-by \Df\Sentry\Client::registerDefaultBreadcrumbHandlers()
	 */
	function install() {$this->_prev = set_error_handler(
		/**
		 * 2017-07-10
		 * @param int $code
		 * @param string $m
		 * @param string $file
		 * @param int $line
		 * @param array $context
		 * @return bool|mixed
		 */
		function($code, $m, $file = '', $line = 0, $context=[]) {
			// 2017-07-10
			// «Magento 2.1 php7.1 will not be supported due to mcrypt deprecation»
			// https://github.com/magento/magento2/issues/5880
			// [PHP 7.1] How to fix the «Function mcrypt_module_open() is deprecated» bug?
			// https://mage2.pro/t/2392
			if (E_DEPRECATED !== $code || !df_contains($m, 'mcrypt') && !df_contains($m, 'mdecrypt')) {
				$this->_c->breadcrumbs->record([
					'category' => 'error_reporting',
					'message' => $m,
					'level' => $this->_c->translateSeverity($code),
					'data' => ['code' => $code, 'line' => $line, 'file' => $file]
				]);
			}
			return !$this->_prev ? false : call_user_func($this->_prev, $code, $m, $file, $line, $context);
		}, E_ALL);
	}

	/**
	 * 2020-06-28
	 * @used-by __construct()
	 * @used-by install()
	 * @var C
	 */
	private $_c;

	/**
	 * 2020-06-28
	 * @used-by install()
	 * @var callable
	 */
	private $_prev;
}
