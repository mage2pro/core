<?php
// 2016-11-17
namespace Df\Core\T\lib;
use Df\Payment\Method as M;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
class trans extends \Df\Core\TestCase {
	/**
	 * 2016-11-17
	 */
	public function t00() {}

	/**
	 * @test
	 * 2016-11-17
	 */
	public function t01() {
		/** @var T $t */
		$t = df_trans(310);
		/** @var OP $op */
		$op = dfp_by_trans($t);
		/** @var bool $isTest */
		$isTest = dfp_is_test($op);
		$this->assertTrue(is_bool($isTest));
	}
}