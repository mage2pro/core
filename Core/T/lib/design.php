<?php
// 2016-11-29
namespace Df\Core\T\lib;
class design extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2016-11-29
	 */
	public function t01() {$this->assertEquals('Magento/luma', df_theme()->getCode());}

	/**
	 * @test
	 * 2016-11-29
	 */
	public function t02() {
		$theme = df_theme_resolver()->get();
		xdebug_break();
	}
}