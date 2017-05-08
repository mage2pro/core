<?php
// 2017-02-13
namespace Df\Core\T\lib;
class url extends \Df\Core\TestCase {
	/** @test 2017-02-13 */
	function t00() {}

	/** 2017-02-13 */
	function t01() {
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill/index/index/') . "\n";
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill/index/index') . "\n";
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill/index/') . "\n";
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill/index') . "\n";
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill/') . "\n";
		echo df_url_trim_index('https://mage2.pro/sandbox/dfe-paymill') . "\n";
		echo df_url_trim_index('https://mage2.pro') . "\n";
		echo df_url_trim_index('/sandbox/dfe-paymill/index/index/') . "\n";
	}
}