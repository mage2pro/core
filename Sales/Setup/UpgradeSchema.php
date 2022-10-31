<?php
namespace Df\Sales\Setup;
use Df\Framework\DB\ColumnType as T;
# 2018-05-01
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2018-05-01
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process():void {
		if ($this->v('3.7.19')) {
			/** 2016-11-04 У нас теперь также есть функция @see df_db_column_add() */
			df_dbc_o(self::F__DF, T::textLong('Mage2.PRO'));
		}
	}

	/**
	 * 2018-05-01
	 * @used-by self::_process()
	 * @used-by df_oi_add()
	 * @used-by df_oi_get()
	 * @var string
	 */
	const F__DF = 'df';
}