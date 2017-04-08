<?php
namespace Df\Payment\Operation\Source;
use Df\Payment\Method as M;
use Df\Payment\Operation\Source;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-04-07
/** @see \Df\Payment\Operation\Source\Creditmemo */
class Order extends Source {
	/**
	 * 2017-04-08
	 * @used-by \Df\Payment\Operation::__construct()
	 * @used-by \Dfe\SecurePay\Refund::p()
	 * @param M $m
	 * 2016-09-05
	 * Размер транзакции в валюте платёжных транзакций,
	 * которая настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 */
	final function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-04-08
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @override
	 * @see \Df\Payment\Operation\Source::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	final function amount() {return dfp_due($this->_m);}

	/**
	 * 2016-09-06
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * @override
	 * @see \Df\Payment\Operation\Source::id()
	 * @used-by \Df\Payment\Operation::id()
	 * @see \Df\Payment\Operation\Source\Creditmemo::id()
	 * @return string
	 */
	function id() {return df_result_sne($this->oq()->getIncrementId());}

	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source::ii()
	 * @used-by oq()
	 * @used-by \Df\Payment\Operation::ii()
	 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
	 * @return OP
	 */
	final function ii() {return dfc($this, function() {return df_ar($this->_m->ii(), OP::class);});}

	/**
	 * 2017-04-08
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by \Df\Payment\Operation::m()
	 * @used-by \Df\Payment\Operation\Source::cFromDoc()
	 * @return M
	 */
	final function m() {return $this->_m;}

	/**
	 * 2017-04-08
	 * @override
	 * @see \Df\Payment\Operation\Source::oq()
	 * @used-by id()
	 * @return O
	 */
	final function oq() {return df_order($this->ii());}

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