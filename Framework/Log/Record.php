<?php
namespace Df\Framework\Log;
use Df\Core\O;
use Exception as E;
use Monolog\LogRecord as MR;
# 2021-09-08
final class Record {
	/**
	 * 2021-09-08
	 * @used-by \Df\Cron\Model\LoggerHandler::p()
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @param MR|array(string => mixed) $d
	 */
	function __construct($d) {
		if (df_class_exists(MR::class) && $d instanceof MR) {
			$this->_source = $d;
			$d = $d->toArray();
		}
		$this->_d = new O($d);
	}

	/**
	 * 2024-02-11
	 * @used-by self::source()
	 * @used-by \Df\Framework\Log\Handler\Info::_p()
	 */
	function a():array {return $this->_d->a();}

	/**
	 * 2021-09-08
	 * @used-by self::ef()
	 * @used-by self::emsg()
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @used-by \Df\Framework\Log\Handler\NoSuchEntity::_p()
	 * @param string|null $e [optional]
	 * @return E|null|bool
	 */
	function e($e = null) {
		$r = $this->d('context/exception'); /** @var E|string|null $r */
		/**
		 * 2026-02-14
		 * 1) «df_xf(): Argument #1 ($t) must be of type Throwable, string given,
		 * called in vendor/mage2pro/core/Framework/Log/Record.php on line 48»
		 * on `/rest/default/V1/carts/mine/payment-information` in Magento 2.4.8-p3:
		 * https://github.com/mage2pro/core/issues/465
		 */
		return !$r || !$e ? $r : $r instanceof $e;
	}

	/**
	 * 2023-08-01
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @return E|null
	 */
	function ef() {return !($e = $this->e()) ? null : df_xf($e);}

	/**
	 * 2024-10-07
	 * @see self::msg()
	 * @used-by \Df\Framework\Log\Handler\ReCaptcha::_p()
	 */
	function emsg(string $s):bool {return
		($e = $this->e()) && df_starts_with(df_xts($e), $s) /** @var E|null $e */
	;}

	/**
	 * 2023-08-01
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 */
	function extra():array {return $this->d('extra');}

	/**
	 * 2023-08-01
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @used-by \Df\Framework\Log\Handler\BrokenReference::_p()
	 * @used-by \Df\Framework\Log\Handler\Info::_p()
	 * @used-by \Df\Framework\Log\Latest\Record::id()
	 */
	function level():int {return $this->d('level');}

	/**
	 * 2021-09-08
	 * @see self::emsg()
	 * @used-by \Df\Cron\Model\LoggerHandler::p()
	 * @used-by \Df\Framework\Log\Handler\BrokenReference::_p()
	 * @used-by \Df\Framework\Log\Handler\Cookie::_p()
	 * @used-by \Df\Framework\Log\Handler\JsMap::_p()
	 * @used-by \Df\Framework\Log\Handler\Maintenance::_p()
	 * @used-by \Df\Framework\Log\Handler\PayPal::_p()
	 * @used-by \Df\Framework\Log\Latest\Record::id()
	 * @param string|string[]|null $s [optional]
	 * @return string|bool
	 */
	function msg($s = '') {
		$r = $this->d('message'); /** @var string $r */
		return df_nes($s) ? $r : df_starts_with($r, $s);
	}

	/**
	 * 2026-01-28
	 * @used-by \Df\Framework\Log\Handler\Info::_p()
	 * @return MR|array(string => mixed)
	 */
	function source() {return $this->_source ?: $this->a();}

	/**
	 * 2021-09-08
	 * @used-by self::e()
	 * @used-by self::extra()
	 * @used-by self::level()
	 * @used-by self::msg()
	 * @return string|null
	 */
	private function d(string $k) {return $this->_d->a($k);}

	/**
	 * 2021-09-08
	 * @used-by self::__construct()
	 * @used-by self::a()
	 * @used-by self::d()
	 * @var O
	 */
	private $_d;
	/**
	 * 2026-01-28
	 * @used-by self::__construct()
	 * @used-by self::source()
	 * @var ?MR
	 */
	private $_source;
}