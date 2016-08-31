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
	public function order() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_order_by_payment($this->payment());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-03-26
	 * Ситуация, когда платёж не найден, является нормальной,
	 * потому что к одной учётной записи Stripe может быть привязано несколько магазинов,
	 * и Stripe будет оповещать сразу все магазины о событиях одного из них.
	 * Магазину надо уметь различать свои события и чужие,
	 * и мы делаем это именно по идентификатору транзакции.
	 * @return Payment|DfPayment|null
	 */
	public function payment() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $paymentId */
			$paymentId = df_fetch_one('sales_payment_transaction', 'payment_id', [
				'txn_id' => $this->id()
			]);
			$this->{__METHOD__} = df_n_set(!$paymentId ? null : df_load(Payment::class, $paymentId));
		}
		return df_n_get($this->{__METHOD__});
	}

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
	public static function sp($id) {
		/** @var array(string => Transaction) $cache */
		static $cache;
		if (!isset($cache[$id])) {
			$cache[$id] = new self([self::$P__ID => $id]);
		}
		return $cache[$id];
	}
}


