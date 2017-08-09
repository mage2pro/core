<?php
namespace Df\Framework\Form;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
/**
 * 2015-11-22
 * @see \Df\Framework\Form\Element\Url
 * @method string|null getClass()
 * 2016-05-30
 * @method string|Phrase|null getComment()
 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form/Field.php#L79-L81
 *	if ((string)$element->getComment()) {
 *		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
 *	}
 * @method AE|null getContainer()
 * @method string|null getContainerClass()
 * @method string|null getCssClass()
 * @method string|null getExtType()
 * @method mixed[] getFieldConfig()
 * @method string|null getFieldExtraAttributes()
 * @method string|null|Phrase getLabel()
 * @method string|null getLabelPosition()
 * @method string|null getNote()
 * @method bool|null getNoDisplay()
 * @method bool|null getNoWrapAsAddon()
 * @method bool|null getRequired()
 * @method string|null getScopeLabel()
 * @method string|null|Phrase getTitle()
 * @method $this setAfterElementHtml(string $value)
 * @method $this setContainerClass(string $value)
 * @method $this setLabelPosition(string $value)
 * @method $this setNote(string $value)
 */
class Element extends AE implements ElementI {
	/**
	 * @override
	 * @see ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {}

	/**
	 * 2015-12-11
	 * $element->getClass() может вернуть строку вида:
	 * «df-google-font df-name-family select admin__control-select».
	 * Оставляем в ней только наши классы: чьи имена начинаются с df-.
	 * Системные классы мы контейнеру не присваиваем,
	 * потому что для классов типа .admin__control-select
	 * в ядре присутствуют правила CSS, которые считают элементы с этими классами
	 * элементами управления, а не контейнерами, и корёжат нам вёрстку.
	 * @param AE|Element $e
	 * @return string
	 */
	final static function getClassDfOnly(AE $e) {return df_cc_s(array_filter(
		df_trim(explode(' ', $e->getClass())), function($c) {return df_starts_with($c, 'df-');}
	));}

	/**
	 * 2015-11-24
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::shouldLabelBeAtRight()
	 * @param AE|Element $e
	 * @return bool
	 */
	final static function shouldLabelBeAtRight(AE $e) {/** @var string|null $p */return
		($p = $e->getLabelPosition())
		? ElementI::AFTER === $p
		: in_array($e->getExtType(), ['checkbox', 'radio'])
	;}

	/**
	 * 2015-12-13
	 * @used-by df_fe_uid()
	 * Метод @uses \Magento\Framework\Data\Form\Element\AbstractElement::_getUiId()
	 * возвращает атрибут и его значение уже в виже слитной строки, поэтому парсим её.
	 * https://github.com/magento/magento2/blob/c58d2d/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L331-L338
	 * @param AE $element
	 * @param string $suffix [optional]
	 * @return string
	 */
	final static function uidSt(AE $element, $suffix = null) {return df_trim(
		df_last(explode('=', $element->_getUiId($suffix))), '"'
	);}
}