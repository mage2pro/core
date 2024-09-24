<?php
/**
 * 2015-02-11
 * Аналог @see array_column() для коллекций.
 * Ещё один аналог: @see \Magento\Framework\Data\Collection::getColumnValues(), но его результат — не ассоциативный.
 * 2016-07-31 При вызове с 2-мя параметрами эта функция идентична функции @see df_each()
 * 2017-07-09
 * Now the function accepts an array as $c.
 * Even in this case it differs from @see array_column():
 * array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * @used-by df_index()
 * @used-by df_product_images_additional()
 * @used-by Wolf\Filter\Block\Navigation::hDropdowns()
 * @param Traversable|array(int|string => _DO|array(string => mixed)) $c
 * @param string|callable $fv
 * @param string|callable|null $fk [optional]
 * @return array(int|string => mixed)
 */
function df_column(iterable $c, $fv, $fk = null):array {return df_map_kr($c, function($k, $v) use($fv, $fk):array {return [
	!$fk ? $k : df_call($v, $fk), df_call($v, $fv)
];});}