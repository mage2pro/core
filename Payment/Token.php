<?php
namespace Df\Payment;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-04-07
final class Token {
	/**
	 * 2017-08-28       
	 * @used-by \Df\Payment\Observer\Multishipping::execute()
	 * @param string $methodCode
	 * @return string|null
	 */
	static function exchangedGet($methodCode) {return dfa(self::$_exchanged, $methodCode);}

	/**
	 * 2017-08-28
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @param string $methodCode
	 * @param string $cardId
	 */
	static function exchangedSet($methodCode, $cardId) {self::$_exchanged[$methodCode] = $cardId;}

	/**
	 * 2017-08-28    
	 * @used-by exchangedGet()
	 * @used-by exchangedSet()
	 * @var array(string => string)
	 */
	private static $_exchanged = [];
	
	/**
	 * 2016-08-23
	 * Для Stripe этот параметр может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * 2017-02-11
	 * @used-by \Df\StripeClone\P\Charge::token()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @param II|OP|QP|O|Q $p
	 * @param bool $required [optional]
	 * @return string
	 */
	static function get($p, $required = true) {
		$r = dfp_iia($p, self::KEY); /** @var string|bool $r */
		return !$required ? $r : df_result_sne($r);
	}

	/**
	 * 2017-04-07
	 * @used-by get()
	 * @used-by \Df\Payment\Observer\Multishipping::execute()   
	 * @used-by \Df\StripeClone\Method::iiaKeys()
	 * @used-by \Dfe\CheckoutCom\Method::iiaKeys()
	 * @used-by \Dfe\Square\Method::iiaKeys()
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @used-by \Dfe\TwoCheckout\Method::iiaKeys()
	 * @used-by Df_Payment/card::dfData(): 
	 * https://github.com/mage2pro/core/blob/2.10.46/Payment/view/frontend/web/card.js#L100
	 * @used-by Df_StripeClone/main::placeOrder():
	 * https://github.com/mage2pro/core/blob/2.10.46/StripeClone/view/frontend/web/main.js#L146
	 * https://github.com/mage2pro/core/blob/2.10.46/StripeClone/view/frontend/web/main.js#L156
	 * @used-by Dfe_Stripe/multishipping::setResult():
	 * https://github.com/mage2pro/stripe/blob/1.10.13/view/frontend/web/multishipping.js#L44
	 */
	const KEY = 'token';
}