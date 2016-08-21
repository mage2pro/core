<?php
namespace Df\Framework\SetupA;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Adapter\Pdo\Mysql as Adapter;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
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
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}

	/**
	 * 2016-08-14
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext $context
	 * @return void
	 */
	public function upgrade(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}

	/** @return Adapter|IAdapter */
	protected function c() {return $this->s()->getConnection();}

	/** @return Setup|SchemaSetupInterface */
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
	 * 2016-08-21
	 * Возвращает true, если установленная ранее версия модуля меньше указанной.
	 * @param string $version
	 * @return bool
	 */
	protected function v($version) {
		return -1 === version_compare($this->_context->getVersion(), $version);
	}

	/**
	 * 2016-08-14
	 * @param SchemaSetupInterface $setup
	 * @param IModuleContext|ModuleContext $context
	 * @return void
	 */
	private function process(SchemaSetupInterface $setup, IModuleContext $context) {
		$setup->startSetup();
		$this->_context = $context;
		$this->_setup = $setup;
		$this->_process();
		$setup->endSetup();
	}

	/**
	 * 2016-08-21
	 * @used-by \Df\Framework\SetupA\Schema::process()
	 * @used-by \Df\Framework\SetupA\Schema::v()
	 * @var IModuleContext|ModuleContext
	 */
	private $_context;

	/**
	 * @used-by \Df\Framework\SetupA\Schema::process()
	 * @used-by \Df\Framework\SetupA\Schema::s()
	 * @var Setup|SchemaSetupInterface
	 */
	private $_setup;
}