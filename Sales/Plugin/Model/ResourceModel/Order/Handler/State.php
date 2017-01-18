<?php
// 2017-01-18
namespace Df\Sales\Plugin\Model\ResourceModel\Order\Handler;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as Sb;
class State {
	/**
	 * 2017-01-18
	 * Цель плагина — сохранение для заказа, для которого был осуществлён частичный возврат,
	 * состояния «Processing» вместо состояние «Complete», которое норовит установить ядро.
	 * @see \Magento\Sales\Model\ResourceModel\Order\Handler\State::check()
	 *
	 * Причём ядро норовит это сделать в 2 местах:
	 *
	 * 1) @see \Magento\Sales\Model\Order\Payment::refund()
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
	 *
	 * Если бы ядро норовило установить состояние «Complete» только здесь,
	 * то плагин нам не был бы необходим: мы бы решили нашу проблему обработкой события
	 * sales_order_payment_refund:
 * @see \Magento\Sales\Model\Order\Payment::refund()
        $this->_eventManager->dispatch(
            'sales_order_payment_refund',
            ['payment' => $this, 'creditmemo' => $creditmemo]
        );
 * https://github.com/magento/magento2/blob/91aa307/app/code/Magento/Sales/Model/Order/Payment.php#L713-L716
	 *
	 * 2) @see \Magento\Sales\Model\ResourceModel\Order\Handler\State::check()
			if (!$order->isCanceled() && !$order->canUnhold()
				&& !$order->canInvoice() && !$order->canShip()
			) {
				if (0 == $order->getBaseGrandTotal() || $order->canCreditmemo()) {
					if ($order->getState() !== Order::STATE_COMPLETE) {
						$order->setState(Order::STATE_COMPLETE)
							->setStatus($order->getConfig()->getStateDefaultStatus(
								Order::STATE_COMPLETE)
							);
					}
				} elseif (floatval($order->getTotalRefunded())
					|| !$order->getTotalRefunded() && $order->hasForcedCanCreditmemo()
				) {
					if ($order->getState() !== Order::STATE_CLOSED) {
						$order->setState(Order::STATE_CLOSED)
							->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
					}
				}
			}
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/ResourceModel/Order/Handler/State.php#L27-L31
	 *
	 * Как видно из этого кода, ядро будет в этой точке программы
	 * повторно пытаться насильно установить нашему заказу состояние «Complete».
	 *
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param O $o
	 * @return string
	 */
	public function aroundCheck(Sb $sb, \Closure $proceed, O $o) {
		$proceed($o);
		/** @var OP $op */
		if (dfp_is_my($op = $o->getPayment())) {
			/** @var CM $cm */
			$cm = $op->getCreditmemo();
			if (!df_is0(floatval($op->getBaseAmountPaid()) - $cm->getBaseGrandTotal())) {
				$o->setState(O::STATE_PROCESSING);
				$o->setStatus($o->getConfig()->getStateDefaultStatus(O::STATE_PROCESSING));
			}
		}
		return $sb;
	}
}


