<?php
// 2017-01-15
namespace Df\StripeClone\WebhookStrategy;
use Df\Sales\Model\Order\Payment as DfPayment;
use Df\StripeClone\Method as M;
use Magento\Payment\Model\Method\AbstractMethod as AM;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
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