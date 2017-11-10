<?php
namespace Df\StripeClone\W;
/**
 * 2017-03-15
 * 2017-03-26, 2017-11-10
 * It is used as a base for the \Df\GingerPaymentsBase\W\Nav virtual type:
 * https://github.com/mage2pro/ginger-payments-base/blob/1.2.3/etc/di.xml#L6
 * I use it because @see \Df\GingerPaymentsBase\Method does not inherit from @see \Df\StripeClone\Method,
 * so @see \Df\Payment\W\F::c() will not resolve the `Nav` class to \Df\StripeClone\W\Nav automatically.
 * @see \Dfe\Stripe\W\Nav\Source
 * @method Event e()
 */
class Nav extends \Df\Payment\W\Nav {
	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\W\Nav::id()
	 * @used-by \Df\Payment\W\Handler::op()
	 * @see \Dfe\Stripe\W\Nav\Source::id()
	 * @return string|null
	 */
	protected function id() {return $this->e2i($this->e()->idBase(), $this->e()->ttCurrent());}

	/**
	 * 2017-01-06
	 * Преобразует идентификатор платежа в платёжной системе
	 * в глобальный внутренний идентификатор родительской транзакции.
	 * @override
	 * @see \Df\Payment\W\Nav::pidAdapt()
	 * @used-by \Df\Payment\W\Nav::pid()
	 * @param string $id
	 * @return string
	 */
	protected function pidAdapt($id) {return $this->e2i($id, $this->e()->ttParent());}
}