<?php
use Magento\Framework\Exception\MailException as ME;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\Invoice as I;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Status\History as H;
use Magento\Sales\Model\ResourceModel\Order\Status\History\Collection as HC;

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_mail_order() instead of @see df_mail_invoice()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @used-by dfp_mail()
 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @throws ME
 */
function df_mail_invoice(I $i):void {
	$s = df_o(InvoiceSender::class); /** @var InvoiceSender $s */
	$s->send($i);
}

/**
 * 2016-05-06
 * https://mage2.pro/t/1543
 * @see df_mail_invoice()
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_mail_order() instead of @see df_mail_invoice()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @used-by dfp_mail()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param O $o
 * @throws ME
 */
function df_mail_order(O $o):void {
	$s = df_o(OrderSender::class); /** @var OrderSender $s */
	$s->send($o);
	df_order_comment($o, 'You have confirmed the order to the customer via email.', false, true);
}

/**
 * 2019-05-17
 * @see \Magento\Shipping\Model\ShipmentNotifier
 * @see \Magento\Sales\Model\AbstractNotifier::notify()
 * @see df_mail_order()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @param Shipment $sh
 * @throws ME
 */
function df_mail_shipment(Shipment $sh):void {
	$s = df_new_om(ShipmentSender::class); /** @var ShipmentSender $s */
	$s->send($sh);
	if (!$sh->getEmailSent()) {
		df_error('A notification email is not sent for the «%s» shipment.', $sh->getIncrementId());
	}
	else {
		$hc = df_new_om(HC::class); /** @var HC $hc */
		if ($h = $hc->getUnnotifiedForInstance($sh)) { /** @var H $h */
			$h->setIsCustomerNotified(1);
			$h->save();
		}
	}
}