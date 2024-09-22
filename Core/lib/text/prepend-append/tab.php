<?php
/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_tab_multiline()
 * @param string|string[] ...$a
 * @return string|string[]|array(string => string)
 */
function df_tab(...$a) {return df_call_a($a, function(string $s):string {return "\t" . $s;});}