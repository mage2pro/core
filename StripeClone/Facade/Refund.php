<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-02-10
 * @see \Dfe\Omise\Facade\Refund
 * @see \Dfe\Paymill\Facade\Refund
 * @see \Dfe\Spryng\Facade\Refund
 * @see \Dfe\Square\Facade\Refund
 * @see \Dfe\Stripe\Facade\Refund
 * @see \Dfe\TBCBank\Facade\Refund
 * @see \Dfe\Vantiv\Facade\Refund()
 * @method static Refund s(M $m)
 */
abstract class Refund extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * Метод должен вернуть идентификатор операции (не платежа!) в платёжной системе.
	 * Мы записываем его в БД и затем при обработке оповещений от платёжной системы
	 * смотрим, не было ли это оповещение инициировано нашей же операцией, и если было, то не обрабатываем его повторно.
	 * 2017-02-14 Этот же идентификатор должен возвращать @see \Df\Payment\W\IRefund::eTransId()
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Omise\Facade\Refund::transId()
	 * @see \Dfe\Paymill\Facade\Refund::transId()
	 * @see \Dfe\Spryng\Facade\Refund::transId()
	 * @see \Dfe\Square\Facade\Refund::transId()
	 * @see \Dfe\Stripe\Facade\Refund::transId()
	 * @see \Dfe\TBCBank\Facade\Refund::transId()
	 * @see \Dfe\Vantiv\Facade\Refund::transId()
	 * @param object $r
	 */
	abstract function transId($r):string;
}