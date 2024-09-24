<?php
/**
 * 2019-01-28
 * @used-by Dfe\Vantiv\API\Client::_construct()
 * 2024-03-05
 * 1) https://3v4l.org/C3qrh
 * 2) The previous code (`dfa_seq`): https://github.com/mage2pro/core/blob/10.6.9/Core/lib/array/main.php#L214-L231
 * @param array(int|string => mixed) $a
 * @return mixed|null
 */
function dfa_try(array $a, string ...$k) {return df_first(dfa($a, $k));}