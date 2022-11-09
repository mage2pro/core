<?php
namespace Df\Payment\Operation\Source;
use Magento\Sales\Model\Order\Creditmemo as CM;
# 2017-04-08
final class Creditmemo extends Order {
	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source\Order::id()
	 * @used-by \Df\Payment\Operation::id()
	 */
	function id():string {return df_result_sne($this->cm()->getIncrementId());}

	/**
	 * 2017-04-08
	 * @used-by self::id()
	 */
	private function cm():CM {return dfc($this, function() {return df_ar($this->ii()->getCreditmemo(), CM::class);});}
}