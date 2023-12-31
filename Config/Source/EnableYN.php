<?php
namespace Df\Config\Source;
use Magento\Config\Model\Config\Source\Enabledisable as Sb;
# 2017-06-25
# 2023-08-06
# "Prevent interceptors generation for the plugins extended from interceptable classes":
# https://github.com/mage2pro/core/issues/327
# 2023-12-31
# "Declare as `final` the final classes implemented `\Magento\Framework\ObjectManager\NoninterceptableInterface`"
# https://github.com/mage2pro/core/issues/345
final class EnableYN extends Sb implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	/**
	 * 2017-06-25
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see Sb::toOptionArray()
	 * @return array(array(string => string|\Magento\Framework\Phrase))
	 */
	function toOptionArray():array {return !df_lang_ru() ? parent::toOptionArray() : df_yes_no();}
}