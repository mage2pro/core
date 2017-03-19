<?php
namespace Df\StripeClone\W;
use Df\StripeClone\Method as M;
/**
 * 2017-03-15
 * @method Event e()
 * @method M mPartial()
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
	final protected function id() {return $this->e2i($this->e()->idBase(), $this->e()->ttCurrent());}

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
	final protected function pidAdapt($id) {return $this->e2i(df_param_sne($id, 0), $this->e()->ttParent());}

	/**
	 * 2017-01-04
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by id()
	 * @used-by pidAdapt()
	 * @uses \Df\StripeClone\Method::e2i()
	 * @param string $id
	 * @param string $type
	 * @return string
	 */
	private function e2i($id, $type) {return $this->mPartial()->e2i(df_param_sne($id, 0), $type);}
}