<?php
namespace Df\Customer\Setup;
use Df\Framework\DB\ColumnType as T;
# 2016-08-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2016-08-21
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		if ($this->v('1.7.2')) {
			/** 2016-11-04 У нас теперь также есть функция @see df_db_column_add() */
			df_dbc_c(self::F__DF, T::textLong('Mage2.PRO'));
		}
	}

	/**
	 * 2016-08-21
	 * @used-by df_ci_add()
	 * @used-by df_ci_get()
	 * @used-by self::_process()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @used-by \Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
	 * @var string
	 */
	const F__DF = 'df';
}