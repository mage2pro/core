<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Magento\Framework\Phrase;
if (!defined ('PHP_INT_MIN')) {
	define('PHP_INT_MIN', ~PHP_INT_MAX);
}
/**
 * 2016-09-02
 * К сожалению, конструкции типа
 * const DF_F_TRIM = \Df\Zf\Filter\StringT\Trim::class;
 * приводят к сбою дибильного компилятора Magento 2:
 * https://github.com/magento/magento2/issues/6179
 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/25
 *
 */
const DF_F_TRIM = '\Df\Zf\Filter\StringT\Trim';
const DF_V_ARRAY = '\Df\Zf\Validate\ArrayT';
const DF_V_BOOL = '\Df\Zf\Validate\Boolean';
const DF_V_FLOAT = '\Df\Zf\Validate\FloatT';
const DF_V_INT = '\Df\Zf\Validate\IntT';
// 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
// https://ru.wikipedia.org/wiki/ISO_3166-1
const DF_V_ISO2 = '\Df\Zf\Validate\StringT\Iso2';
const DF_V_NAT = '\Df\Zf\Validate\Nat';
const DF_V_NAT0 = '\Df\Zf\Validate\Nat0';
const DF_V_STRING = '\Df\Zf\Validate\StringT';
const DF_V_STRING_NE = '\Df\Zf\Validate\StringT\NotEmpty';

/**
 * @param mixed $v
 * @return int
 * @throws DFE
 */
function df_01($v) {
	/** @var int $result */
	$result = df_int($v);
	df_assert_in($result, [0, 1]);
	return $result;
}

/**
 * 2016-08-27
 * Параметр $caller нам реально нужен,
 * потому что посредством @see debug_backtrace() мы можем получить только имя того класса,
 * где абстрактный метод был объявлен, а нам нужно имя класса текущего объекта
 * (в классе которого абстрактный метод должен был быть перекрыт).
 * @param object $caller
 * @return void
 */
function df_abstract($caller) {
	/** @var string $scope */
	$scope = sprintf('<b>\\%s</b> class', df_cts($caller));
	df_error_html("The method %s should be redefined by the {$scope}.", df_caller_mh());
}

/**
 * 2016-11-10
 * @param string|object $v
 * @param string|object|null $class
 * @param string|\Exception|null  $message [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $class, $message = null) {
	if ($class && df_enable_assertions()) {
		$class = df_cts($class);
		!is_null($v) ?: df_error($message ?: "Expected class: «{$class}», given NULL.");
		is_object($v) ?: df_error($message ?:
			"Expected class: «{$class}», given a value of type «%s».", gettype($v)
		);
		/** @var string $cv */
		$cv = df_cts($v);
		if (!is_a($cv, $class, true)) {
			df_error($message ?: "Expected class: «{$class}», given class: «{$cv}».");
		}
	}
	return $v;
}

/**
 * @param mixed $condition
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert($condition, $message = null) {
	if (df_enable_assertions()) {
		if (!$condition) {
			df_error($message);
		}
	}
}

/**
 * @param array|array(string => int[]) $v
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_array($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsArray($v, $stackLevel + 1);
	}
}

/**
 * @param int|float $v
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_between($v, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsBetween($v, $min, $max, $stackLevel + 1);
	}
}

/**
 * @param bool $v
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_boolean($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsBoolean($v, $stackLevel + 1);
	}
}

/**
 * 2016-08-09
 * @used-by df_map_k()
 * @param mixed $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_callable($v, $message = null) {
	if (df_enable_assertions()) {
		if (!is_callable($v)) {
			df_error($message ?:
				"A variable is expected to be a callable, "
				. "but actually it is a «%s».", gettype($v)
			);
		}
	}
}

/**
 * 2016-08-03
 * @param string $name
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_class_exists($name, $message = null) {
	df_param_string_not_empty($name, 0);
	if (df_enable_assertions()) {
		if (!df_class_exists($name)) {
			df_error($message ?: "The required class «{$name}» does not exist.");
		}
	}
}

/**
 * @param string|int|float $expectedResult
 * @param string|int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_eq($expectedResult, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($expectedResult !== $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение «%s», однако получил значение «%s».'
				, $expectedResult
				, $v
			));
		}
	}
}

/**
 * @param float $v
 * @param int $stackLevel [optional]
 * @return void
 */
