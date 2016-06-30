<?php
namespace Df\Framework\Form\Element\Renderer;
use Df\Framework\Form\Element as E;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
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
	 * @param AE|\Df\Framework\Form\Element $element
	 * @return string
	 */
	public function render(AE $element) {
		/** @var bool $labelAtRight */
		$labelAtRight = E::shouldLabelBeAtRight($element);
		/**
		 * 2015-12-11
		 * Класс .df-label-sibling означает: элемент рядом с label.
		 * В данном случае это всегда непосредственно элемент управления,
		 * а вот для блочных элементов это может быть div-оболочка вокруг элемента:
		 * @see \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::elementHtml()
		 */
		$element->addClass('df-label-sibling');
		/**
		 * 2015-12-28
		 * К сожалению, мы не можем назначать классы для label:
		 * @uses \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L425
		 * Потому ситуацию, когда label расположена справа от элемента,
		 * помечаем классом для элемента.
		 * При этом сама label справа может быть выбрана селектором .df-label-sibling ~ label
		 */
		if ($labelAtRight) {
			$element->addClass('df-label-at-right');
		}
		/** @var string $innerA */
		$innerA = [$element->getLabelHtml(), $element->getElementHtml()];
		if ($labelAtRight) {
			$innerA = array_reverse($innerA);
		}
		return df_tag('span',
			['class' => df_cc_clean(' ',
				'df-element-inline'
				/**
				 * 2015-12-11
				 * Класс .field для элементов внутри inline fieldset не добавляю намеренно:
				 * слишком уж много стилей ядро связывает с этим классом, и это чересчур ломает мою вёрстку.
				 * Но система добавляет это класс, когда поле находится не внутри inline fieldset.
				 * Мы же вместо .field опираемся на наш селектор .df-field,
				 * который мы добавляем как к инлайновым полям, так и к блочным:
				 * @see \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::outerCssClasses()
				 * http://code.dmitry-fedyuk.com/m2/all/blob/489029cab0b8be03e4a79f0d33ce9afcdec6a76c/Backend/Block/Widget/Form/Renderer/Fieldset/Element.php#L189
				 */
				,'df-field'
				, E::getClassDfOnly($element)
				// 2015-11-23
				// Моё добавление.
				, $element->getContainerClass()
			)]
			, implode($innerA)
		);
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}

