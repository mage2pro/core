<?php
namespace Df\Payment\W;
use Df\Core\Exception as DFE;
use Df\Framework\Controller\Result\Text;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Df\Payment\W\Exception\Critical;
use Df\Payment\W\Exception\NotForUs;
use Df\Sales\Model\Order as DfOrder;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Handler {
	/**
	 * 2017-01-01
	 * @used-by handle()
	 * @see \Df\PaypalClone\W\Confirmation::_handle()
	 * @see \Df\StripeClone\W\Handler::_handle()
	 * @return void
	 */
	abstract protected function _handle();

	/**
	 * 2017-01-05
	 * Преобразует в глобальный внутренний идентификатор родительской транзакции:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Это случай Stripe-подобных платёжных систем: у них идентификатор формируется платёжной системой.
	 *
	 * 2) Локальный внутренний идентификатор родительской транзакции.
	 * Это случай PayPal-подобных платёжных систем, когда мы сами ранее сформировали
	 * идентификатор запроса к платёжной системе (этот запрос и является родительской транзакцией).
	 * Мы намеренно передавали идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * Такой идентификатор формируется в методах:
	 * @see \Df\PaypalClone\Charge::requestId()
	 * @see \Dfe\AllPay\Charge::requestId()
	 * Глобальный внутренний идентификатор отличается наличием приставки «<имя модуля>-».
	 * @used-by parentId()
	 * @see \Df\PaypalClone\W\Handler::adaptParentId()
	 * @see \Df\StripeClone\W\Handler::adaptParentId()
	 * @param string $id
	 * @return string
	 */
	abstract protected function adaptParentId($id);

	/**
	 * 2016-07-20
	 * 2017-01-04
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @used-by initTransaction()
	 * @see \Df\PaypalClone\W\Handler::id()
	 * @see \Df\StripeClone\W\Handler::id()
	 * @see \Dfe\AllPay\W\Handler\Offline::id()
	 * @return string
	 */
	abstract protected function id();

	/**
	 * 2017-01-05
	 * Возвращает наш внутренний идентификатор родительской транзакции в неком «сыром» формате.
	 * В настоящее время этот «сырой» формат бывает 2-х видов:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Так происходит для Stripe-подобных модулей.
	 * На основе этого идентификатора мы:
	 *     1.1) вычисляем идентификатор родительской транзакции
	 *     (посредством прибавления окончания «-<тип родительской транзакции>»)
	 *     1.2) создаём идентификатор текущей транзакции
	 *     (аналогично, посредством прибавления окончания «-<тип текущей транзакции>»).
	 * @see \Df\StripeClone\W\Handler::parentIdRawKey()
	 *
	 * 2) Переданный нами ранее платёжной системе
	 * наш внутренний идентификатор родительской транзакции (т.е., запроса к платёжой системе)
	 * в локальном (коротком) формате (т.е. без приставки «<имя платёжного модуля>-»).
	 * @see \Df\GingerPaymentsBase\W\Handler::config()
	 * @see \Dfe\AllPay\W\Handler::parentIdRawKey()
	 * @see \Dfe\SecurePay\W\Handler::parentIdRawKey()
	 *
	 * @used-by parentIdRaw()
	 * @return string
	 */
	abstract protected function parentIdRawKey();

	/**
	 * 2017-01-01
	 * @used-by \Df\Payment\W\F::handler()
	 * @param Event $e
	 */
	final function __construct(Event $e) {$this->_event = $e;}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	final function handle() {
		try {
			if ($this->ss()->log()) {
				$this->log();
			}
			$this->validate();
			/**
			 * 2017-01-04
			 * Добавил обработку ситуации, когда к нам пришло сообщение,
			 * не предназначенное для нашего магазина.
			 * Такое происходит, например, когда мы проводим тестовый платёж на локальном компьютере,
			 * а платёжная система присылает оповещение на наш сайт mage2.pro/sandbox
			 * В такой ситуации не стоит падать с искючительной ситуацией,
			 * а лучше просто ответить: «The event is not for our store».
			 * Так и раньше вели себя мои Stripe-подобные модули,
			 * теперь же я распространил такое поведение на все мои платёжные модули.
			 */
			if (!$this->ii()) {
				$this->resultSet($this->resultNotForUs());
			}
			else {
				$this->initTransaction();
				$this->_handle();
			}
		}
		catch (NotForUs $e) {
			$this->resultSet($this->resultNotForUs($e->getMessage()));
		}
		catch (\Exception $e) {
			$this->log($e);
			/**
			 * 2016-07-15
			 * Раньше тут стояло
					if ($this->_order) {
						$this->_order->cancel();
						$this->_order->save();
					}
			 * На самом деле, исключительная ситуация свидетельствует о сбое в программе,
			 * либо о некорректном запросе якобы от платёжного сервера (хакерской попытке, например),
			 * поэтому отменять заказ тут неразумно.
			 * В случае сбоя платёжная система будет присылать
			 * повторные оповещения — вот пусть и присылает,
			 * авось мы к тому времени уже починим программу, если поломка была на нашей строне
			 */
			$this->resultSet(static::resultError($e));
		}
		return $this->result();
	}

	/**
	 * 2016-07-10
	 * 2017-01-04
	 * Добавил возможность возвращения null:
	 * такое происходит, например, когда мы проводим тестовый платёж на локальном компьютере,
	 * а платёжная система присылает оповещение на наш сайт mage2.pro/sandbox
	 * В такой ситуации не стоит падать с искючительной ситуацией,
	 * а лучше просто ответить: «The event is not for our store».
	 * Так и раньше вели себя мои Stripe-подобные модули,
	 * теперь же я распространил такое поведение на все мои платёжные модули.
	 * 2017-01-06
	 * Для Stripe-подобных платёжных модулей алгоритм раньше был таким:
		$id = df_fetch_one('sales_payment_transaction', 'payment_id', ['txn_id' => $this->id()]);
		return !$id ? null : df_load(Payment::class, $id);
	 * https://github.com/mage2pro/core/blob/1.11.6/Payment/Transaction.php?ts=4#L16-L29
	 *
	 * @used-by initTransaction()
	 * @used-by handle()
	 * @used-by m()
	 * @used-by o()
	 * @used-by \Df\PaypalClone\W\Confirmation::capture()
	 * @used-by \Df\StripeClone\W\Strategy::ii()
	 * @return IOP|OP|null
	 */
	final function ii() {return dfc($this, function() {
		/** @var IOP|OP|null $result */
		if ($result = dfp_by_trans($this->tParent())) {
			dfp_webhook_case($result);
		}
		return $result;
	});}

	/**
	 * 2016-08-14
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\StripeClone\W\Strategy::m()
	 * @return M
	 */
	function m() {return dfc($this, function() {return dfp_method_by_p($this->ii());});}

	/**
	 * 2016-07-10
	 * 2017-01-06
	 * Аналогично можно получить результат и из транзакции: $this->tParent()->getOrder()
	 * @used-by \Df\PaypalClone\W\Confirmation::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::o()
	 * @return Order|DfOrder
	 */
	final function o() {return dfc($this, function() {return df_order_by_payment($this->ii());});}

	/**
	 * 2016-07-10
	 * @used-by initTransaction()
	 * @used-by tParent()
	 * @used-by \Df\StripeClone\W\Strategy::parentId()
	 * @return string
	 */
	final function parentId() {return dfc($this, function() {return
		$this->adaptParentId($this->parentIdRaw())
	;});}

	/**
	 * 2016-07-09
	 * 2017-01-04
	 * Возвращает одно из двух:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Так происходит для Stripe-подобных модулей.
	 * На основе этого идентификатора мы:
	 *     1.1) вычисляем идентификатор родительской транзакции
	 *     (посредством прибавления окончания «-<тип родительской транзакции>»)
	 *     1.2) создаём идентификатор текущей транзакции
	 *     (аналогично, посредством прибавления окончания «-<тип текущей транзакции>»).
	 *
	 * 2) Переданный нами ранее платёжной системе
	 * наш внутренний идентификатор родительской транзакции (т.е., запроса к платёжой системе)
	 * в локальном (коротком) формате (т.е. без приставки «<имя платёжного модуля>-»).
	 *
	 * @used-by parentId()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @return string
	 */
	final function parentIdRaw() {return $this->rr($this->parentIdRawKey());}

	/**
	 * 2016-07-10
	 * @used-by \Dfe\SecurePay\Signer\Response::req()
	 * @param string|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final function parentInfo($k = null) {return dfak($this, function() {return
		df_trans_rd($this->tParent())
	;}, $k);}

	/**
	 * 2017-01-01
	 * @used-by cv()
	 * @used-by initTransaction()
	 * @used-by log()
	 * @used-by \Dfe\AllPay\Block\Info\ATM::paymentId()
	 * @used-by \Dfe\AllPay\W\Handler\BankCard::isInstallment()
	 * @used-by \Dfe\AllPay\W\Handler\Offline::expiration()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final function r($k = null, $d = null) {return $this->_event->r($k, $d);}

	/**
	 * 2017-01-12
	 * @used-by parentIdRaw()
	 * @used-by \Df\StripeClone\W\Handler::ro()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed
	 * @throws Critical
	 */
	final function rr($k = null, $d = null) {return $this->_event->rr($k, $d);}

	/**
	 * 2017-01-07
	 * @used-by handle()
	 * @used-by \Df\StripeClone\W\Strategy::resultSet()
	 * @param Result|Phrase|string|null $v
	 * @return void
	 */
	final function resultSet($v) {
		if (is_string($v)) {
			$v = __($v);
		}
		if ($v instanceof Phrase) {
			$v = Text::i($v);
		}
		$this->_result = $v;
	}

	/**
	 * 2016-08-27
	 * @used-by cv()
	 * @used-by \Df\PaypalClone\W\Confirmation::needCapture()
	 * @used-by \Df\PaypalClone\W\Confirmation::statusExpected()
	 * @param string|null $k [optional]
	 * @param bool $req [optional]
	 * @return mixed|null
	 */
	final protected function c($k = null, $req = true) {return dfc($this, function($k, $req = true) {return
		!is_null($res = dfa($this->configCached(), $k)) || !$req ? /** @var mixed|null $res */ $res :
			df_error("The class %s should define a value for the parameter «{$k}».", get_class($this))
	;}, [$k ?: df_caller_f(), $req]);}

	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Перекрытие этого метода позволяет потомкам разом задать набор параметров данного класса.
	 * Такая техника является более лаконичным вариантом,
	 * нежели объявление и перекрытие методов для отдельных параметров.
	 * @used-by configCached()
	 * @see \Df\GingerPaymentsBase\W\Handler::config()
	 * @see \Dfe\AllPay\W\Handler::config()
	 * @see \Dfe\SecurePay\W\Handler::config()
	 * @return array(string => mixed)
	 */
	protected function config() {return [];}

	/**
	 * 2016-08-27
	 * 2017-01-02
	 * Если задано $d (значение по умолчанию), то мы не требуем обязательности присутствия ключа $k.
	 * @used-by cvo()
	 * @used-by \Df\PaypalClone\W\Handler::externalId()
	 * @used-by \Df\PaypalClone\W\Handler::status()
	 * @used-by \Df\PaypalClone\W\Handler::validate()
	 * @param string $k
	 * @param string|null $d [optional]
	 * @param bool $required [optional]
	 * @return mixed
	 */
	final protected function cv($k = null, $d = null, $required = true) {return
		($k = $this->c($k ?: df_caller_f(), $required && is_null($d))) ? $this->r($k) : $d
	;}

	/**
	 * 2016-12-30
	 * @used-by \Df\PaypalClone\W\Handler::logTitleSuffix()
	 * @param string|null $k [optional]
	 * @param string|null $d [optional]
	 * @return mixed
	 */
	final protected function cvo($k = null, $d = null) {return $this->cv($k ?: df_caller_f(), $d, false);}

	/**
	 * 2017-01-04
	 * @used-by handle()
	 * @see \Dfe\AllPay\W\Handler::resultNotForUs()
	 * @param string|null $message [optional]
	 * @return Result
	 */
	protected function resultNotForUs($message = null) {return
		Text::i($message ?: 'It seems like this event is not for our store.')
	;}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @see \Dfe\AllPay\W\Handler::result()
	 * @return Result
	 */
	protected function result() {return !is_null($this->_result) ? $this->_result : Text::i('success');}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\W\Handler::log()
	 * @see \Df\PaypalClone\W\Handler::logTitleSuffix()
	 * @return string|null
	 */
	protected function logTitleSuffix() {return null;}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by handle()
	 * @see \Df\PaypalClone\W\Handler::validate()
	 * @return void
	 * @throws \Exception
	 */
	protected function validate() {}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\W\Handler::c()
	 * @return array(string => mixed)
	 */
	private function configCached() {return dfc($this, function() {return $this->config();});}

	/**
	 * 2017-01-16
	 * A) Этот метод:
	 * A.1) Устанавливает идентификатор текущей транзакции.
	 * A.2) Указывает идентификатор родительской транзакции.
	 * A.3) Присваивает транзакции информацию из запроса платёжной системы.
	 *
	 * Б) При этом метод НЕ ДОБАВЛЯЕТ ТРАНЗАКЦИЮ!
	 * Б.1) Для PayPal-подобных платёжных модулей добавление транзакции происходит в методе
	 * @see \Df\PaypalClone\W\Confirmation::_handle()
	 *
	 *) Б.2) Для Stripe-подобных платёжных модулей добавление транзакции происходит неявно
	 * при вызове методов ядра:
	 *
	 * Б.2.1) Для операции «authorize» addTransaction() вызывается из:
	 * @see \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize():
	 * 		$transaction = $payment->addTransaction(Transaction::TYPE_AUTH);
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L50
	 *
	 * Б.2.2) Для операции «capture» addTransaction() вызывается из:
	 * @see \Magento\Sales\Model\Order\Payment\Operations\CaptureOperation::capture()
	 * транзакция на самом деле тоже добавляется, и тоже через builder, просто чуть иным кодом:
			$transactionBuilder = $this->transactionBuilder->setPayment($payment);
			$transactionBuilder->setOrder($order);
			$transactionBuilder->setFailSafe(true);
			$transactionBuilder->setTransactionId($payment->getTransactionId());
			$transactionBuilder->setAdditionalInformation($payment->getTransactionAdditionalInfo());
			$transactionBuilder->setSalesDocument($invoice);
			$transaction = $transactionBuilder->build(Transaction::TYPE_CAPTURE);
	 *
	 * Б.2.3) При этом ядро при вызове (из ядра)
	 * @see \Magento\Sales\Model\Order\Payment\Transaction\Manager::generateTransactionId()
	 * смотрит, не были ли ранее установлены идентификаторы транзакции,
	 * и если были, то не перетирает их:
	 * :
		if (!$payment->getParentTransactionId()
			&& !$payment->getTransactionId() && $transactionBasedOn
		) {
			$payment->setParentTransactionId($transactionBasedOn->getTxnId());
		}
		// generate transaction id for an offline action or payment method that didn't set it
		if (
			($parentTxnId = $payment->getParentTransactionId())
			&& !$payment->getTransactionId()
		) {
			return "{$parentTxnId}-{$type}";
		}
		return $payment->getTransactionId();
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Transaction/Manager.php#L73-L80
	 *
	 * @used-by handle()
	 */
	private function initTransaction() {
		/** @var OP $i */
		$i = $this->ii();
		$i->setTransactionId($this->id());
		dfp_set_transaction_info($i, $this->r());
		/**
		 * 2016-07-12
		 * @used-by \Magento\Sales\Model\Order\Payment\Transaction\Builder::linkWithParentTransaction()
		 */
		$i->setParentTransactionId($this->parentId());
	}

	/**
	 * 2016-12-26
	 * @used-by handle()
	 * @used-by resultError()
	 * @param \Exception|null $e [optional]
	 * @return void
	 */
	private function log(\Exception $e = null) {
		/** @var string $data */
		$data = df_json_encode_pretty($this->r());
		/** @var string $title */
		$title = dfp_method_title($this);
		/** @var \Exception|string $v */
		/** @var string|null $suffix */
		if ($e) {
			list($v, $suffix) = [$e, 'exception'];
			df_log_l($e);
		}
		else {
			/** @var Event $ev */
			$ev = $this->_event;
			$v = df_ccc(': ', "[{$title}] {$ev->tl()}", $this->logTitleSuffix());
			/** @var string|null $t $suffix */
			$suffix = is_null($t = $ev->t()) ? null : df_fs_name($t);
		}
		df_sentry_m($this)->user_context(['id' => $title]);
		dfp_sentry_tags($this->m());
		df_sentry($this, $v, ['extra' => ['Payment Data' => $data]]);
		dfp_log_l($this, $data, $suffix);
	}

	/**
	 * 2016-07-10
	 * 2016-12-30
	 * Возвращает транзакцию Magento, породившую данное оповещение от платёжной системы (webhook event).
	 * В то же время не каждое оповещение от платёжной системы инициируется запросом от Magento:
	 * например, оповещение могло быть инициировано некими действиями администратора магазина
	 * в административном интерфейсе магазина в платёжной системе.
	 * Однако первичная транзакция всё равно должна в Magento присутствовать.
	 * 2017-01-08
	 * Добавил обработку ситуации, когда родительская транзакция не найдена.
	 * Такое возможно, например, когда мы выполнили из административной части Stripe
	 * запрос на capture для локального (localhost) магазина, а оповещение пришло на mage2.pro/sandbox.
	 * Так вот, если просто свалиться с исключительной ситуацией (код HTTP 500),
	 * то Stripe задолбает повторными запросами.
	 * Надо вернуть код HTTP 200 и человекопонятное сообщение: мол, запрос — не для нашего магазина.
	 * @used-by ii()
	 * @used-by o()
	 * @used-by parentInfo()
	 * @return T
	 * @throws NotForUs
	 */
	private function tParent() {return dfc($this, function() {
		/** @var T|null $result */
		if (!($result = df_transx($this->parentId(), false))) {
			throw new NotForUs(
				"It seems like this event is not for our store, "
				."because the parent transaction «{$this->parentId()}» "
				."is not found in the store's database."
			);
		}
		return $result;
	});}

	/**
	 * 2017-03-10
	 * @var Event
	 */
	private $_event;

	/**
	 * 2017-01-07
	 * @used-by result()
	 * @used-by resultSet()
	 * @var Result
	 */
	private $_result;

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @used-by \Df\Payment\W\Action::error()
	 * @see \Dfe\AllPay\W\Handler::resultError()
	 * @param \Exception $e
	 * @return Text
	 */
	static function resultError(\Exception $e) {return Text::i(df_lets($e))->setHttpResponseCode(500);}

	/**
	 * 2016-08-27
	 * @used-by \Df\GingerPaymentsBase\W\Handler::config()
	 * @used-by \Dfe\SecurePay\W\Handler::config()
	 * @var string
	 */
	protected static $needCapture = 'needCapture';
}