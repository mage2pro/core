<?php
namespace Df\Widget\P;
use Magento\Framework\Data\Form\Element\Editor;
use Magento\Framework\Data\Form\Element\Label;
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
	 * 3) @see \Magento\Widget\Model\Config\Converter::_convertParameter():
	 * 		if ($xsiType == 'block') {
	 * 			$parameter['type'] = 'label';
	 * 			$parameter['@'] = [];
	 * 			$parameter['@']['type'] = 'complex';
	 * 			foreach ($source->childNodes as $blockSubNode) {
	 * 				if ($blockSubNode->nodeName == 'block') {
	 * 					$parameter['helper_block'] = $this->_convertBlock($blockSubNode);
	 * 					break;
	 * 				}
	 * 			}
	 * 		}
	 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Widget/Model/Config/Converter.php#L136-L146
	 */
	final function prepareElementHtml(Label $l):void {
		# 2024-06-01
		# https://github.com/dmatthew/magento2-widget-parameters/blob/d52665e5/Block/Adminhtml/Widget/Type/Wysiwyg.php#L42-L61
		$e = df_new_omd(Editor::class, [
			'config' => df_wysiwyg_config()->getConfig(array_fill_keys(['add_images', 'add_variables', 'add_widgets'], false))
			,'label' => ''
			,'wysiwyg' => true
		] + $l->getData()); /** @var Editor $e */
		$e->setForm($l->getForm());
		if ($l['required']) {
			$e->addClass('required-entry');
		}
		$l->addData(['after_element_html' => $e->getElementHtml(), 'value' => '']);
	}
}