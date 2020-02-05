<?php
/**
 * Обратите внимание, что здесь нужно именно «==», а не «===».
 * http://php.net/manual/en/function.is-int.php#35820
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
 * @see \Df\Zf\Validate\Nat::isValid()
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
 * @used-by df_desc()
 * @used-by df_leaf()
 * @used-by df_leaf_sne()
 * @used-by sift_prefix()
 * @used-by \Df\Zf\Validate\StringT\Iso2::filter()
 * @param mixed $v
 * @return bool
 */
function df_es($v) {return '' === $v;}