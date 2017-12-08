<?php
namespace Df\API;
use Df\API\Document as D;
// 2017-07-13
final class Operation implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @param D $req
	 * @param D $res
	 */
	function __construct(D $req, D $res) {$this->_req = $req; $this->_res = $res;}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
	 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
	 * @used-by \Dfe\Moip\API\Facade\Notification::targets()
	 * @used-by \Dfe\Moip\Facade\O::toArray()
	 * @used-by \Dfe\Square\Facade\Charge::refund()
	 * @used-by \Dfe\Square\Facade\O::toArray()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return $this->_res->a($k, $d);}

	/**
	 * 2017-07-13
	 * @return string
	 */
	function j() {return $this->_res->j();}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $k
	 * @return bool
	 */
	function offsetExists($k) {return $this->_res->offsetExists($k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $k
	 * @return array(string => mixed)|mixed|null
	 */
	function offsetGet($k) {return $this->_res->offsetGet($k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v) {$this->_res->offsetSet($k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k) {$this->_res->offsetUnset($k);}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function req($k = null, $d = null) {return $this->_req->a($k, $d);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by req()
	 * @var D
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
	 * @var D
	 */
	private $_res;
}


