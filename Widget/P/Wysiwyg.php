<?php
namespace Df\Widget\P;
use Magento\Framework\View\Element\BlockInterface as IBlock;
/**
 * 2024-06-01
 * "Implement an ability to use the built-in WYSIWYG editor as a CMS widget parameter":
 * https://github.com/mage2pro/core/issues/392
 * @used-by \Magento\Framework\View\Element\BlockFactory::createBlock():
 * 		$block = $this->objectManager->create($blockName, $arguments);
 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Element/BlockFactory.php#L44
 */
final class Wysiwyg
	/**
	 * 2024-06-01
	 * «Df\Widget\P\Wysiwyg does not implement BlockInterface»: https://github.com/mage2pro/core/issues/393
	 * @see \Magento\Framework\View\Element\BlockFactory::createBlock():
	 * 		if (!$block instanceof BlockInterface) {
	 * 			throw new \LogicException($blockName . ' does not implement BlockInterface');
	 * 		}
	 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Element/BlockFactory.php#L45-L47
	 */
	implements IBlock {
	/**
	 * 2024-06-01
	 * @override
	 * @see \Magento\Framework\View\Element\BlockInterface::toHtml()
	 */
	function toHtml():string {return __METHOD__;}
}