function df_assert_float($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsFloat($v, $stackLevel + 1);
	}
}

/**
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_ge($lowBound, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound > $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение не меньше «%s», однако получил значение «%s».'
				, $lowBound
				, $v
			));
		}
	}
}

/**
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_gt($lowBound, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound >= $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение больше «%s», однако получил значение «%s».'
				, $lowBound
				, $v
			));
		}
	}
}

/**
 * @param int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_gt0($v, $message = null) {
	if (df_enable_assertions()) {
		if (0 >= $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал положительное значение, однако получил «%s».', $v
			));
		}
	}
}

/**
 * @param int|float $v
 * @param mixed[] $allowedResults
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_in($v, array $allowedResults, $message = null) {
	if (df_enable_assertions()) {
		if (!in_array($v, $allowedResults, $strict = true)) {
			df_error($message ?: (
				10 >= count($allowedResults)
				? df_sprintf(
					'Проверяющий ожидал значение из множества «%s», однако получил значение «%s».'
					, df_csv_pretty($allowedResults)
					, $v
				)
				: df_sprintf(
					'Проверяющий получил значение «%s», отсутствующее в допустимом множестве значений.'
					, $v
				)
			));
		}
	}
}

/**
 * @param int $v
 * @param int $stackLevel
 * @return void
 */
function df_assert_integer($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsInteger($v, $stackLevel + 1);
	}
}

/**
 * @param string $v
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_iso2($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsIso2($v, $stackLevel + 1);
	}
}

/**
 * @param int|float $highBound
 * @param int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_le($highBound, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound < $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение не больше «%s», однако получил значение «%s».'
				, $highBound
				, $v
			));
		}
	}
}

/**
 * @param int|float $highBound
 * @param int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_lt($highBound, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound <= $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение меньше «%s», однако получил значение «%s».'
				, $highBound
				, $v
			));
		}
	}
}

/**
 * @param string|int|float $notExpectedResult
 * @param string|int|float $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_ne($notExpectedResult, $v, $message = null) {
	if (df_enable_assertions()) {
		if ($notExpectedResult === $v) {
			df_error($message ?: df_sprintf(
				'Проверяющий ожидал значение, отличное от «%s», однако получил именно его.'
				, df_dump($notExpectedResult)
			));
		}
	}
}

/**
 * @param string $v
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_string($v, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertValueIsString($v, $stackLevel + 1);
	}
}

/**
 * @param string $v
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_assert_string_not_empty($v, $stackLevel = 0) {
	df_assert_string($v, $stackLevel + 1);
	if (df_enable_assertions()) {
		Q::assertValueIsString($v, $stackLevel + 1);
		/**
		 * Раньше тут стояло if (!$v), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($v)) {
			Q::raiseErrorVariable(
				$validatorClass = __FUNCTION__
				,$messages = ['Требуется непустая строка, но вместо неё получена пустая.']
				,$stackLevel + 1
			);
		}
	}
}

/**
 * 2016-08-09
 * @param mixed $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws DFE
 */
function df_assert_traversable($v, $message = null) {
	if (df_enable_assertions()) {
		if (!df_check_traversable($v)) {
			df_error($message ?:
				"A variable is expected to be a traversable or an array, "
				. "but actually it is a «%s».", gettype($v)
			);
		}
	}
}

/**
 * @param mixed $v
 * @return bool
 */
