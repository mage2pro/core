<?php
// 2017-01-07
namespace Df\StripeClone\WebhookStrategy\Charge;
use Df\Sales\Model\Order as DfOrder;
use Df\StripeClone\Webhook\IRefund;
class Refunded extends \Df\StripeClone\WebhookStrategy\Charge {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\StripeClone\WebhookStrategy::handle()
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	final public function handle() {
		/** @var IRefund $w */
		$w = df_ar($this->w(), IRefund::class);
		$this->resultSet(
			dfp_refund(
				$this->ii()
				,df_invoice_by_transaction($this->o(), $this->parentId())
				,$w->amount()
			) ?: 'skipped')
		;
	}
}