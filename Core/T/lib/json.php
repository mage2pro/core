<?php
// 2017-08-22
namespace Df\Core\T\lib;
class json extends \Df\Core\TestCase {
	/** @test 2017-08-22 */
	function t00() {}

	/** 2017-08-22 */
	function t01() {print_r(df_json_encode(['A' => 1, 'B' => 2, 'a' => 3]));}
}