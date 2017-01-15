<?php
// 2017-01-07
namespace Df\StripeClone\WebhookStrategy\Charge;
use Df\Sales\Model\Order as DfOrder;
class Refunded extends \Df\StripeClone\WebhookStrategy {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\StripeClone\WebhookStrategy::handle()
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	final public function handle() {$this->resultSet(
		dfp_refund(
			$this->ii()
			,df_invoice_by_transaction($this->o(), $this->parentId())
			,df_last($this->ro('refunds/data'))['amount']
		) ?: 'skipped')
	;}
}