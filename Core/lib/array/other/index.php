<?php
/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * @used-by df_mvars()
 * @used-by \Df\Config\A::get()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, $a):array {return array_combine(df_column($a, $k), df_ita($a));}