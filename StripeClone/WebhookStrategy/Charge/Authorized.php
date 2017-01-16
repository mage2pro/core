<?php
// 2017-01-15
namespace Df\StripeClone\WebhookStrategy\Charge;
class Authorized extends \Df\StripeClone\WebhookStrategy\Charge {
	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\StripeClone\WebhookStrategy::handle()
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	final public function handle() {
		$this->action();
		df_order_send_email($this->o());
		$this->resultSet($this->ii()->getId());
	}
}