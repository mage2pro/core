<?php
namespace Df\Directory\FE;
// 2019-06-01
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Region extends Dropdown {
	/**
	 * 2019-06-01
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::getValues()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return array(array(string => string))
	 */
	function getValues() {return dfc($this, function() {return df_countries_options(
		$this->dfValues() ?: []
	);});}
}