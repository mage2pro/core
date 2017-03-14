<?php
namespace Df\StripeClone\W;
/**
 * 2016-12-26
 * @see \Dfe\Iyzico\W\Handler
 * @see \Dfe\Omise\W\Handler
 * @see \Dfe\Paymill\W\Handler
 * @see \Dfe\Stripe\W\Handler
 */
abstract class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2017-01-06
	 * @used-by id()
	 * @used-by \Df\StripeClone\W\Strategy::currentTransactionType()
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::currentTransactionType()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::currentTransactionType()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::currentTransactionType()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::currentTransactionType()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::currentTransactionType()
	 * @see \Dfe\Stripe\W\Handler\Charge\Captured::currentTransactionType()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::currentTransactionType()
	 * @return string
	 */
	abstract function currentTransactionType();

	/**
	 * 2017-01-06
	 * @used-by adaptParentId()
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::parentTransactionType()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::parentTransactionType()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::parentTransactionType()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::parentTransactionType()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::parentTransactionType()
	 * @see \Dfe\Stripe\W\Handler\Charge\Captured::parentTransactionType()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::parentTransactionType()
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
	 * @see \Dfe\Iyzico\W\Handler::roPath()
	 * @see \Dfe\Omise\W\Handler::roPath()
	 * @see \Dfe\Paymill\W\Handler::roPath()
	 * @see \Dfe\Stripe\W\Handler::roPath()
	 * @return string|null
	 */
	abstract protected function roPath();

	/**
	 * 2017-01-12
	 * @used-by _handle()
	 * @see \Dfe\Omise\W\Handler\Charge\Capture::strategyC()
	 * @see \Dfe\Omise\W\Handler\Charge\Complete::strategyC()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::strategyC()
	 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Captured::strategyC()
	 * @see \Dfe\Stripe\W\Handler\Charge\Refunded::strategyC()
	 * @return string
	 */
	abstract protected function strategyC();

	/**
	 * 2017-01-04
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @used-by \Df\StripeClone\W\Strategy::ro()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::isPending()
	 * @return array(string => mixed)|mixed|null
	 */
	final function ro($k = null, $d = null) {return $this->rr(df_cc_path($this->roPath(), $k), $d);}

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
	 * @see \Df\Payment\W\Handler::_handle()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @return void
	 */
	final protected function _handle() {
		/** @var Strategy $strategy */
		$strategy = df_newa($this->strategyC(), Strategy::class, $this);
		$strategy->handle();
	}

	/**
	 * 2017-01-06
	 * Преобразует идентификатор платежа в платёжной системе
	 * в глобальный внутренний идентификатор родительской транзакции.
	 * @override
	 * @see \Df\Payment\W\Handler::adaptParentId()
	 * @used-by \Df\Payment\W\Handler::parentId()
	 * @param string $id
	 * @return string
	 */
	final protected function adaptParentId($id) {return $this->e2i(
		df_param_sne($id, 0), $this->parentTransactionType()
	);}

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
	 * @see \Df\Payment\W\Handler::parentIdRawKey()
	 * @used-by \Df\Payment\W\Handler::parentIdRaw()
	 * @return string
	 */
	final protected function parentIdRawKey() {return "{$this->roPath()}/{$this->parentIdRawKeySuffix()}";}

	/**
	 * 2017-02-14
	 * @used-by parentIdRawKey()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::parentIdRawKeySuffix()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::parentIdRawKeySuffix()
	 * @return string
	 */
	protected function parentIdRawKeySuffix() {return 'id';}

	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\W\Handler::id()
	 * @used-by \Df\Payment\W\Handler::initTransaction()
	 * @return string
	 */
	final protected function id() {return $this->e2i($this->idBase(), $this->currentTransactionType());}

	/**
	 * 2017-01-17
	 * @used-by id()
	 * @see \Dfe\Omise\W\Handler\Refund\Create::idBase()
	 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded::idBase()
	 * @return string
	 */
	protected function idBase() {return $this->parentIdRaw();}

	/**
	 * 2017-01-04
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by id()
	 * @uses \Df\StripeClone\Method::e2i()
	 * @param string $id
	 * @param string $type
	 * @return string
	 */
	private function e2i($id, $type) {return dfp_method_call_s($this, 'e2i', df_param_sne($id, 0), $type);}
}