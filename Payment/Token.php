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
	 * 2016-08-23
	 * Для Stripe этот параметр может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * 2017-02-11
	 * @used-by \Df\StripeClone\P\Charge::token()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Square\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @param II|OP|QP|O|Q $p
	 * @return string
	 */
	static function get($p) {return df_result_sne(dfp_iia($p, self::KEY));}

	/**
	 * 2017-04-07
	 * @used-by get()
	 * @used-by \Df\StripeClone\Method::iiaKeys()
	 * @used-by \Dfe\CheckoutCom\Method::iiaKeys()
	 * @used-by \Dfe\Square\Method::iiaKeys()
	 * @used-by \Dfe\TwoCheckout\Method::charge()
	 * @used-by \Dfe\TwoCheckout\Method::iiaKeys()
	 */
	const KEY = 'token';
}