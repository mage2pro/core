<?php
namespace Df\Config\Plugin\Block\System\Config\Form;
use Df\Config\Block\System\Config\Form\Fieldset as F;
use Magento\Config\Block\System\Config\Form\Fieldset as Sb;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
// 2015-12-13
// Хитрая идея, которая уже давно пришла мне в голову: наследуясь от модифицируемого класса,
// мы получаем возможность вызывать методы с областью доступа protected у переменной $s.
class Fieldset extends Sb {
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
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param AE $element
	 * @return string
	 */
	public function aroundRender(Sb $sb, \Closure $proceed, AE $element) {
		/** @var string $result */
		/**
		 * 2016-01-01
		 * Потомки @see \Magento\Config\Block\System\Config\Form\Fieldset могли перекрыть метод
		 * @see \Magento\Config\Block\System\Config\Form\Fieldset::render().
		 * Пример: @see \Magento\Config\Block\System\Config\Form\Fieldset\Modules\DisableOutput::render()
		 * Поэтому в случае с классом-потомком неправильно не вызывать метод render().
		 */
		if (get_class($sb) !== df_interceptor_name(Sb::class)) {
			$result = $proceed($element);
		}
		else {
			$sb->setElement($element);
			$result = $sb->_getHeaderHtml($element);
			foreach ($element->getElements() as $field) {
				if (
					$field instanceof \Magento\Framework\Data\Form\Element\Fieldset
					// 2015-12-21
					// Вот в этой добавке и заключается суть модифицации.
					&& !$field instanceof \Df\Framework\Data\Form\Element\Fieldset
				) {
					$result .= df_tag('tr', ['id' => 'row_' . $field->getHtmlId()],
						df_tag('td', ['colspan' => 4], $field->toHtml())
					);
				}
				else {
					$result .= $field->toHtml();
				}
			}
			$result .= $sb->_getFooterHtml($element);
		}
		return $result;
	}
}