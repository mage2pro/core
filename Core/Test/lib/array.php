<?php
namespace Df\Core\Test\lib;
# 2017-07-13
class arrayT extends \Df\Core\TestCase {
	/** 2017-07-13 */
	function t00():void {}

	/** 2020-02-05 @test */
	function t01_dfak_transform():void {
		$a = ['promotions' => [['description' => 'Test']]];
		echo df_dump(dfak_prefix($a, '$', true));
	}
}