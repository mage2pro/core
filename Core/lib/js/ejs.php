<?php
/**
 * 2015-02-17
 * 2020-01-16 It formats $v as a value which can be used in the `var name = <?= df_ejs($v); ?>;` expression.
 * @used-by df_js_data()
 * @used-by royalwholesalecandy.com: app/code/MGS/Mmegamenu/view/adminhtml/templates/category.phtml
 * @used-by vendor/mage2pro/facebook/view/frontend/templates/init.phtml
 * @param mixed $v
 */
function df_ejs($v):string {return !is_string($v) ? df_json_encode($v) : df_quote_single(str_replace(
	"'", '\u0027', df_trim(json_encode($v), '"')
));}