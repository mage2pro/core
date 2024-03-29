<?php
namespace Df\StripeClone\P;
use Df\StripeClone\Method as M;
use Df\StripeClone\Payer;
use Df\StripeClone\Settings as S;
/**
 * 2016-12-28
 * @see \Dfe\Moip\P\Charge
 * @see \Dfe\Omise\P\Charge
 * @see \Dfe\Paymill\P\Charge
 * @see \Dfe\Spryng\P\Charge
 * @see \Dfe\Square\P\Charge
 * @see \Dfe\Stripe\P\Charge
 * @method M m()
 * @method S s()
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2017-02-11
	 * 2017-02-18 Ключ, значением которого является токен банковской карты.
	 * 2017-10-09 The key name of a bank card token or of a saved bank card ID.
	 * 2022-11-13 The result could be an empty string: @see \Dfe\Moip\P\Charge::k_CardId()
	 * @used-by self::request()
	 * @used-by \Df\StripeClone\P\Reg::k_CardId()
	 * @see \Dfe\Moip\P\Charge::k_CardId()
	 * @see \Dfe\Omise\P\Charge::k_CardId()
	 * @see \Dfe\Paymill\P\Charge::k_CardId()
	 * @see \Dfe\Spryng\P\Charge::k_CardId()
	 * @see \Dfe\Square\P\Charge::k_CardId()
	 * @see \Dfe\Stripe\P\Charge::k_CardId()
	 */
	abstract function k_CardId():string;

	/**
	 * 2017-02-18
	 * Dynamic statement descripor
	 * https://mage2.pro/tags/dynamic-statement-descriptor
	 * https://stripe.com/blog/dynamic-descriptors
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Charge::k_DSD()
	 * @see \Dfe\Omise\P\Charge::k_DSD()
	 * @see \Dfe\Paymill\P\Charge::k_DSD()
	 * @see \Dfe\Spryng\P\Charge::k_DSD()
	 * @see \Dfe\Square\P\Charge::k_DSD()
	 * @see \Dfe\Stripe\P\Charge::k_DSD()
	 */
	abstract protected function k_DSD():string;

	/**
	 * 2017-10-09
	 * @used-by self::request()
	 * @see \Dfe\Square\P\Charge::amountAndCurrency()
	 * @return array(string => string|int)
	 */
	protected function amountAndCurrency():array {return [
		self::K_AMOUNT => $this->amountF(), self::K_CURRENCY => $this->currencyC()
	];}

	/**
	 * 2017-06-12
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Charge::inverseCapture()
	 * @see \Dfe\Square\P\Charge::inverseCapture()
	 */
	protected function inverseCapture():bool {return false;}

	/**
	 * 2017-02-11
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Charge::p()
	 * @see \Dfe\Omise\P\Charge::p()
	 * @see \Dfe\Square\P\Charge::p()
	 * @see \Dfe\Stripe\P\Charge::p()
	 * @return array(string => mixed)
	 */
	protected function p():array {return [];}

	/**
	 * 2017-02-18
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Charge::k_Capture()
	 * @see \Dfe\Spryng\P\Charge::k_Capture()
	 * @see \Dfe\Square\P\Charge::k_Capture()
	 */
	protected function k_Capture():string {return self::K_CAPTURE;}

	/**
	 * 2017-10-09
	 * @used-by self::request()
	 * @see \Dfe\Square\P\Charge::k_CustomerId()
	 */
	protected function k_CustomerId():string {return self::K_CUSTOMER_ID;}

	/**
	 * 2018-11-24
	 * @used-by self::request()
	 * @see \Dfe\Square\P\Charge::k_Description()
	 */
	protected function k_Description():string {return self::K_DESCRIPTION;}

	/**
	 * 2017-02-18
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Charge::k_Excluded()
	 * @see \Dfe\Spryng\P\Charge::k_Excluded()
	 * @return string[]
	 */
	protected function k_Excluded():array {return [];}

	/**
	 * 2017-06-11
	 * @used-by self::request()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @see \Dfe\Moip\P\Charge::v_CardId()
	 * @return string|array(string => mixed)
	 */
	protected function v_CardId(string $id, bool $isNew) {return $id;}

	/**
	 * 2016-12-28
	 * 2016-03-07 Stripe: https://stripe.com/docs/api/php#create_charge
	 * 2016-11-13 Omise: https://www.omise.co/charges-api#charges-create
	 * 2017-02-11 Paymill https://developers.paymill.com/API/index#-transaction-object
	 * @used-by \Df\StripeClone\Method::chargeNewParams()
	 * @return array(string => mixed)
	 */
	final static function request(M $m, bool $capture = true):array {
		$i = self::sn($m); /** @var self $i */
		$payer = Payer::s($m); /** @var Payer $payer */
		$k_Excluded = $i->k_Excluded(); /** @var string[] $k_Excluded */
		$r = df_clean_keys([
			# 2016-03-08 Для Stripe текст может иметь произвольную длину: https://mage2.pro/t/903
			$i->k_Description() => $i->description()
			,$i->k_Capture() => $i->inverseCapture() ? !$capture : $capture
			# 2017-02-18
			# «Dynamic statement descripor»
			# https://mage2.pro/tags/dynamic-statement-descriptor
			# https://stripe.com/blog/dynamic-descriptors
			# https://support.stripe.com/questions/does-stripe-support-dynamic-descriptors
			,$i->k_DSD() => $i->s()->dsd()
		] + $i->amountAndCurrency(), $k_Excluded);
		# 2017-11-12
		# Some Stripe's sources are single-use: https://stripe.com/docs/sources#single-use-or-reusable
		# «Stripe API Documentation» → «Payment Methods Supported by the Sources API» →
		# «Single-use or reusable»:
		# «If a source can only be used once, this parameter is set to `single_use`
		# and a source must be created each time a customer makes a payment.
		# Such sources should not be attached to customers and should be charged directly instead.»
		$k_CustomerId = $i->k_CustomerId(); /** @var string|null $k_CustomerId */
		/** @var string|null $customerId */
		if (($customerId = $payer->customerId()) && !in_array($k_CustomerId, $k_Excluded)) {
			$r[$k_CustomerId] = $customerId;
		}
		/**
		 * 2017-07-30
		 * I placed it here in a separate condition branch
		 * because some payment modules (Moip) implement non-card payment options.
		 * A similar code block is here: @see \Df\StripeClone\P\Reg::request()
		 */
		if ($k = $i->k_CardId() /** @var string $k */) {
			$r[$k] = $i->v_CardId($payer->cardId(), $payer->tokenIsNew());
		}
		return $r + $i->p();
	}

	/**
	 * 2017-06-11
	 * @used-by self::request()
	 * @used-by \Df\StripeClone\P\Reg::charge()
	 */
	final static function sn(M $m):self {return dfcf(function(M $m) {return df_new(df_con_heir($m, __CLASS__), $m);}, [$m]);}

	/**
	 * 2017-02-11
	 * @used-by self::amountAndCurrency()
	 * @used-by \Dfe\Moip\P\Charge::k_Excluded()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_AMOUNT = 'amount';

	/**
	 * 2017-02-11
	 * @used-by self::k_Capture()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_CAPTURE = 'capture';

	/**
	 * 2017-02-11
	 * @used-by self::amountAndCurrency()
	 * @used-by \Dfe\Moip\P\Charge::k_Excluded()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 * @used-by \Dfe\Spryng\P\Charge::k_Excluded()
	 */
	const K_CURRENCY = 'currency';

	/**
	 * 2017-02-11
	 * @used-by self::k_CustomerId()
	 * @used-by \Dfe\Moip\P\Charge::k_Excluded()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_CUSTOMER_ID = 'customer';

	/**
	 * 2017-02-11
	 * @used-by self::request()
	 * @used-by \Dfe\Moip\P\Charge::k_Excluded()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 * @used-by \Dfe\Spryng\P\Charge::k_Excluded()
	 */
	const K_DESCRIPTION = 'description';
}