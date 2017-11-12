<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Df\Payment\W\Event;
use Df\Payment\W\F;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
// 2017-03-05
final class TM {
	/**
	 * 2017-03-25
	 * 2017-11-11
	 * The @see \Magento\Sales\Api\Data\TransactionInterface::TYPE_AUTH constant
	 * is absent in Magento < 2.1.0,
	 * but is present as @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Transaction.php#L37
	 * https://github.com/magento/magento2/blob/2.0.17/app/code/Magento/Sales/Api/Data/TransactionInterface.php
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @return bool
	 */
	function confirmed() {return dfc($this, function() {/** @var T|null|false $t */ return
		df_order($this->_ii)->hasInvoices()
		// 2017-03-27
		// Тот случай, когда платёж только авторизован.
		// Magento не создаёт в этом случае invoice
		// @todo Может, надо просто создавать invoice при авторизации платежа?
		|| ($t = $this->tReq(false)) && (T::TYPE_AUTH === $t->getTxnType())
		/**
		 * 2017-08-31
		 * It is for modules with a redirection (PayPal clones).
		 * @see \Dfe\PostFinance\W\Event::ttCurrent()
		 * https://github.com/mage2pro/postfinance/blob/0.1.7/W/Event.php#L35
		 */
		|| df_find(function(T $t) {return T::TYPE_AUTH === $t->getTxnType();}, $this->tResponses())
	;});}

	/**
	 * 2017-03-05
	 * 2017-03-22
	 * Возвращает параметры первичного запроса магазина к ПС.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::option()
	 * @used-by \Df\Payment\Choice::req()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @used-by \Dfe\SecurePay\Signer\Response::values()
	 * @param string|string[]|null $k [optional]
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
	 * @used-by confirmed()
	 * @used-by req()
	 * @used-by tResponses()
	 * @used-by \Df\Payment\Block\Info::confirmed()
	 * @used-by \Df\Payment\Block\Info::siID()
	 * @param bool $throw [optional]
	 * @return T|null
	 */
	function tReq($throw = true) {return dfc($this, function($throw) {return
		df_trans_by_payment($this->_ii, 'asc') ?: (!$throw ? null : df_error(
			"The {$this->_m->titleB()} request transaction is absent."
		))
	;}, [$throw]);}

	/**
	 * 2017-03-29 It returns a response to the primary request to the PSP API.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::res0()
	 * @used-by \Df\Payment\Choice::res0()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
	 * @used-by \Dfe\Moip\Block\Info\Boleto::rCustomerAccount()
	 * @used-by \Dfe\Moip\Block\Info\Boleto::url()
	 * @used-by \Dfe\Square\Block\Info::prepare()
	 * @param string|string[]|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	function res0($k = null) {return dfak($this, function() {return df_trd(
		$this->tReq(), M::IIA_TR_RESPONSE
	);}, $k);}

	/**
	 * 2016-07-18
	 * @used-by df_tmf()
	 * @used-by \Df\Payment\Choice::responseF()
	 * @used-by \Dfe\SecurePay\Refund::process()
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	function responseF(...$k) {return $this->response(...$k);}

	/**
	 * 2016-07-18
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	function responseL(...$k) {return $this->response(...$k);}

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
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	private function response(...$k) {
		/** @var Event|null $ev */
		$ev = dfc($this, function($f) {return
 			call_user_func($f, $this->responses())
		;}, [dfa(['L' => 'df_last', 'F' => 'df_first'], substr(df_caller_f(), -1))]);
		return/** @var int $c */!($c = count($k)) || !$ev ? $ev : $ev->r(1 < $c ? $k : df_first($k));
	}

	/**
	 * 2016-07-18
	 * @used-by response()
	 * @return Event[]
	 */
	private function responses() {return dfc($this, function() {return array_map(function(T $t) {return
		F::s($this->_m, df_trd($t))->e()
	;}, $this->tResponses());});}

	/**
	 * 2017-08-31
	 * @used-by confirmed()
	 * @used-by responses()
	 * @return T[]
	 */
	private function tResponses() {return dfc($this, function() {return
		!($p = $this->tReq(false)) ? [] : df_sort($p->getChildTransactions())
	;});}

	/**
	 * 2017-03-05
	 * @used-by __construct()
	 * @used-by confirmed()
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