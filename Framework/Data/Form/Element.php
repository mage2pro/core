<?php
namespace Df\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Phrase;
/**
 * 2015-11-22
 * Пока что это класс используется только ради описания магических методов в шапке.
 * @method string|null getClass()
 * @method string|null getContainerClass()
 * @method string|null getCssClass()
 * @method string|null getExtType()
 * @method string|null getFieldExtraAttributes()
 * @method string|null|Phrase getLabel()
 * @method string|null getLabelPosition()
 * @method string|null getNote()
 * @method bool|null getNoDisplay()
 * @method bool|null getNoWrapAsAddon()
 * @method bool|null getRequired()
 * @method string|null getScopeLabel()
 * @method string|null|Phrase getTitle()
 * @method AbstractElement|Element setAfterElementHtml(string $value)
 * @method AbstractElement|Element setContainerClass(string $value)
 * @method AbstractElement|Element setLabelPosition(string $value)
 * @method AbstractElement|Element setNote(string $value)
 */
abstract class Element extends AbstractElement implements ElementI {
	/**
	 * @override
	 * @see ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {}

	/**
	 * 2015-12-11
	 * $element->getClass() может вернуть строку вида:
	 * «df-google-font df-name-family select admin__control-select».
	 * Оставляем в ней только наши классы: чьи имена начинаются с df-.
	 * Системные классы мы контейнеру не присваиваем,
	 * потому что для классов типа .admin__control-select
	 * в ядре присутствуют правила CSS, которые считают элементы с этими классами
	 * элементами управления, а не контейнерами, и корёжат нам вёрстку.
	 * @param AbstractElement|Element $e
	 * @return string
	 */
	public static function getClassDfOnly(AbstractElement $e) {
		return implode(' ', array_filter(df_trim(explode(' ', $e->getClass())), function($class) {
			return df_starts_with($class, 'df-');
		}));
	}

	/**
	 * 2015-11-24
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::shouldLabelBeAtRight()
	 * @param AbstractElement|Element $e
	 * @return bool
	 */
	public static function shouldLabelBeAtRight(AbstractElement $e) {
		/** @var string|null $position */
		$position = $e->getLabelPosition();
		return
			$position
			? ElementI::AFTER === $position
			: in_array($e->getExtType(), ['checkbox', 'radio'])
		;
	}
}