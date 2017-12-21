<?php
namespace Df\Payment\Operation;
use Df\Core\Exception as DFE;
use Df\Payment\Settings;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
/**
 * 2017-04-07
 * УРОВЕНЬ 1: исходные данные для операции.
 * УРОВЕНЬ 2: общие алгоритмы операций.
 * УРОВЕНЬ 3: непосредственно сама операция:
 * формирование запроса для конкретной ПС или для группы ПС (Stripe-подобных).
 * @see \Df\Payment\Operation\Source\Order
 * @see \Df\Payment\Operation\Source\Quote
 */
abstract class Source implements \Df\Payment\IMA {
	/**
	 * 2017-04-07
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @see \Df\Payment\Operation\Source\Order::amount()
	 * @see \Df\Payment\Operation\Source\Quote::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	abstract function amount();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::id()
	 * @see \Df\Payment\Operation\Source\Quote::id()
	 * @used-by \Df\Payment\Operation::id()
	 * @return string|int
	 */
	abstract function id();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::ii()
	 * @see \Df\Payment\Operation\Source\Quote::ii()
	 * @used-by \Df\Payment\Operation::ii()
	 * @return II|OP|QP
	 */
	abstract function ii();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::oq()
	 * @see \Df\Payment\Operation\Source\Quote::oq()
	 * @used-by addressB()
	 * @used-by addressS()
	 * @used-by cFromDoc()
	 * @used-by currencyC()
	 * @used-by customerEmail()
	 * @used-by customerName()
	 * @used-by store()
	 * @return O|Q
	 */
	abstract function oq();

	/**
	 * 2016-08-26
	 * Несмотря на то, что опция @see \Df\Payment\Settings::requireBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при requireBillingAddress = false платёжный адрес является вырожденным:
	 * он содержит только email покупателя.
	 * @used-by addressBS()
	 * @used-by addressSB()
	 * @used-by \Df\Payment\Operation::addressB()
	 * @return OA|QA
	 */
	final function addressB() {return $this->oq()->getBillingAddress();}

	/**
	 * 2016-07-02
	 * 2017-12-21
	 * Previously, I used @see addressMixed() here:
	 * 		return $this->addressMixed($bs = true);
	 * https://github.com/mage2pro/core/blob/3.5.2/Payment/Operation/Source.php#L80-L86
	 * https://github.com/mage2pro/core/blob/3.5.2/Payment/Operation/Source.php#L217-L267
	 * Now I think that such address mix is a bad idea.
	 * because the result address could contain components of completely different addresses.
	 * @see addressSB()
	 * @used-by \Df\Payment\Operation::addressBS()
	 * @return OA|QA|null
	 */
	final function addressBS() {
		$a = $this->addressB(); /** @var OA|QA $r */
		return $a->getFirstname() ? $a : $this->addressS(true);
	}

	/**
	 * 2017-04-10
	 * An order/quote can be without a shipping address (consist of the Virtual products). In this case:
	 * *) @uses \Magento\Sales\Model\Order::getShippingAddress() returns null
	 * *) @uses \Magento\Quote\Model\Quote::getShippingAddress() returns an empty object.
	 * 2017-11-02
	 * It is useful for me to return an empty object in the both cases.
	 * https://en.wikipedia.org/wiki/Null_object_pattern
	 * An empty order address can be detected by a `null`-response on
	 * @see \Magento\Sales\Model\Order\Address::getParentId()
	 * @used-by addressBS()
	 * @used-by addressSB()
	 * @used-by addressMixed()
	 * @used-by \Df\Payment\Operation::addressS()
	 * @param bool $empty [optional]
	 * @return OA|QA
	 */
	final function addressS($empty = false) {return df_oq_sa($this->oq(), $empty);}

	/**
	 * 2016-07-02
	 * 2017-12-21
	 * Previously, I used @see addressMixed() here:
	 * 		return $this->addressMixed($bs = false);
	 * https://github.com/mage2pro/core/blob/3.5.2/Payment/Operation/Source.php#L105-L111
	 * https://github.com/mage2pro/core/blob/3.5.2/Payment/Operation/Source.php#L217-L267
	 * Now I think that such address mix is a bad idea.
	 * because the result address could contain components of completely different addresses.
	 * @see addressBS()
	 * @used-by \Df\Payment\Operation::addressSB()
	 * @return OA|QA
	 */
	final function addressSB() {
		$a = $this->addressS(false); /** @var OA|QA $r */
		return $a ?: $this->addressB();
	}

	/**
	 * 2017-04-08
	 * Converts $a from a sales document currency to the payment currency.
	 * The payment currency is usually set here: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by \Df\Payment\Operation::cFromDoc()
	 * @param float $a
	 * @return float
	 */
	final function cFromDoc($a) {return dfpex_from_doc($a, $this->oq(), $this->m());}

