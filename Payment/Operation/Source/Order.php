<?php
namespace Df\Payment\Operation\Source;
use Df\Payment\Method as M;
use Df\Payment\Operation\Source;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-04-07
final class Order extends Source {
	/**
	 * 2017-04-08
	 * @used-by \Df\Payment\Operation::__construct()
	 * @param M $m
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-04-08
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @override
	 * @see \Df\Payment\Operation\Source::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	function amount() {return dfp_due($this->_m);}

	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source::ii()
	 * @used-by oq()
	 * @used-by \Df\Payment\Operation::ii()
	 * @return OP
	 */
	function ii() {return dfc($this, function() {return df_ar($this->_m->ii(), OP::class);});}

	/**
	 * 2017-04-08
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by \Df\Payment\Operation::m()
	 * @return M
	 */
	function m() {return $this->_m;}

	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source::oq()
	 * @return O
	 */
	function oq() {return df_order($this->ii());}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by amount()
	 * @used-by ii()
	 * @used-by m()
	 * @var M
	 */
	private $_m;
}