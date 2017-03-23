<?php
namespace Df\Payment;
use Df\Payment\Method as M;
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
	 * 2017-03-22
	 * Возвращает параметры первичного запроса магазина к ПС.
	 * Пока используется только модулем SecurePay для подписи ответа на оповещения.
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\SecurePay\Signer\Response::values()
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	function req($k = null) {return dfak($this, function() {return df_trd(
		$this->tReq(), M::IIA_TR_REQUEST
	);}, $k);}

	/**
	 * 2016-07-13
	 * 2016-07-28
	 * Транзакции может не быть в случае каких-то сбоев.
	 * Решил не падать из-за этого, потому что мы можем попасть сюда
	 * в невинном сценарии отображения таблицы заказов
	 * (в контексте рисования колонки с названиями способов оплаты).
	 * @used-by req()
	 * @used-by responses()
	 * @used-by \Df\Payment\Block\Info::isWait()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @param bool $throw [optional]
	 * @return T|null
	 */
	function tReq($throw = true) {return dfc($this, function($throw) {return
		df_trans_by_payment($this->_ii, 'asc') ?: (!$throw ? null : df_error(
			"The {$this->_m->titleB()} request transaction is absent."
		))
	;}, [$throw]);}

	/**
	 * 2016-07-18
	 * @used-by \Df\PaypalClone\Method::responseF()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseF($k = null) {return $this->response($k);}

	/**
	 * 2016-07-18
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseL($k = null) {return $this->response($k);}

	/**
	 * 2017-03-05
	 * @used-by s()
	 * @param M $m
	 */
	private function __construct(M $m) {$this->_ii = $m->getInfoInstance(); $this->_m = $m;}

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
	;}, !($p = $this->tReq(false)) ? [] : df_sort($p->getChildTransactions()));});}

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
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-23
	 * @used-by df_tm()
	 * @param string|object $m
	 * @return self
	 */
	static function s($m) {return dfcf(function(M $m) {return new self($m);}, [dfpm($m)]);}
}