	/**
	 * 2017-04-09
	 * Код платёжной валюты: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by \Df\Payment\Operation::currencyC()
	 * @return string
	 */
	final function currencyC() {return dfp_currency($this->m())->oq($this->oq());}

	/**
	 * 2017-04-09
	 * 1) Если покупатель АВТОРИЗОВАН, то email устанавливается в quote
	 * методом @see \Magento\Quote\Model\Quote::setCustomer():
	 * 		$this->_objectCopyService->copyFieldsetToTarget(
	 * 			'customer_account', 'to_quote', $customerDataFlatArray, $this
	 * 		);
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Quote/Model/Quote.php#L969
	 * 2) Если покупатель ГОСТЬ, то email устанавливается в quote ТОЛЬКО ПРИ РАЗМЕЩЕНИИ ЗАКАЗА
	 * (проверял в отладчике!) следующими методами:
	 * 2.1) @see \Magento\Checkout\Model\Type\Onepage::_prepareGuestQuote()
	 *		protected function _prepareGuestQuote()
	 *		{
	 *			$quote = $this->getQuote();
	 *			$quote->setCustomerId(null)
	 *				->setCustomerEmail($quote->getBillingAddress()->getEmail())
	 *				->setCustomerIsGuest(true)
	 *				->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
	 *			return $this;
	 *		}
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/Model/Type/Onepage.php#L566
	 * How is @see \Magento\Checkout\Model\Type\Onepage::_prepareGuestQuote() implemented and used?
	 * https://mage2.pro/t/3632
	 * Этот метод вызывается только из @see \Magento\Checkout\Model\Type\Onepage::saveOrder(),
	 * который вызывается только из @see \Magento\Checkout\Controller\Onepage\SaveOrder::execute()
	 * How is @see \Magento\Checkout\Model\Type\Onepage::saveOrder() implemented and used?
	 * https://mage2.pro/t/891
	 * How is @see \Magento\Checkout\Controller\Onepage\SaveOrder used? https://mage2.pro/t/892
	 * Получается, что при каждом сохранении гостевого заказа quote получает email
	 * (если, конечно, email был уже указан покупателем-гостём).
	 * 2.2) @see \Magento\Quote\Model\QuoteManagement::placeOrder()
	 *		if ($quote->getCheckoutMethod() === self::METHOD_GUEST) {
	 *			$quote->setCustomerId(null);
	 *			$quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
	 *			$quote->setCustomerIsGuest(true);
	 *			$quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
	 *		}
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Quote/Model/QuoteManagement.php#L342
	 * Так как для ГОСТЕЙ, то email устанавливается в quote ТОЛЬКО ПРИ РАЗМЕЩЕНИИ ЗАКАЗА,
	 * то ни на странице корзины, ни на странице оформления заказа серверная quote не имеет адреса!
	 *
	 * При этом пошастать по адресам (shipping, billing) для узнавания email бессмысленно:
	 * на момент нахождения покупателя на платёжном шаге оформления заказа
	 * Magento действительно уже сохранила в БД и quote, и оба адреса для quote (shipping, billing),
	 * но у всех этих 3-х записей в БД поле «email» пусто,
	 * хотя покупатель уже указал свой email на шаге «Shipping Address»: https://mage2.pro/t/3633
	 * @todo Проанализировать заказ гостями виртуальных товаров: ведь там нет shipping address!
	 * @used-by \Df\Payment\Operation::customerEmail()
	 * @return string
	 * @throws DFE
	 */
	final function customerEmail() {return df_result_sne($this->oq()->getCustomerEmail());}

	/**
	 * 2016-08-24
	 * @used-by \Df\Payment\Operation::customerName()
	 * @return string
	 */
	final function customerName() {return df_oq_customer_name($this->oq());}

	/**
	 * 2017-11-01
	 * @uses \Magento\Sales\Model\Order::getCustomerTaxvat()
	 * @uses \Magento\Quote\Model\Quote::getCustomerTaxvat()
	 * @used-by \Df\Payment\Operation::customerVAT()
	 * @return string|null
	 */
	final function customerVAT() {return $this->oq()->getCustomerTaxvat();}

	/**
	 * 2017-04-09
	 * @used-by currencyC()
	 * @used-by \Df\Payment\Operation::s()
	 * @return Settings
	 */
	final function s() {return $this->m()->s();}

	/**
	 * 2017-04-08
	 * @uses \Magento\Quote\Model\Quote::getStore()
	 * @uses \Magento\Sales\Model\Order::getStore()
	 * @used-by \Df\Payment\Operation::store()
	 * @return Store
	 */
	final function store() {return $this->oq()->getStore();}
}