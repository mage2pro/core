<?php
namespace Df\Core;
use Magento\Framework\Session\Storage;
/**
 * 2021-10-22 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
 * @see \Df\Checkout\Session
 */
abstract class Session implements \ArrayAccess {
	/**
	 * 2021-10-22
	 * @used-by st()
	 * @see \Df\Checkout\Session::stC()
	 * @return string
	 */
	abstract protected function stC();

	/**
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @used-by df_prop()
	 * @param string $k
	 * @return bool
	 */
	final function offsetExists($k) {return $this->st()->offsetExists($this->k($k));}

	/**
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @used-by df_prop()
	 * @param string $k
	 * @return mixed
	 */
	final function offsetGet($k) {return $this->get($k);}

	/**
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	final function offsetSet($k, $v) {return $this->set($v, $k);}

	/**
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	final function offsetUnset($k) {return $this->unset($k);}

	/**
	 * 2021-10-26
	 * @param string|null $k [optional]
	 * @return mixed
	 */
	final protected function get($k = null) {return $this->st()->offsetGet($this->k($k ?: df_caller_f()));}

	/**
	 * 2021-10-26
	 * @param mixed $v
	 * @param string|null $k [optional]
	 */
	final protected function set($v, $k = null) {$this->st()->offsetSet($this->k($k ?: df_caller_f()), $v);}

	/**
	 * 2021-10-26
	 * @param string|null $k [optional]
	 */
	final protected function unset($k = null) {return $this->st()->offsetUnset($this->k($k ?: df_caller_f()));}

	/**
	 * 2021-10-26
	 * @used-by get()
	 * @used-by set()
	 * @used-by unset()
	 * @param string $k [optional]
	 * @return string
	 */
	private function k($k) {return "df_$k";}

	/**
	 * 2021-10-26
	 * @used-by get()
	 * @used-by set()
	 * @used-by unset()
	 * @return Storage
	 */
	private function st() {return df_o($this->stC());}

	/**
	 * 2021-10-22
	 * @used-by df_checkout_message()
	 * @used-by \Df\Checkout\B\Messages::_toHtml()
	 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
	 * @return self
	 */
	final static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}
}