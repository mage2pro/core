<?php
/**
 * 2016-07-31
 * Возвращает повторяющиеся элементы исходного массива (не повторяя их). https://3v4l.org/YEf5r
 * В алгоритме пользуемся тем, что @uses array_unique() сохраняет ключи исходного массива.
 * 2020-01-29 dfa_repeated([1,2,2,2,2,3,3,3,4]) => [2,3]
 * 2022-10-15
 * 1) The example above correctly works in 7.2 ≥ PHP ≤ 8.2: https://3v4l.org/Ds194
 * 2) If flags is @see SORT_STRING (it is by default),
 * formerly array has been copied and non-unique elements have been removed (without packing the array afterwards),
 * but now a new array is built by adding the unique elements. This can result in different numeric indexes.
 * https://php.net/manual/function.array-unique.php#refsect1-function.array-unique-changelog
 * @used-by Df\Config\Backend\ArrayT::processI()
 */
function dfa_repeated(array $a):array {return array_values(array_unique(array_diff_key($a, array_unique($a))));}