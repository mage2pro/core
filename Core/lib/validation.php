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
 * 2017-01-14
 * В настоящее время никем не используется.
 * @param mixed $v
 * @return int
 * @throws DFE
 */
function df_01($v) {return df_assert_in(df_int($v), [0, 1]);}

/**
 * 2016-08-27
 * Параметр $caller нам реально нужен,
 * потому что посредством @see debug_backtrace() мы можем получить только имя того класса,
 * где абстрактный метод был объявлен, а нам нужно имя класса текущего объекта
 * (в классе которого абстрактный метод должен был быть перекрыт).
 * @param object $caller
 */
function df_abstract($caller) {
	/**
	 * 2017-11-19
	 * "Improve the «The method \Df\Payment\Block\Info::prepare() should be redefined
	 * by the \Df\Payment\Block\Info class» message": https://github.com/mage2pro/core/issues/56
	 * @var string $callerC
	 */
	if (($callerC = df_cts($caller)) === df_caller_c())  {
		df_error_html(
			"The $callerC class is abstract: you should redefine at least the %s method.", df_caller_ml()
		);
	}
	df_error_html("The method %s should be redefined by the <b>$callerC</b> class.", df_caller_mh());
}

/**
 * 2016-11-10       
 * @used-by \Df\Payment\Choice::f()
 * @param string|object $v
 * @param string|object|null $c [optional]
 * @param string|\Exception|null $m [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $c = null, $m = null) {return dfcf(function($v, $c = null, $m = null) {
	if ($c) {
		$c = df_cts($c);
		!is_null($v) ?: df_error($m ?: "Expected class: «{$c}», given NULL.");
		is_object($v) || is_string($v) ?: df_error($m ?:
			"Expected class: «{$c}», given: a value «%s» of type «%s»."
			,df_dump($v), gettype($v)
		);
		$cv = df_assert_class_exists(df_cts($v)); /** @var string $cv */
		if (!is_a($cv, $c, true)) {
			df_error($m ?: "Expected class: «{$c}», given class: «{$cv}».");
		}
	}
	return $v;
}, func_get_args());}

/**
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @param mixed $cond
 * @param string|\Exception $m [optional]
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
function df_assert_between($v, $min = null, $max = null, $sl = 0) {return
	Q::assertValueIsBetween($v, $min, $max, ++$sl)
;}

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
 * @used-by df_map_k()
 * @param callable $v
 * @param string|\Exception $m [optional]
 * @return callable
 * @throws DFE
 */
function df_assert_callable($v, $m = null) {return is_callable($v) ? $v : df_error($m ?:
	"A callable is required, but got «%s».", gettype($v)
);}

/**
 * 2016-08-03
 * @param string $name
 * @param string|\Exception $m [optional]
 * @return string
 * @throws DFE
 */
function df_assert_class_exists($name, $m = null) {
	df_param_sne($name, 0);
	return df_class_exists($name) ? $name : df_error($m ?: "The required class «{$name}» does not exist.");
}

/**
 * @param string|int|float|bool $expected
 * @param string|int|float|bool $v
 * @param string|\Exception $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_eq($expected, $v, $m = null) {return $expected === $v ? $v : df_error($m ?:
	sprintf("Expected «%s», got «%s».", df_dump($expected), df_dump($v))
);}

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
 * @param string|\Exception $m [optional]
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
 * @param string|\Exception $m [optional]
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
 * @param string|\Exception $m [optional]
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
 * @param string|\Exception $m [optional]
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
 * @param int|float $highBound
 * @param int|float $v
 * @param string|\Exception $m [optional]
 * @return int|float
 * @throws DFE
 */
function df_assert_le($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number <= {$highBound} is expected, but got {$v}."
);}

/**
 * @param int|float $highBound
 * @param int|float $v
 * @param string|\Exception $m [optional]
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
 * @param string|\Exception $m [optional]
 * @return string|int|float|bool
 * @throws DFE
 */
function df_assert_ne($neResult, $v, $m = null) {return $neResult !== $v ? $v : df_error($m ?:
	"The value {$v} is rejected, any others are allowed."
);}

