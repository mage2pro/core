<?php
namespace Df\StripeClone\W;
/**
 * 2017-03-15
 * @see \Dfe\Iyzico\W\Event
 * @see \Dfe\Omise\W\Event
 * @see \Dfe\Paymill\W\Event
 * @see \Dfe\Stripe\W\Event
 */
abstract class Event extends \Df\Payment\W\Event {
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
	 * @used-by k_pid()
	 * @used-by ro()
	 * @see \Dfe\Iyzico\W\Event::roPath()
	 * @see \Dfe\Omise\W\Event::roPath()
	 * @see \Dfe\Paymill\W\Event::roPath()
	 * @see \Dfe\Stripe\W\Event::roPath()
	 * @return string|null
	 */
	abstract protected function roPath();

	/**
	 * 2017-01-06
	 * 2017-03-18
	 * Тип текущей транзакции.
	 * @used-by \Df\StripeClone\W\Nav::id()
	 * @used-by \Df\StripeClone\W\Strategy\Charge::action()
	 * @see \Dfe\Omise\W\Event\Charge\Capture::ttCurrent()
	 * @see \Dfe\Omise\W\Event\Charge\Complete::ttCurrent()
	 * @see \Dfe\Omise\W\Event\Refund::ttCurrent()
	 * @see \Dfe\Paymill\W\Event\Refund::ttCurrent()
	 * @see \Dfe\Paymill\W\Event\Transaction\Succeeded::ttCurrent()
	 * @see \Dfe\Stripe\W\Event\Charge\Captured::ttCurrent()
	 * @see \Dfe\Stripe\W\Event\Charge\Refunded::ttCurrent()
	 * @return string
	 */
	abstract function ttCurrent();

	/**
	 * 2017-01-06
	 * 2017-03-18
	 * Тип родительской транзакции.
	 * @used-by \Df\StripeClone\W\Nav::pidAdapt()
	 * @see \Dfe\Omise\W\Event\Charge\Capture::ttParent()
	 * @see \Dfe\Omise\W\Event\Charge\Complete::ttParent()
	 * @see \Dfe\Omise\W\Event\Refund::ttParent()
	 * @see \Dfe\Paymill\W\Event\Refund::ttParent()
	 * @see \Dfe\Paymill\W\Event\Transaction\Succeeded::ttParent()
	 * @see \Dfe\Stripe\W\Event\Charge\Captured::ttParent()
	 * @see \Dfe\Stripe\W\Event\Charge\Refunded::ttParent()
	 * @return string
	 */
	abstract function ttParent();

	/**
	 * 2017-01-17
	 * @used-by \Df\StripeClone\W\Nav::id()
	 * @see \Dfe\Omise\W\Event\Refund::idBase()
	 * @see \Dfe\Paymill\W\Event\Refund::idBase()
	 * @return string
	 */
	function idBase() {return $this->pid();}

	/**
	 * 2017-01-04
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @used-by \Df\StripeClone\W\Strategy::ro()
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::isPending()
	 * @used-by \Dfe\Omise\W\Handler\Refund\Create::amount()
	 * @used-by \Dfe\Omise\W\Handler\Refund\Create::eTransId()
	 * @used-by \Dfe\Paymill\W\Handler\Refund\Succeeded::amount()
	 * @used-by \Dfe\Paymill\W\Handler\Refund\Succeeded::eTransId()
	 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::amount()
	 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::eTransId()
	 * @return array(string => mixed)|mixed|null
	 */
	final function ro($k = null, $d = null) {return $this->rr(df_cc_path($this->roPath(), $k), $d);}

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
	 * @see \Df\Payment\W\Event::k_pid()
	 * @used-by \Df\Payment\W\Event::pid()
	 * @used-by \Df\StripeClone\W\Handler::idBase()
	 * @return string
	 */
	final protected function k_pid() {return "{$this->roPath()}/{$this->k_pidSuffix()}";}

	/**
	 * 2017-02-14
	 * @used-by k_pid()
	 * @see \Dfe\Omise\W\Event\Refund::k_pidSuffix()
	 * @see \Dfe\Paymill\W\Event\Refund::k_pidSuffix()
	 * @return string
	 */
	protected function k_pidSuffix() {return 'id';}
}