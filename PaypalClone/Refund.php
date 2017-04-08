<?php
namespace Df\PaypalClone;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-30
 * @see \Dfe\SecurePay\Refund
 * @method Method m()
 */
abstract class Refund extends \Df\Payment\Operation {
	/**
	 * 2016-08-30
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @return CM
	 */
	final protected function cm() {return $this->ii()->getCreditmemo();}
}