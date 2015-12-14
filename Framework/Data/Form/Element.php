<?php
namespace Df\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Phrase;
/**
 * 2015-11-22
 * Пока что это класс используется только ради описания магазических методов в шапке.
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
 */
class Element extends AbstractElement {
	/**
	 * 2015-11-24
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::shouldLabelBePlacedAfterElement()
	 * @param AbstractElement|Element $e
	 * @return bool
	 */
	public static function shouldLabelBePlacedAfterElement(AbstractElement $e) {
		/** @var string|null $position */
		$position = $e->getLabelPosition();
		return
			$position
			? ElementI::AFTER === $position
			: in_array($e->getExtType(), ['checkbox', 'radio'])
		;
	}
}