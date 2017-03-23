<?php
namespace Df\PaypalClone;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-30
 * @see \Dfe\SecurePay\Refund
 * @method Method\Normal m()
 */
abstract class Refund extends \Df\Payment\Operation {
	/**
	 * 2016-08-30
	 * @override
	 * @see \Df\Payment\Operation::amountFromDocument()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float
	 */
	final protected function amountFromDocument() {return $this->cm()->getGrandTotal();}

	/**
	 * 2016-08-30
	 * @return CM
	 */
	final protected function cm() {return $this->payment()->getCreditmemo();}

	/**
	 * 2016-08-31
	 * Первый параметр — для test, второй — для live.
	 * @used-by url()
	 * @see \Dfe\SecurePay\Refund::stageNames()
	 * @return string[]
	 */
	protected function stageNames() {return $this->m()->stageNames();}

	/**
	 * 2016-08-31
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @param string $url
	 * @param mixed[] ...$args [optional]
	 * @return string
	 */
	final protected function url($url, ...$args) {return df_url_staged(
		$this->ss()->test(), $url, $this->stageNames(), ...$args
	);}
}