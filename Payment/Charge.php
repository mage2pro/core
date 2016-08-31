<?php
namespace Df\Payment;
use Df\Customer\Model\Customer as DFC;
use Magento\Customer\Model\Customer as C;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
// 2016-07-02
abstract class Charge extends Operation {
	/**
	 * 2016-08-26
	 * Несмотря на то, что опция @see \Df\Payment\Settings::askForBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при askForBillingAddress = false платёжный адрес является вырожденным:
	 * он содержит только email покупателя.
	 * @return OA
	 */
	protected function addressB() {return $this->o()->getBillingAddress();}

	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressSB()
	 * @return OA
	 */
	protected function addressBS() {return $this->addressMixed($bs = true);}

	/**
	 * 2016-08-26
	 * @return OA|null
	 */
	protected function addressS() {return $this->o()->getShippingAddress();}

	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressBS()
	 * @return OA
	 */
	protected function addressSB() {return $this->addressMixed($bs = false);}

	/**
	 * 2016-08-30
	 * @override
	 * @see \Df\Payment\Operation::amountFromDocument()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float
	 */
	protected function amountFromDocument() {return $this->payment()->getAmountOrdered();}

	/**
	 * 2016-08-17
	 * Раньше транзакции проводились в учётной валюте системы.
	 * Отныне они проводятся в валюте заказа, что намного разумнее.
	 * @return string
	 */
	protected function currencyCode() {return $this->o()->getOrderCurrencyCode();}

	/**
	 * 2016-08-22
	 * @return C|null
	 */
	protected function customer() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $id $id */
			$id = $this->o()->getCustomerId();
			$this->{__METHOD__} = df_n_set(!$id ? null : df_customer_get($id));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-08-26
	 * @return string
	 */
	protected function customerEmail() {return $this->o()->getCustomerEmail();}

	/**
	 * 2016-08-24
	 * @return string
	 */
	protected function customerName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_order_customer_name($this->o());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-26
	 * @return string|null
	 */
	protected function customerNameF() {return df_first($this->customerNameA());}

	/**
	 * 2016-08-26
	 * @return string|null
	 */
	protected function customerNameL() {return df_last($this->customerNameA());}

	/**
	 * 2016-08-26
	 * @return string
	 */
	protected function customerIp() {return $this->o()->getRemoteIp();}

	/**
	 * 2016-07-04
	 * @param string $s
	 * @return string
	 */
	protected function text($s) {return df_var($s, $this->meta());}

	/**
	 * 2016-08-24
	 * Несмотря на то, что опция @see \Df\Payment\Settings::askForBillingAddress()
	 * стала общей для всех моих платёжных модулей,
	 * платёжный адрес у заказа всегда присутствует,
	 * просто при askForBillingAddress = false платёжный адрес является вырожденным:
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
	private function addressMixed($bs) {
		if (!isset($this->{__METHOD__}[$bs])) {
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
			$this->{__METHOD__}[$bs] = $result;
		}
		return $this->{__METHOD__}[$bs];
	}

	/**
	 * 2016-08-26
	 * @return array(string|null)
	 */
	private function customerNameA() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(mixed => mixed) $result */
			/** @var array(string|null) $result */
			if ($this->o()->getCustomerFirstname()) {
				$result = [$this->o()->getCustomerFirstname(), $this->o()->getCustomerLastname()];
			}
			else {
				/** @var C|DFC $customer */
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
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-06
	 * @return array(string => string)
	 */
	private function meta() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Metadata::vars($this->store(), $this->o());
		}
		return $this->{__METHOD__};
	}
}