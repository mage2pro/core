<?php
/**
 * 2015-08-24
 * 2022-10-31 @deprecated It is unused.
 */
function df_contains_ci(string $haystack, string $n):bool {return df_contains(mb_strtoupper($haystack), mb_strtoupper($n));}