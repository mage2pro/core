<?php
namespace Df\PaypalClone;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Payment\Model\Method\AbstractMethod as M;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-07-12
 * @see \Df\GingerPaymentsBase\Webhook
 * @see \Dfe\AllPay\Webhook
 * @see \Dfe\SecurePay\Webhook
 */
abstract class Confirmation extends Webhook {
	/**
	 * 2017-01-01
	 * @override
	 * @see \Df\Payment\Webhook::_handle()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return void
	 */
	final protected function _handle() {
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственная транзакции без специального назначения,
		 * и поэтому мы можем безопасно его использовать.
		 *
		 * 2017-01-16
		 * Идентификатор и данные транзакции мы уже установили в методе
		 * @see \Df\Payment\Webhook::initTransaction()
		 */
		$this->ii()->addTransaction(T::TYPE_PAYMENT);
		/** @var bool $succ */
		$succ = $this->isSuccessful();
		/**
		 * 2016-07-14
		 * Если покупатель не смог или не захотел оплатить заказ, то мы заказ отменяем,
		 * а затем, когда платёжная система возвратит покупателя в магазин,
		 * то мы проверим, не отменён ли последний заказ,
		 * и если он отменён — то восстановим корзину покупателя.
		 */
		if (!$succ) {
			$this->o()->cancel();
		}
		else if ($this->needCapture()) {
			$this->capture();
		}
		$this->o()->save();
		/**
		 * 2016-08-17
		 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/17
		 * Письмо отсылаем только если isSuccessful() вернуло true
		 * (при этом не факт, что оплата уже прошла: при оффлайновом способе оплаты
		 * isSuccessful() говорит лишь о том, что покупатель успешно выбрал оффлайновый способ оплаты,
		 * а подтверждение платежа придёт лишь потом, через несколько дней).
		 */
		if ($succ) {
			$this->sendEmailIfNeeded();
		}
	}

	/**
	 * 2016-07-20
	 * @used-by _handle()
	 * @see \Dfe\AllPay\Webhook\BankCard::needCapture()
	 * @see \Dfe\AllPay\Webhook\Offline::needCapture()
	 * @see \Dfe\AllPay\Webhook\WebATM::needCapture()
	 * @return bool
	 */
	protected function needCapture() {return $this->c();}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::type()
	 * @used-by \Df\Payment\Webhook::typeLabel()
	 * @used-by \Dfe\AllPay\Webhook::classSuffix()
	 * @used-by \Dfe\AllPay\Webhook::typeLabel()
	 * @see \Dfe\AllPay\Webhook::type()
	 * @return string
	 */
	protected function type() {return 'confirmation';}

	/**
	 * 2016-07-12
	 * @return void
	 */
	private function capture() {
		/** @var IOP|OP $payment */
		$payment = $this->ii();
		/** @var Method $method */
		$method = $payment->getMethodInstance();
		$method->setStore($this->o()->getStoreId());
		DfPayment::processActionS($payment, M::ACTION_AUTHORIZE_CAPTURE, $this->o());
		DfPayment::updateOrderS(
			$payment
			, $this->o()
			, Order::STATE_PROCESSING
			, $this->o()->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
			, $isCustomerNotified = true
		);
	}

	/**
	 * 2016-08-17
	 * 2016-07-15
	 * Send email confirmation to the customer.
	 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/6
	 * It is implemented by analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Paypal/Model/Ipn.php#L312-L321
	 *
	 * 2016-07-15
	 * What is the difference between InvoiceSender and OrderSender?
	 * https://mage2.pro/t/1872
	 *
	 * 2016-07-18
	 * Раньше тут был код:
			$payment = $this->o()->getPayment();
			if ($payment && $payment->getCreatedInvoice()) {
				df_order_send_email($this->o());
			}
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
	 * @used-by handle()
	 * @return void
	 */
	private function sendEmailIfNeeded() {
		/**
		 * 2016-08-17
		 * @uses \Magento\Sales\Model\Order::getEmailSent() говорит,
		 * было ли уже отослано письмо о заказе.
		 * Отсылать его повторно не надо.
		 */
		if (!$this->o()->getEmailSent()) {
			df_order_send_email($this->o());
		}
		/**
		 * 2016-08-17
		 * Помещаем код ниже в блок else,
		 * потому что если письмо с заказом уже отослано,
		 * то письмо со счётом отсылать не надо,
		 * даже если счёт присутствует и письмо о нём не отсылалось.
		 */
		else {
			/**
			 * 2016-08-17
			 * Перед вызовом
			 * @uses \Magento\Framework\Data\Collection::getLastItem()
			 * @var \Magento\Sales\Model\Order\Invoice $invoice
			 */
			/** @var \Magento\Sales\Model\Order\Invoice $invoice */
			$invoice = $this->o()->getInvoiceCollection()->getLastItem();
			/**
			 * 2016-08-17
			 * @uses \Magento\Framework\Data\Collection::getLastItem()
			 * возвращает объект, если коллекция пуста.
			 */
			if ($invoice->getId() && !$invoice->getEmailSent()) {
				df_invoice_send_email($invoice);
			}
		}
	}
}