function df_bool($v) {
	/**
	 * Хотелось бы ради оптимизации использовать
	 * @see array_flip() + @see isset() вместо @uses in_array(),
	 * однако прямой вызов в лоб @see array_flip() приводит к предупреждению:
	 * «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Более того, следующий тест не проходит:
		$a = array(null => 3, 0 => 4, false => 5);
		$this->assertNotEquals($a[0], $a[false]);
	 * Хотя эти тесты проходят:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	/** @var mixed[] $allowedValuesForNo */
	static $allowedVariantsForNo = [0, '0', 'false', false, null, 'нет', 'no', 'off', ''];
	/** @var mixed[] $allowedVariantsForYes */
	static $allowedVariantsForYes = [1, '1', 'true', true, 'да', 'yes', 'on'];
	/**
	 * Обратите внимание, что здесь использование $strict = true
	 * для функции @uses in_array() обязательно,
	 * иначе любое значение, приводимое к true (например, любая непустая строка),
	 * будет удовлетворять условию.
	 */
	/** @var bool $result */
	if (in_array($v, $allowedVariantsForNo, $strict = true)) {
		$result = false;
	}
	else if (in_array($v, $allowedVariantsForYes, $strict = true)) {
		$result = true;
	}
	else {
		df_error('Система не может распознать «%s» как значение логического типа.', $v);
	}
	return $result;
}

/**
 * @see df_check_traversable()
 * @param mixed $v
 * @return bool
 */
function df_check_array($v) {return \Df\Zf\Validate\ArrayT::s()->isValid($v);}

/**
 * @param int|float $v
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @return bool
 */
function df_check_between($v, $min = null, $max = null) {
	return (new \Df\Zf\Validate\Between([
		'min' => is_null($min) ? PHP_INT_MIN : $min
		,'max' => is_null($max) ? PHP_INT_MAX : $max
		,'inclusive' => true
	]))->isValid($v);
}

/**
 * @param bool $v
 * @return bool
 */
function df_check_boolean($v) {return \Df\Zf\Validate\Boolean::s()->isValid($v);}

/**
 * @param mixed $v
 * @return bool
 */
function df_check_float($v) {return \Df\Zf\Validate\FloatT::s()->isValid($v);}

/**
 * Обратите внимание, что здесь нужно именно «==», а не «===».
 * http://ru2.php.net/manual/en/function.is-int.php#35820
 * @param mixed $v
 * @return bool
 */
function df_check_integer($v) {return is_numeric($v) && ($v == (int)$v);}

/**
 * @param mixed $v
 * @return bool
 */
function df_check_iso2($v) {return \Df\Zf\Validate\StringT\Iso2::s()->isValid($v);}

/**
 * @param string $v
 * @return bool
 */
function df_check_string($v) {return \Df\Zf\Validate\StringT::s()->isValid($v);}

/**
 * @param mixed $v
 * @return bool
 */
function df_check_string_not_empty($v) {
	return \Df\Zf\Validate\StringT\NotEmpty::s()->isValid($v);
}

/**
 * 2016-08-09
 * @see df_assert_traversable()
 * @see df_check_array()
 * @used-by df_map_k()
 * http://stackoverflow.com/questions/31701517#comment59189177_31701556
 * @param mixed $v
 * @return bool
 */
function df_check_traversable($v) {return is_array($v) || $v instanceof \Traversable;}

/**
 * @param mixed $value
 * @return bool
 */
function df_empty_string($value) {return '' === $value;}

/** @return bool */
function df_enable_assertions() {return true;}

/**
 * @param string|string[]|mixed|Exception|Phrase|null $message [optional]
 * @return void
 * @throws DFE
 */
function df_error($message = null) {
	df_header_utf();
	/** @uses df_error_create() */
	throw call_user_func_array('df_error_create', func_get_args());
}

/**
 * 2016-07-31
 * @param string|string[]|mixed|Exception|Phrase|null $message [optional]
 * @return DFE
 */
function df_error_create($message = null) {
	return
		$message instanceof Exception
		? df_ewrap($message)
		: new DFE(
			$message instanceof Phrase
			? $message
			: __(is_array($message) ? implode("\n\n", $message) : df_format(func_get_args()))
		)
	;
}

/**
 * 2016-08-02
 * @param string|string[]|mixed|Exception|Phrase|null $message [optional]
 * @return DFE
 */
function df_error_create_html($message = null) {
	/** @var DFE $result */
	/** @uses df_error_create() */
	$result = call_user_func_array('df_error_create', func_get_args());
	$result->markMessageAsHtml(true);
	return $result;
}

