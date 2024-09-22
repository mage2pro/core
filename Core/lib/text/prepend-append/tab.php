<?php
/**
 * 2022-11-26, 2024-09-23
 * @used-by df_tab()
 * @used-by \Df\Core\Html\Tag::content()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Typography\Css::render()
 * @param string|string[] $v
 */
function df_tab($v):string {return !is_array($v) ? df_tab(df_explode_n($v)) :
	df_cc_n(df_map($v, function(string $s):string {return "\t$s";}))
;}