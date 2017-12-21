<?php
namespace Df\Payment;
use Df\Customer\Model\Customer as DFCustomer;
use Df\Customer\Model\Gender as G;
use Df\Payment\Method as M;
// 2017-07-27
// PHP 5.6.28: «Cannot use Df\Payment\Operation\Source as Source
// because the name is already in use in Payment/Operation.php on line 6»
// https://github.com/mage2pro/core/issues/17
use Df\Payment\Operation\Source as _Source;
use Df\Payment\Operation\Source\Creditmemo as SCreditmemo;
use Df\Payment\Operation\Source\Order as SOrder;
use Df\Payment\Operation\Source\Quote as SQuote;
use Magento\Customer\Model\Customer as C;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
use Zend_Date as ZD;
/**
 * 2016-08-30
 * @see \Df\Payment\Charge
 * @see \Df\StripeClone\P\Preorder
 * @see \Df\StripeClone\P\Reg
 * @see \Dfe\SecurePay\Refund
 * @see \Dfe\Square\P\Address
 * @see \Dfe\Stripe\P\Address
 */
abstract class Operation implements IMA {
	/**
	 * 2017-03-12
	 * @used-by \Df\GingerPaymentsBase\Charge::p()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\P\Charge::sn()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\CheckoutCom\Charge::build()
	 * @used-by \Dfe\Qiwi\Init\Action::charge()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\Stripe\P\_3DS::p()
	 * @used-by \Dfe\TwoCheckout\Charge::p()
	 * @param _Source|SOrder|SQuote|SCreditmemo|M $src
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
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_discount()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_shipping()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItem_tax()
	 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
	 * @used-by \Dfe\YandexKassa\Charge::pLoan()
	 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
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
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
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
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\Moip\P\Charge::v_CardId()
	 * @used-by \Dfe\PostFinance\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\P\Reg::p()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return OA|QA
	 */
	final protected function addressB() {return $this->_src->addressB();}

	/**
	 * 2016-07-02
	 * @see addressSB()
	 * @used-by customerPhone()
	 * @used-by locale()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @return OA
	 */
	final protected function addressBS() {return $this->_src->addressBS();}

	/**
	 * 2016-08-26
	 * 2017-04-10
	 * An order/quote can be without a shipping address (consist of the Virtual products). In this case:
	 * *) @uses \Magento\Sales\Model\Order::getShippingAddress() returns null
	 * *) @uses \Magento\Quote\Model\Quote::getShippingAddress() returns an empty object.
	 * 2017-11-02
	 * It is useful for me to return an empty object in the both cases.
	 * https://en.wikipedia.org/wiki/Null_object_pattern
	 * An empty order address can be detected by a `null`-response on
	 * @see \Magento\Sales\Model\Order\Address::getParentId()
	 * @used-by customerNameA()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Moip\P\Reg::p()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Stripe\P\Address::p()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @param bool $empty [optional]
	 * @return OA|QA
	 */
	final protected function addressS($empty = false) {return $this->_src->addressS($empty);}

	/**
	 * 2016-07-02
	 * @see addressBS()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\CheckoutCom\Charge::cPhone()
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
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\PaypalClone\Charge::testAmountF()
	 * @used-by \Df\StripeClone\P\Charge::amountAndCurrency()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Qiwi\Charge::pBill()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\Stripe\P\_3DS::p()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
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
	 * @used-by \Dfe\Moip\P\Preorder::amountMargin()
	 * @used-by \Dfe\Moip\P\Preorder::pAmount()
	 * @used-by \Dfe\Moip\P\Preorder::pItems()
	 * @see \Dfe\SecurePay\Charge::amountFormat()
	 * @see \Dfe\SecurePay\Refund::amountFormat()
	 * @param float $a
	 * @return float|int|string
	 */
	protected function amountFormat($a) {return $this->m()->amountFormat($a);}

	/**
	 * 2016-08-22
	 * @used-by \Dfe\Stripe\P\Reg::p()
	 * @return C|null
	 */
	final protected function c() {return dfc($this, function() {/** @var int|null $id $id */return
		!($id = $this->o()->getCustomerId()) ? null : df_customer($id)
	;});}

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
	 * 2016-08-17 The payment currency code: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
	 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_shipping()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\P\Charge::amountAndCurrency()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Moip\P\Preorder::pAmount()
	 * @used-by \Dfe\Qiwi\Charge::pBill()
	 * @used-by \Dfe\Stripe\P\_3DS::p()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return string
	 */
	final protected function currencyC() {return $this->_src->currencyC();}

