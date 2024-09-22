<?php
/**
 * 2015-04-05
 * 2022-10-14 @see get_debug_type() has been added to PHP 8: https://php.net/manual/function.get-debug-type.php
 * @see df_dump()
 * @used-by df_ar()
 * @used-by df_assert_gd()
 * @used-by df_oq_currency_c()
 * @used-by df_order()
 * @used-by df_result_s()
 * @used-by dfaf()
 * @used-by dfpex_args()
 * @used-by \Df\Xml\G2::addAttributes()
 * @param mixed $v
 */
function df_type($v):string {return is_object($v) ? sprintf('an object: `%s`', get_class($v), df_dump($v)) : (is_array($v)
	? (10 < ($c = count($v)) ? "an array of $c elements" : 'an array: ' . df_dump($v))
	/** 2020-02-04 We should not use @see df_desc() here */
	: (is_null($v) ? '`null`' : sprintf('«%s» (%s)', df_string($v), gettype($v)))
);}