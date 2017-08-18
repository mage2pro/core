<?php
namespace Df\Backend\Model;
// 2017-08-18
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Url extends \Magento\Backend\Model\Url {
	/**
	 * @override
	 * @see \Magento\Backend\Model\Url::_getActionPath():
	 *		protected function _getActionPath() {
	 *			$path = parent::_getActionPath();
	 *			if ($path) {
	 *				if ($this->getAreaFrontName()) {
	 *					$path = $this->getAreaFrontName() . '/' . $path;
	 *				}
	 *			}
	 *			return $path;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.0/app/code/Magento/Backend/Model/Url.php#L400-L415
	 * @used-by \Magento\Framework\Url::_getRoutePath():
	 * 		$routePath = $this->_getActionPath();
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.0/lib/internal/Magento/Framework/Url.php#L584
	 * @used-by \Magento\Framework\Url::_isSecure():
	 * 		$pathSecure = $this->_urlSecurityInfo->isSecure('/' . $this->_getActionPath());
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.0/lib/internal/Magento/Framework/Url.php#L405
	 */
	final protected function _getActionPath() {
        $r = \Magento\Framework\Url::_getActionPath(); /** @var string $r */  /** @var string $front */
        return !($front = $this->getAreaFrontName()) ? $r : "$front/$r";
	}
}