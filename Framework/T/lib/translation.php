<?php
// 2017-02-09
namespace Df\Framework\T\lib;
class translation extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2017-02-09
	 */
	function t01() {$s = '歐付寶 Rónán allPay Федюк [] --'; print_r([
		'df_fs_name' => df_fs_name($s)
		,'df_translit' => df_translit($s)
		,'df_translit_url' => df_translit_url($s)
		,'df_translit_url_lc' => df_translit_url_lc($s)
		,'transliterator_transliterate' => transliterator_transliterate('Any-Latin; Latin-ASCII', $s)
	]);}
}