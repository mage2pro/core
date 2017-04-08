<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Df\Payment\Operation\Source;
use Df\Payment\Operation\Source\Creditmemo as SCreditmemo;
use Df\Payment\Operation\Source\Order as SOrder;
use Df\Payment\Operation\Source\Quote as SQuote;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
/**
 * 2016-08-30
 * @see \Df\Payment\Charge
 * @see \Dfe\SecurePay\Refund
 */
abstract class Operation implements IMA {
	/**
	 * 2017-03-12
	 * @used-by \Df\GingerPaymentsBase\Charge::p()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\Charge::request()
	 * @used-by \Dfe\CheckoutCom\Charge::build()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\Square\Charge::p()
	 * @used-by \Dfe\TwoCheckout\Charge::p()
	 * @param Source|SOrder|SQuote|SCreditmemo|M $src
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	final function __construct($src) {$this->_src = $src instanceof M ? new SOrder($src) : $src;}

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
	function m() {return $this->_src->m();}

	/**
	 * 2016-09-05
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @return float
	 */
	final protected function amount() {return $this->_src->amount();}

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
	 * 2016-09-06
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\CheckoutCom\Charge::metaData()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\Spryng\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @see \Dfe\AllPay\Charge::id()
	 * @return string
	 */
	protected function id() {return df_result_sne($this->_src->id());}

	/**
	 * 2016-08-30
	 * @used-by o()
	 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
	 * @used-by \Df\StripeClone\Charge::token()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Square\Charge::pCharge()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return II|I|OP
	 */
	final protected function ii() {return $this->_src->ii();}

	/**
	 * @used-by \Df\Payment\Charge::addressB()
	 * @used-by \Df\Payment\Charge::addressMixed()
	 * @used-by \Df\Payment\Charge::addressS()
	 * @used-by \Df\Payment\Charge::c()
	 * @used-by \Df\Payment\Charge::customerDob()
	 * @used-by \Df\Payment\Charge::customerEmail()
	 * @used-by \Df\Payment\Charge::customerGender()
	 * @used-by \Df\Payment\Charge::customerName()
	 * @used-by \Df\Payment\Charge::customerNameA()
	 * @used-by \Df\Payment\Charge::customerIp()
	 * @used-by \Df\Payment\Charge::oiLeafs()
	 * @used-by \Df\Payment\Charge::vars()
	 * @used-by \Dfe\AllPay\Charge::id()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Charge::use3DS()
	 * @used-by \Dfe\Stripe\Charge::pShipping()
	 * @used-by \Dfe\TwoCheckout\Charge::liDiscount()
	 * @used-by \Dfe\TwoCheckout\Charge::liShipping()
	 * @used-by \Dfe\TwoCheckout\Charge::liTax()
	 * @return Order
	 */
	final protected function o() {return df_order($this->ii());}

	/**
	 * 2016-09-06
	 * 2017-01-22
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\StripeClone\Charge::request()
	 * @return Settings
	 */
	protected function s() {return $this->m()->s();}

	/**
	 * 2016-05-06
	 * @used-by \Df\Payment\Charge::vars()
	 * @return Store
	 */
	final protected function store() {return $this->_src->store();}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by amount()
	 * @used-by m()
	 * @used-by store()
	 * @var Source|SOrder|SQuote|SCreditmemo
	 */
	private $_src;
}