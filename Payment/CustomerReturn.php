<?php
// 2016-08-27
namespace Df\Payment;
use Df\Sales\Model\Order as DFO;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-07
 * 2017-03-19
 * @see \Dfe\AllPay\Controller\CustomerReturn\Index
 * @see \Dfe\IPay88\Controller\CustomerReturn\Index
 * @see \Dfe\Robokassa\Controller\CustomerReturn\Index
 * The class is not abstract anymore: you can use it as a base for a virtual type.
 * *) Ginger Payments: https://github.com/mage2pro/ginger-payments/blob/0.4.1/etc/di.xml#L7
 * *) Kassa Compleet: https://github.com/mage2pro/kassa-compleet/blob/0.4.1/etc/di.xml#L7
 * *) Omise: https://github.com/mage2pro/omise/blob/1.7.1/etc/di.xml#L6
 * *) SecurePay: https://github.com/mage2pro/securepay/blob/1.4.1/etc/di.xml#L7
 */
class CustomerReturn extends Action {
	/**
	 * 2016-08-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\ActionInterface::execute()  
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Redirect
	 */
	function execute() {
		if ($this->needLog()) {
			dfp_report($this->m(), $_REQUEST, 'customerReturn');
		}
		$ss = df_checkout_session(); /** @var Session $ss */
		/** @var O|DFO|null $o */ /** @var Redirect $result */
		if (($o = $ss->getLastRealOrder()) && !$o->isCanceled() && $this->isSuccess()) {
			$result = $this->_redirect('checkout/onepage/success');
		}
		else {
			if ($o && $o->canCancel()) {
				$o->cancel()->save();
			}
			$ss->restoreQuote();
			/** @var string $msg */
			$msg = df_var($this->s()->messageFailure($o ? $o->getStore() : null), [
				'originalMessage' => $this->message()
			]);
			if ($o) {
				// 2017-04-13
				// @todo Надо бы здесь дополнительно сохранять в транзакции ответ ПС.
				// У меня-то он логируется в Sentry, но вот администратор магазина его не видит.
				df_order_comment($o, $msg, true, true);
			}
			// 2016-07-14
			// Show an explanation message to the customer
			// when it returns to the store after an unsuccessful payment attempt.
			df_checkout_error($msg);
			// 2016-05-06 «How to redirect a customer to the checkout payment step?» https://mage2.pro/t/1523
			$result = $this->_redirect('checkout', ['_fragment' => 'payment']);
		}
		return $result;
	}

	/**
	 * 2017-04-13
	 * @used-by execute()
	 * @see \Dfe\IPay88\Controller\CustomerReturn\Index::isSuccess()
	 * @see \Dfe\Robokassa\Controller\CustomerReturn\Index::isSuccess()
	 * @return bool
	 */
	protected function isSuccess() {return true;}

	/**
	 * 2016-08-27
	 * @used-by execute()
	 * @see \Dfe\AllPay\Controller\CustomerReturn\Index::message()
	 * @see \Dfe\IPay88\Controller\CustomerReturn\Index::message()
	 * @return string
	 */
	protected function message() {return '';}
}