<?php
namespace Df\Payment\Operation\Source;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\Operation\ISource;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-04-07
final class Order implements ISource {
	/**
	 * 2017-04-08
	 * @used-by \Df\GingerPaymentsBase\Charge::p()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\Charge::request()
	 * @used-by \Dfe\CheckoutCom\Charge::build()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\Square\Charge::p()
	 * @used-by \Dfe\TwoCheckout\Charge::p()
	 * @param M $m
	 * @param float|null $amount [optional]
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	function __construct(M $m, $amount = null) {$this->_m = $m; $this->_amount = $amount;}

	/**
	 * 2017-04-08
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @override
	 * @see ISource::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	function amount() {return $this->_amount;}

	/**
	 * 2017-04-08
	 * @override
	 * @see ISource::ii()
	 * @used-by oq()
	 * @return OP
	 */
	function ii() {return dfc($this, function() {return df_ar($this->_m->getInfoInstance(), OP::class);});}

	/**
	 * 2017-04-08
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see IMA::m()
	 * @return M
	 */
	function m() {return $this->_m;}

	/**
	 * 2017-04-08
	 * @override
	 * @see ISource::oq()
	 * @return O
	 */
	function oq() {return df_order($this->ii());}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by amount()
	 * @var float|null
	 */
	private $_amount;

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by ii()
	 * @used-by m()
	 * @var M
	 */
	private $_m;
}