<?php
namespace Df\Zf\Validate;
class ClassT extends Type {
	/**
	 * @override
	 * @param object $value
	 * @return boolean
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/** @var string $expectedClass */
		$expectedClass = $this->getClassExpected();
		return is_object($value) && ($value instanceof $expectedClass);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {
		return df_sprintf('объект класса «%s»', $this->getClassExpected());
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {
		return df_sprintf('объекта класса «%s»', $this->getClassExpected());
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageInternal() {
		return
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
		;
	}

	/** @return string */
	private function getClassExpected() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::$PARAM__CLASS);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @var string */
	private static $PARAM__CLASS = 'class';

	/**
	 * @used-by s()
	 * @used-by Df_Dataflow_Model_Registry_Collection::getValidator()
	 * @used-by Df_Core_Validator::byName()
	 * @param string $className
	 * @return ClassT
	 */
	public static function i($className) {
		df_param_string_not_empty($className, 0);
		return new self([self::$PARAM__CLASS => $className]);
	}
	/**
	 * @used-by Df_Qa_Method::validateParamClass()
	 * @used-by Df_Qa_Method::validateResultClass()
	 * @used-by Df_Qa_Method::validateValueClass()
	 * @param string $className
	 * @return ClassT
	 */
	public static function s($className) {
		/** @var array(string => \Df\Zf\Validate\Class) */
		static $result;
		if (!isset($result[$className])) {
			$result[$className] = self::i($className);
		}
		return $result[$className];
	}
}