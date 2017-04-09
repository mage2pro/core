<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Df\Payment\Operation\Source;
use Df\Payment\Operation\Source\Creditmemo as SCreditmemo;
use Df\Payment\Operation\Source\Order as SOrder;
use Df\Payment\Operation\Source\Quote as SQuote;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
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
	 * Converts $a from a sales document currency to the payment currency,
	 * and then formats the result according to the payment service rules.
	 * The payment currency is usually set here: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_shipping()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_discount()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_shipping()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_tax()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
	 * @param float $a
	 * @return float|int|string
	 */
	final function cFromDocF($a) {return $this->amountFormat($this->cFromDoc($a));}

	/**
	 * 2016-08-31
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by amountFormat()
	 * @used-by s()
	 * @used-by \Df\PaypalClone\Signer::_sign()   
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
	 * @return M
	 */
	function m() {return $this->_src->m();}

	/**
	 * 2016-08-26
	 * Несмотря на то, что опция @see \Df\Payment\Settings::requireBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при requireBillingAddress = false платёжный адрес является вырожденным:
	 * он содержит только email покупателя.
	 * @return OA|QA
	 */
	final protected function addressB() {return $this->_src->addressB();}

	/**
	 * 2016-07-02
	 * @see addressSB()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return OA
	 */
	final protected function addressBS() {return $this->_src->addressBS();}

	/**
	 * 2016-08-26
	 * 2017-04-10
	 * Если адрес доставки отсутствует, то:
	 * 1) @uses \Magento\Sales\Model\Order::getShippingAddress() возвращает null
	 * 1) @uses \Magento\Quote\Model\Quote::getShippingAddress() возвращает пустой объект
	 * @return OA|QA|null
	 */
	final protected function addressS() {return $this->_src->addressS();}

	/**
	 * 2016-07-02
	 * @see addressBS()
	 * @return OA
	 */
	final protected function addressSB() {return $this->_src->addressSB();}

	/**
	 * 2016-09-05
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by amountF()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
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
	 * @used-by cFromDocF()
	 * @see \Dfe\SecurePay\Charge::amountFormat()
	 * @see \Dfe\SecurePay\Refund::amountFormat()
	 * @param float $a
	 * @return float|int|string
	 */
	protected function amountFormat($a) {return $this->m()->amountFormat($a);}

	/**
	 * 2016-09-06
	 * Converts $a from a sales document currency to the payment currency.
	 * The payment currency is usually set here: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by cFromDocF()
	 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
	 * @param float $a
	 * @return float
	 */
	final protected function cFromDoc($a) {return $this->_src->cFromDoc($a);}

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
	final protected function currencyC() {return $this->_src->currencyC();}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @return string
	 */
	final protected function customerEmail() {return $this->_src->customerEmail();}

	/**
	 * 2016-08-24
	 * @return string
	 */
	final protected function customerName() {return $this->_src->customerName();}

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
	protected function s() {return $this->_src->s();}

	/**
	 * 2016-05-06
	 * @used-by \Df\Payment\Charge::vars()
	 * @return Store
	 */
	final protected function store() {return $this->_src->store();}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by addressB()
	 * @used-by addressBS()
	 * @used-by addressS()
	 * @used-by addressSB()
	 * @used-by cFromDoc()
	 * @used-by currencyC()
	 * @used-by customerEmail()
	 * @used-by m()
	 * @used-by id()
	 * @used-by ii()
	 * @used-by s()
	 * @used-by store()
	 * @var Source|SOrder|SQuote|SCreditmemo
	 */
	private $_src;
}