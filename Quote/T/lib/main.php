<?php
// 2017-03-17
namespace Df\Core\T\lib;
class main extends \Df\Core\TestCase {
	/** 2017-03-17 */
	function t00() {}

	/** @test 2017-03-17 */
	function t01() {
		$q = df_quote();
		$p = $q->getPayment();
		xdebug_break();
	}
}