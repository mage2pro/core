<?php
namespace Df\Customer\Setup;
/**
 * 2017-10-14
 * «[ERROR] Magento\Framework\Exception\LocalizedException:
 * Wrong entity ID in vendor/magento/module-eav/Setup/EavSetup.php:265»:
 * https://github.com/mage2pro/core/pull/36
 * https://mage2.pro/t/4235
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class UpgradeData extends \Df\Framework\Upgrade\Data {
	/**
	 * 2017-10-14
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		/**
		 * 2016-08-22
		 * Помимо добавления поля в таблицу «customer_entity» надо ещё добавить атрибут,
		 * иначе данные не будут сохраняться:
		 *		$attribute = $this->getAttribute($k);
		 *		if (empty($attribute)) {
		 *			continue;
		 *		}
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Eav/Model/Entity/AbstractEntity.php#L1262-L1265
		 */
		if ($this->v('1.7.4')) {
			df_eav_setup()->addAttribute('customer', UpgradeSchema::F__DF, [
				'input' => 'text'
				,'label' => 'Mage2.PRO'
				,'position' => 1000
				,'required' => false
				,'sort_order' => 1000
				,'system' => false
				,'type' => 'static'
				,'visible' => false
			]);
		}
	}
}