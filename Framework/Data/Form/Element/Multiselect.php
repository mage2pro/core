<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\ElementI;
use Magento\Framework\Data\Form\Element\Multiselect as _Multiselect;
// 2016-03-08
class Multiselect extends _Multiselect implements ElementI {
	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Framework\Data\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->addClass('df-multiselect');
		df_fe_init($this, __CLASS__, 'Df_Core::lib/Select2/main.css');
	}

	/**
	 * 2016-05-13
	 * Наша проблема заключается в том, что Magento передаёт флаг $isMultiselect = true
	 * только для элементов типа multiselect:
	 * How is the isMultiselect parameter passed
	 * to the toOptionArray method of @see \Magento\Framework\Data\OptionSourceInterface?
	 * https://mage2.pro/t/1613
	 * Наш же элемент управления имеет другой тип: type='Df\Framework\Data\Form\Element\Multiselect'
	 * https://code.dmitry-fedyuk.com/m2e/stripe/blob/b105882/etc/adminhtml/system.xml#L250
	 * Получается, что флаг $isMultiselect имеет значение false,
	 * и тогда метод @see \Magento\Directory\Model\Config\Source\Country::toOptionArray()
	 * и другие аналогичные методы добавляют фэйковую опцию «--Please Select--».
	 * Нам она не нужна, поэтому удаляем её здесь.
	 *
	 * @override
	 * @see \Magento\Framework\DataObject::__call()
	 * @used-by \Magento\Config\Block\System\Config\Form::_initElement()
	 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Config/Block/System/Config/Form.php#L375-L377
	 * How are the options set to a select/multiselect form element? https://mage2.pro/t/1615
	 * How is @see \Magento\Config\Model\Config\Structure\Element\Field::getOptions()
	 * implemented and used? https://mage2.pro/t/1616
	 * @param array $values
	 */
	public function setValues(array $values) {
		/** @var array(string => string)|null $first */
		$first = df_first($values);
		/**
		 * 2016-05-13
		 * @see \Magento\Directory\Model\Config\Source\Country::toOptionArray()
		 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Directory/Model/Config/Source/Country.php#L51-L51
		 */
		if ($first && (string)__('--Please Select--') === (string)dfa($first, 'label')) {
			array_shift($values);
		}
		$this['values'] = $values;
	}
}

