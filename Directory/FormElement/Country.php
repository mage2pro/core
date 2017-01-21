<?php
// 2017-01-21
namespace Df\Directory\FormElement;
class Country extends Dropdown {
	/**
	 * 2017-01-21
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::getValues()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return array(array(string => string))
	 */
	public function getValues() {return dfc($this, function() {return
		df_countries_options($this->dfValues() ?: [])
	;});}
}