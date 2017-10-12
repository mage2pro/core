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
	 * @used-by addressMixed
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
	 * @used-by addressMixed()
	 * @used-by \Df\Payment\Operation::addressB()
	 * @return OA|QA
	 */
	final function addressB() {return $this->oq()->getBillingAddress();}

	/**
	 * 2016-07-02
	 * @see addressSB()
	 * @used-by \Df\Payment\Operation::addressBS()
	 * @return OA|QA
	 */
	final function addressBS() {return $this->addressMixed($bs = true);}

	/**
	 * 2017-04-10
	 * Если адрес доставки отсутствует, то:
	 * 1) @uses \Magento\Sales\Model\Order::getShippingAddress() возвращает null
	 * 1) @uses \Magento\Quote\Model\Quote::getShippingAddress() возвращает пустой объект
	 * @used-by addressMixed()
	 * @used-by \Df\Payment\Operation::addressS()
	 * @return OA|QA|null
	 */
	final function addressS() {return $this->oq()->getShippingAddress();}

	/**
	 * 2016-07-02
	 * @see addressBS()
	 * @used-by \Df\Payment\Operation::addressSB()
	 * @return OA|QA
	 */
	final function addressSB() {return $this->addressMixed($bs = false);}

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

	/**
	 * 2016-08-24
	 * Несмотря на то, что опция @see \Df\Payment\Settings::requireBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при requireBillingAddress = false платёжный адрес является вырожденным:
	 * он содержит только email покупателя.
	 *
	 * Только что проверил, как метод работает для анонимных покупателей.
	 * Оказывается, если аноничный покупатель при оформлении заказа указал адреса,
	 * то эти адреса в данном методе уже будут доступны как посредством
	 * @see \Magento\Sales\Model\Order::getAddresses()
	 * так и, соответственно, посредством @uses \Magento\Sales\Model\Order::getBillingAddress()
	 * и @uses \Magento\Sales\Model\Order::getShippingAddress()
	 * Так происходит в связи с особенностью реализации метода
	 * @see \Magento\Sales\Model\Order::getAddresses()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L1957-L1969
	 *		if ($this->getData('addresses') == null) {
	 *			$this->setData('addresses', $this->getAddressesCollection()->getItems());
	 *		}
	 *		return $this->getData('addresses');
	 * Как видно, метод необязательно получает адреса из базы данных:
	 * для анонимных покупателей (или ранее покупавших, но указавшим в этот раз новый адрес),
	 * адреса берутся из поля «addresses».
	 * А содержимое этого поля устанавливается методом @see \Magento\Sales\Model\Order::addAddress()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L1238-L1250
	 *
	 * @used-by addressBS()
	 * @used-by addressSB()
	 * @param bool $bs
	 * @return OA|QA
	 */
	private function addressMixed($bs) {return dfc($this, function($bs) {
		/** @var OA|QA[] $aa */
		$aa = df_clean([$this->addressB(), $this->addressS()]);
		if (!$bs) {
			$aa = array_reverse($aa);
		}
		/** @var bool $isO */
		$isO = df_is_o($this->oq());
		/** @var OA|QA $result */
		$result = df_new_omd($isO ? OA::class : QA::class, df_clean(
			df_first($aa)->getData()) + df_last($aa)->getData()
		);
		/**
		 * 2016-08-24
		 * Сам класс @see \Magento\Sales\Model\Order\Address никак order не использует.
		 * Однако пользователи класса могут ожидать работоспособность метода
		 * @see \Magento\Sales\Model\Order\Address::getOrder()
		 * В частности, этого ожидает метод @see \Dfe\TwoCheckout\Address::build()
		 */
		$isO ? $result->setOrder($this->oq()) : $result->setQuote($this->oq());
		return $result;
	}, func_get_args());}
}