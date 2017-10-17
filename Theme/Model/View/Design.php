<?php
namespace Df\Theme\Model\View;
use Magento\Theme\Model\View\Design as mDesign;
// 2017-10-17
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Design extends mDesign {
	/**
	 * 2017-10-17
	 * We should not use @see df_design() here,
	 * because it returns the @see \Magento\Theme\Model\View\Design\Proxy singleton,
	 * not the real @see \Magento\Theme\Model\View\Design singleton
	 * \Magento\Theme\Model\View\Design\Proxy is a wrapper around \Magento\Theme\Model\View\Design,
	 * and it its `_theme` property is always `null`.
	 * It looks like:
	 * namespace Magento\Theme\Model\View\Design;
	 * class Proxy
	 * 		extends \Magento\Theme\Model\View\Design
	 * 		implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	 *		public function __construct(
	 * 			\Magento\Framework\ObjectManagerInterface $objectManager
	 * 			,$instanceName = '\\Magento\\Theme\\Model\\View\\Design', $shared = true
	 * 		) {
	 *			$this->_objectManager = $objectManager;
	 *			$this->_instanceName = $instanceName;
	 *			$this->_isShared = $shared;
	 *		}
	 *		protected function _getSubject() {
	 *			if (!$this->_subject) {
	 *				$this->_subject = true === $this->_isShared
	 *					? $this->_objectManager->get($this->_instanceName)
	 *					: $this->_objectManager->create($this->_instanceName)
	 *				;
	 *			}
	 *			return $this->_subject;
	 *		}
	 * 		<methods...>
	 * }
	 * https://github.com/magento/magento2/blob/2.2.0/app/etc/di.xml#L32
	 * <preference for="Magento\Framework\View\DesignInterface" type="Magento\Theme\Model\View\Design\Proxy" />
	 * @used-by df_layout_update()
	 * @return bool
	 */
	final static function isThemeInitialized() {
		$d = df_o(mDesign::class); /** @var mDesign $d */
		return !!$d->_theme;
	}
}