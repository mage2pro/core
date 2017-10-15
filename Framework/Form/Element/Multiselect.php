<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Multiselect as _Multiselect;
/**
 * 2016-03-08
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @method bool getCanBeEmpty()
 * @method array|string getValue()
 * @method array getValues()
 */
class Multiselect extends _Multiselect implements ElementI {
	/**
	 * 2017-09-23
	 * @override
	 * @see _Multiselect::getElementHtml()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
	 *	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
	 *		return $element->getElementHtml();
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L21-L30
	 * @return string
	 */
	function getElementHtml() {
		/** @var string $r */
		if (!$this->ordered()) {
			$r = parent::getElementHtml();
		}
		else {
			$r = '';
			$this->addClass('select multiselect admin__control-multiselect');
			if ($this->getCanBeEmpty()) {
				$r .= '<input type="hidden" name="' . parent::getName() . '" value="" />';
			}
			$r .=
				'<select id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" '
				. $this->serialize($this->getHtmlAttributes())
				. $this->_getUiId() . ' multiple="multiple">' . "\n"
			;
			$selectedA = $this->getValue();
			if (!is_array($selectedA)) {
				$selectedA = explode(',', $selectedA);
			}
			if ($options = $this->getValues()) {
				// 2017-09-23 BEGIN PATCH
				$options = df_map_r(function(array $o) {return [
					is_array($o['value']) ? df_uid() : $o['value'], $o
				];}, $options);
				$prepend = []; /** @var array $prepend */
				foreach ($selectedA as $selectedI) {
					/** @var string $selectedI */
					if (isset($options[$selectedI])) {
						$prepend[]= $options[$selectedI];
						unset($options[$selectedI]);
					}
				}
				$options = array_merge($prepend, $options);
				// 2017-09-23 END PATCH
				foreach ($options as $option) {
					if (is_array($option['value'])) {
						$r .= '<optgroup label="' . $option['label'] . '">' . "\n";
						foreach ($option['value'] as $groupItem) {
							$r .= $this->_optionToHtml($groupItem, $selectedA);
						}
						$r .= '</optgroup>' . "\n";
					} else {
						$r .= $this->_optionToHtml($option, $selectedA);
					}
				}
			}
			$r .= '</select>' . "\n";
			$r .= $this->getAfterElementHtml();
		}
		return $r;
	}

	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		$this->addClass('df-multiselect');
		df_fe_init($this, __CLASS__, df_asset_third_party('Select2/main.css'), ['ordered' => $this->ordered()]);
	}

	/**
	 * 2016-05-13
	 * Наша проблема заключается в том, что Magento передаёт флаг $isMultiselect = true
	 * только для элементов типа multiselect:
	 * How is the isMultiselect parameter passed
	 * to the toOptionArray method of @see \Magento\Framework\Data\OptionSourceInterface?
	 * https://mage2.pro/t/1613
	 * Наш же элемент управления имеет другой тип: type='Df\Framework\Form\Element\Multiselect'
	 * https://github.com/mage2pro/stripe/blob/b105882/etc/adminhtml/system.xml#L250
	 * Получается, что флаг $isMultiselect имеет значение false,
	 * и тогда метод @see \Magento\Directory\Model\Config\Source\Country::toOptionArray()
	 * и другие аналогичные методы добавляют фэйковую опцию «--Please Select--».
	 * Нам она не нужна, поэтому удаляем её здесь.
	 *
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Multiselect::setValues() It is a magic method.
	 * @see \Magento\Framework\DataObject::__call()
	 * @used-by \Magento\Config\Block\System\Config\Form::_initElement():
	 *		if ($field->hasOptions()) {
	 *			$formField->setValues($field->getOptions());
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form.php#L405-L407
	 * How are the options set to a select/multiselect form element? https://mage2.pro/t/1615
	 * How is @see \Magento\Config\Model\Config\Structure\Element\Field::getOptions()
	 * implemented and used? https://mage2.pro/t/1616
	 * @param array $values
	 */
	final function setValues(array $values) {
		$first = df_first($values); /** @var array(string => string)|null $first */
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

	/**
	 * 2017-09-23
	 * @used-by getElementHtml()
	 * @used-by onFormInitialized()
	 * @return bool
	 */
	private function ordered() {return dfc($this, function() {return df_fe_fc_b($this,
		'dfMultiselect_ordered'
	);});}
}

