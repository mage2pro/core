<?php
// 2017-02-18
namespace Df\Core\T\lib;
use Zend_Date as ZD;
use Zend_Locale as ZL;
class date extends \Df\Core\TestCase {
	/** @test 2017-02-18 */
	function t00() {}

	/** 2017-02-18 */
	function t01() {print_r(df_date_from_db('1982-07-08 00:00:00')->toString('Y-MM-dd'));}

	/** 2017-02-18 */
	function t02() {
		ZL::findLocale('test');
		print_r(df_num_days(df_date_parse('2017-03-08', false)));
	}

	/** 2017-09-05 */
	function t03() {
		$d = ZD::now(); /** @var ZD $d */
		/**
		 * 2017-09-05
		 * Эта операция конвертирует время из пояса @see date_default_timezone_get() в пояс аргумента.
		 * Пример:
		 * $dateS = «2016/07/28 11:35:03»,
		 * date_default_timezone_get() = «Asia/Taipei»
		 * пояс аргумента = «Europe/Moscow»
		 * $result->toString() = 'Jul 28, 2016 6:35:03 AM'
		 */
		$d->setTimezone('Europe/Moscow');
		$d->addDay(45);
		print_r($d->toString('y-MM-ddTHH:mm:ss'));
	}
}