<?php
namespace Df\Framework\Data\Form\Element;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class Font extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-font');
		$this->checkbox('setup', 'Setup?')->addClass('df-setup');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row1 */
		$row1 = $this->inlineFieldset('df-checkboxes')->hide();
		$row1->checkbox('bold', 'B');
		$row1->checkbox('italic', 'I');
		$row1->checkbox('underline', 'U');
		$row1->color();
		//$this->field('family', GoogleFont::_C, 'Family')->setContainerClass('df-hidden');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row2 */
		$row2 = $this->inlineFieldset('df-family')->hide();
		$row2->field('family', GoogleFont::_C, 'Family');
		$row2->size();
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row3 */
		$row3 = $this->inlineFieldset('row3')->hide();
		/**
		 * 2015-12-13
		 * Намеренно указываем в качестве подписи пустую строку, а не null,
		 * что мы ъотим получить пустые теги <label><span></span></label>,
		 * чтобы потом стилизовать их своей иконкой.
		 */
		$row3->size('letter_spacing', '');
		/**
		 * 2015-12-13
		 * Передаём в качестве подписи название класса Font Awesome.
		 * Такое стало возможным благодаря моему плагину
		 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::aroundGetLabelHtml()
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
		$row3->sizePercent('scale_horizontal', 'fa-text-width');
		$row3->sizePercent('scale_vertical', 'fa-text-height');
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
			->setContainerClass('df-hidden')
		;
		df_form_element_init($this, 'font/main', [], [
			'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css'
			,'Df_Framework::formElement/font/main.css'
		], 'before');
	}
}