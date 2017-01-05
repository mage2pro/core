<?php
namespace Df\PaypalClone;
// 2016-08-29
// Позволяет сделать статический метод абстрактным: http://stackoverflow.com/a/6386309
interface ICharge {
	/**
	 * 2016-08-29
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\Payment\Webhook::parentIdLKey()
	 * @see \Dfe\AllPay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @return string
	 */
	static function requestIdKey();
}