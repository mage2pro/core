<?php
namespace Df\PaypalClone;
use Df\PaypalClone\Charge\IP;
use Df\PaypalClone\Charge\IRequestIdKey;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Charge
 * @see \Dfe\SecurePay\Charge
 */
abstract class Charge extends \Df\Payment\Charge implements IP, IRequestIdKey {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::params()
	 * @see \Dfe\SecurePay\Charge::params()
	 * @return array(string => mixed)
	 */
	abstract protected function params();

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
		 * 2016-08-29
		 * Метод @uses \Df\PaypalClone\Charge\IRequestIdKey::requestIdKey() статичен,
		 * но мы вызываем его нестатично (чтобы он был вызван для нужного класса, а не для базового),
		 * и это успешно работает безо всяких предупреждений интерпретатора:
		 * https://3v4l.org/N2VD2
		 * http://stackoverflow.com/a/32746909
		 * http://stackoverflow.com/a/15756165
		 * Некоторые утверждают, что якобы на старых версиях PHP
		 * это может выдавать предупреждение уровня E_STRICT:
		 * http://stackoverflow.com/a/12874405
		 * Однако это неправда, я проверил: https://3v4l.org/1JY8i
		 *
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