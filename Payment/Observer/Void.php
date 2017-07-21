<?php
namespace Df\Payment\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as O;
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
 *		$orderState = $this->getOrderStateResolver()->getStateForOrder($this->getOrder());
 *		$this->getOrder()
 *			->addStatusHistoryComment(
 *				$message,
 *				$this->getOrder()->getConfig()->getStateDefaultStatus($orderState)
 *			)->setIsCustomerNotified($creditmemo->getOrder()->getCustomerNoteNotify());
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
 *		$this->getOrder()->setState(Order::STATE_PROCESSING)
 *			->setStatus($this->getOrder()->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING))
 *			->addStatusHistoryComment($message);
 * 
 * По этой причине мы не можем установить заказу состояние «closed» непосредственно 
 * в @see \Df\Payment\Method::void(), и вынуждены использовать для этого обработчик событий.
 *
 *	2017-01-17
 *	«Void» is not a reserved word even in PHP 7:
 *	http://php.net/manual/reserved.keywords.php
 *	http://php.net/manual/reserved.other-reserved-words.php
 *
 *	2017-07-21
 *	«Void» became a reserved word in PHP 7.1:
 *	http://php.net/manual/en/reserved.other-reserved-words.php#layout-content
 *	«Fatal error: Cannot use 'Void' as class name as it is reserved <...> Segmentation fault».
 *	It could be related to: https://mage2.pro/t/4177
 */
final class VoidT implements ObserverInterface {
	/**
	 * 2017-01-17
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param Observer $ob
	 */
	function execute(Observer $ob) {
		/** @var OP $op */
		if (dfp_my($op = $ob['payment'])) {
			$op->getOrder()->setState(O::STATE_CLOSED)->setStatus(df_order_ds(O::STATE_CLOSED));
		}
	}
}

