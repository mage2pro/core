<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Df\Zf\Validate\StringT\FloatT;
use Df\Zf\Validate\StringT\IntT;
use Exception as E;

/**
 * 2017-01-14
 * В настоящее время никем не используется.
 * @param mixed $v
 * @return int
 * @throws DFE
 */
function df_01($v) {return df_assert_in(df_int($v), [0, 1]);}

/**
 * 2016-11-10       
 * @used-by \Df\Payment\Choice::f()
 * @param string|object $v
 * @param string|object|null $c [optional]
 * @param string|E|null $m [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $c = null, $m = null) {return dfcf(function($v, $c = null, $m = null) {
	if ($c) {
		$c = df_cts($c);
		!is_null($v) ?: df_error($m ?: "Expected class: «{$c}», given `null`.");
		is_object($v) || is_string($v) ?: df_error($m ?: "Expected class: «{$c}», given: %s.", df_type($v));
		$cv = df_assert_class_exists(df_cts($v)); /** @var string $cv */
		if (!is_a($cv, $c, true)) {
			df_error($m ?: "Expected class: «{$c}», given class: «{$cv}».");
		}
	}
	return $v;
}, func_get_args());}

/**
 * 2019-12-14
 * If you do not want the exception to be logged via @see df_bt(),
 * then you can pass an empty string (instead of `null`) as the second argument:
 * @see \Df\Core\Exception::__construct():
 *		if (is_null($m)) {
 *			$m = __($prev ? df_ets($prev) : 'No message');
 *			// 2017-02-20 To facilite the «No message» diagnostics.
 *			if (!$prev) {
 *				df_bt();
 *			}
 *		}
 * https://github.com/mage2pro/core/blob/5.5.7/Core/Exception.php#L61-L67
 * @used-by df_assert_qty_supported()
 * @used-by df_config_field()
 * @used-by df_module_dir()
 * @used-by df_oqi_amount()
 * @used-by dfaf()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by \Inkifi\Mediaclip\Event::oi()
 * @used-by \RWCandy\Captcha\Assert::email()
 * @used-by \RWCandy\Captcha\Assert::name()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @used-by \RWCandy\Captcha\Observer\CustomerSaveBefore::execute()
 * @param mixed $cond
 * @param string|E|null $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert($cond, $m = null) {return $cond ?: df_error($m);}

/**
 * @param array $v
 * @param int $sl [optional]
 * @return array
 * @throws DFE
 */
function df_assert_array($v, $sl = 0) {return Q::assertValueIsArray($v, ++$sl);}

/**
 * 2017-02-18
 * @param array $a
 * @return array(string => mixed)
 * @throws DFE
 */
function df_assert_assoc(array $a) {return df_is_assoc($a) ? $a : df_error('The array should be associative.');}

/**
 * @param int|float $v
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $sl [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_between($v, $min = null, $max = null, $sl = 0) {return Q::assertValueIsBetween(
	$v, $min, $max, ++$sl
);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param bool $v
 * @param int $sl [optional]
 * @return bool
 * @throws DFE
 */
function df_assert_boolean($v, $sl = 0) {return Q::assertValueIsBoolean($v, ++$sl);}

/**
 * 2016-08-09
 * @used-by dfaf()
 * @param callable $v
 * @param string|E $m [optional]
 * @return callable
 * @throws DFE
 */
function df_assert_callable($v, $m = null) {return is_callable($v) ? $v : df_error($m ?:
	"A callable is required, but got «%s».", gettype($v)
);}

/**
 * 2016-08-03
 * @param string $c
 * @param string|E $m [optional]
 * @return string
 * @throws DFE
 */
function df_assert_class_exists($c, $m = null) {
	df_param_sne($c, 0);
	return df_class_exists($c) ? $c : df_error($m ?: "The required class «{$c}» does not exist.");
}

/**
 * @param string|int|float|bool $expected
 * @param string|int|float|bool $v
 * @param string|E $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_eq($expected, $v, $m = null) {return $expected === $v ? $v : df_error($m ?: sprintf(
	"Expected «%s», got «%s».", df_dump($expected), df_dump($v)
));}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param float $v
 * @param int $sl [optional]
 * @return float
 */
function df_assert_float($v, $sl = 0) {return Q::assertValueIsFloat($v, ++$sl);}

