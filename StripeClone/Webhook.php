<?php
namespace Df\StripeClone;
/**
 * 2016-12-26
 * @see \Dfe\Iyzico\Webhook
 * @see \Dfe\Omise\Webhook
 * @see \Dfe\Paymill\Webhook
 * @see \Dfe\Stripe\Webhook
 */
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2017-01-06
	 * @used-by id()
	 * @used-by \Df\StripeClone\WebhookStrategy::currentTransactionType()
	 * @see \Dfe\Omise\Webhook\Charge\Capture::currentTransactionType()
	 * @see \Dfe\Omise\Webhook\Charge\Complete::currentTransactionType()
	 * @see \Dfe\Omise\Webhook\Refund\Create::currentTransactionType()
	 * @see \Dfe\Paymill\Webhook\Transaction\Succeeded::currentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Captured::currentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::currentTransactionType()
	 * @return string
	 */
	abstract function currentTransactionType();

	/**
	 * 2017-01-06
	 * @used-by adaptParentId()
	 * @see \Dfe\Omise\Webhook\Charge\Capture::parentTransactionType()
	 * @see \Dfe\Omise\Webhook\Charge\Complete::parentTransactionType()
	 * @see \Dfe\Omise\Webhook\Refund\Create::parentTransactionType()
	 * @see \Dfe\Paymill\Webhook\Transaction\Succeeded::parentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Captured::parentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::parentTransactionType()
	 * @return string
	 */
	abstract protected function parentTransactionType();

	/**
	 * 2017-01-04
	 * 2017-01-06
	 * Сообщение от платёжной системы — это иерархический JSON.
	 * На верхнем уровне иерархии расположены метаданные:
	 * *) тип сообщения (например: «charge.captured»).
	 * *) идентификатор платежа в платёжной системе
	 * *) тестовый ли платёж или промышленный
	 * *) версия API
	 * *) и.т.п.
	 * Конкретные данные сообщения расположены внутри иерархии по некоему пути.
	 * Этот путь и возвращает наш метод.
	 *
	 * 2017-02-14
	 * Если конкретные данные сообщения расположены прямо на верхнем уровне иерархии,
	 * то метод должен вернуть null или пустую строку.
	 *
	 * @used-by parentIdRawKey()
	 * @used-by ro()
	 * @see \Dfe\Iyzico\Webhook::roPath()
	 * @see \Dfe\Omise\Webhook::roPath()
	 * @see \Dfe\Paymill\Webhook::roPath()
	 * @see \Dfe\Stripe\Webhook::roPath()
	 * @return string|null
	 */
	abstract protected function roPath();

	/**
	 * 2017-01-04
	 * @used-by \Df\StripeClone\WebhookF::i()
	 * @param string $v
	 * @return void
	 */
	final function typeSet($v) {$this->_type = $v;}

	/**
	 * 2017-01-04
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @used-by \Df\StripeClone\WebhookStrategy::ro()
	 * @used-by \Dfe\Omise\Webhook\Charge\Complete::isPending()
	 * @return array(string => mixed)|mixed|null
	 */
	final function ro($k = null, $d = null) {return
		$this->reqr(df_cc_path($this->roPath(), $k), $d)
	;}

	/**
	 * 2017-01-06
	 * Stripe-подобные платёжные системы, в отличие от PayPal-подобных,
	 * отличаются богатством типов оповещений.
	 *
	 * PayPal-подобные платёжные системы присылают, как правило, только один тип оповещений:
	 * оповещение о факте успешности (или неуспешности) оплаты покупателем заказа.
	 *
	 * У Stripe-подобных платёжных систем типов оповещений множество,
	 * причём они порой образуют целые иерархии.
	 * Например, у Stripe оповещения об изменении статуса платежа объединяются в группу «charge»,
	 * которой принадлежат такие типы оповещений, как «charge.captured» и «charge.refunded».
	 *
	 * Разные Stripe-подобные платёжные системы обладают схожими типами платежей.
	 * Пример — те же самые «charge.captured» и «charge.refunded».
	 * По этой причине разумно выделять не только общие черты,
	 * свойственные конкретной Stripe-подобной платёжной системе
	 * и отличащие её от других Stripe-подобных платёжных систем,
	 * но и общие черты типов платежей: обработка того же «charge.captured»
	 * должна иметь общую реализацию для всех Stripe-подобных платёжных модулей.
	 *
	 * Для реализации такой системы из двух параллельных иерархий
	 * я вынес данный метод _handle() на верхний уровень иерархии Stripe-подобных платёжных модулей
	 * и сделал его конечным (final), а иерархию обработчиков разных типов платежей
	 * вынес в стратегию.
	 *
	 * @override
	 * @see \Df\Payment\Webhook::_handle()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return void
	 */
	final protected function _handle() {
		/** @var WebhookStrategy $strategy */
		$strategy = df_newa($this->strategyC(), WebhookStrategy::class, $this);
		$strategy->handle();
	}

	/**
	 * 2017-01-06
	 * Преобразует идентификатор платежа в платёжной системе
	 * в глобальный внутренний идентификатор родительской транзакции.
	 * @override
	 * @see \Df\Payment\Webhook::adaptParentId()
	 * @used-by \Df\Payment\Webhook::parentId()
	 * @param string $id
	 * @return string
	 */
	final protected function adaptParentId($id) {
		df_param_sne($id, 0);
		return $this->e2i($id, $this->parentTransactionType());
	}

	/**
	 * 2017-02-14
	 * Прошлые комментарии для Stripe:
	 * ======
	 * 2017-01-04
	 * Для Stripe-подобные платёжных систем
	 * наш внутренний идентификатор транзакции основывается на внешнем:
	 * <имя модуля>-<внешний идентификатор платежа>-<окончание типа события>.
	 * 2017-01-07
	 * Ключ должен быть именно «data/object/id».
	 * Ключ «id» у события тоже присутствует, но его значением является не идентификатор платежа
	 * («ch_*»), а идентификатор события («evt_*»).
	 * =====
	 * @override
	 * @see \Df\Payment\Webhook::parentIdRawKey()
	 * @used-by \Df\Payment\Webhook::parentIdRaw()
	 * @return string
	 */
	final protected function parentIdRawKey() {return
		"{$this->roPath()}/{$this->parentIdRawKeySuffix()}"
	;}

	/**
	 * 2017-02-14
	 * @used-by parentIdRawKey()
	 * @see \Dfe\Omise\Webhook\Refund\Create::parentIdRawKeySuffix()
	 * @return string
	 */
	protected function parentIdRawKeySuffix() {return 'id';}

	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\Webhook::id()
	 * @used-by \Df\Payment\Webhook::initTransaction()
	 * @return string
	 */
	final protected function id() {return
		$this->e2i($this->idBase(), $this->currentTransactionType())
	;}

	/**
	 * 2017-01-17
	 * @used-by id()
	 * @see \Dfe\Omise\Webhook\Refund\Create::idBase()
	 * @return string
	 */
	protected function idBase() {return $this->parentIdRaw();}

	/**
	 * 2017-01-12
	 * 2017-01-15
	 * Отныне стандартные стратегии ищутся по имени <имя модуля>\WebhookStrategy\<суффикс вебхука>,
	 * причем сначала в папке конечного модуля, а затем в папке текущего (Df\StripeClone).
	 * @used-by _handle()
	 * @see \Dfe\Omise\Webhook\Charge\Capture::strategyC()
	 * @see \Dfe\Omise\Webhook\Charge\Complete::strategyC()
	 * @see \Dfe\Omise\Webhook\Refund\Create::strategyC()
	 * @see \Dfe\Paymill\Webhook\Transaction\Succeeded::strategyC()
	 * @return string
	 */
	protected function strategyC() {
		/** @var string[] $classA */
		$classA = df_explode_class(df_module_name_c(__CLASS__)) + df_explode_class($this);
		$classA[2] .= 'Strategy';
		return df_con_heir($this, df_cc_class($classA));
	}

	/**
	 * 2017-01-04
	 * 2017-01-08
	 * Здесь ещё нельзя использовать @see \Df\StripeClone\Webhook::type(),
	 * потому что сюда мы попадаем из @used-by \Df\StripeClone\Webhook::__construct(),
	 * а тип устанавливается уже после, вызовом @see \Df\StripeClone\Webhook::typeSet()
	 * из @see \Df\StripeClone\WebhookF::i()
	 * @override
	 * @see \Df\Payment\Webhook::testDataFile()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function testDataFile() {return $this->extra(WebhookF::KEY_TYPE);}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::type()
	 * @used-by \Df\Payment\Webhook::typeLabel()
	 * @used-by \Dfe\AllPay\Webhook::classSuffix()
	 * @used-by \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	final protected function type() {return $this->_type;}

	/**
	 * 2017-01-04
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by id()
	 * @uses \Df\StripeClone\Method::e2i()
	 * @param string $id
	 * @param string $txnType
	 * @return string
	 */
	private function e2i($id, $txnType) {
		df_param_sne($id, 0);
		return dfp_method_call_s($this, 'e2i', $id, $txnType);
	}

	/**
	 * 2017-01-04
	 * @used-by type()
	 * @used-by typeSet()
	 * @var string
	 */
	private $_type;
}