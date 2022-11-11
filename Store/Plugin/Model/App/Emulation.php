<?php
namespace Df\Store\Plugin\Model\App;
use Magento\Store\Model\App\Emulation as Sb;
# 2021-09-08 "Diagnose «Environment emulation nesting is not allowed» errors": https://github.com/mage2pro/core/issues/157
final class Emulation {
	/**
	 * 2021-09-08 "Diagnose «Environment emulation nesting is not allowed» errors": https://github.com/mage2pro/core/issues/157
	 * @see \Magento\Store\Model\App\Emulation::startEnvironmentEmulation()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/App/Emulation.php#L104-L158
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/Store/Model/App/Emulation.php#L111-L165
	 * @param Sb $sb
	 * @param integer $storeId
	 * @param string $area
	 * @param bool $force
	 */
	function beforeStartEnvironmentEmulation(Sb $sb, $storeId, $area, $force):void {
		if (!$this->_initial) {
			$this->_initial = ['area' => $area, 'force' => $force, 'storeId' => $storeId, 'trace' => df_bt_log()];
		}
		else {
			df_log_l($sb, [
				'message' => 'Environment emulation nesting is not allowed', 'initial' => $this->_initial
			], 'emulation-nesting');
		}
	}

	/**
	 * 2021-09-08 "Diagnose «Environment emulation nesting is not allowed» errors": https://github.com/mage2pro/core/issues/157
	 * @see \Magento\Store\Model\App\Emulation::stopEnvironmentEmulation()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/App/Emulation.php#L160-L182
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/Store/Model/App/Emulation.php#L167-L189
	 */
	function beforeStopEnvironmentEmulation():void {$this->_initial = null;}

	/**
	 * 2021-09-08
	 * @used-by self::beforeStartEnvironmentEmulation()
	 * @used-by \Df\Store\Plugin\Model\App\Emulation::beforeStopEnvironmentEmulation()
	 * @var array(string => mixed)|null
	 */
	private $_initial;
}