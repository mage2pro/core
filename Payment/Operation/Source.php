<?php
namespace Df\Payment\Operation;
use Df\Payment\Operation\Source\Order as SourceO;
use Df\Payment\Operation\Source\Quote as SourceQ;
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
 * @see SourceO
 * @see SourceQ
 */
abstract class Source implements \Df\Payment\IMA {
	/**
	 * 2017-04-07
	 * Размер транзакции в платёжной валюте: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * @see SourceO::amount()
	 * @used-by \Df\Payment\Operation::amount()
	 * @return float|null
	 */
	abstract function amount();

	/**
	 * 2017-04-07
	 * @see SourceO::ii()
	 * @return II|OP|QP
	 */
	abstract function ii();

	/**
	 * 2017-04-07
	 * @see SourceO::oq()
	 * @return O|Q
	 */
	abstract function oq();
}