<?php
namespace Df\Zf\Validate\StringT;
/** @see \Df\Zf\Validate\StringT\FloatT */
abstract class Parser extends \Df\Zf\Validate {
	/** @return string */
	abstract protected function getZendValidatorClass();

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param string $v
	 */
	function isValid($v):bool {
		$this->v($v);
		return $this->getZendValidator('en_US')->isValid($v) || $this->getZendValidator('ru_RU')->isValid($v);
	}

	/**
	 * @param string $locale
	 * @return \Zend_Validate_Interface
	 */
	protected function getZendValidator($locale) {return dfc($this, function($locale) {return
		df_newa($this->getZendValidatorClass(), \Zend_Validate_Interface::class, $locale)
	;}, func_get_args());}
}