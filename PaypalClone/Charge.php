<?php
namespace Df\PaypalClone;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Charge
 * @see \Dfe\IPay88\Charge
 * @see \Dfe\Paystation\Charge
 * @see \Dfe\SecurePay\Charge
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::pCharge()
	 * @see \Dfe\IPay88\Charge::pCharge()
	 * @see \Dfe\Paystation\Charge::pCharge()
	 * @see \Dfe\SecurePay\Charge::pCharge()
	 * @return array(string => mixed)
	 */
	abstract protected function pCharge();

	/**
	 * 2016-08-29
	 * @used-by p()
	 * @see \Dfe\AllPay\Charge::k_RequestId()
	 * @see \Dfe\IPay88\Charge::k_RequestId()
	 * @see \Dfe\Paystation\Charge::k_RequestId()
	 * @see \Dfe\SecurePay\Charge::k_RequestId()
	 * @return string
	 */
	abstract protected function k_RequestId();

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Charge::p()
	 * @see \Dfe\AllPay\Charge::k_Signature()
	 * @see \Dfe\IPay88\Charge::k_Signature()
	 * @see \Dfe\Paystation\Charge::k_Signature()
	 * @see \Dfe\SecurePay\Charge::k_Signature()
	 * @return string
	 */
	abstract protected function k_Signature();

	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @param Method $m
	 * @return array(string, array(string => mixed))
	 */
	final static function p(Method $m) {
		/** @var self $i */
		$i = df_new(df_con_heir($m, __CLASS__), $m);
		/**
		 * 2017-01-05
		 * @uses \Df\Payment\Operation::id()
		 * @uses \Dfe\AllPay\Charge::id()
		 * Локальный внутренний идентификатор транзакции.
		 * Мы намеренно передаваём этот идентификатор локальным (без приставки с именем модуля)
		 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
		 * ведь там все идентификаторы имели бы одинаковую приставку.
		 */
		/** @var string $id */
		$id = df_assert_sne($i->id());
		/** @var array(string => mixed) $p */
		$p = [$i->k_RequestId() => $id] + $i->pCharge();
		return [$id, $p + [$i->k_Signature() => Signer::signRequest($i, $p)]];
	}
}