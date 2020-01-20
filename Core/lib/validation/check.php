<?php

/**
 * Обратите внимание, что здесь нужно именно «==», а не «===».
 * http://php.net/manual/en/function.is-int.php#35820
 * 2017-01-15 В настоящее время никем не используется.
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