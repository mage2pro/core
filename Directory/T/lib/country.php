<?php
// 2017-01-29
namespace Df\Directory\T\lib;
class country extends \Df\Core\TestCase {
	/** 2017-09-07 @test */
	function t00() {}

	/** 2017-09-07 */
	function t01() {print_r(df_json_encode(df_country_codes_allowed()));}
}