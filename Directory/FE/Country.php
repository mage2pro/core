<?php
namespace Df\Directory\FE;
/**
 * 2017-01-21
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 */
class Country extends Dropdown {
	/**
	 * 2017-01-21
	 * @override
	 * @see \Df\Framework\Form\Element\Select::getValue()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return string|null
	 */
	function getValue() {
		# 2017-01-21
		# @todo По хорошему, здесь надо учитывать область действия настроек.
		# Мы же пока таким кодом запрашиваем глобальное значение.
		$global = df_store_country()->getIso2Code(); /** @var string $global */
		$limited = $this->dfValues(); /** @var string[] $limited */
		return parent::getValue() ?: (!$limited || in_array($global, $limited) ? $global : null);
	}
	
	/**
	 * 2017-01-21
	 * 2019-06-01
	 * If @uses dfValues() returns `[]`, then @uses df_countries_options() returns  all countries allowed in Magento.
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::getValues()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return array(array(string => string))
	 */
	function getValues() {return dfc($this, function():array {return df_countries_options(df_eta($this->dfValues()));});}
}