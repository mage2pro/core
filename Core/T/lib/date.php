<?php
// 2017-02-18
namespace Df\Core\T\lib;
use Zend_Locale as ZL;
class date extends \Df\Core\TestCase {
	/** 2017-02-18 */
	function t01() {echo df_date_from_db('1982-07-08 00:00:00')->toString('Y-MM-dd');}

	/** @test 2017-02-18 */
	function t02() {
		ZL::findLocale('test');
		echo df_num_days(df_date_parse('2017-03-08', false));
	}
}