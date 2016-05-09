<?php
namespace Df\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Creditmemo;
/**
 * 2016-03-27
 * @method Creditmemo getCreatedCreditmemo()
 *
 * 2016-05-09
 * @method string|null getRefundTransactionId()
 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Sales/Model/Order/Payment.php#L652
 */
class Payment extends OP {
	/**
	 * 2016-03-27
	 * https://mage2.pro/t/1031
	 * The methods
	 * @see \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::getInvoiceForTransactionId()
	 * and @see \Magento\Sales\Model\Order\Payment::_getInvoiceForTransactionId()
	 * duplicate almost the same code
	 * @param IO|O $order
	 * @param int $transactionId.
	 * @return Invoice|null
	 */
	public static function getInvoiceForTransactionId(IO $order, $transactionId) {
		/** @var Payment $i */
		$i = df_om()->create(__CLASS__);
		$i->setOrder($order);
		return $i->_getInvoiceForTransactionId($transactionId);
	}

	/**
	 * 2016-05-08
	 * @param OP $op
	 * @param string $action
	 * @param O $order
	 * @return void
	 */
	public static function processActionS(OP $op, $action, O $order) {
		$op->processAction($action, $order);
	}

	/**
	 * 2016-05-08
	 * @param OP $op
	 * @param O $order
	 * @param string $orderState
	 * @param string $orderStatus
	 * @param bool $isCustomerNotified
	 * @return void
	 */
	public static function updateOrderS(OP $op, O $order, $orderState, $orderStatus, $isCustomerNotified) {
		$op->updateOrder($order, $orderState, $orderStatus, $isCustomerNotified);
	}
}