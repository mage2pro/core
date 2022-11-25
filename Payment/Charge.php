<?php
namespace Df\Payment;
/**
 * 2016-07-02
 * @see \Df\GingerPaymentsBase\Charge
 * @see \Df\PaypalClone\Charge
 * @see \Df\StripeClone\P\Charge
 * @see \Dfe\CheckoutCom\Charge
 * @see \Dfe\Qiwi\Charge
 * @see \Dfe\Stripe\P\_3DS
 * @see \Dfe\TBCBank\Charge
 * @see \Dfe\TwoCheckout\Charge
 * @see \Dfe\Vantiv\Charge
 */
abstract class Charge extends Operation {
	/**
	 * 2016-08-27
	 * @used-by self::customerReturnRemote()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 */
	final protected function callback(string $path = 'confirm'):string {return df_webhook($this->m(), $path);}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Robokassa\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\P\Charge::p()
	 * @used-by \Dfe\Vantiv\Charge::pCharge()
	 */
	final protected function customerIp():string {return $this->o()->getRemoteIp();}

	/**
	 * 2016-08-27
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\Omise\P\Charge::p()
	 */
	final protected function customerReturn():string {return dfp_url_customer_return($this->m());}

	/**
	 * 2017-03-06
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Dragonpay\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Qiwi\Charge::pBill()
	 * @used-by \Dfe\Robokassa\Charge::pCharge()
	 * @used-by \Dfe\TBCBank\Charge::common()
	 */
	final protected function description():string {$s = $this->s(); return $this->text(
		$s->description(), $s->v('description_rules/maxLength/value')
	);}

	/**
	 * 2016-09-07 Ключами результата являются человекопонятные названия переменных.
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\Stripe\P\Charge::p()
	 * @return array(string => string)
	 */
	final protected function metadata(int $lK = 0, int $lV = 0):array {
		$k = $this->s()->metadata(); /** @var string[] $k */ /** @var array(string => string) $m */
		$m = array_combine(dfa(Metadata::s()->map(), $k), dfa($this->vars(), $k));
		return array_combine(dfa_chop(array_keys($m), $lK), dfa_chop(array_values($m), $lV));
	}

	/**
	 * 2016-07-04
	 * @used-by self::description()
	 * @used-by \Dfe\AllPay\Charge::descriptionOnKiosk()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItem()
	 * @used-by \Dfe\Moip\P\Charge::p()
	 * @param callable|string $f [optional]
	 */
	final protected function text(string $s, int $max = 0, $f = 'textFilter'):string {
		$r = df_var($s, $this->vars()); /** @var string $r */
		/**
		 * 2017-11-22
		 * I intentionally do not use @see df_call() here,
		 * because it will require to make @see self::textFilter() `public` instead of `protected`,
		 * and I do not want an extra `public` method.
		 */
		return df_chop($f instanceof \Closure ? $f($r) : $this->$f($r), $max);
	}

	/**
	 * 2017-11-13
	 * @used-by self::text()
	 * @see \Dfe\AlphaCommerceHub\Charge::textFilter()
	 * @see \Dfe\TBCBank\Charge::textFilter()
	 */
	protected function textFilter(string $s):string {return $s;}

	/**
	 * 2016-05-06
	 * @used-by self::text()
	 * @used-by self::metadata()
	 * @return array(string => string)
	 */
	private function vars():array {return dfc($this, function() {return Metadata::vars($this->store(), $this->oq());});}
}