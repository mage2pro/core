<?php
namespace Df\PaypalClone\W;
/**
 * 2016-07-12
 * @see \Dfe\AllPay\W\Handler
 * @see \Dfe\Dragonpay\W\Handler
 * @see \Dfe\IPay88\W\Handler
 * @see \Dfe\Robokassa\W\Handler
 * 2017-03-20
 * The class is not abstract anymore: you can use it as a base for the virtual types:
 * 1) PostFinance: https://github.com/mage2pro/postfinance/blob/0.1.5/etc/di.xml#L7
 * 2) SecurePay: https://github.com/mage2pro/securepay/blob/1.4.2/etc/di.xml#L8
 * @method Event e()
 */
class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2017-08-15
	 * @override
	 * @see \Df\Payment\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	final protected function strategyC() {return \Df\Payment\W\Strategy\ConfirmPending::class;}
}