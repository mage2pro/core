<?php
// 2016-11-29
namespace Df\Core\T\lib;
class design extends \Df\Core\TestCase {
	/** @test 2016-11-04 */
	function t00() {}

	/** 2016-11-29 */
	function t01() {$this->assertEquals('Magento/luma', df_theme()->getCode());}
}