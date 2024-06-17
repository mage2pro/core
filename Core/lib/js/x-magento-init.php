<?php
/**
 * 2019-06-01
 * $m could be:
 * 		1) A module name: «A_B»
 * 		2) A class name: «A\B\C».
 * 		3) An object: it comes down to the case 2 via @see get_class()
 * 		4) `null`.
 * @used-by df_js()
 * @used-by \KingPalm\B2B\Block\RegionJS\Frontend::_toHtml()
 * @used-by vendor/kingpalm/adult/view/frontend/templates/popup.phtml
 * @see df_widget()
 * @param string|object|null $m
 * @param array(string => mixed) $p [optional]
 */
function df_js_x(string $selector, $m, string $script = '', array $p = []):string {return df_tag(
	'script', ['type' => 'text/x-magento-init'], df_json_encode([$selector => [
		df_cc_path(null === $m ? '' : df_module_name($m), $script ?: 'main') => $p
	]])
);}