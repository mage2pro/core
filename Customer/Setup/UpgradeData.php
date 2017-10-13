<?php
namespace Df\Customer\Setup;
// 2016-08-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeData extends \Df\Framework\Upgrade\Data {
	/**
	 * 2016-08-21
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
			df_eav_setup()->addAttribute('customer', self::F__DF, [
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

	/**
	 * 2016-08-21
	 * @used-by _process()
	 * @used-by df_ci_add()
	 * @used-by df_ci_get()
	 * @used-by \Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
	 * @var string
	 */
	const F__DF = 'df';
}