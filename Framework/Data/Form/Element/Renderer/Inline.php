<?php
namespace Df\Framework\Data\Form\Element\Renderer;
use Df\Framework\Data\Form\Element;
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
				/**
				 * 2015-12-11
				 * По аналогии с @see \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::elementHtml()
				 * http://code.dmitry-fedyuk.com/m2/all/blob/489029cab0b8be03e4a79f0d33ce9afcdec6a76c/Backend/Block/Widget/Form/Renderer/Fieldset/Element.php#L113
				 */
				,'hidden' !== $element->getType()
				 && Element::shouldLabelBePlacedAfterElement($element) ? 'control' : null
				/**
				 * 2015-12-11
				 * $element->getClass() может вернуть строку вида:
				 * «df-google-font df-name-family select admin__control-select».
				 * Оставляем в ней только наши классы: чли имена начинаются с df-.
				 * Системные классы мы контейнеру не присваиваем,
				 * потому что для классов типа .admin__control-select
				 * в ядре присутствуют правила CSS, которые считают элементы с этими классами
				 * элементами управления, а не контейнерами, и корёжат нам вёрстку.
				 */
				, implode(' ', array_filter(df_trim(explode(' ', $element->getClass())), function($class) {
					return df_starts_with($class, 'df-');
				}))
				, $element->getContainerClass()
			)]
			, implode($innerA)
		);
	}

	/** @return Inline */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}

