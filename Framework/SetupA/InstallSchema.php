<?php
namespace Df\Framework\SetupA;
use Magento\Framework\DB\Adapter;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
abstract class InstallSchema implements InstallSchemaInterface {
	/**
	 * @used-by InstallSchema::install()
	 * @return void
	 */
	abstract protected function _install();

	/**
	 * 2015-10-23
	 * @override
	 * @see InstallSchemaInterface::install()
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$this->_setup = $setup;
		$this->_install();
		$setup->endSetup();
	}

	/** @return Adapter\Pdo\Mysql|Adapter\AdapterInterface */
	protected function c() {return $this->s()->getConnection();}

	/** @return \Magento\Setup\Module\Setup|SchemaSetupInterface */
	protected function s() {return $this->_setup;}

	/**
	 * @param string|array $tableName
	 * @return string
	 */
	protected function t($tableName) {return $this->s()->getTable($tableName);}

	/**
	 * @used-by InstallSchema::install()
	 * @used-by InstallSchema::setup()
	 * @var SchemaSetupInterface
	 */
	private $_setup;
}