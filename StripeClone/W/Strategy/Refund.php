<?php
namespace Df\StripeClone\W\Strategy;
use Df\Sales\Model\Order as DfOrder;
use Df\StripeClone\Method as M;
use Df\StripeClone\W\Handler;
use Df\StripeClone\W\IRefund;
// 2017-01-07
final class Refund extends \Df\StripeClone\W\Strategy {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\StripeClone\W\Strategy::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::::handle()
	 */
	protected function _handle() {
		$h = df_ar($this->h(), IRefund::class); /** @var Handler|IRefund $h */
		// 2017-01-18
		// Переводить здесь размер платежа из копеек (формата платёжной системы)
		// в рубли (формат Magento) не нужно: это делает dfp_refund().
		$this->resultSet((dfp_container_has($this->op(), M::II_TRANS, $h->eTransId()) ? null :
			dfp_refund($this->op(), $h->nav()->pid(), $h->amount())
		) ?: 'skipped');
	}
}