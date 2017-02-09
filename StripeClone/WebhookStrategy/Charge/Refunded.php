<?php
// 2017-01-07
namespace Df\StripeClone\WebhookStrategy\Charge;
use Df\Sales\Model\Order as DfOrder;
use Df\StripeClone\Method as M;
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
		// 2017-01-18
		// Переводить здесь размер платежа из копеек (формата платёжной системы)
		// в рубли (формат Magento) не нужно: это делает dfp_refund().
		$this->resultSet((dfp_container_has($this->ii(), M::II_TRANS, $w->eTransId()) ? null :
			dfp_refund($this->ii() ,df_invoice_by_trans($this->o(), $this->parentId()), $w->amount())
		) ?: 'skipped');
	}
}