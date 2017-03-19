<?php
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Sales\Api\Data\OrderInterface as OI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender as IS;
use Magento\Sales\Model\Order\Invoice as I;
/**
 * 2016-03-27
 * @param OI|O $order
 * @param int $transactionId
 * @param $allowNull [optional]
 * @return I|null
 */
function df_invoice_by_trans(OI $order, $transactionId, $allowNull = false) {
	/** @var I|null $result */
	$result = df_ftn(DfPayment::getInvoiceForTransactionId($order, $transactionId));
	df_assert($allowNull || $result);
	return $result;
}

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_order_send_email() instead of @see df_invoice_send_email()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @used-by \Df\PaypalClone\W\Confirmation::sendEmailIfNeeded()
 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @param I $i
 * @return void
 */
function df_invoice_send_email(I $i) {/** @var IS $s */$s = df_o(IS::class); $s->send($i);}