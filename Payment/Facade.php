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
 * @see \Df\StripeClone\Payer
 */
abstract class Facade {
	/**
	 * 2017-02-11
	 * Unfortunately, we a forced to make the constructor protected, not private,
	 * despite it is used only inside the class.
	 * An attempt to make it private leads to the failure:
	 * «Call to private Df\Payment\Facade::__construct() from context 'Df\StripeClone\Facade\Customer'».
	 * @see \Df\StripeClone\CardFormatter::__construct()
	 * https://github.com/mage2pro/core/blob/2.8.25/StripeClone/CardFormatter.php#L53-L62
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
	 * @used-by cm()
	 * @used-by \Df\StripeClone\Payer::token()
	 * @return II|I|OP|QP
	 */
	final protected function ii() {return $this->m()->getInfoInstance();}

	/**
	 * 2017-02-11
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by ii()
	 * @used-by \Dfe\Paymill\Facade\O::toArray()
	 * @used-by \Dfe\Square\Facade\Charge::create()
	 * @return M
	 */
	protected function m() {return $this->_m;}

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
	 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @used-by \Df\StripeClone\Method::fCharge()
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @used-by \Dfe\Moip\P\Preorder::p()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @param M $m
	 * @return self
	 */
	static function s(M $m) {return dfcf(function(M $m, $c) {
		/**
		 * 2017-07-19
		 * Unable to reduce the implementation to:
		 * 		df_new(df_con_heir($m, $c), $m);
		 * because @uses __construct() is protected.
		 * It is similar to @see \Df\StripeClone\CardFormatter::s()
		 * https://github.com/mage2pro/core/blob/2.8.25/StripeClone/CardFormatter.php#L79-L90
		 */
		$class = df_con_heir($m, $c); /** @var string $class */
		return new $class($m);
	}, [$m, static::class]);}
}