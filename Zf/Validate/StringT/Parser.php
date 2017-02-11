<?php
namespace Df\Zf\Validate\StringT;
abstract class Parser extends \Df\Zf\Validate\Type {
	/** @return string */
	abstract protected function getZendValidatorClass();

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param string $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		return
				$this->getZendValidator('en_US')->isValid($value)
			||
				$this->getZendValidator('ru_RU')->isValid($value)
		;
	}

	/**
	 * @param string $locale
	 * @return \Zend_Validate_Interface
	 */
	protected function getZendValidator($locale) {return dfc($this, function($locale) {return
		df_newa($this->getZendValidatorClass(), \Zend_Validate_Interface::class, $locale)
	;}, func_get_args());}
}