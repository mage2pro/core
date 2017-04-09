<?php
namespace Df\Payment\Operation\Source;
use Df\Payment\Method as M;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
// 2017-04-07
final class Quote extends \Df\Payment\Operation\Source {
	/**
	 * 2017-04-08
	 * @param M $m
	 * @param IQ|Q $q
	 */
	function __construct(M $m, IQ $q) {$this->_m = $m; $this->_q = $q;}

	/**
	 * 2017-04-08
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @override
	 * @see \Df\Payment\Operation\Source::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	function amount() {return dfp_due($this->_m, $this->_q);}

	/**
	 * 2017-04-09
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * @override
	 * @see \Df\Payment\Operation\Source::id()
	 * @used-by \Df\Payment\Operation::id()
	 * @return int
	 */
	function id() {return df_assert($this->_q->getId());}

	/**
	 * 2017-04-09
	 * @override
	 * @see \Df\Payment\Operation\Source::ii()
	 * @used-by oq()
	 * @used-by \Df\Payment\Operation::ii()
	 * @return QP
	 */
	function ii() {return dfc($this, function() {return df_ar(dfp($this->_q), QP::class);});}

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
	 * @used-by \Df\Payment\Operation\Source::cFromDoc()
	 * @used-by \Df\Payment\Operation\Source::currencyC()
	 * @used-by \Df\Payment\Operation\Source::store()
	 * @return Q
	 */
	function oq() {return $this->_q;}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by m()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @var M
	 */
	private $_q;
}