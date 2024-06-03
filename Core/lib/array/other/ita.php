<?php
/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see Traversable, но и массив.
 * 2022-10-18
 * @uses iterator_to_array() allows an array as the first argument since PHP 8.2:
 * https://php.net/manual/migration82.other-changes.php#migration82.other-changes.functions.spl
 * 2023-07-26 "Replace `array|Traversable` with `iterable`": https://github.com/mage2pro/core/issues/255
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * @used-by df_filter_f()
 * @used-by df_index()
 * @used-by df_map()
 * @used-by dfa_select_ordered()
 * @used-by dfak_transform()
 * @used-by \Df\Qa\Dumper::dumpObject()
 */
function df_ita(iterable $i):array {return is_array($i) ? $i : iterator_to_array($i);}