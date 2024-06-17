<?php
/**
 * 2018-05-21
 * 2020-01-16
 * @used-by vendor/inkifi/map/view/frontend/templates/create.phtml:
 *		echo df_js_data('inkifiMap', ['keys' => [
 *			'google' => $s->keyGoogle(), 'mapBox' => $s->keyMapBox(), 'openCage' => $s->keyOpenCage()
 *		]]);
 * https://github.com/inkifi/map/blob/0.1.5/view/frontend/templates/create.phtml#L4-L6
 * @param array(string => mixed) $v
 */
function df_js_data(string $n, array $v):string {return df_tag('script', ['type' => 'text/javascript'], sprintf(
	"window.%s = %s;", df_cc('.', $n), df_ejs($v)
));}