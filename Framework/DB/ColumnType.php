<?php
namespace Df\Framework\DB;
use Magento\Framework\DB\Ddl\Table as T;
// 2019-06-05
final class ColumnType {
	/**
	 * 2019-06-05
	 * @used-by df_db_column_add()
	 * @used-by textLong()
	 * @param string $c
	 * @param array(string => string|int) $o [optional]
	 * @return array(string => string|int)
	 */
	static function text($c, array $o = []) {return $o + [
		'comment' => $c, 'length' => 255, 'nullable' => true, 'type' => T::TYPE_TEXT
	];}

	/**
	 * 2019-06-05
	 * @used-by \Df\Customer\Setup\UpgradeSchema::_process()
	 * @used-by \Df\Sales\Setup\UpgradeSchema::_process()
	 * @param string $c
	 * @return array(string => string|int)
	 */
	static function textLong($c) {return self::text($c, ['length' => 65536]);}
}