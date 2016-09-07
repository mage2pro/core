<?php
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
/**
 * 2016-03-27
 * @param OrderInterface|Order $order
 * @param int $transactionId
 * @param $allowNull [optional]
 * @return Invoice|null
 */
function df_invoice_by_transaction(OrderInterface $order, $transactionId, $allowNull = false) {
	/** @var Invoice|null $result */
	$result = df_ftn(DfPayment::getInvoiceForTransactionId($order, $transactionId));
	df_assert($allowNull || $result);
	return $result;
}

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_order_send_email() instead of @see df_invoice_send_email()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @param Invoice $invoice
 * @return void
 */
function df_invoice_send_email(Invoice $invoice) {
	/** @var InvoiceSender $sender */
	$sender = df_o(InvoiceSender::class);
	$sender->send($invoice);
}


