<?php
// 2016-11-01
namespace Df\Core\lib;
class db extends \PHPUnit\Framework\TestCase {
	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists() {
		//\Mage_Core_Model_Resource_Setup::applyAllUpdates();
		$this->assertTrue(df_db_column_exists('customer/customer_group', 'customer_group_id'));
		$this->assertFalse(df_db_column_exists('customer/customer_group', 'customer_group_id1'));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists2() {
		$this->expectException(\Exception::class);
		df_db_column_exists('customer/customer_group1', 'customer_group_id1');
	}
}