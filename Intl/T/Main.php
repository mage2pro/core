<?php
// 2017-06-17
namespace Df\Intl\T;
class Main extends \Df\Core\TestCase {
	/** 2017-06-17 */
	function t00() {}

	/** 2017-06-17 @test */
	function t01() {print_r(df_json_encode([
		__('Dashboard')->__toString()
	]));}
}