<?php
namespace Df\Sales\Model\Order;
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
	 * @param int $transactionId.
	 * @return Invoice|null
	 */
	public static function getInvoiceForTransactionId($transactionId) {
		return df_ftn(self::s()->_getInvoiceForTransactionId($transactionId));
	}

	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}