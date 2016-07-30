<?php
namespace Df\Framework\Form\Element;
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
	public function onFormInitialized() {
		parent::onFormInitialized();
		// 2016-07-30
		// Этот стиль будет применён к элементу <fieldset>.
		$this->addClass('df-font');
		$this->checkbox('setup', 'Setup?');
		/** @var \Df\Framework\Form\Element\Fieldset\Inline $row1 */
		$row1 = $this->fieldsetInline('df-checkboxes')->hide();
		$row1->checkbox('bold', 'B', ['title' => 'Bold']);
		$row1->checkbox('italic', 'I', ['title' => 'Italic']);
		$row1->checkbox('underline', 'U', ['title' => 'Underline']);
		$row1->color('', null, ['title' => 'Font Color']);
		/** @var \Df\Framework\Form\Element\Fieldset\Inline $row2 */
		$row2 = $this->fieldsetInline('df-family')->hide();
		$row2->field('family', GoogleFont::class, null, ['title' => 'Font Family']);
		$row2->quantity('size', null, ['title' => 'Font Size']);
		/** @var \Df\Framework\Form\Element\Fieldset\Inline $row3 */
		$row3 = $this->fieldsetInline('row3')->hide();
		/**
		 * 2015-12-13
		 * Намеренно указываем в качестве подписи пустую строку, а не null,
		 * чтобы получить пустые теги <label><span></span></label>
		 * и потом стилизовать их своей иконкой.
		 */
		$row3->quantity('letter_spacing', '', ['title' => 'Letter Spacing']);
		/**
		 * 2015-12-13
		 * Передаём в качестве подписи название класса Font Awesome.
		 * Такое стало возможным благодаря моему плагину
		 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::aroundGetLabelHtml()
		 * http://code.dmitry-fedyuk.com/m2/all/blob/73bed4fbb751ab47ad1bb70a8d90f455da26b500/Framework/Data/Form/Element/AbstractElementPlugin.php#L53
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
		$row3->percent('scale_horizontal', 'fa-text-width', 100, ['title' => 'Horizontal Scale']);
		$row3->percent('scale_vertical', 'fa-text-height', 100, ['title' => 'Vertical Scale']);
		/**
		 * 2015-12-13
		 * Намеренно указываем в качестве подписи пустую строку, а не null,
		 * чтобы получить пустые теги <label><span></span></label>
		 * и потом стилизовать их своей иконкой.
		 */
		df_hide($this->select('letter_case', '', \Df\Config\Source\LetterCase::s(), [
			'title' => 'Letter Case'
		]));
		df_fe_init($this, __CLASS__, DF_FA);
	}
}