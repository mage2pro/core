<?php
/**
 * 2019-08-26
 * @used-by vendor/inkifi/map/view/frontend/templates/create.phtml
 * 1) An usage example:
 * https://github.com/inkifi/map/blob/0.0.5/view/frontend/templates/create.phtml#L11
 * https://github.com/inkifi/map/blob/0.0.5/view/frontend/web/js/create.js
 * 2) Another example: https://github.com/inkifi/map/blob/0.0.6/view/frontend/templates/create.phtml#L1-L2
 * @see df_js_x()
 * @see df_widget()
 * @param array(string => mixed) $p [optional]
 */
function df_js_c(string $s, array $p = []):string {return df_js(null, 'Magento_Ui/js/core/app', ['components' => [
	$s => ['component' => $s] + $p
]]);}