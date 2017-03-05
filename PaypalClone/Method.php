<?php
namespace Df\PaypalClone;
use Df\Payment\WebhookF;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-08-27
 * @see \Df\PaypalClone\Method\Normal
 * @see \Dfe\Klarna\Method
 */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-08-31  
	 * @used-by \Df\PaypalClone\Refund::requestP()
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final function requestP($k = null) {return dfak($this, function() {return
		df_trans_raw_details($this->transParent())
	;}, $k);}

	/**
	 * 2016-07-18  
	 * @final 
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @used-by \Df\PaypalClone\BlockInfo::responseF()
	 * @param string|null $k [optional]
	 * @return Webhook|string|null
	 */
	function responseF($k = null) {return $this->response($k);}

	/**
	 * 2016-07-18
	 * @final
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @used-by \Df\PaypalClone\BlockInfo::responseL()
	 * @param string|null $k [optional]
	 * @return Webhook|string|null
	 */
	function responseL($k = null) {return $this->response($k);}

	/**
	 * 2016-07-10
	 * @used-by \Df\PaypalClone\Method\Normal::getConfigPaymentAction()
	 * @param string $id
	 * @param array(string => mixed) $data
	 */
	final protected function addTransaction($id, array $data) {
		$this->ii()->setTransactionId(self::e2i($id));
		$this->iiaSetTR($data);
		//$this->ii()->setIsTransactionClosed(false);
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственный транзакция без специального назначения,
		 * и поэтому мы можем безопасно его использовать
		 * для сохранения информации о нашем запросе к платёжной системе.
		 */
		$this->ii()->addTransaction(T::TYPE_PAYMENT);
	}

	/**
	 * 2016-07-18
	 * @used-by responseF()
	 * @used-by responseL()
	 * @param string|null $k [optional]
	 * @return Webhook|string|null
	 */
	private function response($k = null) {
		/** @var Webhook|null $result */
		$result = dfc($this, function($f) {return
 			call_user_func($f, $this->responses())
		;}, [dfa(['L' => 'df_last', 'F' => 'df_first'], substr(df_caller_f(), -1))]);
		return !$result || is_null($k) ? $result : $result->req($k);
	}

	/**
	 * 2016-07-18
	 * @return Webhook[]
	 */
	private function responses() {return dfc($this, function() {
		/** @var string $fc */
		$fc = df_con_heir($this, WebhookF::class);
		return array_map(function(T $t) use($fc) {
			/** @var WebhookF $f */
			$f = new $fc($this, df_trans_raw_details($t));
			return $f->i();
		}, $this->transChildren());
	});}

	/**
	 * 2016-07-13
	 * @return T[]
	 */
	private function transChildren() {return dfc($this, function() {return
		!$this->transParent() ? [] :
			df_sort($this->transParent()->getChildTransactions(), function(T $a, T $b) {return
				$a->getId() - $b->getId();
			})
	;});}

	/**
	 * 2016-07-13
	 * 2016-07-28
	 * Транзакции может не быть в случае каких-то сбоев.
	 * Решил не падать из-за этого, потому что мы можем попасть сюда
	 * в невинном сценарии отображения таблицы заказов
	 * (в контексте рисования колонки с названиями способов оплаты).
	 * @return T|null
	 */
	private function transParent() {return dfc($this, function() {return
		df_trans_by_payment_first($this->ii())
	;});}

	/**
	 * 2016-07-10
	 * 2017-01-05
	 * Преобразует в глобальный внутренний идентификатор транзакции:
	 * 1) Внешний идентификатор транзакции.
	 * Это случай, когда идентификатор формируется платёжной системой.
	 * 2) Локальный внутренний идентификатор транзакции.
	 * Это случай, когда мы сами сформировали идентификатор запроса к платёжной системе.
	 * Мы намеренно передавали идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * Такой идентификатор формируется в методах:
	 * @see \Df\PaypalClone\Charge::requestId()
	 * @see \Dfe\AllPay\Charge::requestId()
	 *
	 * Глобальный внутренний идентификатор отличается наличием приставки «<имя модуля>-».
	 *
	 * @used-by \Df\PaypalClone\Method::addTransaction()
	 * @used-by \Df\PaypalClone\Webhook::e2i()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @param string $externalId
	 * @return string
	 */
	final static function e2i($externalId) {return self::codeS() . "-$externalId";}
}