<?php
namespace Df\Sales\Setup;
// 2018-05-01
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2018-05-01
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		if ($this->v('3.7.19')) {
			/** 2016-11-04 У нас теперь также есть функция @see df_db_column_add() */
			$this->column('sales_order', self::F__DF, 'text');
		}
	}

	/**
	 * 2018-05-01
	 * @used-by _process()
	 * @used-by df_oi_add()
	 * @used-by df_oi_get()
	 * @var string
	 */
	const F__DF = 'df';
}