<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-04-17
 * @see \Dfe\AllPay\Status
 */
class Status {
	/**
	 * 2017-04-17
	 * @used-by p()
	 * @see \Dfe\AllPay\Status::_p()
	 * @return string|null
	 */
	protected function _p() {return null;}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Status::_p()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-04-17
	 * @used-by s()
	 * @param M $m
	 */
	private function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-04-17
	 * @used-by __construct()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-04-17
	 * @used-by dfp_status()
	 * @param II|OP|QP|O|Q|T $op
	 * @return string|null
	 */
	final static function p($op) {return dfcf(function(OP $op) {
		/** @var OP $m */
		$m = df_ar(dfpm($op), M::class);
		/** @var string $c */
		$c = df_con_hier($m, __CLASS__);
		/** @var self $i */
		$i = new $c($m);
		return $i->_p();
	}, [dfp($op)]);}
}