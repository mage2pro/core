<?php
namespace Df\Payment;
use Df\Customer\Model\Customer as DFCustomer;
use Df\Customer\Model\Gender as G;
use Magento\Customer\Model\Customer as C;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
use Zend_Date as ZD;
/**
 * 2016-07-02
 * @see \Df\GingerPaymentsBase\Charge
 * @see \Df\Payment\Charge\WithToken
 * @see \Df\PaypalClone\Charge
 */
abstract class Charge extends Operation {
	/**
	 * 2016-08-26
	 * Несмотря на то, что опция @see \Df\Payment\Settings::requireBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при requireBillingAddress = false платёжный адрес является вырожденным:
	 * он содержит только email покупателя.
	 * @return OA
	 */
	final protected function addressB() {return $this->o()->getBillingAddress();}

	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressSB()
	 * @return OA
	 */
	final protected function addressBS() {return $this->addressMixed($bs = true);}

	/**
	 * 2016-08-26
	 * @return OA|null
	 */
	final protected function addressS() {return $this->o()->getShippingAddress();}

	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressBS()
	 * @return OA
	 */
	final protected function addressSB() {return $this->addressMixed($bs = false);}

	/**
	 * 2016-08-30
	 * @override
	 * @see \Df\Payment\Operation::amountFromDocument()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float
	 */
	final protected function amountFromDocument() {return $this->payment()->getAmountOrdered();}

	/**
	 * 2016-08-22
	 * @return C|null
	 */
	final protected function c() {return dfc($this, function() {/** @var int|null $id $id */return
		!($id = $this->o()->getCustomerId()) ? null : df_customer($id)
	;});}

	/**
	 * 2016-08-27
	 * @param string $path [optional]
	 * @return string
	 */
	final protected function callback($path = 'confirm') {return df_url_callback($this->route($path));}

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
	 * @used-by \Dfe\Spryng\Charge::pCustomer()
	 * @param string $format [optional]
	 * @return string|null
	 */
	final protected function customerDobS($format = 'Y-MM-dd') {return
		!($zd = $this->customerDob()) ? null : $zd->toString($format)
	;}

	/**
	 * 2016-08-26
	 * @return string
	 */
	final protected function customerEmail() {return $this->o()->getCustomerEmail();}

	/**
	 * 2017-02-18
	 * @used-by \Dfe\Spryng\Charge::pCustomer()
	 * @param string $m
	 * @param string $f
	 * @return string|null
	 */
	final protected function customerGender($m, $f) {return
		dfa([G::MALE => $m, G::FEMALE => $f], $this->o()->getCustomerGender())
	;}

	/**
	 * 2016-08-24
	 * @return string
	 */
	final protected function customerName() {return dfc($this, function() {return
		df_order_customer_name($this->o())
	;});}

	/**
	 * 2016-08-26
	 * @return string|null
	 */
	final protected function customerNameF() {return df_first($this->customerNameA());}

	/**
	 * 2016-08-26
	 * @return string|null
	 */
	final protected function customerNameL() {return df_last($this->customerNameA());}

	/**
	 * 2016-08-26
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\SecurePay\Charge::params()
	 * @used-by \Dfe\Spryng\Charge::pCharge()
	 * @return string
	 */
	final protected function customerIp() {return $this->o()->getRemoteIp();}

	/**
	 * 2016-08-27
	 * @return string
	 */
	final protected function customerReturn() {return df_url_frontend($this->route('customerReturn'));}

	/**
	 * 2016-08-27
	 * Этот метод решает 2 проблемы, возникающие при работе на localhost:
	 * 1) Некоторые способы оплаты (SecurePay) вообще не позволяют указывать локальные адреса.
	 * 2) Некоторые способы оплаты (allPay) допускают локальный адрес возврата,
	 * но для тестирования его нам использовать нежелательно,
	 * потому что сначала вручную сэмулировать и обработать callback.
	 * @return string
	 */
	final protected function customerReturnRemote() {return $this->callback('customerReturn');}

