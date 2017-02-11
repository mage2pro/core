<?php
namespace Df\Framework\Form\Element;
use Df\Config\Source\LetterCase;
use Df\Config\Source\SizeUnit;
use Df\Framework\Form\Element\Quantity as Q;
use Df\Framework\Form\Element\Fieldset\Inline as FInline;
use Df\Typography\Font as O;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class Font extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	function onFormInitialized() {
		parent::onFormInitialized();
		// 2016-07-30
		// Этот стиль будет применён к элементу <fieldset>.
		$this->addClass('df-font');
		$this->checkbox(O::enabled, 'Setup?');
		/** @var FInline $row1 */
		$row1 = $this->fieldsetInline('df-checkboxes')->hide();
		$row1->checkbox(O::bold, 'B', ['title' => 'Bold']);
		$row1->checkbox(O::italic, 'I', ['title' => 'Italic']);
		$row1->checkbox(O::underline, 'U', ['title' => 'Underline']);
		$row1->color(O::color, null, ['title' => 'Font Color']);
		/** @var FInline $row2 */
		$row2 = $this->fieldsetInline('df-family')->hide();
		$row2->field(O::family, GoogleFont::class, null, ['title' => 'Font Family']);
		/** @var array(array(string => string)) $sizeValues */
		$sizeValues = [Q::P__VALUES => SizeUnit::s()->toOptionArray()];
		$row2->quantity(O::size, null, $sizeValues + ['title' => 'Font Size']);
		/** @var FInline $row3 */
		$row3 = $this->fieldsetInline('row3')->hide();
		/**
		 * 2015-12-13
		 * Намеренно указываем в качестве подписи пустую строку, а не null,
		 * чтобы получить пустые теги <label><span></span></label>
		 * и потом стилизовать их своей иконкой.
		 */
		$row3->quantity(O::letter_spacing, '', $sizeValues + ['title' => 'Letter Spacing']);
		/**
		 * 2015-12-13
		 * Передаём в качестве подписи название класса Font Awesome.
		 * Такое стало возможным благодаря моему плагину
		 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::aroundGetLabelHtml()
		 * https://github.com/mage2pro/core/tree/73bed4fbb751ab47ad1bb70a8d90f455da26b500/Framework/Data/Form/Element/AbstractElementPlugin.php#L53
		 */
		/**
		 * 2015-12-13
			.test {
				transform : scale(1,1.5);
				-webkit-transform:scale(1,1.5); // Safari and Chrome
				-moz-transform:scale(1,1.5); // Firefox
				-ms-transform:scale(1,1.5); // IE 9+
				-o-transform:scale(1,1.5); // Opera
				letter-spacing: 10px;
			}
		 * https://developer.mozilla.org/en-US/docs/Web/CSS/transform-function#scale()
		 * http://stackoverflow.com/a/16447826
		 */
		$row3->percent(O::scale_horizontal, 'fa-text-width', 100, ['title' => 'Horizontal Scale']);
		$row3->percent(O::scale_vertical, 'fa-text-height', 100, ['title' => 'Vertical Scale']);
		/**
		 * 2015-12-13
		 * Намеренно указываем в качестве подписи пустую строку, а не null,
		 * чтобы получить пустые теги <label><span></span></label>
		 * и потом стилизовать их своей иконкой.
		 */
		df_hide($this->select(O::letter_case, '', LetterCase::s(), ['title' => 'Letter Case']));
		df_fe_init($this, __CLASS__, df_fa());
	}
}