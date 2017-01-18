<?php
namespace Df\Payment\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-01-18
 * Событие: sales_order_payment_refund
 * @see \Magento\Sales\Model\Order\Payment::refund() 
        $this->_eventManager->dispatch(
            'sales_order_payment_refund',
            ['payment' => $this, 'creditmemo' => $creditmemo]
        );
 * https://github.com/magento/magento2/blob/91aa307/app/code/Magento/Sales/Model/Order/Payment.php#L713-L716
 *
 * Цель обработчика — сохранение для заказа, для которого был осуществлён частичный возврат,
 * состояния «Processing» вместо состояние «Complete», которое норовит установить ядро:
 * @see \Magento\Sales\Model\Order\Payment::refund()
		$orderState = $this->getOrderStateResolver()->getStateForOrder($this->getOrder());
		$this->getOrder()
			->addStatusHistoryComment(
				$message,
				$this->getOrder()->getConfig()->getStateDefaultStatus($orderState)
			)->setIsCustomerNotified($creditmemo->getOrder()->getCustomerNoteNotify());
	https://github.com/magento/magento2/blob/91aa307/app/code/Magento/Sales/Model/Order/Payment.php#L707-L712

 * @see \Magento\Sales\Model\Order\StateResolver::getStateForOrder()
		if (!$order->isCanceled() && !$order->canUnhold()
			&& !$order->canInvoice() && !$order->canShip()
		) {
			if ($this->isOrderComplete($order)) {
				$orderState = Order::STATE_COMPLETE;
			} elseif ($this->isOrderClosed($order, $arguments)) {
				$orderState = Order::STATE_CLOSED;
			}
		}
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/StateResolver.php#L84-L90
 */
class Refund implements ObserverInterface {
	/**
	 * 2017-01-18
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param Observer $observer
	 */
	public function execute(Observer $observer) {
		/** @var OP $op */
		if (dfp_is_my($op = $observer['payment'])) {
			/** @var CM $cm */
			$cm = $op->getCreditmemo();
			if (!df_is0(floatval($op->getBaseAmountPaid()) - $cm->getBaseGrandTotal())) {
				/** @var Order $o */
				$o = $op->getOrder();
				$o->setState(Order::STATE_PROCESSING);
				$o->setStatus($o->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
			}
		}
	}
}

