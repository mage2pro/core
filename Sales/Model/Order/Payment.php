<?php
namespace Df\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as _Payment;
use Magento\Sales\Model\Order\Creditmemo;
/**
 * 2016-03-27
 * @method Creditmemo getCreatedCreditmemo()
 */
class Payment extends _Payment {
	/**
	 * 2016-03-27
	 * https://mage2.pro/t/1031
	 * The methods
	 * @see \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::getInvoiceForTransactionId()
	 * and @see \Magento\Sales\Model\Order\Payment::_getInvoiceForTransactionId()
	 * duplicate almost the same code
	 * @param OrderInterface|Order $order
	 * @param int $transactionId.
	 * @return Invoice|null
	 */
	public static function getInvoiceForTransactionId(OrderInterface $order, $transactionId) {
		/** @var Payment $i */
		$i = df_om()->create(__CLASS__);
		$i->setOrder($order);
		return $i->_getInvoiceForTransactionId($transactionId);
	}
}