<?php
# 2016-11-17
namespace Df\Payment\Test;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
class trans extends \Df\Core\TestCase {
	/**
	 * @test
	 * 2016-11-17
	 */
	function t01():void {
		$t = df_trans(310); /** @var T $t */
		$op = dfp($t); /** @var OP $op */
		$isTest = dfp_is_test($op); /** @var bool $isTest */
		$this->assertTrue(is_bool($isTest));
	}
}