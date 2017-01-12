<?php
namespace Df\Qa;
class Method {
	/**
	 * @param array $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsArray($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\ArrayT::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param mixed $paramValue
	 * @param int $paramOrdering
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsBetween(
		$paramValue, $paramOrdering, $min = null, $max = null, $stackLevel = 0
	) {
		self::validateParam(
			new \Df\Zf\Validate\Between([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$paramValue
			,$paramOrdering
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsBoolean($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\Boolean::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param float $paramValue
	 * @param float $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsFloat($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\FloatT::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param int $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsInteger($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\IntT::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsIso2($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\StringT\Iso2::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsString($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(\Df\Zf\Validate\StringT::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsArray($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\ArrayT::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $resultValue
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsBetween($resultValue, $min = null, $max = null, $stackLevel = 0) {
		self::validateResult(
			new \Df\Zf\Validate\Between([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$resultValue
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsBoolean($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\Boolean::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param float $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsFloat($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\FloatT::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsInteger($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\IntT::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsIso2($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\StringT\Iso2::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsString($resultValue, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\StringT::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsArray($resultValue, $stackLevel = 0) {
		self::validateValue(\Df\Zf\Validate\ArrayT::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $value
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsBetween($value, $min = null, $max = null, $stackLevel = 0) {
		self::validateValue(
			new \Df\Zf\Validate\Between([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$value
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsBoolean($value, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\Boolean::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param float $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsFloat($value, $stackLevel = 0) {
		self::validateValue(\Df\Zf\Validate\FloatT::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param int $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsInteger($value, $stackLevel = 0) {
		self::validateValue(\Df\Zf\Validate\IntT::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsIso2($value, $stackLevel = 0) {
		self::validateValue(\Df\Zf\Validate\StringT\Iso2::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsString($value, $stackLevel = 0) {
		self::validateValue(\Df\Zf\Validate\StringT::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $method
	 * @param array $messages
	 * @param int $paramOrdering  zero-based
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorParam($method, array $messages, $paramOrdering, $stackLevel = 1) {
		/** @var \Df\Qa\State $state */
		$state = self::caller($stackLevel);
		/** @var string $paramName */
		$paramName = 'Неизвестный параметр';
		if (!is_null($paramOrdering) && $state->method()) {
			/** @var \ReflectionParameter $methodParameter */
			$methodParameter = $state->methodParameter($paramOrdering);
			if ($methodParameter instanceof \ReflectionParameter) {
				$paramName = $methodParameter->getName();
			}
		}
		/** @var string $messagesS */
		$messagesS = df_cc_n($messages);
		self::throwException(
			"[{$state->methodName()}]"
			."\nThe argument «{$paramName}» is rejected by the «{$method}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param string $validator
	 * @param array $messages
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorResult($validator, array $messages, $stackLevel = 1) {
		/** @var string $messagesS */
		$messagesS = df_cc_n($messages);
		/** @var string $method */
		$method = self::caller($stackLevel)->methodName();
		self::throwException(
			"[{$method}]\nA result of this method is rejected by the «{$validator}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param mixed $paramValue
	 * @param string $className
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function validateParamClass($paramValue, $className, $paramOrdering, $stackLevel = 0) {
		self::validateParam(
			\Df\Zf\Validate\ClassT::s($className), $paramValue, $paramOrdering, $stackLevel + 1
		);
	}

	/**
	 * @param mixed $resultValue
	 * @param string $className
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function validateResultClass($resultValue, $className, $stackLevel = 0) {
		self::validateResult(\Df\Zf\Validate\ClassT::s($className), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param string $validator
	 * @param array $messages
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorVariable($validator, array $messages, $stackLevel = 1) {
		/** @var string $messagesS */
		$messagesS = df_cc_n($messages);
		/** @var string $method */
		$method = self::caller($stackLevel)->methodName();
		self::throwException(
			"[{$method}]\nThe validator «{$validator}» has catched a variable with an invalid value."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $resultValue
	 * @param int $stackLevel
	 * @return void
	 * @throws \Exception
	 */
	public static function validateResult(\Zend_Validate_Interface $validator, $resultValue, $stackLevel = 1) {
		if (!$validator->isValid($resultValue)) {
			self::raiseErrorResult(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,++$stackLevel
			);
		}
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $value
	 * @param int $stackLevel
	 * @return void
	 * @throws \Exception
	 */
	public static function validateValue(\Zend_Validate_Interface $validator, $value, $stackLevel = 1) {
		if (!$validator->isValid($value)) {
			/** @var string $messagesS */
			$messagesS = df_cc_n($validator->getMessages());
			/** @var string $validatorClass */
			$validatorClass = get_class($validator);
			self::throwException(
				"The validator «{$validatorClass}» has catched a variable with an invalid value."
				."\nThe diagnostic message:\n{$messagesS}"
				, $stackLevel
			);
		}
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel
	 * @return void
	 * @throws \Exception
	 */
	public static function validateParam(
		\Zend_Validate_Interface $validator, $paramValue, $paramOrdering, $stackLevel = 1
	) {
		if (!$validator->isValid($paramValue)) {
			self::raiseErrorParam(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,$paramOrdering
				,++$stackLevel
			);
		}
	}

	/**
	 * 2017-01-12
	 * @used-by df_assert_string_not_empty()
	 * @used-by df_param_string_not_empty()
	 * @used-by df_result_string_not_empty()
	 */
	const NES = 'A non-empty string is required, but got an empty one.';

	/**
	 * Ообъект \Df\Qa\State конструируется на основе $stackLevel + 2,
	 * потому что нам нужно вернуть название метода,
	 * который вызвал тот метод, который вызвал метод caller.
	 * @used-by raiseErrorParam()
	 * @used-by raiseErrorResult()
	 * @used-by raiseErrorVariable()
	 * @param int $offset [optional]
	 * @return \Df\Qa\State
	 */
	private static function caller($offset) {return \Df\Qa\State::i(
		debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset]
	);}

	/**
	 * @param string $message
	 * @param int $stackLevel [optional]
	 * @throws \Exception
	 * @return void
	 */
	private static function throwException($message, $stackLevel = 0) {
		/**
		 * 2015-01-28
		 * Раньше тут стояло throw $e, что приводило к отображению на экране
		 * диагностического сообщения в неверной кодировке.
		 * @uses df_error() точнее: эта функция в режиме разработчика
		 * отсылает браузеру заголовок HTTP о требуемой кодировке.
		 */
		df_error(new \Exception($message, $stackLevel + 1));
	}
}