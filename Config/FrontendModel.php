<?php
namespace Df\Config;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * 2015-12-15
 * frontend_model должна:
 * https://mage2.pro/t/219
 * 1) наследоваться от @see \Magento\Framework\View\Element\AbstractBlock
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Layout/Generator/Block.php#L261-L265
 * 2) реализовывать интерфейс
 * @see \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L311-L315
 */
abstract class FrontendModel extends AbstractBlock implements RendererInterface {
	/**
	 * 2015-12-15
	 * @used-by FrontendModel::render()
	 * @return string
	 */
	abstract protected function _render();

	/**
	 * 2015-12-15
	 * @override
	 * @see RendererInterface::render()
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getHtml()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L465
	 * @param AE $element
	 * @return string
	 */
	function render(AE $element) {
		/**
		 * Система использует frontend_model как одиночки:
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L319
		 * А нам удобнее для каждого рисования создавать отдельный экземпляр.
		 */
		$i = clone $this;
		$i->_element = $element;
		return $i->_render();
	}

	/** @return AE */
	protected function e() {return $this->_element;}

	/**
	 * 2015-12-15
	 * @used-by FrontendModel::e()
	 * @used-by FrontendModel::render()
	 * @var AE
	 */
	private $_element;
}


