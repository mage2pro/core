<?php
namespace Df\Zf\Validate\StringT;
abstract class Parser extends \Df\Zf\Validate\Type {
	/** @return string */
	abstract protected function getZendValidatorClass();

	/**
	 * @override
	 * @param string $value
	 * @return bool
	 */
	public function isValid($value) {
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
	protected function getZendValidator($locale) {
		df_param_string_not_empty($locale, 0);
		if (!isset($this->{__METHOD__}[$locale])) {
			/** @var string $class */
			$class = $this->getZendValidatorClass();
			/** @var \Zend_Validate_Interface $result */
			$result = new $class($locale);
			df_assert($result instanceof \Zend_Validate_Interface);
			$this->{__METHOD__}[$locale] = $result;
		}
		return $this->{__METHOD__}[$locale];
	}
}