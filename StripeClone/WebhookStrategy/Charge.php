<?php
// 2017-01-15
namespace Df\StripeClone\WebhookStrategy;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
abstract class Charge extends \Df\StripeClone\WebhookStrategy {
	/**
	 * 2017-01-15
	 * @param string $action
	 * @return void
	 */
	final protected function action($action) {
		/** @var O $o */
		$o = $this->o();
		/** @var OP $ii */
		$ii = $this->ii();
		$this->m()->setStore($o->getStoreId());
		DfPayment::processActionS($ii, $action, $o);
		/** @var string $status */
		$status = $o->getConfig()->getStateDefaultStatus(O::STATE_PROCESSING);
		DfPayment::updateOrderS($ii, $o, O::STATE_PROCESSING, $status, $isCustomerNotified = true);
		$o->save();
	}
}