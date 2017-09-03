<?php
namespace Df\Config\Source;
use Magento\Config\Model\Config\Source\Enabledisable as Sb;
// 2017-06-25
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class EnableYN extends Sb {
	/**
	 * 2017-06-25
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see Sb::toOptionArray()
	 * @return array(array(string => string|\Magento\Framework\Phrase))
	 */
	function toOptionArray() {return !df_lang_ru() ? parent::toOptionArray() : df_yes_no();}
}