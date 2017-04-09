<?php
namespace Df\Payment\Operation;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Store\Model\Store;
/**
 * 2017-04-07
 * УРОВЕНЬ 1: исходные данные для операции.
 * УРОВЕНЬ 2: общие алгоритмы операций.
 * УРОВЕНЬ 3: непосредственно сама операция:
 * формирование запроса для конкретной ПС или для группы ПС (Stripe-подобных).
 * @see \Df\Payment\Operation\Source\Order
 * @see \Df\Payment\Operation\Source\Quote
 */
abstract class Source implements \Df\Payment\IMA {
	/**
	 * 2017-04-07
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @see \Df\Payment\Operation\Source\Order::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	abstract function amount();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::id()
	 * @used-by \Df\Payment\Operation::id()
	 * @return string
	 */
	abstract function id();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::ii()
	 * @used-by \Df\Payment\Operation::ii()
	 * @return II|OP|QP
	 */
	abstract function ii();

	/**
	 * 2017-04-07
	 * @see \Df\Payment\Operation\Source\Order::oq()
	 * @used-by cFromDoc()
	 * @used-by store()
	 * @return O|Q
	 */
	abstract function oq();

	/**
	 * 2017-04-08
	 * Converts $a from a sales document currency to the payment currency.
	 * The payment currency is usually set here: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @used-by \Df\Payment\Operation::cFromDoc()
	 * @param float $a
	 * @return float
	 */
	final function cFromDoc($a) {return dfpex_from_doc($a, $this->oq(), $this->m());}

	/**
	 * 2017-04-08
	 * @uses \Magento\Quote\Model\Quote::getStore()
	 * @uses \Magento\Sales\Model\Order::getStore()
	 * @used-by \Df\Payment\Operation::store()
	 * @return Store
	 */
	final function store() {return $this->oq()->getStore();}
}