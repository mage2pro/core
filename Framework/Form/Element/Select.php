<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Select as _Select;
/**
 * 2015-11-28
 * @see \Df\Framework\Form\Element\GoogleFont  
 * @see \Df\Framework\Form\Element\Select2
 * @see \Df\Framework\Form\Element\Select\Range
 * @method string|null getValue()
 * @method array getValues()
 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L62
 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L124
 */
class Select extends _Select implements ElementI {
	/**
	 * 2015-11-28
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
	 * @see \Df\Framework\Form\Element\GoogleFont::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Select2\Number::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Select\Range::onFormInitialized()
	 */
	function onFormInitialized() {}

	/**
	 * 2016-01-29
	 * @param string|null $k [optional]
	 * @param string|null|callable $d [optional]
	 * @return array(string => mixed)
	 */
	final protected function fc($k = null, $d = null) {return df_fe_fc($this, $k, $d);}
}