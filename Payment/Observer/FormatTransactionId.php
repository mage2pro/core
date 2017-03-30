<?php
namespace Df\Payment\Observer;
use Df\Payment\Method;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\MethodInterface as IMethod;
use Magento\Sales\Model\Order\Payment\Transaction;
/**
 * 2016-08-20
 * Событие: sales_order_payment_transaction_html_txn_id
 * @see \Magento\Sales\Model\Order\Payment\Transaction::getHtmlTxnId()
 * How is the «sales_order_payment_transaction_html_txn_id» event triggered and handled?
 * https://mage2.pro/t/1965
 */
final class FormatTransactionId implements ObserverInterface {
	/**
	 * 2016-08-20
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {
		/** @var IMethod|Method $m */
		/** @var Transaction $t */
		if (dfp_my($m = dfpm($t = $o['data_object']))) {
			/** @used-by \Magento\Sales\Model\Order\Payment\Transaction::getHtmlTxnId() */
			$t['html_txn_id'] = $m->tidFormat($t);
		}
	}
}