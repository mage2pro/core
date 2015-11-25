<?php
namespace Df\Framework\Data\Form\Element\Renderer;
use Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\ElementI;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
/**
 * 2015-11-19
 * Этот рендерер позволяет разместить несколько элементов формы в едином ряду,
 * в отличие от стандартного административного рендерера
 * @see \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/Block/Widget/Form/Renderer/Fieldset/Element.php
 * @see https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/view/adminhtml/templates/widget/form/renderer/fieldset/element.phtml
 *
 * К сожалению, не получается для этой цели просто отменить рендерер:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L464-L468
	if ($this->_renderer) {
		$html = $this->_renderer->render($this);
	} else {
		$html = $this->getDefaultHtml();
	}
 * потому что тогда элемент вернёт $this->getDefaultHtml() вместо $this->getElementHtml()
 */
class Inline implements RendererInterface {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Renderer\RendererInterface::render()
	 * @param AbstractElement|\Df\Framework\Data\Form\Element $element
	 * @return string
	 */
	public function render(AbstractElement $element) {
		/** @var string $innerA */
		$innerA = [$element->getLabelHtml(), $element->getElementHtml()];
		if (Element::shouldLabelBePlacedAfterElement($element)) {
			$innerA = array_reverse($innerA);
		}
		return df_tag('span',
			['class' => df_concat_clean(' ',
				'df-element-inline', $element->getClass(), $element->getContainerClass()
			)]
			, implode($innerA)
		);
	}

	/** @return Inline */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}

