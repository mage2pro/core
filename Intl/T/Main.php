<?php
// 2017-06-17
namespace Df\Intl\T;
class Main extends \Df\Core\TestCase {
	/** 2017-06-17 */
	function t00() {}

	/** 2017-06-17 @test */
	function t01() {echo df_json_encode_pretty([
		__('Dashboard')->__toString()
	]);}
}