/**
 * 2017-01-14
 * @param mixed $v
 * @param string|\Exception $m [optional]
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
	// Раньше тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	return '' !== strval($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, $ms = [Q::NES], $sl);
}

/**
 * 2016-08-09
 * @param \Traversable|array $v
 * @param string|\Exception $m [optional]
 * @return \Traversable|array
 * @throws DFE
 */
function df_assert_traversable($v, $m = null) {return df_check_traversable($v) ? $v : df_error($m ?:
	"A variable is expected to be a traversable or an array, "
	. "but actually it is a «%s».", gettype($v)
);}

/**
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
	/** @var mixed[] $no */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', ''];
	/** @var mixed[] $yes */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on'];
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
 * Обратите внимание, что здесь нужно именно «==», а не «===».
 * http://php.net/manual/en/function.is-int.php#35820
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param mixed $v
 * @return bool
 */
function df_check_integer($v) {return is_numeric($v) && ($v == (int)$v);}

/**
 * @used-by df_country()
 * @param mixed $v
 * @return bool
 */
function df_check_iso2($v) {return \Df\Zf\Validate\StringT\Iso2::s()->isValid($v);}

/**
 * @used-by df_result_s()
 * @param string $v
 * @return bool
 */
function df_check_s($v) {return \Df\Zf\Validate\StringT::s()->isValid($v);}

/**
 * @used-by \Df\Core\Helper\Text::firstInteger()
 * @param mixed $v
 * @return bool
 */
function df_check_sne($v) {return \Df\Zf\Validate\StringT\NotEmpty::s()->isValid($v);}

/**
 * 2016-08-09
 * @used-by df_assert_traversable()
 * http://stackoverflow.com/questions/31701517#comment59189177_31701556
 * @param \Traversable|array $v
 * @return bool
 */
function df_check_traversable($v) {return is_array($v) || $v instanceof \Traversable;}

/**
 * @param mixed $value
 * @return bool
 */
function df_empty_string($value) {return '' === $value;}

