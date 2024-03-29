<?php
namespace Df\StripeClone\W;
/**
 * 2017-03-15
 * 2017-03-26, 2017-11-10
 * It is used as a base for the \Dfe\GingerPaymentsBase\W\Nav virtual type:
 * https://github.com/mage2pro/ginger-payments-base/blob/1.2.3/etc/di.xml#L6
 * I use it because @see \Dfe\GingerPaymentsBase\Method does not inherit from @see \Df\StripeClone\Method,
 * so @see \Df\Payment\W\F::c() will not resolve the `Nav` class to \Df\StripeClone\W\Nav automatically.
 * 2018-09-28 It is used as a base for the \Dfe\TBCBank\W\Nav virtual type.
 * @see \Dfe\Stripe\W\Nav\Source
 * @method Event e()
 */
class Nav extends \Df\Payment\W\Nav {
	/**
	 * 2017-01-06
	 * The method returns the full identifier of the current payment trasaction.
	 * It is used only to assign it to the current transaction,
	 * so in future we can navigate the transaction by the identifier.
	 * @override
	 * @see \Df\Payment\W\Nav::id()
	 * @used-by \Df\Payment\W\Nav::op()
	 * @see \Dfe\Stripe\W\Nav\Source::id()
	 */
	protected function id():string {return $this->e2i($this->e()->idBase(), $this->e()->ttCurrent());}

	/**
	 * 2017-01-06
	 * Преобразует идентификатор платежа в платёжной системе в глобальный внутренний идентификатор родительской транзакции.
	 * @override
	 * @see \Df\Payment\W\Nav::pidAdapt()
	 * @used-by \Df\Payment\W\Nav::pid()
	 */
	final protected function pidAdapt(string $id):string {return $this->e2i($id, $this->e()->ttParent());}
}