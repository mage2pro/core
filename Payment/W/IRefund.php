<?php
namespace Df\Payment\W;
/**
 * 2017-01-17
 * @see \Dfe\Omise\W\Handler\Refund\Create
 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded
 * @see \Dfe\Qiwi\W\Handler
 * @see \Dfe\Stripe\W\Handler\Charge\Refunded
 */
interface IRefund {
	/**
	 * 2017-01-17
	 * В валюте заказа (платежа), в формате платёжной системы (копейках).
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::amount()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::amount()
	 * @see \Dfe\Qiwi\W\Handler::amount()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::amount()
	 * @return int
	 */
	function amount();

	/**
	 * 2017-01-19 Метод должен вернуть идентификатор операции (не платежа!) в платёжной системе.
	 * 2017-02-14
	 * Он нужен нам для избежания обработки оповещений о возвратах, инициированных нами же
	 * из административной части Magento: @see \Df\StripeClone\Method::_refund()
	 * Это должен быть тот же самый идентификатор,
	 * который возвращает @see \Df\StripeClone\Facade\Refund::transId()
	 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::eTransId()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::eTransId()
	 * @see \Dfe\Qiwi\W\Handler::eTransId()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::eTransId()
	 * @return string
	 */
	function eTransId();
}

