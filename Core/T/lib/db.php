<?php
// 2016-11-01
namespace Df\Core\T\lib;
class db extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists() {
		$this->assertTrue(df_db_column_exists('customer_group', 'customer_group_id'));
		$this->assertFalse(df_db_column_exists('customer_group', 'non_existent_column'));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists2() {
		$this->expectException(\Exception::class);
		df_db_column_exists('non_existent_table', 'customer_group_id');
	}
}