<?php
namespace Df\StripeClone\Webhook;
/**
 * 2017-01-17
 * @see \Dfe\Omise\Webhook\Refund\Create
 * @see \Dfe\Stripe\Webhook\Charge\Refunded
 */
interface IRefund {
	/**
	 * 2017-01-17
	 * В валюте заказа (платежа), в формате платёжной системы (копейках).
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @see \Dfe\Omise\Webhook\Refund\Create::amount()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::amount()
	 * @return int
	 */
	function amount();

	/**
	 * 2017-01-19
	 * Метод должен вернуть идентификатор операции (не платежа!) в платёжной системе
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @see \Dfe\Omise\Webhook\Refund\Create::eTransId()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::eTransId()
	 * @return string
	 */
	function eTransId();
}

