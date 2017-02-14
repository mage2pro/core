<?php
namespace Df\StripeClone\Webhook;
/**
 * 2017-01-17
 * @see \Dfe\Omise\Webhook\Refund\Create
 * @see \Dfe\Paymill\Webhook\Refund\Succeeded
 * @see \Dfe\Stripe\Webhook\Charge\Refunded
 */
interface IRefund {
	/**
	 * 2017-01-17
	 * В валюте заказа (платежа), в формате платёжной системы (копейках).
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @see \Dfe\Omise\Webhook\Refund\Create::amount()
	 * @see \Dfe\Paymill\Webhook\Refund\Succeeded::amount()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::amount()
	 * @return int
	 */
	function amount();

	/**
	 * 2017-01-19
	 * Метод должен вернуть идентификатор операции (не платежа!) в платёжной системе.
	 * 2017-02-14
	 * Он нужен нам для избежания обработки оповещений о возвратах, инициированных нами же
	 * из административной части Magento: @see \Df\StripeClone\Method::_refund()
	 * Это должен быть тот же самый идентификатор,
	 * который возвращает @see \Df\StripeClone\Facade\Refund::transId()
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @see \Dfe\Omise\Webhook\Refund\Create::eTransId()
	 * @see \Dfe\Paymill\Webhook\Refund\Succeeded::eTransId()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::eTransId()
	 * @return string
	 */
	function eTransId();
}

