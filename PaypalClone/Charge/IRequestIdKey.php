<?php
namespace Df\PaypalClone\Charge;
/**
 * 2016-08-29
 * Позволяет сделать статический метод абстрактным: http://stackoverflow.com/a/6386309
 * @see \Df\PaypalClone\Charge
 */
interface IRequestIdKey {
	/**
	 * 2016-08-29
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\PaypalClone\Webhook::parentIdRawKey()
	 * @see \Dfe\AllPay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @return string
	 */
	static function requestIdKey();
}