<?php
namespace Df\API;
use Df\Core\O;
# 2017-07-13
final class Operation implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by \Df\API\Facade::p()
	 */
	function __construct(O $req, O $res) {$this->_req = $req; $this->_res = $res;}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\AlphaCommerceHub\API\Facade\PayPal::capture()
	 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
	 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
	 * @used-by \Dfe\Moip\API\Facade\Notification::targets()
	 * @used-by \Dfe\Moip\Facade\O::toArray()
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::t04_delete_all()
	 * @used-by \Dfe\Square\API\Facade\Location::map()
	 * @used-by \Dfe\Square\Facade\Charge::refund()
	 * @used-by \Dfe\Square\Facade\O::toArray()
	 * @used-by \Dfe\TBCBank\API\Facade::check()
	 * @used-by \Dfe\TBCBank\Facade\O::toArray()
	 * @used-by \Dfe\Vantiv\Facade\O::toArray()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t05()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t06()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t07()
	 * @param string|string[] $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = '', $d = null) {return $this->_res->a($k, $d);}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Sift\Test\CaseT\API\Account\Decisions::t01()
	 */
	function j():string {return $this->_res->j();}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $k
	 */
	function offsetExists($k):bool {return $this->_res->offsetExists($k);}

	/**
	 * 2017-07-13
	 * 2022-10-24
	 * 1) `mixed` as a return type is not supported by PHP < 8:
	 * https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * 2) `ReturnTypeWillChange` allows us to suppress the return type absence notice:
	 * https://github.com/mage2pro/core/issues/168#user-content-absent-return-type-deprecation
	 * https://github.com/mage2pro/core/issues/168#user-content-returntypewillchange
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $k
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	function offsetGet($k) {return $this->_res->offsetGet($k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v):void {$this->_res->offsetSet($k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k):void {$this->_res->offsetUnset($k);}

	/**
	 * 2017-07-13
	 * @used-by \CanadaSatellite\Bambora\Action::check() (https://github.com/canadasatellite-ca/bambora)
	 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
	 * @param string|string[] $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function req($k = '', $d = null) {return $this->_req->a($k, $d);}

	/**
	 * 2019-04-05
	 * @used-by \CanadaSatellite\Bambora\Action::check() (https://github.com/canadasatellite-ca/bambora)
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImage::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImages::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Get::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Submit::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 */
	function res():O {return $this->_res;}

	/**
	 * 2017-07-13
	 * @used-by self::__construct()
	 * @used-by self::req()
	 * @var O
	 */
	private $_req;

	/**
	 * 2017-07-13
	 * @used-by self::__construct()
	 * @used-by self::a()
	 * @used-by self::j()
	 * @used-by self::offsetExists()
	 * @used-by self::offsetGet()
	 * @used-by self::offsetSet()
	 * @used-by self::offsetUnset()
	 * @used-by self::res()
	 * @var O
	 */
	private $_res;
}