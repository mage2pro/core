<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\PlaceOrder;
use Magento\Sales\Model\Order\Payment\Transaction as T;
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @return string
	 */
	abstract protected function redirectUrl();

	/**
	 * 2016-08-27
	 * Первый параметр — для test, второй — для live.
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @used-by \Df\Payment\R\Refund::stageNames()
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
		$this->saveRequest($id, $url, $p);
		return null;
	}

	/**
	 * 2016-08-31
	 * @param string|null $key [optional]
	 * @return array(string => string)|string|null
	 */
	public function requestP($key = null) {
		/** @var array(string => string) $result */
		$result = dfc($this, function() {
			/** @var array(string => string) $result */
			$result = df_trans_raw_details($this->transParent());
			unset($result[self::TRANSACTION_PARAM__URL]);
			return $result;
		});
		return is_null($key) ? $result : dfa($result, $key);
	}

	/**
	 * 2016-07-18
	 * @used-by \Df\Payment\R\BlockInfo::responseF()
	 * @param string|null $key [optional]
	 * @return Response|string|null
	 */
	public function responseF($key = null) {return $this->response($key);}

	/**
	 * 2016-07-18
	 * @used-by \Df\Payment\R\BlockInfo::responseL()
	 * @param string|null $key [optional]
	 * @return Response|string|null
	 */
	public function responseL($key = null) {return $this->response($key);}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$params [optional]
	 * @return string
	 */
	final public function url($url, $test = null, ...$params) {return
		$this->url2($url, $test, $this->stageNames(), $params)
	;}

	/**
	 * 2016-08-31
	 * @used-by \Df\Payment\R\Refund::url()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param string[] $stageNames
	 * @param mixed[] ...$params [optional]
	 * @return string
	 */
	final public function url2($url, $test = null, array $stageNames, ...$params) {
		$test = !is_null($test) ? $test : $this->s()->test();
		/** @var string $stage */
		$stage = call_user_func($test ? 'df_first' : 'df_last', $stageNames);
		return vsprintf(str_replace('{stage}', $stage, $url), df_args($params));
	}

	/**
	 * 2016-07-18
	 * @param string|null $key [optional]
	 * @return Response|string|null
	 */
	private function response($key = null) {
		/** @var Response|null $result */
		$result = dfc($this, function($f) {return
 			call_user_func($f, $this->responses())
		;}, [dfa(['L' => 'df_last', 'F' => 'df_first'], substr(df_caller_f(), -1))]);
		return !$result || is_null($key) ? $result : $result[$key];
	}

	/**
	 * 2016-07-18
	 * @return Response[]
	 */
	private function responses() {return dfc($this, function() {
		/** @var string $class */
		$class = df_ar(df_con($this, 'Response'), Response::class);
		return array_map(function(T $t) use($class) {return
			/** @uses \Df\Payment\Webhook\Response::i() */
			call_user_func([$class, 'i'], df_trans_raw_details($t))
		;}, $this->transChildren());
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
}