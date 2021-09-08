<?php
namespace Df\Framework\Logger;
use Df\Core\O;
use Exception as E;
/**
 * 2021-09-08
 * @see \Df\Framework\Logger\Handler\Cookie
 * @see \Df\Framework\Logger\Handler\NoSuchEntity
 */
abstract class Handler {
	/**
	 * 2021-09-08
	 * @used-by p()
	 * @see \Df\Framework\Logger\Handler\Cookie::_p()
	 * @see \Df\Framework\Logger\Handler\NoSuchEntity::_p()
	 * @return bool
	 */
	abstract protected function _p();

	/**
	 * 2021-09-08
	 * @used-by e()
	 * @used-by msg()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 * @return O
	 */
	final protected function d($k = null, $d = null) {return $this->_d->a($k, $d);}

	/**
	 * 2021-09-08
	 * @used-by \Df\Framework\Logger\Handler\NoSuchEntity::_p()
	 * @param string|null $e [optional]
	 * @return E|bool
	 */
	final protected function e($e = null) {
		$r = $this->d('context/exception'); /** @var E|null $r */
		return !$e ? $r : $r instanceof $e;
	}

	/**
	 * 2021-09-08
	 * @used-by \Df\Framework\Logger\Handler\Cookie::_p()
	 * @param string|string[]|null $s [optional]
	 * @return string|bool
	 */
	final protected function msg($s = null) {
		$r = $this->d('message'); /** @var string $r */
		return null === $s ? $r : df_starts_with($r, $s);
	}

	/**
	 * 2021-09-08
	 * @used-by p()
	 * @param O $d
	 */
	private function __construct(O $d) {$this->_d = $d;}

	/**
	 * 2021-09-08
	 * @used-by __construct()
	 * @used-by d()
	 * @var O
	 */
	private $_d;

	/**
	 * 2021-09-08
	 * @used-by \Df\Framework\Logger\Dispatcher::handle()
	 * @param array(string => mixed) $d
	 * @return bool
	 */
	final static function p(array $d) {
		$i = new static::class(new O($d)); /** @var self $i */
		return $i->_p();
	}
}
