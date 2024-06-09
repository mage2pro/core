<?php
/**
 * 2017-06-21
 * @used-by dfe_modules()
 * @return string[]
 */
function df_modules_p(string $p):array {return dfcf(function($p) {return df_sort(array_filter(
	df_module_list()->getNames(), function(string $m) use($p):bool {return df_starts_with($m, $p);}
));}, [$p]);}