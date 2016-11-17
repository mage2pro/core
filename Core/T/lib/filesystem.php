<?php
// 2016-11-17
namespace Df\Core\T\lib;
class filesystem extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2016-11-04
	 */
	public function t00() {}

	/**
	 * @test
	 * 2016-11-04
	 */
	public function t01() {
		echo df_module_path_test_data(\Dfe\Omise\Source\Prefill::s(), 'charge.capture.json')
	;}
}