<?php
namespace Df\Payment\Charge;
/**
 * 2016-07-02
 * @see \Df\StripeClone\Charge 
 * @see \Dfe\CheckoutCom\Charge
 * @see \Dfe\Square\Charge
 * @see \Dfe\TwoCheckout\Charge
 */
abstract class WithToken extends \Df\Payment\Charge {
	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Charge::cardId()
	 * @used-by \Df\StripeClone\Charge::newCard()   
	 * @used-by \Df\StripeClone\Charge::usePreviousCard()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Square\Charge::_request()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return string
	 */
	final protected function token() {return $this[self::$P__TOKEN];}

	/**
	 * 2016-07-02
	 * @override
	 * @see \Df\Payment\Operation::_construct()
	 * @see \Dfe\CheckoutCom\Charge::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TOKEN, DF_V_STRING_NE);
	}
	/** @var string */
	protected static $P__TOKEN = 'token';
}