<?php
namespace Df\Payment\W\Strategy;
use Df\Sales\Model\Order as DFO;
use Df\Sales\Model\Order\Invoice as DfInvoice;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
/**
 * 2017-01-06
 * 2017-03-26
 * Эта стратегия в силу своей реализации успешно работает только с ранее авторизованными транзакциями.
 * Она работает через вызов @uses \Magento\Sales\Model\Order\Invoice::register()
 * с предварительной установкой  $result->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
 * а это в свою очередь вызывает метод @uses \Magento\Sales\Model\Order\Payment::canCapture(),
 * который возвращает true только при наличии предыдущей транзакции типа авторизация:
 *	// Check Authorization transaction state
 *	$authTransaction = $this->getAuthorizationTransaction();
 *	if ($authTransaction && $authTransaction->getIsClosed()) {
 *		$orderTransaction = $this->transactionRepository->getByTransactionType(
 *			Transaction::TYPE_ORDER,
 *			$this->getId(),
 *			$this->getOrder()->getId()
 *		);
 *		if (!$orderTransaction) {
 *			return false;
 *		}
 *	}
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment.php#L268-L279
 */
final class CapturePreauthorized extends \Df\Payment\W\Strategy {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\Payment\W\Strategy::_handle()
	 * @used-by \Df\Payment\W\Strategy::handle()
	 */
	protected function _handle() {
		$o = $this->o(); /** @var O|DFO $o */
		// 2016-12-30
		// Мы не должны считать исключительной ситуацией повторное получение
		// ранее уже полученного оповещения.
		// В документации к Stripe, например, явно сказано:
		// «Webhook endpoints may occasionally receive the same event more than once.
		// We advise you to guard against duplicated event receipts
		// by making your event processing idempotent.»
		// https://stripe.com/docs/webhooks#best-practices
		if (!$o->canInvoice()) {
			$this->softFailure('The order does not allow an invoice to be created.');
		}
		else {
			$o->setCustomerNoteNotify(true)->setIsInProcess(true);
			/** @var Invoice $i */
			df_db_transaction()->addObject($i = $this->invoice())->addObject($o)->save(); 
			df_mail_invoice($i);
			// 2017-09-13
			// We do not set a response here, because PayPal clones require a specific response on success.
		}
	}
	
	/**
	 * 2016-03-26
	 * @used-by _handle()
	 * @return Invoice|DfInvoice
	 * @throws LE
	 */
	private function invoice() {
		$invoiceService = df_o(InvoiceService::class); /** @var InvoiceService $invoiceService */
		/** @var Invoice|DfInvoice $result */
		if (!($result = $invoiceService->prepareInvoice($this->o()))) {
			throw new LE(__('We can\'t save the invoice right now.'));
		}
		if (!$result->getTotalQty()) {
			throw new LE(__('You can\'t create an invoice without products.'));
		}
		df_register('current_invoice', $result);
		/**
		 * 2016-03-26
		 * @used-by \Magento\Sales\Model\Order\Invoice::register()
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Invoice.php#L599-L609
		 * Используем именно @see \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE,
		 * а не @see \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFINE,
		 * чтобы была создана транзакция capture.
		 */
		$result->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
		$result->register();
		return $result;
	}
}