<?php
# 2017-06-17
namespace Df\Intl\Test;
class Main extends \Df\Core\TestCase {
	/** 2017-06-17 */
	function t00():void {}

	/** 2017-06-17 @test */
	function t01():void {print_r(df_json_encode([__('Dashboard')->__toString()]));}
}