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
	 * @return string
	 */
	public function aroundRender(_Fieldset $subject, \Closure $proceed, AbstractElement $element) {
		/** @var string $result */
		/**
		 * 2016-01-01
		 * Потомки @see \Magento\Config\Block\System\Config\Form\Fieldset могли перекрыть метод
		 * @see \Magento\Config\Block\System\Config\Form\Fieldset::render().
		 * Пример: @see \Magento\Config\Block\System\Config\Form\Fieldset\Modules\DisableOutput::render()
		 * Поэтому в случае с классом-потомком неправильно не вызывать метод render().
		 */
		if (get_class($subject) !== _Fieldset::class) {
			$result = $proceed($element);
		}
		else {
			$subject->setElement($element);
			$result = $subject->_getHeaderHtml($element);
			foreach ($element->getElements() as $field) {
				if (
					$field instanceof \Magento\Framework\Data\Form\Element\Fieldset
					// 2015-12-21
					// Вот в этой добавке и заключается суть модифицации.
					&& !$field instanceof \Df\Framework\Data\Form\Element\Fieldset
				) {
					$result .= df_tag('tr', ['id' => 'row_' . $field->getHtmlId(),
						df_tag('td', ['colspan' => 4])], $field->toHtml()
					);
				}
				else {
					$result .= $field->toHtml();
				}
			}
			$result .= $subject->_getFooterHtml($element);
		}
		return $result;
	}
}


