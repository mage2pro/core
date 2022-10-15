<?php
/**
 * 2021-03-22
 * @used-by df_assert_between()
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Api\Data\OrderInterface::afterGetPayment() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/29)
 * @used-by \Mageplaza\Blog\Controller\Router::match() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/190)
 * @param int|string $v
 * @param int|float|null $min
 * @param int|float|null $max
 * @param bool $inclusive [optional]
 * @return bool
 */
function df_between($v, $min, $max, $inclusive = true) {return $inclusive ? $v >= $min && $v <= $max : $v > $min && $v < $max;}

/**
 * We need `==` here, not `===`: https://php.net/manual/function.is-int.php#35820
 * @see \Df\Zf\Validate\IntT::isValid()
 * @used-by df_is_nat()
 * @used-by \Df\Core\Text\Regex::matchInt()
 * @param mixed $v
 * @return bool
 */
function df_is_int($v) {return is_numeric($v) && ($v == (int)$v);}

/**
 * 2020-02-03
 * @see df_nat()
 * @used-by dfp()
 * @used-by \Dfe\AllPay\Method::plan()
 * @param mixed $v
 * @return bool
 */
function df_is_nat($v) {return df_is_int($v) && 0 < $v;}

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
 * @used-by df_desc()
 * @used-by df_leaf()
 * @used-by df_leaf_sne()
 * @used-by sift_prefix()
 * @param mixed $v
 * @return bool
 */
function df_es($v) {return '' === $v;}

/** 2022-10-15 @see is_iterable() has been added to PHP 7.1: https://www.php.net/manual/function.is-iterable.php */
if (!function_exists('is_iterable')) {
	/**
	 * 2016-08-09 http://stackoverflow.com/questions/31701517#comment59189177_31701556
	 * @used-by dfaf()
	 * @used-by df_assert_traversable()
	 * @param \Traversable|array $v
	 * @return bool
	 */
	function is_iterable($v) {return is_array($v) || $v instanceof \Traversable;}
}