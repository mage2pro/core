<?php
namespace Df\API;
use Df\Core\O;
# 2017-07-13
final class Operation implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by \Df\API\Facade::p()
	 * @param O $req
	 * @param O $res
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
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return $this->_res->a($k, $d);}

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
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $k
	 */
	function offsetGet($k):mixed {return $this->_res->offsetGet($k);}

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
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function req($k = null, $d = null) {return $this->_req->a($k, $d);}

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
	 * @used-by __construct()
	 * @used-by req()
	 * @var O
	 */
	private $_req;

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @used-by j()
	 * @used-by offsetExists()
	 * @used-by offsetGet()
	 * @used-by offsetSet()
	 * @used-by offsetUnset()
	 * @used-by res()
	 * @var O
	 */
	private $_res;
}