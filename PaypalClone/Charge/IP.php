<?php
namespace Df\PaypalClone\Charge;
use Df\PaypalClone\Method;
/**
 * 2017-03-05
 * Позволяет сделать статический метод абстрактным: http://stackoverflow.com/a/6386309
 * @see \Df\PaypalClone\Charge
 */
interface IP {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @param Method $method
	 * @return array(string, array(string => mixed))
	 */
	static function p(Method $method);
}