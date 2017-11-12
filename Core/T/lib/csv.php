<?php
// 2017-06-23
namespace Df\Core\T\lib;
class csv extends \Df\Core\TestCase {
	/** @test 2017-06-23 */
	function t00() {}

	/** 2017-06-23 */
	function t01() {
		$l = 'Test,Тест'; /** @var string $l */
		$d = '"'; /** @var string $d */
		if (!df_starts_with($l, $d)) {
			$p = mb_strpos($l, ',');
			$l = $d . mb_substr($l, 0, $p) . $d . mb_substr($l, $p);
		}
		if (!df_ends_with($l, $d)) {
			$p = mb_strrpos($l, ',');
			$l = mb_substr($l, 0, $p + 1) . $d . mb_substr($l, $p + 1) . $d;
		}
		print_r($l);
	}
}