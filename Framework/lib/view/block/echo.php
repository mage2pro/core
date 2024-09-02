<?php
use Closure as F;
/**
 * 2024-09-02
 * 1) "Implement `df_block_echo()`": https://github.com/mage2pro/core/issues/432
 * 2) A short syntax:
 *		df_block_echo('CabinetsBay_Catalog', 'category/l2/l3', [
 *			'cc' => df_category_children($b->getCurrentCategory())->setOrder('position', 'ASC')
 *		])('filters', 'items')
 * https://3v4l.org/NNHbU
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/l3.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @param string|object|null $c
 */
function df_block_echo($c = null, string $p = '', array $v = []):F {return function(string ...$tt) use($c, $p, $v):void {
	df_map($tt, function(string $t) use($c, $p, $v):void {
		echo df_block_output($c, df_cc_path($p, $t), $v);
	});
};}