<?php
// 2017-02-18
namespace Df\Core\T\lib;
class date extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2017-02-18
	 */
	function t01() {
		echo df_date_from_db('1982-07-08 00:00:00')->toString('Y-MM-dd');
	}
}