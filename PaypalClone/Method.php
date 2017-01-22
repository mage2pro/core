<?php
// 2016-08-27
namespace Df\PaypalClone;
use Df\Payment\PlaceOrder;
use Df\Payment\WebhookF;
use Magento\Sales\Model\Order\Payment\Transaction as T;
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @return string
	 */
	abstract protected function redirectUrl();

	/**
	 * 2016-08-27
	 * Первый параметр — для test, второй — для live.
	 * @used-by url()
	 * @used-by \Df\PaypalClone\Refund::stageNames()
	 * @return string[]
	 */
	abstract public function stageNames();

	/**
	 * @override
	 * @see \Df\Payment\Method::getConfigPaymentAction()
	 * @return string
	 *
	 * 2016-08-27
	 * Сюда мы попадаем только из метода @used-by \Magento\Sales\Model\Order\Payment::place()
	 * причём там наш метод вызывается сразу из двух мест и по-разному.
	 * Умышленно возвращаем null.
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Sales/Model/Order/Payment.php#L334-L355
	 */
	final public function getConfigPaymentAction() {
		/** @var string $id */
		/** @var array(string => mixed) $p */
		list($id, $p) = Charge::p($this);
		/** @var string $url */
		$url = $this->url($this->redirectUrl());
		/** @var array(string => mixed) $request */
		$request = ['params' => $p, 'uri' => $url];
		/**
		 * 2016-07-01
		 * К сожалению, если передавать в качестве результата ассоциативный массив,
		 * то его ключи почему-то теряются. Поэтому запаковываем массив в JSON.
		 */
		$this->iiaSet(PlaceOrder::DATA, df_json_encode($request));
		// 2016-12-20
		if ($this->s()->log()) {
			dfp_report($this, $request, 'request');
		}
		// 2016-05-06
		// Письмо-оповещение о заказе здесь ещё не должно отправляться.
		// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
		$this->o()->setCanSendNewEmailFlag(false);
		// 2016-07-10
		// Сохраняем информацию о транзакции.
		$this->addTransaction($id, $p);
		return null;
	}

	/**
	 * 2016-08-31
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final public function requestP($k = null) {return dfak($this, function() {return
		df_trans_raw_details($this->transParent())
	;}, $k);}

	/**
	 * 2016-07-18
	 * @used-by \Df\PaypalClone\BlockInfo::responseF()
	 * @param string|null $key [optional]
	 * @return Webhook|string|null
	 */
	public function responseF($key = null) {return $this->response($key);}

	/**
	 * 2016-07-18
	 * @used-by \Df\PaypalClone\BlockInfo::responseL()
	 * @param string|null $key [optional]
	 * @return Webhook|string|null
	 */
	public function responseL($key = null) {return $this->response($key);}

	/**
	 * 2016-08-27
	 * @used-by getConfigPaymentAction()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$args [optional]
	 * @return string
	 */
	final public function url($url, $test = null, ...$args) {return df_url_staged(
		!is_null($test) ? $test : $this->s()->test(), $url, $this->stageNames(), ...$args
	);}

	/**
	 * 2016-07-10
	 * @used-by getConfigPaymentAction()
	 * @param string $id
	 * @param array(string => mixed) $data
	 */
	private function addTransaction($id, array $data) {
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
	 * @param string|null $key [optional]
	 * @return Webhook|string|null
	 */
	private function response($key = null) {
		/** @var Webhook|null $result */
		$result = dfc($this, function($f) {return
 			call_user_func($f, $this->responses())
		;}, [dfa(['L' => 'df_last', 'F' => 'df_first'], substr(df_caller_f(), -1))]);
		return !$result || is_null($key) ? $result : $result->req($key);
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
			df_usort($this->transParent()->getChildTransactions(), function(T $a, T $b) {return
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
	final public static function e2i($externalId) {return self::codeS() . "-$externalId";}
}