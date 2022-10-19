<?php
namespace Df\Core;
use Magento\Framework\Session\Storage;
/**
 * 2021-10-22 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
 * @see \CanadaSatellite\Bambora\Session
 * @see \Df\Checkout\SessionBase
 * @see \Df\Customer\SessionBase
 */
abstract class Session implements \ArrayAccess {
	/**
	 * 2021-10-22
	 * @used-by __construct()
	 * @see \Df\Checkout\SessionBase::c()
	 * @see \Df\Customer\SessionBase::c()
	 * @see \Dfe\TBCBank\Session::c()
	 * @see \Frugue\Core\Session::c()
	 * @return string
	 */
	abstract protected function c();

	/**
	 * 2021-10-26
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @used-by df_prop()
	 * @param string $k
	 */
	final function offsetExists($k): bool {return $this->_st->offsetExists($this->k($k));}

	/**
	 * 2021-10-26
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @used-by df_prop()
	 * @param string $k
	 */
	final function offsetGet($k):mixed {return $this->_st->offsetGet($this->k($k));}

	/**
	 * 2021-10-26
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	final function offsetSet($k, $v): void {$this->_st->offsetSet($this->k($k), $v);}

	/**
	 * 2021-10-26
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	final function offsetUnset($k): void {$this->_st->offsetUnset($this->k($k));}

	/**
	 * 2021-10-26
	 * @used-by s()
	 */
	private function __construct() {
		$this->_prefix = df_module_name($this) . '_';
		$this->_st = df_o($this->c());
	}

	/**
	 * 2021-10-26
	 * @used-by offsetExists()
	 * @used-by offsetGet()
	 * @used-by offsetSet()
	 * @used-by offsetUnset()
	 * @param string $k [optional]
	 * @return string
	 */
	private function k($k) {return $this->_prefix . $k;}

	/**
	 * 2021-10-27
	 * @used-by __construct()
	 * @used-by k()
	 * @var string
	 */
	private $_prefix;

	/**
	 * 2021-10-26
	 * @used-by __construct()
	 * @used-by offsetExists()
	 * @used-by offsetGet()
	 * @used-by offsetSet()
	 * @used-by offsetUnset()
	 * @var Storage
	 */
	private $_st;

	/**
	 * 2021-10-22
	 * @used-by df_checkout_message()
	 * @used-by \Df\Checkout\B\Messages::_toHtml()
	 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Customer\Plugin\Block\Form\Register::afterGetFormData()
	 * @used-by \Df\Sso\Css::isAccConfirmation()
	 * @used-by \Df\Sso\Css::isRegCompletion()
	 * @used-by \Df\Sso\CustomerReturn::_execute()
	 * @used-by \Dfe\TBCBank\Init::p()
	 * @used-by \Dfe\TBCBank\Init\Action::redirectParams()
	 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
	 * @return self
	 */
	final static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}
}