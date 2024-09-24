<?php
use Df\Core\Exception as DFE;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * @used-by df_wishlist_item_candidates()
 * @used-by Dfe\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by Df\Xml\G::importArray()
 * @used-by Dfe\FacebookLogin\Customer::responseJson()
 */
function df_assert_array(array $a):array {return $a;}

/**
 * 2017-02-18
 * @used-by df_clean_keys()
 * @return array(string => mixed)
 * @throws DFE
 */
function df_assert_assoc(array $a):array {return df_is_assoc($a) ? $a : df_error('The array should be associative.');}

/**
 * 2024-05-21 "Implement `df_assert_count()`": https://github.com/mage2pro/core/issues/380
 * 2024-05-23 @deprecated It is unused.
 * @see df_assert_eq()
 */
function df_assert_count($expected, array $a, $m = null):array {/** @var int $v */ return $expected === ($v = count($a)) ? $a :
	df_error($m ?: "The array should have $expected} elements, but it has $v.", ['array' => $a])
;}

/**
 * 2017-01-14 Отныне функция возвращает $v: это позволяет нам значительно сократить код вызова функции.
 * @used-by df_assert_address_type()
 * @used-by df_date_from_timestamp_14()
 * @used-by Dfe\Zoho\App::title()
 * @used-by Dfe\Omise\W\Event\Charge\Complete::isPending()
 * @param string|float|int|bool|null $v
 * @param array(string|float|int|bool|null) $a
 * @param string|T $m [optional]
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