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
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Refunded::handle()
	 * @return int
	 */
	function amount();
}

