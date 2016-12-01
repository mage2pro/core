<?php
// 2016-12-01
namespace Df\Customer\T\lib;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\ResourceModel\Customer as CR;
class customer extends \Df\Core\TestCase {
	/**
	 * 2016-12-01
	 */
	public function t00() {}

	/**
	 * @test
	 * 2016-12-01
	 */
	public function t01() {
		/** @var CR $r */
		$cr = df_customer_resource();
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_db_from($cr, $cr->getEntityIdField());
		xdebug_break();
	}
}