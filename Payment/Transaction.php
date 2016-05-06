<?php
namespace Df\Payment;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\Data\OrderInterface;
class Transaction extends \Df\Core\O {
	/**
	 * 2016-03-26
	 * @return Order|DfOrder
	 * @throws LE
	 */
	public function order() {
		if (!isset($this->{__METHOD__})) {
			/** @var Order $result */
			$result = $this->payment()->getOrder();
			if (!$result->getId()) {
				throw new LE(__('The order no longer exists.'));
			}
			/**
			 * 2016-03-26
			 * Очень важно! Иначе order создать свой экземпляр payment:
			 * @used-by \Magento\Sales\Model\Order::getPayment()
			 */
			$result[OrderInterface::PAYMENT] = $this->payment();
			$this->{__METHOD__} = $result;
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
		$this->_prop(self::$P__ID, RM_V_STRING_NE);
	}

	/** @var string */
	private static $P__ID = 'id';

	/**
	 * 2016-05-05
	 * @param string $id
	 * @return $this
	 */
	public static function s($id) {
		/** @var array(string => Transaction) $cache */
		static $cache;
		if (!isset($cache[$id])) {
			$cache[$id] = new self([self::$P__ID => $id]);
		}
		return $cache[$id];
	}
}


