<?php
use Magento\Sales\Model\Order as O;
/**
 * 2016-03-15
 * @param int|null $id
 * @return bool
 */
function df_customer_is_new($id) {
	/** @var array(int => bool) $cache */
	static $cache;
	if (!isset($cache[$id])) {
		/** @var bool $result */
		$result = !$id;
		if ($id) {
			/** @var \Magento\Framework\DB\Select $select */
			$select = df_select()->from(df_table('sales_order'), 'COUNT(*)')
				->where('? = customer_id', $id)
				->where('state IN (?)', [O::STATE_COMPLETE, O::STATE_PROCESSING])
			;
			$result = !df_conn()->fetchOne($select);
		}
		$cache[$id] = $result;
	}
	return $cache[$id];
}

