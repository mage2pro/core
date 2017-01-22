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
	/** @return string */
	protected function token() {return $this[self::$P__TOKEN];}

	/**
	 * 2016-07-02
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TOKEN, DF_V_STRING_NE);
	}
	/** @var string */
	protected static $P__TOKEN = 'token';
}