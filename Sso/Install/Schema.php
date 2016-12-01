<?php
namespace Df\Sso\Install;
use Magento\Framework\DB\Adapter\Pdo\Mysql as Adapter;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
abstract class Schema implements InstallSchemaInterface {
	/**
	 * 2016-06-04
	 * @used-by \Df\Sso\Install\Schema::install()
	 * @return string
	 */
	abstract public function fId();

	/**
	 * 2016-06-04
	 * @used-by \Df\Sso\Install\Schema::install()
	 * @return string
	 */
	abstract public function fName();

	/**
	 * 2015-10-06
	 * @override
	 * @see InstallSchemaInterface::install()
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	final public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$this->_conn = $setup->getConnection();
		// 2015-10-10
		// Не хочу проблем из-за идиотов с длинными именами, поэтому пусть будет 255.
		$this->column($this->fName(), 'varchar(255) DEFAULT NULL');
		// 2016-06-04
		// Идентификатор может быть длинным, например «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
		$this->column($this->fId(), 'varchar(255) DEFAULT NULL');
		$this->_install();
		$setup->endSetup();
	}

	/**
	 * 2016-06-05
	 * 2016-08-21
	 * Этот метод намеренно не объявлен абстрактным, потому что, например,
	 * потомок @see \Dfe\AmazonLogin\Setup\InstallSchema не перекрывает его.
	 * @used-by \Df\Sso\Install\Schema::install()
	 * @see \Dfe\FacebookLogin\Setup\InstallSchema::_install()
	 * @return void
	 */
	protected function _install() {}

	/**
	 * 2016-06-05
	 * 2016-08-22
	 * Помимо добавления поля в таблицу «customer_entity» надо ещё добавить атрибут
	 * что мы делаем методом @see \Df\Sso\Install\Data::attribute()
	 * иначе данные не будут сохраняться: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Eav/Model/Entity/AbstractEntity.php#L1262-L1265
	 * @param string $name
	 * @param string $definition
	 * @return void
	 */
	final protected function column($name, $definition) {
		/**
		 * 2016-11-04
		 * У нас теперь также есть функция @see df_db_column_add()
		 */
		$this->_conn->addColumn($this->table(), $name, $definition);
	}

	/**
	 * 2016-06-05
	 * @return string
	 */
	private function table() {return df_table('customer_entity');}

	/**
	 * 2016-06-05
	 * @used-by \Df\Sso\Install\Schema::conn()
	 * @used-by \Df\Sso\Install\Schema::install()
	 * @var Adapter|IAdapter
	 */
	private $_conn;
}