/**
 * 2016-07-31
 * @param string|string[]|mixed|Exception|Phrase|null $message [optional]
 * @return void
 * @throws DFE
 */
function df_error_html($message = null) {
	df_header_utf();
	/** @uses df_error_create_html() */
	throw call_user_func_array('df_error_create_html', func_get_args());
}

/**
 * @param mixed|mixed[] $v
 * @param bool $allowNull [optional]
 * @return float|float[]
 * @throws DFE
 */
function df_float($v, $allowNull = true) {
	/** @var int|int[] $result */
	if (is_array($v)) {
		$result = df_map(__FUNCTION__, $v, $allowNull);
	}
	else {
		/** @var float $result */
		if (is_float($v)) {
			$result = $v;
		}
		else if (is_int($v)) {
			$result = floatval($v);
		}
		else if ($allowNull && (is_null($v) || ('' === $v))) {
			$result = 0.0;
		}
		else {
			/** @var bool $valueIsString */
			$valueIsString = is_string($v);
			static $cache = [];
			/** @var array(string => float) $cache */
			if ($valueIsString && isset($cache[$v])) {
				$result = $cache[$v];
			}
			else {
				if (!\Df\Zf\Validate\StringT\FloatT::s()->isValid($v)) {
					/**
					 * Обратите внимание, что мы намеренно используем @uses df_error(),
					 * а не @see df_error().
					 * Например, модуль доставки «Деловые Линии»
					 * не оповещает разработчика только об исключительных ситуациях
					 * класса @see Exception,
					 * которые порождаются функцией @see df_error().
					 * О сбоях преобразования типов надо оповещать разработчика.
					 */
					df_error(\Df\Zf\Validate\StringT\FloatT::s()->getMessage());
				}
				else {
					df_assert($valueIsString);
					/**
					 * Хотя @see Zend_Validate_Float вполне допускает строки в формате «60,15»
					 * при установке надлежащей локали (например, ru_RU),
					 * @uses floatval для строки «60,15» вернёт значение «60», обрубив дробную часть.
					 * Поэтому заменяем десятичный разделитель на точку.
					 */
					// Обратите внимание, что 368.0 === floatval('368.')
					$result = floatval(str_replace(',', '.', $v));
					$cache[$v] = $result;
				}
			}
		}
	}
	return $result;
}

/**
 * @param mixed $v
 * @param bool $allow0 [optional]
 * @param bool $throw [optional]
 * @return float|null
 * @throws DFE
 */
function df_float_positive($v, $allow0 = false, $throw = true) {
	/** @var float|null $result */
	if (!$throw) {
		try {
			$result = df_float_positive($v, $allow0, true);
		}
		catch (Exception $e) {
			$result = null;
		}
	}
	else {
		/** @var float $result */
		$result = df_float($v, $allow0);
		if ($allow0) {
			df_assert_ge(0.0, $result);
		}
		else {
			df_assert_gt0($result);
		}
	}
	return $result;
}

/**
 * @param mixed $v
 * @return float
 * @throws DFE
 */
function df_float_positive0($v) {return df_float_positive($v, $allow0 = true);}

/**
 * @param mixed|mixed[] $v
 * @param bool $allowNull [optional]
 * @return int|int[]
 * @throws DFE
 */
function df_int($v, $allowNull = true) {
	/** @var int|int[] $result */
	if (is_array($v)) {
		$result = df_map(__FUNCTION__, $v, $allowNull);
	}
	else {
		if (is_int($v)) {
			$result = $v;
		}
		else if (is_bool($v)) {
			$result = $v ? 1 : 0;
		}
		else {
			if ($allowNull && (is_null($v) || ('' === $v))) {
				$result = 0;
			}
			else {
				if (!\Df\Zf\Validate\StringT\IntT::s()->isValid($v)) {
					/**
					 * Обратите внимание, что мы намеренно используем @uses df_error(),
					 * а не @see df_error().
					 * Например, модуль доставки «Деловые Линии»
					 * не оповещает разработчика только об исключительных ситуациях
					 * класса @see Exception,
					 * которые порождаются функцией @see df_error().
					 * О сбоях преобразования типов надо оповещать разработчика.
					 */
					df_error(\Df\Zf\Validate\StringT\IntT::s()->getMessage());
				}
				else {
					$result = (int)$v;
				}
			}
		}
	}
	return $result;
}

