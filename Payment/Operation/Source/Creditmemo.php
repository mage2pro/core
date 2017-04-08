<?php
namespace Df\Payment\Operation\Source;
use Magento\Sales\Model\Order\Creditmemo as CM;
// 2017-04-08
final class Creditmemo extends Order {
	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source\Creditmemo::id()
	 * @used-by \Df\Payment\Operation::id()
	 * @return string
	 */
	function id() {return df_result_sne($this->cm()->getIncrementId());}

	/**
	 * 2017-04-08
	 * @return CM
	 */
	private function cm() {return dfc($this, function() {return df_ar(
		$this->ii()->getCreditmemo(), CM::class
	);});}
}