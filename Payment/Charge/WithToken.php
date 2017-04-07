<?php
namespace Df\Payment\Charge;
use Df\Payment\Method as M;
/**
 * 2016-07-02
 * @see \Df\StripeClone\Charge 
 * @see \Dfe\CheckoutCom\Charge
 * @see \Dfe\Square\Charge
 * @see \Dfe\TwoCheckout\Charge
 */
abstract class WithToken extends \Df\Payment\Charge {
	/**
	 * 2017-03-12
	 * @override
	 * @see \Df\Payment\Operation::__construct 
	 * @used-by \Df\StripeClone\Charge::request()
	 * @used-by \Dfe\CheckoutCom\Charge::build() 
	 * @used-by \Dfe\Square\Charge::p() 
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @param M $m
	 * @param string $token
	 * @param float|null $amount [optional]
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	final function __construct(M $m, $token, $amount = null) {
		parent::__construct($m, $amount); $this->_token = df_param_sne($token, 1);
	}
	
	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Charge::cardId()
	 * @used-by \Df\StripeClone\Charge::newCard()   
	 * @used-by \Df\StripeClone\Charge::usePreviousCard()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Square\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return string
	 */
	final protected function token() {return $this->_token;}

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by token()
	 * @var string
	 */
	private $_token;
}