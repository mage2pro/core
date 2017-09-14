<?php
namespace Df\PaypalClone\W;
/**
 * 2017-03-15
 * 2017-09-14 It is used as a base for the \Dfe\Qiwi\W\Nav virtual type.
 * @method Event e()
 */
final class Nav extends \Df\Payment\W\Nav {
	/**
	 * 2017-01-06
	 * The method returns the full identifier of the current payment trasaction.
	 * It is used only to assign it to the current transaction,
	 * so in future we can navigate the transaction by the identifier.
	 * @override
	 * @see \Df\Payment\W\Nav::id()
	 * @used-by \Df\Payment\W\Nav::op()
	 * @return string
	 */
	protected function id() {return $this->e2i($this->e()->idE(), $this->e()->ttCurrent());}

	/**
	 * 2017-01-06
	 * 2017-03-23
	 * Возвращает идентификатор родительской транзакции в Magento
	 * на основе некоего полученного из ПС значения.
	 * Эта основа в настоящее время бывает 2-х видов:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Это случай Stripe-подобных платёжных систем: у них идентификатор формируется платёжной системой.
	 *
	 * 2) Локальный внутренний идентификатор родительской транзакции.
	 * Это случай PayPal-подобных платёжных систем, когда мы сами ранее сформировали
	 * идентификатор запроса к платёжной системе (этот запрос и является родительской транзакцией).
	 * Такой идентификатор формируется в методах:
	 * @see \Df\Payment\Operation::id()
	 * @see \Dfe\AllPay\Charge::id()
	 *
	 * @override
	 * @see \Df\Payment\W\Nav::pidAdapt()
	 * @used-by \Df\Payment\W\Nav::pid()
	 * @param string $id
	 * @return string
	 */
	protected function pidAdapt($id) {return $this->e2i($id);}
}