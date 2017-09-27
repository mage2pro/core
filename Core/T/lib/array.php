<?php
namespace Df\Core\T\lib;
// 2017-07-13
class arrayT extends \Df\Core\TestCase {
	/** @test 2017-07-13 */
	function t00() {}

	/** 2017-07-13 */
	function t01() {$a = []; print_r(array_shift($a));}

	/** 2017-07-13 */
	function t02() {
		$a = ['a' => ['b' => ['c' => 3, 'd' => 5]]];
		dfa_deep_unset($a, 'a/b/c');
		print_r($a);
		dfa_deep_unset($a, 'a/dummy');
		print_r($a);
		dfa_deep_unset($a, 'dummy');
		print_r($a);
		dfa_deep_unset($a, 'a/b/d');
		print_r($a);
	}
}