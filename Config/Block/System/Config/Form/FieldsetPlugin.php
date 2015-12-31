<?php
namespace Df\Config\Block\System\Config\Form;
use Magento\Config\Block\System\Config\Form\Fieldset as _Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
// 2015-12-13
// Хитрая идея, которая уже давно пришла мне в голову: наследуясь от модифицируемого класса,
// мы получаем возможность вызывать методы с областью доступа protected у переменной $subject.
class FieldsetPlugin extends _Fieldset {
	/**
	 * 2016-01-01
	 * Потрясающая техника, которую я изобрёл только что.
	 */
	public function __construct() {}

	/**
	 * 2015-12-21
	 * Цель перекрытия — устранения дефекта:
	 * «Magento 2 backend incorrectly renders the nested fieldsets:
	 * adds nested TR tags with the same id».
	 * https://mage2.pro/t/330
	 *
	 * Этот дефект приводит к неработоспособности условия <depends> для элемента:
	 * т.е. видимость элемента перестаёт зависеть от другой опции.
	 *
	 * @see \Magento\Config\Block\System\Config\Form\Fieldset::render()
	 * @param _Fieldset|Fieldset $subject
	 * @param \Closure $proceed
	 * @param AbstractElement $element
	 * @return \Magento\Framework\Option\ArrayInterface|mixed
	 */
	public function aroundRender(_Fieldset $subject, \Closure $proceed, AbstractElement $element) {
		$subject->setElement($element);
		$html = $subject->_getHeaderHtml($element);
		foreach ($element->getElements() as $field) {
			if (
				$field instanceof \Magento\Framework\Data\Form\Element\Fieldset
				// 2015-12-21
				// Вот в этой добавке и заключается суть модифицации.
				&& !$field instanceof \Df\Framework\Data\Form\Element\Fieldset
			) {
				$html .= '<tr id="row_' . $field->getHtmlId() . '"><td colspan="4">' . $field->toHtml() . '</td></tr>';
			}
			else {
				$html .= $field->toHtml();
			}
		}
		$html .= $subject->_getFooterHtml($element);
		return $html;
	}
}