	/**
	 * 2016-09-07
	 * 2017-02-02
	 * Отныне метод упорядочивает позиции заказа по имени.
	 * Ведь этот метод используется только для передачи позиций заказа в платежные системы,
	 * а там они отображаются покупателю и администратору,
	 * и удобно, чтобы они были упорядочены по имени.
	 * @used-by \Dfe\CheckoutCom\Charge::setProducts()
	 * @used-by \Dfe\AllPay\Charge::productUrls()
	 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
	 * @param \Closure $f
	 * @param string|null $locale [optional]
	 * @return mixed[]
	 */
	final protected function oiLeafs(\Closure $f, $locale = null) {return
		df_oi_leafs($this->o(), $f, $locale)
	;}

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::callback()
	 * @used-by \Df\PaypalClone\Charge::customerReturn()
	 * @param string $path [optional]
	 * @return string
	 */
	final protected function route($path = ''){return df_cc_path(df_route($this), $path);}

	/**
	 * 2016-07-04
	 * @used-by \Df\StripeClone\Charge::request()
	 * @param string $s
	 * @return string
	 */
	final protected function text($s) {return df_var($s, $this->meta());}

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
			if ($this->getData('addresses') == null) {
				$this->setData('addresses', $this->getAddressesCollection()->getItems());
			}
			return $this->getData('addresses');
	 * Как видно, метод необязательно получает адреса из базы данных:
	 * для анонимных покупателей (или ранее покупавших, но указавшим в этот раз новый адрес),
	 * адреса берутся из поля «addresses».
	 * А содержимое этого поля устанавливается методом @see \Magento\Sales\Model\Order::addAddress()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L1238-L1250
	 *
	 * @param bool $bs
	 * @return OA
	 */
	private function addressMixed($bs) {return dfc($this, function($bs) {
		/** @var OA[] $aa */
		$aa = df_clean([$this->addressB(), $this->addressS()]);
		$aa = $bs ? $aa : array_reverse($aa);
		/** @var OA $result */
		$result = df_create(OA::class, df_clean(df_first($aa)->getData()) + df_last($aa)->getData());
		/**
		 * 2016-08-24
		 * Сам класс @see \Magento\Sales\Model\Order\Address никак order не использует.
		 * Однако пользователи класса могут ожидать работоспособность метода
		 * @see \Magento\Sales\Model\Order\Address::getOrder()
		 * В частности, этого ожидает метод @see \Dfe\TwoCheckout\Address::build()
		 */
		$result->setOrder($this->o());
		return $result;
	}, func_get_args());}

	/**
	 * 2016-08-26
	 * @return array(string|null)
	 */
	private function customerNameA() {return dfc($this, function() {
		/** @var array(string|null) $result */
		if ($this->o()->getCustomerFirstname()) {
			$result = [$this->o()->getCustomerFirstname(), $this->o()->getCustomerLastname()];
		}
		else {
			/** @var C|DFCustomer $customer */
			$customer = $this->o()->getCustomer();
			if ($customer && $customer->getFirstname()) {
				$result = [$customer->getFirstname(), $customer->getLastname()];
			}
			else {
				/** @var OA $ba */
				$ba = $this->addressB();
				if ($ba->getFirstname()) {
					$result = [$ba->getFirstname(), $ba->getLastname()];
				}
				else {
					/** @var OA|null $ba */
					$sa = $this->addressS();
					if ($sa && $sa->getFirstname()) {
						$result = [$sa->getFirstname(), $sa->getLastname()];
					}
					else {
						$result = [null, null];
					}
				}
			}
		}
		return $result;
	});}

	/**
	 * 2016-05-06
	 * @return array(string => string)
	 */
	private function meta() {return dfc($this, function() {return
		Metadata::vars($this->store(), $this->o())
	;});}
}