<?php
namespace Df\API;
// 2017-07-13
final class Document implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by \Df\API\Facade::p()
	 * @param array(string => mixed) $a
	 */
	function __construct(array $a) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return dfak($this->_a, $k, $d);}

	/**
	 * 2017-07-13
	 * @used-by \Df\API\Operation::j()
	 * @return string
	 */
	function j() {return df_json_encode($this->_a);}

	/**
	 * 2017-07-13
	 * «This method is executed when using isset() or empty() on objects implementing ArrayAccess.
	 * When using empty() ArrayAccess::offsetGet() will be called and checked if empty
	 * only if ArrayAccess::offsetExists() returns TRUE».
	 * http://php.net/manual/arrayaccess.offsetexists.php
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $k
	 * @return bool
	 */
	function offsetExists($k) {return !is_null(dfa_deep($this->_a, $k));}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $k
	 * @return array(string => mixed)|mixed|null
	 */
	function offsetGet($k) {return dfa_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v) {dfa_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k) {dfa_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @var array(string => mixed)
	 */
	private $_a;
}