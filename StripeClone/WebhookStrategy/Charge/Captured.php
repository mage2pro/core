<?php
// 2017-01-06
namespace Df\StripeClone\WebhookStrategy\Charge;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Invoice as DfInvoice;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
final class Captured extends \Df\StripeClone\WebhookStrategy\Charge {
	/**
	 * 2017-01-07
	 * @override
	 * @see \Df\StripeClone\WebhookStrategy::handle()
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	function handle() {
		/**
		 * 2016-12-30
		 * Мы не должны считать исключительной ситуацией повторное получение
		 * ранее уже полученного оповещения.
		 * В документации к Stripe, например, явно сказано:
		 * «Webhook endpoints may occasionally receive the same event more than once.
		 * We advise you to guard against duplicated event receipts
		 * by making your event processing idempotent.»
		 * https://stripe.com/docs/webhooks#best-practices
		 */
		if (!$this->o()->canInvoice()) {
			$this->resultSet('The order does not allow an invoice to be created.');
		}
		else {
			$this->o()->setIsInProcess(true);
			$this->o()->setCustomerNoteNotify(true);
			/** @var Transaction $t */
			$t = df_db_transaction();
			$t->addObject($this->invoice());
			$t->addObject($this->o());
			$t->save();
			/** @var InvoiceSender $sender */
			$sender = df_o(InvoiceSender::class);
			$sender->send($this->invoice());
			$this->resultSet($this->ii()->getId());
		}
	}
	
	/**
	 * 2016-03-26
	 * @return Invoice|DfInvoice
	 * @throws LE
	 */
	private function invoice() {return dfc($this, function() {
		/** @var InvoiceService $invoiceService */
		$invoiceService = df_o(InvoiceService::class);
		/** @var Invoice|DfInvoice $result */
		$result = $invoiceService->prepareInvoice($this->o());
		if (!$result) {
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
		 * Используем именно \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE,
		 * а не \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFINE,
		 * чтобы была создана транзакция capture.
		 */
		$result->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
		$result->register();
		return $result;
	});}	
}