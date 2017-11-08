<?php
namespace Df\PaypalClone\W;
/**
 * 2016-07-12
 * It is used as a base of the following virtual classes:
 * \Dfe\Stripe\W\Handler\Source\Canceled: https://github.com/mage2pro/stripe/blob/f633f877/etc/frontend/di.xml#L20-L25
 * \Dfe\Stripe\W\Handler\Source\Chargeable: https://github.com/mage2pro/stripe/blob/f633f877/etc/frontend/di.xml#L26-L32
 * \Dfe\Stripe\W\Handler\Source\Failed: https://github.com/mage2pro/stripe/blob/f633f877/etc/frontend/di.xml#L33-L40
 */
final class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2017-08-15
	 * @override
	 * @see \Df\Payment\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	protected function strategyC() {return \Df\Payment\W\Strategy\ConfirmPending::class;}
}