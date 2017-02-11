<?php
namespace Df\Zf\Validate;
/**    
 * 2017-01-14
 * @used-by \Df\Core\Validator::byName()
 */
final class ClassT extends Type {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param object $value
	 * @return boolean
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		/** @var string $expectedClass */
		$expectedClass = $this->getClassExpected();
		return is_object($value) && ($value instanceof $expectedClass);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return
		"объект класса «{$this->getClassExpected()}»"
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return
		"объекта класса «{$this->getClassExpected()}»"
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageInternal() {return
		is_null($this->getValue())
		? "Система вместо объекта класса «{$this->getClassExpected()}» получила значение «NULL»."
		: (
			is_object($this->getValue())
			? strtr(
				"Система вместо требуемого класса «{$this->getClassExpected()}»"
				. ' получила объект класса «{полученный класс}».'
				,['{полученный класс}' => get_class($this->getValue())]
			)
			: parent::getMessageInternal()
		)
	;}

	/** @return string */
	private function getClassExpected() {return dfc($this, function() {return
		df_result_sne($this->cfg(self::$PARAM__CLASS))				
	;});}

	/** @var string */
	private static $PARAM__CLASS = 'class';

	/**
	 * @used-by \Df\Core\Validator::byName()
	 * @param string $className
	 * @return ClassT
	 */
	static function i($className) {
		df_param_sne($className, 0);
		return new self([self::$PARAM__CLASS => $className]);
	}
}