<?php
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender as IS;
use Magento\Sales\Model\Order\Invoice as I;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_mail_order() instead of @see df_mail_invoice()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @used-by dfp_mail()
 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @param I $i
 */
function df_mail_invoice(I $i) {/** @var IS $s */$is = df_o(IS::class); $is->send($i);}

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
 */
function df_mail_order(O $o) {
	/** @var OrderSender $os */
	$os = df_o(OrderSender::class);
	$os->send($o);
	df_order_comment($o, 'You have confirmed the order to the customer via email.', false, true);
}

/**
 * 2017-03-26
 * 2016-08-17
 * 2016-07-15
 * Send email confirmation to the customer.
 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/6
 * It is implemented by analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Paypal/Model/Ipn.php#L312-L321
 *
 * 2016-07-15 What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 *
 * 2016-07-18
 * Раньше тут был код:
 *		$payment = $this->o()->getPayment();
 *		if ($payment && $payment->getCreatedInvoice()) {
 *			df_mail_order($this->o());
 *		}
 *
 * 2016-08-17
 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/13
 * В сценарии оффлайновой оплаты мы попадаем в эту точку программы дважды:
 * 1) Когда платёжная система уведомляет нас о том,
 * что покупатель выбрал оффлайновый способ оплаты.
 * В этом случае счёта ещё нет ($this->capture() выше не выполнялось),
 * и отсылаем покупателю письмо с заказом.
 *
 * 2) Когда платёжная система уведомляет нас о приходе оплаты.
 * В этом случае счёт уже присутствует, и отсылаем покупателю письмо со счётом.
 *
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 *
 * @param O $o
 */
function dfp_mail(O $o) {
	/**
	 * 2016-08-17
	 * @uses \Magento\Sales\Model\Order::getEmailSent() говорит,
	 * было ли уже отослано письмо о заказе. Отсылать его повторно не надо.
	 */
	if (!$o->getEmailSent()) {
		df_mail_order($o);
	}
	// 2016-08-17
	// Помещаем код ниже в блок else, потому что если письмо с заказом уже отослано,
	// то письмо со счётом отсылать не надо, даже если счёт присутствует и письмо о нём не отсылалось.
	else {
		/** @var I $i */
		$i = $o->getInvoiceCollection()->getLastItem();
		/**
		 * 2016-08-17
		 * @uses \Magento\Framework\Data\Collection::getLastItem()
		 * возвращает объект, если коллекция пуста: https://mage2.pro/t/3538
		 */
		if ($i->getId() && !$i->getEmailSent()) {
			df_mail_invoice($i);
		}
	}
}