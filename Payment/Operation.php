<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
/**
 * 2016-08-30
 * @see \Df\Payment\Charge
 * @see \Df\PaypalClone\Refund
 */
abstract class Operation implements IMA {
	/**
	 * 2016-08-30
	 * @used-by \Df\Payment\Operation::amount()
	 * @see \Df\Payment\Charge::amountFromDocument()
	 * @see \Df\PaypalClone\Refund::amountFromDocument()
	 * @return float
	 */
	abstract protected function amountFromDocument();

	/**
	 * 2017-03-12
	 * @see \Df\Payment\Charge\WithToken::__construct()
	 * @used-by \Df\GingerPaymentsBase\Charge::p()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @param M $m
	 * @param float|null $amount [optional]
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	public function __construct(M $m, $amount = null) {$this->_m = $m; $this->_amount = $amount;}

	/**
	 * 2016-09-07
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_shipping()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_discount()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_shipping()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_tax()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
	 * @param float $amount
	 * @return float|int|string
	 */
	final function cFromOrderF($amount) {return $this->amountFormat($this->m()->cFromOrder($amount));}

	/**
	 * 2016-08-31
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by \Df\PaypalClone\Signer::_sign()   
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
	 * @return M
	 */
	function m() {return $this->_m;}

	/**
	 * 2016-09-05
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @return float
	 */
	final protected function amount() {return dfc($this, function() {return
		$this->_amount ?: $this->cFromOrder($this->amountFromDocument())
	;});}

	/**
	 * 2016-09-07
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * 2017-02-11
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\StripeClone\Charge::request()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Square\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return float|int|string
	 */
	final protected function amountF() {return dfc($this, function() {return
		$this->amountFormat($this->amount())
	;});}

	/**
	 * 2016-09-07
	 * Конвертирует денежную величину (в валюте платежа) из обычного числа в формат платёжной системы.
	 * В частности, некоторые платёжные системы хотят денежные величины в копейках (Checkout.com),
	 * обязательно целыми (allPay) и т.п.
	 * @used-by amountF()
	 * @see \Dfe\SecurePay\Charge::amountFormat()
	 * @see \Dfe\SecurePay\Refund::amountFormat()
	 * @param float $amount
	 * @return float|int|string
	 */
	protected function amountFormat($amount) {return $this->m()->amountFormat($amount);}

	/**
	 * 2016-08-17
	 * Код платёжной валюты: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * 2017-02-11
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_shipping()
	 * @used-by \Df\StripeClone\Charge::request()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Square\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return string
	 */
	final protected function currencyC() {return $this->m()->cPayment();}

	/**
	 * 2016-09-06
	 * Конвертирует $amount из валюты заказа в валюту платежа.
	 * @param float $amount
	 * @return float
	 */
	final protected function cFromOrder($amount) {return $this->m()->cFromOrder($amount);}

	/**
	 * 2016-08-08
	 * @see \Df\Payment\Method::iia()
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	final protected function iia(...$keys) {return dfp_iia($this->payment(), $keys);}

	/**
	 * @used-by \Dfe\TwoCheckout\Charge::liDiscount()
	 * @used-by \Dfe\TwoCheckout\Charge::liShipping()
	 * @used-by \Dfe\TwoCheckout\Charge::liTax()
	 * @return Order
	 */
	final protected function o() {return df_order($this->payment());}

	/**
	 * 2016-09-06
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\PaypalClone\Charge::requestId()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\CheckoutCom\Charge::metaData()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return string
	 */
	final protected function oii() {return $this->o()->getIncrementId();}

	/**
	 * 2016-08-30
	 * @used-by \Df\PaypalClone\Refund::tm()
	 * @return II|I|OP
	 */
	final protected function payment() {return $this->m()->getInfoInstance();}

	/**
	 * 2016-09-06
	 * 2017-01-22
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\StripeClone\Charge::request()
	 * @return Settings
	 */
	protected function ss() {return $this->m()->s();}

	/** @return Store */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by amount()
	 * @var float|null
	 */
	private $_amount;

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by m()
	 * @var M
	 */
	private $_m;
}