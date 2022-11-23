<?php
namespace Df\Framework\DB;
use Magento\Framework\DB\Ddl\Table as T;
/**
 * 2019-06-05
 * 1) @see \Magento\Sales\Setup\SalesSetup::_getAttributeColumnDefinition()
 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Sales/Setup/SalesSetup.php#L197-L244
 * 2) @see \Magento\Framework\DB\Adapter\Pdo\Mysql::$_ddlColumnTypes:
 * 	protected $_ddlColumnTypes      = [
 *		Table::TYPE_BOOLEAN       => 'bool',
 *		Table::TYPE_SMALLINT      => 'smallint',
 *		Table::TYPE_INTEGER       => 'int',
 *		Table::TYPE_BIGINT        => 'bigint',
 *		Table::TYPE_FLOAT         => 'float',
 *		Table::TYPE_DECIMAL       => 'decimal',
 *		Table::TYPE_NUMERIC       => 'decimal',
 *		Table::TYPE_DATE          => 'date',
 *		Table::TYPE_TIMESTAMP     => 'timestamp',
 *		Table::TYPE_DATETIME      => 'datetime',
 *		Table::TYPE_TEXT          => 'text',
 *		Table::TYPE_BLOB          => 'blob',
 *		Table::TYPE_VARBINARY     => 'blob',
 *	];
 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L134-L153
 */
final class ColumnType {
	/**
	 * 2019-06-05
	 * Magento does not support `tinyint` explicitly:
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::$_ddlColumnTypes:
	 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L134-L153
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::_getColumnDefinition():
	 *	if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
	 *		throw new \Zend_Db_Exception('Invalid column definition data');
	 *	}
	 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L2438-L2440
	 * That is why I use `boolean`, which is translated to `tinyint(1)` automatically.
	 * @used-by \KingPalm\B2B\Setup\UpgradeSchema::_process()
	 * @param array(string => string|int) $o [optional]
	 * @return array(string => string|int)
	 */
	static function bool(string $c, array $o = []):array {return $o + [
		'comment' => $c, 'default' => 0, 'length' => 1, 'nullable' => false, 'type' => 'boolean'
	];}

	/**
	 * 2019-06-05
	 * @used-by df_db_column_add()
	 * @used-by self::textLong()
	 * @param array(string => string|int) $o [optional]
	 * @return array(string => string|int)
	 */
	static function text(string $c, array $o = []):array {return $o + [
		'comment' => $c, 'length' => 255, 'nullable' => true, 'type' => T::TYPE_TEXT
	];}

	/**
	 * 2019-06-05
	 * @used-by \Df\Customer\Setup\UpgradeSchema::_process()
	 * @used-by \Df\Sales\Setup\UpgradeSchema::_process()
	 * @used-by \Dfe\Markdown\Setup\UpgradeSchema::_process()
	 * @param string $c
	 * @return array(string => string|int)
	 */
	static function textLong($c):array {return self::text($c, ['length' => 65536]);}
}