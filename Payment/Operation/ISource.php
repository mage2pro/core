<?php
namespace Df\Payment\Operation;
use Df\Payment\Method as M;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-04-07
 * УРОВЕНЬ 1: исходные данные для операции.
 * УРОВЕНЬ 2: общие алгоритмы операций.
 * УРОВЕНЬ 3: непосредственно сама операция:
 * формирование запроса для конкретной ПС или для группы ПС (Stripe-подобных).
 * @see \Df\Payment\Operation\Source\Order
 * @see \Df\Payment\Operation\Source\Quote
 */
interface ISource {
	/**
	 * 2017-04-07
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @return float
	 */
	function amount();

	/**
	 * 2017-04-07
	 * @return II|OP|QP
	 */
	function ii();

	/**
	 * 2017-04-07
	 * @return M
	 */
	function m();

	/**
	 * 2017-04-07
	 * @return O|Q
	 */
	function oq();
}