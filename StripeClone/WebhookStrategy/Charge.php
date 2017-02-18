<?php
namespace Df\StripeClone\WebhookStrategy;
use Df\Sales\Model\Order\Payment as DfPayment;
use Df\StripeClone\Method as M;
use Magento\Payment\Model\Method\AbstractMethod as AM;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-01-15
 * @see \Df\StripeClone\WebhookStrategy\Charge\Authorized
 * @see \Df\StripeClone\WebhookStrategy\Charge\Captured
 * @see \Df\StripeClone\WebhookStrategy\Charge\Refunded
 */
abstract class Charge extends \Df\StripeClone\WebhookStrategy {
	/**
	 * 2017-01-15
	 * @used-by \Df\StripeClone\WebhookStrategy\Charge\Authorized::handle()
	 * @return void
	 */
	final protected function action() {
		/** @var O $o */
		$o = $this->o();
		/** @var OP $ii */
		$ii = $this->ii();
		$coreAction = dftr($this->currentTransactionType(), [
			M::T_AUTHORIZE => AM::ACTION_AUTHORIZE
			,M::T_CAPTURE => AM::ACTION_AUTHORIZE_CAPTURE
		]);
		/**
		 * 2016-03-15
		 * Если оставить открытой транзакцию «capture»,
		 * то операция «void» (отмена авторизации платежа) будет недоступна:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 * Транзакция считается закрытой, если явно не указать «false».
		 *
		 * 2017-01-16
		 * Наоборот: если закрыть транзакцию типа «authorize»,
		 * то операция «Capture Online» из административного интерфейса будет недоступна:
		 * @see \Magento\Sales\Model\Order\Payment::canCapture()
				if ($authTransaction && $authTransaction->getIsClosed()) {
					$orderTransaction = $this->transactionRepository->getByTransactionType(
						Transaction::TYPE_ORDER,
						$this->getId(),
						$this->getOrder()->getId()
					);
					if (!$orderTransaction) {
						return false;
					}
				}
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L263-L281
		 * «How is \Magento\Sales\Model\Order\Payment::canCapture() implemented and used?»
		 * https://mage2.pro/t/650
		 * «How does Magento 2 decide whether to show the «Capture Online» dropdown
		 * on a backend's invoice screen?»: https://mage2.pro/t/2475
		 */
		$ii->setIsTransactionClosed(AM::ACTION_AUTHORIZE_CAPTURE === $coreAction);
		/**
		 * 2017-01-15
		 * $this->m()->setStore($o->getStoreId()); здесь не нужно,
		 * потому что это делается автоматически в ядре:
		 * @see \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize():
		 * 		$method->setStore($order->getStoreId());
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L44
		 */
		DfPayment::processActionS($ii, $coreAction, $o);
		/** @var string $status */
		$status = $o->getConfig()->getStateDefaultStatus(O::STATE_PROCESSING);
		DfPayment::updateOrderS($ii, $o, O::STATE_PROCESSING, $status, $isCustomerNotified = true);
		$o->save();
	}
}