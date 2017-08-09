<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Text as _Text;
/**
 * 2015-11-24
 * @see \Df\Framework\Form\Element\Color    
 * @see \Df\Framework\Form\Element\Number
 * @method $this setAfterElementHtml(string $value)
 */
class Text extends _Text implements ElementI {
	/**
	 * 2016-11-20
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * Перекрываем магический метод,
	 * потому что к магическим методам не применяются плагины, а нам надо применить плагин
	 * @see \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form/Field.php#L79-L81
	 *	if ((string)$element->getComment()) {
	 *		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
	 *	}
	 * @return string|null
	 */
	function getComment() {return $this['comment'];}
	
	/**
	 * 2015-11-24
	 * 2015-12-12
	 * Мы не можем делать этот метод абстрактным, потому что наш плагин
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * работает так:
	 *		if ($subject instanceof \Df\Framework\Form\ElementI) {
	 *			$subject->onFormInitialized();
	 *		}
	 * Т.е. будет попытка вызова абстрактного метода.
	 * Также обратите внимание, что для филдсетов этот метод не является абстрактным:
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @see \Df\Framework\Form\Element\Color::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Number::onFormInitialized()
	 */
	function onFormInitialized() {}

	/**
	 * 2015-11-24
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Text::getValue() It is a magic method.
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getEscapedValue()
	 * @return string|null
	 */
	final function getValue() {/** @var string|null $r */return !is_array($r = $this['value']) ? $r :
		df_error(
			"The form element «%1» of the class «%2» mistakenly returns an array as its value:\n%3",
			$this->getName(), df_cts($this), df_dump($r)
		)
	;}
}