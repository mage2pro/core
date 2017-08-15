<?php
namespace Df\Payment\Source;
use Magento\Payment\Model\Method\AbstractMethod as M;
/**
 * 2017-03-21
 * Этот класс не абстрактен: он используется напрямую модулями, не поддерживающими Review
 * в силу необходимости проверки 3D Secure: Checkout.com, Paymill, Omise.
 * @see \Df\Payment\Source\ACR
 */
class AC extends \Df\Config\Source {
	/**
	 * 2017-03-21
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @see \Df\Payment\Source\ACR::map()
	 * @return array(string => string)
	 */
	protected function map() {return [self::A => 'Authorize', self::C => 'Capture'];}

	/**
	 * 2017-03-21
	 * @used-by \Df\Payment\Source\AC::map()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::action()
	 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
	 * @used-by \Dfe\CheckoutCom\Response::action()
	 * @used-by \Dfe\CheckoutCom\Response::magentoTransactionId()
	 */
	const A = M::ACTION_AUTHORIZE;

	/**
	 * 2017-03-21
	 * @used-by c()
	 * @used-by \Df\Payment\Init\Action::preconfigured()
	 * @used-by \Df\Payment\Source\AC::map()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\PaypalClone\W\Event::ttCurrent()
	 */
	const C = M::ACTION_AUTHORIZE_CAPTURE;

	/**
	 * 2017-03-21
	 * @used-by \Df\Payment\Init\Action::preconfiguredToCapture()
	 * @used-by \Dfe\CheckoutCom\Method::isCaptureDesired()
	 * @param string $v
	 * @return bool
	 */
	static function c($v) {return self::C === $v;}
}