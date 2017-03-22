<?php
namespace Df\PaypalClone;
use Df\Payment\W\Event;
use Df\Payment\W\F;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
// 2017-03-05
final class TM {
	/**
	 * 2017-03-05
	 * @used-by \Df\PaypalClone\Method::tm()
	 * @param Method $m
	 */
	function __construct(Method $m) {$this->_ii = $m->getInfoInstance(); $this->_m = $m;}

	/**
	 * 2017-03-05
	 * @used-by \Df\PaypalClone\Refund::requestP()
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	function requestP($k = null) {return dfak($this, function() {return df_trd($this->p());}, $k);}

	/**
	 * 2016-07-18
	 * @used-by \Df\PaypalClone\Method::responseF()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseF($k = null) {return $this->response($k);}

	/**
	 * 2016-07-18
	 * @used-by \Df\PaypalClone\Method::responseL()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseL($k = null) {return $this->response($k);}

	/**
	 * 2016-07-13
	 * 2016-07-28
	 * Транзакции может не быть в случае каких-то сбоев.
	 * Решил не падать из-за этого, потому что мы можем попасть сюда
	 * в невинном сценарии отображения таблицы заказов
	 * (в контексте рисования колонки с названиями способов оплаты).
	 * @used-by requestP()
	 * @used-by responses()
	 * @return T|null
	 */
	private function p() {return dfc($this, function() {return df_trans_by_payment_first($this->_ii);});}

	/**
	 * 2016-07-18
	 * @used-by responseF()
	 * @used-by responseL()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	private function response($k = null) {
		/** @var Event|null $result */
		$result = dfc($this, function($f) {return
 			call_user_func($f, $this->responses())
		;}, [dfa(['L' => 'df_last', 'F' => 'df_first'], substr(df_caller_f(), -1))]);
		return !$result || is_null($k) ? $result : $result->r($k);
	}

	/**
	 * 2016-07-18
	 * @used-by response()
	 * @return Event[]
	 */
	private function responses() {return dfc($this, function() {return array_map(function(T $t) {return
		F::s($this->_m, df_trd($t))->e()
	;}, !($p = $this->p()) ? [] : df_sort($p->getChildTransactions()));});}

	/**
	 * 2017-03-05
	 * @used-by __construct()
	 * @used-by parent()
	 * @var II|I|OP|QP
	 */
	private $_ii;

	/**
	 * 2017-03-05
	 * @used-by __construct()
	 * @used-by responses()
	 * @var Method
	 */
	private $_m;
}