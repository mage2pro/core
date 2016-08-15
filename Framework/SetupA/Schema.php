<?php
namespace Df\Framework\SetupA;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Adapter;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
abstract class Schema implements InstallSchemaInterface, UpgradeSchemaInterface {
	/**
	 * @used-by InstallSchema::process()
	 * @return void
	 */
	abstract protected function _process();

	/**
	 * 2015-10-23
	 * @override
	 * @see InstallSchemaInterface::install()
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$this->process($setup, $context);
	}

	/**
	 * 2016-08-14
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$this->process($setup, $context);
	}

	/** @return Adapter\Pdo\Mysql|Adapter\AdapterInterface */
	protected function c() {return $this->s()->getConnection();}

	/** @return \Magento\Setup\Module\Setup|SchemaSetupInterface */
	protected function s() {return $this->_setup;}

	/**
	 * 2016-08-14
	 * @param string $class [optional]
	 * @return EavSetup
	 */
	protected function sEav($class = EavSetup::class) {
		df_param_string_not_empty($class, 0);
		if (!isset($this->{__METHOD__}[$class])) {
			$this->{__METHOD__}[$class] = df_create($class, ['setup' => $this->s()]);
		}
		return $this->{__METHOD__}[$class];
	}

	/**
	 * @param string|array $tableName
	 * @return string
	 */
	protected function t($tableName) {return $this->s()->getTable($tableName);}

	/**
	 * 2016-08-14
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	private function process(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$this->_setup = $setup;
		$this->_process();
		$setup->endSetup();
	}

	/**
	 * @used-by InstallSchema::install()
	 * @used-by InstallSchema::setup()
	 * @var SchemaSetupInterface
	 */
	private $_setup;
}