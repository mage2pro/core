<?php
// 2017-04-01
namespace Df\Core\T\lib;
class module extends \Df\Core\TestCase {
	/**
	 * 2017-04-01
	 */
	function t00() {}

	/** @test 2017-04-01 */
	function t01() {
		$ml = df_module_list();
		xdebug_break();
	}
}