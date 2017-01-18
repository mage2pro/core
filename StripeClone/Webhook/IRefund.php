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
	 * В валюте заказа (платежа).
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @see \Dfe\Omise\Webhook\Refund\Create::amount()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::amount()
	 * @return int
	 */
	function amount();
}