/**
 * 2015-04-13
 * В отличие от @see df_int() функция df_int_simple():
 * 1) намеренно не проводит валидацию данных ради ускорения
 * 2) работает только с массивами
 * Ключи массива сохраняются: http://3v4l.org/NHgdK
 * @used-by df_fetch_col_int()
 * @used-by df_products_update()
 * @used-by Df_Catalog_Model_Product_Exporter::applyRule()
 * @used-by Df_Shipping_Rate_Request::getQty()
 * @param mixed[] $values
 * @return int[]
 */
function df_int_simple(array $values) {return array_map('intval', $values);}

/**
 * 2015-03-04
 * Эта функция проверяет, принадлежит ли переменная $variable хотя бы к одному из классов $class.
 * Обратите внимание, что т.к. алгоритм функции использует стандартный оператор instanceof,
 * то переменная $variable может быть не только объектом,
 * а иметь произвольный тип: http://php.net/manual/language.operators.type.php#example-146
 * Если $variable не является объектом, то функция просто вернёт false.
 *
 * Наша функция не загружает при этом $class в память интерпретатора PHP.
 * Если $class ещё не загружен в память интерпретатора PHP, то функция вернёт false.
 * В принципе, это весьма логично!
 * Если проверяемый класс ещё не был загружен в память интерпретатора PHP,
 * то проверяемая переменная $variable гарантированно не может принадлежать данному классу!
 * http://3v4l.org/KguI5
 * Наша функция отличается по сфере применения
 * как от оператора instanceof, так и от функции @see is_a() тем, что:
 * 1) Умеет проводить проверку на приналежность не только одному конкретному классу,
 * а и хотя бы одному из нескольких.
 * 2) @is_a() приводит к предупреждению уровня E_DEPRECATED интерпретатора PHP версий ниже 5.3:
 * http://php.net/manual/function.is-a.php
 * 3) Даже при проверке на принаджежность одному классу код с @see df_is() получается короче,
 * чем при применении instanceof в том случае, когда мы не уверены, существует ли класс
 * и загружен ли уже класс интерпретатором PHP.
 * Например, нам приходилось писать так:
		class_exists('Df_1C_Cml2Controller', $autoload = false)
	&&
		df_state()->getController() instanceof Df_1C_Cml2Controller
 * Или так:
		$controllerClass = 'Df_1C_Cml2Controller';
		$result = df_state()->getController() instanceof $controllerClass;
 * При этом нельзя писать
		df_state()->getController() instanceof 'Df_1C_Cml2Controller'
 * потому что правый операнд instanceof может быть строковой переменной,
 * но не может быть просто строкой!
 * http://php.net/manual/en/language.operators.type.php#example-148
 * @param mixed $variable
 * @param string|string[] $class
 * @return bool
 * @used-by Df_1C_Observer::df_catalog__attribute_set__group_added()
 */
