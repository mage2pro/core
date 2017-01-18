<?php
namespace Df\Payment\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-01-17
 * Событие: sales_order_payment_void
 * @see \Magento\Sales\Model\Order\Payment::void() 
 *		$this->_eventManager->dispatch('sales_order_payment_void', [
 *			'payment' => $this, 'invoice' => $document
 * 		]);  
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L567
 * Цель обработчика — закрытие заказов,
 * для оплаты которых использовались мои платёжные модули,
 * и для платежей которых была выполнена операция «Void».   
 * 
 * Ядро так делает для операции «refund»:
 * @see \Magento\Sales\Model\Order\Payment::refund()
		$orderState = $this->getOrderStateResolver()->getStateForOrder($this->getOrder());
		$this->getOrder()
			->addStatusHistoryComment(
				$message,
				$this->getOrder()->getConfig()->getStateDefaultStatus($orderState)
			)->setIsCustomerNotified($creditmemo->getOrder()->getCustomerNoteNotify());
 * https://github.com/magento/magento2/blob/1856c28/app/code/Magento/Sales/Model/Order/Payment.php#L707-L712
 * 
 * Для операции «void» ядро так не делает, однако я посчитал логичным закрывать заказ.
 *
 * Метод @see \Magento\Sales\Model\Order\Payment::_void()
 * не просто оставляет заказ в состоянии «Processing»,
 * а даже насильно переводит его в это состояние:
 * 		$this->setOrderStateProcessing($message);
 * https://github.com/magento/magento2/blob/1856c28/app/code/Magento/Sales/Model/Order/Payment.php#L1129
 * @see \Magento\Sales\Model\Order\Payment::setOrderStateProcessing()
		$this->getOrder()->setState(Order::STATE_PROCESSING)
			->setStatus($this->getOrder()->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING))
			->addStatusHistoryComment($message);
 * 
 * По этой причине мы не можем установить заказу состояние «closed» непосредственно 
 * в @see \Df\Payment\Method::void(), и вынуждены использовать для этого обработчик событий.    
 */
class Void implements ObserverInterface {
	/**
	 * 2017-01-17
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param Observer $observer
	 */
	public function execute(Observer $observer) {
		/** @var OP $op */
		if (dfp_is_my($op = $observer['payment'])) {
			/** @var Order $o */
			$o = $op->getOrder();
			$o->setState(Order::STATE_CLOSED);
			$o->setStatus($o->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));			
		}
	}
}

