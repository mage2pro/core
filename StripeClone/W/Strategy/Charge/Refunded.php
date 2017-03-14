<?php
// 2017-01-07
namespace Df\StripeClone\W\Strategy\Charge;
use Df\Sales\Model\Order as DfOrder;
use Df\StripeClone\Method as M;
use Df\StripeClone\W\IRefund;
final class Refunded extends \Df\StripeClone\W\Strategy\Charge {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\StripeClone\W\Strategy::handle()
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @return void
	 */
	function handle() {
		/** @var IRefund $h */
		$h = df_ar($this->h(), IRefund::class);
		// 2017-01-18
		// Переводить здесь размер платежа из копеек (формата платёжной системы)
		// в рубли (формат Magento) не нужно: это делает dfp_refund().
		$this->resultSet((dfp_container_has($this->ii(), M::II_TRANS, $h->eTransId()) ? null :
			dfp_refund($this->ii() ,df_invoice_by_trans($this->o(), $this->parentId()), $h->amount())
		) ?: 'skipped');
	}
}