<?php
/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_tab_multiline()
 * @used-by \Df\Typography\Css::render()
 * @param string|string[] ...$a
 * @return string|string[]|array(string => string)
 */
function df_tab(...$a) {return df_call_a($a, function(string $s):string {return "\t" . $s;});}

/**
 * @used-by \Df\Core\Html\Tag::content()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Qa\Dumper::dumpObject()
 */
function df_tab_multiline(string $s):string {return df_cc_n(df_tab(df_explode_n($s)));}