/**
 * @used-by df_address_is_billing()
 * @used-by df_ar()
 * @used-by df_assert()
 * @used-by df_assert_assoc()
 * @used-by df_assert_callable()
 * @used-by df_assert_class_exists()
 * @used-by df_assert_eq()
 * @used-by df_assert_ge()
 * @used-by df_assert_gt()
 * @used-by df_assert_https()
 * @used-by df_assert_in()
 * @used-by df_assert_le()
 * @used-by df_assert_leaf()
 * @used-by df_assert_lt()
 * @used-by df_assert_ne()
 * @used-by df_assert_nef()
 * @used-by df_assert_traversable()
 * @used-by df_bool()
 * @used-by df_call()
 * @used-by df_caller_mm()
 * @used-by df_con_hier_suf()
 * @used-by df_con_hier_suf_ta()
 * @used-by df_con_s()
 * @used-by df_country()
 * @used-by df_country_ctn()
 * @used-by df_customer()
 * @used-by df_date_from_db()
 * @used-by df_extend()
 * @used-by df_fe_m()
 * @used-by df_file_name()
 * @used-by df_float()
 * @used-by df_int()
 * @used-by df_invoice_by_trans()
 * @used-by df_json_decode()
 * @used-by df_leaf_sne()
 * @used-by df_load()
 * @used-by df_module_file()
 * @used-by df_not_implemented()
 * @used-by df_oq()
 * @used-by df_oq_currency_c()
 * @used-by df_oq_sa()
 * @used-by df_oq_shipping_amount()
 * @used-by df_oq_shipping_desc()
 * @used-by df_oqi_is_leaf()
 * @used-by df_oqi_qty()
 * @used-by df_oro_get_list()
 * @used-by df_pad()
 * @used-by df_route()
 * @used-by df_sentry_m()
 * @used-by df_sprintf_strict()
 * @used-by df_string()
 * @used-by df_try()
 * @used-by df_xml_children()
 * @used-by df_xml_parse()
 * @used-by df_xml_throw_last()
 * @used-by dfc()
 * @used-by dfp()
 * @used-by dfp_due()
 * @used-by dfp_oq()
 * @used-by dfp_refund()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Core\Helper\Text::quote()
 * @used-by \Df\Core\O::cacheKeyPerStore()
 * @used-by \Df\Core\R\ConT::generic()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Core\Validator::byName()
 * @used-by \Df\Core\Validator::check()
 * @used-by \Df\Core\Validator::checkProperty()
 * @used-by \Df\Core\Validator::resolve()
 * @used-by \Df\Framework\Form\Element\Text::getValue()
 * @used-by \Df\Geo\Client::onError()
 * @used-by \Df\GingerPaymentsBase\Api::req()
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Df\Payment\Method::getInfoBlockType()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Payment\Settings::key()
 * @used-by \Df\Payment\Source\Identification::get()
 * @used-by \Df\Payment\TID::i2e()
 * @used-by \Df\Payment\TM::tReq()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Df\Payment\W\Nav::p()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Df\PaypalClone\W\Event::validate()
 * @used-by \Df\Qa\Message\Failure\Error::throwLast()
 * @used-by \Df\Qa\Method::throwException()
 * @used-by \Df\Qa\State::methodParameter()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Df\StripeClone\Facade\Charge::cardData()
 * @used-by \Df\Xml\Parser\Entity::descendS()
 * @used-by \Df\Xml\Parser\Entity::descendWithCast()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Df\Xml\X::addChild()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Df\Zf\Filter\StringT\Trim::filter()
 * @used-by \Df\Zf\Filter\StringTrim::_splitUtf8()
 * @used-by \Df\Zf\Validate\Boolean::filter()
 * @used-by \Df\Zf\Validate\FloatT::filter()
 * @used-by \Df\Zf\Validate\IntT::filter()
 * @used-by \Df\Zf\Validate\Nat0::filter()
 * @used-by \Df\Zf\Validate\Nat::filter()
 * @used-by \Dfe\AmazonLogin\Customer::validate()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @used-by \Dfr\Core\Dictionary::e()
 * @used-by \Dfr\Core\Realtime\Dictionary::translate()
 * @param array ...$args
 * @throws DFE
 */
function df_error(...$args) {df_header_utf(); throw df_error_create(...$args);}

/**
 * 2016-07-31
 * @used-by df_error()
 * @used-by df_error_create_html()
 * @used-by \Df\API\Response\Validator::validate()
 * @param string|string[]|mixed|Exception|Phrase|null $m [optional]
 * @return DFE
 */
function df_error_create($m = null) {return
	$m instanceof Exception ? df_ewrap($m) :
		new DFE($m instanceof Phrase ? $m : (
			!$m ? null : (is_array($m) ? implode("\n\n", $m) : (
				df_contains($m, '%1') ? __($m, ...df_tail(func_get_args())) :
					df_format(func_get_args())
			))
		))
;}

/**
 * 2016-08-02
 * @param array ...$args
 * @return DFE
 */
function df_error_create_html(...$args) {return df_error_create(...$args)->markMessageAsHtml(true);}

/**
 * 2016-07-31
 * @used-by df_abstract()
 * @used-by df_caller_f()
 * @used-by df_config_e()
 * @used-by df_should_not_be_here()
 * @used-by \Df\OAuth\App::validateResponse()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @param array ...$args
 * @throws DFE
 */
