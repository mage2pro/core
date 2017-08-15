<?php
namespace Df\Payment\W\Strategy;
use Df\Payment\Method as M;
use Df\Payment\W\Handler;
use Df\Payment\W\IRefund;
use Df\Sales\Model\Order as DfOrder;
// 2017-01-07
final class Refund extends \Df\Payment\W\Strategy {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\Payment\W\Strategy::_handle()
	 * @used-by \Df\Payment\W\Strategy::::handle()
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