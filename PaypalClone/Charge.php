<?php
namespace Df\PaypalClone;
use Df\PaypalClone\Charge\IP;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Charge
 * @see \Dfe\SecurePay\Charge
 */
abstract class Charge extends \Df\Payment\Charge implements IP {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::params()
	 * @see \Dfe\SecurePay\Charge::params()
	 * @return array(string => mixed)
	 */
	abstract protected function params();

	/**
	 * 2016-08-29
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @return string
	 */
	abstract protected function requestIdKey();

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::signatureKey()
	 * @see \Dfe\SecurePay\Charge::signatureKey()
	 * @return string
	 */
	abstract protected function signatureKey();

	/**
	 * 2016-08-29
	 * Локальный внутренний идентификатор транзакции.
	 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::requestId()
	 * @return string
	 */
	protected function requestId() {return $this->oii();}

	/**
	 * 2016-08-27
	 * @override
	 * @see \Df\PaypalClone\Charge\IP::p()
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @param Method $method
	 * @return array(string, array(string => mixed))
	 */
	final static function p(Method $method) {
		/** @var self $i */
		$i = df_create(df_con($method, 'Charge'), [self::$P__METHOD => $method]);
		/**
		 * 2017-01-05
		 * @uses requestId()
		 * @uses \Dfe\AllPay\Charge::requestId()
		 * Локальный внутренний идентификатор транзакции.
		 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
		 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
		 * ведь там все идентификаторы имели бы одинаковую приставку.
		 */
		/** @var string $id */
		$id = df_assert_sne($i->requestId());
		/** @var array(string => mixed) $p */
		$p = [$i->requestIdKey() => $id] + $i->params();
		return [$id, $p + [$i->signatureKey() => Signer::signRequest($i, $p)]];
	}
}