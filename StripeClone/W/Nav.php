<?php
namespace Df\StripeClone\W;
/**
 * 2017-03-15
 * 2017-03-26 It is used as a base for the \Df\GingerPaymentsBase\W\Nav virtual type.
 * @method Event e()
 */
final class Nav extends \Df\Payment\W\Nav {
	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\W\Handler::id()
	 * @used-by \Df\Payment\W\Handler::op()
	 * @return string
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