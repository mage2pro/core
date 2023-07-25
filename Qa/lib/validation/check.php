<?php
use Magento\Framework\Phrase;
/**
 * 2021-03-22
 * @used-by df_assert_between()
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Api\Data\OrderInterface::afterGetPayment() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/29)
 * @used-by \Mageplaza\Blog\Controller\Router::match() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/190)
 * @param int|string $v
 * @param int|float|null $min
 * @param int|float|null $max
 */
function df_between($v, $min, $max, bool $inclusive = true):bool {return
	$inclusive ? $v >= $min && $v <= $max : $v > $min && $v < $max
;}

/**
 * We need `==` here, not `===`: https://php.net/manual/function.is-int.php#35820
 * @see \Df\Zf\Validate\IntT::isValid()
 * @used-by df_is_nat()
 * @used-by \Df\Core\Text\Regex::matchInt()
 * @param mixed $v
 */
function df_is_int($v):bool {return is_numeric($v) && ($v == (int)$v);}

/**
 * 2020-02-03
 * @see df_nat()
 * @used-by dfp()
 * @used-by \Dfe\AllPay\Method::plan()
 * @param mixed $v
 */
function df_is_nat($v):bool {return df_is_int($v) && 0 < $v;}

/**
 * @used-by df_country()
 * @param mixed $v
 */
function df_check_iso2($v):bool {return \Df\Zf\Validate\StringT\Iso2::s()->isValid($v);}

/**
 * 2015-02-16
 * Раньше здесь стояло просто `is_string($value)`
 * Однако интерпретатор PHP способен неявно и вполне однозначно (без двусмысленностей, как, скажем, с вещественными числами)
 * конвертировать целые числа и `null` в строки,
 * поэтому пусть целые числа и `null` всегда проходят валидацию как строки.
 * 2016-07-01 Добавил `|| $value instanceof Phrase`
 * 2017-01-13 Добавил `|| is_bool($value)`
 * @used-by df_result_s()
 * @param mixed $v
 */
function df_check_s($v):bool {return is_string($v) || is_int($v) || is_null($v) || is_bool($v) || $v instanceof Phrase;}

/** 2022-10-15 @see is_iterable() has been added to PHP 7.1: https://www.php.net/manual/function.is-iterable.php */
if (!function_exists('is_iterable')) {
	/**
	 * 2016-08-09 http://stackoverflow.com/questions/31701517#comment59189177_31701556
	 * @used-by df_assert_traversable()
	 * @used-by df_find()
	 * @used-by dfaf()
	 * @used-by \Df\Qa\Dumper::dumpObject()
	 * @param mixed $v
	 */
	function is_iterable($v):bool {return is_array($v) || $v instanceof Traversable;}
}