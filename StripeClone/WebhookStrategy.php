<?php
// 2017-01-06
namespace Df\StripeClone;
use Df\Sales\Model\Order as DfOrder;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
abstract class WebhookStrategy {
	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	abstract public function handle();

	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @param Webhook $w
	 */
	final public function __construct(Webhook $w) {$this->_w = $w;}

	/**
	 * 2017-01-07
	 * @return IOP|OP|null
	 */
	final protected function ii() {return $this->_w->ii();}

	/**
	 * 2017-01-06
	 * @return Order|DfOrder
	 */
	final protected function o() {return $this->_w->o();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\Webhook\Charge\RefundedStrategy::parentId()
	 * @return string
	 */
	final protected function parentId() {return $this->_w->parentId();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\Webhook\Charge\CapturedStrategy::handle()
	 * @param Result|Phrase|string $v
	 * @return void
	 */
	final protected function resultSet($v) {$this->_w->resultSet($v);}

	/**
	 * 2017-01-07
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function ro($k = null, $d = null) {return $this->_w->ro($k, $d);}

	/**
	 * 2017-01-06
	 * @used-by __construct()
	 * @used-by o()
	 * @used-by resultSet()
	 * @var Webhook
	 */
	private $_w;
}