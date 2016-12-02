<?php
namespace Df\Framework;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Adapter\Pdo\Mysql as Adapter;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Module\Setup;
use Magento\Setup\Model\ModuleContext;
abstract class Install {
	/**
	 * @used-by InstallSchema::process()
	 * @return void
	 */
	abstract protected function _process();

	/** @return Adapter|IAdapter */
	final protected function c() {return $this->s()->getConnection();}

	/**
	 * 2016-12-02
	 * Перекрывайте этот метод, если в изначальной версии модуля отсутствовал этот класс.
	 * В таком случае укажите здесь ту версию модуля, в которой был добавлен этот класс.
	 * @used-by isInitial()
	 * @see \Dfe\BlackbaudNetCommunity\Setup\InstallSchema::initial()
	 * @return string
	 */
	protected function initial() {return '0.0.1';}

	/**
	 * 2016-12-02
	 * @used-by \Df\Sso\Install\Schema::_process()
	 * @return bool
	 */
	final protected function isInitial() {return $this->v($this->initial());}

	/**
	 * 2016-08-14
	 * @param Setup $setup
	 * @param IModuleContext|ModuleContext $context
	 * @return void
	 */
	final protected function process(Setup $setup, IModuleContext $context) {
		$setup->startSetup();
		$this->_context = $context;
		$this->_setup = $setup;
		$this->_process();
		$setup->endSetup();
	}

	/** @return Setup */
	final protected function s() {return $this->_setup;}

	/**
	 * 2016-08-14
	 * @param string $class [optional]
	 * @return EavSetup
	 */
	final protected function sEav($class = EavSetup::class) {return dfc($this, function($class) {return
		df_create($class, ['setup' => $this->s()])
	;}, [$class]);}

	/**
	 * @param string|array $tableName
	 * @return string
	 */
	final protected function t($tableName) {return $this->s()->getTable($tableName);}

	/**
	 * 2016-08-21
	 * Возвращает true, если установленная ранее версия модуля меньше указанной.
	 * @param string $version
	 * @return bool
	 */
	final protected function v($version) {return
		-1 === version_compare($this->_context->getVersion(), $version)
	;}

	/**
	 * 2016-12-02
	 * @used-by process()
	 * @used-by v()
	 * @var IModuleContext|ModuleContext
	 */
	private $_context;

	/**
	 * 2016-12-02
	 * @used-by process()
	 * @used-by s()
	 * @var Setup
	 */
	private $_setup;
}