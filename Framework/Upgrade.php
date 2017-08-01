<?php
namespace Df\Framework;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\DB\Adapter\Pdo\Mysql as Adapter;
use Magento\Framework\Module\Setup;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Setup\Model\ModuleContext;
/**
 * 2016-12-08
 * Нам достаточно реализовывать только классы Upgrade,
 * а классы Install можно не делать, потому что при обновлении ядро вызывает только классы Upgrade,
 * а при первичной установке — оба класса (Install и Upgrade),
 * причём оба являются факультативными:
 *		$installer = $this->getSchemaDataHandler($moduleName, $installType);
 *		if ($installer) {
 *			$this->log->logInline("Installing $type... ");
 *			$installer->install($setup, $moduleContextList[$moduleName]);
 *		}
 *		$upgrader = $this->getSchemaDataHandler($moduleName, $upgradeType);
 *		if ($upgrader) {
 *			$this->log->logInline("Upgrading $type... ");
 *			$upgrader->upgrade($setup, $moduleContextList[$moduleName]);
 *		}
 * https://github.com/magento/magento2/blob/2.1.2/setup/src/Magento/Setup/Model/Installer.php#L840-L850
 * @see \Df\Framework\Upgrade\Data
 * @see \Df\Framework\Upgrade\Schema
 */
abstract class Upgrade {
	/**
	 * 2016-12-08
	 * @used-by process()
	 * @see \Df\Customer\Setup\UpgradeSchema::_process()
	 * @see \Df\Sso\Upgrade\Data::_process()
	 * @see \Df\Sso\Upgrade\Schema::_process()
	 * @see \Dfe\Markdown\Setup\UpgradeSchema::_process()
	 */
	abstract protected function _process();

	/**
	 * 2016-12-08
	 * @used-by column()
	 * @used-by \Dfe\Markdown\Setup\UpgradeSchema::createTableEav()
	 * @return Adapter|IAdapter
	 */
	final protected function c() {return $this->s()->getConnection();}

	/**
	 * 2017-08-01
	 * 2016-11-04 У нас теперь также есть функция @see df_db_column_add()
	 * @used-by \Df\Customer\Setup\UpgradeSchema::_process()
	 * @used-by \Df\Sso\Upgrade\Schema::columnCE()
	 * @used-by \Dfe\Markdown\Setup\UpgradeSchema::_process()
	 * @param string $table
	 * @param string $name
	 * @param string $definition
	 */
	final protected function column($table, $name, $definition) {$this->c()->addColumn(
		df_table($table), $name, $definition
	);}

	/**
	 * 2016-12-02
	 * 2017-08-01
	 * Override this method if the initially installed module has not included this class.
	 * In this case provide here the number of the version when the class has been added.
	 * @used-by isInitial()
	 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeSchema::initial()
	 * @return string
	 */
	protected function initial() {return '0.0.1';}

	/**
	 * 2016-12-02
	 * @used-by \Df\Sso\Upgrade\Schema::_process()
	 * @return bool
	 */
	final protected function isInitial() {return $this->v($this->initial());}

	/**
	 * 2016-08-14
	 * @param Setup $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	final protected function process(Setup $setup, IModuleContext $context) {
		$setup->startSetup();
		$this->_context = $context;
		$this->_setup = $setup;
		$this->_process();
		$setup->endSetup();
	}

	/**
	 * 2016-12-08
	 * @return Setup
	 */
	final protected function s() {return $this->_setup;}

	/**
	 * 2016-08-14
	 * @param string $c [optional]
	 * @return EavSetup
	 */
	final protected function sEav($c = EavSetup::class) {return dfc($this, function($c) {return
		df_new_om($c, ['setup' => $this->s()])
	;}, [$c]);}

	/**
	 * 2016-12-08
	 * @param string|array $tableName
	 * @return string
	 */
	final protected function t($tableName) {return $this->s()->getTable($tableName);}

	/**
	 * 2016-08-21
	 * 2017-08-01 It checks whether the installed version of the current module is lower than $v.
	 * @param string $v
	 * @return bool
	 */
	final protected function v($v) {return -1 === version_compare($this->_context->getVersion(), $v);}

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