<?php
namespace Df\Framework\Form\Element\Select;
use Df\Framework\Form\Element\Select;
// 2016-01-29
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Range extends Select {
	/**
	 * 2016-01-29
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Select::getValues() It is a magic method.
	 * @used-by \Magento\Framework\Data\Form\Element\Select::getElementHtml():
	 *		if ($values = $this->getValues()) {
	 *			foreach ($values as $key => $option) {
	 *				if (!is_array($option)) {
	 *					$html .= $this->_optionToHtml(['value' => $key, 'label' => $option], $value);
	 *				}
	 *				elseif (is_array($option['value'])) {
 	 *					$html .= '<optgroup label="' . $option['label'] . '">' . "\n";
	 *					foreach ($option['value'] as $groupItem) {
	 *						$html .= $this->_optionToHtml($groupItem, $value);
	 *					}
	 *					$html .= '</optgroup>' . "\n";
	 *				}
	 *				else {
	 *					$html .= $this->_optionToHtml($option, $value);
	 *				}
	 *			}
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L63
	 * @used-by \Magento\Framework\Data\Form\Element\Select::_prepareOptions()
	 *		protected function _prepareOptions() {
	 *			$values = $this->getValues();
	 *			if (empty($values)) {
	 *				$options = $this->getOptions();
	 *				if (is_array($options)) {
	 *					$values = [];
	 *					foreach ($options as $value => $label) {
	 *						$values[] = ['value' => $value, 'label' => $label];
	 *					}
	 *				}
	 *				elseif (is_string($options)) {
	 *					$values = [['value' => $options, 'label' => $options]];
	 *				}
	 *				$this->setValues($values);
	 *			}
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L125
	 * @return array(array(string => string))
	 */
	final function getValues() {return dfc($this, function() {return df_a_to_options(range(
		df_fe_fc_i($this, 'dfMin'), df_fe_fc_i($this, 'dfMax')
	));});}

	/**
	 * 2016-01-29
	 * @override
	 * @see \Df\Framework\Form\Element\Select::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {$this->addClass('df-dropdown-number'); parent::onFormInitialized();}
}