	/**
	 * 2017-02-18
	 * @uses \Magento\Sales\Model\Order::getCustomerDob() возвращает строку вида «1982-07-08 00:00:00».
	 * @used-by customerDobS()
	 * @return ZD|null
	 */
	final protected function customerDob() {return dfc($this, function() {return
		!($s = $this->o()->getCustomerDob()) ? null : df_date_from_db($s)
	;});}

	/**
	 * 2017-02-18
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\Moip\P\Reg::p()
	 * @used-by \Dfe\Spryng\P\Reg::p()
	 * @param string $format [optional]
	 * @return string|null
	 */
	final protected function customerDobS($format = 'Y-MM-dd') {return
		!($zd = $this->customerDob()) ? null : $zd->toString($format)
	;}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\Moip\P\Reg::p()
	 * @used-by \Dfe\Square\P\Charge::p()
	 * @used-by \Dfe\YandexKassa\Charge::pCharge()
	 * @used-by \Dfe\YandexKassa\Charge::pTax()
	 * @return string
	 */
	final protected function customerEmail() {return $this->_src->customerEmail();}

	/**
	 * 2017-02-18
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\Spryng\P\Reg::p()
	 * @param string $m
	 * @param string $f
	 * @return string|null
	 */
	final protected function customerGender($m, $f) {return dfa(
		[G::MALE => $m, G::FEMALE => $f], $this->o()->getCustomerGender()
	);}

	/**
	 * 2016-08-24
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Moip\P\Reg::p()
	 * @used-by \Dfe\PostFinance\Charge::pCharge()
	 * @return string
	 */
	final protected function customerName() {return $this->_src->customerName();}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\P\Reg::p()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @return string|null
	 */
	final protected function customerNameF() {return df_first($this->customerNameA());}

	/**
	 * 2016-08-26
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Spryng\P\Reg::p()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @return string|null
	 */
	final protected function customerNameL() {return df_last($this->customerNameA());}

	/**
	 * 2017-09-16
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Moip\P\Charge::v_CardId()
	 * @used-by \Dfe\Moip\P\Reg::p()
	 * @used-by \Dfe\PostFinance\Charge::pCharge()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @used-by \Dfe\YandexKassa\Charge::pCharge()
	 * @return string
	 */
	final protected function customerPhone() {return $this->addressBS()->getTelephone();}

	/**
	 * 2016-08-27
	 * Этот метод решает 2 проблемы, возникающие при работе на localhost:
	 * 1) Некоторые способы оплаты (SecurePay) вообще не позволяют указывать локальные адреса.
	 * 2) Некоторые способы оплаты (allPay) допускают локальный адрес возврата,
	 * но для тестирования его нам использовать нежелательно,
	 * потому что сначала вручную сэмулировать и обработать callback.
	 *
	 * 2017-03-06
	 * Этот метод имеет преимущества перед функцией @see df_url_checkout_success(),
	 * изложенные мной 2016-07-14 для модуля allPay:
	 *
	 * «Раньше здесь стояло df_url_checkout_success(),
	 * что показывало покупателю страницу об успешности заказа
	 * даже если покупатель не смог или не захотел оплачивать заказ.
	 *
	 * Теперь же, покупатель не смог или не захотел оплатить заказ,
	 * то при соответствующем («ReturnURL») оповещении платёжной системы
	 * мы заказ отменяем, а затем, когда платёжная система возврат покупателя в магазин,
	 * то мы проверим, не отменён ли последний заказ,
	 * и если он отменён — то восстановим корзину покупателя.»
	 * https://github.com/mage2pro/allpay/blob/1.1.31/Charge.php?ts=4#L365-L378
	 *
	 * @see customerReturnRemoteWithFailure()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\IPay88\Charge::pCharge()
	 * @used-by \Dfe\Moip\P\Preorder::pCheckoutPreferences()
	 * @used-by \Dfe\Qiwi\Charge::pRedirect()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\Stripe\P\_3DS::p()
	 * @used-by \Dfe\YandexKassa\Charge::pCharge()
	 * @return string
	 */
	final protected function customerReturnRemote() {return dfp_url_customer_return_remote($this->m());}

	/**
	 * 2017-08-23
	 * @see customerReturnRemote()
	 * @used-by \Dfe\PostFinance\Charge::pCharge()
	 * @used-by \Dfe\Qiwi\Charge::pRedirect()
	 * @return string
	 */
	final protected function customerReturnRemoteWithFailure() {return dfp_url_customer_return_remote_f(
		$this->m()
	);}

	/**
	 * 2017-11-01
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge(
	 * @return string|null
	 */
	final protected function customerVAT() {return $this->_src->customerVAT();}

