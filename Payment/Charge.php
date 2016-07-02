<?php
namespace Df\Payment;
use Magento\Sales\Model\Order;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
// 2016-07-02
abstract class Charge extends \Df\Core\O {
	/**
	 * 2016-07-02
	 * @return OrderAddress
	 */
	protected function addressBS() {
		if (!isset($this->{__METHOD__})) {
			/** @var OrderAddress $result */
			$result = $this->o()->getBillingAddress();
			$this->{__METHOD__} = $result ? $result : $this->o()->getShippingAddress();
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-02
	 * @return OrderAddress
	 */
	protected function addressSB() {
		if (!isset($this->{__METHOD__})) {
			/** @var OrderAddress $result */
			$result = $this->o()->getShippingAddress();
			$this->{__METHOD__} = $result ? $result : $this->o()->getBillingAddress();
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	protected function amount() {
		return $this->cfg(self::$P__AMOUNT, $this->payment()->getBaseAmountOrdered());
	}

	/** @return string */
	protected function currencyCode() {return $this->o()->getBaseCurrencyCode();}

	/** @return Order */
	protected function o() {return $this->payment()->getOrder();}

	/** @return II|I|OP */
	protected function payment() {return $this[self::$P__PAYMENT];}

	/** @return Store */
	protected function store() {return $this->o()->getStore();}

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