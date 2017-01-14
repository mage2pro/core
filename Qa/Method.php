<?php
namespace Df\Qa;
use Df\Zf\Validate\ArrayT as VArray;
use Df\Zf\Validate\Between as VBetween;
class Method {
	/**
	 * @param array $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return array
	 * @throws \Exception
	 */
	public static function assertParamIsArray($v, $ord, $sl = 0) {return
		self::validateParam(VArray::s(), $v, $ord, $sl + 1)
	;}

	/**
	 * @param int|float $v
	 * @param int $ord
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return int|float
	 * @throws \Exception
	 */
	public static function assertParamIsBetween($v, $ord, $min = null, $max = null, $sl = 0) {return
		self::validateParam(
			new VBetween([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$v
			,$ord
			,$sl + 1
		)
	;}

	/**
	 * @param bool $paramValue
	 * @param int $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsBoolean($paramValue, $paramOrdering, $sl = 0) {
		self::validateParam(\Df\Zf\Validate\Boolean::s(), $paramValue, $paramOrdering, $sl + 1);
	}

	/**
	 * @param float $paramValue
	 * @param float $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsFloat($paramValue, $paramOrdering, $sl = 0) {
		self::validateParam(\Df\Zf\Validate\FloatT::s(), $paramValue, $paramOrdering, $sl + 1);
	}

	/**
	 * @param int $paramValue
	 * @param int $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsInteger($paramValue, $paramOrdering, $sl = 0) {
		self::validateParam(\Df\Zf\Validate\IntT::s(), $paramValue, $paramOrdering, $sl + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsIso2($paramValue, $paramOrdering, $sl = 0) {
		self::validateParam(\Df\Zf\Validate\StringT\Iso2::s(), $paramValue, $paramOrdering, $sl + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertParamIsString($paramValue, $paramOrdering, $sl = 0) {
		self::validateParam(\Df\Zf\Validate\StringT::s(), $paramValue, $paramOrdering, $sl + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsArray($resultValue, $sl = 0) {
		self::validateResult(VArray::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param int|float $resultValue
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsBetween($resultValue, $min = null, $max = null, $sl = 0) {
		self::validateResult(
			new VBetween([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$resultValue
			,$sl + 1
		);
	}

	/**
	 * @param bool $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsBoolean($resultValue, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\Boolean::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param float $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsFloat($resultValue, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\FloatT::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param int $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsInteger($resultValue, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\IntT::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsIso2($resultValue, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\StringT\Iso2::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertResultIsString($resultValue, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\StringT::s(), $resultValue, $sl + 1);
	}

	/**
	 * @param array $v
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsArray($v, $sl = 0) {
		self::validateValue(VArray::s(), $v, $sl + 1);
	}

	/**
	 * @param int|float $value
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsBetween($value, $min = null, $max = null, $sl = 0) {
		self::validateValue(
			new VBetween([
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			])
			,$value
			,$sl + 1
		);
	}

	/**
	 * @param bool $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsBoolean($value, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\Boolean::s(), $value, $sl + 1);
	}

	/**
	 * @param float $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsFloat($value, $sl = 0) {
		self::validateValue(\Df\Zf\Validate\FloatT::s(), $value, $sl + 1);
	}

	/**
	 * @param int $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsInteger($value, $sl = 0) {
		self::validateValue(\Df\Zf\Validate\IntT::s(), $value, $sl + 1);
	}

	/**
	 * @param string $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsIso2($value, $sl = 0) {
		self::validateValue(\Df\Zf\Validate\StringT\Iso2::s(), $value, $sl + 1);
	}

	/**
	 * @param string $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function assertValueIsString($value, $sl = 0) {
		self::validateValue(\Df\Zf\Validate\StringT::s(), $value, $sl + 1);
	}

	/**
	 * @param string $method
	 * @param array $messages
	 * @param int $paramOrdering  zero-based
	 * @param int $sl
	 * @return void
	 */
	public static function raiseErrorParam($method, array $messages, $paramOrdering, $sl = 1) {
		/** @var \Df\Qa\State $state */
		$state = self::caller($sl);
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
			, $sl
		);
	}

	/**
	 * @param string $validator
	 * @param array $messages
	 * @param int $sl
	 * @return void
	 */
	public static function raiseErrorResult($validator, array $messages, $sl = 1) {
		/** @var string $messagesS */
		$messagesS = df_cc_n($messages);
		/** @var string $method */
		$method = self::caller($sl)->methodName();
		self::throwException(
			"[{$method}]\nA result of this method is rejected by the «{$validator}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * @param mixed $paramValue
	 * @param string $className
	 * @param int $paramOrdering
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function validateParamClass($paramValue, $className, $paramOrdering, $sl = 0) {
		self::validateParam(
			\Df\Zf\Validate\ClassT::s($className), $paramValue, $paramOrdering, $sl + 1
		);
	}

	/**
	 * @param mixed $resultValue
	 * @param string $className
	 * @param int $sl [optional]
	 * @return void
	 * @throws \Exception
	 */
	public static function validateResultClass($resultValue, $className, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\ClassT::s($className), $resultValue, $sl + 1);
	}

	/**
	 * @param string $validator
	 * @param array $messages
	 * @param int $sl
	 * @return void
	 */
	public static function raiseErrorVariable($validator, array $messages, $sl = 1) {
		/** @var string $messagesS */
		$messagesS = df_cc_n($messages);
		/** @var string $method */
		$method = self::caller($sl)->methodName();
		self::throwException(
			"[{$method}]\nThe validator «{$validator}» has catched a variable with an invalid value."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $resultValue
	 * @param int $sl
	 * @return void
	 * @throws \Exception
	 */
	public static function validateResult(\Zend_Validate_Interface $validator, $resultValue, $sl = 1) {
		if (!$validator->isValid($resultValue)) {
			self::raiseErrorResult(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,++$sl
			);
		}
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $value
	 * @param int $sl
	 * @return void
	 * @throws \Exception
	 */
	public static function validateValue(\Zend_Validate_Interface $validator, $value, $sl = 1) {
		if (!$validator->isValid($value)) {
			/** @var string $messagesS */
			$messagesS = df_cc_n($validator->getMessages());
			/** @var string $validatorClass */
			$validatorClass = get_class($validator);
			self::throwException(
				"The validator «{$validatorClass}» has catched a variable with an invalid value."
				."\nThe diagnostic message:\n{$messagesS}"
				, $sl
			);
		}
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $v
	 * @param int $paramOrdering
	 * @param int $sl
	 * @return mixed
	 * @throws \Exception
	 */
	public static function validateParam(
		\Zend_Validate_Interface $validator, $v, $paramOrdering, $sl = 1
	) {
		if (!$validator->isValid($v)) {
			self::raiseErrorParam(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,$paramOrdering
				,++$sl
			);
		}
		return $v;
	}

	/**
	 * 2017-01-12
	 * @used-by df_assert_string_not_empty()
	 * @used-by df_param_string_not_empty()
	 * @used-by df_result_string_not_empty()
	 */
	const NES = 'A non-empty string is required, but got an empty one.';

	/**
	 * Ообъект \Df\Qa\State конструируется на основе $sl + 2,
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
	 * @param int $sl [optional]
	 * @throws \Exception
	 * @return void
	 */
	private static function throwException($message, $sl = 0) {
		/**
		 * 2015-01-28
		 * Раньше тут стояло throw $e, что приводило к отображению на экране
		 * диагностического сообщения в неверной кодировке.
		 * @uses df_error() точнее: эта функция в режиме разработчика
		 * отсылает браузеру заголовок HTTP о требуемой кодировке.
		 */
		df_error(new \Exception($message, $sl + 1));
	}
}