/**
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|E $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_ge($lowBound, $v, $m = null) {return $lowBound <= $v ? $v : df_error($m ?:
	"A number >= {$lowBound} is expected, but got {$v}."
);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param int|float $lowBound
 * @param int|float $v
 * @param string|E $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_gt($lowBound, $v, $m = null) {return $lowBound <= $v ? $v : df_error($m ?:
	"A number > {$lowBound} is expected, but got {$v}."
);}

/**
 * @used-by df_float_positive()
 * @used-by df_nat()
 * @used-by \Df\Customer\Settings\BillingAddress::restore()
 * @used-by \Dfe\CurrencyFormat\FE::onFormInitialized()
 * @param int|float $v
 * @param string|E $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_gt0($v, $m = null) {return 0 <= $v ? $v : df_error($m ?:
	"A positive number is expected, but got {$v}."
);};

/**
 * 2017-01-14
 * Отныне функция возвращает $v: это позволяет нам значительно сократить код вызова функции.
 * @param string|float|int|bool|null $v
 * @param array(string|float|int|bool|null) $a
 * @param string|E $m [optional]
 * @return string|float|int|bool|null
 * @throws DFE
 */
function df_assert_in($v, array $a, $m = null) {
	if (!in_array($v, $a, true)) {
		df_error($m ?: "The value «{$v}» is rejected" . (
			10 >= count($a)
			? sprintf(". Allowed values: «%s».", df_csv_pretty($a))
			: " because it is absent in the list of allowed values."
		));
	}
	return $v;
}

/**
 * @param int $v
 * @param int $sl
 * @return int
 */
