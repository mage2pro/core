<?php
namespace Df\Widget\P;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * 2024-06-01
 * 1) "Implement an ability to use the built-in WYSIWYG editor as a CMS widget parameter":
 * https://github.com/mage2pro/core/issues/392
 * @used-by \Magento\Framework\View\Element\BlockFactory::createBlock():
 * 		$block = $this->objectManager->create($blockName, $arguments);
 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Element/BlockFactory.php#L44
 * 2.1) @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * 2.2) «Class Df\Widget\P\Wysiwyg\Interceptor may not inherit from final class (Df\Widget\P\Wysiwyg)»:
 * https://github.com/mage2pro/core/issues/395
 * 3.1) «Invalid block type: Df\Widget\P\Wysiwyg»:
 * https://github.com/mage2pro/core/issues/394
 * 3.2) @see \Magento\Framework\View\Layout\Generator\Block::getBlockInstance():
 * 		if (!$block instanceof \Magento\Framework\View\Element\AbstractBlock) {
 * 			throw new LocalizedException(
 * 				new \Magento\Framework\Phrase(
 * 					'Invalid block type: %1',
 * 					[is_object($block) ? get_class($block) : (string) $block]
 * 				),
 * 				$e
 * 			);
 *		}
 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Layout/Generator/Block.php#L277-L285
 * 4.1) «Df\Widget\P\Wysiwyg does not implement BlockInterface»: https://github.com/mage2pro/core/issues/393
 * 4.2) @see \Magento\Framework\View\Element\BlockFactory::createBlock():
 * 		if (!$block instanceof BlockInterface) {
 * 			throw new \LogicException($blockName . ' does not implement BlockInterface');
 * 		}
 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Element/BlockFactory.php#L45-L47
 */
class Wysiwyg extends AbstractBlock {
	/**
	 * 2024-06-01
	 * 1) «Invalid method Df\Widget\P\Wysiwyg\Interceptor::prepareElementHtml»: https://github.com/mage2pro/core/issues/396
	 * 2) @used-by \Magento\Widget\Block\Adminhtml\Widget\Options::_addField():
	 * 		if ($helper = $parameter->getHelperBlock()) {
	 * 			$helperBlock = $this->getLayout()->createBlock(
	 * 				$helper->getType(),
	 * 				'',
	 * 				['data' => $helper->getData()]
	 * 			);
	 * 			if ($helperBlock instanceof \Magento\Framework\DataObject) {
	 * 				$helperBlock->setConfig(
	 * 					$helper->getData()
	 * 				)->setFieldsetId(
	 * 					$fieldset->getId()
	 * 				)->prepareElementHtml(
	 * 					$field
	 * 				);
	 * 			}
	 * 		}
	 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Widget/Block/Adminhtml/Widget/Options.php#L209-L224
	 */
	final function prepareElementHtml():void {}

	/**
	 * 2024-06-01
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::_toHtml()
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::_loadCache()
	 */
	final protected function _toHtml():string {return __METHOD__;}
}