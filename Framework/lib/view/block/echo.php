<?php
use Closure as F;
/**
 * 2024-09-02
 * 1) "Implement `df_block_echo()`": https://github.com/mage2pro/core/issues/432
 * 2) A short syntax:
 *		df_block_echo($b, 'category/l2/l3', [
 *			'cc' => df_category_children($b->getCurrentCategory())->setOrder('position', 'ASC')
 *		])('filters', 'items')
 * https://github.com/cabinetsbay/catalog/blob/0.1.7/view/frontend/templates/category/l2/l3.phtml#L7-L10
 * https://3v4l.org/NNHbU
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/l3.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @param string|object|null $m
 */
function df_block_echo($m = null, string $p = '', array $v = []):F {return function(string ...$tt) use($m, $p, $v):void {
	df_map($tt, function(string $t) use($m, $p, $v):void {
		echo df_block_output($m, df_cc_path($p, $t), $v);
	});
};}