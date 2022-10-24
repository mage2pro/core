<?php
namespace Df\Framework\Log;
/**
 * 2021-09-08
 * @see \Df\Framework\Log\Handler\Cookie
 * @see \Df\Framework\Log\Handler\NoSuchEntity
 * @see \Df\Framework\Log\Handler\PayPal
 */
abstract class Handler {
	/**
	 * 2021-09-08
	 * @used-by self::p()
	 * @see \Df\Framework\Log\Handler\Cookie::_p()
	 * @see \Df\Framework\Log\Handler\NoSuchEntity::_p()
	 * @return bool
	 */
	abstract protected function _p();

	/**
	 * 2021-08-09
	 * @used-by \Df\Framework\Log\Handler\Cookie::_p()
	 * @used-by \Df\Framework\Log\Handler\NoSuchEntity::_p()
	 * @used-by \Df\Framework\Log\Handler\PayPal::_p()
	 * @return Record
	 */
	final protected function r() {return $this->_r;}

	/**
	 * 2021-09-08
	 * @used-by self::p()
	 * @param Record $r
	 */
	private function __construct(Record $r) {$this->_r = $r;}

	/**
	 * 2021-09-08
	 * @used-by self::__construct()
	 * @used-by self::r()
	 * @var Record
	 */
	private $_r;

	/**
	 * 2021-09-08
	 * @used-by \Df\Framework\Log\Dispatcher::handle()
	 * @param Record $r
	 * @return bool
	 */
	final static function p(Record $r) {
		$i = new static($r); /** @var self $i */
		return $i->_p();
	}
}
