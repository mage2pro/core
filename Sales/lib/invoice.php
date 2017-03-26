<?php
use Df\Core\Exception as DFE;
use Df\Sales\Model\Order\Payment as DfOP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender as IS;
use Magento\Sales\Model\Order\Invoice as I;
/**
 * 2016-03-27
 * @used-by dfp_refund()
 * @param O $o
 * @param int $tid
 * @return I|null
 * @throws DFE
 */
function df_invoice_by_trans(O $o, $tid) {return DfOP::getInvoiceForTransactionId($o, $tid) ?: df_error(
	"No invoice found for the transaction {$tid}."
);}

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_order_send_email() instead of @see df_invoice_send_email()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @used-by \Df\PaypalClone\W\Handler::sendEmailIfNeeded()
 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @param I $i
 * @return void
 */
function df_invoice_send_email(I $i) {/** @var IS $s */$s = df_o(IS::class); $s->send($i);}