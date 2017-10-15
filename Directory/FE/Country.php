<?php
namespace Df\Directory\FE;
// 2017-01-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Country extends Dropdown {
	/**
	 * 2017-01-21
	 * @override
	 * @see \Df\Framework\Form\Element\Select::getValue()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return string|null
	 */
	function getValue() {
		// 2017-01-21
		// @todo По хорошему, здесь надо учитывать область действия настроек.
		// Мы же пока таким кодом запрашиваем глобальное значение.
		/** @var string $global */
		$global = df_store_country()->getIso2Code();
		return parent::getValue() ?: (in_array($global, $this->dfValues()) ? $global : null);
	}
	
	/**
	 * 2017-01-21
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::getValues()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return array(array(string => string))
	 */
	function getValues() {return dfc($this, function() {return
		df_countries_options($this->dfValues() ?: [])
	;});}
}