function df_assert_integer($v, $sl = 0) {return Q::assertValueIsInteger($v, ++$sl);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_assert_iso2($v, $sl = 0) {return Q::assertValueIsIso2($v, ++$sl);}

/**
 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
 * @param int|float $highBound
 * @param int|float $v
 * @param string|E $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_le($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number <= {$highBound} is expected, but got {$v}."
);}

/**
 * @used-by \RWCandy\Captcha\Assert::name()
 * @param int|float $highBound
 * @param int|float $v
 * @param string|E $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_lt($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number < {$highBound} is expected, but got {$v}."
);}

/**
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @param string|int|float|bool $neResult
 * @param string|int|float|bool $v
 * @param string|E $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_ne($neResult, $v, $m = null) {return $neResult !== $v ? $v : df_error($m ?:
	"The value {$v} is rejected, any others are allowed."
);}

/**
 * 2017-01-14       
 * @used-by \Df\Qa\Trace\Frame::context()
 * @param mixed $v
 * @param string|E $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert_nef($v, $m = null) {return false !== $v ? $v : df_error($m ?:
	"The «false» value is rejected, any others are allowed."
);}

/**
 * @used-by df_currency_base()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\StripeClone\Payer::newCard()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfr\Core\Realtime\Dictionary::handleForController()
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_assert_sne($v, $sl = 0) {
	$sl++;
	Q::assertValueIsString($v, $sl);
	// The previous code `if (!$v)` was wrong because it rejected the '0' string.
	return '' !== strval($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, $ms = [Q::NES], $sl);
}

/**
 * 2016-08-09
 * @used-by dfaf()
 * @param \Traversable|array $v
 * @param string|E $m [optional]
 * @return \Traversable|array
 * @throws DFE
 */
function df_assert_traversable($v, $m = null) {return df_check_traversable($v) ? $v : df_error($m ?:
	'A variable is expected to be a traversable or an array, ' . 'but actually it is %s.', df_type($v)
);}

/**
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Payment\Comment\Description::getCommentText()
 * @used-by \Df\Shipping\Settings::enable()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
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
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Хотя эти тесты проходят:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Обратите внимание, что здесь использование $strict = true
	 * для функции @uses in_array() обязательно,
	 * иначе любое значение, приводимое к true (например, любая непустая строка),
	 * будет удовлетворять условию.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true :
		df_error('A boolean value is expected, but got «%s».', df_dump($v))
	);
}

/**
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Controller\Adminhtml\Product\MassUpdateProducts::beforeExecute()
 * @param mixed|mixed[] $v
 * @param bool $allowNull [optional]
 * @return float|float[]
 * @throws DFE
 */
function df_float($v, $allowNull = true) {/** @var int|int[] $r */
	if (is_array($v)) {
		$r = df_map(__FUNCTION__, $v, $allowNull);
	}
	else {
		if (is_float($v)) {
			$r = $v;
		}
		elseif (is_int($v)) {
			$r = floatval($v);
		}
		elseif ($allowNull && (is_null($v) || ('' === $v))) {
			$r = 0.0;
		}
		else {
			$valueIsString = is_string($v); /** @var bool $valueIsString */
			static $cache = []; /** @var array(string => float) $cache */
			if ($valueIsString && isset($cache[$v])) {
				$r = $cache[$v];
			}
			else {
				if (!FloatT::s()->isValid($v)) {
					/**
					 * Обратите внимание, что мы намеренно используем @uses df_error(),
					 * а не @see df_error().
					 * Например, модуль доставки «Деловые Линии»
					 * не оповещает разработчика только об исключительных ситуациях
					 * класса @see Exception,
					 * которые порождаются функцией @see df_error().
					 * О сбоях преобразования типов надо оповещать разработчика.
					 */
					df_error(FloatT::s()->getMessage());
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
					$r = floatval(str_replace(',', '.', $v));
					$cache[$v] = $r;
				}
			}
		}
	}
	return $r;
}

/**
 * @param mixed $v
 * @param bool $allow0 [optional]
 * @param bool $throw [optional]
 * @return float|null
 * @throws DFE
 */
function df_float_positive($v, $allow0 = false, $throw = true) {/** @var float|null $r */
	if (!$throw) {
		try {$r = df_float_positive($v, $allow0, true);}
		catch (E $e) {$r = null;}
	}
	else {
		$r = df_float($v, $allow0);
		$allow0 ? df_assert_ge(0, $r) : df_assert_gt0($r);
	}
	return $r;
}

/**
 * @param mixed $v
 * @return float
 * @throws DFE
 */
function df_float_positive0($v) {return df_float_positive($v, $allow0 = true);}

/**
 * @see df_is_int()
 * @used-by df_product_id()
 * @used-by dfa_key_int()
 * @used-by \Dfe\Color\Image::palette()
 * @used-by \Inkifi\Pwinty\API\Entity\Shipment::items()
 * @param mixed|mixed[] $v
 * @param bool $allowNull [optional]
 * @return int|int[]
 * @throws DFE
 */
function df_int($v, $allowNull = true) {/** @var int|int[] $r */
	if (is_array($v)) {
		$r = df_map(__FUNCTION__, $v, $allowNull);
	}
	else {
		if (is_int($v)) {
			$r = $v;
		}
		elseif (is_bool($v)) {
			$r = $v ? 1 : 0;
		}
		else {
			if ($allowNull && (is_null($v) || ('' === $v))) {
				$r = 0;
			}
			else {
				if (!IntT::s()->isValid($v)) {
					df_error(IntT::s()->getMessage());
				}
				else {
					$r = (int)$v;
				}
			}
		}
	}
	return $r;
}

/**
 * 2015-04-13
 * В отличие от @see df_int() функция df_int_simple():
 * 1) намеренно не проводит валидацию данных ради ускорения
 * 2) работает только с массивами
 * Ключи массива сохраняются: http://3v4l.org/NHgdK
 * @see dfa_key_int()
 * @used-by df_fetch_col_int()
 * @used-by df_products_update()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
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
 *		class_exists('Df_1C_Cml2Controller', $autoload = false)
 *	&&
 *		df_state()->getController() instanceof Df_1C_Cml2Controller
 * Или так:
 *		$controllerClass = 'Df_1C_Cml2Controller';
 *		$result = df_state()->getController() instanceof $controllerClass;
 * При этом нельзя писать
 *		df_state()->getController() instanceof 'Df_1C_Cml2Controller'
 * потому что правый операнд instanceof может быть строковой переменной,
 * но не может быть просто строкой!
 * https://php.net/manual/language.operators.type.php#example-148
 * @param mixed $variable
 * @param string|string[] $class
 * @return bool
 */
function df_is($variable, $class) {/** @var bool $r */
	if (2 < func_num_args()) {
		$arguments = func_get_args(); /** @var mixed[] $arguments */
		$class = df_tail($arguments); /** @var string[] $classes */
	}
	if (!is_array($class)) {
		$r = $variable instanceof $class;
	}
	else {
		$r = false;
		foreach ($class as $classItem) {/** @var string $classItem */
			if ($variable instanceof $classItem) {
				$r = true;
				break;
			}
		}
	}
	return $r;
}

/**
 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
 * @used-by \Justuno\M2\Controller\Cart\Add::product()
 * @see df_is_nat()
 * @param mixed $v
 * @param bool $allow0 [optional]
 * @return int
 * @throws DFE
 */
function df_nat($v, $allow0 = false) {/** @var int $r */
	$r = df_int($v, $allow0);
	$allow0 ? df_assert_ge(0, $r) : df_assert_gt0($r);
	return $r;
}

/**
 * @param mixed $v
 * @return int
 * @throws DFE
 */
function df_nat0($v) {return df_nat($v, $allow0 = true);}