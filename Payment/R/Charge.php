<?php
// 2016-08-27
namespace Df\Payment\R;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order\Payment as OP;
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\Charge\WithRedirect::p()
	 * @return array(string => mixed)
	 */
	abstract protected function params();

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\Charge\WithRedirect::p()
	 * @return string
	 */
	abstract protected function signatureKey();

	/**
	 * 2016-08-27
	 * @param Method $method
	 * @return array(string => mixed)
	 */
	final public static function p(Method $method) {
		/** @var II|I|OP $payment */
		$payment = $method->getInfoInstance();
		/** @var self $i */
		$i = df_create(df_convention($method, 'Charge'), [self::$P__PAYMENT => $payment]);
		/** @var array(string => mixed) $p */
		$p = $i->params();
		return $p + [$i->signatureKey() => Signer::i($p, $i)->sign()];
	}
}