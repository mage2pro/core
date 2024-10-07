<?php
namespace Df\Framework\Log;
/**
 * 2021-09-08
 * @see \Df\Framework\Log\Handler\BrokenReference
 * @see \Df\Framework\Log\Handler\Cookie
 * @see \Df\Framework\Log\Handler\Info
 * @see \Df\Framework\Log\Handler\JsMap
 * @see \Df\Framework\Log\Handler\Maintenance
 * @see \Df\Framework\Log\Handler\NoSuchEntity
 * @see \Df\Framework\Log\Handler\PayPal
 * @see \Df\Framework\Log\Handler\ReCaptcha
 */
abstract class Handler {
	/**
	 * 2021-09-08
	 * @used-by self::p()
	 * @see \Df\Framework\Log\Handler\BrokenReference::_p()
	 * @see \Df\Framework\Log\Handler\Cookie::_p()
	 * @see \Df\Framework\Log\Handler\Info::_p()
	 * @see \Df\Framework\Log\Handler\JsMap::_p()
	 * @see \Df\Framework\Log\Handler\NoSuchEntity::_p()
	 * @see \Df\Framework\Log\Handler\PayPal::_p()
	 * @see \Df\Framework\Log\Handler\ReCaptcha::_p()
	 */
	abstract protected function _p():bool;

	/**
	 * 2021-08-09
	 * @used-by \Df\Framework\Log\Handler\BrokenReference::_p()
	 * @used-by \Df\Framework\Log\Handler\Cookie::_p()
	 * @used-by \Df\Framework\Log\Handler\Info::_p()
	 * @used-by \Df\Framework\Log\Handler\JsMap::_p()
	 * @used-by \Df\Framework\Log\Handler\Maintenance::_p()
	 * @used-by \Df\Framework\Log\Handler\NoSuchEntity::_p()
	 * @used-by \Df\Framework\Log\Handler\PayPal::_p()
	 */
	final protected function r():Record {return $this->_r;}

	/**
	 * 2021-09-08
	 * @used-by self::p()
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
	 */
	final static function p(Record $r):bool {
		$i = new static($r); /** @var self $i */
		return $i->_p();
	}
}