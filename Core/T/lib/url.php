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

	/** 2017-08-18 */
	function t02() {echo sprintf("«%s»\n", df_url_backend_ns());}
}