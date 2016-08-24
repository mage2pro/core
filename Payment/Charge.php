<?php
namespace Df\Payment;
use Magento\Customer\Model\Customer;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
// 2016-07-02
abstract class Charge extends \Df\Core\O {
	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressSB()
	 * @return OA
	 */
	protected function addressBS() {return $this->addressMixed($bs = true);}

	/**
	 * 2016-07-02
	 * @see \Df\Payment\Charge::addressBS()
	 * @return OA
	 */
	protected function addressSB() {return $this->addressMixed($bs = false);}

	/**
	 * 2016-08-17
	 * Раньше транзакции проводились в учётной валюте системы.
	 * Отныне они проводятся в валюте заказа, что намного разумнее.
	 * @return float
	 */
	protected function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this[self::$P__AMOUNT] ?: $this->payment()->getAmountOrdered();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-17
	 * Раньше транзакции проводились в учётной валюте системы.
	 * Отныне они проводятся в валюте заказа, что намного разумнее.
	 * @return string
	 */
	protected function currencyCode() {return $this->o()->getOrderCurrencyCode();}

	/**
	 * 2016-08-22
	 * @return Customer|null
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
	 * 2016-08-08
	 * @see \Df\Payment\Method::iia()
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	protected function iia(...$keys) {return df_payment_iia($this->payment(), $keys);}

	/** @return Order */
	protected function o() {return $this->payment()->getOrder();}

	/** @return II|I|OP */
	protected function payment() {return $this[self::$P__PAYMENT];}

	/** @return Store */
	protected function store() {return $this->o()->getStore();}

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
			$aa = df_clean([$this->o()->getBillingAddress(), $this->o()->getShippingAddress()]);
			$aa = $bs ? $aa : array_reverse($aa);
			$this->{__METHOD__}[$bs] = df_create(OA::class,
				df_clean(df_first($aa)->getData()) + df_last($aa)->getData()
			);
		}
		return $this->{__METHOD__}[$bs];
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

	/**
	 * 2016-07-02
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__AMOUNT, RM_V_FLOAT, false)
			->_prop(self::$P__PAYMENT, II::class)
		;
	}

	/** @var string */
	protected static $P__AMOUNT = 'amount';
	/** @var string */
	protected static $P__PAYMENT = 'payment';
}