<?php
namespace Df\Payment;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
class Transaction extends \Df\Core\O {
	/**
	 * 2016-03-26
	 * @return Order|DfOrder
	 * @throws LE
	 */
	function order() {return dfc($this, function() {return df_order_by_payment($this->payment());});}

	/**
	 * 2016-03-26
	 * Ситуация, когда платёж не найден, является нормальной,
	 * потому что к одной учётной записи Stripe может быть привязано несколько магазинов,
	 * и Stripe будет оповещать сразу все магазины о событиях одного из них.
	 * Магазину надо уметь различать свои события и чужие,
	 * и мы делаем это именно по идентификатору транзакции.
	 * @return Payment|DfPayment|null
	 */
	function payment() {return dfc($this, function() {
		/** @var int|null $id */
		$id = df_fetch_one('sales_payment_transaction', 'payment_id', ['txn_id' => $this->id()]);
		return !$id ? null : df_load(Payment::class, $id);
	});}

	/**
	 * 2016-05-05
	 * Transaction ID.
	 * @return string
	 */
	private function id() {return $this[self::$P__ID];}

	/**
	 * 2016-05-05
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ID, DF_V_STRING_NE);
	}

	/** @var string */
	private static $P__ID = 'id';

	/**
	 * 2016-05-05
	 * @param string $id
	 * @return $this
	 */
	static function sp($id) {return dfcf(function($id) {return
		new self([self::$P__ID => $id])
	;}, func_get_args());}
}