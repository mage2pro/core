<?php
namespace Df\PaypalClone\W;
/**
 * 2017-03-15
 * @see \Dfe\AllPay\W\Nav\Offline
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
	 * @used-by \Df\Payment\W\Nav::op()
	 * @return string
	 */
	final protected function id() {return $this->e2i($this->e()->idE(), $this->type());}

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
	final protected function pidAdapt($id) {return $this->e2i($id);}

	/**
	 * 2017-03-22
	 * @used-by id()
	 * @see \Dfe\AllPay\W\Nav\Offline::type()
	 * @return string|null
	 */
	protected function type() {return null;}
}