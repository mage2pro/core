<?php
namespace Df\Payment\Block;
use Magento\Framework\Phrase;
/**
 * 2016-05-06
 * По аналогии с @see \Magento\Braintree\Block\Info
 * https://github.com/magento/magento2/blob/135f967/app/code/Magento/Braintree/Block/Info.php
 * https://mage2.pro/t/898/3
 */
class ConfigurableInfo extends \Magento\Payment\Block\ConfigurableInfo {
	/**
	 * 2016-05-06
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::getLabel()
	 * @used-by \Magento\Payment\Block\ConfigurableInfo::setDataToTransfer()
	 * @param string $field
	 * @return Phrase
	 */
	protected function getLabel($field) {return __($field);}
}