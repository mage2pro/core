<?php
use Closure as F;
/**
 * 2024-09-02 "Implement `df_block_echo()`": https://github.com/mage2pro/core/issues/432
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/l3.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @param string|object|null $c
 */
function df_block_echo($c = null, string $p = '', array $v = []):F {return function(string ...$tt) use($c, $p, $v):void {
	df_map($tt, function(string $t) use($c, $p, $v):void {
		echo df_block_output($c, df_cc_path($p, $t), $v);
	});
};}