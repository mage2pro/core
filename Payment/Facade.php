<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-02-11
 * @see \Df\StripeClone\Facade\Charge
 * @see \Df\StripeClone\Facade\Customer
 * @see \Df\StripeClone\Facade\O
 * @see \Df\StripeClone\Facade\Refund
 */
abstract class Facade {
	/**
	 * 2017-02-11
	 * Метод нужно объявлять именно protected, а не private, хотя он используется только в этом классе.
	 * Если объявить метод как private, то произойдёт сбой:
	 * «Call to private Df\Payment\Facade::__construct()
	 * from context 'Df\StripeClone\Facade\Customer'».
	 * @used-by s()
	 * @param M $m
	 */
	final protected function __construct(M $m) {$this->_m = $m;}
	
	/**
	 * 2017-02-11
	 * @used-by \Dfe\Stripe\Facade\Charge::refundMeta()
	 * @used-by \Dfe\Stripe\Facade\Charge::refundAdjustments()
	 * @return CM|null
	 */
	final protected function cm() {return $this->ii()->getCreditmemo();}

	/**
	 * 2017-02-11
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by ii()
	 * @used-by \Dfe\Paymill\Facade\O::toArray()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-02-11
	 * @used-by cm()
	 * @return II|I|OP|QP
	 */
	private function ii() {return $this->m()->getInfoInstance();}

	/**
	 * 2017-02-11
	 * @used-by __construct()
	 * @used-by m()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-02-11
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @used-by \Df\StripeClone\Method::fCharge()
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @param M $m
	 * @return self
	 */
	static function s(M $m) {return dfcf(function(M $m, $c) {
		/** @var string $class */
		$class = df_con_heir($m, $c);
		return new $class($m);
	}, [$m, static::class]);}
}