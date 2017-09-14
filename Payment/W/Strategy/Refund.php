<?php
namespace Df\Payment\W\Strategy;
use Df\Payment\Method as M;
use Df\Payment\W\Handler;
use Df\Payment\W\IRefund;
/**
 * 2017-01-07
 * @used-by \Dfe\Omise\W\Handler\Refund\Create::strategyC()
 * @used-by \Dfe\Paymill\W\Handler\Refund\Succeeded::strategyC()
 * @used-by \Dfe\Qiwi\W\Handler::strategyC()
 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::strategyC()
 */
final class Refund extends \Df\Payment\W\Strategy {
	/**
	 * 2017-01-07
	 * 2017-01-18
	 * Переводить здесь размер платежа из копеек (формата платёжной системы)
	 * в рубли (формат Magento) не нужно: это делает dfp_refund().
	 * 2017-09-13 We do not set a response here, because PayPal clones require a specific response on success.
	 * @override
	 * @see \Df\Payment\W\Strategy::_handle()
	 * @used-by \Df\Payment\W\Strategy::::handle()
	 */
	protected function _handle() {
		$h = df_ar($this->h(), IRefund::class); /** @var Handler|IRefund $h */
		if (!dfp_container_has($this->op(), M::II_TRANS, $h->eTransId())) {
			dfp_refund($this->op(), $h->nav()->pid(), $h->amount());
		}
	}
}