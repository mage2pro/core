<?php
namespace Df\Sso\Upgrade;
/**
 * 2016-06-04
 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema
 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeSchema
 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema
 */
abstract class Schema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2016-06-04
	 * @used-by \Df\Sso\Upgrade\Schema::_process()
	 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema::fId()
	 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeSchema::fId()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema::fId()
	 * @return string
	 */
	static function fId() {df_abstract(__CLASS__); return '';}

	/**
	 * 2016-12-02
	 * @param string|object $c
	 * @return string
	 */
	static function fIdC($c) {return
		df_con_s(str_replace('_', '\\', df_cts($c)), 'Setup\UpgradeSchema', 'fId')
	;}

	/**
	 * 2016-12-02
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema::_process()
	 */
	protected function _process() {
		if ($this->isInitial()) {
			/**
			 * 2016-06-04
			 * 2017-08-01 An Amazon ID can be long, e.g.: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
			 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema
			 */
			$this->columnCE(static::fId(), 'varchar(255) DEFAULT NULL');
		}
	}

	/**
	 * 2016-06-05
	 * 2016-08-22
	 * Помимо добавления поля в таблицу «customer_entity» надо ещё добавить атрибут
	 * что мы делаем методом @see \Df\Sso\Upgrade\Data::attribute()
	 * иначе данные не будут сохраняться: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Eav/Model/Entity/AbstractEntity.php#L1262-L1265
	 * @used-by _process()
	 * @used-by \Dfe\FacebookLogin\Setup\UpgradeSchema::_process()
	 * @param string $name
	 * @param string $definition
	 * 2016-11-04 У нас теперь также есть функция @see df_db_column_add()
	 */
	final protected function columnCE($name, $definition) {$this->column(
		'customer_entity', $name, $definition
	);}
}