function df_error_html(...$args) {df_header_utf(); throw df_error_create_html(...$args);}

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
		elseif (is_int($v)) {
			$result = floatval($v);
		}
		elseif ($allowNull && (is_null($v) || ('' === $v))) {
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
		$result = df_float($v, $allow0); /** @var float $result */
		if ($allow0) {
			df_assert_ge(0, $result);
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
 * @used-by dfa_key_int()
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
		elseif (is_bool($v)) {
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
 * @see dfa_key_int()
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
 * @throws DFE
 */
function df_not_implemented($method) {df_error("The method «{$method}» is not implemented yet.");}

/**
 * @param int|float  $v
 * @param int $ord	zero-based
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $sl [optional]
 * @return int|float
 * @throws DFE
 */
function df_param_between($v, $ord, $min = null, $max = null, $sl = 0) {return
	Q::assertParamIsBetween($v, $ord, $min, $max, ++$sl)
;}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param float $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return float
 * @throws DFE
 */
function df_param_float($v, $ord, $sl = 0) {return Q::assertParamIsFloat($v, $ord, ++$sl);}

/**
 * @param int $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return int
 * @throws DFE
 */
function df_param_integer($v, $ord, $sl = 0) {return Q::assertParamIsInteger($v, $ord, ++$sl);}

/**
 * @used-by df_country_ctn()
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_iso2($v, $ord, $sl = 0) {return Q::assertParamIsIso2($v, $ord, ++$sl);}

/**
 * 2017-04-22
 * @used-by df_file_write()
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_s($v, $ord, $sl = 0) {
	$sl++;
	// Раньше тут стояло:
	// $method->assertParamIsString($v, $ord, $sl)
	// При второй попытке тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	return Q::assertValueIsString($v, $sl) ? $v : Q::raiseErrorParam(__FUNCTION__, $ms = [Q::S], $ord, $sl);
}

/**
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_param_sne($v, $ord, $sl = 0) {
	$sl++;
	// Раньше тут стояло:
	// $method->assertParamIsString($v, $ord, $sl)
	// При второй попытке тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	Q::assertValueIsString($v, $sl);
	return '' !== strval($v) ? $v : Q::raiseErrorParam(__FUNCTION__, $ms = [Q::NES], $ord, $sl);
}

/**
 * @used-by df_db_column_describe()
 * @used-by \Df\Xml\X::asCanonicalArray()()
 * @param array $v
 * @param int $sl [optional]
 * @return array
 * @throws DFE
 */
function df_result_array($v, $sl = 0) {return Q::assertResultIsArray($v, ++$sl);}

/**
 * @param float $v
 * @param int $sl [optional]
 * @return float
 * @throws DFE
 */
function df_result_float($v, $sl = 0) {return Q::assertResultIsFloat($v, ++$sl);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param int $v
 * @param int $sl [optional]
 * @return int
 * @throws DFE
 */
function df_result_integer($v, $sl = 0) {return Q::assertResultIsInteger($v, ++$sl);}

/**
 * 2017-01-15
 * В настоящее время никем не используется.
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_result_iso2($v, $sl = 0) {return Q::assertResultIsIso2($v, ++$sl);}

/**
 * Раньше тут стояло: Q::assertResultIsString($v, ++$sl)
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @see df_assert_sne()
 * @see df_param_sne()
 * @see df_result_sne()
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_result_s($v, $sl = 0) {return df_check_s($v) ? $v : Q::raiseErrorResult(
	__FUNCTION__
	,[df_sprintf('A string is required, but got a value of the type «%s».', gettype($v))]
	,++$sl
);}

/**
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function df_result_sne($v, $sl = 0) {
	$sl++;
	df_result_s($v, $sl);
	// Раньше тут стояло:
	// Q::assertResultIsString($v, $sl)
	// При второй попытке тут стояло if (!$v), что тоже неправильно,
	// ибо непустая строка '0' не проходит такую валидацию.
	return '' !== strval($v) ? $v : Q::raiseErrorResult(__FUNCTION__, [Q::NES], $sl);
}

/**
 * @param int|float $v
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $sl [optional]
 * @return int|float
 * @throws DFE
 */
function df_result_between($v, $min = null, $max = null, $sl = 0) {return
	Q::assertResultIsBetween($v, $min, $max, ++$sl)
;}

/**
 * @see df_not_implemented()
 * @throws DFE
 */
function df_should_not_be_here() {df_error_html('The method %s is not allowed to call.', df_caller_mh());}

/**
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
 *		if (Mage::getIsDeveloperMode()) {
 *			throw new Exception($errorMessage);
 *		}
 *		else {
 *			Mage::log($errorMessage, Zend_Log::ERR);
 *		}
 * @param bool $r [optional]
 * @throws DFE
 */
function df_throw_last_error($r = false) {$r ?: \Df\Qa\Message\Failure\Error::throwLast();}