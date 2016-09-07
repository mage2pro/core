<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Select as _Select;
/**
 * 2016-09-04
 * @method string|null getValue()
 *
 * 2016-08-10
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
			if ($subject instanceof \Df\Framework\Form\ElementI) {
				$subject->onFormInitialized();
			}
	 * Т.е. будет попытка вызова абстрактного метода.
	 * Также обратите внимание, что для филдсетов этот метод не является абстрактным:
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {}

	/**
	 * 2016-01-29
	 * @param string|null $key [optional]
	 * @param string|null|callable $default [optional]
	 * @return array(string => mixed)
	 */
	protected function fc($key = null, $default = null) {return df_fe_fc($this, $key, $default);}
}