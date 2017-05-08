<?php
// 2016-11-01
namespace Df\Core\T\lib;
class DbColumn extends \Df\Core\TestCase {
	/** @test 2016-11-04 */
	function t00() {}

	/** 2016-11-04 */
	function df_db_column_add_drop() {
		/** @var $name */
		$name = df_uid(4, 'test_');
		df_db_column_add(self::$TABLE, $name, "int(9) unsigned NOT null DEFAULT '0'");
		$this->assertTrue(df_db_column_exists(self::$TABLE, $name));
		/** @var array(string => string|int|null) $info */
		$info = df_db_column_describe(self::$TABLE, $name);
		$this->assertEquals('0', $info['DEFAULT']);
		$this->assertTrue($info['UNSIGNED']);
		df_db_column_drop(self::$TABLE, $name);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $name));
	}

	/** 2016-11-04 */
	function df_db_column_add_drop_2() {
		/** @var $name */
		$name = df_uid(4, 'test_');
		df_db_column_add(self::$TABLE, $name, "varchar(9) NOT null DEFAULT 'test'");
		$this->assertTrue(df_db_column_exists(self::$TABLE, $name));
		/** @var array(string => string|int|null) $info */
		$info = df_db_column_describe(self::$TABLE, $name);
		$this->assertEquals('test', $info['DEFAULT']);
		$this->assertEquals('9', $info['LENGTH']);
		df_db_column_drop(self::$TABLE, $name);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $name));
	}

	/** 2016-11-01 */
	function df_db_column_exists() {
		$this->assertTrue(df_db_column_exists(self::$TABLE, 'customer_group_id'));
		$this->assertFalse(df_db_column_exists(self::$TABLE, 'non_existent_column'));
	}

	/**  2016-11-01 */
	function df_db_column_exists2() {
		$this->expectException(\Exception::class);
		df_db_column_exists('non_existent_table', 'customer_group_id');
	}

	/** 2016-11-04 */
	function df_db_column_rename() {
		/** @var string $from */
		$from = df_uid(4, 'test_');
		/** @var string $to */
		$to = df_uid(4, 'test_');
		df_db_column_add(self::$TABLE, $from);
		$this->assertTrue(df_db_column_exists(self::$TABLE, $from));
		df_db_column_rename(self::$TABLE, $from, $to);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $from));
		$this->assertTrue(df_db_column_exists(self::$TABLE, $to));
		df_db_column_drop(self::$TABLE, $to);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $to));
	}

	/** @var string */
	private static $TABLE = 'customer_group';
}