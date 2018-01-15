<?php
namespace Df\StripeClone\W;
use Df\Payment\W\Exception\Critical;
/**
 * 2017-03-15
 * @see \Df\GingerPaymentsBase\W\Event
 * @see \Dfe\Moip\W\Event
 * @see \Dfe\Omise\W\Event
 * @see \Dfe\Paymill\W\Event
 * @see \Dfe\Stripe\W\Event
 */
abstract class Event extends \Df\Payment\W\Event {
	/**
	 * 2017-01-06
	 * 2017-03-18 Тип родительской транзакции
	 * @used-by \Df\StripeClone\W\Nav::pidAdapt()
	 * @see \Df\GingerPaymentsBase\W\Event::ttParent()
	 * @see \Dfe\Moip\W\Event::ttParent()
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
	 * @see \Df\GingerPaymentsBase\W\Event::roPath()
	 * @see \Dfe\Moip\W\Event::roPath()
	 * @see \Dfe\Omise\W\Event::roPath()
	 * @see \Dfe\Paymill\W\Event::roPath()
	 * @see \Dfe\Stripe\W\Event::roPath()
	 * @return string|null
	 */
	abstract protected function roPath();

	/**
	 * 2017-01-17
	 * @used-by \Df\GingerPaymentsBase\W\Handler::strategyC()
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
	 * @used-by \Dfe\Omise\W\Handler\Charge\Complete::isPending()
	 * @used-by \Dfe\Omise\W\Handler\Refund\Create::amount()
	 * @used-by \Dfe\Omise\W\Handler\Refund\Create::eTransId()
	 * @used-by \Dfe\Paymill\W\Handler\Refund\Succeeded::amount()
	 * @used-by \Dfe\Paymill\W\Handler\Refund\Succeeded::eTransId()
	 * @used-by \Dfe\Stripe\W\Event\Source::checkIgnored()
	 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::amount()
	 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::eTransId()
	 * @return mixed|int|string|null|array(string => mixed)
	 * @throws Critical
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
	 * 2017-03-26
	 * df_cc_path() нам нужна, потому что roPath() может возвращать null
	 * в том случае, когда основные данные события расположены на верхнем уровне вложенности:
	 * @see \Df\GingerPaymentsBase\W\Event::roPath()
	 * @override
	 * @see \Df\Payment\W\Event::k_pid()
	 * @used-by \Df\Payment\W\Event::pid()
	 * @return string
	 */
	final protected function k_pid() {return df_cc_path($this->roPath(), $this->k_pidSuffix());}

	/**
	 * 2017-02-14
	 * 2017-11-10
	 * From now on, an instance of this class can express not only a `charge` event,
	 * but a Stripe's `source.*` event too, and these events have the same `id` property:
	 * "An initial reusable source for a card which requires a 3D Secure verification': https://mage2.pro/t/4893
	 * "A derived single-use 3D Secure source": https://mage2.pro/t/4894
	 * @used-by k_pid()
	 * @see \Df\GingerPaymentsBase\W\Event::k_pidSuffix()
	 * @see \Dfe\Omise\W\Event\Refund::k_pidSuffix()
	 * @see \Dfe\Paymill\W\Event\Refund::k_pidSuffix()
	 * @return string
	 */
	protected function k_pidSuffix() {return 'id';}
}