function df_is($variable, $class) {
	/** @var bool $result */
	if (2 < func_num_args()) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		/** @var string[] $classes */
		$class = df_tail($arguments);
	}
	if (!is_array($class)) {
		$result = $variable instanceof $class;
	}
	else {
		$result = false;
		foreach ($class as $classItem) {
			/** @var string $classItem */
			if ($variable instanceof $classItem) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * @param mixed $v
 * @param bool $allow0 [optional]
 * @return int
 * @throws DFE
 */
function df_nat($v, $allow0 = false) {
	/** @var int $result */
	$result = df_int($v, $allow0);
	if ($allow0) {
		df_assert_ge(0, $result);
	}
	else {
		df_assert_gt0($result);
	}
	return $result;
}

/**
 * @param mixed $v
 * @return int
 * @throws DFE
 */
function df_nat0($v) {return df_nat($v, $allow0 = true);}

/**
 * 2016-07-27
 * @see df_should_not_be_here()
 * @param string $method
 * @return void
 * @throws DFE
 */
function df_not_implemented($method) {df_error("The method «{$method}» is not implemented yet.");}

/**
 * @param array $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_array($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsArray($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param int|float  $resultValue
 * @param int $paramOrdering	zero-based
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_between($resultValue, $paramOrdering, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsBetween(
			$resultValue, $paramOrdering, $min, $max, $stackLevel + 1
		);
	}
}

/**
 * @param bool $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_boolean($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsBoolean($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param float $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_float($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsFloat($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param int $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_integer($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsInteger($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param string $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_iso2($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertParamIsIso2($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param string $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_string($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * $method->assertParamIsString($paramValue, $paramOrdering, $stackLevel + 1)
		 */
		/**
		 * 2015-02-16
		 * Раньше здесь стояло просто !is_string($value)
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа и null в строки,
		 * поэтому пусть целые числа и null всегда проходят валидацию как строки.
		 */
		if (!(is_string($paramValue) || is_int($paramValue) || is_null($paramValue))) {
			Q::raiseErrorParam(
				$validatorClass = __FUNCTION__
				,$messages =
					[
						df_sprintf(
							'Требуется строка, но вместо неё получена переменная типа «%s».'
							,gettype($paramValue)
						)
					]
				,$paramOrdering
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param string $paramValue
 * @param int $paramOrdering	zero-based
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_param_string_not_empty($paramValue, $paramOrdering, $stackLevel = 0) {
	df_param_string($paramValue, $paramOrdering, $stackLevel + 1);
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * $method->assertParamIsString($paramValue, $paramOrdering, $stackLevel + 1)
		 *
		 * При второй попытке тут стояло if (!$paramValue), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($paramValue)) {
			Q::raiseErrorParam(
				$validatorClass = __FUNCTION__
				,$messages = ['Требуется непустая строка, но вместо неё получена пустая.']
				,$paramOrdering
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param array $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_array($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsArray($resultValue, $stackLevel + 1);
	}
}

/**
 * @param bool $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_boolean($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsBoolean($resultValue, $stackLevel + 1);
	}
}

/**
 * @param float $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_float($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsFloat($resultValue, $stackLevel + 1);
	}
}

/**
 * @param int $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_integer($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsInteger($resultValue, $stackLevel + 1);
	}
}

/**
 * @param string $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_iso2($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsIso2($resultValue, $stackLevel + 1);
	}
}

/**
 * @param string $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_string($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		// Раньше тут стояло:
		// Q::assertResultIsString($resultValue, $stackLevel + 1)
		if (!is_string($resultValue)) {
			Q::raiseErrorResult(
				$validatorClass = __FUNCTION__
				,$messages = [df_sprintf(
					'Требуется строка, но вместо неё получена переменная типа «%s».'
					, gettype($resultValue)
				)]
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param string $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_string_not_empty($resultValue, $stackLevel = 0) {
	df_result_string($resultValue, $stackLevel + 1);
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * Q::assertResultIsString($resultValue, $stackLevel + 1)
		 *
		 * При второй попытке тут стояло if (!$resultValue), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($resultValue)) {
			Q::raiseErrorResult(
				$validatorClass = __FUNCTION__
				,$messages = ['Требуется непустая строка, но вместо неё получена пустая.']
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param int|float $resultValue
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
 * @return void
 * @throws DFE
 */
function df_result_between($resultValue, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Q::assertResultIsBetween($resultValue, $min, $max, $stackLevel + 1);
	}
}

/**
 * @see df_not_implemented()
 * @return void
 * @throws DFE
 */
function df_should_not_be_here() {df_error_html('The method %s is not allowed to call.', df_caller_mh());}

/**
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
		if (Mage::getIsDeveloperMode()) {
			throw new Exception($errorMessage);
		}
 		else {
			Mage::log($errorMessage, Zend_Log::ERR);
		}
 * @param bool $isOperationSuccessfull [optional]
 * @throws DFE
 */
function df_throw_last_error($isOperationSuccessfull = false) {
	if (!$isOperationSuccessfull) {
		\Df\Qa\Message\Failure\Error::throwLast();
	}
}