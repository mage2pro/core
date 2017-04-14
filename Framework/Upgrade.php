<?php
namespace Df\Framework;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Adapter\Pdo\Mysql as Adapter;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Module\Setup;
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
	 */
	abstract protected function _process();

	/**
	 * 2016-12-08
	 * @return Adapter|IAdapter
	 */
	final protected function c() {return $this->s()->getConnection();}

	/**
	 * 2016-12-02
	 * Перекрывайте этот метод, если в изначальной версии модуля отсутствовал этот класс.
	 * В таком случае укажите здесь ту версию модуля, в которой был добавлен этот класс.
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
	 * @param string $class [optional]
	 * @return EavSetup
	 */
	final protected function sEav($class = EavSetup::class) {return dfc($this, function($class) {return
		df_new_om($class, ['setup' => $this->s()])
	;}, [$class]);}

	/**
	 * 2016-12-08
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