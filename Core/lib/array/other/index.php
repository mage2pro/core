<?php
use Magento\Framework\DataObject as _DO;
/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * https://3v4l.org/AOWmO
 * @used-by df_mvars()
 * @used-by Df\Config\A::get()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, iterable $a):array {return array_combine(df_column($a, $k), df_ita($a));}