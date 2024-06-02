<?php
/**
 * 2016-03-25 http://stackoverflow.com/a/1320156
 * @used-by df_action_is()
 * @used-by df_c()
 * @used-by df_cc()
 * @used-by df_cc_br()
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @used-by df_cc_path()
 * @used-by df_cc_path_t()
 * @used-by df_cc_s()
 * @used-by df_ccc()
 * @used-by df_class_replace_last()
 * @used-by df_contains()
 * @used-by df_csv_pretty()
 * @used-by df_explode_class_camel()
 * @used-by df_explode_xpath()
 * @used-by df_mail()
 * @used-by df_string_clean()
 * @used-by dfa_unpack()
 * @used-by \Df\Payment\Block\Info::rPDF()
 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
 */
function dfa_flatten(array $a):array {
	$r = []; /** @var mixed[] $r */
	array_walk_recursive($a, function($a) use(&$r):void {$r[]= $a;});
	return $r;
}