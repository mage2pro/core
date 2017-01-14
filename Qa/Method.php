<?php
namespace Df\Qa;
use Df\Zf\Validate\ArrayT as VArray;
use Df\Zf\Validate\Between as VBetween;
use Df\Zf\Validate\Boolean as VBoolean;
use Df\Zf\Validate\FloatT as VFloat;
use Df\Zf\Validate\IntT as VInt;
use Df\Zf\Validate\StringT as VString;
use Df\Zf\Validate\StringT\Iso2 as VIso2;
use \Exception as E;
final class Method {
	/**
	 * @param array $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return array
	 * @throws E
	 */
	public static function assertParamIsArray($v, $ord, $sl = 0) {return self::vp(
		VArray::s(), $v, $ord, ++$sl
	);}

	/**
	 * @param int|float $v
	 * @param int $ord
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return int|float
	 * @throws E
	 */
	public static function assertParamIsBetween($v, $ord, $min = null, $max = null, $sl = 0) {return
		self::vp(VBetween::i($min, $max), $v, $ord, ++$sl)
	;}

	/**
	 * @param bool $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return bool
	 * @throws E
	 */
	public static function assertParamIsBoolean($v, $ord, $sl = 0) {return
		self::vp(VBoolean::s(), $v, $ord, ++$sl)
	;}

	/**
	 * @param float $v
	 * @param float $ord
	 * @param int $sl [optional]
	 * @return float
	 * @throws E
	 */
	public static function assertParamIsFloat($v, $ord, $sl = 0) {return
		self::vp(VFloat::s(), $v, $ord, ++$sl)
	;}

	/**
	 * @param int $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return int
	 * @throws E
	 */
	public static function assertParamIsInteger($v, $ord, $sl = 0) {return
		self::vp(VInt::s(), $v, $ord, ++$sl)
	;}

	/**
	 * @param string $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return string
	 * @throws E
	 */
	public static function assertParamIsIso2($v, $ord, $sl = 0) {return
		self::vp(VIso2::s(), $v, $ord, ++$sl)
	;}

	/**
	 * @param string $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return string
	 * @throws E
	 */
	public static function assertParamIsString($v, $ord, $sl = 0) {return
		self::vp(VString::s(), $v, $ord, ++$sl)
	;}

	/**
	 * @param array $v
	 * @param int $sl [optional]
	 * @return array
	 * @throws E
	 */
	public static function assertResultIsArray($v, $sl = 0) {return
		self::validateResult(VArray::s(), $v, ++$sl)
	;}

	/**
	 * @param int|float $v
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return int|float
	 * @throws E
	 */
	public static function assertResultIsBetween($v, $min = null, $max = null, $sl = 0) {return
		self::validateResult(VBetween::i($min, $max), $v, ++$sl)
	;}

	/**
	 * @param bool $v
	 * @param int $sl [optional]
	 * @return bool
	 * @throws E
	 */
	public static function assertResultIsBoolean($v, $sl = 0) {return
		self::validateResult(VBoolean::s(), $v, ++$sl)
	;}

	/**
	 * @param float $v
	 * @param int $sl [optional]
	 * @return float
	 * @throws E
	 */
	public static function assertResultIsFloat($v, $sl = 0) {return
		self::validateResult(VFloat::s(), $v, ++$sl)
	;}

	/**
	 * @param int $v
	 * @param int $sl [optional]
	 * @return int
	 * @throws E
	 */
	public static function assertResultIsInteger($v, $sl = 0) {return
		self::validateResult(VInt::s(), $v, ++$sl)
	;}

	/**
	 * @param string $v
	 * @param int $sl [optional]
	 * @return string
	 * @throws E
	 */
	public static function assertResultIsIso2($v, $sl = 0) {return
		self::validateResult(VIso2::s(), $v, ++$sl)
	;}

	/**
	 * @param string $v
	 * @param int $sl [optional]
	 * @return string
	 * @throws E
	 */
	public static function assertResultIsString($v, $sl = 0) {return
		self::validateResult(VString::s(), $v, ++$sl)
	;}

	/**
	 * @param array $v
	 * @param int $sl [optional]
	 * @return array
	 * @throws E
	 */
	public static function assertValueIsArray($v, $sl = 0) {return
		self::validateValue(VArray::s(), $v, ++$sl)
	;}

	/**
	 * @param int|float $value
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsBetween($value, $min = null, $max = null, $sl = 0) {
		self::validateValue(VBetween::i($min, $max), $value, ++$sl);
	}

	/**
	 * @param bool $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsBoolean($value, $sl = 0) {
		self::validateResult(VBoolean::s(), $value, ++$sl);
	}

	/**
	 * @param float $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsFloat($value, $sl = 0) {
		self::validateValue(VFloat::s(), $value, ++$sl);
	}

	/**
	 * @param int $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsInteger($value, $sl = 0) {
		self::validateValue(VInt::s(), $value, ++$sl);
	}

	/**
	 * @param string $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsIso2($value, $sl = 0) {
		self::validateValue(VIso2::s(), $value, ++$sl);
	}

	/**
	 * @param string $value
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function assertValueIsString($value, $sl = 0) {
		self::validateValue(VString::s(), $value, ++$sl);
	}

	/**
	 * @param string $method
	 * @param array $messages
	 * @param int $ord  zero-based
	 * @param int $sl
	 * @return void
	 */
	public static function raiseErrorParam($method, array $messages, $ord, $sl = 1) {
		/** @var \Df\Qa\State $state */
		$state = self::caller($sl);
		/** @var string $paramName */
		$paramName = 'Неизвестный параметр';
		if (!is_null($ord) && $state->method()) {
			/** @var \ReflectionParameter $methodParameter */
			$methodParameter = $state->methodParameter($ord);
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
	 * @param mixed $v
	 * @param string $className
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function vpClass($v, $className, $ord, $sl = 0) {
		self::vp(\Df\Zf\Validate\ClassT::s($className), $v, $ord, ++$sl);
	}

	/**
	 * @param mixed $v
	 * @param string $className
	 * @param int $sl [optional]
	 * @return void
	 * @throws E
	 */
	public static function validateResultClass($v, $className, $sl = 0) {
		self::validateResult(\Df\Zf\Validate\ClassT::s($className), $v, ++$sl);
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
	 * @param mixed $v
	 * @param int $sl
	 * @return mixed
	 * @throws E
	 */
	public static function validateResult(\Zend_Validate_Interface $validator, $v, $sl = 1) {
		if (!$validator->isValid($v)) {
			self::raiseErrorResult(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,++$sl
			);
		}
		return $v;
	}

	/**
	 * @param \Zend_Validate_Interface $validator
	 * @param mixed $value
	 * @param int $sl
	 * @return void
	 * @throws E
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
	 * @param int $ord
	 * @param int $sl
	 * @return mixed
	 * @throws E
	 */
	public static function vp(
		\Zend_Validate_Interface $validator, $v, $ord, $sl = 1
	) {
		if (!$validator->isValid($v)) {
			self::raiseErrorParam(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,$ord
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
	 * @throws E
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
		df_error(new \Exception($message, ++$sl));
	}
}