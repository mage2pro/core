<?php
namespace Df\Payment;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
// 2016-08-30
abstract class Operation extends \Df\Core\O {
	/**
	 * 2016-08-30
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float
	 */
	abstract protected function amountFromDocument();

	/**
	 * 2016-08-17
	 * Раньше транзакции проводились в учётной валюте системы.
	 * Отныне они проводятся в валюте заказа, что намного разумнее.
	 * @return float
	 */
	protected function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this[self::$P__AMOUNT] ?: $this->amountFromDocument();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-08
	 * @see \Df\Payment\Method::iia()
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	protected function iia(...$keys) {return dfp_iia($this->payment(), $keys);}

	/**
	 * 2016-08-31
	 * @override
	 * @see \Df\Payment\Operation::method()
	 * @return Method
	 */
	protected function m() {return $this[self::$P__METHOD];}

	/** @return Order */
	protected function o() {return $this->payment()->getOrder();}

	/** @return II|I|OP */
	protected function payment() {return $this->m()->getInfoInstance();}

	/** @return Store */
	protected function store() {return $this->o()->getStore();}

	/**
	 * 2016-08-30
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__AMOUNT, DF_V_FLOAT, false)
			->_prop(self::$P__METHOD, Method::class)
		;
	}
	/** @var string */
	protected static $P__AMOUNT = 'amount';
	/** @var string */
	protected static $P__METHOD = 'method';
}