	/**
	 * 2016-09-06
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * 2017-09-04 Our local (without the module prefix) internal payment ID.
	 * @used-by \Df\GingerPaymentsBase\Charge::pCharge()
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\CheckoutCom\Charge::metaData()
	 * @used-by \Dfe\Moip\P\Preorder::p()
	 * @used-by \Dfe\SecurePay\Charge::pCharge()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\Spryng\P\Charge::p()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @see \Df\PaypalClone\Charge::id()
	 * @see \Dfe\Qiwi\Charge::id()
	 * @return string
	 */
	protected function id() {return df_result_sne($this->_src->id());}

	/**
	 * 2016-08-30
	 * @used-by o()
	 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
	 * @used-by \Df\StripeClone\P\Charge::token()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\TwoCheckout\Charge::pCharge()
	 * @return II|I|OP
	 */
	final protected function ii() {return $this->_src->ii();}

	/**
	 * 2017-03-06
	 * @used-by oiLeafs()
	 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
	 * @return string
	 */
	final protected function locale() {return dfc($this, function() {return df_locale_by_country(
		$this->addressBS()->getCountryId()
	);});}

	/**
	 * @used-by \Df\Payment\Charge::addressB()
	 * @used-by \Df\Payment\Charge::addressS()
	 * @used-by \Df\Payment\Charge::c()
	 * @used-by \Df\Payment\Charge::customerDob()
	 * @used-by \Df\Payment\Charge::customerEmail()
	 * @used-by \Df\Payment\Charge::customerGender()
	 * @used-by \Df\Payment\Charge::customerIp()
	 * @used-by \Df\Payment\Charge::customerName()
	 * @used-by \Df\Payment\Charge::customerNameA()
	 * @used-by \Df\Payment\Charge::oiLeafs()
	 * @used-by \Df\Payment\Charge::vars()
	 * @used-by \Dfe\AllPay\Charge::id()
	 * @used-by \Dfe\AllPay\Charge::pCharge()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Moip\P\Preorder::pAmount()
	 * @used-by \Dfe\Qiwi\Charge::id()
	 * @used-by \Dfe\Stripe\P\Address::p()
	 * @used-by \Dfe\TwoCheckout\Charge::liDiscount()
	 * @used-by \Dfe\TwoCheckout\Charge::liShipping()
	 * @used-by \Dfe\TwoCheckout\Charge::liTax()
	 * @return O
	 */
	final protected function o() {return df_order($this->ii());}

	/**
	 * 2016-09-07
	 * 2017-02-02
	 * Отныне метод упорядочивает позиции заказа по имени.
	 * Ведь этот метод используется только для передачи позиций заказа в платежные системы,
	 * а там они отображаются покупателю и администратору,
	 * и удобно, чтобы они были упорядочены по имени.
	 * @used-by \Dfe\AllPay\Charge::productUrls()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
	 * @used-by \Dfe\CheckoutCom\Charge::setProducts()
	 * @used-by \Dfe\Moip\P\Preorder::pItems()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
	 * @used-by \Dfe\YandexKassa\Charge::pLoan()
	 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
	 * @param \Closure $f
	 * @return array(int => mixed)
	 */
	final protected function oiLeafs(\Closure $f) {return df_oqi_leafs($this->o(), $f, $this->locale());}

	/**
	 * 2016-09-06
	 * 2017-01-22
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return Settings
	 */
	protected function s() {return $this->_src->s();}

	/**
	 * 2016-05-06
	 * @used-by \Df\Payment\Charge::vars()
	 * @used-by \Dfe\Qiwi\Charge::pBill()
	 * @return Store
	 */
	final protected function store() {return $this->_src->store();}

	/**
	 * 2016-08-26
	 * @return array(string|null)
	 */
	private function customerNameA() {return dfc($this, function() {
		/** @var O $o */ $o = $this->o();
		/** @var OA $ba */ $ba = $this->addressB();
		/** @var C|DFCustomer $c */
		/** @var string|null $f */
		return ($f = $o->getCustomerFirstname()) ? [$f, $o->getCustomerLastname()] : (
			($c = $o->getCustomer()) && ($f = $c->getFirstname()) ? [$f, $c->getLastname()] : (
				$f = $ba->getFirstname() ? [$f, $ba->getLastname()] : (
					($sa = $this->addressS()) && ($f = $sa->getFirstname()) ? [$f, $sa->getLastname()] :
						[null, null]
				)
			)
		);
	});}

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
	 * @var _Source|SOrder|SQuote|SCreditmemo
	 */
	private $_src;

	/**
	 * 2017-08-23
	 * @used-by dfp_url_customer_return_remote_f()
	 * @used-by \Df\Payment\CustomerReturn::isSuccess()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 */
	const FAILURE = 'failure';
}